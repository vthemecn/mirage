<?php
/**
 * @author: vtheme <mail@vtheme.cn>
 * @author URI: https://vtheme.cn
 */

wp_reset_postdata();

$vt_config = vt_get_config();

$sticky_arr = get_option( 'sticky_posts' );

?>



<div class="articles-widget">
    <div class="articles-header">
        <?= __('最新文章','vt')?>
    </div>
    <div class="articles-list">
        <?php if($sticky_arr): ?>
            <?php
            $args = array(
                'post__in' => $sticky_arr,
                'ignore_sticky_posts' => 1,
                'orderby' => array('date' => "desc")
            );

            if( $vt_config['posts_ids'] ){
                $args['category__in'] = $vt_config['posts_ids'];
            }

            if( $vt_config['posts_not_in_ids'] ){
                $args['category__not_in'] = $vt_config['posts_not_in_ids'];
            }

            $query_posts = new WP_Query( $args );
            ?>
            <?php while ($query_posts->have_posts()) : ?>
                <?php $query_posts->the_post(); ?>
                <?php get_template_part( 'templates/media/media' ); ?>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php endif ?>


        <?php
        $args = array(
            'posts_per_page' => get_option('posts_per_page'),
            'ignore_sticky_posts' => true,
            'post__not_in' => $sticky_arr,
            'orderby' => array('date' => "desc")
        );

        if( $vt_config['posts_ids'] ){
            $args['category__in'] = $vt_config['posts_ids'];
        }

        if( $vt_config['posts_not_in_ids'] ){
            $args['category__not_in'] = $vt_config['posts_not_in_ids'];
        }

        $query_posts = new WP_Query( $args );
        ?>
        <?php while ($query_posts->have_posts()) : ?>
            <?php $query_posts->the_post(); ?>
            <?php get_template_part( 'templates/media/media' ); ?>
        <?php endwhile; ?>
    </div>
    <button
        type="button"
        class="posts-more-button articles-more"
        data-auto-load='<?php echo $vt_config['posts_auto_load'] ?>'
        data-auto-limit='<?php echo $vt_config['posts_auto_load_limit'] ?>'
        data-no-more='false'
        data-current-page='1'>
        <i class="iconfont">&#xe895;</i>
        <span><?= __('查看更多','vt')?></span>
    </button>
</div>



<?php
wp_reset_postdata();
?>


