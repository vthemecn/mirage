<?php
/**
 * @author: vtheme <mail@vtheme.cn>
 * @author URI: https://vtheme.cn
 */

wp_reset_postdata();

$vt_config = vt_get_config();

$sticky_arr = get_option( 'sticky_posts' );
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
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
                'orderby' => array('date' => "desc"),
                'paged' => $paged
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
            'orderby' => array('date' => "desc"),
                'paged' => $paged
        );

        if( $vt_config['posts_ids'] ){
            $args['category__in'] = $vt_config['posts_ids'];
        }

        if( $vt_config['posts_not_in_ids'] ){
            $args['category__not_in'] = $vt_config['posts_not_in_ids'];
        }

        $query_posts = new WP_Query( $args );
        ?>
        <?php if ( $query_posts->have_posts() ) : ?>
            <?php while ($query_posts->have_posts()) : ?>
                <?php $query_posts->the_post(); ?>
                <?php get_template_part( 'templates/media/media' ); ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-content">
                <img src="<?php bloginfo('template_url'); ?>/assets/images/empty.png">
                <p><?=__('暂无内容','vt')?></p>
            </div>
        <?php endif; ?>  
    </div>
    
</div>

<?php
/**
 * 自定义查询分页
 */
// 设置全局 $wp_query 变量为你的自定义查询
$original_wp_query = $GLOBALS['wp_query'];
$GLOBALS['wp_query'] = $query_posts;

the_posts_pagination(array(
    'mid_size' => 3,
    'prev_text' => '<',
    'next_text' => '>',
    'screen_reader_text' => ' ',
    'aria_label' => "",
));
// 重置全局 $wp_query 对象，重置文章数据
$GLOBALS['wp_query'] = $original_wp_query;
wp_reset_postdata();
?>

