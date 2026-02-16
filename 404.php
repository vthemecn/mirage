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
	<img src="<?php bloginfo('template_url'); ?>/assets/images/404.svg" alt="404 <?=__('Page not found','vt')?>">
	<p>404 <?=__('Page not found','vt')?></p>
	<a href="<?php bloginfo('url'); ?>"><?=__('Go to home','vt')?></a>
</div>


<?php
get_footer();