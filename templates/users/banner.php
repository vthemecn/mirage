<?php
// 获取当前用户会员等级
$param_user_level = get_user_meta($param_user->ID, 'membership_level', true);
$param_user_level = $param_user_level !=="" ? $param_user_level : 'free';

$levels = array(
        'free' => __('普通用户', 'vt'),
        'vip' => __('VIP用户', 'vt'),
        'svip' => __('SVIP用户', 'vt')
    );
$level_text = $levels[$param_user_level];
?>

<div class="user-center-container">
    <div class="profile-hero">
        <div class="hero-bg">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/banner.jpg" class="hero-bg-img" alt="Profile Banner" onerror="this.style.display='none'; this.parentElement.style.background='linear-gradient(135deg, var(--theme-color, #5a667d) 0%, #7f8fa4 100%)'">
        </div>
        <div class="hero-content">
            <div class="hero-avatar-widget">
                <?php if(get_current_user_id() == $param_user->ID): ?>
                    <div class="avatar-upload-container">
                        <div class="avatar-wrapper" id="avatar-wrapper">
                            <?php echo get_avatar($param_user->ID, 80, '', '', ''); ?>
                        </div>
                        <div class="avatar-upload-overlay">
                            <span>点击上传头像</span>
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
                <p class="hero-bio"><?php echo esc_html($param_user->description ? $param_user->description : '暂无个人简介'); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
    // 确保ajax_object已定义
    if (typeof ajax_object === 'undefined') {
        var ajax_object = {
            ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>",
            nonce: "<?php echo wp_create_nonce('ajax_nonce'); ?>"
        };
    }
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
            showNotification('请选择图片文件', 'error');
            return;
        }
        
        // 验证文件大小 (最大5MB)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('头像文件不能超过5MB', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('action', 'upload_avatar');
        formData.append('security', ajax_object.nonce);
        
        // 显示上传进度
        showNotification('正在上传头像...', 'success');
        
        fetch(ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 更新头像
                const img = avatarWrapper.querySelector('img');
                if (img) {
                    img.src = data.data.avatar_url + '?t=' + Date.now(); // 添加时间戳防止缓存
                } else {
                    avatarWrapper.innerHTML = `<img src="${data.data.avatar_url}" alt="avatar" class="avatar">`;
                }
                
                showNotification(data.data.message, 'success');
            } else {
                showNotification(data.data || data.message || '上传失败', 'error');
            }
        })
        .catch(error => {
            showNotification('网络错误，请稍后重试', 'error');
        });
    }
    
    function showNotification(message, type) {
        // 检查Notyf是否存在
        if (typeof notyf !== 'undefined') {
            if (type === 'success') {
                notyf.success(message);
            } else {
                notyf.error(message);
            }
        } else {
            alert(message);
        }
    }
});
</script>