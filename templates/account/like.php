<?php
/**
 * 页面：/account/edit
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

// 获取全局变量
global $wp_query;
global $current_user;
global $wpdb;
global $post;


// 使用当前登录用户
$param_user_id = get_current_user_id();
$param_user = get_user_by('id', $param_user_id);

// 获取用户性别
$user_meta = get_user_meta($param_user_id);
$gender = isset($user_meta['gender'][0]) ? $user_meta['gender'][0] : '0';

// 获取当前用户会员等级
$param_user_level = get_user_meta($param_user->ID, 'membership_level', true);
$param_user_level = $param_user_level !=="" ? $param_user_level : 'free';

$levels = array(
        'free' => __('Regular User', 'vt'),
        'vip' => __('VIP User', 'vt'),
        'svip' => __('SVIP User', 'vt')
    );
$level_text = $levels[$param_user_level];

?>


<?php
global $wpdb;

$param_user_id = get_current_user_id();
$param_user = get_user_by('id', $param_user_id);

// 每页显示条数
$pageSize = 5;
// 数据总数
$sql = "SELECT COUNT(s.id) FROM {$wpdb->prefix}vt_star AS s
        WHERE s.type='like' AND s.user_id=%d";
$count_query = $wpdb->prepare($sql, $param_user_id);
$counter = $wpdb->get_var($count_query);

$page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
$start = $pageSize * ($page - 1);

$sql = "SELECT s.id AS star_id, s.user_id, s.object_id, p.post_title, p.post_date
            FROM {$wpdb->prefix}vt_star AS s
        LEFT JOIN {$wpdb->prefix}posts AS p ON p.ID=s.object_id 
        WHERE s.type='like' AND s.user_id=%d
        ORDER BY s.id DESC LIMIT %d OFFSET %d";
$list = $wpdb->get_results($wpdb->prepare($sql, $param_user_id, $pageSize, $start));

// foreach($list as $k=>$v){
//     $list[$k]->nickname = get_user_meta($v->user_id, 'nickname', true);
//     $list[$k]->avatar = vt_get_custom_avatar_url($v->user_id);
//     $list[$k]->user_id = $v->user_id;
//     $list[$k]->post_author = $v->user_id;
//     $list[$k]->post = get_post($v->object_id);
//     $list[$k]->hit_counter = get_post_meta($v->object_id, 'post_views_count', true);
//     $list[$k]->thumbnail = get_the_post_thumbnail_url($v->object_id, 'thumbnail');
// }

// 计算总页数
$max_num_pages = ceil($counter / $pageSize);
?>


<div class="vt-account-banner">
    <div class="title"><?=__('Account','vt')?></div>
</div>


<?php require_once THEME_DIR . '/templates/account/tab.php'; ?>


<div class="vt-account-container">
    <div class="vt-account-wrapper">
        <div class="vt-account-panel">
            <div class="vt-account-panel-title"><?= __('Like', 'vt'); ?></div>
            
            <?php foreach($list as $k=>$v): ?>
                <?php
                $post = get_post($v->object_id);
                setup_postdata($post);
                require THEME_DIR . '/templates/card.php';
                ?>
            <?php endforeach; ?>


            <?php if($list && $max_num_pages > 1): ?>
            <div class="vt-captain-pagination pagination-container">
                <?php
                // 构造查询参数格式的 base URL
                $base = remove_query_arg('paged');
                if (strpos($base, '?') === false) {
                    $base .= '?paged=%#%';
                } else {
                    $base .= '&paged=%#%';
                }

                echo paginate_links([
                    'base'      => $base,
                    'current'   => $page,
                    'total'     => $max_num_pages,
                    'prev_text' => '<i class="fas fa-chevron-left"></i>',
                    'next_text' => '<i class="fas fa-chevron-right"></i>',
                    'type'      => 'list',
                    'end_size'  => 2,
                    'mid_size'  => 2,
                ]);
                ?>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>


<?php
$account_ajax_object = array(
    'update_user_api_url' => home_url('/wp-json/vtheme/v1/users/'.$param_user_id)
);
?>

<script type="text/javascript">
var account_ajax_object = <?php echo json_encode($account_ajax_object); ?>;
console.log('account_ajax_object', account_ajax_object);
</script>


<?php get_footer(); ?>
