<?php
  // Start a session if a brand has been set
  if(isset($_POST['brand'])) {
    $link = get_permalink($_POST['brand']);
    wp_redirect($link);
  }
?>

<?php get_header(); ?>

<?php
  // Fetch all the brand information for use in
  // the option field, in alphabetical order
  $brand_posts = get_posts(array(
    'numberposts' => -1,
    'post_type'   => 'pw_brand',
    'orderby'     => 'title',
    'order'       => 'ASC'
    )
  );

  // Get the current user id
  $current_user = wp_get_current_user();

  // Build the brand list. Only include a brand if the current user
  // is able to create updates for that brand
  if(count($brand_posts) > 0) {
    foreach($brand_posts as $key => $brand) {
      if(pw_currentusercan("create", "update", $brand->ID)) {
        $brands[$brand->ID] = $brand->post_title;
      }
    }
  }
?>

<script>
  // Make this work
  (function($) {
    $(document).ready(function() {


      $("#button").click(function(e) {
        e.preventDefault();

        // Get selected brands ID
        var brand_id = $("#brand-id option:selected").attr('value');

        var data_string = 'action=getBrandDna&brandId=' + brand_id;

        $.ajax({
          type    : 'POST',
          url     : 'wp-admin/admin-ajax.php',
          data    : data_string,
          success : function(res) { 


            dna = JSON.parse(res);


            // var pure_dna_html = '<ul>';
            // for(var i = 0; i < dna.pure.length; i++) {
            //   pure_dna_html += '<li>' + dna.pure[i] + '</li>';
            // }
            // pure_dna_html += '</ul>';



            var dna_html = '<div class="session-start-dna">';
            for(var i = 0; i < dna.length; i++) {
              dna_html += '<ul>';
              for(var j = 0; j < dna[i].length; j++) {
                dna_html += '<li>' + dna[i][j] + '</li>';
              }
              dna_html += '</ul>';
            }
            dna_html += '</div>';

            alert(dna_html);


          }
        });

      });
    });
  })(jQuery);
</script>

<div class="session-start-container">
  <h2 class="page-title">Search for update inspiration</h2>
  <form method="POST">

    <select name="brand" id="brand-id">
      <?php foreach($brands as $key => $brand): ?>
        <option value="<?= $key; ?>"><?= $brand; ?></option>
      <?php endforeach; ?>
    </select>



    <input type="submit" value="Start session!">
  </form>
</div>

<?php get_footer(); ?>