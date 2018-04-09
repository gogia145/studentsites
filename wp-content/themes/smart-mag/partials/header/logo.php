<?php
/**
 * Partial: Logo 
 */

// attributes for the logo link
$attribs = array(
	'href'  => home_url('/'),
	'title' => get_bloginfo('name', 'display'),
	'rel'   => 'home'
);

if (Bunyad::options()->image_logo && Bunyad::options()->image_logo_mobile) {
	$attribs['class'] = 'is-logo-mobile';
}

?>
		<a <?php Bunyad::markup()->attribs('main-logo', $attribs); ?>>
		
			<?php if (Bunyad::options()->image_logo): // custom logo ?>
											
				<?php if (Bunyad::options()->image_logo_mobile): // add mobile logo if set ?>
					<img src="<?php echo esc_attr(Bunyad::options()->image_logo_mobile); ?>" class="logo-mobile" width="0" height="0" />
				<?php endif; ?>
				
				<img <?php
						/**
						 * Get escaped attributes and add optionally add srcset for retina
						 */ 
						Bunyad::markup()->attribs('image-logo', array(
							'src'    => Bunyad::options()->image_logo,
							'class'  => 'logo-image',
							'alt'    => get_bloginfo('name', 'display'),
							'srcset' => array(Bunyad::options()->image_logo => '', Bunyad::options()->image_logo_retina => '2x')
						)); ?> />
					 
			<?php else: ?>
				<?php echo do_shortcode(Bunyad::options()->text_logo); ?>
			<?php endif; ?>
			
		</a>