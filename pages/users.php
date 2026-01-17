<?php
/**
 * `/inc/rewrite.php` 所引用的文件
 */

global $wp_query;
global $current_user;
global $wpdb;

global $param_user;

$param_user_id = $wp_query->query_vars['user_id'];

$param_user = get_user_by('id', $param_user_id); 


$gender = get_user_meta($param_user_id, 'gender', true);
switch($gender){
    case '0': $gender = '保密'; break;
    case '1': $gender = '男'; break;
    case '2': $gender = '女'; break;
    default: $gender = '保密'; break;
}

// p($param_user_id);
// p($current_user);

$has_auth = false;
if( $param_user_id ==  $current_user->ID || current_user_can( 'manage_options' ) ){
    $has_auth = true;
}

get_header();
?>


<?php require_once get_template_directory() . '/templates/users/banner.php'; ?>


<div class="user-center-container">
    <?php require_once get_template_directory() . '/templates/users/sider.php'; ?>
    
    <div class="user-wrapper">
        <div class="user-center-panel">
            <h3>概览</h3>

            <div class="detail-widget">
                <div class="detail-item">
                    <div class="detail-label">昵称: </div>
                    <div class="detail-value"><?php echo $param_user->display_name; ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">用户名: </div>
                    <div class="detail-value"><?php echo $param_user->user_nicename; ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">性别: </div>
                    <div class="detail-value"><?php echo $gender; ?></div>
                </div>
                
                <?php if($has_auth): ?>
                <div class="detail-item">
                    <div class="detail-label">邮箱: </div>
                    <div class="detail-value">
                        <div><?php echo $param_user->user_email; ?></div>
                        <span class="private-note">（仅自己可见）</span>
                    </div>
                </div>
                <?php endif ?>
                
                <div class="detail-item">
                    <div class="detail-label">签名: </div>
                    <div class="detail-value"><?php echo $param_user->description ? $param_user->description : '暂无简介'; ?></div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">加入于: </div>
                    <div class="detail-value "><?= date('Y年m月', strtotime($param_user->user_registered)); ?></div>
                </div>
            </div>
        </div>

        <div class="user-center-panel">
            <div class="user-stats">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-thumbs-up"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">
                            <?php 
                                $like_count = $wpdb->get_var($wpdb->prepare(
                                    "SELECT COUNT(*) FROM {$wpdb->prefix}vt_star WHERE type='like' AND user_id=%d", 
                                    $param_user_id
                                )); 
                                echo $like_count;
                            ?>
                        </div>
                        <div class="stat-label">点赞的文章</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">
                            <?php 
                                $star_count = $wpdb->get_var($wpdb->prepare(
                                    "SELECT COUNT(*) FROM {$wpdb->prefix}vt_star WHERE type='star' AND user_id=%d", 
                                    $param_user_id
                                )); 
                                echo $star_count;
                            ?>
                        </div>
                        <div class="stat-label">收藏的文章</div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">
                            <?php 
                                $post_count = count_user_posts($param_user_id);
                                echo $post_count;
                            ?>
                        </div>
                        <div class="stat-label">发布的文章</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.user-stats {
    display: flex;
    justify-content: space-around;
    gap: 15px;
    margin-top: 20px;
}

.stat-item {
    display: flex;
    align-items: center;
    flex: 1;
    background: var(--vt-bg-color-card);
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 0 2px 0 var(--vt-shadow-color);
}

.stat-icon {
    font-size: 24px;
    color: #4a90e2;
    margin-right: 12px;
    min-width: 30px;
    text-align: center;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: var(--vt-text-color);
    line-height: 1.2;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-top: 4px;
}

@media (max-width: 768px) {
    .user-stats {
        flex-direction: column;
    }
    
    .stat-item {
        margin-bottom: 10px;
    }
}
</style>

<?php get_footer(); ?>