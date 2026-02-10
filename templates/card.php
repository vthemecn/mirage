<?php
global $post;
$target = 'target="_blank"';
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
        </a>
    </div>
    <div class="vt-card-body">
        <h3 class="vt-card-title">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" <?=$target?>>
                <?php the_title(); ?>
            </a>
        </h3>

        <?php if (has_excerpt()) : ?>
            <div class="vt-card-excerpt">
                <?php the_excerpt(); ?>
            </div>
        <?php else : ?>
            <div class="vt-card-excerpt">
                <?php echo wp_trim_words(get_the_content(), 100, '...'); ?>
            </div>
        <?php endif; ?>
        
        <div class="vt-card-meta no-scrollbar">
            <?php
            $categories = get_the_category();
            if ($categories) :
            ?>
                <?php foreach ($categories as $category) : ?>
                    <a href="<?php echo esc_url(get_tag_link($category->term_id)); ?>" class="vt-card-tag">
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
            <a class="vt-card-author" href="" target="_blank">
                <div class="avatar"><img src="<?=$avatar?>"></div>
                <span><?= get_the_author_meta('nickname', $post->post_author) ?></span>
            </a>
            <div class="vt-card-time">
                <i class="fas fa-clock"></i>
                <?php echo get_the_time('Y-m-d'); ?>
            </div>
            <div class="vt-card-stats">
                <span class="vt-user-post-stat-item">
                    <i class="fas fa-eye"></i>
                    <?php echo get_post_views(get_the_ID()); ?>
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