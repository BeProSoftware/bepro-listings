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
		echo '<link type="text/css" rel="stylesheet" href="'.plugins_url('css/bepro_listings.css', __FILE__ ).'" ><link type="text/css" rel="stylesheet" href="'.plugins_url('css/jquery-ui-1.8.18.custom.css', __FILE__ ).'" ><meta name=\"plugin\" content=\"Bepro Listings plugin\">
		<style type="text/css">
			.l_featured{
				background-color:#F0E68C;
				float: left;
				clear: both;
				padding:10px 10px 0;
				border-radius:10px;
				-moz-border-radius:10px;
				-webkit-border-radius:10px;
			}
		
		</style>
		
		';
		
	} 

	function bepro_listings_javascript() {
		$data = get_option("bepro_listings");
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('google-maps' , 'http://maps.google.com/maps/api/js' , false , '3.5&sensor=false');
		$plugindir = plugins_url("bepro-listings");
		
		$scripts .= "\n".'<script type="text/javascript" src="'.$plugindir.'/js/bepro_listings.js"></script><script type="text/javascript" src="'.plugins_url("js/markerclusterer.js", __FILE__ ).'"></script><script type="text/javascript" src="'.plugins_url("js/jquery.validate.min.js", __FILE__ ).'"></script><script type="text/javascript" src="'.plugins_url("js/jquery.maskedinput-1.3.min.js", __FILE__ ).'"></script>';
		
		$scripts .= '
		<script type="text/javascript">
			if(!ajaxurl)	
					var ajaxurl = "'.admin_url('admin-ajax.php').'";
            jQuery(document).ready(function(){
				if(jQuery("#min_date"))
					jQuery("#min_date").datepicker();
				if(jQuery("#max_date"))	
					jQuery("#max_date").datepicker();
					
				jQuery(".delete_link").click(function(element){
					element.preventDefault();
					tr_element = jQuery(this).parent().parent();
					
					file = jQuery(this)[0].id;
					file = file.split("::");
					check = confirm("are you sure you want to delete " +file[2]+ "?");
					if(check){
						jQuery.post(ajaxurl, { "action":"bepro_ajax_delete_post", post_id:file[1] }, function(i, message) {
						   var obj = jQuery.parseJSON(i);
						   alert(obj["status"]);
						   if(obj["status"] == "Deleted Successfully!")
						   tr_element.css("display","none");
						});
					}
				});	
			});
			
		</script>';
		
		if($data["ajax_on"] == "on")
			$scripts .= "\n".'<script type="text/javascript" src="'.$plugindir.'/js/bepro_listings_ajax.js"></script>';
			
		echo $scripts;
		return;
	}

	
	function bepro_listings_menus() {
		add_submenu_page('edit.php?post_type=bepro_listings', 'Option', 'Options', 4, 'bepro_listings_options', 'bepro_listings_options');
		$num_admin_menus = 0;
		$num_menus = apply_filters("bepro_listings_num_admin_menus", $num_admin_menus);
		if($num_menus > 0)
			add_submenu_page('edit.php?post_type=bepro_listings', 'AddOns', 'AddOns', 5, 'bepro_listings_addons', 'bepro_listings_addons');
			
	}
	
	     
	//setup for multisite 
	function bepro_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		global $wpdb;
		bepro_listings_install_table($blog_id);
	}
	
	//Setup database for multisite
	function bepro_listings_install_table($blog_id = false) {
		global $wpdb;
		$bepro_listings_version = BEPRO_LISTINGS_VERSION;

		//Manage Multi Site
		if($blog_id && ($blog_id != 1)){
			$table_name = $wpdb->prefix.$blog_id."_".BEPRO_LISTINGS_TABLE_BASE;
			$meta_table = $wpdb->prefix.$blog_id."_"."bepro_listing_typesmeta";
		}else{
			$table_name = $wpdb->prefix.BEPRO_LISTINGS_TABLE_BASE;
			$meta_table = $wpdb->prefix."bepro_listing_typesmeta";
		}		
		
 		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'")!=$table_name) {

			$sql = "CREATE TABLE " . $table_name . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				email tinytext DEFAULT NULL,
				phone tinytext DEFAULT NULL,
				cost float DEFAULT NULL,
				post_id int(9) NOT NULL,
				first_name tinytext DEFAULT NULL,
				last_name tinytext DEFAULT NULL,
				address_line1 tinytext DEFAULT NULL,
				city tinytext DEFAULT NULL,
				state tinytext DEFAULT NULL,
				country tinytext DEFAULT NULL,
				postcode tinytext DEFAULT NULL,
				website varchar(55) DEFAULT NULL,
				lat varchar(15) DEFAULT NULL,
				lon varchar(15) DEFAULT NULL,
				created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY  (id),
				UNIQUE KEY `post_id` (`post_id`)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			
			//Switch to new blog
			
			if($blog_id)switch_to_blog($blog_id);
			
			//initial bepro listing
			$user_id = get_current_user_id();
			
			$post = array(
				  'post_author' => $user_id,
				  'post_content' => "<p>This is your first listing. Delete this one in your admin and create one of your own. If you need help, our <a href='http://www.beprosoftware.com/services/'>North American Wordpress Web Development</a> team can help. Also note we have tons of <a href='beprosoftware.com/products/bepro-listings'>Wordpress Directory</a> and <a href='beprosoftware.com/products/bepro-listings'>Wordpress classifieds plugin</a> addons for this plugin like: </p>
				  <ul>
					<li><a href='http://www.beprosoftware.com/shop/bepro-listings-tags/'>Tags</a> - This was definitely an achilles heel for this plugin. Now you and your members can tag your listings and allow users to search them via the tag widget</li>
					<li><a href='http://www.beprosoftware.com/shop/bepro-listings-contact/'>Contact</a> - Add a contact form to your listing pages. This provides the option to have all emails go to one address or the address for the person who created the listing</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-galleries/'>Gallery</a> - Three 3 gallery options including slider &amp; lightbox, plus three new listings templates</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-videos/'>Video</a> - Improve on the Gallery plugin with the ability to add and feature videos in your listings from website like youtube and uploaded documents (mp4, mpeg, avi, wmv, webm, etc)</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-documents/'>Documents</a> - Allow users to add and manage document listings on your website from the front end (zip, doc, pdf, odt, csv, etc)</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-icons/'>Icons</a> - Tons of google map icons from the 'Map Icons Collection' by Nicolas Mollet</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-realestate/'>Real Estate</a> - Everything needed to run a realestate website, including related info (# rooms, #baths, etc) and search options</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-s2member/'>S2Member</a> - For those interested in running a paid directory/classifieds, this plugin integrates with the popular membershp plugin 's2member'</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-audio/'>Audio</a> - Create a, podcasts, music, or any other type of audio focused website. We support, wav, mp3, and several file types</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-favorites/'>Favorites</a> - Allow visitors and registered users to interact with listings. They can record their likes/dislikes and view them via shortcodes</li>
					<li><a href='www.beprosoftware.com/shop/bepro-listings-authors/'>Authors</a> - Give your Blog writers and their listings more visibility. With this plugin you add their profile info to their listing pages.</li>
				</ul>
				<h2>Classifieds / Porfolio / Directory Themes</h2>
				<p>We also have several $1 one dollar wordpress themes you can purchase with free data. This provides a great tutorial and / or way to get setup quickly</p>
				<ul>
					<li><a href='http://www.beprosoftware.com/shop/bycater/'>ByCater</a> - Vertile theme for any <strong>wordpress lisings</strong></li>
					<li><a href='http://www.beprosoftware.com/shop/folioprojects/'>FolioProjects</a> - Best used for <strong>Wordpress portfolios</strong></li>
					<li><a href='http://www.beprosoftware.com/shop/mt-classifieds/'>MT CLassifieds</a> - Great <strong>Wordpress Classifieds Theme</strong></li>
					<li><a href='http://www.beprosoftware.com/shop/whatlocalscallit/'>WhatLocalsCallIt</a> - Perfect <strong>Wordpress Directory Theme</strong></li>
				</ul>
				
				<p>Check them all out on the <a href='http://www.beprosoftware.com/products/bepro-listings/'>BePro Lisitngs documentation</a> page along with <b>shortcodes</b> and <b>instructions</b></p>
				
				<iframe width='560' height='315' src='//www.youtube.com/embed/zg2o1XK7vKk' frameborder='0' allowfullscreen></iframe>",
				  'post_status' => "publish", 
				  'post_title' => "Your First Wordpress Listing",
				  'post_category' => array($my_cat_id),
				  'post_type' => "bepro_listings"
				);  
				
			//Create post
			$post_id = wp_insert_post( $post, $wp_error ); 
			
			
			
			//setup category
			$my_cat_id = term_exists( "Business", "bepro_listing_types"); 
			if(is_array($my_cat_id)) $my_cat_id = $my_cat_id["term_id"];
			wp_set_post_terms( $post_id, array($my_cat_id), "bepro_listing_types", false );
			wp_set_object_terms( $post_id, $my_cat_id, "bepro_listing_types", false);
			
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
		}
		
		
		if ($wpdb->get_var("SHOW TABLES LIKE '$meta_table'")!=$meta_table){
			create_metadata_table($meta_table, "bepro_listing_types");
		}
		$var_name = "bepro_listing_typesmeta";
		$wpdb->$var_name = $meta_table;
		
		//set version
		update_option('bepro_listings_version', $bepro_listings_version);

		//add first post
		$lat = floatval('44.6470678');
		$lon = floatval('-63.5747943');
		if(!empty($post_id))$wpdb->query("INSERT INTO ".$table_name." (email, phone, cost, address_line1, city, postcode, state, country, website, lat, lon, first_name, last_name, post_id) VALUES('support@beprosoftware.com','561-555-4321', 0, '','halifax', '', 'NS','Canada', 'beprosoftware.com', '$lat', '$lon', 'Lead', 'Tester', $post_id)");
		
	}
	
	function create_metadata_table($table_name, $type) {
		global $wpdb;
	 
		if (!empty ($wpdb->charset))
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		if (!empty ($wpdb->collate))
			$charset_collate .= " COLLATE {$wpdb->collate}";
				 
		  $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			{$type}_id bigint(20) NOT NULL default 0,
		 
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext DEFAULT NULL,
					 
			UNIQUE KEY meta_id (meta_id)
		) {$charset_collate};";
		 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	
	//if selected, show link in footer
	function footer_message(){
		echo '<div id="bepro_lisings_footer">
								<a href="http://www.beprosoftware.com/products/bepro-listings" title="Wordpress Classifieds & Directory Plugin" rel="generator">Proudly powered by BePro Lisitngs</a>
			</div>';
	}
	
	
	function load_constants(){
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
		if ( !defined( 'BEPRO_LISTINGS_CATEGORY' ) )
			define( 'BEPRO_LISTINGS_CATEGORY', "bepro_listing_types" );
		
		// The Main table name (check if multisite)
		if (function_exists('is_multisite') && is_multisite()) {
			global $wpdb;
			$cur_blog_id = ($wpdb->blogid == 1)? "":$wpdb->blogid.'_';
			define( 'BEPRO_LISTINGS_TABLE_NAME', $cur_blog_id.'bepro_listings' );
		}else if ( !defined( 'BEPRO_LISTINGS_TABLE_NAME' ) ){
			define( 'BEPRO_LISTINGS_TABLE_NAME', 'bepro_listings' );
		}	
		
		// Base Table Name
		if ( !defined( 'BEPRO_LISTINGS_TABLE_BASE' ) )
			define( 'BEPRO_LISTINGS_TABLE_BASE', 'bepro_listings' );
		
		// Current version
		if ( !defined( 'BEPRO_LISTINGS_VERSION' ) ){
			define( 'BEPRO_LISTINGS_VERSION', '2.1.12' );
		}	
		
		$data = get_option("bepro_listings");
		if(empty($data))
			Bepro_listings::bepro_listings_activate();
		
		//Load Languages
		load_plugin_textdomain( 'bepro-listings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		
		//load default options if they dont already exist		
		if(empty($data["bepro_listings_list_template_1"])){
			//general
			$data["show_cost"] = "on";
			$data["show_con"] = "on";
			$data["show_geo"] = "on";
			$data["num_images"] = 3;
			//forms
			$data["validate_form"] = "on";
			$data["success_message"] = 'Listing Created and pending admin approval.';			
			$data["default_status"] = 'pending';			
			$data["default_user_id"] = get_current_user_id();	
			$data["fail_message"] = "Issue saving listing. Try again or contact the Admin";			
			$data["bepro_listings_cat_required"] = "";					
			$data["bepro_listings_cat_exclude"] = "";					
			//search listings
			$data["default_image"] = plugins_url("images/no_img.jpg", __FILE__ );
			$data["link_new_page"] = 1;
			$data["ajax_on"] = "on";
			$data["num_listings"] = 3;
			$data["distance"] = 150;
			$data["details_link"] = "Item";
			$data["show_web_link"] = "";
			//Page/post
			$data["gallery_size"] = "thumbnail";
			$data["gallery_cols"] = 3;
			$data["show_details"] = "on";
			$data["add_detail_links"] = "on";
			$data["show_content"] = "on";
			//map
			$data["map_query_type"] = "curl";
			//buddypress
			$data["buddypress"] = 0;
			//Support
			$data["footer_link"] = 0;
			//item page template
			$data['bepro_listings_item_title_template'] = 'bepro_listings_item_title';
			$data['bepro_listings_item_gallery_template'] = "bepro_listings_item_gallery";
			$data['bepro_listings_item_after_gallery_template'] = "bepro_listings_item_after_gallery";
			$data['bepro_listings_item_details_template'] = 'bepro_listings_item_details';
			$data['bepro_listings_item_content_template'] = 'bepro_listings_item_content_info';
			
			//item list template
			$data['bepro_listings_list_template_1'] = array("bepro_listings_list_title" => "bepro_listings_list_title_template","bepro_listings_list_below_title" => "bepro_listings_list_category_template","bepro_listings_list_image" => "bepro_listings_list_image_template","bepro_listings_list_content" => "bepro_listings_list_content_template","bepro_listings_list_end" => "bepro_listings_list_links_template", "style" => plugins_url("css/generic_listings_1.css", __FILE__ ));
			$data['bepro_listings_list_template_2'] = array("bepro_listings_list_title" => "bepro_listings_list_title_template","bepro_listings_list_below_title" => "bepro_listings_list_category_template","bepro_listings_list_above_title" => "bepro_listings_list_image_template","bepro_listings_list_image" => "bepro_listings_list_geo_template","bepro_listings_list_content" => "bepro_listings_list_content_template","bepro_listings_list_end" => "bepro_listings_list_links_template", "style" => plugins_url("css/generic_listings_2.css", __FILE__ ));
			
			//save
			update_option("bepro_listings", $data);
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
		return $wpdb->get_results("SELECT p.ID, t.term_id
				FROM ".$wpdb->prefix."posts p
				LEFT JOIN ".$wpdb->prefix."term_relationships rel ON rel.object_id = p.ID
				LEFT JOIN ".$wpdb->prefix."term_taxonomy tax ON tax.term_taxonomy_id = rel.term_taxonomy_id
				LEFT JOIN ".$wpdb->prefix."terms t ON t.term_id = tax.term_id
				WHERE p.ID =".$post_id);
	}
	
	//On delete post, also delete the listing from the database and all attachments
	function bepro_delete_post($post_id){
		global $wpdb;
		$wpdb->query("DELETE FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id =".$post_id);
		return;
	}

	//On delete post, also delete the listing from the database and all attachments
	function bepro_ajax_delete_post(){
		global $wpdb;
		$post_id = $_POST["post_id"];
		$user_data = wp_get_current_user();
		$post_data = get_post($post_id);
		if(is_admin() || ($post_data->post_author == $user_data->ID)){
			$ans = wp_delete_post( $post_id, true );
			if($ans){$message["status"] = "Deleted Successfully!";
			}else{$message["status"] = "Problem Deleting Listing";
			}
		}else{
			$message["status"] = "Problem Deleting Listing";;
		}
		echo json_encode($message);
		exit;
	}
	
	function bepro_listings_save($post_id = false){
		global $wpdb;
		if(!empty($_POST["save_bepro_listing"])){
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
			
			$item_name = $wpdb->escape($_POST["item_name"]);
			$content = $wpdb->escape(strip_tags($_POST["content"]));
			$categories = $wpdb->escape($_POST["categories"]);
			$username = $wpdb->escape(strip_tags($_POST["username"]));
			$password = $wpdb->escape(strip_tags($_POST["password"]));
			$post_id = (empty($post_id))? $wpdb->escape($_POST["bepro_post_id"]):$post_id;
			$cost =  trim(addslashes(strip_tags($_POST["cost"])));
			$cost = str_replace(array("$",","), array("",""), $cost);
			$cost = (!is_numeric($cost) || ($cost < 0))? "NULL": $cost; 

			//Figure out user_id
			if(is_user_logged_in()){
				$user_id = $user_data->ID;
			}elseif(isset($username) && !empty($password)){
				$user_id = wp_create_user( $username, $password, $email );		
			}
			if(empty($user_id))$user_id = $default_user_id;
			
			$user_id = apply_filters("bl_save_listing_user_id_overide", $user_id);
			
			if(!empty($user_id) && ($user_id != 0)){
				if(empty($post_id)){
					$post = array(
					  'post_author' => $user_id,
					  'post_content' => $content,
					  'post_status' => $default_status, 
					  'post_title' => $item_name,
					  'post_type' => "bepro_listings"
					);  
					//Create post
					$post_id = wp_insert_post( $post, $wp_error ); 
				}else if(!is_admin()){
					$wpdb->query("UPDATE ".$wpdb->prefix."posts SET post_content = '".$content."' WHERE ID=".$post_id);
				}
			
				if(empty($wp_error)){
					$post_data = get_post($post_id);
					//setup custom bepro listing post categories
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
					}
					
					//manage lat/lon
					if(is_numeric($_POST['lat']) && is_numeric($_POST['lon'])){
						$lat = $_POST['lat'];
						$lon = $_POST['lon'];
					}else{
						$latlon = get_bepro_lat_lon();
						$lat = $latlon["lat"];
						$lon = $latlon["lon"];
					}
					
					$post_data = $_POST;
					$post_data["post_id"] = $post_id;
					$post_data["lat"] = $lat;
					$post_data["lon"] = $lon;
					$post_data["cost"] = $cost;
					$listing = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id =".$post_id);
					
					if($listing){
						$result = bepro_update_post($post_data);
					}else{
						$result = bepro_add_post($post_data);
					}
					if(!$wp_error){
						$return_message = true;
					}else{
						$return_message = false;
					}
				}
			}else{
				$return_message = false;
			}
		}
		
		return $return_message;
	}
	
	function get_bepro_lat_lon(){
		$latlon = array();
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
			if($addr_search_1->results[0]->geometry->location){
				$latlon["lon"] = (string)$addr_search_1->results[0]->geometry->location->lng;
				$latlon["lat"] = (string)$addr_search_1->results[0]->geometry->location->lat;
			}
		}
		return $latlon;
	}
	
	function bepro_add_post($post){
		global $wpdb;
		do_action("bepro_listings_add_listing", $post);
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
			lon           = '".$wpdb->escape(strip_tags($post['lon']))."'");
	}
	
	function bepro_update_post($post){
		global $wpdb;
		do_action("bepro_listings_update_listing", $post);
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
			website       = '".$wpdb->escape(strip_tags($_POST['website']))."'
			WHERE post_id ='".$wpdb->escape(strip_tags($post['post_id']))."'");
	}
	
	//Create BePro Listings custom post type.
	function create_post_type() {
		$labels = array(
			'name' => _x('BePro Listings', 'post type general name'),
			'singular_name' => _x('Listing', 'post type singular name'),
			'add_new' => _x('Add New', 'Listing'),
			'add_new_item' => __('Add New Listing'),
			'edit_item' => __('Edit Listing'),
			'new_item' => __('New Listing'),
			'view_item' => __('View Listing'),
			'search_items' => __('Search Listing'),
			'not_found' =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon' => ''
		);
	 
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_icon' => plugins_url("images/blogs.png", __FILE__ ) ,
			'rewrite' => array("slug" => "listings", 'with_front' => false),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'author')
		  ); 
	 
		register_post_type( 'bepro_listings' , $args );
		register_taxonomy("bepro_listing_types", 
			"bepro_listings", 
			array('hierarchical' 			=> true,
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
				'rewrite' => array("slug" => "listing_types", 'with_front' => false))
			);	 
			register_taxonomy_for_object_type( 'bepro_listing_types', 'bepro_listings' );
			load_constants();
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
		$raw_cat = explode(",",$cat_to_check);
		$cats = (is_array($raw_cat))? $raw_cat:"";
		$is_int = array();
		foreach($cats as $cat){
			$is_int[] = is_numeric($cat)? true:false;
		}
		if(in_array(false,array_values($is_int))){
			return false;
		}else{
			return true;
		}
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

	function bepro_listings_description_tab(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/tab-description.php");
	}
	
	function bepro_listings_comments_tab(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/tab-comments.php");
	}
	
	function bepro_listings_description_panel(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/description.php");
	}
	
	function bepro_listings_comments_panel(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/comments.php");
	}



?>
