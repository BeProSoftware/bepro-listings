<div class="shortcode_results_2">
<?php
	do_action( 'bepro_listings_list_above_title', $result, $data );
	do_action( 'bepro_listings_list_title', $result, $data);
	do_action( 'bepro_listings_list_below_title', $result, $data);
	do_action( 'bepro_listings_list_above_image', $result, $data);
	do_action( 'bepro_listings_list_image', $result, $data);
	do_action( 'bepro_listings_list_after_image', $result, $data);
	do_action("bepro_listings_list_content", $result, $data);
	do_action("bepro_listings_after_content", $result, $data);
	do_action("bepro_listings_list_end", $result, $data);
?>
</div>