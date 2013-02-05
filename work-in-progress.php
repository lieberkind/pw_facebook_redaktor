<?php
/*
Template Name: Work in Progress
*/
?>


<?php get_header(); ?>

<script type="text/javascript">

/* LIVE SEARCH */
jQuery(document).ready(function() {


  /* Connect the tableSort function to the keyup event */
  (function($) {
    $("#update-search").keyup(searchTable);
  })(jQuery);

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

});
</script>

<script type="text/javascript">
/*
* Add an update category
*/
function addUpdateTerm(e) {

  // Prevent the #-url to cause a to jump to top of page
  e.preventDefault();

  // Fetch the new update category name from the input field
  var new_category_name = jQuery("input[name=new_category]", "#edit-update-categories").val();

  // Make an ajax object
  var call = new AjaxObject({
    action    : "addUpdateTerm",
    post_data : { 
      term_name : new_category_name 
    }, 
    success   : function(res) {
      var response  = JSON.parse(res);
      var html      = '<input type="checkbox" name="update-category[]" value="' + response.term_id + '" checked>' + new_category_name + '<br>';
      jQuery('form', '#edit-update-categories').append(html);
    }
  });

  // Invoke the call
  call.call_ajax();
}


/*
* Gets all update category terms and builds a category list with the
* correct categories checked
*/
function getUpdateTerms(e) {
  e.preventDefault();

  // Fetch the update ID and the dialog to open
  var uid     = jQuery(this).parents('td').attr('id');
  var dialog  = jQuery("#edit-update-categories");

  // Make the call object
  var call = new AjaxObject({
    action    : "getUpdateTerms",
    post_data : {
      update_id : uid
    },
    success   : function(res) {
      response = JSON.parse(res);

      // The HTML string to build
      var html = '<input type="hidden" name="update_id" value="' + uid + '">';

      // Build the category list, and check the categories that the update is posted in
      for(obj in response) {
        var checked = '';
        
        if(response[obj].checked)
          checked = 'checked';

        html += '<input type="checkbox" name="update-category[]" value="' + response[obj].term_id +'" ' + checked + '>' + response[obj].name + '<br>';
      }

      jQuery('form', dialog).html(html);
      dialog.dialog('open');
    }
  });

  // Invoke the call
  call.call_ajax();
}

/*
* CATEGORY DIALOG
*/
var CATEGORYDIALOG = (function($) {
  var _updateID,
      _dialog
  ;

  // Get update category terms
  function _getUpdateTerms() {
    var call = new AjaxObject({
      action    : "getUpdateTerms",
      post_data : { update_id : _updateID },
      success   : function(res) { _successFunc(JSON.parse(res)); }
    });
    call.call_ajax();
  }

  // The function that runs on ajax success
  function _successFunc(data) {
    // The HTML string to build
    var html = '<input type="hidden" name="update_id" value="' + _updateID + '">';

    // Build the category list, and check the categories that the update is posted in
    for(obj in data) {
      var checked = '';

      if(data[obj].checked)
        checked = 'checked';

      html += '<input type="checkbox" name="update-category[]" value="' + data[obj].term_id +'" ' + checked + '>' + data[obj].name + '<br>';
    }

    $('form', _dialog).html(html);
  }

  var API = {};

  API.init = function(dialog) {
    _dialog = $(dialog);
  }

  API.open = function(e) {
    e.preventDefault();
    _updateID = $(this).parents('td').attr('id');

    _getUpdateTerms();

    _dialog.dialog('open');
  }

  API.insertTerm = function(e) {
    e.preventDefault();
  }

  return API;
}(jQuery));




/*
* DELETE DIALOG
*/ 
var DELETEDIALOG = (function() {

  // Private members
  var _dialog; 
  var _updateID;
  
  // Private functions
  function _deleteUpdate() {
    var call = new AjaxObject({
      action    : "deleteUpdate",
      post_data : {
        update_id : _updateID
      },
      success   : function(res) { _successFunc(res); }
    });
    call.call_ajax();
  }

  function _successFunc(res) {
    var response = JSON.parse(res);

    if(response > 0) {
      _dialog.dialog('close');
      jQuery("td#" + _updateID).parents("tr").fadeOut(function() {
        jQuery(this).remove();
      });
    } else {
      alert('Something went wrong, please try again.');
      _dialog.dialog('close');
    }
  }


  // Public API
  var API = {};

  API.init = function() {

    console.log("Init was called...");

    _dialog = jQuery("#delete-update-dialog");

    _dialog.dialog({
      autoOpen : false,
      title    : "Delete update?",
      modal    : true,
      buttons  : {
        "Cancel" : function() { _dialog.dialog('close')},
        "Delete" : function() { _deleteUpdate(); }
      }
    });
  };

  API.open = function(e) {
    e.preventDefault();

    _updateID = jQuery(this).parents('td').attr('id');
    
    _dialog
      .find('.delete-update-message')
      .html('Are you sure you want to delete the update with ID ' + _updateID)
    ;
    
    _dialog.dialog('open');
  };

  return API;

} ());



/*
* EDIT DIALOG
*/
var EDITDIALOG = (function() {
  var _updateID,
    _updateContent,
    _updateLink,
    _dialog
  ;

  function _editUpdate() {
    var _updateContent  = jQuery('textarea[name=update-content]', _dialog).val();
    var _updateLink     = jQuery('input[name=update-link]', _dialog).val();

    var call = new AjaxObject({
      action    : "editUpdate",
      post_data : {
        update_id       : _updateID,
        update_content  : _updateContent, // These have to be different
        update_link     : _updateLink     // These have to be different - fetch from form
      },
      success   : function(res) { _successFunc(res) }
    });

    call.call_ajax();
  }

  function _successFunc(res) {
    var response = JSON.parse(res);

    if(response.is_content_updated || response.is_link_updated) {
      _dialog.dialog('close');
      jQuery("td#" + _updateID).siblings(".update-content").text(response.updated_content);
      jQuery("td#" + _updateID).siblings(".update-extra").children(".update-link").val(response.updated_link);
    } else {
      _dialog.dialog('close');
      alert('Something went wrong. Please try again.');
    }
  }

  var API = {};

  API.init = function() {
    _dialog = jQuery("#edit-update-dialog");
    _dialog
      .dialog({
        autoOpen  : false,
        modal     : true,
        title     : "Edit update",
        buttons   : {
            "Save"    : function() { _editUpdate(); },
            "Cancel"  : function() { _dialog.dialog('close'); }
        }
      })
    ;
  }

  API.open = function(e) {
      e.preventDefault();

      _updateID       = jQuery(this).parents('td').attr('id');
      _updateContent  = jQuery(this).parent().siblings('.update-content').text();
      _updateLink     = jQuery(this).parent().siblings('.update-extra').children('.update-link').attr('value');

      var form_html = "";
      form_html += '<input type="hidden" name="update-id" value="' + _updateID + '">';
      form_html += '<textarea name="update-content">' + _updateContent + '</textarea>';
      form_html += '<input type="text" name="update-link" value="' + _updateLink + '">';

      _dialog.find('form').html(form_html);
      _dialog.dialog('open');
  }

  return API;


} ());




jQuery(document).ready(function() {

  // Initialize modules
  DELETEDIALOG.init();
  EDITDIALOG.init();
  CATEGORYDIALOG.init("#edit-update-categories");

  // Bind events
  jQuery("#add-new-category").click(addUpdateTerm);
  jQuery(".edit-update-categories").click(CATEGORYDIALOG.open);
  jQuery(".delete-update").click(DELETEDIALOG.open);
  jQuery(".edit-update").click(EDITDIALOG.open);

  // Initialize fancybox
  // jQuery(".fancybox").fancybox({
  //   ajax: {
  //     type    : 'POST',
  //     url     : 'wp-admin/admin-ajax.php',
  //     data    : post_data_string,
  //     success : function(data) { 
  //       alert();
  //     }      
  //   }
  // });

  jQuery(".fancybox").fancybox();


  jQuery('#edit-update-categories').dialog({
    autoOpen: false,
    modal: true,
    title: "Edit update categories",
    buttons: {
      Save : function() {

        var update_id = jQuery("input[name=update_id]", this).attr('value');
        var new_terms = '';

        jQuery("input:checked", this).each(function() {
          new_terms += jQuery(this).attr('value') + ",";
        });

        setUpdateTerms(update_id, new_terms, this);
      }
    }
  });

});

</script>

<?php 



  //Get updates with related content
  $query_string =
  'SELECT 
    posts.ID AS ID,
    posts.post_author AS update_author,
    postmeta.meta_value AS update_content,
    postmeta_2.meta_value AS update_link,
    postmeta_3.meta_value AS update_brand_id
  FROM
    wp_posts AS posts, 
    wp_postmeta AS postmeta,
    wp_postmeta AS postmeta_2,
    wp_postmeta AS postmeta_3
  WHERE posts.ID = postmeta.post_id
    AND posts.post_type = "pw_update"
    AND posts.ID = postmeta_2.post_id
    AND postmeta_2.meta_key = "pw_update_link"
    AND posts.ID = postmeta_3.post_id
    AND postmeta_3.meta_key = "pw_update_brand"
    AND posts.post_status = "draft"
    AND postmeta.meta_key = "pw_update_update"
  ORDER BY post_date DESC';
  
  $updates = $wpdb->get_results($query_string);

  $q_args = array(
    'post_type'       => 'pw_update',
    'post_status'     => 'draft',
    'posts_per_page'  => -1
  );

  $my_q = new WP_Query($q_args);


  // Fetch all the brands for sorting options
  $brands = get_posts(array(
    'post_type'   => 'pw_brand',
    'numberposts' => -1,
    'orderby'     => 'name',
    'order'       => 'ASC'
  ));

  // Fetch all the update categories
  $update_cats = get_terms('pw_update-update-categories', array(
    'hide_empty'  => 0,
    'fields'      => 'names',
    'orderby'     => 'name',
    'order'       => 'ASC'
  ));

  //echo '<pre style="color: black;">';
  //  print_r($updates);
  //echo '</pre>';

  print_r($my_q->posts);
?>

<header class="work-in-progress-header">
  <h2 class="page-title">Work in Progress</h2> 
  <div class="sorting-options">
    <div class="update-search-container">
      <input type="text" class="update-search" id="update-search" placeholder="Search for update text or ID">
    </div>
    <form class="update-sorting-options" id="update-sorting-options">

      <select class="update-category-select" name="cat-name">
        <option value="">All categories</option>
        <?php foreach ($update_cats as $key => $uc): ?>
          <option value="<?= $uc ?>"><?= $uc ?></option>
        <?php endforeach ?>
      </select>

      <select class="update-brand-select" name="brand-id">
        <option value="">All brands</option>
        <?php foreach($brands as $key => $brand): ?>
          <?php if(pw_currentusercan("read", "update", $brand->ID)): ?>
            <option value="<?= $brand->post_title; ?>"><?= $brand->post_title; ?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
      
      <input type="button" value="Sort" id="sort-updates">
    </form>
  </div>
</header>

<section class="work-in-progress-updates">
  <table class="update-list list-view" id="update-list">
    <tr class="list-header">
      <th class="update-id-header">ID</th>
      <th class="update-content-header">Update</th>
      <th class="update-note-header">Note</th>
      <th class="update-extra-header">Image</th>
      <th class="update-author-header">Author</th>
      <th class="update-brand-header">Brand</th>
      <th class="update-actions-header">Actions</th>
    </tr>

    <?php foreach($updates as $key => $update): ?>

      <?php
        // If the user can't access the brand, jump over this iteration
        if(!pw_currentusercan("read", "update", $update->update_brand_id)) {
          continue;
        }

        // Get update categories
        $update_categories = wp_get_post_terms($update->ID, 'pw_update-update-categories', array('fields' => 'names'));

        // Get the attached image, if there is one.
        $attachment_id = get_post_meta($update->ID, 'pw_update_image', true);
        $attachment_url = wp_get_attachment_url($attachment_id);
      ?>

      <tr class="row">
        <td class="update-id searchable"><?= $update->ID; ?></td>
        <td class="update-content searchable"><?= $update->update_content; ?></td>
        <td class="update-note">
          <?php if ($update->update_link): ?>
            <?= $update->update_link; ?>
          <?php else: ?>
            <span class="no-content">No note</span>
          <?php endif; ?>
        </td>
        <td class="update-extra">
          <input type="hidden" value="<?= $update->update_link; ?>" class="update-link">
          <?php if($attachment_url): ?>
            <a href="<?= get_permalink($update->ID); ?>#status-update" class="fancybox status-update-link">
              <img src="<?= $attachment_url; ?>" width="50px">
            </a>
          <?php else: ?>
            <a href="<?= get_permalink($update->ID); ?>#status-update" class="fancybox status-update-link">
              <span class="no-content">No image</span>
            </a>
          <?php endif; ?>

        </td>
        <td class="update-author"><?= the_author_meta('user_nicename', $update->update_author); ?></td>
        <td class="update-brand"><?= get_the_title($update->update_brand_id); ?></td>
        <td class="update-categories" style="display: none;">
          <?php foreach ($update_categories as $key => $uc): ?>
            <?= $uc; ?>
          <?php endforeach ?>
        </td>
        <td class="update-actions" id="<?= $update->ID; ?>">
          <a href="#" class="edit-update">Edit</a>
          <a href="#" class="delete-update">Delete</a>
          <a href="#" class="edit-update-categories">Category</a>
          <a href="#" class="update-to-pending" onclick="updateToPending('<?= bloginfo('url'); ?>', this)">Send</a>
        </td>
      </tr>      
    <?php endforeach; ?>
  </table>



  <!-- 
    The display should be "none" by default to prevent a brief flash.
    Whether or not this should be set in the CSS is a good question 
  -->
  <div id="edit-update-dialog" style="display: none">
    <form>
    </form>
  </div>


  <!-- The dialog that opens when the Category action button is pressed -->
  <div id="edit-update-categories" style="display: none">
    <form>
    </form>
    <input type="text" name="new_category">
    <a href="#" id="add-new-category">Add new category</a>
  </div>

  <div id="delete-update-dialog" style="display: none">
    <p class="delete-update-message"></p>
  </div>








</section>
<?php get_footer(); ?>
