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
                /*
                $like_counter = 0;
                $sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
                $result = $wpdb->get_row($wpdb->prepare($sql, [$current_post->ID, 'like']), ARRAY_A );
                $like_counter = $result['num'];
                */
                
                $output .= '<div class="media-item">
                    <div class="media-thumbnail">
                        <a href="'.get_the_permalink() .'" target="_blank">
                            <img src="'.$thumbnail.'">
                        </a>
                    </div>
                    <div class="media-body">
                        <div class="media-title">
                            <a class="title" href="'.get_the_permalink() .'" target="_blank">'.get_the_title(). '</a>
                        </div>
                        <div class="media-description">
                            '. get_the_excerpt() .'
                        </div>
                        <div class="media-box">';

                $output .=  <<<EOD
                            <a class="meta-item author" href="{$user_center_url}" target="_blank">
                                <div class="avatar"><img src="{$avatar}"></div>
                                <span>{$nickname}</span>
                            </a>
                            EOD;


                $output .= '<div class="meta-item date">
                                <i class="fa-solid fa-clock"></i>'.vt_get_time(get_the_time('Y-m-d')).'
                            </div>
                            ';

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
        $subject    = "Mirage 主题邮箱 SMTP 设置成功";
        $body       = "
        <div style='width:600px;margin:50px auto; padding-left:77px; background:#fff;font-size:16px;color:#55798d;padding-right80px;'>
            <p><img src='".get_bloginfo('template_url')."/assets/images/logo.png' style='max-height:50px;user-select:none'></p>
            <h3>Mirage 主题邮箱 SMTP 设置成功</h3>
            <p>Mirage 主题官网地址 <a href='https://vtheme.cn/mirage'>https://vtheme.cn/mirage</a></p>
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
}


