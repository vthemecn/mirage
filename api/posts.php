<?php
namespace vtheme\api;

class Posts {
    
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