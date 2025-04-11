<?php

/**
 * Template Name: 普通文章
 * Template Post Type: post
 *
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();
?>


<?php
$config = vt_get_config();

setPostViews(get_the_ID()); // 更新文章浏览次数

$vt_post_id = get_the_ID();
$vt_post = get_post($vt_post_id, ARRAY_A);
$vt_author_id = $vt_post['post_author'];
$vt_avatar = vt_get_custom_avatar_url($vt_author_id);

global $wpdb;
$current_user_id = get_current_user_id();

// 喜欢数量
$like_counter = 0;
$sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
$res = $wpdb->get_row($wpdb->prepare($sql, [$vt_post_id, 'like']), ARRAY_A );
$like_counter = $res['num'];

// 当前用户是否喜欢
$is_like = false;
if($current_user_id){
    $sql = "SELECT id AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%d AND user_id=%d AND type='like'";
    $res = $wpdb->get_row($wpdb->prepare($sql, [$vt_post_id, $current_user_id]), ARRAY_A );
    $is_like = $res ? true : false;
}

// 收藏数量
$star_counter = 0;
$sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
$res = $wpdb->get_row($wpdb->prepare($sql, [$vt_post_id, 'star']), ARRAY_A );
$star_counter = $res['num'];

// 当前用户是否收藏
$is_star = false;
if($current_user_id){
    $sql = "SELECT id AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%d AND user_id=%d AND type='star'";
    $res = $wpdb->get_row($wpdb->prepare($sql, [$vt_post_id, $current_user_id]), ARRAY_A );
    $is_star = $res ? true : false;
}

$user_center_url = home_url() . '/users/' .$post->post_author;

// 获取文章缩略图
$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($vt_post_id), 'large');
$thumbnail_image = $thumbnail ? $thumbnail[0] : get_bloginfo('template_url') . '/assets/images/user-center-banner.jpg';
?>


<div class="main-container">
    <div class="main-widget">
        <div class="article-widget">
            <div class="article-title">
                <?php the_title(); ?>
            </div>
            <div class="article-meta">
                <!-- <span class="meta author">
                    <img src="<?php echo $vt_avatar ?>" >
                    <span>
                        <a href="/users/<?php echo $post->post_author ?>" target='_blank'>
                            <?php echo get_the_author_meta('nickname', $post->post_author) ?>
                        </a>
                    </span>
                </span> -->

                <?php if($config['user_center_is_on']):?>
                    <a class="meta author" href="<?php echo $user_center_url ?>" target="_blank">
                        <img src="<?php echo $vt_avatar ?>">
                        <span><?php echo get_the_author_meta('nickname', $post->post_author) ?></span>
                    </a>
                <?php else: ?>
                    <span class="meta author">
                        <img src="<?php echo $vt_avatar ?>">
                        <span><?php echo get_the_author_meta('nickname', $post->post_author) ?></span>
                    </span>
                <?php endif ?>


                <span class="meta date">
                    <i class="fa-solid fa-clock"></i>
                    <?php the_time('Y-m-d'); ?>
                </span>
                <span class='meta hit-conuter'>
                    <i class="fa-solid fa-eye"></i>
                    <?php echo getPostViews(get_the_ID()); ?>
                </span>
                <?php if($config['comments_is_on']):?>
                    <span class='meta'>
                        <i class="fa-solid fa-comment"></i><?php echo $post->comment_count; ?>
                    </span>
                <?php endif ?>
            </div>
            
            <div class="article-content line-numbers">
                <?php /* if(get_the_excerpt()): ?>
                    <div class="content-excerpt">
                        <?php echo get_the_excerpt() ?>
                    </div>
                <?php endif */ ?>

                <?php
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        the_content();
                        // get_template_part( 'templates/content', get_post_type() );
                    }
                }
                ?>

                <?php if($config['show_copyright']):?>
                    <div class="copyright-content">
                        <i class="fa-solid fa-circle-info"></i>
                        <?php echo $config['show_copyright_text']; ?>
                    </div>
                <?php endif ?>

                <?php
                $post_categories = wp_get_post_categories($vt_post_id);
                ?>
                <div class="tags-widget">
                    <div class="icon"><i class="fa-solid fa-folder-open"></i></div>
                    <?php foreach ($post_categories as $c): ?>
                        <?php $cat = get_category($c); ?>
                        <div class="tag-item">
                            <a href="<?=esc_url(get_category_link($cat->term_id))?>" target="_blank"><?=$cat->name?></a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php
                $tags = get_the_tags($vt_post_id);
                ?>
                <?php if($tags):?>
                    <div class="tags-widget">
                        <div class="icon"><i class="fa-solid fa-tag"></i></i></div>
                        <?php foreach ($tags as $t): ?>
                            <div class="tag-item"><a href="<?=get_tag_link($t->term_id)?>" target="_blank"><?=$t->name?></a></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
            <div class="content-action">
                <div class="widget-action like <?php echo $is_like ? ' active' : '' ?>">
                    <i class="fa-solid fa-heart"></i>
                    <span><?=__('点赞','vt')?></span>
                    <span class='number'><?= $like_counter ? $like_counter : '' ?></span>
                </div>
                <?php if($config['user_center_is_on']):?>
                <div class="widget-action star <?php echo $is_star ? ' active' : '' ?>">
                    <i class="fa-solid fa-star"></i>
                    <span><?=__('收藏','vt')?></span>
                    <span class='number'><?= $star_counter ? $star_counter : '' ?></span>
                </div>
                <?php endif ?>
                <?php /*
                <div class="widget-action comment">
                    <i class="iconfont">&#xe68f;</i>
                    <span><?=__('评论</span>
                </div>
                */ ?>
                <div class="widget-action share-poster">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span><?=__('分享','vt')?></span>
                </div>
                <div class="widget-action coin">
                    <i class="fa-solid fa-coins"></i>
                    <span><?=__('打赏','vt')?></span>
                </div>

                <?php if( current_user_can( 'manage_options' ) ): ?>
                    <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit" class="widget-action">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span><?=__('编辑','vt')?></span>
                    </a>
                <?php endif ?>
            </div>
        </div><!-- .article-widget -->

        <div class="article-prev-next-nav">
            <?php
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            ?>
            
            <div class="article-nav prev-nav">
                <div class="article-link">
                    <?php if ($prev_post) : ?>
                        <a href="<?php echo get_permalink($prev_post->ID); ?>"><?php echo $prev_post->post_title; ?></a>
                    <?php else: ?>
                        <?=__('没有了','vt')?>
                    <?php endif; ?>
                </div>
                <div class="arrow-icon">
                    <i class="fa-solid fa-chevron-left"></i>
                    <?=__('上一篇','vt')?>
                </div>
            </div>

            <div class="article-nav next-nav">
                <div class="article-link">
                    <?php if ($next_post) : ?>
                        <a href="<?php echo get_permalink($next_post->ID); ?>"><?php echo $next_post->post_title; ?></a>
                    <?php else: ?>
                        <?=__('没有了','vt')?>
                    <?php endif; ?>
                </div>
                <div class="arrow-icon">
                    <?=__('下一篇','vt')?> <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
        </div>

        <?php
        if ($config['comments_is_on'] && (comments_open() || get_comments_number()) ) {
            comments_template();
        }
        ?>

    </div><!-- .main-widget -->

    <div class="sider little-widget">
        <?php if ( is_active_sidebar( 'posts-sidebar' ) ) : ?>
            <?php dynamic_sidebar( 'posts-sidebar' ); ?>
        <?php endif; ?>
    </div>
</div>


<div class="vt-dialog" id="share-dialog" class="normal-dialog">
    <div class="share-dialog-widget dialog-widget">
        <a href="javascript:;" class="close-button close"><i class="fa-solid fa-xmark"></i></a>
        <div class="share-card">
            <div class="thumbnail">
                <img src="<?= $thumbnail_image?>">    
            </div>
            <div class="share-card-body">
                <h3><?= get_the_title(); ?></h3>
                <p><?= mb_substr(get_the_excerpt(), 0, 100) ?></p>
            </div>
            <div class="share-card-footer">
                <div class="info">
                    <img src="<?= $config['site_logo'] ?>">
                    <p><?=__('识别右侧二维码阅读全文','vt')?></p>
                </div>
                <div id="qrcode"></div>
            </div>         
        </div>
        <div class="top-layer" class="">生成中...</div>
        <img src="" id='poster' class="hide" />
    </div>
</div>


<?php
$qrcode_image = $config['qrcode_image'] ? $config['qrcode_image'] : get_template_directory_uri() . '/assets/images/default.jpg';
?>
<div class="vt-dialog" id="coin-dialog" class="normal-dialog">
    <div class="coin-dialog-widget dialog-widget">
        <a href="javascript:;" class="close-button close">
            <i class="fa-solid fa-xmark"></i>
        </a>
        <div class="coin-widget">
            <img src="<?= $qrcode_image?>">
        </div>
        <div class="coin-info"><?=$config['qrcode_title']?></div>
    </div>
</div>


<input type="hidden" name="wp_create_nonce" value="<?php echo wp_create_nonce('wp_rest'); ?>">
<input type="hidden" name="post_id" value="<?php echo $vt_post_id ?>">
<input type="hidden" name="post_url" value="<?= get_permalink() ?>">
<input type="hidden" name="current_user_id" value="<?= $current_user->ID?>" />

<?php if($config['highlight_is_on']):?>
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/assets/lib/prism/prism.css">
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/assets/lib/prism/prism.js"></script>
<?php endif ?>


<?php get_footer(); ?>
