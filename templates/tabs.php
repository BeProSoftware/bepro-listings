<?php
/**
 * Single listing tabs
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */

// Get tabs
ob_start();

do_action('bepro_listings_tabs');

$tabs = trim( ob_get_clean() );

if ( ! empty( $tabs ) ) : ?>
	<div class="bepro_listings_tabs">
		<ul class="tabs">
			<?php echo $tabs; ?>
		</ul>
		<?php do_action('bepro_listings_tab_panels'); ?>
	</div>
<?php endif; ?>