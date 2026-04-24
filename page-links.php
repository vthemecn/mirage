<?php

/**
 * Template Name: Links
 * Template Post Type: page
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();

// 获取所有链接分类
$link_categories = get_categories(array(
    'taxonomy' => 'link_category',
    'hide_empty' => false
));

$full_width = 0;

?>

<div class="main-container page-link">
    <div class="main-widget">
        <div class="article-widget">
            <div class="page-title">
                <?php the_title(); ?>
                
                <?php if( current_user_can( 'manage_options' ) ): ?>
                    <a href="/wp-admin/post.php?post=<?php echo get_the_ID() ?>&action=edit" class="page-edit-action">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span><?=__('Edit','vt')?></span>
                    </a>
                <?php endif ?>
            </div>

            <?php if (!empty($link_categories)): ?>
                <?php foreach ($link_categories as $category): ?>
                    <?php
                    // 获取当前分类下的链接
                    $links = get_bookmarks(array(
                        'category' => $category->term_id,
                        'orderby' => 'rating',
                        'order' => 'DESC',
                        'limit' => -1
                    ));
                    
                    // 如果该分类下有链接才显示
                    if (!empty($links)):
                    ?>
                    <div class="links-widget">
                        <?php if ($category->name != 'Uncategorized' && $category->name != __('Uncategorized','vt')): ?>
                            <div class="widget-title"><?php echo esc_html($category->name); ?></div>
                        <?php endif; ?>
                        
                        <div class="link-items">
                            <?php foreach($links as $link): ?>
                            <div class="link-item">
                                <?php
                                $link_target = !empty($link->link_target) ? 'target="' . esc_attr($link->link_target) . '"' : '';
                                ?>
                                <a href="<?php echo esc_url($link->link_url); ?>" <?php echo $link_target; ?> rel="nofollow">
                                </a>
                                <?php if ($link->link_image): ?>
                                    <img src="<?php echo esc_url($link->link_image); ?>" alt="<?php echo esc_attr($link->link_name); ?>">
                                <?php else: ?>
                                    <img src="<?php echo THEME_URL.'/assets/images/link.png'; ?>" alt="<?php echo esc_attr($link->link_name); ?>">
                                <?php endif; ?>
                                <div class="link-body">
                                    <div class="link-title"><?php echo esc_html($link->link_name); ?></div>
                                    <div class="link-description"><?php echo esc_html($link->link_description); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <?php
                // 如果没有分类，显示所有链接
                $links = get_bookmarks(array(
                    'orderby' => 'rating',
                    'order' => 'DESC',
                    'limit' => -1
                ));
                ?>
                <div class="links-widget">
                    <?php if (!empty($links)): ?>
                        <?php foreach($links as $link): ?>
                        <div class="link-item">
                            <?php
                            $link_target = !empty($link->link_target) ? 'target="' . esc_attr($link->link_target) . '"' : '';
                            ?>
                            <a href="<?php echo esc_url($link->link_url); ?>" <?php echo $link_target; ?> rel="nofollow">
                            </a>
                            <?php if ($link->link_image): ?>
                                <img src="<?php echo esc_url($link->link_image); ?>" alt="<?php echo esc_attr($link->link_name); ?>">
                            <?php else: ?>
                                <img src="<?php echo THEME_URL.'/assets/images/link.png'; ?>" alt="<?php echo esc_attr($link->link_name); ?>">
                            <?php endif; ?>
                            <div class="link-body">
                                <div class="link-title"><?php echo esc_html($link->link_name); ?></div>
                                <div class="link-description"><?php echo esc_html($link->link_description); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div><!-- .main-widget -->

    <?php if(!$full_width):?>
        <div class="sider">
            <?php if ( is_active_sidebar( 'default-sidebar' ) ) { ?>
                <?php dynamic_sidebar( 'default-sidebar' ); ?>
            <?php } ?>

            <?= vt_theme_ad('ad_sidebar'); ?>
        </div>
    <?php endif; ?>
</div>


<?php get_footer(); ?>