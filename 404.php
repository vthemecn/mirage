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
	<img src="<?php bloginfo('template_url'); ?>/assets/images/404.svg" alt="404 <?=__('页面没找到','vt')?>">
	<p>404 <?=__('页面没找到','vt')?></p>
	<a href="<?php bloginfo('url'); ?>"><?=__('返回首页','vt')?></a>
</div>


<?php
get_footer();
