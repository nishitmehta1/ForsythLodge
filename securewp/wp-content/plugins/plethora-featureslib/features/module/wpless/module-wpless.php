<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M			      (c) 2014-2015

Description: WP Less ( Please reference to this on https://github.com/oyejorge/less.php - DO NOT USE THE OFFICIAL RELEASE )
Version: 1.0

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

  /**
   * @package Plethora Framework
   */

	class Plethora_Module_Wpless{

        public static $feature_title         = "WP Less Module";                       // FEATURE DISPLAY TITLE  
        public static $feature_description   = "Dynamic LESS stylesheet compilation";  // FEATURE DISPLAY DESCRIPTION 
        public static $theme_option_control  = false;                                  // FEATURE CONTROLLED IN THEME OPTIONS PANEL 
        public static $theme_option_default  = true;                                   // DEFAULT ACTIVATION OPTION STATUS 
        public static $theme_option_requires = array();                                // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                                   // DYNAMIC CLASS CONSTRUCTION?
        public static $dynamic_method        = false;                                  // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )		

		public function __construct() {

	        add_action('init', array($this, 'init'), 9);
		}

		public function init() {

			// Load WP-LESS, if not already loaded by other plugin
			if ( !class_exists('WPLessPlugin') && !is_admin() ) {

				define('WP_LESS_COMPILATION', 'deep');

				require_once PLE_FLIB_FEATURES_DIR .'/module/wpless/wp-less/lib/Plugin.class.php';

				// The file is empty...just used for core directory reference
				$WPLessPlugin = WPPluginToolkitPlugin::create('WPLess', PLE_FLIB_FEATURES_DIR .'/module/wpless/wp-less/bootstrap-for-theme.php', 'WPLessPlugin');
				
				// Get and filter basic theme LESS variables declared on Plethora_Themeoptions class
				$less_variables = method_exists('Plethora_Themeoptions', 'less_variables') ? Plethora_Themeoptions::less_variables( array() ) : array();
				$less_variables = apply_filters( 'plethora_module_wpless_variables', $less_variables );

				if ( !empty( $less_variables ) && is_array( $less_variables ) ) { 
			    
			    	$WPLessPlugin->setVariables( $less_variables );
			    }

				add_action('init', array($this, 'hook_less'));

				// READY and WORKING
				add_action('after_setup_theme', array($WPLessPlugin, 'install'));

				// NOT WORKING
				//@see http://core.trac.wordpress.org/ticket/14955
				add_action('uninstall_theme', array($WPLessPlugin, 'uninstall'));

				$WPLessPlugin->dispatch();
			}
		}

       /** 
       * Less file enqueue hook
       * This is use
       * @since 2.0
       *
       */
		function hook_less() {

			if ( !is_admin() ) { 

	            // wp_enqueue_scripts 
			    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_less'), 100);

			}
		}	

       /** 
       * Enqueues less files
       * @since 2.0
       *
       */
		function enqueue_less() {

			$less_files['style'] = array(

				'handle' => ASSETS_PREFIX .'-dynamic-style',
				'src'    => PLE_THEME_ASSETS_URI.'/less/style.less',
				'deps'   => array( ASSETS_PREFIX .'-custom-bootstrap' ),
				'ver'    => false,
				'media'  => 'all'
			);

			$less_files = apply_filters( 'plethora_module_wpless_files', $less_files );

			foreach ( $less_files as $less_file ) {

		        // Should load custom bootstrap stylesheet first
			    wp_register_style( $less_file['handle'], $less_file['src'], $less_file['deps'] );
			    wp_enqueue_style( $less_file['handle'] );
		    }
		}	
	}