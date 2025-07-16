<?php
/**
 * 移动端底部菜单
 */

$vt_options = vt_get_config();

$current_user_id = get_current_user_id();

?>


<div class="mobile-nav">
    <div class="mobile-nav-container">
        <?php
        $mobile_nav_config = $vt_options['mobile_nav_config'];
        foreach ($mobile_nav_config as $k => $v) :
        ?>
            <a class="nav-button" href="<?=$v['nav_link']?>">
                <i class="<?=$v['nav_icon']?>"></i>
                <b><?=$v['nav_text']?></b>
            </a>
        <?php endforeach ?>
    </div>
</div>

<style>
body{ padding-bottom:45px; }

@media only screen and (min-width: 900px) {
    body{ padding-bottom:0; }
}
</style>
