<?php
/*
Plugin Name:	Legendary SEO Pages
Plugin URI:		http://www.legendarylion.com
Description:	This plugin houses all of the SEO landing pages for the Legendary Organic SEO Program.
Version:		1.0.5
Updated:		2024-01-07
Author:			Legendary Lion
Author URI:		http://www.legendarylion.com
License:		All rights reserved.
*/

// define path to easily include functions with path requirements
define('LLSEOPAGES_MAIN_FILE_PATH', __FILE__);

require 'plugin-update-checker/plugin-update-checker.php';
$myLegendarySEOUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/legendary-lion/legendary-seo-pages',
	__FILE__,
	'legendary-seo-pages'
);
$myLegendarySEOUpdateChecker->setBranch('master');
$myLegendarySEOUpdateChecker->getVcsApi()->enableReleaseAssets();


// all includes
include( plugin_dir_path( __FILE__ ) . 'includes/initialize.php');
// temporarily removing reports until we can figure out how to only count authentic conversions (not just visits) -- 2023-05-31 AC
// include( plugin_dir_path( __FILE__ ) . 'views/reports.php');
include( plugin_dir_path( __FILE__ ) . 'views/admin-settings.php');


function list_seo_pages() {

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
// for admins -- this causes a 400 error if commented out when visiting the page -- leaving in for now since the IP will only be logged once (returns 0)
add_action( 'wp_ajax_ll_marketing_add_data', 'll_marketing_add_data' );
// for non-logged in users
add_action( 'wp_ajax_nopriv_ll_marketing_add_data', 'll_marketing_add_data' );

// add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
  
// function my_custom_dashboard_widgets() {
// global $wp_meta_boxes;
 
// wp_add_dashboard_widget('custom_help_widget', 'Legendary SEO Pages', 'custom_dashboard_help');
// }
 
// function custom_dashboard_help() {
// 	ll_seo_reports_page_callback();
// }



add_action( 'wp_enqueue_scripts', 'legendary_seo_enqueue_scripts' );
function legendary_seo_enqueue_scripts()
{
    if ('seopages' === get_post_type() && is_singular()){
		// wp_enqueue_script( 'legendary-seo-tracker', '/wp-content/plugins/legendary-seo-pages/js/tracking.js', '', '', false);
        // return print "Yo World!";

		$traffic_source = "Organic SEO Lead";
		if(isset($_GET['source'])){
			$traffic_source = $_GET['source'];
		}

		// add cookie to users browser to customize the rest of the browsing experience on the site
		setcookie(
			'll_marketing_tracker',
			$traffic_source,
			strtotime("+1 year"),
		);

		$ip_address = '';
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ip_address = $_SERVER['HTTP_X_FORWARDED'];
		} else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
		} else if (isset($_SERVER['HTTP_FORWARDED'])) {
			$ip_address = $_SERVER['HTTP_FORWARDED'];
		} else if (isset($_SERVER['REMOTE_ADDR'])) {
			$ip_address = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip_address = 'UNKNOWN';
		}

		$url = $_SERVER['REQUEST_URI'];
		?>
		<script>

			// vanilla javascript to call to the function to add an entry to the db, this should be set to 2000 by default
			let time = 2000
			setTimeout( function(){
				// alert('Waited for... ' + time / 1000 + ' seconds.')
				// call function from javascript
				
				var data = {
					'action': 'll_marketing_add_data',
					'ip_address': '<?php echo $ip_address;?>',
					'name': '<?php echo $traffic_source;?>',
					'url': '<?php echo $url;?>',

				};
				var ajax_url = "<?php echo admin_url('admin-ajax.php');?>";

				// We can also pass the url value separately from ajaxurl for front end AJAX implementations
				jQuery.post(ajax_url, data, function(response) {
					console.log(response);
				});

			}, time);
		</script>
	<?php
	
	}
	
}

// if the option is checked in plugin settings to inject footer links, do that now
if(get_option('ll_seo_inject_page_links_to_footer') == true){
	add_action('wp_footer', 'list_seo_pages');
}