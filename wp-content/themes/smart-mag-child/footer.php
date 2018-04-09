
	<?php do_action('bunyad_post_main_content'); ?>
	
	<footer class="main-footer">
	
	<?php if (!Bunyad::options()->disable_footer): ?>
		<div class="wrap">
		
		<?php if (is_active_sidebar('main-footer')): ?>
			<ul class="widgets row cf">
				<?php dynamic_sidebar('main-footer'); ?>
			</ul>
		<?php endif; ?>
		
		</div>
	
	<?php endif; ?>
	
	
	<?php if (!Bunyad::options()->disable_lower_footer): ?>
		<div class="lower-foot">
			<div class="wrap">
		
			<?php if (is_active_sidebar('lower-footer')): ?>
			
			<div class="widgets">
				<?php dynamic_sidebar('lower-footer'); ?>
			</div>
			
			<?php endif; ?>
		
			</div>
		</div>		
	<?php endif; ?>
	
	</footer>
	
</div> <!-- .main-wrap -->

<?php wp_footer(); ?>
<script>
var pageurl = '';
</script>
<?php if(is_page('be-a-reporter')){ ?>
<script>
pageurl = 'be-a-reporter';
</script>
<?php } ?>
<script>
var ajax_url = "<?php echo admin_url( 'admin-ajax.php' )?>";
	function global_campus_selectbox(obj){
		val = jQuery(obj).val();
		jQuery.ajax({
			url : ajax_url,
			type : 'post',
			data : {
				action : 'global_campus_select',
				val : val
			},
			success : function( response ) {
				window.location.reload();
			}
		});
	}
	jQuery(document).ready(function(){
		if(pageurl!=''){
			jQuery('.fed_form_post').find('select option').each(function(){
				if(jQuery(this).val()=='subscriber'){
					jQuery(this).remove();
				}
			});
		}
	});
 	jQuery(document).ready(function(){	

    jQuery("#menu-main-menu li a").each(function() {	
		 jQuery(this).addClass("myanchor");
         jQuery(".mega-menu").find('a').removeClass("myanchor");		
		});
	}); 
</script>
</body>
</html>
