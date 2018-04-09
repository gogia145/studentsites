<?php
/**
 * General Admin functionality - hooks, methods.
 *  
 * This file serves to be the functions.php for admin functionality. Any
 * non-specific functionality is contained here.
 * 
 * Also see admin/ folder in the root.
 *
 */
class Bunyad_Theme_Admin
{
	
	public function __construct()
	{
		// require plugins before init
		$this->require_plugins();
		
		add_action('bunyad_theme_init', array($this, 'init'));
	}
	
	public function init()
	{
		/**
		 * Category meta - custom category fields 
		 */
		add_action('category_edit_form_fields', array($this, 'edit_category_meta'), 10, 2);
		add_action('category_add_form_fields', array($this, 'edit_category_meta'), 10, 2);
		
		add_action('edited_category', array($this, 'save_category_meta'), 10, 2);
		add_action('create_category', array($this, 'save_category_meta'), 10, 2);
		
		// User meta fields
		add_filter('user_contactmethods', array($this, 'add_profile_fields'));
		
		// Regenerate thumbnails can be too slow
		add_action('wp_ajax_regeneratethumbnail', array($this, 'set_image_editor_gd'), 8);
		
		// setup importer actions
		include_once locate_template('inc/admin/import.php');
	}
	
	/**
	 * Setup and recommend plugins
	 */
	public function require_plugins()
	{
		// don't load if outside admin or if user doesn't have permission
		if (!current_user_can('install_plugins')) {
			return;
		}
		
		// load the plugin activation class and plugin updater
		require_once get_template_directory() . '/lib/vendor/tgm-activation.php';
		require_once get_template_directory() . '/inc/admin/plugins-update.php';

		
		// Recommended / packaged plugins
		$plugins = array(
			array(
				'name'     	=> 'Bunyad Shortcodes', // The plugin name
				'slug'     	=> 'bunyad-shortcodes', // The plugin slug (typically the folder name)
				'source'   	=> get_template_directory() . '/lib/vendor/plugins/bunyad-shortcodes.zip', // The plugin source
				'required' 	=> true, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'version'   => '1.0.9'

			),
	
			array(
				'name'     	=> 'Bunyad Page Builder',
				'slug'     	=> 'bunyad-siteorigin-panels',
				'source'   	=> get_template_directory() . '/lib/vendor/plugins/bunyad-siteorigin-panels.zip', 
				'required' 	=> true,
				'force_activation' => false,
				'version'   => '1.3.80',
			),
			
			array(
				'name'      => 'Bunyad Widgets',
				'slug'      => 'bunyad-widgets',
				'source'    => get_template_directory() . '/lib/vendor/plugins/bunyad-widgets.zip',
				'required'  => true,
				'force_activation' => false,
				'version'   => '1.0.7'
			),
				
			array(
				'name'      => 'Sphere Core',
				'slug'      => 'sphere-core',
				'source'    => get_template_directory() . '/lib/vendor/plugins/sphere-core.zip',
				'required'  => true,
				'force_activation' => false,
				'version'   => '1.0.0'
			),
			
			array(
				'name' => 'Custom sidebars (Optional)',
				'slug' => 'custom-sidebars',
				'required' => false,
			),
			
			array(
				'name' => 'WP Retina 2x (Optional)',
				'slug' => 'wp-retina-2x',
				'required' => false,
			),
			
			array(
				'name'   => 'Contact Form 7 (Optional)',
				'slug'   => 'contact-form-7',
				'required' => false,
			),
			
			array(
				'name'   => 'Regenerate Thumbnails (Optional)',
				'slug'   => 'regenerate-thumbnails',
				'required' => false,
			),
			
			array(
				'name'     => 'Revolution Slider (Optional)',
				'slug'     => 'revslider',
				'source'   => get_template_directory() . '/lib/vendor/plugins/revslider.zip',
				'required' => false,
			)
	
		);
		
		// Set for update checking
		Bunyad::registry()->set('packaged_plugins', $plugins);

		tgmpa($plugins, array('is_automatic' => true));
		
		// set revslider as packaged
		if (function_exists('set_revslider_as_theme')) {
			set_revslider_as_theme();
		}
	}
	
	/**
	 * Action callback: Add form fields to category editing / adding form
	 */
	public function edit_category_meta($term = null)
	{
		// add required assets
		wp_enqueue_style('cat-options', get_template_directory_uri() . '/admin/css/cat-options.css');
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		
		// add media scripts
		wp_enqueue_media(); 
		
		wp_enqueue_script('theme-options', get_template_directory_uri() . '/admin/js/options.js', array('jquery'));
		
		// get our category meta template
		include_once locate_template('admin/category-meta.php');
	}
	
	/**
	 * Action callback: Save custom meta for categories
	 */
	public function save_category_meta($term_id)
	{
		// have custom meta?
		if (!empty($_POST['meta']) && is_array($_POST['meta'])) 
		{
			$meta = $_POST['meta'];
			
			// editing?
			if (($option = Bunyad::options()->get('cat_meta_' . $term_id))) {
				$meta = array_merge($option, $_POST['meta']);
			}
			
			Bunyad::options()->update('cat_meta_' . $term_id, $meta);
			
			// clear custom css cache
			delete_transient('bunyad_custom_css_cache');
		}
	}
	
	/**
	 * Filter callback: Add theme-specific profile fields
	 */
	public function add_profile_fields($fields)
	{
		$fields = array_merge((array) $fields, array(
			'twitter'  => __('Twitter URL', 'bunyad-admin'),
			'gplus'    => __('Google+ URL', 'bunyad-admin'),
			'facebook' => __('Facebook URL', 'bunyad-admin'),
			'linkedin' => __('LinkedIn URL', 'bunyad-admin'),
		));
		
		return $fields;
	}
	
	/**
	 * Set GD as preferred editor when bulk-resizing
	 */
	public function set_image_editor_gd()
	{
		add_filter('wp_image_editors', array($this, '_set_image_editor_gd'));
	}

	/**
	 * Filter callback: Swap positions to set GD as preferred
	 */
	public function _set_image_editor_gd($editors) 
	{
		array_unshift($editors, 'WP_Image_Editor_GD');
		
		return array_unique($editors);
	}
	
}

// init and make available in Bunyad::get('admin')
Bunyad::register('admin', array(
	'class' => 'Bunyad_Theme_Admin',
	'init' => true
));