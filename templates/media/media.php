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
    
    <div class="media-body">
        <div class="media-title">
            <?php if(is_sticky()): ?>
                <div class="sticky">置顶</div>
            <?php endif ?>
            <a href="<?php the_permalink() ?>" target="_blank"><?php the_title(); ?></a>
        </div>
        <div class="media-description">
            <?php echo get_the_excerpt(); ?>
        </div>
        <div class="media-box">
            <?php if($vt_config['user_center_is_on']):?>
                <a class="meta-item author" href="<?php echo $user_center_url ?>" target="_blank">
                    <div class="avatar"><img src="<?php echo $avatar ?>"></div>
                    <span><?php echo get_the_author_meta('nickname', $post->post_author) ?></span>
                </a>
            <?php else: ?>
                <div class="meta-item author">
                    <div class="avatar"><img src="<?php echo $avatar ?>"></div>
                    <span><?php echo get_the_author_meta('nickname', $post->post_author) ?></span>
                </div>
            <?php endif ?>
            <div class="meta-item date">
                <i class="fa-solid fa-clock"></i><?= vt_get_time(get_the_time('Y-m-d H:i:s')) ?>
            </div>
        </div>
        
    </div>
</div>

