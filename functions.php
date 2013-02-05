<?php

/**
Load scripts
**/

// The styles for this should be made custom when design is ready!
function load_jquery_ui() {
    global $wp_scripts;
 
    // tell WordPress to load jQuery UI tabs
    wp_enqueue_script('jquery-ui-dialog');
 
    // get registered script object for jquery-ui
    $ui = $wp_scripts->query('jquery-ui-core');
 
    // tell WordPress to load the Smoothness theme from Google CDN
    // NB: as at 2012-06-14, the Google CDN stops at v1.8.18; use Microsoft's instead
    // $url = "https://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery.ui.all.css";
    //$url = "https://ajax.aspnetcdn.com/ajax/jquery.ui/{$ui->ver}/themes/smoothness/jquery.ui.all.css";
    $url = get_bloginfo('template_url') . '/stylesheets/smoothness/jquery-ui-1.9.2.custom.min.css';
    wp_enqueue_style('jquery-ui-smoothness', $url, false, $ui->ver);
}
add_action('init', 'load_jquery_ui');


/* Hide the admin bar from front end */
show_admin_bar(false);


/* Load necessary javascript */
function pw_load_javascripts() {
  
  // Register and enqueue fancybox
  wp_register_style('fancybox-style', 'https://apps.speakup.dk/cdn/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css');
  wp_register_script('fancybox', 'https://apps.speakup.dk/cdn/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'));
  wp_enqueue_style('fancybox-style');
  wp_enqueue_script('fancybox');


  wp_enqueue_script('jquery');
  //wp_enqueue_script('jquery-ui-dialog', array('jquery'));
  wp_enqueue_script('ajax-implementations', get_bloginfo('template_url') . '/js/ajax-implementation.js', array('jquery'));

  // Page specific JavaScript
  wp_register_script('single-pw_brand', get_bloginfo('template_url') . '/js/single-pw_brand.js', array('jquery'));

  // Fileuploader
  wp_register_script('fileuploader', get_bloginfo('template_url') . '/js/fileuploader.js', array());
  wp_register_style('fileuploader-css', get_bloginfo('template_url') . '/stylesheets/fileuploader.css', false);
  wp_enqueue_script('fileuploader');
  wp_enqueue_style('fileuploader-css');

  wp_enqueue_script('jquery-ui-draggable', array('jquery'));

  wp_register_script('pw_functions', get_bloginfo('template_url') . '/js/functions.js', array('jquery'));
  wp_enqueue_script('pw_functions');

  wp_register_script('iframe-transport', get_bloginfo('template_url') . '/js/jquery.iframe-transport.js', array('jquery'));
  wp_enqueue_script('iframe-transport');

  wp_register_script('qunit', get_bloginfo('template_url') . '/js/qunit.js', array());
  wp_register_style('qunit-css', get_bloginfo('template_url') . '/stylesheets/qunit.css', false);
  wp_enqueue_style('qunit-css');
  wp_enqueue_script('qunit');

  // QUnit tests
  wp_register_script('single-pw_brand-tests', get_bloginfo('template_url') . '/tests/single-pw_brand-tests.js', array());
  wp_enqueue_script('single-pw_brand-tests');
  
  // Register the ajax-script colorpicker for use on the "Create Brand" page
  wp_register_script('collapsible', get_bloginfo('template_url') . '/js/collapsible/jquery.collapsible.min.js', array('jquery'));
  wp_register_script('createbrand', get_bloginfo('template_url') . '/js/createbrand.js', array('jquery'));
  wp_register_script('farbtastic', get_bloginfo('template_url') . '/js/farbtastic/farbtastic.js', array('jquery'));
  wp_register_style('farbtastic-style', get_bloginfo('template_url') . '/js/farbtastic/farbtastic.css', false);

  // Register and enqueue the stylesheet that customizes the login form
  wp_register_style('login-style', get_bloginfo('template_url') . '/stylesheets/login.css', false);
  wp_enqueue_style('login-style');

}
add_action('init', 'pw_load_javascripts');

/**
Register image sizes
**/
add_image_size('person-inspiration-image', '390', '290', true);

/**
Register navigation menus
**/
register_nav_menu('primary_navigation', 'Primary Navigation');
register_nav_menu('primary_nav_admins', 'Admin Navigation');

/**
Register post types
**/
require_once('post-types/post-type-pw_brand.php');
require_once('post-types/post-type-pw_person.php');
require_once('post-types/post-type-pw_update.php');


/**
Custom functions
**/

// Restrict access to wp-admin for everyone but admins
// function restrict_admin(){
//   //if not administrator, kill WordPress execution and provide a message
//   if ( !current_user_can('administrator') ) {
//     wp_die( __('You are not allowed to access this part of the site') );
//   }
// }
// add_action( 'admin_init', 'restrict_admin', 1 );

// Pick $amount random elements from the $array. If the $array
// has less than $amount elements, select them all.
function get_random_elements($array, $amount) {
  $array_elements_count = count($array);
  
  if($array_elements_count >= $amount) {
    $random_indexes = array_rand($array, $amount);
  } else {
    $random_indexes = array_rand($array, $array_elements_count);
  }

  // If $amount is 1 return only that element,
  // and not an array of elements
  if($amount == 1) {
    return $array[$random_indexes];
  }


  $result = array();
  foreach ($random_indexes as $key => $ri) {
    $result[] = $array[$ri];
  }

  return $result;
}

// Add a term to the database and associate it with
// post with id $id
function pw_add_term($id, $term, $tax) {
  $term_id = is_term($term);
  if (!$term_id) {
    $term_id = wp_insert_term($term, $tax);
    $term_id = $term_id['term_id'];
  }
  $term_id = intval($term_id);

  $result =  wp_set_object_terms($id, array($term_id), $tax, FALSE);

  return $result;
}

/*
* Adds $terms to the taxonomy, $tax, on the post with $id
*/
function pw_add_terms($id, $terms, $tax) {

  foreach ($terms as $key => $term) {
    $term_id = term_exists($term, $tax);

    if (!$term_id) {
      $term_id = wp_insert_term($term, $tax);
    } 
    
    $term_id = $term_id['term_id'];
    $term_ids[] = intval($term_id); // wat
  }

  $result =  wp_set_object_terms($id, $term_ids, $tax, FALSE);

  return $result;
}

/*
* Allow images to be uploaded and inserted to posts from a form on the frontend
* If the file is successfully uploaded, return the new attachment id. Else return false.
*/
function insert_attachment($file_handler,$post_id,$setthumb='false') {
  // check to make sure its a successful upload
  if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) return false;

  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
  require_once(ABSPATH . "wp-admin" . '/includes/media.php');

  $attach_id = media_handle_upload( $file_handler, $post_id );
  return $attach_id;
}


// // TODO: Find out about the taxonomies
// add_action( 'init', 'unregister_taxonomy');
// function unregister_taxonomy(){
//   global $wp_taxonomies;
//   $taxonomy = 'pw_person-i-own-words';
//   if ( taxonomy_exists( $taxonomy))
//     unset( $wp_taxonomies[$taxonomy]);
// }


function pw_currentusercan($action, $post_type = null, $brand_id = null) {

  // Get the current user
  $user = wp_get_current_user();

  // ... and the users who has access to $brand_id
  if($brand_id) {
    $allowed_brand_users = get_post_meta($brand_id, 'pw_brand_collaborators', false);
    $user_can_access_brand = in_array($user->ID, $allowed_brand_users);
  }

  // If the user is not logged in, there's access to nothing
  if($user->ID == 0) { return false; }

  /*
  * ADMINISTRATORS
  */
  if(in_array("administrator", $user->roles)) 
  { 
  // Admins are 1337 and can do everything!
    return true; 
  }

  /*
  * CHEIF EDITORS
  */
  if(in_array("cheif_editor", $user->roles))
  {
    // C:REATE
    if(strcmp("create", $action) == 0)
    {
      $create_rights =
          strcmp("update", $post_type) == 0   // Can create updates for ALL brands
      ||  strcmp("person", $post_type) == 0;  // Can create personas for ALL brands
      
      return $create_rights ? true : false;
    }

    // R:EAD
    if(strcmp("read", $action) == 0) 
    {
      $read_rights =
          strcmp("brand", $post_type) == 0    // Can read ALL brands
      ||  strcmp("update", $post_type) == 0   // Can read ALL updates
      ||  strcmp("person", $post_type) == 0;  // Can read ALL personas
      
      return $read_rights ? true : false; // Could probably be replaced by "return $read_rights"
    }

    // U:PDATE
    if(strcmp("update", $action) == 0)
    {
      $update_rights =
          strcmp("brand", $post_type) == 0    // Can update ALL brands
      ||  strcmp("update", $post_type) == 0   // Can update ALL updates
      ||  strcmp("person", $post_type) == 0;  // Can update ALL persons

      return $update_rights ? true : false;
    }

    // D:ELETE
    if(strcmp("delete", $action) == 0)
    {
      $delete_rights =
          strcmp("update", $post_type) == 0   // Can delete ALL updates
      ||  strcmp("person", $post_type) == 0;  // Can delete ALL persons

      return $delete_rights ? true : false;
    }

    // AD: APPROVE / DENY
    if(strcmp("approve", $action))
      return false;                           // Can't approve / deny anything 

    // RA: RESTRICT ACCESS TO
    // if(strcmp("restrict", $action) == 0)
    // {
    //   $ra_rights = 
    //     strcmp("brand", $post_type);          // Can restrict access to ALL brands

    //   return $ra_rights ? true : false;
    // }
  }
  

  /* 
  * PROJECT MANAGERS
  */
  if(in_array("project_manager", $user->roles))
  {
    // C:REATE
    if(strcmp("create", $action) == 0) 
    {
      $create_rights = 
          strcmp("update", $post_type) == 0 && $user_can_access_brand // Can create updates for OWN brands
      ||  strcmp("person", $post_type) == 0 && $user_can_access_brand; // Can create personas for OWN brands

      return $create_rights ? true : false;
    }

    // R:EAD
    if(strcmp("read", $action) == 0) 
    {
      $read_rights =
          strcmp("brand", $post_type) == 0                              // Can read ALL brands
      ||  strcmp("update", $post_type) == 0 && $user_can_access_brand   // Can read OWN brands updates
      ||  strcmp("person", $post_type) == 0 && $user_can_access_brand;  // Can read OWN brands personas

      return $read_rights ? true : false;
    }

    // U:PDATE
    if(strcmp("update", $action) == 0)
    {
      $update_rights =
          strcmp("brand", $post_type) == 0  && $user_can_access_brand   // Can update OWN brands
      ||  strcmp("update", $post_type) == 0 && $user_can_access_brand   // Can update OWN brands updates
      ||  strcmp("person", $post_type) == 0 && $user_can_access_brand;  // Can update OWN bradns personas
      
      return $update_rights ? true : false;
    }

    // D:ELETE
    if(strcmp("delete", $action) == 0) 
    {
      $delete_rights =
          strcmp("brand", $post_type) == 0  && $user_can_access_brand    // Can delete OWN brands
      ||  strcmp("update", $post_type) == 0 && $user_can_access_brand    // Can delete OWN brands updates
      ||  strcmp("person", $post_type) == 0 && $user_can_access_brand;   // Can delete OWN bradns personas

      return $delete_rights ? true : false;
    }

    // AD: APPROVE/DENY
    if(strcmp("approve", $action) == 0)
    {
      $ad_rights = strcmp("update", $post_type) == 0 && $user_can_access_brand;
      return $ad_rights ? true : false;
    }

    // // RA: RESTRICT ACCESS TO
    // if(strcmp("restrict", $action) == 0)
    // {
    //   $ra_rights = strcmp("brand", $post_type) == 0 && $user_can_access_brand;
    //   return $ra_rights ? true : false;
    // }


  }


  /*
  * EDITORS
  */
  if(in_array("editor", $user->roles))
  {
    // C:REATE
    if(strcmp("create", $action) == 0)
    {
      $create_rights =
        strcmp("update", $post_type) == 0 && $user_can_access_brand;    // Can create updates for OWN brands

      return $create_rights ? true : false;
    }

    if(strcmp("read", $action) == 0)
    {
      $read_rights =
          strcmp("brand", $post_type) == 0   && $user_can_access_brand
      ||  strcmp("update", $post_type) == 0  && $user_can_access_brand
      ||  strcmp("person", $post_type) == 0  && $user_can_access_brand;

      return $read_rights ? true : false;  
    }

    if(strcmp("update", $action) == 0) 
    {
      $update_rights =
        strcmp("update", $post_type) == 0 && $user_can_access_brand;

      return $update_rights ? true : false;
    }

    if(strcmp("delete", $action) == 0) 
    {
      $delete_rights =
        strcmp("update", $post_type) == 0 && $user_can_access_brand;

      return $delete_rights ? true : false;
    }
  }
  return false;
}


/**
AJAX functions
**/
include('ajax-functions.php');
























?>