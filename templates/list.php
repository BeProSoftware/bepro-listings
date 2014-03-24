<?php
		global $wpdb, $bp;
		
	
		if(isset($_GET["message"]))echo "<span class='classified_message'>".$_GET["message"]."</span>";
		echo "
			<h2>My Item Listings</h2>
			<table id='classified_listings_table'><tr>
				<td>Name</td>
				<td>Type</td>
				<td>Image</td>
				<td>Address</td>
				<td>Posted</td>
				<td>Status</td>
				<td>Actions</td>
			</tr>
		";
		if(sizeof($items) > 0){
			foreach($items as $item){
				echo "
					<tr>
						<td>".$item->post_title."</td>
						<td>".get_the_term_list($item->post_id, 'bepro_listing_types', '', ', ','')."</td>
						<td>".((has_post_thumbnail( $item->post_id ))?"Yes":"No")."</td>
						<td>".((isset($item->lat) && isset($item->lon))?"Valid":"Not Valid")."</td>
						<td>".date("M jS, Y", strtotime($item->created))."</td>
						<td>".(($item->post_status == "publish")? "Published":"Pending")."</td>
						<td>".(($item->post_status == "publish")? "<a href='".get_permalink($item->post_id)."' target='_blank'>View</a>":"");
						if($bp->displayed_user->id == $bp->loggedin_user->id)echo " | <a href='$listing_url".$item->id."'>Edit</a> | <a id='file::".$item->post_id."::".$item->post_title."' href='#' class='delete_link'>Delete</a>";
				echo "	</td>
					</tr>
				";
			}
		}else{
			if($bp->displayed_user->id == $bp->loggedin_user->id){
				echo "<tr><td colspan=7>No Live listings. Why not create one for free <a href='$listing_url'>here</a></td></tr>";
			}else{
				echo "<tr><td colspan=7>No Live listings for this user</td></tr>";
			}
		}		
		echo "</table>";
?>