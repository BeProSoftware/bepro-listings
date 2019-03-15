jQuery(document).ready(function(){
	jQuery(".clear_search button").click(function(element){
		element.preventDefault();
	});
	
	jQuery("body").on("click",".clear_search",function(element){
		element.preventDefault();
		location.reload(true);
	});	
});