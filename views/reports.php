<?php

function ll_seo_pages_reports_page() {
	add_submenu_page(
		'edit.php?post_type=seopages',
        'Legendary SEO Reports',
        'Reports',
        'manage_options',
        'll-seo-reports',
        'll_seo_reports_page_callback' );
}
add_action('admin_menu', 'll_seo_pages_reports_page');

// ll_seo_options_page will be the function that will be called, when this page is displayed.
function ll_seo_reports_page_callback() {
	// $ll_template_setting_value = get_option('ll_seo_option_template');

    global $wpdb;
    // this adds the prefix which is set by the user upon instillation of wordpress
    $table_name = $wpdb->prefix . "ll_marketing_tracker";
    // this will get the data from your table
    $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY `time` DESC" );	

 	$now = date('Y-m-d'); 

	$today_counter = 0;
	$month_counter = 0;
	$year_counter = 0;
	$all_time_counter = 0;


	// today
	foreach($retrieve_data as $data){
		// echo date('Y-m-d', strtotime($data->time));
		if(date('Y-m-d', strtotime($data->time)) == $now){
			$today_counter++;
		}
	}

	// month
	foreach($retrieve_data as $data){
		// echo date('Y-m-d', strtotime($data->time));
		if(date('Y-m-d', strtotime($data->time)) <= $now && date('Y-m-d', strtotime($data->time)) > date('Y-m-d', strtotime("today - 1 month")) ){
			$month_counter++;
		}
	}
	// year
	foreach($retrieve_data as $data){
		// echo date('Y-m-d', strtotime($data->time));
		if(date('Y-m-d', strtotime($data->time)) <= $now && date('Y-m-d', strtotime($data->time)) > date('Y-m-d', strtotime("today - 1 year")) ){
			$year_counter++;
		}
	}

	// all time
	$all_time_counter = count($retrieve_data);
	

	// display table using native wordpress table helper class
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

?>

<style>

</style>
<div class="container">
	<h1>Legendary SEO Reports</h1>
	<hr/>
	<?php
	// get the last item in the array
	$last_item = (array) end($retrieve_data);
	?>
	<p>Data since: <?php echo date('F j, Y, g:i a', strtotime($last_item['time']));?>
	<div class="d-flex justify-content-around" style="display:flex;flex-direction:row;justify-content:center;text-align:center;">
		<div class="counter-container" style="margin:40px;font-weight:bold;">
			<p>Today</p>
			<h2 style="font-size:30px;"><?php echo $today_counter;?></h2>
		</div>
		<div class="counter-container" style="margin:40px;font-weight:bold;">
			<p>Month</p>
			<h2 style="font-size:30px;"><?php echo $month_counter;?></h2>
		</div>
		<div class="counter-container" style="margin:40px;font-weight:bold;">
			<p>Year</p>
			<h2 style="font-size:30px;"><?php echo $year_counter;?></h2>
		</div>
		<div class="counter-container" style="margin:40px;font-weight:bold;">
			<p>All Time</p>
			<h2 style="font-size:30px;"><?php echo $all_time_counter;?></h2>
		</div>
	</div>
	<p>Last 25 leads</p>
	<table class="widefat fixed" style="" cellspacing="0">
		<th class="" scope="row">#</th>
		<th class="" scope="row">ID</th>
		<th class="" scope="row">Time</th>
		<th class="" scope="row">Campaign Name</th>
		<th class="" scope="row">Page Name</th>
		<th class="" scope="row">IP Address</th>
		<tfoot>
		<?php 
		// get first 25
		$retrieve_data = array_slice($retrieve_data, 0, 25, true);
		?>

		<?php foreach ($retrieve_data as $key => $value):?>

			<tr>
				<td class="column-id"><?php echo $key;?></td>
				<td class="column-id"><?php echo $value->id;?></td>
				<td class="column-time"><?php echo date('F j, Y, g:i a', strtotime($value->time));?></td>
				<td class="column-name"><?php echo $value->name;?></td>
				<td class="column-url"><?php echo $value->url;?></td>
				<td class="column-ip_address"><?php echo $value->ip_address;?></td>
			</tr>
		<?php endforeach;?>
		</tfoot>
	</table>

</div>

<?php
}
