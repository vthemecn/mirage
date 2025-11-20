<?php
$vt_config = vt_get_config();

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
        <div class="widget-header">
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
