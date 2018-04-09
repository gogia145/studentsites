<?php
/**
 * Dynamic and Custom CSS for AMP 
 */

ob_start();

// RTL prefix if needed
$rtl = is_rtl() ? 'rtl-' : '';

// Main CSS file for AMP
include locate_template('css/amp/' . $rtl . 'amp.css');

// Review post? Extra CSS needed
$has_review = false;
if (is_single() && Bunyad::posts()->meta('reviews')) {
	include locate_template('css/amp/' . $rtl . 'review.css');
	$has_review = true;
}

$default_color = '#e54e53';

/**
 * Generate dynamic styles
 */

// Change main color?
if (Bunyad::options()->css_main_color != $default_color):
	$color = esc_html(Bunyad::options()->css_main_color);

	echo "
		a,
		a:visited,
		.post-meta .posted-by a,
		.main-menu section[expanded] > .menu-item {
			color: $color; 
		}
		
		.post-header .cat-title {
			background: $color; 
		}
		
		.main-head {
			border-top-color: $color; 
		}
	";
	
	if ($has_review) {
		
		echo "
			.review-box .overall,
			.review-box .bar {
				background: $color; 
			}
			
			.main-stars,
			.main-stars span:before {
				color: $color; 
			}
			
			.review-box .heading {
				border-color: $color;
			}
		";
	}
	
endif;

/**
 * Options CSS
 */
require_once locate_template('inc/css-compiler.php');

$css = new Bunyad_Custom_Css();
$css->init('amp_css_');
$css->process_elements();

echo implode('', $css->css);


// Custom CSS?
if (Bunyad::options()->amp_custom_css) {
	echo Bunyad::options()->amp_custom_css;
}


// Cleanup CSS
$content = ob_get_clean();
$content = str_replace(array("\r", "\n", "\t"), '', $content);

echo $content;