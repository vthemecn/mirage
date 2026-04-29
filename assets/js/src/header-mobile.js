import toast from "./toast";

export default function () {
  mobileMenu();
  mobileSearch();
  initHeaderScrollBehavior();
}


/**
 * 移动端向下滑动时隐藏header，向上滑动时显示header
 * 特点：
 * 1. requestAnimationFrame: scroll 把操作频率限制在屏幕刷新率内，避免掉帧，让动画更流畅
 * 2. { passive: true } 不会阻止这个事件的默认行为（比如阻止滚动）”。这让浏览器能更早地处理滚动，显著提升移动端滚动的流畅度
 * 3. ticking 标志位: 节流机制。它确保在上一帧动画执行完之前，不会重复请求下一帧，减少计算
 */
function initHeaderScrollBehavior() {
  const header = document.querySelector('.header.mobile');
  if (!header) return;

  let lastScrollY = window.scrollY;
  let ticking = false;

  function updateHeader() {
    const currentScrollY = window.scrollY;
    
    // 向下滑动且滚动距离超过50px时隐藏
    if (currentScrollY > lastScrollY && currentScrollY > 50) {
      header.classList.add('header-hidden');
    } 
    // 向上滑动时显示
    else if (currentScrollY < lastScrollY) {
      header.classList.remove('header-hidden');
    }
    
    lastScrollY = currentScrollY;
    ticking = false;
  }

  window.addEventListener('scroll', function() {
    if (!ticking) {
      window.requestAnimationFrame(updateHeader);
      ticking = true;
    }
  }, { passive: true });
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
  // console.log('mobileMenuCloseButton', mobileMenuCloseButton);

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
    }
  }

  searchForm.onsubmit = function () {
    if (searchInput.value == "") {
      toast.open({ title: "请输入关键词" });
      return false;
    }
  }

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


