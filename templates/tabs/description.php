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

		<?php 
		$content = apply_filters( 'the_content', $post->post_content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		$content = stripslashes($content);
		echo $content;
		?>

	</div>
<?php endif; ?>