<?php
$current_action = get_query_var('action', 'edit');
?>
<div class="vt-account-tab">
    <a class="vt-account-tab-item <?=vt_active($current_action,'edit')?>" href="<?=home_url('/account/edit')?>">
        <i class="fa-solid fa-user"></i>
        <?=__('Profile','vt')?>
    </a>
    <a class="vt-account-tab-item <?=vt_active($current_action,'like')?>" href="<?=home_url('/account/like')?>">
        <i class="fa-solid fa-heart"></i>
        <?=__('Like','vt')?>
    </a>
    <a class="vt-account-tab-item <?=vt_active($current_action,'star')?>" href="<?=home_url('/account/star')?>">
        <i class="fa-solid fa-star"></i>
        <?=__('Star','vt')?>
    </a>
</div>