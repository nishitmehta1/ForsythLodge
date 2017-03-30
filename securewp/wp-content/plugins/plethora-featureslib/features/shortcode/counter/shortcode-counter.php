<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Counter shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Counter') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Counter extends Plethora_Shortcode { 

    public static $feature_title         = "Counter Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public static $assets                = array(
                                                    array( 'script' => array( 'counter-up' ) ), // Scripts files - wp_enqueue_script
                                                    array( 'style'  => array( 'animate' ) ),  // Style files - wp_register_style
                                            );
    public $wp_slug                      =  'counter';           // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
   
    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'             => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'             => esc_html__("Counter", 'plethora-framework'), 
                    'description'      => esc_html__('Add a counter feature', 'plethora-framework'), 
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
              "param_name" => "title",
              "type"       => "textfield",                                        
              "holder"     => "h3",                                               
              "class"      => "plethora_vc_title",                                                    
              "heading"    => esc_html__("Title ( no HTML please )", 'plethora-framework'),
              "value"      => '',                                     
            ),
            array(
              "param_name"  => "content",
              "type"        => "textarea",                                        
              "heading"     => esc_html__("Subtitle ( HTML allowed )", 'plethora-framework'),
              'description' => Plethora_Theme::allowed_html_for( 'heading', true ),
              "value"       => '',                                     
            ),
            array(
              "param_name"       => "counter_value",
              "type"             => "textfield",                                        
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Counter Value ( only number value )", 'plethora-framework'),
              "value"            => '',                                     
            ),
            array(
              "param_name"       => "counter_delay",
              "type"             => "textfield",                                        
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Counter Delay ( in milliseconds )", 'plethora-framework'),
              "value"            => '10',                                     
            ),
            array(
              "param_name"       => "counter_time",
              "type"             => "textfield",                                        
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Counter Time ( in milliseconds )", 'plethora-framework'),
              "value"            => '1000',                                     
            ),
            // array(
            //   "param_name"       => "animation",
            //   "type"             => "dropdown",
            //   'edit_field_class' => 'vc_col-sm-6 vc_column',
            //   "heading"          => esc_html__("Animation ( HTML allowed )", 'plethora-framework'),
            //   "value"            => Plethora_Module_Style::get_options_array( array( 
            //                           'type'            => 'animations', 
            //                           'use_in'          => 'vc',
            //                           'prepend_default' => true
            //                         )),
            // ),
            // array(
            //   "param_name"       => "animation_delay",
            //   "type"             => "textfield",                                        
            //   'edit_field_class' => 'vc_col-sm-6 vc_column',
            //   "heading"          => esc_html__("Animation Delay", 'plethora-framework'),
            //   "value"            => '400',                                     
            // ),
            array(
              "param_name"       => "el_class",
              "type"             => "textfield",                                        
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Extra Class", 'plethora-framework'),
              "value"            => '',                                     
            ),
            array(
              "param_name"       => "id",
              "type"             => "textfield",                                        
              "heading"          => esc_html__("ID", 'plethora-framework'),
              "description"      => sprintf( esc_html__('%1$sDO NOT LEAVE THIS VALUE EMPTY OR IDENTICAL WITH OTHER ELEMENTS, ESPECIALLY ON DUPLICATE. Leaving this empty OR identical with other counter instances, will cause problems on counter functionality.%2$s', 'plethora-framework'), '<span style="color:red">', '</span>' ),       
              "std"              => uniqid(),
              "save_always"      => true,
            ),
            array(
              "param_name" => "icon",
              "type"       => "iconpicker",
              "value"      => 'fa fa-signal',
              'group'      => esc_html__( 'Icon', 'plethora-framework' ),
              "heading"    => esc_html__('Select icon', 'plethora-framework'),
              'settings'   => array(
                                'type'         => 'plethora',
                                'iconsPerPage' => 56, // default 100, how many icons per/page to display
                              ),
            ),
            array(
              "param_name"    => "css",
              "type"          => "css_editor",
              'group'         => esc_html__( 'Design options', 'plethora-framework' ),
              "heading"       => esc_html__('CSS box', 'plethora-framework'),
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
          'title'           => '',
          'counter_value'   => 'text-left',
          'counter_time'    => '1000',
          'counter_delay'   => '10',
          //'animation'       => '',
          //'animation_delay' => '',
          'icon'            => 'fa fa-signal',
          'id'              => '',
          'el_class'        => '',
          'css'             => '',
        ), $atts ) );

        // Create init script
        Plethora_Theme::enqueue_init_script( array(
                                                'handle'   => 'counter-up',
                                                'script'   => $this->waypoint_init_script( $id, $counter_time, $counter_delay ),
                                                'multiple' => true
                                              )
                                            );

        // Place all values in 'shortcode_atts' variable
        $shortcode_atts = array (
                                'title'                     => $title,  
                                'content'                   => wp_kses( $content, Plethora_Theme::allowed_html_for( 'heading' ) ),
                                'counter_value'             => is_numeric( $counter_value ) ? $counter_value : 0, 
                                'counter_time'              => is_numeric( $counter_time ) ? $counter_time : 0, 
                                'counter_delay'             => is_numeric( $counter_delay ) ? $counter_delay : 0, 
                                //'animation_class'           => !empty( $animation ) ? 'os-animation '. esc_attr( $animation ) : '', 
                                //'animation_data_attr'       => !empty( $animation ) ? esc_attr( $animation ) : '', 
                                //'animation_delay_data_attr' => is_numeric( $animation_delay ) ? esc_attr( $animation_delay ) : 0, 
                                'icon'                      => esc_attr( $icon ), 
                                'el_class'                  => esc_attr( $el_class ), 
                                'id'                        => esc_attr( $id ), 
                                'css'                       => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),
                               );


        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

    }

    public function waypoint_init_script( $id, $time, $delay ) {

      $script = '';

      if ( !empty( $id ) && is_numeric( $time ) && is_numeric( $delay ) ) {
        $script .=  '<script> ';
        $script .= '"use strict";';
        $script .= '(function($) { ';
        $script .= '$("#'. $id .'").counterUp({';
        $script .= 'delay: '. $delay .',';
        $script .= 'time: '. $time .'';
        $script .= ' }); ';
        $script .= ' })(jQuery)';
        $script .= ' </script>';
      }

      return $script;
    }

	}
	
 endif;