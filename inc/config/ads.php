<?php
/**
 * 广告配置 - 移动端优先
 * 文件位置：/cs-config/theme-options.php
 */

if (!defined('ABSPATH')) {
    exit;
}

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
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
            'dependency' => ['ad_header_enable', '==', 'true'],
        ],
        [
            'type'       => 'code_editor',
            'id'         => 'ad_header_code_pc',
            'title'      => __('Header Ad','vt').' - ' .__('PC Code','vt'),
            'desc'       => __('AD Code for width > 481px','vt'),
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
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
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
            'dependency' => ['ad_footer_enable', '==', 'true'],
        ],
        [
            'type'       => 'code_editor',
            'id'         => 'ad_footer_code_pc',
            'title'      => __('Footer Ad','vt').' - ' .__('PC Code','vt'),
            'desc'       => __('AD Code for width > 481px','vt'),
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
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
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
            'dependency' => ['ad_sidebar_enable', '==', 'true'],
        ],
        [
            'type'       => 'code_editor',
            'id'         => 'ad_sidebar_code_pc',
            'title'      => __('Sidebar Ad','vt').' - ' .__('PC Code','vt'),
            'desc'       => __('AD Code for width > 481px','vt'),
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
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
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
            'dependency' => ['ad_single_top_enable', '==', 'true'],
        ],
        [
            'type'       => 'code_editor',
            'id'         => 'ad_single_top_code_pc',
            'title'      => __('Article Header Ad','vt').' - ' .__('PC Code','vt'),
            'desc'       => __('AD Code for width > 481px','vt'),
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
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
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads-sm.jpg" style="width:100%"></a>',
            'dependency' => ['ad_single_bottom_enable', '==', 'true'],
        ],
        [
            'type'       => 'code_editor',
            'id'         => 'ad_single_bottom_code_pc',
            'title'      => __('Article Footer Ad','vt').' - ' .__('PC Code','vt'),
            'desc'       => __('AD Code for width > 481px','vt'),
            'default'    => '<a href="https://example.com" target="_blank"><img src="'.THEME_URL.'/assets/images/ads.jpg" style="width:100%"></a>',
            'dependency' => ['ad_single_bottom_enable', '==', 'true'],
        ],
        
    ],
]);