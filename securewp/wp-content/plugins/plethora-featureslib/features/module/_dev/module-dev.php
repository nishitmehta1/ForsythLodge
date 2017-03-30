<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			       (c) 2017

In-house Dev Utility  Module

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Dev') ) {

	/**
	 */
	class Plethora_Module_Dev {

		public static $feature_title        = "Dev Module";
		public static $feature_description  = "Provides usefull operation and info for Plethora developers ( in-house use, not a public feature )";
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		public function __construct() {

			add_action('init', array( $this, 'init') );
		}

		public function init() {

		}
	}
}