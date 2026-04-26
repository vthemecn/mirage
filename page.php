<?php

/**
 * Template Name: Page
 * Template Post Type: page
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();

$full_width = 0;
$full_width_attribute = $full_width ? 'full-width' : '';
?>


<?php if(vt_get_config('breadcrumbs_enabled')):?>
    <div class="main-container" <?=$full_width_attribute?> >
        <div class="main-widget">
            <?php echo get_breadcrumbs() ?>
        </div>
    </div>
<?php endif;?>


<div class="main-container">
    <div class="main-widget">
        <div class="article-widget">
            <div class="page-title">
                <?php the_title(); ?>
            </div>

            <div class="content-action">
                <?php if( current_user_can( 'manage_options' ) ): ?>
                    <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit" class="widget-action">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span><?=__('Edit','vt')?></span>
                    </a>
                <?php endif ?>
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
        </div>
    </div><!-- .main-widget -->

    <?php if(!$full_width):?>
        <div class="sider">
            <?php if ( is_active_sidebar( 'default-sidebar' ) ) { ?>
                <?php dynamic_sidebar( 'default-sidebar' ); ?>
            <?php } ?>

            <?= vt_theme_ad('ad_sidebar'); ?>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
