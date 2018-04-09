<?php
/**
 * Use accordion markup in AMP menu
 */
class Bunyad_Theme_Amp_Walker extends Walker_Nav_Menu 
{
	protected $in_accordion = array();

	/**
	 * Empty output for level start 
	 * 
	 * @see Walker_Nav_Menu::start_lvl()
	 */
	public function start_lvl(&$output, $depth = 0, $args = array()) {
		
	}
	
	/**
	 * Use spans for normal links and amp-accordion for sub-menus
	 *  
	 * @see Walker_Nav_Menu::start_el()
	 */
	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) 
	{
		
		$classes = empty($item->classes) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
		$class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
		
		/**
		 * Extract link from parent generator
		 */
		$link = ''; // Avoid passing $output ref
		parent::start_el($link, $item, $depth, $args, $id);
		
		if (preg_match('#(<a[^>]*>.+?</a>)#', $link, $match)) {
			$link = $match[1];
		}
		else {
			$link = $item->attr_title;
		}
		
		/**
		 * Generate accordion if has child items
		 */
		if ($this->has_children) {
			
			// $output .= str_repeat("\t", $depth+1);
			
			$output .= '<amp-accordion class="accordion" disable-session-states><section>' . "\n";
			$output .= '<h6' . $class_names .'>' . $link  . "</h6>\n";
			$output .= '<div class="sub-menu">';
			
			$this->in_accordion[$depth] = true;
		}
		else {
			$output .= "\t\t" . '<span'. $class_names .'>' . $link . '</span>';
		}
		
		$output .= "\n\n";
	}
	
	/**
	 * Empty output for element end
	 * 
	 * @see Walker_Nav_Menu::end_el()
	 */
	public function end_el(&$output, $item, $depth = 0, $args = array()) {
	}
	
	/**
	 * @see Walker_Nav_Menu::end_lvl()
	 */
	public function end_lvl(&$output, $depth = 0, $args = array()) 
	{
		// An accordion is open?
		if (!empty($this->in_accordion[$depth])) {
			
			// $output .= str_repeat("\t", $depth+1);
			
			$output .= '</div></section></amp-accordion>' . "\n";
			$this->in_accordion[$depth] = false;
		}
	}
}