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
                <?php echo get_avatar($param_user->ID, 80, '', '', ''); ?>
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
