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
                <th scope="row"><label for="basic_style">主题色调</label></th>
                <td>
                    <div class="style-color-container">
                        <label class="color-label">
                            <div class="color-show" style="background-color:#007aff;"></div>
                            <input name="basic_style" type="radio" value="1" <?php if ($vt_config['basic_style'] == 1) { ?>checked="checked" <?php } ?>>
                        </label>
                        <label class="color-label">
                            <div class="color-show" style="background-color: #dd524d;"></div>
                            <input name="basic_style" type="radio" value="2" <?php if ($vt_config['basic_style'] == 2) { ?>checked="checked" <?php } ?>>
                        </label>
                        <label class="color-label">
                            <div class="color-show" style="background-color: #535353;"></div>
                            <input name="basic_style" type="radio" value="3" <?php if ($vt_config['basic_style'] == 3) { ?>checked="checked" <?php } ?>>
                        </label>
                        <label class="color-label">
                            <div class="color-show" style="background-color: #62ad4c;"></div>
                            <input name="basic_style" type="radio" value="4" <?php if ($vt_config['basic_style'] == 4) { ?>checked="checked" <?php } ?>>
                        </label>
                        <label class="color-label">
                            <input type="text" name="basic_style_color" value="<?php echo $vt_config['basic_style_color'] ?>" class="regular-text" placeholder="自定义颜色">
                            <input name="basic_style" type="radio" value="0" <?php if ($vt_config['basic_style'] == 0) { ?>checked="checked" <?php } ?>>
                        </label>
                    </div>
                    <p class="description">选择网站的主色调</p>
                </td>
            </tr>
            <style type="text/css">
            input[name="basic_style_color"]{
                background-color: <?php echo $vt_config['basic_style_color'] ?>
            }
            </style>

            <!-- tr>
                <th scope="row"><label for="is_show_sql_counter">显示数据库查询次数</label></th>
                <td>
                    <p>
                        <label>
                            <input name="is_show_sql_counter" type="radio" value="1" <?php if ($vt_config['is_show_sql_counter'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                        <label>
                            <input name="is_show_sql_counter" type="radio" value="0" <?php if ($vt_config['is_show_sql_counter'] == 0) { ?>checked="checked" <?php } ?>>
                            不显示
                        </label>
                    </p>
                </td>
            </tr -->
            
            <tr>
                <th scope="row"><label for="update_is_on">更新提示</label></th>
                <td>
                    <p>
                        <label>
                            <input name="update_is_on" type="radio" value="1" <?php if ($vt_config['update_is_on'] == 1) { ?>checked="checked" <?php } ?>>
                            开启
                        </label>
                        <label>
                            <input name="update_is_on" type="radio" value="0" <?php if ($vt_config['update_is_on'] == 0) { ?>checked="checked" <?php } ?>>
                            关闭
                        </label>
                    </p>
                    <p class="description">关闭以后，不提示PHP版本，主题和插件更新提示</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="editor_type">编辑器</label></th>
                <td>
                    <p>
                        <label>
                            <input name="editor_type" type="radio" value="0" <?php if ($vt_config['editor_type'] == 0) { ?>checked="checked" <?php } ?>>
                            古腾堡编辑器
                        </label>
                        <label>
                            <input name="editor_type" type="radio" value="1" <?php if ($vt_config['editor_type'] == 1) { ?>checked="checked" <?php } ?>>
                            经典编辑器
                        </label>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="editor_revision">文章修订版本</label></th>
                <td>
                    <p>
                        <label>
                            <input name="editor_revision" type="radio" value="0" <?php if ($vt_config['editor_revision'] == 0) { ?>checked="checked" <?php } ?>>
                            关闭
                        </label>
                        <label>
                            <input name="editor_revision" type="radio" value="1" <?php if ($vt_config['editor_revision'] == 1) { ?>checked="checked" <?php } ?>>
                            开启
                        </label>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="widget_title_type">边框标题样式</label></th>
                <td>
                    <div>
                        <label class="color-label">
                            <input name="widget_title_type" type="radio" value="0" <?php if ($vt_config['widget_title_type'] == 0) { ?>checked="checked" <?php } ?>>
                            默认
                        </label>
                        <label class="color-label">
                            <input name="widget_title_type" type="radio" value="1" <?php if ($vt_config['widget_title_type'] == 1) { ?>checked="checked" <?php } ?>>
                            竖线
                        </label>
                        <label class="color-label">
                            <input name="widget_title_type" type="radio" value="2" <?php if ($vt_config['widget_title_type'] == 2) { ?>checked="checked" <?php } ?>>
                            圆点
                        </label>
                        <label class="color-label">
                            <input name="widget_title_type" type="radio" value="3" <?php if ($vt_config['widget_title_type'] == 3) { ?>checked="checked" <?php } ?>>
                            横线
                        </label>
                    </div>
                </td>
            </tr>
            
            
            <tr>
                <th scope="row"><label for="page_data_type">显示页面参数</label></th>
                <td>
                    <div>
                        <label class="color-label">
                            <input name="page_data_type" type="radio" value="0" <?php if ($vt_config['page_data_type'] == 0) { ?>checked="checked" <?php } ?>>
                            不显示
                        </label>
                        <label class="color-label">
                            <input name="page_data_type" type="radio" value="1" <?php if ($vt_config['page_data_type'] == 1) { ?>checked="checked" <?php } ?>>
                            显示查询次数
                        </label>
                        <label class="color-label">
                            <input name="page_data_type" type="radio" value="2" <?php if ($vt_config['page_data_type'] == 2) { ?>checked="checked" <?php } ?>>
                            显示查询次数和页面执行时间
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="page_data_type">默认文章列表排版</label></th>
                <td>
                    <div style="display:flex">
                        <label class="color-label">
                            <p><img src="<?php echo bloginfo('template_url'); ?>/inc/config/images/list-type-0.jpg"></p>
                            <input name="list_type" type="radio" value="1" <?php if ($vt_config['list_type'] == 1) { ?>checked="checked" <?php } ?>>
                            启用
                        </label>
                        <label class="color-label">
                            <p><img src="<?php echo bloginfo('template_url'); ?>/inc/config/images/list-type-1.jpg"></p>
                            <input name="list_type" type="radio" value="2" <?php if ($vt_config['list_type'] == 2) { ?>checked="checked" <?php } ?>>
                            启用
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="list_cards_col">每行列数</label></th>
                <td>
                    <select name="list_cards_col" id="list_cards_col">
                        <option value="3" <?php if($vt_config['list_cards_col']==3) echo 'selected'; ?>>三列</option>
                        <option value="4" <?php if($vt_config['list_cards_col']==4) echo 'selected'; ?>>四列</option>
                        <option value="5" <?php if($vt_config['list_cards_col']==5) echo 'selected'; ?>>五列</option>
                        <option value="6" <?php if($vt_config['list_cards_col']==6) echo 'selected'; ?>>六列</option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="show_admin_bar">顶部工具栏</label></th>
                <td>
                    <div>
                        <label class="color-label">
                            <input name="show_admin_bar" type="radio" value="0" <?php if ($vt_config['show_admin_bar'] == 0) { ?>checked="checked" <?php } ?>>
                            不显示
                        </label>
                        <label class="color-label">
                            <input name="show_admin_bar" type="radio" value="1" <?php if ($vt_config['show_admin_bar'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="show_copyright">文章版权声明</label></th>
                <td>
                    <div>
                        <label class="color-label">
                            <input name="show_copyright" type="radio" value="0" <?php if ($vt_config['show_copyright'] == 0) { ?>checked="checked" <?php } ?>>
                            不显示
                        </label>
                        <label class="color-label">
                            <input name="show_copyright" type="radio" value="1" <?php if ($vt_config['show_copyright'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                    </div>
                    <br>
                    <?php $vt_config['show_copyright_text'] = $vt_config['show_copyright_text'] ? $vt_config['show_copyright_text'] : '声明：本站所有文章，如无特殊说明或标注，均为演示数据。'?>
                    <textarea name="show_copyright_text" id="show_copyright_text" class="regular-text" rows="3" placeholder="请输入文章版权声明"><?php echo $vt_config['show_copyright_text']?></textarea>
                </td>
            </tr>

            <tr>
                <th scope="row">卡片图片高宽比</th>
                <td>
                    <input type="text" name="image_items_height" id="image_items_height" value="<?php echo ($vt_config['image_items_height']); ?>" class="regular-text" placeholder="默认100%">
                    <p>填入图片高宽比</p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="default_image">默认图片</label></th>
                <td>
                    <div>
                        <?php if ($vt_config['default_image']) { ?>
                            <img class="my-img-preview" src="<?php echo $vt_config['default_image']; ?>" style="display:block;">
                        <?php } else { ?>
                            <img class="my-img-preview" src="" style="display:none;">
                        <?php } ?>
                        <input type="text" name="default_image" id="default_image" value="<?php echo ($vt_config['default_image']); ?>" class="regular-text image-input" />
                        <button type="button" class="upload-button">上传</button>
                        <button type="button" class="delete-button">删除</button>
                        <p class="description" id="tagline-description">推荐图片尺寸 300*300 或 500*500,没有图片时默认显示这个图片</p>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="background_image">背景图片</label></th>
                <td>
                    <div>
                        <?php if ($vt_config['background_image']) { ?>
                            <img class="my-img-preview" src="<?php echo $vt_config['background_image']; ?>" style="display:block;">
                        <?php } else { ?>
                            <img class="my-img-preview" src="" style="display:none;">
                        <?php } ?>
                        <input type="text" name="background_image" id="background_image" value="<?php echo ($vt_config['background_image']); ?>" class="regular-text image-input" />
                        <button type="button" class="upload-button">上传</button>
                        <button type="button" class="delete-button">删除</button>
                        <p class="description" id="tagline-description">推荐图片尺寸 1920*1080</p>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="qrcode_image">收款二维码</label></th>
                <td>
                    <div>
                        <?php if ($vt_config['qrcode_image']) { ?>
                            <img class="my-img-preview" src="<?php echo $vt_config['qrcode_image']; ?>" style="display:block;">
                        <?php } else { ?>
                            <img class="my-img-preview" src="" style="display:none;">
                        <?php } ?>
                        <input type="text" name="qrcode_image" id="qrcode_image" value="<?php echo ($vt_config['qrcode_image']); ?>" class="regular-text image-input" />
                        <button type="button" class="upload-button">上传</button>
                        <button type="button" class="delete-button">删除</button>
                        <p class="description" id="tagline-description">推荐图片尺寸 300*300 或 500*500</p>
                    </div>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="dark_mode_type">暗黑模式</label></th>
                <td>
                    <div>
                        <label class="color-label">
                            <input name="dark_mode_type" type="radio" value="0" <?php if ($vt_config['dark_mode_type'] == 0) { ?>checked="checked" <?php } ?>>
                            禁用
                        </label>
                        <label class="color-label">
                            <input name="dark_mode_type" type="radio" value="1" <?php if ($vt_config['dark_mode_type'] == 1) { ?>checked="checked" <?php } ?>>
                            手动
                        </label>
                        <label class="color-label">
                            <input name="dark_mode_type" type="radio" value="2" <?php if ($vt_config['dark_mode_type'] == 2) { ?>checked="checked" <?php } ?>>
                            跟随系统
                        </label>
                        <!-- <label class="color-label">
                            <input name="dark_mode_type" type="radio" value="3" <?php if ($vt_config['dark_mode_type'] == 3) { ?>checked="checked" <?php } ?>>
                            跟随客户端时间
                        </label> -->
                    </div>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="is_show_login_register">显示登录按钮</label></th>
                <td>
                    <p>
                        <label>
                            <input name="is_show_login_register" type="radio" value="1" <?php if ($vt_config['is_show_login_register'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                        <label>
                            <input name="is_show_login_register" type="radio" value="0" <?php if ($vt_config['is_show_login_register'] == 0) { ?>checked="checked" <?php } ?>>
                            不显示
                        </label>
                    </p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="user_center_is_on">个人中心</label></th>
                <td>
                    <p>
                        <label>
                            <input name="user_center_is_on" type="radio" value="1" <?php if ($vt_config['user_center_is_on'] == 1) { ?>checked="checked" <?php } ?>>
                            开启
                        </label>
                        <label>
                            <input name="user_center_is_on" type="radio" value="0" <?php if ($vt_config['user_center_is_on'] == 0) { ?>checked="checked" <?php } ?>>
                            关闭
                        </label>
                    </p>
                    <p class="description"></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="highlight_is_on">代码高亮</label></th>
                <td>
                    <p>
                        <label>
                            <input name="highlight_is_on" type="radio" value="1" <?php if ($vt_config['highlight_is_on'] == 1) { ?>checked="checked" <?php } ?>>
                            开启
                        </label>
                        <label>
                            <input name="highlight_is_on" type="radio" value="0" <?php if ($vt_config['highlight_is_on'] == 0) { ?>checked="checked" <?php } ?>>
                            关闭
                        </label>
                    </p>
                    <p class="description"></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="comments_is_on">开启评论</label></th>
                <td>
                    <p>
                        <label>
                            <input name="comments_is_on" type="radio" value="1" <?php if ($vt_config['comments_is_on'] == 1) { ?>checked="checked" <?php } ?>>
                            开启
                        </label>
                        <label>
                            <input name="comments_is_on" type="radio" value="0" <?php if ($vt_config['comments_is_on'] == 0) { ?>checked="checked" <?php } ?>>
                            关闭
                        </label>
                    </p>
                </td>
            </tr>
            
        </tbody>
    </table>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="input_save" value="保存" class="button button-primary" />
</form>


