<?php

/**
 * Determine the listing style to use
 */
if (empty($type)) {
	$type = Bunyad::options()->default_cat_template;
}

// loop template
$template = strstr($type, 'modern') ? 'loop' : 'loop-' . $type;

// set loop grid type
$loop_grid = '';

if (strstr($type, '-3')) {
	$loop_grid = 3;
	
	// remove -3 suffix
	$template = str_replace('-3', '', $template);
}

// Columns attribute specified in shortcode
if (!empty($atts['columns'])) {
	$loop_grid = $atts['columns'];
}

Bunyad::registry()->set('loop_grid', $loop_grid);


// save current options so that can they can be restored later
$options = Bunyad::options()->get_all();

// enable pagination if infinite scroll is enabled - required
if ($pagination_type == 'infinite') {
	$pagination = 1;
}

Bunyad::options()
	->set('blog_no_pagination', ($pagination == 0 ? 1 : 0)) // inverse the original pagination option; different meaning
	->set('pagination_type', $pagination_type);

// @DEBUG
//if (!isset($filters)) {	
//	$atts['filters'] = 'category';
//	$atts['filters_terms'] = 'entertainment,vogue';
//}

//$atts['cat'] = 'entertainment';

// Backward compatibility: heading_type arg used to be empty when page heading style was needed (pre 3.0)
if (empty($atts['heading_type']) && !empty($atts['heading'])) {
	$atts['heading_type'] = 'page';
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


// $post and $wp_query are required for the local scope of timeline
global $bunyad_loop, $post, $wp_query;

$bunyad_loop = $query;
//Bunyad::registry()->set('loop', $query);

?>

	<section <?php Bunyad::markup()->attribs('blog-block', array('class' => array('block-wrap', $tag), 'data-id' => $block->block_id)); ?>>
	
		<?php echo $block->output_heading(); ?>
		
		<div class="block-content">
		<?php
		
			// Get our loop template with include to preserve local variable scope
			include locate_template(sanitize_file_name($template . '.php'));
		
		?>
		</div>
	
	</section>

<?php

// Enqueue the js to footer
if (Bunyad::options()->pagination_type == 'infinite') {
	wp_enqueue_script('smartmag-infinite-scroll');
}

// restore all options
Bunyad::options()->set_all($options);
wp_reset_query();