<?php
/*
	This file is part of BePro Listings.

    BePro Listings is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    BePro Listings is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with BePro Listings.  If not, see <http://www.gnu.org/licenses/>.
*/	
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class BL_Search_Filter_Widget extends WP_Widget {

	function BL_Search_Filter_Widget() {
		// Instantiate the parent object
		parent::__construct( false, 'Search Filter' );
	}

	function widget( $args, $instance ) {
		// Widget output
		$instance["echo_this"] = true;
		echo $args['before_widget'];
		echo $args['before_title'] .__("Filter Listings", "bepro-listings"). $args['after_title'];
		echo Bepro_listings::search_filter_options($instance);
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
		if ($_POST["id_base"] == "bl_search_filter_widget"){
			$instance = array();
			$instance = apply_filters("bl_search_widget_save_hook", $instance);
			$instance['listing_page'] = attribute_escape($_POST['listing_page']);
			update_option('filter_search_widget', $instance);
			do_action("bl_save_search_widget_action");
			return $instance;
		}
	}

	function form( $instance ) {
		// Output admin widget options form
		?>
		  <p><label>Listing Page url<input name="listing_page"
		type="text" value="<?php echo $instance['listing_page']; ?>" /></label></p>
		<?php
		do_action("bl_search_widget_control", $instance);
	}
}

class BL_Map_Widget extends WP_Widget {

	function BL_Map_Widget() {
		// Instantiate the parent object
		parent::__construct( false, 'Listings Map' );
	}

	function widget( $args, $instance ) {
		// Widget output
		$data = get_option('bepro_map_widget');
		$data["echo_this"] = true;
		echo $args['before_widget'];
		echo $args['before_title'] .__("Listings Map", "bepro-listings"). $args['after_title'];
		bepro_generate_map(array(), $data);
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
		if ($_POST["id_base"] == "bl_map_widget"){
			$data['size'] = attribute_escape($_POST['size']);
			update_option('bepro_map_widget', $data);
			echo "success";
		}
	}

	function form( $instance ) {
		// Output admin widget options form
		 $data = get_option('bepro_map_widget');
		?>
		    <p><label>Size<select name="size">
		    <option value="" <?php echo ($data['size'] == "")? "selected='selected'":""; ?> >Select One</option>
		    <option value="1" <?php echo ($data['size'] == 1)? "selected='selected'":""; ?> >1</option>
		    <option value="2" <?php echo ($data['size'] == 2)? "selected='selected'":""; ?>>2</option>
		    <option value="3" <?php echo ($data['size'] == 3)? "selected='selected'":""; ?>>3</option>
		    <option value="4" <?php echo ($data['size'] == 4)? "selected='selected'":""; ?>>4</option>
		    </select></label></p>
		<?php
	}
}

class BL_Recent_Listings_Widget extends WP_Widget {

	function BL_Recent_Listings_Widget() {
		// Instantiate the parent object
		parent::__construct( false, 'Recent Listings' );
	}

	function widget( $args, $instance ) {
		// Widget output
		$data = get_option('bepro_recent_widget');
	
		echo $args['before_widget'];
		$before_title = $args['before_title'];
		$title = empty($data["heading"])? __("Recent Listings", "bepro-listings"):$data["heading"];
		$after_title = $args['after_title'];
		$num_posts = empty($data["num"])? 5:$data["num"];
		$cat = empty($data["bepro_cat"])? 0: $data["bepro_cat"];
		
		if($cat)
			$r = new WP_Query(array("posts_per_page" => $num_posts, "post_type" => "bepro_listings",'tax_query' =>
			array(array(
				'taxonomy' => 'bepro_listing_types',
				'terms'    => $cat
			))
			));
		else
			$r = new WP_Query(array("posts_per_page" => $num_posts, "post_type" => "bepro_listings"));

		if ( $r->have_posts() ) {

			echo $before_widget;

			if ( $title )
				echo $before_title . $title . $after_title;

			$check = apply_filters("bl_recent_widget_override", array(), $r);
			if(empty($check)){
				echo '<ul class="recent_listings_widget">';

				while ( $r->have_posts()) {
					$r->the_post();

					echo '<li>
						<a href="' . get_permalink() . '" class="sidebar_recent_imgs">
							' . get_the_post_thumbnail( $r->post->ID, 'bepro_listings' ) . '</a>';
					echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>
					</li>';
				}

				echo '</ul>';
			}else{
				echo $check;
			}
			echo $after_widget;
		} 
		echo $args['after_widget'];
	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
		if ($_POST["id_base"] == "bl_recent_listings_widget"){
			$data['heading'] = attribute_escape($_POST['heading']);
			$data['num'] = attribute_escape($_POST['num']);
			$data['bepro_cat'] = attribute_escape($_POST['bepro_cat']);
			update_option('bepro_recent_widget', $data);
			echo "success";
		}
	}

	function form( $instance ) {
		// Output admin widget options form
		$data = get_option('bepro_recent_widget');
	    ?>
		  <p><label>Heading <input type="text" name="heading" value="<?php echo $data['heading']; ?>"></label></p>
		  <p><label>Category <?php wp_dropdown_categories( array('show_option_all' => 'All','selected' => (@$data["bepro_cat"]),'name' => 'bepro_cat','taxonomy' => 'bepro_listing_types') ); ?></label></p>
		  <p><label>Num Listings <select name="num">
		  <option value="" <?php echo ($data['num'] == "")? "selected='selected'":""; ?> >Select One</option>
		  <option value="1" <?php echo ($data['num'] == 1)? "selected='selected'":""; ?> >1</option>
		  <option value="2" <?php echo ($data['num'] == 2)? "selected='selected'":""; ?>>2</option>
		  <option value="3" <?php echo ($data['num'] == 3)? "selected='selected'":""; ?>>3</option>
		  <option value="4" <?php echo ($data['num'] == 4)? "selected='selected'":""; ?>>4</option>
		  <option value="5" <?php echo ($data['num'] == 5)? "selected='selected'":""; ?>>5</option>
		  </select></label></p>
	    <?php
	}
}


function register_bepro_listings_widgets(){
	register_widget( 'BL_Search_Filter_Widget' );
	register_widget( 'BL_Map_Widget' );
	register_widget( 'BL_Recent_Listings_Widget' );
}
?>
