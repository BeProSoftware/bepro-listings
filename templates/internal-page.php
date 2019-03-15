<?php
/**
 * The Template for displaying all single listings.
 *
 * Override this template by copying it to yourtheme/template/single-listing.php
 *
 * @author 		BePro Software Team
 * @package 	bepro_listings/Templates
 * @version     2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 ?>
<div class="entry">
		<?php
		global $wpdb, $post;
		//get listing information related to this post
		$page_id = $post->ID;
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id = ".$page_id);
		//get settings
		$data = get_option("bepro_listings");
		if($item){
			$launch_tabs = apply_filters("bpl_show_page_tabs", false);
			if(($data["show_content"] == 1) || ($data["show_geo"] == 1) || ($data["show_comments"] == 1) || $launch_tabs){
				add_action("bepro_listings_item_details", "bepro_listings_item_tabs");
			}
			$page_template = "";
			$page_template = apply_filters("bepro_listings_change_page_template",$page_template,$item);
			if(empty($page_template))
				$page_template = plugin_dir_path( __FILE__ )."/content-single-listing.php";
			include($page_template); 
		}else{
			the_content();
		}
		?>
</div>
