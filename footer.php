<?php
$config = vt_get_config();

$footer_bg_type = $config['footer_bg_type'] == 'darkness' ? 'darkness' : 'light';

$current_theme = wp_get_theme();
$version = $current_theme->get('Version');
?>


<footer class="footer" <?= $footer_bg_type ?> >

    <?php if(vt_get_config('sns_items') && count($config['sns_items'])):?>
        <div class="footer-sns">
            <?php foreach($config['sns_items'] as $k => $v): ?>
                <a href="<?=$v['link']?>" target="_balnk" rel="nofollow" title="<?=$v['title']?>">
                    <img src="<?=$v['image']?>">
                </a>
            <?php endforeach;?>
        </div>
    <?php endif; ?>

    <?php if( vt_get_config('footer_links') && count($config['footer_links']) ):?>
        <div class="footer-links">
            <?php foreach($config['footer_links'] as $k => $v): ?>
                <a href="<?=$v['link']?>" target="_balnk" rel="nofollow"><?=$v['title']?></a>
            <?php endforeach;?>
        </div>
    <?php endif; ?>

    <div class="footer-container">
        <?php echo $config['footer_copyright']; ?>
        <span>
            Theme by <a href="https://vtheme.cn/mirage" copyright target="_blank">Mirage</a>
        </span>
        <?php if($config['page_data_type'] == 1): ?>
            <span><?=__('Query Count','vt')?>:<?php echo get_num_queries(); ?>  </span>
            <span><?=__('Execution Time','vt')?>:<?php echo timer_stop( false, 3 ); ?> </span>
        <?php elseif($config['page_data_type'] == 2): ?>
            <script> console.log('<?=__('Query Count','vt') .':'. get_num_queries() .' '.__('Execution Time','vt') .':'. timer_stop( false, 3 ); ?>') </script>
        <?php endif ?>
    </div>
</footer>


<?php
require_once(TEMP_DIR . "/templates/bar.php");
?>

<script src="<?php bloginfo('template_url'); ?>/assets/lib/qrcode.js"></script>
<script src="<?php bloginfo('template_url'); ?>/assets/lib/html2canvas.js"></script>
<!-- <script src="<?php bloginfo('template_url'); ?>/assets/js/index.js?v=<?= $version ?>"></script> -->

<?php wp_footer(); ?>

<?= $config['footer_js']?>

</body>
</html>
