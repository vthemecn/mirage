<?php
/**
 * 收藏和点赞
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link: https://vtheme.cn
 * @date: 2024-11-24
 */

namespace api;

class Star
{
    public function index($request)
    {
        // 1.检查用户权限
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $response = new \WP_REST_Response(array("error" => __('401', 'f')));
            $response->set_status(401);
            return $response;
        }
        
        // 2.检查 type 是否存在
        $query_arr = $request->get_query_params();
        $type_arr = ['star','like'];
        if(!in_array($query_arr['type'], $type_arr)){
            $response = new \WP_REST_Response(array("error" => 'type 是 star 或者 like'));
            $response->set_status(404);
            return $response;
        }
        
        // 3.处理参数
        $per_page = ($query_arr['per_page'] > 1 && $query_arr['per_page'] < 50) ? $query_arr['per_page'] : 20;
        $page = $query_arr['page'] ? $query_arr['page'] : 1;
        $start = ($page - 1) * $per_page;
        
        // 4. 获取列表
        global $wpdb;
        $sql = "SELECT s.*, p.post_title FROM wp_vt_star AS s
                LEFT JOIN wp_posts AS p ON s.object_id=p.ID
                WHERE s.type=%s AND s.user_id=%d
                ORDER BY s.created_at DESC, s.id DESC
                LIMIT %d, %d";
        $data = [$query_arr['type'], $user_id, $start, $per_page];
        $res = $wpdb->get_results($wpdb->prepare($sql, $data), ARRAY_A);
        if($res){
            $response = new \WP_REST_Response($res);
            $response->set_status(200);
            return $response;
        } else {
            $response = new \WP_REST_Response($res);
            $response->set_status(404);
            return $response;
        }
    }
    
    
    public function store($request)
    {
        global $wpdb;
        
        // 1.验证请求json
        $request_data = $request->get_json_params();
        $v = new \Validator();
        $v->name('对象ID')->value($request_data['object_id'])->required();
        $v->name('类型')->value($request_data['type'])->required();
        if (!$v->isSuccess()) {
            $response = new \WP_REST_Response(array("error" => $v->getErrors()[0]));
            $response->set_status(400);
            return $response;
        }

        // 2.检查用户权限
        $user_id = get_current_user_id();
        if ($user_id == 0 && $request_data['type'] == 'like') {
            return $this->storeWithoutAuth($request, $request_data);
        }
        if ($user_id == 0) {
            $response = new \WP_REST_Response(array("error" => __('401', 'f')));
            $response->set_status(401);
            return $response;
        }
        
        // 3.检查文章是否存在
        $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE ID=%s LIMIT 1";
        $res = $wpdb->get_row($wpdb->prepare($sql, [$request_data['object_id']]), ARRAY_A);
        if(!$res){
            $response = new \WP_REST_Response(array("error" => '对象ID不存在'));
            $response->set_status(404);
            return $response;
        }

        // 4.检查 type 是否存在
        $type_arr = ['star','like'];
        if(!in_array($request_data['type'], $type_arr)){
            $response = new \WP_REST_Response(array("error" => 'type 是 star 或者 like'));
            $response->set_status(404);
            return $response;
        }
        
        // 5.添加收藏
        $data = [$user_id, $request_data['object_id'], $request_data['type'], wp_date('Y-m-d H:i:s')];
        
        $sql = "SELECT * FROM {$wpdb->prefix}vt_star WHERE user_id=%d AND object_id=%s AND type=%s";
        $res = $wpdb->query($wpdb->prepare($sql, $data));
        if(!$res){
            $sql = "INSERT INTO {$wpdb->prefix}vt_star (user_id, object_id, type, created_at)
                    VALUES (%d, %s, %s, %s)";
            $res = $wpdb->query($wpdb->prepare($sql, $data));
        }

        // 6.组合返回数据
        $sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
        $res = $wpdb->get_row($wpdb->prepare($sql, [$request_data['object_id'], $request_data['type'] ]));
        $counter = $res->num;
        
        $response_data = [
            'user_id'   => $user_id,
            'object_id' => $request_data['object_id'],
            'type'      => $request_data['type'],
            'counter'   => $counter,
        ];
        
        $response = new \WP_REST_Response($response_data);
        $response->set_status(201);
        return $response;
    }


    /**
     * 未登录用户可以点赞
     */
    public function storeWithoutAuth($request, $request_data)
    {
        global $wpdb;

        // 添加收藏
        $data = [0, $request_data['object_id'], 'like', wp_date('Y-m-d H:i:s')];
        $sql = "INSERT INTO {$wpdb->prefix}vt_star (user_id, object_id, type, created_at)
                VALUES (%d, %s, %s, %s)";
        $res = $wpdb->query($wpdb->prepare($sql, $data));

        // 返回数据
        $sql = "SELECT count(*) AS num FROM {$wpdb->prefix}vt_star WHERE object_id=%s AND type=%s";
        $res = $wpdb->get_row($wpdb->prepare($sql, [$request_data['object_id'], $request_data['type'] ]));
        $counter = $res->num;
        
        $response_data = [
            'user_id'   => 0,
            'object_id' => $request_data['object_id'],
            'type'      => $request_data['type'],
            'counter'   => $counter,
        ];
        
        $response = new \WP_REST_Response($response_data);
        $response->set_status(201);
        return $response;
    }
    
    
    public function destory($request)
    {
        // 1. 检查用户权限
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            $response = new \WP_REST_Response(array("error" => __('401', 'f')));
            $response->set_status(401);
            return $response;
        }
        
        // 2. 检查 type 是否存在
        $request_data = $request->get_json_params();
        $type_arr = ['star','like'];
        if(!in_array($request_data['type'], $type_arr)){
            $response = new \WP_REST_Response(array("error" => 'type 是 star 或者 like'));
            $response->set_status(404);
            return $response;
        }
        
        // 2. 删除记录
        global $wpdb;
        $object_id = $request->get_url_params()['id'];
        $sql = "DELETE FROM {$wpdb->prefix}vt_star WHERE object_id=%d AND user_id=%d AND type=%s";
        $data = [$object_id, $user_id, $request_data['type']];
        $res = $wpdb->query($wpdb->prepare($sql, $data));

        $sql = "SELECT count(id) as counter FROM {$wpdb->prefix}vt_star WHERE object_id=%d AND `type`=%s";
        $res = $wpdb->get_row($wpdb->prepare($sql, [$object_id, $request_data['type']]), ARRAY_A);
        header("X-VT-Counter:" . $res['counter']);

        $response = new \WP_REST_Response([]);
        $response->set_status(200);
        return $response;
    }
    
    
    public function update($request)
    {
        return "update";
    }
    

    public function show($request)
    {
        return "show";
    }
}
