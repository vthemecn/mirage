<?php

namespace vtheme\api;

class Users
{
    public function update($request)
    {
        $user_id = $request['id'];
        $current_user_id = get_current_user_id();

        // 验证用户权限
        if ($current_user_id != $user_id && !current_user_can('manage_options')) {
            return new \WP_Error('unauthorized', '没有权限修改此用户', array('status' => 401));
        }

        // 验证用户ID
        $user = get_user_by('id', $user_id);
        if (!$user) {
            return new \WP_Error('user_not_found', '用户不存在', array('status' => 404));
        }

        // 获取请求数据
        $params = json_decode($request->get_body(), true);

        // 更新用户信息
        $update_data = array();
        $meta_data = array();

        // 更新昵称
        if (isset($params['nickname']) && !empty($params['nickname'])) {
            $update_data['display_name'] = sanitize_text_field($params['nickname']);
        }

        // 更新邮箱
        if (isset($params['email']) && is_email($params['email'])) {
            $update_data['user_email'] = sanitize_email($params['email']);
        }

        // 更新描述
        if (isset($params['description'])) {
            $update_data['description'] = sanitize_textarea_field($params['description']);
        }

        // 更新用户数据
        if (!empty($update_data)) {
            $update_data['ID'] = $user_id;
            $result = wp_update_user($update_data);

            if (is_wp_error($result)) {
                return new \WP_Error('update_failed', $result->get_error_message(), array('status' => 500));
            }
        }

        // 更新用户元数据
        if (isset($params['gender'])) {
            $gender = sanitize_text_field($params['gender']);
            if (in_array($gender, array('0', '1', '2'))) {
                update_user_meta($user_id, 'gender', $gender);
                $meta_data['gender'] = $gender;
            }
        }

        if (isset($params['mobile'])) {
            $mobile = sanitize_text_field($params['mobile']);
            update_user_meta($user_id, 'mobile', $mobile);
            $meta_data['mobile'] = $mobile;
        }

        // 返回更新后的用户信息
        $updated_user = get_user_by('id', $user_id);
        $user_data = array(
            'id' => $updated_user->ID,
            'display_name' => $updated_user->display_name,
            'user_email' => $updated_user->user_email,
            'description' => $updated_user->description,
            'gender' => get_user_meta($user_id, 'gender', true),
            'mobile' => get_user_meta($user_id, 'mobile', true),
        );

        return new \WP_REST_Response($user_data, 200);
    }
}