<?php
namespace vtheme\api;

class Posts {
    
    private function filter_external_images($content) {
        // 获取当前站点的基础URL
        $site_url = site_url();
        
        // 使用DOMDocument来解析HTML内容
        $dom = new \DOMDocument();
        // 使用LIBXML_NOERROR标志抑制错误输出，避免因HTML格式问题导致的警告
        $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR);
        
        $images = $dom->getElementsByTagName('img');
        $removed_nodes = array();
        
        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            
            // 检查src属性是否为外部链接
            if (filter_var($src, FILTER_VALIDATE_URL)) {
                // 解析URL以获取主机部分
                $src_host = parse_url($src, PHP_URL_HOST);
                $site_host = parse_url($site_url, PHP_URL_HOST);
                
                // 如果图片链接不在当前站点域内，则标记为删除
                if ($src_host !== $site_host) {
                    $removed_nodes[] = $img;
                }
            }
        }
        
        // 移除外部图片标签
        foreach ($removed_nodes as $node) {
            $node->parentNode->removeChild($node);
        }
        
        // 输出处理后的HTML内容
        $output = $dom->saveHTML();
        
        // 移除loadHTML添加的额外HTML标签
        $output = str_replace(['<?xml encoding="UTF-8">', '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">'], '', $output);
        
        return $output;
    }
    
    public function create($request) {
        $current_user = wp_get_current_user();
        
        if (!$current_user->exists()) {
            return new \WP_Error('unauthorized', '用户未登录', array('status' => 401));
        }
        
        $title = sanitize_text_field($request->get_param('post_title'));
        $content = $request->get_param('post_content');
        $category_id = $request->get_param('post_category');
        $status = $request->get_param('post_status') ?: 'private'; // 默认为私有（待审核）
        
        if (empty($title) || empty($content)) {
            return new \WP_Error('invalid_data', '标题和内容不能为空', array('status' => 400));
        }
        
        // 过滤外部图片链接
        $content = $this->filter_external_images($content);
        
        // 验证分类ID是否存在
        if (!empty($category_id) && !get_term_by('term_id', $category_id, 'category')) {
            return new \WP_Error('invalid_category', '分类不存在', array('status' => 400));
        }
        
        // 准备文章数据
        $post_data = array(
            'post_title' => $title,
            'post_content' => $content,
            'post_status' => $status, // 使用private状态，等待管理员审核
            'post_author' => $current_user->ID,
            'post_type' => 'post'
        );
        
        // 插入文章
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // 设置文章分类
        if (!empty($category_id)) {
            wp_set_post_terms($post_id, array($category_id), 'category');
        }
        
        // 处理上传的封面图片
        if (isset($_FILES['featured_image'])) {
            $upload_file = $_FILES['featured_image'];
            
            // 验证上传的文件
            if ($upload_file['error'] === UPLOAD_ERR_OK) {
                $file_type = wp_check_filetype($upload_file['name']);
                
                // 检查文件类型是否为允许的图片类型
                if (in_array($file_type['type'], array('image/jpeg', 'image/png', 'image/gif', 'image/webp'))) {
                    // 将文件移动到WordPress媒体库
                    $upload_dir = wp_upload_dir();
                    $file_path = $upload_dir['path'] . '/' . $upload_file['name'];
                    
                    if (move_uploaded_file($upload_file['tmp_name'], $file_path)) {
                        // 使用WordPress媒体函数处理图片
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                        require_once(ABSPATH . 'wp-admin/includes/media.php');
                        
                        // 插入附件到媒体库
                        $attachment = array(
                            'guid'           => $upload_dir['url'] . '/' . basename($file_path),
                            'post_mime_type' => $file_type['type'],
                            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($file_path)),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );
                        
                        $attachment_id = wp_insert_attachment($attachment, $file_path, $post_id);
                        
                        if ($attachment_id) {
                            // 生成缩略图等
                            $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
                            wp_update_attachment_metadata($attachment_id, $attach_data);
                            
                            // 设置为特色图片
                            set_post_thumbnail($post_id, $attachment_id);
                        }
                    }
                }
            }
        }
        
        // 返回成功信息
        return array(
            'success' => true,
            'message' => '文章发布成功，等待管理员审核',
            'post_id' => $post_id
        );
    }
    
    public function index($request) {
        $current_user = wp_get_current_user();
        
        if (!$current_user->exists()) {
            return new \WP_Error('unauthorized', '用户未登录', array('status' => 401));
        }
        
        $posts = get_posts(array(
            'author' => $current_user->ID,
            'posts_per_page' => -1,
            'post_status' => array('publish', 'private', 'draft', 'pending')
        ));
        
        $result = array();
        
        foreach ($posts as $post) {
            $result[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'status' => $post->post_status,
                'date' => $post->post_date,
                'categories' => wp_get_post_terms($post->ID, 'category', array('fields' => 'names'))
            );
        }
        
        return array(
            'success' => true,
            'posts' => $result
        );
    }
    
    public function update($request) {
        $post_id = $request->get_param('id');
        $current_user = wp_get_current_user();
        
        if (!$current_user->exists()) {
            return new \WP_Error('unauthorized', '用户未登录', array('status' => 401));
        }
        
        // 检查是否是文章作者或管理员
        $post = get_post($post_id);
        if (!$post || ($post->post_author != $current_user->ID && !current_user_can('manage_options'))) {
            return new \WP_Error('forbidden', '没有权限编辑此文章', array('status' => 403));
        }
        
        $title = sanitize_text_field($request->get_param('post_title'));
        $content = $request->get_param('post_content');
        $category_id = $request->get_param('post_category');
        
        if (empty($title) || empty($content)) {
            return new \WP_Error('invalid_data', '标题和内容不能为空', array('status' => 400));
        }
        
        // 过滤外部图片链接
        $content = $this->filter_external_images($content);
        
        // 更新文章
        $post_data = array(
            'ID' => $post_id,
            'post_title' => $title,
            'post_content' => $content
        );
        
        $result = wp_update_post($post_data);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        // 更新分类
        if (!empty($category_id)) {
            wp_set_post_terms($post_id, array($category_id), 'category');
        }
        
        return array(
            'success' => true,
            'message' => '文章更新成功'
        );
    }
    
    public function delete($request) {
        $post_id = $request->get_param('id');
        $current_user = wp_get_current_user();
        
        if (!$current_user->exists()) {
            return new \WP_Error('unauthorized', '用户未登录', array('status' => 401));
        }
        
        // 检查是否是文章作者或管理员
        $post = get_post($post_id);
        if (!$post || ($post->post_author != $current_user->ID && !current_user_can('manage_options'))) {
            return new \WP_Error('forbidden', '没有权限删除此文章', array('status' => 403));
        }
        
        $result = wp_delete_post($post_id, true);
        
        if (!$result) {
            return new \WP_Error('delete_failed', '删除文章失败', array('status' => 500));
        }
        
        return array(
            'success' => true,
            'message' => '文章删除成功'
        );
    }
}