<?php
/**
 * Single listing tabs
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */

global $post;

if ( $post->post_content ) : ?>
	<li class="description_tab"><a href="#tab-description"><?php _e('Description', 'bepro_listings'); ?></a></li>
<?php endif; ?>