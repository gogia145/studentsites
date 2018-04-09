<?php
/**
 * Ads handling for AMP
 */
class Bunyad_Theme_Amp_Ads
{
	public function __construct() {
		
		add_action('bunyad_amp_pre_main', array($this, 'ad_below_header'));
		
		// Register assets
		add_filter('amp_post_template_data', array($this, 'setup_assets'));
		
		add_filter('the_content', array($this, 'ad_below_post'));
		add_filter('the_content', array($this, 'ad_within_post'));
	}
	
	/**
	 * Register amp-ad JS if needed
	 */
	public function setup_assets($data)
	{
		$have_ads = false;
		$options  = Bunyad::options()->get_all('amp_ad_');
		
		foreach ($options as $option) {
			if (!empty($option)) {
				$have_ads = true;
				break;
			}
		}
		
		if ($have_ads) {
			$data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-0.1.js';
		}
		
		return $data;
	}
	
	/**
	 * Below header ad
	 */
	public function ad_below_header()
	{
		$code = $this->convert_adsense(
			(string) Bunyad::options()->amp_ad_header,
			true  // Above the fold
		);
		
		echo $this->_wrap_code($code, 'header');
	}
	
	/**
	 * Filter callback: Inject add below post content
	 */
	public function ad_below_post($content)
	{
		$code = $this->convert_adsense(
			(string) Bunyad::options()->amp_ad_post_below
		);
		
		$content .= $this->_wrap_code($code);
		
		return $content;
	}
	
	/**
	 * Filter callback: Add ads withint post content after X paragraphs
	 */
	public function ad_within_post($content)
	{
		$ads = Bunyad::options()->amp_ad_paragraphs;
		if (!$ads OR empty($ads['code'])) {
			return $content;
		}
		
		foreach ($ads['code'] as $key => $code) {
			$number = $ads['number'][$key];
			
			$code = $this->_wrap_code(
				$this->convert_adsense($code)
			);
			
			$content = $this->_inject_after_paragraph($code, $number, $content);
		}
		
		return $content;
	}
	
	
	/**
	 * Wrap the code with wrapper
	 * 
	 * @param string $code
	 * @param string $class
	 */
	public function _wrap_code($code, $class = '')
	{
		if (empty($code)) {
			return;
		}
		
		$code = '<div class="a-wrap '. esc_attr($class) .'">' . $code . '</div>';
		return $code;
	}
	
	/**
	 * Inject code after X paragraphs
	 */
	public function _inject_after_paragraph($insert, $number, $content)
	{
		$p_tag  = '</p>';
		$paras  = explode($p_tag, $content);
		$number = absint($number);
		
		foreach ($paras as $key => $para) {
			
			$paras[$key] .= $p_tag;
			
			// Add after this paragraph
			if ($key + 1 === $number) {
				$paras[$key] .= $insert;
			}
		}
		
		return implode('', $paras);
	}
	
	
	/**
	 * Convert Adsense code to AMP code
	 * 
	 * @param  string  $code
	 * @param  boolean $above  Above the fold?
	 */
	public function convert_adsense($code, $above = false) 
	{
		if (!strstr($code, 'adsbygoogle')) {
			return $code;
		}
			
		// Get ad client
		preg_match('#<ins[^>]*data-ad-client="([^"]*)"#s', $code, $match);
		if (!empty($match[1])) {
			$ad_client = $match[1];
		}
		
		// Get ad slot
		preg_match('#<ins[^>]*data-ad-slot="([^"]*)"#s', $code, $match);
		if (!empty($match[1])) {
			$ad_slot = $match[1];
		}
		
		// Both required
		if (!isset($ad_client) OR !isset($ad_slot)) {
			return;
		}
		
		// Get width and height
		$width  = 300;
		$height = 250;
		
		preg_match('#style=.*width:(\d+).*height:(\d+)#', $code, $match);
		if (!empty($match[1]) && !empty($match[2])) {
			$width  = $match[1];
			$height = $match[2];
		}
		
		$attribs = array(
			'type'   => 'adsense',
			// 'layout' => 'responsive',  // Disabled: Responsive doesn't serve ads often - 300x250 is better bet
			'width'  => intval($width),
			'height' => intval($height),
			'data-ad-client' => $ad_client,
			'data-ad-slot'   => $ad_slot
		);
		
		// Above the fold - limit height
		if ($above) {
			
			unset($attribs['width']);
			
			$attribs = array_merge($attribs, array(
				'layout' => 'fixed-height',
				'height' => 100
			));
		}
		
		$code = '<amp-ad ' 
				. Bunyad::markup()->attribs('amp-ad-code', $attribs, array('echo' => false)) 
				. '></amp-ad>';
		
		return $code;
	}
}

// init and make available in Bunyad::get('amp_ads')
Bunyad::register('amp_ads', array(
	'class' => 'Bunyad_Theme_Amp_Ads',
	'init' => true
));