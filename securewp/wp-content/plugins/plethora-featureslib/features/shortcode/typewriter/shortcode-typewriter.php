<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Typewriter Heading shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Typewriter') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Typewriter extends Plethora_Shortcode { 

    public static $feature_title         = "Typewriter Heading Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public static $assets                = array(
                                                    array( 'script' => array( 'animated-headline' ) ), // Scripts files - wp_enqueue_script
                                            );
    public $wp_slug                      =  'typewriter';           // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
   
    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'             => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'             => esc_html__("Typewriter Heading", 'plethora-framework'), 
                    'description'      => esc_html__('Add a heading with typewriter effect', 'plethora-framework'), 
                    'class'            => '', 
                    'weight'           => 1, 
                    'category'         => esc_html__('Plethora Shortcodes', 'plethora-framework'),
                    'icon'             => $this->vc_icon(), 
                    // 'custom_markup' => $this->vc_custom_markup( 'Counter' ), 
                    'params'           => $this->params(), 
                    );
        // Add the shortcode
        $this->add( $map );

    }

    /** 
    * Returns shortcode parameters for VC panel
    *
    * @return array
    * @since 2.0
    *
    */
    public function params() {

      $params = array(

           array(
              "param_name"       => "pre",
              "type"             => "textfield",                                        
              "heading"          => esc_html__("Heading // Pre-Typewriter Text ( no HTML please )", 'plethora-framework'),
              "description"      => esc_html__("This is placed before the typewriter text. Leave empty if you don't need it.", 'plethora-framework'),
              "value"            => '',                                     
            ),
            array(
              "param_name"       => "parts",
              "type"             => "textfield",                                        
              "heading"          => esc_html__("Heading // Typewriter Text Parts ( no HTML please )", 'plethora-framework'),
              "description"      => esc_html__("This is the actual typewriter text. Separate text parts with |", 'plethora-framework'),
              "value"            => '',                                     
            ),
           array(
              "param_name"  => "tag",
              "type"        => "dropdown",
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"     => esc_html__("Heading Tag", 'plethora-framework'),
              "description" => esc_html__("The tag that will wrap the whole heading( in example h3 )", 'plethora-framework'),
              "std"         => 'h3',
              "value"       => array( 
                                      'h1'  => 'h1',
                                      'h2'  => 'h2',
                                      'h3'  => 'h3',
                                      'h4'  => 'h4',
                                      'h5'  => 'h5',
                                      'h6'  => 'h6',
                                      'p'   => 'p',
                                      'div' => 'div',
                                      )                                  
            ),

            array(
                "param_name"    => "subtitle",                                  
                "type"          => "textfield",                                        
                "holder"        => "h4",                                               
                "class"         => "plethora_vc_title",                                                 
                "heading"       => esc_html__("Heading Subtitle", 'plethora-framework'),      
                "value"         => '',
                "description"   => esc_html__("Set the heading subtitle", 'plethora-framework'),       
                "admin_label"   => false,                                              
            ),

            array(
                "param_name"       => "subtitle_position",
                "type"             => "dropdown",
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                "heading"          => esc_html__('Subtitle Position', 'plethora-framework'),
                "std"              => 'bottom',
                "value"            => array( 
                                        'Bottom'  => 'bottom', 
                                        'Top'     => 'top',
                                      ),
                "description"     => esc_html__('Choose whether you want the subtitle to be displayed above or below the title.', 'plethora-framework'),       
            ),

            array(
                "param_name"       => "divider",                                  
                "type"             => "checkbox",                                        
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                "heading"          => esc_html__("Display divider", 'plethora-framework'),      
                "std"              => 1,
                "value"            => array( __( 'Yes', 'plethora-framework' ) => 1 ),
            ),

            array(
                "param_name"       => "align",
                "type"             => "value_picker",
                'edit_field_class' => 'vc_col-sm-6 vc_column',
                "heading"          => esc_html__('Heading Set Align', 'plethora-framework'),
                "description"      => esc_html__('Align for all text elements', 'plethora-framework'),
                "picker_type"      => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                "picker_cols"      => "3",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                "value"            => 'text-center',     
                "values_index"     => array(        
                                      esc_html__('Left', 'plethora-framework')     => 'text-left',
                                      esc_html__('Centered', 'plethora-framework') => 'text-center',
                                      esc_html__('Right', 'plethora-framework')    => 'text-right',
                                  ),            // Title=>value array with all values to display
            ),

            array(
              "param_name"       => "el_class",
              "type"             => "textfield",                                        
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Extra Class", 'plethora-framework'),
              "value"            => '',                                     
            ),
            array(
              "param_name" => "css",
              "type"       => "css_editor",
              'group'      => esc_html__( 'Design options', 'plethora-framework' ),
              "heading"    => esc_html__('CSS box', 'plethora-framework'),
            ),
      );

      return $params;
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
        extract( shortcode_atts( array( 
          'pre'               => '',
          'parts'             => '',
          'tag'               => 'h3',
          'subtitle'          => '',
          'subtitle_position' => 'bottom',
          'divider'           => 1,
          'align'             => 'text-center',
          'el_class'          => '',
          'css'               => '',
        ), $atts ) );

        $parts = !empty( $parts ) ? explode('|', $parts) : array();
        $parts_arr = array();
        $count = 0;
        foreach ( $parts as $part ) {
            $count++;
            $part_class  = $count === 1 ? 'is-visible' : '';
            $parts_arr[] = array( 'text' => $part, 'class' => esc_attr( $part_class ) );
        }
        // Place all values in 'shortcode_atts' variable
        $shortcode_atts = array (
                                'pre'               => $pre,  
                                'parts'             => $parts_arr,
                                'tag'               => esc_attr( $tag ), 
                                'subtitle'          => esc_attr( $subtitle ), 
                                'subtitle_position' => esc_attr( $subtitle_position ),
                                'divider'           => method_exists( 'Plethora_Theme', 'get_svg_divider') ? Plethora_Theme::get_svg_divider( $divider ) : '',
                                'align'             => esc_attr( $align ), 
                                'el_class'          => esc_attr( $el_class ), 
                                'css'               => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),
                               );

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

    }
	}
	
 endif;