<?php

/**
 * 后台编辑分类页，添加自定义字段
 */


// 分类添加页字段
function vt_add_category_field()
{
    echo '<div class="form-field">
        <label for="vt-keywords">关键词</label>
        <input name="vt-keywords" id="vt-keywords" type="text" value="" size="40">
        <p>输入关键词</p>
        </div>';
        
    // 图片上传字段
    echo '<div class="form-field">
          <label for="vt-thumbnail">缩略图</label>
          <div class="image-upload-wrapper">
              <input name="vt-thumbnail" id="vt-thumbnail" type="hidden" value="">
              <div class="image-preview" style="margin: 10px 0;"></div>
              <button type="button" class="upload-image-button button">上传图片</button>
              <button type="button" class="remove-image-button button" style="display:none;">移除图片</button>
          </div>
          <p>点击上传图片作为分类缩略图</p>
        </div>';
        
    echo '<div class="form-field">
    <label for="cat-num">序号</label>
    <input name="_term_order" id="cat-num" type="text" value="" size="40">
    </div>';
}
add_action('category_add_form_fields', 'vt_add_category_field', 10, 2);


// 分类编辑页字段
function vt_edit_category_field($tag)
{
    $cat_keywords = get_term_meta($tag->term_id, 'vt-keywords', true);
    $cat_thumbnail = get_term_meta($tag->term_id, 'vt-thumbnail', true);
    $vt_full_width = get_term_meta($tag->term_id, 'vt_full_width', true);
    $vt_list_type = get_term_meta($tag->term_id, 'vt_list_type', true);
    
    echo '<tr class="form-field">
            <th scope="row"><label for="vt-keywords">关键词</label></th>
            <td>
                <input name="vt-keywords" id="vt-keywords" type="text" value="' . esc_attr($cat_keywords) . '" size="40"/><br>
                <p class="description">' . $tag->name . ' 关键词</p>
            </td>
        </tr>';
        
    // 图片上传字段
    echo '<tr class="form-field">
        <th scope="row"><label for="vt-thumbnail">缩略图</label></th>
        <td>
            <div class="image-upload-wrapper">
                <input name="vt-thumbnail" id="vt-thumbnail" type="hidden" value="' . esc_attr($cat_thumbnail) . '">
                <div class="image-preview" style="margin: 10px 0;">';
                
    if (!empty($cat_thumbnail)) {
        echo '<img src="' . esc_url($cat_thumbnail) . '" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 5px;" />';
    }
    
    echo '</div>
                <button type="button" class="upload-image-button button">' . (!empty($cat_thumbnail) ? '更换图片' : '上传图片') . '</button>
                <button type="button" class="remove-image-button button" style="' . (empty($cat_thumbnail) ? 'display:none;' : '') . '">移除图片</button>
            </div>
            <p class="description">点击上传图片作为分类缩略图</p>
        </td>
    </tr>';

    $checked_0 = $vt_full_width == 0 ? 'checked="checked"' : '';
    $checked_1 = $vt_full_width == 1 ? 'checked="checked"' : '';

    echo '<tr class="form-field">
            <th scope="row"><label>全宽显示</label></th>
            <td>
                <label>
                    <input type="radio" name="vt_full_width" value="1" ' . $checked_1 . '>
                    <span class="date-time-text">开启</span>
                </label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <label>
                    <input type="radio" name="vt_full_width" value="0" ' . $checked_0 . '>
                    <span class="date-time-text">关闭</span>
                </label>
            </td>
        </tr>';
        
    $checked_list_0 = $vt_list_type == 0 ? 'checked="checked"' : '';
    $checked_list_1 = $vt_list_type == 1 ? 'checked="checked"' : '';

    echo '<tr class="form-field">
            <th scope="row"><label for="vt-keywords">模版选择</label></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>模版选择</span></legend>
                    <label>
                        <input type="radio" name="vt_list_type" value="0" ' . $checked_list_0 . '>
                        <span class="date-time-text">文章列表</span>
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="vt_list_type" value="1" ' . $checked_list_1 . '>
                        <span class="date-time-text">卡片列表</span>
                    </label>
                </fieldset>
            </td>
        </tr>';
        
    echo '<tr class="form-field">
    <th scope="row"><label for="cat-num">排序</label></th>
    <td>
    <input name="_term_order" id="cat-num" type="text" value="';
    echo (!empty($tag->term_group)) ? $tag->term_group : '0';
    echo '" size="40"/><br>
    </td>
    </tr>';
}
add_action('category_edit_form_fields', 'vt_edit_category_field', 10, 2);


// 保存数据
function vt_save_category_data($term_id)
{
    if (!current_user_can('manage_categories')) {
        return $term_id;
    }
    
    // 保存分类自定义字段
    if (isset($_POST['vt-keywords'])) {
        update_term_meta($term_id, 'vt-keywords', sanitize_text_field($_POST['vt-keywords']));
    }
    
    if (isset($_POST['vt-thumbnail'])) {
        update_term_meta($term_id, 'vt-thumbnail', esc_url_raw($_POST['vt-thumbnail']));
    }
    
    if (isset($_POST['vt_list_type'])) {
        update_term_meta($term_id, 'vt_list_type', intval($_POST['vt_list_type']));
    }

    if (isset($_POST['vt_full_width'])) {
        update_term_meta($term_id, 'vt_full_width', intval($_POST['vt_full_width']));
    }
    
    // 保存排序字段
    global $wpdb;
    if (isset($_POST['_term_order'])) {
        $wpdb->update($wpdb->terms, array('term_group' => $_POST['_term_order']), array('term_id' => $term_id));
    }
}
add_action('created_category', 'vt_save_category_data', 10, 1);
add_action('edited_category', 'vt_save_category_data', 10, 1);


// 添加图片上传的JavaScript支持
function vt_category_image_upload_script() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // 初始化媒体上传器
        var mediaUploader;
        
        // 上传图片按钮点击事件
        $(document).on('click', '.upload-image-button', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var wrapper = button.closest('.image-upload-wrapper');
            var imageInput = wrapper.find('input[type="hidden"]');
            var preview = wrapper.find('.image-preview');
            
            // 如果媒体上传器已经存在，重新打开
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // 创建新的媒体上传器
            mediaUploader = wp.media({
                title: '选择分类缩略图',
                button: {
                    text: '使用此图片'
                },
                multiple: false
            });
            
            // 选择图片后的回调
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                // 设置隐藏字段的值
                imageInput.val(attachment.url);
                
                // 显示图片预览
                preview.html('<img src="' + attachment.url + '" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 5px;" />');
                
                // 更新按钮文字和显示状态
                button.text('更换图片');
                wrapper.find('.remove-image-button').show();
            });
            
            // 打开媒体上传器
            mediaUploader.open();
        });
        
        // 移除图片按钮点击事件
        $(document).on('click', '.remove-image-button', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var wrapper = button.closest('.image-upload-wrapper');
            var imageInput = wrapper.find('input[type="hidden"]');
            var preview = wrapper.find('.image-preview');
            
            // 清空字段和预览
            imageInput.val('');
            preview.empty();
            
            // 恢复按钮状态
            wrapper.find('.upload-image-button').text('上传图片');
            button.hide();
        });
    });
    </script>
    <?php
}
add_action('admin_footer-edit-tags.php', 'vt_category_image_upload_script');
add_action('admin_footer-term.php', 'vt_category_image_upload_script');
