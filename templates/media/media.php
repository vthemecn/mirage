<?php
$vt_config = vt_get_config();

$cur_post_id = get_the_ID();

$author_id = get_the_author_id();
$avatar = vt_get_custom_avatar_url($author_id);

$user_center_url = home_url() . '/users/' .$post->post_author;
// $default_image = $vt_config['default_image'] ? $vt_config['default_image'] : 
//                     get_template_directory_uri() . '/assets/images/default.jpg';

// 喜欢数量
$like_counter = 0;
$sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
$res = $wpdb->get_row($wpdb->prepare($sql, [$cur_post_id, 'like']), ARRAY_A );
$like_counter = $res['num'];

?>

<div class="media-item">
    <div class="media-thumbnail">
        <a href="<?php the_permalink() ?>" target="_blank">
            <img src="<?= vt_get_thumbnail_url($cur_post_id, 'medium') ?>" alt="<?php the_title(); ?>">
        </a>
    </div>
    
    <div class="media-main">
        <div class="media-title">
            <?php if(is_sticky()): ?>
                <div class="sticky">置顶</div>
            <?php endif ?>
            <a href="<?php the_permalink() ?>" target="_blank"><?php the_title(); ?></a>
        </div>
        <div class="media-description">
            <?php echo get_the_excerpt(); ?>
        </div>
        <div class="media-meta">
            <?php if($vt_config['user_center_is_on']):?>
                <a class="author" href="<?php echo $user_center_url ?>" target="_blank">
                    <img src="<?php echo $avatar ?>">
                    <span><?php echo get_the_author_meta('nickname', $post->post_author) ?></span>
                </a>
            <?php else: ?>
                <span class="author">
                    <img src="<?php echo $avatar ?>">
                    <span><?php echo get_the_author_meta('nickname', $post->post_author) ?></span>
                </span>
            <?php endif ?>

            <span class="date">
                <i class="fa-solid fa-clock"></i><?php the_time('Y-m-d'); ?>
            </span>
            <span class="hit-counter">
                <i class="fa-solid fa-eye"></i><?php echo getPostViews(get_the_ID()); ?>
            </span>
            <span class="star_counter">
                <i class="fa-solid fa-star"></i><?= $like_counter ?>
            </span>
            <?php if($vt_config['comments_is_on']):?>
                <span>
                    <i class="fa-solid fa-comment"></i><?php echo $post->comment_count; ?>
                </span>
            <?php endif ?>
        </div>
    </div>
</div>

