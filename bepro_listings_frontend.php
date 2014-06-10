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

	//ajax update front end
	function bl_ajax_frontend_update(){
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
				echo "<h1>";
				do_action( 'bepro_listings_item_title' );
				echo "</h1>";
				do_action( 'bepro_listings_item_above_gallery' );
				do_action( 'bepro_listings_item_gallery');
				do_action( 'bepro_listings_item_after_gallery');
				do_action( 'bepro_listings_item_before_details');
				do_action( 'bepro_listings_item_details');
				do_action( 'bepro_listings_item_after_details');
				do_action("bepro_listings_item_content_info");
				do_action("bepro_listings_item_end");
				echo "</div>";
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
		$l_type = "";
		if(!empty($_POST["l_type"]))
		foreach($_POST["l_type"] as $check_l_type){
			if(is_numeric($check_l_type))
				$l_type .='<input type="hidden" name="l_type[]" value="'.$check_l_type.'">';
		}
		$button = '
			<form method="post" id="result_page_back_button">
				<input type="hidden" name="filter_search" value="1">
				'.$l_type.'
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
			  'l_type' => $wpdb->escape($_REQUEST["l_type"])
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
			  'pop_up' => $wpdb->escape($_POST["pop_up"]),
			  'size' => $wpdb->escape($_POST["size"]),
			  'l_type' => $wpdb->escape($_REQUEST["l_type"]),
			  'show_paging' => $wpdb->escape($_POST["show_paging"]),
			  'map_id' => $wpdb->escape($_POST["map_id"]),
			  'bl_post_id' => $wpdb->escape($_POST["bl_post_id"])
		 ), $atts));
		 
		//Setup data
		$data = get_option("bepro_listings");
		$num_results = $data["num_listings"]; 
		$size = empty($size)? 1:$size;
		
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
					$map_cities .= bepro_listings_detailed_infowindow($result, $counter);
				}else{
					$map_cities .= bepro_listings_simple_infowindow($result, $counter);
				}
			}
			$counter++;
		}
		$declare_for_map = apply_filters("bepro_listings_declare_for_map", '');
		//javascript initialization of the map
		$map = "<script type='text/javascript'>
			jQuery(document).ready(function(){
				var currentlat;
				var currentlon;
				markers = new Array();
				positions = new Array();
				var currentlat = $currlat;
				var currentlon = $currlon;
				var openwindow = false;
				var latlng = new google.maps.LatLng(currentlat, currentlon);
				icon_1 = new google.maps.MarkerImage('".plugins_url("images/icons/icon_1.png", __FILE__)."');
				var myOptions = {
					zoom:10,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
				$declare_for_map
				map = new google.maps.Map(document.getElementById('".$map_id."'), myOptions);
				
				$map_cities
				//cluster markers
				if(markers.length > 1){
					var markerCluster = new MarkerClusterer(map, markers);
					//makes sure map view fits all markers
					 latlngbounds = new google.maps.LatLngBounds();
					for ( var i = 0; i < positions.length; i++ ) {
						latlngbounds.extend( positions[ i ] );
					}
					map.fitBounds( latlngbounds );
				}
			});
		</script>
		<div id='shortcode_map'>
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
	
	function bepro_listings_simple_infowindow($result, $counter){
		$permalink = get_permalink( $result->post_id );
		$data = get_option("bepro_listings");
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		
		if($target == 2){
			$link = 'var win=window.open("'.$permalink.'", "_blank"); win.focus();';
		}elseif($target == 3){
			$link = 'bl_ajax_get_page("'.$result->post_id.'")';	
		}else{
			$link = 'window.location.href = "'.$permalink.'"';
		}
		
		return 'var infowindow_'.$counter.' = new google.maps.InfoWindow( { content: "<div class=\"marker_content\"><span class=\"marker_detais\">'.$result->post_title.'</span></div>", size: new google.maps.Size(50,50)});
				  google.maps.event.addListener(marker_'.$counter.', "mouseover", function() {
					if(openwindow){
						eval(openwindow).close();
					}
					infowindow_'.$counter.'.open(map,marker_'.$counter.');
					openwindow = infowindow_'.$counter.';
				  });
				  google.maps.event.addListener(marker_'.$counter.', "click", function() {
					'.$link.';
				  });
			';
	}
	
	function bepro_listings_detailed_infowindow($result, $counter){
		$thumbnail = get_the_post_thumbnail($result->post_id, 'thumbnail'); 
		$data = get_option("bepro_listings");
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		$default_img = (!empty($thumbnail))? $thumbnail:'<img src="'.$data["default_image"].'"/>';
		if($target == 2){
			$link = "<a href=\"http://".urlencode($result->website)."\" target='_blank'>Visit Website</a>";
		}elseif($target == 3){
			$link = $link = "<a href=\"http://".urlencode($result->website)."\" class='bl_ajax_result_page' post_id=\"".$result->post_id."\">Visit Website</a>";
		}else{
			$link ="<a href=\"http://".urlencode($result->website)."\" post_id=\"".$bp_listing->post_id."\">Visit Website</a>";
		}
		
		return "var infowindow_".$counter." = new google.maps.InfoWindow( { content: '<div class=\"marker_content\"><span class=\"marker_title\">".addslashes(substr($result->post_title,0,18))."</span><span class=\"marker_img\">".$default_img."</span><span class=\"marker_detais\">".$result->address_line1.", ".$result->city.", ".$result->state.", ".$result->country."</span><span class=\"marker_links\">".$link."<br /><a href=\"".get_permalink($result->post_id)."\">View Listing</a></span></div>', size: new google.maps.Size(50,50)});
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
		return 'position = new google.maps.LatLng('.$result->lat.','.$result->lon.');
					var marker_'.$counter.' = new google.maps.Marker({
						position: position,
						icon:icon_1,
						map: map,
						clickable: true,
						title: "'.$result->item_name.'",
					});
					
					markers.push(marker_'.$counter.');
					positions.push(position);	
				';
	}
	
	//Show categories Called from shortcode
	function display_listing_categories($atts = array(), $echo_this = false){
		global $wpdb;
		$data = get_option("bepro_listings");
		$no_img = plugins_url("images/no_img.jpg", __FILE__ );
		extract(shortcode_atts(array(
			  'url_input' => $wpdb->escape($_POST["url"]),
			  'ctype' => $wpdb->escape($_POST["ctype"]),
			  'cat' => $wpdb->escape($_POST["cat"])
		 ), $atts));
		
		
		$cat_heading = (!empty($_REQUEST["l_type"]) && (is_numeric($_REQUEST["l_type"]) || is_array($_REQUEST["l_type"])))? $data["cat_empty"]:$data["cat_heading"];
		
		$parent = (!empty($cat) && is_numeric($cat))? $cat:0;
		$parent = (!empty($_REQUEST["l_type"]) && (is_numeric($_REQUEST["l_type"]) || is_array($_REQUEST["l_type"])))? $_REQUEST["l_type"]:0; 
		
		$query_args = array('orderby'=>'count', "parent" => 0);
		$parent =(is_array($parent) || (is_numeric($parent)))? $query_args['include'] = $parent:$query_args['parent'] = $parent; 
		
		$categories = get_terms( array('bepro_listing_types'), $query_args);
		
		$cat_list = "<div id='shortcode_cat' class='cat_lists'>";
		
		if($categories && (count($categories) > 0)){
			//If only one category was selected then show its name in the heading
			if((is_numeric($_REQUEST["l_type"]) || (is_array($_REQUEST["l_type"]) && (count($_REQUEST["l_type"]) == 1))) && !empty($categories)){
				$cat = $categories[0];
				$cat_list .= "<h3>".$cat->name." ".$data["cat_singular"]."</h3>";
			}else{
				$cat_list .= "<h3>".__($cat_heading,"bepro_listings")."</h3>";
				foreach($categories as $cat){
					$cat_list.= bepro_cat_templates($cat, $url_input, $ctype);
				}
			}
		}else{
			$cat_list .= "<div class='cat_list_no_item'> ".$cat_heading." Created.</div>";
		}
		$cat_list.= "</div>";
		
		$cat_list.= "<div id='bl_ctype' class='bl_shortcode_selected'>$ctype</div>";
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
			  'type' => $wpdb->escape($_POST["type"]),
			  'l_type' => $wpdb->escape($_REQUEST["l_type"]),
			  'ex_type' => $wpdb->escape($_REQUEST["ex_type"]),
			  'l_featured' => $wpdb->escape($_POST["l_featured"]),
			  'order_dir' => $wpdb->escape($_POST["order_dir"]),
			  'order_by' => $wpdb->escape($_POST["order_by"]),
			  'l_ids' => $wpdb->escape($_REQUEST["l_ids"]),
			  'limit' => $wpdb->escape($_REQUEST["limit"]),
			  'show_paging' => $wpdb->escape($_POST["show_paging"])
		 ), $atts));
		 
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
		if(empty($order_by) && !empty($l_featured))$order_by = 2;
		if(empty($order_dir))$order_dir = 1;
		
		$findings = process_listings_results($show_paging, $num_results, $l_type, $l_ids, $order_by,$order_dir);				
		$raw_results = $findings[0];				
			
		//Create the GUI layout for the listings
		if(empty($raw_results) || is_null($raw_results)){
			$results = "<p>your criteria returned no results.</p>";
		}else{
			//item listing template
			$list_templates = isset($data['bepro_listings_list_template_'.$type])? $data['bepro_listings_list_template_'.$type]: $data['bepro_listings_list_template_1'];
			foreach($list_templates as $key => $val){
				if($key == "style")
					$results .="<link href='".$val."' rel='stylesheet' />";
				else if($key == "template_file")
					$results .="";
				else
					add_action($key, $val);
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
				remove_action($key, $val);
			}
		}
		
		//show paging if not featured listings and if its selected as an option
		
		if(($show_paging == 1) && (empty($l_featured))){
			$pages = 0;
			$pages = $findings[1];
			$counter = 1;
			$paging = "<div style='clear:both'><br /></div><div class='paging'>Pages: ";
			while($pages != 0){
				$selected = ((!empty($_REQUEST["lpage"]) && ($counter == $_REQUEST["lpage"]))||(empty($_REQUEST["lpage"]) && ($counter == 1)))? $selected="selected":"";
				$paging .= "<a href='?lpage=".$counter."' class='$selected'>".$counter."</a>";
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
		
		if(!empty($l_featured)){
			$l_featured_id = "_featured";
		}else{
			$show_bl_type = "<div id='bl_type' class='bl_shortcode_selected'>$type</div>";
			$hidden_limit_text = "<div id='bl_limit' class='bl_shortcode_selected'>$limit</div>";
		}
		
		$results = "<div id='shortcode_list$l_featured_id'>".$results."</div>";
		$results .= "$hidden_limit_text $show_bl_type $show_paging";
		if($echo_this){
			echo $results;
		}else{	
			return $results;
		}
	}
	
	//process paging and listings
	function process_listings_results($show_paging = false, $num_results = false, $l_type = false, $l_ids = false, $order_by = 1, $order_dir = 1){
		global $wpdb;
		
		if(!empty($_REQUEST["filter_search"]) || !empty($l_type)){
			
			if(is_array($l_type) && @isset($l_type[0]) && ($l_type[0]==0)){
				$l_type ="";
			}	
			$returncaluse = Bepro_listings::listitems(array('l_type' => $l_type));
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
			  'listing_page' => $wpdb->escape($_POST["listing_page"]),
			  'l_type' => $wpdb->escape($_POST["l_type"])
		 ), $atts));
		
		//get settings
		$data = get_option("bepro_listings");
		
		//Process user requested Bepro listing types 

		$search_form = "<div class='filter_search_form_shortcode'>
			<form id='filter_search_shortcode_form' method='post' action='".$listing_page."'>
				<input type='hidden' name='name_search' value='".$_POST["name_search"]."'>
				<input type='hidden' name='addr_search' value='".$_POST["addr_search"]."'>
				<input type='hidden' name='filter_search' value='1'>
				<div>
						<span class='searchlabel'>".__($data["cat_heading"], "bepro-listings")."</span>
						";
			if($l_type){	
				$l_type = $l_type[0];			
				$parent = (is_numeric($l_type) && ($l_type != "0"))? $l_type:" ";
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
			if($data["show_geo"] == (1||"on"))	
			$search_form .= "<div>".__("Distance", "bepro-listings").': <select name="distance">
						<option value="">None</option>
						<option value="50" '.(($_POST["distance"] == 50)? 'selected="selected"':"").'>50 miles</option>
						<option value="150" '.((($_POST["distance"] == 150) || empty($_POST["distance"]))? 'selected="selected"':"").'>150 miles</option>
						<option value="250" '.(($_POST["distance"] == 250)? 'selected="selected"':"").'>250 miles</option>
						<option value="500" '.(($_POST["distance"] == 500)? 'selected="selected"':"").'>500 miles</option>
						<option value="1000" '.(($_POST["distance"] == 1000)? 'selected="selected"':"").'>1000 miles</option>
					</select></div>';
				
				//min/max cost
				if($data["show_cost"] == (1||"on"))
				$search_form .= '
					<div><span class="label_sep">'.__("Price Range", "bepro-listings").'</span><span class="form_label">'.__("From", "bepro-listings").'</span><input class="input_text" type="text" name="min_cost" value="'.$_POST["min_cost"].'"><span class="form_label">'.__("To", "bepro-listings").'</span><input class="input_text" type="text" name="max_cost" value="'.$_POST["max_cost"].'"></div>';
				
				if($data["show_date"] == (1))
				$search_form .= '
					<div><span class="label_sep">'.__("Date Range", "bepro-listings").'</span><span class="form_label">'.__("From", "bepro-listings").'</span><input class="input_text" type="text" name="min_date" id="min_date" value="'.$_POST["min_date"].'"><span class="form_label">'.__("To", "bepro-listings").'</span><input class="input_text" type="text" name="max_date" id="max_date" value="'.$_POST["max_date"].'"></div>';
				
				$search_form .= apply_filters("bepro_listings_search_filter_shortcode","");
				
				$search_form .= '
						<div><input type="submit" class="form-submit" value="'.__("Filter", "bepro-listings").'" id="edit-submit" name="find">
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
	function bepro_listings_list_title_template($bp_listing){
		$data = get_option("bepro_listings");
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		if($data["link_new_page"] == 4){
			$permalink = $bp_listing->website;
		}else{
			$permalink = get_permalink( $bp_listing->post_id );
		}
		
		$title = $bp_listing->post_title;
		$title = substr($title,0, 18).((strlen($title) > 18)? "...":"");
		$title = apply_filters("bl_list_title_temp",$title, $bp_listing);
		
			
		/*
		* 1 = go to page
		* 2 = new window
		* 3 = ajax page
		* 4 = hide internal
		*/		
		if($target == 2){
			echo "<div class='result_name'><a href='".$permalink."' target='_blank'>".$title."</a></div>";	
		}elseif($target == 3){
			echo "<div class='result_name'><a class='bl_ajax_result_page' post_id='".$bp_listing->post_id."' href='".$permalink."' target='_blank'>".$title."</a></div>";
		}else{
			echo "<div class='result_name'><a href='".$permalink."'>".$title."</a></div>";	
		}
	}
	
	function bepro_listings_list_category_template($bp_listing){
		echo '<span class="result_type">'.get_the_term_list($bp_listing->post_id, 'bepro_listing_types', '', ', ','').'</span>';
	}
	function bepro_listings_list_featured_template($bp_listing){
		if($bp_listing->featured)
			echo '<span class="result_featured"></span>';
	}
	function bepro_listings_list_phone_template($bp_listing){
		if(!empty($bp_listing->phone)){
			echo "<span class='result_phone'>".__("Phone", "bepro-listings")." - ".$bp_listing->phone."</span>";
		}
	}
	function bepro_listings_list_email_template($bp_listing){
		if(!empty($bp_listing->email)){
			echo "<span class='result_email'>".__("Email", "bepro-listings")." - ".$bp_listing->email."</span>";	
		}
	}
	function bepro_listings_list_image_template($bp_listing){
		$data = get_option("bepro_listings");
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
		}else{
			echo '<span class="result_img"><a href="'.$permalink.'">'.$default_img.'</a></span>';
		}
	}
	function bepro_listings_list_geo_template($bp_listing){
		$data = get_option("bepro_listings");
		if($data["show_geo"])
			echo '<span class="result_title">'.$bp_listing->address_line1;
			
			if(!empty($bp_listing->city)) 
				echo ', '.$bp_listing->city;
				
			if(!empty($bp_listing->state)) 
				echo ', '.$bp_listing->state;
			
			if(!empty($bp_listing->country))
				echo ', '.$bp_listing->country;
			
			echo '</span>';
	}
	function bepro_listings_list_content_template($bp_listing){
		$content =  substr(strip_tags($bp_listing->post_content), 0, 80);
		echo '<span class="result_desc">'.stripslashes(do_shortcode($content)).'...</span>';
	}
	
	function bepro_listings_list_cost_template($bp_listing){
		$data = get_option("bepro_listings");
		if($data["show_cost"]){
			if(is_numeric($bp_listing->cost)){ 
				//formats the price to have comas and dollar sign like currency.
				setlocale(LC_MONETARY, "en_US");
				$cost = ($bp_listing->cost == 0)? "Free" : $data["currency_sign"].sprintf('%01.2f', $bp_listing->cost);
			}else{
				$cost = "Please Contact";
			} 
			//cost
			echo '<span class="result_cost">'.apply_filters("bl_cost_listing_value",$cost).'</span>';
		}
	}
	
	function bepro_listings_list_links_template($bp_listing){
		$data = get_option("bepro_listings");
		$target = empty($data["link_new_page"])? 1:$data["link_new_page"];
		$show_web_link = $data["show_web_link"];
		$details_link = empty($data["details_link"])? "Item":$data["details_link"];
		$permalink = get_permalink( $bp_listing->post_id );
		
			
		/*
		* 1 = go to page
		* 2 = new window
		* 3 = ajax page
		* 4 = hide internal
		*/		
		if($target == 2){
			if(!empty($bp_listing->website) && !empty($show_web_link))
				echo '<span class="result_button"><a href="http://'.$bp_listing->website.'"  target="_blank">Website</a></span>';
			
			if($bp_listing->post_status == "publish")
				echo '<span class="result_button"><a href="'.$permalink.'" target="_blank">'.$details_link.'</a></span>';
		}elseif($target == 3){
			if($bp_listing->post_status == "publish")
				echo '<span class="result_button"><a class="bl_ajax_result_page" post_id="'.$bp_listing->post_id.'" href="'.$permalink.'" '.$target.'>'.$details_link.'</a></span>';
		}elseif($target == 4){
				$website = (stristr($bp_listing->website,"http"))? $bp_listing->website:"http://".$bp_listing->website;
				echo '<span class="result_button"><a href="'.$website.'"  target="_blank">Website</a></span>';
		}else{
			if(!empty($bp_listing->website) && !empty($show_web_link))
				echo '<span class="result_button"><a href="http://'.$bp_listing->website.'">Website</a></span>';
			
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
		$data = get_option("bepro_listings");
		$num_images = $data["num_images"];
		
		//Show wordpress gallery for this page
		$gallery = ($num_images == 0)? "":do_shortcode("[gallery size='".$data["gallery_size"]."' columns=".((!empty($data["gallery_cols"]))? $data["gallery_cols"]:3)."]");
		echo "<div class='bepro_listing_gallery'>".apply_filters("bepro_listings_item_gallery_feature", $gallery)."</div>";
	}
	function bepro_listings_item_after_gallery_template(){
		$data = get_option("bepro_listings");
		$page_id = get_the_ID();
		//show categories
		$cats = get_the_term_list($page_id, 'bepro_listing_types', '', ', ','');
		if($cats) 
		echo $cat_section = "<div class='bepro_listing_category_section'><h3>".__($data["cat_heading"], "bepro-listings")." : </h3>".$cats."</div>";
	}
	function bepro_listings_item_details_template(){
		global $wpdb;
		$page_id = get_the_ID();
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id = ".$page_id);
		//get settings
		$data = get_option("bepro_listings");
		$add_detail_links = empty($data["add_detail_links"])? false:true;
		if(is_numeric($item->cost)){
			//formats the price to have comas and dollar sign like currency.
			$cost = ($item->cost == 0)? __("Free", "bepro-listings") : $data["currency_sign"].sprintf('%01.2f', $item->cost);
		}else{
			$cost = __("Please Contact", "bepro-listings");
		}
		
		if(($data["show_geo"] == "on") || ($data["show_cost"] == "on") || ($data["show_con"] == "on") ){
			echo "<span class='bepro_listing_info'><h3>".__("Details", "bepro-listings")." : </h3>";
			if($data["show_cost"] == "on"){
				echo "<div class='item_cost'>".__(apply_filters("bl_cost_listing_label","Cost"), "bepro-listings")." - ".apply_filters("bl_cost_listing_value",$cost)."</div>";
			}	
				//If we have geographic data then we can show this listings address information
				if($item->lat){
					$map_url = "http://maps.google.com/maps?&z=10&q=".$item->lat."+".$item->lon."+(".urlencode($item->address_line1.", ".$item->city.", ".$item->state.", ".$item->country).")&mrt=yp ";
					echo "<div class='bepro_address_info'><span class='item_label'>".__("Address", "bepro-listings")."</span> - <a href='$map_url' target='_blank'>".__("View Map", "bepro-listings")."</a></div>";
				}
				//If there is contact information then show it
				if($data["show_con"] == "on"){
					if(!empty($item->email)){
						if($add_detail_links){
							$email = "<span class='item_label'>".__("Email", "bepro-listings")."</span> - <a href='mailto:".$item->email."'>".$item->email."</a><br />";
						}else{
							$email = "<span class='item_label'>".__("Email", "bepro-listings")."</span> - ".$item->email."<br />";
						}
					}
					if(!empty($item->phone)){
						if($add_detail_links){
							$phone = "<span class='item_label'>".__("Phone", "bepro-listings")."</span> - <a href='tel:".$item->phone."'>".$item->phone."</a>";
						}else{
							$phone = "<span class='item_label'>".__("Phone", "bepro-listings")."</span> - ".$item->phone;
						}
					}
					echo "<div class='item_contactinfo'>
							".(empty($item->first_name)? "":"<span class='item_label'>".__("First Name", "bepro-listings")."</span> - ".$item->first_name."<br />")."
							".(empty($item->last_name)? "":"<span class='item_label'>".__("Last Name", "bepro-listings")."</span> - ".$item->last_name."<br />")."
							".$email."
							".$phone."
						</div>";
				}
			echo "</span>";
		}
		
	}
	function bepro_listings_item_content_template(){
		$data = get_option("bepro_listings");
		if(!empty($data["show_content"]) && (($data["show_content"] == "on")|| ($data["show_content"] == 1)) ){
			echo "<div class='bepro_listing_desc'>".stripslashes(apply_filters("bepro_listings_item_content",bepro_listings_item_tabs()))."</div>";
		}	
	}
	
	//item page tabs
	function bepro_listings_item_tabs(){
		include(plugin_dir_path( __FILE__ ).'/templates/tabs.php');
	}
	
	//User form for creating Bepro Listings
	function user_create_listing($atts = array()){
		global $wpdb;
		
		extract(shortcode_atts(array(
			  'register' => $wpdb->escape($_POST["register"])
		 ), $atts));
		
		//get settings
		$data = get_option("bepro_listings");
		$default_user_id = $data["default_user_id"];
		$num_images = $data["num_images"];
		$validate = $data["validate_form"];
		$show_cost = $data["show_cost"];
		$show_con = $data["show_con"];
		$show_geo = $data["show_geo"];
		
		if(empty($default_user_id) && empty($register)){
			echo "You must provide a 'default user id' in the admin settings or use the registration=1 option.";	
			return;
		}
		
		if(!empty($_POST["save_bepro_listing"])){
			$wp_upload_dir = wp_upload_dir();
			if(bepro_listings_save()){
				echo "<h2>Listing Successfully Saved</h2>";
			}else{
				echo "<h2>Issue saving your listing. Please contact the website administrator</h2>";
			}
		}
		ob_start();
		
		include( dirname( __FILE__ )."/templates/form.php");
		$results = ob_get_contents();
		ob_end_clean();	
		
		return $results;
	}
	
?>
