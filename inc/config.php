<?php
require_once THEME_DIR .'/inc/codestar/codestar-framework.php';


$attempts_text = "";

if(is_admin() && isset($_GET['page']) && $_GET['page']=="mirage" ){
    // 统计登录失败的次数
    $attempts = get_option('vt_failed_attempts', []);
    $ip_counter = sizeof($attempts);
    $attempt_counter = 0;
    foreach ($attempts as $k => $v) {
        $attempt_counter = $attempt_counter + $v['counter'];
    }
    $attempts_text = "一个小时内有".$ip_counter."个IP，".$attempt_counter."次登录失败";
}


// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {
    $prefix = THEME_OPTION_NAME;

    CSF::createOptions( $prefix, array(
        'menu_title' => __('Mirage settings','vt'),
        'menu_slug'  => 'mirage',
        'menu_position' => '81',
        'framework_title' => __('Mirage settings','vt'),
        'footer_text' => '',
        'theme' => 'light',
        'show_bar_menu' => false,
        'footer_text' => '<img src="https://vtheme.cn/static/images/logo.png" style="width:20px;position:relative;top:5px"> Theme by <a href="https://vtheme.cn/mirage" copyright target="_blank">Mirage</a>'
    ));

}
    /**
     * 常规设置
     */
    CSF::createSection( $prefix, array(
        'title'  => __('General settings','vt'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(
            array(
                'id'    => 'basic_style',
                'type'  => 'color',
                'title' => __('Theme color tone','vt'),
                'default' => '#007aff'
            ),
            array(
                'id'         => 'update_is_on',
                'type'       => 'radio',
                'title'      => __('Update prompt','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '0',
                'inline' => true,
                'desc' => __('After closing, there will be no prompts for PHP version, theme, and plugin updates','vt')
            ),
            array(
                'id'         => 'page_data_type',
                'type'       => 'radio',
                'title'      => __('Display page parameters','vt'),
                'options'    => array('0' => __('Hide','vt'), '1' => __('Display','vt'), '2'=>'console.log'),
                'default'    => '0',
                'inline' => true
            ),
            array(
                'id'         => 'editor_type',
                'type'       => 'radio',
                'title'      => __('Editor','vt'),
                'options'    => array( '0' => __('Classic Editor','vt'), '1' => __('Gutenberg Editor','vt') ),
                'default'    => '1',
                'inline' => true
            ),
            array(
                'id'         => 'dark_mode_type',
                'type'       => 'radio',
                'title'      => __('Dark mode','vt'),
                'options'    => array(
                    '1' => __('On','vt'),
                    '0' => __('Off','vt'),
                    '2' => __('Follow the system','vt'),
                ),
                'default'    => '1',
                'inline' => true
            ),
            array(
                'id'    => 'default_image',
                'type'  => 'upload',
                'title' => __('Default Image','vt'),
                'default' => get_template_directory_uri() . '/assets/images/default.jpg',
                'preview' => true
            ),
            array(
                'id'    => 'background_image',
                'type'  => 'upload',
                'title' => __('Background image','vt'),
                'default' => '',
                'preview' => true
            ),
            array(
                'id'      => 'lazy_load_enabled',
                'type'    => 'switcher',
                'title'   => __('Lazy Loading','vt'),
                'default' => true
            ),
            array(
                'id'         => 'attempts_is_on',
                'type'       => 'radio',
                'title'      => __('Anti brute force cracking','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '1',
                'desc'       => $attempts_text,
                'inline'     => true
            )
        )
    ));

    
    /**
     * SEO设置
     */
    CSF::createSection( $prefix, array(
        'title'  => __('SEO settings','vt'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(
            array(
                'id'    => 'keywords',
                'type'  => 'text',
                'title' => __('Keywords','vt')
            ),
            array(
                'id'    => 'description',
                'type'  => 'textarea',
                'title' => __('Description','vt')
            ),
        )
    ));


    /**
     * 首页设置
     */
    CSF::createSection( $prefix, array(
        'id' => 'home_fields',
        'title' => __('Home page settings','vt'),
        'icon' => 'dashicons dashicons-admin-home'
    ));

    /**
     * 首页设置 - 布局设置
     */
    CSF::createSection($prefix, array(
        'parent'      => 'home_fields',
        'title'       => __('Layout setting','vt'),
        // 'icon'        => 'fa fa-home',
        'description' => '',
        'fields'      => array(
            array(
                'id'             => 'home_layout',
                'type'           => 'sorter',
                'title'          => '',
                'enabled_title'  => __('Display','vt'),
                'disabled_title' => __('Hide','vt'),
                'default'        => array(
                    'enabled'  => array(
                        'slider'   => __('Sliders','vt'),
                        'last'     => __('Latest','vt')
                    ),
                    'disabled' => array()
                ),
            ),

        ),
    ));


    /**
     * 首页设置 - 幻灯片模块
     */
    CSF::createSection($prefix, array(
        'parent'      => 'home_fields',
        'title'       => __('Sliders','vt'),
        // 'icon'        => 'fa fa-home',
        'description' => '',
        'fields'      => array(
            array(
                'id'      => 'slider_autoplay',
                'type'    => 'switcher',
                'title'   => __('Autoplay','vt'),
                'label'   => '',
                'default' => true,
            ),
            array(
                'id'      => 'slider_height',
                'type'    => 'text',
                'title'   => __('Slide height','vt'),
                'default' => '380'
            ),
            array(
                'id'         => 'slider_items',
                'type'       => 'group',
                'title'      => __('Slide list','vt'),
                'fields'     => array(
                    array(
                        'id'      => 'title',
                        'type'    => 'text',
                        'title'   => __('Title','vt')
                    ),
                    array(
                        'id'          => 'image',
                        'type'        => 'upload',
                        'title'       => __('Upload','vt'),
                        'library'     => 'image',
                        'placeholder' => 'http://',
                        'preview'     => true
                    ),
                    array(
                        'id'      => 'target',
                        'type'    => 'switcher',
                        'title'   => __('New window opens link','vt'),
                        'label'   => ''
                    ),
                    array(
                        'id'      => 'link',
                        'type'    => 'text',
                        'title'   => __('Link address','vt')
                    ),
                ),
                'default' => array(
                                array(
                                    'title' => __('Hello Mirage','vt'),
                                    'image' => get_template_directory_uri() . '/assets/images/slider1.jpg'
                                ),
                                array(
                                    'title' => __('Hello Mirage','vt'),
                                    'image' => get_template_directory_uri() . '/assets/images/slider2.jpg'
                                ),
                                array(
                                    'title' => __('Hello Mirage','vt'),
                                    'image' => get_template_directory_uri() . '/assets/images/slider3.jpg'
                                ),
                            )
            ),
            array(
                'id'         => 'top_items',
                'type'       => 'group',
                'title'      => __('Recommendation','vt'),
                'fields'     => array(
                    array(
                        'id'      => 'title',
                        'type'    => 'text',
                        'title'   => __('Title','vt')
                    ),
                    array(
                        'id'          => 'image',
                        'type'        => 'upload',
                        'title'       => __('Upload','vt'),
                        'library'     => 'image',
                        'placeholder' => 'http://',
                        'preview'     => true
                    ),
                    array(
                        'id'      => 'target',
                        'type'    => 'switcher',
                        'title'   => __('New window opens link','vt'),
                        'label'   => ''
                    ),
                    array(
                        'id'      => 'link',
                        'type'    => 'text',
                        'title'   => __('Link address','vt')
                    ),
                ),
                'default' => array(
                                array(
                                    'title' => __('Hello Mirage','vt'),
                                    'image' => get_template_directory_uri() . '/assets/images/hot.jpg'
                                ),
                                array(
                                    'title' => __('Hello Mirage','vt'),
                                    'image' => get_template_directory_uri() . '/assets/images/hot.jpg'
                                )
                            )
            )

        )
    ));


    /**
     * 首页设置 - 最新文章模块
     */
    CSF::createSection($prefix, array(
        'parent'      => 'home_fields',
        'title'       => __('Latest','vt'),
        'description' => '',
        'fields'      => array(
            array(
                'id'    => 'hot_is_show',
                'type'  => 'radio',
                'title' => __('Recommended module','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '1',
                'inline' => true
            ),
            array(
                'id'      => 'hot_title',
                'type'    => 'text',
                'title'   => __('Hot topics','vt'),
                'default' => __('Hot topics','vt'),
                'dependency' => array('hot_is_show', '==', '1'),
            ),
            array(
                'id'         => 'hot_items',
                'type'       => 'group',
                'title'      => __('Recommended List','vt'),
                'dependency' => array('hot_is_show', '==', '1'),
                'fields'     => array(
                    array(
                        'id'      => 'title',
                        'type'    => 'text',
                        'title'   => __('title','vt')
                    ),
                    array(
                        'id'          => 'image',
                        'type'        => 'upload',
                        'title'       => __('upload','vt'),
                        'library'     => 'image',
                        'placeholder' => 'http://',
                        'preview'     => true
                    ),
                    array(
                        'id'      => 'target',
                        'type'    => 'switcher',
                        'title'   => __('new window opens link','vt'),
                        'label'   => ''
                    ),
                    array(
                        'id'      => 'link',
                        'type'    => 'text',
                        'title'   => __('link address','vt')
                    ),
                ),
                'default' => array(
                                array(
                                    'title' => __('Hot topics','vt').' 1',
                                    'image' => get_template_directory_uri() . '/assets/images/hot.jpg'
                                ),
                                array(
                                    'title' => __('Hot topics','vt').' 2',
                                    'image' => get_template_directory_uri() . '/assets/images/hot.jpg'
                                ),
                                array(
                                    'title' => __('Hot topics','vt').' 3',
                                    'image' => get_template_directory_uri() . '/assets/images/hot.jpg'
                                ),
                                array(
                                    'title' => __('Hot topics','vt').' 4',
                                    'image' => get_template_directory_uri() . '/assets/images/hot.jpg'
                                )
                            )
            ),


            array(
                'id'      => 'posts_ids',
                'type'    => 'select',
                'title'   => __('Category','vt'),
                'chosen'      => true,
                'multiple'    => true,
                'options'     => 'categories'
            ),
            array(
                'id'      => 'posts_not_in_ids',
                'type'    => 'select',
                'title'   => __('Exclude classification','vt'),
                'chosen'      => true,
                'multiple'    => true,
                'options'     => 'categories'
            ),
            array(
                'id'    => 'posts_auto_load',
                'type'  => 'radio',
                'title' => __('Auto Load','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '1',
                'inline' => true,
                'dependency' => array('posts_nav', '==', '1'),

            ),
            array(
                'id'    => 'posts_auto_load_limit',
                'type'  => 'text',
                'title' => __('Automatic loading times','vt'),
                'default' => '1',
                'desc' => __('Automatically load to which page, 0 means unlimited loading','vt'),
                'dependency' => array('posts_nav', '==', '1')
            ),
            array(
                'id'      => 'full_width',
                'type'    => 'switcher',
                'title'   => __('Full width','vt'),
                'label'   => '',
                'default' => false,
            ),
            array(
                'id'    => 'list_type',
                'type'  => 'radio',
                'title' => __('Layout','vt'),
                'options'    => array('0' => __('List','vt'), '1' => __('Card','vt')),
                'default'    => '0',
                'inline' => true
            ),
        )
    ));


    /**
     * 顶部设置
     */
    CSF::createSection( $prefix, array(
        'title'  => __('Header settings','vt'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(
            // array(
            //     'id'         => 'header_type',
            //     'type'       => 'radio',
            //     'title'      => __('显示类型','vt'),
            //     'options'    => array('0' => __('简单','vt'), '1' => __('全部','vt')),
            //     'default'    => '0',
            //     'inline' => true
            // ),
            array(
                'id'    => 'site_logo',
                'type'  => 'upload',
                'title' => __('Website Logo','vt'),
                'default' => get_template_directory_uri() . '/assets/images/logo.png',
                'preview' => true
            ),
            array(
                'id'    => 'site_logo_darkness',
                'type'  => 'upload',
                'title' => __('Website Logo (Dark Mode)','vt'),
                'default' => get_template_directory_uri() . '/assets/images/logo-darkness.png',
                'preview' => true
            ),
            array(
                'id'         => 'show_admin_bar',
                'type'       => 'radio',
                'title'      => __('Top Toolbar','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '0',
                'inline' => true
            ),
        )
    ));

    /**
     * 底部设置
     */
    CSF::createSection( $prefix, array(
        'title'  => __('Footer setting','vt'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(
            array(
                'id'    => 'footer_bg_type',
                'type'  => 'radio',
                'title' => __('Background color','vt'),
                'options' => array('light' => __('light','vt'), 'darkness' => __('dark','vt')),
                'default' => 'light',
                'inline' => true
            ),
            array(
                'id'    => 'footer_copyright',
                'type'  => 'textarea',
                'title' => __('Footer copyright','vt'),
                'default' => '&copy; '.date('Y').' <a href="http://www.vtheme.cn">VTHEME.cn</a> All Rights Reserved.'
            ),
            array(
                'id'       => 'footer_js',
                'type'     => 'code_editor',
                'title'    => __("Customize JS code",'vt'),
                'subtitle' => '',
                // 'settings' => array(
                //     'theme' => 'dracula',
                //     'mode'  => 'javascript',
                // ),
                'sanitize' => false,
                'default'  => '',
            ),
            array(
                'id'         => 'sns_items',
                'type'       => 'group',
                'title'      => __('SNS links','vt'),
                'fields'     => array(
                    array(
                        'id'      => 'title',
                        'type'    => 'text',
                        'title'   => __('Title','vt')
                    ),
                    array(
                        'id'          => 'image',
                        'type'        => 'upload',
                        'title'       => __('Upload','vt'),
                        'library'     => 'image',
                        'placeholder' => 'http://',
                        'preview'     => true
                    ),
                    array(
                        'id'      => 'link',
                        'type'    => 'text',
                        'title'   => __('Link','vt'),
                        'default' => '',
                    )
                ),
                'default' => array(
                                array(
                                    'title' => 'Gitee',
                                    'image' => get_template_directory_uri() . '/assets/images/sns-gitee.png',
                                    'link' => 'https://gitee.com/'
                                ),
                                array(
                                    'title' => 'Github',
                                    'image' => get_template_directory_uri() . '/assets/images/sns-github.png',
                                    'link' => 'https://github.com/'
                                ),
                                array(
                                    'title' => '抖音',
                                    'image' => get_template_directory_uri() . '/assets/images/sns-douyin.png',
                                    'link' => 'https://douyin.com/'
                                ),
                                array(
                                    'title' => 'Bilibili',
                                    'image' => get_template_directory_uri() . '/assets/images/sns-bilibili.png',
                                    'link' => 'https://bilibili.com/'
                                ),
                                array(
                                    'title' => 'Weibo',
                                    'image' => get_template_directory_uri() . '/assets/images/sns-weibo.png',
                                    'link' => 'https://weibo.com/'
                                )
                            )
            ),
            array(
                'id'         => 'footer_links',
                'type'       => 'group',
                'title'      => __('Links','vt'),
                'fields'     => array(
                    array(
                        'id'      => 'title',
                        'type'    => 'text',
                        'title'   => __('Title','vt')
                    ),
                    array(
                        'id'      => 'link',
                        'type'    => 'text',
                        'title'   => __('Link','vt'),
                        'default' => '',
                    )
                ),
                'default' => array(
                                array( 'title' => 'VTHEME', 'link' => 'https://vtheme.cn/'),
                                array( 'title' => 'Mirage theme', 'link' => 'https://vtheme.cn/mirage'),
                                array( 'title' => 'WordPress', 'link' => 'https://wordpress.org/'),
                                array( 'title' => 'WordPress CN', 'link' => 'https://cn.wordpress.org/'),
                                array( 'title' => 'About', 'link' => home_url()),
                            )
            ),
        )
    ));


    /**
     * 文章设置
     */
    CSF::createSection( $prefix, array(
        'title'  => __('Article settings','vt'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(
            array(
                'id'         => 'vt_columns',
                'type'       => 'radio',
                'title'      => __('Number of rows and columns','vt'),
                'options'    => array(
                    '2' => __('2','vt'),
                    '3' => __('3','vt'),
                    '4' => __('4','vt'),
                    '5' => __('5','vt')
                ),
                'default'    => '3',
                'inline' => true
            ),
            array(
                'id'         => 'editor_revision',
                'type'       => 'radio',
                'title'      => __('Article revision version','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '0',
                'inline' => true
            ),
            array(
                'id'         => 'show_copyright',
                'type'       => 'switcher',
                'title'      => __('Article copyright statement','vt'),
                'default'    => true
            ),
            array(
                'id'         => 'show_copyright_text',
                'type'       => 'textarea',
                'title'      => __('Article copyright statement','vt'),
                'dependency' => array('show_copyright', '==', 'true'),
                'default'    => __('Article copyright statement','vt')
            ),
            array(
                'id'         => 'highlight_is_on',
                'type'       => 'radio',
                'title'      => __('Code highlighting','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '1',
                'inline' => true
            ),
            array(
                'id'         => 'toc_is_on',
                'type'       => 'radio',
                'title'      => __('Generate table of contents','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '1',
                'inline'     => true,
                'desc'       => ''
            ),
            array(
                'id'    => 'image_items_height',
                'type'  => 'slider',
                'title' => __('Image aspect ratio','vt'),
                'unit'  => '%',
                'max'   => '300',
                'default' => '100'
            ),
            array(
                'id'         => 'qrcode_items',
                'type'       => 'group',
                'title'      => __('Payment QR code','vt'),
                'fields'     => array(
                    array(
                        'id'      => 'title',
                        'type'    => 'text',
                        'title'   => __('标题','vt'),
                    ),
                    array(
                        'id'          => 'image',
                        'type'        => 'upload',
                        'title'       => __('上传','vt'),
                        'library'     => 'image',
                        'placeholder' => 'http://',
                        'preview'     => true
                    ),

                ),
                'default' => array(
                                array(
                                    'title' => __('扫描二维码','vt'),
                                    'image' => get_template_directory_uri() . '/assets/images/qrcode.jpg'
                                ),
                                array(
                                    'title' => __('扫描二维码','vt'),
                                    'image' => get_template_directory_uri() . '/assets/images/qrcode.jpg'
                                )
                            )
            )
            
        )   
    ));


    /**
     * 邮箱设置
     */
    CSF::createSection( $prefix, array(
        'title'  => __('Email settings','vt'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(
            array(
                'id'    => 'smtp_is_on',
                'type'  => 'radio',
                'title' => __('Customize email','vt'),
                'options'    => array('1' => __('On','vt'), '0' => __('Off','vt')),
                'default'    => '1',
                'inline' => true,
                'desc' => ""

            ),
            array(
                'id'    => 'smtp_host',
                'type'  => 'text',
                'title' => __('SMTP address','vt'),
            ),
            array(
                'id'    => 'smtp_port',
                'type'  => 'text',
                'title' => __('SMTP port','vt'),
                'default' => '465'
            ),
            array(
                'id'    => 'smtp_username',
                'type'  => 'text',
                'title' => __('SMTP account','vt'),
            ),
            array(
                'id'    => 'smtp_password',
                'type'  => 'text',
                'title' => __('SMTP password','vt'),
            ),
            array(
                'id'    => 'smtp_nicename',
                'type'  => 'text',
                'title' => __('Sender nickname','vt'),
            )
        )
    ));


    /**
     * 广告设置
     */
    CSF::createSection('mirage', [
        'title'  => __('Ad settings','vt'),
        'icon'   => 'fas fa-ad',
        'fields' => [
            
            // ========== 头部广告 ==========
            [
                'type'    => 'switcher',
                'id'      => 'ad_header_enable',
                'title'   => __('Header Ad','vt'),
                'default' => false,
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_header_code',
                'title'      => __('Header Ad','vt').' - '.__('Mobile Code','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
                'dependency' => ['ad_header_enable', '==', 'true'],
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_header_code_pc',
                'title'      => __('Header Ad','vt').' - ' .__('PC Code','vt'),
                'desc'       => __('AD Code for width > 481px','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
                'dependency' => ['ad_header_enable', '==', 'true'],
            ],
            
            // ========== 底部广告 ==========
            [
                'type'    => 'switcher',
                'id'      => 'ad_footer_enable',
                'title'   => __('Footer Ad','vt'),
                'default' => false,
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_footer_code',
                'title'      => __('Footer Ad','vt').' - '.__('Mobile Code','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
                'dependency' => ['ad_footer_enable', '==', 'true'],
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_footer_code_pc',
                'title'      => __('Footer Ad','vt').' - ' .__('PC Code','vt'),
                'desc'       => __('AD Code for width > 481px','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
                'dependency' => ['ad_footer_enable', '==', 'true'],
            ],
            
            // ========== 侧边栏广告 ==========
            [
                'type'    => 'switcher',
                'id'      => 'ad_sidebar_enable',
                'title'   => __('Sidebar Ad','vt'),
                'default' => false,
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_sidebar_code',
                'title'      => __('Sidebar Ad','vt').' - '.__('Mobile Code','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
                'dependency' => ['ad_sidebar_enable', '==', 'true'],
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_sidebar_code_pc',
                'title'      => __('Sidebar Ad','vt').' - ' .__('PC Code','vt'),
                'desc'       => __('AD Code for width > 481px','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
                'dependency' => ['ad_sidebar_enable', '==', 'true'],
            ],
            
            // ========== 文章头部广告 ==========
            [
                'type'    => 'switcher',
                'id'      => 'ad_single_top_enable',
                'title'   => __('Article Header Ad','vt'),
                'default' => false,
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_single_top_code',
                'title'      => __('Article Header Ad','vt').' - '.__('Mobile Code','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
                'dependency' => ['ad_single_top_enable', '==', 'true'],
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_single_top_code_pc',
                'title'      => __('Article Header Ad','vt').' - ' .__('PC Code','vt'),
                'desc'       => __('AD Code for width > 481px','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
                'dependency' => ['ad_single_top_enable', '==', 'true'],
            ],
            
            // ========== 文章底部广告 ==========
            [
                'type'    => 'switcher',
                'id'      => 'ad_single_bottom_enable',
                'title'   => __('Article Footer Ad','vt'),
                'default' => false,
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_single_bottom_code',
                'title'      => __('Article Footer Ad','vt').' - '.__('Mobile Code','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
                'dependency' => ['ad_single_bottom_enable', '==', 'true'],
            ],
            [
                'type'       => 'code_editor',
                'id'         => 'ad_single_bottom_code_pc',
                'title'      => __('Article Footer Ad','vt').' - ' .__('PC Code','vt'),
                'desc'       => __('AD Code for width > 481px','vt'),
                'default'    => '<a href="https://vtheme.cn/mirage" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
                'dependency' => ['ad_single_bottom_enable', '==', 'true'],
            ],
            
        ],
    ]);

    
    /**
     * 备份
     */
    CSF::createSection( $prefix, array(
        'title'  => __('Backup','vt'),
        'icon'   => 'dashicons dashicons-shield',
        'fields' => array(
            array(
                'type' => 'backup',
            )
        )
    ));


