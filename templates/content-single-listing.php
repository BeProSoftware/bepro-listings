<?php	

	$data = get_option("bepro_listings");	
	$launch_tabs = apply_filters("bpl_show_page_tabs", false);	
	if(($data["show_content"] == 1) || ($data["show_geo"] == 1) || ($data["show_comments"] == 1) || ($data["show_details"] == 1) || $launch_tabs){		
		add_action("bepro_listings_item_details", "bepro_listings_item_tabs");	
	}	
	echo "<h1>";	
	do_action( 'bepro_listings_item_title' );	
	echo "</h1>";	
	do_action( 'bepro_listings_item_above_gallery' );	
	do_action( 'bepro_listings_item_gallery');	
	do_action( 'bepro_listings_item_after_gallery');	
	do_action( 'bepro_listings_item_before_details');	
	do_action( 'bepro_listings_item_details');	
	do_action( 'bepro_listings_item_after_details');	
	do_action("bepro_listings_item_content_info");	
	do_action("bepro_listings_item_end");

?>