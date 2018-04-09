<?php
/**
 * Header for AMP
 */

$logo = $this->get('the_logo');
$has_nav = has_nav_menu('main-mobile');

?>


<?php if ($has_nav): ?>
<amp-sidebar id="menu-canvas" class="menu-canvas" layout="nodisplay" role="menu">
<div class="menu-contain">
	<div class="top">
		<span><?php esc_html_e('Navigate', 'bunyad'); ?></span>
		<i class="close-icon fa fa-times" on="tap:menu-canvas.close" tabindex="1" role="button"></i>
	</div>
	<?php 
	wp_nav_menu(array(
		'theme_location' => 'main-mobile', 
		'walker'     => new Bunyad_Theme_Amp_Walker(),
		'items_wrap' => '<div class="%2$s">%3$s</div>',
		'container_class' => 'main-menu',
	)); 
	?>	
</div>
</amp-sidebar>

<?php endif; ?>


<header id="top" class="main-head">
	<div class="wrap">

		<?php if ($has_nav): ?>	
			<span class="main-nav-icon" on="tap:menu-canvas.toggle" tabindex="0" role="button"></span>
		<?php endif; ?> 
	
		<a href="<?php echo esc_url($this->get('home_url')); ?>" class="logo-link">
			
			<?php if ($logo): ?>
			
				<amp-img src="<?php echo esc_url($logo['url']); ?>" 
					width="<?php echo esc_attr($logo['width']); ?>" height="<?php echo esc_attr($logo['height']); ?>" class="logo-image" 
					alt="<?php echo esc_attr($this->get('blog_name')); ?>"></amp-img>
			
			<?php else: ?>
			
				<?php echo esc_html($this->get('blog_name')); ?>
				
			<?php endif; ?>
		</a>
	</div>
</header>
