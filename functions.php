<?php

/**
 * functions and definitions
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

define('THEME_OPTION_NAME', 'mirage');
define('TEMP_DIR', get_template_directory());
define('TEMP_URL', get_bloginfo('template_url'));


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

$config = vt_get_config();


require_once TEMP_DIR . '/inc/helper.php';
require_once TEMP_DIR . '/inc/i18n.php';
require_once TEMP_DIR . '/inc/config.php';
require_once TEMP_DIR . '/inc/menu.php';
require_once TEMP_DIR . '/inc/widget.php';
require_once TEMP_DIR . '/inc/setting.php';
require_once TEMP_DIR . '/inc/switch.php';
require_once TEMP_DIR . '/inc/shortcode.php';
require_once TEMP_DIR . '/inc/category.php';
require_once TEMP_DIR . '/inc/user.php';
require_once TEMP_DIR . '/inc/validator/validator.php';
require_once TEMP_DIR . '/inc/ajax.php';


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
            $avatar = TEMP_URL . '/assets/images/avatar.jpg';
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
        $config = vt_get_config();
    
        $url = get_the_post_thumbnail_url($post_id, $size);
        if (!$url) {
            $url = $config['default_image'];
            $url = $url ? $url : get_template_directory_uri() . '/assets/images/default.jpg';
        }
        return $url;
    }
}





