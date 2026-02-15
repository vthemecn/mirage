<?php
$config = vt_get_config();
$hot_items = $config['hot_items'] ? $config['hot_items'] : array();

$full_width = $config['full_width'];
$full_width_class = $full_width==1 ? 'full-width' : '';

$vt_list_type = $config['list_type'];
$vt_list_type_class = '';
if($vt_list_type > 0){
    $vt_columns_num = $config['vt_columns'];
    $vt_list_type_class = 'vt-column vt-column-' . $vt_columns_num;
}
?>

<div class="main-container" <?=$full_width_class?>>
    <div class="main-widget">

        <?php if($config['hot_is_show']):?>
        <div class="hot-widget">
            <div class="header">
                <div class="title"><?=$config['hot_title']?></div>
                <?php /* ?><a href="/">更多</a> <?php */ ?>
            </div>
            <div class="body">
                <div class="hot-list">
                    <?php foreach($hot_items as $k => $v):?>
                        <a class="hot-item" href="<?= $v['link']?>" target="_blank">
                            <div class="hot-thumbnail">
                                <img src="<?= $v['image']?>" alt="<?= $v['title']?>">
                            </div>
                            <div class="hot-title"><?= $v['title']?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif;?>


        <?php
        if($config['posts_nav'] == '0'){
            require_once get_template_directory() . '/templates/home/last-nav.php';
        } else {
            require_once get_template_directory() . '/templates/home/last-ajax.php';
        }
        ?>
    </div>

    <?php if(!vt_get_config('full_width')):?>
        <div class="sider little-widget">
            <?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
                <?php dynamic_sidebar( 'default-sidebar' ); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>


