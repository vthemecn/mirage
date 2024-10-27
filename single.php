<?php

/**
 * Template Name: 普通文章
 * Template Post Type: post
 *
 * @author: soushenji <soushenji@qq.com>
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
                    <i class="iconfont">&#xe76d;</i>
                    <?php the_time('Y-m-d'); ?>
                </span>
                <span class='meta hit-conuter'>
                    <i class="iconfont">&#xe752;</i>
                    <?php echo getPostViews(get_the_ID()); ?>
                </span>
                <?php if($config['comments_is_on']):?>
                    <span class='meta'>
                        <i class="iconfont">&#xe8a6;</i><?php echo $post->comment_count; ?>
                    </span>
                <?php endif ?>
            </div>
            
            <div class="article-content">
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
                        <i class="iconfont">&#xe788;</i>
                        <?php echo $config['show_copyright_text']; ?>
                    </div>
                <?php endif ?>

                <?php
                $tags = get_the_tags( $vt_post_id );
                ?>
                <?php if($tags):?>
                    <div class="article-tags-widget">
                        <?php foreach($tags as $k=>$v):?>
                            <a href="javascript:;" class="tag-item"><?php echo $v->name?></a>
                        <?php endforeach?>
                    </div>
                <?php endif ?>

            </div>
            <div class="content-action">
                <div class="widget-action like <?php echo $is_like ? ' active' : '' ?>">
                    <i class="iconfont">&#xe663;</i>
                    <span>点赞</span>
                    <span class='number'><?php echo $like_counter ?></span>
                </div>
                <div class="widget-action star <?php echo $is_star ? ' active' : '' ?>">
                    <i class="iconfont">&#xe882;</i>
                    <span>收藏</span>
                    <span class='number'><?php echo $star_counter ?></span>
                </div>
                <?php /*
                <div class="widget-action comment">
                    <i class="iconfont">&#xe68f;</i>
                    <span>评论</span>
                </div>
                */ ?>
                <div class="widget-action share-poster">
                    <i class="iconfont">&#xe691;</i>
                    <span>分享</span>
                </div>
                <div class="widget-action coin">
                    <i class="iconfont">&#xe88d;</i>
                    <span>打赏</span>
                </div>

                <?php if( current_user_can( 'manage_options' ) ): ?>
                    <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit" class="widget-action">
                        <i class="iconfont">&#xe77a;</i> <span>编辑</span>
                    </a>
                <?php endif ?>
            </div>
        </div><!-- .article-widget -->

        <div class="article-prev-next-nav">
            <?php
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            ?>
            <?php if ($prev_post) : ?>
                <div class="article-nav prev-nav">
                    <div class="article-link">
                        <a href="<?php echo get_permalink($prev_post->ID); ?>"><?php echo $prev_post->post_title; ?></a>
                    </div>
                    <div class="arrow-icon"><i class="iconfont">&#xe749;</i>上一篇</div>
                </div>
            <?php endif; ?>

            <?php if ($next_post) : ?>
                <div class="article-nav next-nav">
                    <div class="article-link">
                        <a href="<?php echo get_permalink($next_post->ID); ?>"><?php echo $next_post->post_title; ?></a>
                    </div>
                    <div class="arrow-icon">下一篇<i class="iconfont">&#xe748;</i></div>
                </div>
            <?php endif; ?>
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
        <a href="javascript:;" class="close-button close"><i class="iconfont">&#xe75e;</i></a>
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
                    <p>识别右侧二维码阅读全文</p>
                </div>
                <div id="qrcode"></div>
            </div>         
        </div>
        <div class="top-layer" class="">生成中...</div>
        <img src="" id='poster' class="hide" />
    </div>
</div>


<div class="vt-dialog" id="coin-dialog" class="normal-dialog">
    <div class="coin-dialog-widget dialog-widget">
        <a href="javascript:;" class="close-button close"><i class="iconfont">&#xe75e;</i></a>
        <div class="coin-widget">
            <img src="<?= $config['qrcode_image']?>">
        </div>
        <div class="coin-info">真诚赞赏，手留余香</div>
    </div>
</div>


<input type="hidden" name="wp_create_nonce" value="<?php echo wp_create_nonce('wp_rest'); ?>">
<input type="hidden" name="post_id" value="<?php echo $vt_post_id ?>">
<input type="hidden" name="post_url" value="<?= get_permalink() ?>">

<?php if($config['highlight_is_on']):?>
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/assets/js/lib/highlight/styles/stackoverflow-light.min.css">
<script src="<?php bloginfo('template_url'); ?>/assets/js/lib/highlight/highlight.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('pre').forEach((el) => {
            hljs.highlightElement(el);
        });
    });
</script>
<?php endif ?>

<?php get_footer(); ?>
