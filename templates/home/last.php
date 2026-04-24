<?php
$config = vt_get_config();

$full_width = $config['full_width'];
$full_width_class = $full_width==1 ? 'full-width' : '';

$vt_list_type = $config['list_type'];
$vt_list_type_class = '';
if($vt_list_type > 0){
    $vt_columns_num = $config['vt_columns'];
    $vt_list_type_class = 'vt-column vt-column-' . $vt_columns_num;
}
?>

<div class="main-container" <?=$full_width_class?>>
    <div class="main-widget">        
        <?= vt_theme_ad('ad_header'); ?>


        <?php
        wp_reset_postdata();

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $posts_per_page = get_option('posts_per_page');
        $offset = ($paged - 1) * $posts_per_page;

        // 获取置顶文章 ID
        $sticky_posts = get_option('sticky_posts');
        $sticky_ids = !empty($sticky_posts) ? implode(',', array_map('intval', $sticky_posts)) : '0';
        
        // 获取配置
        $posts_ids = vt_get_config('posts_ids', []);
        $posts_not_in_ids = vt_get_config('posts_not_in_ids', []);
        ?>



        <div class="main-widget">
            <div class="media-list <?=$vt_list_type_class?>">
                <?php
                global $wpdb;
                
                // 构建分类条件
                $cat_where = '';
                if( $posts_ids ){
                    $cat_ids = implode(',', array_map('intval', $posts_ids));
                    $cat_where .= " AND p.ID IN (
                        SELECT object_id FROM {$wpdb->term_relationships} tr
                        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                        WHERE tt.term_id IN ($cat_ids)
                    )";
                }
                
                if( $posts_not_in_ids ){
                    $not_cat_ids = implode(',', array_map('intval', $posts_not_in_ids));
                    $cat_where .= " AND p.ID NOT IN (
                        SELECT object_id FROM {$wpdb->term_relationships} tr
                        INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                        WHERE tt.term_id IN ($not_cat_ids)
                    )";
                }
                
                // 使用自定义 SQL 确保置顶文章优先
                $sql = "
                    SELECT * FROM (
                        -- 置顶文章查询
                        (SELECT p.*, 1 as is_sticky_priority
                        FROM {$wpdb->posts} p
                        WHERE p.post_status = 'publish' 
                        AND p.post_type = 'post'
                        AND p.ID IN ($sticky_ids)
                        $cat_where
                        ORDER BY p.post_date DESC)
                        
                        UNION ALL
                        
                        -- 普通文章查询
                        (SELECT p.*, 0 as is_sticky_priority
                        FROM {$wpdb->posts} p
                        WHERE p.post_status = 'publish' 
                        AND p.post_type = 'post'
                        AND p.ID NOT IN ($sticky_ids)
                        $cat_where
                        ORDER BY p.post_date DESC)
                    ) as combined_results
                    ORDER BY is_sticky_priority DESC, post_date DESC
                    LIMIT $offset, $posts_per_page
                ";
                
                $posts = $wpdb->get_results($sql);
                ?>
                
                <?php if ( !empty($posts) ) : ?>
                    <?php 
                    foreach ($posts as $post) {
                        setup_postdata($post);
                        get_template_part( 'templates/card' );
                    }
                    wp_reset_postdata();
                    ?>
                <?php else: ?>
                    <div class="no-content">
                        <img src="<?php bloginfo('template_url'); ?>/assets/images/empty.png">
                        <p><?php echo __('暂无内容','vt'); ?></p>
                    </div>
                <?php endif; ?>  
            </div>
            
        </div>

        <?php
        // 分页逻辑需要重新计算（因为使用了自定义查询）
        $total_sticky = !empty($sticky_posts) ? count($sticky_posts) : 0;

        // 重新查询获取总数用于分页
        $args = array(
            'posts_per_page' => -1,
            'ignore_sticky_posts' => true,
            'fields' => 'ids'
        );

        if( $posts_ids ){
            $args['category__in'] = $posts_ids;
        }

        if( $posts_not_in_ids ){
            $args['category__not_in'] = $posts_not_in_ids;
        }

        $count_query = new WP_Query($args);
        $total_posts = $count_query->found_posts;

        // 计算总页数
        $max_num_pages = ceil($total_posts / $posts_per_page);

        // 显示分页
        // the_posts_pagination(array(
        //     'current' => $paged,
        //     'total' => $max_num_pages,
        //     'mid_size' => 2,
        //     'prev_text' => '<',
        //     'next_text' => '>',
        //     'screen_reader_text' => ' ',
        //     'aria_label' => "",
        // ));
        $pagination = paginate_links([
            'current'   => $paged,
            'total'     => $max_num_pages,
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>',
            'type'      => 'list',
            'end_size'  => 2,
            'mid_size'  => 2,
        ]);
        ?>

        <div class="vt-captain-pagination pagination-container">
            <?php echo $pagination ?>
        </div>

        <?= vt_theme_ad('ad_footer'); ?>
    </div>

    <?php if(!$full_width):?>
        <div class="sider">
            <?php if ( is_active_sidebar( 'default-sidebar' ) ) { ?>
                <?php dynamic_sidebar( 'default-sidebar' ); ?>
            <?php } ?>

            <?= vt_theme_ad('ad_sidebar'); ?>
        </div>
    <?php endif; ?>

</div>


