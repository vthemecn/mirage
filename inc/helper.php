<?php


/**
 * 多语言人性化时间格式化函数（WordPress 版本）
 * 默认语言：英文
 */
function formatHumanTime($time, $lang = '') {
    // WordPress 语言代码映射
    $langMap = [
        'zh_CN' => 'zh', 'zh_TW' => 'zh-tw', 'zh_HK' => 'zh-tw', 'zh_MO' => 'zh-tw', 'zh_SG' => 'zh',
        'en_US' => 'en', 'en_GB' => 'en', 'en_CA' => 'en', 'en_AU' => 'en', 'en_NZ' => 'en', 'en_ZA' => 'en', 'en_IE' => 'en',
        'ja' => 'ja', 'ja_JP' => 'ja',
        'ko' => 'ko', 'ko_KR' => 'ko',
    ];
    
    $directLangs = ['zh', 'zh-tw', 'en', 'ja', 'ko'];
    
    // 自动获取 WordPress 站点语言
    if (empty($lang)) {
        $lang = function_exists('get_locale') ? get_locale() : 'en_US';
    }
    
    // 转换语言代码
    $langKey = in_array($lang, $directLangs) ? $lang : ($langMap[$lang] ?? 'en');
    
    // 转换时间为时间戳
    $timestamp = is_string($time) ? strtotime($time) : intval($time);
    
    // 验证时间戳
    if ($timestamp === false || $timestamp <= 0) {
        $invalidTexts = [
            'zh' => '无效时间', 'zh-tw' => '無效時間', 'en' => 'Invalid time',
            'ja' => '無効な時間', 'ko' => '잘못된 시간'
        ];
        return $invalidTexts[$langKey] ?? 'Invalid time';
    }
    
    $diff =  current_time('timestamp') - $timestamp;
    
    // 未来时间
    if ($diff < 0) {
        return date('Y-m-d', $timestamp);
    }
    
    // 多语言配置 [分钟，小时，天，后缀，刚刚]
    // 注意：单位本身不包含"前/ago"，后缀统一添加
    $units = [
        'zh'    => ['分钟', '小时', '天', '前', '刚刚'],
        'zh-tw' => ['分鐘', '小時', '天', '前', '剛剛'],
        'en'    => [' min', ' hour', ' day', ' ago', 'Just now'],
        'ja'    => ['分', '時間', '日', '前', 'たった今'],
        'ko'    => ['분', '시간', '일', '전', '방금']
    ];
    
    $u = $units[$langKey] ?? $units['en'];
    
    if ($diff < 60) {
        return $u[4];
    }
    if ($diff < 3600) {
        $val = floor($diff / 60);
        $plural = ($langKey === 'en' && $val > 1) ? 's' : '';
        return $val . $u[0] . $plural . $u[3];
    }
    if ($diff < 86400) {
        $val = floor($diff / 3600);
        $plural = ($langKey === 'en' && $val > 1) ? 's' : '';
        return $val . $u[1] . $plural . $u[3];
    }
    if ($diff < 2592000) {
        $val = floor($diff / 86400);
        $plural = ($langKey === 'en' && $val > 1) ? 's' : '';
        return $val . $u[2] . $plural . $u[3];
    }
    if ($diff < 31536000) {
        return date('m-d', $timestamp);
    }
    
    return date('Y-m-d', $timestamp);
}


function vt_footer_check()
{
    global $pagenow;

    if ( $pagenow === 'wp-login.php' ) return;

    if ( ! is_admin() && ! wp_doing_ajax() && ! wp_doing_cron() && ! defined('WP_CLI') ) {
        $footer_str = file_get_contents(THEME_DIR . '/footer.php');
        if(!strstr($footer_str, base64_decode('TWlyYWdl'))){
            die(__('Please keep the theme footer credit intact.','vt'));
        }
    }
}


/**
 * breadcrumb 面包屑导航
 * @return void
 */
function get_breadcrumbs()
{
    global $wp_query;
    if (!is_home()) {
        // Start the UL
        echo '';
        // Add the Home link
        $url = get_bloginfo('url');
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


/**
 * 缩减浏览次数显示
 */
function vt_format_view_count($count) {
    $count = (int)$count;
    $locale = get_locale();
    
    // 中文系语言
    $is_chinese = in_array($locale, ['zh_CN', 'zh_TW', 'zh_HK', 'ja', 'ko_KR']);
    
    if ($count <= 1000) {
        return number_format_i18n($count);
    }
    
    if ($count < 10000) {
        $num = rtrim(rtrim(round($count / 1000, 1), '0'), '.');
        return $num . ($is_chinese ? '千' : 'k');
    }
    
    if ($count < 100000000) {
        if ($is_chinese) {
            $num = rtrim(rtrim(round($count / 10000, 1), '0'), '.');
            return $num . '万';
        } else {
            $num = rtrim(rtrim(round($count / 1000000, 1), '0'), '.');
            return $num . 'M';
        }
    }
    
    $num = rtrim(rtrim(round($count / 100000000, 1), '0'), '.');
    return $num . ($is_chinese ? '亿' : 'M');
}


/**
 * 初始化暗黑模式配置
 * 
 * @return array 包含暗黑模式状态和 CSS 类名的数组
 *               - is_dark_mode (bool): 是否为暗黑模式
 *               - dark_mode_class (string): 要应用的 CSS 类名
 */
function vt_init_dark_mode() {
    $is_dark_mode = false;
    $dark_mode_class = '';
    $dark_mode_type = vt_get_config('dark_mode_type', 2);
    
    if ($dark_mode_type == 0) { // 禁用
        $_COOKIE['darkModeType'] = 0;
    } elseif ($dark_mode_type == 1) { // 手动
        $_COOKIE['darkModeType'] = 1;
        $is_dark_mode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] == 1 ? true : false;
        $dark_mode_class = $is_dark_mode ? 'dark-mode' : '';
    } elseif ($dark_mode_type == 2) { // 自动
        $_COOKIE['darkModeType'] = 2;
        $is_dark_mode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] == 1 ? true : false;
        $dark_mode_class = 'dark-mode-auto';
    }
    
    return array(
        'is_dark_mode' => $is_dark_mode,
        'dark_mode_class' => $dark_mode_class,
    );
}


function vt_generate_verification_code($length = 6) {
    return str_pad(rand(pow(10, $length-1), pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}


function vt_save_verification_code($email, $code) {
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

function vt_verify_email_verification_code($email, $code) {
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
        vt_delete_user_verification_code($email);
        return false;
    }

    // 确保两个验证码都是字符串再进行比较
    $expected_code = (string)$stored_data['code'];
    $received_code = (string)$code;
    
    // 使用更安全的比较方式
    $is_valid = hash_equals($expected_code, $received_code);
    
    error_log("Verifying code for {$email}. Expected: {$expected_code}, Received: {$received_code}, Result: " . ($is_valid ? 'true' : 'false'));
    
    return $is_valid;
}

function vt_delete_user_verification_code($email) {
    $option_name = 'email_verification_' . md5($email);
    delete_option($option_name);
}

function vt_delete_reset_verification_code($email) {
    $option_name = 'reset_password_verification_' . md5($email);
    delete_option($option_name);
}

function vt_active($current, $action){
    $current = sanitize_key($current);
    $action = sanitize_key($action);
    return $action === $current ? 'active' : '';
}

