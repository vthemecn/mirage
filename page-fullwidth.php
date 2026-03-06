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


<div class="main-container">
    <div class="main-widget">
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


<?php get_footer(); ?>
