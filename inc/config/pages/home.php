<?php
/*
 * 首页调用
 *
 * @author: soushenji <soushenji@qq.com>
 * @link: https://vtheme.cn
 */

defined('ABSPATH') || exit;

$config = vt_get_config();

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
    <div class="home-widget">
        <!-- 幻灯片设置 -->
        <h3 class="title-light">幻灯片</h3>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="_home_options[sliders_is_show]">显示幻灯片模块</label></th>
                    <td>
                        <label>
                            <input name="_home_options[sliders_is_show]" type="radio" 
                                value="1" <?php if ($config['_home_options']['sliders_is_show'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                        <label>
                            <input name="_home_options[sliders_is_show]" type="radio" 
                                value="0" <?php if ($config['_home_options']['sliders_is_show'] == 0) { ?>checked="checked" <?php } ?>>
                            隐藏
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="_home_options[sliders_height]">幻灯片高度</label></th>
                    <td><input type="text" name="_home_options[sliders_height]" id="_home_options[sliders_height]" value="<?php echo $config['_home_options']['sliders_height'] ?>" class="regular-text" placeholder="幻灯片高度"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="slider_img_0">幻灯片</label></th>
                    <td>
                        <div class="slider-list">
                          <?php
                          if($config['_home_options']['sliders']):
                              foreach($config['_home_options']['sliders'] as $k=>$v):
                          ?>
                              <div class="number-item">
                                  <img class="my-img-preview" src="<?php echo $v['pic-url'] ?>" style="display:block;">
                                  <input type="text" name="_home_options[sliders][<?php echo $k ?>][pic-url]" value="<?php echo $v['pic-url'] ?>" class="pic-input" placeholder="图片地址">
                                  <input type="text" name="_home_options[sliders][<?php echo $k ?>][title]" value="<?php echo $v['title'] ?>" class="" placeholder="标题">
                                  <input type="text" name="_home_options[sliders][<?php echo $k ?>][description]" value="<?php echo $v['description'] ?>" class="" placeholder="描述">
                                  <input type="text" name="_home_options[sliders][<?php echo $k ?>][url]" value="<?php echo $v['url'] ?>" class="" placeholder="链接地址">
                                  <div class="action">
                                      <a href="javascript:;" class="upload-slider-button">上传</a>
                                      <a href="javascript:;" class="delete-slider-button">删除</a>
                                  </div>
                              </div>
                          <?php
                              endforeach;
                          endif;
                          ?>
                        </div>
                        <button type="button" class="add-slider-button" data-id="slider-item-template">增加幻灯片</button>
                    </td>
                </tr>
            </tbody>
        </table>


        <!-- 幻灯片设置 -->
        <h3 class="title-light">推荐模块</h3>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="_home_options[hot_is_show]">显示推荐模块</label></th>
                    <td>
                        <label>
                            <input name="_home_options[hot_is_show]" type="radio" 
                                value="1" <?php if ($config['_home_options']['hot_is_show'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                        <label>
                            <input name="_home_options[hot_is_show]" type="radio" 
                                value="0" <?php if ($config['_home_options']['hot_is_show'] == 0) { ?>checked="checked" <?php } ?>>
                            隐藏
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="_home_options[hot_title]">推荐模块标题</label></th>
                    <td>
                        <input type="text" name="_home_options[hot_title]" 
                        value="<?= $config['_home_options']['hot_title']; ?>" 
                        class="regular-text" placeholder="">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="slider_img_0">幻灯片</label></th>
                    <td>
                        <div class="slider-list">
                          <?php
                          if($config['_home_options']['hot']):
                              foreach($config['_home_options']['hot'] as $k=>$v):
                          ?>
                              <div class="number-item">
                                  <img class="my-img-preview" src="<?php echo $v['pic-url'] ?>" style="display:block;">
                                  <input type="text" name="_home_options[hot][<?php echo $k ?>][pic-url]" value="<?php echo $v['pic-url'] ?>" class="pic-input" placeholder="图片地址">
                                  <input type="text" name="_home_options[hot][<?php echo $k ?>][title]" value="<?php echo $v['title'] ?>" class="" placeholder="标题">
                                  <input type="text" name="_home_options[hot][<?php echo $k ?>][url]" value="<?php echo $v['url'] ?>" class="" placeholder="链接地址">
                                  <div class="action">
                                      <a href="javascript:;" class="upload-slider-button">上传</a>
                                      <a href="javascript:;" class="delete-slider-button">删除</a>
                                  </div>
                              </div>
                          <?php
                              endforeach;
                          endif;
                          ?>
                        </div>
                        <button type="button" class="add-slider-button" data-id="hot-item-template">增加幻灯片</button>
                    </td>
                </tr>
            </tbody>
        </table>



        <!-- 最新文章模块 -->
        <h3 class="title-light">最新文章</h3>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="_home_options[articles_is_show]">显示最新文章模块</label></th>
                    <td>
                        <label>
                            <input name="_home_options[articles_is_show]" type="radio" 
                                value="1" <?php if ($config['_home_options']['articles_is_show'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                        <label>
                            <input name="_home_options[articles_is_show]" type="radio" 
                                value="0" <?php if ($config['_home_options']['articles_is_show'] == 0) { ?>checked="checked" <?php } ?>>
                            隐藏
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="_home_options[artilces_title]">文章模块标题</label></th>
                    <td>
                        <input type="text" name="_home_options[artilces_title]" id="_home_options[artilces_title]" 
                        value="<?php echo $config['_home_options']['artilces_title']; ?>" 
                        class="regular-text" placeholder="">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="artilces_not_in_ids">要排除的分类</label></th>
                    <td>
                        <input type="text" name="_home_options[artilces_not_in_ids]" 
                            id="_home_options[artilces_not_in_ids]"
                            value="<?php echo $config['_home_options']['artilces_not_in_ids']; ?>" 
                            class="regular-text" placeholder="">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="_home_options[articles_auto_load]">自动加载</label></th>
                    <td>
                        <label>
                            <input name="_home_options[articles_auto_load]" type="radio" 
                                value="1" <?php if ($config['_home_options']['articles_auto_load'] == 1) { ?>checked="checked" <?php } ?>>
                            开启
                        </label>
                        <label>
                            <input name="_home_options[articles_auto_load]" type="radio" 
                                value="0" <?php if ($config['_home_options']['articles_auto_load'] == 0) { ?>checked="checked" <?php } ?>>
                            关闭
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="articles_auto_limit">自动加载次数</label></th>
                    <td>
                        <input type="text" name="_home_options[articles_auto_limit]" 
                            id="_home_options[articles_auto_limit]"
                            value="<?php echo $config['_home_options']['articles_auto_limit']; ?>" 
                            class="regular-text" placeholder="">
                        <p class="description" id="tagline-description">自动加载到第几页，0代表无限加载</p>
                    </td>
                </tr>
            </tbody>
        </table>


        <!-- 搜索模块 -->
        <h3 class="title-light hide">搜索模块</h3>
        <table class="form-table hide" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="list_cards_num">搜索条主标题</label></th>
                    <td><input type="text" name="list_cards_num" id="list_cards_num" value="<?php echo ($config['list_cards_num']); ?>" class="regular-text" placeholder=""></td>
                </tr>
                <tr>
                    <th scope="row"><label for="list_cards_num">搜索条描述</label></th>
                    <td><input type="text" name="list_cards_num" id="list_cards_num" value="<?php echo ($config['list_cards_num']); ?>" class="regular-text" placeholder=""></td>
                </tr>
                <tr>
                    <th scope="row"><label for="meta_description">搜索条背景图片</label></th>
                    <td>
                        <div>
                            <?php if ($config['site_logo']) { ?>
                                <img class="my-img-preview" src="<?php echo $config['site_logo']; ?>" style="display:block;">
                            <?php } else { ?>
                                <img class="my-img-preview" src="" style="display:none;">
                            <?php } ?>
                            <input type="text" name="site_logo" id="site_logo" value="<?php echo ($config['site_logo']); ?>" class="regular-text image-input" />
                            <button type="button" class="upload-button">上传</button>
                            <button type="button" class="delete-button">删除</button>
                            <p class="description" id="tagline-description">图片尺寸 180*50</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 友情链接 -->
        <h3 class="title-light">友情链接</h3>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="_home_options[links_is_show]">显示友情链接模块</label></th>
                    <td>
                        <label>
                            <input name="_home_options[links_is_show]" type="radio" 
                                value="1" <?php if ($config['_home_options']['links_is_show'] == 1) { ?>checked="checked" <?php } ?>>
                            显示
                        </label>
                        <label>
                            <input name="_home_options[links_is_show]" type="radio" 
                                value="0" <?php if ($config['_home_options']['links_is_show'] == 0) { ?>checked="checked" <?php } ?>>
                            隐藏
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>
        
    </div>
    <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('vt_options_update') ?>">
    <input type="hidden" name="_action" value="vt_options_update">
    <input type="submit" name="input_save" value="保存" class="button button-primary" />
</form>


<!-- 幻灯片模块模板 -->
<div style="display: none;" id="slider-item-template">
    <div class="number-item">
        <img class="my-img-preview" src="" style="display:none;">
        <input type="text" name="_home_options[sliders][0][pic-url]" value="" class="pic-input" placeholder="图片地址">
        <input type="text" name="_home_options[sliders][0][title]" value="" class="" placeholder="标题">
        <input type="text" name="_home_options[sliders][0][description]" value="" class="" placeholder="描述">
        <input type="text" name="_home_options[sliders][0][url]" value="" class="" placeholder="链接地址">
        <div class="action">
            <a href="javascript:;" class="upload-slider-button">上传</a>
            <a href="javascript:;" class="delete-slider-button">删除</a>
        </div>
    </div>
</div>

<div style="display: none;" id="hot-item-template">
    <div class="number-item">
        <img class="my-img-preview" src="" style="display:none;">
        <input type="text" name="_home_options[hot][0][pic-url]" value="" class="pic-input" placeholder="图片地址">
        <input type="text" name="_home_options[hot][0][title]" value="" class="" placeholder="标题">
        <input type="text" name="_home_options[hot][0][url]" value="" class="" placeholder="链接地址">
        <div class="action">
            <a href="javascript:;" class="upload-slider-button">上传</a>
            <a href="javascript:;" class="delete-slider-button">删除</a>
        </div>
    </div>
</div>

