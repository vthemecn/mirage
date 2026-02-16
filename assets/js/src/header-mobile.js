import toast from "./toast";

export default function () {
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


