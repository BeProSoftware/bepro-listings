<?php
	
	require("../../../../wp-load.php");
	if(!function_exists("wp_create_thumbnail"))
	require ( '../../../../wp-admin/includes/image.php' );
	
	if(empty($_POST["id"]) || !is_user_logged_in() ||!is_numeric($_POST["id"]) ) exit;
	/*CHECK USER IS ALLOUD TO INTERACT WITH THIS item
	*
	*
	*
	*/
	
	global $wpdb, $bp;
	// Helper functions
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;
	$yours = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE user_id = $user_id AND id = ".$_POST["id"]);
	if(!$yours) exit;

function exit_status($str){
	echo json_encode(array('status'=>$str));
	exit;
}	

	if(wp_delete_post( $yours->post_id))exit_status('Deleted Successfully!');	
	exit_status('Something went wrong with your Delete!');
		
?>