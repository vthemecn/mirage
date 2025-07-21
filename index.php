<?php

/**
 * 主模版文件
 *
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();


?>


<div class="main-container">
    <div class="main-widget">
        <?php
        if( array_key_exists('sliders', $config['home_layout']['enabled']) ){
            require get_template_directory() . '/templates/home/sliders.php';
        }
        ?>

        <?php
        if( array_key_exists('hot', $config['home_layout']['enabled']) ){
            require get_template_directory() . '/templates/home/hot.php';
        }
        ?>

        <?php
        if( array_key_exists('posts', $config['home_layout']['enabled']) ){
            require get_template_directory() . '/templates/home/posts.php';
        }
        ?>
    </div>
    
    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'default-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>

<?php
if( array_key_exists('links', $config['home_layout']['enabled']) ){
    require get_template_directory() . '/templates/home/links.php';
}
?>


<?php
get_footer();
