<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2017

File Description: Call To Booking shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Bookingcallto') ):

/**
* @package Plethora Framework
*/

class Plethora_Shortcode_Bookingcallto extends Plethora_Shortcode { 

	public static $feature_title         = "Call To Booking Form Shortcode";  // Feature display title  (string)
	public static $feature_description   = "";                  // Feature display description (string)
	public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
	public static $theme_option_default  = true;                // Default activation option status ( boolean )
	public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
	public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
	public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
	public static $assets                = array(
												array( 'script' => array( 'jquery-ui-datepicker' ) ), // had to use plethora version, due to VC conflicts
											 );
	public $wp_slug                      =  'bookingcallto';

	public $default_param_values;
   
	public function __construct() {

		// Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
		$map = array( 
					'base'        => SHORTCODES_PREFIX . $this->wp_slug,
					'name'        => esc_html__("Call To Booking", 'plethora-framework'), 
					'description' => esc_html__('Get and transfer basic info to booking form page', 'plethora-framework'), 
					'class'       => '', 
					'weight'      => 1, 
					'icon'        => $this->vc_icon(), 
					// 'custom_markup' => $this->vc_custom_markup( 'Button' ), 
					'params'      => $this->params(), 
					);
		// Add the shortcode
		$this->add( $map );

	}

	/** 
	* Returns shortcode parameters INDEX for VC panel
	* @return array
	*/
	public function params_index() {

	#GENERAL TAB
		$params_index['form_method'] = array( 
			'param_name'  => 'form_method',
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Booking Request Form Method', 'plethora-framework' ),
			'description' => esc_html__( 'If you want to point this to a Contact Form 7 booking form page in this website, then you must use GET method.', 'plethora-framework' ),
			'value'       => array(
				esc_html__('GET method', 'plethora-framework')  => 'get',
				esc_html__('POST method', 'plethora-framework') => 'post',
			),
		);
		$params_index['form_action'] = array( 
			'param_name'  => 'form_action',
			'type'        => 'vc_link',
			'heading'     => esc_html__('Booking Request Form Page', 'plethora-framework'),
			'description' => esc_html__('This must be the link of the page where the Contact Form 7 Booking Request form is displayed.', 'plethora-framework'),
		);
		$params_index['form_target'] = array( 
			  'param_name'  => 'form_target',
			  'type'        => 'dropdown',
			  'heading'     => esc_html__('Submit Behavior', 'plethora-framework'),
			  'value'            => array( 
									  esc_html__('Pass values to selected booking form page', 'plethora-framework')                 => '_self',
									  esc_html__('Pass values to selected booking form page, in new tab', 'plethora-framework')     => '_blank',
									  // esc_html__('Link to %1$s page, in ajax window', 'plethora-framework') => 'ajax',
									),
		);
		$params_index['date_arrival'] = array( 
			'param_name' => 'date_arrival',
			'type'       => 'checkbox',
			'heading'    => esc_html__('Start Date Field', 'plethora-framework'),
		);
		$params_index['date_departure'] = array( 
			'param_name' => 'date_departure',
			'type'       => 'checkbox',
			'heading'    => esc_html__('End Date Field', 'plethora-framework'),
		);
		$params_index['date_format'] = array( 
			'param_name'  => 'date_format',
			'type'        => 'textfield',
			'heading'     => esc_html__('Date Format', 'plethora-framework'),
			'description' => sprintf( esc_html__('%sDay, month, year format for date fields. Default: yy-mm-dd | %sOther date format examples%s%s', 'plethora-framework'), '<small>', '<a href="https://jqueryui.com/resources/demos/datepicker/date-formats.html" target="_blank">', '</a>', '</small>' ),
		);
		$params_index['adults'] = array( 
			  'param_name' => 'adults',
			  'type'       => 'checkbox',
			  'heading'    => esc_html__('Adults Field', 'plethora-framework'),
		);
		$params_index['children'] = array( 
			  'param_name' => 'children',
			  'type'       => 'checkbox',
			  'heading'    => esc_html__('Children Field', 'plethora-framework'),
		);
		$params_index['rooms'] = array( 
			  'param_name' => 'rooms',
			  'type'       => 'checkbox',
			  'heading'    => esc_html__('Rooms Selection Field', 'plethora-framework'),
		);
		$params_index['services'] = array( 
			'param_name'  => 'services',
			'type'        => 'checkbox',
			'heading'     => esc_html__('Services Selection Field', 'plethora-framework'),
		);
		$params_index['hidden_1'] = array( 
			'param_name'  => 'hidden_1',
			'type'        => 'checkbox',
			'heading'     => esc_html__('Hidden Field 1', 'plethora-framework'),
			'description' => esc_html__('Enable this, if you need an additional hidden field to use this form with external booking systems', 'plethora-framework'),
		);
		$params_index['hidden_2'] = array( 
			'param_name'  => 'hidden_2',
			'type'        => 'checkbox',
			'heading'     => esc_html__('Hidden Field 2', 'plethora-framework'),
			'description' => esc_html__('Enable this, if you need an additional hidden field to use this form with external booking systems', 'plethora-framework'),
		);
		$params_index['el_class'] = array( 
			  'param_name'  => 'el_class',
			  'type'        => 'textfield',
			  'heading'     => esc_html__('Extra Class', 'plethora-framework'),
			  'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
		);

	#FIELDS CONFIG TAB

		$params_index['date_arrival_name'] = array( 
			  'param_name'  => 'date_arrival_name',
			  'type'        => 'textfield',                                        
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Arrival Date Field Name', 'plethora-framework'),
			  'description' => sprintf( esc_html__('Must match the %1$sarrival date field name%2$s attribute on the targeted %1$sCF7 booking form%2$s.%3$sCF7 form field sample configuration: %1$s[date date_arrival default:get]%2$s', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
			  'dependency'  => array( 
								  'element' => 'date_arrival', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['date_arrival_title'] = array( 
			  'param_name'  => 'date_arrival_title',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Arrival Date Title', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'date_arrival', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['date_arrival_placeholder'] = array( 
			  'param_name'  => 'date_arrival_placeholder',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Arrival Date Place/der', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'date_arrival', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['date_arrival_colsize'] = array( 
			  'param_name' => 'date_arrival_colsize',
			  'type'       => 'dropdown',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'    => esc_html__('Size', 'plethora-framework'),
			  'value'    => $this->get_col_options(),
			  'dependency' => array( 
								  'element' => 'date_arrival', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['date_departure_name'] = array( 
			  'param_name'  => 'date_departure_name',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Departure Date Field Name', 'plethora-framework'),
			  'description' => sprintf( esc_html__('Must match the %1$sdeparture date field name%2$s attribute on the targeted %1$sCF7 booking form%2$s.%3$sCF7 form field sample configuration: %1$s[date date_departure default:get]%2$s', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
			  'dependency'  => array( 
								  'element' => 'date_departure', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['date_departure_title'] = array( 
			  'param_name'  => 'date_departure_title',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Dept. Date Title', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'date_departure', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['date_departure_placeholder'] = array( 
			  'param_name'  => 'date_departure_placeholder',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Dept. Date Placeholder', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'date_departure', 
								  'value'   => array( 'true' ),  
										)
		);

		$params_index['date_departure_colsize'] = array( 
			  'param_name' => 'date_departure_colsize',
			  'type'       => 'dropdown',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'    => esc_html__('Size', 'plethora-framework'),
			  'value'    => $this->get_col_options(),
			  'dependency' => array( 
								  'element' => 'date_departure', 
								  'value'   => array( 'true' ),  
										)
		);

		$params_index['adults_name'] = array( 
			  'param_name'  => 'adults_name',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Adults Field Name', 'plethora-framework'),
			  'description' => sprintf( esc_html__('Must match the %1$sadults field name%2$s attribute on the targeted %1$sCF7 booking form%2$s.%3$sCF7 form field sample configuration: %1$s[select adults default:get "1" "2" "3" "4"]%2$s', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
			  'dependency'  => array( 
								  'element' => 'adults', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['adults_title'] = array( 
			  'param_name'  => 'adults_title',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Adults Title', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'adults', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['adults_max'] = array( 
			  'param_name'  => 'adults_max',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Max Adults', 'plethora-framework'),
			  'description' => esc_html__('Only numbers', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'adults', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['adults_colsize'] = array( 
			  'param_name' => 'adults_colsize',
			  'type'       => 'dropdown',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'    => esc_html__('Size', 'plethora-framework'),
			  'value'    => $this->get_col_options(),
			  'dependency' => array( 
								  'element' => 'adults', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['children_name'] = array( 
			  'param_name'  => 'children_name',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Children Field Name', 'plethora-framework'),
			  'description' => sprintf( esc_html__('Must match the %1$schildren field name%2$s attribute on the targeted %1$sCF7 booking form%2$s.%3$sCF7 form field sample configuration: %1$s[select children default:get "1" "2" "3" "4"]%2$s', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
			  'dependency'  => array( 
								  'element' => 'children', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['children_title'] = array( 
			  'param_name'  => 'children_title',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Children Title', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'children', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['children_max'] = array( 
			  'param_name'  => 'children_max',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Max Children', 'plethora-framework'),
			  'description' => esc_html__('Only numbers', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'children', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['children_colsize'] = array( 
			  'param_name' => 'children_colsize',
			  'type'       => 'dropdown',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'    => esc_html__('Size', 'plethora-framework'),
			  'value'    => $this->get_col_options(),
			  'dependency' => array( 
								  'element' => 'children', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['rooms_name'] = array( 
			  'param_name'  => 'rooms_name',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Rooms Field Name', 'plethora-framework'),
			  'description' => sprintf( esc_html__('Must match the %1$sroom field name%2$s attribute on the targeted %1$sCF7 booking form%2$s.%3$sCF7 form field sample configuration: %1$s[select_posts selected_room post_type:room default:get]%2$s', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
			  'dependency'  => array( 
								  'element' => 'rooms', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['rooms_title'] = array( 
			  'param_name'  => 'rooms_title',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Rooms Field Title', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'rooms', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['rooms_multiple'] = array( 
			  'param_name' => 'rooms_multiple',
			  'type'       => 'checkbox',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'    => esc_html__('Multi Rooms Selection', 'plethora-framework'),
			  'dependency' => array( 
								  'element' => 'rooms', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['rooms_colsize'] = array( 
			  'param_name' => 'rooms_colsize',
			  'type'       => 'dropdown',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'    => esc_html__('Size', 'plethora-framework'),
			  'value'    => $this->get_col_options(),
			  'dependency' => array( 
								  'element' => 'rooms', 
								  'value'   => array( 'true' ),  
										)
		);

		$params_index['services_name'] = array( 
			  'param_name'  => 'services_name',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Services Field Name', 'plethora-framework'),
			  'description' => sprintf( esc_html__('Must match the %1$sservices field name%2$s attribute on the targeted %1$sCF7 booking form%2$s.%3$sCF7 form field sample configuration: %1$s[select_categories selected_service post_type:service default:get]%2$s', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
			  'dependency'  => array( 
								  'element' => 'services', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['services_title'] = array( 
			  'param_name'  => 'services_title',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Services Field Title', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
			  'dependency'  => array( 
								  'element' => 'services', 
								  'value'   => array( 'true' ),  
										)
		);

		$params_index['services_multiple'] = array( 
			  'param_name' => 'services_multiple',
			  'type'       => 'checkbox',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'    => esc_html__('Multi Services Selection', 'plethora-framework'),
			  'dependency' => array( 
								  'element' => 'services', 
								  'value'   => array( 'true' ),  
										)
		);
		$params_index['services_colsize'] = array( 
			'param_name' => 'services_colsize',
			'type'       => 'dropdown',
			'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			'heading'    => esc_html__('Size', 'plethora-framework'),
			'value'      => $this->get_col_options(),
			'dependency' => array( 
				'element' => 'services', 
				'value'   => array( 'true' ),  
			)
		);

		$params_index['hidden_1_name'] = array( 
			'param_name'  => 'hidden_1_name',
			'type'        => 'textfield',
			'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			'heading'     => esc_html__('Hidden Field 1 Name', 'plethora-framework'),
			'description' => sprintf( esc_html__('Apparently you need to use this hidden field for an external booking service. This field name must match one the %1$shidden field name%2$s attribute on the targeted booking form. No HTML here.', 'plethora-framework'), '<strong>', '</strong>' ),
			'dependency'  => array( 
				'element' => 'hidden_1', 
				'value'   => array( 'true' ),  
			)
		);
		$params_index['hidden_1_value'] = array( 
			'param_name'  => 'hidden_1_value',
			'type'        => 'textfield',
			'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			'heading'     => esc_html__('Hidden Field 1 Value', 'plethora-framework'),
			'description' => esc_html__('No HTML', 'plethora-framework'),
			'dependency'  => array( 
				'element' => 'hidden_1', 
				'value'   => array( 'true' ),  
			)
		);

		$params_index['hidden_2_name'] = array( 
			'param_name'  => 'hidden_2_name',
			'type'        => 'textfield',
			'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			'heading'     => esc_html__('Hidden Field 2 Name', 'plethora-framework'),
			'description' => sprintf( esc_html__('Apparently you need to use this hidden field for an external booking service. This field name must match one the %1$shidden field name%2$s attribute on the targeted booking form. No HTML here.', 'plethora-framework'), '<strong>', '</strong>' ),
			'dependency'  => array( 
				'element' => 'hidden_2', 
				'value'   => array( 'true' ),  
			)
		);
		$params_index['hidden_2_value'] = array( 
			'param_name'  => 'hidden_2_value',
			'type'        => 'textfield',
			'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			'heading'     => esc_html__('Hidden Field 2 Value', 'plethora-framework'),
			'description' => esc_html__('No HTML', 'plethora-framework'),
			'dependency'  => array( 
				'element' => 'hidden_2', 
				'value'   => array( 'true' ),  
			)
		);

		$params_index['submit_title'] = array( 
			  'param_name'  => 'submit_title',
			  'type'        => 'textfield',
			  'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'     => esc_html__('Submit Field Title', 'plethora-framework'),
			  'description' => esc_html__('No HTML', 'plethora-framework'),
		);
		$params_index['submit_colsize'] = array( 
			  'param_name' => 'submit_colsize',
			  'type'       => 'dropdown',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  'heading'    => esc_html__('Size', 'plethora-framework'),
			  'value'      => $this->get_col_options(),
		);

		$params_index['submit_style'] = array( 
			  "param_name" => "submit_style",                                  
			  "type"       => "dropdown",                                        
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  "class"      => "vc_hidden",                                         
			  "heading"    => esc_html__("Submit Style", 'plethora-framework'),      
			  "value"      => array(
								esc_html__( 'Default', 'plethora-framework' )     => 'btn',
								esc_html__( 'Inverted', 'plethora-framework' )    => 'btn btn-inv',
								esc_html__( 'Link Button', 'plethora-framework' ) => 'btn-link',
								),
		);
		$params_index['submit_size'] = array( 
			  "param_name" => "submit_size",                                  
			  "type"       => "dropdown",                                        
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  "heading"    => esc_html__("Submit size", 'plethora-framework'),      
			  "value"      => array(
								'Default'     =>'',
								'Large'       =>'btn-lg',
								'Small'       =>'btn-sm',
								'Extra Small' =>'btn-xs'
							),
		);

		$params_index['submit_colorset'] = array( 
			  "param_name" => "submit_colorset",                                  
			  "type"       => "dropdown",                                        
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  "holder"     => "",                                               
			  "class"      => "vc_hidden",                                         
			  "heading"    => esc_html__("Submit Color", 'plethora-framework'),      
			  "value"      => array(
								esc_html__( 'Default', 'plethora-framework' )   => 'btn-default',
								esc_html__( 'Primary', 'plethora-framework' )   => 'btn-primary',
								esc_html__( 'Secondary', 'plethora-framework' ) => 'btn-secondary',
								esc_html__( 'Dark', 'plethora-framework' )      => 'btn-dark',
								esc_html__( 'Light', 'plethora-framework' )     => 'btn-light',
								esc_html__( 'White', 'plethora-framework' )     => 'btn-white',
								esc_html__( 'Black', 'plethora-framework' )     => 'btn-black',
								esc_html__( 'Success', 'plethora-framework' )   => 'btn-success',
								esc_html__( 'Info', 'plethora-framework' )      => 'btn-info',
								esc_html__( 'Warning', 'plethora-framework' )   => 'btn-warning',
								esc_html__( 'Danger', 'plethora-framework' )    => 'btn-danger',
							),
		);

		$params_index['submit_class'] = array( 
			  'param_name' => 'submit_class',
			  'type'       => 'textfield',
			  'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
			  "heading"    => esc_html__("Submit Extra Class", 'plethora-framework'),      
			  'description' => esc_html__('Style submit button differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
		);

	#DESIGN OPTIONS TAB
		$params_index['css'] = array( 
			  "param_name"    => "css",
			  "type"          => "css_editor",
			  'group'         => esc_html__( 'Design options', 'plethora-framework' ),
			  "heading"       => esc_html__('CSS box', 'plethora-framework'),
		);
	#HELP TAB
		$intro_text = '<p>'. esc_html__( 'Create a form that collects some basic booking information and passing them on the final booking form. On the final booking form, the user will be able to see the matching fields already filled and continue with the form submission.', 'plethora-theme' ) .'</p>';
		$intro_text .= '<p>'. esc_html__( 'Pay attention to the following', 'plethora-theme' ) .'</p>';
		$intro_text .= '<ol style="font-size:12px;">';
		$intro_text .= sprintf( '<li style="margin-left:15px">'. esc_html__( 'All the common fields of this form and the final booking CF7 form %1$smust have the same field name%2$s. You have the possibility to place your own field names on both forms.', 'plethora-theme' ) .'</li>', '<strong>', '</strong>' );
		$intro_text .= sprintf( '<li style="margin-left:15px">'. esc_html__( 'Avoid giving generic or WP reserved names to your fields. In example, use %1$sxenia_room%2$s instead of %1$sroom%2$s', 'plethora-theme' ) .'</i>', '<strong>"', '"</strong>' );
		$intro_text .= sprintf( '<li style="margin-left:15px">'. esc_html__( 'You must NOT USE more than two Call To Booking shortcodes per page', 'plethora-theme' ) .'</i>', '<strong>"', '"</strong>' );
		$intro_text .= '</ol>';
		$params_index['help'] = array( 
			  'param_name'  => 'help',
			  'type'        => 'custom_markup',
			  'group'      => esc_html__('Help', 'plethora-framework'),                                       
			  'default'    => '<div class="vc_col-xs-12 vc_shortcode-param vc_column wpb_element_label"><div class="edit_form_line vc_clearfix" style="text-transform:initial; font-weight:normal;">'. trim( $intro_text ) .'</div></div>',
		);


	  return $params_index;
	}

	public function get_col_options() {

		return array(
			'1/12'  => 'col-md-1 col-sm-2',
			'2/12'  => 'col-md-2 col-sm-4',
			'3/12'  => 'col-md-3 col-sm-6',
			'4/12'  => 'col-sm-4',
			'5/12'  => 'col-sm-5',
			'6/12'  => 'col-sm-6',
			'7/12'  => 'col-sm-7',
			'8/12'  => 'col-sm-8',
			'9/12'  => 'col-sm-9',
			'10/12' => 'col-sm-10',
			'11/12' => 'col-sm-11',
			'12/12' => 'col-sm-12',
		);
	}

	/** 
	* Returns shortcode content OR content template
	*
	* @return array
	* @since 1.0
	*
	*/
	public function content( $atts, $content = null ) {

		// Extract user input
		extract( shortcode_atts( $this->get_default_param_values(), $atts ) );

		// if dates are on, we need a js init
		if ( $date_arrival || $date_departure ) {

			$script_args = array(
				'date_format'   => empty( $date_format ) ? 'yy-mm-dd' : $date_format,
			);
			$enqueue_args = array(

				'handle' => 'jquery-ui-datepicker',
				'script' => self::get_datepicker_script( $script_args ),
				'multiple' => false,
			);
			Plethora_Theme::enqueue_init_script( $enqueue_args );           
		}     

		// Form submission opts 
		$form_action     =  self::vc_build_link($form_action);
		$form_action_url = !empty( $form_action['url'] ) ? $form_action['url'] : '#';
		
		$fields = array();
		// Fields: arrival date 
		if ( $date_arrival ) {

			$fields[] = self::get_field_config( $date_arrival_name, $date_arrival_title, '', $date_arrival_colsize, 'date_check_in',  $date_arrival_placeholder ); 
		}
		// Fields: departure date 
		if ( $date_departure ) { 

			$fields[] = self::get_field_config( $date_departure_name, $date_departure_title, '', $date_departure_colsize, 'date_check_out', $date_departure_placeholder ); 
		}

		// Fields: adults 
		if ( $adults ) {

			$fields[] = self::get_field_config( $adults_name, $adults_title, self::get_field_countval_options( $adults_max, 2 ), $adults_colsize, 'adults' ); 
		}

		// Fields: children 
		if ( $children ) {

			$fields[] = self::get_field_config( $children_name, $children_title, self::get_field_countval_options( $children_max, 0, 0 ), $children_colsize, 'children' ); 
		}

		// Fields: rooms 
		if ( $rooms ) {

			$fields[] = self::get_field_config( $rooms_name, $rooms_title, self::get_field_post_options( 'room' ), $rooms_colsize, 'selected_room', '', $rooms_multiple ); 
		}

		// Fields: services 
		if ( $services ) {

			$fields[] = self::get_field_config( $services_name, $services_title, self::get_field_post_options( 'service', true ), $services_colsize, 'selected_service', '', $services_multiple ); 
		}

		// Fields: hidden_1
		if ( $hidden_1 ) {

			$fields[] = self::get_field_config( $hidden_1_name, '', $hidden_1_value, '', '', '', false, true ); 
		}

		// Fields: hidden_2
		if ( $hidden_2 ) {

			$fields[] = self::get_field_config( $hidden_2_name, '', $hidden_2_value, '', '', '', false, true ); 
		}

		// Place all values in 'shortcode_atts' variable
		$css = esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) );
		$shortcode_atts = array (
			'form_action_url' => esc_url( $form_action_url ),  
			'form_method'     => esc_attr( $form_method ),  
			'form_target'     => esc_attr( $form_target ),  
			'submit_title'    => esc_attr( $submit_title ),  
			'submit_colsize'  => esc_attr( $submit_colsize ),  
			'submit_style'    => esc_attr( $submit_style ),  
			'submit_size'     => esc_attr( $submit_size ),  
			'submit_colorset' => esc_attr( $submit_colorset ),  
			'submit_class'    => esc_attr( $submit_class ),  
			'fields'          => $fields,
			'id'              => '',
			'el_class'        => esc_attr( $el_class ),
			'css'             => esc_attr( $css ),
		);

		$sc_container = '<div class="wpb_content_element call_to_booking_sc '. esc_attr( $el_class ) .' '. esc_attr( $css ) .'">';
		$sc_container .= Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );
		$sc_container .= '</div>';
		return $sc_container;

	}

	public static function get_field_config( $name, $title = '', $val = '', $colsize = '', $id = '', $placeholder = '', $multiple = false, $hidden = false  ) {

		return array(
			'field_is_select'   => is_array( $val ) && ! $hidden ? true : false,
			'field_is_input'    => is_array( $val ) && ! $hidden ? false : true,
			'field_not_hidden'   => $hidden ? false : true,
			'field_input_type'  => $hidden ? 'hidden' : 'text',
			'field_colsize'     => ! $hidden ? esc_attr( $colsize ) : '',
			'field_label'       => ! $hidden ? esc_html( $title ) : '',
			'field_name'        => esc_attr( $name ),
			'field_value'       => ! is_array( $val ) ? esc_attr( $val ) : '',
			'field_options'     => is_array( $val ) ? $val : array(),
			'field_id'          => esc_attr( $id ),
			'field_placeholder' => esc_attr( $placeholder ),
			'field_multiple'    => $multiple ? ' multiple' : '',
		);

	}

	public static function get_field_countval_options( $max, $start_from = 1, $opt_selected = 1 ) {

		$options = array();
		$max  = intval( $max );
		for ( $i = $start_from; $i <= $max; $i++ ) { 
			$selected  = ( $opt_selected == $i ) ? ' selected' : ''; 
			$options[] = array( 'opt_val' => esc_attr( $i ), 'opt_title' => $i, 'opt_selected' => $selected ); 
		} 

		return $options;
	}

	public static function get_field_post_options( $post_type, $include_empty = false ) {

		$options = array();
		if ( $include_empty ) {
			$options[] = array( 'opt_val' => '', 'opt_title' => '-', 'opt_selected' => '' );
		}
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'suppress_filters' => false,
		);
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {

			$selected  = ( Plethora_Theme::get_this_page_id() ) == $post->ID ? ' selected' : '';
			$options[] = array( 'opt_val' => esc_attr( $post->post_title ), 'opt_title' => $post->post_title, 'opt_selected' => $selected ); 
		}
		wp_reset_postdata();

		return $options;
	}

	public static function get_datepicker_script( $script_args ) {

		$output = '
<script type="text/javascript">
jQuery(function($) {

	"use strict";

	var dateFormat = "'. esc_js( $script_args['date_format'] ).'",
	  from = $( "#date_check_in" ).datepicker({
			dateFormat: dateFormat,
			defaultDate: "+1",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			constrainInput: true,
			minDate: "0"
		})
		.on( "change", function() {
		  to.datepicker( "option", "minDate", getDate( this ) );
		}),
	  to = $( "#date_check_out" ).datepicker({
			dateFormat: dateFormat,
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			constrainInput: true,
			minDate: "+1"
	  })
	  .on( "change", function() {
			from.datepicker( "option", "maxDate", getDate( this ) );
	  });
 
	function getDate( element ) {
	  var date;
	  try {
		date = $.datepicker.parseDate( dateFormat, element.value );
	  } catch( error ) {
		date = null;
	  }
 
	  return date;
	}


});
</script>';
		return $output;

	}
}
	
endif;