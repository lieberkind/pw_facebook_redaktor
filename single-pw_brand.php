<?php 
// First of all, get the brand ID.
$brand_id = $post->ID;

// If the current user doesn't have access to the brand, die.
if(!pw_currentusercan("read", "brand", $brand_id)) {
  die("You don't have access to this brand");
}

// Get the update type
if(isset($_GET['update-type'])) {
  $update_type = $_GET['update-type'];
}

// Fetch all brand meta and the brand logo
$brand_meta = get_post_custom($brand_id);
$brand_logo = rwmb_meta('pw_brand_logo', array('type' => 'image'), $brand_id);

// Fetch DNA terms
$pure_terms     = wp_get_post_terms($brand_id, 'pw_brand-pure-words', array('fields' => 'names'));
$social_terms   = wp_get_post_terms($brand_id, 'pw_brand-social-words', array('fields' => 'names'));
$traffic_terms  = wp_get_post_terms($brand_id, 'pw_brand-traffic-words', array('fields' => 'names'));
$topical_terms  = wp_get_post_terms($brand_id, 'pw_brand-topcial-words', array('fields' => 'names'));

include('insertupdate.php');
?>

<?php get_header(); ?>
<?php wp_enqueue_script('single-pw_brand'); ?>

<script type="text/javascript">
  // Triggers the click event of the specified ID
  function getClickBehavior(id) {
    document.getElementById(id).click();
  }

  function initInspiration() {
    // Initialize inspiration
    INSPIRATION.init(brand_id, jQuery("#inspiration-container"), PERSONA.getId());
  }

  /* WHEN PAGE IS READY */
  jQuery(document).ready(function() {

    // Whenever a file needs to be uploaded display the path
    // on the right of the button
    var $button = jQuery("#update-upload-image-button")
    $button.change(function() {
      var button_val = $button.val();
      // Matches, for example, "C:\fakepath\"
      var filename = button_val.replace(/[A-z][:\\\/]*[A-z]+[\\\/]/, "");
      jQuery("#brand-file-upload-container .file-path").html(filename);
    });

    jQuery('#upload-image-button-visible').click(function(e) {
      e.preventDefault();
      getClickBehavior('update-upload-image-button');
    })

    // Toggle the visibility of the more container when the 
    // tab is clicked
    jQuery('.more-tab').click(function() {
      jQuery('.more-container').toggleClass('visible');
    });
    
    // Make inspiration words draggable
    jQuery('.inspiration-word').draggable({containment: "parent"});

    // Initialize the persona, callback to initialize the inspiration
    PERSONA.init(brand_id, jQuery('#brand-persona'), initInspiration);

    // Center inspiration image inside container
    // Would be nice to find a CSS solution to this problem
    var inspiration_container = jQuery("#inspiration-container");
    var inspiration_image     = jQuery("#inspiration-image");
    inspiration_image.load(function() {
      var computed_margin = (inspiration_container.height() - inspiration_image.height()) / 2;
      inspiration_image.css('margin-top', computed_margin);
    });

    // Make sure that the inspiration image stays centered when
    // the window is resized
    jQuery(window).resize(function() {
      var computed_margin = (inspiration_container.height() - inspiration_image.height()) / 2;
      inspiration_image.css('margin-top', computed_margin);  
    });

    // Register click event for refresh button
    jQuery("#refresh-inspiration-content").click(function(e) {
      e.preventDefault();
      INSPIRATION.update(function() {
        INSPIRATION.render(function() {
          showSystemMessage('success', 'Content refreshed!');
        });
      });
    });


    
    
  });

</script>

<div class="container" id="<?= $brand_id; ?>">

  <h2 class="generator-page-title">Generator</h2>

  <div class="update-generator">
    <div class="inspiration-container" id="inspiration-container">
      <span class="inspiration-word" id="say-word"></span>
      <span class="inspiration-word" id="do-word"></span>
      <span class="inspiration-word" id="think-word"></span>
      <span class="inspiration-word" id="own-word"></span>

      <img class="inspiration-image" id="inspiration-image">
      <a href="#" class="refresh-inspiration-content" id="refresh-inspiration-content">Refresh</a>

      <div class="more-wrapper">
        <p class="more-tab">More</p>
        <div class="more-container">
          <div class="brand-dna">
            <h3 class="brand-dna-container-title">Brand DNA</h3>
            <div class="brand-dna-terms-container">
              
              <h4 class="terms-list-title">Pure</h4>
              <ul class="dna-terms-list">
                <?php foreach ($pure_terms as $key => $term): ?>

                  <li class="dna-list-term">
                    <?php if($key == count($pure_terms) - 1): ?>
                      <?= $term ?>
                    <?php else: ?>
                      <?= $term ?>,
                    <?php endif; ?>
                  </li>

                <?php endforeach ?>
              </ul>

              <h4 class="terms-list-title">Social terms</h4>
              <ul class="dna-terms-list">
                <?php foreach ($social_terms as $key => $term): ?>

                  <li class="dna-list-term">
                    <?php if($key == count($social_terms) - 1): ?>
                      <?= $term ?>
                    <?php else: ?>
                      <?= $term ?>,
                    <?php endif; ?>
                  </li>

                <?php endforeach ?>
              </ul>

              <h4 class="terms-list-title">Topical terms</h4>
              <ul class="dna-terms-list">
                <?php foreach ($topical_terms as $key => $term): ?>

                  <li class="dna-list-term">
                    <?php if($key == count($topical_terms) - 1): ?>
                      <?= $term ?>
                    <?php else: ?>
                      <?= $term ?>,
                    <?php endif; ?>
                  </li>

                <?php endforeach ?>
              </ul>

              <h4 class="terms-list-title">Traffic terms</h4>
              <ul class="dna-terms-list">
                <?php foreach ($traffic_terms as $key => $term): ?>
                  <li class="dna-list-term">
                    <?php if($key == count($traffic_terms) - 1): ?>
                      <?= $term ?>
                    <?php else: ?>
                      <?= $term ?>,
                    <?php endif; ?>
                  </li>
                <?php endforeach ?>
              </ul>

            </div>
          </div>
          <div class="brand-persona" id="brand-persona">
            <h3 class="brand-persona-title">Persona</h3>
          </div>
        </div>
      </div>

    </div>

    <div class="update-form-container" id="update-form-container">

      <div class="brand-info">
        <!-- Ugly as hell! Should be refactored -->
        <div class="brand-logo-container">
          <?php foreach ($brand_logo as $key => $logo): ?>
            <img src="<?= $logo['url']; ?>" class="brand-logo">
          <?php endforeach; ?>
        </div>
        <p class="brand-name"><?= get_the_title($brand_id); ?></p>
      </div>

      <form class="update-form" method="POST" name="new_update" id="new_update" enctype="multipart/form-data" target="upload_target">

        <textarea name="update-content" class="update-content" placeholder="Skriv statusopdatering her..."></textarea>
        <textarea placeholder="Evt. note til statusopdateringen..." type="text" name="update-link" class="update-note"></textarea>
        
        <div class="upload-button-container" id="brand-file-upload-container">
          <button class="button" id="upload-image-button-visible">Billedupload</button>
          <span class="file-path"></span>
        </div>
        
        <input type="file" name="update-image" class="update-upload-image" id="update-upload-image-button">
        <input type="submit" value="Gem update" class="button submit-update">

        <!-- This iframe shoud be hidden with CSS -->
        <iframe src="" id="upload_target" name="upload_target" style="display: none;"></iframe>
      </form>

    </div>
  </div>
</div>

<!-- <div id="qunit"></div>
<div id="qunit-fixture"></div> -->
<?php get_footer(); ?> 






