<?php
/**
 * Show options for ordering
 * 
 * @version     3.0.0
 */

global $woocommerce, $wp_query;

if ( 1 == $wp_query->found_posts || ! woocommerce_products_will_display() )
	return;
?>
<form class="woocommerce-ordering" method="get">
		<input type="hidden" name="orderby" value="" />

		<?php
			$catalog_orderby = $catalog_orderby_options;
			$selected        = current($catalog_orderby);
			
			if (array_key_exists($orderby, $catalog_orderby)) {
				$selected = $catalog_orderby[$orderby];
			}				
		?>
		
		<div class="order-select">
		
			<span><?php echo esc_html($selected); ?> <i class="fa fa-angle-down"></i></span>
		
			<ul class="drop">

			<?php 
	
				foreach ($catalog_orderby as $id => $name) {
					echo '<li data-value="' . esc_attr($id) . '" class="' . ($orderby == $id ? 'active' : '') . '"><a href="#">' . esc_attr($name) . '</a></li>';
				}
			?>
			
			</ul>
		</div>
		
	<?php if (function_exists('wc_query_string_form_fields')): ?>
		<?php wc_query_string_form_fields( null, array( 'orderby', 'submit' ) ); ?>
	<?php 
		else: 
			trigger_error('Please upgrade to WooCommerce version 3.0+', E_USER_ERROR);
		endif;
	?>
</form>