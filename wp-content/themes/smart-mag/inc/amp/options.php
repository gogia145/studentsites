<?php
/**
 * Options for AMP
 */
return array(
			'title' => __('AMP', 'bunyad-admin'),
			'id'    => 'options-tab-amp',
			'icon'  => 'dashicons-smartphone',
			'sections' => array(
				array(
					'desc'   => __('These settings are for AMP Posts only and do not effect rest of the site.', 'bunyad-admin'), 
					'fields' => array(
						array(
							'name' 	  => 'amp_analytics_ga',
							'label'   => __('Google Analytics', 'bunyad-admin'),
							'value'   => '',
							'desc'    => __('Add your Google Analytics Property ID thats in format UA-XXXXX-Y.', 'bunyad-admin'),
							'type'    => 'text',
						),
							
						array(
							'name'    => 'amp_logo',
							'label'   => __('AMP Logo (2x) - Optional', 'bunyad-admin'),
							'desc'    => __('IMPORTANT: Use a 2x image (twice the size of your original logo) since mobile phones have retina screens. Height should not be more than 120px. If not set, Mobile Logo will be used from Header settings. ', 'bunyad-admin'),
							'type'    => 'upload',
							'options' => array(
								'type'  => 'image',
								'title' => __('Upload This Picture', 'bunyad-admin'), 
								'insert_label' => __('Use As Logo', 'bunyad-admin')
							),
						),
							
						array(
							'name'  => 'amp_css_header_bg_color',
							'value' => '',
							'label' => __('Header Background Color', 'bunyad-admin'),
							'desc'  => __('Change background color for header strip.', 'bunyad-admin'),
							'type' => 'color',
							'css' => array(
								'selectors' => array(
									'.main-head' => 'background-color: %s',
								),
							)
						),
							
						array(
							'name'  => 'amp_css_body_bg_color',
							'value' => '',
							'label' => __('Body Background Color', 'bunyad-admin'),
							'desc'  => __('Change background color for whole page.', 'bunyad-admin'),
							'type' => 'color',
							'css' => array(
								'selectors' => array(
									'body' => 'background-color: %s',
								),
							)
						),
						
						array(
							'name' 	  => 'amp_custom_css',
							'label'   => __('AMP Custom CSS', 'bunyad-admin'),
							'value'   => '',
							'desc'    => __('Please make sure to follow AMP CSS rules such as no !important.', 'bunyad-admin'),
							'type'  => 'textarea',
							'options' => array('cols' => 75, 'rows' => 15)
						),
					)
				), // section
				
				array(
					'title'  => __('Advertisement Codes', 'bunyad-admin'),
					'desc'   => __('IMPORTANT: Responsive Adsense code will be converted to valid AMP-AD code automatically. For other networks, please use valid AMP-AD code only.', 'bunyad-admin'), 
					'fields' => array(
						array(
							'name' 	  => 'amp_ad_header',
							'label'   => __('Below Header', 'bunyad-admin'),
							'value'   => '',
							'desc'    => __('Add an ad below header.', 'bunyad-admin'),
							'type'    => 'textarea',
							'options' => array('cols' => 75, 'rows' => 5),
							'strip'   => 'none',
						),
							
						array(
							'name'    => 'amp_ad_post_below',
							'label'   => __('After Post Content', 'bunyad-admin'),
							'desc'    => __('Add an ad after post content.', 'bunyad-admin'),
							'type'    => 'textarea',
							'options' => array('cols' => 75, 'rows' => 5),
							'strip'   => 'none',
						),
							
						array(
							'name'    => 'amp_ad_paragraphs',
							'label'   => __('Between Paragraphs in Post', 'bunyad-admin'),
							'value'   => '',
							'desc'    => __('Add an ad after specified number of paragraphs on the AMP posts.', 'bunyad-admin'),
							'type'    => 'multiple',
							'sub_fields' => array(
								array(
									'label' => __('After Paragraph', 'bunyad-admin'),
									'name'  => 'number',
									'type'  => 'number'
								),
								array(
									'label'   => '',
									'placeholder'   =>__('Ad Code', 'bunyad-admin'),
									'name'    => 'code',
									'type'    => 'textarea',
									'options' => array('cols' => 75, 'rows' => 5),
									'strip'   => 'none',
								),
							)
						),
					)
				), // section
				
				
			)
		);