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
	<div class="frontend_bepro_listings_vert_tabs">
		<ul class="resp-tabs-list">
			<?php echo $tabs; ?>
		</ul>
		<div class="resp-tabs-container">
		<?php do_action('bepro_listings_tab_panels'); ?>
		</div>
	</div>
<?php endif; ?>