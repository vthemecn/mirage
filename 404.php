<?php

/**
 * 404 模版
 * 
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();
?>


<div class="page-no-found">
	<img src="<?php bloginfo('template_url'); ?>/assets/images/404.svg" alt="404 页面没找到">
	<p>404 页面没找到</p>
	<a href="<?php bloginfo('url'); ?>">返回首页</a>
</div>


<?php
get_footer();
