<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="col-md-4">
	<div class="shortcode_results_1 <?php echo empty($data["show_imgs"])? "no_img":""; ?>">
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
</div>
