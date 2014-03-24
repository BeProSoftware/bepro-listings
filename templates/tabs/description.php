<?php
/**
 * Description tab
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */

global $post;

if ( $post->post_content ) : ?>
	<div class="panel entry-content" id="tab-description">

		<?php the_content(); ?>

	</div>
<?php endif; ?>