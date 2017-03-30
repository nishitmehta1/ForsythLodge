<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Plethora Demo Importer Module Class

*/
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( ! class_exists('Plethora_Module_Demoimporter') ) {

/**
 * Main Plethora Importer class
 * 
 * @package Plethora Framework
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c)
 *
 */

 class Plethora_Module_Demoimporter {

	public static $feature_title         = "Demo Importer";
	public static $feature_description   = "Import any Plethora theme demo";
	public static $theme_option_control  = true;                      // Will this feature be controlled in theme options panel ( boolean )
	public static $theme_option_default  = true;                      // Default activation option status ( boolean )
	public static $theme_option_requires = array();                   // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
	public static $dynamic_construct     = true;                      // Dynamic class construction ? ( boolean )
	public static $dynamic_method        = false;                     // Additional method invocation ( string/boolean | method name or false )

	// USED FOR IMPORT SELECTION PROCESS
	public $admin_page_slug;
	public $prefix = 'pdi';
	public $demos = array();

	// RAW DEMO FILE DATA ATTRs
	public $demo;
	public $demo_slug;
	public $demo_dir;
	public $demo_url;

	// IMPORT CONFIG
	public $allow_create_users      = true;
	public $allow_fetch_attachments = true;
	public $max_attachment_size     = 0;
	public $request_timeout         = 30;

	// PROCESSED DEMO DATA ATTRs
	public $base_url;
	public $authors;
	public $categories;
	public $tags;
	public $terms;
	public $posts;
	public $widgets;
	public $menu_locations;
	public $static_pages;
	public $theme_options;

	// IMPORT PROCESS HELPER ATTRs ( using transients for updating/recovering values between ajax calls )
	public $processed_authors    = array();
	public $author_mapping       = array();
	public $processed_terms      = array();
	public $processed_posts      = array();
	public $post_orphans         = array();
	public $processed_menu_items = array();
	public $menu_item_orphans    = array();
	public $missing_menu_items   = array();
	public $url_remap            = array();
	public $featured_images      = array();

	// ERROR LOGGING FOR USERS / DEVs
	public $errors;

	/**
	 * General config and hooking up the admin page
	 */
	public function __construct() {

	  // GENERAL CONFIGURATION
	  $this->demo_url = PLE_THEME_ASSETS_URI . '/demos';
	  $this->demo_dir = PLE_THEME_ASSETS_DIR . '/demos';

	  add_filter( 'mime_types', array( $this, 'add_json_mime' ) );

	  if (  is_admin() ) {

		add_action( 'admin_menu', array( $this, 'register_importer_menu_page' ) );
		add_action( 'wp_ajax_pdi_demo_panel', array( $this, 'ui_demo_panel' ) );       // Ajax for grid view
		add_action( 'wp_ajax_pdi_import', array( $this, 'import_route' ) );
	  }
	}

	/**
	 * Initialize all panel related action. Hook on 'init' with late priority
	 */
	public function register_importer_menu_page() {

		$this->admin_page_slug = add_submenu_page( 'tools.php', 
					   THEME_DISPLAYNAME . ' '. esc_html__('Demo Importer', 'plethora-framework') , 
					   THEME_DISPLAYNAME . ' '. esc_html__('Demo Importer', 'plethora-framework') , 
					   'manage_options', 
					   'plethora-demo-importer',
					   array( $this, 'ui' ) 
					  );
		
 
		if ( !empty( $this->admin_page_slug ) ) {  // continue if we get a valid admin page slug

		  add_action( 'current_screen', array( $this, 'set_demos'), 99 );
		  add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_importer_scripts' ) );
		  add_action( 'admin_print_footer_scripts', array( $this, 'enqueue_importer_grid_init' ) );
		}
	}

	/**
	 * Locates, retrieves and saves all demo files
	 */
	public function set_demos() {

	  $screen = get_current_screen();
	  if ( $screen->id === $this->admin_page_slug  ) { // this makes sure that demos are not loaded on every page load

		$demos = array();

		if ( is_dir( $this->demo_dir ) ) {

		  $demo_assets = scandir( $this->demo_dir );

		  // get jsons first
		  foreach ( $demo_assets as $demo_asset ) {

			if ( ! is_dir( $demo_asset ) ) {

			  $asset     = wp_check_filetype( $demo_asset, array( 'json' => 'application/json', 'png' => 'image/png' ) );
			  $file_ext  = $asset['ext'];
			  $file_name = basename($demo_asset, '.'.$asset['ext'] );
			  $file      = $this->demo_dir .'/'. $file_name .'.'. $file_ext;
			  $thumb_dir = $this->demo_dir .'/'. $file_name .'.png';
			  $thumb_uri = $this->demo_url .'/'. $file_name .'.png';
			  $thumb_default_uri = PLE_THEME_URI .'/screenshot.png';

			  if ( THEME_SLUG === substr($file_name, 0, strlen( THEME_SLUG ) ) && $asset['ext'] === 'json' ) {

				$demo_contents = wp_unslash( json_decode( Plethora_WP::get_file_contents( $file ) ) );
				$demos[$file_name]['slug']             = $file_name;
				$demos[$file_name]['file']             = $file;
				$demos[$file_name]['thumbnail']        = file_exists( $thumb_dir ) ? $thumb_uri : $thumb_default_uri ;
				$demos[$file_name]['title']            = $demo_contents->title;
				$demos[$file_name]['desc']             = $demo_contents->desc;
				$demos[$file_name]['online']           = $demo_contents->online;
				$demos[$file_name]['plugins_required'] = $demo_contents->plugins_required;
				$demos[$file_name]['attachments_count']= isset( $demo_contents->posts->attachment ) ? count( $demo_contents->posts->attachment ) : 0;
			  }
			}
		  }
		}
		$this->demos = $demos;
	  }
	}

	/**
	 * Enqueue all scripts used for importer
	 * Also, dispatching import process data to ajax handling script
	 */
	public function enqueue_importer_scripts( $hook ) {

	  if ( $this->admin_page_slug === $hook ) { // no need to load it on every admin page!

		$this->load_init = true;
		wp_enqueue_script( ASSETS_PREFIX . '-importer-modernizr', plugin_dir_url( __FILE__ ).'assets/modernizr-importer.js', array('jquery'), false, false ); 
		wp_enqueue_style( ASSETS_PREFIX . '-importer',  plugin_dir_url( __FILE__ ).'assets/importer.css', false );
		wp_enqueue_script( ASSETS_PREFIX . '-importer',  plugin_dir_url( __FILE__ ).'assets/importer.js', array( 'jquery' ), false, true );
		wp_enqueue_script( ASSETS_PREFIX . '-importer-ajax', plugin_dir_url( __FILE__ ) . 'assets/importer-ajax.js', array( 'jquery' ) );
		// Pass information to ajax script. Will be available to ajax script as 'pdi' object
		$pdi['ajaxurl']           = admin_url( 'admin-ajax.php' );  // url for ALL ajax calls
		$pdi['pdi_nonce']         = wp_create_nonce( 'pdi-nonce' ); // security nonce
		$pdi['actions']           = $this->get_import_mask();      // actions index
		$pdi['attachments_count'] = $this->get_attachments_counts();       // attachment counts ( just for the attachment import process )      
		wp_localize_script( ASSETS_PREFIX . '-importer-ajax', $this->prefix, $pdi );     
	  }        
	}

	/**
	 * Enqueue grid init script on footer
	 */
	public function enqueue_importer_grid_init( $hook ) {

	  if ( !empty( $this->load_init ) && $this->load_init === true ) {
		echo "<script>\n";
		echo "jQuery(document).ready(function($) { \n";
		echo "  $(function() { Grid.init(); });\n";
		echo "}); \n";
		echo "\n</script>";
	  }
	}

//// AJAX ONLY METHODS START

	/**
	 * Returns all import process handling information according to selected demo contents
	 * Used for import table creation AND ajax import calls
	 */
	private function get_import_mask( $for_import_table = false ) {

	  $error_notice    = esc_html__( 'Import failed due to server unavailabilty...process stopped, you should try again after a while', 'plethora-framework' );

	  $counter = $for_import_table ? count( $this->authors ) : 0;
	  $import_mask['users'] =     array( 
									'title'             => esc_html__( 'Prepare Users', 'plethora-framework' ), 
									'counter'           => $counter,
									'in_queue_notice'   => $counter . esc_html__(' users in queue', 'plethora-framework' ),  
									'beforeSend_notice' => esc_html__( 'Preparing users...', 'plethora-framework' ), 
									'success_notice'    => esc_html__( 'Users are ready...let\'s start!', 'plethora-framework' ), 
									'error_notice'      => $error_notice, 
									'response_method'   => 'users',
									''
								  );
	  $counter = $for_import_table ? count( $this->categories ) : 0;
	  $import_mask['categories'] = array( 
									'title'             => esc_html__( 'Categories', 'plethora-framework' ), 
									'counter'           => $counter,
									'in_queue_notice'   => $counter . esc_html__(' categories in queue', 'plethora-framework' ), 
									'beforeSend_notice' => esc_html__( 'Importing taxonomy terms...', 'plethora-framework' ), 
									'success_notice'    => esc_html__( 'All categories imported!', 'plethora-framework' ), 
									'error_notice'      => $error_notice, 
									'response_method'   => 'categories'
								  );
	  $counter = $for_import_table ? count( $this->tags ) : 0;
	  $import_mask['tags'] = array( 
									'title'             => esc_html__( 'Tags', 'plethora-framework' ), 
									'counter'           => $counter,
									'in_queue_notice'   => $counter . esc_html__(' tags in queue', 'plethora-framework' ), 
									'beforeSend_notice' => esc_html__( 'Importing tags...', 'plethora-framework' ), 
									'success_notice'    => esc_html__( 'All tags imported!', 'plethora-framework' ), 
									'error_notice'      => $error_notice, 
									'response_method'   => 'tags'
								  );
	  $counter = $for_import_table ? count( $this->terms ) : 0;
	  $import_mask['terms'] = array( 
									'title'             => esc_html__( 'Custom taxonomy terms', 'plethora-framework' ), 
									'counter'           => $counter,
									'in_queue_notice'   => $counter . esc_html__(' terms in queue', 'plethora-framework' ),  
									'beforeSend_notice' => esc_html__( 'Importing taxonomy terms...', 'plethora-framework' ), 
									'success_notice'    => esc_html__( 'All taxonomy terms imported!', 'plethora-framework' ), 
									'error_notice'      => $error_notice, 
									'response_method'   => 'terms'
								  );
	  // Add all existing post types...empty ones will be filtered if needed on the end of this method
	  $post_types = get_post_types();
	  unset( $post_types['nav_menu_item'] ); // re-set nav_menu_item order at the end of the array
	  unset( $post_types['attachment'] );    // re-set attachments order at the end of the array
	  $post_types['nav_menu_item'] = 'nav_menu_item';
	  $post_types['attachment'] = 'attachment';
	  foreach ( $post_types as $key => $post_type ) {

		$post_type_obj = get_post_type_object( $post_type );
		$counter = $for_import_table && isset( $this->posts->$post_type ) ? count( $this->posts->$post_type ) : 0;
		$import_mask[$post_type] =     array( 
									  'title'             => $post_type_obj->label, 
									  'counter'           => $counter,
									  'in_queue_notice'   => $counter .' '. strtolower( $post_type_obj->label ) .' '. esc_html__(' items in queue', 'plethora-framework' ),  
									  'beforeSend_notice' => esc_html__( 'Importing attachment', 'plethora-framework' ), 
									  'success_notice'    => $post_type_obj->label . esc_html__( ' and related data imported!', 'plethora-framework' ), 
									  'error_notice'      => $error_notice, 
									  'response_method'   => $post_type
									);
	  }

	  $counter = $for_import_table ? count( $this->widgets ) : 0;
	  $import_mask['widgets'] =  array( 
									'title'             => esc_html__( 'Widgets', 'plethora-framework' ), 
									'counter'           => $counter,
									'in_queue_notice'   => $counter . esc_html__(' widgets in queue', 'plethora-framework' ), 
									'beforeSend_notice' => esc_html__( 'Importing widgets...', 'plethora-framework' ), 
									'success_notice'    => esc_html__( 'All widgets imported!', 'plethora-framework' ), 
									'error_notice'      => $error_notice, 
									'response_method'   => 'widgets'
								  );
	  $import_mask['theme_options'] = array( 
									'title'             => esc_html__( 'Theme Options', 'plethora-framework' ), 
									'in_queue_notice'   => esc_html__('In queue', 'plethora-framework' ),  
									'beforeSend_notice' => esc_html__( 'Updating theme options...', 'plethora-framework' ), 
									'success_notice'    => esc_html__( 'Theme options updated!', 'plethora-framework' ), 
									'error_notice'      => $error_notice, 
									'response_method'   => 'theme_options'
								  );
	  $import_mask['finish'] = array( 
									'title'             => esc_html__( 'Finish Import', 'plethora-framework' ), 
									'in_queue_notice'   => esc_html__('In queue', 'plethora-framework' ), 
									'beforeSend_notice' => esc_html__( 'Wrapping up...', 'plethora-framework' ), 
									'success_notice'    => esc_html__( 'Everything is good...enjoy your demo!', 'plethora-framework' ), 
									'error_notice'      => $error_notice, 
									'response_method'   => 'finish'
								  );
 
	  if ( $for_import_table ) { 

		if ( empty( $this->demo->categories ) ) { unset( $import_mask['categories'] ); }
		if ( empty( $this->demo->tags ) ) { unset( $import_mask['tags'] ); }
		if ( empty( $this->demo->terms ) ) { unset( $import_mask['terms'] ); }
		if ( empty( $this->demo->widgets ) ) { unset( $import_mask['widgets'] ); }
		if ( empty( $this->demo->theme_options ) ) { unset( $import_mask['theme_options'] ); }
		if ( empty( $this->demo->posts ) ) { unset( $import_mask['users'] ); } // no need for users if no posts!
		// special filtering for post types
		foreach ( $post_types as $key => $post_type ) {
		  
		  if ( empty( $this->demo->posts->$post_type ) ) {

			  unset( $import_mask[$post_type] );
		  }
		}
	  }
	  
	 return $import_mask;
	}

	/**
	 * Verifies ajax file call nonce 
	 */
	public function verify_nonce() {

	  $nonce = !empty( $_POST['pdi_nonce'] ) ? $_POST['pdi_nonce'] : '' ;
	  if ( wp_verify_nonce( $nonce, 'pdi-nonce' ) ) {

		$this->errors['ajax_nonce'] = esc_html__('No...you can\'t do this, please go play something else!', 'plethora-framework' );
		return true;
	  }

	  return false;
	}

	/**
	 * Locates and retrieves the selected demo file
	 */
	public function set_demo() {

	  $demo_slug = !empty( $_POST['demo_slug'] ) ? sanitize_title( $_POST['demo_slug'] ) : '' ;
	  $this->demo_slug = $demo_slug;
	  if ( empty( $this->demo_slug  ) ) { return $demo; }

	  if ( file_exists( $this->demo_dir .'/'. $this->demo_slug . '.json' ) ) {

		$file_contents = Plethora_WP::get_file_contents( $this->demo_dir .'/'. $this->demo_slug . '.json' ) ;
		if ( !empty( $file_contents ) ) {

		  $demo = wp_unslash( json_decode( $file_contents ) );
		}       
	  }

	  // Full demo file contents
	  $this->demo = !empty( $demo ) ? $demo : array();
	  if ( !empty( $this->demo ) ) { // Additional data setup
		
		$demo                 = $this->demo;
		$this->base_url       = $demo->online;
		$this->posts          = !empty( $demo->posts ) ? $demo->posts : array() ;
		$this->authors        = !empty( $demo->authors ) && !empty( $demo->posts )  ? $demo->authors : array() ;
		$this->terms          = !empty( $demo->terms ) ? $demo->terms : array() ;
		$this->categories     = !empty( $demo->categories ) ? $demo->categories : array() ;
		$this->tags           = !empty( $demo->tags ) ? $demo->tags : array() ;
		$this->widgets        = !empty( $demo->widgets ) ? $demo->widgets : array() ;
		$this->menu_locations = !empty( $demo->menu_locations ) ? $demo->menu_locations : array() ;
		$this->static_pages   = !empty( $demo->static_pages ) ? $demo->static_pages : array() ;
		$this->theme_options  = !empty( $demo->theme_options ) ? $demo->theme_options : array() ;
		return true;

	  } else {

		$this->errors['demo_empty'] = esc_html__('Demo data could not be retrieved. Import not possible', 'plethora-framework' );
		return false;
	  }
	}

	/**
	 * Main import controller...all ajax import should address this!
	 */
	public function import_route() {
		
	  if ( $this->verify_nonce() && $this->set_demo() ) { // verify nonce and selected demo data first!
		
		add_filter( 'http_request_timeout', array( &$this, 'set_request_timeout' ) );

		$this->set_helper_transients(); // Retrieves and reset helper values from db

		$response = !empty( $_POST['response_method'] ) ? sanitize_title( $_POST['response_method'] ) : '' ;
		if ( !empty( $response ) ) {

		  switch ( $response ) {

			case 'users':
			  $this->get_author_mapping();      // ajax response, no late escape applied here!
			  echo true;
			  break;

			case 'categories':
			  echo $this->import_categories();  // ajax response, no late escape applied here!
			  break;
			
			case 'tags':
			  echo $this->import_tags();        // ajax response, no late escape applied here!
			  break;
			
			case 'terms':
			  echo $this->import_terms();       // ajax response, no late escape applied here!
			  break;
			
			case 'widgets':
			  echo $this->import_widgets();     // ajax response, no late escape applied here!
			  break;

			case 'theme_options':
			  echo $this->import_theme_options();// ajax response, no late escape applied here!
			  break;

			case 'finish':

			  $this->backfill_parents();
			  $this->backfill_attachment_urls();
			  $this->remap_featured_images();
			  $this->set_menu_locations();
			  $this->set_static_pages();
			  foreach ( get_taxonomies() as $tax ) {

				delete_option( "{$tax}_children" );
				_get_term_hierarchy( $tax );
			  }
			  echo true;
			  break;

			default:  // handle all post imports

			  if ( in_array( $response, get_post_types() ) ) {

				$post_import_key = isset( $_POST['post_import_key'] ) ? $_POST['post_import_key'] : NULL;
				echo $this->import_post( $response, $post_import_key ); // ajax response, no late escape applied here!
			  }
			  break;
		  }
		}
		$this->update_helper_transients(); // Saves helper values in db
	  }
	  exit;
	}

	public function remove_helper_transients() {

	  delete_transient( $this->prefix .'_processed_authors' );
	  delete_transient( $this->prefix .'_author_mapping' );
	  delete_transient( $this->prefix .'_processed_terms' );
	  delete_transient( $this->prefix .'_processed_posts' );
	  delete_transient( $this->prefix .'_post_orphans' );
	  delete_transient( $this->prefix .'_processed_menu_items' );
	  delete_transient( $this->prefix .'_menu_item_orphans' );
	  delete_transient( $this->prefix .'_missing_menu_items' );
	  delete_transient( $this->prefix .'_url_remap' );
	  delete_transient( $this->prefix .'_featured_images' );
	}

	public function set_helper_transients() {

	  $this->processed_authors    = get_transient( $this->prefix .'_processed_authors' ) !== false ? get_transient( $this->prefix .'_processed_authors' ) : array();
	  $this->author_mapping       = get_transient( $this->prefix .'_author_mapping' ) !== false ? get_transient( $this->prefix .'_author_mapping' ) : array();
	  $this->processed_terms      = get_transient( $this->prefix .'_processed_terms' ) !== false ? get_transient( $this->prefix .'_processed_terms' ) : array();
	  $this->processed_posts      = get_transient( $this->prefix .'_processed_posts' ) !== false ? get_transient( $this->prefix .'_processed_posts' ) : array();
	  $this->post_orphans         = get_transient( $this->prefix .'_post_orphans' ) !== false ? get_transient( $this->prefix .'_post_orphans' ) : array();
	  $this->processed_menu_items = get_transient( $this->prefix .'_processed_menu_items' ) !== false ? get_transient( $this->prefix .'_processed_menu_items' ) : array();
	  $this->menu_item_orphans    = get_transient( $this->prefix .'_menu_item_orphans' ) !== false ? get_transient( $this->prefix .'_menu_item_orphans' ) : array();
	  $this->missing_menu_items   = get_transient( $this->prefix .'_missing_menu_items' ) !== false ? get_transient( $this->prefix .'_missing_menu_items' ) : array();
	  $this->url_remap            = get_transient( $this->prefix .'_url_remap' ) !== false ? get_transient( $this->prefix .'_url_remap' ) : array();
	  $this->featured_images      = get_transient( $this->prefix .'_featured_images' ) !== false ? get_transient( $this->prefix .'_featured_images' ) : array();
	}

	public function update_helper_transients() {

	  set_transient( $this->prefix .'_processed_authors', $this->processed_authors, 60*30 );
	  set_transient( $this->prefix .'_author_mapping', $this->author_mapping, 60*30 );
	  set_transient( $this->prefix .'_processed_terms', $this->processed_terms, 60*30 );
	  set_transient( $this->prefix .'_processed_posts', $this->processed_posts, 60*30 );
	  set_transient( $this->prefix .'_post_orphans', $this->post_orphans, 60*30 );
	  set_transient( $this->prefix .'_processed_menu_items', $this->processed_menu_items, 60*30 );
	  set_transient( $this->prefix .'_menu_item_orphans', $this->menu_item_orphans, 60*30 );
	  set_transient( $this->prefix .'_missing_menu_items', $this->missing_menu_items, 60*30 );
	  set_transient( $this->prefix .'_url_remap', $this->url_remap, 60*30 );
	  set_transient( $this->prefix .'_featured_images', $this->featured_images, 60*30 );
	}

	/**
	 * Map old author logins to local user IDs based on decisions made
	 * in import options form. Can map to an existing user, create a new user
	 * or falls back to the current user in case of error with either of the previous
	 */
	function get_author_mapping() {
	 
	  $imported_authors = $this->authors;
	  $create_users = $this->allow_create_users();
	  foreach ( $imported_authors as $i => $old_login ) {
		// Multisite adds strtolower to sanitize_user. Need to sanitize here to stop breakage in process_posts.
		$santized_old_login = sanitize_user( $old_login->data->user_login, true );
		$old_id = isset( $old_login->data->ID ) ? intval($old_login->data->ID) : false;

		if ( ! empty( $_POST['user_map'][$i] ) ) {

		  $user = get_userdata( intval($_POST['user_map'][$i]) );

		  if ( isset( $user->ID ) ) {

			if ( $old_id ) {

			  $this->processed_authors[$old_id] = $user->ID;
			}

			$this->author_mapping[$santized_old_login] = $user->ID;
		  }

		} else if ( $create_users ) {

			$user_data = array(
			  'user_login'   => $old_login->data->user_login,
			  'user_pass'    => wp_generate_password(),
			  'user_email'   => isset( $old_login->data->user_email ) ? $old_login->data->user_email : '',
			  'display_name' => isset( $old_login->data->display_name ) ? $old_login->data->display_name : '',
			);
			$user_id = wp_insert_user( $user_data );

		  if ( ! is_wp_error( $user_id ) ) {

			if ( $old_id ) {

			  $this->processed_authors[$old_id] = $user_id;
			}

			$this->author_mapping[$santized_old_login] = $user_id;

		  } else {

			printf( esc_html__( 'Failed to create new user for %s. Their posts will be attributed to the current user.', 'plethora-framework' ), esc_html( $old_login->data->display_name ) );
			if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
			  echo ' ' . $user_id->get_error_message();
			echo '<br />';

		  }
		}

		// failsafe: if the user_id was invalid, default to the current user
		if ( ! isset( $this->author_mapping[$santized_old_login] ) ) {
		  if ( $old_id )
			$this->processed_authors[$old_id] = (int) get_current_user_id();
		  $this->author_mapping[$santized_old_login] = (int) get_current_user_id();
		}
	  }
	}

	public function import_categories() {

	  if ( empty( $this->categories ) ) {
		return false;
	  }

	  foreach ( $this->categories as $cat ) {
		// if the category already exists leave it alone
		$term_id = term_exists( $cat->category_nicename, 'category' );
		if ( $term_id ) {

		  if ( is_array($term_id) ) { $term_id = $term_id['term_id']; }
		  if ( isset($cat->term_id) ) {

			$this->processed_terms[intval($cat->term_id)] = (int) $term_id;
		  }
		  continue;
		}

		$category_parent      = empty( $cat->category_parent ) ? 0 : category_exists( $cat->category_parent );
		$category_description = isset( $cat->category_description ) ? $cat->category_description : '';
		$catarr = array(
		  'category_nicename' => $cat->category_nicename,
		  'category_parent' => $category_parent,
		  'cat_name' => $cat->cat_name,
		  'category_description' => $category_description
		);

		$id = wp_insert_category( $catarr );
		if ( ! is_wp_error( $id ) ) {

		  if ( isset($cat->term_id) )
			$this->processed_terms[intval($cat->term_id)] = $id;

		} else {

		  $this->errors['categories']['user'][] = esc_html__( 'Failed to import category: ', 'plethora-framework' ) . esc_html( $cat->category_nicename );
		  $this->errors['categories']['dev'][] = $id->get_error_message();
		  continue;
		}
	  }

	  unset( $this->categories ); // PLEFIXME: Is this necessary?
	  return true;

	}

	public function import_tags() {

	  if ( empty( $this->tags ) ) {
		return false;
	  }
	  // print_r( $this->tags );
	  // return false;
	  foreach ( $this->tags as $tag ) {
		// if the tag already exists leave it alone
		$term_id = term_exists( $tag->slug, 'post_tag' );
		if ( $term_id ) {
		  if ( is_array($term_id) ) $term_id = $term_id['term_id'];
		  if ( isset($tag->term_id) )
			$this->processed_terms[intval($tag->term_id)] = (int) $term_id;
		  continue;
		}

		$tag_desc = isset( $tag->tag_description ) ? $tag->tag_description : '';
		$tagarr = array( 'slug' => $tag->slug, 'description' => $tag_desc );

		$id = wp_insert_term( $tag->name, 'post_tag', $tagarr );
		if ( ! is_wp_error( $id ) ) {
		  if ( isset( $tag->term_id ) ) {

			$this->processed_terms[intval( $tag->term_id )] = $id['term_id'];
		  }
		} else {

		  $this->errors['tags']['user'][] = esc_html__( 'Failed to import post tag: ', 'plethora-framework' ) . esc_html($tag->name);
		  $this->errors['tags']['dev'][] = $id->get_error_message();
		  continue;
		}
	  }

	  unset( $this->tags );
	  return true;
	}


	public function import_terms() { 

	  if ( empty( $this->terms ) ) {
		return false;
	  }

	  foreach ( $this->terms as $term ) {
		// if the term already exists in the correct taxonomy leave it alone
		$term_id = term_exists( $term->slug, $term->taxonomy );
		if ( $term_id ) {
			if ( is_array($term_id) ) { $term_id = $term_id['term_id']; }
			if ( isset( $term->term_id ) ) {

				$this->processed_terms[intval( $term->term_id )] = (int) $term_id;
			}
		  	continue;
		}

		if ( empty( $term->term_parent ) ) {
		  $parent = 0;
		} else {
		  $parent = term_exists( $term->term_parent, $term->taxonomy );
		  if ( is_array( $parent ) ) { $parent = $parent['term_id']; }
		}
		$description = isset( $term->description ) ? $term->description : '';
		$termarr = array( 'slug' => $term->slug, 'description' => $description, 'parent' => intval($parent) );

		$new_term = wp_insert_term( $term->name, $term->taxonomy, $termarr );
		if ( ! is_wp_error( $new_term ) ) {

			if ( ! empty( $new_term['term_id'] ) && ! empty( $term->termmeta ) ) {

				foreach ( $term->termmeta as $meta_key => $meta_vals ) {

					$meta_vals	= ! is_array( $meta_vals ) ? array( $meta_vals ) : $meta_vals;
					foreach ( $meta_vals as $meta_val ) {

						update_term_meta( $new_term['term_id'], $meta_key, $meta_val );
					}
				}
			}

			$this->processed_terms[intval( $term->term_id )] = intval( $new_term['term_id'] );

		} else {

		  $this->errors['terms']['user'][] = esc_html__( 'Failed to import post tag: ', 'plethora-framework' ) . esc_html($tag->name);
		  $this->errors['terms']['dev'][] = $new_term->get_error_message();
		  continue;
		}
	  }

	  unset( $this->terms );
	  return true;
	}

	public function import_post( $post_type, $post_import_key = NULL ) { 
	  // Set posts to import according to post type
	  $import_posts = array();
	  if ( isset( $this->posts->$post_type ) ) {

		foreach ( $this->posts->$post_type as $post_key => $post_obj ) {

		  if ( $post_type === 'attachment' && ! is_null( $post_import_key ) && $post_import_key == $post_key ) {

			  $import_posts = array( $post_obj );

		  } elseif ( $post_type !== 'attachment' ) {

			  $import_posts[] = $post_obj;
		  }
		}    
	  }
	  foreach ( $import_posts as $post ) {

		if ( isset( $post ) && ! post_type_exists( $post->post_type ) ) {
		  printf( esc_html__( 'Failed to import &#8220;%s&#8221;: Invalid post type %s', 'plethora-framework' ),
			esc_html($post->post_title), esc_html($post->post_type) );
		  echo '<br />';
		  continue;
		}

		if ( isset( $this->processed_posts[$post->ID] ) && ! empty( $post->ID ) )
		  continue;

		if ( $post->post_status == 'auto-draft' )
		  continue;

		if ( 'nav_menu_item' == $post->post_type ) {
		  
		  $this->import_post_menu_item( $post );
		  continue;
		}

		$post_type_object = get_post_type_object( $post->post_type );

		$post_exists = post_exists( $post->post_title, '', $post->post_date );
		if ( $post_exists && get_post_type( $post_exists ) == $post->post_type ) {
		  printf( esc_html__('%s &#8220;%s&#8221; already exists.', 'plethora-framework'), $post_type_object->labels->singular_name, esc_html($post->post_title) );
		  echo '<br />';
		  $comment_post_ID = $post_id = $post_exists;
		} else {
		  $post_parent = (int) $post->post_parent;
		  if ( $post_parent ) {
			// if we already know the parent, map it to the new local ID
			if ( isset( $this->processed_posts[$post_parent] ) ) {
			  $post_parent = $this->processed_posts[$post_parent];
			// otherwise record the parent for later
			} else {
			  $this->post_orphans[intval($post->ID)] = $post_parent;
			  $post_parent = 0;
			}
		  }

		  // map the post author
		  $author = sanitize_user( $post->post_author, true );
		  if ( isset( $this->author_mapping[$author] ) )
			$author = $this->author_mapping[$author];
		  else
			$author = (int) get_current_user_id();

		  $postdata = array(
			'import_id' => $post->ID, 
			'post_author' => $author, 
			'post_date' => $post->post_date,
			'post_date_gmt' => $post->post_date_gmt, 
			'post_content' => $post->post_content,
			'post_excerpt' => $post->post_excerpt, 
			'post_title' => $post->post_title,
			'post_status' => $post->post_status, 
			'post_name' => $post->post_name,
			'comment_status' => $post->comment_status, 
			'ping_status' => $post->ping_status,
			'guid' => $post->guid, 
			'post_parent' => $post_parent, 
			'menu_order' => $post->menu_order,
			'post_type' => $post->post_type, 
			'post_password' => $post->post_password
		  );

		  $original_post_ID = $post->ID;

		  if ( 'attachment' == $postdata['post_type'] ) {
			$remote_url = ! empty($post->attachment_url) ? $post->attachment_url : $post->guid;

			// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
			// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
			$postdata['upload_date'] = $post->post_date;
			if ( isset( $post->postmeta ) ) {

			  foreach( $post->postmeta as $key => $meta ) {

				if ( $key == '_wp_attached_file' ) {

				  if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta[0], $matches ) ) {

					$postdata['upload_date'] = $matches[0];
				  }
				  break;
				}
			  }
			}

			$comment_post_ID = $post_id = $this->import_post_attachments( $postdata, $remote_url );

		  } else {

			$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
		  }

		  if ( is_wp_error( $post_id ) ) {
			printf( esc_html__( 'Failed to import %s &#8220;%s&#8221;', 'plethora-framework' ),
			  $post_type_object->labels->singular_name, esc_html($post->post_title) );
			if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
			  echo ': ' . $post_id->get_error_message();
			echo '<br />';
			continue;
		  }

		  if ( isset( $post->is_sticky ) && $post->is_sticky == 1 )
			stick_post( $post_id );
		}

		// map pre-import ID to local ID
		$this->processed_posts[intval($post->ID)] = (int) $post_id;

		if ( ! isset( $post->terms ) )
		  $post->terms = array();

		// add categories, tags and other terms
		if ( ! empty( $post->terms ) ) {
		  $terms_to_set = array();
		  foreach ( $post->terms as $taxonomy => $post_terms ) {

			foreach( $post_terms as $term ) {

			  $term_exists = term_exists( $term->slug, $taxonomy );
			  $term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
			  if ( ! $term_id ) {
				$t = wp_insert_term( $term->name, $taxonomy, array( 'slug' => $term->slug ) );
				if ( ! is_wp_error( $t ) ) {
				  $term_id = $t['term_id'];
				} else {
				  printf( esc_html__( 'Failed to import %s %s', 'plethora-framework' ), esc_html($taxonomy), esc_html($term->name) );
				  if ( defined('IMPORT_DEBUG') && IMPORT_DEBUG )
					echo ': ' . $t->get_error_message();
				  echo '<br />';
				  continue;
				}
			  }
			  $terms_to_set[$taxonomy][] = intval( $term_id );
			}
		  }

		  foreach ( $terms_to_set as $tax => $ids ) {
			$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
		  }
		  unset( $post->terms, $terms_to_set );
		}

		if ( ! isset( $post->comments ) )
		  $post->comments = array();

		// add/update comments
		if ( ! empty( $post->comments ) ) {
		  $num_comments = 0;
		  $inserted_comments = array();
		  foreach ( $post->comments as $comment ) {
			$comment_id = $comment->comment_ID;
			$newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
			$newcomments[$comment_id]['comment_author']       = $comment->comment_author;
			$newcomments[$comment_id]['comment_author_email'] = $comment->comment_author_email;
			$newcomments[$comment_id]['comment_author_IP']    = $comment->comment_author_IP;
			$newcomments[$comment_id]['comment_author_url']   = $comment->comment_author_url;
			$newcomments[$comment_id]['comment_date']         = $comment->comment_date;
			$newcomments[$comment_id]['comment_date_gmt']     = $comment->comment_date_gmt;
			$newcomments[$comment_id]['comment_content']      = $comment->comment_content;
			$newcomments[$comment_id]['comment_approved']     = $comment->comment_approved;
			$newcomments[$comment_id]['comment_type']         = $comment->comment_type;
			$newcomments[$comment_id]['comment_parent']     = $comment->comment_parent;
			$newcomments[$comment_id]['commentmeta']          = isset( $comment->commentmeta ) ? $comment->commentmeta : array();
			if ( isset( $this->processed_authors[$comment->user_id] ) )
			  $newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment->user_id];
		  }
		  ksort( $newcomments );

		  foreach ( $newcomments as $key => $comment ) {
			// if this is a new post we can skip the comment_exists() check
			if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
			  if ( isset( $inserted_comments[$comment['comment_parent']] ) )
				$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
			  $comment = wp_filter_comment( $comment );
			  $inserted_comments[$key] = wp_insert_comment( $comment );

			  foreach( $comment['commentmeta'] as $meta ) {
				$value = maybe_unserialize( $meta['value'] );
				add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
			  }

			  $num_comments++;
			}
		  }
		  unset( $newcomments, $inserted_comments, $post->comments );
		}

		if ( ! isset( $post->postmeta ) )
		  $post->postmeta = array();

		// add/update post meta
		if ( ! empty( $post->postmeta ) ) {
		  foreach ( $post->postmeta as $meta_key => $meta_value ) {
			$key = $meta_key;
			$value = false;

			if ( '_edit_last' == $key ) {
			  if ( isset( $this->processed_authors[intval($meta_value[0])] ) )
				$value = $this->processed_authors[intval($meta_value[0])];
			  else
				$key = false;
			}

			if ( $key ) {
			  // export gets meta straight from the DB so could have a serialized string
			  if ( ! $value )
				$value = maybe_unserialize( $meta_value[0] );

			  add_post_meta( $post_id, $key, $value );

			  // if the post has a featured image, take note of this in case of remap
			  if ( '_thumbnail_id' == $key )
				$this->featured_images[$post_id] = (int) $value;
			}
		  }
		}
	  }

	  return true;
	}

	function import_post_attachments( $post, $url ) {
	  if ( ! $this->allow_fetch_attachments() )
		echo new WP_Error( 'attachment_processing_error',
		  esc_html__( 'Fetching attachments is not enabled', 'plethora-framework' ) );

	  // if the URL is absolute, but does not contain address, then upload it assuming base_site_url
	  if ( preg_match( '|^/[\w\W]+$|', $url ) )
		$url = rtrim( $this->base_url, '/' ) . $url;

	  $upload = $this->fetch_remote_file( $url, $post );
	  if ( is_wp_error( $upload ) )
		return $upload;

	  if ( $info = wp_check_filetype( $upload['file'] ) )
		$post['post_mime_type'] = $info['type'];
	  else
		return new WP_Error( 'attachment_processing_error', esc_html__('Invalid file type', 'plethora-framework') );

	  $post['guid'] = $upload['url'];

	  // as per wp-admin/includes/upload.php
	  $post_id = wp_insert_attachment( $post, $upload['file'] );
	  wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

	  // remap resized image URLs, works by stripping the extension and remapping the URL stub.
	  if ( preg_match( '!^image/!', $info['type'] ) ) {
		$parts = pathinfo( $url );
		$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

		$parts_new = pathinfo( $upload['url'] );
		$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

		$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
	  }

	  return $post_id;
	}


	/**
	 * Attempt to create a new menu item from import data
	 *
	 * Fails for draft, orphaned menu items and those without an associated nav_menu
	 * or an invalid nav_menu term. If the post type or term object which the menu item
	 * represents doesn't exist then the menu item will not be imported (waits until the
	 * end of the import to retry again before discarding).
	 *
	 * @param array $item Menu item details from WXR file
	 */
	function import_post_menu_item( $item ) {
	  // skip draft, orphaned menu items
	  if ( 'draft' == $item->post_status )
		return;

	  $menu_slug = false;
	  if ( isset($item->slug) ) {

		$menu_slug = $item->slug;
	  }

	  // no nav_menu term associated with this menu item
	  if ( ! $menu_slug ) {
		_e( 'Menu item skipped due to missing menu slug', 'plethora-framework' );
		echo '<br />';
		return;
	  }

	  $menu_id = term_exists( $menu_slug, 'nav_menu' );
	  if ( ! $menu_id ) {
		printf( esc_html__( 'Menu item skipped due to invalid menu slug: %s', 'plethora-framework' ), esc_html( $menu_slug ) );
		echo '<br />';
		return;

	  } else {

		$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
	  }

	  foreach ( $item->postmeta as $key => $meta ) {

		$$key = $meta[0];
	  }

	  if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {

		$_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];

	  } else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {

		$_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];

	  } else if ( 'custom' != $_menu_item_type ) {

		// associated object is missing or not imported yet, we'll retry later
		$this->missing_menu_items[] = $item;
		return;
	  }

	  if ( isset( $this->processed_menu_items[intval($_menu_item_menu_item_parent)] ) ) {

		$_menu_item_menu_item_parent = $this->processed_menu_items[intval($_menu_item_menu_item_parent)];

	  } else if ( $_menu_item_menu_item_parent ) {

		$this->menu_item_orphans[intval($item->ID)] = (int) $_menu_item_menu_item_parent;
		$_menu_item_menu_item_parent = 0;
	  }

	  // wp_update_nav_menu_item expects CSS classes as a space separated string
	  $_menu_item_classes = maybe_unserialize( $_menu_item_classes );
	  if ( is_array( $_menu_item_classes ) ) {

		$_menu_item_classes = implode( ' ', $_menu_item_classes );
	  }

	  $args = array(
		'menu-item-object-id'   => $_menu_item_object_id,
		'menu-item-object'      => $_menu_item_object,
		'menu-item-parent-id'   => $_menu_item_menu_item_parent,
		'menu-item-position'    => intval( $item->menu_order ),
		'menu-item-type'        => $_menu_item_type,
		'menu-item-title'       => $item->post_title,
		'menu-item-url'         => $_menu_item_url,
		'menu-item-description' => $item->post_content,
		'menu-item-attr-title'  => $item->post_excerpt,
		'menu-item-target'      => $_menu_item_target,
		'menu-item-classes'     => $_menu_item_classes,
		'menu-item-xfn'         => $_menu_item_xfn,
		'menu-item-status'      => $item->post_status
	  );

	  $id = wp_update_nav_menu_item( $menu_id, 0, $args );
	  if ( $id && ! is_wp_error( $id ) ) {
		$this->processed_menu_items[intval($item->ID)] = (int) $id;
	  }
	}


	/**
	 * Attempt to download a remote file attachment
	 *
	 * @param string $url URL of item to fetch
	 * @param array $post Attachment details
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	function fetch_remote_file( $url, $post ) {
	  // extract the file name and extension from the url
	  $file_name = basename( $url );

	  // get placeholder file in the upload dir with a unique, sanitized filename
	  $upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
	  if ( $upload['error'] )
		return new WP_Error( 'upload_dir_error', $upload['error'] );

	  // fetch the remote url and write it to the placeholder file
	  $request = new WP_Http;
	  $headers = $request->get( $url, array( 'filename' => $upload['file'], 'stream' => true ) );

	  // request failed
	  if ( ! $headers ) {
		@unlink( $upload['file'] );
		return new WP_Error( 'import_file_error', esc_html__('Remote server did not respond', 'plethora-framework') );
	  }

	  // make sure the fetch was successful
	  if ( $headers['response']['code'] != '200' ) {
		@unlink( $upload['file'] );
		return new WP_Error( 'import_file_error', sprintf( esc_html__('Remote server returned error response %1$d %2$s', 'plethora-framework'), esc_html($headers['response']['message']), $headers['response']['code'] ) );
	  }

	  $filesize = filesize( $upload['file'] );

	  if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
		@unlink( $upload['file'] );
		return new WP_Error( 'import_file_error', esc_html__('Remote file is incorrect size', 'plethora-framework') );
	  }

	  if ( 0 == $filesize ) {
		@unlink( $upload['file'] );
		return new WP_Error( 'import_file_error', esc_html__('Zero size file downloaded', 'plethora-framework') );
	  }

	  return $upload;
	}

	public function import_widgets() {

	  $raw_data = $this->widgets;
	  global $wp_registered_sidebars;

	  // Get all available widgets site supports
	  $available_widgets = $this->get_available_widgets();

	  // Get all existing widget instances
	  $widget_instances = array();
	  foreach ( $available_widgets as $widget_data ) {

		$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
	  }

	  // Begin results
	  $results = array();

	  // Loop import data's sidebars
	  foreach ( $raw_data as $sidebar_id => $widgets ) {

		// Skip inactive widgets
		// (should not be in export file)
		if ( 'wp_inactive_widgets' == $sidebar_id ) {
		  continue;
		}

		// Check if sidebar is available on this site
		// Otherwise add widgets to inactive, and say so
		if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {

		  $sidebar_available    = true;
		  $use_sidebar_id       = $sidebar_id;
		  $sidebar_message_type = 'success';
		  $sidebar_message      = '';
		} else {

		  $sidebar_available    = false;
		  $use_sidebar_id       = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
		  $sidebar_message_type = 'error';
		  $sidebar_message      = esc_html__( 'Sidebar does not exist in theme (using Inactive)', 'plethora-framework' );
		}

		// Result for sidebar
		$results[$sidebar_id]['name']         = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
		$results[$sidebar_id]['message']      = $sidebar_message;
		$results[$sidebar_id]['widgets']      = array();

		// Loop widgets
		foreach ( $widgets as $widget_instance_id => $widget ) {

		  $widget = is_object( $widget ) ? get_object_vars( $widget ) : $widget;

		  $fail = false;

		  // Get id_base (remove -# from end) and instance ID number
		  $id_base            = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
		  $instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

		  // Does site support this widget?
		  if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
			$fail = true;
			$widget_message_type = 'error';
			$widget_message      = esc_html__( 'Site does not support widget', 'plethora-framework' ); // explain why widget not imported
		  }

		  // Does widget with identical settings already exist in same sidebar?
		  if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

			// Get existing widgets in this sidebar
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			$sidebar_widgets  = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

			// Loop widgets with ID base
			$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
			foreach ( $single_widget_instances as $check_id => $check_widget ) {

			  // Is widget in same sidebar and has identical settings?
			  if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

				$fail = true;
				$widget_message_type = 'warning';
				$widget_message      = esc_html__( 'Widget already exists', 'plethora-framework' ); // explain why widget not imported

				break;
			  }
			}
		  }
		  // No failure
		  if ( ! $fail ) {
			// Add widget instance
			$single_widget_instances   = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
			$single_widget_instances   = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
			$single_widget_instances[] = $widget; // add it
			  // Get the key it was given
			  end( $single_widget_instances );
			  $new_instance_id_number = key( $single_widget_instances );
			  // If key is 0, make it 1
			  // When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
			  if ( '0' === strval( $new_instance_id_number ) ) {
				$new_instance_id_number = 1;
				$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
				unset( $single_widget_instances[0] );
			  }
			  // Move _multiwidget to end of array for uniformity
			  if ( isset( $single_widget_instances['_multiwidget'] ) ) {
				$multiwidget = $single_widget_instances['_multiwidget'];
				unset( $single_widget_instances['_multiwidget'] );
				$single_widget_instances['_multiwidget'] = $multiwidget;
			  }
			  // Update option with new widget
			  update_option( 'widget_' . $id_base, $single_widget_instances );
			// Assign widget instance to sidebar
			$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
			$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
			$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
			update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data
			// After widget import action
			$after_widget_import = array(
			  'sidebar'           => $use_sidebar_id,
			  'sidebar_old'       => $sidebar_id,
			  'widget'            => $widget,
			  'widget_type'       => $id_base,
			  'widget_id'         => $new_instance_id,
			  'widget_id_old'     => $widget_instance_id,
			  'widget_id_num'     => $new_instance_id_number,
			  'widget_id_num_old' => $instance_id_number
			);
			// Success message
			if ( $sidebar_available ) {
			  $widget_message_type = 'success';
			  $widget_message      = esc_html__( 'Imported', 'plethora-framework' );
			} else {
			  $widget_message_type = 'warning';
			  $widget_message      = esc_html__( 'Imported to Inactive', 'plethora-framework' );
			}
		  }
		  // Result for widget instance
		  $results[$sidebar_id]['widgets'][$widget_instance_id]['name']         = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
		  $results[$sidebar_id]['widgets'][$widget_instance_id]['title']        = ! empty( $widget->title ) ? $widget->title : esc_html__( 'No Title', 'plethora-framework' ); // show "No Title" if widget instance is untitled
		  $results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
		  $results[$sidebar_id]['widgets'][$widget_instance_id]['message']      = $widget_message;
		}
	  }
	  // return apply_filters( 'wie_import_results', $results );
	  return true;
	}

	public function import_theme_options() {

	  $theme_options = $this->object_to_array( $this->theme_options );
	  update_option( THEME_OPTVAR, $theme_options );
	}


	/**
	 * Attempt to associate posts and menu items with previously missing parents
	 *
	 * An imported post's parent may not have been imported when it was first created
	 * so try again. Similarly for child menu items and menu items which were missing
	 * the object (e.g. post) they represent in the menu
	 */
	public function backfill_parents() {
	  global $wpdb;

	  // find parents for post orphans
	  foreach ( $this->post_orphans as $child_id => $parent_id ) {
		$local_child_id = $local_parent_id = false;
		if ( isset( $this->processed_posts[$child_id] ) )
		  $local_child_id = $this->processed_posts[$child_id];
		if ( isset( $this->processed_posts[$parent_id] ) )
		  $local_parent_id = $this->processed_posts[$parent_id];

		if ( $local_child_id && $local_parent_id )
		  $wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
	  }

	  // all other posts/terms are imported, retry menu items with missing associated object
	  $missing_menu_items = $this->missing_menu_items;
	  foreach ( $missing_menu_items as $item )
		$this->import_post_menu_item( $item );

	  // find parents for menu item orphans
	  foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
		$local_child_id = $local_parent_id = 0;
		if ( isset( $this->processed_menu_items[$child_id] ) )
		  $local_child_id = $this->processed_menu_items[$child_id];
		if ( isset( $this->processed_menu_items[$parent_id] ) )
		  $local_parent_id = $this->processed_menu_items[$parent_id];

		if ( $local_child_id && $local_parent_id )
		  update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
	  }
	}

	/**
	 * Use stored mapping information to update old attachment URLs
	 */
	public function backfill_attachment_urls() {
	  global $wpdb;
	  // make sure we do the longest urls first, in case one is a substring of another
	  uksort( $this->url_remap, array( $this, 'compare_string_lengths') );

	  foreach ( $this->url_remap as $from_url => $to_url ) {
		// remap urls in post_content
		$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url) );
		// remap enclosure urls
		$result = $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url) );
	  }
	}

	/**
	 * Update _thumbnail_id meta to new, imported attachment IDs
	 */
	public function remap_featured_images() {
	  // cycle through posts that have a featured image
	  foreach ( $this->featured_images as $post_id => $value ) {
		if ( isset( $this->processed_posts[$value] ) ) {
		  $new_id = $this->processed_posts[$value];
		  // only update if there's a difference
		  if ( $new_id != $value )
			update_post_meta( $post_id, '_thumbnail_id', $new_id );
		}
	  }
	}

	/**
	 * Set menu location for all menus
	 */
	public function set_menu_locations() {

	  if ( empty( $this->processed_menu_items ) ) { return; }

	  $registered_nav_menus = get_registered_nav_menus();
	  $locations = array();

	  foreach ( $registered_nav_menus as $regmenu_key => $regmenu_name ) {

		if ( array_key_exists( $regmenu_key, $this->menu_locations )  ) {

		  $location_menu_obj = $this->menu_locations->$regmenu_key;

		  if ( isset( $location_menu_obj->slug ) ) {

			$menu = get_term_by( 'slug', $location_menu_obj->slug, 'nav_menu');
			
			if ( isset( $menu->term_id ) ) {

			  $locations[$regmenu_key] = $menu->term_id;
			}
		  }
		}
	  }

	  set_theme_mod('nav_menu_locations', $locations);
	}

	public function set_static_pages() {

	  $static_pages = $this->static_pages;
	  if ( isset( $static_pages ) ) {

		foreach ( $static_pages as $static_type => $page_slug ) {

		  switch ( $static_type ) {
			case 'page_on_front':

			  $page_on_front_id = $this->get_page_id_by_slug( $page_slug );
			  if ( $page_on_front_id ) {

				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $page_on_front_id );
			  }
			  break;
			
			case 'page_for_posts':

			  $page_for_posts_id = $this->get_page_id_by_slug( $page_slug );
			  if ( $page_for_posts_id ) {

				update_option( 'page_for_posts', $page_for_posts_id );
			  }
			  break;
		  }
		}
		
		flush_rewrite_rules(); // will refresh rewrite rules
	  }
	}

//// AJAX METHODS END    

//// UI METHODS START

	public function ui() {
	  echo '<div class="pdi">';
	  echo '  <section class="pdi-header">';
	  echo $this->ui_header(); // html output already escaped where needed
	  echo '</section>';
	  echo '  <section class="pdi-workpanel" id="pdi-workpanel">';
	  echo $this->ui_demos_grid();  // html output already escaped where needed
	  echo '  </section>';
	  echo '  <div class="loader"><img src="'. plugin_dir_url( __FILE__ ) .'/assets/ring.gif" /></div>';
	  echo '</div>';
	}

	public function ui_header() {

	  $header = '<h1><span class="dashicons dashicons-migrate"></span>'. THEME_DISPLAYNAME  .' '. esc_html__('Demo Importer') .'</h1>';
	  $header .= '<p>'. sprintf( esc_html__('Preview and select a demo to import. We always recommend importing only one demo at a single installation. Before trying to import the demo of your choice, make sure that you have installed / activated all required plugins. %1$sNote that most of the images will be blurred, due to license restrictions.%2$s'), '<strong style="color:darkred">', '</strong>' ) .'</p>';
	  return $header;
	}

	public function ui_demos_grid() {

	  $demos = $this->demos;
	  $output = '<ul id="og-grid" class="og-grid">';

	  $plugin_info_header = '<h3>Plugins required: </h3>';
	  foreach ( $demos as $slug => $demo ) { 
		  
		  $title           = !empty( $demo['title'] ) ? esc_attr( $demo['title'] ) : '<i>'. esc_html__('Untitled', 'plethora-framework') .'</i>';
		  $desc            = !empty( $demo['desc'] ) ? esc_attr( $demo['desc'] ) : 'There is no description for this demo';
		  $online          = !empty( $demo['online'] ) ? '<a href="'. $demo['online'] .'" target="_blank">'. esc_html__('Preview this demo online', 'plethora-framework') .'</a>'  : '';
		  $online          = esc_attr( $online );
		  $plugin_info     = $this->ui_plugins_info( $demo['plugins_required'] );
		  $plugins         = !empty( $plugin_info ) ? esc_attr( $plugin_info_header . $plugin_info ) : '';
		  $importdemo_text = esc_html__('Go To Import Panel', 'plethora-framework');
		  $importdemo      = $this->required_plugins_inactive === 0 ? esc_attr( '<a href="#" class="button button-primary start_import" id="'.$slug.'">'.$importdemo_text.'</a>' ) : esc_attr( '<span class="button button-primary disabled" title="'.esc_html__('Please install all required plugins first', 'plethora-framework') .'">'.$importdemo_text.'</a>' );
		  $output .= '<li>';
		  $output .= '<a href="#" data-largesrc="'.$demo['thumbnail'].'" data-title="'. $title.'" data-description="'. $desc .'" data-online="'. $online.'" data-plugins="'. $plugins .'" data-importdemo="'. $importdemo .'">';
		  $output .= '<img src="'.$demo['thumbnail'].'" alt="'.$demo['slug'].'" width="225"/>';
		  $output .= '</a>';
		  $output .= '<h4><center>'.$title.'</center></h4>';
		  $output .= '</li>';
	  }
	  $output .= '</ul>';
	  return $output;

	}

	public function ui_plugins_info( $plugins_required ) {
	  
	  $plugins_installed = get_plugins();
	  $plugin_info = '';
	  $this->required_plugins_inactive = 0;

	  foreach ( $plugins_required as $plugin_slug => $plugin ) {

		if ( is_plugin_active( $plugin_slug ) ) { 

		  $plugin_info .= '<div><a href="'. $plugin->PluginURI .'" target="_blank" style="color:darkgreen">'. $plugin->Name .' ( by '. wp_strip_all_tags( $plugin->Author ) .' )</a><span>Kudos! You have already installed / activated this plugin.</span></div>';

		} else {

		  ++$this->required_plugins_inactive; 

		  if ( array_key_exists( $plugin_slug, $plugins_installed ) ) { 

			$plugin_info .= '<div><a href="'. esc_url( network_admin_url( 'plugins.php' ) ) .'" target="_blank" style="color:darkred">'. $plugin->Name .' ( by '. wp_strip_all_tags( $plugin->Author ) .' )</a><span>You have to activate it before importing this demo.</span></div>';

		  } else {

			$plugin_info .= '<div><a href="'. esc_url( network_admin_url( 'plugin-install.php?tab=search&s='. $plugin->Name .'&type=term' ) ).'" target="_blank" style="color:red">'. $plugin->Name .' ( by '. wp_strip_all_tags( $plugin->Author ) .' )</a><span>You have to install AND activate it before importing this demo.</span></div>';
		  }
		}
	  }

	  return $plugin_info;
	}


	public function ui_demo_panel() {

	  if ( $this->verify_nonce() && $this->set_demo() ) { // verify nonce and set selected demo data first!
		
		$this->remove_helper_transients(); // IMPORTANT!!! Remove helper import transients
		$demo_thumb  = $this->demo_url .'/'. $this->demo_slug . '.png';
		$demo_title  = $this->demo->title;
		$demo_desc   = $this->demo->desc;
		$demo_online = !empty( $this->demo->online ) ? '<a href="'. esc_url( $this->demo->online ) .'" target="_blank">'. esc_html__('Preview this demo online', 'plethora-framework') .'</a>'  : '';
		$output = '<div class="demo_sidepanel">';
		$output .= '<a href="'.get_site_url().'" class="import_button import_init" id="'. $this->demo_slug .'">Click here to start import<br>Do not leave page after</a>';
		$output .= '<img src="'. $demo_thumb .'" />';
		$output .= '<p>'. esc_html__('Demo', 'plethora-framework') .': <strong>'. $demo_title .'</strong></p>';
		$output .= '<p>'. esc_html__('Online Version', 'plethora-framework') .': <strong>'. $demo_online  .'</strong></p>';
		// $output .= $this->ui_demo_panel_options();
		$output .= '</div>';

		$output .= '<div class="demo_importpanel">';
		$output .= '<table cellspacing = "0" cellborders="0">';

		$item_loading_img = PLE_FLIB_FEATURES_URI .'/module/demoimporter/assets/ripple.gif';
		foreach ( $this->get_import_mask( true ) as $import_type => $import_type_data ) {

		  if ( $import_type === 'attachment') { 

			  $attachments = $this->demo->posts->attachment;
			  $attach_count = count( $attachments );
			  $output .= '<tr>';
			  $output .= '<th id="'.$import_type.'" class="'.$import_type.'"><h3>Attachments</h3></th>';
			  $output .= '<td class="'.$import_type.' pdi-status centered">';
			  $output .= '<div class="placeholder">'. $import_type_data['in_queue_notice'] .'</div>';
			  
			  foreach ( $attachments as $key => $attach_obj ) { 

				$reverse_key = $attach_count - intval( $key );
				$item_title = !empty( $attach_obj->title ) ? $attach_obj->title : $attach_obj->post_title;
				$output .= '<div class="a'.$reverse_key.' import_item hide">'. $item_title .'</div>';
			  }
			  $output .= '</td>';
			  $output .= '</tr>';

		  } else {

			  $output .= '<tr>';
			  $output .= '<th class="'.$import_type.'" id="'.$import_type.'"><h3>'. $import_type_data['title'] .'</h3></th>';
			  $output .= '<td class="'.$import_type.' pdi-status centered">';
			  $output .= '<div class="import_item">'. $import_type_data['in_queue_notice'] .'</div>';
			  $output .= '</td>';
			  $output .= '</tr>';
		  }
		}
		$output .= '</table>';
		$output .= '</div>';

		echo $output;  // html output already escaped where needed
	  }

	  exit;
	}

	/**
	 * Display pre-import options, author importing/mapping and option to
	 * fetch attachments
	 */
	function ui_demo_panel_options() {
	  $j = 0;
	  $output = '';
	  if ( ! empty( $this->posts ) && ! empty( $this->authors ) ) {

		$output .= '<h3>'. esc_html__( 'Assign Authors', 'plethora-framework' ) .'</h3>';
		$output .= '<p>'. esc_html__( 'To make it easier for you to edit and save the imported content, you may want to reassign the author of the imported item to an existing user of this site. For example, you may want to import all the entries as <code>admin</code>s entries.', 'plethora-framework' ) .'</p>';
		foreach ( $this->authors as $author ) {
		  $output .= '<div> '. $this->ui_demo_panel_author_select( $j++, $author ) .'</div>';
		}
		if ( $this->allow_create_users() ) {

		  $output .= '<p>'.  sprintf ( esc_html__( 'If a new user is created by WordPress, a new password will be randomly generated and the new user&#8217;s role will be set as %s. Manually changing the new user&#8217;s details will be necessary.', 'plethora-framework' ), esc_html( get_option('default_role') ) ) .'</p>';
		}
	  }
	  return $output;
	}

	/**
	 * Display import options for an individual author. That is, either create
	 * a new user based on import info or map to an existing user
	 *
	 * @param int $n Index for each author in the form
	 * @param array $author Author information, e.g. login, display name, email
	 */
	function ui_demo_panel_author_select( $n, $author ) {

	  $output = esc_html__( 'Import author:', 'plethora-framework' );
	  $output .= ' <strong>' . esc_html( $author->data->display_name );
	  $output .= ' (' . esc_html( $author->data->user_login ) . ')';
	  $output .= '</strong><br />';
	  $output .= '<div style="margin-left:18px">';
	  $create_users = $this->allow_create_users();
	  if ( $create_users ) {
		$output .= esc_html__( 'or create new user with login name:', 'plethora-framework' );
		$output .= '<input type="text" name="user_new['.$n.']" value="" /><br />';
		$output .= esc_html__( 'or assign posts to an existing user:', 'plethora-framework' );
		$output .= wp_dropdown_users( array( 'name' => "user_map[$n]", 'multi' => true, 'echo' => false , 'show_option_all' => esc_html__( '- Select -', 'plethora-framework' ) ) );
		$output .= '<input type="hidden" name="imported_authors['.$n.']" value="' . esc_attr( $author->data->user_login ) . '" />';
		$output .= '';
	  }
	  $output .= '</div>';
	  return $output;
	}

//// UI METHODS END

//// HELPERS START

	/**
	 * Making sure that the installation supports json files
	 */
	public function add_json_mime( $mime_types ) {

	  $mime_types['json'] = 'application/json';
	  return $mime_types;
	}

	/**
	 * Returns attachments counts for all demos. Used in post import ajax loop
	 */
	public function get_attachments_counts() {

	  $attach_counts = array();
	  foreach ( $this->demos as $demo_slug => $demo ) {
		
		$attach_counts[$demo_slug] = $demo['attachments_count'];
	  }

	  return $attach_counts;
	}

	/**
	 * Decide whether or not the importer is allowed to create users.
	 */
	function allow_create_users() {

	  return apply_filters( 'import_allow_create_users', $this->allow_create_users );
	}

	/**
	 * Decide whether or not the importer should attempt to download attachment files.
	 */
	function allow_fetch_attachments() {

	  return apply_filters( 'import_allow_fetch_attachments', $this->allow_fetch_attachments );
	}

	/**
	 * Returns the difference in length between two strings
	 */
	public static function compare_string_lengths( $a, $b ) {

	  return strlen($b) - strlen($a);
	}

	/**
	 * Added to http_request_timeout filter to force timeout at set seconds during import
	 */
	function set_request_timeout() {

	  return $this->request_timeout;
	}

	/**
	 * Return available widgets for this installation
	 */
	function get_available_widgets() {

	  global $wp_registered_widget_controls;
	  $widget_controls   = $wp_registered_widget_controls;
	  $available_widgets = array();

	  foreach ( $widget_controls as $widget ) {

		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

		  $available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
		  $available_widgets[$widget['id_base']]['name'] = $widget['name'];
		}
	  }

	  return $available_widgets;
	}

	function get_page_id_by_slug( $post_slug ) {

	  $post_id = 0;
	  $args=array(
		'name'                => $post_slug,
		'post_type'           => 'page',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => 1
	  );

	  $query = null;
	  $query = new WP_Query($args);

	  $raw_posts = $query->get_posts();
	  foreach ( $raw_posts as $raw_post ) {

		$post_id = $raw_post->ID;
	  }

	  wp_reset_query();

	  return $post_id;
	}

	function object_to_array( $object ) {

	  if ( !is_array( $object ) && !is_object( $object ) ) { return $object; } 
	  
	  if( is_object( $object ) ) {

		$object = get_object_vars( $object );
	  } 
	  
	  return array_map( array( $this, 'object_to_array') , $object );
	}        

//// HELPERS END
 }
}