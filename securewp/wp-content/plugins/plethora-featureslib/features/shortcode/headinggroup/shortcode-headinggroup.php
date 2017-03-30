<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015 - 2016

File Description: Entry shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Headinggroup') ) {

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Headinggroup extends Plethora_Shortcode { 

      public static $feature_name          = "Heading Group";             // FEATURE DISPLAY TITLE 
      public static $feature_title         = "Heading Group Shortcode";   // FEATURE DISPLAY TITLE 
      public static $feature_description   = "Display Heading Group";     // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                        // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                        // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array();                     // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                        // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;                       // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public static $assets                = array();                     // ENQUEUE STYLES AND SCRIPTS
      public $wp_slug                      = 'headinggroup';              // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )

      public function __construct() {

          // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'        => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'        => esc_html__( 'Heading Group', 'plethora-framework'), 
                      'description' => esc_html__('Display Heading Group', 'plethora-framework'), 
                      'class'       => '',
                      'weight'      => 1,
                      'icon'        => $this->vc_icon(), 
                      'params'      => $this->params(), 
                      );
          $this->add( $map );         // ADD ΤΗΕ SHORTCODE
      }

      /** 
      * Returns shortcode parameters INDEX for VC panel
      * @return array
      */
      public function params_index() {

        $params_index['content'] = array( 
              "param_name"    => "content",                                  
              "type"          => "textarea_html",                                        
              "holder"        => "div",                                               
              "class"         => "plethora_vc_title",                                                 
              "heading"       => esc_html__("Heading Title", 'plethora-framework'),      
              "description"   => esc_html__("Set the heading title. Accepts HTML.", 'plethora-framework'),       
              "admin_label"   => false
        );
        $params_index['subtitle'] = array( 
              "param_name"    => "subtitle",                                  
              "type"          => "textfield",                                        
              "holder"        => "h4",                                               
              "class"         => "plethora_vc_title",                                                 
              "heading"       => esc_html__("Heading Subtitle", 'plethora-framework'),      
              "description"   => esc_html__("Set the heading subtitle", 'plethora-framework'),       
              "admin_label"   => false,                                              
        );
        $params_index['align'] = array( 
              "param_name"       => "align",
              "type"             => "value_picker",
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__('Heading Align', 'plethora-framework'),
              "picker_type"      => "single",  // Multiple or single class selection ( 'single'|'multiple' )
              "picker_cols"      => "3",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
              "values_index"     => array(        
                                    esc_html__('Left', 'plethora-framework')     => 'text-left',
                                    esc_html__('Centered', 'plethora-framework') => 'text-center',
                                    esc_html__('Right', 'plethora-framework')    => 'text-right',
                                ),            // Title=>value array with all values to display
        );
        $params_index['subtitle_position'] = array( 
              "param_name"    => "subtitle_position",
              "type"          => "dropdown",
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"       => esc_html__('Subtitle Position', 'plethora-framework'),
              "value"         => array( 
                                'Bottom'  => 'bottom', 
                                'Top'     => 'top',
                              ),
              "description"   => esc_html__('Choose whether you want the subtitle to be displayed above or below the title.', 'plethora-framework'),       
        );
        $params_index['extra_class'] = array( 
              "param_name"    => "extra_class",                                  
              "type"          => "textfield",                                        
              "holder"        => "h4",                                               
              "class"         => "plethora_vc_title",                                                 
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"       => esc_html__("Add Extra CSS Class to this element", 'plethora-framework'),      
              "admin_label"   => false,                                              
        );
        $params_index['css'] = array( 
              "param_name"    => "css",
              "type"          => "css_editor",
              'group'         => esc_html__( 'Design options', 'plethora-framework' ),
              "heading"       => esc_html__('CSS box', 'plethora-framework'),
        );

        // Used only in theme(s):  Healthflex / Music
        $params_index['type'] = array( 
              "param_name"       => "type",
              "type"             => "value_picker",
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__('Heading Type', 'plethora-framework'),
              "picker_type"      => "single",  // Multiple or single class selection ( 'single'|'multiple' )
              "picker_cols"      => "3",       // Picker columns for selections display (1,2,3,4,6)               
              "values_index"     => $this->get_heading_types()
        );

        // Used only in theme(s):  Avoir/Xenia
        $params_index['divider'] = array( 
              "param_name"       => "divider",                                  
              "type"             => "checkbox",                                        
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Display divider", 'plethora-framework'),      
              "std"              => 1,
              "value"            => array( __( 'Yes', 'plethora-framework' ) => 1 ),
        );

        // Used only in theme(s):  Xenia
        $params_index['background_title'] = array( 
              "param_name"    => "background_title",                                  
              "type"          => "textfield",                                        
              "holder"        => "h4",                                               
              "class"         => "plethora_vc_title",                                                 
              "heading"       => esc_html__("Heading Background title", 'plethora-framework'),      
              "description"   => esc_html__("Set the background title", 'plethora-framework'),       
              "admin_label"   => false,                                              
        );

        return $params_index;

      }

      /** 
      * Returns array of values for the 'type' index parameter
      * @return array
      */
      public function get_heading_types(){

        $output_array = array(

          esc_html__('Default', 'plethora-framework')    => '',
          esc_html__('Fancy', 'plethora-framework')      => 'fancy',
          esc_html__('Elegant', 'plethora-framework')    => 'elegant',
          esc_html__('Extra Bold', 'plethora-framework') => 'xbold',
          esc_html__('Thin', 'plethora-framework')       => 'thin',

        );

        return $output_array;

      }

      /** 
      * Configure parameters displayed
      * Will be displayed all items from params_index() with identical 'id'
      * This method should be used for extension class overrides
      *
      * @return array
      */
      public function params_config() {

          $params_config = array();

          return $params_config;
      }

      /** 
      * Returns divider
      * @return string
      */
        public function get_svg_divider( $divider_status = false ) {

        return '';
      }

      /** 
      * Returns shortcode content
      * @return array
      */
      public function content( $atts, $content = null ) {

        // Extract user input
        extract( shortcode_atts( $this->get_default_param_values(), $atts ) );

        $content = $this->remove_wpautop( $content );
        $subtitle_top = ( $subtitle_position == "top" )? TRUE : FALSE;

        $shortcode_atts = array(
                'title'             => ( ( isset( $content ) ) ? $content : '' ),
                'subtitle_position' => ( ( isset( $subtitle_position ) ) ? "subtitle_" . $subtitle_position : '' ),
                'subtitle'          => ( ( isset( $subtitle ) ) ? $subtitle : '' ),
                'subtitle_top'      => ( ( isset( $subtitle_top ) ) ? $subtitle_top : '' ),
                'background_title'  => ( ( isset( $background_title ) ) ? $background_title : '' ), // Xenia
                'type'              => ( ( isset( $type ) ) ? $type : '' ),
                'divider'           => ( ( isset( $divider ) ) ? $this->get_svg_divider( $divider ) : '' ), // Avoir, Xenia
                'align'             => ( ( isset( $align ) ) ? $align : '' ),
                'extra_class'       => ( ( isset( $extra_class ) ) ? $extra_class : '' ),
                'css'               => ( ( isset( $css ) ) ? esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ) : '' )
        );

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );
      }
  }
}