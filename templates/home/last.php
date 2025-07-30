<?php
$config = vt_get_config();
?>

<div class="main-container">
    <div class="main-widget">
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


