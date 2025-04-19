<?php
$vt_config = vt_get_config();
?>

<style>
@media screen and (min-width:900px) {
    .swiper.swiper-container { height: <?= $vt_config['slider_height'] ?>px; }
}
</style>

<div class="swiper swiper-container">
    <div class="swiper-wrapper">
        <?php if ($vt_config['slider_items']) : ?>
            <?php foreach ($vt_config['slider_items'] as $k => $v) : ?>
                <a class="swiper-slide" href="<?php echo $v['link'] ?>" <?php echo $v['target'] ? "target='_blank'" : '' ?> >
                    <img src="<?php echo $v['image'] ?>" />
                </a>
            <?php endforeach ?>
        <?php endif ?>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
</div>
