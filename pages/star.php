<?php
/**
 * `/inc/rewrite.php` 所引用的文件
 */

global $wp_query;
global $current_user;
global $wp;

$param_user_id = $wp_query->query_vars['user_id'];
$param_user = get_user_by('id', $param_user_id);

global $wpdb;

// 每页显示条数
$pageSize = 6;
// 数据总数
$sql = "SELECT COUNT(s.id) FROM {$wpdb->prefix}vt_star AS s
        WHERE s.type='star' AND s.user_id=%d";
$count_query = $wpdb->prepare($sql, $param_user_id);
$counter = $wpdb->get_var($count_query);

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = $pageSize * ($page - 1);

$sql = "SELECT s.id AS star_id, s.user_id, s.object_id, p.post_title FROM {$wpdb->prefix}vt_star AS s
        LEFT JOIN {$wpdb->prefix}posts AS p ON p.ID=s.object_id 
        WHERE s.type='star' AND s.user_id=%d
        ORDER BY s.id DESC LIMIT %d OFFSET %d";
$list = $wpdb->get_results($wpdb->prepare($sql, $param_user_id, $pageSize, $start));

foreach($list as $k=>$v){
    $list[$k]->nickname = get_user_meta($v->user_id, 'nickname', true);
    $list[$k]->avatar = vt_get_custom_avatar_url($v->user_id);
    $list[$k]->user_id = $v->user_id;
    $list[$k]->post = get_post($v->object_id);
    $list[$k]->hit_counter = get_post_meta($v->object_id, 'post_views_count', true);
    $list[$k]->thumbnail = get_the_post_thumbnail_url($v->object_id, 'thumbnail');
}

// 分页类
require_once get_template_directory() . '/inc/paginator/Paginator.php';
$vt_page = new \Paginator($counter, $pageSize);
$vt_page->setQueryField(['page'=>'page']);
$vt_page->pagerCount = 6; // 显示页数
$vt_page->prevText = '上一页';
$vt_page->nextText = '下一页';

get_header();
?>


<div class="user-center-container">
    <?php require_once get_template_directory() . '/templates/users/sider.php'; ?>

    <div class="user-center-panel">
        <h3>我的收藏</h3>

        <div class="user-likes-container">
            <?php if(!$list): ?>
                <div class="user-no-content">
                    <img src="<?php bloginfo('template_url'); ?>/assets/images/empty.png">
                    <span>还没有收藏</span>
                </div>
            <?php endif ?>
            
            <div class="user-likes-list">
            <?php foreach($list as $k=>$v): ?>
                <div class="user-like-item">
                    <div class="like-item-header">
                        <div class="like-item-title">
                            <a href="<?php echo get_permalink($v->object_id) ?>">
                                <?php  echo $v->post_title ?>
                            </a>
                        </div>
                        <?php if ($v->thumbnail) { ?>
                        <div class="like-item-thumb">
                            <a href="<?php echo get_permalink($v->object_id) ?>">
                                <img src="<?php echo $v->thumbnail ?>" alt="<?php echo $v->post_title ?>">
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                    
                    <div class="like-item-body">
                        <div class="like-item-excerpt">
                            <?php echo get_the_excerpt($v->post); ?>
                        </div>
                        
                        <div class="like-item-meta">
                            <span class="like-item-date">
                                <i class="fa-regular fa-calendar"></i>
                                <?php echo wp_date('Y-m-d', strtotime($v->post->post_date) ); ?>
                            </span>
                            <span class="like-item-views">
                                <i class="fa-regular fa-eye"></i>
                                <?php echo $v->hit_counter ?>
                            </span>
                            <span class="like-item-author">
                                <img src="<?php echo $v->avatar ?>" alt="<?php echo $v->nickname ?>">
                                <a href="/users/<?php echo $v->user_id ?>" target='_blank'><?php echo $v->nickname ?></a>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
        
        <?php if($list): ?>
        <div class="pagination">
            <?php echo $vt_page->links(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>


<input type="hidden" name="wp_create_nonce" value="<?php echo wp_create_nonce('wp_rest'); ?>">


<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/inc/page/css/pagination.css">

<?php get_footer(); ?>
