<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Column shortcode

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Vccolumn') ):

	/**
	 * @package Plethora Framework
	 */

	class Plethora_Shortcode_Vccolumn extends Plethora_Shortcode { 

      public static $feature_title         = "Column Shortcode";    // Feature display title  (string)
      public static $feature_description   = "";                    // Feature display description (string)
      public static $theme_option_control  = false;                 // Will this feature be controlled in theme options panel ( boolean )
      public static $theme_option_default  = true;                  // Default activation option status ( boolean )
      public static $theme_option_requires = array();               // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                  // Dynamic class construction ? ( boolean )
      public static $dynamic_method        = false;                 // Additional method invocation ( string/boolean | method name or false )
    	
      public function __construct() {

          if ( function_exists('vc_add_params') ) { 
          
             // Add Plethora parameters setup
            vc_add_params( 'vc_column', $this->add_params() );
          }
    	 }

       /** 
       * Returns ADDITIONAL shortcode options for Visual composer 'vc_column' shortcode
       *
       * The following option IDs exist already in VC...do not use them ( as in VC 4.11 )
       * Plethora Devs: this list should be updated on each VC upate
       *    Extra class:                          'el_class'
       *    Design Options > CSS box:             'css' ( in general, 'css' is reserved...don't use it! )
       *    Responsive Options > Width:           'width'
       *    Responsive Options > Responsiveness:  'offset'
       *    Content:                              'content' ( in general, 'content' is reserved...don't use it! )
       * 
       * The following option IDs are added by Plethora Themes ( as in VC 4.11 )
       *    Color Set:                            'color_set'
       *    Background:                           'transparent' ( select between color set bcg color and transparency )
       *    Transparent Overlay:                  'transparent_overlay'
       *    Content Align:                        'align'
       *    Boxed Design:                         'boxed'
       *    Animation > Animation:                'animation'
       *
       * @return array
       */
       public function add_params() {

          $params =  array(

                    array(
                        "param_name"    => "color_set",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        "heading"       => esc_html__("Color Set", 'hotel-xenia'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true
                                           )),
                        "description"   => esc_html__("Color setup affects text, link & background color. Those colors can be configured on theme options panel", 'hotel-xenia'),
                    ),
                    array(
                        "param_name"    => "transparent",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Background Type", 'hotel-xenia'),
                        "value"         => array( 
                                              esc_html__('Color Set Background', 'hotel-xenia') => '', 
                                              esc_html__('Transparent', 'hotel-xenia')          => 'transparent' ,
                                          ),
                        "description"   => esc_html__("You may set a custom background color under 'Design Options' tab ", 'hotel-xenia'),
                    ),

                   array(
                        "param_name"    => "transparent_overlay",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Transparent Overlay", 'hotel-xenia'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'transparent_overlay', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true,
                                          'default_title'   => esc_html__('None', 'hotel-xenia')
                                           )),
                        "description"   => esc_html__("The transparency percentage can be configured on theme options panel", 'hotel-xenia'),
                    ),                    

                    array(
                        "param_name"    => "align",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Content Align", 'hotel-xenia'),
                        "description"   => esc_html__("Inheritance will align content according to row settings", 'hotel-xenia'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'text_align', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true,
                                          'default_title'   => esc_html__('Inherit', 'hotel-xenia')
                                           )),
                    ),

                    array(
                        "param_name"    => "boxed",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Boxed Design", 'hotel-xenia'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'boxed', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true,
                                          'default_title'   => esc_html__('No', 'hotel-xenia')
                                           )),
                        "description"   => esc_html__("Boxed designs will add an inner padding and some additional styling features", 'hotel-xenia'),
                    ),

                    array(
                        "param_name"    => "animation",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Animation", 'hotel-xenia'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'animations', 
                                          'use_in'          => 'vc',
                                          'prefix_all_values' => 'wow',
                                          'prepend_default' => true,
                                          'default_title'   => esc_html__('None', 'hotel-xenia')
                                           )),
                   ),
          );

          return $params;
       }
	}
	
 endif;