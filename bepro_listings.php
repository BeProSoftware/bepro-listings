<?php
/*
Plugin Name: BePro Listings
Plugin Script: bepro_listings.php
Plugin URI: http://www.beprosoftware.com/shop
Description: Best way to search front end submissions. Use optional base features like Galleries, payments & google maps. Ideal for various websites including Business directories, Classifieds, Product Catalogs, Portfolios & more. Put this shortcode [bl_all_in_one] in any page or post. Visit website for more
Version: 2.2.0017
License: GPL V3
Author: BePro Software Team
Author URI: http://www.beprosoftware.com


Copyright 2012 [Beyond Programs LTD.](http://www.beyondprograms.ca/)

Commercial users are requested to, but not required to contribute, promotion, 
know-how, or money to plug-in development or to www.beprosoftware.com. 

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

if ( !defined( 'ABSPATH' ) ) exit;

class Bepro_listings{

	/**
	 * Welcome to BePro Listings, part of the BePro Software collection.
	*/
	 
	//Start
	function __construct() {
		$data = get_option("bepro_listings");
		
		include(dirname( __FILE__ ) . '/bepro_listings_api.php');
		include(dirname( __FILE__ ) . '/bepro_listings_functions.php');
		include(dirname( __FILE__ ) . '/admin/bepro_listings_admin.php');
		include(dirname( __FILE__ ) . '/admin/meta/gallery_meta.php');
		include(dirname( __FILE__ ) . '/admin/bepro_listings_widgets.php');
		include(dirname( __FILE__ ) . '/bepro_listings_frontend.php');
		include(dirname( __FILE__ ) . '/bepro_listings_profile.php');
		
		add_action("announce_caller", "bpl_check_api_call", 10, 2);
		add_action('init', 'bepro_create_post_type' );
		
		add_action( 'admin_notices', "bpl_admin_rate" );
		add_action('init', array($this, 'check_flush_permalinks') );
		add_action('template_redirect', 'save_data_and_redirect' );
		add_action('admin_init', 'bepro_admin_init' );
		add_action('admin_head', 'bepro_admin_head' );
		add_action('admin_bar_menu', 'bpl_admin_tool_bar', 888 );
		add_action('admin_notices', 'bepro_listings_support_message' );
		add_action('admin_menu', 'bl_scd_register_menu' );
		add_action('wp_head', 'bepro_listings_wphead', 1);
		add_action('wp_footer', 'bepro_listings_javascript');
		add_action('admin_enqueue_scripts', 'bepro_listings_adminhead');
		add_action('admin_menu', 'bepro_listings_menus');
		add_action("widgets_init", "register_bepro_listings_widgets");
		add_action('post_updated', 'bepro_admin_save_details', 10, 3);
		add_action('delete_post', 'bepro_delete_post' );
		add_action('wp_ajax_save-widget', 'bepro_save_widget' );
		add_action("manage_posts_custom_column",  "bepro_listings_custom_columns");
		add_action( "plugins_loaded",  "bepro_listings_setup_category");
		add_action( 'bp_init', array( $this, "start_bp_addon") );
		
		//ajax
		add_action( 'wp_ajax_bepro_ajax_delete_post', 'bepro_ajax_delete_post' );
		add_action( 'wp_ajax_nopriv_bepro_ajax_delete_post', 'bepro_ajax_delete_post' );
		add_action( 'wp_ajax_bl_ajax_result_page', 'bl_ajax_result_page' );
		add_action( 'wp_ajax_nopriv_bl_ajax_result_page', 'bl_ajax_result_page' );
		add_action( 'wp_ajax_bl_ajax_frontend_update', 'bl_ajax_frontend_update' );
		add_action( 'wp_ajax_nopriv_bl_ajax_frontend_update', 'bl_ajax_frontend_update' );
		add_action( 'wp_ajax_bepro_listings_shortcode_dialog', 'bepro_listings_shortcode_dialog' );
		add_action( 'wp_ajax_nopriv_bepro_listings_shortcode_dialog', 'bepro_listings_shortcode_dialog' );
		add_action( 'wp_ajax_bl_create_demo_pages', 'bl_create_demo_pages' );
		add_action( 'wp_ajax_nopriv_bl_create_demo_pages', 'bl_create_demo_pages' );
		add_action( 'wp_ajax_bl_update_demo_options', 'bl_update_demo_options' );
		add_action( 'wp_ajax_nopriv_bl_update_demo_options', 'bl_update_demo_options' );
		add_action( 'wp_ajax_bl_update_demo_labels', 'bl_update_demo_labels' );
		add_action( 'wp_ajax_nopriv_bl_update_demo_labels', 'bl_update_demo_labels' );
		
		//wpmu
		add_action( 'wpmu_new_blog', 'bepro_new_blog', 10, 6);   
		
		//Templates
		add_action( 'bepro_listing_types_add_form_fields', 'bepro_listings_add_category_thumbnail_field' );
		add_action( 'bepro_listing_types_edit_form_fields', 'bepro_listings_edit_category_thumbnail_field', 10,2 );
		add_action( 'created_term', 'bepro_listings_category_thumbnail_field_save', 10,3 );
		add_action( 'edit_term', 'bepro_listings_category_thumbnail_field_save', 10,3 );
		add_action( 'bl_before_frontend_listings', 'bl_show_user_missing_payments', 10 );
		
		if(@$data["show_content"] == 2){
			add_action( 'bepro_listings_item_before_details', 'bepro_listings_description_panel', 11 );
		}else if(@$data["show_content"] == 1){
			add_action( 'bepro_listings_tabs', 'bepro_listings_description_tab', 10 );
			add_action( 'bepro_listings_tab_panels', 'bepro_listings_description_panel', 10 );
		}

		if(@$data["show_comments"] == 2){
			add_action( 'bepro_listings_item_before_details', 'bepro_listings_comments_panel', 20 );
		}else if(@$data["show_comments"] == 1){
			add_action( 'bepro_listings_tabs', 'bepro_listings_comments_tab', 20 );
			add_action( 'bepro_listings_tab_panels', 'bepro_listings_comments_panel', 20 );
		}
		if(@$data["show_geo"] == 2){
			add_action( 'bepro_listings_item_before_details', 'bepro_listings_page_geo', 21 );
		}else if(@$data["show_geo"] == 1){ 
			add_action( 'bepro_listings_tabs', 'bepro_listings_maps_tab', 22 );
			add_action( 'bepro_listings_tab_panels', 'bepro_listings_maps_panel', 22 );
		}
		
		if(@$data["show_details"] == 2){
			add_action( 'bepro_listings_item_before_details', 'bepro_listings_details_panel', 15);
		}else if(@$data["show_details"] == 1){
			add_filter( "bpl_show_page_tabs", "show_true");
			add_action( 'bepro_listings_tabs', 'bepro_listings_details_tab', 15 );
			add_action( 'bepro_listings_tab_panels', 'bepro_listings_details_panel', 15 );
		}
		
		
		//link in footer?
		if(@$data["footer_link"] == ("on" || 1)){
			add_action("wp_footer", "footer_message");
		}
		
		//payment features?
		add_action( 'plugins_loaded', array($this, 'check_load_payment') );
		
		//expiration features?
		if(@is_numeric($data["require_payment"]) && ($data["require_payment"] > 0)){
			add_filter("bepro_listings_search_join_clause", "bepro_search_load_orders");
			add_filter("bepro_listings_add_to_clause", "bepro_search_remove_expiring");
		}
		
		//building the page template
		add_action( ((!empty($data['bepro_listings_item_title_template']))? $data['bepro_listings_item_title_template']:'bepro_listings_item_title'), 'bepro_listings_item_title_template');
		add_action( ((!empty($data['bepro_listings_item_gallery_template']))? $data['bepro_listings_item_gallery_template']:'bepro_listings_item_gallery'), 'bepro_listings_item_gallery_template');
		add_action( ((!empty($data['bepro_listings_item_after_gallery_template']))? $data['bepro_listings_item_after_gallery_template']:'bepro_listings_item_after_gallery'), 'bepro_listings_item_after_gallery_template');
		//add_action( ((!empty($data['bepro_listings_item_details_template']))? $data['bepro_listings_item_details_template']:'bepro_listings_item_details'), 'bepro_listings_item_details_template');
		//add_action( ((!empty($data['bepro_listings_item_content_template']))? $data['bepro_listings_item_content_template']:'bepro_listings_item_content_info'), 'bepro_listings_item_content_template');
		
		//filters
		add_filter('manage_edit-bepro_listing_types_columns', 'bepro_edit_listing_types_column', 10, 3 );
		add_filter('manage_bepro_listing_types_custom_column', 'bepro_listing_types_column', 10, 3 );
		add_filter("manage_edit-bepro_listings_columns", "bepro_listings_edit_columns");
		if(empty($data["page_template"]) || ($data["page_template"] == 1)){
			add_filter('the_content', array( $this, 'bl_post_page_content'), 10);
			add_filter('post_thumbnail_html', array( $this, 'bl_post_page_thumbnail' ) );
			add_filter('the_title', array( $this, 'bl_post_page_title' ) );
			add_filter('comments_template', array( $this, 'bl_post_page_comments' ) );
		}else{
			add_filter('single_template', array( $this, 'post_page_single'), 10);
		}
		add_filter('bepro_listings_search_filter', array( $this, 'bepro_listings_search_filter_return'), 1);
		add_filter('bepro_listings_search_join_clause', array( $this, 'bepro_listings_search_join_clause_return'), 1);
		add_filter('bepro_listings_return_clause', array( $this, 'bepro_listings_return_clause_return'), 1);
		add_filter("bepro_listings_declare_for_map", "bepro_listings_vars_for_map");
		add_filter("bepro_listings_map_marker", "bepro_listings_generate_map_marker", 1, 3);
		add_filter("mce_external_plugins", "bl_tinymce_add_buttons");
		add_filter('mce_buttons', 'bl_tinymce_register_buttons');
		add_filter('wpmu_drop_tables', 'bepro_delete_blog' );
		add_filter('plugin_action_links_'. plugin_basename(__FILE__), 'bepro_listings_add_settings_link');
		
		//shortcodes
		add_shortcode("search_form", array( $this, "searchform"));
		add_shortcode("filter_form", array( $this, "search_filter_options"));
		add_shortcode("generate_map", "bepro_generate_map");
		add_shortcode("display_listings", "display_listings");
		add_shortcode("display_listing_categories", "display_listing_categories");
		add_shortcode("create_listing_form", "user_create_listing");
		add_shortcode("bl_all_in_one", "bl_all_in_one");
		add_shortcode("bl_my_listings", "bl_my_listings");
		add_shortcode("bl_search_filter", "search_filter_shortcode");
		
	}

	//Simple Search Listings Form
	function searchform($atts = array(), $echo_this=false){
		global $wpdb;
		extract(shortcode_atts(array(
			  'listing_page' => $wpdb->escape($_POST["listing_page"]),
			  'l_type' => $wpdb->escape($_POST["l_type"])
		 ), $atts));
		
		$data = get_option("bepro_listings");
		if(@$_REQUEST["l_type"] && is_numeric($_REQUEST["l_type"]) && bl_check_is_valid_cat($_REQUEST["l_type"])){
			$l_type = bl_check_is_valid_cat($_REQUEST["l_type"]);
		}
		$return_text = '
			<div class="search_listings bl_frontend_search_section">
				<form method="post" name="searchform" id="listingsearchform" action="'.$listing_page.'">
					<input type="hidden" name="filter_search" value="1">
					<input type="hidden" name="l_type" value="'.$l_type .'">
					<input type="hidden" name="distance" value="'.$_POST["distance"].'">
					<input type="hidden" name="min_date" value="'.$_POST["min_date"].'">
					<input type="hidden" name="max_date" value="'.$_POST["max_date"].'">
					<input type="hidden" name="listing_page" value="'.$listing_page.'">
					<input type="hidden" name="min_cost" value="'.$_POST["min_cost"].'">
					<input type="hidden" name="max_cost" value="'.$_POST["max_cost"].'">';	
		if(is_numeric($data["show_geo"]) && ($data["show_geo"] > 0))$return_text .= '
					<span class="blsearchwhere">
						<span class="searchlabel">'.__("Where", "bepro-listings").'</span>
						<input type="text" name="addr_search" value="'.$_POST["addr_search"].'">
					</span>';
		if(@$data["search_names"] != 4)$return_text .=	'
					<span class="blsearchname">
						<span class="searchlabel">'.__("Name", "bepro-listings").'</span>
						<input type="text" name="name_search" id="name_search" value="'.$_POST["name_search"].'">
					</span>';
					$return_text .=	'<span class="blsearchbuttons">
					<input type="submit" value="'.__("Search Listings", "bepro-listings").'">
										<a class="clear_search" href="'.get_bloginfo("url")."/".$listing_page.'"><button>'.__("Clear Search","bepro-listings").'</button></a>
					</span>					
				</form>
			</div>
		';
		if($echo_this){
			echo $return_text;
		}else{	
			return $return_text;
		}	
	}

	//Process Filter Search Criteria
	function listitems($atts) {
		global $wpdb;
		extract(shortcode_atts(array(
			  'l_type' => $wpdb->escape($_REQUEST["l_type"]),
			  'min_cost' => $wpdb->escape($_REQUEST["min_cost"]),
			  'max_cost' => $wpdb->escape($_REQUEST["max_cost"]),
			  'min_date' => $wpdb->escape($_REQUEST["min_date"]),
			  'max_date' => $wpdb->escape($_REQUEST["max_date"]),
			  'l_name' => $wpdb->escape($_REQUEST["name_search"]),
			  'l_city' => $wpdb->escape($_REQUEST["addr_search"]),
			  'wp_site' => $wpdb->escape($_POST["wp_site"]),
		 ), $atts));
		 
		 $data = get_option("bepro_listings");
		 
		 //manage search fields throughout dynamic pages and functionality
		 if(!empty($l_name) || !empty($l_city)){
			$_SESSION["name_search"] = $l_name;
			$_SESSION["addr_search"] = $l_city;
		 }else if((empty($l_name) && empty($l_city)) && !empty($_GET["page"])){
			$l_name = $_SESSION["name_search"];
			$l_city = $_SESSION["addr_search"];
		 }else if(empty($l_name) && empty($l_city) && empty($_GET["page"])){
			unset($_SESSION["name_search"]);
			unset($_SESSION["addr_search"]);
		 }
		 
		//Query Bepro Listing Types
		$returncaluse = "";
		if(!empty($l_type) && is_numeric($l_type)){
			$returncaluse  .= "AND t.term_id IN ($l_type)";
		}else if(!empty($l_type) && is_array($l_type)){
			$a_l_type = implode(",", $l_type);
			$returncaluse  .= "AND t.term_id IN ($a_l_type)";
		 }	 
		
		//Query google for lat/lon of users requested address
		$distance = (empty($_POST["distance"]))? $data["distance"]:addslashes(strip_tags($_POST["distance"]));
		$block_geo = apply_filters("bepro_block_geo_search", "");
		if(!empty($l_city) && isset($l_city) && empty($block_geo)){ 
			//newest edits Sep, 02, 2014
			$addresstofind = sprintf('http://maps.googleapis.com/maps/api/geocode/json?address=%s&output=csv&sensor=false',rawurlencode($l_city));
			$ch = curl_init();
			$timeout = 5; 
			curl_setopt ($ch, CURLOPT_URL, $addresstofind);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$_result = curl_exec($ch);
			curl_close($ch);
			
			$_result = json_decode($_result);
			$currentlat = (string)$_result->results[0]->geometry->location->lat; 
			$currentlon = (string)$_result->results[0]->geometry->location->lng;
			 // Variables for proximity query
			 $x = $currentlat;
			 $x2 = 'geo.lat';
			 $y = $currentlon;
			 $y2 = 'geo.lon';
			 // km = 6,371
			 $global_radius = (@$data["dist_measurement"] && ($data["dist_measurement"]==2))? "6371":"3958";
			 if($_result){
				$returncaluse .=  "AND (".$global_radius." * 3.1415926 * SQRT(({$y2} - {$y}) * ({$y2} - {$y}) + COS({$y2} / 57.29578) * COS({$y} / 57.29578) * ({$x2} - {$x}) * ({$x2} - {$x})) / 180) <= {$distance} AND geo.lat IS NOT NULL AND geo.lon IS NOT NULL";
			 }
	   }
	   
	   //Query BePro Listing Name 'LIKE' user query
	   if(!empty($l_name) && (@$data["search_names"] != 4)){
			$listing_table_name = (!empty($wp_site) && is_numeric($wp_site) && ($wp_site > 0))?
				$wpdb->prefix.$wp_site.'_bepro_listings':$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME;
			$search_names = ((@$data["search_names"] == 2) || (@$data["search_names"] == 3))? "first_name LIKE '%$l_name%' OR last_name LIKE '%$l_name%'":"";
			$search_title = ((@empty($data["search_names"])) || (@$data["search_names"] == 1) || (@$data["search_names"] == 3))?"post_title LIKE '%$l_name%'":"";
		
			if(@$data["search_names"] == 2){
				$where_name_search = $search_names;
			}else if(@$data["search_names"] == 3){
				$where_name_search = $search_title." OR ".$search_names;
			}else{
				$where_name_search = $search_title;
			}
			
			$check_avail = $wpdb->get_row("SELECT bl.* FROM ".$listing_table_name." as bl
			LEFT JOIN ".$wpdb->prefix."posts as posts ON posts.ID = bl.post_id
			WHERE $where_name_search LIMIT 1");
			
			//if($check_avail){
				
				//If distance, find listings 'LIKE' user supplied request within radius
				if(!empty($_POST["distance"])){
					$x = $check_avail->lat;
					$x2 = 'geo.lat';
					$y = $check_avail->lon;
					$y2 = 'geo.lon';
					$distance_clause = "AND (3958 * 3.1415926 * SQRT(({$y2} - {$y}) * ({$y2} - {$y}) + COS({$y2} / 57.29578) * COS({$y} / 57.29578) * ({$x2} - {$x}) * ({$x2} - {$x})) / $distance) <= {$distance}";
				}
				$returncaluse .= " AND $where_name_search $distance_clause AND geo.lat IS NOT NULL AND geo.lon IS NOT NULL";
			
	   }
	 
		//min/max cost setup 
	   if(isset($min_cost) && is_numeric($min_cost) && ($min_cost > 0)){
		$returncaluse.= " AND geo.cost > $min_cost";
	   }
	   if(isset($max_cost) && is_numeric($max_cost) && ($max_cost > 0)){
		$returncaluse.= " AND geo.cost < $max_cost";
	   }

	   //setup dates
	   if(!empty($min_date) && (is_numeric(str_replace("/","",$min_date)))){
		$returncaluse.= " AND geo.created >= '".date("y-m-d",strtotime($min_date))."'";
	   }
	   if(!empty($max_date) && (is_numeric(str_replace("/","",$max_date)))){
		$returncaluse.= " AND geo.created <= '".date("y-m-d",strtotime($max_date))."'";
	   }
	   
	   $returncaluse.= apply_filters("bepro_listings_return_clause","");
	   
		return $returncaluse;
	}

	function search_filter_options($atts = array(), $echo_this = false){
		global $wpdb;
		extract(shortcode_atts(array(
			 'bl_form_id' => $wpdb->escape($_POST["bl_form_id"]),
			 'listing_page' => $wpdb->escape($_POST["listing_page"])
		 ), $atts));
		
		if(empty($atts["bl_form_id"]))$atts["bl_form_id"] = $_POST["bl_form_id"];
		//get settings
		$data = get_option("bepro_listings");
		
		//Process user requested Bepro listing types 
		$types = "";
		if(!empty($_REQUEST["l_type"])){
			$l_type = $_REQUEST["l_type"];
			if(is_array($l_type)){
				foreach($l_type as $raw_t){
					$types[$raw_t] = 1; 
				}
			}else if(is_numeric($_REQUEST["l_type"])){
				$types[$_REQUEST["l_type"]]=1;
			}
		}	
		
		$cat_heading = $data["cat_heading"];
		
		$search_form_head = "<div class='filter_search_form bl_frontend_search_section'>
			<form id='filter_search_form' method='post' action='".$listing_page."'>
				<input type='hidden' name='name_search' value='".$_POST["name_search"]."'>
				<input type='hidden' name='listing_page' value='".$listing_page."'>
				<input type='hidden' name='addr_search' value='".$_POST["addr_search"]."'>
				<input type='hidden' name='order_dir' value='".$_POST["order_dir"]."'>
				<input type='hidden' name='filter_search' value='1'>
				<input type='hidden' name='bl_form_id' value='".$atts["bl_form_id"]."'>
				<table>";
			
		$search_form_fields = apply_filters("bepro_listings_search_filter_override",$atts);
		if(empty($search_form_fields) || ($search_form_fields == $atts)){
			$search_form_fields = "
						<tr>
							<td>
							<span class='label_sep'>".__($cat_heading, "bepro-listings")."</span><br />
							";
							
			
			

			$search_form_fields .= bl_build_cat_checkbox(0, "l_type[]", 0, $types);
			$search_form_fields .= '</td>
			</tr>';
			///////////////////////////////////////////////////////////////////////
			if(is_numeric($data["show_geo"]) && ($data["show_geo"] != 0))	
			$search_form_fields .= '
				<tr class="bl_distance_search_option"><td>
					'.__("Distance", "bepro-listings").': <select name="distance">
						<option value="">'.__("None","bepro-listings").'</option>
						<option value="10" '.(($_POST["distance"] == 10)||((empty($_POST["distance"])) && $data["distance"] == 10)? 'selected="selected"':"").'>'.__("10 miles","bepro-listings").'</option>
						<option value="50" '.(($_POST["distance"] == 50)||((empty($_POST["distance"])) && $data["distance"] == 50)? 'selected="selected"':"").'>'.__("50 miles","bepro-listings").'</option>
						<option value="150" '.((($_POST["distance"] == 150) || ((empty($_POST["distance"])) && $data["distance"] == 150))? 'selected="selected"':"").'>'.__("150 miles","bepro-listings").'</option>
						<option value="250" '.(($_POST["distance"] == 250)||((empty($_POST["distance"])) && $data["distance"] == 250)? 'selected="selected"':"").'>'.__("250 miles","bepro-listings").'</option>
						<option value="500" '.(($_POST["distance"] == 500)||((empty($_POST["distance"])) && $data["distance"] == 500)? 'selected="selected"':"").'>'.__("500 miles","bepro-listings").'</option>
						<option value="1000" '.(($_POST["distance"] == 1000)||((empty($_POST["distance"])) && $data["distance"] == 1000)? 'selected="selected"':"").'>'.__("1000 miles","bepro-listings").'</option>
					</select>
				</td></tr>';
				
			//min/max cost
			if(($data["show_cost"] == 1) || ($data["show_cost"] == "on"))
			$search_form_fields .= '
			<tr><td>
				<span class="label_sep">'.__("Price Range", "bepro-listings").'</span><span class="form_label">'.__("From", "bepro-listings").'</span><input class="input_text" type="text" name="min_cost" value="'.$_POST["min_cost"].'"><span class="form_label">'.__("To", "bepro-listings").'</span><input class="input_text" type="text" name="max_cost" value="'.$_POST["max_cost"].'">
			</td></tr>';
			
			if(($data["show_date"] == 1) || ($data["show_date"] == "on"))
			$search_form_fields .= '
			<tr><td>
				<span class="label_sep">'.__("Date Range", "bepro-listings").'</span><span class="form_label">'.__("From", "bepro-listings").'</span><input class="input_text" type="text" name="min_date" id="min_date" value="'.$_POST["min_date"].'"><span class="form_label">'.__("To", "bepro-listings").'</span><input class="input_text" type="text" name="max_date" id="max_date" value="'.$_POST["max_date"].'">
			</td></tr>';
			
			$check_form_fields = apply_filters("bepro_listings_search_filter", ""); 
			if($check_form_fields != $atts)
				$search_form_fields .= $check_form_fields;
		}
				
				$search_form_end = '
				<tr>
					<td>
						<input type="submit" class="form-submit" value="'.__("Search", "bepro-listings").'" id="edit-submit" name="find">
						<a class="clear_search" href="'.get_bloginfo("url")."/".$listing_page.'"><button>'.__("Clear","bepro-listings").'</button></a>
					</td>
				</tr>
			</table>
		</form></div>
		';
		if($echo_this){
			echo $search_form_head.$search_form_fields.$search_form_end;
		}else{	
			return $search_form_head.$search_form_fields.$search_form_end;
		}
	}
	
	//show listing on pages created for it by using file templates
	function post_page_single($content){
		if(get_post_type() == 'bepro_listings'){
			include(plugin_dir_path( __FILE__ )."templates/single-listing.php");
		}else{
			return $content;
		}
		exit;
	}
	
	//show listing on pages created for it by internal page setup
	function bl_post_page_content($content){
		global $post;
		if ( !is_singular( 'bepro_listings' ) ) {
			return $content;
		}
		remove_filter( 'the_content', array( $this, 'bl_post_page_content' ) );
		remove_filter( 'post_thumbnail_html', array( $this, 'bl_post_page_thumbnail' ) );
		remove_filter( 'the_title', array( $this, 'bl_post_page_title' ) );
		remove_filter( 'comments_template', array( $this, 'bl_post_page_comments' ) );
		
		if(get_post_type() === 'bepro_listings'){
			ob_start();
			include(plugin_dir_path( __FILE__ )."templates/internal-page.php");
			$content = ob_get_clean();
		}
		
		add_filter( 'the_content', array( $this, 'bl_post_page_content' ) );
		add_filter( 'post_thumbnail_html', array( $this, 'bl_post_page_thumbnail' ) );
		add_filter( 'the_title', array( $this, 'bl_post_page_title' ) );
		add_filter( 'comments_template', array( $this, 'bl_post_page_comments' ) );
		
		return $content;
	}
	
	function bl_post_page_comments($comment){
		if ( is_singular( 'bepro_listings' ) && in_the_loop() ) {
			return plugin_dir_path( __FILE__ )."templates/no_comments.php";
		}
		return  $comment;
	}
	
	function bl_post_page_thumbnail($thumb){
		if ( is_singular( 'bepro_listings' ) &&  in_the_loop() ) {
			return;
		}
		return $thumb;
	}
	function bl_post_page_title($title){
		if ( is_singular( 'bepro_listings' ) && in_the_loop() ) {
			return;
		}
		return $title;
	}
	
	//buddypress hook
	function start_bp_addon(){
		$data = get_option("bepro_listings");
		if((($data["buddypress"] == 1) || ($data["buddypress"] == "on")) && (!is_admin())){
			add_filter('bp_blogs_activity_new_post_action', 'bepro_listings_bp_activity_action', 1, 3);
			add_filter ( 'bp_blogs_record_post_post_types', 'bepro_listings_bp_activity_publish',1,1 );
			if(!@bp_is_my_profile())
				add_filter ( 'bl_change_my_list_template', 'show_visitor_profile', 15 , 2);
			include( dirname( __FILE__ ) . '/bepro-listings-bp.php' );
		}
	}
	
	
	//flush permalinks
	function flush_permalinks(){
		global $wp_rewrite;  
		$wp_rewrite->flush_rules(); 
	}
	
	//activate
	function bepro_listings_activate() {
		global $wpdb;  
		Bepro_listings::flush_permalinks();	
		
		//network admin activate?
		if (function_exists('is_multisite') && is_multisite() && is_network_admin()){
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach($blogids as $blogid_x){
				bepro_listings_install_table($blogid_x);
			}
		}else{
			bepro_listings_install_table();
		}
	}
	
	// check_upgrade
	function check_flush_permalinks(){
		$bepro_listings_version = get_option("bepro_listings_version");
		if(!empty($bepro_listings_version) && (BEPRO_LISTINGS_VERSION != $bepro_listings_version)){
			update_option('bepro_listings_version', BEPRO_LISTINGS_VERSION);
			$this->flush_permalinks();
		}else if(empty($bepro_listings_version)){
			update_option('bepro_listings_version', BEPRO_LISTINGS_VERSION);
			$this->flush_permalinks();
		}
	}
	
	function check_load_payment(){
		$data = get_option("bepro_listings");
		if((is_numeric($data["require_payment"])) && ($data["require_payment"] == 1) && (class_exists("Bepro_cart"))){
			add_action( 'bepro_listing_types_add_form_fields', 'bepro_listings_edit_category_fee_field');
			add_action( 'bepro_listing_types_edit_form_fields', 'bepro_listings_edit_category_fee_field', 11,2 );
			add_action( 'created_term', 'bepro_listings_category_fee_field_save', 11,3 );
			add_action( 'edit_term', 'bepro_listings_category_fee_field_save', 11,3 );
			add_action( 'bepro_cart_item_payment_complete', 'bepro_payment_completed', 10, 2);
		}else if((is_numeric($data["require_payment"])) &&  (class_exists("Bepro_cart"))){
			add_action( 'bepro_cart_item_payment_complete', 'bepro_payment_completed', 10, 2);
		}else if(!empty($data) && !class_exists("Bepro_cart")){
			$data["require_payment"] = "";
			update_option("bepro_listings", $data);
		}
	}
	
	function get_bpl_plugin_url(){
		return plugin_basename(__FILE__);
	}
	
	function bepro_listings_search_filter_return($i){
		return $i;
	}
	
	function bepro_listings_search_join_clause_return($i){
		return $i;
	}
	
	function bepro_listings_return_clause_return($i){
		return $i;
	}
}

$startup = new Bepro_listings();