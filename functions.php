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


function vt_get_time($time){
    if(is_int($timestamp)){
        $timestamp = $time;
    } else {
        $timestamp = strtotime($time);
    }
    return vt_format_time($timestamp);
}



