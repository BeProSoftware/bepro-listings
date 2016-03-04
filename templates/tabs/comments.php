<?php
/**
 * Reviews tab
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */

global $post;

if ( comments_open() ) : ?>
	<div class="panel entry-content" id="tab-comments">

		<?php 
			$withcomments = 1;
			ob_start();
			comments_template();
			$comments = ob_get_contents();
			ob_end_clean();
			if(!empty($comments)){
				echo $comments;
			}else{
				try{
					get_template_part( "comments" );
				}catch(Exception $e){
					echo __("No Comment Template Found.");
				}
			}
		?>

	</div>
<?php endif; ?>