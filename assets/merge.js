/**
 * 自动合并CSS文件
 */
const fs = require('node:fs');
const path = require('node:path');


/**
 * 合并文件的方法
 * @param  {arr} arrUrls 待合并的文件列表
 * @param  {str} strUrl  合并的文件路径，需要手动创建空文件
 * @param  {fun} filter  过滤器
 * @return 
 */
function merge(arrUrls, strUrl, filter) {
  var content = '';
  if (fs.existsSync(strUrl) == false) {
    console.error(strUrl + ' 不存在，合并失败');
    return;
  }
  // 遍历url并读取文件内容
  if (arrUrls && arrUrls.length && strUrl) {
    arrUrls.forEach(function(url) {
      if (fs.existsSync(url) == false) {
        return;
      }
      let st = fs.statSync(url);
      if (st.isFile()) {
        // 如果是文件
        content += fs.readFileSync(url);
      } else if (st.isDirectory()) {
        // 作为文件夹
        fs.readdirSync(url).forEach(function(filename) {
          if(filename.substring(0,1)=='.') return;
          let dir = path.join(url, filename);
          if (fs.statSync(dir).isFile()) {
            content += fs.readFileSync(dir);
          }
        });
      }
    });
    if (typeof filter == 'function') {
      content = filter(content);
    }
    // 写入新目录
    // 写入项目配置数据
    fs.writeFile(strUrl, content.trim(), function() {
      console.log('资源合并为 ' + strUrl + ' 成功');
    });
  }
};


/**
 * 合并 Home 模块的 css 文件
 */
var cssTimer = null;
const cssFileArr = [
  './assets/css/src/variables.css',
  './assets/css/src/normalize.css',
  './assets/css/src/iconfont.css',
  './assets/css/src/animation.css',
  './assets/css/src/common/common.css',
  './assets/css/src/common/form.css',
  './assets/css/src/common/button.css',
  './assets/css/src/common/pagination-wp.css',
  './assets/css/src/common/pagination.css',
  './assets/css/src/common/content-style.css',
  './assets/css/src/common/content-action.css',
  './assets/css/src/common/toast.css',
  './assets/css/src/common/mobile-nav.css',
  './assets/css/src/common/bar.css',
  './assets/css/src/common/dialog.css',
  './assets/css/src/common/modal.css',
  './assets/css/src/home/home.css',
  './assets/css/src/home/slider.css',
  './assets/css/src/home/hot.css',
  './assets/css/src/home/posts.css',
  './assets/css/src/home/links.css',
  './assets/css/src/article.css',
  './assets/css/src/page.css',
  './assets/css/src/comments.css',
  './assets/css/src/header.css',
  './assets/css/src/footer.css',
  './assets/css/src/widget.css',
  './assets/css/src/container.css',
  './assets/css/src/media/card.css',
  './assets/css/src/media/media.css',
  './assets/css/src/categories/banner.css',
  './assets/css/src/categories/category-list.css',
  './assets/css/src/categories/category-cards.css',
  './assets/css/src/users/index.css',
  './assets/css/src/users/user-center.css',
  './assets/css/src/users/star.css',
  './assets/css/src/dark.css',
  './assets/lib/ui/light-tip.css'
];

fs.watch('./assets/css/src', {
    recursive: true
}, (eventType, filename) => {
    clearTimeout(cssTimer);
    console.log(filename + ' 发生了 ' + eventType);
    cssTimer = setTimeout(() => {
        merge(cssFileArr,'./assets/css/style.css');
    }, 100);
});

