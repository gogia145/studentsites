<?php

class Bunyad_Admin_OptionRenderer
{
	public $default_values = array();
	
	/**
	 * Initialize the template file
	 * 
	 * @param array $options
	 * @param string $file
	 * @param array $populate  default form values for elements
	 * @param array $data 
	 */
	public function template($options, $file, $populate = array(), $data = array())
	{
		$this->default_values = (array) $populate;
		extract($data);

		require_once $file;
	}
	
	public function render($element)
	{
		// Override data from in-memory - something might have changed this element values
		$opts = Bunyad::options()->defaults;
		if (!empty($element['name']) && array_key_exists($element['name'], $opts)) {
			$element = $opts[ $element['name'] ];
		}
		
		// Defaults
		$element = apply_filters('bunyad_option_renderer_element', array_merge(
			array('name' => null, 'label' => null), 
			$element
		));
		
		// preserve default value
		if (isset($element['value'])) {
			$element['default_value'] = $element['value'];
		}
		
		// Set modified/saved value if available
		if (isset($this->default_values[$element['name']])) {
			$element['value'] = $this->default_values[$element['name']];
		}

		switch ($element['type'])
		{
			case 'select':
				$output = $this->render_select($element);
				break;
			
			case 'checkbox':
				$output = $this->render_checkbox($element);
				break;
				
			case 'text':
				$output = $this->render_text($element);
				break;
				
			case 'number':
				$output = $this->render_text(array_merge(array('input_type' => 'number', 'input_size' => '4', 'input_class' => 'small'), $element));
				break;
				
			case 'textarea':
				$output = $this->render_textarea($element);
				break;
				
			case 'radio':
				$output = $this->render_radio($element);
				break;
				
			case 'color':
				$output = $this->render_color_picker($element);
				break;
				
			case 'bg_image':
				$output = $this->render_bg_image($element);
				break;
				
			case 'upload':
				$output = $this->render_upload($element);
				break;

			case 'typography':			
				$output = $this->render_typography($element);
				break;
				
			case 'html':
				$output = $element['html'];
				break;
				
			case 'file':
				$output = include locate_template($element['render']);
				break;
				
			case 'multiple':
				$output = $this->render_multiple($element);
				break;
				
			default:
				$output = '';
				break;
		}
		
		
		// decorate it
		if ($output && empty($element['no_wrap'])) {
			$output = '<label class="element-title">'. $element['label'] . '</label>'
					. '<div class="element-control">' . $output . (isset($element['html_post_output']) ? $element['html_post_output'] : '') . '</div>';
		}
		
		return $output;
	}
	
	public function render_select($element)
	{
		$element = array_merge(array('value' => null), $element);
		
		$output = '<select name="'. esc_attr($element['name']) .'"' . (isset($element['class']) ? ' class="'. esc_attr($element['class']) .'"' : '') . '>';
		
		foreach ( (array) $element['options'] as $key => $option) 
		{
			if (is_array($option)) {
				$output .= '<optgroup label="' . esc_attr($key) . '">' . $this->_render_options($option, $element['value']) . '</optgroup>';
			}
			else {
				$output .= $this->_render_options(array($key => $option), $element['value']);
			}
			
		}
		
		return $output . '</select>';
	}
	
	// helper for: render_select()
	private function _render_options($options, $selected = '') 
	{	
		$output = '';
		
		foreach ($options as $key => $option) {
			$output .= '<option value="'. esc_attr($key) .'"'. selected((string) $selected, $key, false) .'>' . esc_html($option) . '</option>';
		}
		
		return $output;
	}
	
	/**
	 * Render a single checkbox or a group of multiple checkboxes
	 * 
	 * @param array $element
	 */
	public function render_checkbox($element)
	{
		$output = '';
		
		// multiple checkboxes?
		if (!empty($element['multiple'])) 
		{
			$element['value'] = (array) $element['value'];
			foreach ((array) $element['multiple'] as $key => $option) 
			{
				$value = isset($element['value'][$key]) ? $element['value'][$key] : '';
				
				$sub_element = array_merge($element, array(
					'name'  => $element['name'] . '[' . $key . ']',
					'label' => $option,
					'value' => $value,
				));
				
				$output .= '<div class="checkbox"> ' . $this->_render_checkbox($sub_element) . '</div>';
			}
		}
		else {
			return $this->_render_checkbox($element);
		}
						
		return $output;
	}
	
	// helper for: render_checkbox()
	public function _render_checkbox($element) {
		
		$element = array_merge(array('value' => null), $element);
		$element['options'] = array_merge(
			array('checked' => __('Yes', 'bunyad-admin'), 'unchecked' => __('No', 'bunyad-admin')), 
			!empty($element['options']) ? $element['options'] : array()
		);
		
		$output = '<input type="hidden" name="'. esc_attr($element['name']) .'" value="0" />' // always send in POST - even when empty
				. '<input type="checkbox" name="'. esc_attr($element['name']) .'" value="1" id="'. esc_attr($element['name']) .'"'
				. checked($element['value'], 1, false) . ' data-yes="'. esc_attr($element['options']['checked']) .'" data-no="'. esc_attr($element['options']['unchecked']) .'" />
				<label for="'. esc_attr($element['name']) .'">' . $element['label'] . '</label>
				';
				
		return $output;
	}

	/**
	 * Renders a text input field
	 */
	public function render_text($element)
	{
		$element = array_merge(
			array('value' => '', 'input_class' => '', 'input_type' => 'text', 'input_size' => '', 'placeholder' => ''), 
			$element
		);
		
		$attribs = Bunyad::markup()->attribs(
			'element-' . $element['name'], 
			array_filter(array(
				'type'  => $element['input_type'],
				'name'  => $element['name'],
				'value' => $element['value'],
				'class' => array('input', $element['input_class']),
				'size'  => $element['input_size'],
				'placeholder' => $element['placeholder']
			)),
			array('echo' => false)
		);
		
		$output = '<input ' . $attribs . ' />';

		return $output;
	}
	
	public function render_html($element)
	{
		return $element['html'];
	}
	
	/**
	 * Renders a textarea element 
	 */
	public function render_textarea($element)
	{
		// defaults
		$element = array_merge(array(
			'value' => null,
			'placeholder' => '',
			'options' => array('rows' => null, 'cols' => null)
		), $element);
		
		
		$attribs = array(
			'name'  => $element['name'],
			'placeholder' => $element['placeholder']
		);
		
		// Add rows and cols if set
		if ($element['options']['rows'] OR $element['options']['cols']) {
			$attribs = array_merge($attribs, array(
				'rows' => $element['options']['rows'],
				'cols' => $element['options']['cols']
			));
		}
		
		// Create attributes
		$attribs = Bunyad::markup()->attribs(
			'element-' . $element['name'], 
			array_filter($attribs),
			array('echo' => false)
		);
		
		$output = '<textarea '. $attribs . '>'. esc_html($element['value']) .'</textarea>';
		
		return $output;
	}
	
	public function render_radio($element)
	{
		$output = '';
		
		foreach ($element['options'] as $key => $option)
		{
			$output .= '<div class="radio-option"><label><input type="radio" name="'. esc_attr($element['name']) .'" value="'. esc_attr($key) . '"'
					.  checked($element['value'], $key, false) .' /><span>' . esc_html($option) . '</span></label></div>';
		}
				
		return $output;
	}
	
	public function render_color_picker($element)
	{
		$element = array_merge(array('value' => null), $element);
		
		$output = '<input type="text" class="color-picker" name="'. esc_attr($element['name']) .'"'
				. ' value="' . esc_attr($element['value']) . '" data-default-color="' . esc_attr($element['default_value']) 
				. '" /><div class="color-picker-element"></div>';
				
		return $output;
	}
	
	/**
	 * Render background image selector with options to select bg position
	 * 
	 * @param array $element
	 * @uses Bunyad_Admin_OptionRenderer::render_upload()
	 */
	public function render_bg_image($element)
	{
		// future themes on-need-basis implementation
	}
	
	/**
	 * Render an upload element
	 * 
	 * @param array $element
	 */
	public function render_upload($element)
	{
		$button_label = __( 'Upload', 'bunyad-admin' );
		if (!empty($element['options']['button_label'])) {
			$button_label = $element['options']['button_label'];
		}
		
		$element = array_merge(array('value' => null), $element);
		$element['options'] = array_merge(array('editable' => null, 'title' => null, 'type' => null), $element['options']);
		$element = $this->set_sub_values($element, array('bg_type'));
		
		$classes = $image = '';
		
		$output = '<input type="'. ($element['options']['editable'] ? 'text' : 'hidden') .'" name="'. esc_attr($element['name']) .'" class="element-upload" value="'
				. esc_attr($element['value']) .'" />'
				. '<input type="button" class="button upload-btn" value="'. esc_attr($button_label) .'"' 
				. ' data-insert-label="'. esc_attr($element['options']['insert_label']) .'"'
				. ' data-title="'. esc_attr($element['options']['title']) .'"'
				. ' data-title="'. esc_attr($element['options']['type']) .'"'
				.'"/> ';
		
		// image type?
		if ($element['options']['type'] == 'image') 
		{ 
			// existing image?
			if ($element['value']) {
				$image   = '<img src="'. esc_attr($element['value']) .'" />';
				$classes = ' visible ';
			}
		
			// bg type?
			$type = '';
			if (isset($element['bg_type'])) 
			{
				
				$type .= $this->render_select(array(
					'name' => $element['name'] . '_bg_type',
					'value' => $element['bg_type']['value'],
					'options' => array(
						'repeat' => __('Repeat Horizontal and Vertical - Pattern', 'bunyad-admin'),
						'cover'  => __('Fully Cover Background - Photo', 'bunyad-admin'),
						'repeat-x' => __('Repeat Horizontal', 'bunyad-admin'),
						'repeat-y' => __('Repeat Vertical', 'bunyad-admin'),
						'no-repeat' => __('No Repeat', 'bunyad-admin'),
					),
				));
			}
			
			$output .= '<a href="" class="remove-image button after-upload'. $classes .'">'. __('Remove', 'bunyad-admin') .'</a>';			
			$output .= '<div class="image-upload'. $classes .'">'. $image . '</div>';
			
			$output .= '<div class="image-type after-upload'. $classes .'">' . $type . '</div>';
		}
				
		return $output;
	}
	
	/**
	 * Render multiple repeating fields - where multiple of the same can be dynamically added
	 */
	public function render_multiple($element)
	{
		$element = array_merge(
			array(
				'html'  => '',
				'value' => ''
			),
			$element
		);
		

		/**
		 * Print existing field groups while editing
		 */
		$fields = '';
		if (!empty($element['value'])) {
			
			$iterator = $element['value'];
			$first    = current($iterator);
			
			// possibly multi-dimensional array - created by multiple fields in a group
			if (is_array($first)) {
				$iterator = $first;
			}
			
			foreach ($iterator as $key => $value) {
				$fields .= $this->_render_multiple_fields($element, $key);
			}

		}
		else {
			// add an empty field group
			$fields = $this->_render_multiple_fields($element);	
		}
		
		$output = '<div class="element-multiple">' . $fields . '<a href="#">' . __('Add More', 'bunyad-admin')  . '</a></div>';
		
		return $output;
	}
	
	/**
	 * Helper for render_multiple() to add a field
	 * 
	 * @param string   $element  the main element to render sub-elements from
	 * @param integer  $key      position to get the value from saved values, if any
	 */
	public function _render_multiple_fields($element, $key = null)
	{
		// no sub fields for this element?
		if (empty($element['sub_fields'])) {
			return '';
		}
		
		$fields = '';
		
		foreach ($element['sub_fields'] as $field) {

			// defaults
			$field = array_merge(array('label' => '', 'no_wrap' => 1), $field);
			
			/**
			 * If name is omitted, there's likely only one field. The field will become available in an array of type:
			 * 
			 * main_field[]  instead of  main_field['sub_field'][]
			 */

			if (!isset($field['name'])) {
				$field = array_merge($field, array(
					'value' => ($key !== null ? $element['value'][$key] : ''),
					'name'  => $element['name'] . '[]',
				));
			}
			else {
				$field = array_merge($field, array(
					'value' => ($key !== null ? $element['value'][ $field['name'] ][$key] : ''),
					'name'  => $element['name'] . '[' . $field['name'] . '][]'
				));
			}
			
			$label   = ($field['label'] ? '<label>' . $field['label'] . '</label>' : '');
			$fields .= '<div class="field">' . $label . $this->render($field) . '</div>';
			
		}
		
		return '<div class="fields'. ($key == null ? ' default' : '') .'">' . $fields . '<a href="#" class="remove"><span class="dashicons dashicons-no"></span></a></div>';
		
	}
	
	/**
	 * Google web fonts - uses api to get fonts list
	 */
	public function render_typography($element)
	{
		// defaults
		$element = array_merge(
			array(
				'size' => null, 
				'color' => null, 
				'no_preview' => null,
				'families' => false, 
				'suggested' => null
			), 
			$element
		);
		
		$element = $this->set_sub_values($element, array('size', 'color'));
		
		// get fonts
		$fonts = $this->get_google_fonts();
		
		$google = array();
		foreach ($fonts['items'] as $font) 
		{			
			// font families only? no variations!
			if ($element['families'] == true) {
				$google[$font['family']] = $font['family'];
				continue;
			} 
			
			foreach ($font['variants'] as $variant)	
			{
				// not the regular variant?
				$variation = '';
				if ($variant !== 'regular')  {
					$variation = ' ('. ucfirst($variant) . ')';
				}
				
				$google[$font['family'] .':'. $variant] = $font['family'] . $variation;
			}
		}
		
		// standard system fonts?
		$stacks = apply_filters('bunyad_options_font_stacks', array(
			'system: Arial, "Helvetica Neue", Helvetica, sans-serif' => 'Arial, "Helvetica Neue", Helvetica, sans-serif',
			'system: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif' => 'Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif',
			'system: Georgia, Cambria, "Times New Roman", Times, serif' => 'Georgia, Cambria, "Times New Roman", Times, serif',
		));
		
		// add suggested fonts
		if (is_array($element['suggested'])) {
			$options[__('Suggested Fonts', 'bunyad-admin')] = $element['suggested'];
		}
		
		$options[__('Google Fonts', 'bunyad-admin')] = $google;
		$options[__('System Fonts', 'bunyad-admin')] = $stacks;

		// add Typekit Fonts if available
		if (Bunyad::options()->fonts_typekit) {
			
			$values = array_values(Bunyad::options()->fonts_typekit);
			$keys   = array();
			 
			foreach ($values as $key => $font) {
				$keys[$key] = 'custom: ' . $font;
			}
			
			// use font name as both display and value
			$options[__('Typekit Fonts', 'bunyad-admin')] = array_combine($keys, $values);
		}
		
		// add Custom Fonts if available 
		if (Bunyad::options()->fonts_custom) {

			$values = array_values(Bunyad::options()->fonts_custom['name']);
			$keys   = array();
			 
			foreach ($values as $key => $font) {
				$keys[$key] = 'custom: ' . $font;
			}
			
			// use font name as both display and value
			$options[__('Custom Fonts', 'bunyad-admin')] = array_combine($keys, $values);
		}

		// Add a default value
		if (empty($element['default_value'])) {
			$options = array_merge(array('' => __('Default', 'bunyad-admin')), $options);
		}
		
		
		$select = $this->render_select(array_merge(
			$element, array('options' => $options, 'class' => 'font-picker chosen-select')
		));
		
		$output = '<div class="typography">' . $select;
		
		if ($element['size']) {
			// TODO: custom range for each element
			
			$size_options = array_combine(range(9, 60), range(9, 60));
			if (empty($element['size']['default_value'])) {
				$size_options = array('' => __('Default', 'bunyad-admin')) + $size_options;
			}
			
			$output .= $this->render_select(array_merge($element, array(
				'name' => $element['name'] . '_size',
				'options' => $size_options,
				'value'   => $element['size']['value'],
				'class'   => 'size-picker'
			))) . 'px';
		}
		
		// add a color picker?
		if ($element['color']) {
			$output .= $this->render_color_picker(array_merge($element, array(
				'name'  => $element['name'] . '_color',
				'value' => $element['color']['value'],
			)));
		}
		
		if ($element['no_preview'] !== true) {
			$output .= '<p class="preview"></p>';
		}
		
		return $output . '</div>';
	}
	
	
	/**
	 * Get a list of google fonts
	 */
	public function get_google_fonts()
	{
		$fonts = file_get_contents(get_template_directory() .'/admin/fonts.json');
		return json_decode($fonts, true);
	}
	
	/**
	 * Set the default values for sub elements as specified
	 */
	public function set_sub_values($element, $sub = array())
	{
		foreach ($sub as $ele) 
		{
			$key = $element['name'] . '_' . $ele;
			
			// preserve original
			if (isset($element[$ele]['value'])) {
				$element[$ele]['default_value'] = $element[$ele]['value'];
			}
			
			// populate saved value if available
			if (isset($element[$ele]) && array_key_exists($key, $this->default_values)) {
				$element[$ele]['value'] = $this->default_values[$key];
			}
		}
		
		return $element;
	}
}