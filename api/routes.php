<?php
/**
 * Copyright (c) vtheme.cn
 *
 * @author vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */


if (!defined('ABSPATH')) exit;


require_once THEME_DIR . '/api/users.php';
require_once THEME_DIR . '/api/accounts.php';


add_action('rest_api_init', function (){
    /**
     * 账户相关接口
     */
    // 用户登录
    register_rest_route('vtheme/v1', '/accounts/login', array(
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function (\WP_REST_Request $request) {
            $accountsController = new \vtheme\api\Accounts();
            return $accountsController->login($request);
        }
    ));
    
    // 用户注册
    register_rest_route('vtheme/v1', '/accounts/register', array(
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function (\WP_REST_Request $request) {
            $accountsController = new \vtheme\api\Accounts();
            return $accountsController->register($request);
        }
    ));
    
    // 发送注册验证码
    register_rest_route('vtheme/v1', '/accounts/send-verification-code', array(
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function (\WP_REST_Request $request) {
            $accountsController = new \vtheme\api\Accounts();
            return $accountsController->sendVerificationCode($request);
        }
    ));
    
    // 忘记密码
    register_rest_route('vtheme/v1', '/accounts/forgot-password', array(
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function (\WP_REST_Request $request) {
            $accountsController = new \vtheme\api\Accounts();
            return $accountsController->forgotPassword($request);
        }
    ));
    
    // 重置密码
    register_rest_route('vtheme/v1', '/accounts/reset-password', array(
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function (\WP_REST_Request $request) {
            $accountsController = new \vtheme\api\Accounts();
            return $accountsController->resetPassword($request);
        }
    ));

    // 用户登出
    register_rest_route('vtheme/v1', '/accounts/logout', array(
        'methods' => 'POST',
        'callback' => function (\WP_REST_Request $request) {
            $accountsController = new \vtheme\api\Accounts();
            return $accountsController->logout($request);
        },
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ));
    
    // 上传头像
    register_rest_route('vtheme/v1', '/accounts/upload-avatar', array(
        'methods' => 'POST',
        'callback' => function (\WP_REST_Request $request) {
            $accountsController = new \vtheme\api\Accounts();
            return $accountsController->uploadAvatar($request);
        },
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ));
    
    // 获取当前用户信息
    register_rest_route('vtheme/v1', '/accounts/current-user', array(
        'methods' => 'GET',
        'callback' => function (\WP_REST_Request $request) {
            $accountsController = new \vtheme\api\Accounts();
            return $accountsController->getCurrentUser($request);
        },
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ));

    /**
     * 用户更新接口
     */
    register_rest_route('vtheme/v1', '/users/(?P<id>\d+)', array(
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'callback' => function (\WP_REST_Request $request) {
            $usersController = new \vtheme\api\Users();
            return $usersController->update($request);
        }
    ));
    


});

