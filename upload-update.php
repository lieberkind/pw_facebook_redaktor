<?php
// First of all, get the brand ID.
$brand_id = $_POST['brand-id'];
echo $brand_id;

// If the update form has been submitted, insert the update to the database
if($_POST['update-content']) {

  // Get the brand name
  $brand_name = $_POST['brand-name'];

  // Create a new post object to be inserted to the database
  $new_update = array(
    'post_title'    => $brand_name . ': ' . $_POST['update-content'], // The title has the format: "<brandname>: <update-content>"
    'post_type'     => 'pw_update',
    'post_status'   => 'publish'
  );

  // Get the new update id
  $update_id = wp_insert_post($new_update);

  // Add meta data to the new update
  add_post_meta($update_id, 'pw_update_brand', $brand_id);
  add_post_meta($update_id, 'pw_update_update', $_POST['update-content']);
  add_post_meta($update_id, 'pw_update_link', $_POST['update-link']);

  // Upload the image. Update the meta box in the backend accordingly.
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

  echo 'I was called!';
} 

?>