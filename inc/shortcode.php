<?php

/**
 * @param  $atts    shortcode 的各个参数
 * @param  $content 标签内的内容
 * @return          html string
 */
function test_shortcode($atts, $content = null){
    // 使用 extract 函数解析标签内的参数
    extract(shortcode_atts(array( "title" => '标题' ), $atts));


    return '<div class="myshortcode">
            <h3>'. $title .'</h3>
            <p>
                '. $content .'
            </p>
        </div>';
}
 
add_shortcode("test", "test_shortcode");


// 评论后可见内容
function comment_visible_shortcode($atts, $content = null) {
    if (is_feed() || wp_doing_ajax()) {
        return '';
    }
    
    if (is_user_logged_in()) {
        // 已登录用户：检查是否有评论
        global $current_user, $post;
        $user_email = $current_user->user_email;
        $comment_count = get_comments([
            'post_id' => $post->ID,
            'author_email' => $user_email,
            'count' => true,
            'status' => 'approve'
        ]);
        if ($comment_count > 0) {
            return do_shortcode($content);
        }
    } else {
        // 未登录用户：通过 Cookie 检查是否评论过（需配合评论时设置 Cookie）
        if (isset($_COOKIE['comment_author_' . COOKIEHASH])) {
            return do_shortcode($content);
        }
    }

    return '<div class="notice-block comment-notice">
        <i class="fa-solid fa-circle-info"></i>
        <p>评论本文后刷新页面即可查看隐藏内容。</p>
    </div>';
}
add_shortcode('comment_visible', 'comment_visible_shortcode');


// 登录用户可见内容
function logged_in_visible_shortcode($atts, $content = null) {
    if (is_feed() || wp_doing_ajax()) {
        return '';
    }
    
    if (is_user_logged_in()) {
        return do_shortcode($content);
    } else {
        return '<div class="notice-block login-notice">
            <i class="fa-solid fa-circle-info"></i>
            <p>请先 <a href="' . wp_login_url(get_permalink()) . '">登录</a> 查看内容。</p>
        </div>';
    }
}
add_shortcode('logged_in_visible', 'logged_in_visible_shortcode');


// VIP用户或特定角色用户可见内容
function vip_visible_shortcode($atts, $content = null) {
    if (is_feed() || wp_doing_ajax()) {
        return '';
    }
    
    if (!is_user_logged_in()) {
        return '<div class="notice-block vip-notice">
            <i class="fa-solid fa-circle-info"></i>
            <p>请先 <a href="' . wp_login_url(get_permalink()) . '">登录</a>。</p>
        </div>';
    }

    $user = wp_get_current_user();
    
    // 检查是否具有特定权限或角色
    $atts = shortcode_atts(array(
        'role' => '',
        'capability' => ''
    ), $atts);
    
    if (!empty($atts['role'])) {
        // 检查用户角色
        if (in_array($atts['role'], (array) $user->roles)) {
            return do_shortcode($content);
        }
    } elseif (!empty($atts['capability'])) {
        // 检查用户权限
        if (current_user_can($atts['capability'])) {
            return do_shortcode($content);
        }
    } else {
        // 默认检查用户是否具有特殊权限
        if (current_user_can('read_vip_content') || in_array('vip', (array) $user->roles) || in_array('administrator', (array) $user->roles)) {
            return do_shortcode($content);
        }
    }

    return '<div class="notice-block vip-notice">
        <i class="fa-solid fa-circle-info"></i>
        <p>您没有权限查看此内容，请联系管理员升级权限。</p>
    </div>';
}
add_shortcode('vip_content', 'vip_visible_shortcode');