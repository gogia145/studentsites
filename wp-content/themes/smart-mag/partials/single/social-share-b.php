<?php
/**
 * Partial template for social share buttons on single page
 */

// Post and media URL
$url   = urlencode(get_permalink());
$media = urlencode(wp_get_attachment_url(get_post_thumbnail_id($post->ID)));

// Social Services
$services = array(
	'facebook' => array(
		'label' => __('Share', 'bunyad'),
		'icon'  => 'facebook',
		'url'   => 'http://www.facebook.com/sharer.php?u=' . $url,
	),
		
	'twitter' => array(
		'label' => __('Tweet', 'bunyad'), 
		'icon'  => 'twitter',
		'url'   => 'http://twitter.com/home?status=' . $url,
	),
		
	'gplus' => array(
		'label' => __('Google+', 'bunyad'), 
		'icon'  => 'google-plus',
		'url'   => 'http://plus.google.com/share?url=' . $url,
	),
		
	'pinterest' => array(
		'label' => __('Pinterest', 'bunyad'), 
		'icon'  => 'pinterest',
		'url'   => 'http://pinterest.com/pin/create/button/?url='. $url . '&media=' . $media,
		'key'   => 'sf_instagram_id',
	),
	
	'linkedin' => array(
		'label' => __('LinkedIn', 'bunyad'), 
		'icon'  => 'linkedin',
		'url'   => 'http://www.linkedin.com/shareArticle?mini=true&url=' . $url,
	),
		
	'tumblr' => array(
		'label' => __('Tumblr', 'bunyad'), 
		'icon'  => 'tumblr',
		'url'   => 'http://www.tumblr.com/share/link?url=' . $url . '&name=' . urlencode(get_the_title()),
	),
		
	'email' => array(
		'label' => __('Email', 'bunyad'), 
		'icon'  => 'envelope-o',
		'url'   => 'mailto:?subject='. rawurlencode(get_the_title()) .'&body=' . $url,
	),
);

$active = array('facebook', 'twitter', 'gplus', 'pinterest', 'linkedin', 'tumblr', 'email');

?>

<?php if ((is_single() OR Bunyad::options()->social_icons_classic) && Bunyad::options()->social_share): ?>
	
	<div class="post-share-b cf">
		
		<?php 
			foreach ($active as $key): 
				$service = $services[$key];
		?>
		
			<a href="<?php echo esc_url($service['url']); ?>" class="cf service <?php echo esc_attr($key); ?>" target="_blank">
				<i class="fa fa-<?php echo esc_attr($service['icon']); ?>"></i>
				<span class="label"><?php echo esc_html($service['label']); ?></span>
			</a>
				
		<?php endforeach; ?>
		
		<?php if (count($active) > 3): ?>
			<a href="#" class="show-more">+</a>
		<?php endif; ?>
		
	</div>
	
<?php endif; ?>