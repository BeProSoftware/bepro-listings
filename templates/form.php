<?php
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly	do_action("bepro_listing_form_before", @$post_data); 	
	$data = get_option("bepro_listings");	$num_cols = !empty($data["bpl_form_cols"])? $data["bpl_form_cols"]:2;
		if(!empty($validate) && ($validate == "on")){
			echo '
			<script type="text/javascript" src="'.plugins_url("bepro-listings/js/jquery.maskedinput-1.3.min.js", "bepro-listings" ).'"></script>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery("#bepro_create_listings_form").validate({
							rules: {
								item_name: "required",
								content: {
									required: true,
									minlength: 15
								},
								categories: "required",
								first_name: "required",
								last_name: "required",
								country: "required",
								email: {
									required: true,
									email: true
								},
								password: {
									required: true,
									minlength: 5
								},
								agree: "required"
							},
							messages: {
								item_name: "'.__("Please give this a name","bepro-listings").'",
								content: "'.__("Please tell us about this","bepro-listings").'",
								first_name: "'.__("Please enter your firstname","bepro-listings").'",
								last_name: "'.__("Please enter your lastname","bepro-listings").'",
								password: {
									required: "'.__("Please provide a password","bepro-listings").'",
									minlength: "'.__("Your password must be at least 5 characters long","bepro-listings").'"
								},
								email: "'.__("Please enter a valid email address","bepro-listings").'",
								country: "'.__("Where in the world is this?","bepro-listings").'",
								agree: "'.__("Please accept our policy","bepro-listings").'"
							},
							submitHandler: function(form) {
								form.submit();
							}
						});
					});
				</script>
			';
		}
		
		echo '
			<form method="post" enctype="multipart/form-data" id="bepro_create_listings_form" '.((@$listing_saved == true)? "class='bpl_listing_saved'":"").'>
				<input type="hidden" name="save_bepro_listing" value="1">
				<input type="hidden" name="bepro_post_id" value="'.@$post_data->ID.'">
				<input type="hidden" name="redirect" value="'.@$redirect.'">				<div id="bpl_form_fields" style="column-count: '.$num_cols.';-moz-column-count: '.$num_cols.';-webkit-column-count: '.$num_cols.';">				';
		do_action("bepro_listing_form_start", @$post_data);
		echo '
			<div class="add_listing_form_info bepro_form_section">';
			if(($data["require_payment"] == 2)){
				if($item->bl_order_id){
					bl_form_package_field($item->bl_order_id);
				}else{
					bl_form_package_field();
				}
			}
			echo '
				<h3>'.__("Item Information", "bepro-listings").'</h3>
				<span class="form_label">'.__("Item Name", "bepro-listings").'</span><input type="" id="item_name" name="item_name" value="'.@$post_data->post_title.'" '.(isset($post_data->post_title)?'readonly="readonly"':"").'><br />';
				
				if(@$data["use_tiny_mce"]){
					ob_start();
					wp_editor( @$post_data->post_content, "content", array( 'textarea_name' =>  "content", "media_buttons" => false, "teeny" =>true, "quicktags"=>false) );
					$editor = ob_get_contents();
					ob_end_clean();
					echo '<span class="form_label">'.__("Description", "bepro-listings").'</span>'.$editor;
				}else{
					echo '<span class="form_label">'.__("Description", "bepro-listings").'</span><textarea name="content" id="content">'.@$post_data->post_content.'</textarea>';
				}
				
				//show categories
				$exclude = explode(",",$data["bepro_listings_cat_exclude"]);
				$required = explode(",",$data["bepro_listings_cat_required"]);
				
				//show categories
				$cats = get_terms( array('bepro_listing_types'), array("hide_empty" => false));
				$required_list = "";
				$normal_list = "";
				if($cats){
					$cat_style = get_form_cats(@$cats, @$exclude, @$required, @$categories);
					
					//category section
					echo "<div class='bepro_listing_category_section'><h3>".__("Categories","bepro-listings")." : </h3>";					echo (empty($data["bepro_listings_cat_required"]) || empty($cat_style["required_list"])? "" : "<p><strong>".__("Pre Selected","bepro-listings")."</strong></p>".$cat_style["required_list"]);					echo "<div style='clear:both'><br /></div><p><strong>".__("Options","bepro-listings")."</strong></p>".$cat_style["normal_list"]."</div>";
				}
				echo "<div style='clear:both'></div>";
				
		do_action("bepro_listing_form_in_item_before_images", @$post_data);		
			
				if(!empty($num_images) && ($num_images > 0) && (!empty($data["show_imgs"]))){
					$counter = 1;
					echo "<span class='bepro_form_images'><span class='form_heading'>".__("Files","bepro-listings")." (".apply_filters("bepro_listings_upload_file_heading","imgs").")</span>";
					while($counter <= $num_images){
						$filename = @$thunmbnails[$counter-1][5];
						echo '<span class="form_label">'.(empty($filename)? __("File ".$counter, "bepro-listings"):$filename).'</span>';
						if(isset($thunmbnails[$counter-1]) && !stristr(@$thunmbnails[$counter-1][0], "no_img.jpg")){
							echo "<img src='".@$thunmbnails[$counter-1][0]."'><br /><span>".__("Delete","bepro-listings")."?</span><input type='checkbox' name='delete_image_".($counter-1)."' value='".@$thunmbnails[$counter-1][4]."'><br />";
						}
						echo '<input type="file" name="bepro_form_image_'.$counter.'"><br />';
						$counter++;
					}
					echo "</span>";
				}
		do_action("bepro_listings_form_after_item", @$post_data);		
		echo '		
			</div>
			';		
		
		
		if(!empty($show_cost) && ($show_cost == "on")){		
			echo '
			<div class="add_listing_form_cost bepro_form_section">
				<span class="form_label">'.__(apply_filters("bl_cost_listing_label","Cost"), "bepro-listings").'</span><input type="text" name="cost" value="'.(isset($item->cost)? $item->cost:0).'"><br />';
			
			do_action("bepro_listing_form_after_cost", $post_data);
			echo '</div>';
			
		}
		
		if(!empty($show_con) && ($show_con == "on")){		
			echo '		
				<div class="add_listing_form_contact bepro_form_section">
					<h3>'.__("Contact Information", "bepro-listings").'</h3>
					<span class="form_label">'.__("First Name", "bepro-listings").'</span><input type="text" id="first_name" name="first_name" value="'.@$item->first_name.'">
					<span class="form_label">'.__("Last Name", "bepro-listings").'</span><input type="text" id="last_name" name="last_name" value="'.@$item->last_name.'">
					<span class="form_label">'.__("Email", "bepro-listings").'</span><input type="text" id="email" name="email" value="'.@$item->email.'">
					<span class="form_label">'.__("Phone", "bepro-listings").'</span><input type="text" name="phone" id="phone" value="'.@$item->phone.'">
					<span class="form_label">'.__("Website", "bepro-listings").'</span><input type="text" name="website" value="'.@$item->website.'">';
			
			do_action("bepro_listing_form_after_contact", $post_data);		
			echo '	</div>';
				
		}
		
		
		if(!empty($show_geo) && is_numeric($show_geo)){
			echo '
				<div class="add_listing_form_geo bepro_form_section">
					<h3>'.__("Location Information", "bepro-listings").'</h3>
					<span class="form_label">'.__("Address", "bepro-listings").'</span><input type="text" name="address_line1" value="'.@$item->address_line1.'">
					<span class="form_label">'.__("City", "bepro-listings").'</span><input type="text" name="city" value="'.@$item->city.'">
					<span class="form_label">'.__("State", "bepro-listings").'</span><input type="text" name="state" value="'.@$item->state.'">
					<span class="form_label">'.__("Country", "bepro-listings").'</span><input type="text" id="country" name="country" value="'.@$item->country.'">
					<span class="form_label">'.__("Zip / Postal", "bepro-listings").'</span><input type="text" name="postcode" value="'.@$item->postcode.'">';
					
			do_action("bepro_listing_form_after_location", @$post_data);		
			echo '	</div>';
			
		}		
		
		
		if(!empty($register) && !is_user_logged_in()){
			echo '
				<div class="add_listing_form_register bepro_form_section">
					<h3>'.__("Login / Register", "bepro-listings").'</h3>
					<span class="form_label">'.__("Username", "bepro-listings").'</span><input type="text" id="user_name" name="username">
					<span class="form_label">'.__("Password", "bepro-listings").'</span><input type="password" id="password" name="password">';
			do_action("bepro_listing_form_after_register", $post_data);			
			echo '	</div>';
		}
		do_action("bepro_listing_form_end", @$post_data);
		echo '<input type="submit" value="'.__((empty($post_data)?"Create Listing":"Update Listing"), "bepro-listings").'">					</div> <!-- end bpl_form_fields -->
				</form>';
?>