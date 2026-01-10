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
                <h1 class="hero-display-name"><?php echo esc_html($param_user->display_name); ?></h1>
                <p class="hero-bio"><?php echo esc_html($param_user->description ? $param_user->description : '暂无个人简介'); ?></p>
            </div>
        </div>
    </div>
</div>