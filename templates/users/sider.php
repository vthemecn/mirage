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
?>

<div class="user-center-sidebar">
    <div class="user-profile">
        <div class="user-avatar <?php echo $upload_avatar_button?>">
            <?php
            /* <a href="javascript:;" class="update-avatar-button">
                <i class="iconfont">&#xe77f;</i>修改头像
            </a> */ 
            ?>
            <img src="<?= $avatar ?>" class="avatar">
        </div>
        <div class="user-info">
            <div class="nickname">
                <?php echo $param_user->display_name; ?>
            </div>
        </div>
        <form id="avatar_upload" method="post"
            action="<?php echo home_url('/wp-json/vtheme/v1/upload/upload-avatar'); ?>?_wpnonce=<?php echo wp_create_nonce('wp_rest'); ?>" 
            enctype="multipart/form-data" style="display: none;">
            <input type="file" name="avatar-input" id="avatar-input" multiple="false" />
            // <input type="hidden" name="post_id" id="post_id" value="55" />
            <input id="submit_avatar-input" name="submit_avatar-input" type="submit" value="Upload" />
        </form>
    </div>

    <div class="user-nav">
        <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>" class="sidebar-action <?php echo page_active('users'); ?>">
            <i class="fa-solid fa-user"></i><span>概览</span>
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
        
        <?php if($current_user->ID == $param_user_id || current_user_can( 'manage_options' ) ): ?>
            <?php if ($current_user->ID == $param_user_id) : ?>
                <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>/new-post" class="sidebar-action <?php echo page_active('new-post'); ?>">
                    <i class="fa-solid fa-plus"></i>
                    <span>发布</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
                <a href="<?php bloginfo("siteurl") ?>/users/<?php echo $page_user_id ?>/my-posts" class="sidebar-action <?php echo page_active('my-posts'); ?>">
                    <i class="fa-solid fa-book"></i>
                    <span>发布的</span>
                    <i class="fa-solid fa-angle-right"></i>
                </a>
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
        <?php endif ?>
    </div>
    

</div>