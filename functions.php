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
require_once THEME_DIR . '/inc/widget.php';
require_once THEME_DIR . '/inc/setting.php';
require_once THEME_DIR . '/inc/switch.php';
require_once THEME_DIR . '/inc/category.php';
require_once THEME_DIR . '/inc/user.php';
require_once THEME_DIR . '/inc/validator/validator.php';
require_once THEME_DIR . '/inc/ajax.php';


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
            'header_main' => __('Mirage 顶部菜单'),
            'footer_nav'  => __('Mirage 底部菜单'),
            'side_menu'   => __('Mirage 侧边菜单')
        )
    );
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
