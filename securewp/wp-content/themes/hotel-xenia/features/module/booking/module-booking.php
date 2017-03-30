<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2017

Breadcrumb Module Extension Class
Booking Management Module Extension class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Booking') && !class_exists('Plethora_Module_Booking_Ext') ) {

	/**
	* Extend base class
	* Base class file: /plugins/plethora-framework/features/module/booking/module-booking.php
	*/
	class Plethora_Module_Booking_Ext extends Plethora_Module_Booking { 

		/** 
		* Returns single options configuration for 'Theme Options > Content > Single { Post Type }' tab 
		* and the single post edit metabox. 
		*/
		public function single_options_config( $post_type ) {

			$single_options_config = array(
				array( 
					'id'                    => 'booking', 
					'theme_options'         => true, 
					'theme_options_default' => 'simple',
					'metabox'               => true,
					'metabox_default'       => NULL
				),
				array( 
					'id'                    => 'booking-single', 
					'theme_options'         => true, 
					'theme_options_default' => 'simple',
					'metabox'               => true,
					'metabox_default'       => NULL
				),
				array( 
					'id'                    => 'targetprice', 
					'theme_options'         => true, 
					'theme_options_default' => true,
					'metabox'               => true,
					'metabox_default'       => NULL
				),
				array( 
					'id'                    => 'targetprice-text', 
					'theme_options'         => false, 
					'theme_options_default' => NULL,
					'metabox'               => true,
					'metabox_default'       => ''
				),
				array( 
					'id'                    => 'targetprice-text-before', 
					'theme_options'         => true, 
					'theme_options_default' => esc_html__( 'Starting Price From', 'hotel-xenia'),
					'metabox'               => true,
					'metabox_default'       => NULL
				),
				array( 
					'id'                    => 'targetprice-text-after', 
					'theme_options'         => true, 
					'theme_options_default' => esc_html__( '/ Day', 'hotel-xenia'),
					'metabox'               => true,
					'metabox_default'       => NULL
				),
				array( 
					'id'                    => 'pricelist', 
					'theme_options'         => true, 
					'theme_options_default' => false,
					'metabox'               => true,
					'metabox_default'       => NULL
				),
				array( 
					'id'                    => 'pricelist-text', 
					'theme_options'         => false, 
					'theme_options_default' => NULL,
					'metabox'               => true,
					'metabox_default'       => ''
				),
			);

			return $single_options_config;
		}
	}
}