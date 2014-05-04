<?php

add_action('admin_menu', 'giveaway_plugin_menu');

// Create our admin settings page
function giveaway_plugin_menu() {
  global $settings;
  $settings = add_options_page('Give it Away Now Entries', 'Give it Away Now Entries', 'manage_options', 'giveaway-form.php', 'giveaway_plugin_page');
  add_action( 'admin_enqueue_scripts', 'giveaway_enqueue_style' );
  add_action( 'admin_enqueue_scripts', 'giveaway_enqueue_script' );
  add_action("admin_head-{$settings}",'giveaway_inline_js');
}

// Load our js and css for the admin settings page table
function giveaway_enqueue_script() {
  wp_enqueue_script( 'giveaway-admin-js',plugins_url( 'includes/datatables/media/js/jquery.dataTables.min.js' , __FILE__ ), true );
}

function giveaway_enqueue_style() {
  wp_enqueue_style( 'giveaway-admin-css',plugins_url( 'includes/datatables/media/css/jquery.dataTables.css' , __FILE__ ), true );
}

function giveaway_inline_js() {
?>
  <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function() {
      jQuery('#giveaway-admin-table').dataTable();
    } );
  </script>
<?
}

// Grab our entries from the db
function giveaway_entries_load() {
  global $wpdb;
  $table_name = $wpdb->prefix . "give_it_away";
  global $settings;
  $result = ($wpdb->get_results( "SELECT * FROM $table_name"));
  return $result;
}

// Output or info to settings page
function giveaway_plugin_page () {
  global $settings;
  $rows = giveaway_entries_load();

  echo '<table id="giveaway-admin-table"><thead><tr><th>Name</th><th>Email</th><th>Giveaway</th><th>End Date</th></tr></thead><tbody>';
  foreach($rows as $row)
    {
      echo '<tr>';
      echo '<td>'.$row->name.'</td>';
      echo '<td><a href="mailto:'.$row->email.'">'.$row->email.'</a></td>';
      echo '<td><a target="_blank" href="'.get_permalink($row->post_id).'">'.get_the_title($row->post_id).'</a></td>';
      echo '<td>'.get_post_meta( $row->post_id, 'gan_end_date', true ).'</td>';
      echo '</tr>';

    }
  echo '<h3>Give It Away Now Entries</h3></tbody></table><br /><br />';
  echo '<div id="info_contact" style="font-weight:bold;">Do you need help with this plugin, or have feature requests?<br />Give me a shout: <a href="mailto:holler@virtualputty.com">Email</a> | <a target="_blank" href="http://virtualputty.com">VirtualPutty</a></div>';
}