<?php
/**
 * 页面：/account/edit
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

// 获取全局变量
global $wp_query;
global $current_user;
global $wpdb;

// 使用当前登录用户
$param_user_id = get_current_user_id();
$param_user = get_user_by('id', $param_user_id);

// 获取用户性别
$user_meta = get_user_meta($param_user_id);
$gender = isset($user_meta['gender'][0]) ? $user_meta['gender'][0] : '0';

// 获取当前用户会员等级
$param_user_level = get_user_meta($param_user->ID, 'membership_level', true);
$param_user_level = $param_user_level !=="" ? $param_user_level : 'free';

$levels = array(
        'free' => __('Free User', 'vt'),
        'vip' => __('VIP User', 'vt'),
        'svip' => __('SVIP User', 'vt')
    );
$level_text = $levels[$param_user_level];

?>


<div class="vt-account-banner">
    <div class="title"><?=__('Account','vt')?></div>
</div>


<?php require_once THEME_DIR . '/templates/account/tab.php'; ?>


<div class="vt-account-container">
    <div class="vt-account-wrapper">
        <div class="vt-account-panel">
            <div class="vt-account-panel-title"><?php _e('Profile', 'vt'); ?></div>

            <div class="hero-content">
                <div class="hero-avatar-widget">
                    <?php if(get_current_user_id() == $param_user->ID): ?>
                        <div class="avatar-upload-container">
                            <div class="avatar-wrapper" id="avatar-wrapper">
                                <?php echo get_avatar($param_user->ID, 80, '', '', ''); ?>
                            </div>
                            <div class="avatar-upload-overlay">
                                <span><?php _e('Upload Avatar', 'vt'); ?></span>
                                <input type="file" id="avatar-upload-input" accept="image/*" style="display: none;">
                            </div>
                        </div>
                    <?php else: ?>
                        <?php echo get_avatar($param_user->ID, 80, '', '', ''); ?>
                    <?php endif; ?>
                </div>
                <div class="hero-user-info">
                    <h1 class="hero-display-name">
                        <?php echo esc_html($param_user->display_name); ?>
                        <div class="hero-level <?=$param_user_level?>"><i class="fa-solid fa-gem"></i><?=$level_text?></div>
                    </h1>
                    <p class="hero-bio"><?php echo esc_html($param_user->description ? $param_user->description : __('No bio yet', 'vt')); ?></p>
                </div>
            </div>

            <div class="form">
                <div class="field field-text">
                    <label for="user_login"><?php _e('Username:', 'vt'); ?></label>
                    <input name="user_login" id="user_login" type="text" value="<?php echo $param_user->user_login?>" disabled>
                </div>
                <div class="field field-text">
                    <label for="nickname"><?php _e('Nickname:', 'vt'); ?></label>
                    <input name="nickname" id="nickname" type="text" value="<?php echo $param_user->display_name?>">
                </div>
                <div class="field gender">
                    <label><?php _e('Gender:', 'vt'); ?></label>
                    <div class="radio-group">                        
                        <label for="gender_1">
                            <input type="radio" name="gender" id="gender_1" value="1" <?php echo $gender == '1' ? 'checked' : ''; ?>>    
                            <?php _e('Male', 'vt'); ?>
                        </label>
                    
                        <label for="gender_2">
                            <input type="radio" name="gender" id="gender_2" value="2" <?php echo $gender == '2' ? 'checked' : ''; ?>>    
                            <?php _e('Female', 'vt'); ?>
                        </label>

                        <label for="gender_0">
                            <input type="radio" name="gender" id="gender_0" value="0" <?php echo $gender == '0' ? 'checked' : ''; ?>>
                            <?php _e('Secret', 'vt'); ?>
                        </label>
                    </div>
                </div>
                <div class="field field-text">
                    <label for="email"><?php _e('Email:', 'vt'); ?></label>
                    <input name="email" id="email" type="email" value="<?php echo $param_user->user_email?>" disabled>
                </div>
                <!-- 手机号字段已隐藏 -->
                <div class="field field-text textarea">
                    <label for="description"><?php _e('Bio:', 'vt'); ?></label>
                    <textarea name="description" id="description" cols="30" rows="5"><?php echo $param_user->description?></textarea>
                </div>
                <div class="field">
                    <button class="btn btn-primary update-user"><?php _e('Confirm', 'vt'); ?></button>
                </div>
            </div>
        </div>
    </div>

</div>


<?php
$account_ajax_object = array(
    'update_user_api_url' => home_url('/wp-json/vtheme/v1/users/'.$param_user_id)
);
?>
<script type="text/javascript">
var account_ajax_object = <?php echo json_encode($account_ajax_object); ?>;
console.log('account_ajax_object', account_ajax_object);

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarWrapper = document.getElementById('avatar-wrapper');
    const uploadInput = document.getElementById('avatar-upload-input');
    const overlay = document.querySelector('.avatar-upload-overlay');
    
    if (!avatarWrapper) return;
    
    // 显示上传覆盖层
    // avatarWrapper.addEventListener('mouseenter', function() {
    //     overlay.style.display = 'flex';
    // });
    
    // avatarWrapper.addEventListener('mouseleave', function() {
    //     overlay.style.display = 'none';
    // });
    
    // 点击头像上传
    avatarWrapper.addEventListener('click', function() {
        uploadInput.click();
    });
    
    // 点击覆盖层上传
    overlay.addEventListener('click', function() {
        uploadInput.click();
    });
    
    // 文件选择后处理
    uploadInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            handleAvatarUpload(e.target.files[0]);
        }
    });
    
    function handleAvatarUpload(file) {
        // 验证文件类型
        if (!file.type.match('image.*')) {
            showNotification(__('Please select an image file'), 'error');
            return;
        }
        
        // 验证文件大小 (最大5MB)
        if (file.size > 5 * 1024 * 1024) {
            showNotification(__('Avatar file size cannot exceed 5MB'), 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('avatar', file);
        
        // 显示上传进度
        showNotification(__('Uploading avatar...'), 'info');
        
        fetch('/wp-json/vtheme/v1/accounts/upload-avatar', {
            method: 'POST',
            body: formData,
            headers: {
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.avatar_url) {
                // 成功响应 - 直接使用返回的资源
                updateAvatarImage(data.avatar_url);
                showNotification(data.message || __('Avatar uploaded successfully'), 'success');
            } else if (data.error) {
                // 错误响应 - 使用错误消息
                showNotification(data.error.message || __('Upload failed'), 'error');
            } else {
                // 其他情况
                showNotification(__('Invalid response format'), 'error');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showNotification(__('Network error, please try again later'), 'error');
        });
    }
    
    function updateAvatarImage(avatarUrl) {
        console.log('updateAvatarImage called with:', avatarUrl);
        console.log('avatarWrapper element:', avatarWrapper);
        
        // 查找.avatar-wrapper内的所有img元素
        const avatarImages = avatarWrapper.querySelectorAll('img');
        console.log('Found avatar images:', avatarImages.length);
        console.log('Avatar images:', avatarImages);
        
        if (avatarImages.length > 0) {
            // 如果存在img元素，更新所有图片的src
            avatarImages.forEach((img, index) => {
                console.log(`Updating image ${index}:`, img);
                console.log(`Old src: ${img.src}`);
                img.src = avatarUrl + '?t=' + Date.now();
                console.log(`New src: ${img.src}`);
                // 确保图片有正确的class
                if (!img.classList.contains('avatar')) {
                    img.classList.add('avatar');
                }
                // 确保有alt属性
                if (!img.alt) {
                    img.alt = __('User Avatar');
                }
            });
        } else {
            console.log('No existing images found, creating new image');
            // 如果没有img元素，创建新的图片
            const newImg = document.createElement('img');
            newImg.src = avatarUrl + '?t=' + Date.now();
            newImg.alt = __('User Avatar');
            newImg.className = 'avatar';
            console.log('Created new image:', newImg);
            avatarWrapper.appendChild(newImg);
        }
        
        // 触发图片加载事件，确保UI更新
        avatarWrapper.dispatchEvent(new Event('avatarUpdated'));
        console.log('Avatar update completed');
    }
});
</script>


<script>
document.querySelector(".update-user").addEventListener("click", function(e) {
    e.preventDefault();

    var url = account_ajax_object.update_user_api_url;
    
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
            'X-WP-Nonce': ajax_object.wp_rest_nonce
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(errorData.message || __('Update failed'));
            });
        }
        
        return response.json();
    })
    .then(data => {
        showNotification(__('Update successful'));
        // 更新侧边栏昵称
        var nicknameElement = document.querySelector('.user-nav .user-info .nickname');
        if(nicknameElement) {
            nicknameElement.textContent = data.display_name;
        }
    })
    .catch(error => {
        showNotification(__('Update failed: ') + error.message, 'error');
        console.error('Error:', error);
    });
});
</script>


<?php get_footer(); ?>
