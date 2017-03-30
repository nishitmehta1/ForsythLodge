<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2017

Navigation Mini Tools Module Extension Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Navminitools') && !class_exists('Plethora_Module_Navminitools_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/navminitools/module-navminitools.php
   */
  class Plethora_Module_Navminitools_Ext extends Plethora_Module_Navminitools { 

		/** 
		* Returns single options configuration for 'Theme Options > Content > Single { Post Type }' tab 
		* and the single post edit metabox. 
		*/
		public function theme_options_config() {

			$theme_options_config = array(
				array( 
					'id'                    => 'tools-status', 
					'theme_options'         => true,
					'theme_options_default' => array(
						'custom'      => false,
						'woo_cart'    => false,
						'woo_account' => false
					), 
				),
				array( 
					'id'                    => 'switch-to-mobile', 
					'theme_options'         => true,
					'theme_options_default' => 0, 
				),
				array( 
					'id'                    => 'custom-section', 
					'theme_options'         => true, 
					'theme_options_default' => false,
				),
				array( 
					'id'                    => 'custom-markup', 
					'theme_options'         => true, 
					'theme_options_default' => '',
				),
				// Woo Fields Start
				array( 
					'id'                    => 'cart-section-start', 
					'theme_options'         => true, 
					'theme_options_default' => NULL,
					),
				array( 
					'id'                    => 'cart-icon', 
					'theme_options'         => true, 
					'theme_options_default' => 'fa fa-shopping-cart',
					),
				array( 
					'id'                    => 'cart-icon-size', 
					'theme_options'         => true, 
					'theme_options_default' => 16,
					),
				array( 
					'id'                    => 'cart-title', 
					'theme_options'         => true, 
					'theme_options_default' => esc_html__( 'Visit cart', 'plethora-framework' ),
					),
				array( 
					'id'                    => 'cart-count', 
					'theme_options'         => true, 
					'theme_options_default' => true, 
					),
				array( 
					'id'                    => 'cart-count-color', 
					'theme_options'         => true, 
					'theme_options_default' => '#ffffff', 
					),
				array( 
					'id'                    => 'cart-count-bgcolor', 
					'theme_options'         => true, 
					'theme_options_default' => '#2ecc71', 
					),
				array( 
					'id'                    => 'cart-total', 
					'theme_options'         => true, 
					'theme_options_default' => true, 
					),
				array( 
					'id'                    => 'cart-section-end', 
					'theme_options'         => true, 
					'theme_options_default' => NULL,
					),
				array( 
					'id'                    => 'account-section-start', 
					'theme_options'         => true, 
					'theme_options_default' => NULL,
					),
				array( 
					'id'                    => 'account', 
					'theme_options'         => true, 
					'theme_options_default' => true,
					),
				array( 
					'id'                    => 'account-icon', 
					'theme_options'         => true, 
					'theme_options_default' => 'fa fa-user',
					),
				array( 
					'id'                    => 'account-icon-size', 
					'theme_options'         => true, 
					'theme_options_default' => 16,
					),
				array( 
					'id'                    => 'account-title-visit', 
					'theme_options'         => true, 
					'theme_options_default' => esc_html__( 'My Account', 'plethora-framework' ),
					),
				array( 
					'id'                    => 'account-title-login', 
					'theme_options'         => true, 
					'theme_options_default' => esc_html__( 'Login / Register', 'plethora-framework' ),
					),
				array( 
					'id'                    => 'account-section-end', 
					'theme_options'         => true, 
					'theme_options_default' => NULL,
					),
				// Woo Fields End
		  	);

			return $theme_options_config;
		}
  	}
}