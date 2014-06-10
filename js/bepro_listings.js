// bepro_listings.js

// do not distribute without bepro_listings.php



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



jQuery(document).ready(function($) {

	$('.bepro_listings_tabs .panel').hide();
	
	$('.bepro_listings_tabs ul.tabs li a').click(function(){
		
		var $tab = $(this);
		var $tabs_wrapper = $tab.closest('.bepro_listings_tabs');
		
		$('ul.tabs li', $tabs_wrapper).removeClass('active');
		$('div.panel', $tabs_wrapper).hide();
		$('div' + $tab.attr('href')).show();
		$tab.parent().addClass('active');
		
		return false;	
	});
	
	$('.bepro_listings_tabs').each(function() {
		var hash = window.location.hash;
		if (hash.toLowerCase().indexOf("comment-") >= 0) {
			$('ul.tabs li.reviews_tab a', $(this)).click();
		} else {
			$('ul.tabs li:first a', $(this)).click();
		}
	});
	
	if($("#bepro_listings_tabs")){
		$( "#bepro_listings_tabs" ).tabs();
	}
	
	map_count = 0;
	$(".frontend_bepro_listings_vert_tabs").easyResponsiveTabs({           
	type: 'vertical',           
	width: 'auto',
	fit: true,
	activate: function(event) { 
		if((event.target.className == "map_tab resp-tab-item resp-tab-active") && (map_count == 0)){
			launch_frontend_map();
			map_count++;
		} 
	}
	});
});	