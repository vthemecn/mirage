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

            <tr>
                <th scope="row"><label for="baidu_is_on">百度主动推送</label></th>
                <td>
                    <p>
                        <label>
                            <input name="baidu_is_on" type="radio" value="1" <?php if ($vt_config['baidu_is_on'] == 1) { ?>checked="checked" <?php } ?>>
                            开启
                        </label>
                        <label>
                            <input name="baidu_is_on" type="radio" value="0" <?php if ($vt_config['baidu_is_on'] == 0) { ?>checked="checked" <?php } ?>>
                            关闭
                        </label>
                    </p>
                    <p class="description">百度提示：百度搜索主动推送资源，缩短爬虫发现网站链接的时间，不保证收录和展现效果</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="baidu_key">百度准入密钥</label></th>
                <td><input type="text" name="baidu_key" id="baidu_key" value="<?= $vt_config['baidu_key'] ?>" class="regular-text" placeholder="百度准入密钥">
                    <p class="description">
                        获取地址： <a href="https://ziyuan.baidu.com/linksubmit/index" target="_blank">https://ziyuan.baidu.com/linksubmit/index</a>
                    </p>
                </td>
            </tr>



        </tbody>
    </table>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="input_save" value="保存" class="button button-primary" />
</form>