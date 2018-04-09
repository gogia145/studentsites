<?php 
/**
 * AMP Post meta
 */

$post_author = $this->get('post_author');

if ($post_author) {
	$author_avatar_url = get_avatar_url($post_author->user_email, array('size' => 30));
}

?>

<div class="post-meta">

<?php if ($post_author) : ?>
	<span class="author-img">
		<amp-img src="<?php echo esc_url($author_avatar_url); ?>" width="30" height="30" layout="fixed" class="avatar"></amp-img>
	</span>
<?php endif; ?>
	
	<span class="posted-by">
		<?php _ex('By', 'Post Meta', 'bunyad'); ?> 
		<?php the_author_posts_link(); ?>
	</span>
	
			 
	<span class="posted-on">
		<time class="post-date" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
	</span>

</div>	