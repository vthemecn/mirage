<?php

/**
 * Template Name: 页面模板
 * Template Post Type: post
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();

?>

<div class="main-container">
    <div class="main-widget">
        <div class="article-widget">
            <div class="page-title">
                <?php the_title(); ?>
            </div>

            <div class="page-content content-style">
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
                        <i class="fa-solid fa-pen-to-square"></i> <span>编辑</span>
                    </a>
                <?php endif ?>
            </div>
        </div>
    </div><!-- .main-widget -->

    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'default-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
