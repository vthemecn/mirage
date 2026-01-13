<?php
/*
 * 用户中心 sider
 */
global $wp_query;
global $current_user;

$param_user_id = $wp_query->query_vars['user_id'];

$page_user_id = $wp_query->query_vars['user_id'];

$avatar = vt_get_custom_avatar_url($page_user_id);

function page_active($current_page_name)
{
    global $wp_query;
    $vt_page =  $wp_query->query_vars['vt_page'];
    return $vt_page == $current_page_name ? "active" : "";
}

// 检查当前用户是否为文章作者或管理员
$is_owner_or_admin = ($param_user_id == $current_user->ID || current_user_can('manage_options'));

?>

<div class="user-center-sidebar">
    <?php /* <div class="user-profile"></div> */ ?>

    <div class="user-nav">
        <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>" class="sidebar-action <?php echo page_active('users'); ?>">
            <i class="fa-solid fa-user"></i><span>概览</span>
            <i class="fa-solid fa-angle-right"></i>
        </a>

        <?php if($is_owner_or_admin ): ?>
            <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>/new-post" class="sidebar-action <?php echo page_active('new-post'); ?>">
                <i class="fa-solid fa-plus"></i>
                <span>发布</span>
                <i class="fa-solid fa-angle-right"></i>
            </a>
        <?php endif ?>
        
        <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>/my-posts" class="sidebar-action <?php echo page_active('my-posts'); ?>">
            <i class="fa-solid fa-book"></i>
            <span>发布的</span>
            <i class="fa-solid fa-angle-right"></i>
        </a>
        
        <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>/star" class="sidebar-action <?php echo page_active('star'); ?>">
            <i class="fa-solid fa-star"></i><span>收藏</span>
            <i class="fa-solid fa-angle-right"></i>
        </a>
        
        <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>/like" class="sidebar-action <?php echo page_active('like'); ?>">
            <i class="fa-solid fa-thumbs-up"></i><span>点赞</span>
            <i class="fa-solid fa-angle-right"></i>
        </a>
        
        <?php if($is_owner_or_admin ): ?>
            <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>/setting" class="sidebar-action <?php echo page_active('setting'); ?>">
                <i class="fa-solid fa-gear"></i>
                <span>设置</span>
                <i class="fa-solid fa-angle-right"></i>
            </a>
            <a href="<?php echo wp_logout_url('/'); ?>" class="sidebar-action">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>退出</span>
                <i class="fa-solid fa-angle-right"></i>
            </a>
        <?php endif ?>
    </div>
    

</div>