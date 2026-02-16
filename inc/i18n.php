<?php

/**
 * 语言文件支持
 */
function vt_theme_load_theme_textdomain(){
    load_theme_textdomain('vt', get_template_directory() . '/inc/languages');
}
add_action('after_setup_theme', 'vt_theme_load_theme_textdomain');


function vt_i18n_strings(){
    // global $vt_global_i18n_strings;
    $vt_global_i18n_strings = [
        'loading' => __('Loading...', 'vt'),
        'network_error' => __('Network error, please try again.', 'vt'),
        'operation_failed' => __('Operation failed', 'vt'),
        'remove_like' => __('Remove like', 'vt'),
        'like' => __('Like', 'vt'),
        'remove_star' => __('Remove star', 'vt'),
        'star' => __('Star', 'vt'),
        'please_log_in' => __('Please log in', 'vt'),
    ];

    return $vt_global_i18n_strings;
}






