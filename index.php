<?php

/**
 * 主模版文件
 *
 * @author: vtheme <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

get_header();

$home_layout_enabled = vt_get_config('home_layout', ['enabled' => []])['enabled'];

?>

<?php
if($home_layout_enabled){
    foreach($home_layout_enabled AS $k=>$v){
        $k = str_replace('\\', '', $k);
        $k = str_replace('/', '', $k);
        $file_path = THEME_DIR . '/templates/home/'. $k .'.php';
        
        if(file_exists($file_path)){
            require $file_path;
        }
    }
}
?>


<?php
get_footer();
