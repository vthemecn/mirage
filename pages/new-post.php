<?php
/**
 * 用户发布文章页面
 */
global $wp_query;
global $current_user;

$param_user_id = $wp_query->query_vars['user_id'];

$param_user = get_user_by('id', $param_user_id);

$has_auth = false;
if( $param_user_id ==  $current_user->ID || current_user_can( 'manage_options' ) ){
    $has_auth = true;
}

// 如果用户没有权限，跳转到首页
if (!$has_auth) {
    wp_redirect(home_url());
    exit;
}

get_header();
?>


<?php require_once get_template_directory() . '/templates/users/banner.php'; ?>


<div class="user-center-container">
    <?php require_once get_template_directory() . '/templates/users/sider.php'; ?>

    <div class="user-wrapper">
        <div class="user-center-panel">
            <div class="publish-article-container">
                <h3>发布新文章</h3>
                
                <form id="publish-post-form">
                    <div class="form-group">
                        <label for="post-title">文章标题</label>
                        <input type="text" id="post-title" name="post_title" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="post-category">分类</label>
                        <?php
                        $categories = get_categories(array('hide_empty' => false));
                        ?>
                        <select id="post-category" name="post_category" class="form-control" required>
                            <option value="">选择分类</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="post-content">文章内容</label>
                        <?php
                        // 使用WordPress内置的编辑器函数，配置最简化的TinyMCE
                        $settings = array(
                            'tinymce' => array(
                                'plugins' => 'wordpress,wpautoresize,lists,media,paste,tabfocus',
                                'toolbar1' => 'bold,italic,underline,blockquote,bullist,numlist,link,wp_adv',
                                'toolbar2' => 'formatselect,alignleft,aligncenter,alignright,undo,redo',
                                'wpautop' => true,
                                'indent' => false,
                                'elementpath' => false,
                                'branding' => false,
                            ),
                            'quicktags' => true,
                            'media_buttons' => true,
                            'textarea_name' => 'post_content',
                            'textarea_rows' => 15
                        );
                        wp_editor('', 'post-content', $settings);
                        ?>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">提交文章</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 确保TinyMCE加载完成后再操作
    const waitForTinyMCE = function(callback) {
        if (typeof tinymce !== 'undefined' && tinymce.get && tinymce.get('post-content') && tinymce.get('post-content').initialized) {
            callback();
        } else {
            setTimeout(function() {
                waitForTinyMCE(callback);
            }, 250);
        }
    };
    
    // 表单提交事件
    const form = document.getElementById('publish-post-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 等待TinyMCE加载完成后再获取内容
            waitForTinyMCE(function() {
                // 确保TinyMCE内容同步到textarea
                if (typeof tinymce !== 'undefined' && tinymce.get('post-content')) {
                    // 触发内容保存
                    tinymce.get('post-content').save();
                }
                
                // 获取TinyMCE内容
                let content = document.getElementById('post-content').value;
                
                // 验证内容是否为空
                if (!content || content.trim() === '') {
                    alert('文章内容不能为空');
                    return;
                }
                
                // 验证标题是否为空
                const title = document.getElementById('post-title').value;
                if (!title || title.trim() === '') {
                    alert('文章标题不能为空');
                    return;
                }
                
                const formData = new FormData(form);
                
                // 添加文章状态为私有（待审核）
                formData.append('post_status', 'private');
                
                fetch('<?php echo home_url('/wp-json/vtheme/v1/posts'); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('文章发布成功，等待管理员审核！');
                        form.reset();
                        // 重置TinyMCE编辑器内容
                        if (typeof tinymce !== 'undefined' && tinymce.get('post-content')) {
                            tinymce.get('post-content').setContent('');
                        }
                        // 重置textarea
                        document.getElementById('post-content').value = '';
                    } else {
                        alert('发布失败：' + (data.message || '未知错误'));
                    }
                })
                .catch(error => {
                    alert('发布失败：' + error.message);
                });
            });
        });
    }
});
</script>

<?php get_footer(); ?>