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

set_post_views(get_the_ID()); // 更新文章浏览次数

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
// 为未登录用户生成唯一标识
$user_identifier = $current_user_id > 0 ? $current_user_id : 'ip_' . md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
$existing = $wpdb->get_var($wpdb->prepare(
    "SELECT id FROM {$wpdb->prefix}vt_star WHERE user_id = %s AND object_id = %d AND type = 'like'",
    $user_identifier, $vt_post_id
));
$is_like = $existing ? true : $is_like;

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

$user_profile_url = home_url() . '/user/' .$post->post_author;

// 获取文章缩略图
$thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($vt_post_id), 'large');
$thumbnail_image = $thumbnail ? $thumbnail[0] : get_bloginfo('template_url') . '/assets/images/banner.jpg';
?>


<div class="main-container">
    <div class="main-widget">
        <div class="article-widget">
            <div class="article-title">
                <?php the_title(); ?>
            </div>

            <div class="article-box">
                <?php if(is_captain_active()):?>
                    <a class="meta-item author" href="<?php echo $user_profile_url ?>" target="_blank">
                        <div class="avatar"><img src="<?= $vt_avatar ?>"></div>
                        <span><?php echo get_the_author_meta('nickname', $post->post_author) ?></span>
                    </a>
                <?php else: ?>
                    <div class="meta-item author">
                        <div class="avatar"><img src="<?= $vt_avatar ?>"></div>
                        <span><?php echo get_the_author_meta('nickname', $post->post_author) ?></span>
                    </div>
                <?php endif ?>
                
                <div class="meta-item date">
                    <i class="fa-solid fa-clock"></i><?= vt_get_time(get_the_time('Y-m-d H:i:s')) ?>
                </div>

                <?php
                $post_categories = wp_get_post_categories($vt_post_id);
                ?>
                <div class='meta-item category'>
                    <i class="fa-solid fa-folder"></i><?php foreach ($post_categories as $c): ?><?php $cat = get_category($c); ?><a href="<?=esc_url(get_category_link($cat->term_id))?>" class="category-meta-item"  target="_blank"><?=$cat->name?></a>
                    <?php endforeach; ?>
                </div>

                <div class='meta-item hit-conuter'>
                    <i class="fa-solid fa-eye"></i><?php echo get_post_views(get_the_ID()); ?>
                </div>

                <div class='meta-item comment-counter'>
                    <i class="fa-solid fa-comment"></i><?php echo $post->comment_count; ?>
                </div>
            </div>
            
            <div class="article-content content-style line-numbers">
                <?php /* if(get_the_excerpt()): ?>
                    <div class="content-excerpt">
                        <?php echo get_the_excerpt() ?>
                    </div>
                <?php endif */ ?>
                
                <?php
                $content = get_the_content();
                $toc = create_table_of_contents($content);
                ?>
                <?php if($config['toc_is_on'] && $toc):?>                    
                    <div class="toc-widget show">
                        <div class="toc-header">
                            <span>目录</span>
                            <div class="toc-button">
                                <span>展开<i class="fa-regular fa-chevron-down"></i></span>
                                <span>收起<i class="fa-regular fa-chevron-up"></i></span>
                            </div>
                        </div>
                        <div class="toc-body">
                            <?=$toc?>
                        </div>
                    </div>
                <?php endif; ?>
                

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
                    <span><?= $is_like ? __('Remove like','vt') : __('Like','vt')?></span>
                    <span class='number'><?= $like_counter ? $like_counter : '' ?></span>
                </div>

                <?php if(is_captain_active()):?>
                <div class="widget-action star <?php echo $is_star ? ' active' : '' ?>">
                    <i class="fa-solid fa-star"></i>
                    <span><?= $is_star ? __('Remove star','vt') : __('Star','vt')?></span>
                    <span class='number'><?= $star_counter ? $star_counter : '' ?></span>
                </div>
                <?php endif; ?>
               
                <div class="widget-action share-poster">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span><?=__('Share','vt')?></span>
                </div>

                <div class="widget-action coin">
                    <i class="fa-solid fa-coins"></i>
                    <span><?=__('Donate','vt')?></span>
                </div>

                <?php if( current_user_can( 'manage_options' ) ): ?>
                <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit" class="widget-action">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span><?=__('Edit','vt')?></span>
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
                        <?=__('No more','vt')?>
                    <?php endif; ?>
                </div>
                <div class="arrow-icon">
                    <i class="fa-solid fa-chevron-left"></i>
                    <?=__('Previous','vt')?>
                </div>
            </div>

            <div class="article-nav next-nav">
                <div class="article-link">
                    <?php if ($next_post) : ?>
                        <a href="<?php echo get_permalink($next_post->ID); ?>"><?php echo $next_post->post_title; ?></a>
                    <?php else: ?>
                        <?=__('No more','vt')?>
                    <?php endif; ?>
                </div>
                <div class="arrow-icon">
                    <?=__('Next','vt')?> <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
        </div>

        <?php
        if ( comments_open() || get_comments_number() ) {
            comments_template();
        }
        ?>


        <?php /* 推荐模块 */ ?>
        <div class="related-posts-widget">
            <div class="related-header">
                <h3><?=__('Recommendation','vt')?></h3>
            </div>
            <div class="widget-content">
                <?php
                // 获取当前文章的分类和标签
                $post_categories = wp_get_post_categories($vt_post_id);
                $post_tags = wp_get_post_tags($vt_post_id);
                
                // 构建查询参数
                $related_args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 6,
                    'post__not_in' => array($vt_post_id),
                    'orderby' => 'rand'
                );
                
                // 如果有分类或标签，则根据它们查找相关内容
                if (!empty($post_categories) || !empty($post_tags)) {
                    $tax_query = array('relation' => 'OR');
                    
                    if (!empty($post_categories)) {
                        $tax_query[] = array(
                            'taxonomy' => 'category',
                            'field' => 'term_id',
                            'terms' => $post_categories
                        );
                    }
                    
                    if (!empty($post_tags)) {
                        $tax_query[] = array(
                            'taxonomy' => 'post_tag',
                            'field' => 'term_id',
                            'terms' => wp_list_pluck($post_tags, 'term_id')
                        );
                    }
                    
                    $related_args['tax_query'] = $tax_query;
                }
                
                $related_query = new WP_Query($related_args);    
                if ($related_query->have_posts()) :
                ?>
                    <div class="related-posts-list">
                        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                            <!-- 使用 templates/card.php 模板显示相关文章 -->
                            <?php get_template_part('templates/card'); ?>
                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <div class="no-related-posts">
                        <?=__('No related articles','vt')?>
                    </div>
                <?php endif; ?>
                
                <?php wp_reset_postdata(); ?>
            </div>
        </div>

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
                    <p><?=__('Scan QR code','vt')?></p>
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
        <a href="javascript:;" class="close-button close">
            <i class="fa-solid fa-xmark"></i>
        </a>
        <div class="coin-widget-list">
            <?php foreach($config['qrcode_items'] as $item):?>
                <div class="coin-widget">
                    <img src="<?= $item['image']?>">
                    <div class="coin-info"><?= $item['title']?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<script type="text/javascript">
var post_object = {
    post_url: "<?php echo get_permalink(); ?>",
    post_id: "<?= get_the_ID(); ?>",
};
</script>

<?php if($config['highlight_is_on']):?>
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/assets/lib/prism/prism.css">
    <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/assets/lib/prism/prism.js"></script>
<?php endif ?>


<?php get_footer(); ?>
