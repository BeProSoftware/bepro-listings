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
	<li class="description_tab"><?php _e(apply_filters("bl_description_label",'Description'), 'bepro-listings'); ?></li>
<?php endif; ?>