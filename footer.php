<?php
$config = vt_get_config();

$footer_bg_type = $config['footer_bg_type'] == 'darkness' ? 'darkness' : 'light';

$current_theme = wp_get_theme();
$version = $current_theme->get('Version');
?>


<footer class="footer" <?= $footer_bg_type ?> >
    <div class="footer-container">
        <?php echo $config['footer_copyright']; ?>
        <span>
            Theme by <a href="https://vtheme.cn/mirage" copyright target="_blank">Mirage</a>
        </span>
        <?php if($config['page_data_type'] == 1): ?>
            <span>查询次数:<?php echo get_num_queries(); ?> </span>
        <?php elseif($config['page_data_type'] == 2) :?>
            <span>查询次数:<?php echo get_num_queries(); ?>  </span>
            <span>执行时间:<?php echo timer_stop( false, 3 ); ?> </span>
        <?php endif ?>
    </div>
</footer>


<?php
require_once(TEMP_DIR . "/templates/bar.php");
?>

<script src="<?php bloginfo('template_url'); ?>/assets/lib/qrcode.js"></script>
<script src="<?php bloginfo('template_url'); ?>/assets/lib/html2canvas.js"></script>
<script src="<?php bloginfo('template_url'); ?>/assets/js/index.js?v=<?= $version ?>"></script>

<?php wp_footer(); ?>

<?= $config['footer_js']?>

</body>
</html>
