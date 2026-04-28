<?php
/**
 * Copyright (c) vtheme.cn
 *
 * @author vthemecn <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

$current_url = home_url(add_query_arg(array(), $wp->request));

/* 暗黑模式 */
$dark_mode_config = vt_init_dark_mode();
$is_dark_mode = $dark_mode_config['is_dark_mode'];
$dark_mode_class = $dark_mode_config['dark_mode_class'];

?>


<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="api-url" content="<?php echo home_url() ?>">
    <?php wp_head(); ?>

    <?php
    require_once THEME_DIR . '/templates/seo.php';
    require_once THEME_DIR . '/templates/css.php';
    ?>

    <link rel="stylesheet" type="text/css" href="<?= THEME_URL; ?>/assets/lib/fontawesome/css/fontawesome.min.css" />
    <link rel="stylesheet" type="text/css" href="<?= THEME_URL; ?>/assets/lib/fontawesome/css/solid.min.css" />

    <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/assets/lib/swiper/swiper-bundle.min.css" />
    <script src="<?php bloginfo('template_url'); ?>/assets/lib/swiper/swiper-bundle.min.js"></script>

    <?php
    $ajax_data = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('ajax_nonce'),
        'wp_rest_nonce'    => wp_create_nonce('wp_rest'),
        'current_user_id' => get_current_user_id()
    );?>
    
    <script type="text/javascript">
    var ajax_object = <?php echo json_encode($ajax_data); ?>;
    </script>
</head>


<body class="<?php echo $dark_mode_class ?>">
    
    <?php wp_body_open(); ?>

    <?php
    global $current_user;
    // get_currentuserinfo();
    wp_get_current_user();
    ?>

    <header class="header mobile">
        <div class="header-nav">
            <a href="<?php bloginfo('url') ?>" class="logo" title="" rel="home">
                <?php if(vt_get_config('site_logo', '') == ''):?>
                    <?=get_bloginfo('name')?>
                <?php else: ?>
                    <img light src="<?= vt_get_config('site_logo', ''); ?>" alt="<?php bloginfo('name') ?>">
                    <img darkness src="<?= vt_get_config('site_logo_darkness', ''); ?>" alt="<?php bloginfo('name') ?>">
                <?php endif; ?>
            </a>
            <?php if(vt_get_config('dark_mode_type', 2) != 0): ?>
            <a class="top-nav-button dark-mode-button  <?php echo $is_dark_mode ? 'dark' : '' ?>">
                <i class="fa-solid fa-sun sun"></i>
                <i class="fa-solid fa-moon moon"></i>
            </a>
            <?php endif; ?>
            <a class="top-nav-button search-button" href="javascript:;">
                <i class="fa-solid fa-magnifying-glass"></i>
            </a>
            <a class="top-nav-button menu-button" href="javascript:;">
                <i class="fa-solid fa-bars"></i>
            </a>
            <?php /* if (!$current_user->ID && vt_get_config('is_show_login_register', true)) : ?>
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
                            <?php echo get_avatar($current_user->ID, 80, '', '', ''); ?>
                            <div class="user-meta">
                                <div class="user-nickname"><?php echo $nickname ?></div>
                            </div>
                        </div>
                        <div class="user-links-widget">
                            <a href="<?php bloginfo('url') ?>/account">
                                <i class="fa-solid fa-user"></i><?= __('Profile','vt') ?>
                            </a>
                                
                            <?php if (in_array('administrator', $current_user->roles)) :?>
                                <a href="<?php bloginfo('url') ?>/wp-admin/index.php">
                                    <i class="fa-solid fa-gauge"></i><?= __('Dashboard','vt') ?>
                                </a>
                            <?php endif ?>
                            <a href="<?php echo wp_logout_url($current_url); ?>">
                                <i class="fa-solid fa-right-from-bracket"></i><?= __('Logout','vt') ?>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if(vt_get_config('hide_login_button', true) == false):?>
                        <div class="user-widget-mobile">
                            <div class="user-header login-button open-login-dialog">
                                <?php echo get_avatar($current_user->ID, 80, '', '', ''); ?>
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
                    <div class="modal-header"><?= __('Search','vt')?></div>
                    <div class="field field-text">
                        <input type="text" name="s" value="" placeholder="<?= __('Search','vt')?>...">
                    </div>
                    <div class="field field-button">
                        <button type="submit" class=""><?= __('Search','vt')?></button>
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
                <?php if(vt_get_config('site_logo', '') == ''):?>
                    <?=get_bloginfo('name')?>
                <?php else: ?>
                    <img light src="<?= vt_get_config('site_logo', ''); ?>" alt="<?php bloginfo('name') ?>">
                    <img darkness src="<?= vt_get_config('site_logo_darkness', ''); ?>" alt="<?php bloginfo('name') ?>">
                <?php endif; ?>
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
                <?php if(vt_get_config('dark_mode_type', 2) != 0): ?>
                    <a class="nav-button dark-mode-button  <?php echo $is_dark_mode ? 'dark' : '' ?>">
                        <i class="fa-solid fa-sun sun"></i>
                        <i class="fa-solid fa-moon moon"></i>
                    </a>
                <?php endif; ?>
                <?php if(vt_get_config('hide_login_button', true) == false):?>
                    <?php if (!$current_user->ID) : ?>
                        <a href="javascript:;" class="button login-button open-login-dialog"><?= __('Login','vt')?></a>
                    <?php endif ?>
                <?php endif?>
            </div>


            <?php if ($current_user->ID) : ?>
                <?php // $avatar = vt_get_custom_avatar_url($current_user->ID) ?>
                <div class="header-top-profile">
                    <a href="javascript:;" class="header-top-avatar">
                        <?php echo get_avatar($current_user->ID, 80, '', '', ''); ?>
                    </a>

                    <!-- 用户登录后的弹窗 -->
                    <?php
                    $nickname = get_user_meta($current_user->ID, 'nickname', true);
                    $description = get_user_meta($current_user->ID, 'description', true);
                    ?>
                    <div class="user-widget">
                        <div class="user-header">
                            <?php echo get_avatar($current_user->ID, 80, '', '', ''); ?>
                            <div class="user-meta">
                                <div class="user-nickname"><?php echo $nickname ?></div>
                                <div class="user-more"><?php echo $description ?></div>
                            </div>
                        </div>
                        <div class="user-links-widget">
                            <a href="<?php bloginfo('url') ?>/account">
                                <i class="fa-solid fa-user"></i><?= __('Profile','vt') ?>
                            </a>

                            <?php if (in_array('administrator', $current_user->roles)) :?>
                                <a href="<?php bloginfo('url') ?>/wp-admin/index.php">
                                    <i class="fa-solid fa-gauge"></i><?= __('Dashboard','vt') ?>
                                </a>
                            <?php endif ?>
                            
                            <a href="<?php echo wp_logout_url($current_url); ?>">
                                <i class="fa-solid fa-right-from-bracket"></i><?= __('Logout','vt') ?>
                            </a>
                        </div>
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
                <input class="keyword" type="text" name="s" placeholder="<?= __('Search','vt')?>..." value="<?php echo get_search_query(); ?>">
                <button class="button search-button" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </header>
