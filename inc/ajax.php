<?php
/**
 * Copyright (c) vtheme.cn
 *
 * @author vthemecn <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

if (!defined('ABSPATH')) exit;

/**
 * 检查用户登录状态
 */
add_action('wp_ajax_check_user_login_status', 'check_user_login_status');
add_action('wp_ajax_nopriv_check_user_login_status', 'check_user_login_status');

function check_user_login_status() {
    // 验证 nonce
    if (!wp_verify_nonce($_POST['nonce'], 'ajax_nonce')) {
        wp_send_json_error(['message' => __('Security verification failed','vt')]);
        return;
    }
    
    $user_id = get_current_user_id();
    $is_logged_in = $user_id > 0;
    
    wp_send_json_success([
        'is_logged_in' => $is_logged_in,
        'user_id' => $user_id
    ]);
}

/**
 * 喜欢功能 AJAX 处理
 */
add_action('wp_ajax_like_action', 'handle_like_action');
add_action('wp_ajax_nopriv_like_action', 'handle_like_action');

function handle_like_action() {
    // 验证 nonce
    if (!wp_verify_nonce($_POST['nonce'], 'ajax_nonce')) {
        wp_die(__('Security verification failed','vt'));
    }
    
    $object_id = intval($_POST['object_id']);
    $action_type = sanitize_text_field($_POST['action_type']); // 'like' or 'unlike'
    
    if (!$object_id) {
        wp_send_json_error(['message' => 'Invalid object ID']);
        return;
    }
    
    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'vt_star';
    
    // 为未登录用户生成唯一标识
    $user_identifier = $user_id > 0 ? $user_id : 'ip_' . md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
    // $user_identifier = $user_id > 0 ? $user_id : 'ip_' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];
    
    if ($action_type === 'like') {
        // 检查是否已经喜欢过（无论是登录用户还是未登录用户）
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table_name} WHERE (user_id = %d OR user_id = %s) AND object_id = %d AND type = 'like'",
            $user_id, $user_identifier, $object_id
        ));
        
        if (!$existing) {
            // 插入喜欢记录
            $result = $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $user_id > 0 ? $user_id : $user_identifier,
                    'object_id' => $object_id,
                    'type' => 'like',
                    'created_at' => current_time('mysql')
                ),
                array('%s', '%d', '%s', '%s')
            );
            
            if ($result !== false) {
                // 获取喜欢总数
                $like_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table_name} WHERE object_id = %d AND type = 'like'",
                    $object_id
                ));
                
                wp_send_json_success([
                    'message' => 'Liked',
                    'like_count' => $like_count,
                    'liked' => true
                ]);
            } else {
                wp_send_json_error(['message' => 'Like failed']);
            }
        } else {
            wp_send_json_success(['message' => 'Already liked']);
        }
    } elseif ($action_type === 'unlike') {
        // 取消喜欢
        $result = $wpdb->delete(
            $table_name,
            array(
                'user_id' => $user_id,
                'object_id' => $object_id,
                'type' => 'like'
            ),
            array('%d', '%d', '%s')
        );
        
        if ($result !== false) {
            // 获取喜欢总数
            $like_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE object_id = %d AND type = 'like'",
                $object_id
            ));
            
            wp_send_json_success([
                'message' => 'Like removed',
                'like_count' => $like_count,
                'liked' => false,
                'can_unlike' => false
            ]);
        } else {
            wp_send_json_error(['message' => 'Unlike failed']);
        }
    } else {
        wp_send_json_error(['message' => 'Invalid action type']);
    }
}

/**
 * 收藏功能 AJAX 处理
 */
add_action('wp_ajax_star_action', 'handle_star_action');
// 注意：未登录用户禁止收藏，所以不添加 wp_ajax_nopriv_star_action

function handle_star_action() {
    // 验证 nonce
    if (!wp_verify_nonce($_POST['nonce'], 'ajax_nonce')) {
        wp_send_json_error(['message' => __('Security verification failed','vt')]);
        return;
    }
    
    // 检查用户是否登录
    $user_id = get_current_user_id();
    if ($user_id <= 0) {
        wp_send_json_error(['message' => __('Please log in','vt')]);
        return;
    }
    
    $object_id = intval($_POST['object_id']);
    $action_type = sanitize_text_field($_POST['action_type']); // 'star' or 'unstar'
    
    if (!$object_id) {
        wp_send_json_error(['message' => __('Invalid object ID','vt')]);
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'vt_star';
    
    if ($action_type === 'star') {
        // 检查是否已经收藏过
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table_name} WHERE user_id = %d AND object_id = %d AND type = 'star'",
            $user_id, $object_id
        ));
        
        if (!$existing) {
            // 插入收藏记录
            $result = $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $user_id,
                    'object_id' => $object_id,
                    'type' => 'star',
                    'created_at' => current_time('mysql')
                ),
                array('%d', '%d', '%s', '%s')
            );
            
            if ($result !== false) {
                // 获取收藏总数
                $star_count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table_name} WHERE object_id = %d AND type = 'star'",
                    $object_id
                ));
                
                wp_send_json_success([
                    'message' => __('Operation succeeded','vt'),
                    'star_count' => $star_count,
                    'starred' => true
                ]);
            } else {
                wp_send_json_error(['message' => __('Operation failed','vt')]);
            }
        } else {
            wp_send_json_success(['message' => __('Already starred','vt') ]);
        }
    } elseif ($action_type === 'unstar') {
        // 取消收藏
        $result = $wpdb->delete(
            $table_name,
            array(
                'user_id' => $user_id,
                'object_id' => $object_id,
                'type' => 'star'
            ),
            array('%d', '%d', '%s')
        );
        
        if ($result !== false) {
            // 获取收藏总数
            $star_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} WHERE object_id = %d AND type = 'star'",
                $object_id
            ));
            
            wp_send_json_success([
                'message' => __('Operation succeeded','vt'),
                'star_count' => $star_count,
                'starred' => false
            ]);
        } else {
            wp_send_json_error(['message' => __('Operation failed','vt')]);
        }
    } else {
        wp_send_json_error(['message' => __('Invalid action type','vt')]);
    }
}


/**
 * 评论删除功能 AJAX 处理
 */
add_action('wp_ajax_delete_comment', 'handle_delete_comment');

function handle_delete_comment() {
    // 验证 nonce 安全性
    if (!wp_verify_nonce($_POST['nonce'], 'ajax_nonce')) {
        wp_send_json_error(['message' => '安全验证失败']);
        return;
    }
    
    $comment_id = intval($_POST['comment_id']);
    $user_id = get_current_user_id();
    
    // 验证用户是否登录
    if ($user_id <= 0) {
        wp_send_json_error(['message' => __('Please log in','vt')]);
        return;
    }
    
    // 验证用户权限（只能删除自己的评论）
    $comment = get_comment($comment_id);
    if (!$comment) {
        wp_send_json_error(['message' => '评论不存在']);
        return;
    }
    
    if ($comment->user_id != $user_id) {
        wp_send_json_error(['message' => '没有权限删除此评论']);
        return;
    }
    
    // 执行删除操作（true表示永久删除）
    $result = wp_delete_comment($comment_id, true);
    
    if ($result) {
        wp_send_json_success(['message' => '评论删除成功']);
    } else {
        wp_send_json_error(['message' => '删除失败']);
    }
}

/**
 * 获取喜欢状态，未启用
 */
add_action('wp_ajax_get_like_status', 'get_like_status');
add_action('wp_ajax_nopriv_get_like_status', 'get_like_status');

function get_like_status() {
    $object_id = intval($_POST['object_id']);
    
    if (!$object_id) {
        wp_send_json_error(['message' => '无效的对象ID']);
        return;
    }
    
    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'vt_star';
    
    // 获取喜欢总数
    $like_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table_name} WHERE object_id = %d AND type = 'like'",
        $object_id
    ));
    
    // 检查当前用户是否已喜欢（包括未登录用户）
    $is_liked = false;
    if ($user_id > 0) {
        // 登录用户检查
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table_name} WHERE user_id = %d AND object_id = %d AND type = 'like'",
            $user_id, $object_id
        ));
        $is_liked = !empty($existing);
    } else {
        // 未登录用户检查（基于IP和User-Agent）
        $user_identifier = 'ip_' . md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table_name} WHERE user_id = %s AND object_id = %d AND type = 'like'",
            $user_identifier, $object_id
        ));
        $is_liked = !empty($existing);
    }
    
    wp_send_json_success([
        'like_count' => $like_count,
        'is_liked' => $is_liked,
        'can_unlike' => $user_id > 0 // 只有登录用户可以取消喜欢
    ]);
}

/**
 * 获取收藏状态，未启用
 */
add_action('wp_ajax_get_star_status', 'get_star_status');
// 注意：未登录用户禁止收藏，所以不添加 wp_ajax_nopriv_get_star_status

function get_star_status() {
    $object_id = intval($_POST['object_id']);
    
    if (!$object_id) {
        wp_send_json_error(['message' => '无效的对象ID']);
        return;
    }
    
    // 检查用户是否登录
    $user_id = get_current_user_id();
    if ($user_id <= 0) {
        wp_send_json_success([
            'star_count' => 0,
            'is_starred' => false,
            'can_star' => false
        ]);
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'vt_star';
    
    // 获取收藏总数
    $star_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$table_name} WHERE object_id = %d AND type = 'star'",
        $object_id
    ));
    
    // 检查当前用户是否已收藏
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$table_name} WHERE user_id = %d AND object_id = %d AND type = 'star'",
        $user_id, $object_id
    ));
    $is_starred = !empty($existing);
    
    wp_send_json_success([
        'star_count' => $star_count,
        'is_starred' => $is_starred,
        'can_star' => true // 登录用户都可以收藏
    ]);
}