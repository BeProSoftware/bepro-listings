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
class bepro_widgets {
  function filter_search_control(){
		if ($_POST['id_base'] == "bepro-listings-search-filter")bepro_widgets::bepro_save_widget();
		$data = get_option('filter_search_widget');
		?>
		  <p><label>Listing Page url<input name="listing_page"
		type="text" value="<?php echo $data['listing_page']; ?>" /></label></p>
	  <?php
  }
  function filter_search_widget($args){
	$data = get_option('filter_search_widget');
	$data["echo_this"] = true;
    echo $args['before_widget'];
    echo $args['before_title'] .__("Filter Listings", "bepro-listings"). $args['after_title'];
    echo Bepro_listings::search_filter_options($data);
    echo $args['after_widget'];
  }
  function bepro_save_widget(){
	  if ($_POST["id_base"] == "bepro-listings-search-filter"){
		$data['listing_page'] = attribute_escape($_POST['listing_page']);
		update_option('filter_search_widget', $data);
		echo 'success';
	  }
	  if ($_POST["id_base"] == "bepro-listings-map"){
		$data['size'] = attribute_escape($_POST['size']);
		update_option('bepro_map_widget', $data);
		echo "success";
	  }
	  if ($_POST["id_base"] == "recent-bepro-listings"){
		$data['heading'] = attribute_escape($_POST['heading']);
		$data['num'] = attribute_escape($_POST['num']);
		update_option('bepro_recent_widget', $data);
		echo "success";
	  }
  }
  
  function bepro_map_control(){
	 if ($_POST["id_base"] == "bepro-listings-map")bepro_widgets::bepro_save_widget();
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
  function bepro_map_widget($args){
	$data = get_option('bepro_map_widget');
	$data["echo_this"] = true;
    echo $args['before_widget'];
    echo $args['before_title'] .__("Listings Map", "bepro-listings"). $args['after_title'];
    bepro_generate_map(array(), $data);
    echo $args['after_widget'];
  }

  
  function bepro_recent_control(){
	 if ($_POST["id_base"] == "recent-bepro-listings")bepro_widgets::bepro_save_widget();
     $data = get_option('bepro_recent_widget');
	  ?>
		  <p><label>Heading<input type="text" name="heading" value="<?php echo $data['heading']; ?>"></label></p>
		  <p><label>Num Listings<select name="num">
		  <option value="" <?php echo ($data['num'] == "")? "selected='selected'":""; ?> >Select One</option>
		  <option value="1" <?php echo ($data['num'] == 1)? "selected='selected'":""; ?> >1</option>
		  <option value="2" <?php echo ($data['num'] == 2)? "selected='selected'":""; ?>>2</option>
		  <option value="3" <?php echo ($data['num'] == 3)? "selected='selected'":""; ?>>3</option>
		  <option value="4" <?php echo ($data['num'] == 4)? "selected='selected'":""; ?>>4</option>
		  <option value="5" <?php echo ($data['num'] == 5)? "selected='selected'":""; ?>>5</option>
		  </select></label></p>
	  <?php
  }
  function bepro_recent_widget($args){
	$data = get_option('bepro_recent_widget');
	
    echo $args['before_widget'];
    $before_title = $args['before_title'];
	$title = empty($data["heading"])? __("Recent Listings", "bepro-listings"):$data["heading"];
	$after_title = $args['after_title'];
	$num_posts = empty($data["num"])? 5:$data["num"];
	
	$r = new WP_Query(array("posts_per_page" => $num_posts, "post_type" => "bepro_listings"));

	if ( $r->have_posts() ) {

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

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

		echo $after_widget;
	} 
    echo $args['after_widget'];
  }

  function register(){
    wp_register_sidebar_widget("bepro-listings-search-filter",'Bepro Listings Search Filter', array('bepro_widgets', 'filter_search_widget'));
    wp_register_widget_control("bepro-listings-search-filter",'Bepro Listings Search Filter', array('bepro_widgets', 'filter_search_control'));
    wp_register_sidebar_widget("bepro-listings-map",'Bepro Listings Map', array('bepro_widgets', 'bepro_map_widget'));
    wp_register_widget_control("bepro-listings-map",'Bepro Listings Map', array('bepro_widgets', 'bepro_map_control'));
	
    wp_register_sidebar_widget("recent-bepro-listings",'Recent Bepro Listings', array('bepro_widgets', 'bepro_recent_widget'));
    wp_register_widget_control("recent-bepro-listings",'Recent Bepro Listings', array('bepro_widgets', 'bepro_recent_control'));
  }
  
  
  

}
?>
