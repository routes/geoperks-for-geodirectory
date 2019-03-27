<?php 
add_action('init', array('GdShortcodesOnGutenbergClassicEditor', 'run'));
class GdShortcodesOnGutenbergClassicEditor { 
		public static function run() {
			
			add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_thickbox'));
			add_action('admin_print_footer_scripts', array(__CLASS__, 'gd_shortcodes_interface'), 1000);
			add_filter('mce_external_plugins', array(__CLASS__, 'add_gd_plugin_js'), 100 );
			add_filter('mce_buttons_2', array(__CLASS__, 'gd_mce_shortcode_btn'));
		}
	
		public static function enqueue_thickbox() {
			add_thickbox();
		}
		
		public static function gd_shortcodes_interface() {
			ob_start(); 	
			include_once (dirname(__FILE__).'/includes/insert-shortcode-js.php');
			$insert_function = ob_get_clean();   
			ob_start();			
			WP_Super_Duper::shortcode_insert_button( $editor_id = '', $insert_function);
			$strcontent = ob_get_clean();
			echo '<style>.custom-gutenberg-gd-shortcodes-container .super-duper-content-open{ display: none !important;}</style><div class="custom-gutenberg-gd-shortcodes-container">'.$strcontent.'</div>';     
		}
		 
		public static function add_gd_plugin_js( $plugin_array ) {
			
				  $plugin_array['gd_mce_shortcode_plugin_btn'] = plugin_dir_url(__FILE__).'/js/gd-mce-shortcode-plugin.js';
				  return $plugin_array;
		}
		
		public static function gd_mce_shortcode_btn( $buttons ) {	
			$buttons[] = 'gd_mce_shortcode_plugin_btn';
			return $buttons;
		}
}
?>