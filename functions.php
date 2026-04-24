<?php

/**
 * functions and definitions
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

define('THEME_OPTION_NAME', 'mirage');
define('THEME_DIR', get_template_directory());
define('THEME_URL', get_template_directory_uri());
define('VT_JWT_SECRET', 'ssssss');


if (!function_exists('p')) :
    function p($arg)
    {
        echo "<pre style='padding-left:190px;font-size:12px;'>\n";
        print_r($arg);
        echo "</pre>\n\n";
    }
endif;


function vt_get_config($key = null, $default = null)
{
    static $config = null;

    if ($config === null) {
        $raw = get_option(THEME_OPTION_NAME);
        $config = is_array($raw) ? $raw : [];
    }

    if ($key === null) {
        return $config;
    }

    return array_key_exists($key, $config) ? $config[$key] : $default;
}

require_once THEME_DIR . '/inc/helper.php';
require_once THEME_DIR . '/inc/i18n.php';
require_once THEME_DIR . '/inc/config.php';
require_once THEME_DIR . '/inc/menu.php';
require_once THEME_DIR . '/inc/meta.php';
require_once THEME_DIR . '/inc/widget.php';
require_once THEME_DIR . '/inc/setting.php';
require_once THEME_DIR . '/inc/shortcode.php';
require_once THEME_DIR . '/inc/switch.php';
require_once THEME_DIR . '/inc/category.php';
require_once THEME_DIR . '/inc/user.php';
require_once THEME_DIR . '/inc/validator/validator.php';
require_once THEME_DIR . '/inc/ajax.php';
require_once THEME_DIR . '/inc/rewrite.php';
require_once THEME_DIR . '/inc/common/user-stat.php';

require_once THEME_DIR . "/inc/php-jwt/src/JWT.php";
require_once THEME_DIR . "/inc/php-jwt/src/BeforeValidException.php";
require_once THEME_DIR . "/inc/php-jwt/src/ExpiredException.php";
require_once THEME_DIR . "/inc/php-jwt/src/SignatureInvalidException.php";

require_once THEME_DIR . '/api/routes.php';


/**
 * 判断是否启用了 captain 插件
 * captain plugin link: https://vtheme.cn/captain
 */
function is_captain_active() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    return is_plugin_active( 'captain/captain.php' );
}


/**
 * 主题菜单设置
 */
if (function_exists('register_nav_menus')) {
    register_nav_menus(
        array(
            'header_main' => __('Mirage Header Menu'),
            'footer_nav'  => __('Mirage Footer Menu'),
            'side_menu'   => __('Mirage Sider Menu')
        )
    );
}


/**
 * 主题激活后检查和更新模块配置
 */
add_action('after_switch_theme', 'vt_init_theme_modules');

function vt_init_theme_modules() {
    // 只有超级管理员才能执行
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $config = get_option(THEME_OPTION_NAME);
    
    // 定义默认模块（与 config.php 中的 default 保持一致）
    $default_modules = array(
        'slider'   => __('Sliders', 'vt'),
        'notices'  => __('Notices', 'vt'),
        'hot'      => __('Hot topics', 'vt'),
        'last'     => __('Latest', 'vt'),
        'links'    => __('Links', 'vt'),
    );
    
    // 获取当前配置的 enabled 和 disabled
    $enabled = isset($config['home_layout']['enabled']) ? $config['home_layout']['enabled'] : array();
    $disabled = isset($config['home_layout']['disabled']) ? $config['home_layout']['disabled'] : array();
    
    // 合并为 current
    $current = array_merge($enabled, $disabled);
    
    // 找出缺失的模块（在 default 中但不在 current 中）
    $missing = array_diff_key($default_modules, $current);
    
    // 如果有缺失的模块，添加到 disabled 中
    if (!empty($missing)) {
        foreach ($missing as $key => $name) {
            $disabled[$key] = $name;
        }
        
        // 更新配置
        $config['home_layout']['enabled'] = $enabled;
        $config['home_layout']['disabled'] = $disabled;
        update_option(THEME_OPTION_NAME, $config);
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
 */
function set_post_views($postID)
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
*/
function get_post_views($postID)
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


function vt_get_time($time){
    return formatHumanTime($time);
}



/*
 * 获取自定义头像
 */
if (!function_exists('vt_get_custom_avatar_url')) {
    function vt_get_custom_avatar_url($user_id)
    {
        // 修复：统一使用正确的meta key
        $attachment_id = get_user_meta($user_id, "user_avatar_attachment_id", true);
        if ($attachment_id) {
            $avatar = wp_get_attachment_image_src($attachment_id, 'medium')[0];
        } else {
            $avatar = THEME_URL . '/assets/images/avatar.jpg';
        }
        return $avatar;
    }
}



/**
 * 获取特色图片 url
 * @param {int} $post_id 文章ID
 */
if (!function_exists('vt_get_thumbnail_url')) {
    function vt_get_thumbnail_url($post_id, $size='thumbnail')
    {
        $url = get_the_post_thumbnail_url($post_id, $size);
        if (!$url) {
            $url = vt_get_config('default_image', '');
            $url = $url ? $url : get_template_directory_uri() . '/assets/images/default.jpg';
        }
        return $url;
    }
}


/**
 * 显示广告
 * 头部广告      ad_header
 * 底部广告      ad_footer
 * 侧边栏广告    ad_sidebar
 * 文章头部广告  ad_single_top
 * 文章底部广告  ad_single_bottom
 */
// function theme_ad($position) {
//     $config = vt_get_config();
    
//     $enable = $config[$position . '_enable'] ?? false;
//     $code = $config[$position . '_code'] ?? '';
    
//     // 未启用或无代码则返回空
//     if (empty($enable) || empty($code)) {
//         return '';
//     }
    
//     $html = '<div class="vt-theme-ad ad-' . esc_attr($position) . '">' . $code . '</div>';
//     return $html;
// }
function vt_theme_ad($position) {
    $enable = vt_get_config($position . '_enable', true);
    $code = vt_get_config($position . '_code', '');
    $code_pc = vt_get_config($position . '_code_pc', '');
    
    // 未启用则返回空
    if (empty($enable)) {
        return '';
    }
    
    // 至少需要移动端代码
    if (empty($code)) {
        return '';
    }
    
    $html = '<div class="vt-theme-ad ad-' . esc_attr($position) . '">';
    
    // 移动端广告（默认显示）
    $html .= '<div class="ad-mobile">' . $code . '</div>';
    
    // PC 端广告（可选，不填则使用移动端代码）
    if (!empty($code_pc)) {
        $html .= '<div class="ad-pc">' . $code_pc . '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}


function vt_clean_the_excerpt($content) {
    $protected = ['logged_in_visible', 'vip_content', 'comment_visible', 'member_only', 'paid_content'];
    
    foreach ($protected as $shortcode) {
        $content = preg_replace('/\[' . $shortcode . '\].*?\[\/' . $shortcode . '\]/s', '', $content);
        $content = preg_replace('/\[' . $shortcode . '\]/', '', $content);
    }
    
    $content = strip_shortcodes($content);
    $content = wp_strip_all_tags($content);
    
    return trim($content);
}
// add_filter('the_excerpt', 'vt_clean_the_excerpt', 1);


/**
 * 验证JWT访问令牌
 * 
 * @param string $token JWT令牌字符串
 * @return int|false 用户ID或false（验证失败）
 */
function v_validate_jwt_token($token = null) {
    if (!$token) {
        return false;
    }

    try {
        $secret = defined('VT_JWT_SECRET') ? VT_JWT_SECRET : 'your_strong_secret_key';
        $decoded = Firebase\JWT\JWT::decode($token, $secret, array('HS256'));
        
        if (isset($decoded->exp) && $decoded->exp < time()) {
            return false;
        }

        return $decoded->user_id ?? false;
    } catch (Exception $e) {
        error_log('JWT 验证失败: ' . $e->getMessage());
        return false;
    }
}


/**
 * 生成JWT访问令牌
 * 
 * @param int $user_id 用户ID
 * @param int $expires_in 过期时间（秒），默认24小时
 * @return string|false JWT令牌字符串或false（生成失败）
 */
function v_generate_jwt_token($user_id, $expires_in = 86400) {
    if (!$user_id || !get_user_by('ID', $user_id)) {
        return false;
    }

    try {
        // 优先使用WordPress选项配置的密钥，否则使用默认密钥
        $secret = get_option('captain_jwt_secret', 'default-jwt-secret-key');
        
        $issued_at = time();
        $expiration_time = $issued_at + $expires_in;
        
        $payload = [
            'iss' => home_url(),          // 签发者
            'iat' => $issued_at,          // 签发时间
            'exp' => $expiration_time,    // 过期时间
            'user_id' => $user_id,        // 用户ID
            'sub' => "user_{$user_id}"    // 主题
        ];
        
        $token = Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
        return $token;
    } catch (Exception $e) {
        error_log('JWT 生成失败: ' . $e->getMessage());
        return false;
    }
}



function vt_truncate_comment_content($content, $length = 100) {
    $safe = wp_kses_post($content);
    if (mb_strlen($safe, 'UTF-8') <= $length) {
        return $safe;
    }
    return mb_substr($safe, 0, $length, 'UTF-8') . '…';
}

function vt_friendly_time( $datetime ) {
    $timestamp = is_numeric( $datetime ) ? $datetime : strtotime( $datetime );
    if ( ! $timestamp ) return '';
    return sprintf( __( '%s ago', 'v' ), human_time_diff( $timestamp, current_time( 'timestamp' ) ) );
}

function v_active($current, $action){
    $current = sanitize_key($current);
    $action = sanitize_key($action);
    return $action === $current ? 'active' : '';
}


/**
 * 在前端页面中生成美化URL的辅助函数
 */
function v_get_user_url($user_id, $action = 'profile') {
    if ($action === 'profile') {
        return home_url("/user/{$user_id}");
    }
    return home_url("/user/{$user_id}/{$action}");
}
