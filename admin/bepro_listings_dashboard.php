<?php
/**
 * BePro Listings dashboard page
 */
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="wrap about-wrap">

	<h1><?php _e( 'Welcome to BePro Listings!', "bepro-listings"); ?></h1>
	
	<div class="about-text">
		<?php _e('Congratulations, you are now using the latest version of BePro Listings. With lots of ways to customize, this software is ideal for creating your custom listing needs. This page shows a few of the recent enhancements to the plugin.', "bepro-listings" ); ?>
	</div>
	
	<h2 class="nav-tab-wrapper">
		<a href="#" class="nav-tab nav-tab-active">
			<?php _e( "What's New", "bepro-listings" ); ?>
		</a>
	</h2>
	
	<div class="changelog">
		<h3><?php _e( 'You are using', "bepro-listings" ); _e( 'BePro Listings Version:', "bepro-listings" ); echo " ".BEPRO_LISTINGS_VERSION; ?>   </h3>
	
		<div class="feature-section images-stagger-right">
			<h4><?php _e( 'Directory Theme', "bepro-listings" ); ?></h4>
			<p><?php _e( 'We added compatability for our new <a href="https://www.beprosoftware.com/shop/bepro-business-directory/" target="_blank">Business Directory Theme</a>. This includes lots of new styling elements and features as well as layout tweaks', "bepro-listings" ); ?></p>
			
			<h4><?php _e( 'Markers with the same location', "bepro-listings" ); ?></h4>
			<p><?php _e( 'Now when your locations have the exact same address, they will show up as a scrollable pop up on the map. Before, they would be shown as a clustered marker with no way to get the details. This is ideal for situations like apartment buildings where multiple units share the same address.', "bepro-listings" ); ?></p>
			
			<h4><?php _e( 'Improved Payment Features', "bepro-listings" ); ?></h4>
			<p><?php _e( 'We refined order management for you the administrator. This includes the expiration of listings and the ability to assign orders to users.', "bepro-listings" ); ?></p>
			
			<h4><?php _e( 'Better Form Builder Integration', "bepro-listings" ); ?></h4>
			<p><?php _e( 'We fixed elements focusing on how our <a href="https://www.beprosoftware.com/shop/bepro-listings-form-builder/" target="_blank">Form Builder</a> addon integrates with BePro Listings. Latest enhancements are focused on security', "bepro-listings" ); ?></p>
			
			<h4><?php _e( 'Featured Listings', "bepro-listings" ); ?></h4>
			<p><?php _e( 'We fixed a bug which caused visual errors when trying to utilize the featured listing feature. Now it works as showcased in our <a href="https://www.beprosoftware.com/documentation/featured-listings/" target="_blank">Featured Listings</a> documentation', "bepro-listings" ); ?></p>
			
			<h4><?php _e( 'Google API', "bepro-listings" ); ?></h4>
			<p><?php _e( 'Google has updated their systems requiring everyone using maps to use register on their website to get API keys. We upgraded the plugin to accept a javascript and geocode api since they need to be configured differently.', "bepro-listings" ); ?></p>
			
			<h4><?php _e( 'Support Us', "bepro-listings" ); ?></h4>
			<p><?php _e( 'Hopefully you like BePro Listings. Consider sharing your experience with other users by leaving a <a href="http://wordpress.org/support/view/plugin-reviews/bepro-listings" target="_blank">review on wordpress.org</a>. Your feedback helps to support development of this free solution and informs fellow wordpress users of its usefulness.', "bepro-listings" ); ?></p>
		</div>
	</div>

</div>