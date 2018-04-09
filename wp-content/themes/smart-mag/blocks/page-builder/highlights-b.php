<?php

class Bunyad_PageBuilder_Highlights_B extends Bunyad_PageBuilder_WidgetBase
{
	
	public $no_container = 1;
	public $title_field  = 'heading,type';
	
	public function __construct()
	{
		parent::__construct(
			'bunyad_pagebuilder_highlights_b',
			__('Highlights - B Block', 'bunyad-admin'),
			array('description' => __('A 2 column block that shows 2 large posts followed by smaller posts.', 'bunyad-admin'))
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		
		// supported attributes
		$attrs = array('heading', 'heading_type', 'posts', 'type', 'cat', 'cats', 'tags', 'sort_order', 'sort_by', 'offset', 'post_type', 'filters', 'filters_terms');
		
		// do_shortcode will be run by pagebuilder		
		echo '[highlights_b '. implode(' ', $this->shortcode_attribs($instance, $attrs)) .' /]';
	}
	
	public function form($instance)
	{
		$defaults = array('pagination' => 0, 'heading' => '', 'heading_type' => '', 'posts' => 8, 'type' => '', 'cats' => '', 'post_type' => '');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
				
		$render = Bunyad::factory('admin/option-renderer'); /* @var $render Bunyad_Admin_OptionRenderer */
		
		
	?>
	
	<input type="hidden" name="<?php echo $this->get_field_name('no_container'); ?>" value="1" />
		
	<p>
		<label><?php _e('Number of Posts:', 'bunyad-admin'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('posts')); ?>" type="text" value="<?php echo esc_attr($posts); ?>" />
	</p>
	<p class="description"><?php _e('Configures posts to show for each listing. Leave empty to use theme default number of posts.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Sort By:', 'bunyad-admin'); ?></label>
		<select name="<?php echo esc_attr($this->get_field_name('sort_by')); ?>">
			<option value=""><?php _e('Published Date', 'bunyad-admin'); ?></option>
			<option value="modified"><?php _e('Modified Date', 'bunyad-admin'); ?></option>
			<option value="random"><?php _e('Random', 'bunyad-admin'); ?></option>
		</select>
		
		<select name="<?php echo esc_attr($this->get_field_name('sort_order')); ?>">
			<option value="desc"><?php _e('Latest First - Descending', 'bunyad-admin'); ?></option>
			<option value="asc"><?php _e('Oldest First - Ascending', 'bunyad-admin'); ?></option>
		</select>
	</p>
	
	<p>	
		<label><?php _e('Main Category: (Optional)', 'bunyad-admin'); ?></label>
		<?php wp_dropdown_categories(array(
			'show_option_all' => __('-- None --', 'bunyad-admin'), 'hierarchical' => 1, 'hide_empty' => 0, 'order_by' => 'name', 'class' => 'widefat', 'name' => $this->get_field_name('cat')
		)); ?>
	</p>
	<p class="description"><?php _e('Posts will be limited to this category and category color will be used for heading.', 'bunyad-admin'); ?></p>
	
	
	<p>
		<label><?php _e('Heading: (Optional)', 'bunyad-admin'); ?></label>
		<input class="widefat" name="<?php echo esc_attr($this->get_field_name('heading')); ?>" type="text" value="<?php echo esc_attr($heading); ?>" />
	</p>
	<p class="description"><?php _e('Optional heading.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Heading Style:', 'bunyad-admin'); ?></label>
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('heading_type')); ?>">
			<option value="block"><?php _e('Block - Section Style', 'bunyad-admin'); ?></option>
			<option value="block-alt"><?php _e('Block - Alternate Style', 'bunyad-admin'); ?></option>
			<option value="page"><?php _e('Page Heading Style', 'bunyad-admin'); ?></option>
			<option value="none"><?php _e('No Heading', 'bunyad-admin'); ?></option>
		</select>
	</p>
	<p class="description"><?php _e('Page heading style is normal heading style used for pages. Block section heading style is what you would see often on 
		homepage with multiple sections.', 'bunyad-admin'); ?></p>

	<div class="taxonomydiv"> <!-- borrow wp taxonomydiv > categorychecklist css rules -->
		<label><?php _e('Limit Categories: (Optional)', 'bunyad-admin'); ?></label>
		
		<div class="tabs-panel">
			<ul class="categorychecklist">
				<?php
				ob_start();
				wp_category_checklist();
				
				echo str_replace('post_category[]', $this->get_field_name('cats') .'[]', ob_get_clean());
				?>
			</ul>			
		</div>
	</div>
	<p class="description"><?php _e('By default, main category (or all categories if main is not set) will be used. Tick categories to limit to a specific category or categories.', 'bunyad-admin'); ?></p>
	
	<p class="tag">
		<?php _e('or Limit with Tags: (optional)', 'bunyad-admin'); ?> 
		<input type="text" name="<?php echo $this->get_field_name('tags'); ?>" value="" class="widefat" />
	</p>
	
	<p class="description"><?php _e('Separate tags with comma. e.g. cooking,sports', 'bunyad-admin'); ?></p>
	
	
			
	<div>
		<label><?php _e('Filters:', 'bunyad-admin'); ?></label>
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('filters')); ?>">
			<option value=""><?php _e('None', 'bunyad-admin'); ?></option>
			<option value="category"><?php _e('Categories', 'bunyad-admin'); ?></option>
			<option value="tag"><?php _e('Tags', 'bunyad-admin'); ?></option>
		</select>
		<p class="description"><?php _e('Filters are displayed in block heading and can be either tags or categories (enter below).', 'bunyad-admin'); ?></p>
	</div>
	
	<p>
		<label><?php _e('Filters: Tags/Categories', 'bunyad-admin'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('filters_terms'); ?>" class="widefat" />
	</p>
	<p class="description"><?php _e('Enter tag slugs or category slugs (depending on what you selected for Filters above) separated by comma. e.g. cooking,sports', 'bunyad-admin'); ?></p>
	
	
	<p>
		<label><?php _e('Offset: (Advanced)', 'bunyad-admin'); ?></label> 
		<input type="text" name="<?php echo $this->get_field_name('offset'); ?>" value="0" />
	</p>
	<p class="description"><?php _e('By specifying an offset as 10 (for example), you can ignore 10 posts in the results.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Post Types: (Advanced)', 'bunyad-admin'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('post_type')); ?>" type="text" value="<?php echo esc_attr($post_type); ?>" />
	</p>
	<p class="description"><?php _e('Only for advanced users! You can use a custom post type here - multiples supported when separated by comma. Leave empty to use the default format.', 'bunyad-admin'); ?></p>
	
	<?php
	}
}
