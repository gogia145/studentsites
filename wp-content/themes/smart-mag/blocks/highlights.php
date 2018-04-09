<?php 
/**
 * Highlights Block - a special block that supports multiple columns by default
 */

if ($columns == 2 OR $columns == 3):

	$cats = array_map('trim', explode(',', $cats));
	$tags = array_map('trim', explode(',', $tags));
	$headings = array_map('trim', explode(',', $headings));
	$offsets = array_map('trim', explode(',', $offsets));
	
?>
	[columns class="highlights-box<?php echo ($columns == 3 ? ' three-col' : ''); ?>"]
	
	<?php foreach (range(0, $columns-1) as $key): 
			if (!$tags[$key] && !$cats[$key]) {
				continue;
			}
	?>
		[highlights column="<?php echo ($columns == 3 ? '1/3' : 'half'); ?>" 
			cat="<?php echo esc_attr($cats[$key]); ?>"
			tax_tag="<?php echo esc_attr($tags[$key]); ?>"
			title="<?php echo esc_attr($headings[$key]); ?>" 
			sort_by="<?php echo esc_attr($sort_by); ?>" sort_order="<?php echo esc_attr($sort_order); ?>"
			posts="<?php echo $posts; ?>"
			taxonomy="<?php echo $taxonomy; ?>"
			offset="<?php echo esc_attr($offsets[$key]); ?>" 
			post_type="<?php echo esc_attr($post_type); ?>"
			heading_type="<?php echo esc_attr($heading_type); ?>"
		/]	
	<?php endforeach; ?>
	
	[/columns]

<?php
	
	return;
	
endif;
?>

<?php if ($column): ?>
	[column size="<?php echo $column; ?>"]
<?php endif; ?>

<?php

// Legacy: From old block system
if (!empty($tax_tag)) {
	$atts['tags'] = $tax_tag;
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

$count = 0;
$type = 'thumb';
$heading = 'overlay';

// Auto-detected term missing? No heading then
if (!is_object($block->term)) {
	$heading_type = 'none';
}

if ($heading_type == 'block') {
	$heading = 'section-head';
}

// Small thumbs and auto heading to section-head for 3 cols 
if ($column == '1/3' && Bunyad::core()->get_sidebar() != 'none') {
	$type = '';	
	
	if ($heading_type == 'auto') {
		$heading = 'section-head';
	}
}


// no heading
if ($heading_type == 'none') {
	$heading = '';
}

?>

	<section <?php Bunyad::markup()->attribs('highlights-block', array('class' => array('block-wrap', $tag), 'data-id' => $block->block_id)); ?>>
		
		<?php if ($heading == 'section-head'): ?>
			
			<div class="section-head cat-text-<?php echo $block->term->term_id; ?>">
				<?php echo $block->title_link; ?>
			</div>
							
		<?php endif; ?>
		
		<div class="highlights">
				
		<?php while ($query->have_posts()): $query->the_post(); $count++; ?>
		
		<?php if ($count === 1): // main post - better highlighted ?>
		
			<?php if ($heading == 'overlay'): ?>
				<span class="cat-title larger cat-<?php echo $block->term->term_id; ?>">
					<?php echo $block->title_link; ?>
				</span>
			<?php endif; ?>
			
			<article>
					
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="image-link">
					<?php 
						the_post_thumbnail(
								(Bunyad::core()->get_sidebar() != 'none' && $column == '1/3' 
										? 'gallery-block' 
										: (Bunyad::core()->get_sidebar() == 'none' ?  'main-featured' : 'main-block')
								), 
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
				
				<?php if ($type == 'thumb'): ?>
				
				<div class="excerpt">
					<?php echo Bunyad::posts()->excerpt(null, Bunyad::options()->excerpt_length_highlights, array('add_more' => false)); ?>
				</div>
								
				<?php endif; ?>
				
			</article>
			
		<?php if ($query->post_count > 1): ?>
						
			<ul class="block <?php echo ($type == 'thumb' ? 'posts-list thumb' : 'posts'); ?>">
			
		<?php endif; ?>
		
				
		<?php continue; endif; // main post end ?>
			
			<?php // other posts, in a list ?>

				<li>
			
			<?php if ($type == 'thumb'): ?>
				
					<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>

					<?php if (Bunyad::options()->review_show_widgets): ?>
						<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
					<?php endif; ?>
					
					</a>
					
					<div class="content">

						<?php echo Bunyad::blocks()->meta('above', 'block-small'); ?>
						
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							
						<?php echo Bunyad::blocks()->meta('below', 'block-small'); ?>
						
					</div>
				
			<?php else: ?>
			
					<i class="fa fa-angle-right"></i>
					<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
					
			<?php endif; ?>
			
				</li>
			
		<?php endwhile; ?>
			
			<?php if ($query->post_count > 1): ?> </ul> <?php endif; ?>
			
		<?php wp_reset_postdata(); ?>
		
		</div>
	
	</section>
	
<?php if ($column): ?>
	[/column]
<?php endif; ?>