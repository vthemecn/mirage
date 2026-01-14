/**
 * 文章页
 */



/**
 * 点赞，收藏，评论
 */

import {getCookie, setCookie} from './utils.js';
import dialogTools from './dialog.js';

export default function () {
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

      var currentUser
      
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
        }else{
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
      }else{
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
      // 未登录用户，弹出登录框
      let currentUserId = document.querySelector('input[name="current_user_id"]').value;
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
        }else{
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
    var qrcode = new QRCode(qrcodeSelector, {
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





