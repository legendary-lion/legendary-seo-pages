<?php
/*
Plugin Name:	Legendary SEO Pages
Plugin URI:		http://www.legendarylion.com
Description:	This plugin houses all of the SEO landing pages for the Legendary Organic SEO Program.
Version:		1.0.0
Updated:		2021-12-18
Author:			Legendary Lion
Author URI:		http://www.legendarylion.com
License:		All rights reserved.
*/

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/legendary-lion/legendary-seo-pages',
	__FILE__,
	'legendary-seo-pages'
);
$myUpdateChecker->setBranch('master');
$myUpdateChecker->getVcsApi()->enableReleaseAssets();


// all includes
include( plugin_dir_path( __FILE__ ) . 'includes/initialize.php');
include( plugin_dir_path( __FILE__ ) . 'views/reports.php');
include( plugin_dir_path( __FILE__ ) . 'views/admin-settings.php');



function list_seo_pages() {

	  ob_start();

		  $args = array(
		    'post_type' => 'seopages',
		    'title_li'    => '',
		    'echo' => false,
		);

		// old way of adding the pipes, always added one to the end, so dropped for the preg_replace function
		// echo str_replace('</a></li>','</a></li><li class="legendary-separator"> | </li>', $nav);


		echo '<ul class="legendary-footer-links">';

		// store the menu to a variable
		$nav = wp_list_pages( $args ); 

		// count the amount of links in the menu
		$theCount = substr_count($nav, '</a></li>') - 1; // count how many time the period occurs in the string

		// replace all of the ends of the links with a pipe character
		$theContent = preg_replace('(<\/a><\/li>)', '</a></li> <span class="legendary-link-pipe">|</span> ', $nav, $theCount); // replace all but last one

		echo $theContent;

		echo '</ul>';

		return ob_get_clean();
}
add_shortcode('list-seo-pages', 'list_seo_pages');



function ll_marketing_add_data() {
	global $wpdb;
	

	// $ip_address = '192.168.0.1';
	// $welcome_name = 'Mr. WordPress';
	// $url = 'https://www.awesome.com/test-page/';

	$ip_address = $_POST['ip_address'];
	$name = $_POST['name'];
	$url = $_POST['url'];

	$table_name = $wpdb->prefix . 'll_marketing_tracker';
	$like = $wpdb->esc_like( $_POST['ip_address'] );
	$result = $wpdb->get_row("SELECT * FROM $table_name WHERE ip_address = " .'"'.$like.'"');

	// check to see if this visitor has already been logged (by ip address)
	if($result){
		echo 'Visit already logged. Exiting. ⛔';

		// we're calling this by AJAX - so do this to stop WP after completing the function (else wp returns '0')
		wp_die();
	}

	// add visitor to table
	$wpdb->insert( 
			$table_name, 
			array( 
					'time' => current_time( 'mysql' ), 
					'ip_address' => $ip_address, 
					'name' => $name, 
					'location' => '', 
					'url' => $url, 
				) 
			);

	echo 'Visit logged successfully. ✌';


	// // temporary function to load a bunch of records to test
	// $counter = 0;
	// $limit = 10;

	// while($counter < $limit){

	// 	//Start point of our date range.
	// 	$start = strtotime("10 September 2019");

	// 	//End point of our date range.
	// 	$end = strtotime("today");

	// 	//Custom range.
	// 	$timestamp = mt_rand($start, $end);

	// 	//Print it out.
	// 	$time = date("Y-m-d", $timestamp);

	// 	// add visitor to table
	// 	$wpdb->insert( 
	// 			$table_name, 
	// 			array( 
	// 					// 'time' => current_time( 'mysql' ), 
	// 					'time' => $time, 
	// 					'ip_address' => $ip_address, 
	// 					'name' => $name, 
	// 					'location' => '', 
	// 					'url' => $url, 
	// 				) 
	// 			);

	// 	echo 'Visit logged successfully. ✌';

	// 	$counter++;

	// }


	// we're calling this by AJAX - so do this to stop WP after completing the function (else wp returns '0')
	wp_die();
}

// allow the ll_marketing_add_data function to run users
if ( is_admin() ) {
	// for admins -- this causes a 400 error if commented out when visiting the page -- leaving in for now since the IP will only be logged once
	add_action( 'wp_ajax_ll_marketing_add_data', 'll_marketing_add_data' );

} else {

	// for non-logged in users
	add_action( 'wp_ajax_nopriv_ll_marketing_add_data', 'll_marketing_add_data' );
}