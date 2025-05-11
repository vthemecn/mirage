<?php
/**
 * 
 */


//禁止新版小工具
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
add_filter( 'use_widgets_block_editor', '__return_false');

function vt_widgets_init() {
    $args = array(
        'name' => __( '默认侧边栏', 'vt' ),
        'id' => 'default-sidebar',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    );
    register_sidebar($args);

    $args = array(
        'name' => __( '文章页侧边栏', 'vt' ),
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
 * 图文列表
 */
class ImageArticleWidget extends WP_Widget {
    function __construct(){
        $this->WP_Widget( 'image-article-list', '[MirageV] '.__( '图文列表', 'vt' ), array( 'description' => __('图文列表描述', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        $vt_config = vt_get_config();

        $widget_title_class = $vt_config['widget_title_type'] ? 'type-' . $vt_config['widget_title_type'] : '';
        $title = $instance['title'] ? $instance['title'] : __('图文列表', 'vt');

        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $instance['posts_per_page'] ? $instance['posts_per_page'] : 4,
            //'orderby' => 'rand',
            'meta_query' => array(array( 'key' => '_thumbnail_id'))
        );
        if($instance['cat_id']){
            global $wpdb;
            $sql = "SELECT taxonomy FROM {$wpdb->prefix}term_taxonomy WHERE term_id=%s";
            $res = $wpdb->get_row($wpdb->prepare($sql, $instance['cat_id']), ARRAY_A);

            if($res['taxonomy'] == 'category'){
                $args['cat'] = $instance['cat_id'];
            } else {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $res['taxonomy'],
                        'terms' => $instance['cat_id']
                    )
                );
            }
        }
        $query = new WP_Query($args);
        ?>
        <div class="image-title widget-container">
            <div class="widget-header <?php echo $widget_title_class; ?>">
                <div class="widget-title"><?php echo $title ?></div>
            </div>
            <div class="item-list-wrapper">
                <ul class='item-list'>
                    <?php if ( $query->have_posts() ) : ?>
                        <?php while ( $query->have_posts() ) : ?>
                            <?php
                            $query->the_post();
                            $current_post = get_post();
                            $thumbnail = vt_get_thumbnail_url($current_post->ID, 'medium');
                            
                            $price = get_post_meta( $current_post->ID, 'price', true );
                            $price = $price ? number_format($price/100,2) : '';
                            ?>
                            
                            <li class='item-widget'>
                                <a href="<?php the_permalink() ?>">
                                    <img src="<?= $thumbnail ?>">
                                    <div class="item-title">
                                        <div><?php the_title(); ?></div>
                                        <span>
                                            <?= get_the_time('Y-m-d') ?>
                                            <?php //echo friendly_time(get_the_time('Y-m-d H:i:s')); ?>  
                                        </span>
                                    </div>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </ul>
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
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('标题','vt') ?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?= __('数量','vt') ?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>" value="<?php echo esc_attr($posts_per_page); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('cat_id'); ?>"><?= __('分类ID','vt') ?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('cat_id'); ?>" name="<?php echo $this->get_field_name('cat_id'); ?>" value="<?php echo esc_attr($cat_id); ?>">
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['posts_per_page'] = (!empty($new_instance['posts_per_page'])) ? strip_tags($new_instance['posts_per_page']) : '';
        $instance['cat_id'] = (!empty($new_instance['cat_id'])) ? strip_tags($new_instance['cat_id']) : '';
        return $instance;
    }
}


/**
 * 热门
 */
class HotWidget extends WP_Widget {
    function __construct(){
        $this->WP_Widget( 'hot-list', '[MirageV] '.__( '热门', 'vt' ), array( 'description' => __( '热门描述', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        $vt_config = vt_get_config();

        $widget_title_class = $vt_config['widget_title_type'] ? 'type-' . $vt_config['widget_title_type'] : '';
        $title = $instance['title'] ? $instance['title'] : __('热门文章', 'vt');

        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $instance['posts_per_page'] ? $instance['posts_per_page'] : 4,
            'meta_key'  => 'post_views_count',
            'orderby'   => 'meta_value',
            'order' => 'DESC',
            'ignore_sticky_posts' => true
        );
        $query = new WP_Query($args);
        ?>
        <div class="hot widget-container">
            <div class="widget-header <?php echo $widget_title_class; ?>">
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
                                <div class="time"><?php echo friendly_time(get_the_time('Y-m-d H:i:s')); ?></div>
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
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('标题','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?= __('数量','vt')?>:</label>
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
 * 文章列表
 */
class ArticleWidget extends WP_Widget {
    function __construct(){
        $this->WP_Widget( 'article-list', '[MirageV] '.__( '文章列表', 'vt' ), array( 'description' => __( '文章列表描述', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        $vt_config = vt_get_config();

        $widget_title_class = $vt_config['widget_title_type'] ? 'type-' . $vt_config['widget_title_type'] : '';

        wp_reset_postdata();

        $title = $instance['title'] ? $instance['title'] : __('文章列表', 'vt');

        $args = array(
                    // 'post_type' => 'posts',
                    'posts_per_page' => $instance['posts_per_page'] ? $instance['posts_per_page'] : 4,
                    'meta_key' => 'post_views_count',
                    'orderby' => 'meta_value',
                    'order' => 'DESC'
                );
        // if($instance['cat_id']){
        //     $args['cat'] = $instance['cat_id'];
        // }
        if($instance['cat_id']){
            global $wpdb;
            $sql = "SELECT taxonomy FROM {$wpdb->prefix}term_taxonomy WHERE term_id=%s";
            $res = $wpdb->get_row($wpdb->prepare($sql, $instance['cat_id']), ARRAY_A);

            if($res['taxonomy'] == 'category'){
                $args['cat'] = $instance['cat_id'];
            } else {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $res['taxonomy'],
                        'terms' => $instance['cat_id']
                    )
                );
            }
        }

        $post_query = new WP_Query($args);
        // p($post_query);
        ?>
        <div class="article widget-container">
            <div class="widget-header <?php echo $widget_title_class; ?>">
                <div class="widget-title">
                    <?php echo $title ?>
                </div>
            </div>
            <div class="article-list">
                <?php if ( $post_query->have_posts() ) : ?>
                    <?php while ( $post_query->have_posts() ) : ?>
                        <?php $post_query->the_post(); ?>
                    <div class="article-item"><a href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a></div>
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
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('标题','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?= __('数量','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>" value="<?php echo esc_attr($posts_per_page); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('cat_id'); ?>"><?= __('分类ID','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('cat_id'); ?>" name="<?php echo $this->get_field_name('cat_id'); ?>" value="<?php echo esc_attr($cat_id); ?>">
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['posts_per_page'] = (!empty($new_instance['posts_per_page'])) ? strip_tags($new_instance['posts_per_page']) : '';
        $instance['cat_id'] = (!empty($new_instance['cat_id'])) ? strip_tags($new_instance['cat_id']) : '';
        return $instance;
    }
}


/**
 * 图片列表
 */
class ImageWidget extends WP_Widget {
    function __construct(){
        $this->WP_Widget( 'image-list', '[MirageV] '.__( '图片列表', 'vt' ), array( 'description' => __( '图片列表描述', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        $vt_config = vt_get_config();

        $widget_title_class = $vt_config['widget_title_type'] ? 'type-' . $vt_config['widget_title_type'] : '';
        $title = $instance['title'] ? $instance['title'] : __('图片列表', 'vt');

        wp_reset_postdata();
        $args = array(
            'posts_per_page' => $instance['posts_per_page'] ? $instance['posts_per_page'] : 4
        );
        // if($instance['cat_id']){
        //     $args['cat'] = $instance['cat_id'];
        // }
        if($instance['cat_id']){
            global $wpdb;
            $sql = "SELECT taxonomy FROM {$wpdb->prefix}term_taxonomy WHERE term_id=%s";
            $res = $wpdb->get_row($wpdb->prepare($sql, $instance['cat_id']), ARRAY_A);

            if($res['taxonomy'] == 'category'){
                $args['cat'] = $instance['cat_id'];
            } else {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => $res['taxonomy'],
                        'terms' => $instance['cat_id']
                    )
                );
            }
        }

        $query = new WP_Query( $args );
        ?>
        <div class="images-list-container widget-container">
            <div class="widget-header <?php echo $widget_title_class; ?>">
                <div class="widget-title"><?php echo $title ?></div>
            </div>
            
            <div class="item-list-wrapper">
                <ul class='item-list'>
                    <?php if ( $query->have_posts() ) : ?>
                        <?php while ( $query->have_posts() ) : ?>
                        <?php
                        $query->the_post();
                        $current_post = get_post();
                        $thumbnail = vt_get_thumbnail_url($current_post->ID, 'medium');
                        
                        $price = get_post_meta( $current_post->ID, 'price', true );
                        $price = $price ? number_format($price/100,2) : '';
                        ?>
                        
                        <li class='item-widget'>
                            <a href="<?php the_permalink() ?>">
                                <img src="<?= $thumbnail?>">
                                <div class="item-title"><?php the_title(); ?></div>
                                <?php if($price): ?>
                                    <div class="item-price"><?php echo $vt_options['item_currency_symbol']?> <?php echo $price; ?></div>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </ul>
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
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('标题','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('posts_per_page'); ?>"><?= __('数量','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>" value="<?php echo esc_attr($posts_per_page); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('cat_id'); ?>"><?= __('分类ID','vt')?>:</label>
            <input type="text" class="" id="<?php echo $this->get_field_id('cat_id'); ?>" name="<?php echo $this->get_field_name('cat_id'); ?>" value="<?php echo esc_attr($cat_id); ?>">
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['posts_per_page'] = (!empty($new_instance['posts_per_page'])) ? strip_tags($new_instance['posts_per_page']) : '';
        $instance['cat_id'] = (!empty($new_instance['cat_id'])) ? strip_tags($new_instance['cat_id']) : '';
        return $instance;
    }
}


/**
 * 分类列表
 */
class CategoryWidget extends WP_Widget {
    function __construct(){
        $this->WP_Widget( 'category-list', '[MirageV] '.__( '分类列表', 'vt' ), array( 'description' => __( '分类列表描述', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        $vt_config = vt_get_config();

        $widget_title_class = $vt_config['widget_title_type'] ? 'type-' . $vt_config['widget_title_type'] : '';
        $title = $instance['title'] ? $instance['title'] : __('分类列表', 'vt');
        ?>
        <div class="category widget-container">
            <div class="widget-header <?php echo $widget_title_class; ?>">
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
            <?= __('请创建主题侧边菜单','vt') ?>
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
        $this->WP_Widget( 'user-widget', '[MirageV] '.__( '用户卡片', 'vt' ), array( 'description' => __( '用户卡片描述', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        global $wpdb;
        wp_reset_postdata();

        $vt_config = vt_get_config();
        
        $widget_title_class = $vt_config['widget_title_type'] ? 'type-' . $vt_config['widget_title_type'] : '';
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
                <div class="widget-header <?php echo $widget_title_class; ?>">
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
                        <span><?php echo $posts_counter ?></span> <span><?= __('文章','vt') ?></span>
                    </div>
                    <div class="meta-item">
                        <span><?php echo $comments_counter ?></span> <span><?= __('评论','vt') ?></span>
                    </div>
                    <div class="meta-item">
                        <span><?php echo $like_counter ?></span> <span><?= __('收藏','vt') ?></span>
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
            <label for="<?php echo $this->get_field_id('title'); ?>"><?= __('标题','vt')?>:</label>
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
 * HTML 卡片
 */
class HtmlWidget extends WP_Widget {
    function __construct(){
        $this->WP_Widget( 'html-widget', '[MirageV] '.__( 'HTML卡片', 'vt' ), array( 'description' => __( 'HTML卡片描述', 'vt' ) ) );
    }
 
    function widget( $args, $instance ){
        extract( $args, EXTR_SKIP );
        echo $before_widget;

        global $wpdb;
        wp_reset_postdata();

        $vt_config = vt_get_config();
        $html = $instance['html'];

        ?>

        <div class="html-card-container widget-container">
            <?php echo $html ?>
        </div>
        
        <?php
        echo $after_widget;
    }

    function form($instance) {
        $html = !empty($instance['html']) ? $instance['html'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('html'); ?>"><?= __('标题','vt')?>:</label>
            <textarea rows="3" name="<?php echo $this->get_field_name('html'); ?>"><?php echo $html ?></textarea>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = array();
        $instance['html'] = !empty($new_instance['html']) ? $new_instance['html'] : '';
        return $instance;
    }
}



function vt_add_widget(){
    register_widget('HotWidget');
    register_widget('ArticleWidget');
    register_widget('ImageArticleWidget');
    register_widget('ImageWidget');
    register_widget('CategoryWidget');
    register_widget('UserWidget');
    register_widget('HtmlWidget');
}

add_action( 'widgets_init', 'vt_add_widget' );
