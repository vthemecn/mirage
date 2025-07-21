<?php
/**
 * 使用 rewrite 调用不同的模板文件
 */

function vt_rewrite_rules( $wp_rewrite ) {
    $vt_rules = [
        'users/([^/]*)/star/?'      => 'index.php?vt_page=star&user_id=$matches[1]',
        'users/([^/]*)/like/?'      => 'index.php?vt_page=like&user_id=$matches[1]',
        'users/([^/]*)/setting/?'   => 'index.php?vt_page=setting&user_id=$matches[1]',
        'users/([^/]*)/?'           => 'index.php?vt_page=users&user_id=$matches[1]'
    ];
    $wp_rewrite->rules = $vt_rules + $wp_rewrite->rules;
}
add_action( 'generate_rewrite_rules', 'vt_rewrite_rules' );


function vt_add_query_vars($public_query_vars) {
    $public_query_vars[] = 'vt_page';
    $public_query_vars[] = 'user_id';
    $public_query_vars[] = 'id';
    return $public_query_vars;
}
add_action( 'query_vars', 'vt_add_query_vars' );


function vt_template_redirect() {
    global $wp;
    global $wp_query;
    global $wp_rewrite;

    $vt_page =  isset($wp_query->query_vars['vt_page']) ? $wp_query->query_vars['vt_page'] : null; //查询vt_page变量
    switch ($vt_page) {
        case 'like': require_once(TEMPLATEPATH.'/pages/like.php'); die(); // 个人中心-我的点赞
        case 'users': require_once(TEMPLATEPATH.'/pages/users.php'); die(); // 个人中心-用户列表
        case 'star': require_once(TEMPLATEPATH.'/pages/star.php'); die(); // 个人中心-收藏列表
        case 'setting': require_once(TEMPLATEPATH.'/pages/setting.php'); die(); // 个人中心-设置
    }
}
add_action( 'template_redirect', 'vt_template_redirect' );


function vt_flush_rewrite_rules(){
    global $pagenow;
    global $wp_rewrite;

    if( 'theme.php' == $pagenow && isset( $_GET['activated'] )) {
        $wp_rewrite->flush_rules();
    }
}
add_action( 'load-themes.php', 'vt_flush_rewrite_rules' );


// add_action('init', 'add_vt_rules');
// function add_vt_rules(){
//     add_rewrite_rule(
//         'users/collections/?',
//         'index.php?vt_page=collections',
//         'top'
//     );
// }
