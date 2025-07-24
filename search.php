<?php
/**
 * 文章列表页 按类别请求帖子时，将使用类别模板
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */
get_header();

/**
 * 分类页 Banner
 */
$vt_options = vt_get_config();

// 获取分类第一篇文章的缩略图或者图片
$banner_image = get_bloginfo('template_url') . '/assets/images/banner.jpg';


// $posts_per_page =  get_option('posts_per_page');
// $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
// $args = array(
//     'paged' => $paged,
//     'cat' => $cat,
//     'orderby' => array("menu_order" => "desc",'date' => "desc"),
//     'showposts' => $posts_per_page
// );
// query_posts($args);
?>


<div class="banner" style="background-image: url(<?php echo $banner_image; ?>)">
    <div class="banner-container">
        <div class="title"><?= __('搜索','vt')?></div>
        <div class="description">
            <?= __('搜索','vt')?>
            "<?php echo get_search_query(); ?>"
            <?= __('的相关内容','vt')?>
        </div>
    </div>
</div>


<div class="main-container">
    <div class="main-widget">
        <?php if (have_posts()) : ?>
            <div class="media-list">
                <?php while (have_posts()) : the_post(); ?>
                    <?php $vt_post_type = get_post_meta( $post->ID, 'vt_post_type', true ); ?>
                    <?php get_template_part( 'templates/media/media' ); ?>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="no-content">
                <img src="<?php bloginfo('template_url'); ?>/assets/images/empty.png">
                <p><?=__('暂无内容','vt')?></p>
            </div>
        <?php endif; ?>

        <?php
        the_posts_pagination(array(
            'mid_size' => 3,
            'prev_text' => '<',
            'next_text' => '>',
            'screen_reader_text' => ' ',
            'aria_label' => "",
        ));
        ?>
    </div>


    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'main-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'main-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>


<?php get_footer(); ?>
