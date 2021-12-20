<?php

/**
 * Custom page template for Legendary SEO plugin - 
 * Place this renamed as legendary_seo_template.php in your parent theme to overwrite the default in the active theme 
 * (must be checked in settings to override)
 */

 ?>


<?php

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

if(isset($_COOKIE['ll_marketing_tracker'])):?>

<?php endif;?>

<?php get_header(); ?>
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

<!-- add style overrides here directly to the page if needed -->
<style>
.ll-post-image{
	width: 50%;
	float:right;
	margin:20px;
}

.blog-img img {
    filter: drop-shadow(0px 1px 8px rgba(0, 0, 0, 0.25));
    width: 100%;
	
}

.blog-img img {
    filter: drop-shadow(0px 1px 8px rgba(0, 0, 0, 0.25));
    width: 100%;
}

.blog-img {
    border: 20px solid #fff;
    box-shadow: 0px 1px 8px rgb(0 0 0 / 25%);
}

@media all and (max-width: 1470px) and (min-width:1000px) {

}

@media all and (max-width: 999px) and (min-width:600px) {
	.ll-post-image{
		width:100%;
		float:none;
		margin:0;
		margin-bottom:20px;
	}
}

@media all and (max-width: 600px) and (min-width:200px) {
	.ll-post-image{
		width:100%;
		float:none;
		margin:0;
		margin-bottom:20px;
	}
}
</style>


 <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
 <div class="legendary-entry">
	 <h1><?php the_title();?></h1>
	 <?php echo get_option('ll_seo_option_template_cta_first');?>
	 <?php if(has_post_thumbnail()):?>
		<div class="blog-img ll-post-image">
			<div class="post-thumbnail">
				<?php the_post_thumbnail('large', ['class' => '', 'title' => get_the_title(), 'alt' => get_the_title()]);?>
			</div>
		</div>
		<?php endif;?>
 		<?php the_content(); ?>
 		<?php echo get_option('ll_seo_option_template_cta_last');?>
 	</div>

 <?php endwhile; else : ?>
 	<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
 <?php endif; ?>

<?php get_footer(); ?>