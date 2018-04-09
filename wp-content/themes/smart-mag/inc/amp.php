<?php
/**
 * AMP compatibility for SmartMag - extend official AMP plugin by Automattic
 */
class Bunyad_Theme_Amp
{
	public function __construct()
	{
		add_action('wp', array($this, 'setup'));
		
		// AMP plugin active?
		if (defined('AMP__VERSION')) {
			add_filter('bunyad_theme_options', array($this, 'add_theme_options'));
		}
		
		if (is_admin()) {
			// Remove customizer link for AMP plugin
			remove_action('admin_menu', 'amp_add_customizer_link');
		}
	}
	
	/**
	 * Setup hooks for AMP plugin by Automattic
	 */
	public function setup()
	{
		// Only if it's AMP
		if (!function_exists('is_amp_endpoint') OR !is_amp_endpoint()) {
			return;
		}
		
		// Load required files
		require_once locate_template('inc/amp/walker.php');
		require_once locate_template('inc/amp/ads.php');
		
		$GLOBALS['content_width'] = 702;
		
		add_filter('amp_post_template_dir', array($this, 'template_dir'));
		add_filter('amp_post_template_data', array($this, 'setup_data'));
		
		// Add analytics if available
		add_filter('amp_post_template_analytics', array($this, 'add_analytics'));
		
	}
	
	/**
	 * Templates directory
	 */
	public function template_dir($path) {
	
		// Check child theme first
		$path = get_stylesheet_directory() . '/amp';
		
		if (!file_exists($path)) {
			$path = get_template_directory() . '/amp';
		}
		
		return $path;
	}
	
	/**
	 * Setup and extend post data
	 */
	public function setup_data($data)
	{
		$data['font_urls'] = array();
		
		/** 
		 * Process and set featured video data
		 */
		if (is_single() && Bunyad::posts()->meta('featured_video')) {
			
			
			require_once locate_template('inc/amp/content.php');
			
			$amp_content = new Bunyad_Theme_Amp_Content(
				Bunyad::posts()->meta('featured_video'),
				array(
					'AMP_Twitter_Embed_Handler'   => array(),
					'AMP_YouTube_Embed_Handler'   => array(),
					'AMP_Instagram_Embed_Handler' => array(),
					'AMP_Vine_Embed_Handler'      => array(),
					'AMP_Facebook_Embed_Handler'  => array(),
					'AMP_Gallery_Embed_Handler'   => array(),
				),
				array(
					 'AMP_Video_Sanitizer'  => array(),
					 'AMP_Audio_Sanitizer'  => array(),
					 'AMP_Iframe_Sanitizer' => array(
						 'add_placeholder'  => true,
					 ),
				),
				array(
					'content_max_width' => $data['content_max_width'],
				)
			);
			
			// Add relevant amp scripts for the components necessary
			$data['amp_component_scripts'] = array_merge(
				$data['amp_component_scripts'], 
				$amp_content->get_amp_scripts()
			);
			
			// Get the content
			$data['featured_video'] = $amp_content->get_amp_content();
		}
		
		/**
		 * Setup logo schema
		 */
		$data['the_logo'] = $logo = $this->get_logo();
		
		if (!empty($logo)) {
			$data['metadata']['publisher']['logo'] = array(
				'@type'  => 'ImageObject',
				'url'    => $logo['url'],
				'height' => $logo['height'],
				'width'  => $logo['width'],
			); 
		}
		
		return $data;
	}
	
	/**
	 * Add Analytics Code
	 * 
	 * @param array $analytics
	 */
	public function add_analytics($analytics)
	{
		if (!Bunyad::options()->amp_analytics_ga) {
			return $analytics;
		}
		
		$analytics = (array) $analytics;
		
		// https://developers.google.com/analytics/devguides/collection/amp-analytics/
		$analytics['amp-googleanalytics'] = array(
			'type' => 'googleanalytics',
			'attributes'  => array(),
			'config_data' => array(
				'vars' => array(
					'account' => Bunyad::options()->amp_analytics_ga
				),
				'triggers' => array(
					'trackPageview' => array(
						'on' => 'visible',
						'request' => 'pageview',
					),
				),
			),
		);
		
		return $analytics;
	}
	
	/**
	 * AMP Logo
	 * 
	 * @param boolean $fallback  Fallback to desktop logo if others missing?
	 */
	public function get_logo()
	{
		$logo = false;
		
		// amp_logo has higher priority 
		$logo_image = Bunyad::options()->image_logo_mobile;
		if (Bunyad::options()->amp_logo) {
			$logo_image = Bunyad::options()->amp_logo;	
		}
		
		if (!empty($logo_image)) {
			$id = attachment_url_to_postid($logo_image);
			if ($id) {
				list($url, $width, $height) = wp_get_attachment_image_src($id, 'full');
				
				// 2x logo is expected
				$logo = array(
					'url'    => $url,
					'width'  => $width / 2,
					'height' => $height / 2,
				);
			}
		}
		
		return $logo;
	}
	
	/**
	 * Add theme options for AMP
	 */
	public function add_theme_options($options)
	{
		$options[] = include locate_template('inc/amp/options.php');
		return $options;
	}
}

// init and make available in Bunyad::get('amp')
Bunyad::register('amp', array(
	'class' => 'Bunyad_Theme_Amp',
	'init' => true
));