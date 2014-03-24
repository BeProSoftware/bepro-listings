<?php
/**
 * Single listing tabs
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */

if ( comments_open() ) : ?>
	<li class="comments_tab"><a href="#tab-comments"><?php _e('Comments', 'bepro_listings'); ?><?php echo comments_number(' (0)', ' (1)', ' (%)'); ?></a></li>
<?php endif; ?>