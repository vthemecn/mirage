<?php
/**
 * `/inc/rewrite.php` 所引用的文件
 */

global $wp_query;
global $current_user;
global $wpdb;

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
    <?php // require_once get_template_directory() . '/templates/users/banner.php'; ?>

    <?php require_once get_template_directory() . '/templates/users/sider.php'; ?>

    <div class="user-center-panel">
        <h3>基本资料</h3>

        <div class="">
            <div class="user-item">
                <div class="user-item-header">昵称:</div>
                <div class="user-item-body"><?php echo $param_user->display_name?></div>
            </div>
            <div class="user-item">
                <div class="user-item-header">性别:</div>
                <div class="user-item-body"><?php echo $gender ?></div>
            </div>

            <?php if($has_auth): ?>
                <div class="user-item">
                    <div class="user-item-header">邮箱:</div>
                    <div class="user-item-body">
                        <?php echo $param_user->user_email ?>
                        <span>仅自己可见</span>    
                    </div>
                </div>
            <?php endif ?>

            <?php if($has_auth): ?>
            <div class="user-item">
                <div class="user-item-header">手机号:</div>
                <div class="user-item-body"><?php echo get_user_meta($param_user_id, 'mobile', true); ?></div>
            </div>
            <?php endif ?>
            
            <div class="user-item">
                <div class="user-item-header">简介:</div>
                <div class="user-item-body description">
                    <?php echo $param_user->description ?>
                </div>
            </div>
            
            <div class="user-item achievement">
                <div class="achievement-widget">
                    <span>
                        <?php 
                            $like_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}vt_star WHERE type='like' AND user_id=%d", 
                                $param_user_id
                            )); 
                            echo $like_count;
                        ?>
                    </span>
                    <span>点赞</span>
                </div>
                <div class="achievement-widget">
                    <span>
                        <?php 
                            $star_count = $wpdb->get_var($wpdb->prepare(
                                "SELECT COUNT(*) FROM {$wpdb->prefix}vt_star WHERE type='star' AND user_id=%d", 
                                $param_user_id
                            )); 
                            echo $star_count;
                        ?>
                    </span>
                    <span>收藏</span>
                </div>
                <div class="achievement-widget">
                    <span>
                        <?php 
                            $post_count = count_user_posts($param_user_id);
                            echo $post_count;
                        ?>
                    </span>
                    <span>文章</span>
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php get_footer(); ?>
