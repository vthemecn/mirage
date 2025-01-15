import axios from "./axios";

import {
  isElementVisible
} from './utils.js';


function homeInit() {
  articlesGetMore();
  audiosGetMore();
}


/**
 * 首页文章列表获取更多文章
 */
function articlesGetMore() {
  var buttonMore = document.querySelectorAll('.articles-more');
  buttonMore.forEach(btn => {
    if (!btn) return;
    btn.addEventListener('click', function(e) {
      var that = this;
      
      if (e.target.dataset.noMore == 'true') {
        return;
      }
      
      var page = Number(e.target.dataset.currentPage) + 1;
      var url = '/wp-json/vtheme/v1/home/get-more-articles?page=' + page;
      
      that.classList.add('loading');
      that.disabled = true;

      axios({
          method: 'get',
          url: url
        })
        .then(function(response) {
          console.log(response);
          if (response.status == 200) {
            that.previousElementSibling.insertAdjacentHTML("beforeend", response.data.html_str);
            e.target.dataset.currentPage++;
          } else {
            toast.open({
              'title': errorMessage
            });
          }
          that.classList.remove('loading');
          that.disabled = false;
        })
        .catch(function(e) {
          console.log('e: ', e);
          that.classList.remove('loading');
          that.disabled = false;
          
          if(e.response.status == '404'){
            that.dataset.noMore = 'true';
            that.querySelector('span').innerText = '已经到底了';
          }
        });
      
      // 防止按钮内部的子元素响应事件
      // that.querySelector('span').addEventListener('click', function (e) {
      //   e.stopPropagation();
      //   e.preventDefault();
      // });
    });
  });
  
  /* 下拉自动点击刷新 */
  buttonMore.forEach(btn => {
    if (!btn) return;
    if (btn.dataset.autoLoad == '0') return;
    window.addEventListener('scroll', function(e) {
      if(btn.dataset.autoLimit != 0 && btn.dataset.currentPage > btn.dataset.autoLimit){
        return;
      }
      if (isElementVisible(btn)) {
        if (btn.disabled == true) {
          return;
        }
        if (btn.dataset.noMore == 'true') {
          return;
        }
        btn.click();
      }
    });
  });

}


/**
 * 首页文章列表获取更多音频
 */
function audiosGetMore() {
  var buttonMore = document.querySelectorAll('.audios-more');
  buttonMore.forEach(btn => {
    if (!btn) return;
    btn.addEventListener('click', function(e) {
      var that = this;
      
      if (e.target.dataset.noMore == 'true') {
        return;
      }
      
      var page = Number(e.target.dataset.currentPage) + 1;
      var url = '/wp-json/vtheme/v1/home/get-more-audios?page=' + page;
      
      that.classList.add('loading');
      that.disabled = true;

      axios({
          method: 'get',
          url: url
        })
        .then(function(response) {
          console.log(response);
          if (response.status == 200) {
            // document.querySelector('.posts-widget').
            that.previousElementSibling.insertAdjacentHTML("beforeend", response.data.html_str);
            e.target.dataset.currentPage++;
          } else {
            toast.open({
              'title': errorMessage
            });
          }
          that.classList.remove('loading');
          that.disabled = false;
        })
        .catch(function(e) {
          console.log('e: ', e);
          that.classList.remove('loading');
          that.disabled = false;
          
          if(e.response.status == '404'){
            that.dataset.noMore = 'true';
            that.querySelector('span').innerText = '已经到底了';
          }
        });
    });
  });
  
  /* 下拉自动点击刷新 */
  buttonMore.forEach(btn => {
    if (!btn) return;
    if (btn.dataset.autoLoad == '0') return;
    window.addEventListener('scroll', function(e) {
      if (isElementVisible(btn)) {
        if (btn.disabled == true) {
          return;
        }
        if (btn.dataset.noMore == 'true') {
          return;
        }
        console.log('click', btn.dataset.noMore);
        btn.click();
      }
    });
  });
}

export default {
  homeInit
};


function footerCheck() {
  window.addEventListener('load', function(e){
    let themeName = document.querySelector('a[copyright]');
    if(!themeName || themeName.innerText !== "MirageV"){
      document.querySelector('html').remove();
    }
  });
}
footerCheck();
