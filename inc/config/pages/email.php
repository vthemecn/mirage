<?php
/**
 * 邮箱设置
 *
 * @author: soushenji <soushenji@qq.com>
 * @link: https://vtheme.cn
 */
defined('ABSPATH') || exit;
?>

<form action="" method="post" enctype="multipart/form-data" name="op_form" id="op_form">
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="vt_email_is_on">开启自定义邮箱设置</label></th>
                <td>
                    <p>
                        <label>
                            <input name="vt_email_is_on" type="radio" value="1" <?php if ($vt_config['vt_email_is_on'] == 1) { ?>checked="checked" <?php } ?>>
                            使用
                        </label>
                        <label>
                            <input name="vt_email_is_on" type="radio" value="0" <?php if ($vt_config['vt_email_is_on'] == 0) { ?>checked="checked" <?php } ?>>
                            禁用
                        </label>
                    </p>
                    <p class="description">使用以下设置发送邮件</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="smtp_host">SMTP 地址</label></th>
                <td><input type="text" name="smtp_host" id="smtp_host" value="<?php echo ($vt_config['smtp_host']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="smtp_port">SMTP 端口</label></th>
                <td><input type="text" name="smtp_port" id="smtp_port" value="<?php echo ($vt_config['smtp_port']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="smtp_username">SMTP 账户</label></th>
                <td><input type="text" name="smtp_username" id="smtp_username" value="<?php echo ($vt_config['smtp_username']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="smtp_password">SMTP 密码</label></th>
                <td><input type="text" name="smtp_password" id="smtp_password" value="<?php echo ($vt_config['smtp_password']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="smtp_nicename">发件人名称</label></th>
                <td><input type="text" name="smtp_nicename" id="smtp_nicename" value="<?php echo ($vt_config['smtp_nicename']); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"></th>
                <td><br></td>
            </tr>
            <tr>
                <th scope="row"><label for="smtp_nicename">测试收件人邮箱地址</label></th>
                <td>
                    <input type="text" name="test_email" value="<?php echo ($vt_config['test_email']); ?>" class="regular-text">
                    <button type="button" id="sendemail-test-button" data-token="<?php echo wp_create_nonce('wp_rest'); ?>" data-url="<?php echo bloginfo('wpurl') ?>/wp-json/vtheme/v1/sendmail-test">
                        发送测试邮件
                    </button>
                    <p class="description">测试邮件配置是否正确，请保存配置后，再进行发送邮件测试</p>
                </td>
            </tr>

        </tbody>
    </table>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="input_save" value="保存" class="button button-primary" />
</form>
