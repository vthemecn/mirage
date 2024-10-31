<?php
$vt_config = vt_get_config();

switch ($vt_config['widget_title_type']) {
    case '1': $widget_title_class='type-1'; break;
    case '2': $widget_title_class='type-2'; break;
    case '3': $widget_title_class='type-3'; break;
    default:  $widget_title_class=''; break;
}


$args = array(
    'orderby' => 'name',
    'order' => 'ASC',
    'limit' => -1,
    'category' => '',
    'category_name' => '',
    'hide_invisible' => 1,
    'show_updated' => 0,
    'include' => '',
    'exclude' => '',
    'search' => ''
);

$links = get_bookmarks($args);
?>

<div class="main-container">
    <div class="links widget-container">
        <div class="widget-header <?php echo $widget_title_class?>">
            <div class="widget-title"><?php echo __('友情链接','vt')?></div>
        </div>
        <div class="links-list">
            <?php foreach($links as $k=>$v): ?>
                <a href="<?php echo $v->link_url ?>" target="<?php echo $v->link_target ?>">
                    <?php echo $v->link_name ?>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</div>
