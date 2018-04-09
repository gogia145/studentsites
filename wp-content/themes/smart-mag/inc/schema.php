<?php
/**
 * Auto-schema data generator for common places
 */
class Bunyad_Theme_Schema 
{
	
	/**
	 * Set it up
	 */
	public function __construct()
	{
		add_action('wp_footer', array($this, 'article'));	
		add_action('wp_footer', array($this, 'review'));
	}
	
	/**
	 * Article schema - only on single page
	 */
	public function article() 
	{
		if (!is_single() OR !Bunyad::options()->schema_article) {
			return;
		}
		
		// Buggy plugins might have been playing around
		wp_reset_query();
		rewind_posts();
		
		if (!have_posts()) {
			return;
		}
		
		the_post();

		
		// Get featured image, quit if missing
		$featured = $this->get_featured_image();
		if (!$featured) {
			return;
		}
		
		// Article schema 
		$schema = array(
			'@context' => 'http://schema.org',
			'@type'    => 'Article',
			'headline' => get_the_title(),
			'url'      => get_the_permalink(),
			'image'    => $featured,
			'datePublished' => get_the_date(DATE_W3C),
			'dateModified'  => get_the_modified_date(DATE_W3C),
			'author'   => array(
				'@type' => 'Person',
				'name'  => get_the_author()
			),
			'publisher' => $this->get_publisher(),
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id'   => get_the_permalink(),
			),
			
		);
		
		echo '<script type="application/ld+json">' . json_encode($schema) . "</script>\n";
	}
	
	/**
	 * Review schema - only on single pages
	 */
	public function review()
	{
		if (!is_single() OR !Bunyad::posts()->meta('reviews')) {
			return;
		}
		
		// Buggy plugins might have been playing around
		wp_reset_query();
		rewind_posts();
		
		if (!have_posts()) {
			return;
		}
		
		the_post();
		
		// Review schema 
		$schema = array(
			'@context' => 'http://schema.org',
			'@type'    => 'Review',
			'itemReviewed' => array(
				'@type'  => 'Thing',
				'name'   => get_the_title()
			),
			'author'   => array(
				'@type' => 'Person',
				'name'  => get_the_author()
			),
			//'publisher' => $this->get_publisher(),
			'reviewRating' => array(
				'@type' => 'Rating',
				'ratingValue' => Bunyad::posts()->meta('review_overall'),
				'bestRating' => Bunyad::options()->review_scale,
			),
			
		);
		
		echo '<script type="application/ld+json">' . json_encode($schema) . "</script>\n";
		
		
	}
	
	/**
	 * Get featured image of current article
	 */
	public function get_featured_image()
	{
		$post = get_post();
		$id = get_post_thumbnail_id();
		
		if (!$id) {
			return false;
		}
		
		// Fetch the featured image meta
		$image = wp_get_attachment_image_src($id, 'main-featured');
		list($url, $width, $height) = $image;
		
		// Prepare the schema
		$data = array(
			'@type' => 'ImageObject',
			'url'   => $url,
			'width' => $width,
			'height' => $height
		);
		
		return $data;
	}
	
	/**
	 * Get publisher info
	 */
	public function get_publisher()
	{	
		$data = array(
			'@type' => 'Organization',
			'name'  => get_bloginfo('name'),
		);
		
		// Have image logo?
		if (Bunyad::options()->image_logo) {
			$data['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => Bunyad::options()->image_logo,
			);
		}
		
		return $data;
	}
}



// init and make available in Bunyad::get('schema')
Bunyad::register('schema', array(
	'class' => 'Bunyad_Theme_Schema',
	'init' => true
));