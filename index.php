<?php

/**
 * 主模版文件
 *
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

$config = vt_get_config();

?>

<?php
if($config['home_layout']['enabled']){
    foreach($vt_config['home_layout']['enabled'] AS $k=>$v){
        $k = str_replace('\\', '', $k);
        $k = str_replace('/', '', $k);
        require get_template_directory() . '/templates/home/'. $k .'.php';
    }
}
?>


<?php
get_footer();
