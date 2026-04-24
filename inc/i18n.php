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
        'operation_failed' => __('Operation Failed', 'vt'),
        'remove_like' => __('Remove Like', 'vt'),
        'like' => __('Like', 'vt'),
        'remove_star' => __('Remove Star', 'vt'),
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


/**
 * 解析语言文件
 */
function parse_po_file($po_file_path) {
    $translations = [];
    $content = file_get_contents($po_file_path);
    
    // 匹配 msgid 和 msgstr
    preg_match_all('/msgid\s+"([^"]+)"\s+msgstr\s+"([^"]+)"/s', $content, $matches);
    
    foreach ($matches[1] as $index => $msgid) {
        $msgstr = $matches[2][$index];
        if (!empty($msgstr)) {
            $translations[$msgid] = $msgstr;
        }
    }
    
    return $translations;
}


/**
 * 获取解析缓存
 */
function vt_get_translations_cached() {
    $cache_key = 'vt_translations_' . determine_locale();
    $cached = get_transient($cache_key);
    
    if ($cached !== false) {
        return $cached;
    }
    
    $translations = parse_po_file(get_template_directory() . '/inc/languages/' . determine_locale() . '.po');
    set_transient($cache_key, $translations, DAY_IN_SECONDS);
    
    return $translations;
}



