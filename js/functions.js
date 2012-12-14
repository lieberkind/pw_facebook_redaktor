function showSystemMessage(status, message) {

  var $message_dialog = jQuery(".system-message");

  // Check if the system message exists
  // TODO: If it does, make sure that the error/success 
  // class is removed before it is added
  if($message_dialog.length > 0) {
    if(status === 'success') {
      $message_dialog.addClass('success');
    }
    if(status === 'error') {
      $message_dialog.addClass('error');
    }

    jQuery(".message", $message_dialog).text(message);
  } else {
    var s = '<p class="message">' + message + '</p>';
    $message_dialog = document.createElement('div');
    $message_dialog.innerHTML = s;
    jQuery($message_dialog).addClass("system-message");
    jQuery($message_dialog).addClass(status);
    jQuery('body').prepend($message_dialog);
  }

  jQuery($message_dialog).slideDown().delay(3000).slideUp();
}