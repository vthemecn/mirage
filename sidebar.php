<div class="sider little-widget">
    <?php if ( is_active_sidebar( 'default-sidebar' ) ) { ?>
        <?php dynamic_sidebar( 'default-sidebar' ); ?>
    <?php } ?>

    <?= vt_theme_ad('ad_sidebar'); ?>
</div>