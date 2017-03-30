<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Description: Loads Redux options framework and the rest Redux extensions
Version: 1.2

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


/**
 * Loads Redux options framework and the rest Redux extensions & configuration
 * 
 * @package Plethora Framework
 * @version 1.0
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c) 2015
 *
 */
class Plethora_Optionsframework {

	public $metaboxes = array();

	function __construct() {
			$this->vendor_support();					// Load Redux vendor suppor ( it's a must if we want to avoid CDN calls )
			$this->load_framework();					// Load main framework
			$this->load_themeoptions();					// Get theme options ( do not instantiate yet )
			add_action('redux/extensions/'. THEME_OPTVAR .'/before', array( $this, 'extensions_loader' ), 0); // This loads all Redux Framework extensions ( Metaboxes, Repeater, Support FAQ and others )
			add_action('init', array( $this, 'get_metaboxes_global_tabs_hooks' ), 20); // Get global metabox tabs hooks ( taken from theme options config class)
			add_action('init', array( $this, 'register_metaboxes' ), 20); // Get global metaboxes hooks ( taken from theme options config class)
	}

	private function load_framework() {

	    # LOAD OPTIONS FRAMEWORK & TRIGGER METABOX HOOKS ( metabox hooks MUST be run before Plethora Features Index creation )
	    if ( !class_exists( 'ReduxFramework' ) && file_exists( PLE_FLIB_LIBS_DIR . '/ReduxFramework/ReduxCore/framework.php' ) ) {
	      
	      // Include the file
	      require_once( PLE_FLIB_LIBS_DIR . '/ReduxFramework/ReduxCore/framework.php' );
	    } 
	}

	private function vendor_support() {

	    if ( ! class_exists( 'ReduxFramework_extension_vendor_support' ) ) {
	        if ( file_exists( PLE_FLIB_LIBS_DIR . '/ReduxFramework/vendor_support/extension_vendor_support.php' )) {
	            require PLE_FLIB_LIBS_DIR . '/ReduxFramework/vendor_support/extension_vendor_support.php';
	            new ReduxFramework_extension_vendor_support();
	        }
	    }
	}

	public function extensions_loader( $ReduxFramework ) { 

		$path = PLE_FLIB_LIBS_DIR . '/ReduxFramework/extensions/';
		$folders = scandir( $path, 1 );
		foreach($folders as $folder) {
			if ($folder === '.' or $folder === '..' or !is_dir($path . $folder) ) {
				continue;	
			}
			$extension_class = 'ReduxFramework_Extension_' . $folder;
			$class_file = $path . $folder . '/extension_' . $folder . '.php';
			$class_file = apply_filters( 'redux/extension/'.$ReduxFramework->args['opt_name'].'/'.$folder, $class_file );
			if( !class_exists( $extension_class ) && file_exists($class_file) ) {
				require_once( $class_file );
			}
			$extension = new $extension_class( $ReduxFramework );
		}
	}

	private function load_themeoptions() { 
	    # LOAD THEME OPTIONS CLASS ( not instantiate yet though! ) & TRIGGER METABOX HOOKS (  )
	    if (file_exists( PLE_THEME_INCLUDES_DIR . '/options.php' )) { 

	      // Include the file
	      require_once( PLE_THEME_INCLUDES_DIR . '/options.php' );
	    }
	}

	public function get_metaboxes_global_tabs_hooks() { 
      // Trigger metabox hooks from Plethora_Themeoptions class
      if ( method_exists('Plethora_Module_Themeoptions', 'metabox_hooks')) { 

        Plethora_Module_Themeoptions::metabox_hooks();

      } elseif ( method_exists('Plethora_Themeoptions', 'metabox_hooks')) { 

        Plethora_Themeoptions::metabox_hooks();
      }
	}

	public function register_metaboxes() {

		global $metaboxes;
		$metaboxes = $this->metaboxes;		
		// Get all hooked metaboxes ( used for features that want to add metaboxes out of the box)             
    	if ( has_filter( 'plethora_metabox_add') ) {
			$metaboxes = apply_filters( 'plethora_metabox_add', $metaboxes );
		}

		$this->metaboxes = $metaboxes;		

		// Add action for Redux 
		add_action('redux/metaboxes/'.THEME_OPTVAR.'/boxes', array( $this, 'add_theme_metaboxes' ));
	}

	public function add_theme_metaboxes( $metaboxes ) {

		// just in case!
		$metaboxes = $this->metaboxes;
		return $metaboxes;
	}


}