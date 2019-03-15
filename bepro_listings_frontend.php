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
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	//ajax update front end
	function bl_ajax_frontend_update(){
		$_REQUEST["l_type"] = (!empty($_REQUEST["l_type"]) && !is_numeric($_REQUEST["l_type"]) && !is_array($_REQUEST["l_type"]))? explode(",", $_REQUEST["l_type"]):$_REQUEST["l_type"];
		$map = bepro_generate_map();
		$cat = display_listing_categories();
		$listings = display_listings();
		$filter = Bepro_listings::search_filter_options();
		$short_filter = search_filter_shortcode();
		$search = Bepro_listings::searchform();
		echo json_encode(array("map" =>$map,"cat" =>$cat,"listings" =>$listings,"filter" =>$filter,"short_filter" =>$short_filter,"search" =>$search ));
		exit;
	}
	
	function bl_ajax_result_page(){
		$listings = bl_ajax_get_listings();
		$map = bepro_generate_map();
		$cat = display_listing_categories();
		$filter = Bepro_listings::search_filter_options();
		$search = Bepro_listings::searchform();
		echo json_encode(array("map" =>$map,"cat" =>$cat,"listings" =>$listings,"filter" =>$filter,"search" =>$search ));
		exit;
	}
	
	function bl_ajax_get_listings(){
		/*
		ob_start();
		include(plugin_dir_path( __FILE__ )."templates/ajax-single-listing.php");
		return trim( ob_get_clean() );	
		*/
		
		$post_id = is_numeric($_POST["bl_post_id"])? $_POST["bl_post_id"]:false;
		if($post_id){
			$the_query = new WP_Query( array("post_type" => "bepro_listings", 'p' => $post_id) );
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				ob_start();
				echo '<div id="shortcode_list">';
				echo result_page_back_button();
				include(plugin_dir_path( __FILE__ )."/templates/content-single-listing.php");
				$data = ob_get_contents();
				ob_clean();
			}
			$post = get_post($post_id);
			$_POST["l_name"] = $post->post_title;
			$cats = get_terms( array('bepro_listing_types'), array('post_id' => $post_id));
			$cats = wp_get_post_terms( $post_id , "bepro_listing_types", array("fields" => "ids"));
			$term = array();
			foreach($cats as $cat){
				$term[] = $cat;
			}
			$_POST["l_type"] = $term;
			$_REQUEST["l_type"] = $term;
		}else{
			$data = "something went wrong";
		}
		return $data;
	}
	
	function result_page_back_button(){
		$button = '
			<form method="post" id="result_page_back_button">
				<input type="hidden" name="filter_search" value="1">
				<input type="hidden" name="l_type[]" value="'.(bl_check_is_valid_cat($_REQUEST["l_type"])? bl_check_is_valid_cat($_REQUEST["l_type"]):"").'">
				<input type="hidden" name="type" value="'.$_POST["type"].'">
				<input type="hidden" name="distance" value="'.$_POST["distance"].'">
				<input type="hidden" name="min_date" value="'.$_POST["min_date"].'">
				<input type="hidden" name="max_date" value="'.$_POST["max_date"].'">
				<input type="hidden" name="min_cost" value="'.$_POST["min_cost"].'">
				<input type="hidden" name="max_cost" value="'.$_POST["max_cost"].'">
				<input type="hidden" name="addr_search" value="'.$_POST["addr_search"].'">
				<input type="hidden" name="name_search" id="name_search" value="'.$_POST["name_search"].'">
				<input type="submit" value="Return To Results">
			</form>
		';
		return $button;
	}
	
	//Create map, used by shortcode and widget
	function bl_all_in_one($atts = array(), $echo_this = false){
		global $wpdb;
		
		$return_text = "";
		extract(shortcode_atts(array(
			  'l_type' => esc_sql(@$_REQUEST["l_type"])
		 ), $atts));
		 
		if($l_type == "a1"){ 
			$return_text .= do_shortcode("[search_form]"); 
			$return_text .=  do_shortcode("[generate_map size=1]"); 
			$return_text .=  do_shortcode("[display_listing_categories]"); 
			$return_text .=  do_shortcode("[display_listings]"); 
		}elseif($l_type == "a2"){ 
			$return_text .=  do_shortcode("[search_form]"); 
			$return_text .=  do_shortcode("[generate_map size=2]"); 
			$return_text .=  do_shortcode("[display_listing_categories]"); 
			$return_text .=  do_shortcode("[display_listings]"); 
		}elseif($l_type == "a3"){ 
			$return_text .=  do_shortcode("[search_form]"); 
			$return_text .=  do_shortcode("[generate_map size=3]"); 
			$return_text .=  do_shortcode("[display_listing_categories ctype=1]"); 
			$return_text .=  do_shortcode("[display_listings]"); 
		}elseif($l_type == "a4"){ 
			$return_text .=  do_shortcode("[search_form]"); 
			$return_text .=  do_shortcode("[bl_search_filter]"); 
			$return_text .=  do_shortcode("[display_listing_categories]"); 
			$return_text .=  do_shortcode("[display_listings]"); 
		}else{
			$return_text .=  do_shortcode("[search_form]"); 
			$return_text .=  do_shortcode("[generate_map size=4]"); 
			$return_text .=  do_shortcode("[display_listing_categories]"); 
			$return_text .=  do_shortcode("[display_listings]"); 
		}
		
		if($echo_this){
			echo $return_text;
		}else{	
			return $return_text;
		}
	}
	
	//Create map, used by shortcode and widget
	function bepro_generate_map($atts = array(), $echo_this = false){
		global $wpdb;
		
		extract(shortcode_atts(array(
			  'pop_up' => esc_sql(@$_POST["pop_up"]),
			  'size' => esc_sql(@$_POST["size"]),
			  'l_type' => esc_sql(@$_REQUEST["l_type"]),
			  'show_paging' => esc_sql(@$_POST["show_paging"]),
			  'map_id' => esc_sql(@$_POST["map_id"]),
			  'limit' => esc_sql(@$_REQUEST["limit"]),
			  'bl_form_id' => esc_sql(@$_REQUEST["bl_form_id"]),
			  'bl_post_id' => esc_sql(@$_POST["bl_post_id"])
		 ), $atts));
		 
		$map_cities = "";
		
		//form builder integration
		$_REQUEST["bl_form_id"] = $bl_form_id;
		 
		//Setup data
		$data = get_option("bepro_listings");
		$num_results = (empty($limit)|| !is_numeric($limit))? $data["num_listings"]:$limit; 
		$size = empty($size)? 1:$size;
		
		//bail if geo features aren't turned on
		if(empty($data["show_geo"])) return;
		
		//check map id
		$map_id = empty($map_id)? "map":$map_id;
		
		//Get Listing Results
		$findings = process_listings_results($show_paging, $num_results, $l_type, $bl_post_id);				
		$raw_results = $findings[0];
		
		//Setup Listing Markers
		$counter = 0;
		foreach($raw_results as $result){
			if (!empty($result->lat) && !empty($result->lon)){
				$map_cities .= apply_filters("bepro_listings_map_marker","", $result, $counter);
				$currlat = $result->lat;
				$currlon = $result->lon;
				if($pop_up){
					$map_cities .= bepro_listings_detailed_infowindow($raw_results, $currlat, $currlon, $counter);
				}else{
					$map_cities .= bepro_listings_simple_infowindow($raw_results, $currlat, $currlon, $counter);
				}
			}
			$counter++;
		}
		$declare_for_map = apply_filters("bepro_listings_declare_for_map", '');
		$default_zoom = empty($data["map_zoom"])? 10:$data["map_zoom"];
		$zoom = (empty($currlat) || empty($currlon))? 2:$default_zoom;
		//javascript initialization of the map
		$map = "<script type='text/javascript'>
			jQuery(document).ready(function(){
				var currentlat;
				var currentlon;
				markers = new Array();
				positions = new Array();
				var currentlat = '$currlat';
				var currentlon = '$currlon';
				var openwindow = false;
				var latlng = new google.maps.LatLng(currentlat, currentlon);
				icon_1 = new google.maps.MarkerImage('".plugins_url("images/icons/icon_1.png", __FILE__)."');
				var myOptions = {
					zoom:$zoom,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				$declare_for_map
				map = new google.maps.Map(document.getElementById('".$map_id."'), myOptions);
				
				$map_cities
				//cluster markers
				if(markers.length > 1){
					var markerCluster = new MarkerClusterer(map, markers, {maxZoom: 13,zoomOnClick: true});
					//makes sure map view fits all markers
					 latlngbounds = new google.maps.LatLngBounds();
					for ( var i = 0; i < positions.length; i++ ) {
						latlngbounds.extend( positions[ i ] );
					}
					map.fitBounds( latlngbounds );
				}
			});
		</script>
		<div id='shortcode_map' class='bl_frontend_search_section'>
		<div id='bl_size' class='bl_shortcode_selected'>$size</div>
		<div id='bl_pop_up' class='bl_shortcode_selected'>$pop_up</div>
		<div id='".$map_id."' class='result_map_$size'></div>
		</div>";
		if($echo_this){
			echo $map;
		}else{	
			return $map;
		}
	}
	
	function bepro_listings_vars_for_map($vars){
		return $vars;
	}
	
	function bepro_listings_simple_infowindow($results, $currlat, $currlon, $counter){
		$data = get_option("bepro_listings");
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		$marker_content = "";
		//find all markers with the exact same location
		//$result = "";
		foreach($results as $result){
			if(($result->lat == $currlat)&&($result->lon == $currlon))
				$marker_content .= "<div class=\"marker_content\"><span class=\"marker_detais\">'.addslashes($result->post_title).'</span></div>";
		}
		
		$return_txt = 'var infowindow_'.$counter.' = new google.maps.InfoWindow( { content: '.$marker_content.', size: new google.maps.Size(50,50)});
				  google.maps.event.addListener(marker_'.$counter.', "mouseover", function() {
					if(openwindow){
						eval(openwindow).close();
					}
					infowindow_'.$counter.'.open(map,marker_'.$counter.');
					openwindow = infowindow_'.$counter.';
				  });';
				 

		//figure out link
		$permalink = get_permalink( $result->post_id );
		if($target == 2){
			$link = 'var win=window.open("'.$permalink.'", "_blank"); win.focus();';
		}elseif($target == 3){
			$link = 'bl_ajax_get_page("'.$result->post_id.'")';	
		}elseif($target == 5){
			$link = '';	
		}else{
			$link = 'window.location.href = "'.$permalink.'"';
		}		  
				  
		if(!empty($link))
			$return_txt = '
				  google.maps.event.addListener(marker_'.$counter.', "click", function() {
					'.$link.';
				  });
			';
			
		return $return_txt;
	}
	
	function bepro_listings_detailed_infowindow($results, $currlat, $currlon, $counter){
		$data = get_option("bepro_listings");
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		$marker_content = "";
		
		foreach($results as $result){
			if(($result->lat == $currlat)&&($result->lon == $currlon)){
				$thumbnail = get_the_post_thumbnail($result->post_id, 'thumbnail'); 
				$default_img = (!empty($thumbnail))? $thumbnail:'<img src="'.$data["default_image"].'"/>';
				
				if($target == 2){
					$link = "<a href=\"http://".urlencode($result->website)."\" target=\"_blank\">".__("Visit Website","bepro-listings")."</a>";
				}elseif($target == 3){
					$link =  "<a href=\"http://".urlencode($result->website)."\" class='bl_ajax_result_page' post_id=\"".$result->post_id."\">Visit Website</a>";
				}elseif($target == 5){
					$link = "";
				}else{
					$link ="<a href=\"http://".urlencode($result->website)."\" post_id=\"".$result->post_id."\">Visit Website</a>";
				}
				
				$format_address = bpl_format_address($result);
				
				$marker_content .= "<div class=\"marker_content\"><span class=\"marker_title\">".addslashes(substr($result->post_title,0,18))."</span><span class=\"marker_img\">".$default_img."</span><span class=\"marker_detais\"><span class=\"addr\">".$format_address."</span><span class=\"cont\">".substr($result->post_content,0,55)."</span></span>".(empty($link)?"":"<span class=\"marker_links\">".$link."<br /><a href=\"".get_permalink($result->post_id)."\">".__("View Listing","bepro-listings")."</a></span>")."</div>";
			}
		}
		
		
		return "var infowindow_".$counter." = new google.maps.InfoWindow( { content: '".$marker_content."', size: new google.maps.Size(50,50)});
				  google.maps.event.addListener(marker_".$counter.", \"click\", function() {
					if(openwindow){
						eval(openwindow).close();
					}
					infowindow_".$counter.".open(map,marker_".$counter.");
					openwindow = infowindow_".$counter.";
				  });
			";
	}
	
	function bepro_listings_generate_map_marker($var, $result, $counter){
		return 'position = new google.maps.LatLng('.@$result->lat.','.@$result->lon.');
					var marker_'.$counter.' = new google.maps.Marker({
						position: position,
						icon:icon_1,
						map: map,
						clickable: true,
						title: "'.@$result->item_name.'",
					});
					
					markers.push(marker_'.$counter.');
					positions.push(position);	
				';
	}
	
	//Show categories Called from shortcode. Also filter by search criteria affecting categories.
	/*
		* cat - Set the category parent we are filtering by 
		* ctype - Which layout template to use
	*/
	function display_listing_categories($atts = array(), $echo_this = false){
		global $wpdb;
		$data = get_option("bepro_listings");
		$no_img = plugins_url("images/no_img.jpg", __FILE__ );
		extract(shortcode_atts(array(
			  'url_input' => esc_sql(@$_POST["url"]),
			  'ctype' => esc_sql(@$_POST["ctype"]),
			  'cat' => esc_sql(@$_POST["cat"])
		 ), $atts));
		
		if(!@$_REQUEST["l_type"])$_REQUEST["l_type"] = "";
		
		$cat_heading = (!empty($_REQUEST["l_type"]) && (is_numeric($_REQUEST["l_type"]) || is_array($_REQUEST["l_type"])))? $data["cat_empty"]:$data["cat_heading"];
		
		$parent = (!empty($cat) && is_numeric($cat))? $cat:0;
		
		$query_args = array('orderby'=>'count', "parent" => $parent);
		if(!empty($_REQUEST["l_type"]) && (is_numeric($_REQUEST["l_type"]) || is_array($_REQUEST["l_type"])) && (@$_REQUEST["l_type"][0] != 0) ){
			$query_args['include'] = $_REQUEST["l_type"];
		}
		
		$categories = get_terms( array('bepro_listing_types'), $query_args);
		
		$cat_list = "<div id='shortcode_cat' class='cat_lists bl_frontend_search_section'>";
		
		if($categories && (count($categories) > 0)){
			//If only one category was selected then show its name in the heading
			if((is_numeric($_REQUEST["l_type"]) || (is_array($_REQUEST["l_type"]) && (@$_REQUEST["l_type"][0] != 0) && (count($_REQUEST["l_type"]) == 1))) && !empty($categories)){
				$cat = $categories[0];
				$cat_list .= "<h3>".$cat->name." ".$data["cat_singular"]."</h3>";
				foreach($categories as $cat){
					$cat_list.= bepro_cat_templates($cat, $url_input, $ctype);
				}
			}else{
				$cat_heading = $data["cat_heading"];
				$cat_list .= "<h3>".__($cat_heading,"bepro_listings")."</h3>";
				foreach($categories as $cat){
					$cat_list.= bepro_cat_templates($cat, $url_input, $ctype);
				}
			}
		}else{
			if(sizeof($_REQUEST["l_type"]) == 1){
				$selected_cat = get_term($_REQUEST["l_type"][0], 'bepro_listing_types');
				$cat_list .= "<h3>".$selected_cat->name." ".$data["cat_singular"]."</h3>";
			}else{
				$cat_list .= "<h3>".count($_REQUEST["l_type"])." ".$data["cat_heading"]."</h3>";
			}
			$cat_list .= "<div class='cat_list_no_item'> ".$cat_heading."</div>";
		}
		$cat_list.= "</div>";
		
		$cat_list.= "<div id='bl_cat' class='bl_shortcode_selected'>$parent</div><div id='bl_ctype' class='bl_shortcode_selected'>$ctype</div>";
		if($echo_this){
			echo $cat_list;
		}else{	
			return $cat_list;
		}
	}
	
	/*
	//
	//category templates
	//
	*/	
	
	function bepro_cat_templates($cat, $url_input, $template = 0){
		$no_img = plugins_url("images/no_img.jpg", __FILE__ );
		$url = $url_input."?filter_search=1&l_type=".$cat->term_id;
		$cat_list = "";
		if($template == 1){
			$thumb_id = get_bepro_listings_term_meta( $cat->term_id, "thumbnail_id");
			$img = empty($thumb_id)? $no_img:wp_get_attachment_url( $thumb_id );
			$cat_list .= "<div class='cat_list_item'>
			<div class='cat_img'><a href='".$url."'><img src='".$img."' /></a></div>
			<div class='cat_title'><a href='".$url."'>".$cat->name."&nbsp;(".$cat->count.")</a></div>
			</div>
			";
		}else if($template == 2){
			$thumb_id = get_bepro_listings_term_meta( $cat->term_id, "thumbnail_id");
			$img = empty($thumb_id)? $no_img:wp_get_attachment_url( $thumb_id );
			$cat_list .= "<div class='cat_list_item'>
			<div class='cat_img'><a href='".$url."'><img src='".$img."' /></a></div>
			<div class='cat_title'><a href='".$url."'>".$cat->name."&nbsp;(".$cat->count.")</a></div>
			<div class='cat_desc'>".$cat->description."</div>
			</div>
			";
		}else if($template == 3){
			$sub_categories = get_terms( 'bepro_listing_types', 'orderby=count&hide_empty=0&parent='.$cat->term_id );
			$cat_list = "<div class='cat_list_item'>
			<div class='cat_title cat_head'><a href='".$url."'>".$cat->name."&nbsp;(".$cat->count.")</a></div>
			<div class='cat_desc'>".$cat->description."</div>
			";
			if(!empty($sub_categories)){
				$cat_list .="<ul>";
				foreach($sub_categories as $sub_cat){
					$sub_url = $url_input."?filter_search=1&l_type=".$sub_cat->term_id;
					$cat_list .= "<li><a href='".$sub_url."'>".$sub_cat->name."&nbsp;(".$sub_cat->count.")</a></li>";
				}
				$cat_list .="</ul>";
			}
			$cat_list .= "</div>";
		}else{
			$sub_categories = get_terms( 'bepro_listing_types', 'orderby=count&hide_empty=0&parent='.$cat->term_id );
			$cat_list = "<div class='cat_list_item'>
			<div class='cat_title cat_head'><a href='".$url."'>".$cat->name."&nbsp;(".$cat->count.")</a></div>";
			if(!empty($sub_categories)){
				$cat_list .="<ul>";
				foreach($sub_categories as $sub_cat){
					$sub_url = $base_url."?filter_search=1&l_type=".$sub_cat->term_id;
					$cat_list .= "<li><a href='".$sub_url."'>".$sub_cat->name."&nbsp;(".$sub_cat->count.")</a></li>";
				}
				$cat_list .="</ul>";
			}
			$cat_list .= "</div>";
		}
		
		return $cat_list;
	}
	
	
	//Show listings Called from shortcode or ajax. type is the template to use. l_type is the category to filter by.
	function display_listings($atts = array(), $echo_this = false){
		global $wpdb;

		extract(shortcode_atts(array(
			  'type' => esc_sql(@$_POST["type"]),
			  'l_type' => esc_sql(@$_REQUEST["l_type"]),
			  'ex_type' => esc_sql(@$_REQUEST["ex_type"]),
			  'l_featured' => esc_sql(@$_POST["l_featured"]),
			  'order_dir' => esc_sql(@$_POST["order_dir"]),
			  'order_by' => esc_sql(@$_POST["order_by"]),
			  'l_ids' => esc_sql(@$_REQUEST["l_ids"]),
			  'limit' => esc_sql(@$_REQUEST["limit"]),
			  'bl_form_id' => esc_sql(@$_REQUEST["bl_form_id"]),
			  'bpl_state' => esc_sql(@$_REQUEST["bpl_state"]),
			  'no_filter' => esc_sql(@$_REQUEST["no_filter"]),
			  'show_paging' => esc_sql(@$_POST["show_paging"])
		 ), $atts));
		 
		//form builder and origami integration
		$_REQUEST["bl_form_id"] = $bl_form_id;
		$_POST["l_featured"] = $l_featured;
		
		//experiment
		$_REQUEST["bpl_state"] = $bpl_state;
		
		
		$data = get_option("bepro_listings");
		$num_results = (empty($limit)|| !is_numeric($limit))? $data["num_listings"]:$limit; 
		$type = empty($type)? 1:$type;
		
		//find which types are allowed
		if(!empty($ex_type) && bl_check_is_valid_cat($ex_type)){
			$raw_l_types = get_terms( array('bepro_listing_types'), array('exclude'=> explode(",",$ex_type)));
			foreach($raw_l_types as $raw_l_type){
				$l_type[] = $raw_l_type->term_id;
			}
			if(!empty($l_type) && is_array($l_type))
				$l_type = implode(",",$l_type);
				
		}
		
		
		//make presumption to randomize featured listings
		if(empty($order_by) && !empty($l_featured)){
			$order_by = 2;
		}else if(empty($order_by)){
			$order_by = 1;
		}
		if(empty($order_dir))$order_dir = 1;
		
		$findings = process_listings_results($show_paging, $num_results, $l_type, $l_ids, $order_by,$order_dir,$l_featured);				
		$raw_results = $findings[0];				
		
		//define variables		
		$results = "";	
		$hidden_limit_text = "";
		$show_bl_type = "";
		$bl_form_id_div  = "";
		$l_featured_id = "";
		
		//Create the GUI layout for the listings
		if(empty($raw_results) || is_null($raw_results)){
			$results = "<p>".__("your criteria returned no results.")."</p>";
			$results = apply_filters("bl_search_no_results", $results);
		}else{
			//item listing template
			$list_templates = isset($data['bepro_listings_list_template_'.$type])? $data['bepro_listings_list_template_'.$type]: $data['bepro_listings_list_template_1'];
			foreach($list_templates as $key => $val){
				if($key == "style")
					$results .="<link href='".$val."' rel='stylesheet' type='text/css' />";
				else if($key == "template_file")
					$results .="";
				else
					add_action($key, $val, 10, 2);
			}
			
			$previous = array();
			$check = apply_filters("bl_start_bepro_listing_template", $type);
			$results .= @(is_numeric($check) || ($check == $type))? "":$check;
			//loop over listing template file
			foreach($raw_results as $result){
				$check = apply_filters("bl_before_bepro_listing_template",$type,$result,$previous);
				$results .= @(is_numeric($check) || ($check == $type))? "":$check;
				$result->featured = is_numeric($l_featured);
				$results .= basic_listing_layout($result, $list_templates["template_file"]);
				$check = apply_filters("bl_after_bepro_listing_template", $type,$result,$previous);
				$results .= @(is_numeric($check) || ($check == $type))? "":$check;
				$previous = $result;
			}
			
			$check = apply_filters("bl_end_bepro_listing_template", $type);
			$results .= @(is_numeric($check) || ($check == $type))? "":$check;
			
			foreach($list_templates as $key => $val){
				remove_action($key, $val, 10, 2);
			}
		}
		
		//wrap results with css span
		
		$results = "<span class='the_search_results_only'>".$results."</span>";
		
		//show paging if not featured listings and if its selected as an option
		
		if(($show_paging == 1) && (empty($l_featured))){
			$pages = 0;
			$pages = $findings[1];
			$counter = 1;
			$paging = "<div style='clear:both'><br /></div><div class='paging'><span>Pages:</span> ";
			
			//if ajax is off, we need to carry forward some of the filter vars shown in the address bar
			$get_vars = "";
			if(!empty($_GET["filter_search"])){
				$get_vars .= "&filter_search=1";
				if(!empty($_GET["l_type"])){
					$get_vars .= "&l_type=".$_GET["l_type"];
				}
				if(!empty($_GET["bl_form_id"])){
					$get_vars .= "&bl_form_id=".$_GET["bl_form_id"];
				}
			}
			while($pages != 0){
				$selected = ((!empty($_REQUEST["lpage"]) && ($counter == $_REQUEST["lpage"]))||(empty($_REQUEST["lpage"]) && ($counter == 1)))? $selected="selected":"";
				$paging .= "<a href='?lpage=".$counter.$get_vars."' class='$selected'>".$counter."</a>";
				$pages--;
				$counter++;
			}
			$paging .= "</div>";
			if($counter > 1) $results.= $paging; // if no pages then dont show this
			$show_paging = "<div id='bl_show_paging' class='bl_shortcode_selected'>$show_paging</div>";
		}else{
			if(empty($l_featured))
				$show_paging = "<div id='bl_show_paging' class='bl_shortcode_selected'>0</div>";
		}
		
		$bl_total_results = "";
		
		if(!empty($l_featured)){
			$l_featured_id = "_featured";
			if(@$findings[2])
				$bl_total_results = "<div id='bl_total_results_featured' class='bl_shortcode_selected'>".$findings[2]."</div>";
		}else{
			$show_bl_type = "<div id='bl_type' class='bl_shortcode_selected'>$type</div>";
			$hidden_limit_text = "<div id='bl_limit' class='bl_shortcode_selected'>$limit</div>";
			if(@$findings[2])
				$bl_total_results = "<div id='bl_total_results' class='bl_shortcode_selected'>".$findings[2]."</div>";
		}
		
		
		$bl_order_dir = "";
		if(is_numeric($order_dir))
			$bl_order_dir = "<div id='bl_order_dir' class='bl_shortcode_selected'>$order_dir</div>";
		
		$bl_order_by = "";
		if(is_numeric($order_by) && empty($l_featured))
			$bl_order_by = "<div id='bl_order_by' class='bl_shortcode_selected'>$order_by</div>";
		
		
		if(is_numeric($bl_form_id))
			$bl_form_id_div = "<div id='bl_form_id' class='bl_shortcode_selected'>$bl_form_id</div>";
		
		$bl_l_type = "";
		
		If(!empty($l_type) && empty($l_featured)){
			$l_type = is_array($l_type)? implode(",",array_values($l_type)):$l_type;
			$bl_l_type = "<div id='bl_l_type' class='bl_shortcode_selected'>".$l_type."</div>";
		}
		
		//Reset featured BST edit
		$_POST["l_featured"] = "";
		$l_featured = "";
		$order_by = "";
		$order_dir = "";
		
		$results = "<div id='shortcode_list$l_featured_id' class='bl_frontend_search_section result_type_".$type."'>".$results."</div>";
		$results .= "$hidden_limit_text $show_bl_type $show_paging $bl_order_dir $bl_order_by $bl_form_id_div $bl_l_type $bl_total_results";
		if($echo_this){
			echo $results;
		}else{	
			return $results;
		}
	}
	
	//process paging and listings
	function process_listings_results($show_paging = false, $num_results = false, $l_type = false, $l_ids = false, $order_by = 1, $order_dir = 1, $l_featured = ""){
		global $wpdb;
		
		$returncaluse = "";
		$filter_cat = "";
		if(!empty($_REQUEST["filter_search"]) || !empty($l_type)){
			
			if(is_array($l_type) && @isset($l_type[0]) && ($l_type[0]==0)){
				$l_type ="";
			}	
			$returncaluse = @Bepro_listings::listitems(array('l_type' => $l_type,'l_featured' => $l_featured));
			$filter_cat = true;
		}	
		if(!empty($l_ids)){
			$returncaluse .= " AND posts.ID IN ($l_ids)";
		}
		
		if($order_by == 1){
			$order_by = "posts.post_title";
		}else if($order_by == 2){
			$order_by = "RAND()";
		}else if($order_by == 3){
			$order_by = "geo.last_name";
		}else{
			$order_by = "posts.post_date";
		}
		
		$order_dir = (($order_dir == 1) || (empty($order_dir)))? "ASC":"DESC";

		//Handle Paging selection calculations and process listings
			$page = (empty($_REQUEST["lpage"]))? 1 : $_REQUEST["lpage"];
			$page = ($page - 1) * $num_results;
			$limit_clause = " ORDER BY $order_by $order_dir LIMIT $page , $num_results";
			$resvs = bepro_get_listings($returncaluse, $filter_cat);
			$pages = ceil(count($resvs)/$num_results);
			$findings[1] = $pages;
			$findings[2] = count($resvs);
			$raw_results = bepro_get_listings($returncaluse, $filter_cat, $limit_clause);

		$findings[0] = $raw_results;
		return $findings;
	}
	
	function basic_listing_layout($result, $listing_template_file = ''){ 
		if(empty($listing_template_file))$listing_template_file = plugin_dir_path( __FILE__ ).'/templates/listings/generic_1.php';
		
		//allow other features to tie in
		$get_listing_template = apply_filters("bepro_listings_change_list_template", $listing_template_file,$result);
		if($get_listing_template != -1)$listing_template_file = $get_listing_template;
		
		ob_start();
		$data = get_option("bepro_listings");
		include($listing_template_file);
		$results = ob_get_contents();
		ob_end_clean();	
			
		return $results;			
	}
	
	/*
	//
	//Filter shortcode
	//
	*/	
	
	function search_filter_shortcode($atts = array(), $echo_this = false){
		global $wpdb;
		extract(shortcode_atts(array(
			  'listing_page' => esc_sql(@$_POST["listing_page"]),
			  'l_type' => esc_sql(@$_POST["l_type"])
		 ), $atts));
		
		//get settings
		$data = get_option("bepro_listings");
		
		//Process user requested Bepro listing types 

		$search_form = "<div class='filter_search_form_shortcode'>
			<form id='filter_search_shortcode_form' method='post' action='".$listing_page."'>
				<input type='hidden' name='name_search' value='".@$_POST["name_search"]."'>
				<input type='hidden' name='addr_search' value='".@$_POST["addr_search"]."'>
				<input type='hidden' name='filter_search' value='1'>";
				
		$search_form_fields = apply_filters("bepro_listings_search_filter_shortcode_override",$atts);
		if(!empty($search_form_fields) && ($search_form_fields != $atts)){
			$search_form .= $search_form_fields;
		}else{
			$search_form .= "
				<div class='bl_category_search_option'>
						<span class='searchlabel'>".__($data["cat_heading"], "bepro-listings")."</span>
						";
			if($l_type){	
				$l_type = is_array($l_type)?$l_type[0]:$l_type;
				$parent = (is_numeric($l_type) && ($l_type != "0"))? $l_type:" ";
				$include = (stristr($l_type, ","))? $l_type:" ";
			}else{
				$parent = "";
			}
				$args = array(
					'show_option_all'	=> 'All',
					'orderby'            => 'ID', 
					'order'              => 'ASC',
					'show_count'         => 0,
					'hide_empty'         => 1, 
					'child_of'           => 0,
					'exclude'            => '',
					'include'            => @$include,
					'echo'               => 0,
					'selected'           => $parent,
					'hierarchical'       => 0, 
					'name'               => 'l_type[]',
					'id'                 => 'bl_search_filter',
					'class'              => 'bl_search_filter_class',
					'depth'              => 0,
					'tab_index'          => 0,
					'taxonomy'           => 'bepro_listing_types',
					'hide_if_empty'      => true,
						'walker'             => ''
				);
				$search_form .= wp_dropdown_categories( $args )."</div>";

			///////////////////////////////////////////////////////////////////////
			$dist_measurement = (@$data["dist_measurement"] && ($data["dist_measurement"]==2))? "Km":"Mi";
			if(is_numeric($data["show_geo"]) && ($data["show_geo"] != 0))	
			$search_form .= "<div class='bl_distance_search_option'><span class='searchlabel'>".__("Distance", "bepro-listings").':</span> <select name="distance">
						<option value="">'.__("None","bepro-listings").'</option>
						<option value="10" '.((@$_POST["distance"] == 10)||((empty($_POST["distance"])) && $data["distance"] == 10)? 'selected="selected"':"").'>10 '.$dist_measurement.'</option>
						<option value="50" '.((@$_POST["distance"] == 50)||((empty($_POST["distance"])) && $data["distance"] == 50)? 'selected="selected"':"").'>50 '.$dist_measurement.'</option>
						<option value="150" '.(((@$_POST["distance"] == 150) || ((empty($_POST["distance"])) && $data["distance"] == 150))? 'selected="selected"':"").'>150 '.$dist_measurement.'</option>
						<option value="250" '.((@$_POST["distance"] == 250)||((empty($_POST["distance"])) && $data["distance"] == 250)? 'selected="selected"':"").'>250 '.$dist_measurement.'</option>
						<option value="500" '.((@$_POST["distance"] == 500)||((empty($_POST["distance"])) && $data["distance"] == 500)? 'selected="selected"':"").'>500 '.$dist_measurement.'</option>
						<option value="1000" '.((@$_POST["distance"] == 1000)||((empty($_POST["distance"])) && $data["distance"] == 1000)? 'selected="selected"':"").'>1000 '.$dist_measurement.'</option>
					</select></div>';
				
				//min/max cost
				if(($data["show_cost"] == 1) || ($data["show_cost"] =="on"))
				$search_form .= '
					<div><span class="label_sep">'.__("Price Range", "bepro-listings").'</span><span class="form_label"></span><input class="input_text" type="text" name="min_cost" value="'.@$_POST["min_cost"].'" placeholder="'.__("From", "bepro-listings").'"><span class="form_label"> </span><input class="input_text" type="text" name="max_cost" value="'.@$_POST["max_cost"].'" placeholder="'.__("To", "bepro-listings").'"></div>';
				
				if(($data["show_date"] == 1) || ($data["show_date"] =="on"))
				$search_form .= '
					<div><span class="label_sep">'.__("Date Range", "bepro-listings").'</span><span class="form_label"></span><input class="input_text" type="text" name="min_date" id="min_date" value="'.@$_POST["min_date"].'" placeholder="'.__("From", "bepro-listings").'"><span class="form_label"> </span><input class="input_text" type="text" name="max_date" id="max_date" value="'.@$_POST["max_date"].'" placeholder="'.__("To", "bepro-listings").'"></div>';
				
				$search_form .= apply_filters("bepro_listings_search_filter_shortcode","", $atts);
		}
				$search_form .= '
						<div id="search_filter_shortcode_button"><input type="submit" class="form-submit" value="'.__("Filter", "bepro-listings").'" id="edit-submit" name="find">
						<a class="clear_search" href="'.get_bloginfo("url")."/".$listing_page.'"><button>Clear</button></a></div>
		</form></div>
		';
		
		if($echo_this){
			echo $search_form;
		}else{	
			return $search_form;
		}
	}
	
	/*
	//
	//Listing templates
	//
	*/	
	function bepro_listings_list_title_template($bp_listing, $data){
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		
		//if its an external link (4) then change the permalink
		if($data["link_new_page"] == 4){
			$permalink = $bp_listing->website;
		}else{
			$permalink = get_permalink( $bp_listing->post_id );
		}
		$title_length = !empty($data["title_length"])? $data["title_length"]:18;
		$title = $bp_listing->post_title;
		$title = substr($title,0, $title_length).((strlen($title) > $title_length)? "...":"");
		$title = apply_filters("bl_list_title_temp",$title, $bp_listing);
		$title = stripslashes($title);
			
		/*
		* 1 = go to page
		* 2 = new window
		* 3 = ajax page
		* 4 = hide internal
		* 5 = readonly
		*/		
		if($target == 2){
			echo "<div class='result_name'><a href='".$permalink."' target='_blank'>".$title."</a></div>";	
		}elseif($target == 3){
			echo "<div class='result_name'><a class='bl_ajax_result_page' post_id='".$bp_listing->post_id."' href='".$permalink."' target='_blank'>".$title."</a></div>";
		}elseif($target == 5){
			echo "<div class='result_name'>".$title."</div>";
		}else{
			echo "<div class='result_name'><a href='".$permalink."'>".$title."</a></div>";	
		}
	}
	
	function bepro_listings_list_category_template($bp_listing, $data){
		$terms = get_the_term_list($bp_listing->post_id, 'bepro_listing_types', '', ', ','');
	
		if($data["link_new_page"] == 5)
			$terms = strip_tags($terms);
		
		echo '<span class="result_type">'.$terms.'</span>';
	}
	function bepro_listings_list_featured_template($bp_listing, $data){
		if($bp_listing->featured)
			echo '<span class="result_featured"></span>';
	}
	function bepro_listings_list_phone_template($bp_listing, $data){
		if(!empty($bp_listing->phone)){
			echo "<span class='result_phone'>".__("Phone", "bepro-listings")." - ".$bp_listing->phone."</span>";
		}
	}
	function bepro_listings_list_email_template($bp_listing, $data){
		if(!empty($bp_listing->email) && !empty($data["show_con"])){
			echo "<span class='result_email'>".__("Email", "bepro-listings")." - ".$bp_listing->email."</span>";	
		}
	}
	function bepro_listings_list_image_template($bp_listing, $data){
		if(empty($data["show_imgs"]))return;
		if($data["link_new_page"] == 4){
			$permalink = $bp_listing->website;
		}else{
			$permalink = get_permalink( $bp_listing->post_id );
		}
		
		
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		$thumbnail = get_the_post_thumbnail($bp_listing->post_id, 'thumbnail'); 
		$thumbnail_check = apply_filters("bepro_listings_list_thumbnail",$bp_listing->post_id);
		if(!is_numeric($thumbnail_check)) $thumbnail = $thumbnail_check;
		$default_img = (!empty($thumbnail))? $thumbnail:'<img src="'.$data["default_image"].'"/>';
		
		if($target == 2){
			echo '<span class="result_img"><a href="'.$permalink.'" target="_blank">'.$default_img.'</a></span>';
		}elseif($target == 3){
			echo '<span class="result_img"><a href="'.$permalink.'" class="bl_ajax_result_page" post_id="'.$bp_listing->post_id.'">'.$default_img.'</a></span>';	
		}elseif($target == 5){
			echo '<span class="result_img">'.$default_img.'</span>';	
		}else{
			echo '<span class="result_img"><a href="'.$permalink.'">'.$default_img.'</a></span>';
		}
	}
	function bepro_listings_list_geo_template($bp_listing, $data){
		if($data["show_geo"])
			echo '<span class="result_title">'.bpl_format_address($bp_listing).'</span>';
	}
	function bepro_listings_list_content_template($bp_listing, $data){
		$desc_length = empty($data["desc_length"])? 80:$data["desc_length"];
		$content =  substr(strip_tags($bp_listing->post_content), 0, $desc_length);
		echo '<span class="result_desc">'.stripslashes(do_shortcode($content)).'...</span>';
	}
	
	function bepro_listings_list_cost_template($bp_listing, $data){
		if($data["show_cost"]){
			if(is_numeric($bp_listing->cost)){ 
				//formats the price to have comas and dollar sign like currency.
				setlocale(LC_MONETARY, "en_US");
				$cost = ($bp_listing->cost == 0)? __("Free","bepro-listings") : $data["currency_sign"].sprintf('%01.2f', $bp_listing->cost);
			}else{
				$cost = __("Please Contact", "bepro-listings");
			} 
			//cost
			echo '<span class="result_cost">'.apply_filters("bl_cost_listing_value",$cost).'</span>';
		}
	}
	
	function bepro_listings_list_links_template($bp_listing, $data){
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		$show_web_link = $data["show_web_link"];
		$details_link = empty($data["details_link"])? "Item":$data["details_link"];
		$permalink = get_permalink( $bp_listing->post_id );
		$http = empty($_SERVER["HTTPS"])? "http://":"https://";
			
		/*
		* 1 = go to page
		* 2 = new window
		* 3 = ajax page
		* 4 = hide internal
		*/		
		if($target == 2){
			if(!empty($bp_listing->website) && !empty($show_web_link)){
				$website = (stristr($bp_listing->website,"http"))? $bp_listing->website:$http.$bp_listing->website;
				echo '<span class="result_button"><a href="'.$website.'"  target="_blank">'.__("Website","bepro-listings").'</a></span>';
			}
			
			if($bp_listing->post_status == "publish")
				echo '<span class="result_button"><a href="'.$permalink.'" target="_blank">'.$details_link.'</a></span>';
		}elseif($target == 3){
			if($bp_listing->post_status == "publish")
				echo '<span class="result_button"><a class="bl_ajax_result_page" post_id="'.$bp_listing->post_id.'" href="'.$permalink.'" '.$target.'>'.$details_link.'</a></span>';
		}elseif($target == 4){
				$website = (stristr($bp_listing->website,"http"))? $bp_listing->website:$http.$bp_listing->website;
				echo '<span class="result_button"><a href="'.$website.'"  target="_blank">'.__("Website","bepro-listings").'</a></span>';
		}elseif($target == 5){
				//do nothing. this is readonly
		}else{
			if(!empty($bp_listing->website) && !empty($show_web_link)){
				$website = (stristr($bp_listing->website,"http"))? $bp_listing->website:$http.$bp_listing->website;
				echo '<span class="result_button"><a href="'.$website.'">'.__("Website","bepro-listings").'</a></span>';
			}
			if($bp_listing->post_status == "publish")
				echo '<span class="result_button"><a href="'.$permalink.'">'.$details_link.'</a></span>';
		}
	}
	
	
	/*
	//
	//Item Page templates
	//
	*/
	function bepro_listings_item_title_template(){
		echo get_the_title();
	}
	
	function bepro_listings_item_gallery_template(){
		$post_id = get_the_ID();
		$data = get_option("bepro_listings");
		if(empty($data["show_imgs"]))return;
		$num_images = $data["num_images"];
		
		//get images
		$attachments = bl_get_listing_images($post_id);
		
		//Show wordpress gallery for this page
		$gallery = ($num_images == 0)? "":do_shortcode("[gallery size='".$data["gallery_size"]."' columns=".((!empty($data["gallery_cols"]))? $data["gallery_cols"]:3)." ids='".implode(",",$attachments)."']");
		if(empty($gallery) && ($num_images != 0)){
			$default_img = '<img src="'.$data["default_image"].'"/>';
			if(!empty($default_img))
				$gallery = $default_img;
		}
		echo "<div class='bepro_listing_gallery'>".apply_filters("bepro_listings_item_gallery_feature", $gallery)."</div>";
	}
	
	function bepro_listings_item_after_gallery_template(){
		$override = false;
		$if_override = apply_filters("bepro_listings_override_page_categories",$override);
		
		if(!$if_override){
			$data = get_option("bepro_listings");
			$page_id = get_the_ID();
			//show categories
			$cats = get_the_term_list($page_id, 'bepro_listing_types', '', ', ','');
			if($cats) 
			echo $cat_section = "<div class='bepro_listing_category_section'><h3>".__($data["cat_heading"], "bepro-listings")."  </h3>".$cats."</div>";
		}
	}	
	
	function bepro_listings_page_geo(){		
		global $wpdb;		
		$data = get_option("bepro_listings");		
		$page_id = get_the_ID();		
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id = ".$page_id);		
		if( @$item->lat){			
			$map_url = "http://maps.google.com/maps?&z=10&q=".$item->lat."+".$item->lon."+(".urlencode($item->address_line1.", ".$item->city.", ".$item->state.", ".$item->country).")&mrt=yp ";			
			$address_val = ($data["protect_contact"] == "on")? "<a href='$map_url' target='_blank'>".__("View Map", "bepro-listings")."</a>" : bpl_format_address($item);			
			echo "<div class='bepro_address_info'><span class='item_label'>".__("Address", "bepro-listings")."</span> - $address_val</div>";		
		}	
	}
	
	function bepro_listings_item_details_template(){
		$override = false;
		$if_override = apply_filters("bepro_listings_override_page_content",$override);
		if($if_override) return;
		
		global $wpdb;
		$page_id = get_the_ID();
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id = ".$page_id);
		//get settings
		$data = get_option("bepro_listings");
		$add_detail_links = empty($data["add_detail_links"])? false:true;
		$num_cols = !empty($data["bpl_details_cols"])? $data["bpl_details_cols"]:2;
		if(is_numeric($item->cost)){
			//formats the price to have comas and dollar sign like currency.
			$cost = ($item->cost == 0)? __("Free", "bepro-listings") : $data["currency_sign"].sprintf('%01.2f', $item->cost);
		}else{
			$cost = __("Please Contact", "bepro-listings");
		}
		
		if((!empty($data["show_cost"])) || (!empty($data["show_con"]))){
			echo "<span class='bepro_listing_info'><h3>".__("Details", "bepro-listings")." </h3><div id='bpl_details_cols' style='column-count: ".$num_cols.";-moz-column-count: ".$num_cols.";-webkit-column-count: ".$num_cols.";'>";
			if(is_numeric($data["show_cost"]) || ($data["show_cost"] == "on")){
				echo "<div class='item_cost'>".__(apply_filters("bl_cost_listing_label","Cost"), "bepro-listings")." - ".apply_filters("bl_cost_listing_value",$cost)."</div>";
			}	
				//If we have geographic data then we can show this listings address information
				//If there is contact information then show it
				if(is_numeric($data["show_con"]) || ($data["show_con"] == "on")){
					if(!empty($item->email)){
						if($add_detail_links){
							$email_txt = ($data["protect_contact"] == "on")? "Click to View":$item->email;
							$email = "<span class='item_label'>".__("Email", "bepro-listings")."</span> - <a href='mailto:".$item->email."'>".$email_txt."</a><br />";
						}else{
							$email_txt = ($data["protect_contact"] == "on")? "Private":$item->email;
							$email = "<span class='item_label'>".__("Email", "bepro-listings")."</span> - ".$email_txt."<br />";
						}
					}
					if(!empty($item->phone)){
						if($add_detail_links){
							$phone_txt = ($data["protect_contact"] == "on")? "Click to Call":$item->phone;
							$phone = "<span class='item_label'>".__("Phone", "bepro-listings")."</span> - <a href='tel:".$item->phone."'>".$phone_txt."</a><br />";
						}else{
							$phone_txt = ($data["protect_contact"] == "on")? "Private":$item->phone;
							$phone = "<span class='item_label'>".__("Phone", "bepro-listings")."</span> - ".$phone_txt."<br />";
						}
					}
					if(!empty($item->website)){
						$http_check = substr(@$item->website, 0, 4);
						if($add_detail_links){
							$website = "<span class='item_label'>".__("Website", "bepro-listings")."</span> - <a href='".((@$item->website && ($http_check != "http"))? "http://":"").(@$item->website)."'>".(@$item->website)."</a>";
						}else{
							$website = "<span class='item_label'>".__("Website", "bepro-listings")."</span> - ".$item->website;
						}
					}
					echo "<div class='item_contactinfo'>
							".(empty($item->first_name)? "":"<span class='item_label'>".__("First Name", "bepro-listings")."</span> - ".$item->first_name."<br />")."
							".(empty($item->last_name)? "":"<span class='item_label'>".__("Last Name", "bepro-listings")."</span> - ".$item->last_name."<br />")."
							".$email."
							".$phone."
							".$website."
						</div>";
				}
			echo "</div></span>";
		}
		
	}
	function bepro_listings_item_content_template(){
		$data = get_option("bepro_listings"); 
		if(!empty($data["show_content"]) && is_numeric($data["show_content"]) ){
			include(plugin_dir_path( __FILE__ ).'/templates/tabs/description.php');
		}	
	}
	
	//User form for creating Bepro Listings
	function user_create_listing($atts = array()){
		global $wpdb;
		
		extract(shortcode_atts(array(
			  'bl_form_id' => esc_sql(@$_POST["bl_form_id"]),
			  'origami' => esc_sql(@$_POST["origami"]),
			  'register' => esc_sql(@$_POST["register"]),
			  'redirect' => esc_sql(@$_POST["redirect"])
		 ), $atts));
		
		//addon tie ins
		if(empty($_POST["bl_form_id"]))
			$_POST["bl_form_id"] = $bl_form_id; 
		$_POST["origami"] = $origami; 
		$_POST["redirect"] = $redirect; 
		//get settings
		$data = get_option("bepro_listings");
		$default_user_id = $data["default_user_id"];
		$num_images = $data["num_images"];
		$validate = $data["validate_form"];
		$show_cost = $data["show_cost"];
		$show_con = $data["show_con"];
		$show_geo = $data["show_geo"];
		$listing_saved = false;
		
		if(empty($default_user_id) && empty($register)){
			echo __("You must provide a 'default user id' in the admin settings or use the registration=1 option.","bepro-listings");	
			return;
		}
		
		if(!empty($_POST["save_bepro_listing"])){
			$wp_upload_dir = wp_upload_dir();
			if(bepro_listings_save()){
				$listing_saved = true;
				if(!empty($_POST["redirect"])){
					header("LOCATION: ".get_bloginfo("url").$_POST["redirect"]);
					exit;
				}
				$success_message = apply_filters("bepro_form_success_message",__("Listing Successfully Saved","bepro-listings"));
				echo "<h2>".$success_message."</h2>";
			}else{
				$fail_message = apply_filters("bepro_form_fail_message",__("Issue saving your listing. Please contact the website administrator","bepro-listings"));
				echo "<h2>".$fail_message."</h2>";
			}
		}
		ob_start();
		
		$item = new stdClass();
		$post_data  = new stdClass();
		$thunmbnails  = "";
		
		$frontend_form = dirname( __FILE__ )."/templates/form.php";
		$frontend_form = apply_filters("bl_change_upload_form", $frontend_form);

		if($frontend_form && $frontend_form != "This form has no fields!")
			include($frontend_form);
		if($frontend_form == "This form has no fields!")
			echo "Form Error!";
		
		$results = ob_get_contents();
		ob_end_clean();	
		
		return $results;
	}
	
	//item page tabs
	function bepro_listings_item_tabs(){
		include(plugin_dir_path( __FILE__ ).'/templates/tabs.php');
	}
	
	function bepro_listings_description_tab(){ 
		include(plugin_dir_path( __FILE__ )."templates/tabs/tab-description.php");
	}
	
	function bepro_listings_comments_tab(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/tab-comments.php");
	}
	
	function bepro_listings_details_tab(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/tab-details.php");
	}
	
	function bepro_listings_maps_tab(){
		$data = get_option("bepro_listings");
		if(is_numeric($data["show_geo"])){
			global $wpdb;
			$page_id = get_the_ID();
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id = ".$page_id);
			if(@$item && !empty($item->lat) && !empty($item->lon))
				include(plugin_dir_path( __FILE__ )."templates/tabs/tab-maps.php");
		}
	}
	
	function bepro_listings_description_panel(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/description.php");
	}
	
	function bepro_listings_comments_panel(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/comments.php");
	}
	
	function bepro_listings_maps_panel(){
		$data = get_option("bepro_listings");
		if(is_numeric($data["show_geo"])){
			global $wpdb;
			$page_id = get_the_ID();
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id = ".$page_id);
			if(@$item && !empty($item->lat) && !empty($item->lon))
				include(plugin_dir_path( __FILE__ )."templates/tabs/maps.php");
		}
	}
	
	function show_true(){ 
		return true;
	}
	function bepro_listings_details_panel(){
		include(plugin_dir_path( __FILE__ )."templates/tabs/details.php");
	}

	function bl_form_package_field($bl_order_id = null, $return_this = false, $selected=false){
		$data = get_option("bepro_listings");
		$order = false;
		$return_text = "";
		$user_id = get_current_user_id();
		if(!$selected && is_numeric($_POST["bpl_package"])){
			$selected = $_POST["bpl_package"];
		}else if($bl_order_id){
			$order = bl_get_payment_order($bl_order_id); 

			//allow user to change which package this listing is associated with but show its selected
			if(@is_numeric($order->feature_id))
				$selected = $order->feature_id;
		}
		
		$return_text .= '<div id="flat_fee">';
		if(@$data["require_payment"] && ($data["require_payment"] == 2)){
			$packages = get_posts(array("post_type" => "bpl_packages"));
			if(!$packages || (sizeof($packages) < 1)) return;
			
			$return_text .= '<h3>'.__("Available Packages", "bepro-listings").'</h3>';
			foreach($packages as $package){
				$num_listings = get_post_meta($package->ID, "num_package_listings", true);
				$duration = get_post_meta($package->ID, "package_duration", true);
				$cost = get_post_meta($package->ID, "package_cost", true);
				$vacant = bl_get_vacant_order_id($user_id, 2, $package->ID, false);
				$listings_left = "";
				
				if($vacant){
					global $wpdb;
					$exiting_listings = $wpdb->get_row("SELECT count(*) as num_listings FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." as bl WHERE bl.bl_order_id =".$vacant);		
					$exiting_listings = (!empty($exiting_listings))? $exiting_listings->num_listings:"";
					
					$listings_left = is_numeric($exiting_listings) ? ($num_listings - $exiting_listings):"";
					$listings_left = is_numeric($listings_left) && ($listings_left > 0)? "(".($listings_left)." ".__("Remaining","bepro-listings").")":"";
				}
				
				
				//this is a package, we need to know how many listings are possible
				if(!$num_listings || !is_numeric($num_listings) || ($num_listings < 1)) return; 
				
				$package_div[] = array();
				$return_text .= '<div class="package_option"><input type="radio" name="bpl_package" id="package_sel_'.$package->ID.'" value="'.$package->ID.'" '.((@$order && ($order->feature_id == $package->ID))? "checked='checked'":"").' '.((@$selected && ($selected == $package->ID))? "checked='checked'":"").'> <span class="package_head">'.$package->post_title.' '.$listings_left.'</span>
				<span class="package_options">
					<ul>
						<li># '.__("Days","bepro-listings").' '.$duration.'</li>
						<li># '.__("Listings","bepro-listings").' '.$num_listings.'</li>
						<li>'.__("Cost","bepro-listings").' '.$data["currency_sign"].$cost.'</li>
					</ul>
				<span>
				<span class="package_details">'.$package->post_content.'<span>
				</div>';
				
			}
		}
		$return_text .= '</div><div style="clear:both"></div>';
		if($return_this)
			return $return_text;
		echo $return_text;
	}
	
	/*
		cats = all possible categories
		categores = the selected categories
	*/
	function get_form_cats($cats, $exclude = array(), $required = array(), $categories = array(), $multiple = true){
		$data = get_option("bepro_listings");
		$required_hidden = ""; // disable required and use hidden select to send to backend
		$normal_list = "";
		$required_list = "";
		foreach($cats as $cat){
			if(!empty($exclude) && in_array($cat->term_id, array_values($exclude))){
			
			}elseif(!empty($required) && in_array($cat->term_id, array_values($required))){
				if($data["form_cat_style"] == 2){
					$required_list .= '<option value="'.$cat->term_id.'" selected="selected" disabled>'.$cat->name.'</option>';
					$required_hidden .= '<input type="hidden" name="categories[]" value="'.$cat->term_id.'"/>';
				}else{
					$required_list .= '<span class="bepro_form_cat"><span class="form_label">'.$cat->name.'</span><input type="checkbox" id="categories" name="categories[]" value="'.$cat->term_id.'" checked="checked" disabled="disabled"></span>';
				}
			}else{
				if($data["form_cat_style"] == 2){
					$selected = isset($categories[$cat->term_id])? "selected='selected'":"";
					$normal_list .= '<option value="'.$cat->term_id.'" '.$selected.'>'.$cat->name.'</option>';
				}else{
					$checked = isset($categories[$cat->term_id])? "checked='checked'":"";
					$normal_list .= '<span class="bepro_form_cat"><span class="form_label">'.$cat->name.'</span><input type="checkbox" id="categories" name="categories[]" value="'.$cat->term_id.'" '.$checked.'></span>';
				}
			}
		}
		if($multiple) $multiple_text = "multiple";
		
		if($data["form_cat_style"] == 2){
			$required_list = $required_hidden."<select name='categories[]' class='chosen-select' ".$multiple_text.">".$required_list."</select>";
			$normal_list = "<select name='categories[]' class='chosen-select' ".$multiple_text."><option></option>".$normal_list."</select>";
		}
		return array("required_list" => $required_list,"normal_list" => $normal_list);
	}
	
?>