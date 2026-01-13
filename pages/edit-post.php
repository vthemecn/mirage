<?php
/**
 * 用户编辑文章页面
 */
global $wp_query;
global $current_user;

$param_user_id = $wp_query->query_vars['user_id'];
$post_id = $wp_query->query_vars['post_id'];

// 获取文章
$post = get_post($post_id);

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
            <div class="user-center-panel">
                <h2>编辑文章</h2>
                
                <form id="edit-post-form" class="form">
                    <div class="field field-text">
                        <label for="post-title">文章标题</label>
                        <input type="text" id="post-title" name="post_title" class="form-control" value="<?php echo esc_attr($post->post_title); ?>" required>
                    </div>
                    
                    <div class="field field-select">
                        <label for="post-category">分类</label>
                        <?php
                        $categories = get_categories(array('hide_empty' => false));
                        $post_category = get_the_category($post->ID);
                        $selected_category = !empty($post_category) ? $post_category[0]->term_id : '';
                        ?>
                        <select id="post-category" name="post_category" class="form-control" required>
                            <option value="">选择分类</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category->term_id; ?>" <?php selected($selected_category, $category->term_id); ?>><?php echo $category->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="field field-upload">
                        <label>封面图片</label>
                        <div class="image-upload-area" id="imageUploadArea">
                            <div class="image-upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>拖拽图片到这里或点击选择文件</p>
                                <input type="file" id="featured-image" name="featured_image" accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-light" id="selectImageBtn">选择图片</button>
                            </div>
                            <div class="image-preview" id="imagePreview" style="<?php echo get_post_thumbnail_id($post->ID) ? 'display: block;' : 'display: none;'; ?>">
                                <img id="previewImage" src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id($post->ID), 'medium'); ?>" alt="预览图">
                                <button type="button" class="btn btn-sm btn-danger" id="removeImageBtn">移除图片</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="field field-textarea">
                        <label for="post-content">文章内容</label>
                        <?php
                        // 使用WordPress内置的编辑器函数，配置最简化的TinyMCE
                        $settings = array(
                            'tinymce' => array(
                                'plugins' => 'wordpress,wpautoresize,lists,media,paste,tabfocus,image',
                                'toolbar1' => 'bold,italic,underline,blockquote,bullist,numlist,link,|,image,|,undo,redo',
                                'toolbar2' => '',
                                'wpautop' => true,
                                'indent' => false,
                                'elementpath' => false,
                                'branding' => false,
                                'images_upload_handler' => 'function(blobInfo, success, failure, progress) {
                                    var formData = new FormData();
                                    formData.append("image", blobInfo.blob(), blobInfo.filename());

                                    fetch("' . home_url('/wp-json/vtheme/v1/upload/image') . '", {
                                        method: "POST",
                                        body: formData,
                                        headers: {
                                            "X-WP-Nonce": "' . wp_create_nonce('wp_rest') . '"
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            success(data.url);
                                        } else {
                                            failure(data.message || "上传失败");
                                        }
                                    })
                                    .catch(error => {
                                        failure("上传过程中发生错误");
                                    });
                                }',
                                'image_advtab' => false,
                                'image_description' => false,
                                'image_title' => false,
                                'image_dimensions' => false,
                                'paste_data_images' => false,
                            ),
                            'quicktags' => true,
                            'media_buttons' => false,
                            'textarea_name' => 'post_content',
                            'textarea_rows' => 15
                        );
                        wp_editor($post->post_content, 'post-content', $settings);
                        ?>
                    </div>
                    
                    <div class="field">
                        <button type="submit" class="btn btn-primary">更新文章</button>
                        <button type="button" id="delete-post-btn" class="btn btn-danger" style="margin-left: 10px;">删除文章</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<style>
.image-upload-area {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    background-color: #fafafa;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.image-upload-area:hover {
    border-color: #007cba;
    background-color: #f0f8ff;
}

.image-upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.image-upload-content i {
    font-size: 48px;
    color: #aaa;
    margin-bottom: 10px;
}

.image-preview {
    margin-top: 15px;
    text-align: center;
}

.image-preview img {
    max-width: 100%;
    max-height: 200px;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#removeImageBtn {
    margin-top: 10px;
}

/* 使用类似设置页面的表单样式 */
.form {
    max-width: 800px;
}

.field {
    margin-bottom: 20px;
}

.field label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
}

.field input[type="text"],
.field input[type="email"],
.field select,
.field textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
}

.field input[type="text"]:focus,
.field input[type="email"]:focus,
.field select:focus,
.field textarea:focus {
    outline: none;
    border-color: #007cba;
    box-shadow: 0 0 0 2px rgba(0, 124, 186, 0.2);
}

.field-textarea {
    display: flex;
    flex-direction: column;
}

.field-textarea label {
    margin-bottom: 5px;
}

.field-textarea textarea {
    width: 100%;
    min-height: 100px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    resize: vertical;
}

.user-center-panel h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
    color: #333;
}
</style>

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
    
    // 图片上传功能
    const imageUploadArea = document.getElementById('imageUploadArea');
    const featuredImageInput = document.getElementById('featured-image');
    const selectImageBtn = document.getElementById('selectImageBtn');
    const removeImageBtn = document.getElementById('removeImageBtn');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    
    // 点击选择图片按钮
    selectImageBtn.addEventListener('click', function() {
        featuredImageInput.click();
    });
    
    // 点击上传区域选择图片
    imageUploadArea.addEventListener('click', function(e) {
        if(e.target === imageUploadArea || e.target.classList.contains('image-upload-content')) {
            featuredImageInput.click();
        }
    });
    
    // 文件选择事件
    featuredImageInput.addEventListener('change', function() {
        if(this.files && this.files[0]) {
            const file = this.files[0];
            
            // 验证文件类型
            if(!file.type.match('image.*')) {
                alert('请选择图片文件！');
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // 移除图片
    removeImageBtn.addEventListener('click', function() {
        featuredImageInput.value = '';
        imagePreview.style.display = 'none';
        previewImage.src = '';
        
        // 添加一个标记，表示需要移除特色图片
        const removeFeaturedImageInput = document.createElement('input');
        removeFeaturedImageInput.type = 'hidden';
        removeFeaturedImageInput.name = 'remove_featured_image';
        removeFeaturedImageInput.value = '1';
        document.getElementById('edit-post-form').appendChild(removeFeaturedImageInput);
    });
    
    // 拖拽上传功能
    imageUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.style.borderColor = '#007cba';
        this.style.backgroundColor = '#e6f2ff';
    });
    
    imageUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.style.borderColor = '#ccc';
        this.style.backgroundColor = '#fafafa';
    });
    
    imageUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.style.borderColor = '#ccc';
        this.style.backgroundColor = '#fafafa';
        
        if(e.dataTransfer.files && e.dataTransfer.files[0]) {
            const file = e.dataTransfer.files[0];
            
            // 验证文件类型
            if(!file.type.match('image.*')) {
                alert('请选择图片文件！');
                return;
            }
            
            // 将文件赋给隐藏的input
            featuredImageInput.files = e.dataTransfer.files;
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // 表单提交事件
    const form = document.getElementById('edit-post-form');
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
                
                // 添加文章ID
                formData.append('post_id', <?php echo $post_id; ?>);
                
                fetch('<?php echo home_url('/wp-json/vtheme/v1/posts/' . $post_id); ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('文章更新成功！');
                        // 可以选择跳转到我的文章列表页
                        window.location.href = '<?php echo home_url("/users/{$param_user_id}/my-posts"); ?>';
                    } else {
                        alert('更新失败：' + (data.message || '未知错误'));
                    }
                })
                .catch(error => {
                    alert('更新失败：' + error.message);
                });
            });
        });
    }
    
    // 删除文章按钮事件
    const deletePostBtn = document.getElementById('delete-post-btn');
    if (deletePostBtn) {
        deletePostBtn.addEventListener('click', function() {
            if (confirm('确定要删除这篇文章吗？此操作不可恢复！')) {
                fetch('<?php echo home_url('/wp-json/vtheme/v1/posts/' . $post_id); ?>', {
                    method: 'DELETE',
                    headers: {
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('文章删除成功！');
                        // 跳转到我的文章列表页
                        window.location.href = '<?php echo home_url("/users/{$param_user_id}/my-posts"); ?>';
                    } else {
                        alert('删除失败：' + (data.message || '未知错误'));
                    }
                })
                .catch(error => {
                    alert('删除失败：' + error.message);
                });
            }
        });
    }
});
</script>

<?php get_footer(); ?>