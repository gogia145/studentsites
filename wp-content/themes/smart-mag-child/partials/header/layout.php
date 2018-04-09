<?php
/**
 * Partial: Default Header
 */
$class = (empty($class) ? 'main-head' : $class);
// Get the partial template for top bar
get_template_part('partials/header/top-bar');

?>
	<div id="main-head" class="<?php echo esc_attr($class); ?>">
		
		<div class="wrap">
			
			<?php if (Bunyad::options()->mobile_header == 'modern'): // modern mobile header? ?>
				<div class="mobile-head">
				
					<div class="menu-icon"><a href="#"><i class="fa fa-bars"></i></a></div>
					<div class="title">
						<?php get_template_part('partials/header/logo'); ?>
					</div>
					<div class="search-overlay">
						<a href="#" title="<?php esc_attr_e('Search', 'bunyad'); ?>" class="search-icon"><i class="fa fa-search"></i></a>
					</div>
					
				</div>
			<?php endif; ?>

			<header <?php Bunyad::markup()->attribs('header', array('class' => Bunyad::options()->header_style)); ?>>
			
				<div class="title">
					<?php get_template_part('partials/header/logo'); ?>
				</div>
				<div class="mid-menu">
				<?php
				  wp_nav_menu( array(
					'menu' => 'Mid Menu'
				) ); 
								 ?></div>
				<?php if (Bunyad::options()->header_style !== 'centered'): ?>

					<div class="right">
					 
					 <?php
					 $selected = '';
					 if(isset($_SESSION['global_campus_value']) && !empty($_SESSION['global_campus_value'])){
					 	$selected = $_SESSION['global_campus_value'];
					 }
					 global $wpdb;
					$results = $wpdb->get_var( "SELECT input_value FROM {$wpdb->prefix}fed_post WHERE input_meta = '_select_campus'");
					// $field_key = "field_5a82e0c11c92a"; //field key of parent
					 //$field = get_field_object($field_key); 
					 if( $results )
						{
							$choices = array();
							$resultant = explode('|',$results);
							if(isset($resultant) && !empty($resultant)){
								foreach($resultant as $res){
									$resval = explode(',',$res);
									$choices[$resval[0]] = $resval[1];
								}
							}
						    echo '<select onchange="global_campus_selectbox(this)">';
						        foreach( $choices as $k => $v )
						        {
						        	$checked = '';
						        	if($v == $selected){
						        		$checked='selected';
						        	}
						            echo '<option '.$checked.' value="' . $k . '">' . $v . '</option>';
						        }
						    echo '</select>';
						}
					  ?>
					<?php dynamic_sidebar('header-right');	?>
					</div>
					
				<?php endif; ?>
				
			</header>
				
			<?php if (!Bunyad::options()->nav_layout): // normal width navigation? ?>
				
				<?php get_template_part('partials/header/nav'); ?>
				
			<?php endif; ?>
				
		</div>
		
		<?php 
			// Full width navigation goes out of wrap container
			if (Bunyad::options()->nav_layout == 'nav-full'): 
				get_template_part('partials/header/nav');
			endif;
		?>
		
	</div>