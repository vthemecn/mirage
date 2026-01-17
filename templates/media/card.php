<?php
$vt_config = vt_get_config();
?>

<div class="card-item">
    <a class="card-image" href="<?php the_permalink() ?>">
        <?php  $cur_post = get_post(); ?>
        <img src="<?= vt_get_thumbnail_url($cur_post->ID, 'medium') ?>" alt="<?php the_title(); ?>">
    </a>
    <div class="item-info">
        <a class="title" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
    </div>
    <?php /* ?>
    <div class="card-meta">
        <span class="meta date">
            <i class="iconfont">&#xe76d;</i><?php the_time('Y-m-d'); ?>
        </span>
        <span class='meta hit-conuter'>
            <i class="iconfont">&#xe752;</i><?php echo getPostViews(get_the_ID()); ?>
        </span>
        <span class='meta hit-conuter'>
            <i class="iconfont">&#xe663;</i>238
        </span>
    </div>
    <?php */ ?>
</div>