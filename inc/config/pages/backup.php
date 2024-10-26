<?php
/*
 * 备份设置
 *
 * @author: soushenji <soushenji@qq.com>
 * @link: https://vtheme.cn
 */

defined('ABSPATH') || exit;
?>

<form action="" method="post" enctype="application/x-www-form-urlencoded" name="op_form" id="op_form">
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="option_backup">备份</label></th>
                <td>
                    <textarea name="option_backup" id="option_backup" class="regular-text" rows="10"><?php echo json_encode($vt_config, JSON_UNESCAPED_UNICODE); ?></textarea>
                    <p class="description" id="tagline-description"></p>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="backup_save" value="保存配置" class="button button-primary" />
    <input type="submit" name="backup_reset" value="重置配置" class="button button-danger" />
    <button class="button button-download" type='button'>下载配置</button>
</form>

<script>
    var form = document.getElementById('op_form');
    form.addEventListener('submit', function(e) {
        var cfm = confirm('确定要执行操作吗？');
        if (cfm == true) {
            e.submit();
        }
        e.preventDefault();
    });
    
    document.querySelector('.button-download').addEventListener('click', function(e){
        var url = '/wp-admin/admin-ajax.php?action=vt_download_config'
        window.open(url);
    });

</script>
