<?php 

/**
 * "loop" to display posts when using an existing query. Uses content.php template
 * to render in normal format.
 */

?>

	<?php
	
	global $bunyad_loop;
	
	if (!is_object($bunyad_loop)) {
		$bunyad_loop = $wp_query;
	}
	
	if ($bunyad_loop->have_posts()):
	
		$attribs = array('class' => array('row b-row listing', 'meta-' . Bunyad::options()->meta_position));
		
		// Infinite load?
		if (Bunyad::options()->pagination_type == 'infinite') {
			$attribs['data-infinite'] = Bunyad::markup()->unique_id('listing-'); 
		}
		
		// set larger image when full-width, for 2-col grid
		$image = Bunyad::core()->get_sidebar() == 'none' ?  'main-featured' : 'main-block';
		
		// Grid type
		$columns = Bunyad::registry()->loop_grid;
		$col_class = 'half';
		
		if ($columns) {
			
			if ($columns == 1) {
				$col_class = '';
			}
			else if ($columns == 3) {
				
				// Change image to smaller for 3 col
				$image = Bunyad::core()->get_sidebar() == 'none' ?  'main-block' : 'gallery-block';
				$col_class = 'one-third';
			}
		}
		else {
			$columns = 2;
		}
		
		$attribs['class'][] = 'grid-' . $columns;
		
		// Excerpts
		$excerpts = isset($excerpts) ? $excerpts : true;
		
	?>
	
	<div <?php Bunyad::markup()->attribs('loop', $attribs); ?>>
		
		<?php while ($bunyad_loop->have_posts()): $bunyad_loop->the_post(); ?>
			
		<div class="column <?php echo esc_attr($col_class); ?> b-col">
		
			<article <?php post_class('highlights'); ?>>

			<?php echo Bunyad::blocks()->cat_label(); ?>
				
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="image-link">
					<?php the_post_thumbnail($image, array('class' => 'image', 'title' => strip_tags(get_the_title()))); ?>
					
					<?php if (get_post_format()): ?>
						<span class="post-format-icon <?php echo esc_attr(get_post_format()); ?>"><?php
							echo apply_filters('bunyad_post_formats_icon', ''); ?></span>
					<?php endif; ?>

					<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
				</a>
				
				<?php echo Bunyad::blocks()->meta('above', 'listing'); ?>
				
				<h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				
				<?php echo Bunyad::blocks()->meta('below', 'listing'); ?>
				
				
				<?php if ($excerpts): // Show excerpt ?>
				
				<div class="excerpt"><?php 
					echo Bunyad::posts()->excerpt(null, Bunyad::options()->excerpt_length_modern, array('add_more' => false)); 
				?></div>
				
				<?php endif; ?>
			
			</article>
		</div>
			
		<?php endwhile;  ?>
				
	</div>
	
	
	<?php if (!Bunyad::options()->blog_no_pagination): // pagination can be disabled ?>
		
	<div class="main-pagination">
		<?php echo Bunyad::posts()->paginate(array(), $bunyad_loop); ?>
	</div>
		
	<?php endif; ?>
		

	<?php elseif (is_archive() OR is_search()): // show error on archive only ?>

		<article id="post-0" class="page no-results not-found">
			<div class="post-content">
				<h1><?php _e( 'Nothing Found!', 'bunyad' ); ?></h1>
				<p><?php _e('Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'bunyad'); ?></p>
			</div><!-- .entry-content -->
		</article><!-- #post-0 -->
	
	<?php endif; ?>
