<?php

//BePro Listings Installation Wizard
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<h1><?php _e("BePro Listings INSTALLATION WIZARD","bepro-listings"); ?></h1>
<p><?php _e('Use the following steps to help you setup BePro Listings. Otherwise,',"bepro-listings"); ?> <a href="index.php?page=bepro-listngs-dashboard"><?php _e("Click Here","bepro-listings"); ?></a> <?php _e('to skip',"bepro-listings"); ?></p>
<div class="swMain">
	<ul class="anchor">
	   <li class="nav-tab-wrapper">
		  <div id="tb1_1_highlight" class="selected nav-tab">
			  <span class="stepNumber">1.</span>
			  <span class="stepDesc">
			  <?php _e("Pages","bepro-listings"); ?>
			  </span>
		  </div>
	   </li>
	   <li class="nav-tab-wrapper">
		  <div id="tb1_2_highlight" class=" nav-tab">
		  <span class="stepNumber">2.</span>
		  <span class="stepDesc">
		  <?php _e("Options","bepro-listings"); ?>
		  </div>
		  </a>
	   </li>
	   <li class="nav-tab-wrapper">
		  <div id="tb1_3_highlight" class=" nav-tab">
		  <span class="stepNumber">3.</span>
		  <span class="stepDesc">
		  <?php _e("Labels","bepro-listings"); ?>
		  </span>
		  </div>
	   </li>
	   <li class="nav-tab-wrapper">
		  <div id="tb1_4_highlight" class=" nav-tab">
		  <span class="stepNumber">4.</span>
		  <span class="stepDesc">
		  <?php _e("Summary","bepro-listings"); ?>
		  </span>
		  </div>
	   </li>
	</ul>

	<div class="stepContainer">
		<div class="content">
			<div id="tb1_1" class="tab-pane active">
				<h2><?php _e("STEP 1: CREATE WORDPRESS PAGES","bepro-listings"); ?></h2>
				<div class="wizard_info">
					<form name="form_1_wizard" id="form_1_wizard" method="post">
						<input type="hidden" name="action" value="bl_create_demo_pages" />
						<table>
							<tr><td><?php _e("Select","bepro-listings"); ?></td><td><?php _e("Page Name","bepro-listings"); ?></td><td><?php _e("Description","bepro-listings"); ?></td><td><?php _e("Shortcode","bepro-listings"); ?></td></tr>
							<tr><td><input type="checkbox" name="demo_pages[]" value="Listings" checked="checked"></td><td><?php _e("Listings","bepro-listings"); ?></td><td><?php _e("Allow users to search and view the listings","bepro-listings"); ?></td><td>[bl_all_in_one]</td></tr>
							<tr><td><input type="checkbox" name="demo_pages[]" value="My Listings" checked="checked"></td><td><?php _e("My Listings","bepro-listings"); ?></td><td><?php _e("Allow users to edit their listings on the front end","bepro-listings"); ?></td><td>[bl_my_listings]</td></tr>
							<tr><td><input type="checkbox" name="demo_pages[]" value="Submissions" checked="checked"></td><td><?php _e("Submissions","bepro-listings"); ?></td><td><?php _e("Allow users to submit listings from the front end as visitors","bepro-listings"); ?></td><td>[create_listing_form]</td></tr>
						</table>
						<input type="submit" value="Create Pages &rsaquo;&rsaquo;">
					</form>
				</div>
				<div class="wizard_info"><h3><?php _e("DETAILS","bepro-listings"); ?></h3><p><?php _e('A few pages are needed to capture user submissions on the front end and allow users to search results. This wizard will help you to install those pages. You can rename them or delete them once you see how they work.','bepro-listings')?><br /><br /><?php _e("Or you can","bepro-listings"); ?> <span class="bl_skip_wizard_step"><?php _e("Skip this Step","bepro-listings"); ?></span>.</p></div>
				</div>
		</div>
	</div>
	<div class="stepContainer">
		<div class="content">
			<div id="tb1_2" class="tab-pane hidden">
				<h2><?php _e("STEP 2: Configure BePro Listings Options","bepro-listings"); ?></h2>
				<div class="wizard_info">
					<form name="form_2_wizard" id="form_2_wizard" method="post">
						<input type="hidden" name="action" value="bl_update_demo_options" />
						<table>
							<tr><td><?php _e("Select","bepro-listings"); ?></td><td><?php _e("Feature","bepro-listings"); ?></td><td><?php _e("Description","bepro-listings"); ?></td></tr>
							<tr><td><input type="checkbox" name="show_imgs" value="1" checked="checked"></td><td><?php _e("Show Images","bepro-listings"); ?></td><td><?php _e("Show images on the listings","bepro-listings"); ?></td></tr>
							<tr><td><input type="checkbox" name="show_cost" value="1" checked="checked"></td><td><?php _e("Show Cost","bepro-listings"); ?></td><td><?php _e("Show Prices on Listings","bepro-listings"); ?></td></tr>
							<tr><td><input type="checkbox" name="show_con" value="1" checked="checked"></td><td><?php _e("Show Contact","bepro-listings"); ?></td><td><?php _e("Show and search names and other contact info","bepro-listings"); ?></td></tr>
							<tr><td><input type="checkbox" name="show_geo" value="1" checked="checked"></td><td><?php _e("Show Geo","bepro-listings"); ?></td><td><?php _e("Load Google Maps and address search","bepro-listings"); ?></td></tr>
						</table>
						<input type="submit" value="Save Options &rsaquo;&rsaquo;">
					</form>
				</div>
				<div class="wizard_info"><h3><?php _e("DETAILS","bepro-listings"); ?></h3><p><?php _e("BePro Listings has lots of configuration options. They make sure that the plugin only loads the features your customers need. The options on the left include a few of our major avaialble features. You will have access to the exhaustive list of options later.","bepro-listings"); ?> <br /><br /><?php _e("Or you can","bepro-listings"); ?> <span class="bl_skip_wizard_step"><?php _e("Skip this Step","bepro-listings"); ?></span>.</p></div>
			</div>
		</div>
	</div>
	<div class="stepContainer">
		<div class="content">
			<div id="tb1_3" class="tab-pane hidden">
				<h2><?php _e("STEP 3: BePro Listings Labels","bepro-listings"); ?></h2>
				<div class="wizard_info">
					<form name="form_3_wizard" id="form_3_wizard" method="post">
						<input type="hidden" name="action" value="bl_update_demo_labels" />
						<table>
							<tr><td><?php _e("Label","bepro-listings"); ?></td><td><?php _e("Feature","bepro-listings"); ?></td><td><?php _e("Description","bepro-listings"); ?></td></tr>
							<tr><td><input type="input" name="cat_heading" value="Categories"></td><td><?php _e("Cat Heading","bepro-listings"); ?></td><td><?php _e("Plural form of category heading","bepro-listings"); ?></td></tr>
							<tr><td><input type="input" name="cat_empty" value="No Categories"></td><td><?php _e("Cat Empty","bepro-listings"); ?></td><td><?php _e("What to show when there are no categories matching search results","bepro-listings"); ?></td></tr>
							<tr><td><input type="input" name="cat_singular" value="Category"></td><td><?php _e("Cat Singular","bepro-listings"); ?></td><td><?php _e("Heading when a single category is selected during search","bepro-listings"); ?></td></tr>
							<tr><td><input type="input" name="cat_permalink" value="listing_types"></td><td><?php _e("Category Permalink","bepro-listings"); ?></td><td><?php _e("Url path for listing categories","bepro-listings"); ?></td></tr>
							<tr><td><input type="input" name="permalink" value="listings"></td><td><?php _e("Permalink","bepro-listings"); ?></td><td><?php _e("Url path for all listings","bepro-listings"); ?></td></tr>
						</table>
						<input type="submit" value="Save Labels &rsaquo;&rsaquo;">
					</form>
				</div>
				<div class="wizard_info"><h3><?php _e("DETAILS","bepro-listings"); ?></h3><p><?php _e("BePro Listings currently supports a range of languages and directory niches. We allow you to change various terms to match the needs of your particular website. The options on the left include features affecting all users.","bepro-listings"); ?> <br /><br /><?php _e("Or you can","bepro-listings"); ?> <span class="bl_skip_wizard_step"><?php _e("Skip this Step","bepro-listings"); ?></span>.</p></div>
			</div>
		</div>
	</div>
	<div class="stepContainer">
		<div class="content">
			<div id="tb1_4" class="tab-pane hidden">
				<h2><?php _e("STEP 4: Summary","bepro-listings"); ?></h2>
				<div class="wizard_info">
					<p><strong><?php _e("Setup Complete.","bepro-listings"); ?></strong> <a href="index.php?page=bepro-listngs-dashboard"><button><?php _e("GO TO WELCOME SCREEN","bepro-listings"); ?> &rsaquo;&rsaquo;</button></a></p>
					<p><?php _e("You are now ready to use the BePro Listings plugin. For next steps, consider one of the following.","bepro-listings"); ?></p>
					<ul id="next_steps">
						<li><a href="edit.php?post_type=bepro_listings" target="_blank"><?php _e("Manage Listings","bepro-listings"); ?></a> <?php _e("Via the Admin","bepro-listings"); ?></li>
						<li><a href="edit.php?post_type=bepro_listings&page=bepro_listings_options" target="_blank"><?php _e("View","bepro-listings"); ?></a> <?php _e("all configuration options","bepro-listings"); ?></li>
						<li><a href="post-new.php?post_type=bepro_listings" target="_blank"><?php _e("Create","bepro-listings"); ?></a> <?php _e("a new listing","bepro-listings"); ?></li>
						<li><a href="edit.php?post_type=bepro_listings&page=bepro_listings_status" target="_blank"><?php _e("View","bepro-listings"); ?></a> <?php _e("Plugin Status & Errors","bepro-listings"); ?></li>
					</ul>
					<h4><?php _e("CONTRIBUTE","bepro-listings"); ?></h4>
					<p><?php _e("Hopefully you like our plugin. Consider leaving a review or feedback. Spreading the word is one of the best ways to help support free solutions like this one.","bepro-listings"); ?> </p>
					<ul id="say_hi">
						<li><?php _e("Leave a","bepro-listings"); ?> <a href="https://wordpress.org/support/view/plugin-reviews/bepro-listings?filter=5" target="_blank"><?php _e("Review","bepro-listings"); ?></a> <?php _e("on wordpress.org","bepro-listings"); ?></li>
						<li><?php _e("Leave a","bepro-listings"); ?> <a href="https://www.beprosoftware.com/shop/bepro-listings" target="_blank"><?php _e("Review","bepro-listings"); ?></a> <?php _e("on beprosoftware.com","bepro-listings"); ?></li>
						<li><?php _e("Say Hi on Twitter","bepro-listings"); ?> <a href="https://twitter.com/beprosoftware" target="_blank">@BeProSoftware</a></li>
						<li><?php _e("Submit code on","bepro-listings"); ?> <a href="https://github.com/beprosoftware" target="_blank">github</a></li>
						<li><?php _e("We also appreciate","bepro-listings"); ?> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=support@beprosoftware.com&item_name=Donation+for+BePro+Listings" target="_blank"><?php _e("Donations","bepro-listings"); ?></a> <?php _e("via paypal","bepro-listings"); ?> </li>
					</ul>
				</div>
				<div class="wizard_info">
					<h3><?php _e("MORE INFO","bepro-listings"); ?></h3>
					<p><?php _e("Interested in learning more about BePro Lisitngs? With our library of Video Screencast Tutorials and over 20 addons and themes, this plugin has lots of ways to extend your user experience. Consider the following options","bepro-listings"); ?></p>
					<ul id="info_options">
						<li><?php _e("Browse the","bepro-listings"); ?> <a href="https://www.beprosoftware.com/documentation/bepro-listings/" target="_blank"><?php _e("Documentation","bepro-listings"); ?></a> <?php _e("on beprosoftware.com","bepro-listings"); ?></li>
						<li><?php _e("Shop for","bepro-listings"); ?> <a href="https://www.beprosoftware.com/shop/" target="_blank"><?php _e("Addons","bepro-listings"); ?></a> <?php _e("on beprosoftware.com","bepro-listings"); ?></li>
						<li><?php _e("Visit our","bepro-listings"); ?> <a href="https://www.beprosoftware.com/forums/" target="_blank"><?php _e("Forums","bepro-listings"); ?></a> <?php _e("on beprosoftware.com","bepro-listings"); ?></li>
						<li><?php _e("Search through our","bepro-listings"); ?> <a href="http://beprothemes.com/demos" target="_blank"><?php _e("Demos","bepro-listings"); ?></a> <?php _e("on beprothemes.com","bepro-listings"); ?></li>
						<li><?php _e("Return to our","bepro-listings"); ?> <a href="https://wordpress.org/plugins/bepro-listings/" target="_blank"><?php _e("Plugin Page","bepro-listings"); ?></a> <?php _e("on wordpress.org","bepro-listings"); ?></li>
						<li><?php _e("View Free","bepro-listings"); ?> <a href="https://www.beprosoftware.com/documentation/category/video_documentation/" target="_blank"><?php _e("Video Screencasts Tutorials","bepro-listings"); ?></a> <?php _e("on beprosoftware.com","bepro-listings"); ?></li>
						<li><?php _e("View Premium","bepro-listings"); ?> <a href="https://www.beprosoftware.com/subscriptions/" target="_blank"><?php _e("Video Screencasts Tutorials","bepro-listings"); ?></a> <?php _e("on beprosoftware.com","bepro-listings"); ?></li>
						<li><?php _e("Try our Premium","bepro-listings"); ?> <a href="https://www.beprosoftware.com/themes" target="_blank"><?php _e("Themes","bepro-listings"); ?></a> <?php _e("on beprosoftware.com","bepro-listings"); ?></li>
						<li><?php _e("Get Support from our","bepro-listings"); ?> <a href="http://www.beprosoftware.com/services/" target="_blank">BePro Software Team</a> <?php _e("on beprosoftware.com","bepro-listings"); ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>