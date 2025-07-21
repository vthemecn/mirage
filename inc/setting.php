<?php
$vt_config = vt_get_config();


function add_theme_support_all()
{
    //文章编辑页，没有页面属性选择模板，添加注释 Template Name

    // 新的 WordPress 网页标题设置方法
    add_theme_support('title-tag');

    /* Enable support for Post Thumbnails on posts and pages.
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    // Set post thumbnail size.
    set_post_thumbnail_size(1200, 9999);
}
add_action('after_setup_theme', 'add_theme_support_all');



/**
 * 支持文章排序
 */
function vt_add_post_attributes()
{
  add_post_type_support('post', 'page-attributes');
}
add_action('init', 'vt_add_post_attributes', 500);

function vt_pre_insert_post($post, \WP_REST_Request $request)
{
  $body = $request->get_body();
  if ($body) {
    $body = json_decode($body);
    if (isset($body->menu_order)) {
      $post->menu_order = $body->menu_order;
    }
  }
  return $post;
}
add_filter('rest_pre_insert_post', 'vt_pre_insert_post', 12, 2);

function vt_prepare_post(\WP_REST_Response $response, $post, $request)
{
  $response->data['menu_order'] = $post->menu_order;
  return $response;
}
add_filter('rest_prepare_post', 'vt_prepare_post', 12, 3);



// 不显示顶部的工具栏
if($vt_config['show_admin_bar'] != 1){
  show_admin_bar(false);
}


// function smartwp_reverse_comment_order( $comments ) {
//  return array_reverse( $comments );
// }
// add_filter ('comments_array', 'smartwp_reverse_comment_order');

/**
 * 设置显示字数
 */
add_filter('excerpt_length', 'vt_excerpt_length', 999);
function vt_excerpt_length($length) {
    return 200;
}


/**
 * 禁用更新
 */
if($vt_config['update_is_on'] == 0){
    add_filter( 'pre_site_transient_update_core', '__return_null'); // 移除版本更新提示
    add_filter( 'pre_site_transient_update_plugins', '__return_null'); // 移除插件更新提示
    add_filter('pre_site_transient_update_themes', '__return_null'); // 关闭插件提示
    
    remove_action('admin_init', '_maybe_update_core');    // 禁用 WordPress 检查更新
    remove_action('admin_init', '_maybe_update_plugins'); // 禁用 WordPress 更新插件
    remove_action('admin_init', '_maybe_update_themes');  // 禁用 WordPress 更新主题
    // add_filter( 'automatic_updater_disabled', '__return_true' );
    remove_action( 'load-update-core.php', 'wp_update_plugins' );
    remove_action ('load-update-core.php', 'wp_update_themes'); 
    
    function vt_remove_php_nag() {
        remove_meta_box( 'dashboard_php_nag', 'dashboard', 'normal' );// 建议更新PHP版本
    }
    add_action( 'wp_dashboard_setup', 'vt_remove_php_nag' );
}


/**
 * 是否禁用古腾堡编辑器，启用经典编辑器
 */
if ($vt_config['editor_type'] == 1) {
    /* Disable Gutenberg Block Editor */
    add_filter('use_block_editor_for_post', '__return_false', 10);
    /* Disable Widgets Block Editor */
    // add_filter( 'use_widgets_block_editor', '__return_false' );
    
    //添加HTML编辑器自定义快捷标签按钮
    add_action('after_wp_tiny_mce', 'add_button_mce');
    function add_button_mce($mce_settings)
    {
        ?>
        <script type="text/javascript">
        QTags.addButton( 'hr', 'hr', "<hr />", "" );
        QTags.addButton( 'h1', 'h1', "<h1>", "</h1>" );
        QTags.addButton( 'h2', 'h2', "<h2>", "</h2>" );
        QTags.addButton( 'h3', 'h3', "<h3>", "</h3>" );
        QTags.addButton( 'p', 'p', "<p>", "</p>" );
        QTags.addButton( 'pre', 'pre', "<pre>", "</pre>" );
        </script>
        <?php
    }
    
    // 修改编辑器内容样式
    function vt_add_editor_style( $mceInit ) {
      $styles = "#tinymce{ font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell,'Helvetica Neue',sans-serif; } pre { font-size:13px; background-color:#f0f0f0; padding:8px;border-radius:3px}";
    
      if ( !isset( $mceInit['content_style'] ) ) {
        $mceInit['content_style'] = $styles . ' ';
      } else {
        $mceInit['content_style'] .= ' ' . $styles . ' ';
      }
      return $mceInit;
    }
    add_filter( 'tiny_mce_before_init', 'vt_add_editor_style' );

    // 设置经典编辑器默认可视化
    // add_filter('wp_default_editor', create_function('', 'return "tinymce";'));
    add_filter('wp_default_editor', function(){return 'tinymce';});
}


/* 删除登录页面底部的语言切换 */
add_filter('login_display_language_dropdown', '__return_false');


/* 修改登录页的样式 */
function custom_loginlogo() {
    $vt_config = vt_get_config();
    echo '<style type="text/css">
    h1 a {
        background-image: url('. $vt_config['site_logo'] .') !important;
        width:260px !important;
        height:80px !important;
        margin: 0 auto !important;
        background-size: contain !important;
        background-position: center center !important;
    }
    /* #loginform{  border-radius: 5px; } */
    </style>';
}
add_action('login_head', 'custom_loginlogo'); 
//自定义登录页面logo链接
// add_filter('login_headerurl', create_function(false,"return get_bloginfo('url');"));
add_filter('login_headerurl', 'login_headerurl_action');
function login_headerurl_action(){ return get_bloginfo('url'); }


/* 文章自动保存 */
if($vt_config['editor_revision'] == 0){
    //禁用文章自动保存
    add_action('wp_print_scripts','vt_not_autosave');
    function vt_not_autosave(){
        wp_deregister_script('autosave');
    }
    //禁用文章修订版本
    add_filter( 'wp_revisions_to_keep', 'vt_revisions_to_keep', 10, 2 );
    function vt_revisions_to_keep( $num, $post ) {
        return 0;
    }
}


//开启友情链接管理
if( $config['links_is_show'] ){
    add_filter( 'pre_option_link_manager_enabled', '__return_true' );
}


/* 登录后跳转控制 */
function vt_login_redirect( $redirect_to, $request, $user ) {
    $vt_config = vt_get_config();
    // 如果登录成功并且用户是管理员，则跳转到后台管理页面
    if ( is_array( $user->roles ) && in_array( 'administrator', $user->roles ) ) {
        return admin_url();
    } else {
        // 否则跳转到个人资料页面
        if($vt_config['user_center_is_on']){
            return home_url( '/users/' .  $user->ID );
        }
        return home_url( '/wp-admin/profile.php' );
    }
}

add_filter( 'login_redirect', 'vt_login_redirect', 10, 3 );


/**
 * 自定义图片名称
 */
add_filter('wp_handle_upload_prefilter', 'vt_upload_filter');
function vt_upload_filter($file)
{
    $info = pathinfo($file['name']);
    $ext = $info['extension'];

    global $current_user;

    $filedate = date('YmdHis') . rand(10, 99) . $current_user->ID; //为了避免时间重复，再加一段2位的随机数
    $file['name'] = $filedate . '.' . $ext;
    return $file;
}



if ($vt_config['smtp_is_on'] == 1) {
    add_action('phpmailer_init', 'mail_smtp');
    function mail_smtp($phpmailer)
    {
        $config = vt_get_config();

        $phpmailer->IsSMTP();
        $phpmailer->SMTPAuth     = true;
        $phpmailer->SMTPSecure   = "ssl";
        $phpmailer->Port         = $config['smtp_port'];
        $phpmailer->Host         = $config['smtp_host'];
        $phpmailer->Username     = $config['smtp_username'];
        $phpmailer->Password     = $config['smtp_password'];
    }

    add_filter('wp_mail_from', 'vt_wp_mail_from');
    function vt_wp_mail_from()
    {
        $config = vt_get_config();
        return $config['smtp_username'];
    }

    add_filter('wp_mail_from_name', 'mail_from_name');
    function mail_from_name()
    {
        $config = vt_get_config();
        return $config['smtp_nicename'];
    }

    // 保存邮件发送错误信息
    add_action('wp_mail_failed', 'vt_add_mail_error');
    function vt_add_mail_error($wp_error)
    {
        update_option('vt_mail_error', $wp_error->get_error_message('wp_mail_failed'));
    }
}


/**
 * 限制登录尝试次数
 */
if($vt_config['attempts_is_on']){
    add_filter('authenticate', 'vt_authenticate_action', 1, 3);
}
function vt_authenticate_action($user, $username, $password){
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_option('vt_failed_attempts');
    $need_update = false;

    if (!$attempts) {
        $attempts = array();
    } else {
        foreach ($attempts as $k => $v) {
            if($v['flag'] != wp_date('YmdH')){
                unset($attempts[$k]);
                $need_update = true;
            }
        }
    }

    if($need_update){
        update_option('vt_failed_attempts', $attempts);
    }

    $max_attempts = 10;
    if (isset($attempts[$ip]) && $attempts[$ip]['counter'] >= $max_attempts) {
        remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
        remove_filter('authenticate', 'wp_authenticate_email_password', 20, 3);
        return new WP_Error('too_many_retries', '您已多次登录失败，请1小时后重试！');
    }
}

/**
 * 更新错误记录
 */
if($vt_config['attempts_is_on']){
    add_action('wp_login_failed', 'vt_login_failed_action');
}
function vt_login_failed_action($username){
    $ip = $_SERVER['REMOTE_ADDR'];
    $attempts = get_option('vt_failed_attempts');
 
    if (!$attempts) {
        $attempts = array();
    } else {
        foreach ($attempts as $k => $v) {
            if($v['flag'] != wp_date('YmdH')){ unset($attempts[$k]); }
        }
    }
 
    if (isset($attempts[$ip])) {
        $attempts[$ip]['counter']++;
    } else {
        $item = array('flag'=>wp_date('YmdH'), 'counter'=>1);
        $attempts[$ip] = $item;
    }
 
    update_option('vt_failed_attempts', $attempts);
}

/**
 * 登录框摇晃
 */
add_filter('shake_error_codes', function ($error_codes) {
    $error_codes[]  = 'too_many_retries';
    return $error_codes;
});


function vt_admin_jscss() {
 wp_enqueue_style( 'vt-jscss', get_template_directory_uri() . '/assets/css/admin.css' );
 // wp_enqueue_script('vt-jscss', get_template_directory_uri() . '/js/test.js' );
}
add_action('admin_head', 'vt_admin_jscss');


/**
 * TinyMCE 增加 codesample 插件
 * 参考： https://vtheme.cn/archives/1090
 */
function code_sample_add_external_plugin($mce_plugins)
{
    // $mce_plugins['codesample'] = 'https://cdn.jsdelivr.net/npm/tinymce@4.9.11/plugins/codesample/plugin.min.js';
    $mce_plugins['codesample'] = get_template_directory_uri() . '/assets/lib/tinymce/codesample/plugin.min.js';
    return $mce_plugins;
}
function code_sample_button($mce_buttons)
{
    $mce_buttons[] = 'codesample';
    return $mce_buttons;
}

if($vt_config['tinymce_codesample']){
    add_filter('mce_external_plugins', 'code_sample_add_external_plugin', 999, 1);
    add_filter('mce_buttons', 'code_sample_button', 999, 1);
}


/**
 * 禁止定期管理员邮箱验证
 */
add_filter( 'admin_email_check_interval', '__return_false' );



/**
 * 生成文章目录（TOC）
 */
function create_table_of_contents($content) {
    $matches = array();
    $regexp = "/<h([2-6])>(.*?)<\/h[2-6]>/";
    $regexp = '/<h([2-6]).*?\>(.*?)<\/h[2-6]>/is';

    if (preg_match_all($regexp, $content, $matches)) {
        $toc = '<ul class="toc_list">';
        foreach ($matches[0] as $key => $match) {
            $heading_level = intval($matches[1][$key]);
            $heading_text = wp_kses($matches[2][$key], '');
            $anchor = sanitize_title($heading_text);
            $content = str_replace($match, '<h' . $heading_level . ' id="' . $anchor . '">' . $heading_text . '</h' . $heading_level . '>', $content);
            $toc .= '<li class="level-'.$heading_level.'"><a href="#' . $anchor . '"> ' . $heading_text . '</a></li>';
        }
        $toc .= '</ul>';
    }
    return $toc ? $toc : '';
}


/**
 * 识别文章中所有的 <h2> 到 <h6> 标签；
 * 为每个标题添加唯一的 id（基于标题内容生成）；
 */
function add_heading_ids_to_content($content) {
    if (is_single() || is_page() ) {
        // 匹配所有 h2 到 h6 标签
        $pattern = '/<h([2-6])>([^<]+)<\/h[2-6]>/i';
        $pattern = '/<h([2-6]).*?\>(.*?)<\/h[2-6]>/is';

        // 替换回调函数
        $callback = function($matches) {
            $heading_level = $matches[1]; // h2-h6
            $heading_text = strip_tags($matches[2]); // 标题文字
            $id = sanitize_title($heading_text); // 生成安全的 id

            // 返回带 id 的新标题
            return "<h{$heading_level} id=\"{$id}\">{$heading_text}</h2>";
        };

        // 执行正则替换
        $content = preg_replace_callback($pattern, $callback, $content);
    }

    return $content;
}
add_filter('the_content', 'add_heading_ids_to_content');


/**
 * 后台添加自定义js
 */
function vt_add_admin_js(){ 
    wp_enqueue_media();
    wp_enqueue_script('vt-uploader', get_bloginfo('template_url').'/assets/lib/admin.js', array('jquery'), false, true );
}
add_action('admin_enqueue_scripts', 'vt_add_admin_js');