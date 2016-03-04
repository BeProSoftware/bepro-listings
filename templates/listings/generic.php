<div class="shortcode_results">
<?php
	do_action( 'bepro_listings_list_above_title', $result, $data );
	do_action( 'bepro_listings_list_title', $result, $data);
	do_action( 'bepro_listings_list_below_title', $result, $data);
	do_action( 'bepro_listings_list_above_image', $result, $data);
	do_action( 'bepro_listings_list_image', $result, $data);
	do_action( 'bepro_listings_list_after_image', $result, $data);
	do_action("bepro_listings_list_content", $result, $data);
	do_action("bepro_listings_list_end", $result, $data);
?>
<div style="clear:both"><br /></div>
</div>