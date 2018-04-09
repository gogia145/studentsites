<?php
/**
 * Register Social Follow widget
 */

class Bunyad_Social_Widget extends WP_Widget 
{
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'bunyad-social',
			esc_html__('Bunyad - Social Follow', 'bunyad-widgets'),
			array('description' => esc_html__('Show social follower buttons.', 'bunyad-widgets'), 'classname' => 'widget-social')
		);
		
	}

	/**
	 * Register the widget if the plugin is active
	 */
	public function register_widget() {
		
		if (!class_exists('Sphere_Plugin_SocialFollow')) {
			return;
		}
		
		register_widget(__CLASS__);
	}
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance) 
	{
		$title = apply_filters('widget_title', esc_html($instance['title']));

		echo $args['before_widget'];

		if (!empty($title)) {
			
			echo $args['before_title'] . wp_kses_post($title) . $args['after_title']; // before_title/after_title are built-in WordPress sanitized
		}

		$services = $this->services();
		$active   = $instance['social'];

		?>
		
		<ul class="social-follow" itemscope itemtype="http://schema.org/Organization">
			<link itemprop="url" href="<?php echo esc_url(home_url('/')); ?>">
			<?php 
			foreach ($active as $key):
								
				$service = $services[$key];
				$count   = 0;
				
				if (Bunyad::options()->sf_counters) { 
					$count = Bunyad::get('social-follow')->count($key);
				}
			?>
			
				<li class="service">

					<a href="<?php echo esc_url($service['url']); ?>" class="service-link <?php echo esc_attr($key); ?> cf" target="_blank" itemprop="sameAs">
						<i class="icon fa fa-<?php echo esc_attr($service['icon']); ?>"></i>
						<span class="label"><?php echo esc_html($service['text']); ?></span>
						
						<?php if ($count > 0): ?>
							<span class="count"><?php echo esc_html($this->readable_number($count)); ?></span>
						<?php endif; ?>
					</a>

				</li>
			
			<?php 
			endforeach; 
			?>
		</ul>
		
		<?php

		echo $args['after_widget'];
	}
	
	/**
	 * Supported services
	 */
	public function services()
	{
		/**
		 * Setup an array of services and their associate URL, label and icon
		 */
		$services = array(
			'facebook' => array(
				'label' => __('Facebook', 'bunyad-widgets'),
				'text' => Bunyad::options()->sf_facebook_label,
				'icon'  => 'facebook-square',
				'url'   => 'https://facebook.com/%',
				'key'   => 'sf_facebook_id',
			),
				
			'gplus' => array(
				'label' => __('Google+', 'bunyad-widgets'), 
				'text'  => Bunyad::options()->sf_gplus_label,
				'icon'  => 'google-plus',
				'url'   => 'https://plus.google.com/%',
				'key'   => 'sf_gplus_id',
			),
				
			'twitter' => array(
				'label' => __('Twitter', 'bunyad-widgets'), 
				'text'  => Bunyad::options()->sf_twitter_label,
				'icon'  => 'twitter',
				'url'   => 'https://twitter.com/%',
				'key'   => 'sf_twitter_id',
			),
				
			'instagram' => array(
				'label' => __('Instagram', 'bunyad-widgets'), 
				'text'  => Bunyad::options()->sf_instagram_label,
				'icon'  => 'instagram',
				'url'   => 'https://instagram.com/%',
				'key'   => 'sf_instagram_id',
			),
			
			'youtube' => array(
				'label' => __('YouTube', 'bunyad-widgets'), 
				'text'  => Bunyad::options()->sf_youtube_label,
				'icon'  => 'youtube',
				'url'   => '%',
				'key'   => 'sf_youtube_url',
			),
				
			'vimeo' => array(
				'label' => __('Vimeo', 'bunyad-widgets'), 
				'text'  => Bunyad::options()->sf_vimeo_label,
				'icon'  => 'vimeo',
				'url'   => '%',
				'key'   => 'sf_youtube_url',
			),
		);
		
		$services = $this->_replace_urls($services);
		
		return $services;
	}
	
	/**
	 * Perform URL replacements
	 * 
	 * @param  array  $services
	 * @return array
	 */
	public function _replace_urls($services) 
	{
		foreach ($services as $id => $service) {
		
			if (!isset($service['key'])) {
				continue;
			}
			
			// Get the URL or username from settings/
			$services[$id]['url']  = str_replace('%', Bunyad::options()->get($service['key']), $service['url']);
		}
			
		return $services;
	}


	/**
	 * Make count more human in format 1.4K, 1.5M etc.
	 * 
	 * @param integer $number
	 */
	public function readable_number($number)
	{
		if ($number < 1000) {
			return $number;
		}

		if ($number < 10^6) {
			return round($number / 1000, 1) . 'K';
		}
		
		return round($number / 10^6, 1) . 'M';
	}
		

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance)
	{
		$defaults = array('title' => '', 'social' => array());
		$instance = array_merge($defaults, (array) $instance);
		
		extract($instance);
		
		// Merge current values for sorting reasons
		$services = array_merge(array_flip($social), $this->services());
		
		?>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php echo esc_html__('Title:', 'bunyad-widgets'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		
		<div>
			<label for="<?php echo esc_attr($this->get_field_id('social')); ?>"><?php echo esc_html__('Social Icons:', 'bunyad-widgets'); ?></label>
			
			<p><small><?php esc_html_e('Drag and drop to re-order.', 'bunyad-widgets'); ?></small></p>
			
			<div class="bunyad-social-services">
			<?php foreach ($services as $key => $service): ?>
			
			
				<p>
					<label>
						<input class="widefat" type="checkbox" name="<?php echo esc_attr($this->get_field_name('social')); ?>[]" value="<?php echo esc_attr($key); ?>"<?php 
						echo (in_array($key, $social) ? ' checked' : ''); ?> /> 
					<?php echo esc_html($service['label']); ?></label>
				</p>
			
			<?php endforeach; ?>
			
			</div>
			
			<p><small><?php echo esc_html__('Configure from Theme Settings > Social Follow.', 'bunyad-widgets'); ?></small></p>
			
		</div>
		
		<script>
		jQuery(function($) { 
			$('.bunyad-social-services').sortable();
		});
		</script>
	
	
		<?php
	}

	/**
	 * Save widget.
	 * 
	 * Strip out all HTML using wp_kses
	 * 
	 * @see wp_kses_post()
	 */
	public function update($new, $old)
	{
		foreach ($new as $key => $val) {

			// Social just needs intval
			if ($key == 'social') {
				
				array_walk($val, 'intval');
				$new[$key] = $val;

				continue;
			}
			
			// Filter disallowed html 			
			$new[$key] = wp_kses_post($val);
		}
		
		return $new;
	}
}
