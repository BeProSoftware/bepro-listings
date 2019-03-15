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
 
	//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//delete options
delete_option('bepro_listings');
delete_option("bpl_rate_ignore");
delete_option("bpl_nag_ignore");

// For site options in multisite
delete_site_option('bepro_listings'); 
delete_site_option('bpl_rate_ignore'); 
delete_site_option('bpl_nag_ignore'); 

//delete listings and table
$listings = get_posts(array('post_type' => 'bepro_listings','posts_per_page'=>-1, 
    'numberposts'=>-1));
foreach($listings as $listing){
	wp_delete_post($listing->ID, true);
}
//delete orders table
$orders = get_posts(array('post_type' => 'bpl_orders','posts_per_page'=>-1, 
    'numberposts'=>-1));
foreach($orders as $order){
	wp_delete_post($order->ID, true);
}
//delete packages table
$packages = get_posts(array('post_type' => 'bpl_packages','posts_per_page'=>-1, 
    'numberposts'=>-1));
foreach($packages as $package){
	wp_delete_post($package->ID, true);
}
//recreate table
global $wpdb;
$wpdb->query("DROP TABLE ".$wpdb->prefix."bepro_listings");
$wpdb->query("DROP TABLE ".$wpdb->prefix."bepro_listing_typesmeta");
$wpdb->query("DROP TABLE ".$wpdb->prefix."bepro_listing_orders");

//remove BePro Emails if exists
if(class_exists("bepro_email")){
	$bepro_email = new Bepro_email();
	$bepro_email->delete_all_owner_emails("bepro_listings");
}

?>
