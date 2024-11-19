<?php

$vt_config = vt_get_config();

if (!$vt_config['frontend_is_on']) {
    header('HTTP/1.1 403 Forbidden');
    echo '{error:"403 当前主题关闭了前端，仅能通过接口访问"}';
    exit();
}

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


    <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/assets/lib/swiper/swiper-bundle.min.css" />
    <script src="<?php bloginfo('template_url'); ?>/assets/lib/swiper/swiper-bundle.min.js"></script>

    <?php
    // 自定义颜色
    $primary_color = $vt_config['basic_style_color'];
    $hover_color = $vt_config['basic_style_color'];
    switch ($vt_config['basic_style']) {
        case "0": break;
        case "1": $primary_color = '#007aff'; $hover_color = '#007aff'; break;
        case "2": $primary_color = '#dd524d'; $hover_color = '#dd524d'; break;
        case "3": $primary_color = '#535353'; $hover_color = '#535353'; break;
        case "4": $primary_color = '#62ad4c'; $hover_color = '#62ad4c'; break;
        default: $primary_color = '#007aff'; $hover_color = '#007aff';
    }
    echo "<style> :root{ --vt-color-primary:{$primary_color}; --vt-color-primary-hover:{ $hover_color}; }  </style>";
    ?>

    <?php if($vt_config['background_image']): ?>
    <style>body:before { background-image:url(<?php echo $vt_config['background_image']?>) }</style>
    <?php endif ?>
    
    <style> .card-item .card-image { padding-bottom: <?php echo $vt_config['image_items_height'] ?> !important; } </style>
</head>

<?php
global $widget_title_class;
$widget_title_class = $vt_config['widget_title_type'] ? ' type-'.$vt_config['widget_title_type'] : '';

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

/* logo 闪光 */
$flash_class = $vt_config['logo_is_flashing'] ? 'flash' : '';
?>

<body class="<?php echo $dark_mode_class ?>">
    <?php wp_body_open(); ?>

   
    <?php
    global $current_user;
    get_currentuserinfo();
    ?>

    <header class="header mobile">
        <div class="header-nav">
            <a class="top-nav-button search-button" href="javascript:;">
                <i class="iconfont">&#xe8aa;</i>
            </a>
            <a href="<?php bloginfo('siteurl') ?>" class="logo <?php echo $flash_class?>" title="" rel="home">
                <img src="<?php echo $vt_config['site_logo']; ?>" alt="<?php bloginfo('name') ?>">
            </a>
            <a class="top-nav-button menu-button" href="javascript:;">
                <i class="iconfont">&#xe7f6;</i>
            </a>
        </div>

        <!-- 移动端菜单 -->
        <div class="mobile-menu-modal">
            <div class="mobile-menu">
                <div class="action-widget">
                    <?php if ($current_user->ID && $vt_config['is_show_login_register']) : ?>
                        <a class="action-button" href="<?php bloginfo('siteurl') ?>/wp-admin/index.php">
                            <i class="iconfont">&#xe8a5;</i>
                        </a>
                    <?php endif ?>
                    <?php if($vt_config['dark_mode_type'] == 1): ?>
                        <a class="action-button dark-mode-button  <?php echo $is_dark_mode ? 'dark' : '' ?>">
                            <i class="iconfont sun">&#xe7ff;</i>
                            <i class="iconfont moon">&#xe804;</i>
                        </a>
                    <?php endif; ?>
                    <a class="action-button close" href="javascript:;">
                        <i class="iconfont">&#xe75e;</i>
                    </a>
                </div>

                <div class="site-logo">
                    <img src="<?php echo $vt_config['site_logo']?>">
                </div>
                
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
                <a class="modal-close-button" href="javascript:;"><i class="iconfont">&#xe75e;</i></a>
            </div>
        </div>
    </header>


    <!-- large screen -->
    <header class="header pc">
        <div class="header-nav">
            <a href="<?php bloginfo('siteurl') ?>" class="logo <?php echo $flash_class?>" title="" rel="home">
                <img src="<?php echo $vt_config['site_logo']; ?>" alt="<?php bloginfo('name') ?>">
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
                    <i class="iconfont">&#xe8aa;</i>
                </a>
                <?php if($vt_config['dark_mode_type'] == 1): ?>
                    <a class="nav-button dark-mode-button  <?php echo $is_dark_mode ? 'dark' : '' ?>">
                        <i class="iconfont sun">&#xe7ff;</i>
                        <i class="iconfont moon">&#xe804;</i>
                    </a>
                <?php endif; ?>
                <?php if (!$current_user->ID && $vt_config['is_show_login_register']) : ?>
                    <a href="/wp-login.php" class="button login-button">登录</a>
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
                            <a href="<?php bloginfo('siteurl') ?>/users/<?php echo $current_user->ID ?>">
                                <i class="iconfont">&#xe8a5;</i>个人中心
                            </a>
                        <?php else: ?>
                            <a href="<?php bloginfo('siteurl') ?>/wp-admin/profile.php">
                                <i class="iconfont">&#xe8a5;</i>个人资料
                            </a>
                        <?php endif ?>
                        
                        <?php if (in_array('administrator', $current_user->roles)) :?>
                            <a href="<?php bloginfo('siteurl') ?>/wp-admin/index.php">
                                <i class="iconfont">&#xe763;</i>后台面板
                            </a>
                        <?php endif ?>
                        <a href="<?php echo wp_logout_url('/'); ?>"><i class="iconfont">&#xe7c1;</i>退出登录</a>
                    </div>
                </div>
            <?php endif ?>
        </div>
        
        <!-- 搜索弹窗 -->
        <div class="search-widget">
            <div class="close-widget">
                <a href="javascript:;"><i class="iconfont">&#xe75e;</i></a>
            </div>
            <form method="get" class="search" action="/">
                <input class="keyword" type="text" name="s" placeholder="搜索..." value="<?php echo get_search_query(); ?>">
                <button class="button search-button" type="submit">
                    <i class="iconfont">&#xe8aa;</i>
                </button>
            </form>
        </div>
    </header>
