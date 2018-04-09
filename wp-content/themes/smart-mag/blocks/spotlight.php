<?php
/**
 * Spotlight Block
 */
?>

<?php
/**
 * Process the block and setup the query
 * 
 * @var array  $atts  Shortcode attribs by Bunyad_ShortCodes::__call()
 * @var string $tag   Shortcode used, example: highlights
 * 
 * @see Bunyad_ShortCodes::__call()
 */
$block = new Bunyad_Theme_Block($atts, $tag);
$query = $block->process()->query;


// Number of large posts to show
$large_posts = 1;

?>

<section <?php Bunyad::markup()->attribs('spotlight-block', array('class' => array('block-wrap', 'spotlight-block'), 'data-id' => $block->block_id)); ?>>

	<?php echo $block->output_heading(); ?>
	
	<div class="block-content">
	
		<div>
			<?php 
				// Show one Large Post
				while ($query->have_posts()): $query->the_post();?>
			
				<article class="overlay-post">
						
					<a href="<?php the_permalink(); ?>" class="image-link">
						<?php 
							the_post_thumbnail(
								(Bunyad::core()->get_sidebar() == 'none' ?  'main-full' : 'overlay-large'), 
								array('class' => 'image', 'title' => strip_tags(get_the_title()))
							); 
						?>
						
						<?php if (get_post_format()): ?>
							<span class="post-format-icon <?php echo esc_attr(get_post_format()); ?>"><?php
								echo apply_filters('bunyad_post_formats_icon', ''); ?></span>
						<?php endif; ?>
						
						<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
					</a>
					
					<div class="meta">
						<?php echo Bunyad::blocks()->cat_label(); ?>
						<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					</div>
					
				</article>
				
				<?php if (($query->current_post+1) == $large_posts): break; endif; ?>
			
			<?php endwhile; ?>
				
				
			<div class="row cf">
				<?php 
					// Grid Posts
					while ($query->have_posts()): $query->the_post(); ?>
	
					<article class="col-4 grid-post">			
						<a href="<?php the_permalink() ?>"><?php 
							the_post_thumbnail('post-thumbnail',
								(Bunyad::core()->get_sidebar() == 'none' ? 'overlay-large' : 'grid-small'),
								array('title' => strip_tags(get_the_title()))
							); 
						?>
						</a>
							
						<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						
					</article>
				
				<?php endwhile; ?>
	
			</div>
		</div>
	
	</div> <!-- .block-content -->
	
</section>
	
<?php wp_reset_postdata(); ?>