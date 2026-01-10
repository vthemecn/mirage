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


<style>
/* .user-center-panel .form .field-textarea {
    display: flex;
    flex-direction: column;
}

.user-center-panel .form .field-textarea label {
    margin-bottom: 5px;
}

.user-center-panel .form .field-textarea textarea {
    width: 100%;
    min-height: 100px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    resize: vertical;
} 

.user-center-panel .field-text.gender .radio-group {
    display: flex;
    gap: 15px;
    margin-top: 5px;
}

.user-center-panel .field-text.gender .radio-item {
    display: flex;
    align-items: center;
    gap: 5px;
}
.user-center-panel .field-text.gender label{
    width: auto !important;
}
.user-center-panel .field-text.gender input[type="radio"] {
    margin: 0;
}*/

.radio-group {

}
</style>

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
                <div class="field gender">
                    <label>性别:</label>
                    <div class="radio-group">                        
                        <label for="gender_1">
                            <input type="radio" name="gender" id="gender_1" value="1" <?php echo $gender == '1' ? 'checked' : ''; ?>>    
                            男
                        </label>
                    
                        <label for="gender_2">
                            <input type="radio" name="gender" id="gender_2" value="2" <?php echo $gender == '2' ? 'checked' : ''; ?>>    
                            女
                        </label>

                        <label for="gender_0">
                            <input type="radio" name="gender" id="gender_0" value="0" <?php echo $gender == '0' ? 'checked' : ''; ?>>
                            保密
                        </label>
                    </div>
                </div>
                <div class="field field-text">
                    <label for="email">邮箱:</label>
                    <input name="email" id="email" type="email" value="<?php echo $param_user->user_email?>" disabled>
                </div>
                <!-- 手机号字段已隐藏 -->
                <div class="field field-text textarea">
                    <label for="description">签名:</label>
                    <textarea name="description" id="description" cols="30" rows="5"><?php echo $param_user->description?></textarea>
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
    requestData.gender = document.querySelector("input[name='gender']:checked").value;
    requestData.description = document.querySelector("textarea[name='description']").value;
    // 不包含手机号和邮箱，因为它们被禁用了
    requestData.email = "<?php echo $param_user->user_email?>"; // 使用原始邮箱值

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