<?php
/**
 * Block class to initialize a single block
 * 
 * Following properties are available after process() has run:
 * 
 * @property  WP_Query  $query        Block query available.
 * @property  string    $title_link   Auto title link HTML for block heading. Recommendation: Use output_title() instead.
 * @property  string    $title        Title  text of the block.
 * @property  string    $link         Link of the block title.
 * @property  WP_Term|null   $term    Current block's main category.
 */
class Bunyad_Theme_Block 
{
	public $_options = array();
	public $_data = array();
	public $_block;
	public $_processed = false;

	/**
	 * Initialize
	 * 
	 * @param attribs $attribs Array of block attributes
	 * @param string  $block   Block shortcode / tag name
	 */
	public function __construct($options = array(), $block = '') 
	{
		$this->_options = $options;
		$this->_block   = $block;
	}
	
	/**
	 * Process data to setup the block query
	 */
	public function process()
	{
		
		$defaults = array(
			'posts'   => 4,
			'offset'  => '',
			'heading' => '',
			'heading_type' => '',
			'link'    => '',
			'cat'     => '',             // Main category
			'cats'    => '',             // Alias for 'terms'
			'tags'    => '',             // Tag slugs - separated by commas - Not in 'terms' as can limit by tags AND cats
			'terms'   => '',             // Categories, or custom taxonomies' term ids, or author ids - separated by commas 
			'taxonomy'   => '',          // Limit to a specific custom taxonomy
			'sort_order' => '',
			'sort_by'    => '',
			'post_format' => '',
			'post_type'   => '',
			'filters' => false,          // Values: category, tag, or taxonomy - if empty, defaults to category 
			'filters_terms' => '',
			'filters_tax'   => '',       // When using custom taxonomy
			'filters_load'  => 'preload', // Empty or preload enables filters preloading
		);
		
		extract(
			array_merge($defaults, $this->_options), 
			EXTR_SKIP
		);
		
		$this->set_block_id();
		
		
		// Have custom query args?
		$query_args = array('ignore_sticky_posts' => 1);
		if (isset($this->_data['query_args']) && is_array($this->_data['query_args'])) {
			$query_args = $this->_data['query_args'];
		}
		
		$query_args = array_merge($query_args, array(
			'posts_per_page' => (!empty($posts) ? intval($posts) : 4), 
			'order' => ($sort_order == 'asc' ? 'asc' : 'desc'), 
			'offset' =>  ($offset ? $offset : '')
		));
		
		// Add pagination if available
		$page = (get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));
		if (!empty($page)) {
			$query_args['paged'] = $page;
		}
	
		
		/**
		 * Sortng criteria
		 */
		switch ($sort_by) {
			case 'modified':
				$query_args['orderby'] = 'modified';
				break;
				
			case 'random':
				$query_args['orderby'] = 'rand';
				break;
	
			case 'comments':
				$query_args['orderby'] = 'comment_count';
				break;
				
			case 'alphabetical':
				$query_args['orderby'] = 'title';
				break;
				
			case 'rating':
				$query_args = array_merge($query_args, array('meta_key' => '_bunyad_review_overall', 'orderby' => 'meta_value_num'));
				break; 	
		}
		
		/**
		 * Setup aliases for backward compatibility
		 */
		
		if (!empty($cats)) {
			$terms = $cats;
		}
	
		// Alias for heading
		// Legacy: $title may be set by old block system 
		if (empty($title)) {
			$title = $heading;
		}
		
		// Main category / taxonomy term object
		$term = '';
		
		// template helper variables
		$term_id   = '';
		$term_wrap = array('block-wrap');
		$main_term = '';
		
			
		/**
		 * Limit by custom taxonomy?
		 */
		if (!empty($taxonomy)) {
	
			$_taxonomy = $taxonomy; // preserve
			
			// get the term
			$terms = explode(',', $terms);
			$term = get_term_by('id', (!empty($cat) ? $cat : current($terms)), $_taxonomy);
			
			// add main cat to terms list
			if (!empty($cat)) {
				array_push($terms, $cat);
			}
			
			$query_args['tax_query'] = array(array(
				'taxonomy' => $_taxonomy,
				'field' => 'id',
				'terms' => (array) $terms
			));
			
			if (empty($title)) {
				$title = $term->slug; 
			}
			
			$link = get_term_link($term, $_taxonomy);
			
		}
		else {
			
			// terms / cats may have slugs instead of ids
			if (!empty($terms)) {
				$terms = explode(',', $terms);
				
				if (count($terms) && !is_numeric($terms[0])) {
					$results = get_terms('category', array('slug' => $terms, 'hide_empty' => false, 'hierarchical' => false));
					$terms = wp_list_pluck($results, 'term_id');
				}
			}
			else {
				$terms = array();
			}
		
			/**
			 * Got main category/term? Use it for filter, link, and title
			 */
			if (!empty($cat)) {
				
				// Might be an id or a slug
				$term = $category = is_numeric($cat) ? get_category($cat) : get_category_by_slug($cat);
				
				// Category is always the priority main term
				$main_term = $term;
				
				if (!empty($category)) {
					array_push($terms, $category->term_id);
						
					if (empty($title)) {
						$title = $category->cat_name;
					}
				
					$link = get_category_link($category);
				}
			}
			
			/**
			 * Filtering by tag(s)?
			 */
			if (!empty($tags)) {
		
				// add query filter supporting multiple tags separated with comma
				$query_args['tag'] = $tags;
				
				// get the first tag
				$tax_tag = current(explode(',', $tags));
				$term = get_term_by('slug', $tax_tag, 'post_tag');

				// Use the first tag as main term if a category isn't already the main term
				if (!$main_term) {
					$main_term = $term;
				}
				
				if (empty($title)) {
					$title = $term->slug; 
				}
				
				if (empty($link)) {
					$link = get_term_link($term, 'post_tag');
				}
			}
			
			/**
			 * Multiple categories/terms filter
			 */
			if (count($terms)) {
				$query_args['cat'] = join(',', $terms);
				
				// No category as main and no tag either? Pick first category from multi filter
				if (!$main_term) {
					$main_term = current($terms);
				}
			}
		}
		
		/**
		 * Post Formats?
		 */
		if (!empty($post_format)) {
			
			if (!isset($query_args['tax_query'])) {
				$query_args['tax_query'] = array();
			}
			
			$query_args['tax_query'][] = array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => array('post-format-' . $post_format),
			);
		}
		
		/**
		 * Custom Post Types?
		 */
		if (!empty($post_type)) {
			$query_args['post_type'] = array_map('trim', explode(',', $post_type));
		}
		
		
		/**
		 * Execute the query
		 */

		// add a filter - $tag comes from Bunyad_Shortcodes:__call() magic handler
		$query_args = apply_filters('bunyad_block_query_args', $query_args, $this->_block, $this->_options);
		$this->_query = new WP_Query($query_args);
	
		// set title link if possible
		if (!empty($link)) {
			$title_link = '<a href="' . esc_url($link) .'">' . esc_html($title) . '</a>';
		}
		else {
			$title_link = $title;
		}
		
		// disable title if empty
		if (empty($title_link)) {
			$this->_options['heading_type'] = 'none';
		}
		
		/**
		 * Custom CSS for main color
		 */
		if (!empty($main_color)) {
			
			// add to shortcode counters - for suffix
			$this->_sc_handle->counter++;
				
			$term_wrap[] = $selector = esc_attr($this->_sc_handle->css_suffix('block-css'));
			
			echo "
			<style>
				
			.{$selector} .read-more a, .{$selector} .image-overlay { background: {$main_color}; }
			.{$selector} .section-head .title { background: {$main_color}; }
			.{$selector} .section-head { border-color: {$main_color}; }
				
			</style>
			";
		}
		
		// setup accessible variables
		$this->_data = array_merge($this->_data, array(
			'title_link' => $title_link,
			'link'       => $link,
			'title'      => $title,
			'query_args' => $query_args,
			'query'      => $this->_query,
			'term'    	 => $main_term, 
		));
		
		$this->_processed = true;
				
		return $this;
	}
	
	public function set_block_id()
	{
		Bunyad::registry()->block_count++;
		$this->block_id = Bunyad::registry()->block_count;
	}
	
	/**
	 * Process and output display filters 
	 * 
	 * @return string Display filters HTML
	 */
	public function output_filters() 
	{
		if (!$this->_processed) {
			return;
		}
		
		extract($this->_options, EXTR_SKIP);
		$display_filters = array();
		
		/**
		 * Process display filters - supports ids or slugs
		 */
		if (!empty($filters)) {
			
			// Which taxonomy? Default to category
			$tax = 'category';

			if ($filters == 'tag') {
				$tax = 'post_tag';	
			}
			else if ($filters == 'taxonomy' && !empty($filters_tax)) {
				$tax = $filters_tax;
			}
			
			// Auto-select 3 sub-cats for category if terms are missing
			if ($tax == 'category' && empty($filters_terms) && is_object($this->term)) {

				$filters_terms = join(',', wp_list_pluck(
					get_categories(array('child_of' => $this->term->term_id, 'number' => 3, 'hierarchical' => false)),
					'term_id'
				));
			}
			
			if (empty($filters_terms)) {
				return;
			}
			
			// Process filter terms
			if (is_string($filters_terms)) {
				$filters_terms  = explode(',', $filters_terms);
			}
			
			foreach ($filters_terms as $id) {
				
				// Supports slugs
				if (!is_numeric($id)) {
					$term = get_term_by('slug', $id, $tax);
				}
				else {
					$term = get_term($id);
				}
				
				if (!is_object($term)) {
					continue;
				}
				
				$link = get_term_link($term);
				$display_filters[] = '<li><a href="'. esc_url($link)  .'" data-id="' . esc_attr($term->term_id) . '">'. esc_html($term->name) .'</a></li>';
				
					
				/** 
				 * Add to the registry for preloading filter contents
				 */
				if (empty($filters_load) OR $filters_load == 'preload') {
					
					// legacy - Bunyad plugin not updated
					if (empty($this->_options['block_file'])) {
						$this->_options['block_file'] = Bunyad::registry()->block['render'];
					}
					
					if (!Bunyad::registry()->block_filters_preload) {
						Bunyad::registry()->block_filters_preload = new ArrayObject();
					}
					
					$atts = $this->_options;
					
					// Don't need these attributes for filters
					unset(
						$atts['cat'], 
						$atts['cats'], 
						$atts['tags'], 
						$atts['filters'], 
						$atts['taxonomy'], 
						$atts['offset'],
						$atts['sub_cats'], // Legacy - prevent infinite loops
						$atts['sub_tags']  // Legacy
					);
					
					$atts = array_merge($atts, array(
						'_is_filter' => true,
						'terms'    => $term->term_id, 
						'filters'  => false,
						'taxonomy' => $tax
					));
			
					// Add to global registry
					Bunyad::registry()->block_filters_preload[] = array(
						'options' => $this->_options,
						'block'   => $this->_block,
						'atts'    => $atts,
						'data'    => $this->_data
							
					);
				}
			}
		}
		
		if (!count($display_filters)) {
			return '';
		}
		
		ob_start();
		
		?>
		
		<ul class="subcats filters">
			<li><a href="#" data-id="0" class="active"><?php esc_html_e('All', 'bunyad'); ?></a></li>
			<?php echo join("\n", $display_filters); ?>
		</ul>
		
		<?php 
		
		return ob_get_clean(); 
	}
	
	/**
	 * Get block heading HTML
	 * 
	 * @return string
	 */
	public function output_heading()
	{	
		// No title link processed? It's important the process() run first
		if (!$this->_processed OR !$this->title_link) {
			return;
		}
		
		extract($this->_options, EXTR_SKIP);
		
		// Heading disabled?
		if ($heading_type == 'none') {
			return;
		}
		
		// Get display filters to show in heading
		$filters = $this->output_filters();
		
		ob_start();
		
		?>
		
		
		<?php if (!$heading_type OR in_array($heading_type, array('default', 'block-filter', 'block'))): 
		
				$classes = array('section-head prominent heading');
		
				if (is_object($this->term)) { 
					$classes[] = 'cat-text-' . $this->term->term_id;	
				}
				
				$title = preg_replace('/(\*|_)(.*?)\1/', '<span class="color">\2</span>', $this->title_link);
		
		?>
			
			<div <?php Bunyad::markup()->attribs('block-heading-wrap', array('class' => $classes)); ?>>
				<h3 class="heading"><?php echo $title; ?></h3>
									
				<?php echo $filters; ?>

			</div>
			
		<?php elseif ($heading_type == 'block-alt'): 
		
				$classes = array('block-head');
				
				if (is_object($this->term)) { 
					$classes[] = 'cat-text-' . $this->term->term_id;	
				}
		?>

			<div <?php Bunyad::markup()->attribs('block-heading-wrap', array('class' => $classes)); ?>>
				<h3 class="heading"><?php echo $this->title_link; ?></h3>
									
				<?php echo $filters; ?>

			</div>			
		
		<?php else: // "page" heading style ?>
		
			<h2 class="main-heading prominent"><?php echo esc_html($heading); ?></h2>
		
		<?php endif; ?>
		
		<?php
		
		return ob_get_clean();
	}	

	/**
	 * Magic method to get a variable from $this->_data
	 * 
	 * @param string $key
	 */
	public function __get($key) 
	{
		if (!isset($this->_data[$key])) {
			return;
		}
		
		return $this->_data[$key];
	}
	
	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
		
		return $this;
	}
}