<?php

/**
 * 主模版文件
 *
 * @author: soushenji <soushenji@qq.com>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();

$full_width = $config['full_width']==1 ? 'full-width' : '';

?>


<div class="main-container" <?=$full_width?> >
    <div class="widget-one">
        <?php
        if($config['_home_options']['sliders_is_show']){
            require get_template_directory() . '/templates/home/sliders.php';
        }
        ?>

        <?php
        if($config['_home_options']['hot_is_show']){
            require get_template_directory() . '/templates/home/hot.php';
        }
        ?>

        <?php
        if($config['_home_options']['articles_is_show']){
            require get_template_directory() . '/templates/home/articles.php';
        }
        ?>

        <?php
        if($config['_home_options']['links_is_show']){
            require get_template_directory() . '/templates/home/links.php';
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
get_footer();
