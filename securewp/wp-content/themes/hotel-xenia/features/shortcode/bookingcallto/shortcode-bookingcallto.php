<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             	   (c) 2017

Call To Booking Shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Shortcode_Bookingcallto') && !class_exists('Plethora_Shortcode_Bookingcallto_Ext') ) {

	/**
	* Extend base class
	* Base class file: /plugins/plethora-framework/features/shortcode/bookingcallto/shortcode-bookingcallto.php
	*/
	class Plethora_Shortcode_Bookingcallto_Ext extends Plethora_Shortcode_Bookingcallto { 

		/** 
		* Configure parameters displayed
		* Will be displayed all items from params_index() with identical 'id'
		* This method should be used for extension class overrides
		*
		* @return array
		*/
		public function params_config() {

			$params_config = array(
			#GENERAL TAB
				array( 
				  'id'         => 'form_action', 
				  'default'    => '#',
				  ),
				array( 
				  'id'         => 'form_method', 
				  'default'    => 'get',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'form_target', 
				  'default'    => 'normal',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'date_arrival', 
				  'default'    => 'true',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'date_departure', 
				  'default'    => 'true',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'adults', 
				  'default'    => 'true',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'children', 
				  'default'    => 'true',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'rooms', 
				  'default'    => 'false',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'services', 
				  'default'    => 'false',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'hidden_1', 
				  'default'    => 'false',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'hidden_2', 
				  'default'    => 'false',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'date_format', 
				  'default'    => 'yy-mm-dd',
				  'field_size' => '6',
				  ),
				array( 
				  'id'         => 'el_class', 
				  'default'    => '',
				  'field_size' => '6',
				  ),
			#FIELDS CONFIG TAB
				array( 
				  'id'         => 'date_arrival_name', 
				  'default'    => 'date_arrival',
				  ),
				array( 
				  'id'         => 'date_arrival_title', 
				  'default'    => esc_html__('Check in', 'hotel-xenia'),
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'date_arrival_placeholder', 
				  'default'    => esc_html__('Select Arrival Date', 'hotel-xenia'),
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'date_arrival_colsize', 
				  'default'    => 'col-md-3',
				  'field_size' => '2',
				  ),
				array( 
				  'id'         => 'date_departure_name', 
				  'default'    => 'date_departure',
				  'field_size' => '',
				  ),
				array( 
				  'id'         => 'date_departure_title', 
				  'default'    => esc_html__('Check out', 'hotel-xenia'),
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'date_departure_placeholder', 
				  'default'    => esc_html__('Select Departure Date', 'hotel-xenia'),
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'date_departure_colsize', 
				  'default'    => 'col-md-3',
				  'field_size' => '2',
				  ),
				array( 
				  'id'         => 'adults_name', 
				  'default'    => 'adults',
				  ),
				array( 
				  'id'         => 'adults_title', 
				  'default'    => esc_html__('Adults', 'hotel-xenia'),
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'adults_max', 
				  'default'    => '4',
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'adults_colsize', 
				  'default'    => 'col-md-3',
				  'field_size' => '2',
				  ),
				array( 
				  'id'         => 'children_name', 
				  'default'    => 'children',
				  ),
				array( 
				  'id'         => 'children_title', 
				  'default'    => esc_html__('Children', 'hotel-xenia'),
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'children_max', 
				  'default'    => '4',
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'children_colsize', 
				  'default'    => 'col-md-3',
				  'field_size' => '2',
				  ),
				array( 
				  'id'         => 'rooms_name', 
				  'default'    => 'selected_room',
				  ),
				array( 
				  'id'         => 'rooms_title', 
				  'default'    => esc_html__('Room Type', 'hotel-xenia'),
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'rooms_multiple', 
				  'default'    => 0,
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'rooms_colsize', 
				  'default'    => 'col-md-3',
				  'field_size' => '2',
				  ),
				array( 
				  'id'         => 'services_name', 
				  'default'    => 'selected_service',
				  ),
				array( 
				  'id'         => 'services_title', 
				  'default'    => esc_html__('Interested In', 'hotel-xenia'),
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'services_multiple', 
				  'default'    => 0,
				  'field_size' => '5',
				  ),
				array( 
				  'id'         => 'services_colsize', 
				  'default'    => 'col-md-3',
				  'field_size' => '2',
				  ),
				array( 
				  'id'         => 'hidden_1_name', 
				  'default'    => 'hidden_field_1',
				  ),
				array( 
				  'id'         => 'hidden_1_value', 
				  'default'    => 'hidden_value_1',
				  ),
				array( 
				  'id'         => 'hidden_2_name', 
				  'default'    => 'hidden_field_2',
				  ),
				array( 
				  'id'         => 'hidden_2_value', 
				  'default'    => 'hidden_value_2',
				  ),
				array( 
				  'id'         => 'submit_title', 
				  'default'    => esc_html__('Book Now', 'hotel-xenia'),
				  'field_size' => '10',
				  ),
				array( 
				  'id'         => 'submit_colsize', 
				  'default'    => 'col-md-3',
				  'field_size' => '2',
				  ),
				array( 
				  'id'         => 'submit_style', 
				  'default'    => 'btn',
				  'field_size' => '4',
				  ),
				array( 
				  'id'         => 'submit_size', 
				  'default'    => '',
				  'field_size' => '4',
				  ),
				array( 
				  'id'         => 'submit_colorset', 
				  'default'    => 'btn-default',
				  'field_size' => '4',
				  ),
				array( 
				  'id'         => 'submit_class', 
				  'default'    => '',
				  ),
			#DESIGN OPTIONS TAB
				array( 
				  'id'         => 'css', 
				  'default'    => '',
				  ),
			#HELP TAB
				array( 
				  'id'         => 'help', 
				  ),
			);

		return $params_config;
	}
  }
}