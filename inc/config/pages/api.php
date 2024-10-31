<?php

/* 
 * @author: vtheme <mail@vtheme.cn>
 * @link: https://vtheme.cn
 */
defined('ABSPATH') || exit;
?>


<form action="" method="post" enctype="multipart/form-data" name="op_form" id="op_form">
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="is_development">开启开发模式</label></th>
                <td>
                    <p>
                        <label>
                            <input name="is_development" type="radio" value="1" <?php if ($vt_config['is_development'] == 1) { ?>checked="checked" <?php } ?>>
                            开启
                        </label>
                        <label>
                            <input name="is_development" type="radio" value="0" <?php if ($vt_config['is_development'] == 0) { ?>checked="checked" <?php } ?>>
                            关闭
                        </label>
                    </p>
                    <p class="description">开启开发模式后，短信验证码不会真实发送</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="upload_images_limit">上传图片限制</label></th>
                <td>
                    <input type="text" name="upload_images_limit" id="upload_images_limit" value="<?php echo ($vt_config['upload_images_limit']); ?>" class="regular-text" placeholder="请输入微信手机号">
                    <p class="description">每个用户每天上传图片的数量限制</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="account_mode">账户类型</label></th>
                <td>
                    <?php
                    $account_mode = is_array($vt_config['account_mode']) ? $vt_config['account_mode'] : json_decode($vt_config['account_mode'], true);
                    ?>
                    <p>
                        <label>
                            <input name="account_mode[]" type="checkbox" value="mobile" <?php if (in_array('mobile', $account_mode)) { ?>checked="checked" <?php } ?>>
                            手机登录
                        </label>
                        <label>
                            <input name="account_mode[]" type="checkbox" value="email" <?php if (in_array('email', $account_mode)) { ?>checked="checked" <?php } ?>>
                            邮箱登录
                        </label>
                        <label style="display:none">
                            <input name="account_mode[]" type="checkbox" value="username" checked="checked">
                            用户名登录
                        </label>
                    </p>
                    <p class="description">用户注册登录模式</p>
                </td>
            </tr>

        </tbody>
    </table>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="input_save" value="保存" class="button button-primary" />
</form>
