<?php
/*
Template Name: Create Persona
*/
?>

<?php 
  if(!pw_currentusercan("create", "person")) {
    die("You don't have permission to create personas");
  }

  if(isset($_POST['persona-name']))
    $persona_name = $_POST['persona-name'];
  else
    $persona_name = null;

  // If some data has been submitted...
  if($persona_name) {
    $persona['name']      = $_POST['persona-name'];
    $persona['updates']   = $_POST['create-persona-status-update'];
    $persona['brand-id']  = $_POST['create-persona-brand'];

    $persona_id = wp_insert_post(array(
      'post_title'  => $persona['name'],
      'post_type'   => 'pw_person'
    ));

    // Insert the relevant meta data
    add_post_meta($persona_id, 'pw_person_update', $persona['updates']);
    add_post_meta($persona_id, 'pw_person_brand', $persona['brand-id']);



    // Add I say words
    if(isset($_POST['i-say-words'])) {
      $i_say_words = explode(",", $_POST['i-say-words']);
      pw_add_terms($persona_id, $i_say_words, 'pw_person-i-say-words');
    }

    // Add I do words
    if(isset($_POST['i-do-words'])) {
      $i_do_words = explode(",", $_POST['i-do-words']);
      pw_add_terms($persona_id, $i_do_words, 'pw_person-i-do-words');
    }

    // Add I think words
    if(isset($_POST['i-think-words'])) {
      $i_think_words = explode(",", $_POST['i-think-words']);
      pw_add_terms($persona_id, $i_think_words, 'pw_person-i-think-words');
    }

    // Add I own words
    if(isset($_POST['i-own-words'])) {
      $i_own_words = explode(",", $_POST['i-own-words']);
      pw_add_terms($persona_id, $i_own_words, 'pw_person-i-owns-words');
    }    

    // Insert terms
    // $i_say_words    = $_POST['i-say-words'];
    // $i_do_words     = $_POST['i-do-words'];
    // $i_think_words  = $_POST['i-think-words'];
    // $i_own_words    = $_POST['i-own-words'];

    // wp_set_object_terms($persona_id, $i_say_words, 'pw_person-i-say-words', true);
    // wp_set_object_terms($persona_id, $i_do_words, 'pw_person-i-do-words', true);
    // wp_set_object_terms($persona_id, $i_think_words, 'pw_person-i-think-words', true);
    // wp_set_object_terms($persona_id, $i_own_words, 'pw_person-i-own-words', true);

    // This is the file loop. Adds the personas image
    if($_FILES) {
      echo 'Entering file loop!';
      foreach ($_FILES as $file => $array) {
        $newupload = insert_attachment($file, $persona_id);

        // If insert_attachment failed, it returns false.
        if($newupload) {
          add_post_meta($persona_id, 'pw_person_image', $newupload);
        }
      }
    }

    die('Yeah, die!!');
  }
?>

<?php 
  
  // Fetch needed data: all brands
  $brand_posts = get_posts(array(
    'numberposts' => -1,
    'post_type'   => 'pw_brand',
    'orderby'     => 'title',
    'order'       => 'ASC'
    )
  );

  // Get the current user
  $current_user = wp_get_current_user();

  // If the current user can access a brand,
  // add it to the brand list
  if(count($brand_posts) > 0) {
    foreach($brand_posts as $key => $brand) {
      if(pw_currentusercan("read", "brand", $brand->ID)) {
        $brands[$brand->ID] = $brand->post_title;
      }
    }
  }
?>

<?php get_header(); ?>

<script type="text/javascript">
  jQuery(document).ready(function() {

    // When "tilføj ny statusupdate" is clicked, a new textarea should appear
    jQuery("#create-new-status-update").click(function() {
      var $formElement = jQuery(document.createElement('textarea'));
      $formElement.attr('name', 'create-persona-status-update[]');
      $formElement.addClass('create-persona-status-update');
      jQuery("#create-persona-status-updates").append($formElement);
    });
  });
</script>


<div class="create-persona-container">
  <h2 class="page-title">Create Persona</h2>
  <form method="POST" class="create-persona-form" id="create-persona-form" enctype="multipart/form-data" target="create-persona-target">

    <div class="create-persona-form-col-1">
      <div class="create-persona-persona-information">
        <label for="persona-name">Navn på Persona</label>
        <input type="text" name="persona-name" id="persona-name">
        <input type="file" name="persona-image">
      </div>
      <h3 class="create-persona-statusupdates-headline">Statusopdateringer</h3>
      <fieldset id="create-persona-status-updates">
        <textarea class="create-persona-status-update[]" name="create-persona-status-update[]"></textarea>
      </fieldset>
      <a href="#" class="create-new-status-update" id="create-new-status-update">Tilføj ny statusupdate</a>
    </div>

    <div class="create-persona-form-col-2">
      <label for="create-persona-brand">Vælg brand</label>
      <select name="create-persona-brand" class="create-persona-brand" id="create-persona-brand">
        <?php foreach($brands as $key => $brand): ?>
          <option value="<?= $key; ?>"><?= $brand; ?></option>
        <?php endforeach; ?>
      </select>


      <!-- <input id="i-say-words-list"> -->
      <label for="i-say-words">I say words</label>
      <textarea type="text" id="i-say-words" name="i-say-words"></textarea>

      <label for="i-do-words">I do words</label>
      <textarea type="text" id="i-do-words" name="i-do-words"></textarea>

      <label for="i-think-words">I think words</label>
      <textarea type="text" id="i-think-words" name="i-think-words"></textarea>

      <label for="i-own-words">I own words</label>
      <textarea type="text" id="i-own-words" name="i-own-words"></textarea>


    </div>
    <div class="create-persona-form-col-3">
      <label>Inspiration images</label>
      <input type="submit" value="Gem persona">
    </div>
  </form>
  <iframe id="create_persona_target" name="create-persona-target" style=""></iframe>
</div>












<?php get_footer(); ?>