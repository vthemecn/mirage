<?php
global $post;
$target = false ? 'target="_blank"' : '';
$avatar = vt_get_custom_avatar_url($post->post_author);

// 获取主题URL常量
$theme_url = defined('THEME_URL') ? THEME_URL : get_template_directory_uri();
?>
<div class="vt-card-item">
    <div class="vt-card-thumbnail">
        <a href="<?php the_permalink(); ?>" <?=$target?> >
            <?php if (has_post_thumbnail()) : ?>
                <?php 
                $thumbnail_id = get_post_thumbnail_id();
                $thumbnail_size = !empty($post->vt_image_size) ? $post->vt_image_size : 'medium';
                $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, $thumbnail_size)[0];
                $placeholder_url = $theme_url . '/assets/images/placeholder.svg';
                ?>
                <img class="lazyload-img" 
                     data-src="<?= esc_url($thumbnail_url); ?>" 
                     src="<?= esc_url($placeholder_url); ?>" 
                     alt="<?php the_title_attribute(); ?>" />
            <?php else : ?>
                <img src="<?= esc_url($theme_url . '/assets/images/default.jpg'); ?>" alt="<?php the_title_attribute(); ?>" />
            <?php endif; ?>
            <div class="vt-card-badget-widget">
                <?php if(is_sticky()): ?>
                    <div class="vt-card-badget sticky"><?=__('Sticky','vt')?></div>
                <?php endif ?>
                <?php /* <div class="vt-card-badget vip">VIP</div> */ ?>
            </div>
        </a>
    </div>
    <div class="vt-card-body">
        <h3 class="vt-card-title">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" <?=$target?>>
                <?php the_title(); ?>
            </a>
        </h3>

        <div class="vt-card-excerpt">
            <?= vt_clean_the_excerpt(get_the_excerpt()); ?>
        </div>

        <div class="vt-card-meta no-scrollbar">
            <?php
            $categories = get_the_category();
            if ($categories) :
            ?>
                <?php foreach ($categories as $category) : ?>
                    <a href="<?php echo esc_url(get_tag_link($category->term_id)); ?>" class="vt-card-tag vt-category">
                        <i class="fas fa-folder"></i>
                        <?php echo esc_html($category->name); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php
            $tags = get_the_tags();
            if ($tags) :
            ?>
                <?php foreach ($tags as $tag) : ?>
                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="vt-card-tag">
                        <i class="fas fa-tags"></i>
                        <?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="vt-card-footer">
            <?php if(!is_captain_active()):?>
                <div class="vt-card-author">
                    <div class="avatar"><img src="<?=$avatar?>"></div>
                    <span><?= get_the_author_meta('display_name', $post->post_author) ?></span>
                </div>
            <?php else: ?>
                <?php $profile_url = home_url('/user/' . $post->post_author)?>
                <a class="vt-card-author" href="<?=$profile_url?>">
                    <div class="avatar"><img src="<?=$avatar?>"></div>
                    <span><?= get_the_author_meta('display_name', $post->post_author) ?></span>
                </a>
            <?php endif; ?>
            <div class="vt-card-time"  data-time="<?= get_the_time('Y-m-d H:i:s') ?>">
                <i class="fas fa-clock"></i>
                <?php echo vt_get_time(get_the_time('Y-m-d H:i:s') ); ?>
            </div>
            <div class="vt-card-stats">
                <span class="vt-user-post-stat-item">
                    <i class="fas fa-eye"></i>
                    <?php echo vt_format_view_count(get_post_views(get_the_ID())); ?>
                </span>
                <span class="vt-user-post-stat-item">
                    <i class="fas fa-heart"></i>
                    <?php echo get_post_meta(get_the_ID(), '_likes_count', true) ?: 0; ?> 
                </span>
                <span class="vt-user-post-stat-item">
                    <i class="fas fa-comment"></i>
                    <?php echo get_comments_number(); ?> 
                </span>
            </div>
        </div>
    </div>
</div>