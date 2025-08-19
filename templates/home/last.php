<?php
$config = vt_get_config();
$hot_items = $config['hot_items'] ? $config['hot_items'] : array();
?>

<div class="main-container">
    <div class="main-widget">


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


        <?php
        if($config['posts_nav'] == '0'){
            require_once get_template_directory() . '/templates/home/last-nav.php';
        } else {
            require_once get_template_directory() . '/templates/home/last-ajax.php';
        }
        ?>
    </div>
    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'default-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>


