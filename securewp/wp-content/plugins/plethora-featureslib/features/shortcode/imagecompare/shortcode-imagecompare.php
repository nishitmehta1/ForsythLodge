<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Image Compare shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_ImageCompare') ):

	/**
	 * @package Plethora Framework
	 */

	class Plethora_Shortcode_ImageCompare extends Plethora_Shortcode { 

      public $wp_slug                      = 'imagecompare';      // Frontend only script & style files that should be loaded only when this feature is present (script, style, init)
      public static $feature_title         = "Image Compare Shortcode";      // Feature display title  (string)
      public static $feature_description   = "";      // Feature display description (string)
      public static $theme_option_control  = true;      // Will this feature be controlled in theme options panel ( boolean )
      public static $theme_option_default  = true;      // Default activation option status ( boolean )
      public static $theme_option_requires = array();      // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;      // Dynamic class construction ? ( boolean )
      public static $dynamic_method        = false;      // Additional method invocation ( string/boolean | method name or false )
      public static $assets                = array(
                                  array( 'script' => array( 'jquery-event-move')),  // Scripts files - wp_enqueue_script
                                  array( 'script' => array( 'twentytwenty')),       // Scripts files - wp_enqueue_script
                                  array( 'style'  => array( 'twentytwenty')),       // Style files - wp_register_style
                              );
      
      public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                      'base'              => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'              => esc_html__('Image Compare', 'plethora-framework'),
                      'description'       => esc_html__('Highlight differences between two images', 'plethora-framework'),
                      'class'             => '',
                      'weight'            => 1,
                      'category'          => esc_html__('Teasers & Info Boxes', 'plethora-framework'),
                      'admin_enqueue_js'  => array(), 
                      'admin_enqueue_css' => array(),
                      'icon'              => $this->vc_icon(), 
                      // 'custom_markup'     => $this->vc_custom_markup( 'Image Compare' ), 
                      'params'            => $this->params(), 
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
                      "param_name"    => "before_image",                                  
                      "type"          => "attach_image",                                        
                      "holder"        => "img",                                               
                      "class"         => "plethora_vc_image",                                                 
                      "heading"       => esc_html__("Before Image", 'plethora-framework'),      
                      "value"         => '',
                      "description"   => esc_html__("Upload before-image", 'plethora-framework'),       
                  ),
                  array(
                      "param_name"    => "after_image",                                  
                      "type"          => "attach_image",                                        
                      "class"         => "vc_hidden",                                                 
                      "heading"       => esc_html__("After Image", 'plethora-framework'),      
                      "value"         => '',
                      "description"   => esc_html__("Upload after-image", 'plethora-framework'),       
                  ),
                  array(
                      "param_name"       => "default_offset",                                  
                      "type"             => "textfield",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Image offset", 'plethora-framework'),      
                      "value"            => '0.5',
                      "description"      => esc_html__("Set the default split offset", 'plethora-framework'),       
                  ),
                  array(
                      "param_name"    => "orientation",                                  
                      "type"          => "dropdown",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"       => esc_html__("Orientation", 'plethora-framework'),      
                      "value"         => array(
                                            esc_html__('Horizontal', 'plethora-framework') =>'horizontal', 
                                            esc_html__('Vertical', 'plethora-framework')   =>'vertical'
                                          ),
                      "description"   => esc_html__("Set the orientation of the effect", 'plethora-framework'),       
                      "admin_label"   => false,                                              
                  ),

                  array(
                      'param_name'  => 'el_class',
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'type'        => 'textfield',
                      'heading'     => esc_html__('Extra Class', 'plethora-framework'),
                      'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
                  ),

                  // DESIGN OPTIONS TAB STARTS >>>>
                  array(
                      'param_name'  => 'css',
                      'type'        => 'css_editor',
                      'group'       => esc_html__('Design Options', 'plethora-framework'),                                              
                      'heading'     => esc_html__('Design Options', 'plethora-framework'),
                  ),
                  // <<<< DESIGN OPTIONS TAB ENDS
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

          // EXTRACT USER INPUT
          extract( shortcode_atts( array(
            'default_offset' => '0.5', 
            'orientation'    => 'horizontal',
            'before_image'   => '',
            'after_image'    => '',
            'css'            => '',
            'el_class'       => '',
            ), $atts ) );

          // Add Init Script
          Plethora_Theme::enqueue_init_script( array(
                      'handle'   => 'twentytwenty',
                      'script'   => $this->init_script_twentytwenty()
          ));

          // VC CSS FILTERING >>>
          $css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts );

          // Prepare final values that will be used in template
          $before_image = (!empty($before_image)) ? wp_get_attachment_image_src( $before_image, 'full' ) : '';
          $after_image  = (!empty($after_image)) ? wp_get_attachment_image_src( $after_image, 'full' ) : '';

          // Place all values in 'shortcode_atts' variable
          $shortcode_atts = array (
                                  'content'        => $content,  
                                  'default_offset' => $default_offset,
                                  'orientation'    => $orientation, 
                                  'before_image'   => esc_url( $before_image[0]), 
                                  'after_image'    => esc_url( $after_image[0]), 
                                  'extra_class'    => !empty( $el_class ) ? esc_attr( $el_class ) : '', 
                                  'css'            => !empty( $css ) ? esc_attr( $css ) : '', 
                                 );

          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

       }

       public function init_script_twentytwenty() {
          
        return '
        <script type="text/javascript">
        jQuery(function($) { "use strict"; $(window).load(function(){ $.fn.twentytwenty && $(\'.twentytwenty-container\') && $(\'.twentytwenty-container\').twentytwenty({ default_offset_pct: 0.5, orientation: \'horizontal\' }); }); });        
        </script>
        ';
       }
	}
	
 endif;