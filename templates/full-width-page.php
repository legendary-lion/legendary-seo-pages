<?php

/**
 * Custom page template for Legendary SEO plugin - 
 * Place this renamed as legendary_seo_template.php in your parent theme to overwrite the default in the active theme 
 * (must be checked in settings to override)
 */

 ?>
<?php get_header(); ?>

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