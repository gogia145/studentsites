<?php 
/**
 * Partial Template for Single Post "Modern Layout" - called from single.php
 */

$classes = get_post_class();

// Dynamic style enabled by default, unless overridden
$is_dynamic = (isset($is_dynamic) ? $is_dynamic : true);
?>

<article id="post-<?php the_ID(); ?>" class="<?php echo join(' ', $classes); ?>">
	
	<header class="post-header-b cf">
	
		<div class="category cf">
			<?php echo Bunyad::blocks()->cat_label(array('force_show' => true)); ?>
		</div>
	
		<div class="heading cf">
			<?php 
				$tag = 'h1';
			?>
	
			<<?php echo $tag; ?> class="post-title">
			<?php if (!is_front_page() && is_singular()): the_title(); else: ?>
			
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?></a>
					
			<?php endif;?>
			</<?php echo $tag; ?>>
		
		</div>

		<div class="post-meta-b cf">
		
			<span class="author-img"><?php echo get_avatar(get_the_author_meta('user_email'), 35); ?></span>
			
			<span class="posted-by"><?php _ex('By', 'Post Meta', 'bunyad'); ?> 
				<?php the_author_posts_link(); ?>
			</span>
			 
			<span class="posted-on">
				<time class="post-date" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
			</span>
			
			<a href="<?php comments_link(); ?>" class="comments"><i class="fa fa-comments-o"></i><?php comments_number(); ?></a>
				
		</div>	
	
	<?php get_template_part('partials/single/social-share-b')?>
	
	<?php Bunyad::core()->partial('partials/single/featured', array('caption_class' => 'wp-caption-text')); ?>
		
	</header><!-- .post-header -->

	
<?php
	// page builder for posts enabled?
	$panels = get_post_meta(get_the_ID(), 'panels_data', true);
	if (!empty($panels) && !empty($panels['grids']) && is_singular() && !is_front_page()):
?>
	
	<?php Bunyad::posts()->the_content(); ?>

<?php 
	else: 
	
		$post_classes = array('post-content');
		
		if ($is_dynamic) {
			$post_classes[] = 'post-dynamic';
		}
		
		if (Bunyad::posts()->meta('content_slider')) {
			$post_classes[] = 'post-slideshow';
		}
	
?>

	<div <?php Bunyad::markup()->attribs('post-content-modern', array('class' => $post_classes)); ?>>
	
		<?php 
			// get post body content
			get_template_part('partials/single/post-content'); 
		?>
		
	</div><!-- .post-content -->
		
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
