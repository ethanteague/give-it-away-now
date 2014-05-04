<?php
/**
 * Template Name: Giveaway
 *
 */

get_header();

$url = plugin_dir_url(__FILE__);

$date = get_post_meta( $post->ID, 'gan_end_date', true );

print '<script type="text/javascript" src="' . $url . 'includes/cookie/jquery.cookie.js"></script>';
print '<link type="text/css" rel="stylesheet" href="' . $url . 'css/gan_style.css" />';

?>

<script type="text/javascript">

  function validateForm() {
  var x = document.forms["gan-submit-form"]["name"].value;
  var y = document.forms["gan-submit-form"]["email"].value;
  if ( x == '' || y == '' ) {
    alert("Both fields must be completed");
    return false;
  }
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  var demail=jQuery("#email-box").attr("value");
  if(!regex.test(demail)){
    alert("You must enter a valid e-mail address");
    return false;
  }

  // Set our cookie if fields are legit
  if(regex.test(demail) && x !== ''){
    jQuery.cookie('<?php echo "page_id_cookie-".$post->ID; ?>', '<?php echo $post->ID; ?>', { expires: 1, path: '/' });
  }
  }

  // Make sure the form is complete and set cookie
jQuery(document).ready(function() {
      jQuery('#gan_submit').click(function() {
          validateForm();
        })
        })
</script>


  <main class="giveaway">

  <div class="gan-wrap">
  <div class="gan-interior-wrap">
<?php print the_post_thumbnail('giveaway-featured'); ?>
  </div>
  </div>

<?php
  $the_why = get_post_meta( $post->ID, 'gan_why', true );
if (!empty($the_why)) { ?>
<div class="gan-wrap">
   <div class="gan-interior-wrap">
   <h4>Why we love this product</h4>
   <p class="giveaway-details"><?php print $the_why; ?></p>
</div>
</div>
<? } ?>

<?php
  $the_about = get_post_meta( $post->ID, 'gan_about', true );
if (!empty($the_about)) { ?>
<div class="gan-wrap">
   <div class="gan-interior-wrap">
   <h4>About this Giveaway</h4>
   <p class="giveaway-details"><?php print $the_about; ?></p>
</div>
<? } ?>
   </div>

   <div class="gan-wrap">
   <div class="gan-interior-wrap">
<?php if (!empty($date)) { ?>
<span class="give-label">Giveaway Ends:</span>
   <span class="giveaway-details"><?php print $date; ?></span>
<br />
<? } ?>
<?php
  $the_quantity = get_post_meta( $post->ID, 'gan_quantity', true );
   if (!empty($the_quantity)) { ?>
<span class="give-label">Number Available:</span>
<span class="giveaway-details"><?php print $the_quantity; ?></span>
<br />
<? } ?>
   <span class="give-label">Max Entries Per Day:</span>
   <span class="giveaway-details">1</span>
   <br />
   </div>
   </div>

   <div class="gan-wrap">
<?

$dt = new DateTime();
$date = strtotime($date);
$today = strtotime($dt->format('m/d/Y'));
global $wpdb;
$table_name = $wpdb->prefix . "give_it_away";

$email_san = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

if (!empty($_POST['name']) && !empty($email_san)) {
// Put the value in the table
  $wpdb->insert( $table_name, array( 'name' => $_POST['name'], 'email' => $_POST['email'], 'post_id' => $_POST['post_id']  ) );
}

if(!in_array($post->ID,$_COOKIE)) {?>
  <div id="gan-form-wrap">
  <h4>Enter to Win!</h4>
  <form id="gan-submit-form" name="gan-submit-form" action="" method="post" >
  <div>
  <input type="hidden" name="post_id" value="<?php print $post->ID; ?>" />
  <input type="hidden" name="post_url" value="<?php print get_permalink($post->ID); ?>" />
  <label for="name">Name *</label>
  <input type="text" name="name" id="name" />
  </div>
  <div>
  <label for="email">Email *</label>
  <input type="text" name="email" id="email-box" />
  </div>
  <input type="submit" id="gan_submit">
  </form>

  </div><!-- .gan-form-wrap -->
<? }

  else if ($today > $date) {
?>
<div id="gan-ended-wrap">
This giveaway has ended.
</div>
<?
  }

  else { ?>
    <div id="gan-wait-wrap">
    Thanks for entering! Come back tomorrow for another chance to win.
                                                          </div>
<? }  ?>

    </div><!-- .gan-wrap for form and gan-wait-wrap -->

</main><!-- .giveaway -->

<?php get_footer(); ?>
