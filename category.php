<?php
/**
 * 不同分类使用不同模版
 */

$config = vt_get_config();

global $wp_query;
$cat_ID = get_query_var('cat');
$vt_cat_list_type = get_option('vt_cat_list_type_' . $cat_ID);

$template_path = 'templates/categories/category-list';

if ($vt_cat_list_type == 0) {
    if($config['list_type']==1){
        $template_path = 'templates/categories/category-cards';
    }else{
        $template_path = 'templates/categories/category-list';
    }
} else {
    if ($vt_cat_list_type ==1){
        $template_path = 'templates/categories/category-list';
    }elseif($vt_cat_list_type ==2){
        $template_path = 'templates/categories/category-cards';
    }
}

get_template_part($template_path);
