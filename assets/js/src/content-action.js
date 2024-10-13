/**
 * 点赞，收藏，评论
 */

import toast from "./toast";
import axios from './axios';

import dialogTools from './dialog.js';


export default function () {
  likeInit();
  starInit();
  sharePosterInit();
  coinInit();
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
      
      if( this.classList.contains('active') ){
        await axios.request({ method: 'DELETE', url: deleteUrl, data: {'type':'like'} })
        .then(function (response) {
          if(response.status == 204){
            that.classList.remove('active');
            that.querySelector('.number').innerText = --that.querySelector('.number').innerText;
          }
        });
      } else {
        var data = {};
        data.object_id = document.querySelector("input[name='post_id']").value;
        data.type = 'like';
        
        await axios.request({
          method: 'post',
          url: addUrl,
          data: JSON.stringify(data)
        })
        .then(function (response) {
          console.log(response);
          if(response.status == 201){
            that.classList.add('active');
            that.querySelector('.number').innerText = response.data.counter;
          }
        });
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
      var that = this;
      
      var wpnonce = document.querySelector("input[name='wp_create_nonce']").value;
      var post_id = document.querySelector("input[name='post_id']").value;
      
      var addUrl = '/wp-json/vtheme/v1/stars' + "?_wpnonce=" + wpnonce;
      var deleteUrl = '/wp-json/vtheme/v1/stars/' + post_id + "?_wpnonce=" + wpnonce;
      
      if( this.classList.contains('active') ){
        await axios.request({ method: 'DELETE', url: deleteUrl, data: {'type':'star'} })
        .then(function (response) {
          if(response.status == 204){
            that.classList.remove('active');
            that.querySelector('.number').innerText = --that.querySelector('.number').innerText;
          }
        });
      } else {
        var data = {};
        data.object_id = document.querySelector("input[name='post_id']").value;
        data.type = 'star';
        
        await axios.request({
          method: 'post',
          url: addUrl,
          data: JSON.stringify(data)
        })
        .then(function (response) {
          console.log(response);
          if(response.status == 201){
            that.classList.add('active');
            that.querySelector('.number').innerText = response.data.counter;
          }
        });
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





