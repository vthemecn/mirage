<?php
/**
 * `/inc/rewrite.php` 所引用的 file
 */

global $wp_query;
global $current_user;

$param_user_id = $wp_query->query_vars['user_id'];
$param_user = get_user_by('id', $param_user_id);

// 获取用户性别
$user_meta = get_user_meta($param_user_id);
$gender = isset($user_meta['gender'][0]) ? $user_meta['gender'][0] : '0';

get_header();
?>



<div class="user-center-container">
    <?php // require_once get_template_directory() . '/templates/users/banner.php'; ?>

    <?php require_once get_template_directory() . '/templates/users/sider.php'; ?>

    <div class="user-wrapper">
        <div class="user-center-panel">
            <h3>我的设置</h3>

            <div class="form">
                <div class="field field-text">
                    <label for="user_login">账号:</label>
                    <input name="user_login" id="user_login" type="text" value="<?php echo $param_user->user_login?>" disabled>
                </div>
                <div class="field field-text">
                    <label for="nickname">昵称:</label>
                    <input name="nickname" id="nickname" type="text" value="<?php echo $param_user->display_name?>">
                </div>
                <div class="field field-select">
                    <label for="gender">性别:</label>
                    <select name="gender" id="gender">
                        <option value="0" <?php echo $gender == '0' ? 'selected' : ''; ?>>保密</option>
                        <option value="1" <?php echo $gender == '1' ? 'selected' : ''; ?>>男</option>
                        <option value="2" <?php echo $gender == '2' ? 'selected' : ''; ?>>女</option>
                    </select>
                </div>
                <div class="field field-text">
                    <label for="email">邮箱:</label>
                    <input name="email" id="email" type="email" value="<?php echo $param_user->user_email?>" <?php echo ($param_user_id == $current_user->ID) ? '' : 'disabled'; ?>>
                    <?php if($param_user_id != $current_user->ID): ?>
                    <span>仅自己可见</span>
                    <?php endif; ?>
                </div>
                <div class="field field-text">
                    <label for="mobile">手机号:</label>
                    <input name="mobile" id="mobile" type="text" value="<?php echo get_user_meta($param_user_id, 'mobile', true); ?>" <?php echo ($param_user_id == $current_user->ID) ? '' : 'disabled'; ?>>
                </div>
                <div class="field field-textarea">
                    <label for="description">签名:</label>
                    <textarea name="description" id="description" cols="20" rows="6"><?php echo $param_user->description?></textarea>
                </div>
                <div class="field">
                    <button class="btn btn-primary update-user">确定</button>
                </div>
            </div>
        </div>
    </div>
    
</div>

<input type="hidden" name="api_url" value="<?php echo home_url('/wp-json/vtheme/v1/users/'.$param_user_id); ?>">
<input type="hidden" name="wp_create_nonce" value="<?php echo wp_create_nonce('wp_rest'); ?>">

<script>
document.querySelector(".update-user").addEventListener("click", function(e) {
    e.preventDefault();
    
    var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
    var url = document.querySelector('input[name="api_url"]').value;
    
    var requestData = {};
    requestData.nickname = document.querySelector("input[name='nickname']").value;
    requestData.gender = document.querySelector("select[name='gender']").value;
    requestData.description = document.querySelector("textarea[name='description']").value;
    requestData.mobile = document.querySelector("input[name='mobile']").value;
    requestData.email = document.querySelector("input[name='email']").value;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': wpnonce
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || '修改失败');
            });
        }
        console.log('1111');
        
        return response.json();
    })
    .then(data => {
        console.log('2222');
        
        // 显示成功提示
        if (typeof LightTip !== 'undefined' && typeof LightTip.success === 'function') {
            LightTip.success('修改成功！');
        } else {
            alert('修改成功！');
        }
        
        // 更新侧边栏昵称
        var nicknameElement = document.querySelector('.user-nav .user-info .nickname');
        if(nicknameElement) {
            nicknameElement.textContent = data.display_name;
        }
    })
    .catch(error => {
        // 显示错误提示
        if (typeof LightTip !== 'undefined' && typeof LightTip.error === 'function') {
            LightTip.error('修改失败：' + error.message);
        } else {
            alert('修改失败：' + error.message);
        }
        
        console.error('Error:', error);
    });
});
</script>

<?php get_footer(); ?>