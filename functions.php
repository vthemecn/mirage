<?php

/**
 * functions and definitions
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */


// 打印函数
if (!function_exists('p')) :
    function p($arg)
    {
        echo "<pre style='padding-left:190px;font-size:12px;'>\n";
        print_r($arg);
        echo "</pre>\n\n";
    }
endif;

define('THEME_OPTION_NAME', '_miragev_config');
define('TEMP_DIR', get_template_directory());


require_once TEMP_DIR . '/inc/config/init.php';       // 主题后台面板 
require_once TEMP_DIR . '/inc/i18n.php';              // 国际化
require_once TEMP_DIR . '/inc/my-menu.php';
require_once TEMP_DIR . '/inc/widget.php';
require_once TEMP_DIR . '/inc/setting.php';
require_once TEMP_DIR . '/inc/switch.php';
require_once TEMP_DIR . '/inc/shortcode.php';
require_once TEMP_DIR . '/inc/category.php';
require_once TEMP_DIR . '/inc/avatar.php';
require_once TEMP_DIR . '/inc/rewrite.php';
require_once TEMP_DIR . '/inc/meta-api.php';
require_once TEMP_DIR . '/inc/helper.php';

require_once TEMP_DIR . "/inc/php-jwt/src/JWT.php";
require_once TEMP_DIR . "/inc/php-jwt/src/BeforeValidException.php";
require_once TEMP_DIR . "/inc/php-jwt/src/ExpiredException.php";
require_once TEMP_DIR . "/inc/php-jwt/src/SignatureInvalidException.php";

require_once TEMP_DIR . '/inc/validator/validator.php';

require_once TEMP_DIR . '/api/routes.php';


$vt_config = vt_get_config();

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
    $vt_config = vt_get_config();

    $url = get_the_post_thumbnail_url($post_id, $size);
    if (!$url) {
        $url = $vt_config['default_image'];
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
            'header_main' => __('MirageV 顶部菜单'),
            'footer_nav'  => __('MirageV 底部菜单')
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


//时间格式多久以前  
function friendly_time($ptime)
{
    $ptime = strtotime($ptime);
    $etime = time() - $ptime;
    if ($etime < 1) return '刚刚';
    $interval = array(
        12 * 30 * 24 * 60 * 60 => '年前 (' . date('Y-m-d', $ptime) . ')',
        30 * 24 * 60 * 60 => '个月前 (' . date('m-d', $ptime) . ')',
        7 * 24 * 60 * 60 => '周前 (' . date('m-d', $ptime) . ')',
        24 * 60 * 60 => '天前',
        60 * 60 => '小时前',
        60 => '分钟前',
        1 => '秒前'
    );
    foreach ($interval as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}



function vt_custom_js_and_css() {
    $current_theme = wp_get_theme();
    wp_enqueue_style('customstyle', get_template_directory_uri() . '/assets/css/style.css', array(), $current_theme->get('Version'), 'all');
}
add_action('wp_enqueue_scripts', 'vt_custom_js_and_css');


add_filter('post_type_link', 'custom_type_link', 1, 3);
function custom_type_link($link, $post = 0)
{
    if ($post->post_type == 'audios') {
        return home_url('archives/audios/' . $post->ID);
    } else {
        return $link;
    }
}

add_action('init', 'custom_type_rewrites_init');
function custom_type_rewrites_init()
{
    add_rewrite_rule('archives/audios/([0-9]+)?$', 'index.php?post_type=audios&p=$matches[1]', 'top');
}


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


/**
 * 百度主动推送
 * @param  [type] $vt_post_id 文章ID
 * @param  [type] $baidu_key  百度准入密钥
 * @return [json]              
 */
function baidu_seo($vt_post_id, $baidu_key)
{
    $post_url = get_permalink($vt_post_id);
    $urls = array($post_url);

    $api = 'http://data.zz.baidu.com/urls?site='.home_url().'&token='.$baidu_key;

    $ch = curl_init();
    $options =  array(
        CURLOPT_URL => $api,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => implode("\n", $urls),
        CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    return $result;
}


