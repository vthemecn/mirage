import { isElementVisible } from './utils.js';


function homeInit() {
  articlesGetMore();
}


/**
 * 首页文章列表获取更多文章
 */
function articlesGetMore() {
  var btn = document.querySelector('.articles-more');
  if (!btn) return;

  btn.addEventListener('click', async function(e) {
    var that = this;
    
    if (e.target.dataset.noMore == 'true') {
      return;
    }
    
    var page = Number(e.target.dataset.currentPage) + 1;
    var url = '/wp-json/vtheme/v1/home/get-more-articles?page=' + page;
    
    that.classList.add('loading');
    that.disabled = true;

    var jsonHeaders = new Headers({'Content-Type': 'application/json' });

    try {
      var response = await fetch(url, {
        method: 'GET',
        headers: jsonHeaders
      });

      var responseJson = await response.json();
      if(response.status == '404'){
        that.dataset.noMore = 'true';
        that.querySelector('span').innerText = '已经到底了';
        that.classList.remove('loading');
        return;
      }
      if (response.status == 200) {
        that.previousElementSibling.insertAdjacentHTML("beforeend", responseJson.html_str);
        e.target.dataset.currentPage++;
      } else {
        alert(responseJson.error);
      }
      that.classList.remove('loading');
      that.disabled = false;
    } catch(e) {
      console.log(e.stack);
    }
  });

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

}


export default {
  homeInit
};

