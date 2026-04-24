<?php
global $current_user;

$footer_bg_type = vt_get_config('footer_bg_type', 'light') == 'darkness' ? 'darkness' : 'light';
$sns_items = vt_get_config('sns_items', []);
$footer_links = vt_get_config('footer_links', []);
$footer_copyright = vt_get_config('footer_copyright', '');
$page_data_type = vt_get_config('page_data_type', 0);
$footer_js = vt_get_config('footer_js', '');

$current_theme = wp_get_theme();
$version = $current_theme->get('Version');
?>


<footer class="footer" <?= $footer_bg_type ?> >

    <?php if(vt_get_config('sns_items') && count($sns_items)):?>
        <div class="footer-sns">
            <?php foreach($sns_items as $k => $v): ?>
                <a href="<?=$v['link']?>" target="_balnk" rel="nofollow" title="<?=$v['title']?>">
                    <img src="<?=$v['image']?>">
                </a>
            <?php endforeach;?>
        </div>
    <?php endif; ?>

    <?php if( vt_get_config('footer_links') && count($footer_links) ):?>
        <div class="footer-links">
            <?php foreach($footer_links as $k => $v): ?>
                <a href="<?=$v['link']?>" target="_balnk" rel="nofollow"><?=$v['title']?></a>
            <?php endforeach;?>
        </div>
    <?php endif; ?>

    <div class="footer-container">
        <?php echo $footer_copyright; ?>
        <span>
            Theme by <a href="https://vtheme.cn/mirage" copyright target="_blank">Mirage</a>
        </span>
        <?php if($page_data_type == 1): ?>
            <span><?=__('Query Count','vt')?>:<?php echo get_num_queries(); ?>  </span>
            <span><?=__('Execution Time','vt')?>:<?php echo timer_stop( false, 3 ); ?> </span>
        <?php elseif($page_data_type == 2): ?>
            <script> console.log('<?=__('Query Count','vt') .':'. get_num_queries() .' '.__('Execution Time','vt') .':'. timer_stop( false, 3 ); ?>') </script>
        <?php endif ?>
    </div>
</footer>


<?php
require_once(THEME_DIR . "/templates/bar.php");

if (!$current_user->ID) {
    require_once(THEME_DIR . "/templates/login-dialog.php");
}

?>

<script src="<?php bloginfo('template_url'); ?>/assets/lib/qrcode.js"></script>
<script src="<?php bloginfo('template_url'); ?>/assets/lib/html2canvas.js"></script>
<!-- <script src="<?php bloginfo('template_url'); ?>/assets/js/index.js?v=<?= $version ?>"></script> -->

<?php wp_footer(); ?>

<?= $footer_js?>

</body>
</html>

