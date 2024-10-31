<?php
/* 
 * 常规设置
 *
 * @author: vtheme <mail@vtheme.cn>
 * @link: https://vtheme.cn
 */
defined('ABSPATH') || exit;
?>


<form action="" method="post" enctype="multipart/form-data" name="op_form" id="op_form">
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="meta_keywords">网站关键词</label></th>
                <td><input type="text" name="meta_keywords" id="meta_keywords" value="<?php echo ($vt_config['meta_keywords']); ?>" class="regular-text" placeholder="请输入网站关键词"></td>
            </tr>
            <tr>
                <th scope="row"><label for="meta_description">网站描述</label></th>
                <td>
                    <textarea name="meta_description" id="meta_description" class="regular-text" rows="5" placeholder="请输入主题描述"><?php echo ($vt_config['meta_description']); ?></textarea>
                    <p class="description" id="tagline-description">用简洁的文字描述本站点。</p>
                </td>
            </tr>



        </tbody>
    </table>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="input_save" value="保存" class="button button-primary" />
</form>