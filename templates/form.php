<?php
	
		if(!empty($validate) && ($validate == "on")){
			echo '
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
								item_name: "Please give this a name",
								content: "Please tell us about this",
								first_name: "Please enter your firstname",
								last_name: "Please enter your lastname",
								password: {
									required: "Please provide a password",
									minlength: "Your password must be at least 5 characters long"
								},
								email: "Please enter a valid email address",
								country: "Where in the world is this?",
								agree: "Please accept our policy"
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
			<form method="post" enctype="multipart/form-data" id="bepro_create_listings_form">
				<input type="hidden" name="save_bepro_listing" value="1">
				<input type="hidden" name="bepro_post_id" value="'.$post_data->ID.'">';
		do_action("bepro_listing_form_start", $post_data);		
		echo '
			<div class="add_listing_form_info bepro_form_section">
				<h3>'.__("Item Information", "bepro-listings").'</h3>
				<span class="form_heading">'.__("Item Name", "bepro-listings").'</span><input type="" id="item_name" name="item_name" value="'.$post_data->post_title.'" '.(isset($post_data->post_title)?'readonly="readonly"':"").'><br />
				<span class="form_heading">'.__("Description", "bepro-listings").'</span><textarea name="content" id="content">'.$post_data->post_content.'</textarea>';
				
				//show categories
				$data = get_option("bepro_listings");
				$exclude = explode(",",$data["bepro_listings_cat_exclude"]);
				$required = explode(",",$data["bepro_listings_cat_required"]);
				$page_id = get_the_ID();
				//show categories
				$cats = get_terms( array('bepro_listing_types'), array("hide_empty" => false));
				$required_list = "";
				$normal_list = "";
				if($cats){
					foreach($cats as $cat){
						if(!empty($exclude) && in_array($cat->term_id, array_values($exclude))){
						
						}elseif(!empty($required) && in_array($cat->term_id, array_values($required))){
							$required_list .= '<span class="bepro_form_cat"><span class="form_label">'.$cat->name.'</span><input type="checkbox" id="categories" name="categories[]" value="'.$cat->term_id.'" checked="checked" disabled="disabled"></span>';
						}else{
							$normal_list .= '<span class="bepro_form_cat"><span class="form_label">'.$cat->name.'</span><input type="checkbox" id="categories" name="categories[]" value="'.$cat->term_id.'"></span>';
						}
					}
					echo $cat_section = "<div class='bepro_listing_category_section'><h3>Categories : </h3><p><strong>Pre Selected</strong></p>".(empty($required_list)?"None":$required_list)."<div style='clear:both'><br /></div><p><strong>Optional</strong></p>".$normal_list."</div>";
				}
				echo "<div style='clear:both'></div>";
				
		do_action("bepro_listing_form_in_item_before_images", $post_data);		
			
				if(!empty($num_images) && ($num_images > 0)){
					$counter = 1;
					echo "<span class='bepro_form_images'><span class='form_heading'>Files (".apply_filters("bepro_listings_upload_file_heading","imgs").")</span>";
					while($counter <= $num_images){
						$filename = $thunmbnails[$counter-1][5];
						echo '<span class="form_label">'.(empty($filename)? __("File ".$counter, "bepro-listings"):$filename).'</span>';
						if(isset($thunmbnails[$counter-1]) && !stristr($thunmbnails[$counter-1][0], "no_img.jpg")){
							echo "<img src='".$thunmbnails[$counter-1][0]."'><br /><span>Delete?</span><input type='checkbox' name='delete_image_".($counter-1)."' value='".$thunmbnails[$counter-1][4]."'><br />";
						}
						echo '<input type="file" name="bepro_form_image_'.$counter.'"><br />';
						$counter++;
					}
					echo "</span>";
				}
		do_action("bepro_listings_form_after_item", $post_data);		
		echo '		
			</div>
			';		
		
		
		if(!empty($show_cost) && ($show_cost == "on")){		
			echo '
			<div class="add_listing_form_cost bepro_form_section">
				<span class="form_label">'.__("Cost", "bepro-listings").'</span><input type="text" name="cost" value="'.(isset($item->cost)? $item->cost:0).'"><br />';
			
			do_action("bepro_listing_form_after_cost", $post_data);
			echo '</div>';
			
		}
		
		if(!empty($show_con) && ($show_con == "on")){		
			echo '		
				<div class="add_listing_form_contact bepro_form_section">
					<h3>'.__("Contact Information", "bepro-listings").'</h3>
					<span class="form_label">'.__("First Name", "bepro-listings").'</span><input type="text" id="first_name" name="first_name" value="'.$item->first_name.'">
					<span class="form_label">'.__("Last Name", "bepro-listings").'</span><input type="text" id="last_name" name="last_name" value="'.$item->last_name.'">
					<span class="form_label">'.__("Email", "bepro-listings").'</span><input type="text" id="email" name="email" value="'.$item->email.'">
					<span class="form_label">'.__("Phone", "bepro-listings").'</span><input type="text" name="phone" id="phone" value="'.$item->phone.'">
					<span class="form_label">'.__("Website", "bepro-listings").'</span><input type="text" name="website" value="'.$item->website.'">';
			
			do_action("bepro_listing_form_after_contact", $post_data);		
			echo '	</div>';
				
		}
		
		
		if(!empty($show_geo) && ($show_geo == "on")){
			echo '
				<div class="add_listing_form_geo bepro_form_section">
					<h3>'.__("Location Information", "bepro-listings").'</h3>
					<span class="form_label">'.__("Address", "bepro-listings").'</span><input type="text" name="address_line1" value="'.$item->address_line1.'">
					<span class="form_label">'.__("City", "bepro-listings").'</span><input type="text" name="city" value="'.$item->city.'">
					<span class="form_label">'.__("State", "bepro-listings").'</span><input type="text" name="state" value="'.$item->state.'">
					<span class="form_label">'.__("Country", "bepro-listings").'</span><input type="text" id="country" name="country" value="'.$item->country.'">
					<span class="form_label">'.__("Zip / Postal", "bepro-listings").'</span><input type="text" name="postal" value="'.$item->postal.'">';
					
			do_action("bepro_listing_form_after_location", $post_data);		
			echo '	</div>';
			
		}		
		
		
		if(!empty($register) && !is_user_logged_in()){
			echo '
				<div class="add_listing_form_register bepro_form_section">
					<h3>'.__("Login / Register", "bepro-listings").'</h3>
					<span class="form_label">'.__("Username", "bepro-listings").'</span><input type="text" id="user_name" name="user_name">
					<span class="form_label">'.__("Password", "bepro-listings").'</span><input type="text" id="password" name="password">';
			do_action("bepro_listing_form_after_register", $post_data);			
			echo '	</div>';
		}
		do_action("bepro_listing_form_end", $post_data);
		echo '<input type="submit" value="'.__((empty($post_data)?"Create Listing":"Update Listing"), "bepro-listings").'">
				</form>';
?>