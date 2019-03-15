<?php 
/*
Plugin Name: GeoPerks for GeoDirectory
Plugin URI: https://geoperks.club
Description: GeoPerks is special sauce for GeoDirectory
Version: 1.0
Author: @alexrollin, @rightmentor, @ismiaini, @wpappsclub
Author URI: http://wpapps.club
Text Domain: geoperks-for-geodirectory
Domain Path: /languages
License: GPL3
*/
add_action('init', array('GeoperksForGeodirectoryFeatures', 'init'));
class GeoperksForGeodirectoryFeatures {
	
	public static function init() {
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_thickbox'));
		add_action('admin_print_footer_scripts', array(__CLASS__, 'gd_shortcodes_interface'), 1000);
		add_filter('mce_external_plugins', array(__CLASS__, 'add_gd_plugin_js'), 100 );
		add_filter('mce_buttons_2', array(__CLASS__, 'gd_mce_shortcode_btn'));
		add_filter('geodir_posts_order_by_sort', array(__CLASS__, 'cust_post_order_by_featured_first') ,10,4);
	}
	
	public static function cust_post_order_by_featured_first($orderby, $sort_by, $table, $query)
	{
		if(geodir_is_page('search'))
		{
			if(!empty($orderby)) {
				$orderby = $table. '.featured DESC , ' . $orderby ;
			}
			else {
				$orderby = $table. '.featured DESC ' ;    
			}
		}		
		return $orderby;
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