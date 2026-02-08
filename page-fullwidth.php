<?php

/**
 * Template Name: FullWidth
 * Template Post Type: page
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();

?>


<?php
if (have_posts()) {

    while (have_posts()) {
        the_post();
        the_content();
        // get_template_part( 'templates/content', get_post_type() );
    }
}
?>

<?php /* if( current_user_can( 'manage_options' ) ): ?>
    <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit" class="widget-action">
        <i class="fa-solid fa-pen-to-square"></i> <span>编辑</span>
    </a>
<?php endif  */ ?>
            

<?php get_footer(); ?>
