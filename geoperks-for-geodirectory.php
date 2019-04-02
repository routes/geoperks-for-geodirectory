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

define('GEOPERKSFORGD_POST_TYPE', 'geoperksforgd');
define('GEOPERKSFORGD_TEXT_DOMAIN', 'geoperksforgd_text_domain');
define( 'GEO_PERKS_FOR_GD_FILE_PATH', __FILE__);
define( 'GEO_PERKS_FORGD_PATH', dirname(__FILE__));


add_action('init', array('GeoperksForGeodirectoryFeatures', 'init'),100);
add_action('admin_notices', array('GeoperksForGeodirectoryFeatures', 'gd_admin_notice_activation_notice'));
register_activation_hook(__FILE__,array( 'GeoperksForGeodirectoryFeatures','admin_notice_activation_hook') );
add_action('admin_init', array( 'GeoperksForGeodirectoryFeatures','gdfor_plugin_redirect'));
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array('GeoperksForGeodirectoryFeatures', 'plugin_settings_link' ) );

class GeoperksForGeodirectoryFeatures {
	
	private static $cnt=1;
	public static function init() {
		
		
		$geoperkFeature2 = get_option('geoperksforgd_list_feature_2',true);
		
		if($geoperkFeature2!=0) {
			if(!class_exists('GdShortcodesOnGutenbergClassicEditor')) {
				include_once GEO_PERKS_FORGD_PATH.'/perks/feature-2/perk-run.php';        
			}
		}
		 
		$geoperkFeature1 = get_option('geoperksforgd_list_feature_1',true);
		
		if($geoperkFeature1!=0) {
			if(!class_exists('GeopFeaturedFirstInSearch')) {
				include_once GEO_PERKS_FORGD_PATH.'/perks/feature-1/perk-init.php';        
			}
		}
		
		add_action('admin_enqueue_scripts', array( __CLASS__, 'load_admin_scripts'));
		
		
		add_action("wp_ajax_sync_ajax_call_gd", array(__CLASS__, 'ajax_insert_post_content'));
		
		add_filter('geodir_settings_tabs_array',array(__CLASS__,'geoperk_setting_tab'),1000);
		add_action('geodir_settings_geoperksforgd_settings', array(__CLASS__,'geoperk_setting_form'), 30);
		
		add_action('wp_ajax_geoperksforgeodirectory-enable_disable_action', array(__CLASS__, 'enable_disable_perk_forgd'));
		
		
		if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'geoperksforgd_settings')  {
			$first_time_sync = get_option('perk_first_time_sync', true);
			if($first_time_sync !=  'yes') {
				self::insert_post_content();
			}
		}
		
		
		
		
	}
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	
	public static function plugin_settings_link($links) {
		$url = get_admin_url() . 'admin.php?page=gd-settings&tab=geoperksforgd_settings';
		$settings_link = '<a href="'.$url.'">' . __( 'Settings', 'textdomain' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
	
	public static function gdfor_plugin_redirect() {
		if (get_option('gdfor_plugin_do_activation_redirect', false)) {
			delete_option('gdfor_plugin_do_activation_redirect');
			wp_redirect( admin_url( 'admin.php?page=gd-settings&tab=geoperksforgd_settings' ) );
			exit;
		}
	}
	
	public static function admin_notice_activation_hook() {
		$userplugin_path = plugin_dir_path( __DIR__ );
		$plugin_data = get_plugin_data($userplugin_path . 'geodirectory/geodirectory.php');
		$plugin_version =explode(".",$plugin_data['Version']);
	 	$plugin_version =(int)$plugin_version[0];
		if (!is_plugin_active('geodirectory/geodirectory.php' ) || $plugin_version < 2) {
			set_transient('gd-admin-notice-activation', true, 5);
		}
		else
		{
			add_option('gdfor_plugin_do_activation_redirect', true);
		}
	}
	
	public static function gd_admin_notice_activation_notice() {
		if( get_transient( 'gd-admin-notice-activation' ) ){	
			echo '<div class="error"><p>Geodirectory need to be activated and at least version >=2.0 to activate GeoPerks for GeoDirectory plugin</p></div>';
			$userplugin_path = plugin_dir_path( __DIR__ );	
			deactivate_plugins($userplugin_path . 'geoperks-for-geodirectory/geoperks-for-geodirectory.php');
			unset($_GET['activate']);
			delete_transient( 'gd-admin-notice-activation' );
		}
		
	}
	
	public static function perkforgd_default() {         
		$geoperk_default = array();
		$geoperk_default[] = array(
		 'ID' => 'feature_1',
		 'post_title' => 'Featured first in search',
		 'post_content' => '',
		 'perk_url' => 'https://geoperks.club/',
		 'perk_version' => '1.0');
		 
		 $geoperk_default[] = array(
		 'ID' => 'feature_2',
		 'post_title' => 'Gd Shortcodes on Gutenberg Classic Editor',
		 'post_content' => '',
		 'perk_url' => 'https://geoperks.club/',
		 'perk_version' => '1.0');
		 
		return $geoperk_default;
    }
	
	public static function enable_disable_perk_forgd() {         
        if(isset($_REQUEST['perk_id'])) {
            $perk_id = $_REQUEST['perk_id'];
            $perk_status = (isset($_REQUEST['perk_status'])?$_REQUEST['perk_status']:0);
            update_option('geoperksforgd_list_'.$perk_id, $perk_status);
        }
    }
	
	
	public static function geoperk_setting_tab($tabs) {	
        $tabs['geoperksforgd_settings'] = __( 'Geoperks for GeoDirectory', GEOPERKSFORGD_TEXT_DOMAIN);
        return $tabs; 
	}
	
	public static function geoperk_setting_form() {        
       include_once GEO_PERKS_FORGD_PATH.'/admin/manage_perks.php';        
    }
	
	public static function get_geoperksonclient_list() {
		$perks = get_option('geoperksonclient_list', true);
		if(!is_array($perks)){
			$perks  = array();
		} 
		return  $perks;
	}
	
	public static function add_button_to_views( $views ) {
		$views['my-button'] = '<a id="a-update-from-provider-gd" href="javascript:void(0);"  ><button id="update-from-provider-gd" type="button"  title="Sync" class="sync-button" >'.__( "Sync",  GEOPERKSFORGD_TEXT_DOMAIN).'</button><label class="loadspinner" style="display:none;" ><img src="'.admin_url('images/loading.gif').'"  ></label></a>';
		
		return $views;
	}
	
	public static function load_admin_scripts() {
		wp_enqueue_style('geoperksforgd-admin-settings-css', plugins_url('assets/css/admin-settings.css',GEO_PERKS_FOR_GD_FILE_PATH ));
		
		wp_register_script( 'geoperksforgd-admin-settings-js', plugins_url('assets/js/admin-settings-gd.js',GEO_PERKS_FOR_GD_FILE_PATH), '' , '', true);

		$params_array = array(
			'ajax_url' => admin_url('admin-ajax.php')
		);
		wp_localize_script( 'geoperksforgd-admin-settings-js', 'geoperksforgd_js_params', $params_array );
		wp_enqueue_script( 'geoperksforgd-admin-settings-js' );

	}
	
	
	
	public static function ajax_insert_post_content() {
		$response_data = array('status'=> 1, 'message' => '');
		self::insert_post_content();
		echo json_encode($response_data); exit;
	}
	
	
	
	public static function insert_post_content() {
		global $wpdb;
		$params = array('for_free_plugin' => '1','site_url'=>get_site_url());
		$pos = strpos($_SERVER['HTTP_HOST'], 'test.geoperks.club');
		if($pos===false){
			
			$url = 'https://geoperks.club/wp-json/geoperks/v1/user-perk-list';
			
		}else {
			
			$url = 'https://test.geoperks.club/wp-json/geoperks/v1/user-perk-list';
		}
		
		$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(
				'Content-Type: application/x-www-form-urlencoded'
			),
			'body' => $params,
			'cookies' => array()
			)
		);
		if(is_array($response)) {
			if(isset($response['body'])) {
				$perk_response   =  json_decode($response['body']);    
				if(isset($perk_response->user_perks)) {
					$user_perks = $perk_response->user_perks;
				}
			} 
		}
		
		$querystr = "SELECT * 
				FROM $wpdb->postmeta 
				WHERE meta_key LIKE 'meta_free_perk_id' 
				ORDER BY meta_value ASC";

		$meta_free_perk_id1 = $wpdb->get_results( $querystr, ARRAY_A );

		$newArr= array();
		foreach($meta_free_perk_id1 as $valArr)
		{
			$meatValuenew='';
			$postIdnew='';
			foreach($valArr as $k => $v)
			{
				if($k=='meta_value')
					$meatValuenew=$v;
				if($k=='post_id')
					$postIdnew=$v;
			}

			$newArr[$meatValuenew]=$postIdnew;

		}

		if(!empty($user_perks)) {

			foreach($user_perks as $perk) {	
				$my_post = array(
					'post_type'     => GEOPERKSFORGD_POST_TYPE,
					'post_title'    => wp_strip_all_tags( $perk->post_title ),
					'post_content'  => $perk->post_content,
					'post_status'   => $perk->post_status,
					'post_author'   => 1,
					'post_date' 	=> $perk->post_date,
					'post_modified' => $perk->post_modified,
					'guid'			=> $perk->guid,
					'meta_input'   => array(
						'meta_free_perk_id' => $perk->ID,
						'meta_perk_version' => $perk->version,
						'meta_perk_url' => $perk->url,
						'meta_perk_is_setting_required' => $perk->is_setting_required,
						'meta_is_free' => $perk->is_free
					)						
				);

				if (isset($newArr[$perk->ID]) && $newArr[$perk->ID] > 0 ) {
					$my_post['ID'] = $newArr[$perk->ID];
					wp_update_post($my_post);
				}else {
					wp_insert_post( $my_post );
				}
			}
			
			update_option('perk_first_time_sync', 'yes');

		}else{return;}

	}
}
?>