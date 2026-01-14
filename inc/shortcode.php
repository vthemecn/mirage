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
            return '<div class="privilege-content-wrapper comment-required">' . do_shortcode($content) . '</div>';
        }
    } else {
        // 未登录用户：通过 Cookie 检查是否评论过（需配合评论时设置 Cookie）
        if (isset($_COOKIE['comment_author_' . COOKIEHASH])) {
            return '<div class="privilege-content-wrapper comment-required">' . do_shortcode($content) . '</div>';
        }
    }

    return '<div class="privilege-content-wrapper comment-required">
        <div class="privilege-indicator"><i class="fa-solid fa-lock"></i>评论可见</div>
        <div class="privilege-content">
            <div class="privilege-title">
                <i class="fa-solid fa-comment"></i>评论后可查看隐藏内容
            </div>
            <div class="privilege-action">
                <a href="#respond" class="privilege-btn">发表评论</a>
            </div>
        </div>
    </div>';
}
add_shortcode('comment_visible', 'comment_visible_shortcode');


// 登录用户可见内容
function logged_in_visible_shortcode($atts, $content = null) {
    if (is_feed() || wp_doing_ajax()) {
        return '';
    }
    
    if (is_user_logged_in()) {
        return '<div class="privilege-content-wrapper login-required">' . do_shortcode($content) . '</div>';
    } else {
        return '<div class="privilege-content-wrapper login-required">
            <div class="privilege-indicator"><i class="fa-solid fa-lock"></i>登录可见</div>
            <div class="privilege-content">
                <div class="privilege-title">
                    <i class="fa-solid fa-lock"></i>本内容需登录后查看
                </div>
                <div class="privilege-action">
                    <a href="' . wp_login_url(get_permalink()) . '" class="privilege-btn">立即登录</a>
                </div>
            </div>
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
        return '<div class="privilege-content-wrapper vip-required">
            <div class="privilege-indicator"><i class="fa-solid fa-lock"></i>VIP可见</div>
            <div class="privilege-content">
                <div class="privilege-title">
                    <i class="fa-solid fa-crown"></i>VIP会员专享内容
                </div>
                <div class="privilege-action">
                    <a href="' . wp_login_url(get_permalink()) . '" class="privilege-btn">登录查看</a>
                </div>
            </div>
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
            return '<div class="privilege-content-wrapper vip-required">' . do_shortcode($content) . '</div>';
        }
    } elseif (!empty($atts['capability'])) {
        // 检查用户权限
        if (current_user_can($atts['capability'])) {
            return '<div class="privilege-content-wrapper vip-required">' . do_shortcode($content) . '</div>';
        }
    } else {
        // 默认检查用户是否具有特殊权限
        if (current_user_can('read_vip_content') || in_array('vip', (array) $user->roles) || in_array('administrator', (array) $user->roles)) {
            return '<div class="privilege-content-wrapper vip-required">' . do_shortcode($content) . '</div>';
        }
    }

    return '<div class="privilege-content-wrapper vip-required">
        <div class="privilege-indicator">VIP可见</div>
        <div class="privilege-content">
            <div class="privilege-title">
                <i class="fa-solid fa-gem"></i>此内容仅限VIP会员查看
            </div>
            <div class="privilege-action">
                <a href="' . wp_login_url(get_permalink()) . '" class="privilege-btn">登录升级</a>
            </div>
        </div>
    </div>';
}
add_shortcode('vip_content', 'vip_visible_shortcode');