(function() {
        tinymce.PluginManager.add('gd_mce_shortcode_plugin_btn', function( editor, url ) {
           editor.addButton( 'gd_mce_shortcode_plugin_btn', {
				tooltip: 'Add Goedirectory Shortcode',
				icon: 'dashicon dashicons-screenoptions',
				cmd: 'WP_GD_Shortcode_For_Classic_Editor'
       
       
              });
        
			editor.addCommand( 'WP_GD_Shortcode_For_Classic_Editor', function() {
				tb_show("Add Shortcode", "#TB_inline?width=100%&height=550&inlineId=super-duper-content");
			});
	});
})();