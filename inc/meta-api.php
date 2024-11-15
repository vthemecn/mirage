<?php

add_action('rest_api_init', function () {
    register_rest_field('post', 'user', array(
        'get_callback' => function ($object) {
            $user = array();
            $user['id'] = $object['author'];
            $user['nickname'] = get_usermeta($object['author'], 'nickname');
            $user['avatar'] = "";
            
            $avatar_attachment_id = get_usermeta($object['author'], 'user_avatar_attachment_id');
            $avatar = wp_get_attachment_image_src($avatar_attachment_id, 'medium');
            if ($avatar_attachment_id && $avatar) {
                $user['avatar'] = $avatar[0];
            }
            return $user;
        }
    ));
});


// https://developer.wordpress.org/rest-api/extending-the-rest-api/modifying-responses/
register_meta(
    'posts',
    'oss_url',
    array(
        'single' => true,
        'type' => 'string',
        'show_in_rest' => true
    )
);



