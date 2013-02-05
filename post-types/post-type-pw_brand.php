<?php

add_action('init', 'register_pw_brand');
add_action('admin_init', 'register_pw_brand_metaboxes');


function register_pw_brand() {
  $labels = array(
    'name'                => _x('Brands', 'post type general name'),
    'singular_name'       => _x('Brand', 'post type singular name'),
    'add_new'             => _x('Add New', 'brand'),
    'add_new_item'        => __('Add New Brand'),
    'edit_item'           => __('Edit Brand'),
    'new_item'            => __('New Brand'),
    'view_item'           => __('View Brand'),
    'search_items'        => __('Search Brand'),
    'not_found'           =>  __('Nothing found'),
    'not_found_in_trash'  => __('Nothing found in Trash'),
    'parent_item_colon'   => ''
  );

  $args = array(
    '_builtin'            => false,
    'labels'              => $labels,
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'query_var'           => true,
    'menu_position'       => 5,
    'capability_type'     => 'post',
    'hierarchical'        => false,
    'supports'            => array('title')
  );

  register_post_type('pw_brand', $args);
  register_pw_brand_taxonomies();
}

function register_pw_brand_taxonomies() {

  $pure_words_labels = array(
    'name'                => _x('Pure Words', 'taxonomy general name'),
    'singular_name'       => _x('Pure Word', 'taxonomy singular name'),
    'search_items'        => __('Search Pure Words'),
    'popular_items'       => __('Popular Pure Words'),
    'all_items'           => __('All Pure Words'),
    'edit_item'           => __('Edit Pure Word'),
    'update_item'         => __('Update Pure Word'),
    'add_new_item'        => __('Add New Pure Word'),
    'new_item_name'       => __('New Pure Word')  
  );
  
  $pure_word_args = array(
    'labels'            => $pure_words_labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    '_builtin'          => false
  );

  register_taxonomy('pw_brand-pure-words', 'pw_brand', $pure_word_args);

  $topical_words_labels = array(
    'name'                => _x('Topical Words', 'taxonomy general name'),
    'singular_name'       => _x('Topical Word', 'taxonomy singular name'),
    'search_items'        => __('Search Topical Words'),
    'popular_items'       => __('Popular Topical Words'),
    'all_items'           => __('All Topical Words'),
    'edit_item'           => __('Edit Topical Word'),
    'update_item'         => __('Update Topical Word'),
    'add_new_item'        => __('Add New Topical Word'),
    'new_item_name'       => __('New Topical Word')  
  );
  
  $topical_word_args = array(
    'labels'            => $topical_words_labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    '_builtin'          => false
  );

  register_taxonomy('pw_brand-topcial-words', 'pw_brand', $topical_word_args);

  $social_words_labels = array(
    'name'                => _x('Social Words', 'taxonomy general name'),
    'singular_name'       => _x('Social Word', 'taxonomy singular name'),
    'search_items'        => __('Search Social Words'),
    'popular_items'       => __('Popular Social Words'),
    'all_items'           => __('All Social Words'),
    'edit_item'           => __('Edit Social Word'),
    'update_item'         => __('Update Social Word'),
    'add_new_item'        => __('Add New Social Word'),
    'new_item_name'       => __('New Social Word')  
  );
  
  $social_word_args = array(
    'labels'            => $social_words_labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    '_builtin'          => false
  );

  register_taxonomy('pw_brand-social-words', 'pw_brand', $social_word_args);  

  $traffic_words_labels = array(
    'name'                => _x('Traffic Words', 'taxonomy general name'),
    'singular_name'       => _x('Traffic Word', 'taxonomy singular name'),
    'search_items'        => __('Search Traffic Words'),
    'popular_items'       => __('Popular Traffic Words'),
    'all_items'           => __('All Traffic Words'),
    'edit_item'           => __('Edit Traffic Word'),
    'update_item'         => __('Update Traffic Word'),
    'add_new_item'        => __('Add New Traffic Word'),
    'new_item_name'       => __('New Traffic Word')  
  );
  
  $traffic_word_args = array(
    'labels'            => $traffic_words_labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    '_builtin'          => false
  );

  register_taxonomy('pw_brand-traffic-words', 'pw_brand', $traffic_word_args);  

  
}

function register_pw_brand_metaboxes() {
  $prefix = 'pw_brand_';

  $users = get_users();

  $user_names = array();
  foreach ($users as $key => $user) {
    $user_names[$user->ID] = $user->display_name;
  }
  
  $meta_boxes[] = array(
    'id'      => 'brand-info',
    'title'   => 'Brand Information',
    'pages'   => array('pw_brand'),
    'context' => 'normal',

    'fields'  => array(
      array(
        'name'  => 'Brand logo',
        'id'    => $prefix . 'logo',
        'type'  => 'image'
      ),
      array(
        'name'  => 'Facebook Page',
        'id'    => $prefix . 'fb-page',
        'type'  => 'text',
        'size'  => 60 // Why is this here?
      )
    )
  );

  $meta_boxes[] = array(
    'id'      => 'brand-collaborators',
    'title'   => 'Brand Collaborators',
    'pages'   => array('pw_brand'),
    'context' => 'normal',

    'fields' => array(
      array(
        'name'    => 'Brand Collaborators',
        'desc'    => 'Who has access to post updates for the brand?',
        'id'      => $prefix . 'collaborators',
        'type'    => 'checkbox_list',
        'options' => $user_names
      )
    )
  );

  $meta_boxes[] = array(
    'id'      => 'dna-options',
    'title'   => 'Brand Information',
    'pages'   => array('pw_brand'),
    'context' => 'normal',

    'fields'  => array(
      // DNA 1

      array(
        'name'  => 'DNA: Social words',
        'id'    => $prefix . 'dna-social',
        'type'  => 'text',
        'clone' => true,
        'size'  => 70
      ),
      array(
        'name'  => 'DNA: Topical words',
        'id'    => $prefix . 'dna-topical',
        'type'  => 'text',
        'clone' => true,
        'size'  => 70
      ),
      array(
        'name'  => 'DNA: Social words',
        'id'    => $prefix . 'dna_social',
        'type'  => 'text',
        'clone' => true,
        'size'  => 70
      ),
      array(
        'name'  => 'DNA: Social words',
        'id'    => $prefix . 'dna_social',
        'type'  => 'text',
        'clone' => true,
        'size'  => 70
      ),
    )
  );

  foreach ($meta_boxes as $meta_box) {
    new RW_Meta_Box($meta_box);
  }
}

?>