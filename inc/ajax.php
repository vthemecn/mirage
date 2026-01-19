<?php


if (!defined('ABSPATH')) exit;


// 添加登录、注册、找回密码的AJAX处理函数
add_action('wp_ajax_nopriv_login_user', 'handle_login_user');
add_action('wp_ajax_login_user', 'handle_login_user');
add_action('wp_ajax_nopriv_register_user', 'handle_register_user');
add_action('wp_ajax_register_user', 'handle_register_user');
add_action('wp_ajax_nopriv_forgot_password', 'handle_forgot_password');
add_action('wp_ajax_forgot_password', 'handle_forgot_password');
add_action('wp_ajax_nopriv_reset_password_with_code', 'handle_reset_password_with_code');
add_action('wp_ajax_reset_password_with_code', 'handle_reset_password_with_code');

// 添加发送验证码的处理函数
add_action('wp_ajax_nopriv_send_verification_code', 'handle_send_verification_code');
add_action('wp_ajax_send_verification_code', 'handle_send_verification_code');

// 添加上传头像的处理函数
add_action('wp_ajax_upload_avatar', 'handle_upload_avatar');

function handle_login_user() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $username = sanitize_text_field($_POST['username']);
    $password = $_POST['password'];

    // 验证输入
    if (empty($username) || empty($password)) {
        wp_send_json_error('请填写所有必填字段');
        return;
    }

    // 尝试登录用户
    $credentials = array(
        'user_login' => $username,
        'user_password' => $password,
        'remember' => true
    );

    $user = wp_signon($credentials, false);

    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    } else {
        // 手动设置当前用户和认证Cookie，确保登录状态能够保持
        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID, true); // 设置记住用户为true
        
        // 触发wp_login动作，确保插件和其他功能可以正确处理登录
        do_action('wp_login', $user->user_login, $user);
        
        wp_send_json_success('登录成功');
    }
}

function handle_register_user() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $username = sanitize_text_field($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $verification_code = sanitize_text_field($_POST['verification_code']);

    // 验证输入
    if (empty($username) || empty($email) || empty($password) || empty($verification_code)) {
        wp_send_json_error('请填写所有必填字段');
        return;
    }

    if (strlen($password) < 6) {
        wp_send_json_error('密码长度至少为6位');
        return;
    }

    // 检查验证码
    $verification_result = verify_email_verification_code($email, $verification_code);
    if (!$verification_result) {
        wp_send_json_error('验证码错误或已过期');
        return;
    }

    // 检查用户名是否已存在
    if (username_exists($username)) {
        wp_send_json_error('用户名已存在');
        return;
    }

    // 检查邮箱是否已存在
    if (email_exists($email)) {
        wp_send_json_error('邮箱已被注册');
        return;
    }

    // 创建新用户
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_message());
    } else {
        // 注册成功后删除验证码
        delete_user_verification_code($email);
        
        // 自动登录用户
        $user = get_user_by('id', $user_id);
        if ($user) {
            wp_set_current_user($user_id, $user->user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user->user_login, $user);
        }
        
        wp_send_json_success('注册成功并已自动登录');
    }
}

function handle_forgot_password() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $user_email = sanitize_email($_POST['email']);

    if (empty($user_email)) {
        wp_send_json_error('请输入邮箱地址');
        return;
    }

    // 获取用户数据
    $user_data = get_user_by('email', $user_email);

    if (!$user_data) {
        wp_send_json_error('找不到与此邮箱关联的账户');
        return;
    }

    // 生成验证码
    $code = generate_verification_code();
    
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
    $subject = '密码重置验证码';
    $message = "您请求重置密码，验证码是：{$code}，有效期为10分钟。";
    
    $sent = wp_mail($user_email, $subject, $message);

    if ($sent) {
        wp_send_json_success('密码重置验证码已发送到您的邮箱');
    } else {
        wp_send_json_error('邮件发送失败，请联系管理员');
    }
}

function handle_reset_password_with_code() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $email = sanitize_email($_POST['email']);
    $code = sanitize_text_field($_POST['code']);
    $new_password = $_POST['new_password'];

    if (empty($email) || empty($code) || empty($new_password)) {
        wp_send_json_error('请填写所有必填字段');
        return;
    }

    if (strlen($new_password) < 6) {
        wp_send_json_error('密码长度至少为6位');
        return;
    }

    // 验证验证码
    $option_name = 'reset_password_verification_' . md5($email);
    $stored_data = get_option($option_name);
    
    if (!$stored_data || !isset($stored_data['code']) || !isset($stored_data['expires_at']) || !isset($stored_data['user_id'])) {
        wp_send_json_error('验证码错误或已过期');
        return;
    }

    // 检查验证码是否过期
    if ($stored_data['expires_at'] < time()) {
        delete_reset_verification_code($email);
        wp_send_json_error('验证码已过期，请重新获取');
        return;
    }

    // 检查验证码是否匹配
    $expected_code = (string)$stored_data['code'];
    $received_code = (string)$code;
    
    if (!hash_equals($expected_code, $received_code)) {
        wp_send_json_error('验证码错误');
        return;
    }

    // 重置密码
    $user_id = $stored_data['user_id'];
    $result = wp_set_password($new_password, $user_id);

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    } else {
        // 删除验证码
        delete_reset_verification_code($email);
        wp_send_json_success('密码重置成功');
    }
}

function handle_send_verification_code() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    $email = sanitize_email($_POST['email']);

    if (empty($email)) {
        wp_send_json_error('请输入邮箱地址');
        return;
    }

    // 验证邮箱格式
    if (!is_email($email)) {
        wp_send_json_error('请输入有效的邮箱地址');
        return;
    }

    // 检查邮箱是否已被注册
    if (email_exists($email)) {
        wp_send_json_error('该邮箱已被注册');
        return;
    }

    // 生成验证码
    $code = generate_verification_code();
    
    // 保存验证码到数据库
    save_verification_code($email, $code);
    
    // 发送邮件
    $subject = '注册验证码';
    $message = "您的注册验证码是：{$code}，有效期为10分钟。";
    
    $sent = wp_mail($email, $subject, $message);

    if ($sent) {
        wp_send_json_success('验证码已发送到您的邮箱');
    } else {
        wp_send_json_error('验证码发送失败，请稍后重试');
    }
}

function generate_verification_code($length = 6) {
    return str_pad(rand(pow(10, $length-1), pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}

function save_verification_code($email, $code) {
    // 使用选项API存储验证码，设置过期时间
    $option_name = 'email_verification_' . md5($email);
    $expiration_time = time() + 600; // 10分钟过期
    
    $data = array(
        'code' => (string)$code,  // 确保验证码为字符串
        'expires_at' => $expiration_time
    );
    
    // 使用带过期时间的选项存储
    update_option($option_name, $data, false);
}

function verify_email_verification_code($email, $code) {
    $option_name = 'email_verification_' . md5($email);
    $stored_data = get_option($option_name);
    
    if (!$stored_data || !isset($stored_data['code']) || !isset($stored_data['expires_at'])) {
        error_log("Verification data not found for email: " . $email);
        return false;
    }

    // 检查验证码是否过期
    if ($stored_data['expires_at'] < time()) {
        error_log("Verification code expired for email: " . $email);
        // 删除过期的验证码
        delete_user_verification_code($email);
        return false;
    }

    // 确保两个验证码都是字符串再进行比较
    $expected_code = (string)$stored_data['code'];
    $received_code = (string)$code;
    
    // 使用更安全的比较方式
    $is_valid = hash_equals($expected_code, $received_code);
    
    error_log("Verifying code for {$email}. Expected: {$expected_code}, Received: {$received_code}, Result: " . ($is_valid ? 'true' : 'false'));
    
    if ($is_valid) {
        // 验证成功后删除验证码
        delete_user_verification_code($email);
    }
    
    return $is_valid;
}

function delete_user_verification_code($email) {
    $option_name = 'email_verification_' . md5($email);
    delete_option($option_name);
}

function delete_reset_verification_code($email) {
    $option_name = 'reset_password_verification_' . md5($email);
    delete_option($option_name);
}

function handle_upload_avatar() {
    // 验证nonce
    if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
        wp_die('Security check failed');
    }

    // 检查用户权限
    if (!is_user_logged_in()) {
        wp_send_json_error('请先登录');
        return;
    }

    $user_id = get_current_user_id();
    
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error('请选择要上传的头像文件');
        return;
    }

    $file = $_FILES['avatar'];
    
    // 验证文件类型
    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
    if (!in_array($file['type'], $allowed_types)) {
        wp_send_json_error('只允许上传 JPG、PNG 或 GIF 格式的图片');
        return;
    }
    
    // 验证文件大小 (最大5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        wp_send_json_error('头像文件不能超过5MB');
        return;
    }
    
    // 上传文件
    $upload_overrides = array(
        'test_form' => false,
        'mimes' => array(
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        )
    );
    
    $movefile = wp_handle_upload($file, $upload_overrides);
    
    if ($movefile && !isset($movefile['error'])) {
        // 保存头像附件ID到用户元数据
        $attachment_id = attachment_upload($movefile['url'], $user_id);
        update_user_meta($user_id, 'user_avatar_attachment_id', $attachment_id);
        
        wp_send_json_success(array(
            'message' => '头像上传成功',
            'avatar_url' => vt_get_custom_avatar_url($user_id)
        ));
    } else {
        wp_send_json_error($movefile['error']);
    }
}

function attachment_upload($file_url, $post_id = 0) {
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


// 添加发布文章页面所需的TinyMCE编辑器支持
function enqueue_tinymce_for_frontend() {
    global $wp_query;
    
    // 检查当前是否在用户中心发布文章页面
    $vt_page = isset($wp_query->query_vars['vt_page']) ? $wp_query->query_vars['vt_page'] : null;
    $current_user = wp_get_current_user();
    
    if (($vt_page === 'new-post' || $vt_page === 'my-posts') && $current_user->exists()) {
        // 为前端加载TinyMCE编辑器
        wp_enqueue_editor();
    }
}

add_action('wp_enqueue_scripts', 'enqueue_tinymce_for_frontend');

// 加载管理员审核功能
require_once get_template_directory() . '/inc/admin-posts.php';




