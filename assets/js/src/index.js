/**
 * JavaScript 项目主文件
 */

import __ from './i18n.js';
// 将翻译函数挂载到全局 window 对象,供其他模块使用
window.__ = __;

import './notyf.js';

// 幻灯片
import swiper from './swiper';
swiper();

import bar from './bar';
bar();

import { lazyLoad } from './lazy-load';
lazyLoad();

import single from './single.js';
single();

import { darkModeInit } from './dark-mode';
darkModeInit();

import headerMobile from "./header-mobile";
headerMobile();

import headerPc from "./header-pc";
headerPc();

import home from './home';
home.homeInit();

import dialogTools from './login-dialog.js';
dialogTools();

import comments from './comments.js';
comments();

import widget from './widget.js';
widget.sideMenuInit();

function vt_footer_check() {
  window.addEventListener('load', function(e){
    let themeName = document.querySelector('a[copyright]');
    if(!themeName || themeName.innerText !== "Mirage"){
      document.body.innerHTML = 'keep_theme_footer';
    }
  });
}
vt_footer_check(); // 主题底部署名检查
