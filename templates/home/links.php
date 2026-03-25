<?php
/**
 * 友情链接
 */


// 获取链接数据
$links = get_bookmarks(array(
    'orderby' => 'rating',
    'order' => 'DESC',
    'limit' => -1
));
?>

<div class="main-container links">
    <div class="home-header">
        <?= vt_get_config('links_title') ?>
    </div>
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
</div>