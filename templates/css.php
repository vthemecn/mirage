<style>
:root {
    --vt-color-primary: <?= vt_get_config('basic_style', '') ?>;
}
body:before {
    position: fixed;
    <?php if(vt_get_config('background_image', '')): ?>
    background-image:url(<?php echo vt_get_config('background_image', '')?>);
    <?php endif; ?>
}
.dark-mode-switch{
    position: relative;
    <?php
    if(vt_get_config('dark_mode_type', 2) == 0){ // 禁用
        echo 'display:none;';
    } elseif(vt_get_config('dark_mode_type', 2) == 1) { // 手动
        echo 'display:block;';
    } elseif(vt_get_config('dark_mode_type', 2) == 2) { // 自动
        echo 'display:block;';
    }
    ?>
}
</style>