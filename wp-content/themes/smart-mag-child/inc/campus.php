<?php  
	if ( isset($_POST['campus_submit']) && !empty($_POST['campus_submit']) ) {
		 
		update_option( 'campus_of_week', $_POST['campus'] );
		
	}
	global $wpdb;
	$results = $wpdb->get_var( "SELECT input_value FROM {$wpdb->prefix}fed_post WHERE input_meta = '_select_campus'");
	 
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
		$campus_select = get_option( 'campus_of_week' );  
		?>
		<h3>Select Campus of the Week</h3>
		<form action="" method="post">
		<?php 
			foreach( $choices as $k => $v ) {  
			$checked = '';
				if($v == $campus_select){
					$checked = 'checked';
				}
				 
				if ($k != 'null') {
				 echo '<input type="radio" name="campus" value="'.$v.'" '.$checked.'> '.$v.'<br>';
					continue;  
				}
			} 

	  } ?>
	  <input type="submit" class="button action"name="campus_submit" value="Submit">
	</form>

