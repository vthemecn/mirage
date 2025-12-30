<?php
$default_avatar = get_bloginfo('template_directory') . '/assets/images/avatar.jpg';
$avatar = isset($param_user->avatar) ? $param_user->avatar : $default_avatar; 

$upload_avatar_button = $param_user_id ==  $current_user->ID ? 'upload-avatar-button' : '';
?>

<div class="user-panel">
    <div class="user-avatar <?php echo $upload_avatar_button?>">
        <?php
        /* <a href="javascript:;" class="update-avatar-button">
            <i class="iconfont">&#xe77f;</i>修改头像
        </a> */ 
        ?>
        <img src="<?php echo $avatar ?>" class="avatar">
    </div>
    <div class="user-info">
        <div class="nickname">
            <?php echo $param_user->display_name; ?>
        </div>
    </div>
</div>

<form id="avatar_upload" method="post" 
    action="<?php echo home_url('/wp-json/vtheme/v1/upload/upload-avatar'); ?>?_wpnonce=<?php echo wp_create_nonce('wp_rest'); ?>" 
    enctype="multipart/form-data" style="display: none;">
    <input type="file" name="avatar-input" id="avatar-input" multiple="false" />
    // <input type="hidden" name="post_id" id="post_id" value="55" />
    <input id="submit_avatar-input" name="submit_avatar-input" type="submit" value="Upload" />
</form>