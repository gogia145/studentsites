<!doctype html>
<html amp <?php echo AMP_HTML_Utils::build_attributes_string($this->get('html_tag_attributes')); ?>>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css?ver=4.7.3" />
	
	<?php do_action('amp_post_template_head', $this); ?>
	
	<script async custom-element="amp-accordion" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>
	<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
	
	<style amp-custom>
		<?php $this->load_parts(array('style')); ?>
		<?php do_action('amp_post_template_css', $this); ?>
	</style>
</head>

<body class="<?php echo esc_attr($this->get('body_class')); ?>">

<?php $this->load_parts(array('header-bar')); ?>

<?php do_action('bunyad_amp_pre_main'); ?>

<div class="main-wrap">
	<?php while (have_posts()): the_post(); ?>
	<article class="the-post">
	
		<header class="post-header">
			<div class="category cf">
				<?php echo Bunyad::blocks()->cat_label(array('force_show' => true)); ?>
			</div>
		
			<h1 class="post-title"><?php echo wp_kses_data($this->get('post_title')); ?></h1>
			
			<?php $this->load_parts(apply_filters('amp_post_article_header_meta', array('meta'))); ?>
			<?php $this->load_parts(array('featured-image')); ?>
	
		</header>
	
		<div class="post-content">
			<?php echo $this->get('post_amp_content'); // amphtml content; no kses ?>
		</div>
	
		<footer class="post-footer">
			<?php $tags = get_the_tag_list('', '', '', $this->ID); ?>
			
			<?php if ($tags && !is_wp_error($tags)): ?>
			<div class="post-tags">
				<?php echo $tags; ?>
			</div>
			<?php endif; ?>
			
			<?php get_template_part('partials/single/social-share'); ?>
		
			<?php if (comments_open()): ?>
		
				<a href="<?php echo esc_url($this->get('comments_link_url')); ?>" class="comment-link"><?php esc_html_e('Write a Comment', 'bunyad'); ?></a>
				
			<?php endif; ?>
			
		</footer>
	
	</article>
	
	
	<?php endwhile; ?>
</div>

<?php $this->load_parts(array('footer')); ?>

<?php do_action('amp_post_template_footer', $this); ?>

</body>
</html>