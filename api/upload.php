<?php
namespace vtheme\api;

// 引入WordPress全局函数
use function wp_handle_upload;

class Upload {
    
    public function image($request) {
        $current_user = wp_get_current_user();
        
        if (!$current_user->exists()) {
            return new \WP_Error('unauthorized', '用户未登录', array('status' => 401));
        }
        
        // 检查用户今天的上传数量
        $today = date('Y-m-d');
        $upload_count_option = 'user_daily_upload_count_' . $current_user->ID . '_' . $today;
        $upload_count = get_user_meta($current_user->ID, $upload_count_option, true);
        
        if (!$upload_count) {
            $upload_count = 0;
        }
        
        // 限制每天最多上传10张图片
        if ($upload_count >= 10) {
            return new \WP_Error('upload_limit', '今日上传图片数量已达上限（10张）', array('status' => 400));
        }
        
        if (!isset($_FILES['image'])) {
            return new \WP_Error('no_file', '没有接收到文件', array('status' => 400));
        }
        
        $uploaded_file = $_FILES['image'];
        
        // 验证文件
        if ($uploaded_file['error'] !== UPLOAD_ERR_OK) {
            return new \WP_Error('upload_error', '文件上传失败：' . $uploaded_file['error'], array('status' => 400));
        }
        
        // 检查文件类型
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp');
        $file_type = mime_content_type($uploaded_file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            return new \WP_Error('invalid_type', '不支持的图片格式', array('status' => 400));
        }
        
        // 检查文件大小（限制为5MB）
        if ($uploaded_file['size'] > 5 * 1024 * 1024) {
            return new \WP_Error('file_too_large', '文件大小不能超过5MB', array('status' => 400));
        }
        
        // 确保WordPress媒体处理函数可用
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // 使用WordPress的函数处理上传
        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array(
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            )
        );
        
        // 使用wp_handle_upload处理文件上传，注意使用全局命名空间
        $movefile = \wp_handle_upload($uploaded_file, $upload_overrides);
        
        if ($movefile && !isset($movefile['error']) && $movefile !== false) {
            // 增加用户当日上传计数
            update_user_meta($current_user->ID, $upload_count_option, $upload_count + 1);
            
            // 返回图片URL
            return array(
                'success' => true,
                'url' => $movefile['url'],
                'file' => $movefile['file']
            );
        } else {
            $error_message = isset($movefile['error']) ? $movefile['error'] : '上传失败';
            return new \WP_Error('upload_failed', '文件上传失败：' . $error_message, array('status' => 500));
        }
    }
}