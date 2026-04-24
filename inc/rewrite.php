<?php
/**
 * Copyright (c) vtheme.cn
 *
 * @author vthemecn <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

/*
工作原理说明：
- 用户访问 /user/123/posts
- 重写规则将其转换为 index.php?user_id=123&action=posts
- template_redirect 钩子检测到 user_id 参数
- 直接加载 templates/users/posts.php 模板文件
- 无需创建 WordPress 页面，更加简洁灵活

账户中心同理：
- 用户访问 /account/profile
- 重写规则将其转换为 index.php?vt_account=1&action=profile
- template_redirect 钩子检测到 vt_account 参数
- 验证登录状态后加载 templates/accounts/profile.php 模板文件
*/


if (!defined('ABSPATH')) exit;

/**
 * 添加自定义重写规则（不依赖 WordPress 页面）
 * 直接通过 template_redirect 加载模板文件
 */
function vt_add_rewrite_rules() {
    // ========== 账户中心规则 - /account/:action ==========
    // 账户中心基础规则 - /account（默认为 edit）
    // 使用 'top' 优先级确保高于 WordPress 页面匹配
    add_rewrite_rule(
        '^account/?$',
        'index.php?vt_account=1&action=edit',
        'top'
    );
    
    // 账户中心各个子页面 - /account/:action
    add_rewrite_rule(
        '^account/([a-z-]+)/?$',
        'index.php?vt_account=1&action=$matches[1]',
        'top'
    );

    /*
    // ========== 用户个人主页规则 - /user/:id ==========
    // 用户个人主页基础规则 - /user/123
    add_rewrite_rule(
        '^user/([0-9]+)/?$',
        'index.php?user_id=$matches[1]&action=comments',
        'top'
    );
    
    // 用户点赞页面 - /user/123/likes
    add_rewrite_rule(
        '^user/([0-9]+)/likes/?$',
        'index.php?user_id=$matches[1]&action=likes',
        'top'
    );
    */
}
add_action('init', 'vt_add_rewrite_rules');

/**
 * 让 WordPress 识别自定义查询变量
 */
function vt_add_query_vars($vars) {    
    // 账户中心变量
    $vars[] = 'vt_account';

    // 用户个人主页变量
    $vars[] = 'user_id';
    $vars[] = 'action';
    
    return $vars;
}
add_filter('query_vars', 'vt_add_query_vars');

/**
 * 根据查询变量直接加载模板文件（不依赖 WordPress 页面）
 */
function vt_template_redirect() {
    global $wp_query;

    // ========== 处理账户中心 (/account) ==========
    if (isset($wp_query->query_vars['vt_account']) && get_query_var('vt_account')) {
        // 仅登录用户可访问
        if (!is_user_logged_in()) {
            wp_redirect(home_url());
            exit;
        }
        
        $action = get_query_var('action', 'edit');
        
        // 根据 action 参数加载对应的模板文件
        $template_path = THEME_DIR . '/templates/account/' . sanitize_key($action) . '.php';

        if (file_exists($template_path)) {
            include $template_path;
            exit; // 终止 WordPress 默认加载流程
        }
    }

    /*
    // ========== 处理用户个人主页 (/user/:id) ==========
    if (isset($wp_query->query_vars['user_id'])) {
        $user_id = get_query_var('user_id');
        $action = get_query_var('action', 'posts');
        
        // 验证用户 ID 是否有效
        if (!get_userdata($user_id)) {
            return;
        }
        
        // 根据 action 参数加载对应的模板文件
        $template_path = THEME_DIR . '/templates/user/' . sanitize_key($action) . '.php';
        
        if (file_exists($template_path)) {
            include $template_path;
            exit; // 终止 WordPress 默认加载流程
        }
    }
    */
}
add_action('template_redirect', 'vt_template_redirect');

/**
 * 插件激活时刷新重写规则
 */
function vt_activate_rewrite() {
    vt_add_rewrite_rules();
    flush_rewrite_rules();
}
register_activation_hook(THEME_DIR . 'captain.php', 'vt_activate_rewrite');

/**
 * 插件停用时刷新重写规则
 */
function vt_deactivate_rewrite() {
    flush_rewrite_rules();
}
register_deactivation_hook(THEME_DIR . 'captain.php', 'vt_deactivate_rewrite');

/**
 * 主题激活后自动刷新重写规则
 */
function vt_flush_rewrite_on_theme_activation() {
    global $pagenow;
    global $wp_rewrite;
    
    if ('themes.php' == $pagenow && isset($_GET['activated'])) {
        vt_add_rewrite_rules();
        $wp_rewrite->flush_rules();
    }
}
add_action('load-themes.php', 'vt_flush_rewrite_on_theme_activation');


/**
 * 禁止在 /user 页面将 ?paged=N 重定向到 /page/N
 * WordPress 的 canonical URL 重定向机制在起作用 
 * 它认为“Page 页面的分页应该用 /page/2 而不是 ?paged=2”，于是强制跳转。
 */
function disable_paged_redirect_on_user_page() {
    // 判断是否当前请求是 /user 页面（可根据你的实际路径调整）
    if (is_page('user') || strpos($_SERVER['REQUEST_URI'], '/user') === 0) {
        // 移除 canonical 重定向钩子
        remove_action('template_redirect', 'redirect_canonical');
    }
    if (is_page('account') || strpos($_SERVER['REQUEST_URI'], '/account') === 0) {
        // 移除 canonical 重定向钩子
        remove_action('template_redirect', 'redirect_canonical');
    }
}
add_action('wp', 'disable_paged_redirect_on_user_page', 9);

