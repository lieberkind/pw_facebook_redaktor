<?php
/*
* Template Name: Create Brand
*/
?>

<?php 
  get_header(); 
  if(!pw_currentusercan("create", "brand")) {
    die("You don't have permission to create brands");
  }

  if($_POST['brand-name']) 
  {
    $new_brand = array(
      'post_title'  => $_POST['brand-name'],
      'post_type'   => 'pw_brand',
      'post_status' => 'publish'
    );

    $new_brand_id = wp_insert_post($new_brand);

    if($_FILES) {
      echo 'Entering file loop!';
      foreach ($_FILES as $file => $array) {
        $newupload = insert_attachment($file, $new_brand_id);

        // If insert_attachment failed, it returns false.
        if($newupload) {
          add_post_meta($new_brand_id, 'pw_brand_logo', $newupload);
        }
      }
    }

    add_post_meta($new_brand_id, 'pw_brand_fb-page', $_POST['brand-facebook-page']);

    add_post_meta($new_brand_id, 'pw_brand_dna-1-title', $_POST['brand-dna-1']);
    add_post_meta($new_brand_id, 'pw_brand_dna-1-description', $_POST['brand-dna-1-description']);
    add_post_meta($new_brand_id, 'pw_brand_dna-2-title', $_POST['brand-dna-2']);
    add_post_meta($new_brand_id, 'pw_brand_dna-2-description', $_POST['brand-dna-2-description']);
    add_post_meta($new_brand_id, 'pw_brand_dna-3-title', $_POST['brand-dna-3']);
    add_post_meta($new_brand_id, 'pw_brand_dna-3-description', $_POST['brand-dna-3-description']);
    add_post_meta($new_brand_id, 'pw_brand_dna-4-title', $_POST['brand-dna-4']);
    add_post_meta($new_brand_id, 'pw_brand_dna-4-description', $_POST['brand-dna-4-description']);

    echo '<script type="text/javascript">top.showSystemMessage("success", "Brand was created!");</script>';
    die("I was called!");
  }
?>

<div class="create-brand-container">
  <h2 class="page-title"><?php the_title(); ?></h2>
  <form id="create-brand-form" method="POST" enctype="multipart/form-data" target="create-brand-target">
    <div class="brand-information">
      <input type="text" name="brand-name" placeholder="Brand navn">
      <input type="text" name="brand-facebook-page" placeholder="Facebook side">
      <input type="text" name="brand-twitter-page" placeholder="Twitter">
    </div>
    <div class="brand-dna">
      <input type="text" name="brand-dna-1" placeholder="Brand DNA 1">
      <textarea type="text" name="brand-dna-1-description" placeholder="Beskrivelse af DNA 1"></textarea>
      <input type="text" name="brand-dna-2" placeholder="Brand DNA 2">
      <textarea type="text" name="brand-dna-2-description" placeholder="Beskrivelse af DNA 2"></textarea>
      <input type="text" name="brand-dna-3" placeholder="Brand DNA 3">
      <textarea type="text" name="brand-dna-3-description" placeholder="Beskrivelse af DNA 3"></textarea>
      <input type="text" name="brand-dna-4" placeholder="Brand DNA 4">
      <textarea type="text" name="brand-dna-4-description" placeholder="Beskrivelse af DNA 4"></textarea>
    </div>

    <div class="brand-visual">
      <input type="file" name="lala"> <!-- Change the name of this field  -->
    </div>

    <input type="submit" id="create-form">
    <iframe src="" id="create-brand-target" name="create-brand-target"></iframe>
  </form>
</div>

<?php get_footer(); ?>