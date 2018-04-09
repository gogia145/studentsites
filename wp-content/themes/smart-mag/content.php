<?php

/**
 * Content Template is used for every post format and used on single posts
 * 
 * Note: This is only used on classic post layout. Check partials/single/ folder for 
 * other more post layouts (layout-{name}.php). This template is called by single.php
 */


$classes = get_post_class();


// using the title above featured image variant?
$layout = Bunyad::posts()->meta('layout_template');
if (is_single() && $layout == 'classic-above') {
	$classes[] = 'title-above'; 
}
else {
	$layout = 'classic';
}

?>

<article id="post-<?php the_ID(); ?>" class="<?php echo join(' ', $classes); ?>">
	
	<header class="post-header cf">
	
	<?php if ($layout == 'classic-above'): ?>
	
		<?php get_template_part('partials/single/classic-title-meta'); ?>
	
	<?php endif; ?>

	<?php get_template_part('partials/single/featured'); ?>
	
	<?php if ($layout != 'classic-above'): ?>
	
		<?php get_template_part('partials/single/classic-title-meta'); ?>
	
	<?php endif; ?>
		
	</header><!-- .post-header -->

	
<?php
	// page builder for posts enabled?
	$panels = get_post_meta(get_the_ID(), 'panels_data', true);
	if (!empty($panels) && !empty($panels['grids']) && is_singular() && !is_front_page()):
?>
	
	<?php Bunyad::posts()->the_content(); ?>

<?php 
	else: 
?>

	<div class="post-container cf">
	
		<div class="post-content-right">
			<div class="post-content description <?php echo (Bunyad::posts()->meta('content_slider') ? 'post-slideshow' : ''); ?>">
	
			<?php 
				// get post body content
				get_template_part('partials/single/post-content'); 
			?>
		
			</div><!-- .post-content -->
		</div>
		
	</div>
	
<?php 
	endif; // end page builder blocks test
?>
	
	<?php 
		// add social share
		get_template_part('partials/single/social-share');
	?>
		
</article>

<?php 

// add next/previous 
get_template_part('partials/single/post-navigation');

// add author box
get_template_part('partials/single/author-box');

// add related posts
get_template_part('partials/single/related-posts');

