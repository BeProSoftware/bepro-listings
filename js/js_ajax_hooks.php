<?php


?>

<script type="text/javascript">

	function bl_ajax_complete(){
		jQuery('body').css('cursor', 'default'); 
		jQuery(".bl_frontend_search_section").css("opacity", "1")
		if(jQuery("#bl_size"))
			jQuery("#bl_size").remove();
		if(jQuery("#bl_pop_up"))
			jQuery("#bl_pop_up").remove();
		if(jQuery("#bl_ctype"))
			jQuery("#bl_ctype").remove();;
		if(jQuery("#bl_cat"))
			jQuery("#bl_cat").remove();;
		if(jQuery("#bl_limit"))
			jQuery("#bl_limit").remove();
		if(jQuery("#bl_type"))
			jQuery("#bl_type").remove();
		if(jQuery("#bl_order"))
			jQuery("#bl_order").remove();
		if(jQuery("#bl_show_paging"))
			jQuery("#bl_show_paging").remove();
		if(jQuery("#bl_form_id"))
			jQuery("#bl_form_id").remove();
		if(jQuery("#bl_l_type"))
			jQuery("#bl_l_type").remove();
		
		<?php do_action("bl_ajax_complete_hook"); ?>
	}

	function bl_ajax_end(options){
		if(((options.cat).length > 0) && (jQuery("#shortcode_cat")))
			jQuery("#shortcode_cat").replaceWith(options.cat);
		if(((options.listings).length > 0) && (jQuery("#shortcode_list")))
			jQuery("#shortcode_list").replaceWith(options.listings);
		if(((options.filter).length > 0) && (jQuery("#filter_search_form")))
			jQuery("#filter_search_form").replaceWith(options.filter);
		if((options.short_filter) && ((options.short_filter).length > 0) && (jQuery(".filter_search_form_shortcode")))
			jQuery(".filter_search_form_shortcode").replaceWith(options.short_filter);
		if(((options.search).length > 0) && (jQuery("#listingsearchform")))
			jQuery(".search_listings").replaceWith(options.search);
		if((options.map) && ((options.map).length > 0) && (jQuery("#shortcode_map")))
			jQuery("#shortcode_map").replaceWith(options.map);	
		
		<?php do_action("bl_ajax_end_hook"); ?>
		
		if(jQuery(".bl_date_input") && jQuery.isFunction("datepicker"))
			jQuery(".bl_date_input").datepicker();
		if(jQuery(".bl_time_input") && jQuery.isFunction("timepicker"))	
			jQuery(".bl_time_input").timepicker();
	}
	
	function bl_ajax_get_shortcode_vals(cat){ 
		returnstr = '';
		get_cat = typeof cat !== 'undefined' ? false : true; //if cat is empty then get cat filter values
		if(jQuery("#bl_size").length > 0)
			returnstr = returnstr + "&size=" + jQuery("#bl_size").html();
		if(jQuery("#bl_pop_up").length > 0)
			returnstr = returnstr + "&pop_up=" + jQuery("#bl_pop_up").html();
		if(jQuery("#bl_ctype").length > 0)
			returnstr = returnstr + "&ctype=" + jQuery("#bl_ctype").html();
		if(jQuery("#bl_limit").length > 0)
			returnstr = returnstr + "&limit=" + jQuery("#bl_limit").html();
		if(jQuery("#bl_type").length > 0)
			returnstr = returnstr + "&type=" + jQuery("#bl_type").html();
		if(jQuery("#bl_order_dir").length > 0)
			returnstr = returnstr + "&order_dir=" + jQuery("#bl_order_dir").html();
		if(jQuery("#bl_order_by").length > 0)
			returnstr = returnstr + "&order_by=" + jQuery("#bl_order_by").html();
		if(jQuery("#bl_show_paging").length > 0)
			returnstr = returnstr + "&show_paging=" + jQuery("#bl_show_paging").html();
		if(jQuery("#bl_form_id").length > 0)
			returnstr = returnstr + "&bl_form_id=" + jQuery("#bl_form_id").html();
		
		
		if(get_cat == true){
			if(jQuery("#bl_cat").length > 0)
				returnstr = returnstr + "&cat=" + jQuery("#bl_cat").html();
			if(jQuery("#bl_l_type").length > 0)
				returnstr = returnstr + "&l_type=" + jQuery("#bl_l_type").html();
		}
		
		<?php do_action("bl_ajax_get_shortcode_vals_hook"); ?>
			
		return returnstr;	
	}

</script>