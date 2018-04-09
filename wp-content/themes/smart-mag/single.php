<?php 

/**
 * The single post template is selected based on your global Theem Settings or the post 
 * setting. 
 * 
 * Template files for the post layouts are as follows:
 * 
 * Classic: Uses content.php
 * Post Cover: partials/single/layout-cover.php
 * Modern: partials/single/layout-modern.php
 */

$template = Bunyad::posts()->meta('layout_template');
$partial  = 'partials/single/layout-' . $template;

if (!$template OR strstr($template, 'classic')) {
	$template = 'classic';
	$partial  = 'content';
}

if ($template != 'classic') {
	Bunyad::core()->add_body_class('post-layout-' . $template);
}

?>

<?php get_header(); ?>

<div class="main wrap cf">

	<?php if (in_array($template, array('cover'))): // Cover layout is a special case - defines it's own structure ?>
		
		<?php get_template_part($partial); ?>
	
	<?php else: ?>
	
	<div class="row">
	
		<div class="col-8 main-content">
		
			<?php while (have_posts()) : the_post(); ?>
	
				<?php 
					
					$panels = get_post_meta(get_the_ID(), 'panels_data', true);
					
					if (!empty($panels) && !empty($panels['grid'])):
						
						get_template_part('content', 'builder');
					
					else:
					
						get_template_part($partial, 'single');
						
					endif; 
				?>
	
				<div class="comments">
				<?php comments_template('', true); ?>
				</div>
	
			<?php endwhile; // end of the loop. ?>
	
		</div>
		
		<?php Bunyad::core()->theme_sidebar(); ?>
	
	</div> <!-- .row -->
		
	<?php endif; ?>

</div> <!-- .main -->

<?php get_footer(); ?>