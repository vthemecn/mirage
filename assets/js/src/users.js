import toast from "./toast";

export default function init (){
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
  }
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

