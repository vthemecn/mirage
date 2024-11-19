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
