<?php
$vt_config = vt_get_config();

$footer_bg_type = $vt_config['footer_bg_type'] == 'darkness' ? 'darkness' : 'light';

$current_theme = wp_get_theme();
$version = $current_theme->get('Version');
?>


<footer class="footer" <?= $footer_bg_type ?> >
    <div class="footer-container">
        <?php echo $vt_config['footer_copyright']; ?>
        <br/>
        <span>
            Theme by <a href="https://vtheme.cn/miragev" copyright target="_blank">MirageV</a>
        </span>
        <?php if($vt_config['page_data_type'] == 1): ?>
            <span>查询次数:<?php echo get_num_queries(); ?> </span>
        <?php elseif($vt_config['page_data_type'] == 2) :?>
            <span>查询次数:<?php echo get_num_queries(); ?>  </span>
            <span>执行时间:<?php echo timer_stop( false, 3 ); ?> </span>
        <?php endif ?>
    </div>
</footer>


<?php
require_once(TEMP_DIR . "/templates/bar.php");

if ($vt_config['mobile_nav_show'] == 1) {
    include_once(get_template_directory() . "/templates/mobile-nav.php");
}
?>

<script src="<?php bloginfo('template_url'); ?>/assets/lib/qrcode.js"></script>
<script src="<?php bloginfo('template_url'); ?>/assets/lib/html2canvas.js"></script>
<script src="<?php bloginfo('template_url'); ?>/assets/js/index.js?v=<?= $version ?>"></script>

<?php wp_footer(); ?>

<?= $vt_config['footer_js']?>

</body>
</html>
