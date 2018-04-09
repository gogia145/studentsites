<?php
/**
 * Focus block - for both news focus and focus grid block
 */

// Is it news focus?
$the_block = (empty($the_block) ? 'news-focus' : $the_block);

// Number of highlighted posts
$highlights = (empty($highlights) ? 1 : intval($highlights));

/**
 * Legacy block system variable conversion
 */
if (!isset($atts['_is_filter']) && empty($atts['filters'])) {
	$atts['filters'] = 'category';   // Always enabled due to legacy reasons
}

if (!empty($sub_cats)) {
	$atts['filters_terms'] = $sub_cats;
}
// entered sub tags instead?
else if (!empty($sub_tags)) {
	$atts['filters'] = 'tag';
	$atts['filters_terms'] = $sub_cats; 	
}

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

?>

<section <?php Bunyad::markup()->attribs('focus-block', array('class' => array('block-wrap', $the_block), 'data-id' => $block->block_id)); ?>>
	
	<?php echo $block->output_heading(); ?>
		
	<div class="block-content">

		<div class="row b-row highlights">

			<div class="column half b-col blocks">
			
			<?php
			// Main posts - better highlighted
			$count = 0;
			while ($query->have_posts()): 
			
				$query->the_post(); 
				$count++;
			
				// Image sizes can be different for news focus vs focus grid
				if ($the_block == 'news-focus') {
					$the_image = Bunyad::core()->get_sidebar() != 'none' && $column == '1/3' 
							? 'gallery-block' 
							: (Bunyad::core()->get_sidebar() == 'none' ?  'main-featured' : 'main-block');
								
				}
				else {
					$the_image = (Bunyad::core()->get_sidebar() == 'none' ?  'main-featured' : 'focus-grid-large');
				}
			
				?>
				
				<article>
						
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="image-link">
						<?php the_post_thumbnail(
									$the_image, 
									array('class' => 'image', 'title' => strip_tags(get_the_title()))); ?>
						
						<?php if (get_post_format()): ?>
							<span class="post-format-icon <?php echo esc_attr(get_post_format()); ?>"><?php
								echo apply_filters('bunyad_post_formats_icon', ''); ?></span>
						<?php endif; ?>
						
						<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
					</a>
					
					<?php echo Bunyad::blocks()->meta('above'); ?>
					
					<h2 class="post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					
					<?php echo Bunyad::blocks()->meta('below'); ?>
					
					<div class="excerpt">
						<?php
	
							// Excerpt option key for news focus is excerpt_length_news_focus
							$excerpt = 'excerpt_length_' . ($the_block == 'news-focus' ? 'news_focus' : 'focus_grid');
							
							echo Bunyad::posts()->excerpt(null, Bunyad::options()->{$excerpt}, array('add_more' => false)); ?>
					</div>
					
				</article>
				
				<?php 	
					if ($count == $highlights) {
						break;
					}
				?>
				
			
			<?php endwhile; ?>
			
			</div>
			
			
			<ul class="column half b-col block posts-list thumb">
	
			<?php while ($query->have_posts()): $query->the_post(); ?>
			
				<?php if ($the_block == 'news-focus'): // other posts, in a list ?>
				
					<li>
					
						<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>
	
						<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
							<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
						<?php endif; ?>
						
						</a>
						
						<div class="content">
	
							<?php echo Bunyad::blocks()->meta('above', 'block-small'); ?>
						
							<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
								<?php if (get_the_title()) the_title(); else the_ID(); ?></a>
								
							<?php echo Bunyad::blocks()->meta('below', 'block-small'); ?>
								
						</div>
						
					</li>
				
				<?php elseif ($the_block == 'focus-grid'): ?>
				
					<li class="post">
					
						<a href="<?php the_permalink() ?>" class="small-image"><?php 
							the_post_thumbnail(
								apply_filters('bunyad_block_image', 'focus-grid-small'), 
								array('title' => strip_tags(get_the_title())
							)); 
							?>
							
							<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
								<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
							<?php endif; ?>
						</a>
						
						<div class="content">
	
							<?php echo Bunyad::blocks()->meta('above', 'block-small'); ?>
						
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							
							<?php echo Bunyad::blocks()->meta('below', 'block-small'); ?>
								
						</div>
						
					</li>
				
				<?php endif; ?>
			
			<?php endwhile; ?>
			
			</ul>
				
			<?php wp_reset_postdata(); ?>
			
		</div>
	
	</div> <!-- .block-content -->
		
</section>