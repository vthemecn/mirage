<?php
/**
 * 
 */


//禁止新版小工具
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false');

function vt_widgets_init() {
    $args = array(
        'name' => __( 'Default Sidebar', 'vt' ),
        'id' => 'default-sidebar',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    );
    register_sidebar($args);

    $args = array(
        'name' => __( 'Post Sidebar', 'vt' ),
        'id' => 'posts-sidebar',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    );
    register_sidebar($args);
}
add_action('init', 'vt_widgets_init');


/**
 * Recent Posts
 */
class ArticleWidget extends WP_Widget {
    function __construct(){
        parent::__construct( 'image-article-list', '[Mirage] '.__( 'Recent Posts', 'vt' ), array( 'description' => __('Recent Posts', 'vt' ) ) );
    }
    function widget( $args, $instance ) {
        extract( $args, EXTR_SKIP );
        echo $before_widget;
    
        // 标题
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'vt' );
        $posts_per_page = (int) ( $instance['posts_per_page']) > 0 ? (int) ( $instance['posts_per_page']) : 4;

        // 构建查询参数
        $query_args = array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' =>  $posts_per_page,
            'ignore_sticky_posts' => true
        );
    
        // 如果指定了分类 ID，才加入分类条件
        if ( ! empty( $instance['cat_id'] ) ) {
            $query_args['cat'] = (int) $instance['cat_id'];
        }
    
        $query = new WP_Query( $query_args );
    
        // 输出 HTML
        ?>
        <div class="recent-posts widget-container">
            <div class="widget-header">
                <div class="widget-title"><?php echo esc_html( $title ); ?></div>
            </div>
            <div class="recent-posts-wrapper">
                <?php if ( $query->have_posts() ) : ?>
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <?php get_template_part('templates/card'); ?>
                    <?php endwhile; ?>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
        <?php
        echo $after_widget;
    }

    function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $posts_per_page = !empty($instance['posts_per_page']) ? $instance['posts_per_page'] : '';
        $cat_id = !empty($instance['cat_id']) ? $instance['cat_id'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('title','vt') ?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?= __('count','vt') ?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>" value="<?php echo esc_attr($posts_per_page); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('cat_id'); ?>"><?= __('Category ID','vt') ?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('cat_id'); ?>" name="<?php echo $this->get_field_name('cat_id'); ?>" value="<?php echo esc_attr($cat_id); ?>">
        </p>
        <?php
    }

    function update( $new_instance, $old_instance ) {
        return array(
            'title'          => sanitize_text_field( $new_instance['title'] ?? '' ),
            'posts_per_page' => (int) ( $new_instance['posts_per_page'] ?? 4 ),
            'cat_id'         => (int) ( $new_instance['cat_id'] ?? 0 ),
        );
    }
}


/**
 * Popular
 */
class HotWidget extends WP_Widget {
    function __construct(){
       parent::__construct( 'hot-list', '[Mirage] '.__( 'Popular', 'vt' ), array( 'description' => __( 'Popular', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        $title = !empty($instance['title']) ? $instance['title'] : __('Popular', 'vt');
        $posts_per_page = (int) ( $instance['posts_per_page']) > 0 ? (int) ( $instance['posts_per_page']) : 4;

        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $posts_per_page,
            'meta_key'  => 'post_views_count',
            'orderby'   => 'meta_value',
            'order' => 'DESC',
            'ignore_sticky_posts' => true
        );
        $query = new WP_Query($args);
        ?>
        <div class="hot widget-container">
            <div class="widget-header">
                <div class="widget-title"><?php echo $title ?></div>
            </div>
            <ul class="hot-list">
                <?php if ( $query->have_posts() ) : ?>
                    <?php while ( $query->have_posts() ) : ?>
                        <?php $query->the_post(); ?>
                        <li>
                            <div class="hot-order"></div>
                            <div class="item">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                <div class="time">
                                    <?= vt_get_time(get_the_time('Y-m-d H:i:s')) ?>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </ul>
        </div>
        <?php
        echo $after_widget;
    }

    function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $posts_per_page = !empty($instance['posts_per_page']) ? $instance['posts_per_page'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('title','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?= __('count','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>" value="<?php echo esc_attr($posts_per_page); ?>">
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['posts_per_page'] = (!empty($new_instance['posts_per_page'])) ? strip_tags($new_instance['posts_per_page']) : '';
        return $instance;
    }
}

/**
 * 分类列表
 */
class CategoryWidget extends WP_Widget {
    function __construct(){
        parent::__construct( 'category-list', '[Mirage] '.__( 'Category List', 'vt' ), array( 'description' => __( 'Category List', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        $title = isset($instance['title']) ? $instance['title'] : __('Category List', 'vt');
        ?>
        <div class="category widget-container">
            <div class="widget-header">
                <div class="widget-title">
                    <?php echo $title ?>
                </div>
            </div>
            <div class="category-list">
                <?php
                $theme_location = "primary";
                if (has_nav_menu('side_menu')) {
                    $theme_location = "side_menu";
                }
                $nav_str = wp_nav_menu(array(
                    'theme_location'    => $theme_location, 
                    'menu'              => '', 
                    'container'         => false,
                    'container_class'   => '', 
                    'container_id'      => '', 
                    'menu_class'        => 'side-menu', 
                    'menu_id'           => '',  
                    'echo'              => false, 
                    'fallback_cb'       => 'SideMenu::fallback',  
                    'before'            => '',
                    'after'             => '',
                    'link_before'       => '',
                    'link_after'        => '',
                    'items_wrap'        => '<ul id="%1$s" class="%2$s">%3$s</ul>',  
                    'depth'             => 2, 
                    'walker'            => new SideMenu()
                ));

                // echo str_replace('sub-menu', 'select', $nav_str);
                echo $nav_str;
                ?>
            </div>
        </div>
        <?php
        echo $after_widget;
    }

    function form($instance) {
        ?>
        <p>
            <?= __('Please create theme sidebar menu','vt') ?>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        return $instance;
    }
}




/**
 * 用户卡片
 */
class UserWidget extends WP_Widget {
    function __construct(){
        parent::__construct( 'user-widget', '[Mirage] '.__( '用户卡片', 'vt' ), array( 'description' => __( '用户卡片描述', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        global $wpdb;
        wp_reset_postdata();

        $title = $instance['title'];

        $user_id = get_the_author_meta('ID');
        $avatar_url = get_template_directory_uri() . '/assets/images/avatar.jpg';
        $avatar_id = get_the_author_meta('user_avatar_attachment_id');
        $avatar = wp_get_attachment_image_src($avatar_id, 'medium');
        $avatar_url = isset($avatar[0]) ? $avatar[0] : $avatar_url;

        $nickname = get_user_meta($user_id, 'nickname', true);

        $sql = "SELECT count(id) AS counter FROM wp_posts WHERE post_author=%d AND post_type='post' AND post_status='publish'";
        $res = $wpdb->get_results($wpdb->prepare($sql, [$user_id]), ARRAY_A);
        $posts_counter = $res[0]['counter'] ? $res[0]['counter'] : 0;

        $sql = "SELECT count(comment_ID) AS counter FROM wp_comments WHERE user_id=%d";
        $res = $wpdb->get_results($wpdb->prepare($sql, [$user_id]), ARRAY_A);
        $comments_counter = $res[0]['counter'] ? $res[0]['counter'] : 0;

        $sql = "SELECT count(id) AS counter FROM wp_vt_star WHERE type='like' AND user_id=%d";
        $res = $wpdb->get_results($wpdb->prepare($sql, [$user_id]), ARRAY_A);
        $like_counter = $res[0]['counter'] ? $res[0]['counter'] : 0;

        ?>

        <?php if($user_id):?>
            <div class="user-card-container widget-container">
                <?php if($title):?>
                <div class="widget-header">
                    <div class="widget-title"><?php echo $title ?></div>
                </div>
                <?php endif ?>
                <div class="user-header">
                    <a href="javascript:;" class="user-avatar">
                        <img src="<?php echo $avatar_url?>">
                    </a>
                    <div class="nickname"><?php echo $nickname ?></div>
                    <div class="description"></div>
                </div>
                <div class="user-meta">
                    <div class="meta-item">
                        <span><?php echo $posts_counter ?></span> <span><?= __('Articles','vt') ?></span>
                    </div>
                    <div class="meta-item">
                        <span><?php echo $comments_counter ?></span> <span><?= __('Comments','vt') ?></span>
                    </div>
                    <div class="meta-item">
                        <span><?php echo $like_counter ?></span> <span><?= __('Like','vt') ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php
        echo $after_widget;
    }

    function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $posts_per_page = !empty($instance['posts_per_page']) ? $instance['posts_per_page'] : '';
        $cat_id = !empty($instance['cat_id']) ? $instance['cat_id'] : '';
        $post_type = !empty($instance['post_type']) ? $instance['post_type'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('title','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Tags Widget
 */
class TagsWidget extends WP_Widget {
    function __construct(){
        parent::__construct( 'tags-widget', '[Mirage] '.__( 'Tags', 'vt' ), array( 'description' => __( 'Tags', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        global $wpdb;
        wp_reset_postdata();

        $title = $instance['title'] ? $instance['title'] : __('Tags','vt');

        $tags = get_tags(array('orderby'=>'count', 'order'=>'DESC', 'hide_empty'=>false));
        ?>

        <div class="tag-container widget-container">
            <div class="widget-header">
                <div class="widget-title"><?=$title?></div>
            </div>
            <div class="tag-list">
                <?php foreach ($tags as $tag): ?>
                    <a href="<?=get_tag_link($tag->term_id)?>" class="tag-item">
                        <span>#</span><?=$tag->name?>
                        <?php /*<span><?=$tag->count?></span>*/ ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php
        echo $after_widget;
    }

    function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('title','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? $new_instance['title'] : '';
        return $instance;
    }
}


/**
 * 最新评论
 */
class CommentsWidget extends WP_Widget {
    function __construct(){
        parent::__construct( 'recent-comments', '[Mirage] '.__( 'Comments', 'vt' ), array( 'description' => __( 'Comments', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        $title = $instance['title'] ? $instance['title'] : __('Comments', 'vt');

        // 获取最新评论
        $comments_per_page = isset($instance['comments_per_page']) ? $instance['comments_per_page'] : 3;
        $comments = get_comments(array(
            'number' => $comments_per_page,
            'status' => 'approve',
            'type' => 'comment'
        ));
        ?>
        <div class="recent-comments widget-container">
            <div class="widget-header">
                <div class="widget-title"><?php echo $title ?></div>
            </div>
            <ul class="recent-comments-list">
                <?php if ( $comments ) : ?>
                    <?php foreach ( $comments as $comment ) : ?>
                        <li class="recent-comment-item">
                            <div class="comment-author">
                                <div class="comment-author">
                                    <?php 
                                    // 使用用户ID获取自定义头像URL
                                    $avatar_url = vt_get_custom_avatar_url($comment->user_id, 32);
                                    if ($avatar_url): ?>
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($comment->comment_author); ?>" width="32" height="32" />
                                    <?php else:
                                        // 如果没有自定义头像，则使用默认头像
                                        echo get_avatar($comment, 32);
                                    endif; ?>
                                    <span class="comment-author-name"><?php echo esc_html($comment->comment_author); ?></span>
                                </div>
                            </div>
                            <div class="comment-content">
                                <a href="<?php echo get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID; ?>">
                                    <?php echo wp_trim_words( $comment->comment_content, 15 ); ?>
                                </a>
                            </div>
                            <div class="comment-meta">
                                <span class="comment-date"><?= vt_get_time($comment->comment_date) ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="no-comments"><?php _e('暂无评论', 'vt'); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <?php
        echo $after_widget;
    }

    function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $comments_per_page = !empty($instance['comments_per_page']) ? $instance['comments_per_page'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('title','vt')?>:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('comments_per_page'); ?>"><?= __('Count','vt')?>:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('comments_per_page'); ?>" name="<?php echo $this->get_field_name('comments_per_page'); ?>" value="<?php echo esc_attr($comments_per_page); ?>">
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['comments_per_page'] = (!empty($new_instance['comments_per_page'])) ? intval($new_instance['comments_per_page']) : 5;
        return $instance;
    }
}


function vt_add_widget(){
    register_widget('HotWidget');
    register_widget('ArticleWidget');
    register_widget('UserWidget');
    register_widget('TagsWidget');
    register_widget('CommentsWidget'); 
    register_widget('CategoryWidget');
}

add_action( 'widgets_init', 'vt_add_widget' );


// 1. 禁用区块小工具
add_action('after_setup_theme', function() {
    remove_theme_support('widgets-block-editor');
});

// 2. 移除区块小工具菜单项
add_action('admin_menu', function() {
    remove_submenu_page('themes.php', 'gutenberg-widgets');
}, 999);

// 3. 只保留自定义小工具（传统模式下）
add_action('widgets_init', function() {
    if (!is_admin()) return;
    
    global $wp_widget_factory;
    $allowed = [
        'HotWidget',
        'ArticleWidget',
        'UserWidget',
        'TagsWidget',
        'CommentsWidget',
        'CategoryWidget',
    ];
    
    foreach (array_keys($wp_widget_factory->widgets) as $class) {
        if (!in_array($class, $allowed, true)) {
            unregister_widget($class);
        }
    }
}, 9999);