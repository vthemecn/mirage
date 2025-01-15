<?php

/**
 * 标签列表页
 * 
 * @author: vtheme
 * @link http://vtheme.cn
 */

get_header();
?>


<?php
$config = vt_get_config();

// $category = get_term($cat);
$tagName = single_tag_title('',false);
$tagObject = get_term_by('name',$tagName,'post_tag');
$tagID = $tagObject->term_id;

// p($tagObject);

$term_full_width = get_term_meta($cat, 'full_width', true);
$full_width = '';
if($term_full_width == 0){ // 分类跟随系统设置
    $full_width = $config['full_width']==1 ? 'full-width' : '';
}
if($term_full_width == 1) {
    $full_width = 'full-width';
}

// 获取分类第一篇文章的缩略图或者图片
$banner_image = get_bloginfo('template_url') . '/assets/images/user-center-banner.jpg';
$args = array(
    'tag__in' => array($tagID),
    'orderby' => array("menu_order" => "desc",'date' => "desc"),
    'posts_per_page'=>1
);
$query_posts = new WP_Query($args);
if($query_posts->posts){
    $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($query_posts->posts[0]->ID), 'medium');
    $banner_image = $thumbnail ? $thumbnail[0] : $banner_image;
}

// 列数
$col_num_class = $config['list_cards_col'];
?>


<div class="banner" style="background-image: url(<?php echo $banner_image; ?>)">
    <div class="banner-container">
        <div class="title"><?php echo $tagName; ?></div>
        <?php if(0 && $tagName):?>
            <div class="description"><?= $tagName ?></div>
        <?php endif; ?>
    </div>
</div>


<div class="main-container" <?=$full_width?> >
    <div class="main-widget">
        <?php if (have_posts()) : ?>
            <div class="posts-widget <?php echo $col_num_class?>">
                <?php
                wp_reset_postdata();

                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $posts_per_page =  get_option('posts_per_page');
                $args = array(
                    'paged' => $paged,
                    'tag__in' => array($tagID),
                    'orderby' => array("menu_order" => "desc",'date' => "desc"),
                    'posts_per_page' => $posts_per_page
                );
                $query_posts = new WP_Query($args);
                ?>

                <?php while ($query_posts->have_posts()) : ?>
                    <?php $query_posts->the_post(); ?>
                    <?php get_template_part( 'templates/media/card' ); ?>
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
        <?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'default-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
