<?php

/**
 * 语言文件支持
 */


// add_action('after_setup_theme', 'alanhou_setup');
// function alanhou_setup(){
//   load_theme_textdomain('vt', get_template_directory.'/languages');
// }


// add_action('after_setup_theme', 'vt_theme_load_theme_textdomain');
// function vt_theme_load_theme_textdomain()
// {
    load_theme_textdomain('vt', get_template_directory() . '/inc/languages');
// }

/**
 * 根据设置，修改当前语言
 */
// add_filter('locale', 'vt_theme_localized');
// function vt_theme_localized($locale)
// {
//     $vt_config = vt_get_config();
//     return $vt_config['language'] ? $vt_config['language'] : 'zh_CN';
// }
