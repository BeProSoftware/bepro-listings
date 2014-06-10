<?php
/*
Plugin Name: BePro Listings
Plugin Script: bepro_listings.php
Plugin URI: http://www.beprosoftware.com/shop
Description: Create any directory website (Business, classifieds, real estate, etc). Base features include, front end upload, gallery, buddypress integration, ajax search and filter. Use google maps and various listing templates to showcase info. Put this shortcode [bl_all_in_one] in any page or post. Visit website for other shortcodes
Version: 2.1.28
License: GPL V3
Author: BePro Software Team
Author URI: http://www.beprosoftware.com


Copyright 2012 [Beyond Programs LTD.](http://www.beyondprograms.com/)

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
		include(dirname( __FILE__ ) . '/bepro_listings_functions.php');
		include(dirname( __FILE__ ) . '/admin/bepro_listings_admin.php');
		include(dirname( __FILE__ ) . '/admin/bepro_listings_widgets.php');
		include(dirname( __FILE__ ) . '/bepro_listings_frontend.php');
		include(dirname( __FILE__ ) . '/bepro_listings_profile.php');
		
		add_action('init', 'bepro_create_post_type' );
		add_action('init', array($this, 'check_flush_permalinks') );
		add_action('admin_init', 'bepro_admin_init' );
		add_action('admin_head', 'bepro_admin_head' );
		add_action('wp_head', 'bepro_listings_wphead', 1);
		add_action('wp_footer', 'bepro_listings_javascript');
		add_action('admin_enqueue_scripts', 'bepro_listings_adminhead');
		add_action('admin_menu', 'bepro_listings_menus');
		add_action("widgets_init", array('bepro_widgets', 'register'));
		add_action('post_updated', 'bepro_admin_save_details' );
		add_action('delete_post', 'bepro_delete_post' );
		add_action('wp_ajax_save-widget', 'bepro_save_widget' );
		add_action("manage_posts_custom_column",  "bepro_listings_custom_columns");
		add_action( "plugins_loaded",  "bepro_listings_setup_category");
		add_action( 'bp_init', array( $this, "start_bp_addon") );
		add_action( 'wp_ajax_bepro_ajax_delete_post', 'bepro_ajax_delete_post' );
		add_action( 'wp_ajax_nopriv_bepro_ajax_delete_post', 'bepro_ajax_delete_post' );
		add_action( 'wp_ajax_bl_ajax_result_page', 'bl_ajax_result_page' );
		add_action( 'wp_ajax_nopriv_bl_ajax_result_page', 'bl_ajax_result_page' );
		add_action( 'wp_ajax_bl_ajax_frontend_update', 'bl_ajax_frontend_update' );
		add_action( 'wp_ajax_nopriv_bl_ajax_frontend_update', 'bl_ajax_frontend_update' );
		add_action( 'wpmu_new_blog', 'bepro_new_blog', 10, 6);   
		add_action( 'bepro_listing_types_add_form_fields', 'bepro_listings_add_category_thumbnail_field' );
		add_action( 'bepro_listing_types_edit_form_fields', 'bepro_listings_edit_category_thumbnail_field', 10,2 );
		add_action( 'created_term', 'bepro_listings_category_thumbnail_field_save', 10,3 );
		add_action( 'edit_term', 'bepro_listings_category_thumbnail_field_save', 10,3 );
		add_action( 'bepro_listings_tabs', 'bepro_listings_description_tab', 10 );
		add_action( 'bepro_listings_tabs', 'bepro_listings_comments_tab', 20 );
		add_action( 'bepro_listings_tabs', 'bepro_listings_maps_tab', 21 );
		add_action( 'bepro_listings_tab_panels', 'bepro_listings_description_panel', 10 );
		add_action( 'bepro_listings_tab_panels', 'bepro_listings_comments_panel', 20 );
		add_action( 'bepro_listings_tab_panels', 'bepro_listings_maps_panel', 21 );
		
		//item page template
		$data = get_option("bepro_listings");
		if($data["footer_link"] == ("on" || 1)){
			add_action("wp_footer", "footer_message");
		}
		add_action( ((!empty($data['bepro_listings_item_title_template']))? $data['bepro_listings_item_title_template']:'bepro_listings_item_title'), 'bepro_listings_item_title_template');
		add_action( ((!empty($data['bepro_listings_item_gallery_template']))? $data['bepro_listings_item_gallery_template']:'bepro_listings_item_gallery'), 'bepro_listings_item_gallery_template');
		add_action( ((!empty($data['bepro_listings_item_after_gallery_template']))? $data['bepro_listings_item_after_gallery_template']:'bepro_listings_item_after_gallery'), 'bepro_listings_item_after_gallery_template');
		add_action( ((!empty($data['bepro_listings_item_details_template']))? $data['bepro_listings_item_details_template']:'bepro_listings_item_details'), 'bepro_listings_item_details_template');
		add_action( ((!empty($data['bepro_listings_item_content_template']))? $data['bepro_listings_item_content_template']:'bepro_listings_item_content_info'), 'bepro_listings_item_content_template');
		
		//filters
		add_filter('manage_edit-bepro_listing_types_columns', 'bepro_edit_listing_types_column', 10, 3 );
		add_filter('manage_bepro_listing_types_custom_column', 'bepro_listing_types_column', 10, 3 );
		add_filter("manage_edit-bepro_listings_columns", "bepro_listings_edit_columns");
		add_filter('single_template', array( $this, 'post_page_single'), 15);
		add_filter('bepro_listings_search_filter', array( $this, 'bepro_listings_search_filter_return'), 1);
		add_filter('bepro_listings_search_join_clause', array( $this, 'bepro_listings_search_join_clause_return'), 1);
		add_filter('bepro_listings_return_clause', array( $this, 'bepro_listings_return_clause_return'), 1);
		add_filter("bepro_listings_declare_for_map", "bepro_listings_vars_for_map");
		add_filter("bepro_listings_map_marker", "bepro_listings_generate_map_marker", 1, 3);
		
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
			  'listing_page' => $wpdb->escape($_POST["listing_page"])
		 ), $atts));
		
		$data = get_option("bepro_listings");
		
		$return_text = '
			<div class="search_listings">
				<form method="post" name="searchform" id="listingsearchform" action="'.get_bloginfo("url")."/".$listing_page.'">
					<input type="hidden" name="filter_search" value="1">
					<input type="hidden" name="l_type" value="'.$_POST["l_type"].'">
					<input type="hidden" name="distance" value="'.$_POST["distance"].'">
					<input type="hidden" name="min_date" value="'.$_POST["min_date"].'">
					<input type="hidden" name="max_date" value="'.$_POST["max_date"].'">
					<input type="hidden" name="min_cost" value="'.$_POST["min_cost"].'">
					<input type="hidden" name="max_cost" value="'.$_POST["max_cost"].'">';	
		if($data["show_geo"] == (1||"on"))$return_text .= '
					<span class="blsearchwhere">
						<span class="searchlabel">'.__("Where", "bepro-listings").'</span>
						<input type="text" name="addr_search" value="'.$_POST["addr_search"].'">
					</span>';
		$return_text .=	'
					<span class="blsearchname">
						<span class="searchlabel">'.__("Name", "bepro-listings").'</span>
						<input type="text" name="name_search" id="name_search" value="'.$_POST["name_search"].'">
					</span>
					<span class="blsearchbuttons">
					<input type="submit" value="'.__("Search Listings", "bepro-listings").'">
										<a class="clear_search" href="'.get_bloginfo("url")."/".$listing_page.'"><button>Clear Search</button></a>
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
			  'min_cost' => $wpdb->escape($_POST["min_cost"]),
			  'max_cost' => $wpdb->escape($_POST["max_cost"]),
			  'min_date' => $wpdb->escape($_POST["min_date"]),
			  'max_date' => $wpdb->escape($_POST["max_date"]),
			  'l_name' => $wpdb->escape($_POST["name_search"]),
			  'l_city' => $wpdb->escape($_POST["addr_search"]),
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
		
		if(!empty($atts["l_type"]) && !is_array($l_type)){
			$returncaluse  .= "AND t.term_id IN ($l_type)";
		}else if(!empty($l_type) && (is_numeric($l_type) || is_array($l_type))){
			if(is_array($l_type))$l_type = implode(",", $l_type);
			$returncaluse  .= "AND t.term_id IN ($l_type)";
		 }	 
		
		//Query google for lat/lon of users requested address
		$distance = (empty($_POST["distance"]))? $data["distance"]:$_POST["distance"];
		if(!empty($l_city) && isset($l_city)){ 
			//newest edits aug, 12, 2012
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
			 
			 if($_result){
				$returncaluse =  "AND (3958 * 3.1415926 * SQRT(({$y2} - {$y}) * ({$y2} - {$y}) + COS({$y2} / 57.29578) * COS({$y} / 57.29578) * ({$x2} - {$x}) * ({$x2} - {$x})) / 180) <= {$distance} AND geo.lat IS NOT NULL AND geo.lon IS NOT NULL";
			 }
	   }
	   
	   //Query BePro Listing Name 'LIKE' user query
	   if(!empty($l_name)){
			$listing_table_name = (!empty($wp_site) && is_numeric($wp_site) && ($wp_site > 0))?
				$wpdb->prefix.$wp_site.'_bepro_listings':$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME;
	   
			$check_avail = $wpdb->get_row("SELECT bl.* FROM ".$listing_table_name." as bl
			LEFT JOIN ".$wpdb->prefix."posts as posts ON posts.ID = bl.post_id
			WHERE post_title LIKE '%$l_name%' LIMIT 1");
			 
			if($check_avail){
				//If distance, find listings 'LIKE' user supplied request within radius
				if(!empty($_POST["distance"])){
					$x = $check_avail->lat;
					$x2 = 'geo.lat';
					$y = $check_avail->lon;
					$y2 = 'geo.lon';
					$distance_clause = "AND (3958 * 3.1415926 * SQRT(({$y2} - {$y}) * ({$y2} - {$y}) + COS({$y2} / 57.29578) * COS({$y} / 57.29578) * ({$x2} - {$x}) * ({$x2} - {$x})) / $distance) <= {$distance}";
				}
				$returncaluse .= " AND posts.post_title LIKE '%$l_name%' $distance_clause AND geo.lat IS NOT NULL AND geo.lon IS NOT NULL";
			}
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
			  'listing_page' => $wpdb->escape($_POST["listing_page"])
		 ), $atts));
		
		//get settings
		$data = get_option("bepro_listings");
		
		//Process user requested Bepro listing types 
		if(!empty($_POST["l_type"])){
			$l_type = $_POST["l_type"];
			foreach($l_type as $raw_t){
				$types[$raw_t] = 1; 
			}
		}	
		
		$cat_heading = (!empty($_REQUEST["l_type"]) && (is_numeric($_REQUEST["l_type"]) || is_array($_REQUEST["l_type"])))? $data["cat_empty"]:$data["cat_heading"];
		
		$search_form = "<div class='filter_search_form'>
			<form id='filter_search_form' method='post' action='".$listing_page."'>
				<input type='hidden' name='name_search' value='".$_POST["name_search"]."'>
				<input type='hidden' name='addr_search' value='".$_POST["addr_search"]."'>
				<input type='hidden' name='filter_search' value='1'>
				<table>
					<tr>
						<td>
						<span class='searchlabel'>".__($cat_heading, "bepro-listings")."</span><br />
						";
			$options = listing_types();
			foreach($options as $opt){
				$checked = (isset($types[$opt->term_id]))? "checked='checked'":"";
				$search_form .= '<input type="checkbox" name="l_type[]" value="'.$opt->term_id.'" '.$checked.'/><span class="searchcheckbox">'.$opt->name.'</span><br />';
			}

			$search_form .= '</td>
			</tr>';
			///////////////////////////////////////////////////////////////////////
			if($data["show_geo"] == (1||"on"))	
			$search_form .= '
				<tr><td>
					'.__("Distance", "bepro-listings").': <select name="distance">
						<option value="">None</option>
						<option value="50" '.(($_POST["distance"] == 50)? 'selected="selected"':"").'>50 miles</option>
						<option value="150" '.((($_POST["distance"] == 150) || empty($_POST["distance"]))? 'selected="selected"':"").'>150 miles</option>
						<option value="250" '.(($_POST["distance"] == 250)? 'selected="selected"':"").'>250 miles</option>
						<option value="500" '.(($_POST["distance"] == 500)? 'selected="selected"':"").'>500 miles</option>
						<option value="1000" '.(($_POST["distance"] == 1000)? 'selected="selected"':"").'>1000 miles</option>
					</select>
				</td></tr>';
				
				//min/max cost
				if($data["show_cost"] == (1||"on"))
				$search_form .= '
				<tr><td>
					<span class="label_sep">'.__("Price Range", "bepro-listings").'</span><span class="form_label">'.__("From", "bepro-listings").'</span><input class="input_text" type="text" name="min_cost" value="'.$_POST["min_cost"].'"><span class="form_label">'.__("To", "bepro-listings").'</span><input class="input_text" type="text" name="max_cost" value="'.$_POST["max_cost"].'">
				</td></tr>';
				
				if($data["show_date"] == (1))
				$search_form .= '
				<tr><td>
					<span class="label_sep">'.__("Date Range", "bepro-listings").'</span><span class="form_label">'.__("From", "bepro-listings").'</span><input class="input_text" type="text" name="min_date" id="min_date" value="'.$_POST["min_date"].'"><span class="form_label">'.__("To", "bepro-listings").'</span><input class="input_text" type="text" name="max_date" id="max_date" value="'.$_POST["max_date"].'">
				</td></tr>';
				
				$search_form .= apply_filters("bepro_listings_search_filter","");
				
				$search_form .= '
				<tr>
					<td>
						<input type="submit" class="form-submit" value="'.__("Search", "bepro-listings").'" id="edit-submit" name="find">
						<a class="clear_search" href="'.get_bloginfo("url")."/".$listing_page.'"><button>Clear</button></a>
					</td>
				</tr>
			</table>
		</form></div>
		';
		if($echo_this){
			echo $search_form;
		}else{	
			return $search_form;
		}
	}
	
	//show listing on pages created for it
	function post_page_single($content){
		if(get_post_type() == 'bepro_listings'){
			include(plugin_dir_path( __FILE__ )."templates/single-listing.php");
		}else{
			return $content;
		}
		exit;
	}
	
	//buddypress hook
	function start_bp_addon(){
		$data = get_option("bepro_listings");
		if($data["buddypress"] == (1||"on")){
			add_filter('bp_blogs_activity_new_post_action', 'bepro_listings_bp_activity_action', 1, 3);
			add_filter ( 'bp_blogs_record_post_post_types', 'bepro_listings_bp_activity_publish',1,1 );
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
		
		if (function_exists('is_multisite') && is_multisite()){ 
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
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