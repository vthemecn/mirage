<?php
/**
 * 管理员审核用户提交的文章功能
 */

// 添加菜单项到WordPress管理后台
function add_user_posts_admin_menu() {
    add_submenu_page(
        'edit.php',
        '用户提交的文章',
        '用户提交',
        'manage_options',
        'user-submitted-posts',
        'user_submitted_posts_page'
    );
}
add_action('admin_menu', 'add_user_posts_admin_menu');

// 显示用户提交的文章列表页面
function user_submitted_posts_page() {
    // 获取所有状态为private的文章（用户提交的文章）
    $args = array(
        'post_type' => 'post',
        'post_status' => 'private',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_submitted_by_user',
                'compare' => 'EXISTS'
            )
        )
    );
    
    $posts = get_posts($args);
    
    if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['post_id'])) {
        $post_id = intval($_GET['post_id']);
        $post = get_post($post_id);
        
        if ($post && $post->post_status == 'private') {
            // 更新文章状态为publish
            wp_update_post(array(
                'ID' => $post_id,
                'post_status' => 'publish'
            ));
            
            echo '<div class="notice notice-success is-dismissible"><p>文章已批准发布！</p></div>';
        }
    }
    ?>
    
    <div class="wrap">
        <h1>用户提交的文章</h1>
        <p>这些是用户提交等待审核的文章，状态为私有(private)，普通用户无法看到，需要管理员审核后才能发布。</p>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>标题</th>
                    <th>作者</th>
                    <th>分类</th>
                    <th>提交时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td>
                            <strong><a href="<?php echo get_edit_post_link($post->ID); ?>"><?php echo esc_html($post->post_title); ?></a></strong>
                            <br>
                            <small><?php echo wp_trim_words($post->post_content, 20); ?></small>
                        </td>
                        <td>
                            <?php 
                            $author = get_user_by('id', $post->post_author);
                            echo $author ? esc_html($author->display_name) : '未知用户';
                            ?>
                        </td>
                        <td>
                            <?php
                            $categories = get_the_category($post->ID);
                            if (!empty($categories)) {
                                $cat_names = array_map(function($cat) {
                                    return $cat->name;
                                }, $categories);
                                echo implode(', ', $cat_names);
                            } else {
                                echo '未分类';
                            }
                            ?>
                        </td>
                        <td><?php echo get_the_date('Y-m-d H:i:s', $post->ID); ?></td>
                        <td>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=user-submitted-posts&action=approve&post_id=' . $post->ID), 'approve_post_' . $post->ID); ?>" class="button button-primary">批准发布</a>
                            <a href="<?php echo get_edit_post_link($post->ID); ?>" class="button">编辑</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if (empty($posts)): ?>
                    <tr>
                        <td colspan="5">暂无待审核的文章</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// 在文章保存时，标记用户提交的文章
function mark_user_submitted_post($post_id) {
    // 检查是否是通过API创建的文章
    $post = get_post($post_id);
    if ($post && $post->post_status == 'private' && $post->post_author != 0) {
        // 标记这是用户提交的文章
        update_post_meta($post_id, '_submitted_by_user', true);
    }
}
add_action('save_post', 'mark_user_submitted_post');