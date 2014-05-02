<?php
/**
 * Template Name: Giveaway All
 *
 */

get_header();

$url = plugin_dir_url(__FILE__);
print '<link type="text/css" rel="stylesheet" href="' . $url . 'css/gan_style.css" />';

?>

<main class="giveaway">

  <script>

  // If we don't have any more items, hide pager
  function gan_pagers() {
    if (jQuery('.gan-item-wrap').length == 0) {
      jQuery('.dirs').css('display', 'none');
    }
  }

// Fire gan_pagers
jQuery(document).ready(function() {
    gan_pagers();
  })

</script>
<?
  $page_num_raw = $_SERVER['QUERY_STRING'];
$page_num = explode('=', $page_num_raw);
$page_arg = !empty($page_num[1]) ? $page_num[1] : 1;

$args=array(
	'post_type' => 'giveaway',
	'post_status' => 'publish',
  'posts_per_page' => 12,
  'paged' => $page_arg,
  'orderby' => 'date',
);

$query = new WP_Query( $args );
print '<div id="gan-archive-wrap">';
while ( $query->have_posts() ) {
	$query->the_post();

  $link = get_permalink();
  $pic =   get_the_post_thumbnail($post->ID,'giveaway-thumb');
  $title = get_the_title();

  $dt = new DateTime();
  $today = strtotime($dt->format('m/d/Y'));
  $date = strtotime(get_post_meta( $post->ID, 'gan_end_date', true ));

  if ( $today < $date ) {
    $date_info = '<span class="green">OPEN UNTIL: '.get_post_meta( $post->ID, 'gan_end_date', true ).'</span>';
  } else {
    $date_info = '<span class="red">ENDED: ['.get_post_meta( $post->ID, 'gan_end_date', true ).']</span>';
  }

	print '<div class="gan-item-wrap">';
  print '<a href="' . $link . '"><div>' . $pic . '</div></a>';
  print '<h4><a href="' . $link . '">' . $title . '</a></h4>';
  print '<span class="gan-date">' . $date_info . '</span></div>';
}
print '<div style="clear:both"></div></div>';
if ( !empty($page_arg) && $page_arg > 1 ) {
  $page_arg_prev = $page_arg - 1;
  print '<a class="dirs" href="?q=' . $page_arg_prev . '">&laquo; Previous</a>';
}

if ( !empty($page_arg) ) {
  $page_arg_next = $page_arg + 1;
  print '<a class="dirs" href="?q=' . $page_arg_next . '">Next &raquo;</a>';
}
else
  {
    print '<a href="?q=2">Next &raquo;</a>';
  }

?>

</main><!-- .giveaway -->

<?php get_footer(); ?>
