<?php
$config = vt_get_config();

$hot_items = $config['hot_items'] ? $config['hot_items'] : array();
?>

<div class="hot-widget">
    <div class="header">
        <div class="title"><?=$config['hot_title']?></div>
        <?php /* ?><a href="/">更多</a> <?php */ ?>
    </div>
    <div class="body">
        <div class="hot-list">
            <?php foreach($hot_items as $k => $v):?>
                <a class="hot-item" href="<?= $v['url']?>" target="_blank">
                    <div class="hot-thumbnail">
                        <img src="<?= $v['image']?>" alt="<?= $v['title']?>">
                    </div>
                    <div class="hot-title"><?= $v['title']?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>