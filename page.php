<?php

/**
 * Template Name: 页面模板
 * Template Post Type: post
 * 
 * @author: soushenji <soushenji@qq.com>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();

$full_width = $config['full_width']==1 ? 'full-width' : '';

?>

<div class="main-container" <?=$full_width?>>
    <div class="widget-one">
        <div class="singular-article-container">
            <div class="page-title">
                <?php the_title(); ?>
            </div>

            <div class="page-content">
                <?php
                if (have_posts()) {

                    while (have_posts()) {
                        the_post();
                        the_content();
                        // get_template_part( 'templates/content', get_post_type() );
                    }
                }
                ?>
            </div>

            <div class="content-action">
                <?php if( current_user_can( 'manage_options' ) ): ?>
                    <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit" class="widget-action">
                        <i class="iconfont">&#xe77a;</i> <span>编辑</span>
                    </a>
                <?php endif ?>
            </div>
        </div>
    </div><!-- .widget-one -->

    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'default-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
