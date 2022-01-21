<?php 

// Register Settings For the Plugin
function ll_seo_register_settings() {

	// add an option and register a setting
	add_option( 'll_seo_option_template');
	register_setting( 'll_seo_options_group', 'll_seo_option_template');

	add_option( 'll_seo_inject_page_links_to_footer');
	register_setting( 'll_seo_options_group', 'll_seo_inject_page_links_to_footer');

	add_option( 'll_seo_option_template_cta_first');
	register_setting( 'll_seo_options_group', 'll_seo_option_template_cta_first');


	add_option( 'll_seo_option_template_cta_last');
	register_setting( 'll_seo_options_group', 'll_seo_option_template_cta_last');

}
add_action( 'admin_init', 'll_seo_register_settings' );



// ll_seo_options_page will be the function that will be called, when this page is displayed.
function ll_seo_options_page_callback() {
	$ll_template_setting_value = get_option('ll_seo_option_template');
	?>
  	<div class="container">
	  	<h1>Legendary SEO Settings</h1>
	  	<hr/>
	  	<form method="post" action="options.php">
		  	<?php settings_fields( 'll_seo_options_group' ); ?>
		  	<table cellpadding="10">
				<tr>
					<td style="text-align:left;" ><input type="checkbox" id="ll_seo_option_template" name="ll_seo_option_template" value="1" <?php checked( '1', get_option( 'll_seo_option_template' ) );  ?> /></td>
					<th style="text-align:left;" scope="row"><label for="ll_seo_option_template">Use custom page template override? </label></th>
				</tr>
				<p><small>This option will check your <u>Parent Theme</u> and <u>Child Theme</u> for page template:</small> <b>legendary_seo_template.php</b></p>
				<tr>
					<td style="text-align:left;" ><input type="checkbox" id="ll_seo_inject_page_links_to_footer" name="ll_seo_inject_page_links_to_footer" value="1" <?php checked( '1', get_option( 'll_seo_inject_page_links_to_footer' ) ); ?> /></td>
					<th style="text-align:left;"  scope="row"><label for="ll_seo_inject_page_links_to_footer">Inject page links to footer? </label></th>
				</tr>
		  	</table>

		  	<p><b><u>Shortcodes / Options</u></b></p>
		  	<p><small>Footer Links<br />echo do_shortcode("[list-seo-pages]");</small></p><hr>
		  	<p><small>CTA Above Content<br />get_option('ll_seo_option_template_cta_first');</small></p><hr>
		  	<p><small>CTA Below Content<br />get_option('ll_seo_option_template_cta_last');</small></p><hr>

		  	<hr/>

			<p>CTA Top HTML</p>
			<textarea style="width:50%;min-height:200px;" id="ll_seo_option_template_cta_first" name="ll_seo_option_template_cta_first"/><?php echo get_option( 'll_seo_option_template_cta_first'); ?></textarea>
			<p>CTA Bottom HTML</p>
			<textarea style="width:50%;min-height:200px;" id="ll_seo_option_template_cta_last" name="ll_seo_option_template_cta_last"/><?php echo get_option( 'll_seo_option_template_cta_last'); ?></textarea>

		  	<?php  submit_button(); ?>
	  	</form>
  	</div>

	<?php
} 