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
}
add_action( 'show_user_profile', 'vt_add_profile_fields' );
add_action( 'edit_user_profile', 'vt_add_profile_fields' );
add_action( 'user_new_form', 'vt_add_profile_fields' );


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
        $avatar = THEME_URL . '/assets/images/avatar.jpg';
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


/*****************************************************************************/


function v_update_profile_init() {
    // 在 init 钩子中注册原来的函数
    add_action('profile_update', 'v_update_profile', 10, 2);
    add_action('user_register', 'v_update_profile');
}

function v_update_profile($user_id, $old_data = null){
    // 仅在后台执行，且当前用户有编辑用户权限
    if (!is_admin() || !current_user_can('edit_users')) {
        return;
    }

    // 检查 POST 数据中是否提供了会员等级
    if (isset($_POST['membership_level'])) {
        $membership_level = sanitize_text_field($_POST['membership_level']);
        update_user_meta($user_id, 'membership_level', $membership_level);
    }
}
add_action('init', 'v_update_profile_init');


/**
 * 添加上传字段
 */
function v_add_profile_fields( $user ) {    
    // 获取当前用户会员等级
    $membership_level = ($user!=='add-new-user') ? get_user_meta($user->ID, 'membership_level', true) : 'free';
    $membership_level = $membership_level !=="" ? $membership_level : 'free';

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
add_action( 'show_user_profile', 'v_add_profile_fields' );
add_action( 'edit_user_profile', 'v_add_profile_fields' );
add_action( 'user_new_form', 'v_add_profile_fields' );


/**
 * 获取用户会员等级
 */
function vt_get_user_membership_level($user_id) {
    $level = get_user_meta($user_id, 'membership_level', true);
    return !empty($level) ? $level : 'free';
}

/**
 * 在用户列表中添加会员等级列
 */
add_filter('manage_users_columns', 'v_add_membership_level_column');
function v_add_membership_level_column($columns) {
    $columns['membership_level'] = __('会员等级', 'vt');
    return $columns;
}

/**
 * 填充会员等级列的数据
 */
add_filter('manage_users_custom_column', 'v_show_membership_level_column_content', 10, 3);
function v_show_membership_level_column_content($value, $column_name, $user_id) {
    if ('membership_level' !== $column_name) {
        return $value;
    }

    $level = get_user_meta($user_id, 'membership_level', true);
    
    // 如果没有设置等级，默认为 free
    if (empty($level)) {
        $level = 'free';
    }
    
    // 定义等级对应的显示文本
    $levels = array(
        'free' => __('普通用户', 'vt'),
        'vip' => __('VIP用户', 'vt'),
        'svip' => __('SVIP用户', 'vt')
    );
    
    return isset($levels[$level]) ? $levels[$level] : $levels['free'];
}

/**
 * 使会员等级列可排序
 */
add_filter('manage_users_sortable_columns', 'v_add_membership_level_column_sortable');
function v_add_membership_level_column_sortable($columns) {
    $columns['membership_level'] = 'membership_level';
    return $columns;
}

/**
 * 处理用户列表排序
 */
add_action('pre_get_users', 'v_handle_membership_level_column_sorting', 1);
function v_handle_membership_level_column_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    // 检查当前页面是否是 users.php
    global $pagenow;
    if ($pagenow !== 'users.php') {
        return;
    }
    
    // 如果用户手动选择了排序,则不干预
    if (isset($_GET['orderby']) && !empty($_GET['orderby'])) {
        return;
    }

    // 如果是按会员等级排序(保留原有功能)
    if ('membership_level' === $query->get('orderby')) {
        $query->set('meta_key', 'membership_level');
        $query->set('orderby', 'meta_value');
    }
}

/**
 * 备用方案：通过 users_list_table_query_args 过滤器强制设置排序
 */
add_filter('users_list_table_query_args', 'v_force_user_list_order');
function v_force_user_list_order($args) {
    // 只在没有手动指定排序时生效
    if (!isset($_GET['orderby']) || empty($_GET['orderby'])) {
        $args['orderby'] = 'ID';
        $args['order'] = 'DESC';
    }
    return $args;
}

/*****************************************************************************/
