<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Class Description: Style related helper methods. 
Used in-house for easier style options management

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Style') ) {

	class Plethora_Module_Style {

		public static $feature_title        = "Styles Manager";						// Feature display title  (string)
		public static $feature_description  = "Style related helper methods";		// Feature display description (string)
		public static $theme_option_control = false;								// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default	= true;									// Default activation option status ( boolean )
		public static $theme_option_requires= array();								// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct	= false;								// Dynamic class construction ? ( boolean )
		public static $dynamic_method		= false;								// Additional method invocation ( string/boolean | method name or false )

		/**
		* Setup all style related attrs on construction
		*/
		protected function __construct(){

		}

		/**
		* PUBLIC | Returns various info regarding styling form options (i.e. color sets, strechy wrapper rations, etc )
		*/
		public static function get_options_array( $args ) {

			$default_args = array( 
					'type'              => '',		// style type ( 'colorsets', 'stretchy_ratios' )
					'use_in'            => 'redux',	// 'redux', 'vc' // different option outputs depending on the form 
					'title_alt'         => false,		// Use alternative title for option title
					'prepend_default'   => false,		// Prepend 'Default' option ( title: 'Default', value: '' )
					'append_default'    => false,		// Append 'Default' option ( title: 'Default', value: '' )
					'default_title'     => esc_html__('Default', 'plethora-framework' ),	// Set the default option title ( ie. 'None', 'No', 'Default')
					'prepend_options'   => array(),	// Prepend additonal options to output ( strictly array, in value=>title pairs )
					'append_options'    => array(),	// Prepend additonal options to output ( strictly array, in value=>title pairs )
					'prefix_all_values' => array(),	// Append additonal strings to value output ( can be a string too )
					'suffix_all_values' => array(),	// Append additonal strings to value output ( can be a string too )
					'only_values'       => false,		// Use true, if you want a simple array with values ( i.e. for dependency array in VC )
			);

			// merge and check
			$args   = wp_parse_args( $args, $default_args);  

			$prefixToVal = $args['prefix_all_values'];
			$prefixToVal = is_array( $prefixToVal ) ? $prefixToVal : array( $prefixToVal );
			$suffixToVal = $args['suffix_all_values'];
			$suffixToVal = is_array( $suffixToVal ) ? $suffixToVal : array( $suffixToVal );

			if ( empty( $args['type'] ) ) { return array(); }

			$return    = array();

			// PREPEND 'default option'
			if ( $args['prepend_default'] ) {  $return[''] = $args['default_title'];  }

			// PREPEND USER GIVEN OPTIONS
			$prepend_options = $args['prepend_options'];	// CACHE
			if ( is_array( $prepend_options ) && !empty ( $prepend_options )) {
				foreach ( $prepend_options as $useroption_value => $useroption_title ) {

					$return[$useroption_value] = $useroption_title;
				}
			}

			// DEFAULT STYLE OPTIONS
			$options = self::get_index_values( $args['type'] );
			foreach ( $options as $opt_key => $opt_args ) {

				$title = $args['title_alt'] ? $opt_args['title_alt'] : $opt_args['title'];	// normal or alternative title
				$value = trim( implode(' ', $prefixToVal ).' '. $opt_args['value'] .' '. implode(' ', $suffixToVal ) );		// value with the user given addition
				$return[$value] = $title;
			}

			// APPEND USER GIVEN OPTIONS
			$append_options = $args['append_options'];	// CACHE
			if ( is_array( $append_options ) && !empty ( $append_options )) {
				foreach ( $append_options as $useroption_value => $useroption_title ) {

					$return[$useroption_value] = $useroption_title;
				}
			}

			if ( $args['append_default']  ) {  $return[''] = $args['default_title'];  }		// APPEND 'default option'

			// RETURN VALUES WITHOUT KEYS
			if ( $args['only_values']  ) {

				$return_only_values = array();
				foreach ( $return as $value => $key ) {

					$return_only_values[] = $value;
				}
				$return = $return_only_values;
			}

			if ( $args['use_in'] === 'vc' && !$args['only_values'] ) {  $return = array_flip( $return );  }		// IF USED IN VC, THEN FLIP ARRAY

		    return $return;
		}

		/**
		* Get default style index values for:
		* 	Page Layouts / type = 'page_layouts'
		* 	Color Sets / type = 'color_sets'
		* 	Media Strech Ratios / type = 'stretchy_ratios'
		* 	Background Image Vertical Align / type = 'bgimage_valign'
		* 	Transparent Overlay / type = 'transparent_overlay'
		* 	Container boxing / type = 'boxed'
		* 	Text Align / type = 'text_align'
		* 	Animations / type = 'animations'
		*
		* May hook @ 'plethora_module_style_{type}'
		*
		*/
		public static function get_index_values( $type ) {

			if ( empty( $type )) { return array(); }

			switch ( $type ) {

				case 'page_layouts':

					$return = array( 
                          	'no_sidebar' 	=> array( 
												'title'             => PLE_CORE_ASSETS_URI .'/images/plethora/page_layouts/no_sidebar.png',
												'title_alt'         => esc_html__( 'No Sidebar', 'plethora-framework' ),
												'value'             => 'no_sidebar',
												'container_classes' => array(
																			'content'           => 'sidebar_off',
																			'content_main_loop' => 'col-md-12',
												),
                          				 ),
                          	'right_sidebar' => array( 
												'title'             => PLE_CORE_ASSETS_URI .'/images/plethora/page_layouts/right_sidebar.png',
												'title_alt'         => esc_html__( 'Right Sidebar', 'plethora-framework' ),
												'value'             => 'right_sidebar',
												'container_classes' => array(
																			'content'            => 'sidebar_on',
																			'content_main_loop'  => 'col-md-8',
																			'content_main_right' => 'col-md-4',
												),
                          				 ),
                          	'left_sidebar' => array( 
												'title'             => PLE_CORE_ASSETS_URI .'/images/plethora/page_layouts/left_sidebar.png',
												'title_alt'         => esc_html__( 'Left Sidebar', 'plethora-framework' ),
												'value'             => 'left_sidebar',
												'container_classes' => array(
																			'content'           => 'sidebar_on',
																			'content_main_loop' => 'col-md-8',
																			'content_main_left' => 'col-md-4',
												),
                          				 ),
        					'no_sidebar_narrow' => array( 
		                                        'title'             => PLE_CORE_ASSETS_URI .'/images/plethora/page_layouts/no_sidebar_narrow.png',
		                                        'title_alt'         => esc_html__( 'No Sidebar ( Narrow )', 'plethora-framework' ),
		                                        'value'             => 'no_sidebar_narrow',
		                                        'container_classes' => array(
		                                                'content'           => 'sidebar_off narrow_layout',
		                                                'content_main_loop' => 'col-md-8 col-md-offset-2',
		                                        ),
										),
 
					);
					break;


				case 'color_sets':

					$return = array( 
                          	'primary' 	=> array( 
												'title'     => esc_html__( 'Primary', 'plethora-framework' ),
												'title_alt' => esc_html__( 'Primary Color Set', 'plethora-framework' ),
												'value'     => 'primary_section',
                          				 ),
                          	'secondary' => array( 
												'title'     => esc_html__( 'Secondary', 'plethora-framework' ),
												'title_alt' => esc_html__( 'Secondary Color Set', 'plethora-framework' ),
												'value'     => 'secondary_section',
                          				 ),
                          	'dark'		=> array( 
												'title'     => esc_html__( 'Dark', 'plethora-framework' ),
												'title_alt' => esc_html__( 'Dark Color Set', 'plethora-framework' ),
												'value'     => 'dark_section',
                          				 ),
                          	'light'   	=> array( 
												'title'     => esc_html__( 'Light', 'plethora-framework' ),
												'title_alt' => esc_html__( 'Light Color Set', 'plethora-framework' ),
												'value'     => 'light_section',
                          				 ),
                          	'black'		=> array( 
												'title'     => esc_html__( 'Black', 'plethora-framework' ),
												'title_alt' => esc_html__( 'Black Color Set', 'plethora-framework' ),
												'value'     => 'black_section',
		                          				 ),
                          	'white'		=> array( 
												'title'     => esc_html__( 'White', 'plethora-framework' ),
												'title_alt' => esc_html__( 'White Color Set', 'plethora-framework' ),
												'value'     => 'white_section',
                          				 	),
                      	);
					break;

				case 'stretchy_ratios':

					$return = array( 
                          	'16-9' 	=> array( 
											'title'     => esc_html__( '16:9', 'plethora-framework' ),
											'title_alt' => esc_html__( '16:9 Display Ratio', 'plethora-framework' ),
											'value'     => 'stretchy_wrapper ratio_16-9',
										),
                          	'4-3' 	=> array( 
											'title'     => esc_html__( '4:3', 'plethora-framework' ),
											'title_alt' => esc_html__( '4:3 Display Ratio', 'plethora-framework' ),
											'value'     => 'stretchy_wrapper ratio_4-3',
										),
                          	'15-9'	=> array( 
											'title'     => esc_html__( '15:9', 'plethora-framework' ),
											'title_alt' => esc_html__( '15:9 Display Ratio', 'plethora-framework' ),
											'value'     => 'stretchy_wrapper ratio_15-9',
										),
                          	'2-1'	=> array( 
											'title'     => esc_html__( '2:1', 'plethora-framework' ),
											'title_alt' => esc_html__( '2:1 Display Ratio', 'plethora-framework' ),
											'value'     => 'stretchy_wrapper ratio_2-1',
										),
                          	'2-3'	=> array( 
											'title'     => esc_html__( '2:3', 'plethora-framework' ),
											'title_alt' => esc_html__( '2:3 Display Ratio', 'plethora-framework' ),
											'value'     => 'stretchy_wrapper ratio_2-3',
										),
                          	'3-4'	=> array( 
											'title'     => esc_html__( '3:4', 'plethora-framework' ),
											'title_alt' => esc_html__( '3:4 Display Ratio', 'plethora-framework' ),
											'value'     => 'stretchy_wrapper ratio_3-4',
										),
                          	'1-1'	=> array( 
											'title'     => esc_html__( '1:1', 'plethora-framework' ),
											'title_alt' => esc_html__( '1:1 Display Ratio', 'plethora-framework' ),
											'value'     => 'stretchy_wrapper ratio_1-1',
										),
                      	);
					break;

				case 'bgimage_valign':

					$return = array( 
                          	'middle'	=> array( 
											'title'     => esc_html__( 'Middle', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Middle Align', 'plethora-framework' ),
											'value'     => 'bg_vcenter',
										),
                          	'top' 		=> array( 
											'title'     => esc_html__( 'Top', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Top Align', 'plethora-framework' ),
											'value'     => 'bg_vtop',
										),
                          	'bottom'	=> array( 
											'title'     => esc_html__( 'Bottom', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Bottom Align', 'plethora-framework' ),
											'value'     => 'bg_vbottom',
										),
                      	);
					break;

				case 'transparent_overlay':

					$return = array( 
                          	'full'		=> array( 
											'title'     => esc_html__( 'Full', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Transparent Overlay: Full', 'plethora-framework' ),
											'value'     => 'transparent_film',
										),
                          	'to_top'	=> array( 
											'title'     => esc_html__( 'Gradient To Top', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Transparent Overlay: Gradient To Top', 'plethora-framework' ),
											'value'     => 'gradient_film_to_top',
										),
                          	'to_bottom'	=> array( 
											'title'     => esc_html__( 'Gradient To Bottom', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Transparent Overlay: Gradient To Bottom', 'plethora-framework' ),
											'value'     => 'gradient_film_to_bottom',
										),
                      	);
					break;

				case 'boxed':

					$return = array( 
                          	'boxed'		 	=> array( 
											'title'     => esc_html__( 'Boxed', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Boxed Design', 'plethora-framework' ),
											'value'     => 'boxed',
										),
                          	'boxed_plus' 	=> array( 
											'title'     => esc_html__( 'Boxed Plus', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Boxed Plus Design', 'plethora-framework' ),
											'value'     => 'boxed_plus',
										),
                          	'boxed_special'	=> array( 
											'title'     => esc_html__( 'Boxed Special', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Boxed Special Design', 'plethora-framework' ),
											'value'     => 'boxed_special',
										),
                      	);
					break;

				case 'text_align':

					$return = array( 
                          	'text-left'		=> array( 
											'title'     => esc_html__( 'Left', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Align Left', 'plethora-framework' ),
											'value'     => 'text-left',
										),
                          	'text-center' 	=> array( 
											'title'     => esc_html__( 'Center', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Align Center', 'plethora-framework' ),
											'value'     => 'text-center',
										),
                          	'text-right'	=> array( 
											'title'     => esc_html__( 'Right', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Align Right', 'plethora-framework' ),
											'value'     => 'text-right',
										),
                          	'text-justify'	=> array( 
											'title'     => esc_html__( 'Justify', 'plethora-framework' ),
											'title_alt' => esc_html__( 'Justify Align', 'plethora-framework' ),
											'value'     => 'text-justify',
										),
                      	);
					break;

				case 'animations':

					$return = array( 
							'bounce'             => array( 'title' => esc_html__( 'bounce', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounce animation', 'plethora-framework' ), 'value' => 'bounce' ),
							'bounceIn'           => array( 'title' => esc_html__( 'bounceIn', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceIn animation', 'plethora-framework' ), 'value' => 'bounceIn' ),
							'bounceInDown'       => array( 'title' => esc_html__( 'bounceInDown', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceInDown animation', 'plethora-framework' ), 'value' => 'bounceInDown' ),
							'bounceInLeft'       => array( 'title' => esc_html__( 'bounceInLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceInLeft animation', 'plethora-framework' ), 'value' => 'bounceInLeft' ),
							'bounceInRight'      => array( 'title' => esc_html__( 'bounceInRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceInRight animation', 'plethora-framework' ), 'value' => 'bounceInRight' ),
							'bounceInUp'         => array( 'title' => esc_html__( 'bounceInUp', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceInUp animation', 'plethora-framework' ), 'value' => 'bounceInUp' ),
							'bounceOut'          => array( 'title' => esc_html__( 'bounceOut', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceOut animation', 'plethora-framework' ), 'value' => 'bounceOut' ),
							'bounceOutDown'      => array( 'title' => esc_html__( 'bounceOutDown', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceOutDown animation', 'plethora-framework' ), 'value' => 'bounceOutDown' ),
							'bounceOutLeft'      => array( 'title' => esc_html__( 'bounceOutLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceOutLeft animation', 'plethora-framework' ), 'value' => 'bounceOutLeft' ),
							'bounceOutRight'     => array( 'title' => esc_html__( 'bounceOutRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceOutRight animation', 'plethora-framework' ), 'value' => 'bounceOutRight' ),
							'bounceOutUp'        => array( 'title' => esc_html__( 'bounceOutUp', 'plethora-framework' ), 'title_alt' => esc_html__( 'bounceOutUp animation', 'plethora-framework' ), 'value' => 'bounceOutUp' ),
							'fadeIn'             => array( 'title' => esc_html__( 'fadeIn', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeIn animation', 'plethora-framework' ), 'value' => 'fadeIn' ),
							'fadeInDown'         => array( 'title' => esc_html__( 'fadeInDown', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeInDown animation', 'plethora-framework' ), 'value' => 'fadeInDown' ),
							'fadeInDownBig'      => array( 'title' => esc_html__( 'fadeInDownBig', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeInDownBig animation', 'plethora-framework' ), 'value' => 'fadeInDownBig' ),
							'fadeInLeft'         => array( 'title' => esc_html__( 'fadeInLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeInLeft animation', 'plethora-framework' ), 'value' => 'fadeInLeft' ),
							'fadeInLeftBig'      => array( 'title' => esc_html__( 'fadeInLeftBig', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeInLeftBig animation', 'plethora-framework' ), 'value' => 'fadeInLeftBig' ),
							'fadeInRight'        => array( 'title' => esc_html__( 'fadeInRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeInRight animation', 'plethora-framework' ), 'value' => 'fadeInRight' ),
							'fadeInRightBig'     => array( 'title' => esc_html__( 'fadeInRightBig', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeInRightBig animation', 'plethora-framework' ), 'value' => 'fadeInRightBig' ),
							'fadeInUp'           => array( 'title' => esc_html__( 'fadeInUp', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeInUp animation', 'plethora-framework' ), 'value' => 'fadeInUp' ),
							'fadeInUpBig'        => array( 'title' => esc_html__( 'fadeInUpBig', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeInUpBig animation', 'plethora-framework' ), 'value' => 'fadeInUpBig' ),
							'fadeOut'            => array( 'title' => esc_html__( 'fadeOut', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOut animation', 'plethora-framework' ), 'value' => 'fadeOut' ),
							'fadeOutDown'        => array( 'title' => esc_html__( 'fadeOutDown', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOutDown animation', 'plethora-framework' ), 'value' => 'fadeOutDown' ),
							'fadeOutDownBig'     => array( 'title' => esc_html__( 'fadeOutDownBig', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOutDownBig animation', 'plethora-framework' ), 'value' => 'fadeOutDownBig' ),
							'fadeOutLeft'        => array( 'title' => esc_html__( 'fadeOutLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOutLeft animation', 'plethora-framework' ), 'value' => 'fadeOutLeft' ),
							'fadeOutLeftBig'     => array( 'title' => esc_html__( 'fadeOutLeftBig', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOutLeftBig animation', 'plethora-framework' ), 'value' => 'fadeOutLeftBig' ),
							'fadeOutRight'       => array( 'title' => esc_html__( 'fadeOutRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOutRight animation', 'plethora-framework' ), 'value' => 'fadeOutRight' ),
							'fadeOutRightBig'    => array( 'title' => esc_html__( 'fadeOutRightBig', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOutRightBig animation', 'plethora-framework' ), 'value' => 'fadeOutRightBig' ),
							'fadeOutUp'          => array( 'title' => esc_html__( 'fadeOutUp', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOutUp animation', 'plethora-framework' ), 'value' => 'fadeOutUp' ),
							'fadeOutUpBig'       => array( 'title' => esc_html__( 'fadeOutUpBig', 'plethora-framework' ), 'title_alt' => esc_html__( 'fadeOutUpBig animation', 'plethora-framework' ), 'value' => 'fadeOutUpBig' ),
							'flash'              => array( 'title' => esc_html__( 'flash', 'plethora-framework' ), 'title_alt' => esc_html__( 'flash animation', 'plethora-framework' ), 'value' => 'flash' ),
							'flip'               => array( 'title' => esc_html__( 'flip', 'plethora-framework' ), 'title_alt' => esc_html__( 'flip animation', 'plethora-framework' ), 'value' => 'flip' ),
							'flipInX'            => array( 'title' => esc_html__( 'flipInX', 'plethora-framework' ), 'title_alt' => esc_html__( 'flipInX animation', 'plethora-framework' ), 'value' => 'flipInX' ),
							'flipInY'            => array( 'title' => esc_html__( 'flipInY', 'plethora-framework' ), 'title_alt' => esc_html__( 'flipInY animation', 'plethora-framework' ), 'value' => 'flipInY' ),
							'flipOutX'           => array( 'title' => esc_html__( 'flipOutX', 'plethora-framework' ), 'title_alt' => esc_html__( 'flipOutX animation', 'plethora-framework' ), 'value' => 'flipOutX' ),
							'flipOutY'           => array( 'title' => esc_html__( 'flipOutY', 'plethora-framework' ), 'title_alt' => esc_html__( 'flipOutY animation', 'plethora-framework' ), 'value' => 'flipOutY' ),
							'hinge'              => array( 'title' => esc_html__( 'hinge', 'plethora-framework' ), 'title_alt' => esc_html__( 'hinge animation', 'plethora-framework' ), 'value' => 'hinge' ),
							'jello'              => array( 'title' => esc_html__( 'jello', 'plethora-framework' ), 'title_alt' => esc_html__( 'jello animation', 'plethora-framework' ), 'value' => 'jello' ),
							'lightSpeedIn'       => array( 'title' => esc_html__( 'lightSpeedIn', 'plethora-framework' ), 'title_alt' => esc_html__( 'lightSpeedIn animation', 'plethora-framework' ), 'value' => 'lightSpeedIn' ),
							'lightSpeedOut'      => array( 'title' => esc_html__( 'lightSpeedOut', 'plethora-framework' ), 'title_alt' => esc_html__( 'lightSpeedOut animation', 'plethora-framework' ), 'value' => 'lightSpeedOut' ),
							'pulse'              => array( 'title' => esc_html__( 'pulse', 'plethora-framework' ), 'title_alt' => esc_html__( 'pulse animation', 'plethora-framework' ), 'value' => 'pulse' ),
							'rollIn'             => array( 'title' => esc_html__( 'rollIn', 'plethora-framework' ), 'title_alt' => esc_html__( 'rollIn animation', 'plethora-framework' ), 'value' => 'rollIn' ),
							'rollOut'            => array( 'title' => esc_html__( 'rollOut', 'plethora-framework' ), 'title_alt' => esc_html__( 'rollOut animation', 'plethora-framework' ), 'value' => 'rollOut' ),
							'rotateIn'           => array( 'title' => esc_html__( 'rotateIn', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateIn animation', 'plethora-framework' ), 'value' => 'rotateIn' ),
							'rotateInDownLeft'   => array( 'title' => esc_html__( 'rotateInDownLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateInDownLeft animation', 'plethora-framework' ), 'value' => 'rotateInDownLeft' ),
							'rotateInDownRight'  => array( 'title' => esc_html__( 'rotateInDownRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateInDownRight animation', 'plethora-framework' ), 'value' => 'rotateInDownRight' ),
							'rotateInUpLeft'     => array( 'title' => esc_html__( 'rotateInUpLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateInUpLeft animation', 'plethora-framework' ), 'value' => 'rotateInUpLeft' ),
							'rotateInUpRight'    => array( 'title' => esc_html__( 'rotateInUpRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateInUpRight animation', 'plethora-framework' ), 'value' => 'rotateInUpRight' ),
							'rotateOut'          => array( 'title' => esc_html__( 'rotateOut', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateOut animation', 'plethora-framework' ), 'value' => 'rotateOut' ),
							'rotateOutDownLeft'  => array( 'title' => esc_html__( 'rotateOutDownLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateOutDownLeft animation', 'plethora-framework' ), 'value' => 'rotateOutDownLeft' ),
							'rotateOutDownRight' => array( 'title' => esc_html__( 'rotateOutDownRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateOutDownRight animation', 'plethora-framework' ), 'value' => 'rotateOutDownRight' ),
							'rotateOutUpLeft'    => array( 'title' => esc_html__( 'rotateOutUpLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateOutUpLeft animation', 'plethora-framework' ), 'value' => 'rotateOutUpLeft' ),
							'rotateOutUpRight'   => array( 'title' => esc_html__( 'rotateOutUpRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'rotateOutUpRight animation', 'plethora-framework' ), 'value' => 'rotateOutUpRight' ),
							'rubberBand'         => array( 'title' => esc_html__( 'rubberBand', 'plethora-framework' ), 'title_alt' => esc_html__( 'rubberBand animation', 'plethora-framework' ), 'value' => 'rubberBand' ),
							'shake'              => array( 'title' => esc_html__( 'shake', 'plethora-framework' ), 'title_alt' => esc_html__( 'shake animation', 'plethora-framework' ), 'value' => 'shake' ),
							'slideInDown'        => array( 'title' => esc_html__( 'slideInDown', 'plethora-framework' ), 'title_alt' => esc_html__( 'slideInDown animation', 'plethora-framework' ), 'value' => 'slideInDown' ),
							'slideInLeft'        => array( 'title' => esc_html__( 'slideInLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'slideInLeft animation', 'plethora-framework' ), 'value' => 'slideInLeft' ),
							'slideInRight'       => array( 'title' => esc_html__( 'slideInRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'slideInRight animation', 'plethora-framework' ), 'value' => 'slideInRight' ),
							'slideInUp'          => array( 'title' => esc_html__( 'slideInUp', 'plethora-framework' ), 'title_alt' => esc_html__( 'slideInUp animation', 'plethora-framework' ), 'value' => 'slideInUp' ),
							'slideOutDown'       => array( 'title' => esc_html__( 'slideOutDown', 'plethora-framework' ), 'title_alt' => esc_html__( 'slideOutDown animation', 'plethora-framework' ), 'value' => 'slideOutDown' ),
							'slideOutLeft'       => array( 'title' => esc_html__( 'slideOutLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'slideOutLeft animation', 'plethora-framework' ), 'value' => 'slideOutLeft' ),
							'slideOutRight'      => array( 'title' => esc_html__( 'slideOutRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'slideOutRight animation', 'plethora-framework' ), 'value' => 'slideOutRight' ),
							'slideOutUp'         => array( 'title' => esc_html__( 'slideOutUp', 'plethora-framework' ), 'title_alt' => esc_html__( 'slideOutUp animation', 'plethora-framework' ), 'value' => 'slideOutUp' ),
							'swing'              => array( 'title' => esc_html__( 'swing', 'plethora-framework' ), 'title_alt' => esc_html__( 'swing animation', 'plethora-framework' ), 'value' => 'swing' ),
							'tada'               => array( 'title' => esc_html__( 'tada', 'plethora-framework' ), 'title_alt' => esc_html__( 'tada animation', 'plethora-framework' ), 'value' => 'tada' ),
							'wobble'             => array( 'title' => esc_html__( 'wobble', 'plethora-framework' ), 'title_alt' => esc_html__( 'wobble animation', 'plethora-framework' ), 'value' => 'wobble' ),
							'zoomIn'             => array( 'title' => esc_html__( 'zoomIn', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomIn animation', 'plethora-framework' ), 'value' => 'zoomIn' ),
							'zoomInDown'         => array( 'title' => esc_html__( 'zoomInDown', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomInDown animation', 'plethora-framework' ), 'value' => 'zoomInDown' ),
							'zoomInLeft'         => array( 'title' => esc_html__( 'zoomInLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomInLeft animation', 'plethora-framework' ), 'value' => 'zoomInLeft' ),
							'zoomInRight'        => array( 'title' => esc_html__( 'zoomInRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomInRight animation', 'plethora-framework' ), 'value' => 'zoomInRight' ),
							'zoomInUp'           => array( 'title' => esc_html__( 'zoomInUp', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomInUp animation', 'plethora-framework' ), 'value' => 'zoomInUp' ),
							'zoomOut'            => array( 'title' => esc_html__( 'zoomOut', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomOut animation', 'plethora-framework' ), 'value' => 'zoomOut' ),
							'zoomOutDown'        => array( 'title' => esc_html__( 'zoomOutDown', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomOutDown animation', 'plethora-framework' ), 'value' => 'zoomOutDown' ),
							'zoomOutLeft'        => array( 'title' => esc_html__( 'zoomOutLeft', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomOutLeft animation', 'plethora-framework' ), 'value' => 'zoomOutLeft' ),
							'zoomOutRight'       => array( 'title' => esc_html__( 'zoomOutRight', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomOutRight animation', 'plethora-framework' ), 'value' => 'zoomOutRight' ),
							'zoomOutUp'          => array( 'title' => esc_html__( 'zoomOutUp', 'plethora-framework' ), 'title_alt' => esc_html__( 'zoomOutUp animation', 'plethora-framework' ), 'value' => 'zoomOutUp' ),
                      	);
					break;

				default:
					$return = array(); 
					break;
			}

			return apply_filters( 'plethora_module_style_'. $type .'', $return );
		}
	}
}