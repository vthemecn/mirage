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

  let flag = localStorage.getItem('miragev');
  let time = getTime();
  if(flag == time){ return; }
  localStorage.setItem('miragev', time);

  const data = { domain:window.location.host, project:'miragev' };

  fetch('https://vtheme.cn/wp-json/wm/v1/info', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json'},
      body: JSON.stringify(data),
  })
  .then(response => response.json())
  .then(responseJson => {
      // console.log(responseJson);
  })
  .catch(error => {
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