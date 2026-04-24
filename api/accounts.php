<?php
/**
 * Copyright (c) vtheme.cn
 *
 * @author vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

namespace vtheme\api;

if (!defined('ABSPATH')) exit;

// 显式包含WordPress文件处理功能
if (!function_exists('wp_handle_upload')) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

class Accounts {
    
    /**
     * 用户登录接口
     */
    public function login(\WP_REST_Request $request) {
        $username = sanitize_text_field($request->get_param('username'));
        $password = $request->get_param('password');
        
        // 验证输入
        if (empty($username) || empty($password)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'missing_fields',
                    'message' => __('Please fill in all required fields', 'vt')
                ]
            ], 400);
        }
        
        // 尝试登录用户
        $credentials = array(
            'user_login' => $username,
            'user_password' => $password,
            'remember' => true
        );
        
        $user = wp_signon($credentials, false);
        
        if (is_wp_error($user)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'login_failed',
                    'message' => $user->get_error_message()
                ]
            ], 401);
        }
        
        // 设置 WordPress 原生认证 Cookie（用于后台和传统页面访问）
        \wp_set_auth_cookie($user->ID, true);
        
        // 生成 JWT token（用于 REST API 认证）
        $jwt_token = v_generate_jwt_token($user->ID);
        
        // 返回用户资源本身
        return new \WP_REST_Response([
            'user_id' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'access_token' => $jwt_token
        ], 200);
    }
    
    /**
     * 用户注册接口
     */
    public function register(\WP_REST_Request $request) {
        $username = sanitize_text_field($request->get_param('username'));
        $email = sanitize_email($request->get_param('email'));
        $password = $request->get_param('password');
        $verification_code = sanitize_text_field($request->get_param('verification_code'));
        
        // 验证输入
        if (empty($username) || empty($email) || empty($password) || empty($verification_code)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'missing_fields',
                    'message' => __('Please fill in all required fields', 'vt')
                ]
            ], 400);
        }
        
        if (strlen($password) < 6) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'weak_password',
                    'message' => __('Password must be at least 6 characters long', 'vt')
                ]
            ], 400);
        }
        
        // 检查验证码
        $verification_result = vt_verify_email_verification_code($email, $verification_code);
        if (!$verification_result) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'invalid_verification_code',
                    'message' => __('Verification code is incorrect or expired', 'vt')
                ]
            ], 400);
        }
        
        // 检查用户名是否已存在
        if (username_exists($username)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'username_exists',
                    'message' => __('Username already exists', 'vt')
                ]
            ], 409);
        }
        
        // 检查邮箱是否已存在
        if (email_exists($email)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'email_exists',
                    'message' => __('Email has been registered', 'vt')
                ]
            ], 409);
        }
        
        // 创建新用户
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'registration_failed',
                    'message' => $user_id->get_error_message()
                ]
            ], 400);
        }

        // 注册成功后删除验证码
        vt_delete_user_verification_code($email);
        
        // 执行传统的WordPress原生登录
        $user = get_user_by('ID', $user_id);
        if ($user) {
            // 手动设置当前用户和认证Cookie，确保登录状态能够保持
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id, true); // 设置记住用户为true
            
            // 触发wp_login动作，确保插件和其他功能可以正确处理登录
            do_action('wp_login', $user->user_login, $user);
        }
        
        // 生成JWT token
        $jwt_token = v_generate_jwt_token($user_id);
        
        // 返回用户资源本身
        return new \WP_REST_Response([
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email,
            'access_token' => $jwt_token
        ], 201);
    }
    
    /**
     * 发送注册验证码接口
     */
    public function sendVerificationCode(\WP_REST_Request $request) {
        $email = sanitize_email($request->get_param('email'));
        
        if (empty($email)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'missing_email',
                    'message' => __('Please enter your email address', 'vt')
                ]
            ], 400);
        }
        
        // 验证邮箱格式
        if (!is_email($email)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'invalid_email',
                    'message' => __('Please enter a valid email address', 'vt')
                ]
            ], 400);
        }
        
        // 检查邮箱是否已被注册
        if (email_exists($email)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'email_exists',
                    'message' => __('This email has been registered', 'vt')
                ]
            ], 409);
        }
        
        // 生成验证码
        $code = \vt_generate_verification_code();
        
        // 保存验证码到数据库
        \vt_save_verification_code($email, $code);
        
        // 发送邮件
        $subject = __('Registration Verification Code', 'vt');
        $message = sprintf(__('Your registration verification code is: %s, valid for 10 minutes.', 'vt'), $code);
        
        $sent = wp_mail($email, $subject, $message);
        
        if ($sent) {
            return new \WP_REST_Response([
                'message' => __('Verification code has been sent to your email', 'vt')
            ], 200);
        }
        
        return new \WP_REST_Response([
            'error' => [
                'code' => 'email_send_failed',
                'message' => __('Failed to send verification code, please try again later', 'vt')
            ]
        ], 500);
    }
    
    /**
     * 忘记密码接口
     */
    public function forgotPassword(\WP_REST_Request $request) {
        $user_email = sanitize_email($request->get_param('email'));
        
        if (empty($user_email)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'missing_email',
                    'message' => __('Please enter your email address', 'vt')
                ]
            ], 400);
        }
        
        // 获取用户数据
        $user_data = get_user_by('email', $user_email);
        
        if (!$user_data) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'user_not_found',
                    'message' => __('No account found associated with this email', 'vt')
                ]
            ], 404);
        }
        
        // 生成验证码
        $code = \vt_generate_verification_code();
        
        // 保存验证码到数据库，前缀为reset_password_，表示是用于密码重置的
        $option_name = 'reset_password_verification_' . md5($user_email);
        $expiration_time = time() + 600; // 10分钟过期
        
        $data = array(
            'code' => (string)$code,
            'expires_at' => $expiration_time,
            'user_id' => $user_data->ID
        );
        
        update_option($option_name, $data, false);
        
        // 发送邮件
        $subject = __('Password Reset Verification Code', 'vt');
        $message = sprintf(__('You requested to reset your password. Your verification code is: %s, valid for 10 minutes.', 'vt'), $code);
        
        $sent = wp_mail($user_email, $subject, $message);
        
        if ($sent) {
            return new \WP_REST_Response([
                'message' => __('Password reset verification code has been sent to your email', 'vt')
            ], 200);
        }
        
        return new \WP_REST_Response([
            'error' => [
                'code' => 'email_send_failed',
                'message' => __('Failed to send email, please contact administrator', 'vt')
            ]
        ], 500);
    }
    
    /**
     * 重置密码接口
     */
    public function resetPassword(\WP_REST_Request $request) {
        $email = sanitize_email($request->get_param('email'));
        $code = sanitize_text_field($request->get_param('code'));
        $new_password = $request->get_param('new_password');
        
        if (empty($email) || empty($code) || empty($new_password)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'missing_fields',
                    'message' => __('Please fill in all required fields', 'vt')
                ]
            ], 400);
        }
        
        if (strlen($new_password) < 6) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'weak_password',
                    'message' => __('Password must be at least 6 characters long', 'vt')
                ]
            ], 400);
        }
        
        // 验证验证码
        $option_name = 'reset_password_verification_' . md5($email);
        $stored_data = get_option($option_name);
        
        if (!$stored_data || !isset($stored_data['code']) || !isset($stored_data['expires_at']) || !isset($stored_data['user_id'])) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'invalid_verification_code',
                    'message' => __('Verification code is incorrect or expired', 'vt')
                ]
            ], 400);
        }
        
        // 检查验证码是否过期
        if ($stored_data['expires_at'] < time()) {
            vt_delete_reset_verification_code($email);
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'verification_code_expired',
                    'message' => __('Verification code has expired, please request a new one', 'vt')
                ]
            ], 400);
        }
        
        // 检查验证码是否匹配
        $expected_code = (string)$stored_data['code'];
        $received_code = (string)$code;
        
        if (!hash_equals($expected_code, $received_code)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'verification_code_mismatch',
                    'message' => __('Verification code is incorrect', 'vt')
                ]
            ], 400);
        }
        
        // 重置密码
        $user_id = $stored_data['user_id'];
        wp_set_password($new_password, $user_id);
        
        // 删除验证码
        vt_delete_reset_verification_code($email);

        // 执行传统的WordPress原生登录
        $user = get_user_by('ID', $user_id);
        if ($user) {
            // 手动设置当前用户和认证Cookie，确保登录状态能够保持
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id, true); // 设置记住用户为true
            
            // 触发wp_login动作，确保插件和其他功能可以正确处理登录
            do_action('wp_login', $user->user_login, $user);
        }
        
        return new \WP_REST_Response([
            'message' => __('Password reset successful', 'vt')
        ], 200);
    }
    
    /**
     * 上传头像接口
     */
    public function uploadAvatar(\WP_REST_Request $request) {
        // 检查用户权限
        if (!is_user_logged_in()) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'unauthorized',
                    'message' => __('Please login first', 'vt')
                ]
            ], 401);
        }
        
        $user_id = get_current_user_id();

        $vip_avatar_upload = vt_get_config('vip_avatar_upload', false);
        $membership_level = get_user_meta($user_id, 'membership_level', true);
        $membership_level = $membership_level !=="" ? $membership_level : 'free';
        if ($vip_avatar_upload && $membership_level === 'free') {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'unauthorized',
                    'message' => __('You do not have permission to upload avatar', 'vt')
                ]
            ], 401);
        }
        
        // 检查是否有上传的文件
        if (!$request->get_file_params() || !isset($request->get_file_params()['avatar'])) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'missing_file',
                    'message' => __('Please select an avatar file to upload', 'vt')
                ]
            ], 400);
        }
        
        $file = $request->get_file_params()['avatar'];
        
        // 验证文件类型
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
        if (!in_array($file['type'], $allowed_types)) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'invalid_file_type',
                    'message' => __('Only JPG, PNG or GIF format images are allowed', 'vt')
                ]
            ], 400);
        }
        
        // 验证文件大小 (最大5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'file_too_large',
                    'message' => __('Avatar file size cannot exceed 5MB', 'vt')
                ]
            ], 400);
        }
        
        // 上传文件 - 修复MIME类型配置
        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            )
        );
        
        // 使用全局命名空间前缀调用WordPress函数
        $movefile = \wp_handle_upload($file, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            // 保存头像附件ID到用户元数据
            $attachment_id = $this->attachmentUpload($movefile['url'], $user_id);
            update_user_meta($user_id, 'user_avatar_attachment_id', $attachment_id);
            
            // 返回资源本身
            return new \WP_REST_Response([
                'avatar_url' => vt_get_custom_avatar_url($user_id)
            ], 200);
        }
        
        return new \WP_REST_Response([
            'error' => [
                'code' => 'upload_failed',
                'message' => $movefile['error']
            ]
        ], 500);
    }

    private function attachmentUpload($file_url, $post_id = 0) {
        $file_name = basename($file_url);
        $upload_dir = wp_upload_dir();
        
        // 复制文件到上传目录
        $new_file = $upload_dir['path'] . '/' . wp_unique_filename($upload_dir['path'], $file_name);
        copy($file_url, $new_file);
        
        // 获取文件类型
        $wp_filetype = wp_check_filetype($new_file, null);
        
        // 创建附件对象
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($file_name),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        // 插入附件
        $attach_id = wp_insert_attachment($attachment, $new_file, $post_id);
        
        // 生成缩略图
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $new_file);
        wp_update_attachment_metadata($attach_id, $attach_data);
        
        return $attach_id;
    }
    
    /**
     * 获取当前用户信息接口
     */
    public function getCurrentUser(\WP_REST_Request $request) {
        // 检查用户权限
        if (!is_user_logged_in()) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'unauthorized',
                    'message' => __('Please login first', 'vt')
                ]
            ], 401);
        }
        
        $user_id = get_current_user_id();
        $user = get_user_by('ID', $user_id);
        
        if (!$user) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'user_not_found',
                    'message' => __('User does not exist', 'vt')
                ]
            ], 404);
        }
        
        // 返回用户资源本身
        return new \WP_REST_Response([
            'user_id' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'display_name' => $user->display_name,
            'avatar_url' => vt_get_custom_avatar_url($user_id),
            'registered_date' => $user->user_registered
        ], 200);
    }
    
    /**
     * 用户登出接口
     */
    public function logout(\WP_REST_Request $request) {
        // 检查用户权限
        if (!is_user_logged_in()) {
            return new \WP_REST_Response([
                'error' => [
                    'code' => 'unauthorized',
                    'message' => __('Please login first', 'vt')
                ]
            ], 401);
        }
        
        $user_id = get_current_user_id();
        wp_logout();
        
        return new \WP_REST_Response([
            'message' => __('Logout successful', 'vt')
        ], 200);
    }
}



