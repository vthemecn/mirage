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
}
add_action('category_add_form_fields', 'vt_add_category_field', 10, 2);


// 分类编辑页字段
function vt_edit_category_field($tag)
{
    echo '<tr class="form-field">
            <th scope="row"><label for="cat-keywords">关键词</label></th>
            <td>
                <input name="cat-keywords" id="cat-keywords" type="text" value="' . get_option('cat-keywords-' . $tag->term_id) . '" size="40"/><br>
                <p class="description">' . $tag->name . ' 关键词</p>
            </td>
        </tr>';
    echo '<tr class="form-field">
        <th scope="row"><label for="cat-thumbnail">缩略图</label></th>
        <td>
        <input name="cat-thumbnail" id="cat-thumbnail" type="text" value="' . get_option('cat-thumbnail-' . $tag->term_id) . '" size="40"/><br>
        <p class="description">输入缩略图的地址</p>
        </td>
        </tr>';

    $vt_full_width = get_option('vt_cat_full_width_' . $tag->term_id);
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
}
add_action('category_edit_form_fields', 'vt_edit_category_field', 10, 2);


// 保存数据
function vt_taxonomy_metadate($term_id)
{
    if (!current_user_can('manage_categories')) {
        return $term_id;
    }
    
    if (isset($_POST['cat-keywords'])) {
        $cat_key = 'cat-keywords-' . $term_id;
        $cat_value = $_POST['cat-keywords'];
        update_option($cat_key, $cat_value);
    }
    
    if (isset($_POST['cat-thumbnail'])) {
        $cat_key = 'cat-thumbnail-' . $term_id;
        $cat_value = $_POST['cat-thumbnail'];
        update_option($cat_key, $cat_value);
    }
    
    if (isset($_POST['vt_cat_list_type'])) {
        $key   = 'vt_cat_list_type_' . $term_id;
        $value = $_POST['vt_cat_list_type'];
        update_option($key, $value);
    }

    if (isset($_POST['vt_full_width'])) {
        $key   = 'vt_cat_full_width_' . $term_id;
        $value = $_POST['vt_full_width'];
        update_option($key, $value);
    }
}
add_action('created_category', 'vt_taxonomy_metadate', 10, 1);
add_action('edited_category', 'vt_taxonomy_metadate', 10, 1);



/**
 * 分类模板选择
 */
add_action('category_edit_form_fields', 'vt_edit_category_list_type', 10, 2);
function vt_edit_category_list_type($tag)
{
    $vt_cat_list_type = get_option('vt_cat_list_type_' . $tag->term_id);
    $checked_0 = $vt_cat_list_type == 0 ? 'checked="checked"' : '';
    $checked_1 = $vt_cat_list_type == 1 ? 'checked="checked"' : '';
    $checked_2 = $vt_cat_list_type == 2 ? 'checked="checked"' : '';

    echo '<tr class="form-field">
            <th scope="row"><label for="cat-keywords">模版选择</label></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span>模版选择</span></legend>
                    <label>
                        <input type="radio" name="vt_cat_list_type" value="0" ' . $checked_0 . '>
                        <span class="date-time-text">跟随系统</span>
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="vt_cat_list_type" value="1" ' . $checked_1 . '>
                        <span class="date-time-text">文章列表</span>
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="vt_cat_list_type" value="2" ' . $checked_2 . '>
                        <span class="date-time-text">卡片列表</span><!--<code></code>-->
                    </label>
                </fieldset>
            </td>
        </tr>';
}



function vt_add_cat_field()
{
    echo '<div class="form-field">
    <label for="cat-num">序号</label>
    <input name="_term_order" id="cat-num" type="text" value="" size="40">
    </div>';
}
add_action('category_add_form_fields', 'vt_add_cat_field', 10, 2);

// 分类编辑字段
function vt_edit_cat_field($tag)
{
    echo '<tr class="form-field">
    <th scope="row"><label for="cat-num">排序</label></th>
    <td>
    <input name="_term_order" id="cat-num" type="text" value="';
    echo (!empty($tag->term_group)) ? $tag->term_group : '0';
    echo '" size="40"/><br>
    </td>
    </tr>';
}
add_action('category_edit_form_fields', 'vt_edit_cat_field', 10, 2);

// 保存数据
function vt_tax_metadate($term_id)
{
    global $wpdb;
    if (isset($_POST['_term_order'])) {
        $wpdb->update($wpdb->terms, array('term_group' => $_POST['_term_order']), array('term_id' => $term_id));
    }
}

// 虽然要两个钩子，但是我们可以两个钩子使用同一个函数
add_action('created_category', 'vt_tax_metadate', 10, 1);
add_action('edited_category', 'vt_tax_metadate', 10, 1);


