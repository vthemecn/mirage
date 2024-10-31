<?php
$vt_config = vt_get_config();
$colored_class = $vt_config['footer_bg_type'] == 0 ? 'colored' : '';
?>

<footer class="footer <?php echo $colored_class ?>">
    <div class="footer-container">
        <?php echo $vt_config['footer_copyright']; ?>
        <br/>
        <span>
            Powered by <a href="https://vtheme.cn/miragev" copyright target="_blank">MirageV</a>
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
require_once(get_stylesheet_directory() . "/templates/bar.php");

if ($vt_config['is_mobile_nav_show'] == 1) {
    include_once(get_template_directory() . "/templates/mobile-nav.php");
}

$current_theme = wp_get_theme();

?>

<script src="<?php bloginfo('template_url'); ?>/assets/js/lib/qrcode.js"></script>
<script src="<?php bloginfo('template_url'); ?>/assets/js/lib/html2canvas.js"></script>
<script src="<?php bloginfo('template_url'); ?>/assets/js/bundle.js?t=<?php echo $current_theme->get('Version') ?>"></script>

<?php wp_footer(); ?>

</body>
</html>
