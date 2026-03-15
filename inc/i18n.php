<?php

/**
 * 语言文件支持
 */
// function vt_theme_load_theme_textdomain(){
    load_theme_textdomain('vt', get_template_directory() . '/inc/languages');
// }
// add_action('after_setup_theme', 'vt_theme_load_theme_textdomain');


function vt_i18n_strings(){
    // global $vt_global_i18n_strings;
    $vt_global_i18n_strings = [
        'loading' => __('Loading...', 'vt'),
        'network_error' => __('Network error, please try again.', 'vt'),
        'operation_failed' => __('Operation failed', 'vt'),
        'remove_like' => __('Remove like', 'vt'),
        'like' => __('Like', 'vt'),
        'remove_star' => __('Remove star', 'vt'),
        'star' => __('Star', 'vt'),
        'please_log_in' => __('Please log in', 'vt'),
        
        // 评论计数相关
        'no_comments' => __('No comments', 'vt'),
        'one_comment' => __('One comment', 'vt'),
        'multiple_comments' => __('comments', 'vt'),
        
        // 评论提交相关
        'submitting' => __('Submitting...', 'vt'),
        'submit_failed' => __('Submit failed', 'vt'),
        'network_error_retry' => __('Network error, please try again.', 'vt'),
        
        // 评论删除相关
        'confirm_delete_comment' => __('Are you sure you want to delete this comment?', 'vt'),
        'deleting' => __('Deleting...', 'vt'),
        'delete_success' => __('Comment deleted successfully', 'vt'),
        'delete_failed' => __('Delete failed', 'vt'),
        
        // 评论回复相关
        'reply_to_comment' => __('Reply to comment...', 'vt'),
        'submit_reply' => __('Submit Reply', 'vt'),
        'cancel' => __('Cancel', 'vt'),
        'please_enter_reply' => __('Please enter reply content', 'vt'),
        'submitting_reply' => __('Submitting...', 'vt'),
        'comment_form_not_found' => __('Comment form not found', 'vt'),
        
        // 评论状态相关
        'awaiting_moderation' => __('Your comment is awaiting moderation.', 'vt'),
        
        // 按钮文字
        'reply' => __('Reply', 'vt'),
        'delete' => __('Delete', 'vt'),
        
        // 评论字数限制相关
        'comment_cannot_be_empty' => __('Comment cannot be empty.', 'vt'),
        'comment_max_length' => __('Comment cannot exceed %d characters.', 'vt'),
    ];

    return $vt_global_i18n_strings;
}






