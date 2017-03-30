<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Controller class for modules

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module') ) {

    /**
     * @package Plethora Controllers
     */
	class Plethora_Module {

		public static $controller_title             = 'Modules Manager';		// CONTROLLER TITLE
		public static $controller_description       = 'Activate/deactivate any Plethora module avaible. Notice that on deactivation, all dependent features will be deactivated automatically.';		
		public static $controller_dynamic_construct = false;					// DYNAMIC CLASS CONSTRUCTION
		public static $controller_dynamic_method    = false;					// INVOKE ANY METHOD AFTER DYNAMIC CONSTRUCTION? ( method name OR false )
		public static $dynamic_features_loading     = true;						// LOAD FEATURES DYNAMICALLY ( always true, false if stated so in controller variables )

		public function __construct() {}

	}

}