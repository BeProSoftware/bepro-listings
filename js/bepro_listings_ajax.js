jQuery(document).ready(function(){
	jQuery("body").on("click",".clear_search",function(element){
		element.preventDefault();
		shortcode_vals = bl_ajax_get_shortcode_vals(true);
		fairy_dust = "";
		bl_ajax_init();
		bl_ajax_fetch_content(fairy_dust, shortcode_vals);
	});
	
	jQuery("body").on("click",".cat_list_item a",function(element){
		element.preventDefault();
		href = jQuery(this).attr("href");
		raw_val = href.split("l_type=");
		l_type = raw_val[1];
		shortcode_vals = bl_ajax_get_shortcode_vals(true);
		bl_ajax_init();
		fairy_dust = "";
		if(jQuery("#filter_search_form").length > 0){
			jQuery("#filter_search_form input[type=checkbox]").prop('checked', false);
			fairy_dust = jQuery("#filter_search_form").serialize();
		}else if(jQuery("#listingsearchform").length > 0){
			if(jQuery("input[name=filter_search]").val() == 1)
				fairy_dust = jQuery("#listingsearchform").serialize();
		}

		fairy_dust = fairy_dust + "&l_type[]=" + l_type;		
			
		bl_ajax_fetch_content(fairy_dust, shortcode_vals);
	});
	
	jQuery("body").on("click",".bl_ajax_result_page",function(element){
		element.preventDefault();
		post_id = jQuery(this).attr("post_id");
		bl_ajax_get_page(post_id);
	});	
	
	jQuery("body").on("click",".paging a",function(element){
		element.preventDefault();
		href = jQuery(this).attr("href");
		raw_val = href.split("lpage=");
		lpage = raw_val[1];
		shortcode_vals = bl_ajax_get_shortcode_vals();
		bl_ajax_init();
		fairy_dust = "";
		if(jQuery("#filter_search_form").length > 0){
			fairy_dust = jQuery("#filter_search_form").serialize();
		}else if(jQuery("#listingsearchform").length > 0){
			if(jQuery("input[name=filter_search]").val() == 1)
				fairy_dust = jQuery("#listingsearchform").serialize();
		}

		fairy_dust = fairy_dust + "&lpage=" + lpage;		
			
		bl_ajax_fetch_content(fairy_dust, shortcode_vals);	
	});
	
	jQuery("body").on("submit","#result_page_back_button", function(element){
		element.preventDefault();
		fairy_dust = jQuery("#result_page_back_button").serialize();
		shortcode_vals = bl_ajax_get_shortcode_vals();
		bl_ajax_init();
		bl_ajax_fetch_content(fairy_dust, shortcode_vals);
	});
	
	jQuery("body").on("submit","#listingsearchform", function(element){
		element.preventDefault();
		fairy_dust = jQuery("#listingsearchform").serialize();
		shortcode_vals = bl_ajax_get_shortcode_vals(true);
		bl_ajax_init();
		bl_ajax_fetch_content(fairy_dust, shortcode_vals); 
	});
	jQuery("body").on("submit","#filter_search_form", function(element){
		element.preventDefault();
		fairy_dust = jQuery("#filter_search_form").serialize();
		shortcode_vals = bl_ajax_get_shortcode_vals(true);
		bl_ajax_init();
		bl_ajax_fetch_content(fairy_dust, shortcode_vals);
	});
	
	jQuery("body").on("submit","#filter_search_shortcode_form", function(element){
		element.preventDefault();
		fairy_dust = jQuery("#filter_search_shortcode_form").serialize();
		shortcode_vals = bl_ajax_get_shortcode_vals(true);
		bl_ajax_init();
		bl_ajax_fetch_content(fairy_dust, shortcode_vals);
	});
});


function bl_ajax_init(){
	jQuery('body').css('cursor', 'wait'); 
	jQuery(".bl_frontend_search_section").css("opacity", ".3");
}

function bl_ajax_fetch_content(fairy_dust, shortcode_vals){
	jQuery.ajax({
		type : "POST",
		url : ajaxurl, 
		data: fairy_dust + "&action=bl_ajax_frontend_update" + shortcode_vals, 
		success : function(r_c){
		options = jQuery.parseJSON(r_c);
		bl_ajax_complete();
		bl_ajax_end(options);
	}});
}

function bl_ajax_get_page(post_id){	
	shortcode_vals = bl_ajax_get_shortcode_vals();
	bl_ajax_init();
	fairy_dust = "";
	if(jQuery("#filter_search_form")){
		fairy_dust = jQuery("#filter_search_form").serialize();
	}else if(jQuery("#listingsearchform")){
		if(jQuery("input[name=filter_search]").val(l_type))
			fairy_dust = jQuery("#listingsearchform").serialize();
	}

	fairy_dust = fairy_dust + "&bl_post_id=" + post_id;		
		
	jQuery.ajax({
		type : "POST",
		url : ajaxurl, 
		data: fairy_dust + "&action=bl_ajax_result_page" + shortcode_vals, 
		success : function(r_c){
			options = jQuery.parseJSON(r_c);
			bl_ajax_complete();
			bl_ajax_end(options);
				
			launch_bepro_listing_tabs();
			try{
				bl_launch_gallery();
			}catch(err) {}
		}
	});	
}
