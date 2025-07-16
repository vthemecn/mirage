<?php

$vt_config = vt_get_config();

?>


<div class="float-widget">

    <?php if ($vt_config['is_side_bar_user_center_show']) : ?>
        <div class="tool-widget">
            <a class="tool-button" href="<?php echo home_url('/user-center'); ?>">
                <i class="iconfont">&#xe8a5;</i>
            </a>
        </div>
    <?php endif ?>

    <?php /* if ($vt_options['is_side_bar_dark_mode_show']) : ?>
        <?php $is_dark_mode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] == 1 ? true : false; ?>
        <div class="tool-widget">
            <a href="javascript:;" class="tool-button night-mode-button <?php echo $is_dark_mode ? "sun-icon" : '' ?>">
                <i class="iconfont night-mode">&#xe804;</i>
                <i class="iconfont daytime-mode">&#xe7ff;</i>
            </a>
        </div>
    <?php endif */ ?>

    <div class="tool-widget">
        <a class="tool-button to-top" onclick="javascript:;" style="display:none">
            <i class="fa-solid fa-arrow-up"></i>
        </a>
    </div>
    
</div>
