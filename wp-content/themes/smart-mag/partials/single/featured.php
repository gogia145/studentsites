<?php 
/**
 * Partial: Featured part of single posts
 */

/*
 * Don't output if featured image is disabled and it is a single post.
 * Featured images are output in loops/non-single regardless of being disabled.
 */
if (Bunyad::posts()->meta('featured_disable') && is_single()) {
	return;
}

$caption_class = isset($caption_class) ? $caption_class : 'caption';

?>

	<div class="featured">
		<?php if (get_post_format() == 'gallery'): // get gallery template ?>
		
			<?php get_template_part('partial-gallery'); ?>
			
		<?php elseif (Bunyad::posts()->meta('featured_video')): // featured video available? ?>
		
			<div class="featured-vid">
				<?php echo apply_filters('bunyad_featured_video', Bunyad::posts()->meta('featured_video')); ?>
			</div>
			
		<?php else:  ?>
		
			<?php 
				/**
				 * Normal featured image
				 */
		
				$caption = get_post(get_post_thumbnail_id())->post_excerpt;
				$url     = get_permalink();
				
				// on single page? link to image
				if (is_single()):
					$url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); 
					$url = $url[0];
				endif;
		?>
		
			<a href="<?php echo $url; ?>" title="<?php the_title_attribute(); ?>">
			
			<?php if (Bunyad::options()->blog_thumb != 'thumb-left'): // normal container width image ?>
			
				<?php if ((!in_the_loop() && Bunyad::posts()->meta('layout_style') == 'full') OR Bunyad::core()->get_sidebar() == 'none'): // largest images - no sidebar? ?>
			
					<?php the_post_thumbnail('main-full', array('title' => strip_tags(get_the_title()))); ?>
			
				<?php else: ?>
				
					<?php the_post_thumbnail('main-featured', array('title' => strip_tags(get_the_title()))); ?>
				
				<?php endif; ?>
				
			<?php else: ?>
				<?php the_post_thumbnail('thumbnail', array('title' => strip_tags(get_the_title()))); ?>
			<?php endif; ?>
							
			</a>
							
			<?php if (!empty($caption)): // have caption ? ?>
					
				<div class="<?php echo $caption_class; ?>"><?php echo $caption; ?></div>
					
			<?php endif;?>
			
		<?php endif; // end normal featured image ?>
	</div>