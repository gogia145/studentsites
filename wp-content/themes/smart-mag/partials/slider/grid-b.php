<?php 
/**
 * Grid slider - B
 *  
 * To be calld from partial-sliders.php
 */
 
$count = 0;

?>
	
	<div class="main-featured">
		<div class="wrap cf">
		
		<div <?php Bunyad::markup()->attribs('featured-grid-b', array_merge(array('class' => 'featured-grid featured-grid-b'), $data_vars)); ?>>
			<ul class="grid">
			
				<li class="first">
					<?php while ($query->have_posts()): $query->the_post(); ?>

					<div class="item large-item">
						
						<a href="<?php the_permalink(); ?>" class="image-link"><?php
							the_post_thumbnail('grid-slider-b-large', array('alt' => esc_attr(get_the_title()), 'title' => '')); ?></a>
						
						<div class="caption caption-large">
							<?php echo Bunyad::blocks()->cat_label(array('force_show' => true)); ?>
						
							<h3><a href="<?php the_permalink(); ?>" class="item-heading"><?php the_title(); ?></a></h3>
							<time class="the-date" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>

						</div>
												
					</div>
					
					<?php break; ?>
					
					<?php endwhile; ?>
				
				</li>

				<li class="second">
					<?php while ($query->have_posts()): $query->the_post(); $count++; ?>
					
					<?php 
						// Medium or small
						if ($count == 1) {
							$class = 'item medium-item';
							$image = 'grid-slider-b-med'; 
						}
						else {
							$class = 'col-6 item small-item';
							$image = 'grid-slider-b-small';
						}
					?>
					
					<div class="<?php echo esc_attr($class); ?>">
					
						<a href="<?php the_permalink(); ?>" class="image-link"><?php
							the_post_thumbnail($image, array('alt' => esc_attr(get_the_title()), 'title' => '')); ?></a>
							
						<div class="caption caption-small">
							<?php echo Bunyad::blocks()->cat_label(array('force_show' => true)); ?>
						
							<h3><a href="<?php the_permalink(); ?>" class="item-heading heading-small"><?php the_title(); ?></a></h3>
							<time class="the-date" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>

						</div>

					</div>
					
					<?php endwhile; ?>

				</li>
				
			</ul>

			<?php wp_reset_query(); ?>
			
		</div>
		
		</div> <!-- .wrap -->
	</div>
