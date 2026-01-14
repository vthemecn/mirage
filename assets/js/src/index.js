/**
 * JavaScript 项目主文件
 */

// 幻灯片
import swiper from './swiper';
swiper();

import bar from './bar';
bar();


import { lazyLoad } from './lazy-load';
lazyLoad();

import articles from './articles.js';
articles();

import { darkModeInit } from './dark-mode';
darkModeInit();

import headerMobile from "./header-mobile";
headerMobile();

import headerPc from "./header-pc";
headerPc();

import home from './home';
home.homeInit();

import users from './users.js';
users();

import comments from './comments.js';
comments();

import widget from './widget.js';
widget.sideMenuInit();

import loginDialog from './login-dialog.js';
loginDialog();

function footerCheck() {
  window.addEventListener('load', function(e){
    let themeName = document.querySelector('a[copyright]');
    if(!themeName || themeName.innerText !== "Mirage"){
      document.body.innerHTML = 'keep_theme_footer';
    }
  });
}
footerCheck(); // 主题底部署名检查
