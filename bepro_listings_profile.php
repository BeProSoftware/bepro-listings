<?php

	function bl_my_listings(){
		global $wpdb, $post;
		$current_url =  get_permalink( $post->ID );
		$return_text = "";
		if(!is_user_logged_in()){
			$return_text .= "<p>".__("You need to be Logged In to see your Listings.", "bepro-listings")."</p>";
			$args = array(
					'echo'           => true,
					'redirect'       => $current_url, 
					'form_id'        => 'loginform',
					'label_username' => __( 'Username' ),
					'label_password' => __( 'Password' ),
					'label_remember' => __( 'Remember Me' ),
					'label_log_in'   => __( 'Log In' ),
					'id_username'    => 'user_login',
					'id_password'    => 'user_pass',
					'id_remember'    => 'rememberme',
					'id_submit'      => 'wp-submit',
					'remember'       => true,
					'value_username' => NULL,
					'value_remember' => false
			);
			wp_login_form($args);
			return;
		}
		
		$types = listing_types();
		$current_user = wp_get_current_user();
		$user_id = $current_user->id;
		
		if(isset($_POST["save_bepro_listing"]) && !empty($_POST["save_bepro_listing"])){
			$success = false;
			$success = bepro_listings_save();
			if($success){
				$success_message = apply_filters("bepro_form_success_message","Listing Successfully Saved");
				$message =  "<span class='bl_succsss_message'>".__($success_message,"bepro-listings")."</span>";
			}else{
				$fail_message = apply_filters("bepro_form_fail_message",__("Issue saving your listing. Please contact the website administrator","bepro-listings"));
				$message =  "<span class='bl_fail_message'>".__($fail_message,"bepro-listings")."</span>";
			}
			
			$current_user = wp_get_current_user();
			$current_url = get_permalink( $post->ID );
			echo "<span class='classified_message'>".$message."</span>";
		}
		
		if(!empty($_GET["bl_manage"])){
			if(!empty($_GET["bl_id"])){
				bl_profile_update_listing_content();
			}else{
				bl_profile_add_listing_content();
			}
		}else{
			$data = get_option("bepro_listings");
			// get records
			if(@$data["require_payment"]){
				$items = $wpdb->get_results("SELECT geo.*, orders.status as order_status, orders.expires, wp_posts.post_title, wp_posts.post_status FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as geo 
				LEFT JOIN ".$wpdb->prefix."posts as wp_posts on wp_posts.ID = geo.post_id 
				LEFT JOIN ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." AS orders on orders.bl_order_id = geo.bl_order_id WHERE wp_posts.post_status != 'trash' AND wp_posts.post_author = ".$user_id);
			}else{
				$items = $wpdb->get_results("SELECT geo.*, wp_posts.post_title, wp_posts.post_status FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as geo 
				LEFT JOIN ".$wpdb->prefix."posts as wp_posts on wp_posts.ID = geo.post_id WHERE wp_posts.post_status != 'trash' AND wp_posts.post_author = ".$user_id);
			}
			
			$listing_url = "?bl_manage=1&bl_id=";
			$add_listing_button = "<p><a href='".$listing_url."'>".__("Add a Listing")."</a></p>";
			
			//allow addons to override create listing button
			$return_text .= apply_filters("bl_change_add_listing_button", $add_listing_button, $listing_url);
			
			//allow addons to change profile template
			$bl_my_list_template = apply_filters("bl_change_my_list_template",dirname( __FILE__ ) . '/templates/list.php', $items);
			
			ob_start(); 
			if(!empty($bl_my_list_template))
				include( $bl_my_list_template );
			$return_text .= ob_get_clean(); 
			return $return_text;
		}
	}
	
	function bl_profile_update_listing_content(){
		global $wpdb, $post;
		$bl_id = is_numeric($_GET["bl_id"])? $_GET["bl_id"]:"0";
		$data = get_option("bepro_listings");
		//get information 
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE id = ".$bl_id);
		
		if(!$item) return;
		
		$post_data = get_post($item->post_id);
		//get categories
		$raw_categories = listing_types_by_post($item->post_id);
		$categories = array();
		if($raw_categories){
			foreach($raw_categories as $category) {
				$categories[$category->term_id] = $category->term_id;
			}
		}
		$args = array(
			'numberposts' => -1,
			'post_parent' => $item->post_id,
			'post_type' => 'attachment'
		);  
		//get images
		$attachments = get_posts($args);
		$thunmbnails = array();
		$file_icons = array("application/pdf" => "document.png", "text/plain" => "text.png", "text/csv" => "spreadsheet.png","application/rar" => "archive.png", "application/x-tar" => "archive.png", "application/zip" => "archive.png", "application/x-gzip" => "archive.png","application/x-7z-compressed" => "archive.png","application/msword" => "document.png","application/vnd.oasis.opendocument.text" => "document.png","application/vnd.oasis.opendocument.presentation" => "text.png","application/vnd.oasis.opendocument.spreadsheet" => "interactive.png","application/vnd.oasis.opendocument.graphics" => "interactive.png", "application/vnd.oasis.opendocument.chart" => "spreadsheet.png","application/wordperfect" => "document.png", "video/x-ms-asf" => "video.png", "video/x-ms-wmv" => "video.png",  "video/x-ms-wmx" => "video.png", "video/x-ms-wm" => "video.png", "video/avi" => "video.png", "video/divx" => "video.png","video/x-flv" => "video.png", "video/quicktime" => "video.png", "video/mpeg" => "video.png", "video/mp4" => "video.png", "video/ogg" => "video.png", "video/webm" => "video.png", "video/x-matroska" => "video.png");
		if($attachments){  
			foreach ($attachments as $attachment) {
				$image = wp_get_attachment_image_src($attachment->ID,'thumbnail', false);
				if(!$image){
					$p_type = get_post_mime_type($attachment->ID);
					$f_type = empty($file_icons[$p_type])? "text.png":$file_icons[$p_type];
					$image[0] = get_bloginfo("wpurl")."/wp-includes/images/crystal/".$f_type;
				}
				$image[4] =  $attachment->ID;
				$image[5] = basename ( get_attached_file( $attachment->ID ) );
				$thunmbnails[] = $image;
			}
		}

		//get settings
		$data = get_option("bepro_listings");
		$default_user_id = $data["default_user_id"];
		$num_images = $data["num_images"];
		$validate = $data["validate_form"];
		$show_cost = $data["show_cost"];
		$show_con = $data["show_con"];
		$show_geo = $data["show_geo"];
		$cat_drop = $data["cat_drop"];
		
		$listing_url = "?bl_manage=1&bl_id=";
		$url = get_permalink( $post->ID );
		if(!empty($_POST["save_bepro_listing"])){
			echo "<p><a href='".$url."'>".__("Return to List","bepro-listings")."</a></p>";
		}else{
			echo "<p><a href='".$url."'>".__("Cancel","bepro-listings")."</a></p>";
		}
		
		$frontend_form = dirname( __FILE__ )."/templates/form.php";
		$frontend_form = apply_filters("bl_change_upload_form", $frontend_form);
		if($frontend_form)
		require( $frontend_form);
	}
	
	function bl_profile_add_listing_content(){
		global $post;
		//get settings
		$data = get_option("bepro_listings");
		$default_user_id = $data["default_user_id"];
		$num_images = $data["num_images"];
		$validate = $data["validate_form"];
		$show_cost = $data["show_cost"];
		$show_con = $data["show_con"];
		$show_geo = $data["show_geo"];
		
		$listing_url = "?bl_manage=1&bl_id=";
		$url = get_permalink( $post->ID );
		if(!empty($_POST["save_bepro_listing"])){
			echo "<p><a href='".$url."'>".__("Return to My List","bepro-listings")."</a></p>";
		}else{
			echo "<p><a href='".$url."'>".__("Cancel","bepro-listings")."</a></p>";
		}
		
		$frontend_form = dirname( __FILE__ )."/templates/form.php";
		$frontend_form = apply_filters("bl_change_upload_form", $frontend_form);
		if($frontend_form)
		require( $frontend_form );
	}

?>