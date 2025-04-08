<?php
require_once TEMP_DIR . '/api/home.php';
require_once TEMP_DIR . '/api/star.php';


add_action('rest_api_init', function (){
    /**
     * 获取更多文章
     * 路径：/wp-json/vtheme/v1/home/get-more-articles
     * posts_per_page, page
     */
    register_rest_route('vtheme/v1', 'home/get-more-articles', [
        'methods'  => 'GET',
        'callback' => function ($request) {
            $homeController = new \vtheme\api\HomeController();
            return $homeController->getMoreArticles($request);
        }
    ]);
    
    /**
     * 测试邮箱设置
     * 路径：/wp-json/vtheme/v1/home/get-more-articles
     * posts_per_page, page
     */
    register_rest_route('vtheme/v1', 'sendmail-test', [
        'methods'  => 'POST',
        'callback' => function ($request) {
            $homeController = new \vtheme\api\HomeController();
            return $homeController->sendmail($request);
        }
    ]);

    /**
     * 文章点赞和收藏接口
     */
    // 收藏列表
    register_rest_route('vtheme/v1', '/stars', array(
        'methods' => 'GET',
        'callback' => function (\WP_REST_Request $request) {
            $starsController = new \api\Star();
            return $starsController->index($request);
        }
    ));
    // 添加一个收藏
    register_rest_route('vtheme/v1', '/stars', array(
        'methods' => 'POST',
        'callback' => function (\WP_REST_Request $request) {
            $starsController = new \api\Star();
            return $starsController->store($request);
        }
    ));
    // 删除一个收藏
    register_rest_route('vtheme/v1', '/stars/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => function (\WP_REST_Request $request) {
            $starsController = new \api\Star();
            return $starsController->destory($request);
        }
    ));

});


