<?php
/**
 * Map tab
 *
 * @author 		BePro Listings
 * @package 	bepro_listings/Templates
 */

global $post;

if ( $post->post_content ) : 

$result = process_listings_results(false, 1, false, $post->ID, 1, 1);

$result = $result[0][0];
?>
<script type="text/javascript">
		function launch_frontend_map() {
			icon_1 = new google.maps.MarkerImage('<?php echo plugins_url("bepro-listings/images/icons/icon_1.png", "bepro-listings"); ?>');
			var mapOptions = {
			zoom: <?php echo empty($data["map_zoom"])? 6:$data["map_zoom"]; ?>,
			center: new google.maps.LatLng(<?php echo $result->lat; ?>, <?php echo $result->lon; ?>)
			};

			var map = new google.maps.Map(document.getElementById('page_details_map'),
			  mapOptions);

			var marker = new google.maps.Marker({
			position: map.getCenter(),
			map: map,
			icon:icon_1,
			title: '<?php echo $result->item_name; ?>'
			});
		}
</script>
	<div class="panel entry-content" id="tab-map">
		<div id="bpl_page_location_tab">
			<?php 
				$map_url = "http://maps.google.com/maps?&z=10&q=".$result->lat."+".$result->lon."+(".urlencode($result->address_line1.", ".$result->city.", ".$result->state.", ".$result->country).")&mrt=yp ";
				$address_val = ($data["protect_contact"] == "on")? "<a href='$map_url' target='_blank'>".__("View Map", "bepro-listings")."</a>" : "<ul>".(!empty($result->address_line1)? "<li>".$result->address_line1."</li>":"").(!empty($result->city)? "<li>".$result->city."</li>":"").(!empty($result->state)? "<li>".$result->state."</li>":"").(!empty($result->country)? "<li>".$result->country."</li>":"").(!empty($result->postal)? "<li>".$result->postal."</li>":"")."</ul>";
				echo "<div class=''><h3>".__("Address", "bepro-listings")."</h3> $address_val</div>";
			?>
		</div>
		<div id="page_details_map_wrap"><div id="page_details_map"></div></div>		
	</div>
<?php endif; ?>