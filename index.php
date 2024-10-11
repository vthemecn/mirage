<?php

/**
 * 主模版文件
 *
 * @author: soushenji <soushenji@qq.com>
 * @link https://github.com/soushenji
 */

get_header();
$vt_config = vt_get_config();

?>


<div class="main-container">
    <div class="widget-one">
        <?php require get_template_directory() . '/templates/home/sliders.php'; ?>
        <?php require get_template_directory() . '/templates/home/articles.php'; ?>
        <?php require get_template_directory() . '/templates/home/links.php'; ?>
    </div>
    
    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'default-sidebar' ); ?>
        <?php endif; ?>
    </div> 
</div>





<?php
get_footer();
