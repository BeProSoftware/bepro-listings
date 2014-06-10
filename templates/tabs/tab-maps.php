<?php
/**
 * Single listing tabs
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */

global $post;

if ( $post->post_content ) : ?>
	<li class="map_tab"><?php _e('View Map', 'bepro_listings'); ?></li>
<?php endif; ?>