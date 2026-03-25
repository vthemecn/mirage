<?php
/**
 * 文章自定义字段管理
 */

// 添加文章元数据框
add_action('add_meta_boxes', 'vt_add_basic_meta_boxes');
function vt_add_basic_meta_boxes() {
    add_meta_box(
        'vt_basic_meta_boxes', // 元数据框 ID
        __('Basic Settings','vt'), // 标题
        'vt_basic_setting_callback',  // 回调函数
        'post',                               // 应用于文章
        'side',                              // 上下文（side = 侧边栏）
        'default',                           // 优先级
        // array( '__back_compat_meta_box' => true )
    );
}

// 渲染元数据框内容
function vt_basic_setting_callback($post) {
    // 添加 nonce 字段用于安全验证
    wp_nonce_field('vt_basic_nonce', 'vt_basic_nonce_field');
    
    // 获取现有值
    $full_width = get_post_meta($post->ID, '_vt_article_full_width', true);
    $checked = ($full_width === 'on') ? 'checked="checked"' : '';
    
    echo '<p>
            <label for="vt_article_full_width">
                <input type="checkbox" id="vt_article_full_width" name="vt_article_full_width" value="on" ' . $checked . ' /> 
                '. __('Full Width','vt') .'
            </label>
          </p>';
}

// 保存文章元数据
add_action('save_post', 'vt_save_article_meta');
function vt_save_article_meta($post_id) {
    // 验证 nonce
    if (!isset($_POST['vt_basic_nonce_field']) || 
        !wp_verify_nonce($_POST['vt_basic_nonce_field'], 'vt_basic_nonce')) {
        return;
    }
    
    // 检查是否为自动保存
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 检查用户权限
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // 保存或更新元数据
    if (isset($_POST['vt_article_full_width'])) {
        update_post_meta($post_id, '_vt_article_full_width', 'on');
    } else {
        update_post_meta($post_id, '_vt_article_full_width', 'off');
    }
}
