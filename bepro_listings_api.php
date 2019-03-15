<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class bepro_listings_api{
	function __construct(){
	
	}
	
	function answer($request){
		$response = "";
		$action = (string)$request->action;
		foreach($request->records as $listing){
			switch($action){
				case "update":
					$response .= $this->update($listing);
					break;
				case "delete":
					$response .= $this->delete($listing);
					break;
				default:
					$response .= "<response><error>5</error></response>";
			}
		}
		echo $response;
	}
	
	function update($listings){
		if(!$listings->listing->item_name) return "<response><error>3</error></response>";
		$data = get_option("bepro_listings");
		$_POST["save_bepro_listing"] = 1;
		$response_text = "";
		
		try{
			foreach($listings->listing as $listing){
				$_POST["bepro_post_id"] = @(string)$listing->id;
				$_POST["item_name"] = @(string)$listing->item_name;
				$_POST["content"] = @(string)$listing->content;
				$_POST["categories"] = @(string)$listing->categories;
				
				if(@$data["show_cost"]){
					$_POST["cost"] = @(string)$listing->cost;
				}
				
				if(@$data["show_con"]){
					$_POST["first_name"] = @(string)$listing->first_name;
					$_POST["last_name"] = @(string)$listing->last_name;
					$_POST["email"] = @(string)$listing->email;
					$_POST["phone"] = @(string)$listing->phone;
					$_POST["website"] = @(string)$listing->website;
				}
				
				if(@$data["show_geo"]){
					$_POST["address_line1"] = @(string)$listing->address_line1;
					$_POST["city"] = @(string)$listing->city;
					$_POST["postcode"] = @(string)$listing->postcode;
					$_POST["state"] = @(string)$listing->state;
					$_POST["country"] = @(string)$listing->country;
					$_POST["lat"] = @(string)$listing->lat;
					$_POST["lon"] = @(string)$listing->lon;
				}
				
				if(@$data["show_img"]){
					$_POST["photo"] = @$listing->photo;
				}
				$result = bepro_listings_save(false, true);
				if(is_numeric($result))
					$response_text .= "<listing><id>".$result."</id></listing>";
					
				
			}
			return "<response><error>0</error>".$response_text."</response>";
		}catch(Exception $e){
			return "<response><error>6</error><listing><id>".@$listing->id."</id><item_name>".@$listing->item_name."</item_name></listing></response>";
		}
	}
	
	function delete($listings){
		if(!$listings->listing->id) return "<response><error>4</error></response>";
		if((string)$listings->listing->id == "*"){
			try{
				$all_listings = get_posts(array('post_type' => 'bepro_listings', 'posts_per_page'=>-1, 
    'numberposts'=>-1));
				foreach($all_listings as $del_listing){
					wp_delete_post($del_listing->ID, true);
				}
				return "<response><error>0</error><listing><id>".((string)$listing->listing->id)."</id></listing></response>";
			}catch(Exception $e){
				return "<response><error>6</error></response>";
			}
		}else if(is_numeric((string)$listings->listing->id)){
			$removed = array();
			try{
				foreach($listings->listing as $listing){
					if(@(string)$listing->id && wp_delete_post((string)$listing->id, true)){ 
						$removed[] = (string)$listing->id;
					}
				}
				$return_listings = "";
				foreach($removed as $remove){
					$return_listings .= "<listing><id>".$remove."</id></listing>";
				}
				
				return "<response><error>0</error>".$return_listings."</response>";
			}catch(Exception $e){
				return "<response><error>6</error></response>";
			}
		}
	}
}
?>