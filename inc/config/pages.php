<?php
// 设置首页
defined('ABSPATH') || exit;

$action = isset($_GET['action']) ? $_GET['action'] : "basic";

$option_pages = array(
    'basic'         => '常规设置',
    'home'          => '首页调用',
    'header'        => '顶部设置',
    'footer'        => '底部设置',
    'seo'           => 'SEO设置',
    'email'         => '邮箱设置',
    'api'           => 'API设置',
    'backup'        => '备份'
);

if (!array_key_exists($action, $option_pages)) {
    echo "<b>vtheme-options: </b> ./inc/config/pages no found!";
    exit;
}
?>

<link rel='stylesheet' href='<?php bloginfo('template_url'); ?>/inc/config/css/style.css' />
<script src="<?php bloginfo('template_url'); ?>/inc/config/js/index.js"></script>

<div class="wrap">
    <h1>BlogV 主题设置</h1>
    <p>
        BlogV 主题地址： <a href="https://vtheme.cn/blogv" target="_blank">https://vtheme.cn/blogv</a>
    </p>
    <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['input_save'])) { ?>
        <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
            <p><strong>设置已保存。</strong></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text">忽略此通知。</span></button>
        </div>
    <?php } ?>

    <hr class="wp-header-end">
    <nav class="nav-tab-wrapper wp-clearfix" aria-label="次要菜单">
        <?php foreach ($option_pages as $k => $v) : ?>
            <a href="<?php echo bloginfo('wpurl') ?>/wp-admin/themes.php?page=vtheme-options&action=<?php echo $k ?>" class="nav-tab <?php echo $k == $action ? 'nav-tab-active' : '' ?>">
                <?php echo $v; ?>
            </a>
        <?php endforeach ?>
    </nav>

    <?php include_once 'pages/' . $action . ".php";?>
</div>
