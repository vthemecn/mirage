<?php

/**
 * functions and definitions
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

define('THEME_OPTION_NAME', 'mirage');
define('TEMP_DIR', get_template_directory());


if (!function_exists('p')) :
    function p($arg)
    {
        echo "<pre style='padding-left:190px;font-size:12px;'>\n";
        print_r($arg);
        echo "</pre>\n\n";
    }
endif;


function vt_get_config()
{
    global $vt_config;
    $vt_config = $vt_config ? $vt_config : get_option(THEME_OPTION_NAME);
    return $vt_config;
}

$config = vt_get_config();


require_once TEMP_DIR . '/inc/i18n.php';
require_once TEMP_DIR . '/inc/config.php';
require_once TEMP_DIR . '/inc/menu.php';
require_once TEMP_DIR . '/inc/widget.php';
require_once TEMP_DIR . '/inc/setting.php';
require_once TEMP_DIR . '/inc/switch.php';
require_once TEMP_DIR . '/inc/shortcode.php';
require_once TEMP_DIR . '/inc/category.php';
require_once TEMP_DIR . '/inc/user.php';
require_once TEMP_DIR . '/inc/rewrite.php';
require_once TEMP_DIR . '/inc/meta-api.php';
require_once TEMP_DIR . '/inc/helper.php';
require_once TEMP_DIR . '/inc/validator/validator.php';
require_once TEMP_DIR . '/api/routes.php';


// 添加登录、注册、找回密码的AJAX处理函数
add_action('wp_ajax_nopriv_login_user', 'handle_login_user');
add_action('wp_ajax_login_user', 'handle_login_user');
add_action('wp_ajax_nopriv_register_user', 'handle_register_user');
add_action('wp_ajax_register_user', 'handle_register_user');
add_action('wp_ajax_nopriv_forgot_password', 'handle_forgot_password');
add_action('wp_ajax_forgot_password', 'handle_forgot_password');

// 添加发送验证码的处理函数
add_action('wp_ajax_nopriv_send_verification_code', 'handle_send_verification_code');
add_action('wp_ajax_send_verification_code', 'handle_send_verification_code');

// 添加上传头像的处理函数
add_action('wp_ajax_upload_avatar', 'handle_upload_avatar');

function handle_login_user() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $username = sanitize_text_field($_POST['username']);
    $password = $_POST['password'];

    // 验证输入
    if (empty($username) || empty($password)) {
        wp_send_json_error('请填写所有必填字段');
        return;
    }

    // 尝试登录用户
    $credentials = array(
        'user_login' => $username,
        'user_password' => $password,
        'remember' => true
    );

    $user = wp_signon($credentials, false);

    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    } else {
        wp_send_json_success('登录成功');
    }
}

function handle_register_user() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $username = sanitize_text_field($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $verification_code = sanitize_text_field($_POST['verification_code']);

    // 验证输入
    if (empty($username) || empty($email) || empty($password) || empty($verification_code)) {
        wp_send_json_error('请填写所有必填字段');
        return;
    }

    if (strlen($password) < 6) {
        wp_send_json_error('密码长度至少为6位');
        return;
    }

    // 检查验证码
    $verification_result = verify_email_verification_code($email, $verification_code);
    if (!$verification_result) {
        wp_send_json_error('验证码错误或已过期');
        return;
    }

    // 检查用户名是否已存在
    if (username_exists($username)) {
        wp_send_json_error('用户名已存在');
        return;
    }

    // 检查邮箱是否已存在
    if (email_exists($email)) {
        wp_send_json_error('邮箱已被注册');
        return;
    }

    // 创建新用户
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_message());
    } else {
        // 注册成功后删除验证码
        delete_user_verification_code($email);
        
        // 自动登录用户
        $user = get_user_by('id', $user_id);
        if ($user) {
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user->user_login, $user);
        }
        
        wp_send_json_success('注册成功并已自动登录');
    }
}

function handle_forgot_password() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $user_email = sanitize_email($_POST['email']);

    if (empty($user_email)) {
        wp_send_json_error('请输入邮箱地址');
        return;
    }

    // 获取用户数据
    $user_data = get_user_by('email', $user_email);

    if (!$user_data) {
        wp_send_json_error('找不到与此邮箱关联的账户');
        return;
    }

    // 生成密码重置密钥
    $key = get_password_reset_key($user_data);
    if (is_wp_error($key)) {
        wp_send_json_error('发生错误，请稍后重试');
        return;
    }

    // 发送密码重置邮件
    $subject = '密码重置链接';
    $message = sprintf(
        "您好，\n\n您请求重置密码，请点击下面的链接进行重置：\n\n%s\n\n如果您没有请求重置密码，请忽略此邮件。",
        network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_data->user_login), 'login')
    );

    $sent = wp_mail($user_email, $subject, $message);

    if ($sent) {
        wp_send_json_success('重置密码链接已发送到您的邮箱');
    } else {
        wp_send_json_error('邮件发送失败，请联系管理员');
    }
}

function handle_send_verification_code() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $email = sanitize_email($_POST['email']);

    if (empty($email)) {
        wp_send_json_error('请输入邮箱地址');
        return;
    }

    // 验证邮箱格式
    if (!is_email($email)) {
        wp_send_json_error('请输入有效的邮箱地址');
        return;
    }

    // 检查邮箱是否已被注册
    if (email_exists($email)) {
        wp_send_json_error('该邮箱已被注册');
        return;
    }

    // 生成验证码
    $code = generate_verification_code();
    
    // 保存验证码到数据库
    save_verification_code($email, $code);
    
    // 发送邮件
    $subject = '注册验证码';
    $message = "您的注册验证码是：<strong>{$code}</strong>，有效期为10分钟。";
    
    $sent = wp_mail($email, $subject, $message);

    if ($sent) {
        wp_send_json_success('验证码已发送到您的邮箱');
    } else {
        wp_send_json_error('验证码发送失败，请稍后重试');
    }
}

function generate_verification_code($length = 6) {
    return str_pad(rand(pow(10, $length-1), pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}

function save_verification_code($email, $code) {
    // 使用选项API存储验证码，设置过期时间
    $option_name = 'email_verification_' . md5($email);
    $expiration_time = time() + 600; // 10分钟过期
    
    $data = array(
        'code' => (string)$code,  // 确保验证码为字符串
        'expires_at' => $expiration_time
    );
    
    // 使用带过期时间的选项存储
    update_option($option_name, $data, false);
}

function verify_email_verification_code($email, $code) {
    $option_name = 'email_verification_' . md5($email);
    $stored_data = get_option($option_name);
    
    if (!$stored_data || !isset($stored_data['code']) || !isset($stored_data['expires_at'])) {
        error_log("Verification data not found for email: " . $email);
        return false;
    }

    // 检查验证码是否过期
    if ($stored_data['expires_at'] < time()) {
        error_log("Verification code expired for email: " . $email);
        // 删除过期的验证码
        delete_user_verification_code($email);
        return false;
    }

    // 确保两个验证码都是字符串再进行比较
    $expected_code = (string)$stored_data['code'];
    $received_code = (string)$code;
    
    // 使用更安全的比较方式
    $is_valid = hash_equals($expected_code, $received_code);
    
    error_log("Verifying code for {$email}. Expected: {$expected_code}, Received: {$received_code}, Result: " . ($is_valid ? 'true' : 'false'));
    
    if ($is_valid) {
        // 验证成功后删除验证码
        delete_user_verification_code($email);
    }
    
    return $is_valid;
}

function delete_user_verification_code($email) {
    $option_name = 'email_verification_' . md5($email);
    delete_option($option_name);
}

function handle_upload_avatar() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    // 检查用户权限
    if (!is_user_logged_in()) {
        wp_send_json_error('请先登录');
        return;
    }

    $user_id = get_current_user_id();
    
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error('请选择要上传的头像文件');
        return;
    }

    $file = $_FILES['avatar'];
    
    // 验证文件类型
    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
    if (!in_array($file['type'], $allowed_types)) {
        wp_send_json_error('只允许上传 JPG、PNG 或 GIF 格式的图片');
        return;
    }
    
    // 验证文件大小 (最大5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        wp_send_json_error('头像文件不能超过5MB');
        return;
    }
    
    // 上传文件
    $upload_overrides = array(
        'test_form' => false,
        'mimes' => array(
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        )
    );
    
    $movefile = wp_handle_upload($file, $upload_overrides);
    
    if ($movefile && !isset($movefile['error'])) {
        // 保存头像附件ID到用户元数据
        $attachment_id = attachment_upload($movefile['url'], $user_id);
        update_user_meta($user_id, 'user_avatar_attachment_id', $attachment_id);
        
        wp_send_json_success(array(
            'message' => '头像上传成功',
            'avatar_url' => vt_get_custom_avatar_url($user_id)
        ));
    } else {
        wp_send_json_error($movefile['error']);
    }
}

function attachment_upload($file_url, $post_id = 0) {
    $file_name = basename($file_url);
    $upload_dir = wp_upload_dir();
    
    // 复制文件到上传目录
    $new_file = $upload_dir['path'] . '/' . wp_unique_filename($upload_dir['path'], $file_name);
    copy($file_url, $new_file);
    
    // 获取文件类型
    $wp_filetype = wp_check_filetype($new_file, null);
    
    // 创建附件对象
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($file_name),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    
    // 插入附件
    $attach_id = wp_insert_attachment($attachment, $new_file, $post_id);
    
    // 生成缩略图
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data = wp_generate_attachment_metadata($attach_id, $new_file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    
    return $attach_id;
}


function footerCheck()
{
    global $pagenow;

    if ( $pagenow === 'wp-login.php' ) return;

    if ( ! is_admin() && ! wp_doing_ajax() && ! wp_doing_cron() && ! defined('WP_CLI') ) {
        $footer_str = file_get_contents(TEMP_DIR . '/footer.php');
        if(!strstr($footer_str, base64_decode('TWlyYWdl'))){
            die(__('keep_theme_footer','vt'));
        }
    }
}


/*
 * 获取自定义头像
 */
function vt_get_custom_avatar_url($user_id)
{
    $attachment_arr = get_user_meta($user_id, "user_avatar_attachment_id");
    $attachment_id = is_array($attachment_arr) ? $attachment_arr[0] : 0;
    if ($attachment_id) {
        $avatar = wp_get_attachment_image_src($attachment_id, 'medium')[0];
    } else {
        $avatar = get_bloginfo('template_directory') . '/assets/images/avatar.jpg';
    }
    return $avatar;
}


/**
 * 获取特色图片 url
 * @param {int} $post_id 文章ID
 */
function vt_get_thumbnail_url($post_id, $size='thumbnail')
{
    $config = vt_get_config();

    $url = get_the_post_thumbnail_url($post_id, $size);
    if (!$url) {
        $url = $config['default_image'];
        $url = $url ? $url : get_template_directory_uri() . '/assets/images/default.jpg';
    }
    return $url;
}


/**
 * 主题菜单设置
 */
if (function_exists('register_nav_menus')) {
    register_nav_menus(
        array(
            'header_main' => __('Mirage 顶部菜单'),
            'footer_nav'  => __('Mirage 底部菜单'),
            'side_menu'   => __('Mirage 侧边菜单')
        )
    );
}


/**
 * breadcrumb 面包屑导航
 * @return void
 * @author soushenji < soushenji@qq.com >
 */
function get_breadcrumbs()
{
    global $wp_query;
    if (!is_home()) {
        // Start the UL
        echo '';
        // Add the Home link
        $url = get_bloginfo('siteurl');
        echo '<a href="' . $url . '">首页</a>';
        if (is_category()) {
            $catTitle = single_cat_title("", false);
            $cat = get_cat_ID($catTitle);
            echo " &gt; " . get_category_parents($cat, TRUE, " &gt; ") . "";
        } elseif (is_archive() ) {
            $category = get_queried_object();
            echo " &gt; " . $category->name;
        } elseif (is_search()) {
            echo " &gt; 搜索结果";
        } elseif (is_404()) {
            echo " &gt; 404 Not Found";
        } elseif (is_single()) {
            $post_id = get_the_ID();
            echo ' &gt; ' . vt_get_post_category_name($post_id) . ' > ';
        } elseif (is_page()) {
            $post = $wp_query->get_queried_object();
            if ($post->post_parent == 0) {
                echo " &gt; " . the_title('', '', FALSE) . "";
            } else {
                $title = the_title('', '', FALSE);
                $ancestors = array_reverse(get_post_ancestors($post->ID));
                array_push($ancestors, $post->ID);
                foreach ($ancestors as $ancestor) {
                    if (
                        $ancestor != end($ancestors)
                    ) {
                        echo ' &gt; ' . strip_tags(apply_filters('single_post_title', get_the_title($ancestor))) . '';
                    } else {
                        echo ' &gt; ' . strip_tags(apply_filters('single_post_title', get_the_title($ancestor))) . '';
                    }
                }
            }
        }
        // End the UL
        echo "";
    }
}


function vt_get_post_category_name($post_ID){
    global $wpdb;
    $sql="SELECT `term_taxonomy_id` FROM $wpdb->term_relationships WHERE `object_id`='".intval($post_ID)."';";
    $cat_id_arr = $wpdb->get_results($sql,ARRAY_A);
    $cat_id = $cat_id_arr[0]['term_taxonomy_id'];
    // $term = get_term( $cat_id, 'taxonomy_name' );
    $term = get_term( $cat_id );
    return $term->name;
}


/**
 * 设置文章浏览次数
 * @param [type] $postID
 * @return void
 * @author soushenji < soushenji@qq.com >
 */
function setPostViews($postID)
{
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '1');
    } else {
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

/**
 * 获取文章浏览次数
* @param [type] $postID
* @return void
* @author soushenji < soushenji@qq.com >
*/
function getPostViews($postID)
{
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count == '') {
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return 0;
    }
    return $count;
}


function vt_custom_js_and_css() {
    $current_theme = wp_get_theme();
    wp_enqueue_style('customstyle', get_template_directory_uri() . '/assets/css/style.css', array(), $current_theme->get('Version'), 'all');
}
add_action('wp_enqueue_scripts', 'vt_custom_js_and_css');


/**
 * 参考：https://stackoverflow.com/questions/62241215/get-current-user-id-returns-0-even-after-modificaton-to-user-php/62242542#62242542
 */
function vt_get_user_id()
{
    $user_id = apply_filters('determine_current_user', false);
    wp_set_current_user($user_id);
    $user_id = get_current_user_id();
    return $user_id;
}


function get_user_by_id($user_id)
{
    $user = new \WP_User($user_id);
    if($user->ID==0){ return false; }
    
    $user_data = (array)$user->data;
    
    $user_data['id'] = $user_data['ID'];
    
    $user_data['avatar'] = '';
    $attachment_id = get_user_meta($user_id, 'user_avatar_attachment_id', true); //头像ID
    $avatar = wp_get_attachment_image_src($attachment_id, 'medium'); // 头像路径
    if ($attachment_id && $avatar) {
        $user_data['avatar'] = $avatar[0];
    }
    unset($user_data['user_pass']);

    // 获取用户信息
    $res['nickname']    = get_user_meta($user_id, 'nickname', true);
    $res['mobile']      = get_user_meta($user_id, 'mobile', true);
    $res['gender']      = get_user_meta($user_id, 'gender', true);
    $res['address']     = get_user_meta($user_id, 'address', true);
    $res['dob']         = get_user_meta($user_id, 'dob', true);
    $res['description'] = get_user_meta($user_id, 'description', true);
    $res['ip']          = get_user_meta($user_id, 'ip', true);
    $res['updated_at']  = get_user_meta($user_id, 'updated_at', true);
    
    $user_data = array_merge($user_data, $res);
    return $user_data;
}


function vt_format_time($timestamp) {
    $now = time();
    $diff = $now - $timestamp;
    echo 'diff' . $diff .' -- ';
    // 如果是未来时间，返回标准格式
    if ($diff < 0) {
        // return date('Y-m-d H:i', $timestamp);
        return date('Y-m-d', $timestamp);
    }

    $yearInSeconds = 365 * 24 * 60 * 60;
    $monthInSeconds = 30 * 24 * 60 * 60;
    $dayInSeconds = 24 * 60 * 60;
    $hourInSeconds = 60 * 60;

    if ($diff >= $yearInSeconds) {
        // 超过一年，显示年-月-日
        return date('Y-m-d', $timestamp);
    } elseif ($diff >= $monthInSeconds) {
        // 一年内，但超过一个月，显示月-日
        return date('m-d', $timestamp);
    } elseif ($diff >= $dayInSeconds) {
        // 超过一天，显示 X天前
        $days = floor($diff / $dayInSeconds);
        return $days == 0 ? '今天' : "{$days}天前";
    } else {
        // 一天内，显示 X小时前
        $hours = floor($diff / $hourInSeconds);
        return $hours == 0 ? '刚刚' : "{$hours}小时前";
    }
}

/**
 * 将时间戳或文章ID格式化为人性化的时间显示
 *
 * 规则：
 * - 1小时内：显示 "X分钟前"
 * - 1小时到24小时内：显示 "X小时前"
 * - 1天到30天内：显示 "X天前"
 * - 30天到1年内：显示 "m-d" (月-日)
 * - 超过1年：显示 "Y-m-d" (年-月-日)
 *
 * @param int|string $time 时间戳 (int) 或 日期时间字符串 (string) 或 文章ID (int)
 * @return string 格式化后的时间字符串
 */
function wordpress_format_time_ago($time) {
    // 获取 WordPress 设置的时区对象
    $wp_timezone = wp_timezone();
    
    // 处理输入
    if (is_numeric($time) && $time > 9999999999) { // 假设是文章ID (通常大于10位)
        $post_id = $time;
        $timestamp = get_post_time('U', true, $post_id); // 'U' 获取时间戳, true 获取GMT时间
        if (!$timestamp) {
            return '未知时间';
        }
    } elseif (is_numeric($time)) { // 假设是时间戳
        $timestamp = (int)$time;
    } else { // 假设是日期字符串
        $date_obj = new DateTime($time, $wp_timezone);
        $timestamp = $date_obj->getTimestamp();
    }

    // 创建 DateTime 对象并应用 WordPress 时区
    $date_obj = new DateTime("@$timestamp");
    $date_obj->setTimezone($wp_timezone);

    // 获取当前时间 (WordPress 时区)
    $now = new DateTime('now', $wp_timezone);

    // 计算时间差 (使用 WordPress 的 human_time_diff 更精确)
    $diff_in_seconds = $now->getTimestamp() - $timestamp;
    $diff_in_days = $diff_in_seconds / DAY_IN_SECONDS; // DAY_IN_SECONDS 是 WordPress 常量 (86400)

    // 判断规则
    if ($diff_in_seconds < MINUTE_IN_SECONDS) { // 少于 1 分钟
        return '刚刚';
    } elseif ($diff_in_seconds < HOUR_IN_SECONDS) { // 少于 1 小时
        $minutes = floor($diff_in_seconds / MINUTE_IN_SECONDS);
        return $minutes . '分钟前';
    } elseif ($diff_in_seconds < DAY_IN_SECONDS) { // 少于 24 小时
        $hours = floor($diff_in_seconds / HOUR_IN_SECONDS);
        return $hours . '小时前';
    } elseif ($diff_in_days < 30) { // 少于 30 天
        // 使用 human_time_diff 获取更人性化的“天”数 (它会处理 1天、2天等)
        $days_text = human_time_diff($timestamp, $now->getTimestamp());
        // human_time_diff 可能返回 "1 天", "2 天" 等，我们只取数字部分或直接使用
        // 简单处理：如果包含“天”，就用它，否则计算
        if (strpos($days_text, '天') !== false || strpos($days_text, 'day') !== false) {
            return $days_text;
        } else {
            // 如果 human_time_diff 没有返回“天”，手动计算 (更精确)
            $days = floor($diff_in_days);
            return $days . '天前';
        }
    } elseif ($diff_in_days < 365) { // 少于 1 年
        // 格式化为 "月-日"
        return $date_obj->format('m-d');
    } else { // 超过 1 年
        // 格式化为 "年-月-日"
        return $date_obj->format('Y-m-d');
    }
}


function vt_get_time($time){

    return wordpress_format_time_ago($time);
}

// 添加发布文章页面所需的TinyMCE编辑器支持
function enqueue_tinymce_for_frontend() {
    global $wp_query;
    
    // 检查当前是否在用户中心发布文章页面
    $vt_page = isset($wp_query->query_vars['vt_page']) ? $wp_query->query_vars['vt_page'] : null;
    $current_user = wp_get_current_user();
    
    if (($vt_page === 'new-post' || $vt_page === 'my-posts') && $current_user->exists()) {
        // 为前端加载TinyMCE编辑器
        wp_enqueue_editor();
    }
}

add_action('wp_enqueue_scripts', 'enqueue_tinymce_for_frontend');

// 加载管理员审核功能
require_once get_template_directory() . '/inc/admin-posts.php';
