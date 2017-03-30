<?php

/**
 * Woocommerce functionality
 * 
 */
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Ecwid') && class_exists('EcwidPlatform') ) {

	class Plethora_Module_Ecwid {
        
		public static $feature_title         = "Ecwid Support Module";							// Feature display title  (string)
		public static $feature_description   = "Adds support for Ecwid plugin to your theme";	// Feature display description (string)
		public static $theme_option_control  = true;													// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();									// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;												// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )
		
		public function __construct() {

		// WooCommerce support
	        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20);  // Style enqueing - keep priority to 20 to make sure that it will be loaded after Woo defaults
		}


		public function enqueue() {

    		wp_register_style( 'plethora-ecwid', PLE_THEME_ASSETS_URI . '/css/ecwid.css', array( 'ecwid-css' ) );
            wp_enqueue_style( 'plethora-ecwid' );
		}
	}
}