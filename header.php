<?php

$vt_config = vt_get_config();

// if (!$vt_config['frontend_is_on']) {
//     header('HTTP/1.1 403 Forbidden');
//     echo '{error:"403 当前主题关闭了前端，仅能通过接口访问"}';
//     exit();
// }

$current_url = home_url(add_query_arg(array(), $wp->request));
?>


<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $keywords    = $vt_config['meta_keywords'];
    $description = $vt_config['meta_description'];
    if (is_home()) {
    } elseif (is_category()) {
        // $keywords = single_cat_title('', false);
        $description = category_description();
    } elseif (is_tag()) {
        // $keywords = single_tag_title('', false);
        $description = tag_description();
    } elseif (is_single()) {
        $description = get_the_excerpt();
    }
    $keywords    = $keywords ? trim(strip_tags($keywords)) : '';
    $description = $description ? trim(strip_tags($description)) : $vt_config['meta_description'];
    ?>
    <meta name="keywords" content="<?php echo $keywords ?>">
    <meta name="description" content="<?php echo $description ?>">
    <meta name="api-url" content="<?php echo home_url() ?>">
    <?php wp_head(); ?>


    <link rel="stylesheet" type="text/css" href="<?= get_bloginfo('template_url'); ?>/assets/lib/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= get_bloginfo('template_url'); ?>/assets/lib/fontawesome/css/solid.min.css" />

    <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/assets/lib/swiper/swiper-bundle.min.css" />
    <script src="<?php bloginfo('template_url'); ?>/assets/lib/swiper/swiper-bundle.min.js"></script>

    <?php
    $primary = $vt_config['basic_style'];
    $hover = $vt_config['basic_style'];
    echo "<style>:root{ --vt-color-primary:{$primary}; --vt-color-primary-hover:{ $hover}; }</style>";
    ?>

    <?php if($vt_config['background_image']): ?>
    <style>body:before { background-image:url(<?php echo $vt_config['background_image']?>) }</style>
    <?php endif ?>
    <?php if($vt_config['border_radius_is_on']==0): ?>
    <style>:root{ --vt-border-radius:0px; }</style>
    <?php endif ?>
    
    <style> .card-item .card-image { padding-bottom: <?php echo $vt_config['image_items_height'] ?> !important; } </style>

    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/assets/lib/notyf/notyf.min.css">
    <script src="<?php bloginfo('template_url'); ?>/assets/lib/notyf/notyf.umd.js"></script>
</head>

<?php

/* 暗黑模式 */
$is_dark_mode = false;
$dark_mode_class = '';
if($vt_config['dark_mode_type'] == 0){ // 禁用
    $_COOKIE['darkModeType'] = 0;
} elseif($vt_config['dark_mode_type'] == 1) { // 手动
    $_COOKIE['darkModeType'] = 1;
    $is_dark_mode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] == 1 ? true : false;
    $dark_mode_class = $is_dark_mode ? "dark-mode" : '';
} elseif($vt_config['dark_mode_type'] == 2) { // 自动
    $_COOKIE['darkModeType'] = 2;
    $is_dark_mode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] == 1 ? true : false;
    $dark_mode_class = 'dark-mode-auto';
}

/* 侧边栏位置 */
$sidebar_position = $vt_config['sidebar_position']=='1' ? 'sidebar-position="left"' : '';
?>

<body class="<?php echo $dark_mode_class ?>" 
    <?=$sidebar_position?>
    >
    
    <?php wp_body_open(); ?>

    <?php
    global $current_user;
    // get_currentuserinfo();
    wp_get_current_user();
    ?>

    <header class="header mobile">
        <div class="header-nav">
            <a href="<?php bloginfo('url') ?>" class="logo" title="" rel="home">
                <img light src="<?= $vt_config['site_logo']; ?>" alt="<?php bloginfo('name') ?>">
                <img darkness src="<?= $vt_config['site_logo_darkness']; ?>" alt="<?php bloginfo('name') ?>">
            </a>
            <a class="top-nav-button dark-mode-button  <?php echo $is_dark_mode ? 'dark' : '' ?>">
                <i class="fa-solid fa-sun sun"></i>
                <i class="fa-solid fa-moon moon"></i>
            </a>
            <a class="top-nav-button search-button" href="javascript:;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </a>
            <a class="top-nav-button menu-button" href="javascript:;">
                <i class="fa-solid fa-bars"></i>
            </a>
            <?php /* if (!$current_user->ID && $vt_config['is_show_login_register']) : ?>
                <a class="top-nav-button login-button open-login-dialog" href="javascript:;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </a>
            <?php endif */ ?>
            
            <?php /* if ($current_user->ID) : ?>
                <?php $avatar = vt_get_custom_avatar_url($current_user->ID) ?>
                <a class="top-nav-button header-top-avatar" href="javascript:;">
                    <img src="<?php echo $avatar ?>" alt="">
                </a>
            <?php endif */ ?>
        </div>

        <!-- 移动端菜单 -->
        <div class="mobile-menu-modal">
            <div class="mobile-menu">
                <div class="action-widget">
                    <a class="action-button close" href="javascript:;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                </div>

                <?php if ($current_user->ID) : ?>
                    <?php
                    $nickname = get_user_meta($current_user->ID, 'nickname', true);
                    $description = get_user_meta($current_user->ID, 'description', true);
                    $avatar = vt_get_custom_avatar_url($current_user->ID);
                    ?>
                    <div class="user-widget-mobile">
                        <div class="user-header">
                            <img src="<?php echo $avatar ?>" class="avatar" alt="Avatar">
                            <div class="user-meta">
                                <div class="user-nickname"><?php echo $nickname ?></div>
                                <div class="user-more"><?php echo $description ?></div>
                            </div>
                        </div>
                        <div class="links-widget">
                            <?php if($vt_config['user_center_is_on']):?>
                                <a href="<?php bloginfo('url') ?>/users/<?php echo $current_user->ID ?>">
                                    <i class="fa-solid fa-user"></i><?= __('个人中心','vt') ?>
                                </a>
                            <?php else: ?>
                                <a href="<?php bloginfo('url') ?>/wp-admin/profile.php">
                                    <i class="fa-solid fa-user"></i><?= __('个人资料','vt') ?>
                                </a>
                            <?php endif ?>
                            
                            <?php if (in_array('administrator', $current_user->roles)) :?>
                                <a href="<?php bloginfo('url') ?>/wp-admin/index.php">
                                    <i class="fa-solid fa-gauge"></i><?= __('后台面板','vt') ?>
                                </a>
                            <?php endif ?>
                            <a href="<?php echo wp_logout_url($current_url); ?>">
                                <i class="fa-solid fa-right-from-bracket"></i><?= __('退出登录','vt') ?>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($vt_config['is_show_login_register']) : ?>
                        <div class="user-widget-mobile">
                            <div class="user-header login-button open-login-dialog">
                                <?php $avatar = vt_get_custom_avatar_url($current_user->ID) ?>
                                <img src="<?=$avatar ?>" class="avatar" alt="Avatar">
                                <div class="user-meta">
                                    <div class="user-nickname" style="cursor: pointer;"><?php _e('请登录', 'vt') ?></div>
                                    <div class="user-more"></div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                <?php endif ?>

                <?php
                $theme_location = "primary";
                if (has_nav_menu('header_main')) {
                    $theme_location = "header_main";
                }
                $nav_str = wp_nav_menu(array(
                    'theme_location'    => $theme_location, 
                    'menu'              => '', 
                    'container'         => false,
                    'container_class'   => '', 
                    'container_id'      => '', 
                    'menu_class'        => 'mobile-ul', 
                    'menu_id'           => '',  
                    'echo'              => false, 
                    'fallback_cb'       => 'MyMenu::fallback',  
                    'before'            => '',
                    'after'             => '',
                    'link_before'       => '',
                    'link_after'        => '',
                    'items_wrap'        => '<ul id="%1$s" class="%2$s">%3$s</ul>',  
                    'depth'             => 4, 
                    'walker'            => new MyMenu()
                ));

                // echo str_replace('sub-menu', 'select', $nav_str);
                echo $nav_str;
                ?>
            </div>
        </div>

        <!-- 移动端搜索对话框 -->
        <div class="mobile-search-modal modal" id="search-modal-dialog">
            <div class="modal-dialog">
                <form action="/" method="get">
                    <div class="modal-header">搜索一下</div>
                    <div class="field field-text">
                        <input type="text" name="s" value="" placeholder="请输入关键词">
                    </div>
                    <div class="field field-button">
                        <button type="submit" class="">搜索</button>
                    </div>
                </form>
                <a class="modal-close-button" href="javascript:;">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        </div>
    </header>


    <!-- large screen -->
    <header class="header pc">
        <div class="header-nav">
            <a href="<?php bloginfo('url') ?>" class="logo" title="" rel="home">
                <img light src="<?= $vt_config['site_logo']; ?>" alt="<?php bloginfo('name') ?>">
                <img darkness src="<?= $vt_config['site_logo_darkness']; ?>" alt="<?php bloginfo('name') ?>">
            </a>

            <?php
            $theme_location = "primary";
            if (has_nav_menu('header_main')) { $theme_location = "header_main"; }
            
            $nav_str = wp_nav_menu(array(
                'theme_location'    => $theme_location, 
                'menu'              => '',
                'container'         => false,
                'container_class'   => '',
                'container_id'      => '',
                'menu_class'        => 'pc-nav',
                'menu_id'           => '', 
                'echo'              => false, 
                'fallback_cb'       => 'MyMenu::fallback', 
                'before'            => '',
                'after'             => '',
                'link_before'       => '',
                'link_after'        => '',
                'items_wrap'        => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'depth'             => '4', 
                'walker'            => new MyMenu()   
            ));
            
            echo $nav_str;
            ?>
            
            
            <div class="header-button-group">
                <a class="nav-button search-toggle-button" href="javascript:;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </a>
                <?php if($vt_config['dark_mode_type'] == 1): ?>
                    <a class="nav-button dark-mode-button  <?php echo $is_dark_mode ? 'dark' : '' ?>">
                        <i class="fa-solid fa-sun sun"></i>
                        <i class="fa-solid fa-moon moon"></i>
                    </a>
                <?php endif; ?>
                <?php if (!$current_user->ID && $vt_config['is_show_login_register']) : ?>
                    <a href="javascript:;" class="button login-button open-login-dialog"><?= __('登录','vt')?></a>
                <?php endif ?>
            </div>

            <?php if ($current_user->ID) : ?>
                <?php $avatar = vt_get_custom_avatar_url($current_user->ID) ?>
                <a href="javascript:;" class="header-top-avatar">
                    <img src="<?php echo $avatar ?>" alt="">
                </a>
            <?php endif ?>
            
            <!-- 用户登录后的弹窗 -->
            <?php if ($current_user->ID) : ?>
                <?php
                $nickname = get_user_meta($current_user->ID, 'nickname', true);
                $description = get_user_meta($current_user->ID, 'description', true);
                ?>
                <div class="user-widget">
                    <div class="user-header">
                        <img src="<?php echo $avatar ?>" class="avatar">
                        <div class="user-meta">
                            <div class="user-nickname"><?php echo $nickname ?></div>
                            <div class="user-more"><?php echo $description ?></div>
                        </div>
                    </div>
                    <div class="links-widget">
                        <?php if($vt_config['user_center_is_on']):?>
                            <a href="<?php bloginfo('url') ?>/users/<?php echo $current_user->ID ?>">
                                <i class="fa-solid fa-user"></i><?= __('个人中心','vt') ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php bloginfo('url') ?>/wp-admin/profile.php">
                                <i class="fa-solid fa-user"></i><?= __('个人资料','vt') ?>
                            </a>
                        <?php endif ?>
                        
                        <?php if (in_array('administrator', $current_user->roles)) :?>
                            <a href="<?php bloginfo('url') ?>/wp-admin/index.php">
                                <i class="fa-solid fa-gauge"></i><?= __('后台面板','vt') ?>
                            </a>
                        <?php endif ?>
                        <a href="<?php echo wp_logout_url($current_url); ?>">
                            <i class="fa-solid fa-right-from-bracket"></i><?= __('退出登录','vt') ?>
                        </a>
                    </div>
                </div>
            <?php endif ?>
        </div>
        
        <!-- 搜索弹窗 -->
        <div class="search-widget">
            <div class="close-widget">
                <a href="javascript:;"><i class="fa-solid fa-xmark"></i></a>
            </div>
            <form method="get" class="search" action="/">
                <input class="keyword" type="text" name="s" placeholder="<?= __('搜索','vt')?>..." value="<?php echo get_search_query(); ?>">
                <button class="button search-button" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </header>

    <!-- 登录/注册/找回密码对话框 -->
    <div class="vt-dialog login-register-dialog" id="login-register-dialog">
        <div class="dialog-widget">
            <div class="dialog-header">
                <div class="title">用户登录</div>
                <a class="close close-button" href="javascript:;">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
            <div class="dialog-body">
                <div class="auth-tabs">
                    <div class="tabs-nav">
                        <button type="button" class="tab-btn active" data-tab="login">登录</button>
                        <button type="button" class="tab-btn" data-tab="register">注册</button>
                        <button type="button" class="tab-btn" data-tab="forgot">找回密码</button>
                    </div>
                    
                    <!-- 登录表单 -->
                    <div class="tab-content active" id="tab-login">
                        <form id="login-form">
                            <div class="field field-text">
                                <label for="login-username">用户名或邮箱</label>
                                <input type="text" id="login-username" name="username" required>
                            </div>
                            <div class="field field-text">
                                <label for="login-password">密码</label>
                                <input type="password" id="login-password" name="password" required>
                            </div>
                            <div class="field field-button">
                                <button type="submit" class="btn primary">登录</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- 注册表单 -->
                    <div class="tab-content" id="tab-register">
                        <form id="register-form">
                            <div class="field field-text">
                                <label for="register-username">用户名</label>
                                <input type="text" id="register-username" name="username" required>
                            </div>
                            <div class="field field-text">
                                <label for="register-email">邮箱</label>
                                <input type="email" id="register-email" name="email" required>
                            </div>
                            <div class="field field-text">
                                <label for="register-password">密码</label>
                                <input type="password" id="register-password" name="password" required>
                            </div>
                            <div class="field field-text">
                                <label for="register-confirm-password">确认密码</label>
                                <input type="password" id="register-confirm-password" name="confirm_password" required>
                            </div>
                            <div class="field field-text">
                                <label for="register-verification-code">验证码</label>
                                <div class="verification-input-group">
                                    <input type="text" id="register-verification-code" name="verification_code" placeholder="请输入验证码" maxlength="6" required>
                                    <button type="button" class="btn secondary" id="send-verification-code">发送验证码</button>
                                </div>
                            </div>
                            <div class="field field-button">
                                <button type="submit" class="btn primary">注册</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- 找回密码表单 -->
                    <div class="tab-content" id="tab-forgot">
                        <form id="forgot-form-step1">
                            <div class="field field-text">
                                <label for="forgot-email">邮箱</label>
                                <input type="email" id="forgot-email" name="email" required>
                            </div>
                            <div class="field field-button">
                                <button type="submit" class="btn primary">发送验证码</button>
                            </div>
                        </form>
                        
                        <form id="forgot-form-step2" style="display:none;">
                            <div class="field field-text">
                                <label for="forgot-code">验证码</label>
                                <input type="text" id="forgot-code" name="code" placeholder="请输入收到的验证码" maxlength="6" required>
                            </div>
                            <div class="field field-text">
                                <label for="forgot-new-password">新密码</label>
                                <input type="password" id="forgot-new-password" name="new_password" placeholder="请输入新密码" required>
                            </div>
                            <div class="field field-text">
                                <label for="forgot-confirm-password">确认新密码</label>
                                <input type="password" id="forgot-confirm-password" name="confirm_new_password" placeholder="请再次输入新密码" required>
                            </div>
                            <div class="field field-button">
                                <button type="submit" class="btn primary">重置密码</button>
                            </div>
                        </form>
                        
                        <div id="forgot-back" style="display:none; text-align:center; margin-top:15px;">
                            <a href="javascript:;" onclick="showForgotStep1()">← 返回输入邮箱</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 传递WordPress的AJAX URL和nonce到JavaScript
        var ajax_object = {
            ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>",
            nonce: "<?php echo wp_create_nonce('ajax_nonce'); ?>"
        };
        
        // 显示第一步：输入邮箱
        function showForgotStep1() {
            document.getElementById('forgot-form-step1').style.display = 'block';
            document.getElementById('forgot-form-step2').style.display = 'none';
            document.getElementById('forgot-back').style.display = 'none';
        }
        
        // 显示第二步：输入验证码和新密码
        function showForgotStep2() {
            document.getElementById('forgot-form-step1').style.display = 'none';
            document.getElementById('forgot-form-step2').style.display = 'block';
            document.getElementById('forgot-back').style.display = 'block';
        }
    </script>
</body>
</html>