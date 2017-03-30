<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: About Us Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_Aboutus') ) {

	/**
	 * @package Plethora Framework
	 */
	class Plethora_Widget_Aboutus extends WP_Widget  {

		public static $feature_title          = "About Us";							// FEATURE DISPLAY TITLE
		public static $feature_description    = "Display your company information";	// FEATURE DISPLAY DESCRIPTION (STRING)
		public static $theme_option_control   = true;        						// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
		public static $theme_option_default   = true;        						// DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
		public static $theme_option_requires  = array();        					// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct      = false;        						// DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
		public static $dynamic_method         = false; 								// ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ) | THIS A PARENT METHOD, FOR ADDING ACTION
		public static $wp_slug 				  =  'aboutus-widget';					// SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
		public static $assets;

		public function __construct() { 

            /* LEAVE INTACT ACROSS WIDGET CLASSES */

            $id_base     = WIDGETS_PREFIX . self::$wp_slug;
            $name        = '> PL | ' . self::$feature_title;
            $widget_ops  = array( 
              'classname'   => self::$wp_slug, 
              'description' => self::$feature_title 
              );
            $control_ops = array( 'id_base' => $id_base );

            parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT

            /* ADDITIONAL WIDGET CODE STARTS HERE */

		}

		public function params() {

			$params_index['title'] = array( 
                "param_name"      => "title",
                "type"            => "textfield",
                "heading"         => esc_html__("Title", 'plethora-framework'),
                "is_widget_title" => true,
			);
			$params_index['logo'] = array( 
				"param_name" => "logo",
				"type"       => "attach_image",
				"heading"    => esc_html__('Logo Image', 'plethora-framework'),
				'default'    => PLE_THEME_ASSETS_URI .'/images/logo-white.png',
			);
			$params_index['logo_max_width'] = array( 
				"param_name" => "logo_max_width",
				"type"       => "textfield",
				"heading"    => esc_html__("Logo Max Width ( i.e 150px OR 80% )", 'plethora-framework'),
				'default'    => ''
			);
			$params_index['description'] = array( 
				"param_name" => "description",
				"type"       => "textarea",
				"heading"    => esc_html__("Description ( simple text, HTML and shortocodes )", 'plethora-framework'),
				'default'    => esc_html__('Plethora\'s WP Themes are mainly niche oriented but so flexible that it can fit in any Business Site!', 'plethora-framework')
			);
			$params_index['address'] = array( 
				"param_name" => "address",
				"type"       => "textfield",
				"heading"    => esc_html__("Address", 'plethora-framework'),
				'default'    => esc_html__('79 Folsom Ave, San Francisco, CA 94107', 'plethora-framework')
			);
			$params_index['googleMapURL'] = array( 
				"param_name" => "googleMapURL",
				"type"       => "textfield",
				"heading"    => esc_html__("Google Map URL", 'plethora-framework'),
				'default'    => 'https://www.google.com/maps/place/79+Folsom+St,+San+Francisco,+CA+94105,+USA/@37.7902642,-122.3929651,17z/data=!3m1!4b1!4m2!3m1!1s0x8085807aad0a9e0b:0x378e593dff7a2ac3?hl=en'
			);
			$params_index['telephone'] = array( 
				"param_name" => "telephone",
				"type"       => "textfield",
				"heading"    => esc_html__("Telephone", 'plethora-framework'),
				'default'    => '(+30) 210 1234567',
			);
			$params_index['email'] = array( 
				"param_name" => "email",
				"type"       => "textfield",
				"heading"    => esc_html__("Email", 'plethora-framework'),
				'default'    => 'info@plethorathemes.com',
			);
			$params_index['url'] = array( 
				"param_name" => "url",
				"type"       => "textfield",
				"heading"    => esc_html__("URL", 'plethora-framework'),
				'default'    => 'http://plethorathemes.com',
			);
			$params_index['socials'] = array( 
				"param_name" => "socials",
				"type"       => "radio",
				"heading"    => esc_html__("Display Socials", 'plethora-framework'),
				'default' => true,
				'value' => array(
					esc_html__('No', 'plethora-framework')  => false,
					esc_html__('Yes', 'plethora-framework') => true,
				)
			);
			$params_index['orientation'] = array( 
				"param_name" => "orientation",
				"type"       => "radio",
				"heading"    => esc_html__("Orientation", 'plethora-framework'),
				'default' => '',
				'value' => array(
					esc_html__('Vertical', 'plethora-framework')   => '',
					esc_html__('Horizontal', 'plethora-framework') => 'horizontal',
				)
			);
			$params_index['extra_class'] = array( 
				"param_name"  => "extra_class",                                  
				"type"        => "textfield",                                    
				"heading"     => esc_html__("Extra class(es)", 'plethora-framework'),       
				"description" => esc_html__("Separate classes ONLY with space", 'plethora-framework'),
				"default"     => '',                                   
			);

          	return $params_index;
		}

		function widget( $args, $instance ) {

			$params                 = $this->params();                                                // GET PARAMETERS
			$widget_atts            = Plethora_Widget::get_widget_atts( $params, $args, $instance );  // GET WIDGET ATTRIBUTES
			$widget_atts['id_base'] = $this->id_base;                                                 // ADD id_base FOR MAIN WRAPPER CLASS OUTPUT
			// Add website socials, if social module exists
			if ( $widget_atts['socials'] && method_exists( 'Plethora_Module_Social', 'get_icons' ) ) { 

				$site_socials = Plethora_Module_Social::get_icons('all');
				$widget_atts['social_items'] = array();
				foreach ( $site_socials as $key => $social ) {

					$widget_atts['social_items'][] = array( 
								'social_title' => esc_attr( $social['title'] ),
								'social_icon'  => esc_attr( $social['icon'] ),
								'social_url'   => esc_url( $social['url'] )
								);
				}
			} else {

				$widget_atts['social_items'] = array();
			}
			// print_r( $widget_atts );

			// Get output from template
			echo Plethora_Widget::get_templatepart_output( $widget_atts, __FILE__ );
		}

		function update( $new_instance, $old_instance ) {

			return $new_instance;

		}

		function form( $instance ) {

			$field_params = $this->params();	                                    // GET OPTIONS PARAMETERS
			$defaults     = Plethora_Widget::get_form_defaults( $field_params );  // GET DEFAULT 
			$instance     = wp_parse_args((array) $instance, $defaults);          // PARSE INSTANCE ARGUMENTS

			foreach( $field_params as $key => $field_args ){                      // CREATE THE FORM!

				$field_args['obj']      = $this;
				$field_args['instance'] = $instance;
				echo Plethora_Widget::get_field( $field_args ) ;
			}
		}
	}
 }