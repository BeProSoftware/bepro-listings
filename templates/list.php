<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
		global $wpdb, $bp;
		echo "<div class='bepro_item_listings bepro_plugin bepro_container'><div class='bepro_row'>";
		do_action("bl_before_frontend_listings");
		if(isset($_GET["message"]))echo "<span class='classified_message'>".$_GET["message"]."</span>";
		echo "<h3 class='my_listings_heading'>".__("My Item Listings", "bepro-listings")."</h3>"; 
		
		if((@$items) && (sizeof($items) > 0)){
			echo "<table id='classified_listings_table' class='bepro_table'><tr>
					<th>".__("Name", "bepro-listings")."</th>
					<th>".__("Type", "bepro-listings")."</th>
					<th>".__("Image", "bepro-listings")."</th>
					<th>".__("Address", "bepro-listings")."</th>
					<th>".__("Notices", "bepro-listings")."</th>
					<th>".__("Status", "bepro-listings")."</th>
					<th>".__("Actions", "bepro-listings")."</th>
				</tr>
			";
			//variables to check if listing has expired
			$date = new DateTime(@$item->expires);
			$now = new DateTime();
			
			foreach($items as $item){
				$notice = __("None","bepro-listings");
				$post_status = (($item->post_status == "publish")? __("Published","bepro-listings"):__("Pending","bepro-listings"));
				$order_status = @$item->order_status;
				
				if(!empty($data["require_payment"]) && ($post_status == "Published")){
					if(@$item->order_status && ($item->order_status!= 1)){
						$notice = __("Payment Issue","bepro-listings");
					}else{
						$notice = __("Expires:","bepro-listings")." ".((empty($item->expires) || ($item->expires == "0000-00-00 00:00:00"))? __("Never","bepro-listings"):date("M, d Y", strtotime($item->expires)));
					}
				}else if(!empty($data["require_payment"])  && ($post_status == "Pending")){
					if($order_status == 1){
						$notice = __("Paid: Processing","bepro-listings");
					}else if($order_status == 2){
						$notice = __("Pay: Required","bepro-listings");
					}else if($order_status == 3){
						$notice = __("Pay: Failed","bepro-listings");
					}else{
						$notice = __("Options: Missing","bepro-listings");
					}
				}
				echo "
					<tr>
						<td>".$item->post_title."</td>
						<td>".get_the_term_list($item->post_id, 'bepro_listing_types', '', ', ','')."</td>
						<td>".((has_post_thumbnail( $item->post_id ))?__("Yes","bepro-listings"):__("No","bepro-listings"))."</td>
						<td>".((isset($item->lat) && isset($item->lon))?__("Valid","bepro-listings"):__("Not Valid","bepro-listings"))."</td>
						<td>".$notice."</td>
						<td>".$post_status."</td>
						<td>";
						
						
						if(!empty($item->bl_order_id) && ($post_status == "publish") && !empty($data["require_payment"]) && ($date < $now)){
							echo __("Pay","bepro-listings");
						}else if($post_status == "Published"){ 
							echo "<a href='".get_permalink($item->post_id)."' target='_blank'>".__("View", "bepro-listings")."</a>";
						}else if((@$order_status) && ($order_status != 1)){
							echo __("Pay","bepro-listings");
						}else if(empty($item->bl_order_id) && ($post_status != "publish") && !empty($data["require_payment"])){
							echo $data["currency_sign"]."???";
						}else{
							echo __("Wait", "bepro-listings");
						}
						if((!function_exists("bp_is_my_profile")) ||($bp->displayed_user->id == $bp->loggedin_user->id) || ($data["buddypress"] == false))echo " | <a href='$listing_url".$item->id."'>".__("Edit", "bepro-listings")."</a> | <a id='file::".$item->post_id."::".$item->post_title."' href='#' class='delete_link'>".__("Delete", "bepro-listings")."</a>";
				echo "	</td>
					</tr>
				";
			}
		}else{
			echo "<table id=''>";
			if(function_exists("bp_is_my_profile") && @bp_is_my_profile()){
				echo "<tr><td colspan=7>".__("No Live listings created", "bepro-listings")."</a></td></tr>";
			}else{
				echo "<tr><td colspan=7>".__("No live listings for this user", "bepro-listings")."</td></tr>";
			}
		}		
		echo "</table>";
		echo "</div></div>";
?>