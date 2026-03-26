/**
 * 头像上传
 * /wp-admin/profile.php
 */
jQuery(document).ready(function() {
  var _orig_send_attachment = wp.media.editor.send.attachment;

  jQuery('.vt-avatar').click(function() {
    var button = jQuery(this);

    wp.media.editor.send.attachment = function(props, attachment) {
        jQuery('#vt_avatar_id').val(attachment.id);
        jQuery('.user-profile-picture .avatar').attr('src', attachment.url);

        wp.media.editor.send.attachment = _orig_send_attachment;
    }

    wp.media.editor.open();
    return false;
  });
});


/**
 * 主题版本检查
 */
function infoInit(){
  var logoLink = document.querySelector('a[copyright]');
  if(!logoLink) return;

  let flag = localStorage.getItem('mirage');
  let time = getTime();
  if(flag == time){ return; }
  localStorage.setItem('mirage', time);

  // 从全局变量获取主题版本等信息
  const data = { 
    domain: window.location.host, 
    project: 'mirage',
    version: vt_admin_data ? vt_admin_data.theme_version : 'unknown'
  };

  // 创建 AbortController 用于控制请求超时
  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 3000); // 3 秒超时

  fetch('https://vtheme.cn/wp-json/wm/v1/info', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json'},
      body: JSON.stringify(data),
      signal: controller.signal // 传入 abort signal
  })
  .then(response => response.json())
  .then(responseJson => {
      clearTimeout(timeoutId); // 清除超时定时器
      // console.log(responseJson);
  })
  .catch(error => {
      clearTimeout(timeoutId); // 清除超时定时器
      // console.error(error);
  });
}
infoInit();


function getTime() {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');
  return `${year}${month}${day}`;
}