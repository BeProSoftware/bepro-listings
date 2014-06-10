<?php
/**
 * The Template for displaying all single listings.
 *
 * Override this template by copying it to yourtheme/template/single-listing.php
 *
 * @author 		BePro Software Team
 * @package 	bepro_listings/Templates
 * @version     2.0.0
 */

get_header("listings"); ?>
<div class="entry">
		<?php
		global $wpdb, $post;
		//get listing information related to this post
		$page_id = $post->ID;
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.BEPRO_LISTINGS_TABLE_NAME." WHERE post_id = ".$page_id);
		//get settings
		$data = get_option("bepro_listings");
		?>
		
		<?php while ( have_posts() ) : the_post(); ?>
		
		<?php
		if($item){
			$page_template = plugin_dir_path( __FILE__ )."/content-single-listing.php";
			$page_template = apply_filters("bepro_listings_change_page_template",$page_template,$item);
			include($page_template); 
		}else{
			the_content();
		}		
		?>

		<?php endwhile; // end of the loop. ?>
</div>

<?php get_sidebar("listings"); ?>
<?php get_footer("listings"); ?>