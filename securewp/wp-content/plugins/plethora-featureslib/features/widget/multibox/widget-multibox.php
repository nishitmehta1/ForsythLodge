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

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_Multibox') ) {

	/**
	 * @package Plethora Framework
	 */
	class Plethora_Widget_Multibox extends WP_Widget  {

		public static $feature_title          = "Multibox";						// FEATURE DISPLAY TITLE
		public static $feature_description    = "Styled box with text or HTML.";	// FEATURE DISPLAY DESCRIPTION (STRING)
		public static $theme_option_control   = true;        						// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
		public static $theme_option_default   = true;        						// DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
		public static $theme_option_requires  = array();        					// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct      = false;        						// DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
		public static $dynamic_method         = false; 								// ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ) | THIS A PARENT METHOD, FOR ADDING ACTION
		public static $wp_slug 				  = 'multibox-widget';					// SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
		public static $assets;
		public $test;

		public function __construct() { 

			/* LEAVE INTACT ACROSS WIDGET CLASSES */

			$id_base     = WIDGETS_PREFIX . self::$wp_slug;
			$name        = '> PL | ' . self::$feature_title;
			$widget_ops  = array( 
			  'classname'   => self::$wp_slug, 
			  'description' => esc_html__('Styled box with text or HTML', 'plethora-framework')
			  );
			$control_ops = array( 'id_base' => $id_base );

			$this->test = parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT

			/* ADDITIONAL WIDGET CODE STARTS HERE */
			Plethora_Widget::enableMedia();     /* MEDIA MANAGER */
		}

		public function params() {

			$params_index['title'] = array( 
				"param_name"      => "title",
				"type"            => "textfield",
				"heading"         => esc_html__("Title", 'plethora-framework'),
				"is_widget_title" => true,
			);
			$params_index['title_align'] = array( 
				"param_name" => "title_align",
				"type"       => "dropdown",
				"heading"    => esc_html__('Title Align', 'plethora-framework'),
				"value"      => Plethora_Module_Style::get_options_array( array( 
					'type'   => 'text_align', 
					'use_in' => 'vc',
				)),
			);
			$params_index['content'] = array( 
				"param_name" => "content",
				"type"       => "textarea",
				"heading"    => esc_html__("Content ( simple text, HTML and shortocodes )", 'plethora-framework'),
			);
			$params_index['align'] = array( 
				"param_name" => "align",
				"type"       => "dropdown",
				"heading"    => esc_html__("Content Align", 'plethora-framework'),
				"value"      => Plethora_Module_Style::get_options_array( array( 
					'type'            => 'text_align', 
					'use_in'          => 'vc',
					'prepend_default' => true,
					'default_title'   => esc_html__('Inherit', 'plethora-framework')
				)),
			);

			$params_index['color_set'] = array( 
				"param_name" => "color_set",
				"type"       => "dropdown",
				"heading"    => esc_html__("Color Set", 'plethora-framework'),
				"description"   => esc_html__("Choose a color setup for this section. Remember: all colors in above options can be configured via the theme options panel", 'plethora-framework'),
				"value"      => Plethora_Module_Style::get_options_array( array( 
					'type'            => 'color_sets', 
					'use_in'          => 'vc',
					'prepend_default' => true
				)),
			);
			$params_index['bgimage'] = array( 
				"param_name"  => "bgimage",
				"type"        => "attach_image",
				"heading"     => esc_html__("Background Image ( if active )", 'plethora-framework'),
				"value"       => '',
				"description" => esc_html__("Upload/select a background image for this section", 'plethora-framework'),
				"dependency"  => array(
					"element" => "background",
					"value"   => array("bgimage")
				),
			);
			$params_index['bgimage_valign'] = array( 
				"param_name" => "bgimage_valign",
				"type"       => "dropdown",
				"heading"    => esc_html__("Bacground Image Vertical Align ( if active )", 'plethora-framework'),
				"value"      => Plethora_Module_Style::get_options_array( array( 
					'type'   => 'bgimage_valign', 
					'use_in' => 'vc',
				)),
				"admin_label" => false, 
				"dependency"  => array(
					"element" => "background",
					"value"   => array( 'bgimage')
				),
			);

			$params_index['transparent_overlay'] = array( 
				"param_name" => "transparent_overlay",
				"type"       => "dropdown",
				"heading"    => esc_html__("Transparent Overlay", 'plethora-framework'),
				"value"      => Plethora_Module_Style::get_options_array( array( 
					'type'            => 'transparent_overlay', 
					'use_in'          => 'vc',
					'prepend_default' => true,
					'default_title'   => esc_html__('None', 'plethora-framework')
				)),
				"description"   => esc_html__("The transparency percentage can be configured on theme options panel", 'plethora-framework'),
			);

			$params_index['boxed'] = array( 
				"param_name"  => "boxed",
				"type"        => "dropdown",
				"heading"     => esc_html__("Boxed Design", 'plethora-framework'),
				"description" => esc_html__("Boxed design will add an inner padding and some additional styling according to selected color set", 'plethora-framework'),
				"value"       => Plethora_Module_Style::get_options_array( array( 
					'type'            => 'boxed', 
					'use_in'          => 'vc',
					'prepend_default' => true,
					'default_title'   => esc_html__('No', 'plethora-framework')
				)),
			);

			$params_index['animation'] = array( 
				"param_name" => "animation",
				"type"       => "dropdown",
				"heading"    => esc_html__("Animation", 'plethora-framework'),
				"value"      => Plethora_Module_Style::get_options_array( array( 
					'type'              => 'animations', 
					'use_in'            => 'vc',
					'prefix_all_values' => 'wow',
					'prepend_default'   => true,
					'default_title'     => esc_html__('None', 'plethora-framework')
				)),
			);
			$params_index['extra_class'] = array( 
				"param_name"  => "extra_class",                                  
				"type"        => "textfield",                                    
				"heading"     => esc_html__("Extra class(es)", 'plethora-framework'),       
				"description" => esc_html__("Separate classes ONLY with space", 'plethora-framework'),
				"value"       => '',                                   
			);


			return $params_index;
		}

	function widget( $args, $instance ) {

	  $params                 = $this->params();                                                // GET PARAMETERS
	  $widget_atts            = Plethora_Widget::get_widget_atts( $params, $args, $instance );  // GET WIDGET ATTRIBUTES
	  $widget_atts['id_base'] = $this->id_base;                                                 // ADD id_base FOR MAIN WRAPPER CLASS OUTPUT

	  // Get output from template
	  echo Plethora_Widget::get_templatepart_output( $widget_atts, __FILE__ );
	  // OR get ouput from shortcode
	  // echo Plethora_Widget::get_shortcode_output( 'vc_column', $widget_atts );
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