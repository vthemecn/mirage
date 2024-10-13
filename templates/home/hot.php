<?php
$config = vt_get_config();

$hot_items = $config['_home_options']['hot'] ? $config['_home_options']['hot'] : array();
$hot_items = array_slice($hot_items, 0, 4);
?>

<div class="hot-widget">
    <div class="header">
        <div class="title"><?=$config['_home_options']['hot_title']?></div>
        <?php /* ?><a href="/">更多</a> <?php */ ?>
    </div>
    <div class="body">
        <div class="hot-list">
            <?php foreach($hot_items as $k => $v):?>
                <a class="hot-item" href="<?= $v['url']?>" target="_blank">
                    <div class="hot-thumbnail">
                        <img src="<?= $v['pic-url']?>" alt="<?= $v['title']?>">
                    </div>
                    <div class="hot-title"><?= $v['title']?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>