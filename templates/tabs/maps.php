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
			zoom: 6,
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

		<div id="page_details_map_wrap"><div id="page_details_map"></div></div>

	</div>
<?php endif; ?>