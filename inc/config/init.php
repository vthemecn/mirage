<?php

/**
 * 主题后台面板 - 初始化
 */
defined('ABSPATH') || exit;
require_once get_template_directory() . "/inc/config/config.php";

add_action('admin_menu', 'vt_options_init');
function vt_options_init()
{
	// 添加顶部菜单
    if(current_user_can( 'manage_options' )){
        add_theme_page("MirageV 主题设置", "MirageV 主题设置", 'edit_theme_options', "vtheme-options",  'display');
        // add_menu_page('Light 主题设置', 'Light 主题设置', 'administrator', 'vtheme-options', 'display', 'dashicons-admin-generic');
    }
    
	// CSRF 验证 检查 wp_nonce，防止 XSS
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$action = isset($_POST['_action']) ? $_POST['_action'] : '';
		$wp_nonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : '';
		// p($_POST);
		if ($action != 'vt_options_update') {
			return;
		}
        
        if(!current_user_can( 'manage_options' )){
            echo '没有权限';
            exit;
        }

		if (wp_verify_nonce($wp_nonce, "vt_options_update") == false) {
			echo "请求缺少 _wpnonce";
			die();
		}
	}

	// 如果 wp_option 表中没有记录，就添加主题设置记录
	global $vt_config;
	$vt_config = get_option(THEME_OPTION_NAME);
	if (!$vt_config) {
		$vt_config = vt_get_default_config();
		update_option(THEME_OPTION_NAME, $vt_config);
	}

	// 如果是 POST 请求，就更新设置变量
	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['input_save'])) {
		foreach ($_POST as $k => $v) {
			if (is_array($v)) {
				// $vt_config[$k] = json_encode($v, true);
				$vt_config[$k] = $v;
			} else {
				$vt_config[$k] = stripslashes($v);
			}
		}
		update_option(THEME_OPTION_NAME, $vt_config);
	}
    
	// 保存设置备份
	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['backup_save'])) {
		$option_backup = $_POST['option_backup'];
		$option_backup = stripslashes($option_backup);
		$option_backup = json_decode($option_backup, JSON_UNESCAPED_UNICODE);
		if ($option_backup) {
			update_option(THEME_OPTION_NAME, $option_backup);
		}
	}
    
	// 重置
	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['backup_reset'])) {
		$vt_config = vt_get_default_config();
		update_option(THEME_OPTION_NAME, $vt_config);
	}
}


/**
 * 主题后台面板 - 界面
 */
function display()
{
	// 调用 WordPress 的图片上传
	wp_enqueue_script('my-upload', get_bloginfo('stylesheet_directory') . '/inc/config/js/upload.js');  // 加载upload.js文件    
	wp_enqueue_script('thickbox');  // 加载上传图片的js(wp自带) 
	wp_enqueue_style('thickbox'); // 加载css(wp自带)   

	global $vt_config;
	include_once(get_stylesheet_directory() . '/inc/config/pages.php');
}


/*
 * 后台顶部添加链接
 */
add_action('admin_bar_menu', 'add_toolbar_link', 999);
function add_toolbar_link($wp_admin_bar)
{
    $plugin_manage = array(
        'id'    => 'vtheme_manage',
        'title' => 'MirageV 主题设置',
        'href'  => home_url('/wp-admin/themes.php?page=vtheme-options')
    );
    if(current_user_can( 'manage_options' )){
        $wp_admin_bar->add_node($plugin_manage);
    }
}



add_action( 'wp_ajax_vt_download_config', 'vt_download_config' );
function vt_download_config() {
    if(!current_user_can( 'manage_options' )){ echo '{"error":"403"}'; exit; }
    
    $vt_config = get_option(THEME_OPTION_NAME);
    $text=json_encode($vt_config, JSON_UNESCAPED_UNICODE);
    $fileName= 'vtheme_'.date('Ymd').'.json';
    header("Content-Type: application/json");
    header("Content-Disposition: attachment; filename=".$fileName);
    echo $text;
    exit;
}


function footerCheck()
{
	$footer_str = file_get_contents(TEMP_DIR . '/footer.php');
	if(!strstr($footer_str, base64_decode('TWlyYWdlVg=='))){
		die();
	}
}
footerCheck();





