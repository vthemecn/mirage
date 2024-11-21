<?php
defined('ABSPATH') || exit;


/**
 * 获取插件设置
 */
function vt_get_config()
{
    global $vt_config;
    $vt_config = $vt_config ? $vt_config : get_option(THEME_OPTION_NAME);
    return $vt_config;
}


/**
 * 获取默认设置
 */
function vt_get_default_config()
{
	$config = [];
  
	/**
	 * 常规设置
	 */
    $config['site_logo'] = get_bloginfo('template_url') . '/assets/images/logo.png';
    $config['basic_style'] = '1';
    $config['is_show_login_register'] = '1';

    $config['basic_style_color'] = '#f5c802';

    $config['widget_title_type'] = 1;

    $config['is_show_sql_counter']  = 0;
    $config['frontend_is_on'] = 1;
    $config['update_is_on'] = 1;

    $config['editor_type'] = 1;
    $config['editor_revision'] = 0;
    $config['page_data_type'] = 0;

    $config['list_type'] = 0; // 0 文章列表，1 卡片列表
    $config['widget_is_on'] = 0;

    $config['list_cards_col'] = 4;
    $config['image_items_height'] = '100%';
	$config['default_image'] = get_bloginfo('template_url') . '/assets/images/default.jpg';
    $config['background_image'] = '';
    $config['dark_mode_type'] = 1;
    $config['highlight_is_on'] = 0;
    $config['comments_is_on'] = 1;

    
	/**
	 * API 设置
	 */
	$config['is_development'] = 0; // 是否开启开发模式
	$config['upload_images_limit'] = 50; // 每个用户每天上传图片的数量限制
	$config['wx_app_id'] = 'wx7da9wdadcctg43x9';
	$config['app_about_id'] = '';
	$config['app_using_id'] = '';
	$config['app_privacy_id'] = '';


	/**
	 * SEO设置
	 */
	$config['meta_keywords'] = "";
	$config['meta_description'] = "";

	/**
	 * 顶部设置
	 */
	$config['header_type'] = 0; //header类型
	$config['header_description'] = '在这里可以填写网站的简介和网站的 Slogan，或者留空';
	$config['header_ad_pic'] = '';
	$config['logo_is_flashing'] = 0;

	/**
	 * 底部设置
	 */
	$config['footer_logo'] = get_bloginfo('template_url') . '/assets/images/logo.png';
	$config['footer_description'] = "";
	$config['footer_bg_color'] = '';
	$config['footer_copyright'] = '&copy; Copyright '.date('Y').' <a href="https://vtheme.cn" target="_blank">VTHEME 微主题</a>';
	$config['footer_qrcode'] = get_bloginfo('template_url') . '/assets/images/qrcode.jpg';
	$config['footer_qrcode_title'] = '扫一扫联系我';


	// 移动端底部导航设置
	$config['is_mobile_nav_show'] = 0;
	$config['mobile_nav_config']  = "#xe89a;|首页|/\n#xe71d;|电话|tel:18812341234\n#xe903;|产品|/archives/category/产品方案";

	/**
	 * 首页设置
	 */
	$config['section_header_type'] = 1;

	// 幻灯片
	$config['_home_options']['sliders_is_show'] = 1;
	$config['_home_options']['sliders_height'] = '350px';
	$config['_home_options']['sliders'] = array(
	  array('pic-url' => get_bloginfo('template_url') . '/assets/images/banner1.jpg'),
	  array('pic-url' => get_bloginfo('template_url') . '/assets/images/banner2.jpg'),
	);

	// 推荐
	$config['_home_options']['hot_is_show'] = 1;
	$config['_home_options']['hot_title'] = '热门推荐';
	$config['_home_options']['hot'] = array();

	// 最新文章
	$config['_home_options']['articles_is_show'] = 1;
	$config['_home_options']['articles_title'] = '最新文章';
	$config['_home_options']['articles_not_in_ids'] = '';
	$config['_home_options']['articles_auto_load'] = 0;
	$config['_home_options']['articles_auto_limit'] = 0;

	// 友情链接
	$config['_home_options']['links_is_show'] = 1;

	/**
	 * 邮箱设置
	 */
	$config['vt_email_is_on'] 	= 1;
	$config['smtp_host'] 		= 'smtp.qq.com';
	$config['smtp_port'] 		= '465';
	$config['smtp_username'] 	= '';
	$config['smtp_password'] 	= '';
	$config['smtp_nicename'] 	= 'MirageV 主题';
	$config['test_email']		= '';

	/**
	 * 浮动工具栏
	 */
	$config['is_side_bar_dark_mode_show'] = 1;

	/**
	 * 第三方登录设置
	 */
	$config['is_wxapp_active'] 	= 1;
	$config['vt_wx_appid'] 		= "wx01aa84e3c1------";
	$config['vt_wx_secret'] 	= '';
    
	return $config;
}
