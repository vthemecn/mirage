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
  if(!likeButtons.length) return;
  
  // 初始化点赞状态
  // await initializeLikeStatus(notyf);
  
  likeButtons.forEach(button => {
    button.addEventListener('click', async function() {
      console.log('click like button');
      
      var that = this;
      
      const objectId = ajax_object.post_id;
      if (!objectId) return;
 
      const isLiked = that.classList.contains('active');
      let actionType = isLiked ? 'unlike' : 'like';
      
      try {
        const response = await fetch(ajax_object.ajax_url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            action: 'like_action',
            object_id: objectId,
            action_type: actionType,
            nonce: ajax_object.nonce,
          })
        });
        
        const result = await response.json();
        console.log('Result: ', result);
        
        if (result.success) {
          // 更新UI
          if (actionType === 'like') {
            that.classList.add('active');
            // 更新按钮文本显示
            const spanElement = that.querySelector('span');
            spanElement.textContent = '取消点赞';
          } else {
            that.classList.remove('active');
            // 更新按钮文本显示
            const spanElement = that.querySelector('span');
            if (spanElement) {
              spanElement.textContent = '点赞';
            }
          }
          
          // 更新点赞数
          const numberElement = that.querySelector('.number');
          if (numberElement) {
            numberElement.textContent = result.data.like_count || '';
          }
          
          notyf.success(result.data.message);
        } else {
          notyf.error(result.data.message || '操作失败');
        }
      } catch (error) {
        console.error('点赞操作失败:', error);
        notyf.error('网络错误，请重试');
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
    var url = ajax_object.post_url;
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





