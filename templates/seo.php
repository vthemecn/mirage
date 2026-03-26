<?php
$keywords    = vt_get_config('keywords', '');
$description = vt_get_config('description', '');

if (is_home()) {

} elseif (is_category()) {
    $category = get_queried_object();
    $keywords = get_term_meta($category->term_id, 'vt-keywords', true);
    $description = category_description();
} elseif (is_tag()) {
    $tag = get_queried_object();
    $keywords = single_tag_title('', false);
    $description = tag_description();
} elseif (is_single()) {
    $description = get_the_excerpt();
}

$keywords    = $keywords ? trim(strip_tags($keywords)) : '';
$description = $description ? trim(strip_tags($description)) : vt_get_config('description', '');
?>

<meta name="keywords" content="<?php echo esc_attr($keywords); ?>">
<meta name="description" content="<?php echo esc_attr($description); ?>">