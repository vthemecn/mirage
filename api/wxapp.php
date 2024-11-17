<?php

namespace vtheme\api;

class Wxapp
{
    public function __construct()
    {
        // require_once TEMP_DIR . "/api/jwt.php";
        // require_once TEMP_DIR . '/api/user.php';
    }

    /**
     * http GET 请求
     */
    public function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }


    /**
     * 微信小程序登录接口
     * 参考文档：https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/login.html
     *         https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/login/auth.code2Session.html
     *         https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/signature.html
     *         https://www.php.cn/uni-app/481462.html
     *         https://www.jb51.net/article/174181.htm
     */
    public function login($request)
    {
        global $wpdb;

        $config = vt_get_config();

        $data = $request->get_json_params();

        $appid = $config['wx_app_id'];
        $appsecret = $config['wx_app_secret'];
        $grant_type = "authorization_code"; //授权（必填）
        $code = $data['code'];    //有效期5分钟 登录会话

        $url = "https://api.weixin.qq.com/sns/jscode2session?"
            . "appid=" . $appid
            . "&secret=" . $appsecret
            . "&js_code=" . $code
            . "&grant_type=" . $grant_type;
        $res = json_decode($this->httpGet($url), true);

        if ($res['errcode']) {
            $response_data = array(
                "error" => "微信服务端返回错误信息",
                "detail" => $res['errcode'] . " - " . $res['errmsg']
            );
            $response = new \WP_REST_Response($response_data);
            $response->set_status(500);
            return $response;
        }

        
        // 1. 检查该 open_id 是否存已注册,不存在 open_id，执行注册逻辑
        $open_id = $res['openid'];

        $sql = "SELECT * FROM {$wpdb->prefix}usermeta WHERE meta_key=%s AND meta_value=%s";
        $res = $wpdb->get_row($wpdb->prepare($sql, array('wxapp_open_id', $open_id)), ARRAY_A);
        if (!$res) {
            return $this->register($open_id);
        }
        
        $user_id = $res['user_id'];

        $token = $this->createToken($user_id);

        $helper = new \inc\Helper();
        $user_data = $helper->getUserById($user_id);

        $response_data = array(
            "message" => "登录成功",
            "token" => $token,
            "user"  => $user_data
        );

        $response = new \WP_REST_Response($response_data);
        $response->set_status(200);
        return $response;
    }


    private function register($open_id)
    {
        $user_login = uniqid('u_') . mt_rand(10,99);
        $nickname = "微信用户";

        // 创建用户
        $user_id = wp_insert_user(array(
            'user_login'    => $user_login,
            'user_pass'     => uniqid(),
            'nickname'      => $nickname,
            'display_name'  => $nickname,
            'user_email'    => ""
        ));

        if (is_wp_error($user_id)) {
            $response = new \WP_REST_Response(array("error" => "注册失败，请稍后再试"));
            $response->set_status(500);
            return $response;
        } else {
            update_user_meta($user_id, 'wxapp_open_id', $open_id);

            $token = $this->createToken($user_id);

            $user_object = get_user_by('id', $user_id);
            $user_data = $user_object->data;

            $response_data = array(
                "message" => "注册成功",
                "token" => $token,
                "user"  => $user_data
            );

            $response = new \WP_REST_Response($response_data);
            $response->set_status(200);
            return $response;
        }
    }


    /**
     * 生成 JWT Token
     */
    public function createToken($user_id)
    {
        $time                    = time();
        $payload['iat']          = $time; // 签发时间
        $payload['exp']          = $time + 60 * 1000; // 过期时间
        $payload['nbf']          = $time; // 生效时间
        $payload['user_id']      = $user_id; // 用户 ID
        $key    = defined('JWT_KEY') ? JWT_KEY : 'default_key';
        $token  = \Firebase\JWT\JWT::encode($payload, $key);
        return $token;
    }


}

