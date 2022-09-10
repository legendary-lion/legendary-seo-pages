<?php

// Register SEO Pages Custom Post Type
function ll_seo_pages_post_type() {

	$labels = array(
	'name'                => _x( 'Legendary SEO Pages', 'Post Type General Name', 'text_domain' ),
	'singular_name'       => _x( 'SEO Page', 'Post Type Singular Name', 'text_domain' ),
	'menu_name'           => __( 'Legendary SEO', 'text_domain' ),
	'parent_item_colon'   => __( 'Parent Page:', 'text_domain' ),
	'all_items'           => __( 'All Pages', 'text_domain' ),
	'view_item'           => __( 'View Page', 'text_domain' ),
	'add_new_item'        => __( 'Add New Page', 'text_domain' ),
	'add_new'             => __( 'Add New', 'text_domain' ),
	'edit_item'           => __( 'Edit Page', 'text_domain' ),
	'update_item'         => __( 'Update Page', 'text_domain' ),
	'search_items'        => __( 'Search SEO Pages', 'text_domain' ),
	'not_found'           => __( 'Not found', 'text_domain' ),
	'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);


	$args = array(
		'label'               => __( 'SEO Pages', 'text_domain' ),
		'description'         => __( 'Post Type Description', 'text_domain' ),
		'labels'              => $labels,
		// commented out to prevent default categories from appearing in SEO page types
		// 'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-dashboard',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'supports' => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'has_archive' => false,
	);

	register_post_type( 'SEO Pages', $args );
	// activate the below function only when 404 errors are present after registering the custom post type
	// flush_rewrite_rules();
}
add_action( 'init', 'll_seo_pages_post_type', 99 );


// set up options page
function ll_seo_pages_options_page() {
	add_submenu_page(
		'edit.php?post_type=seopages',
		'Legendary SEO Page Options',
		'Configuration',
		'manage_options',
		'll-seo-options',
		'll_seo_options_page_callback' );
}
add_action('admin_menu', 'll_seo_pages_options_page');




// /**
//  * Proper way to enqueue scripts and styles
//  */
// function ll_seo_pages_scripts() {

//     wp_enqueue_style( 'style-name', get_stylesheet_uri() );
//     // wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );

// }
// add_action( 'wp_enqueue_scripts', 'll_seo_pages_scripts' );



/**
 * Include CSS file
 */
function legendary_seo_styles() {

    wp_register_style( 'legendary-seo-styles',  plugin_dir_url( __FILE__ ) . '../css/legendary-seo-styles.css' );
    wp_enqueue_style( 'legendary-seo-styles' );

}
add_action( 'wp_enqueue_scripts', 'legendary_seo_styles' );



// Create database for user tracking 

global $ll_marketing_tracker_db_version;
$ll_marketing_tracker_db_version = '1.0';

function ll_marketing_tracker_install() {
	global $wpdb;
	global $ll_marketing_tracker_db_version;

	$table_name = $wpdb->prefix . 'll_marketing_tracker';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		url varchar(55) DEFAULT '' NOT NULL,
		location varchar(55) DEFAULT '' NOT NULL,
		ip_address varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'll_marketing_tracker_db_version', $ll_marketing_tracker_db_version );
}
register_activation_hook( LLSEOPAGES_MAIN_FILE_PATH, 'll_marketing_tracker_install' );


// function ll_marketing_tracker_install_data() {
// 	global $wpdb;

// 	$welcome_name = 'Mr. WordPress';
// 	$url = 'https://www.awesome.com/test-page/';

// 	$table_name = $wpdb->prefix . 'll_marketing_tracker';

// 	$wpdb->insert( 
// 			$table_name, 
// 			array( 
// 					'time' => current_time( 'mysql' ), 
// 					'ip_address' => $_SERVER['REMOTE_ADDR'], 
// 					'name' => $welcome_name, 
// 					'location' => '', 
// 					'url' => $url, 
// 				) 
// 			);
// }
// register_activation_hook( __FILE__, 'll_marketing_tracker_install_data' );


// add admin css styles
// Update CSS within in Admin (remove eye in SEO Yoast preview)
function my_custom_fonts() {
	
	echo '<style>
	.snippet-editor__heading-icon-eye{
		background:transparent !important;
    } 
	.yoast-section__heading-icon{
		padding-left: 45px !important;
	}
	</style>';
}
add_action('admin_head', 'my_custom_fonts');


// Template Redirect and Custom Template Options Check
function ll_get_custom_page_template($template) {
	if( is_singular('seopages') ) {
		$ll_template_setting_value = get_option('ll_seo_option_template');
		if ( $ll_template_setting_value == '1' ){

			$locate = locate_template('legendary_seo_template.php');
			$template = $locate;
			
		} else {
			
			$template = plugin_dir_path( __FILE__ ) . '../templates/full-width-page.php';
		}
	}
	
	return $template;
}
add_filter('template_include', 'll_get_custom_page_template', 99);


// rewrite the slug out of the url
function seo_pages_remove_slug( $post_link, $post, $leavename ) {

    if ( 'seopages' != $post->post_type || 'publish' != $post->post_status ) {

        return $post_link;

    }

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;

}
add_filter( 'post_type_link', 'seo_pages_remove_slug', 10, 3 );


// prevent 404 when requesting without a namespaced slug
function seo_pages_request( $query ) {

    if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
    }

    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'seopages', 'page' ) );
    }
}
add_action( 'pre_get_posts', 'seo_pages_request' );