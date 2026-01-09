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

$has_auth = false;
if( $param_user_id ==  $current_user->ID || current_user_can( 'manage_options' ) ){
    $has_auth = true;
}

get_header();
?>


<div class="user-center-container">
    <?php require_once get_template_directory() . '/templates/users/sider.php'; ?>
    
    <div class="user-wrapper">
        <div class="user-center-panel">
            <div class="user-details">
                <h3>个人资料</h3>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">昵称</div>
                        <div class="detail-value"><?php echo $param_user->display_name; ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">用户名</div>
                        <div class="detail-value"><?php echo $param_user->user_nicename; ?></div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-value"><?php echo $gender; ?></div>
                        <div class="stat-label">性别</div>
                    </div>
                    
                    <?php if($has_auth): ?>
                    <div class="detail-item">
                        <div class="detail-label">邮箱</div>
                        <div class="detail-value"><?php echo $param_user->user_email; ?> <span class="private-note">（仅自己可见）</span></div>
                    </div>
                    <?php endif ?>
                    
                    <?php if($has_auth): ?>
                    <div class="detail-item">
                        <div class="detail-label">手机号</div>
                        <div class="detail-value"><?php echo get_user_meta($param_user_id, 'mobile', true); ?></div>
                    </div>
                    <?php endif ?>
                    
                    <div class="detail-item full-width">
                        <div class="detail-label">个人简介</div>
                        <div class="detail-value description"><?php echo $param_user->description ? $param_user->description : '暂无简介'; ?></div>
                    </div>

                    加入于 <?php echo date('Y年m月', strtotime($param_user->user_registered)); ?>
                </div>
            </div>

            <hr>

            <div class="user-stats">
                <div class="stat-item">
                    <div class="stat-value">
                        <?php 
                            $like_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}vt_star WHERE type='like' AND user_id=%d", 
                                $param_user_id
                            )); 
                            echo $like_count;
                        ?>
                    </div>
                    <div class="stat-label">点赞</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?php 
                            $star_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}vt_star WHERE type='star' AND user_id=%d", 
                                $param_user_id
                            )); 
                            echo $star_count;
                        ?>
                    </div>
                    <div class="stat-label">收藏</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">
                        <?php 
                            $post_count = count_user_posts($param_user_id);
                            echo $post_count;
                        ?>
                    </div>
                    <div class="stat-label">文章</div>
                </div>
                
            </div>

        </div>

    </div>
</div>

<?php get_footer(); ?>