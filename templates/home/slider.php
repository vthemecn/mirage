<?php
$vt_config = vt_get_config();
?>

<style>
@media screen and (min-width:900px) {
    .swiper.swiper-container { height: <?= $vt_config['slider_height'] ?>px; }
}


</style>

<div class="main-container top">
    <div class="main-widget">

        <div class="swiper swiper-container" data-autoplay="<?= $vt_config['slider_autoplay'] ?>">
            <div class="swiper-wrapper">
                <?php if ($vt_config['slider_items']) : ?>
                    <?php foreach ($vt_config['slider_items'] as $k => $v) : ?>
                        <?php
                        $target = (isset($v['target']) && $v['target']) ? 'target="_blank"' : '';    
                        ?>
                        <a class="swiper-slide" href="<?php echo $v['link'] ?>" <?=$target?> >
                            <img src="<?php echo $v['image'] ?>" />
                            <?php if (!empty($v['title'])) : ?>
                                <div class="slider-title"><?php echo esc_html($v['title']); ?></div>
                            <?php endif; ?>
                        </a>
                    <?php endforeach ?>
                <?php endif ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>

        <div class="hot-img-widget">
            <?php if ($vt_config['top_items']) : ?>
                <?php foreach ($vt_config['top_items'] as $k => $v) : ?>
                    <?php if($k>1) break; ?>
                    <?php
                    $target = (isset($v['target']) && $v['target']) ? 'target="_blank"' : '';    
                    ?>
                    <a class="item" href="<?php echo $v['link'] ?>" <?=$target?> >
                        <img src="<?= $v['image'] ?>" />
                        <div class="title"><?= $v['title'] ?></div>
                    </a>
                <?php endforeach ?>
            <?php endif ?>
        </div>

    </div>
</div>