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
require_once TEMP_DIR . '/inc/avatar.php';
require_once TEMP_DIR . '/inc/rewrite.php';
require_once TEMP_DIR . '/inc/meta-api.php';
require_once TEMP_DIR . '/inc/helper.php';
require_once TEMP_DIR . '/inc/validator/validator.php';
require_once TEMP_DIR . '/api/routes.php';


function footerCheck()
{
    $footer_str = file_get_contents(TEMP_DIR . '/footer.php');
    if(!strstr($footer_str, base64_decode('TWlyYWdlVg=='))){
        die();
    }
}
// footerCheck();


/*
 * 获取自定义头像
 */
function vt_get_custom_avatar_url($user_id)
{
    $attachment_id = get_user_meta($user_id, "user_avatar_attachment_id")[0];
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



