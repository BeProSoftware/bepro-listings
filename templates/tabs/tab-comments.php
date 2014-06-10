<?php
/**
 * Single listing tabs
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */

if ( comments_open() ) : ?>
	<li class="comments_tab"><?php _e(apply_filters("bl_comments_label",'Comments'), 'bepro-listings'); ?><?php echo comments_number(' (0)', ' (1)', ' (%)'); ?></li>
<?php endif; ?>