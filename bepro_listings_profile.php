<?php

	function bl_my_listings(){
		global $wpdb, $post;
		$current_url =  get_permalink( $post->ID );
		if(!is_user_logged_in()){
			echo "<p>You need to be Logged In to see your Listings.</p>";
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
			if($success)
				$message = urlencode("Success saving listing");
			else
				$message = urlencode("Error saving listing");
			$current_user = wp_get_current_user();
			$current_url = get_permalink( $post->ID );
			wp_redirect($current_url ."?message=".$message);
			exit;
		}
		
		if(!empty($_GET["bl_manage"])){
			if(!empty($_GET["bl_id"])){
				bl_profile_update_listing_content();
			}else{
				bl_profile_add_listing_content();
			}
		}else{
		
			// get records
			$items = $wpdb->get_results("SELECT geo.*, wp_posts.post_title, wp_posts.post_status FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as geo 
			LEFT JOIN ".$wpdb->prefix."posts as wp_posts on wp_posts.ID = geo.post_id WHERE wp_posts.post_status != 'trash' AND wp_posts.post_author = ".$user_id);
			
			$listing_url = "?bl_manage=1&bl_id=";
			require( dirname( __FILE__ ) . '/templates/list.php' );
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
		require( dirname( __FILE__ ) . '/templates/form.php' );
	}
	
	function bl_profile_add_listing_content(){
		//get settings
		$data = get_option("bepro_listings");
		$default_user_id = $data["default_user_id"];
		$num_images = $data["num_images"];
		$validate = $data["validate_form"];
		$show_cost = $data["show_cost"];
		$show_con = $data["show_con"];
		$show_geo = $data["show_geo"];
		
		$listing_url = "?bl_manage=1&bl_id=";
		require( dirname( __FILE__ ) . '/templates/form.php' );
	}

?>