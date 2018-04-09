<?php
/**
 * "Highlights B" Block
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
$large_posts = 2;

?>

<section <?php Bunyad::markup()->attribs('highlights-b-block', array('class' => array('block-wrap', 'highlights highlights-b'), 'data-id' => $block->block_id)); ?>>

	<?php echo $block->output_heading(); ?>
	
	<div class="block-content">
	
		<div class="container cf">
			<div class="large b-row cf">
				
			<?php while ($query->have_posts()): $query->the_post(); ?>
			
				<div class="column half b-col">
					<article>
						
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="image-link">
						<?php 
							the_post_thumbnail(
								(Bunyad::core()->get_sidebar() == 'none' ?  'main-featured' : 'main-block'), 
								array('class' => 'image', 'title' => strip_tags(get_the_title()))
							); 
						?>
						
						<?php if (get_post_format()): ?>
							<span class="post-format-icon <?php echo esc_attr(get_post_format()); ?>"><?php
								echo apply_filters('bunyad_post_formats_icon', ''); ?></span>
						<?php endif; ?>
						
						<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
					</a>
					
					<?php echo Bunyad::blocks()->meta('above', 'highlights'); ?>
					
					<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					
					<?php echo Bunyad::blocks()->meta('below', 'highlights'); ?>
					
					<div class="excerpt">
						<?php echo Bunyad::posts()->excerpt(null, Bunyad::options()->excerpt_length_highlights, array('add_more' => false)); ?>
					</div>
					
					</article>
				</div>
			
				<?php
					// This loop is for large posts only 
					if (($query->current_post+1) == $large_posts): 
						break; 
					endif; 
				?>
			
			<?php endwhile; ?>
				
			</div>
			
			<ul class="b-row posts-list thumb">
			
			<?php while ($query->have_posts()): $query->the_post(); ?>

				<li class="column half b-col">
					<article class="post cf">
						<a href="<?php the_permalink() ?>" class="image-link"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>
	
						<?php if (Bunyad::options()->review_show_widgets): ?>
							<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
						<?php endif; ?>
						
						</a>
						
						<div class="content">
	
							<?php echo Bunyad::blocks()->meta('above', 'block-small'); ?>
							
							<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
								<?php if (get_the_title()) the_title(); else the_ID(); ?></a>
								
							<?php echo Bunyad::blocks()->meta('below', 'block-small'); ?>
							
						</div>
					</article>
				</li>
			
			<?php endwhile; ?>
							
			</ul>

		</div>
	
	</div> <!-- .block-content -->
	
</section>
	
<?php wp_reset_postdata(); ?>