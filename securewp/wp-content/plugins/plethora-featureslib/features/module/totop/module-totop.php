<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Top Bar Module

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Totop') ) {

	/**
	 */
	class Plethora_Module_Totop {


		// Feature display title  (string)
		public static $feature_title        = "Move To Top";
		// Feature display description (string)
		public static $feature_description  = "Integration module for Move To Top functionality ";
		// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_control = false;
		// Default activation option status ( boolean )
		public static $theme_option_default	= true;
		// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $theme_option_requires= array();
		// Dynamic class construction ? ( boolean )
		public static $dynamic_construct	= true;
		// Additional method invocation ( string/boolean | method name or false )
		public static $dynamic_method		= false;

		function __construct(){

		  $backtotop = Plethora_Theme::option( THEMEOPTION_PREFIX .'backtotop', 1);

		  if ( $backtotop && is_admin() ) { 

	          add_action( 'plethora_themeoptions_general_misc_fields', array( $this, 'misc_options_tab_fields' )); 
		  
		  } elseif ( $backtotop && ! is_admin() ) { 

      		  add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets') );
      		  add_action( 'wp_footer', array( $this, 'template_part'), 1);
		  }
		}

		function enqueue_assets() {

			 wp_enqueue_script( ASSETS_PREFIX . '-totop' ); // Asset already registered in Framework library       
		}

		function template_part() {

      		Plethora_WP::get_template_part( 'templates/global/totop' );
		}

		function misc_options_tab_fields( $fields ) { 

			$fields[] = array(
						'id'      => THEMEOPTION_PREFIX .'backtotop',
						'type'    => 'switch', 
						'title'   => esc_html__('Back to top functionality', 'plethora-framework'),
						'desc'    => esc_html__('Enable / disable the back to top icon ', 'plethora-framework'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
			);
			return $fields;
		}
	}
}