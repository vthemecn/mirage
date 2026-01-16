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
    // 创建 Notyf 实例
    const notyf = new Notyf({
      duration: 2000,
      position: { x: 'center', y: 'top' },
      types: [
        {
          type: 'success',
          background: '#28a745'
        },
        {
          type: 'error',
          background: '#dc3545'
        }
      ]
    });

    likeInit(notyf);
    starInit(notyf);
    sharePosterInit();
    coinInit();
    tocInit();
  }

  /**
   * 点赞事件绑定
   */
  async function likeInit(notyf){
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
            // 取消点赞不显示提示
          }else {
            notyf.error(responseJson.error);
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
            notyf.success('今天已经点赞过了');
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
          notyf.success('点赞成功');
        }else {
          notyf.error(responseJson.error);
        }
        
      });
    });
  }


  /**
   * 收藏事件绑定
   */
  async function starInit(notyf){
    var starButtons = document.querySelectorAll('.widget-action.star');
    if(!starButtons) return;
    starButtons.forEach( button=>{
      button.addEventListener('click', async function(){
        // 检查用户是否已登录
        let currentUserId = document.querySelector('input[name="current_user_id"]').value;
      
        if(currentUserId == 0){
          console.log('currentUserId', currentUserId, currentUserId == 0, 'xxx');

          // 如果未登录，弹出登录对话框
          var loginModal = document.querySelector('.login-register-dialog'); // 修复选择器
          if(loginModal){
            loginModal.showModal();
            document.querySelector('body').classList.add('no-scroll');
          }
          return; // 直接返回，不执行后续操作
        }
        
        var that = this;
        
        var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
        var post_id = document.querySelector("input[name='post_id']").value;
        
        var addUrl = '/wp-json/vtheme/v1/stars' + "?_wpnonce=" + wpnonce;
        var deleteUrl = '/wp-json/vtheme/v1/stars/' + post_id + "?_wpnonce=" + wpnonce; // 修复API URL，添加vtheme前缀
        
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
            notyf.error(responseJson.error);
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
            notyf.success('收藏成功');
          } else if(response.status == 401) {
            notyf.error('请登录后重试');
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
   * 登录/注册/找回密码对话框
   */


  function loginDialog() {
    initLoginDialog();
  }

  function initLoginDialog() {
    const dialogElement = document.querySelector('.login-register-dialog');
    if(!dialogElement) return;
    
    // 注册对话框功能
    dialogTools.registerDialog(dialogElement);
    
    // 打开登录对话框
    const loginBtns = document.querySelectorAll('.open-login-dialog');
    if(loginBtns.length > 0) {
      loginBtns.forEach(function(loginBtn) {
        loginBtn.addEventListener('click', function(e) {
          e.preventDefault();
          dialogElement.showModal();
        });
      });
    }
    
    // Tab切换功能
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        // 移除所有活动状态
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // 添加当前活动状态
        this.classList.add('active');
        document.getElementById(`tab-${this.dataset.tab}`).classList.add('active');
        
        // 更新标题
        document.querySelector('.dialog-header .title').textContent = this.textContent;
        
        // 如果切换到找回密码标签，重置为第一步
        if(this.dataset.tab === 'forgot') {
          showForgotStep1();
        }
      });
    });
    
    // 登录表单提交
    const loginForm = document.getElementById('login-form');
    if(loginForm) {
      loginForm.addEventListener('submit', handleLoginFormSubmit);
    }
    
    // 注册表单提交
    const registerForm = document.getElementById('register-form');
    if(registerForm) {
      registerForm.addEventListener('submit', handleRegisterFormSubmit);
    }
    
    // 添加发送验证码功能
    const sendVerificationBtn = document.getElementById('send-verification-code');
    if(sendVerificationBtn) {
      sendVerificationBtn.addEventListener('click', handleSendVerificationCode);
    }
    
    // 找回密码第一步表单提交
    const forgotFormStep1 = document.getElementById('forgot-form-step1');
    if(forgotFormStep1) {
      forgotFormStep1.addEventListener('submit', handleForgotStep1Submit);
    }
    
    // 找回密码第二步表单提交
    const forgotFormStep2 = document.getElementById('forgot-form-step2');
    if(forgotFormStep2) {
      forgotFormStep2.addEventListener('submit', handleForgotStep2Submit);
    }
  }

  // 显示找回密码第一步
  function showForgotStep1() {
    document.getElementById('forgot-form-step1').style.display = 'block';
    document.getElementById('forgot-form-step2').style.display = 'none';
    document.getElementById('forgot-back').style.display = 'none';
  }

  // 显示找回密码第二步
  function showForgotStep2() {
    document.getElementById('forgot-form-step1').style.display = 'none';
    document.getElementById('forgot-form-step2').style.display = 'block';
    document.getElementById('forgot-back').style.display = 'block';
  }

  async function handleSendVerificationCode() {
    const emailInput = document.getElementById('register-email');
    const email = emailInput.value;
    
    // 验证邮箱格式
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
      showNotification('请输入有效的邮箱地址', 'error');
      return;
    }
    
    // 更改按钮状态
    const btn = this;
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = '发送中...';
    
    try {
      const response = await fetch(ajax_object.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=send_verification_code&email=${encodeURIComponent(email)}&security=${ajax_object.nonce}`
      });
      
      const result = await response.json();
      
      if(result.success) {
        showNotification('验证码已发送，请查收邮件', 'success');
        
        // 启动倒计时
        startCountdown(btn, originalText, 60);
      } else {
        showNotification(result.data || result.message || '验证码发送失败', 'error');
        btn.disabled = false;
        btn.textContent = originalText;
      }
    } catch(error) {
      showNotification('网络错误，请稍后重试', 'error');
      btn.disabled = false;
      btn.textContent = originalText;
    }
  }

  function startCountdown(button, originalText, seconds) {
    if(seconds <= 0) {
      button.disabled = false;
      button.textContent = originalText;
      return;
    }
    
    button.textContent = `${seconds}秒后重试`;
    setTimeout(() => {
      startCountdown(button, originalText, seconds - 1);
    }, 1000);
  }

  async function handleLoginFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const username = formData.get('username');
    const password = formData.get('password');
    
    // 简单验证
    if (!username || !password) {
      showNotification('请填写所有必填字段', 'error');
      return;
    }
    
    // 显示加载状态
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = '登录中...';
    submitBtn.disabled = true;
    
    try {
      const response = await fetch(ajax_object.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=login_user&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&security=${ajax_object.nonce}`
      });
      
      const result = await response.json();
      
      if(result.success) {
        showNotification('登录成功，正在跳转...', 'success');
        // 登录成功，刷新页面
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        showNotification(result.data || result.message || '登录失败，请重试', 'error');
      }
    } catch(error) {
      showNotification('网络错误，请稍后重试', 'error');
    } finally {
      // 恢复按钮状态
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  }

  async function handleRegisterFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const username = formData.get('username');
    const email = formData.get('email');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    const verificationCode = formData.get('verification_code');
    
    // 验证
    if (!username || !email || !password || !confirmPassword || !verificationCode) {
      showNotification('请填写所有必填字段', 'error');
      return;
    }
    
    if (password !== confirmPassword) {
      showNotification('两次输入的密码不一致', 'error');
      return;
    }
    
    if (password.length < 6) {
      showNotification('密码长度至少为6位', 'error');
      return;
    }
    
    // 验证邮箱格式
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showNotification('请输入有效的邮箱地址', 'error');
      return;
    }
    
    // 显示加载状态
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = '注册中...';
    submitBtn.disabled = true;
    
    try {
      const response = await fetch(ajax_object.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=register_user&username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&verification_code=${encodeURIComponent(verificationCode)}&security=${ajax_object.nonce}`
      });
      
      const result = await response.json();
      
      if(result.success) {
        showNotification('注册成功，正在自动登录...', 'success');
        // 注册成功，刷新页面以反映登录状态
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        showNotification(result.data || result.message || '注册失败，请重试', 'error');
      }
    } catch(error) {
      showNotification('网络错误，请稍后重试', 'error');
    } finally {
      // 恢复按钮状态
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  }

  async function handleForgotStep1Submit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const email = formData.get('email');
    
    if (!email) {
      showNotification('请输入邮箱地址', 'error');
      return;
    }
    
    // 验证邮箱格式
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showNotification('请输入有效的邮箱地址', 'error');
      return;
    }
    
    // 显示加载状态
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = '发送中...';
    submitBtn.disabled = true;
    
    try {
      const response = await fetch(ajax_object.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=forgot_password&email=${encodeURIComponent(email)}&security=${ajax_object.nonce}`
      });
      
      const result = await response.json();
      
      if(result.success) {
        showNotification('密码重置验证码已发送到您的邮箱', 'success');
        // 显示第二步表单
        showForgotStep2();
      } else {
        showNotification(result.data || result.message || '发送失败，请重试', 'error');
      }
    } catch(error) {
      showNotification('网络错误，请稍后重试', 'error');
    } finally {
      // 恢复按钮状态
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  }

  async function handleForgotStep2Submit(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const email = document.getElementById('forgot-email').value; // 从前一步获取邮箱
    const code = formData.get('code');
    const newPassword = formData.get('new_password');
    const confirmNewPassword = formData.get('confirm_new_password');
    
    // 验证
    if (!email || !code || !newPassword || !confirmNewPassword) {
      showNotification('请填写所有必填字段', 'error');
      return;
    }
    
    if (newPassword !== confirmNewPassword) {
      showNotification('两次输入的新密码不一致', 'error');
      return;
    }
    
    if (newPassword.length < 6) {
      showNotification('密码长度至少为6位', 'error');
      return;
    }
    
    // 显示加载状态
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = '重置中...';
    submitBtn.disabled = true;
    
    try {
      const response = await fetch(ajax_object.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=reset_password_with_code&email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}&new_password=${encodeURIComponent(newPassword)}&security=${ajax_object.nonce}`
      });
      
      const result = await response.json();
      
      if(result.success) {
        showNotification('密码重置成功，请使用新密码登录', 'success');
        // 返回第一步并切换到登录标签
        setTimeout(() => {
          showForgotStep1();
          document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
          document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
          document.querySelector('.tab-btn[data-tab="login"]').classList.add('active');
          document.getElementById('tab-login').classList.add('active');
          document.querySelector('.dialog-header .title').textContent = '用户登录';
        }, 1500);
      } else {
        showNotification(result.data || result.message || '重置失败，请重试', 'error');
      }
    } catch(error) {
      showNotification('网络错误，请稍后重试', 'error');
    } finally {
      // 恢复按钮状态
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  }

  function showNotification(message, type) {
    const notyf = new Notyf({
      duration: 3000,
      position: { x: 'center', y: 'top' }
    });

    if (typeof notyf !== 'undefined') {
      if (type === 'success') {
        notyf.success(message);
      } else {
        notyf.error(message);
      }
    } else {
      alert(message);
    }
  }

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

})();
