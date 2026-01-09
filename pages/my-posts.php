<?php
/**
 * 用户文章列表页面
 */
global $wp_query;
global $current_user;
global $wp;

$param_user_id = $wp_query->query_vars['user_id'];

$param_user = get_user_by('id', $param_user_id);

$has_auth = false;
if( $param_user_id ==  $current_user->ID || current_user_can( 'manage_options' ) ){
    $has_auth = true;
}

// 如果用户没有权限，跳转到首页
if (!$has_auth) {
    wp_redirect(home_url());
    exit;
}

// 分页参数
$posts_per_page = 10; // 每页显示10篇文章
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $posts_per_page;

// 获取用户的文章总数
$args = array(
    'author' => $param_user_id,
    'post_status' => array('publish', 'private', 'draft', 'pending'),
    'post_type' => 'post',
    'numberposts' => -1
);
$total_posts = count(get_posts($args));

// 获取用户的文章（带分页）
$my_posts = get_posts(array(
    'author' => $param_user_id,
    'posts_per_page' => $posts_per_page,
    'offset' => $offset,
    'post_status' => array('publish', 'private', 'draft', 'pending'),
    'post_type' => 'post'
));

// 初始化分页器
require_once get_template_directory() . '/inc/paginator/Paginator.php';
$paginator = new \Paginator($total_posts, $posts_per_page);

get_header();
?>

<div class="user-center-container">
    <?php require_once get_template_directory() . '/templates/users/sider.php'; ?>

    <div class="user-center-panel">
        <div class="my-posts-container">
            <h3>我的文章</h3>
            
            <?php if (empty($my_posts)): ?>
                <p>暂无文章</p>
            <?php else: ?>
                <table class="table my-posts-table">
                    <thead>
                        <tr>
                            <th>标题</th>
                            <th>分类</th>
                            <th>状态</th>
                            <th>发布时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($my_posts as $post): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo get_permalink($post->ID); ?>" target="_blank">
                                        <?php echo esc_html($post->post_title); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $categories = get_the_category($post->ID);
                                    if (!empty($categories)) {
                                        echo esc_html($categories[0]->name);
                                    } else {
                                        echo '未分类';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    switch ($post->post_status) {
                                        case 'publish':
                                            echo '<span class="status status-publish">已发布</span>';
                                            break;
                                        case 'private':
                                            echo '<span class="status status-private">待审核</span>';
                                            break;
                                        case 'draft':
                                            echo '<span class="status status-draft">草稿</span>';
                                            break;
                                        case 'pending':
                                            echo '<span class="status status-pending">待审核</span>';
                                            break;
                                        default:
                                            echo '<span class="status status-unknown">' . $post->post_status . '</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo get_the_date('Y-m-d H:i', $post->ID); ?></td>
                                <td>
                                    <a href="<?php echo home_url('/users/' . $param_user_id . '/edit-post/' . $post->ID); ?>" class="btn btn-sm btn-primary">编辑</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- 分页 -->
                <div class="pagination-container">
                    <?php 
                        // 设置查询参数，确保URL正确
                        $paginator->setQueryParams(array(
                            'user_id' => $param_user_id,
                            'vt_page' => 'my-posts'
                        ));
                        echo $paginator->links();
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.my-posts-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.my-posts-table th,
.my-posts-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.my-posts-table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
}

.status-publish {
    background-color: #d4edda;
    color: #155724;
}

.status-private, .status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-draft {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-unknown {
    background-color: #f8d7da;
    color: #721c24;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.85em;
}

.pagination-container {
    margin-top: 20px;
    text-align: center;
}

.m-pagination {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.m-pager {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 10px 0;
}

.m-pager-number {
    margin: 0 5px;
}

.m-pager-number a,
.m-pager-number span {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    border: 1px solid #ddd;
    color: #333;
}

.m-pager-number.active a,
.m-pager-number.active span {
    background-color: #007cba;
    color: white;
    border-color: #007cba;
}

.m-pagination-prev,
.m-pagination-next {
    margin: 0 10px;
}
</style>

<?php get_footer(); ?>