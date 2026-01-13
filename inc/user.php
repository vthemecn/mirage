<?php

/**
 * 添加上传字段
 */
function vt_add_profile_fields( $user ) {
    $avatar_id = ($user!=='add-new-user') ? get_user_meta($user->ID, 'user_avatar_attachment_id', true): false;

    if( !empty($avatar_id) ){
        $image = wp_get_attachment_image_src( $avatar_id, 'thumbnail' );
    }
    ?>
    <input type="hidden" name="vt_avatar_id" id="vt_avatar_id" value="<?php echo !empty($avatar_id) ? $avatar_id : ''; ?>" />
    <?php
    
    // 获取当前用户会员等级
    $membership_level = ($user!=='add-new-user') ? get_user_meta($user->ID, 'membership_level', true) : 'free';
    $membership_level = $membership_level !=="" ? $membership_level : 'free';
    echo '$membership_level: '.$membership_level;

    // 显示会员等级选择
    echo '<h3>' . __('会员等级', 'vt') . '</h3>';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th><label for="membership_level">' . __('等级选择', 'vt') . '</label></th>';
    echo '<td>';
    
    // 定义会员等级选项
    $levels = array(
        'free' => __('普通用户', 'vt'),
        'vip' => __('VIP用户', 'vt'),
        'svip' => __('SVIP用户', 'vt')
    );
    
    foreach ($levels as $level_key => $level_label) {
        $checked = ($membership_level === $level_key) ? ' checked="checked"' : '';
        echo '<p><input type="radio" id="level_'.$level_key.'" name="membership_level" value="'.$level_key.'"'.$checked.'> ';
        echo '<label for="level_'.$level_key.'">'.$level_label.'</label></p>';
    }
    
    echo '</td>';
    echo '</tr>';
    echo '</table>';
}
add_action( 'show_user_profile', 'vt_add_profile_fields' );
add_action( 'edit_user_profile', 'vt_add_profile_fields' );
add_action( 'user_new_form', 'vt_add_profile_fields' );


/**
 * 更新用户头像和会员等级
 */
function vt_update_profile($user_id){
    if( current_user_can('edit_users') ){
        // 检查POST数据中是否提供了vt_avatar_id
        if (isset($_POST['vt_avatar_id'])) {
            $avatar_id = empty($_POST['vt_avatar_id']) ? '' : $_POST['vt_avatar_id'];
            update_user_meta($user_id, 'user_avatar_attachment_id', $avatar_id);
        }
        
        // 检查POST数据中是否提供了会员等级
        if (isset($_POST['membership_level'])) {
            $membership_level = sanitize_text_field($_POST['membership_level']);
            update_user_meta($user_id, 'membership_level', $membership_level);
        }
    }
}
add_action('profile_update', 'vt_update_profile');
add_action('user_register', 'vt_update_profile');


/*
 * 设置用户头像(系统默认使用的是网络头像)
 */
add_filter('get_avatar', 'vt_custom_avatar', 1, 5);
function vt_custom_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    // $id_or_email->user_id
    $attachment_id = get_user_meta($id_or_email, "user_avatar_attachment_id", true);
    $avatar_item = wp_get_attachment_image_src($attachment_id, 'thumbnail');

    if(!$avatar_item) {
        $avatar = get_bloginfo('template_url') . '/assets/images/avatar.jpg';
    } else {
        $avatar = $avatar_item[0];
    }
    $avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
    return $avatar;
}



/**
 * 覆盖用户上传头像按钮
 */
function set_profile_avatar() {
    // $current_user = wp_get_current_user();
    if (current_user_can( 'upload_files' ) ) {
        return '<a class="button vt-avatar"  id="vt-avatar">上传头像</a>';
    } else {
        return '';
    }
}
add_filter( 'user_profile_picture_description', 'set_profile_avatar', 1 );


/**
 * 获取用户会员等级
 */
function get_user_membership_level($user_id) {
    $level = get_user_meta($user_id, 'membership_level', true);
    return !empty($level) ? $level : 'free';
}