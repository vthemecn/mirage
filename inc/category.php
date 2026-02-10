<?php

/**
 * 后台编辑分类页，添加自定义字段
 */


// 分类添加页字段
function vt_add_category_field()
{
    echo '<div class="form-field">
        <label for="cat-keywords">关键词</label>
        <input name="cat-keywords" id="cat-keywords" type="text" value="" size="40">
        <p>输入关键词</p>
        </div>';
    echo '<div class="form-field">
          <label for="cat-thumbnail">缩略图</label>
          <input name="cat-thumbnail" id="cat-thumbnail" type="text" value="" size="40">
          <p>输入缩略图的地址</p>
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
    $cat_keywords = get_term_meta($tag->term_id, 'cat-keywords', true);
    $cat_thumbnail = get_term_meta($tag->term_id, 'cat-thumbnail', true);
    $vt_full_width = get_term_meta($tag->term_id, 'vt_cat_full_width', true);
    $vt_cat_list_type = get_term_meta($tag->term_id, 'vt_cat_list_type', true);
    
    echo '<tr class="form-field">
            <th scope="row"><label for="cat-keywords">关键词</label></th>
            <td>
                <input name="cat-keywords" id="cat-keywords" type="text" value="' . esc_attr($cat_keywords) . '" size="40"/><br>
                <p class="description">' . $tag->name . ' 关键词</p>
            </td>
        </tr>';
    echo '<tr class="form-field">
        <th scope="row"><label for="cat-thumbnail">缩略图</label></th>
        <td>
        <input name="cat-thumbnail" id="cat-thumbnail" type="text" value="' . esc_attr($cat_thumbnail) . '" size="40"/><br>
        <p class="description">输入缩略图的地址</p>
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
        
    $checked_list_0 = $vt_cat_list_type == 0 ? 'checked="checked"' : '';
    $checked_list_1 = $vt_cat_list_type == 1 ? 'checked="checked"' : '';

    echo '<tr class="form-field">
            <th scope="row"><label for="cat-keywords">模版选择</label></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>模版选择</span></legend>
                    <label>
                        <input type="radio" name="vt_cat_list_type" value="0" ' . $checked_list_0 . '>
                        <span class="date-time-text">文章列表</span>
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="vt_cat_list_type" value="1" ' . $checked_list_1 . '>
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
    if (isset($_POST['cat-keywords'])) {
        update_term_meta($term_id, 'cat-keywords', sanitize_text_field($_POST['cat-keywords']));
    }
    
    if (isset($_POST['cat-thumbnail'])) {
        update_term_meta($term_id, 'cat-thumbnail', esc_url_raw($_POST['cat-thumbnail']));
    }
    
    if (isset($_POST['vt_cat_list_type'])) {
        update_term_meta($term_id, 'vt_cat_list_type', intval($_POST['vt_cat_list_type']));
    }

    if (isset($_POST['vt_full_width'])) {
        update_term_meta($term_id, 'vt_cat_full_width', intval($_POST['vt_full_width']));
    }
    
    // 保存排序字段
    global $wpdb;
    if (isset($_POST['_term_order'])) {
        $wpdb->update($wpdb->terms, array('term_group' => $_POST['_term_order']), array('term_id' => $term_id));
    }
}
add_action('created_category', 'vt_save_category_data', 10, 1);
add_action('edited_category', 'vt_save_category_data', 10, 1);
