<?php
/*
 * 主题样式设置
 *
 * @author: soushenji <soushenji@qq.com>
 * @link: https://vtheme.cn
 */

$args = array(
    'type' => 'post',
    'child_of' => 0,
    'parent' => '',
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => 0,
    'hierarchical' => 1,
    'exclude' => '',
    'include' => '',
    'number' => '',
    'taxonomy' => 'category',
    'pad_counts' => false
);

$categories = get_categories($args);

?>

<form action="" method="post" enctype="multipart/form-data" name="op_form" id="op_form">
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="basic_style">主题的基本色调</label></th>
                <td>
                    <label class="style-laebl">
                        <input name="basic_style" type="radio" value="0" <?php if ($vt_config['basic_style'] == 0) { ?>checked="checked" <?php } ?>>
                        <div class="color-widget" style="background:#007aff;">#007aff</div>
                    </label>
                    <br>
                    <label class="style-laebl">
                        <input name="basic_style" type="radio" value="1" <?php if ($vt_config['basic_style'] == 1) { ?>checked="checked" <?php } ?>>
                        <div class="color-widget" style="background:#dd524d;">#dd524d</div>
                    </label>
                    <br>
                    <label class="style-laebl">
                        <input name="basic_style" type="radio" value="2" <?php if ($vt_config['basic_style'] == 2) { ?>checked="checked" <?php } ?>>
                        <div class="color-widget" style="background:#333;">#333</div>
                    </label>

                    <!-- <p class="description">幻灯片尺寸 1500*500</p> -->
                </td>
            </tr>





        </tbody>
    </table>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="input_save" value="保存" class="button button-primary" />
</form>



<style>
    .style-laebl {
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    .color-widget {
        width: 75px;
        height: 30px;
        line-height: 30px;
        text-align: center;
    }
</style>