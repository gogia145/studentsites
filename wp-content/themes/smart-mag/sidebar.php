
	<?php
	
		// sidebar HTML attributes
		$attribs = array('class' => 'col-4 sidebar');
		$sticky  = 0;
		if (Bunyad::options()->sticky_sidebar) {
			$attribs['data-sticky'] = 1;
			$sticky = 1;
		}
	
		do_action('bunyad_sidebar_start'); 	
	?>		
		
		
		<aside <?php Bunyad::markup()->attribs('sidebar', $attribs); ?>>
		
		<?php // Add sticky sidebar wrappers to support ancient ad networks still relying on document.write() ?>
			<div class="<?php echo ($sticky ? 'theiaStickySidebar' : ''); ?>">
			
				<ul>
				
				<?php if (!dynamic_sidebar('primary-sidebar')) : ?>
					<?php _e("<li>Nothing yet.</li>", 'bunyad'); ?>
				<?php endif; ?>
		
				</ul>
		
			</div>
		
		</aside>
		
	<?php do_action('bunyad_sidebar_end'); ?>