<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
    function display_item_list() {
		add_action( 'bp_template_content', 'display_item_content' );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
    }
	
	function display_item_content(){
		global $wpdb, $bp;
		$types = listing_types();
		$current_user = wp_get_current_user();
		$user_id = $bp->displayed_user->id;
		
		// get records
		$data = get_option("bepro_listings");
		if(@$data["require_payment"]){
			$items = $wpdb->get_results("SELECT geo.*, orders.status as order_status, orders.expires, wp_posts.post_title, wp_posts.post_status FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as geo 
			LEFT JOIN ".$wpdb->prefix."posts as wp_posts on wp_posts.ID = geo.post_id 
			LEFT JOIN ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." AS orders on orders.bl_order_id = geo.bl_order_id WHERE wp_posts.post_status != 'trash' AND wp_posts.post_author = ".$user_id);
		}else{
			$items = $wpdb->get_results("SELECT geo.*, wp_posts.post_title, wp_posts.post_status FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as geo 
			LEFT JOIN ".$wpdb->prefix."posts as wp_posts on wp_posts.ID = geo.post_id WHERE wp_posts.post_status != 'trash' AND wp_posts.post_author = ".$user_id);
		}
		
		$listing_url = $bp->loggedin_user->domain.$bp->current_component."/".BEPRO_LISTINGS_CREATE_SLUG."/";
		
		//allow addons to override create listing button. The default for buddypress is to not have a button
		$add_new_button = apply_filters("bl_change_add_listing_button", false, $listing_url);
		if($add_new_button && bp_is_my_profile())
			echo $add_new_button;
		
		//allow addons to change profile template
		$bl_my_list_template = apply_filters("bl_change_my_list_template",dirname( __FILE__ ) . '/templates/list.php', $items);
		if($bl_my_list_template)
			require( $bl_my_list_template );
	}
	
	function show_visitor_profile($template, $items){
		$ids = array();
		foreach($items as $item){
			$ids[] = $item->post_id;
		}
		echo "<h2>My Listings</h2>";
		if((sizeof($ids) > 0) && ($ids[0] != NULL)){
			echo do_shortcode("[display_listings l_ids='".implode(",", $ids)."' type='2' show_paging=1]");
		}else{
			echo "<p>".__("No live listings for this user","bepro-listings")."</p>";
		}
		echo "<div style='clear:both'><br /></div>";
		return "";
	}
	
    function create_listings() {
		global $wpdb, $bp;
		$current_user = wp_get_current_user();
		
		//categories
		
		$user_ID = $bp->displayed_user->id;
		if(isset($_POST["save_bepro_listing"]) && !empty($_POST["save_bepro_listing"])){
			$success = false;
			$success = bepro_listings_save();
			if($success)
				$message = urlencode(__("Success saving listing","bepro-listings"));
			else
				$message = urlencode(__("Error saving listing","bepro-listings"));
			$current_user = wp_get_current_user();
			$bp_profile_link = bp_core_get_user_domain( $bp->displayed_user->id);
			wp_redirect($bp_profile_link  . BEPRO_LISTINGS_SLUG ."?message=".$message);
			exit;

		}elseif(isset($bp->action_variables[0]) && ($bp->current_action == BEPRO_LISTINGS_CREATE_SLUG)){
			add_action( 'bp_template_content', 'update_listing_content' );
		}else{
			add_action( 'bp_template_content', 'create_listing_content' );
		}
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
    }
	
	function create_listing_content(){
		global $bp;
		//get settings
		$data = get_option("bepro_listings");
		$default_user_id = $data["default_user_id"];
		$num_images = $data["num_images"];
		$validate = $data["validate_form"];
		$show_cost = $data["show_cost"];
		$show_con = $data["show_con"];
		$show_geo = $data["show_geo"];
		
		$listing_url = $bp->loggedin_user->domain.$bp->current_component;
		$frontend_form = dirname( __FILE__ )."/templates/form.php";
		$frontend_form = apply_filters("bl_change_upload_form", $frontend_form);
		if($frontend_form)
			require( $frontend_form );
	}
	
	function update_listing_content(){
		global $wpdb, $bp, $post;
		$data = get_option("bepro_listings");
		$listing_url = $bp->loggedin_user->domain.$bp->current_component."/";
		//get information 
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE id = ".$bp->action_variables[0]);
		if(!$item){
			header("Location: ".$listing_url."?message=Listing Does not exist");
			exit;
		}	
		$post_data = get_post($item->post_id);
		$user_id = get_current_user_id();
		if($post_data->post_author != $user_id){
			header("Location: ".$listing_url."?message=".__("You do not own this listing","bepro-listings"));
			exit;
		}
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
		
		$siteNo = $bp->groups->current_group->id;
		$listing_url = $bp->loggedin_user->domain.BEPRO_LISTINGS_SLUG."/";
		
		$frontend_form = dirname( __FILE__ )."/templates/form.php";
		$frontend_form = apply_filters("bl_change_upload_form", $frontend_form);
		if($frontend_form)
			require( $frontend_form );
	}
 
	/**
	 * Get the current view type when the item type is 'group'
	 *
	 * @package BuddyPress Docs
	 * @since 1.0-beta
	 */
	function get_current_view(  ) {
		global $bp;

		if ( empty( $bp->action_variables[0] ) ) {
			// An empty $bp->action_variables[0] means that you're looking at a list
			$view = 'list';
		}  else if ( $bp->action_variables[0] == "create" ) {
			// Create new doc
			$view = 'create';
		}  else if ( !empty( $bp->action_variables[1] ) && $bp->action_variables[1] == "edit" ) {
			// Create new doc
			$view = 'edit';
		} 
	

		return $view;
	}
			
	// Set up Cutsom BP navigation
	function bepro_listings_nav() {
		global $bp;
			$settings_link = $bp->loggedin_user->domain . BEPRO_LISTINGS_SLUG. '/';

			bp_core_new_nav_item( array(
					'name' => __( BEPRO_LISTINGS_SLUG, 'bepro-listings' ),
					'slug' => BEPRO_LISTINGS_SLUG,
					'position' => 20,
					'default_subnav_slug' => 'List',
					'screen_function' => 'display_item_list' 
			) );
			
			bp_core_new_subnav_item( array( 'name' => __( BEPRO_LISTINGS_LIST_SLUG, 'bepro-listings' ), 'slug' => BEPRO_LISTINGS_LIST_SLUG, 'parent_url' => $settings_link, 'parent_slug' => BEPRO_LISTINGS_SLUG, 'screen_function' => 'display_item_list', 'position' => 10) );
			
			//if there is a 3rd party plugin which takes over the creation of listings, then don't show the button
			$add_new_button = apply_filters("bl_change_add_listing_button", false, "");
			$item_css_id = "";
			if($add_new_button){
				$item_css_id = "bl_hide_bp_create_menu";
			}
		
			bp_core_new_subnav_item( array( 'name' => __( BEPRO_LISTINGS_CREATE_SLUG, 'bepro-listings' ), 'slug' => BEPRO_LISTINGS_CREATE_SLUG, 'parent_url' => $settings_link, 'parent_slug' => BEPRO_LISTINGS_SLUG, 'screen_function' => 'create_listings', 'position' => false, 'user_has_access' => bp_is_my_profile(), 'item_css_id' => $item_css_id) );


		  // Change the order of menu items
		  $bp->bp_nav[BEPRO_LISTINGS_SLUG]['position'] = 100;
		  bepro_listings_nav_count();
		  
	}

	function bepro_listings_nav_count() {
			global $bp, $wpdb;
			
			$user_id = @$bp->displayed_user->id;
			// This will probably only work on BP 1.3+
			if ( !empty( $bp->bp_nav[BEPRO_LISTINGS_SLUG]) && $user_id && (is_numeric($user_id))) {
				$current_tab_name = $bp->bp_nav[BEPRO_LISTINGS_SLUG]['name'];

				$item_count = $wpdb->get_row("SELECT count(*) as item_count FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as geo 
			LEFT JOIN ".$wpdb->prefix."posts as wp_posts on wp_posts.ID = geo.post_id WHERE wp_posts.post_status != 'trash' AND wp_posts.post_author = ".$user_id);

				if($item_count) $item_count = $item_count->item_count;

				$bp->bp_nav[BEPRO_LISTINGS_SLUG]['name'] = sprintf( __( '%s <span>%d</span>', BEPRO_LISTINGS_SLUG ), $current_tab_name, $item_count );
			}
		}

bepro_listings_nav();

	function bepro_listings_bp_activity_publish( $post_types ) {
		$post_types[] = 'bepro_listings';
		return $post_types;
	}

	function bepro_listings_bp_activity_action( $activity_action, $post, $post_permalink ) {
		global $bp;
		if( $post->post_type == 'bepro_listings' ) {
			if ( is_multisite() )
			$activity_action = sprintf( __( '%1$s created a new listing, %2$s, on the site %3$s', 'bepro_listings' ), bp_core_get_userlink( (int) $post->post_author ), '' . $post->post_title . '', '' . get_blog_option( $blog_id, 'blogname' ) . '' );
			else
			$activity_action = sprintf( __( '%1$s created a new listing, %2$s', 'bepro_listings' ), bp_core_get_userlink( (int) $post->post_author ), '' . $post->post_title . '' );

		}

		return $activity_action;
	}

?>
