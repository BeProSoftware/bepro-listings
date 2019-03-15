<?php
/**
 * Single listing tabs
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post;

if ( $post->post_content ) : ?>
	<li class="map_tab"><?php _e('View Map', 'bepro-listings'); ?></li>
<?php endif; ?>