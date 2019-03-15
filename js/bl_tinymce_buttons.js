// shortcode buttons :)

(function() {
    tinymce.create('tinymce.plugins.bepro_listings', {
        init : function(ed, url) {
            ed.addButton('beprolistings', {
                title : 'BePro Listings Shortcodes',
                cmd : 'bepro_listings_button_click',
				icon: true,
				text: 'Listings',
                image : url + '/../images/bepro_shortcode_icon.png'
            });
			ed.addCommand('bepro_listings_button_click', function() {
				ed.windowManager.open(
				{
				  file   : ajaxurl + '?action=bepro_listings_shortcode_dialog',
				  width  : 420,
				  height : 370,
				  inline : 1,
				  title  : 'BePro Listings Shortcodes'
				}, 
				{
				  plugin_url : url
				}
			  );
            });
        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                    longname : 'BePro Software Buttons',
                    author : 'BePro Software Team',
                    authorurl : 'http://beprosoftware.com/',
                    infourl : 'http://beprosoftware.com/shop/bepro-listings',
                    version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('bepro_listings', tinymce.plugins.bepro_listings);
})();