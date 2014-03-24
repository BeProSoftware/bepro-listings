<?php
/*
	This file is part of BePro Listings.

    BePro Listings is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    BePro Listings is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with BePro Listings.  If not, see <http://www.gnu.org/licenses/>.
*/	

	function bepro_admin_init(){
		$data = get_option("bepro_listings");
		add_meta_box("contact_general_meta", " ", "contact_general_meta", "bepro_listings", "normal", "low");
		if($data["show_cost"] == (1 || "on"))add_meta_box("cost_meta", "Cost $", "cost_meta", "bepro_listings", "side", "low");
		if($data["show_con"] == (1 || "on"))add_meta_box("contact_details_meta", "Lisiting Details", "contact_details_meta", "bepro_listings", "normal", "low");
		if($data["show_geo"] == (1 || "on"))add_meta_box("geographic_details_meta", "Geographic Details", "geographic_details_meta", "bepro_listings", "normal", "low");
	}
	
	function bepro_admin_head(){
		echo '<link type="text/css" rel="stylesheet" href="'.plugins_url('../css/jquery-ui-1.8.18.custom.css', __FILE__ ).'" >';
		echo "<style type='text/css'>.bepro_listings input[type=checkbox]{margin:11px 0;}</style>";
		echo "<style>
		  .ui-tabs-vertical { width: 55em; }
		  .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
		  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
		  .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
		  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
		  .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}
		  .bepro_listings_admin_form{display:none;}
		  </style>
		  ";
		echo '
			<script type="text/javascript">
				jQuery(document).ready(function(){
					if(jQuery("#bepro_listings_tabs")){
						jQuery( "#bepro_listings_tabs" ).tabs();
					}
					if(jQuery("#bepro_listings_addon_tabs")){
						jQuery( "#bepro_listings_addon_tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
						jQuery( "#bepro_listings_addon_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
						jQuery( ".admin_addon_form" ).submit(function(e){
							e.preventDefault();
							id = jQuery(this)[0].id;
							jQuery.post(ajaxurl, jQuery("#" + id).serialize(), function(rtn){
								if(rtn == 1){
									alert("Form saved!");
								}else{
									alert("!Form save error!");
								}								
							})
						});
					}
					jQuery(".bepro_listings_admin_form").css("display","block");
					jQuery(".bepro_listings_loading_msg").css("display","none");
				});
			</script>
		';
	}
	function cost_meta(){
	  global $wpdb, $post;
	  $listing = $wpdb->get_row("SELECT cost FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id =".$post->ID);
	  ?>
	  <span class="form_label">Cost:</span>
	  <input name="cost" value="<?php echo $listing->cost; ?>" />
	  <?php
	}
	 
	function contact_general_meta($post) {
		echo '<input type="hidden" name="save_bepro_listing" value="1">';
	}
	function contact_details_meta($post) {
	  global $wpdb;
	  $listing = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id =".$post->ID);
	  echo '
		<span class="form_label">First Name</span><input type="text" name="first_name" value="'.$listing->first_name.'"><br />
		<span class="form_label">Last Name</span><input type="text" name="last_name" value="'.$listing->last_name.'"><br />
		<span class="form_label">Phone</span><input type="text" name="phone" value="'.$listing->phone.'"><br />
		<span class="form_label">Email</span><input type="text" name="email" value="'.$listing->email.'"><br />
		<span class="form_label">Website</span><input type="text" name="website" value="'.$listing->website.'"><br />
	  ';
	}
	
	function geographic_details_meta($post) {
	  global $wpdb;
	  $listing = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id =".$post->ID);
	  
	  echo '
		<span class="form_label">Lat</span><input type="test" name="lat" value="'.$listing->lat.'"><br />
		<span class="form_label">Lon</span><input type="test" name="lon" value="'.$listing->lon.'"><br />
		<span class="form_label">Address</span><input type="text" name="address_line1" value="'.$listing->address_line1.'"><br />
		<span class="form_label">City</span><input type="text" name="city" value="'.$listing->city.'"><br />
		<span class="form_label">State</span><input type="text" name="state" value="'.$listing->state.'"><br />
		<span class="form_label">Country</span><input type="text" name="country" value="'.$listing->country.'"><br />
		<span class="form_label">postcode</span><input type="text" name="postcode" value="'.$listing->postcode.'"><br />
	  ';
	}
	
	//Save Bepro Listing
	function bepro_admin_save_details($post_id){
		global $wpdb;
		if (!isset($_POST['save_bepro_listing'])) return; 
		if ($parent_id = wp_is_post_revision($post_id)) 
			$post_id = $parent_id;
	  
		$post_type = get_post_type( $post_id);
		if($post_type != "bepro_listings")return;
		//get lat/lon
		bepro_listings_save($post_id);
	}

	//Admin Bepro Listings table columns
	function bepro_listings_edit_columns($columns){
		$data = get_option("bepro_listings");
		$columns = array(
			"cb" => "<input type='checkbox' />",
			"title" => "Item Name",
			"description" => "Description");
		if($data["show_geo"]) $columns["lat_lon"] =  "Lat/Lon?";	
		if($data["show_cost"]) $columns["cost"] =   "Cost";	
		$columns["listing_types"] = "Listing Types";
		$columns["date"] =  "Date";
	 
	  return $columns;
	}
	
	//Admin Bepro Listing table data
	function bepro_listings_custom_columns($column){
	  global $post;
	 
	  switch ($column) {
		case "description":
		  the_excerpt();
		  break;
		case "lat_lon":
		  global $wpdb;
		  $custom = $wpdb->get_row("SELECT lat, lon FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id =".$post->ID);
		  echo (!empty($custom->lat) && !empty($custom->lon))? "Yes": "No";
		  break;
		case "cost":
		  global $wpdb;
		  $custom = $wpdb->get_row("SELECT cost FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id =".$post->ID);
		  echo $custom->cost;
		  break;
		case "listing_types":
		  echo get_the_term_list($post->ID, 'bepro_listing_types', '', ', ','');
		  break;
	  }
	}
	
		
	/**
	 * Category thumbnail fields.
	 *
	 * @access public
	 * @return void
	 */
	function bepro_listings_add_category_thumbnail_field() {
		?>
		<div class="form-field">
			<label><?php _e('Thumbnail', 'bepro_listings'); ?></label>
			<div id="bepro_listing_types_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo bepro_listings_placeholder_img_src(); ?>" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="bepro_listing_types_thumbnail_id" name="bepro_listing_types_thumbnail_id" />
				<button type="submit" class="upload_image_button button"><?php _e('Upload/Add image', 'bepro_listings'); ?></button>
				<button type="submit" class="remove_image_button button"><?php _e('Remove image', 'bepro_listings'); ?></button>
			</div>
			<script type="text/javascript">
				
				 // Only show the "remove image" button when needed
				 if ( ! jQuery('#bepro_listing_types_thumbnail_id').val() )
					 jQuery('.remove_image_button').hide();

				window.send_to_editor_default = window.send_to_editor;

				window.send_to_termmeta = function(html) {

					jQuery('body').append('<div id="temp_image">' + html + '</div>');

					var img = jQuery('#temp_image').find('img');

					imgurl 		= img.attr('src');
					imgclass 	= img.attr('class');
					imgid		= parseInt(imgclass.replace(/\D/g, ''), 10);

					jQuery('#bepro_listing_types_thumbnail_id').val(imgid);
					jQuery('#bepro_listing_types_thumbnail img').attr('src', imgurl);
					jQuery('.remove_image_button').show();
					jQuery('#temp_image').remove();

					tb_remove();

					window.send_to_editor = window.send_to_editor_default;
				}

				jQuery('.upload_image_button').live('click', function(){
					var post_id = 0;

					window.send_to_editor = window.send_to_termmeta;

					tb_show('', 'media-upload.php?post_id=' + post_id + '&amp;type=image&amp;TB_iframe=true');
					return false;
				});

				jQuery('.remove_image_button').live('click', function(){
					jQuery('#bepro_listing_types_thumbnail img').attr('src', '<?php echo bepro_listings_placeholder_img_src(); ?>');
					jQuery('#bepro_listing_types_thumbnail_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}


	/**
	 * Edit category thumbnail field.
	 *
	 * @access public
	 * @param mixed $term Term (category) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 * @return void
	 */
	function bepro_listings_edit_category_thumbnail_field( $term, $taxonomy ) {

		$image 			= '';
		$thumbnail_id 	= get_bepro_listings_term_meta( $term->term_id, 'thumbnail_id', true );
		if ($thumbnail_id) :
			$image = wp_get_attachment_url( $thumbnail_id );
		else :
			$image = bepro_listings_placeholder_img_src();
		endif;
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e('Thumbnail', 'bepro_listings'); ?></label></th>
			<td>
				<div id="bepro_listing_types_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="bepro_listing_types_thumbnail_id" name="bepro_listing_types_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="submit" class="upload_image_button button"><?php _e('Upload/Add image', 'bepro_listings'); ?></button>
					<button type="submit" class="remove_image_button button"><?php _e('Remove image', 'bepro_listings'); ?></button>
				</div>
				<script type="text/javascript">

					window.send_to_termmeta = function(html) {

						jQuery('body').append('<div id="temp_image">' + html + '</div>');

						var img = jQuery('#temp_image').find('img');

						imgurl 		= img.attr('src');
						imgclass 	= img.attr('class');
						imgid		= parseInt(imgclass.replace(/\D/g, ''), 10);

						jQuery('#bepro_listing_types_thumbnail_id').val(imgid);
						jQuery('#bepro_listing_types_thumbnail img').attr('src', imgurl);
						jQuery('#temp_image').remove();

						tb_remove();
					}

					jQuery('.upload_image_button').live('click', function(){
						var post_id = 0;

						window.send_to_editor = window.send_to_termmeta;

						tb_show('', 'media-upload.php?post_id=' + post_id + '&amp;type=image&amp;TB_iframe=true');
						return false;
					});

					jQuery('.remove_image_button').live('click', function(){
						jQuery('#bepro_listing_types_thumbnail img').attr('src', '<?php echo bepro_listings_placeholder_img_src(); ?>');
						jQuery('#bepro_listing_types_thumbnail_id').val('');
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}



	/**
	 * bepro_listings_category_thumbnail_field_save function.
	 *
	 * @access public
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	function bepro_listings_category_thumbnail_field_save( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['bepro_listing_types_thumbnail_id'] ) )
			update_bepro_listings_term_meta( $term_id, 'thumbnail_id', $_POST['bepro_listing_types_thumbnail_id'] );
	}

	
	function bepro_edit_listing_types_column($theme_columns){
	$new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => __('Name'),
        'thumb' => '',
		'description' => __('Description'),
        'slug' => __('Slug'),
        'posts' => __('Posts')
        );
		return $new_columns;
	}

	function bepro_listing_types_column( $columns, $column, $id ) {

		if ( $column == 'thumb' ) {

			$image 			= '';
			$thumbnail_id 	= get_bepro_listings_term_meta( $id, 'thumbnail_id', true );

			if ($thumbnail_id)
				$image = wp_get_attachment_url( $thumbnail_id );
			else
				$image = bepro_listings_placeholder_img_src();

			$columns .= '<img src="' . $image . '" alt="Thumbnail" class="wp-post-image" height="48" width="48" />';

		}

		return $columns;
	}

	
	//Admin css and javascript
	function bepro_listings_adminhead() {
			wp_admin_css('thickbox');
			wp_print_scripts('editor');
			wp_print_scripts('media-upload');
			wp_print_scripts('jquery-ui-tabs');
			if(function_exists('wp_tiny_mce')) wp_tiny_mce();
					do_action('admin_print_styles');
		?><style type="text/css">
		.form_label {
			clear: left;
			display: block;
			float: left;
			margin: 5px 0;
			width: 155px;
		}
		</style>
	<script type="text/javascript">
	// Deals with calling the WordPress Media popup box
	function myMediaPopupHandler(version)
	{
		window.send_to_editor = function(html) {
			imgurl = jQuery('img',html).attr('src');
			jQuery('#upload_image' + version).val(imgurl);
			tb_remove();
		}

		formfield = jQuery('#upload_image' + version).attr('name');
		tb_show('', '<?php echo admin_url(); ?>media-upload.php?type=image&tab=library&TB_iframe=true');
		return false;
	}
	</script>
		<?php
	}

	//Options Page
	function bepro_listings_options() {
		$data = get_option("bepro_listings");
		if(!empty($_POST["update_options"])){
			//general
			$data["show_cost"] = $_POST["show_cost"];
			$data["show_con"] = $_POST["show_con"];
			$data["show_geo"] = $_POST["show_geo"];
			$data["num_images"] = $_POST["num_images"];
	
			//forms
			$data["validate_form"] = $_POST["validate_form"];
			$data["default_user_id"] = $_POST["default_user_id"];
			$data["default_status"] = $_POST["default_status"];
			$data["success_message"] = $_POST["success_message"];			
			$data["fail_message"] = $_POST["fail_message"];		
			
			//check cat exclude
			$data["bepro_listings_cat_exclude"] = (bl_check_is_valid_cat($_POST["bepro_listings_cat_exclude"]))? $_POST["bepro_listings_cat_exclude"]:"";
			
			//check cat required
			$data["bepro_listings_cat_required"] = (bl_check_is_valid_cat($_POST["bepro_listings_cat_required"]))?$_POST["bepro_listings_cat_required"]:"";
			
			//search listings
			$data["default_image"] = $_POST["default_image"];
			$data["link_new_page"] = $_POST["link_new_page"];
			$data["ajax_on"] = $_POST["ajax_on"];
			$data["num_listings"] = $_POST["num_listings"];
			$data["distance"] = $_POST["distance"];
			$data["details_link"] = $_POST["details_link"];
			$data["show_web_link"] = $_POST["show_web_link"];
			
			//Page/post
			$data["gallery_size"] = $_POST["gallery_size"];
			$data["gallery_cols"] = $_POST["gallery_cols"];
			$data["show_details"] = $_POST["show_details"];
			$data["add_detail_links"] = $_POST["add_detail_links"];
			$data["show_content"] = $_POST["show_content"];
			
			//map
			$data["map_query_type"] = $_POST["map_query_type"];
			
			//buddypress
			$data["buddypress"] = $_POST["buddypress"];
			
			//Support
			$data["footer_link"] = $_POST["footer_link"];
			
			update_option("bepro_listings", $data);
		}
		
		
		?>
		<h1>BePro Listings Options</h1>
		<div class="wrap bepro_listings_admin_form">
			<form class="bepro_listings" method="post">
				<input type="hidden" name="update_options" value="1" />
				<div id="bepro_listings_tabs">
					<ul>
						<li><a href="#tabs-1">General</a></li>
						<li><a href="#tabs-2">Forms</a></li>
						<li><a href="#tabs-3">Search/Listings</a></li>
						<li><a href="#tabs-4">Page/Post</a></li>
						<li><a href="#tabs-5">Map</a></li>
						<li><a href="#tabs-6">Buddypress</a></li>
						<li><a href="#tabs-7">Support</a></li>
					</ul>
				
					<div id="tabs-1">
						<span class="form_label"><?php _e("Show Cost", "bepro-listings"); ?></span><input type="checkbox" name="show_cost" <?php echo ($data["show_cost"]== (1 || "on"))? 'checked="checked"':"" ?>><br />
						<span class="form_label"><?php _e("Show Contact", "bepro-listings"); ?></span><input type="checkbox" name="show_con" <?php echo ($data["show_con"]== (1 || "on"))? 'checked="checked"':"" ?>><br />
						<span class="form_label"><?php _e("Show Geography", "bepro-listings"); ?></span><input type="checkbox" name="show_geo" <?php echo ($data["show_geo"]== (1 || "on"))? 'checked="checked"':"" ?>><br />
						<span class="form_label"><?php _e("# Of Uploads", "bepro-listings"); ?></span><select name="num_images"><br />
							<option value="1" <?php echo ($data["num_images"]== 1)? 'selected="selected"':"" ?>>1</option>
							<option value="2" <?php echo ($data["num_images"]== 2)? 'selected="selected"':"" ?>>2</option>
							<option value="3" <?php echo ($data["num_images"]== 3)? 'selected="selected"':"" ?>>3</option>
							<option value="4" <?php echo ($data["num_images"]== 4)? 'selected="selected"':"" ?>>4</option>
							<option value="5" <?php echo ($data["num_images"]== 5)? 'selected="selected"':"" ?>>5</option>
							<option value="6" <?php echo ($data["num_images"]== 6)? 'selected="selected"':"" ?>>6</option>
							<option value="7" <?php echo ($data["num_images"]== 7)? 'selected="selected"':"" ?>>7</option>
							<option value="8" <?php echo ($data["num_images"]== 8)? 'selected="selected"':"" ?>>8</option>
							<option value="9" <?php echo ($data["num_images"]== 9)? 'selected="selected"':"" ?>>9</option>
							<option value="0" <?php echo ($data["num_images"]== 0)? 'selected="selected"':"" ?>>None</option>
						</select>
					</div>
					<div id="tabs-2">
						<span class="form_label"><?php _e("Validate Form", "bepro-listings"); ?></span><input type="checkbox" name="validate_form" <?php echo ($data["validate_form"]== (1 || "on"))? 'checked="checked"':"" ?>><br />
						<span class="form_label"><?php _e("Default User Id", "bepro-listings"); ?></span><input type="text" name="default_user_id" value="<?php echo $data["default_user_id"]; ?>"><br />
						<span class="form_label"><?php _e("Default Status", "bepro-listings"); ?></span><select name="default_status">
							<option value="pending" <?php echo ($data["default_status"]== "pending")? 'selected="selected"':"" ?>>Pending</option>
							<option value="publish" <?php echo ($data["default_status"]== "publish")? 'selected="selected"':"" ?>>Published</option>
						</select></br>
						<span class="form_label"><?php _e("Success Message", "bepro-listings"); ?></span><textarea name="success_message"><?php echo $data["success_message"]; ?></textarea><br />
						<span class="form_label"><?php _e("Fail Message", "bepro-listings"); ?></span><textarea name="fail_message"><?php echo $data["fail_message"]; ?></textarea><br />
						<span class="form_label"><?php _e("Required Categories", "bepro-listings"); ?></span><input type="text" name="bepro_listings_cat_required" value="<?php echo $data["bepro_listings_cat_required"]; ?>" /></br>
						<span class="form_label"><?php _e("Exclude Categories", "bepro-listings"); ?></span><input type="text" name="bepro_listings_cat_exclude" value="<?php echo $data["bepro_listings_cat_exclude"]; ?>" /></br>
					</div>
					<div id="tabs-3">
						<span class="form_label"><?php _e("Default Listing Image", "bepro-listings"); ?></span><input type="text" name="default_image" value="<?php echo $data["default_image"]; ?>" /></br>
						<span class="form_label"><?php _e("How Links Open", "bepro-listings"); ?></span><select name="link_new_page">
							<option value="1" <?php echo ($data["link_new_page"] == 1)? 'selected="selected"':"" ?>>Go To Page</option>
							<option value="2" <?php echo ($data["link_new_page"] == 2)? 'selected="selected"':"" ?>>New Tab</option>
							<option value="3" <?php echo ($data["link_new_page"] == 3)? 'selected="selected"':"" ?>>Ajax Page</option>
						</select><br />
						<span class="form_label"><?php _e("Ajax On?", "bepro-listings"); ?></span><input type="checkbox" name="ajax_on" <?php echo ($data["ajax_on"]== (1 || "on"))? 'checked="checked"':"" ?>><br />
						<span class="form_label"><?php _e("Default # Listings", "bepro-listings"); ?></span><select name="num_listings">
							<option value="1" <?php echo ($data["num_listings"]== 1)? 'selected="selected"':"" ?>>1</option>
							<option value="3" <?php echo ($data["num_listings"]== 3)? 'selected="selected"':"" ?>>3</option>
							<option value="5" <?php echo ($data["num_listings"]== 5)? 'selected="selected"':"" ?>>5</option>
							<option value="8" <?php echo ($data["num_listings"]== 8)? 'selected="selected"':"" ?>>8</option>
							<option value="10" <?php echo ($data["num_listings"]== 10)? 'selected="selected"':"" ?>>10</option>
							<option value="12" <?php echo ($data["num_listings"]== 12)? 'selected="selected"':"" ?>>12</option>
							<option value="16" <?php echo ($data["num_listings"]== 16)? 'selected="selected"':"" ?>>16</option>
							<option value="20" <?php echo ($data["num_listings"]== 20)? 'selected="selected"':"" ?>>20</option>
							<option value="50" <?php echo ($data["num_listings"]== 50)? 'selected="selected"':"" ?>>50</option>
						</select></br>
						<span class="form_label"><?php _e("Default Search Distance (Mi)", "bepro-listings"); ?></span><select name="distance">
							<option value="10" <?php echo ($data["distance"]== 10)? 'selected="selected"':"" ?>>10</option>
							<option value="50" <?php echo ($data["distance"]== 50)? 'selected="selected"':"" ?>>50</option>
							<option value="150" <?php echo ($data["distance"]== 150)? 'selected="selected"':"" ?>>150</option>
							<option value="250" <?php echo ($data["distance"]== 250)? 'selected="selected"':"" ?>>250</option>
							<option value="500" <?php echo ($data["distance"]== 500)? 'selected="selected"':"" ?>>500</option>
							<option value="1000" <?php echo ($data["distance"]== 1000)? 'selected="selected"':"" ?>>1000</option>
						</select>
						<span style="clear:both;display: block;"><br /></span>
						<span class="form_label"><?php _e("Details Link Text", "bepro-listings"); ?></span><input type="text" name="details_link" value="<?php echo $data["details_link"]; ?>" /></br>
						<span class="form_label"><?php _e("Show Website Link?", "bepro-listings"); ?></span><input type="checkbox" name="show_web_link" <?php echo ($data["show_web_link"]== (1 || "on"))? 'checked="checked"':"" ?>><br />
						<span style="clear:both;display: block;"><br /></span>
					</div>
					<div id="tabs-4">
						<span class="form_label"><?php _e("Gallery Size", "bepro-listings"); ?></span><select name="gallery_size">
							<option value="thumbnail" <?php echo ($data["gallery_size"]== "thumbnail")? 'selected="selected"':"" ?>>thumbnail</option>
							<option value="medium" <?php echo ($data["gallery_size"]== "medium")? 'selected="selected"':"" ?>>medium</option>
							<option value="large" <?php echo ($data["gallery_size"]== "large")? 'selected="selected"':"" ?>>large</option>
							<option value="full" <?php echo ($data["gallery_size"]== "full")? 'selected="selected"':"" ?>>full</option>
						</select><br />
						<span class="form_label"><?php _e("#Gallery Columns", "bepro-listings"); ?></span><select name="gallery_cols">
							<option value="3" <?php echo ($data["gallery_cols"]== 3)? 'selected="selected"':"" ?>>3</option>
							<option value="5" <?php echo ($data["gallery_cols"]== 5)? 'selected="selected"':"" ?>>5</option>
							<option value="8" <?php echo ($data["gallery_cols"]== 8)? 'selected="selected"':"" ?>>8</option>
						</select><br />
						<span class="form_label"><?php _e("Show Details", "bepro-listings"); ?></span><input type="checkbox" name="show_details" <?php echo ($data["show_details"]== (1 || "on"))? 'checked="checked"':"" ?>><br />
						<span class="form_label"><?php _e("Links on Details", "bepro-listings"); ?></span><input type="checkbox" name="add_detail_links" <?php echo ($data["add_detail_links"]== (1 || "on"))? 'checked="checked"':"" ?>><br />
						<span class="form_label"><?php _e("Show Content", "bepro-listings"); ?></span><input type="checkbox" name="show_content" <?php echo ($data["show_content"]== (1 || "on"))? 'checked="checked"':"" ?>>
					</div>
					<div id="tabs-5">
						<span class="form_label"><?php _e("Query Type", "bepro-listings"); ?></span><select name="map_query_type">
							<option value="curl" <?php echo ($data["map_query_type"] == "curl")? 'selected="selected"':""; ?>>Curl</option>
							<option value="file_get_contents" <?php echo ($data["map_query_type"] == "file_get_contents")? 'selected="selected"':""; ?>>file_get_contents</option>
						</select>	
					</div>
					<div id="tabs-6">
						<span class="form_label"><?php _e("Buddypress", "bepro-listings"); ?></span><input type="checkbox" name="buddypress" <?php echo ($data["buddypress"]== (1 || "on"))? 'checked="checked"':"" ?>>
					</div>
					<div id="tabs-7">
						<a href="http://beprosoftware.com"><img src="<?php echo BEPRO_LISTINGS_PLUGIN_PATH."/images/bepro_software_logo.png"; ?>"></a><br />
						<iframe width="560" height="315" src="//www.youtube.com/embed/D5YpZX0go88" frameborder="0" allowfullscreen></iframe>
						<p><b>THANK YOU</b> for your interest and support of this plugin. Our BePro Software Team is dedicated to providing you with the tools needed for great websites. You can get involved in any of the following ways:</p>
						<h2>Support For You</h2>
						<ul style="border:1px solid; padding:10px">
							<li>Support Forum - Join our <a href="http://beprosoftware.com/forums" target="_blank">forums</a> for any support questions</li>
							<li>Documentation - Instructions, videos, shortcodes, and more on <a href="http://beprosoftware.com/products/bepro-listings/">this dedicated page</a></li>
							<li>Contact Us - Send us an email via our <a href="http://www.beprosoftware.com/contact-us/">Contact Us</a> Page</li>
							<li>Upgrades - View our list of <a href="http://www.beprosoftware.com/products/" target="_blank">wordpress plugins and themes</a></li>
							<li>Services - We offer <a href="http://www.beprosoftware.com/services/" target="_blank">Various services</a> including support starting at $15/hr</li>
							<li>Social - Our <a href="https://twitter.com/BeProSoftware" target="_blank">Twitter Page</a></li>
							<li>Youtube - Our <a href="http://www.youtube.com/playlist?list=PLMzIqO2N1YpjMx4QfiCjwFsxxfHVy1goG" target="_blank">Youtube Playlist</a> managed by <a href="http://www.adcoordinates.com" target="_blank">Ad Coordinates</a></li>
						</ul>
						<h2>Support For The Plugin</h2>
						<ul style="border:1px solid; padding:10px">		
							<li><span style="color:green;font-weight:bold;font-size:18px;text-decoration:underline">Rate Us</span></a> - Give this plugin a <span style="color:green;font-weight:bold;font-size:18px;text-decoration:underline">rating</span> on <a href="http://wordpress.org/support/view/plugin-reviews/bepro-listings" target="_blank">Wordpress.org</a>. This is the best way to support the plugin and encourage its growth</li>
							<li>Donations - We accept donations of any amount <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=support@beprosoftware.com&item_name=Donation+for+BePro+Listings">via paypal</a></li>
							<li><?php _e("Our Link in your footer?", "bepro-listings"); ?> - <input style="vertical-align:middle" type="checkbox" name="footer_link" value="1" <?php echo ($data["footer_link"]== ("on" || 1))? 'checked="checked"':"" ?>></li>
						</ul>
						<p>NOTE: We can help with customizing this plugin to fit any of your needs. Simply <a href="http://www.beprosoftware.com/contact-us/" target="_blank">reach out</a>. You will receive a personalized responce from a senior member of our <b>BePro Software Team</b></p>
						<hr />
						<p><b>BePro Software&trade;</b> is a service owned and operated by <a href="http://beyondprograms.ca" target="_blank">Beyond Programs Ltd.</a> a Canadian company.</p>
					</div>
				</div>
				<span style="clear:both;display: block;"><br /></span>
				<input type="submit" name="submit" value="Update BePro Listings Options &raquo" />
			</form>
		</div>
		<div class="bepro_listings_loading_msg">
		<p>Jquery is loading... Please wait</p>
		</div>	
			<?php	
	}
	
	function bepro_listings_addons(){
			ob_start();
			do_action('bepro_listings_admin_tabs');
			$tabs = trim( ob_get_clean() );
			?>
		<h1>Add-On Options</h1>
		<div class="wrap bepro_listings_admin_form">
			<div id="bepro_listings_addon_tabs">
				<ul class="tabs">
					<?php echo $tabs; ?>
				</ul>
				<?php do_action('bepro_listings_admin_tab_panels'); ?>
			</div>	
		</div>
		<div class="bepro_listings_loading_msg">
		<p>Jquery is loading... Please wait</p>
		</div>
		<?php
	}
?>
