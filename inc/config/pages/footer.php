<?php
/**
 * 底部设置
 *
 * @author: vtheme <mail@vtheme.cn>
 * @link: https://vtheme.cn
 */
defined('ABSPATH') || exit;

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

            <tr class="td-top-line">
                <th scope="row">
                    <div class='title-light'>Footer 设置</div>
                </th>
                <td></td>
            </tr>

            <tr>
                <th scope="row"><label for="footer_copyright">底部版权</label></th>
                <td>
                    <textarea name="footer_copyright" id="footer_copyright" class="regular-text" rows="5"><?php echo ($vt_config['footer_copyright']); ?></textarea>
                    <p class="description" id="tagline-description">底部版权的文字信息，支持 HTML</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="footer_bg_type">footer 背景颜色</label></th>
                <td>
                    <p>
                        <label>
                            <input name="footer_bg_type" type="radio" value="0" <?php if ($vt_config['footer_bg_type'] == 0) { ?>checked="checked" <?php } ?>>
                            暗色
                        </label>
                        <label>
                            <input name="footer_bg_type" type="radio" value="1" <?php if ($vt_config['footer_bg_type'] == 1) { ?>checked="checked" <?php } ?>>
                            亮色
                        </label>
                    </p>
                </td>
            </tr>


            <tr class="td-top-line">
                <th scope="row">
                    <div class='title-light'>移动端底部导航设置</div>
                </th>
                <td></td>
            </tr>
            <tr>
                <th scope="row"><label for="is_mobile_nav_show">显示移动端导航</label></th>
                <td>
                    <p>
                        <label>
                            <input name="is_mobile_nav_show" type="radio" value="1" <?php if ($vt_config['is_mobile_nav_show'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                        <br>
                        <label>
                            <input name="is_mobile_nav_show" type="radio" value="0" <?php if ($vt_config['is_mobile_nav_show'] == 0) { ?>checked="checked" <?php } ?>>
                            不显示
                        </label>
                    </p>
                    <p class="description">默认显示移动端导航</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="mobile_nav_config">导航设置</label></th>
                <td>
                    <textarea name="mobile_nav_config" id="mobile_nav_config" class="regular-text" rows="5"><?php echo ($vt_config['mobile_nav_config']); ?></textarea>
                    <p class="description" id="tagline-description"></p>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="input_save" value="保存" class="button button-primary" />
</form>
