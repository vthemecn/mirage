<?php

/**
 * 文章列表页 按类别请求帖子时，将使用类别模板
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();
?>


<?php
/**
 * 分类页 Banner
 */
$config = vt_get_config();

$full_width_option = get_option('vt_cat_full_width_' . $cat);
$full_width = $full_width_option==1 ? 'full-width' : '';

$category = get_term($cat);

// $default_image = $config['default_image'] ? $config['default_image'] : get_template_directory_uri() . '/assets/images/default.jpg';

// 获取分类第一篇文章的缩略图或者图片
$banner_image = get_bloginfo('template_url') . '/assets/images/banner.jpg';
$args = array(
    'cat' => $cat,
    'orderby' => array("menu_order" => "desc",'date' => "desc"),
    'posts_per_page'=>1
);
$query_posts = new WP_Query($args);
if($query_posts->posts){
    $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($query_posts->posts[0]->ID), 'large');
    $banner_image = $thumbnail ? $thumbnail[0] : $banner_image;
}

// 列数
$col_num_class = 'col-num-5';
switch ($config['list_cards_col']) {
    case '3':
        $col_num_class = 'col-num-3'; break;
    case '4':
        $col_num_class = 'col-num-4'; break;
    case '5':
        $col_num_class = 'col-num-5'; break;
    case '6':
        $col_num_class = 'col-num-6'; break;
    default:
        break;
}
?>


<div class="banner" style="background-image: url(<?php echo $banner_image; ?>)">
    <div class="banner-container">
        <div class="title"><?php echo $category->name; ?></div>
        <?php if($category->description):?>
            <div class="description"><?php echo $category->description ?></div>
        <?php endif; ?>
    </div>
</div>


<div class="main-container" <?=$full_width?>>
    <div class="main-widget">
        <?php if (have_posts()) : ?>
            <div class="posts-widget <?php echo $col_num_class?>">
                <?php
                wp_reset_postdata();

                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $posts_per_page =  get_option('posts_per_page');
                $args = array(
                    'paged' => $paged,
                    'cat' => $cat,
                    'orderby' => array("menu_order" => "desc",'date' => "desc"),
                    'posts_per_page' => $posts_per_page
                );
                $query_posts = new WP_Query($args);
                ?>

                <?php while ($query_posts->have_posts()) : ?>
                    <?php $query_posts->the_post(); ?>
                    <div class="card-item">
                        <a class="card-image" href="<?php the_permalink() ?>">
                            <?php  $cur_post = get_post(); ?>
                            <img src="<?= vt_get_thumbnail_url($cur_post->ID, 'large') ?>" alt="<?php the_title(); ?>">
                        </a>
                        <div class="item-info">
                            <a class="title" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <div class="no-content">
                <img src="<?php bloginfo('template_url'); ?>/assets/images/empty.png">
                <p><?=__('暂无内容','vt')?></p>
            </div>
        <?php endif; ?>

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
    </div>


    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'default-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
