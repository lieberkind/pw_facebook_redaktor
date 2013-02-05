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


function searchTable() {
  var filter = jQuery(this).val(), count = 0;

  var filter_regexp = new RegExp(filter, "i");

  jQuery("#update-list .update-id").each(function() {

    var $self = jQuery(this);

    // If the list item does not contain the text phrase fade it out
    if ($self.text().search(filter_regexp) < 0) {
      if($self.siblings(".update-content").text().search(filter_regexp) < 0) {
        $self.parents("tr").hide();
      } else {
        $self.parents("tr").show();
      }
    } else {
      $self.parents("tr").show();
    }

  });
}

function sortTable() {
  (function($) {
    // This could be improved by matching on brand-id instead of name.
    // To realize this, a custom data-attribute could be added on every
    // row of the update table, and the select-box holding the brands
    // could have brand id's as values instead of names
    $("#sort-updates").click(function() {
      var form_data = $('#update-sorting-options').serializeArray();
      var brand_cat = form_data[0]['value'];
      var brand_name = form_data[1]['value'];

      $("#update-list .row").each(function() {
        var $cell = $(this);
        var $brand = $('.update-brand', $cell);
        var $update_cats = $('.update-categories', $cell);

        var hide = ($update_cats.text().search(brand_cat) < 0);
        var hide2 = ($brand.text().search(brand_name) < 0);

        if(hide || hide2) {
          $cell.hide();
        } else {
          $cell.show();
        }
      });

    });
  })(jQuery);
}