<?php
/**
 * Plugin Name: Give it Away Now
 * Plugin URI: http://virtualputty.com
 * Description: A plugin to make daily giveaways for blog sites.
 * Version: 1.0
 * Author: Ethan Teague
 * Author URI: http://virtualputty.com
 * License: GPLv2 or later
 */

/*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Load our admin settings
include 'giveaway-admin.php';

// Load our schema
global $wpdb;

$table_name = $wpdb->prefix . "give_it_away";
$sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
id int(10) NOT NULL AUTO_INCREMENT,
name VARCHAR(25) NOT NULL,
email VARCHAR(75) NOT NULL,
post_id VARCHAR(25) NOT NULL,
PRIMARY KEY (id)
);" ;

$wpdb->query($sql);

// Initialize our content type
function giveaway_init() {
	$labels = array(
		'name'               => _x( 'Giveaways', 'post type general name' ),
		'singular_name'      => _x( 'Giveaway', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'book' ),
		'add_new_item'       => __( 'Add New Giveaway' ),
		'edit_item'          => __( 'Edit Giveaway' ),
		'new_item'           => __( 'New Giveaway' ),
		'all_items'          => __( 'All Giveaways' ),
		'view_item'          => __( 'View Giveaway' ),
		'search_items'       => __( 'Search Giveaways' ),
		'not_found'          => __( 'No giveaways found' ),
		'not_found_in_trash' => __( 'No giveaways found in the Trash' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Giveaways'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds our giveaways and giveaway specific data',
		'public'        => true,
    'rewrite' => array( 'slug' => 'giveaway','with_front' => FALSE),
    'query_var' => "giveaway",
    'capability_type' => 'post',
    'hierarchical' => false,
		'menu_position' => 5,
		'supports'      => array( 'title', 'thumbnail'),
		'has_archive'   => true,
	);
	register_post_type( 'giveaway', $args );
}

add_action( 'init', 'giveaway_init' );

// Hook the administrative header output
add_action('admin_head', 'giveaway_logo');

// Give our admin menu some flair
function giveaway_logo() {
  print '
<style type="text/css">
 #menu-posts-giveaway .dashicons-admin-post:before { content:"\f312"; color:#00FF70 !important; }
</style>';
}

// Load our metabox contruction script
function giveaway_cmb_meta_boxes() {
	if ( !class_exists( 'cmb_Meta_Box' ) ) {
		require_once( 'includes/metabox/init.php' );
	}
}

add_action( 'init', 'giveaway_cmb_meta_boxes', 9999 );

// Create our metaboxes
function giveaway_metaboxes( $meta_boxes ) {
	$prefix = 'gan_'; // Prefix for all fields
	$meta_boxes[] = array(
		'id' => 'gan_metabox',
		'title' => 'Giveaway Information',
		'pages' => array('giveaway'), // post type
		'context' => 'normal',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name'    => 'The "Why" Box',
				'desc'    => 'This is where you explain why you love this product',
				'id'      => $prefix . 'why',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 5, ),
			),
			array(
				'name'    => 'The "About" Box',
				'desc'    => 'This is where you tell the viewer about the product',
				'id'      => $prefix . 'about',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 5, ),
			),
      array(
				'name'    => 'Official Rules',
				'desc'    => 'This is where you give detailed explanation of rules',
				'id'      => $prefix . 'official_rules',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 5, ),
			),
      array(
				'name'    => 'Number of Prizes Available',
				'desc'    => 'Inventory Quantity',
				'id'      => $prefix . 'quantity',
				'type'    => 'text_small',
			),
      array(
        'name' => 'Giveaway End Date',
        'desc' => 'This is when the giveaway ends and becomes unpublished',
        'id' => $prefix . 'end_date',
        'type' => 'text_date'
      ),
		),
	);

	return $meta_boxes;
}

add_filter( 'cmb_meta_boxes', 'giveaway_metaboxes' );

// Redirect to plugin template.
function giveaway_template() {
  global $post;

  if ($post->post_type == 'giveaway') {
    $tpl = dirname( __FILE__ ) . '/giveaway-template.php';
  }
  if (is_post_type_archive('giveaway')) {
    $tpl = dirname( __FILE__ ) . '/giveaway-all.php';
  }

  return $tpl;
}

add_filter( "single_template", "giveaway_template" );
add_filter( "archive_template", "giveaway_template" );

// Add custom image sizes
add_image_size( 'giveaway-thumb', 175, 175, true);
add_image_size( 'giveaway-featured', 600, 250, true);


// Add settings link on plugin page
function giveaway_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=giveaway-form.php">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'giveaway_settings_link' );