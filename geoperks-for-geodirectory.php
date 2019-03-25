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

add_action('init', array('GeoperksForGeodirectoryFeatures', 'init'),0);


class GeoperksForGeodirectoryFeatures {
	
	private static $cnt=1;
	public static function init() {
		
		self::register_post_type();
		 
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_thickbox'));
		add_action('admin_print_footer_scripts', array(__CLASS__, 'gd_shortcodes_interface'), 1000);
		add_filter('mce_external_plugins', array(__CLASS__, 'add_gd_plugin_js'), 100 );
		add_filter('mce_buttons_2', array(__CLASS__, 'gd_mce_shortcode_btn'));
		add_filter('geodir_posts_order_by_sort', array(__CLASS__, 'cust_post_order_by_featured_first') ,10,4);
		
		
		add_filter( 'manage_edit-'.GEOPERKSFORGD_POST_TYPE.'_columns', array(__CLASS__, 'edit_columns'));
		add_action( 'manage_'.GEOPERKSFORGD_POST_TYPE.'_posts_custom_column', array(__CLASS__,'manage_columns'), 10, 2 );
		add_filter( 'views_edit-'.GEOPERKSFORGD_POST_TYPE, array(__CLASS__, 'add_button_to_views'));
		add_action('admin_enqueue_scripts', array( __CLASS__, 'load_admin_scripts'));
		
		
		add_action("wp_ajax_sync_ajax_call_gd", array(__CLASS__, 'ajax_insert_post_content'));
		
		
		
		if(isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == GEOPERKSFORGD_POST_TYPE)  {
			$first_time_sync = get_option('perk_first_time_sync', true);
			if($first_time_sync !=  'yes') {
				self::insert_post_content();
			}
		}
		
		
	}
	
	
	
	//////////////////////////////////////////////////////////////////////////////////////////
	 public static function register_post_type() {
		 
		register_post_type(GEOPERKSFORGD_POST_TYPE,
                           array(
                               'labels' => array(
                                   'name' => __( 'Geoperks For GeoDirectory',  GEOPERKSFORGD_TEXT_DOMAIN),
                                   'all_items' => __( 'Geoperks For GeoDirectory',  GEOPERKSFORGD_TEXT_DOMAIN),
                                   'singular_name' => __( 'Geoperk For GeoDirectory',  GEOPERKSFORGD_TEXT_DOMAIN)
                               ),
							   'capabilities' => array(
								'create_posts' => false,
								),
                               'public' => true,
                               'has_archive' => true,
                               'menu_position' => 56,
                               'rewrite' => array('slug' => GEOPERKSFORGD_POST_TYPE),
                           )
                          );

	}
	
	public static function edit_columns() {

		$columns = array(

			//'cb' => '&lt;input type="checkbox" />',
			'srno' => __( 'Sr. No.' ),
			'perk_title' => __( 'Perk' ),
			
			'description' => __( 'Description' ),
			'docs' => __( 'Docs' )
		);

		return $columns;
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
	
	
	
	
	public static function manage_columns( $column, $post_id ) {
		global $post;
		
		$default_version = '1.0';
		$perk_id =  get_post_meta($post_id,'meta_perk_id',true);
		
		$perk_title = get_the_title($post_id); 

		$perk_version =  get_post_meta($post_id,'meta_perk_version',true);
		
		if(empty($perk_version)){
			$perk_version ="1.0";
		}
		
		$perk_url =  get_post_meta($post_id,'meta_perk_url',true);
		$perk_is_setting_required =  get_post_meta($post_id,'meta_perk_is_setting_required',true);

		

		switch( $column ) {

			case 'srno' :

				echo self::$cnt;
				self::$cnt++;
				break;
				
			case 'perk_title' :

				echo '<span class="title-text">'. get_post_field('post_title', $post_id).'</span>';
				echo  '<br />Folder name: perk-'.$perk_id;
				break;

			
			case 'description' :

				echo get_post_field('post_content', $post_id);

				break;

			case 'docs' :

				echo 'Version: '.$perk_version.'<br><a href="'. $perk_url.'" target="_blank">'. __('View More', GEOPERKSFORGD_TEXT_DOMAIN).'</a>';

				break;


			default :
				break;
		}
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
		
		$url = 'https://staging.wpapps.club/wp-json/geoperks/v1/user-perk-list';

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
				WHERE meta_key LIKE 'meta_perk_id' 
				ORDER BY meta_value ASC";

		$meta_perk_id1 = $wpdb->get_results( $querystr, ARRAY_A );

		$newArr= array();
		foreach($meta_perk_id1 as $valArr)
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
						'meta_perk_id' => $perk->ID,
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