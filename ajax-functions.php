<?php

function addUpdateTerm() {
  $term_name = $_POST['term_name'];

  $response = wp_insert_term($term_name, 'pw_update-update-categories');

  echo json_encode($response);

  die();
}
add_action('wp_ajax_addUpdateTerm', 'addUpdateTerm');

function getBrandDna() {
  $brand_id = $_POST['brandId'];

  $pure_terms     = wp_get_post_terms($brand_id, 'pw_brand-pure-words', array('fields' => 'names'));
  $social_terms   = wp_get_post_terms($brand_id, 'pw_brand-social-words', array('fields' => 'names'));
  $traffic_terms  = wp_get_post_terms($brand_id, 'pw_brand-traffic-words', array('fields' => 'names'));
  $topical_terms  = wp_get_post_terms($brand_id, 'pw_brand-topcial-words', array('fields' => 'names'));

  $res['pure']    = $pure_terms;
  $res['social']  = $social_terms;
  $res['traffic'] = $traffic_terms;
  $res['topical'] = $topical_terms;

  echo json_encode($res);
  die();
}
add_action('wp_ajax_getBrandDna', 'getBrandDna');


function setUpdateTerms() {
  $update_id = $_POST['update_id'];
  $new_terms = $_POST['update_terms'];

  $response = wp_set_post_terms($update_id, $new_terms, 'pw_update-update-categories', false);

  echo json_encode($response);

  die();
}
add_action('wp_ajax_setUpdateTerms', 'setUpdateTerms');

function getUpdateTerms() {

  // Fetch the updates ID
  $update_id = $_POST['update_id'];

  // Fetch all terms from update categories
  $terms = get_terms('pw_update-update-categories', array('hide_empty' => 0));

  // Fetch the updates term ids
  $update_terms = wp_get_post_terms($update_id, 'pw_update-update-categories', array('fields' => 'ids'));

  foreach ($terms as $key => $term) {
    $terms[$key]->checked = in_array($term->term_id, $update_terms);
  }

  echo json_encode($terms);

  die();
}
add_action('wp_ajax_getUpdateTerms', 'getUpdateTerms');

function editUpdate() {
  $update = array();
  $update['ID']             = $_POST['update_id'];
  $update['update_content'] = $_POST['update_content'];
  $update['update_link']    = $_POST['update_link'];

  //$update_response = wp_update_post($update);

  $update_response = array();

  $update_response['is_content_updated']  = update_post_meta($update['ID'], 'pw_update_update', $update['update_content']);
  $update_response['updated_content']     = $update['update_content'];
  $update_response['is_link_updated']     = update_post_meta($update['ID'], 'pw_update_link', $update['update_link']);
  $update_response['updated_link']        = $update['update_link'];

  echo json_encode($update_response);

  die();
}
add_action('wp_ajax_editUpdate', 'editUpdate');

function deleteUpdate() {

  $result = wp_delete_post($_POST['update_id']);
  echo $_POST['update_id'];
  die();
}
add_action('wp_ajax_deleteUpdate', 'deleteUpdate');

function updateToPending() {
  $update_id = $_POST['update_id'];
 
  $update = array();
  $update['ID']           = $update_id;
  $update['post_status']  = 'pending';

  $update_response = wp_update_post($update);

  $response = array();

  if($update_response == $update_id) {
    $response['status'] = 1; 
    $response['msg']    = "Post was successfully sent to review";
  } else {
    $response['status'] = 0;
    $response['msg']    = "Something went wrong. Try again!";
  }

  echo json_encode($response);

  die();
}
add_action('wp_ajax_updateToPending', 'updateToPending');

function updateToPublish() {
  $update_id = $_POST['update_id'];
 
  $update = array();
  $update['ID']           = $update_id;
  $update['post_status']  = 'publish';

  $update_response = wp_update_post($update);

  $response = array();

  if($update_response == $update_id) {
    $response['status'] = 1; 
    $response['msg']    = "Post was successfully published";
  } else {
    $response['status'] = 0;
    $response['msg']    = "Something went wrong. Try again!";
  }

  echo json_encode($response);

  die();
}
add_action('wp_ajax_updateToPublish', 'updateToPublish');

function updateToDraft() {
  $update_id = $_POST['update_id'];
 
  $update = array();
  $update['ID']           = $update_id;
  $update['post_status']  = 'draft';

  $update_response = wp_update_post($update);

  $response = array();

  if($update_response == $update_id) {
    $response['status'] = 1; 
    $response['msg']    = "Post was denied";
  } else {
    $response['status'] = 0;
    $response['msg']    = "Something went wrong. Try again!";
  }

  echo json_encode($response);

  die();
}
add_action('wp_ajax_updateToDraft', 'updateToDraft');


function postUpdate() {
  
  // First of all, get the brand ID.
  $brand_id = $_POST['brand-id'];

  // Get the brand object
  $brand_object = get_post($brand_id);

  // If the update form has been submitted, insert the update to the database
  if($_POST['update-content']) {

    // Get the brand name
    $brand_name = $brand_object->post_title;

    // Create a new post object to be inserted to the database
    $new_update = array(
      'post_title'    => $_POST['update-content'], // The title has the format: "<brandname>: <update-content>"
      'post_type'     => 'pw_update',
      'post_status'   => 'publish' // Should maybe be draft or pending instead
    );

    // Get the new update id
    $update_id = wp_insert_post($new_update);

    // Add meta data to the new update
    add_post_meta($update_id, 'pw_update_brand', $brand_id);
    add_post_meta($update_id, 'pw_update_update', $_POST['update-content']);
    add_post_meta($update_id, 'pw_update_link', $_POST['update-link']);

    die('Update published!');
  }
}
add_action('wp_ajax_postUpdate', 'postUpdate');


function sortUpdates() {
  $brand_id = $_POST['brand-id'];

  // Update category ID
  $uc_id = $_POST['update-category'];

  if(strcmp($uc_id, 'all') == 0) {
    $term_id = '';
  } else {
    $term_id = $uc_id;
  }


  $update_args = array(
    'post_type'   => 'pw_update',
    'meta_key'    => 'pw_update_brand',
    'meta_value'  => $brand_id,
    'post_status' => 'draft',
    'tax_query'   => array(
      array(
        'taxonomy'  => 'pw_update-update-categories',
        'field'     => 'id',
        'terms'     => $term_id
      )
    )
  );

  $updates = new WP_Query($update_args);

  $updates = get_posts(array(
    'numberposts' => -1,
    'category'    => '',
    'post_type'   => 'pw_update',
    'order'       => 'ASC',
    'post_status' => 'draft',
    'meta_key'    => 'pw_update_brand',
    'meta_value'  => $brand_id 
  ));

  foreach ($updates as $key => $update) {
    // Get the attached image, if there is one.
    $attachment_id = get_post_meta($update->ID, 'pw_update_image', true);
    $attachment_url = wp_get_attachment_url($attachment_id);


    // Set the updates content
    $updates[$key]->update_content = get_post_meta($update->ID, 'pw_update_update', true);

    // Set the updates image
    $updates[$key]->attachment_url = $attachment_url;
  }

  echo json_encode($updates);
  die(); // Very important!
}
add_action('wp_ajax_sortUpdates', 'sortUpdates');


function getRandomPerson() {
  $brand_id = $_POST['brand_id'];

  // Fetch a random person that belongs to the brand
  // As this always returns 1 element, it's safe to use [0] at the end
  // of the get_posts function call.
  $person = get_posts(array(
    'numberposts' => 1,
    'orderby'     => 'rand',
    'meta_key'    => 'pw_person_brand',
    'meta_value'  => $brand_id,
    'post_type'   => 'pw_person',
  ))[0];


  $result['id'] = $person->ID;
  $result['name'] = $person->post_title;

  // Get person image
  $person_image_id = get_post_meta($person->ID, 'pw_person_image', true);
  $result['image'] = wp_get_attachment_url($person_image_id);

  // Get person updates
  $person_updates = get_post_meta($person->ID, 'pw_person_update', true);
  $result['updates'] = get_random_elements($person_updates, 3);

  echo json_encode($result);
  die();
}
add_action('wp_ajax_getRandomPerson', 'getRandomPerson');

function getRandomInspiration() {
  $person_id = $_POST['person_id'];

  // Fetch all inspiration from the person
  $person_i_do_words = wp_get_object_terms($person_id, 'pw_person-i-do-words', array('fields' => 'names'));
  $person_i_say_words = wp_get_object_terms($person_id, 'pw_person-i-say-words', array('fields' => 'names'));
  $person_i_think_words = wp_get_object_terms($person_id, 'pw_person-i-think-words', array('fields' => 'names'));
  $person_i_owns_words = wp_get_object_terms($person_id, 'pw_person-i-owns-words', array('fields' => 'names'));

  // Get a random element frow each "collection"
  $result['i_do_word'] = get_random_elements($person_i_do_words, 1);
  $result['i_say_word'] = get_random_elements($person_i_say_words, 1);
  $result['i_think_word'] = get_random_elements($person_i_think_words, 1);
  $result['i_own_word'] = get_random_elements($person_i_owns_words, 1);

  // Get all the persons inspiration images
  $person_images = get_post_meta($person_id, 'pw_person_images', false);

  // Get a random inspiration image
  $person_image = get_random_elements($person_images, 1);

  // Get the url of the image
  $result['image_url'] = wp_get_attachment_url($person_image);

  echo json_encode($result);
  die();
}
add_action('wp_ajax_getRandomInspiration', 'getRandomInspiration');

/**
* Insert a new update in the database
**/
function insertUpdate() {
  
}


function ohyeah() {
  $brand_id = $_POST['brand-id'];

  // Get the brand name
  $brand_name = $brand_object->post_title;

  // Create a new post object to be inserted to the database
  $new_update = array(
    'post_title'    => $brand_name . ': ' . $_POST['update-content'], // The title has the format: "<brandname>: <update-content>"
    'post_type'     => 'pw_update',
    'post_status'   => 'draft'
  );

  // Get the new update id
  $update_id = wp_insert_post($new_update);

  echo 'Hej?';

  // Add meta data to the new update
  add_post_meta($update_id, 'pw_update_brand', $brand_id);
  add_post_meta($update_id, 'pw_update_update', $_POST['update-content']);
  add_post_meta($update_id, 'pw_update_link', $_POST['update-link']);

  // Upload the image. Update the meta box in the backend accordingly.
  print_r($_FILES);
  if($_FILES) {
    echo 'Entering file loop!';
    foreach ($_FILES as $file => $array) {
      $newupload = insert_attachment($file, $update_id);

      // If insert_attachment failed, it returns false.
      if($newupload) {
        add_post_meta($update_id, 'pw_update_image', $newupload);
      }
    }
  }
  echo json_encode('Check it out now...');
  //echo 'I was called!';
  die();
}
add_action('wp_ajax_ohyeah', 'ohyeah');










?>