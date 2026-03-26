<?php
$config = vt_get_config();

// 获取热门文章数据
$hot_posts = array();

// 优先使用文章 ID 列表
$hot_post_ids = isset($config['hot_post_ids']) ? trim($config['hot_post_ids']) : '';

if (!empty($hot_post_ids)) {
    // 解析 ID 列表
    $post_ids = array_map('intval', explode(',', $hot_post_ids));
    $post_ids = array_filter($post_ids); // 移除空值
    
    if (!empty($post_ids)) {
        $args = array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'post__in'       => $post_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
            'ignore_sticky_posts' => true
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $hot_posts[] = array(
                    'id'        => get_the_ID(),
                    'title'     => get_the_title(),
                    'link'      => get_permalink(),
                    'thumbnail' => vt_get_thumbnail_url(get_the_ID(), 'medium')
                );
            }
            wp_reset_postdata();
        }
    }
} else {
    // 如果 ID 列表为空，则根据分类获取最新文章
    $hot_cat_id = vt_get_config('hot_cat_id');

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        'ignore_sticky_posts' => true
    );
    
    if ($hot_cat_id) {
        $args['cat'] = $hot_cat_id;
    }
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $hot_posts[] = array(
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'link'      => get_permalink(),
                'thumbnail' => vt_get_thumbnail_url(get_the_ID(), 'medium')
            );
        }
        wp_reset_postdata();
    }
}
?>

<div class="main-container">
    <div class="hot-widget">
        <div class="header">
            <div class="title"><?=$config['hot_title']?></div>
        </div>
        <div class="body">
            <div class="hot-list">
                <?php foreach($hot_posts as $post):?>
                    <a class="hot-item" href="<?=$post['link']?>" target="_blank">
                        <div class="hot-thumbnail">
                            <img src="<?=$post['thumbnail']?>" alt="<?=$post['title']?>">
                        </div>
                        <div class="hot-mask"></div>
                        <div class="hot-title"><?=$post['title']?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>