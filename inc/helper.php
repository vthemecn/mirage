<?php

namespace inc;

class Helper {
    /**
     * 获取用户信息
     * @params {int} $user_id 用户ID
     * @returns
     */
    public function getUserById($user_id)
    {
        $user = new \WP_User($user_id);
        if($user->ID==0){ return false; }
        
        $user_data = (array)$user->data;
        
        $user_data['id'] = $user_data['ID'];
        
        $user_data['avatar'] = '';
        $attachment_id = get_user_meta($user_id, 'user_avatar_attachment_id', true); //头像ID
        $avatar = wp_get_attachment_image_src($attachment_id, 'medium'); // 头像路径
        if ($attachment_id && $avatar) {
            $user_data['avatar'] = $avatar[0];
        }
        unset($user_data['user_pass']);
    
        // 获取用户信息
        $res['nickname']    = get_user_meta($user_id, 'nickname', true);
        $res['mobile']      = get_user_meta($user_id, 'mobile', true);
        $res['gender']      = get_user_meta($user_id, 'gender', true);
        $res['address']     = get_user_meta($user_id, 'address', true);
        $res['dob']         = get_user_meta($user_id, 'dob', true);
        $res['description'] = get_user_meta($user_id, 'description', true);
        $res['ip']          = get_user_meta($user_id, 'ip', true);
        $res['updated_at']  = get_user_meta($user_id, 'updated_at', true);
        
        $user_data = array_merge($user_data, $res);
        return $user_data;
    }
}