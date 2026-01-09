(function () {
  'use strict';

  /**
   * 幻灯片
   */
  function swiper () {
    // 幻灯
    new Swiper('.swiper-container', {
      pagination: {
        el: '.swiper-pagination',
      },
      loop: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      }
    });
  }

  function init$1(){
    toTopInit();
  }

  /*
   * 回到顶部
   */
  function toTopInit() {
    var btn = document.querySelector(".to-top");
    if (!btn) { return; }

    var clientHeight = document.documentElement.clientHeight;
    var timer = null;
    var istop = true;

    window.onscroll = function () {
      var dtop = document.documentElement.scrollTop || document.body.scrollTop;
      if (dtop >= (clientHeight * 0.1)) {
        btn.style.display = "flex";
      } else {
        btn.style.display = "none";
      }
      if (!istop) {
        clearInterval(timer);
      }
      istop = false;
    };

    btn.onclick = function () {
      timer = setInterval(function () {
        var dtop = document.documentElement.scrollTop || document.body.scrollTop;
        var speed = Math.floor(-dtop / 10);
        document.documentElement.scrollTop = dtop + speed;
        document.body.scrollTop = dtop + speed;
        istop = true;
        if (dtop == 0) {
          clearInterval(timer);
        }
      }, 15);
    };
  }

  /**
   * 图片懒加载
   */

  function lazyLoad() {
    window.addEventListener('load', function () {
      var imgs = document.querySelectorAll('.lazyload-img');

      loadImages(imgs);
      window.addEventListener('scroll', function () {
        loadImages(imgs);
      });

    });
  }



  function loadImages(images_arr) {
    if (!images_arr) { return; }
    images_arr.forEach(function (el) {
      var bound = el.getBoundingClientRect();
      if (bound.top <= window.innerHeight) {
        el.src = el.dataset.src;
      }
    });
  }

  /**
   * 公共函数
   */

  function getCookie(c_name) {
    if (document.cookie.length > 0) {
      var c_start = document.cookie.indexOf(c_name + "=");
      if (c_start != -1) {
        c_start = c_start + c_name.length + 1;
        var c_end = document.cookie.indexOf(";", c_start);
        if (c_end == -1) c_end = document.cookie.length;
        return unescape(document.cookie.substring(c_start, c_end));
      }
    }
    return ""
  }


  function setCookie(c_name, value, expiredays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + expiredays);
    var cookieStr = c_name + "=" + escape(value) + ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString());
    cookieStr += "; path=/";
    document.cookie = cookieStr;
  }


  /**
   * 判断是否出现在视口
   * @param {{}}} el 需要判断的 div 选择器
   * @returns {Boolean}
   */
  function isElementVisible(el) {
    const rect = el.getBoundingClientRect();
    const vWidth = window.innerWidth || document.documentElement.clientWidth;
    const vHeight = window.innerHeight || document.documentElement.clientHeight;
    if (
      rect.right < 0 ||
      rect.bottom < 0 ||
      rect.left > vWidth ||
      rect.top > vHeight
    ) {
      return false
    }
    return true
  }

  /**
   * @LightTip.js
   * @author popeyesailorman(yangfan)
   * @version
   * @Created: 20-05-15
   * @edit: 20-05-15
   */

  class LightTip extends HTMLElement {
      static get observedAttributes () {
          return ['open'];
      }

      constructor () {
          super();

          if (arguments.length) {
              LightTip.custom.apply(this, arguments);
          }
      }

      get type () {
          return this.getAttribute('type');
      }

      get time () {
          let strTime = this.getAttribute('time');
          if (!isNaN(strTime) && !isNaN(parseFloat(strTime))) {
              return Number(strTime);
          }

          return 3000;
      }

      set type (value) {
          this.setAttribute('type', value);
      }

      set time (value) {
          this.setAttribute('time', value);
      }

      get open () {
          return this.hasAttribute('open');
      }

      set open (value) {
          this.toggleAttribute('open', value);
      }

      connectedCallback () {
          // 自定义元素设置 tabIndex=0 代表改元素可聚焦，并可通过键盘导航来聚焦到该元素
          this.setAttribute('tabIndex', 0);
          this.setAttribute('role', 'tooltip');

          if (!this.closeMode) {
              this.closeMode = 'hide';
          }

          // 点击组件本身或按键盘 esc/enter 键即可关闭组件
          this.addEventListener('click', () => {
              // 移除元素
              this[this.closeMode]();
          });

          // 全局事件
          this.dispatchEvent(new CustomEvent('connected', {
              detail: {
                  type: 'ui-lighttip'
              }
          }));

          this.isConnectedCallback = true;
      }

      attributeChangedCallback (name, oldValue, newValue) {
          // 让按钮或者之前的触发元素重新获取焦点，便于继续操作
          if (name == 'open' && typeof oldValue !== typeof newValue) {
              if (typeof newValue === 'string') {
                  clearTimeout(this.timer);
                  this.timer = setTimeout(() => {
                      // 标志量，是否是因为时间到关闭
                      this.isTimeHide = true;
                      // 关闭提示
                      this[this.closeMode]();
                      this.position();
                  }, this.time);

                  this.setAttribute('data-tid', this.timer);
                  this.classList.add('ESC');

                  // 组件的 z-index 层级计算
                  this.zIndex();

                  // 组件的定位，不同的提示位置不重叠
                  this.position();
              } else {
                  this.classList.remove('ESC');
              }
              this.tabIndex();

              this.isTimeHide = null;
          }
      }

      zIndex () {
          // 只对<body>子元素进行层级最大化计算处理，这里lighttip默认的z-index值是19
          var numZIndexNew = 19;
          this.parentElement && [...this.parentElement.childNodes].forEach(function (eleChild) {
              if (eleChild.nodeType != 1) {
                  return;
              }
              var objStyleChild = window.getComputedStyle(eleChild);
              var numZIndexChild = objStyleChild.zIndex * 1;
              if (numZIndexChild && objStyleChild.display != 'none') {
                  numZIndexNew = Math.max(numZIndexChild + 1, numZIndexNew);
              }
          });
          this.style.zIndex = numZIndexNew;
      }

      // 定位处理
      position () {
          var elesOpen = [...document.querySelectorAll('ui-lighttip[open]:not([type="loading"])')];
          // 基于 data-tid 排序
          var elesOpenSort = elesOpen.sort(function (eleA, eleB) {
              return (eleA.getAttribute('data-tid') || 0) - (eleB.getAttribute('data-tid') || 0);
          });
          // 确定提示内容
          var objMatchText = {};
          var numIndex = -1;

          elesOpenSort.forEach((ele) => {
              let strText = ele.textContent;
              if (typeof objMatchText[strText] == 'undefined') {
                  numIndex++;
                  objMatchText[strText] = numIndex;
              }
              ele.style.setProperty('--ui-sort-index', objMatchText[strText]);
          });
      }

      // 新的元素层级总是最高
      tabIndex () {
          var eleContainer = this;
          var eleLastActive = LightTip.lastActiveElement;
          if (this.open == true) {
              var eleActiveElement = document.activeElement;
              
              // 键盘索引起始位置定位在提示元素上
              if (eleActiveElement && !eleActiveElement.closest('[keepfocus]')) {
                  if (eleContainer !== eleActiveElement) {
                      LightTip.lastActiveElement = eleActiveElement;
                  }
                  
                  eleContainer.focus();
              }
          } else if (eleLastActive && !eleLastActive.matches('body')) {
              // 获取焦点但不会定位
              eleLastActive.focus({
                  preventScroll: true
              });
              // 如果不是键盘关闭提示，而是点击的话，之前的焦点元素失焦
              // 这里实现有问题，如果是时间到了自动关闭的话，这里不应该失焦
              if (!window.isKeyEvent && !this.isTimeHide) {
                  eleLastActive.blur();
              }
              LightTip.lastActiveElement = null;
          }
          return this;
      }

      // success
      static success (text, time = 3000) {
          return this.custom(text, 'success', time);
      }
      // error
      static error (text, time = 3000) {
          return this.custom(text, 'error', time);
      }
      // normal
      static normal (text, time = 3000) {
          return this.custom(text, 'normal', time);
      }
      // loading
      static loading (text) {
          text = text || '正在加载中...';
          return this.custom(text, 'loading');
      }
      // 调用方法处理
      static custom (text = '', type, time) {
          // 如果是静态方法执行
          // 创建ui-lighttip自定义元素
          if (!this.matches || !this.matches('ui-lighttip')) {
              return LightTip.custom.apply(document.createElement('ui-lighttip'), arguments);
          }

          if (typeof text == 'object') {
              type = text;
              text = '';
          }

          if (typeof text != 'string') {
              return this;
          }

          this.closeMode = 'remove';

          // 如果传入的类型是object形式
          if (type && typeof type === 'object') {
              LightTip.custom.call(this, text, type.type, type.time);
              return;
          }
          // 如果type的类型是number，则赋值给time
          if (typeof type === 'number') {
              LightTip.custom.call(this, text, time, type);
              return;
          }

          if (type == 'loading') {
              if (!text) {
                  text = '正在加载中...';
              }
              time = 999999;
          }

          if (time) {
              this.time = time;
          }
          if (type) {
              this.type = type;
          }

          this.innerHTML = text;
          // 提高无障碍
          if (type == 'success') {
              this.setAttribute('aria-lable', '操作成功');
          } else if (type == 'error') {
              this.setAttribute('aria-lable', '操作失败');
          }

          // append内存中创建的ui-lighttip元素
          if (!this.parentElement) {
              document.body.appendChild(this);

              this.dispatchEvent(new CustomEvent('DOMContentLoaded'));
          }

          this.show();

          return this;
      }
      remove () {
          if (this.parentElement) {
              this.parentElement.removeChild(this);
          }
          this.open = false;
      }
      show () {
          if (this.time > 0) {
              this.open = true;
          }
      }
      hide () {
          this.open = false;
      }
  }

  if (!customElements.get('ui-lighttip')) {
      customElements.define('ui-lighttip', LightTip);
  }

  // 将该方法定义为 window 全局使用的方法
  window.LightTip = LightTip;

  /**
   * dialogTools
   */

  function registerDialog(selector){
    selector.show = show;
    selector.showModal = showModal;
    selector.close = close;

    closeButtonInit.apply(selector);
  }

  function show() {
    this.setAttribute('open','');
  }

  function showModal() {
    document.body.classList.add('no-scroll');
    this.setAttribute('modal','');
    this.setAttribute('open','');
  }

  function close() {
    document.body.classList.remove('no-scroll');
    this.removeAttribute('modal');
    this.removeAttribute('open');
  }

  function closeButtonInit() {
    var closeButtons = this.querySelectorAll('.close');
    closeButtons.forEach(btn => {
      btn.addEventListener('click', e => {
        this.close();
      });
    });
  }

  var dialogTools = { registerDialog };

  /**
   * 文章页
   */


  function articles () {
    likeInit();
    starInit();
    sharePosterInit();
    coinInit();
    tocInit();
  }

  /**
   * 点赞事件绑定
   */
  async function likeInit(){
    var likeButtons = document.querySelectorAll('.widget-action.like');
    if(!likeButtons) return;
    likeButtons.forEach( button=>{
      button.addEventListener('click', async function(){
        var that = this;
        
        var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
        var post_id = document.querySelector("input[name='post_id']").value;
        
        var addUrl = '/wp-json/vtheme/v1/stars' + "?_wpnonce=" + wpnonce;
        var deleteUrl = '/wp-json/vtheme/v1/stars/' + post_id + "?_wpnonce=" + wpnonce;
        
        // 登录用户取消点赞
        if( this.classList.contains('active') ){
          let response = await fetch(deleteUrl, {
            method:'DELETE',
            headers:{'Content-Type': 'application/json'},
            body:JSON.stringify({'type':'like'})
          });
          let responseJson = await response.json();
          if(response.status == 200){
            that.classList.remove('active');
            let num = that.querySelector('.number').innerText;
            num = (--num <= 0) ? '' : num; 

            that.querySelector('.number').innerText = num;
            // lightTip.custom('取消点赞', {type:'warning', time:500});
          }else {
            LightTip.error(responseJson.error);
          }
          return;
        }

        // 未登录用户，通过 cookie 判断是否点赞过
        let currentUserId = document.querySelector('input[name="current_user_id"]').value;
        let likeIdsArr = [];

        if(currentUserId==0){
          let likeIdsStr = getCookie('likeIds');
          likeIdsArr = [];

          try {
            likeIdsArr = JSON.parse(likeIdsStr);
          } catch(e) {
            likeIdsArr = [];
          }

          if(likeIdsArr.indexOf(post_id) !== -1){
            LightTip.success('今天已经点赞过了');
            return;
          }
        }
        
        // 执行点赞请求
        var data = {};
        data.object_id = document.querySelector("input[name='post_id']").value;
        data.type = 'like';

        let response = await fetch(addUrl, {
          method:'POST',
          headers:{'Content-Type': 'application/json'},
          body:JSON.stringify(data)
        });

        let responseJson = await response.json();
        if(response.status == 201){
          that.querySelector('.number').innerText = responseJson.counter;
          if(responseJson.user_id == 0){ //未登录用户
            likeIdsArr.push(post_id);
            setCookie('likeIds', JSON.stringify(likeIdsArr), 1);
            return;
          }
          that.classList.add('active');
          LightTip.normal('点赞成功',1000);
        }else {
          LightTip.error(responseJson.error);
        }
        
      });
    });
  }


  /**
   * 收藏事件绑定
   */
  async function starInit(){
    var starButtons = document.querySelectorAll('.widget-action.star');
    if(!starButtons) return;
    starButtons.forEach( button=>{
      button.addEventListener('click', async function(){
        // 未登录用户，弹出登录框
        document.querySelector('input[name="current_user_id"]').value;
        var loginModal = document.querySelector('.login-dialog');
        if(loginModal){
          loginModal.showModal();
          document.querySelector('body').classList.add('no-scroll');
        }
        

        var that = this;
        
        var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
        var post_id = document.querySelector("input[name='post_id']").value;
        
        var addUrl = '/wp-json/vtheme/v1/stars' + "?_wpnonce=" + wpnonce;
        var deleteUrl = '/wp-json/vtheme/v1/stars/' + post_id + "?_wpnonce=" + wpnonce;
        
        if( this.classList.contains('active') ){
          var that = this;
          var response = await fetch(deleteUrl, {
            method:'DELETE',
            headers:{'Content-Type': 'application/json'},
            body:JSON.stringify({'type':'star'})
          });
          var responseJson = await response.json();
          if(response.status == 200){
            that.classList.remove('active');
            let num = that.querySelector('.number').innerText;
            num = (--num <= 0) ? '' : num; 

            that.querySelector('.number').innerText = num;
          }else {
            LightTip.error(responseJson.error);
          }
        } else {
          var data = {};
          data.object_id = document.querySelector("input[name='post_id']").value;
          data.type = 'star';
          
          var response = await fetch(addUrl, {
            method:'POST',
            headers:{'Content-Type': 'application/json'},
            body:JSON.stringify(data)
          });
          var responseJson = await response.json();
          if(response.status == 201){
            that.classList.add('active');
            that.querySelector('.number').innerText = responseJson.counter;
            LightTip.normal('收藏成功',1000);
          } else if(response.status == 401) {
            LightTip.normal('请登录后重试',1000);
          }
        }

        
      });
    });
  }


  /**
   * 海报生成
   */
  function sharePosterInit() {
    var qrcodeSelector = document.getElementById("qrcode");
    if(!qrcodeSelector) { return; }

    window.addEventListener('load', function(){
      var url = document.querySelector('input[name="post_url"]').value;
      new QRCode(qrcodeSelector, {
          text: url,
          width: 80,
          height: 80,
          colorDark : "#000000",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });
    });


    var shareSelector = document.querySelector('.share-poster');
    var shareDialog = document.querySelector('#share-dialog');
    if(!shareSelector || !shareDialog){ return; }
    dialogTools.registerDialog(shareDialog);

    shareSelector.addEventListener('click', function(e){
      // shareDialog.show();
      shareDialog.showModal();
      // document.body.classList.add('no-scroll');

      var copyDom = document.querySelector('.share-card');
      var width = copyDom.offsetWidth;
      var height = copyDom.offsetHeight;
      var canvas = document.createElement("canvas");

      var scale = 1; // 图片缩放
      canvas.width = width * scale;
      canvas.height = height * scale;
      canvas.getContext("2d").scale(scale, scale);
      　　
      var opts = {
          scale: scale,
          canvas: canvas,
          width: width,
          height: height
      };

      html2canvas(copyDom, opts).then(canvas => {
          let dataURL = canvas.toDataURL("image/png"); // canvas转base64
          document.querySelector('#poster').src = dataURL;
          document.querySelector('#poster').classList.remove('hide');
          document.querySelector('.top-layer').classList.add('hide');
      });
    });


    shareDialog.querySelector('.close-button').addEventListener('click', e=>{
      shareDialog.close();
      // document.body.classList.remove('no-scroll');
    });
  }


  /**
   * 打赏
   */
  function coinInit(argument) {
    var coinButton = document.querySelector('.coin');

    var coinDialog = document.querySelector('#coin-dialog');
    if(!coinButton){ return; }
    dialogTools.registerDialog(coinDialog);

    coinButton && coinButton.addEventListener('click', async e=> {
      coinDialog.showModal();
    });
  }


  /**
   * TOC 目录
   */
  function tocInit() {
    var tocButton = document.querySelector('.toc-button');
    if(!tocButton) return;
    tocButton.addEventListener('click', function(e){
      tocButton.parentNode.parentNode.classList.toggle('show');
    });

  }

  /*
   * 夜间模式按钮
   */


  function darkModeInit() {
    if (document.querySelector(".dark-mode-button")) {
      document.querySelectorAll(".dark-mode-button").forEach(function (btn) {
        btn.addEventListener("click", darkMode);
      });
    }
  }


  function darkMode() {
    console.log("cookie: ", getCookie("darkMode"));
    var isDarkMode = document.body.classList.contains('dark-mode');
    if (isDarkMode) {
      setCookie("darkMode", 0);
    } else {
      setCookie("darkMode", 1);
    }
    document.body.classList.toggle("dark-mode");
    document.querySelectorAll(".dark-mode-button").forEach(function (btn) {
      btn.classList.toggle("dark");
    });
  }

  var counter = 0;

  function open(userOption) {
    var option = { title: ' ', duration: 3 };
    option = Object.assign(option, userOption);

    var id = "id" + counter;
    var htmlString = `<div class="toast-box" id="${id}">${option.title}</div>`;
    document.body.insertAdjacentHTML('beforeend', htmlString);
    counter++;
    setTimeout(function () {
      document.body.removeChild(document.querySelector('#' + id));
    }, option.duration * 1000);
  }

  var toast = { open };

  function headerMobile () {
    mobileMenu();
    mobileSearch();
  }


  /**
   * 移动端下拉
   */
  function mobileMenu(){

    // 显示移动菜单
    var mobileTopMenuButton = document.querySelector('.top-nav-button.menu-button');
    if (!mobileTopMenuButton) {
      return;
    }
    console.log('mobileMenuCloseButton', mobileMenuCloseButton);

    mobileTopMenuButton.addEventListener('click', function () {
      mobileTopMenuButton.classList.add('hidden');
      var mobileDialog = document.querySelector('.mobile-menu-modal');
      mobileDialog.classList.add('show');
      document.querySelector('body').classList.add('no-scroll');
    });

    // 隐藏移动菜单
    var mobileMenuCloseButton = document.querySelector('.action-button.close');
    if (!mobileMenuCloseButton) {
      return;
    }
    // console.log('mobileMenuCloseButton ', mobileMenuCloseButton);
    mobileMenuCloseButton.addEventListener('click', function () {
      var mobileDialog = document.querySelector('.mobile-menu-modal');
      mobileDialog.classList.remove('show');
      document.querySelector('body').classList.remove('no-scroll');
    });

    // 下拉菜单
    var items = document.querySelectorAll(".header.mobile .menu-item-has-children i");
    if(!items) return;
    
    items.forEach(function(i){
      i.addEventListener('click', function(e){
        e.preventDefault();
        e.stopPropagation();
        if(i.parentNode.parentNode.querySelector('.child-menu')){
          i.parentNode.parentNode.classList.toggle('active');
        }
      });
    });
  }



  /*
  function mobileMenu() {
    // 显示移动菜单
    var mobileTopMenuButton = document.querySelector('.top-nav-button.menu-button');
    if (!mobileTopMenuButton) {
      return;
    }
    mobileTopMenuButton.addEventListener('click', function () {
      mobileTopMenuButton.classList.add('hidden');
      var mobileDialog = document.querySelector('.mobile-menu-layout');
      mobileDialog.classList.add('show');
      document.querySelector('body').classList.add('no-scroll');
    });
    // 隐藏移动菜单
    var mobileMenuLayoutCloseButton = document.querySelector('.mobile-menu-layout-close-button');
    if (!mobileMenuLayoutCloseButton) {
      return;
    }
    mobileMenuLayoutCloseButton.addEventListener('click', function () {
      mobileTopMenuButton.classList.remove('hidden');
      var mobileDialog = document.querySelector('.mobile-menu-layout');
      mobileDialog.classList.remove('show');
      document.querySelector('body').classList.remove('no-scroll');
    });
    // 移动菜单的子菜单
    var menuItmes = document.querySelectorAll('.nav-list .menu-item-has-children>a');
    if (menuItmes) {
      menuItmes.forEach(function (item, key) {
        item.addEventListener('click', function (e) {
          item.parentNode.classList.toggle('show');
          e.preventDefault();
        });
      });
    }
  }
  */

  /**
   * 移动端搜索
   */
  function mobileSearch() {
    var mobileTopSearchButton = document.querySelector('.top-nav-button.search-button');
    if (!mobileTopSearchButton) return;

    // 显示移动搜索框
    mobileTopSearchButton.addEventListener('click', function () {
      document.querySelector('#search-modal-dialog').classList.add('show');
      document.querySelector('body').classList.add('no-scroll');
    });

    // 初始化移动搜索框
    var searchForm = document.querySelector(".mobile-search-modal form");
    var searchInput = document.querySelector(".mobile-search-modal input[name='keyword']");
    if (searchForm) {
      searchForm.onkeydown = function (event) {
        if (event.keyCode == 13 && searchInput.value == '') {
          toast.open({ title: "请输入关键词" });
          return false;
        }
      };
    }

    searchForm.onsubmit = function () {
      if (searchInput.value == "") {
        toast.open({ title: "请输入关键词" });
        return false;
      }
    };

    let closeButton = document.querySelector('#search-modal-dialog .modal-close-button');
    if (!closeButton) { return; }
    closeButton.addEventListener('click', function () {
      this.parentNode.parentNode.classList.toggle('show');
      document.querySelector('body').classList.remove('no-scroll');
    });

    // searchForm.addEventListener("submit", function () {
    //   // if (searchInput.value == "") {
    //   //   toast.open({ title: "请输入关键词" });
    //   //   return false;
    //   // } else {
    //   //   searchForm.submit();
    //   // }
    //   console.log(1);
    //   return false;
    // });

  }

  function headerPc () {
    pcSearch();
    nav();
    avatarShow();
  }

  /* PC 端搜索 */
  function pcSearch() {
    var searchToggleButton = document.querySelector('.header.pc .search-toggle-button');
    if(searchToggleButton){
      searchToggleButton.addEventListener('click', function(e){
        this.classList.toggle('active');
        if(this.classList.contains('active')){
          document.querySelector('.header.pc .search-widget').classList.add('show');
          var input = document.querySelector('.header.pc .search-widget .keyword');
          var text = input.value;
          input.value = '';
          input.focus();
          input.value = text;
        }else {
          document.querySelector('.header.pc .search-widget').classList.remove('show');
        }
      });
    }
    
    var closeSearchButton = document.querySelector('.header.pc .close-widget a');
    if(closeSearchButton ){
      closeSearchButton.addEventListener('click', ()=>{
        document.querySelector('.header.pc .search-widget').classList.remove('show');
        searchToggleButton.classList.remove('active');
      });
    }

    var searchButton = document.querySelector(".header.pc .search .button");
    if (searchButton) {
      searchButton.addEventListener("click", function (e) {
        e.preventDefault();
        if (!document.querySelector(".keyword").value) {
          toast.open({ title: "请输入关键词" });
          return false;
        } else {
          document.querySelector(".search").submit();
        }
      });
    }
  }

  /* 导航 */
  function nav(){
    var items = document.querySelectorAll(".header.pc .menu-item-has-children");
    if(!items) return;
    
    items.forEach(function(i){
      i.addEventListener('mouseenter', function(e){
        if(i.querySelector('.child-menu')){
            i.classList.add('active');
        }
      });
      
      i.addEventListener('mouseleave', function(e){
        if(i.querySelector('.child-menu')){
            i.classList.remove('active');
        }
      });
    });
  }

  /* 头像点击 */
  function avatarShow(){
    var avatarShowButton = document.querySelector('.header-top-avatar');
    if(!avatarShowButton){
      return false;
    }
    
    avatarShowButton.addEventListener('click', function(e){
      this.classList.toggle('active');
      if (this.classList.contains('active')) {
        document.querySelector('.header.pc .user-widget').classList.add('show');
      } else {
        document.querySelector('.header.pc .user-widget').classList.remove('show');
      }
    });
  }

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


  var home = {
    homeInit
  };

  function init (){
    avatarUploadAction();
    mobileNavAction();
  }

  /**
   * 用户中心，头像上传事件绑定
   */
  function avatarUploadAction(){
    var uploadAvatarButton = document.querySelector(".upload-avatar-button");
    if(!uploadAvatarButton) return;
    
    uploadAvatarButton.addEventListener('click', function(e) {
      document.querySelector('#avatar-input').click();
    });
    
    var uploadInputControl = document.querySelector("#avatar-input");
    uploadInputControl.onchange = function() {
      if (!this.files[0] || this.files[0] == undefined) return;
      
      // toast.open({title:"上传开始"});
      
      var fd = new FormData();
      fd.append("avatar-input", this.files[0]);
    
      axios({
        method: 'post',
        url: document.querySelector('#avatar_upload').getAttribute('action'),
        data: fd,
        headers: {
          'content-type': 'multipart/form-data'
        },
      }).then(function(response) {
        if (response.status == 201) {
          toast.open({title:"头像上传成功"});
          
          document.querySelector(".user-avatar .avatar").src = response.data.avatar_url;
          document.querySelector(".header-top-avatar img").src = response.data.avatar_url;
    
        } else {
          console.log("图片上传错误");
        }
        uploadInputControl.value = null;
      }).catch(function(error) {
        // layer.closeAll();
        console.log("error: ", error);
        if (error.response.status == 422) {
          alert("文件类型错误");
          return;
        } else {
          alert(error.message);
        }
        uploadInputControl.value = null;
      });
    };
  }


  /**
   * 移动端的一些事件
   */
  function mobileNavAction(){
    var userButton = document.querySelector('.mobile-nav .nav-button.mine');
    if( userButton ){
      userButton.addEventListener('click', e=>{
        if( parseInt(userButton.dataset.userId) > 0){
          location.href = '/users/' + userButton.dataset.userId;
        } else {
          var loginModal = document.querySelector('.modal.login-modal');
          if(loginModal){ loginModal.classList.add('modal-show'); }
        }
      });
    }
  }

  function comments(){
    userLink();
  }

  function userLink() {
    var users = document.querySelectorAll('.nickname a');
    if(!users) return;
    console.log('users', users);
    for (var i = 0; i < users.length; i++) {
      users[i].href = users[i].dataset.url;
    }
  }

  /**
   * 小工具
   */

  function sideMenuInit(){
  	var btns = document.querySelectorAll('.side-menu .arrow');
  	if(btns.length){
  		btns.forEach(btn=>{
  			btn.addEventListener('click', e => {
  				console.log('e', e);
  				e.preventDefault();
            // if(!e.target.classList.contains('arrow')){ return; }
  				e.target.parentNode.parentNode.classList.toggle('hide-children');
  				return;
  			});
  		});
  	}
  }


  var widget = { sideMenuInit };

  /**
   * JavaScript 项目主文件
   */

  swiper();
  init$1();
  lazyLoad();
  articles();
  darkModeInit();
  headerMobile();
  headerPc();
  home.homeInit();
  init();
  comments();
  widget.sideMenuInit();


  function footerCheck() {
    window.addEventListener('load', function(e){
      let themeName = document.querySelector('a[copyright]');
      if(!themeName || themeName.innerText !== "Mirage"){
        document.body.innerHTML = 'keep_theme_footer';
      }
    });
  }
  footerCheck(); // 主题底部署名检查

})();
