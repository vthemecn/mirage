<?php

namespace vtheme\api;

class HomeController
{
    public function getMoreArticles($request)
    {
        global $wpdb;

        $vt_config = vt_get_config();
        // $default_image = $vt_config['default_image'] ? $vt_config['default_image'] : 
        //             get_template_directory_uri() . '/assets/images/default.jpg';
        
        $query = $request->get_query_params();

        $posts_per_page = get_option('posts_per_page');
        $page = intval($query['page']);
        $post_type = isset($query['post_type']) ? $query['post_type'] : 'posts';
        
        $args = array(
            // 'post_type'=>'audios',
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
            'post__not_in' => get_option( 'sticky_posts' ),
            // 'orderby' => array("menu_order" => "desc",'date' => "desc")
        );
        
        if( $vt_config['posts_ids'] ){
            $args['category__in'] = $vt_config['posts_ids'];
        }

        if( $vt_config['posts_not_in_ids'] ){
            $args['category__not_in'] = $vt_config['posts_not_in_ids'];
        }
        
        $res = new \WP_Query( $args );

        $output = '';

        if ( $res->have_posts() ) {
            while ( $res->have_posts() ) {
                $res->the_post();
                $current_post = get_post();
                // $thumbnail_arr = wp_get_attachment_image_src(get_post_thumbnail_id($current_post->ID), 'medium');
                // $thumbnail = $thumbnail_arr ? $thumbnail_arr[0] : $default_image;
                $thumbnail = vt_get_thumbnail_url($current_post->ID, 'medium');
                
                $author_id = get_the_author_id();
                $avatar = vt_get_custom_avatar_url($author_id);

                $user_center_url = home_url() . '/users/' . $current_post->post_author;
                $nickname = get_the_author_meta('nickname', $current_post->post_author);

                // 喜欢数量
                $like_counter = 0;
                $sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
                $result = $wpdb->get_row($wpdb->prepare($sql, [$current_post->ID, 'like']), ARRAY_A );
                $like_counter = $result['num'];
                
                $output .= '<div class="media-item">
                    <div class="media-thumbnail">
                        <a href="'.get_the_permalink() .'" target="_blank">
                            <img src="'.$thumbnail.'">
                        </a>
                    </div>
                    <div class="media-main">
                        <div class="media-title">
                            <a class="title" href="'.get_the_permalink() .'" target="_blank">'.get_the_title(). '</a>
                        </div>
                        <div class="media-description">
                            '. get_the_excerpt() .'
                        </div>
                        <div class="media-meta">';

                if($vt_config['user_center_is_on']){
                    $output .=  <<<EOD
                                <a class="author" href="{$user_center_url}" target="_blank">
                                    <img src="{$avatar}">
                                    <span>{$nickname}</span>
                                </a>
                                EOD;
                } else {
                    $output .=  <<<EOD
                                <span class="author">
                                    <img src="{$avatar}">
                                    <span>{$nickname}</span>
                                </span>
                                EOD;
                }

                $output .= '<span class="date">
                                <i class="fa-solid fa-clock"></i>'.get_the_time('Y-m-d').'
                            </span>
                            <span class="hit-counter">
                                <i class="fa-solid fa-eye"></i>' .getPostViews(get_the_ID()). '
                            </span>
                            <span class="like_counter">
                                <i class="fa-solid fa-star"></i>' .$like_counter. '
                            </span>
                            ';
               
                if($vt_config['comments_is_on']){
                    $output .= '<span class="meta"><i class="fa-solid fa-comment"></i>'. $current_post->comment_count .'</span>';
                }

                $output .= '
                        </div>
                    </div>
                </div>';
                
            }
            wp_reset_postdata();

            $response = new \WP_REST_Response(['html_str'=>$output]);
            $response->set_status(200);

            header("XXX-SQL-Query-Count: " . get_num_queries());
            return $response;
        } else {
            $response = new \WP_REST_Response([]);
            $response->set_status(404);
            return $response;
        }
    }
    
    
    public function sendmail($request)
    {
        if (!is_user_logged_in() || !is_super_admin()) {
            wp_die("没有权限");
        }
        
        $config = vt_get_config();

        // 发送邮件
        $to         = $config['smtp_username'];
        $subject    = "MirageV 主题邮箱 SMTP 设置成功";
        $body       = "
        <div style='width:600px;margin:50px auto; padding-left:77px; background:#fff;font-size:16px;color:#55798d;padding-right80px;'>
            <p><img src='".get_bloginfo('template_url')."/assets/images/logo.png' style='max-height:50px;user-select:none'></p>
            <h3>MirageV 主题邮箱 SMTP 设置成功</h3>
            <p>MirageV 主题官网地址 <a href='https://vtheme.cn/miragev'>https://vtheme.cn/miragev</a></p>
        </div>
        ";
        $headers    = array('Content-Type: text/html; charset=UTF-8');
    
        $flag = wp_mail($to, $subject, $body, $headers);
    
        if ($flag == true) {
            $response = new \WP_REST_Response(array("message" => "测试邮件发送成功"));
            $response->set_status(200);
            return $response;
        } else {
            $error_message = get_option('vt_mail_error');
            $response = new \WP_REST_Response(array(
                "message"   => "测试邮件发送失败",
                "detail"    => $error_message
            ));
            $response->set_status(500);
            return $response;
        }
    
        return $flag;
    }


    public function index($request)
    {
        $config = vt_get_config();

        $response_data = array(
            "app_lastest_ids" => $config['app_lastest_ids'],
            "app_hot_ids" => $config['app_hot_ids'],
            "app_about_id" => $config['app_about_id'],
            "app_using_id" => $config['app_using_id'],
            "app_privacy_id" => $config['app_privacy_id']
        );

        $response = new \WP_REST_Response($response_data);
        $response->set_status(200);
        return $response;
    }



}


