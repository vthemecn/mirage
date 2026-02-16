<?php
/**
 * 文章列表页 按类别请求帖子时，将使用类别模板
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();

$category = get_term($cat);

$full_width = get_term_meta( $cat, 'vt_full_width', true );
$full_width_class = $full_width==1 ? 'full-width' : '';

$vt_list_type = get_term_meta( $cat, 'vt_list_type', true );
$vt_list_type_class = '';
if($vt_list_type > 0){
    $vt_columns_num = $config['vt_columns'];
    $vt_list_type_class = 'vt-column vt-column-' . $vt_columns_num;
}

// 获取分类第一篇文章的缩略图或者图片
$banner_image = get_bloginfo('template_url') . '/assets/images/banner.jpg';
$args = array(
    'cat' => $cat,
    'orderby' => array("menu_order" => "desc",'date' => "desc"),
    'posts_per_page'=>1
);
$query_posts = new WP_Query($args);
if($query_posts->posts){
    $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($query_posts->posts[0]->ID), 'medium');
    $banner_image = $thumbnail ? $thumbnail[0] : $banner_image;
}

?>


<div class="banner" style="background-image: url(<?php echo $banner_image; ?>)">
    <div class="banner-container">
        <div class="title"><?php echo $category->name; ?></div>
        <?php if($category->description): ?>
            <div class="description"><?php echo $category->description ?></div>
        <?php endif; ?>
    </div>
</div>


<div class="main-container" <?= $full_width_class; ?>>
    <div class="main-widget">
        <?php
        // 获取基础参数
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $posts_per_page = get_option('posts_per_page');
        
        // 单一主循环 + ignore_sticky_posts => 1
        // 使用单一查询获取所有文章，并通过设置ignore_sticky_posts => 1来忽略置顶文章的特殊排序行为
        $main_args = array(
            'cat' => $cat,
            'posts_per_page' => $posts_per_page,
            'ignore_sticky_posts' => 1,  // 忽略置顶文章的特殊排序行为
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => $paged
        );

        $main_query = new WP_Query($main_args);
        ?>

        <div class="media-list <?= $vt_list_type_class; ?>">
            <?php 
            if ($main_query->have_posts()) {
                while ($main_query->have_posts()) { 
                    $main_query->the_post(); 
                    $vt_post_type = get_post_meta(get_the_ID(), 'vt_post_type', true);
                    get_template_part('templates/card');
                }
            } else if ($paged == 1) {
            ?>
                <div class="no-content">
                    <img src="<?php bloginfo('template_url'); ?>/assets/images/empty.png">
                    <p><?php echo __('暂无内容','vt'); ?></p>
                </div>
            <?php 
            }
            wp_reset_postdata();
            ?>  
        </div>

        <?php
        // 使用 WordPress 原生分页函数
        the_posts_pagination(array(
            'mid_size' => 3,
            'prev_text' => '<',
            'next_text' => '>',
            'screen_reader_text' => ' ',
            'aria_label' => "",
            'total' => $main_query->max_num_pages,
            'current' => $paged
        ));
        ?>
        
    </div>

    <?php if(!$full_width):?>
    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'default-sidebar' ) ) { ?>
            <?php dynamic_sidebar( 'default-sidebar' ); ?>
        <?php } ?>
    </div>
    <?php endif; ?>
</div>


<?php get_footer(); ?>