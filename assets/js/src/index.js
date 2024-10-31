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

import contentAction from './content-action.js';
contentAction();

import { darkModeInit } from './dark-mode';
darkModeInit();

import headerMobile from "./header-mobile";
headerMobile();

import headerPc from "./header-pc";
headerPc();

import home from './home';
home.homeInit();

// import auth from './auth';
// auth.init();

import users from './users.js';
users();

import orders from './orders.js';
orders();

import comments from './comments.js';
comments();

// import header1 from './header1';
// header1();

// import modal from "../plugins/modal/modal";
// modal();

// PC 端
// import { pcNavSearch } from './pc';
// pcNavSearch();

// import { articleReward } from './article';
// articleReward();

// import { mobileTopMenu, mobileTopSearch } from './mobile';
// mobileTopMenu();
// mobileTopSearch();
