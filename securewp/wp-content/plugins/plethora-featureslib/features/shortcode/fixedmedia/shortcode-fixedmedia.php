<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Entry shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Fixedmedia') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Fixedmedia extends Plethora_Shortcode { 

      public $wp_slug                      = 'fixedmedia';              // This should be the WP slug of the content element ( WITHOUT the prefix constant )
      public static $feature_title         = "Fixed Ratio Media Shortcode";  // FEATURE DISPLAY TITLE 
      public static $feature_description   = "";                            // FEATURE DISPLAY DESCRIPTION
      public static $theme_option_control  = true;                          // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                          // DEFAULT ACTIVATION OPTION STATUS 
      public static $theme_option_requires = array();                       // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                          // DYNAMIC CLASS CONSTRUCTION ? ( boolean )
      public static $dynamic_method        = false;                         // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public static $assets;                                                // SCRIPT & STYLE FILES

      public function __construct() {

          // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'          => esc_html__('Fixed Ratio Media', 'plethora-framework'),
                      'description'   => esc_html__('Display responsive media with a fixed aspect ratio', 'plethora-framework'),
                      'class'         => '',
                      'weight'        => 1,
                      'category'      => esc_html__('Content', 'plethora-framework'),
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( 'Responsive Media' ), 
                      'params'        => $this->params(), 
                      );
          // Add the shortcode
          $this->add( $map );
    	}

       /** 
       * Returns shortcode settings (compatible with Visual composer)
       *
       * @return array
       * @since 1.0
       *
       */
       public function params() {

          $params = array(

                    array(
                      "param_name"    => "media_type",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h3",                                               
                      "class"         => "", 
                      "heading"       => esc_html__("Image", 'plethora-framework'),      
                      "value"         => array(
                                            'Image' => 'image',
                                            'Video' => 'video'
                                         ),
                    ),

                    array(
                      "param_name"    => "image",                                  
                      "type"          => "attach_image",                                        
                      "holder"        => "img",                                               
                      "class"         => "", 
                      "heading"       => esc_html__("Image", 'plethora-framework'),      
                      "value"         => '',
                      'dependency'    => array( 
                                            'element' => 'media_type', 
                                            'value'   => array('image'),  
                                        )
                    ),

                    array(
                      "param_name"    => "video",                                  
                      "type"          => "textfield",                                        
                      "class"         => "", 
                      "heading"       => esc_html__("Video link", 'plethora-framework'),      
                      "description"   => esc_html__('Enter link to video (Note: read more about available formats at WordPress <a target="_blank" href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F">Codex page</a>).', 'plethora-framework'),       
                      "value"         => '',
                      "admin_label"   => false,                                              
                      'dependency'    => array( 
                                            'element' => 'media_type', 
                                            'value'   => array('video'),  
                                        )
                    ),

                    array(
                        "param_name"    => "stretchy_ratio",
                        "type"          => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__('Media Display Ratio', 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'stretchy_ratios', 
                                          'use_in'          => 'vc',
                                          ) 
                        ),
                        "description"   => esc_html__('Your image/video will be displayed on the selected ratio.', 'plethora-framework'),       
                    ),

                    array(
                        "param_name"    => "bgimage_valign",
                        "type"          => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__('Image Vertical Align', 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'bgimage_valign', 
                                          'use_in'          => 'vc',
                                          ) 
                        ),
                        'dependency'    => array( 
                                              'element' => 'media_type', 
                                              'value'   => array('image'),  
                                          )
                    ),

                    array(
                        "param_name"    => "transparent_overlay",
                        "type"          => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__('Transparent Overlay', 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'transparent_overlay', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true
                                           )),
                         "description"   => esc_html__('Set a transparent overlay for your media', 'plethora-framework'),       
                   ),

                    array(
                        "param_name"    => "color_set",
                        "type"          => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__('Color Set', 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true
                                          )),
                        "description"   => esc_html__('Choose a color set will affect the transparent overlay styling', 'plethora-framework'),       
                        'dependency'    => array( 
                                            'element' => 'transparent_overlay', 
                                            'value'   => Plethora_Module_Style::get_options_array( array( 
                                                        'type'            => 'transparent_overlay', 
                                                        'use_in'          => 'vc',
                                                        'only_values' => true
                                                        )) 
                                        )
                    ),
                    array(
                      'param_name'       => 'el_class',
                      'type'             => 'textfield',
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Extra Class', 'plethora-framework'),
                      'description'      => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
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
       * Returns shortcode content
       *
       * @return array
       * @since 1.0
       *
       */
       public function content( $atts, $content = null ) {

          // Extract user input
          extract( shortcode_atts( array( 
              'media_type'          => '',
              'image'               => '',
              'video'               => '',
              'stretchy_ratio'      => 'stretchy_wrapper ratio_16-9',
              'bgimage_valign'      => 'bg_vcenter',
              'transparent_overlay' => '',
              'color_set'           => '',
              'el_class'            => '',
              'css'                 => ''
            ), $atts ) );

          // Prepare final values that will be used in template
          $image_link  = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
          $image_link  = isset($image_link[0]) ? $image_link[0] : '';
          $image_alt   = trim(strip_tags( get_post_meta($image, '_wp_attachment_image_alt', true) ));
          $video_frame = !empty( $video ) ? wp_oembed_get( $video ) : '';

          // Place all values in 'shortcode_atts' variable
          $shortcode_atts = array (
                                  'image_link'          => esc_url( $image_link ), 
                                  'image_alt'           => esc_attr( $image_alt ), 
                                  'video_frame'         => $video_frame, 
                                  'stretchy_ratio'      => esc_attr( $stretchy_ratio ),
                                  'bgimage_valign'      => esc_attr( $bgimage_valign ),
                                  'transparent_overlay' => esc_attr( $transparent_overlay ),
                                  'color_set'           => esc_attr( $color_set ),
                                  'el_class'            => esc_attr( $el_class ),
                                  'css'                 => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),
                                 );
          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

       }
	}
	
 endif;