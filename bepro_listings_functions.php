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
 
	function bepro_listings_wphead() {
		echo '<link type="text/css" rel="stylesheet" href="'.plugins_url('css/bepro_listings.css', __FILE__ ).'" ><link type="text/css" rel="stylesheet" href="'.plugins_url('css/easy-responsive-tabs.css', __FILE__ ).'" ><link type="text/css" rel="stylesheet" href="'.plugins_url('css/jquery-ui-1.8.18.custom.css', __FILE__ ).'" ><meta name="generator" content="BePro Listings '.BEPRO_LISTINGS_VERSION.'">
		<link rel="stylesheet" href="'.plugins_url("css/jquery.ui.timepicker.css", __FILE__ ).'" /><link rel="stylesheet" href="'.plugins_url("css/chosen.css", __FILE__ ).'" />
		';
	} 

	function bepro_listings_javascript() {
		$data = get_option("bepro_listings");
		$secure_url = empty($_SERVER["HTTPS"])? "HTTP":"HTTPS";
		$scripts = "";
		wp_enqueue_script('jquery');
		wp_enqueue_script('validate',plugins_url("js/jquery.validate.min.js", __FILE__ ), array('jquery'), true);
		wp_enqueue_script('jquery-ui-datepicker');
		wp_print_scripts('jquery-ui-tabs');
		wp_enqueue_script(
			'BlTimePicker',
			plugins_url("js/jquery.ui.timepicker.js", __FILE__ ),
			array('jquery', 'jquery-ui-tabs'),
			'',
			true
		);
		
		if(!empty($data["map_use_api"]) && !empty($data["show_geo"]))
			wp_enqueue_script('google-maps' , '//maps.google.com/maps/api/js' , false , '3.5&sensor=false');
		$plugindir = plugins_url("bepro-listings");
		
		$scripts .= "\n".'<script type="text/javascript" src="'.$plugindir.'/js/bepro_listings.js"></script><script type="text/javascript" src="'.plugins_url("js/markerclusterer.js", __FILE__ ).'"></script><script type="text/javascript" src="'.plugins_url("js/easyResponsiveTabs.js", __FILE__ ).'"></script><script type="text/javascript" src="'.plugins_url("js/chosen.jquery.js", __FILE__ ).'"></script>';
		
		$scripts .= '
		<script type="text/javascript">
			if(!ajaxurl)	
					var ajaxurl = "'.admin_url('admin-ajax.php').'";
            jQuery(document).ready(function(){
				if(jQuery("#min_date"))
					jQuery("#min_date").datepicker({dateFormat: "yy-mm-dd"});
				if(jQuery("#max_date"))	
					jQuery("#max_date").datepicker({dateFormat: "yy-mm-dd"});
				if(jQuery(".bl_date_input"))	
					jQuery(".bl_date_input").datepicker({dateFormat: "yy-mm-dd"});
				if(jQuery(".bl_time_input") && jQuery.isFunction("timepicker"))	
					jQuery(".bl_time_input").timepicker();
				if(jQuery("#bepro_listings_package_tabs"))
					jQuery( "#bepro_listings_package_tabs" ).tabs();
				if(jQuery(".chosen-select"))
					jQuery(".chosen-select").chosen();
					
				jQuery(".delete_link").click(function(element){
					element.preventDefault();
					tr_element = jQuery(this).parent().parent();
					
					file = jQuery(this)[0].id;
					file = file.split("::");
					check = confirm("'.__("are you sure you want to delete","bepro-listings").' " +file[2]+ "?");
					if(check){
						jQuery.post(ajaxurl, { "action":"bepro_ajax_delete_post", post_id:file[1] }, function(i, message) {
						   var obj = jQuery.parseJSON(i);
						   alert(obj["status"]);
						   if(obj["status"] == "'.__("Deleted Successfully!","bepro-listings").'")
						   tr_element.css("display","none");
						});
					}
				});	
			});
			
		</script>';
		
		if($data["ajax_on"] == "on"){
			$scripts .= "\n".'<script type="text/javascript" src="'.$plugindir.'/js/bepro_listings_ajax.js"></script>';
		}else{
			$scripts .= "\n".'<script type="text/javascript" src="'.$plugindir.'/js/bepro_listings_no_ajax.js"></script>';
		}		
		$tabs_type = (@$data["tabs_type"] == 2)? "horizontal":"vertical";
		$scripts.= '
			<script type="text/javascript">	
		function launch_bepro_listing_tabs(){
			map_count = 0;
			jQuery(".frontend_bepro_listings_vert_tabs").easyResponsiveTabs({           
			type: "'.$tabs_type.'",           
			width: "auto",
			fit: true,
			activate: function(event) { 
				if((event.target.className == "map_tab resp-tab-item resp-tab-active") && (map_count == 0)){
					launch_frontend_map();
					map_count++;
				} 
			}
			});
		}
		</script>
		';
			
		echo $scripts;
		require(dirname( __FILE__ )."/js/js_ajax_hooks.php");
		return;
	}

	
	function bepro_listings_menus() {
		add_submenu_page('edit.php?post_type=bepro_listings', 'Option', 'Options', 4, 'bepro_listings_options', 'bepro_listings_options');
		$num_admin_menus = 0;
		$num_menus = apply_filters("bepro_listings_num_admin_menus", $num_admin_menus);
		if($num_menus > 0)
			add_submenu_page('edit.php?post_type=bepro_listings', 'AddOns', 'AddOns', 5, 'bepro_listings_addons', 'bepro_listings_addons');
			
		add_submenu_page('edit.php?post_type=bepro_listings', 'BPL Status', 'BPL Status', "manage_options", 'bepro_listings_status', 'bepro_listings_status');
	}
	
	     
	//setup for multisite 
	function bepro_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		global $wpdb;
		if ( is_plugin_active_for_network( 'bepro-listings/bepro_listings.php' ) || is_plugin_active_for_network( 'bepro_listings/bepro_listings.php' ) ) {
			bepro_listings_install_table($blog_id);
		}
	}
	
	function bepro_delete_blog($tables ) {
		global $wpdb;
		$tables[] = $wpdb->prefix . BEPRO_LISTINGS_TABLE_BASE;
		return $tables;
	}
	
	//Setup database for multisite
	function bepro_listings_install_table($blog_id = false) {
		global $wpdb;

		//Manage Multi Site
		if($blog_id && ($blog_id != 1)){
			$table_name = $wpdb->base_prefix.$blog_id."_".BEPRO_LISTINGS_TABLE_BASE;
			$meta_table = $wpdb->base_prefix.$blog_id."_"."bepro_listing_typesmeta";
			$order_table = $wpdb->base_prefix.$blog_id."_"."bepro_listing_orders";
		}else{
			$table_name = $wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE;
			$meta_table = $wpdb->prefix."bepro_listing_typesmeta";
			$order_table = $wpdb->prefix."bepro_listing_orders";
		}		
		
 		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")!=$table_name) {

			$sql = "CREATE TABLE " . $table_name . " (
				id int(9) NOT NULL AUTO_INCREMENT,
				email varchar(100) DEFAULT NULL,
				phone varchar(15) DEFAULT NULL,
				cost float DEFAULT NULL,
				post_id int(9) NOT NULL,
				first_name varchar(100) DEFAULT NULL,
				last_name varchar(100) DEFAULT NULL,
				address_line1 varchar(150) DEFAULT NULL,
				city varchar(150) DEFAULT NULL,
				state varchar(150) DEFAULT NULL,
				country varchar(100) DEFAULT NULL,
				postcode varchar(12) DEFAULT NULL,
				website varchar(155) DEFAULT NULL,
				lat varchar(15) DEFAULT NULL,
				lon varchar(15) DEFAULT NULL,
				bl_order_id int(9) DEFAULT NULL,
				created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				UNIQUE KEY `post_id` (`post_id`)
			)ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
			//Switch to new blog
			
			if($blog_id)switch_to_blog($blog_id);
			
			//initial bepro listing
			$user_id = get_current_user_id();
			$any_listings = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME);
			if(!$any_listings || ($wpdb->num_rows == 0)){
			
			//setup category
			$my_cat_id = term_exists( "Test Category", "bepro_listing_types"); 
			if(is_array($my_cat_id)){
				$my_cat_id = (int)$my_cat_id["term_id"];
			}else{
				$my_cat_id = wp_insert_term("Test Category", "bepro_listing_types");
			}
			
			$post = array(
				  'post_author' => $user_id,
				  'post_content' => "<p>This is your first listing. Delete this one in your admin and create one of your own. If you need help, our <a href='http://www.beprosoftware.com/services/'>Wordpress Development</a> team can help. Also note we have tons of <a href='beprosoftware.com/products/bepro-listings'>Wordpress Directory Plugins</a> and <a href='beprosoftware.com/products/bepro-listings'>Wordpress Directory Themes</a> for this plugin like: </p>
				  <ul>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-form-builder/'>Form Builder</a> - Use the drag and drop interface to create multiple front end upload forms and listing types</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-claim/'>Claim Listings</a> - Monetize your directory and allow users to claim listings</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-recaptcha/'>reCAPTCHA</a> - Reduce spam and malicious submissions with a captcha system powered by google</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-tags/'>Tags</a> - This was definitely an achilles heel for this plugin. Now you and your members can tag your listings and allow users to search them via the tag widget</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-contact/'>Contact</a> - Add a contact form to your listing pages. This provides the option to have all emails go to one address or the address for the person who created the listing</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-galleries/'>Gallery</a> - Three 3 gallery options including slider &amp; lightbox, plus three new listings templates</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-videos/'>Video</a> - Improve on the Gallery plugin with the ability to add and feature videos in your listings from website like youtube and uploaded documents (mp4, mpeg, avi, wmv, webm, etc)</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-documents/'>Documents</a> - Allow users to add and manage document listings on your website from the front end (zip, doc, pdf, odt, csv, etc)</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-icons/'>Icons</a> - Tons of google map icons from the 'Map Icons Collection' by Nicolas Mollet</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-realestate/'>Real Estate</a> - Everything needed to run a realestate website, including related info (# rooms, #baths, etc) and search options</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-s2member/'>S2Member</a> - For those interested in running a paid directory/classifieds, this plugin integrates with the popular membershp plugin 's2member'</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-audio/'>Audio</a> - Create a, podcasts, music, or any other type of audio focused website. We support, wav, mp3, and several file types</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-favorites/'>Favorites</a> - Allow visitors and registered users to interact with listings. They can record their likes/dislikes and view them via shortcodes</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-authors/'>Authors</a> - Give your Blog writers and their listings more visibility. With this plugin you add their profile info to their listing pages.</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-pmpro/'>PMPro</a> - Use Paid Membership Pro to charge users to post listings on your website, with this integration.</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-bookings/'>Booking</a>  - Setup your availability and allow users to schedule time. Perfect for real estate, vehicle, hotel, and other niche sites</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-business-directory/'>Business Directory</a> - Use our business and staff focused listing templates with alphabetic filter. Typical phone book type layout.</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-vehicles/'>Vehicles</a>  - Lists cars, boats, trucks, planes, and other automobiles with their details</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-reviews/'>Reviews</a> - Users can leave and search by star ratings</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-search/'>Search</a> - Add predictive google maps address lookup and auto complete enhancements to the basic search feature</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-software-api/'>Api</a> - Turn your website into a 24/7 remote content manager. Ideal for integration with other data feeds.</li>
					<li><a href='https://www.beprosoftware.com/shop/bepro-listings-export/'>Export</a> - Export search results from BePro Lisitngs and version addons</li>
				</ul>
				<h2>Classifieds / Porfolio / Directory Themes</h2>
				<p>We also have several $1 one dollar wordpress themes you can purchase with free data. This provides a great tutorial and / or way to get setup quickly</p>
				<ul>
					<li><a href='http://www.beprosoftware.com/shop/bycater/'>ByCater</a> - Versitile theme for any <strong>wordpress lisings</strong></li>
					<li><a href='http://www.beprosoftware.com/shop/folioprojects/'>FolioProjects</a> - Best used for <strong>Wordpress portfolios</strong></li>
					<li><a href='http://www.beprosoftware.com/shop/mt-classifieds/'>MT CLassifieds</a> - Great <strong>Wordpress Classifieds Theme</strong></li>
					<li><a href='http://www.beprosoftware.com/shop/whatlocalscallit/'>WhatLocalsCallIt</a> - Perfect <strong>Wordpress Directory Theme</strong></li>
					<li><a href='http://www.beprosoftware.com/shop/mp-directory/'>MP Directory</a> - Another great <strong>Wordpress Directory Theme</strong></li>
				</ul>
				
				<p>Check them all out on the <a href='http://www.beprosoftware.com/products/bepro-listings/'>BePro Lisitngs documentation</a> page along with <b>shortcodes</b> and <b>instructions</b></p>
				
				<iframe width='560' height='315' src='//www.youtube.com/embed/zg2o1XK7vKk' frameborder='0' allowfullscreen></iframe>",
				  'post_status' => "publish", 
				  'post_title' => "Your First Wordpress Listing",
				  'post_category' => array($my_cat_id),
				  'post_type' => "bepro_listings"
				);  
				
				//Create post
			
				$post_id = wp_insert_post( $post, $wp_error );				wp_set_object_terms( $post_id, $my_cat_id, "bepro_listing_types", false);
			}
			
			
			//add first image
			
			$upload_dir = wp_upload_dir();
			$to_filename = $upload_dir['path']."/no_img.jpg";
			$full_filename = plugins_url("images/no_img.jpg", __FILE__ );
			$attachment = array(
				 'post_mime_type' => "image/jpeg",
				 'post_title' => "No Image",
				 'post_content' => '',
				 'post_status' => 'inherit'
			);
			if(@copy($full_filename, $to_filename)){
				$attach_id = wp_insert_attachment( $attachment, $to_filename, $post_id);
				$attach_data = wp_generate_attachment_metadata( $attach_id, $to_filename);
				wp_update_attachment_metadata( $attach_id, $attach_data );
			}
			if($blog_id)restore_current_blog();
			set_transient( '_bepro_listings_activation_wizard', 1, HOUR_IN_SECONDS );
		}
		
		
		if ($wpdb->get_var("SHOW TABLES LIKE '$meta_table'")!=$meta_table){
			create_metadata_table($meta_table, "bepro_listing_types");
		}
		$var_name = "bepro_listing_typesmeta";
		$wpdb->$var_name = $meta_table;

		//add first post
		$lat = floatval('44.6470678');
		$lon = floatval('-63.5747943');
		if(!empty($post_id))$wpdb->query("INSERT INTO ".$table_name." (email, phone, cost, address_line1, city, postcode, state, country, website, lat, lon, first_name, last_name, post_id) VALUES('support@beprosoftware.com','561-555-4321', 0, '','halifax', '', 'NS','Canada', 'beprosoftware.com', '$lat', '$lon', 'Lead', 'Tester', $post_id)");
		
		//create payment table
		create_bl_order_table($order_table);
		
	}
	
	//create payment table
	function create_bl_order_table($order_table){
		global $wpdb;
		if ($wpdb->get_var("SHOW TABLES LIKE '$order_table'")!=$order_table) {

			$sql = "CREATE TABLE " . $order_table . " (
				id int(9) NOT NULL NOT NULL AUTO_INCREMENT,
				bl_order_id int(9) NOT NULL,
				feature_id int(9) NOT NULL,
				cust_user_id int(9) NOT NULL,
				bepro_cart_id int(9) DEFAULT NULL,
				status int(1) DEFAULT 2,
				feature_type varchar(50) DEFAULT NULL,
				expires DATETIME DEFAULT NULL,
				date_paid DATETIME DEFAULT NULL,
				created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				UNIQUE KEY `bl_order_id` (`bl_order_id`)
			)ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}
	
	function create_metadata_table($table_name, $type) {
		global $wpdb;
	 
		if (!empty ($wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET utf8";
		if (!empty ($wpdb->collate))
			$charset_collate .= " COLLATE {$wpdb->collate}";
				 
		  $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			{$type}_id bigint(20) NOT NULL default 0,
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext DEFAULT NULL,
			UNIQUE KEY meta_id (meta_id)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	
	//if selected, show link in footer
	function footer_message(){
		echo '<div id="bepro_lisings_footer">
								<a href="http://www.beprosoftware.com/products/bepro-listings" title="Wordpress Directory Plugin" rel="generator">WordPress Directory powered by BePro Lisitngs</a>
			</div>';
	}
	
	
	function bl_load_constants(){
		// The main slug
		if ( !defined( 'BEPRO_LISTINGS_SLUG' ) )
			define( 'BEPRO_LISTINGS_SLUG', 'Listings' );

		// The slug used when editing a doc
		if ( !defined( 'BEPRO_LISTINGS_LIST_SLUG' ) )
			define( 'BEPRO_LISTINGS_LIST_SLUG', 'List' );

		// The slug used when editing a doc
		if ( !defined( 'BEPRO_LISTINGS_EDIT_SLUG' ) )
			define( 'BEPRO_LISTINGS_EDIT_SLUG', 'edit' );

		// The slug used when creating a new doc
		if ( !defined( 'BEPRO_LISTINGS_CREATE_SLUG' ) )
			define( 'BEPRO_LISTINGS_CREATE_SLUG', 'Create' );
			
		// The slug used when saving new docs
		if ( !defined( 'BEPRO_LISTINGSS_SAVE_SLUG' ) )
			define( 'BEPRO_UPLOADS_SAVE_SLUG', 'save' );

		// The slug used when deleting a doc
		if ( !defined( 'BEPRO_LISTINGS_DELETE_SLUG' ) )
			define( 'BEPRO_LISTINGS_DELETE_SLUG', 'delete' );
			
		// The slug used when deleting a doc
		if ( !defined( 'BEPRO_LISTINGS_SEARCH_SLUG' ) )
			define( 'BEPRO_LISTINGS_SEARCH_SLUG', 'listings' );
			
		// The plugin path
		if ( !defined( 'BEPRO_LISTINGS_PLUGIN_PATH' ) )
			define( 'BEPRO_LISTINGS_PLUGIN_PATH', plugins_url("", __FILE__ ) );
		
		// Plugin Slug
		if ( !defined( 'BEPRO_LISTINGS_CATEGORY' ) )
			define( 'BEPRO_LISTINGS_CATEGORY', "bepro_listing_types" );
			
		// Category Slug
		if ( !defined( 'BEPRO_LISTINGS_CATEGORY_SLUG' ) )
			define( 'BEPRO_LISTINGS_CATEGORY_SLUG', "listing_types" );
		
		// The Main table name (check if multisite)
		global $wpdb;
		if (!is_numeric(substr($wpdb->prefix, -2, 1)) && is_multisite()) {
			$cur_blog_id = ($wpdb->blogid == 1)? "":$wpdb->blogid.'_';
			define( 'BEPRO_LISTINGS_TABLE_NAME', $cur_blog_id.'bepro_listings' );
			define( 'BEPRO_LISTINGS_ORDERS_TABLE_NAME', $wpdb->prefix.'bepro_listing_orders' );
		}else if ( !defined( 'BEPRO_LISTINGS_TABLE_NAME' ) ){
			define( 'BEPRO_LISTINGS_TABLE_NAME', 'bepro_listings' );
			define( 'BEPRO_LISTINGS_ORDERS_TABLE_NAME', $wpdb->prefix.'bepro_listing_orders' );
		}	
		
		// Base Table Name
		if ( !defined( 'BEPRO_LISTINGS_TABLE_BASE' ) )
			define( 'BEPRO_LISTINGS_TABLE_BASE', 'bepro_listings' );
			define( 'BEPRO_LISTINGS_ORDERS_TABLE_BASE', 'bepro_listing_orders' );
		
		// Current version
		if ( !defined( 'BEPRO_LISTINGS_VERSION' ) ){
			define( 'BEPRO_LISTINGS_VERSION', '2.2.0017' );
		}	
	}
	
	function bl_complete_startup(){
		global $wpdb;
		$data = get_option("bepro_listings");
		if(empty($data))
			Bepro_listings::bepro_listings_activate();
		
		//Load Languages
		load_plugin_textdomain( 'bepro-listings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		//load default options if they dont already exist		
		if(empty($data["bepro_listings_list_template_1"])){
			//general
			$data["show_cost"] = 1;
			$data["show_con"] = 1;
			$data["show_geo"] = 1;
			$data["show_imgs"] = 1;
			$data["num_images"] = 3;
			$data["cat_heading"] = __("Categories","bepro-listings");
			$data["cat_empty"] = __("No Sub Categories","bepro-listings");
			$data["cat_singular"] = __("Category","bepro-listings");
			$data["permalink"] = "/".BEPRO_LISTINGS_SEARCH_SLUG;
			$data["cat_permalink"] = "/".BEPRO_LISTINGS_CATEGORY_SLUG;
			
			//forms
			$data["validate_form"] = "on";
			$data["success_message"] = __('Listing Created and pending admin approval.',"bepro-listings");
			$data["use_tiny_mce"] = "";
			$data["default_status"] = 'pending';
			$data["default_user_id"] = get_current_user_id();
			$data["fail_message"] = __("Issue saving listing. Try again or contact the Admin","bepro-listings");
			$data["form_cat_style"] = 2;
			$data["bepro_listings_cat_required"] = "";
			$data["bepro_listings_cat_exclude"] = "";
			//search listings
			$data["default_image"] = plugins_url("images/no_img.jpg", __FILE__ );
			$data["link_new_page"] = 1;
			$data["ajax_on"] = "on";
			$data["num_listings"] = 8;
			$data["distance"] = 150;
			$data["dist_measurement"] = 1;
			$data["search_names"] = 1;
			$data["title_length"] = 18;
			$data["desc_length"] = 80;
			$data["details_link"] = __("Item","bepro-listings");
			$data["show_web_link"] = "";
			$data["currency_sign"] = "$";
			$data["show_date"] = 1;
			//Page/post
			$data["gallery_size"] = "thumbnail";
			$data["gallery_cols"] = 3;
			$data["gallery_cols"] = 1;
			$data["show_details"] = 2;
			$data["add_detail_links"] = "on";
			$data["protect_contact"] = "";
			$data["show_content"] = 1;
			$data["tabs_type"] = 1;
			//map
			$data["map_query_type"] = "curl";
			$data["map_use_api"] = 1;
			$data["map_zoom"] = "10";
			//3rd party
			$data["buddypress"] = 0;
			//Payment
			$data["require_payment"] = "";
			$data["publish_after_payment"] = "";
			$data["redirect_need_funds"] = 1;
			$data["charge_amount"] = 1;
			//Support
			$data["footer_link"] = 0;
			//item page template
			$data['bepro_listings_item_title_template'] = 'bepro_listings_item_title';
			$data['bepro_listings_item_gallery_template'] = "bepro_listings_item_gallery";
			$data['bepro_listings_item_after_gallery_template'] = "bepro_listings_item_after_gallery";
			$data['bepro_listings_item_details_template'] = 'bepro_listings_item_details';
			$data['bepro_listings_item_content_template'] = 'bepro_listings_item_content_info';
			
			//item list template
			$data = create_result_listing_templates($data);
			
			//save
			update_option("bepro_listings", $data);
			
		}
		
		//Things that need to change only if there is an upgrade
		$bepro_listings_version = get_option("bepro_listings_version");
		
		if(version_compare($bepro_listings_version, '2.1.5', '<')){
			//upgrade tables to utf8
			if ((is_numeric(substr($wpdb->prefix, -2, 1)) && is_multisite())){ 
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach($blogids as $blogid_x){
					$wpdb->query("ALTER TABLE ".$wpdb->base_prefix.$blogid_x."_".BEPRO_LISTINGS_TABLE_BASE." CONVERT TO CHARACTER SET utf8;");
				}
			}else{
				$wpdb->query("ALTER TABLE ".$wpdb->base_prefix.BEPRO_LISTINGS_TABLE_BASE." CONVERT TO CHARACTER SET utf8;");
			}
		}
		
		if(version_compare($bepro_listings_version, '2.1.90', '<')){
			$data["map_use_api"] = 1;
			$data["map_zoom"] = "10";
			$data["show_imgs"] = 1;
		}
		
		if(version_compare($bepro_listings_version, '2.1.992', '<')){
			//support for BePro Cart
			if ((is_numeric(substr($wpdb->prefix, -2, 1)) && is_multisite())){ 
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach($blogids as $blogid_x){
					if($blogid_x == 1)
						$blogid_x = "";
					else
						$blogid_x = $blogid_x."_";
					
					$wpdb->query("ALTER TABLE ".$wpdb->base_prefix.$blogid_x.BEPRO_LISTINGS_TABLE_BASE." ADD COLUMN bl_order_id int(9) DEFAULT NULL AFTER lon, DROP COLUMN bepro_cart_id;");
					
					//install new payments table
					create_bl_order_table($wpdb->base_prefix.$blogid_x.BEPRO_LISTINGS_ORDERS_TABLE_BASE);
				}
			}else{
				$wpdb->query("ALTER TABLE ".$wpdb->base_prefix.BEPRO_LISTINGS_TABLE_BASE." ADD COLUMN bl_order_id int(9) DEFAULT NULL AFTER lon, DROP COLUMN bepro_cart_id;");
				//install new payments table
				create_bl_order_table($wpdb->base_prefix.BEPRO_LISTINGS_ORDERS_TABLE_BASE);
			}
			
			//flat fee now works differently
			if($data["flat_fee"]) unset($data["flat_fee"]);
		}
		
		if(version_compare($bepro_listings_version, '2.1.999', '<')){
			if ((is_numeric(substr($wpdb->prefix, -2, 1)) && is_multisite())){ 
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach($blogids as $blogid_x){
					if($blogid_x == 1)
						$blogid_x = "";
					else
						$blogid_x = $blogid_x."_";
					$tbl_1 = $wpdb->base_prefix.$blogid_x.BEPRO_LISTINGS_ORDERS_TABLE_BASE;
					$tbl_2 = $wpdb->base_prefix.$blogid_x.BEPRO_LISTINGS_TABLE_BASE;
					$wpdb->query("ALTER TABLE ".$tbl_1." ADD COLUMN expires DATETIME DEFAULT NULL AFTER date_paid;");
					$orders = $wpdb->get_results("SELECT * FROM ".$tbl_1);
					foreach($orders as $order){
						$record = $wpdb->get_row("SELECT * FROM ".$tbl_2." WHERE bl_order_id = ".$order->bl_order_id." LIMIT 1");
						if($record)
							$wpdb->query("UPDATE ".$tbl_1." SET expires = '".$record->expires."' WHERE bl_order_id = ".$record->bl_order_id);
					}
					$wpdb->query("ALTER TABLE ".$tbl_2." DROP COLUMN expires;");
				}
			}else{
				$tbl_1 = $wpdb->prefix.BEPRO_LISTINGS_ORDERS_TABLE_BASE;
				$tbl_2 = $wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE;
				$wpdb->query("ALTER TABLE ".$tbl_1." ADD COLUMN expires DATETIME DEFAULT NULL AFTER date_paid;");
				$orders = $wpdb->get_results("SELECT * FROM ".$tbl_1);
				foreach($orders as $order){
					$record = $wpdb->get_row("SELECT * FROM ".$tbl_2." WHERE bl_order_id = ".$order->bl_order_id." LIMIT 1");
					if(@$record)
						$wpdb->query("UPDATE ".$tbl_1." SET expires = '".$record->expires."' WHERE bl_order_id = ".$record->bl_order_id);
				}
				$wpdb->query("ALTER TABLE ".$tbl_2." DROP COLUMN expires;");
			}
		}
		
		if(version_compare($bepro_listings_version, '2.1.9994', '<')){
			if(!empty($data["show_cost"]))$data["show_cost"] = 1;
			if(!empty($data["show_con"]))$data["show_con"] = 1;
			if(!empty($data["show_geo"]))$data["show_geo"] = 1;
			if(!empty($data["show_imgs"]))$data["show_imgs"] = 1;
			$data["show_details"] = 2;
			$data["show_content"] = 1;
		}
		
		if(version_compare($bepro_listings_version, '2.1.99995', '<')){
			if($data["show_details"] == "on" )$data["show_details"] = 2;
			if($data["show_content"] == "on" )$data["show_content"] = 1;
			if($data["show_geo"] == "on" )$data["show_geo"] = 1;
			if($data["show_con"] == "on" )$data["show_con"] = 1;
		}
		
		if(version_compare($bepro_listings_version, '2.2.0004', '<')){
			if(empty($data["desc_length"]))$data["desc_length"] = 80;
		}
		
		if($bepro_listings_version != BEPRO_LISTINGS_VERSION){
			$bepro_listings_version = BEPRO_LISTINGS_VERSION;
			
			//new features
			
			//show welcome screen to users who are updating
			set_transient( '_bepro_listings_activation_redirect', 1, HOUR_IN_SECONDS );
			
			//set version
			update_option('bepro_listings_version', $bepro_listings_version);
		}
	}
	
	//create templates for search results
	function create_result_listing_templates($data){
		$data['bepro_listings_list_template_1'] = array("bepro_listings_list_title" => "bepro_listings_list_title_template","bepro_listings_list_above_image" => "bepro_listings_list_featured_template","bepro_listings_list_below_title" => "bepro_listings_list_category_template","bepro_listings_list_image" => "bepro_listings_list_image_template","bepro_listings_list_content" => "bepro_listings_list_content_template","bepro_listings_list_end" => "bepro_listings_list_cost_template","bepro_listings_list_end" => "bepro_listings_list_links_template", "style" => plugins_url("css/generic_listings_1.css", __FILE__ ), "template_file" => plugin_dir_path( __FILE__ ).'/templates/listings/generic_1.php');
		$data['bepro_listings_list_template_2'] = array("bepro_listings_list_title" => "bepro_listings_list_title_template","bepro_listings_list_above_image" => "bepro_listings_list_featured_template","bepro_listings_list_below_title" => "bepro_listings_list_category_template","bepro_listings_list_above_title" => "bepro_listings_list_image_template","bepro_listings_list_image" => "bepro_listings_list_geo_template","bepro_listings_list_content" => "bepro_listings_list_content_template","bepro_listings_after_content" => "bepro_listings_list_cost_template","bepro_listings_list_end" => "bepro_listings_list_links_template", "style" => plugins_url("css/generic_listings_2.css", __FILE__ ), "template_file" => plugin_dir_path( __FILE__ ).'/templates/listings/generic_2.php');
		return $data;
		
		do_action("bpl_recreate_templates", $data);
	}
	
	//BePro Email Integration
	function create_bepro_emails_for_bepro_listings(){
		if(@class_exists("Bepro_email")){
			$bepro_email = new Bepro_email();
			$bepro_email->delete_all_owner_emails("bepro_listings");
			//email 1
			$email1["post_title"] = "Hello [username]";
			$email1["post_content"] = "Your submission to [website_url] has been received. Thank you";
			$email1["bpe_owner"] = "bepro_listings";
			$email1["bpe_times_sent"] = "0";
			$email1["bpe_mail_agent"] = "wp_mail";
			$email1["bpe_email_to"] = "[user_email]";
			$email1["bpe_hook"] = "bepro_listings_add_listing";
			$email1["bpe_tracker"] = "bl_email1";
			$email1["bpe_max_send"] = "";
			$bepro_email->bepro_add_edit_email($email1);
			
			//email 2
			$email2["post_title"] = "New Listing";
			$email2["post_content"] = "Your received a new submission on [website_url].";
			$email2["bpe_owner"] = "bepro_listings";
			$email2["bpe_times_sent"] = "0";
			$email2["bpe_mail_agent"] = "wp_mail";
			$email2["bpe_email_to"] = "[admin_user_email]";
			$email2["bpe_hook"] = "bepro_listings_add_listing";
			$email2["bpe_tracker"] = "bl_email2";
			$email2["bpe_max_send"] = "";
			$bepro_email->bepro_add_edit_email($email2);
		}
	}
	
	//Search wordpress table hierarchy for custom post type 'bepro_listing_types'
	function listing_types(){
		global $wpdb;
		return $wpdb->get_results("SELECT *
			FROM ".$wpdb->prefix."terms AS terms
			LEFT JOIN ".$wpdb->prefix."term_taxonomy AS tx ON tx.term_id = terms.term_id
			WHERE tx.taxonomy = 'bepro_listing_types'");
	}
	
	//Return Listings that meet requested critera.
	function bepro_get_listings($returncaluse = false, $catfinder = false, $limit_clause = false){
		global $wpdb;
		if($catfinder)$cat_finder = "LEFT JOIN ".$wpdb->prefix."term_relationships rel ON rel.object_id = posts.ID
				LEFT JOIN ".$wpdb->prefix."term_taxonomy tax ON tax.term_taxonomy_id = rel.term_taxonomy_id
				LEFT JOIN ".$wpdb->prefix."terms t ON t.term_id = tax.term_id";
				
		$join_filter = apply_filters("bepro_listings_search_join_clause","");
		$returncaluse = apply_filters("bepro_listings_add_to_clause",$returncaluse);
		
		if(!empty($returncaluse)){//if we have a search query
			$raw_results = $wpdb->get_results("SELECT geo.*, posts.post_title, posts.post_content, posts.post_status FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as geo 
		LEFT JOIN ".$wpdb->prefix."posts as posts on posts.ID = geo.post_id $cat_finder $join_filter WHERE (posts.post_status = 'publish' OR posts.post_status = 'private') $returncaluse GROUP BY geo.post_id $limit_clause");
		}else{//general blank search
			$raw_results = $wpdb->get_results("SELECT geo.*, posts.post_title, posts.post_content, posts.post_status FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as geo 
		LEFT JOIN ".$wpdb->prefix."posts as posts on posts.ID = geo.post_id $cat_finder $join_filter WHERE (posts.post_status = 'publish' OR posts.post_status = 'private') GROUP BY geo.post_id $limit_clause");	
		}
		return $raw_results;
	}
	
	//Get the categores of a Bepro Listing
	function listing_types_by_post($post_id){
		global $wpdb;
		return $wpdb->get_results("SELECT p.ID, t.term_id, t.name, t.slug
				FROM ".$wpdb->prefix."posts p
				LEFT JOIN ".$wpdb->prefix."term_relationships rel ON rel.object_id = p.ID
				LEFT JOIN ".$wpdb->prefix."term_taxonomy tax ON tax.term_taxonomy_id = rel.term_taxonomy_id
				LEFT JOIN ".$wpdb->prefix."terms t ON t.term_id = tax.term_id
				WHERE p.ID =".$post_id);
	}
	
	function bpl_get_listing_by_post_id($post_id){
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE." WHERE post_id =".$post_id);
	}
	
	//On delete post, also delete the listing from the database and all attachments
	function bepro_delete_post($post_id){
		global $wpdb;
		
		//BePro Listing deleted
		if(get_post_type($post_id) == 'bepro_listings'){
			$listing = bpl_get_listing_by_post_id($post_id);
			if(@$listing->bl_order_id){
				$bl_order_id = $listing->bl_order_id;
				//if there are no listings attached to this order and its not purchased then delete it
				$orders = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE." WHERE bl_order_id =".$bl_order_id);
				if($wpdb->num_rows < 2){
					$order = bl_get_payment_order($bl_order_id);
					if(@$order && ($order->status != 1)){
						wp_delete_post($bl_order_id, true);
						$wpdb->query("DELETE FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." WHERE bl_order_id =".$bl_order_id);
					}
				}
			}
			$wpdb->query("DELETE FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE." WHERE post_id =".$post_id);
		}
		
		//bepro listings order deleted
		if(get_post_type($post_id) == 'bpl_orders'){
			$wpdb->query("DELETE FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." WHERE bl_order_id =".$post_id);
			$wpdb->query("UPDATE ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE." SET bl_order_id = '' WHERE bl_order_id =".$post_id);
		}
		//cart order deleted so BePro Listings order isn't paid. 
		if(get_post_type($post_id) == 'bpc_cart_orders'){
			$affected_orders = $wpdb->get_row("SELECT * FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." WHERE bepro_cart_id = ".$post_id);
			//Need to deactivate these listings and mark order as unpaid
			if(@$affected_orders){
				$wpdb->query("UPDATE ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." SET bepro_cart_id = NULL, status = 2, date_paid = NULL WHERE bepro_cart_id =".$post_id);
				$wpdb->query("UPDATE ".BEPRO_LISTINGS_TABLE_BASE." SET expire = NOW() WHERE bl_order_id =".$affected_orders->bl_order_id);
			}
		}
		return;
	}

	//On delete post, also delete the listing from the database and all attachments
	function bepro_ajax_delete_post(){
		if(!is_numeric($_POST["post_id"])) exit;
		global $wpdb;
		$post_id = $_POST["post_id"];
		$user_data = wp_get_current_user();
		$post_data = get_post($post_id);
		if(is_admin() || ($post_data->post_author == $user_data->ID)){
			$ans = wp_delete_post( $post_id, true );
			if($ans){$message["status"] = __("Deleted Successfully!","bepro-listings");
			}else{$message["status"] = __("Problem Deleting Listing","bepro-listings");
			}
		}else{
			$message["status"] = __("Problem Deleting Listing","bepro-listings");
		}
		echo json_encode($message);
		exit;
	}
	
	function bepro_listings_save($post_id = false, $return_post_id = false){
		global $wpdb;
		if(!empty($_POST["save_bepro_listing"])){
			//tie in for custom and addon error checking
			$check = apply_filters("scan_incoming_bl_listing",array());
			if(@$check && !empty($check)){
				return false;
			}
			//get settings
			$wp_upload_dir = wp_upload_dir();
			$data = get_option("bepro_listings");
			$user_data = wp_get_current_user();
			$default_user_id = $data["default_user_id"];
			$success_message = $data["success_message"];
			$num_images = $data["num_images"];
			$query_type = $data["map_query_type"];
			$default_status = empty($data["default_status"])? "pending":$data["default_status"];
			$return_message = false;
			
			//retrieve variables
			$item_name = addslashes(strip_tags($_POST["item_name"]));
			$content = (is_admin() && is_user_logged_in())? $_POST["content"]:addslashes(strip_tags(strip_shortcodes($_POST["content"])));
			$categories = $wpdb->escape($_POST["categories"]);
			$username = $wpdb->escape(strip_tags($_POST["username"]));
			$password = $wpdb->escape(strip_tags($_POST["password"]));
			$email = $wpdb->escape(strip_tags($_POST["email"]));
			$post_id = (empty($post_id))? $wpdb->escape($_POST["bepro_post_id"]):$post_id;
			$cost =  trim(addslashes(strip_tags($_POST["cost"])));
			$cost = str_replace(array("$",","), array("",""), $cost);
			$cost = (!is_numeric($cost) || ($cost < 0))? "NULL": $cost; 
			$duration = 0;
			$fee = 0;
			
			//Figure out user_id
			if(is_user_logged_in()){
				$user_id = $user_data->ID;
			}elseif(isset($username) && !empty($password)){
				$user_id = wp_create_user( $username, $password, $email );
				if(is_numeric($user_id)) wp_set_current_user($user_id);
			}
			if(empty($user_id))$user_id = $default_user_id;
			
			$user_id = apply_filters("bl_save_listing_user_id_overide", $user_id);
			//create listing in wordpress
			if(!empty($user_id) && ($user_id != 0)){
				//Check for the post_id and create one if we don't have a valid one
				$new_post = false;
				if(empty($post_id)){
					$new_post = true;
				}if(get_post($post_id)){
					$wpdb->query("UPDATE ".$wpdb->prefix."posts SET post_content = '".$content."' WHERE ID=".$post_id);
				}else{
					$new_post = true;
				}
				//if post_id is empty or wasn't found then create a new one
				if($new_post){
					$post = array(
					  'post_author' => $user_id,
					  'post_content' => $content,
					  'post_status' => $default_status, 
					  'post_title' => $item_name,
					  'post_type' => "bepro_listings"
					);  
					//Create post
					$post_id = wp_insert_post( $post, $wp_error ); 
				}
			
				//once we figured out a post_id then proceed
				if(empty($wp_error) && is_numeric($post_id)){
					$post_data = get_post($post_id);
					/*
						//setup custom bepro listing post categories
						1. If we get category names instead of ID's then we need to find the id's or create them
						2. Once we have an array of category ID's, we can assign them to the current listing
					*/
					if(!is_array($categories) && !empty($categories)){
						$categories = explode(",",$categories);
						if(!is_numeric($categories[0])){
							$cat_array = array();
							foreach($categories as $category){
								$check_cat =  wp_insert_term( $category, "bepro_listing_types");
								if(is_array($check_cat) && (!isset($check_cat["errors"]))){
									$cat_array[] = $check_cat["term_id"];
								}elseif(is_wp_error($check_cat)){
									$cat = get_term_by( "name", $category, "bepro_listing_types");
									$cat_array[] = $cat->term_id;
								}
							}
							$categories = $cat_array;
						}
					}
					if(!empty($categories))wp_set_post_terms($post_id,$categories,'bepro_listing_types');
					
					//setup post images
					if($num_images){
						//delete images
						$counter = 0;
						while($counter < $num_images){
							if(is_numeric($_POST["delete_image_".$counter]) && ($post_data->post_author == $user_data->ID))wp_delete_attachment( $_POST["delete_image_".$counter], true );
							$counter++;
						}
						
						$counter = 1;
						$attachments = get_children(array('post_parent'=>$post_id));
						if(!function_exists("wp_generate_attachment_metadata"))
							require ( ABSPATH . 'wp-admin/includes/image.php' );
						if(!function_exists("media_upload_tabs"))
							require ( ABSPATH . 'wp-admin/includes/media.php' );
						
						while(($counter <= $num_images) && (count($attachments) <= $num_images)) {
							if(!empty($_FILES["bepro_form_image_".$counter]) && (!$_FILES["bepro_form_image_".$counter]["error"])){
								$full_filename = $wp_upload_dir['path']."/".$_FILES["bepro_form_image_".$counter]["name"];
								$check_move = @move_uploaded_file($_FILES["bepro_form_image_".$counter]["tmp_name"], $full_filename);
								if($check_move){
									$filename = basename($_FILES["bepro_form_image_".$counter]["name"]);
									$filename = preg_replace('/\.[^.]+$/', '', $filename);
									$wp_filetype = wp_check_filetype(basename($full_filename), null );
									$attachment = array(
										 'post_mime_type' => $wp_filetype['type'],
										 'post_title' => $filename,
										 'post_content' => '',
										 'post_status' => 'inherit'
									);
									$attach_id = wp_insert_attachment( $attachment, $full_filename, $post_id);
									$attach_data = wp_generate_attachment_metadata( $attach_id, $full_filename);
									wp_update_attachment_metadata( $attach_id, $attach_data );
									if($counter == 1)update_post_meta($post_id, '_thumbnail_id', $attach_id);
								}
							}
							$counter++;
						}
						if(!empty($data["show_imgs"]))
							BL_Meta_Box_Listing_Images::save($post_id, $post_after);
					}
					
					//manage lat/lon
					if(is_numeric($_POST['lat']) && is_numeric($_POST['lon'])){
						$lat = $_POST['lat'];
						$lon = $_POST['lon'];
					}else{
						$latlon = get_bepro_lat_lon();
						if(sizeof($latlon) > 0){
							$lat = $latlon["lat"];
							$lon = $latlon["lon"];
						}
					}
					
					//prepare for save to BePro Listings tables
					$listing = bpl_get_listing_by_post_id($post_id);
					$post_status = $post_data->post_status;
					$post_data = $_POST;
					$post_data["post_id"] = $post_id;
					$post_data["lat"] = @$lat;
					$post_data["lon"] = @$lon;
					$post_data["cost"] = $cost;
					$package_id = is_numeric($_POST["bpl_package"])?$_POST["bpl_package"]:"";
					$bl_order_id = "";
					
					/*
						Figuring out payment stuff if applicable to this listing.
					*/
					//calculate cost and duration
					if(is_numeric($data["require_payment"]) && ($data["require_payment"] > 0)){
						//Get package cost and duration
						if(!empty($data["require_payment"]) && ($data["require_payment"] == 1)){ 
							//if category already has an order ID then reuse it
							if(@$listing && $listing->bl_order_id)
								$bl_order_id = $listing->bl_order_id;
							else
								$bl_order_id = bl_get_vacant_order_id($user_id, $data["require_payment"]);
							
							//calculate for categories
							$fee = bepro_get_total_cat_cost($post_id);
							$duration = $data["cat_fee_duration"];
							
							//If there is no cost but there is a duration set, then set the duration
							if(@is_numeric($duration) && ($duration != 0) && (!$fee || ($fee == 0))){
								$expires = date('Y-m-d H:i:s', strtotime("+".$duration." days"));
							}
							bl_create_payment_order(array("bl_order_id" => $bl_order_id, "feature_id" => $post_id, "cust_user_id" => $user_id, "feature_type" => 1, "status" => 2, "expires" => $expires));
						}else if(is_numeric($package_id) && !empty($data["require_payment"]) && ($data["require_payment"] == 2)){
							$pay_fee = true;
							$status = 2; // post status
							
							//if we already created an order ID lets make some checks
							if(@$listing && $listing->bl_order_id){
								$order = bl_get_payment_order($listing->bl_order_id);
								//check order to see if its the same package selected
								if(@$order->feature_id == $package_id){
									$bl_order_id = $listing->bl_order_id;
									//set this listng as published since its paid
									if(($order->status == 1) && (!empty($data["publish_after_payment"]))){
										remove_action( 'post_updated', "bepro_admin_save_details" );
										wp_update_post(array("ID" => $post_id, "post_status" => "publish"));
										remove_action( 'post_updated', "bepro_admin_save_details" );
										$pay_fee = false;
									}
								}else{
									//go find/create a new order ID capable of accomodating this request
									$bl_order_id = bl_get_vacant_order_id($user_id, $data["require_payment"], $package_id);
								}
							}else{
								$bl_order_id = bl_get_vacant_order_id($user_id, $data["require_payment"], $package_id);
								$order = bl_get_payment_order($bl_order_id);
								if(($order->status == 1) && (!empty($data["publish_after_payment"]))){
									remove_action( 'post_updated', "bepro_admin_save_details" );
									wp_update_post(array("ID" => $post_id, "post_status" => "publish"));
									remove_action( 'post_updated', "bepro_admin_save_details" );
									$pay_fee = false;
								}
							}
							
							if($pay_fee){
								$fee = get_post_meta($package_id, "package_cost", true);
							}else{
								//this is paid so active status
								$status = 1;
							}
							
							//Calculate expiration
							$duration = get_post_meta($package_id, "package_duration", true);
							if(@is_numeric($duration) && ($duration != 0) && (!$fee || ($fee == 0))){
								$expires = date('Y-m-d H:i:s', strtotime("+".$duration." days"));
							}
							
							//add info to order table
							bl_create_payment_order(array("bl_order_id" => $bl_order_id, "feature_id" => $package_id, "cust_user_id" => $user_id, "feature_type" => 2, "status" => $status, "expires" => $expires));
						}
						//save purchase association info to bepro listing
						$post_data["bl_order_id"] = $bl_order_id;
					}
					
					if($listing){
						$result = bepro_update_post($post_data);
					}else{
						$result = bepro_add_post($post_data);
					}
					
					if($result === false){
						$return_message = false;
					}else{
						$return_message = true; //everything updated ok
					}
				}
			}else{
				$return_message = false;
			}
		}
		
		if($return_post_id)
			return $post_id;
			
		return $return_message;
	}
	
	//Gallery functions
	function bl_get_listing_images($post_id){
		$attachments = array();
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		
		if ( metadata_exists( 'post', $post_id, '_listing_image_gallery' ) ) {
			$listing_image_gallery = get_post_meta( $post_id, '_listing_image_gallery', true );
			$attachments = array_filter( explode( ',', $listing_image_gallery ) );
		}else{
			$raw_images = get_children(array('post_parent'=>$post_id,'post_mime_type' => 'image'), ARRAY_A);
			$attachments = array_keys($raw_images);
			//remove featured image for reassignment later
			unset($raw_images[$post_thumbnail_id]);
		}
		
		//add featured image infront of others
		if($post_thumbnail_id)
			array_unshift($attachments, $post_thumbnail_id);
		return $attachments;
	}
	
	function bl_get_listing_attachments($post_id){
		$attachments = array();
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		
		if ( metadata_exists( 'post', $post_id, '_listing_image_gallery' ) ) {
			$listing_image_gallery = get_post_meta( $post_id, '_listing_image_gallery', true );
			$attachments = array_filter( explode( ',', $listing_image_gallery ) );
		}else{
			$raw_images = get_children(array('post_parent'=>$post_id), ARRAY_A);
			$attachments = array_keys($raw_images);
			//remove featured image for reassignment later
			unset($raw_images[$post_thumbnail_id]);
		}
		
		//add featured image infront of others
		if($post_thumbnail_id)
			array_unshift($attachments, $post_thumbnail_id);
		return $attachments;
	}
	
	//function to get a file via url, upload it, and attach to a post
	
	function bl_attach_remote_file($post_id, $remote_url){
		$raw_file = explode("/",$remote_url);
		$uploads = wp_upload_dir();
		$filename = $uploads['path']."/".$raw_file[sizeof($raw_file)-1];//get filename
		if(bl_http_get_file($remote_url, $filename)){
			$wp_filetype = wp_check_filetype(basename($filename), null );
			$attachment = array(
				 'post_mime_type' => $wp_filetype['type'],
				 'post_title' => $filename,
				 'post_content' => '',
				 'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $filename, $post_id);
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename);
			wp_update_attachment_metadata( $attach_id, $attach_data );
			update_post_meta($post_id, '_thumbnail_id', $attach_id);
		}
	}
	
	function bl_http_get_file($remote_url, $local_file)    {
		$data = get_option("bepro_listings");
		$query_type = $data["map_query_type"];
		
		$fp = fopen($local_file, 'w');
		if(empty($query_type) || ($query_type == "curl")){
			$cp = curl_init($remote_url);
			curl_setopt($cp, CURLOPT_FILE, $fp);
			$buffer = curl_exec($cp);
			curl_close($cp);
		}else{
			$fp  =  file_get_contents($remote_url);
		}
		fclose($fp);
		
		return true;
	}
	
	function get_bepro_lat_lon(){
		$latlon = array();
		$data = get_option("bepro_listings");
		$query_type = $data["map_query_type"];
		if(!empty($_POST['postcode']) || !empty($_POST['country'])){  
			$to_addr .= !empty($_POST['address_line1'])? $_POST['address_line1']:"";
			$to_addr .= !empty($_POST['city'])? ", ".$_POST['city']:"";
			$to_addr .= !empty($_POST['state'])? ", ".$_POST['state']:"";
			$to_addr .= !empty($_POST['country'])? ", ".$_POST['country']:"";
			$to_addr .= !empty($_POST['postcode'])? ", ".$_POST['postcode']:"";
			$addresstofind_1 = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($to_addr)."&sensor=false";
			if(empty($query_type) || ($query_type == "curl")){
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, $addresstofind_1);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.001 (windows; U; NT4.0; en-US; rv:1.0) Gecko/25250101');
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				$addr_search_1  =  curl_exec($ch);
				curl_close($ch);
			}else{
				$addr_search_1  =  file_get_contents($addresstofind_1);
				/*
				preg_match('!center:\s*{lat:\s*(-?\d+\.\d+),lng:\s*(-?\d+\.\d+)}!U', $_result, $rawlatlon);
				$lat = $rawlatlon[1];
				$lon =  $rawlatlon[2];
				*/
			}
			
			if($addr_search_1)$addr_search_1 = json_decode($addr_search_1);
			if(@$addr_search_1->results[0]->geometry->location){
				$latlon["lon"] = (string)$addr_search_1->results[0]->geometry->location->lng;
				$latlon["lat"] = (string)$addr_search_1->results[0]->geometry->location->lat;
			}
			if(@$addr_search_1->status)
				$latlon["status"] = $addr_search_1->status;
		}
		return $latlon;
	}
	
	function bepro_add_post($post){
		global $wpdb;
		do_action("bepro_listings_add_listing", $post);
		$wpdb->query("SET NAMES utf8");
		return $wpdb->query("INSERT INTO ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." SET
			first_name    = '".$wpdb->escape(strip_tags($post['first_name']))."',
			last_name     = '".$wpdb->escape(strip_tags($post['last_name']))."',
			cost         = '".$wpdb->escape(strip_tags($post['cost']))."',
			email         = '".$wpdb->escape(strip_tags($post['email']))."',
			website       = '".$wpdb->escape(strip_tags($post['website']))."',
			address_line1 = '".$wpdb->escape(strip_tags($post['address_line1']))."',
			city          = '".$wpdb->escape(strip_tags($post['city']))."',
			postcode      = '".$wpdb->escape(strip_tags($post['postcode']))."',
			state         = '".$wpdb->escape(strip_tags($post['state']))."',
			country       = '".$wpdb->escape(strip_tags($post['country']))."',
			post_id         = '".$post['post_id']."',
			phone         = '".$wpdb->escape(strip_tags($post['phone']))."',
			lat           = '".$wpdb->escape(strip_tags($post['lat']))."',
			lon           = '".$wpdb->escape(strip_tags($post['lon']))."',
			bl_order_id   = '".$wpdb->escape(strip_tags($post['bl_order_id']))."'");
	}
	
	function bepro_update_post($post){
	global $wpdb;
		do_action("bepro_listings_update_listing", $post);
		$wpdb->query("SET NAMES 'utf8'");
		return $wpdb->query("UPDATE ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." SET
			cost    = '".$wpdb->escape(strip_tags($post['cost']))."',
			first_name    = '".$wpdb->escape(strip_tags($post['first_name']))."',
			last_name     = '".$wpdb->escape(strip_tags($post['last_name']))."',
			email         = '".$wpdb->escape(strip_tags($post['email']))."',
			phone         = '".$wpdb->escape(strip_tags($post['phone']))."',
			address_line1 = '".$wpdb->escape(strip_tags($post['address_line1']))."',
			city          = '".$wpdb->escape(strip_tags($post['city']))."',
			postcode      = '".$wpdb->escape(strip_tags($post['postcode']))."',
			state         = '".$wpdb->escape(strip_tags($post['state']))."',
			country       = '".$wpdb->escape(strip_tags($post['country']))."',
			lat           = '".$wpdb->escape(strip_tags($post['lat']))."',
			lon           = '".$wpdb->escape(strip_tags($post['lon']))."',
			website       = '".$wpdb->escape(strip_tags($post['website']))."',
			bl_order_id   = '".$wpdb->escape(strip_tags($post['bl_order_id']))."'
			WHERE post_id ='".$wpdb->escape(strip_tags($post['post_id']))."'");
	}
	
	//Create BePro Listings custom post type.
	function bepro_create_post_type() {
		//some stuff we wanna tie to init. This must move
		bl_load_constants();
		
		//register custom post types
		$labels = array(
			'name' => _x('BePro Listings', 'post type general name', 'bepro-listings'),
			'singular_name' => _x('Listing', 'post type singular name', 'bepro-listings'),
			'menu_name' => _x( 'BePro Listings', 'admin menu', 'bepro-listings' ),
			'name_admin_bar'  => _x( 'BePro Listing', 'add new on admin bar', 'bepro-listings' ),
			'add_new' => _x('Add New', 'Listing', 'bepro-listings'),
			'add_new_item' => __('Add New Listing', 'bepro-listings'),
			'edit_item' => __('Edit Listing', 'bepro-listings'),
			'new_item' => __('New Listing', 'bepro-listings'),
			'view_item' => __('View Listing', 'bepro-listings'),
			'all_items' => __( 'All Listings', 'bepro-listings'),
			'search_items' => __('Search Listings', 'bepro-listings'),
			'parent_item_colon'  => __( 'Parent Listings:','bepro-listings'),
			'not_found' =>  __('Nothing found', 'bepro-listings'),
			'not_found_in_trash' => __('Nothing found in Trash', 'bepro-listings'),
			'parent_item_colon' => ''
		);
		
		$options = get_option("bepro_listings");
		$slug = !empty($options["permalink"])? stripslashes($options["permalink"]):"listings";
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_icon' => 'dashicons-images-alt2',
			'rewrite' => array("slug" => $slug, 'with_front' => false),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'author')
		  ); 
	 
		register_post_type( 'bepro_listings' , $args );
		
		$cat_slug = !empty($options["cat_permalink"])? stripslashes($options["cat_permalink"]):BEPRO_LISTINGS_CATEGORY_SLUG;
		register_taxonomy(BEPRO_LISTINGS_CATEGORY, 
			"bepro_listings", 
			array('hierarchical' 			=> true,
				'public' => true,
				'publicly_queryable' => true,
	            'label' 				=> __( 'BePro Listing Categories', 'bepro_listings'),
	            'labels' => array(
	                    'name' 				=> __( 'Listing Categories', 'bepro_listings'),
	                    'singular_name' 	=> __( 'Listing Category', 'bepro_listings'),
						'menu_name'			=> _x( 'Categories', 'Admin menu name', 'bepro_listings' ),
	                    'search_items' 		=> __( 'Search Listing Categories', 'bepro_listings'),
	                    'all_items' 		=> __( 'All Listing Categories', 'bepro_listings'),
	                    'parent_item' 		=> __( 'Parent Listing Category', 'bepro_listings'),
	                    'parent_item_colon' => __( 'Parent Listing Category:', 'bepro_listings'),
	                    'edit_item' 		=> __( 'Edit Listing Category', 'bepro_listings'),
	                    'update_item' 		=> __( 'Update Listing Category', 'bepro_listings'),
	                    'add_new_item' 		=> __( 'Add New Listing Category', 'bepro_listings'),
	                    'new_item_name' 	=> __( 'New Listing Category Name', 'bepro_listings')
	            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
				'rewrite' => array("slug" => $cat_slug, 'with_front' => false))
			);	 
			register_taxonomy_for_object_type( 'bepro_listing_types', 'bepro_listings' );
			//pagement if $options["require_payment"] == 2
			if($options["require_payment"] == 2){
				$labels = array(
					'name' => _x('BPL Packages', 'post type general name', 'bepro-listings'),
					'singular_name' => _x('Package', 'post type singular name', 'bepro-listings'),
					'menu_name' => _x( 'BPL Package', 'admin menu', 'bepro-listings' ),
					'name_admin_bar'  => _x( 'BPL Package', 'add new on admin bar', 'bepro-listings' ),
					'add_new' => _x('Add New', 'Package', 'bepro-listings'),
					'add_new_item' => __('Add New Package', 'bepro-listings'),
					'edit_item' => __('Edit Package', 'bepro-listings'),
					'new_item' => __('New Package', 'bepro-listings'),
					'view_item' => __('View Package', 'bepro-listings'),
					'all_items' => __( 'BPL Packages', 'bepro-listings'),
					'search_items' => __('Search BPL Packages', 'bepro-listings'),
					'parent_item_colon'  => __( 'Parent Package:','bepro-listings'),
					'not_found' =>  __('Nothing found', 'bepro-listings'),
					'not_found_in_trash' => __('Nothing found in Trash', 'bepro-listings'),
					'parent_item_colon' => ''
				);
				
				$args = array(
					'labels' => $labels,
					'public' => false,
					'publicly_queryable' => false,
					'show_ui' => true,
					'show_in_menu' => "edit.php?post_type=bepro_listings",
					'query_var' => true,
					'rewrite' => false,
					'capability_type' => 'post',
					'hierarchical' => false,
					'menu_position' => null,
					'supports' => array('title','editor')
				  ); 
			 
				register_post_type( 'bpl_packages' , $args );  
			}
			
			//pagement if $options["require_payment"] == 2+
			if(!empty($options["require_payment"])){
				$labels = array(
					'name' => _x('BPL Orders', 'post type general name', 'bepro-listings'),
					'singular_name' => _x('Order', 'post type singular name', 'bepro-listings'),
					'menu_name' => _x( 'BPL Orders', 'admin menu', 'bepro-listings' ),
					'name_admin_bar'  => _x( 'BPl Orders', 'add new on admin bar', 'bepro-listings' ),
					'add_new' => _x('Add New', 'Order', 'bepro-listings'),
					'add_new_item' => __('Add New Order', 'bepro-listings'),
					'edit_item' => __('Edit Order', 'bepro-listings'),
					'new_item' => __('New Order', 'bepro-listings'),
					'view_item' => __('View Order', 'bepro-listings'),
					'all_items' => __( 'BPL Orders', 'bepro-listings'),
					'search_items' => __('Search BPL Orders', 'bepro-listings'),
					'parent_item_colon'  => __( 'Parent BL Order:','bepro-listings'),
					'not_found' =>  __('Nothing found', 'bepro-listings'),
					'not_found_in_trash' => __('Nothing found in Trash', 'bepro-listings'),
					'parent_item_colon' => ''
				);
				
				$args = array(
					'labels' => $labels,
					'public' => false,
					'publicly_queryable' => false,
					'show_ui' => true,
					'show_in_menu' => "edit.php?post_type=bepro_listings",
					'query_var' => true,
					'rewrite' => false,
					'capability_type' => 'post',
					'hierarchical' => false,
					'menu_position' => null,
					'supports' => array('title')
				  ); 
			 
				register_post_type( 'bpl_orders' , $args );  
			}
			if(@$options["perm_chng"]){
				flush_rewrite_rules();
				$options["perm_chng"] = "";
				update_option("bepro_listings", $options);
			}
			
			permalink_save_options();
			bl_complete_startup();
	}
	
	function bepro_listings_setup_category(){
		global $wpdb;
		//setup category
		$var_name = "bepro_listing_typesmeta";
		$meta_table = $wpdb->prefix."bepro_listing_typesmeta";
		$wpdb->$var_name = $meta_table;
	}
	
	//function to check if string is a valid category syntax
	function bl_check_is_valid_cat($cat_to_check){
		$cats = (is_array($cat_to_check))? $cat_to_check:explode(",",addslashes(strip_tags($cat_to_check)));
		$is_int = array();
		foreach($cats as $cat){
			$is_int[] = is_numeric($cat)? true:false;
		}
		if(empty($is_int) || in_array(false,array_values($is_int))){
			return false;
		}else{
			return implode(",",$cats);
		}
	}
	
	function bl_build_cat_checkbox($cat_parent, $form_input_name, $level, $incoming = array()){
		$options = get_terms( array('bepro_listing_types'), array("parent" => $cat_parent, "hide_empty" => 0));
		foreach($options as $opt){
			$checked = (isset($incoming[$opt->term_id]))? "checked='checked'":"";
			$search_form .= '<input type="checkbox"  class="sub_cat_checkbox_'.$level.'" name="'.$form_input_name.'" value="'.$opt->term_id.'" '.$checked.'/><span class="searchcheckbox">'.$opt->name.'</span><br />';
			
			$search_form .= bl_build_cat_checkbox($opt->term_id, $form_input_name, ($level + 1),$incoming);
		}
		return $search_form;
	}

	function bepro_listings_placeholder_img_src() {
		return plugins_url("images/no_img.jpg", __FILE__ );
	}

	function update_bepro_listings_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( 'bepro_listing_types', $term_id, $meta_key, $meta_value, $prev_value );
	}

	function get_bepro_listings_term_meta( $term_id, $key, $single = true ) {
		return get_metadata( 'bepro_listing_types', $term_id, $key, $single );
	}
	
	/**
	 * Edit category map fee field.
	 *
	 * @access public
	 * @param mixed $term Term (category) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 * @return void
	 */
	function bepro_listings_edit_category_fee_field( $term = false, $taxonomy = false ) {
		$data = get_option("bepro_listings");		
	
		if($term)
			$bepro_flat_fee = get_bepro_listings_term_meta( $term->term_id, 'bepro_flat_fee', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e('Fee', 'bepro_listings'); ?></label></th>
			<td>
				<input type="text" name="bepro_flat_fee" id="bepro_flat_fee" size="5" value="<?php echo $bepro_flat_fee; ?>" />
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
	function bepro_listings_category_fee_field_save( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['bepro_flat_fee'] ) )
			update_bepro_listings_term_meta( $term_id, 'bepro_flat_fee', $_POST['bepro_flat_fee'] );
	}
	
	//bepro edits
	function bepro_payment_completed($item, $bepro_cart_id){
		global $wpdb;
		
		//Collect Variables
		$data = get_option("bepro_listings");
		$duration = 0;
		$bl_order_id = $item["item_number"];
		$update_payment["bepro_cart_id"] = $bepro_cart_id;
		$update_payment["date_paid"] = date("Y-m-d H:i:s");
		$update_payment["status"] = 1;
		
		if(stristr($bl_order_id, "BPL_PACKAGE-")){
			$feature_id = explode("-",$bl_order_id);
			$update_payment["cust_user_id"] = get_current_user_id();
			$update_payment["feature_type"] = $data["require_payment"];
			$update_payment["feature_id"] = @$feature_id[1];
			$update_payment["bl_order_id"] = bl_get_vacant_order_id($order->cust_user_id, false);
		}else{
			$order = bl_get_payment_order($bl_order_id);
			$update_payment["bl_order_id"] = $bl_order_id;
			$update_payment["cust_user_id"] = $order->cust_user_id;
			$update_payment["feature_type"] = $order->feature_type;
			$update_payment["feature_id"] = $order->feature_id;
		}
		//duration for categories is calculated differently to durations for packages
		if(@$update_payment["feature_id"] && (@$data["require_payment"] == 2) && (get_post($update_payment["feature_id"]))){
			$duration = get_post_meta($update_payment["feature_id"], "package_duration", true);
		}else if(!empty($data["require_payment"]) && ($data["require_payment"] == 1)){
			$duration = $data["cat_fee_duration"];
		}
		
		//set expiration
		if($duration != 0)
			$update_payment["expires"] = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')." +".$duration." days"));

		
		//save purchase details & publish all listings attached to the package
		if(bl_create_payment_order($update_payment) && ($duration != 0)){
			$posts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE bl_order_id = ".$update_payment["bl_order_id"]);
			
			if($posts && ($wpdb->num_rows > 0)){
				remove_action( 'post_updated', "bepro_admin_save_details" );
				foreach($posts as $post){
					wp_update_post(array("ID" => $post->post_id, "post_status" => "publish"));
				}
				add_action( 'post_updated', "bepro_admin_save_details" );
			}
		}
	}
	
	function bepro_get_total_cat_cost($post_id){
		$types = get_the_terms($post_id, 'bepro_listing_types');
		$cost = 0;
		if(@$types){
			foreach($types as $type){
				$cost += get_bepro_listings_term_meta($type->term_id, 'bepro_flat_fee', true );
			}
		}
		return $cost;
	}
	
	function bepro_search_load_orders($join_str){
		global $wpdb;
		return $join_str." LEFT JOIN ".$wpdb->base_prefix.BEPRO_LISTINGS_ORDERS_TABLE_BASE." AS orders ON orders.bl_order_id = geo.bl_order_id";
	}
	//bepro edits
	function bepro_search_remove_expiring($return_clause){
		return $return_clause." AND ((orders.expires IS NULL) || (orders.expires > NOW()) || (orders.expires = '0000-00-00 00:00:00'))";
	}
	
	//bepro edits
	function save_data_and_redirect(){
		if(!empty($_POST["save_bepro_listing"]) && !empty($_POST["redirect"])){
			$wp_upload_dir = wp_upload_dir();
			if($post_id = bepro_listings_save(false, true)){
				$data = get_option("bepro_listings");
				//add to cart and redirect?
				if(is_numeric($data["require_payment"]) && empty($_POST["bepro_post_id"]) && class_exists("Bepro_cart")){
					//find an order id with space for this type of order
					$bl_order_id = bl_get_vacant_order_id($user_id, $data["require_payment"], $_POST["bl_package"]);
					if($data["require_payment"] == 1){
						$cost = bepro_get_total_cat_cost($post_id);
					}else if(($data["require_payment"] == 2) && (is_numeric($_POST["bl_package"]))){
						$cost = get_post_meta($_POST["bl_package"], "package_cost", true);
					}
					if(!empty($cost) && function_exists("bpc_cart_actions_handler") ){
						$_POST["addcart"] = 1; 
						$_POST["price"] = $cost;
						$_POST["item_number"] = $bl_order_id;
						$_POST["product"] = __("Listing Submission");
						bpc_cart_actions_handler();
					}
				}
				header("LOCATION: ".$_POST["redirect"]);
				exit;
			}
		}
	}
	
	function bl_create_payment_order($order){
		global $wpdb;
		if(empty($order) || !is_array($order)) return false;
		if($curr_record = bl_get_payment_order($order["bl_order_id"])){
			return $wpdb->query("UPDATE ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." SET feature_id = ".$order["feature_id"].", cust_user_id = '".$order["cust_user_id"]."', bepro_cart_id = '".$order["bepro_cart_id"]."', status = '".$order["status"]."', feature_type = '".$order["feature_type"]."', expires = '".@$order["expires"]."', date_paid = '".@$order["date_paid"]."' WHERE bl_order_id = ".$order["bl_order_id"]);
		}else{
			return $wpdb->query("INSERT INTO ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." (bl_order_id, feature_id, cust_user_id, bepro_cart_id, status, feature_type, date_paid, expires) VALUES(".$order["bl_order_id"].",".$order["feature_id"].",".$order["cust_user_id"].",'".$order["bepro_cart_id"]."','".$order["status"]."',".$order["feature_type"].",'".$order["date_paid"]."','".$order["expires"]."')");
		}
	}
	
	function bl_get_payment_order($bl_order_id){
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." WHERE bl_order_id =".$bl_order_id);
	}
	
	function get_user_feature_order($user_id, $feature_id){
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." WHERE feature_id =".$feature_id." AND cust_user_id =".$user_id);
	}
	function get_last_bl_order(){
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." ORDER by created DESC LIMIT 1");
	}
	
	function bl_get_vacant_order_id($user_id, $payment_type, $feature_id = false, $new = true){
		global $wpdb;
		$data = get_option("bepro_listings");
		$found_vacant = false;
		
		if(!empty($payment_type) && ($payment_type == 2)){
			$check_orders = get_user_feature_order($user_id, $feature_id);
			
			//check previous orders to see if there is space in an existing package
			if($check_orders){
				$max_num_listings = get_post_meta($feature_id, "num_package_listings", true);
				foreach($check_orders as $check_order){
					$bl_order_id = $check_order->bl_order_id;
					$get_num_listings = $wpdb->get_row("SELECT COUNT(*) as num_listings FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE." WHERE bl_order_id = ".$bl_order_id);
					if($max_num_listings > $get_num_listings->num_listings){
						return $bl_order_id;
					}
				} 
			}
		}
		
		//if its requested that we prevent new orders from being created
		if(!$new)
			return false;
		
		//if category or cant find package with room then create new order
		if(!$found_vacant){
			$last_order = get_last_bl_order();
			$item_name = "Order #".($last_order->id + 1);
			//create order post
			$post = array(
			  'post_author' => $user_id,
			  'post_status' => "Publish", 
			  'post_title' => $item_name,
			  'post_type' => "bpl_orders"
			);  
			//Create post
			return wp_insert_post( $post, $wp_error ); 
		}
	}
	
	function bl_show_user_missing_payments(){
		global $wpdb;
		$data = get_option("bepro_listings");
		if(($data["require_payment"] != 1) && ($data["require_payment"] != 2)) return;
		$packages = get_posts(array("post_type" => "bpl_packages"));
		//if no packages, then warn user and exit
		if(($data["require_payment"] == 2) && empty($packages)){
			echo "<p class='bl_fail_message'>".__("NOTICE: Notify admin to create payment packaged in wp-admin!","bepro-listings")."</p>";
			return;
		}
		echo "<h3>".__("ORDERS","bepro-listings")."</h3>";
		$user_id = get_current_user_id(); 
		$active = $wpdb->get_results("SELECT * FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." WHERE cust_user_id = ".$user_id." AND status = 1 AND (feature_type = 1 OR feature_type = 2) AND expires > NOW()");
		$expired = $wpdb->get_results("SELECT * FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." WHERE cust_user_id = ".$user_id." AND status = 1 AND (feature_type = 1 OR feature_type = 2) AND expires < NOW()");
		$pending = $wpdb->get_results("SELECT * FROM ".BEPRO_LISTINGS_ORDERS_TABLE_NAME." WHERE cust_user_id = ".$user_id." AND status != 1 AND (feature_type = 1 OR feature_type = 2)");
		?>
		
		<div id="bepro_listings_package_tabs">
			<ul>
				<li><a href="#tabs-1"><?php _e("Pending", "bepro-listings"); ?></a></li>
				<li><a href="#tabs-2"><?php _e("Create", "bepro-listings"); ?></a></li>
				<li><a href="#tabs-3"><?php _e("Active", "bepro-listings"); ?></a></li>
				<li><a href="#tabs-4"><?php _e("Expired", "bepro-listings"); ?></a></li>
			</ul>
				
			<div id="tabs-1">
				<?php
					echo '<h4>'.__("Payment Required", "bepro-listings").'</h4>';
					if(sizeof($pending) > 0){
						echo "<table class='bl_payment_required_table'>
						<tr><td>".__("Name","bepro-listings")."</td><td>".__("Cost","bepro-listings")."</td><td>".__("Action","bepro-listings")."</td></tr>";
						foreach($pending as $pay_this){
							$feature = get_post($pay_this->feature_id);
							$bl_order_id = $pay_this->bl_order_id;
							if($data["require_payment"] == 1){
								$cost = bepro_get_total_cat_cost($pay_this->feature_id);
							}else{
								$cost = get_post_meta($pay_this->feature_id, "package_cost", true);
							}
							echo "<tr><td>".$feature->post_title."</td><td>".$data["currency_sign"].$cost."</td><td>".do_shortcode("[bepro_cart_button item_number='".$bl_order_id."' name='".$feature->post_title."' price='".$cost."']")."</td></tr>";
						}
						echo "</table>";
					}else{
						echo "<p>".__("No Records Found.","bepro-listings")."</p>";
					}
				?>
			</div>
			<div id="tabs-2">
				<?php
					echo '<h4>'.__("Available Options", "bepro-listings").'</h4>';
					if($packages){
						echo "<table class='bl_package_options_table'>
						<tr><td>".__("Name","bepro-listings")."</td><td>".__("Description","bepro-listings")."</td><td># ".__("Listings","bepro-listings")."</td><td># ".__("Days","bepro-listings")."</td><td>".__("Cost","bepro-listings")."</td><td>".__("Action","bepro-listings")."</td></tr>";
						foreach($packages as $package){
							$num_listings = get_post_meta($package->ID, "num_package_listings", true);
							$duration = get_post_meta($package->ID, "package_duration", true);
							$cost = get_post_meta($package->ID, "package_cost", true);
							echo "<tr><td>".$package->post_title."</td><td>".$package->post_content."</td><td>".$num_listings."</td><td>".$duration."</td><td>".$data["currency_sign"].$cost."</td><td>".do_shortcode("[bepro_cart_button item_number='BPL_PACKAGE-".$package->ID."' name='".$package->post_title."' price='".$cost."']")."</td></tr>";
						}
						echo "</table>";
					}else{
						echo "<p>".__("No Records Found.","bepro-listings")."</p>";
					}
				?>
			</div>
			<div id="tabs-3">
			<?php
				echo '<h4>'.__("Paid Orders", "bepro-listings").'</h4>';
				if(sizeof($active) > 0){
					echo "<table class='bl_payment_required_table'>
					<tr><td>".__("Name","bepro-listings")."</td><td># ".__("Attached Listings","bepro-listings")."</td><td>".__("Expires","bepro-listings")."</td></tr>";
					foreach($active as $pay_this){
						$feature = get_post($pay_this->feature_id);
						$related = $wpdb->get_row("SELECT COUNT(*) as num_listings FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE." WHERE bl_order_id = ".$pay_this->bl_order_id);
						echo "<tr><td>".$feature->post_title."</td><td>".((@$related->num_listings)? $related->num_listings:0)."</td><td>".date("M, dS Y", strtotime($pay_this->expires))."</td></tr>";
					}
					echo "</table>";
				}else{
					echo "<p>".__("No Records Found.","bepro-listings")."</p>";
				}
			?>
			</div>
			<div id="tabs-4">
				<?php
					echo '<h4>'.__("Expired Orders", "bepro-listings").'</h4>';
					if(sizeof($expired) > 0){
						echo "<table class='bl_payment_required_table'>
						<tr><td>".__("Name","bepro-listings")."</td><td>".__("Cost","bepro-listings")."</td><td>".__("Date","bepro-listings")."</td><td>".__("Action","bepro-listings")."</td></tr>";
						foreach($expired as $pay_this){
							$feature = get_post($pay_this->feature_id);
							$bl_order_id = $pay_this->bl_order_id;
							if($data["require_payment"] == 1){
								$cost = bepro_get_total_cat_cost($pay_this->feature_id);
							}else{
								$cost = get_post_meta($pay_this->feature_id, "package_cost", true);
							}
							echo "<tr><td>".$feature->post_title."</td><td>".$data["currency_sign"].$cost."</td><td>".date("M, dS Y", strtotime($pay_this->expires))."</td><td>".do_shortcode("[bepro_cart_button item_number='".$bl_order_id."' name='".$feature->post_title."' price='".$cost."']")."</td></tr>";
						}
						echo "</table>";
					}else{
						echo "<p>".__("No Records Found.","bepro-listings")."</p>";
					}
				?>
			</div>
		</div>
		<?php
	}
	
	function bpl_check_api_call($app, $call){
		bl_load_constants();
		if($app == "bepro_listings"){
			if(@$call->records->listing[0]){
				$pickup = new bepro_listings_api();
				$pickup->answer($call);
			}else{
				echo "<response><error>2</error></response>";
			}
		}
	}
	
	/* Display updated notice that can be dismissed */
	function bpl_admin_notice() {
		global $current_user ;
		$user_id = $current_user->ID;
		/* Check that the user hasn't already clicked to ignore the message */
		if ( ! get_user_meta($user_id, 'bpl_nag_ignore') ) {
			echo '<div class="updated"><p>'; 
			printf(__('BePro Listings has been Updated. <a href="'.admin_url( 'index.php?page=bepro-listngs-dashboard' ).'">Click here</a> to see whats new. | <a href="%1$s">Hide Notice</a>'), '?bpl_nag_ignore=0');
			echo "</p></div>";
		}
	}
	

	function bpl_nag_ignore() {
		if(is_admin()){
			global $current_user;
			$user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset($_GET['bpl_nag_ignore']) && ('0' == $_GET['bpl_nag_ignore']) ) {
				 @add_user_meta($user_id, 'bpl_nag_ignore', 'true', true);
			}
		}
	}
	
	/* Display review request that can be dismissed */
	function bpl_admin_rate() {
		global $current_user, $wpdb;
		//vars
		$days_since = 0;
		$user_id = $current_user->ID;
		$vote = get_user_meta($user_id, 'bpl_rate_ignore');
		//how long using plugin
		$first = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." order by created asc");
		if(!$first) return;
		$now = time(); // or your date as well
		$your_date = strtotime($first->created);
		$datediff = $now - $your_date;
		$days = floor($datediff/(86400));
		if($days < 30) return;
		
		
		/* Check that the user hasn't already clicked to ignore the message */
		if(@$vote[0] && $vote[0] != 1){ 
			$now = time(); // or your date as well
			$your_date = strtotime($vote[0]);
			$datediff = $now - $your_date;
			$days_since = floor($datediff/(60*60*24));
		}
		
		//if no rating or time to ask again
		if ( (!$vote) && ($days_since > 30)) {
			$posts = $wpdb->get_row("SELECT count(*) as total FROM ".$wpdb->prefix."posts WHERE post_type = 'bepro_listings' AND post_status = 'publish'");
			echo '<div class="updated">'; 
			printf (("<p>".__('CONGRATULATIONS. You have been using BePro Listings for %1$s days and successfully created %2$s listings. Please consider supporting this free product by spreading the word with a 5 star review.',"bepro-lisings").'</p><ul><li><a href="https://wordpress.org/support/view/plugin-reviews/bepro-listings?filter=5" target="_blank">'.__("Yeah, great product","bepro-listings").'</a></li><li><a href="?bpl_rate_ignore=1">'.__("Already left a review","bepro-listings").'</a></li><li><a href="?bpl_rate_ignore=2">'.__("No, not yet","bepro-listings").'</a></li></ul>'), $days, $posts->total);
			echo "</div>";
		}
	}
	
	function bpl_rate_ignore() {
		if(is_admin()){
			global $current_user;
			$user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset($_GET['bpl_rate_ignore']) && ('1' == $_GET['bpl_rate_ignore']) ) {
				 @add_user_meta($user_id, 'bpl_rate_ignore', 1, true);
			}else if ( isset($_GET['bpl_rate_ignore']) && ('2' == $_GET['bpl_rate_ignore']) ) {
				 @add_user_meta($user_id, 'bpl_rate_ignore', date("Y-m-d H:i:s"), true);
			}
		}
	}
	
	function bpl_format_address($address){
		$return_addr = array();
		if(@$address->address_line1)
			$return_addr[] = $address->address_line1;
		if(@$address->city)
			$return_addr[] = $address->city;
		if(@$address->state)
			$return_addr[] = $address->state;
		if(@$address->country)
			$return_addr[] = $address->country;
		if(@$address->postcode)
			$return_addr[] = $address->postcode;
		
		return implode(",",$return_addr);
	}
?>
