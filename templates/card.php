<?php
global $post;
$target = false ? 'target="_blank"' : '';
$avatar = vt_get_custom_avatar_url($post->post_author);
?>
<div class="vt-card-item">
    <div class="vt-card-thumbnail">
        <a href="<?php the_permalink(); ?>" <?=$target?> >
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('media'); ?>
            <?php else : ?>
                <img src="<?= esc_url( TEMP_URL . '/assets/images/default.jpg'); ?>" alt="<?php the_title_attribute(); ?>" />
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
            <?php if (has_excerpt()) : ?>
                <?php the_excerpt(); ?>
            <?php else : ?>
                <?php echo wp_trim_words(get_the_content(), 100, '...'); ?>
            <?php endif; ?>
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
                <?php $profile_url = home_url('/profile/' . $post->post_author)?>
                <a class="vt-card-author" href="$profile_url">
                    <div class="avatar"><img src="<?=$avatar?>"></div>
                    <span><?= get_the_author_meta('display_name', $post->post_author) ?></span>
                </a>
            <?php endif; ?>
            <div class="vt-card-time">
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