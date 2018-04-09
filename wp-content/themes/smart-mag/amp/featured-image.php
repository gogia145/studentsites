<?php
/**
 * AMP Featured image partial
 */

if (Bunyad::posts()->meta('featured_disable') && is_single()) {
	return;
}

if (!has_post_thumbnail()) {
	//return;
}

// Gallery format has a gallery below
if (get_post_format() == 'gallery') { 
	return;
}

?>

<?php if (!empty($this->data['featured_video'])): // featured video available? ?>

	<div class="featured-vid">
		<?php echo $this->data['featured_video']; ?>
	</div>

<?php else: ?>

	<?php
	/**
	 * Normal featured image
	 */
	$featured_html = get_the_post_thumbnail(null, 'main-featured');
	$caption = get_post(get_post_thumbnail_id())->post_excerpt;
	
	list($sanitized_html, $featured_scripts, $featured_styles) = AMP_Content_Sanitizer::sanitize(
		$featured_html,
		array( 'AMP_Img_Sanitizer' => array() ),
		array(
			'content_max_width' => $this->get( 'content_max_width' ),
		)
	);
	
	$amp_html = $sanitized_html;
	?>
	
	<figure class="featured-image wp-caption">
		<?php echo $amp_html; // amphtml content; no kses ?>
		<?php if ($caption) : ?>
			<p class="wp-caption-text">
				<?php echo wp_kses_data($caption); ?>
			</p>
		<?php endif; ?>
	</figure>

<?php endif; ?>