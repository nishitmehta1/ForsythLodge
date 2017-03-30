<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Pricing Plan shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Pricingplan') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Pricingplan extends Plethora_Shortcode { 

      public $wp_slug                      = 'pricingplan';             // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
      public static $feature_title         = "Pricing Plan Shortcode";  // Feature display title  (string)
      public static $feature_description   = "";                        // Feature display description (string)
      public static $theme_option_control  = true;                      // Will this feature be controlled in theme options panel ( boolean )
      public static $theme_option_default  = true;                      // Default activation option status ( boolean )
      public static $theme_option_requires = array();                   // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                      // Dynamic class construction ? ( boolean )
      public static $dynamic_method        = false;                     // Additional method invocation ( string/boolean | method name or false )
      public static $assets;

      public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                      'base'              => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'              => esc_html__('Pricing Plan', 'plethora-framework'),
                      'description'       => esc_html__('Prices & features', 'plethora-framework'),
                      'class'             => '',
                      'weight'            => 1,
                      'category'          => esc_html__('Teasers & Info Boxes', 'plethora-framework'),
                      'admin_enqueue_js'  => array(), 
                      'admin_enqueue_css' => array(),
                      'icon'              => $this->vc_icon(), 
                      // 'custom_markup'     => $this->vc_custom_markup( 'Pricing Plan' ), 
                      'params'            => $this->params(), 
                      );
        // Add the shortcode
        $this->add( $map );
      }

     /** 
     * Returns shortcode parameters for VC panel
     *
     * @return array
     * @since 1.0
     *
     */
     public function params() {

          $params = array(

                  array(
                      "param_name"    => "image",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                         
                      "heading"       => esc_html__("Display image on top", 'plethora-framework'),      
                      "value"         => array(esc_html__('No', 'plethora-framework')=>0,esc_html__('Yes', 'plethora-framework')=>1),
                  ),
                   array(
                      "param_name"    => "image_url",                                  
                      "type"          => "attach_image",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Image", 'plethora-framework'),      
                      "value"         => '',
                      'dependency'    => array( 
                                            'element' => 'image', 
                                            'value'   => array('1'),  
                                        )
                    ),
                    array(
                      "param_name"       => "image_ratio",
                      "type"             => "dropdown",
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__('Image display ratio', 'plethora-framework'),
                      "value"            => Plethora_Module_Style::get_options_array( array( 
                                            'type'   => 'stretchy_ratios', 
                                            'use_in' => 'vc', 
                                            )),
                      'dependency'       => array( 
                                            'element' => 'image', 
                                            'value'   => array('1'),  
                                        )
                    ),
                   array(
                      "param_name"       => "title",                                  
                      "type"             => "textfield",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "holder"           => "h3",                                               
                      "class"            => "plethora_vc_title",                                                    
                      "heading"          => esc_html__("Title", 'plethora-framework'),
                      "description"      => esc_html__("HTML tags not allowed", 'plethora-framework'),      
                      "value"            => '',                                     
                  ),
                   array(
                      "param_name"       => "subtitle",                                  
                      "type"             => "textfield",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "holder"           => "h4",                                               
                      "class"            => "plethora_vc_title",                                                    
                      "heading"          => esc_html__("Subtitle", 'plethora-framework'),
                      "description"      => esc_html__("HTML tags not allowed", 'plethora-framework'),      
                      "value"            => '',                                     
                  ),
                  array(
                        "param_name"       => "heading_colorset",
                        "type"             => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Title section color set", 'plethora-framework'),
                        "value"            => Plethora_Module_Style::get_options_array( array( 
                                              'type'           => 'color_sets', 
                                              'use_in'         => 'vc', 
                                              'append_options' => array( 'transparent_section' => 'Transparent' )
                                               )),
                    ),
                    array(
                        "param_name"       => "heading_vectorbcg",
                        "type"             => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Title section vector", 'plethora-framework'),
                        "description"      => esc_html__("Title section vector background pattern", 'plethora-framework'),
                        "value"            => array( 
                                            esc_html__('No', 'plethora-framework') => 0,
                                            esc_html__('Yes', 'plethora-framework')  => 1,
                        ),
                    ),
                   array(
                      "param_name"       => "amount",                                  
                      "type"             => "textfield",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "holder"           => "h5",                                               
                      "class"            => "",                                                    
                      "heading"          => esc_html__("Price amount", 'plethora-framework'),
                      "description"      => esc_html__("Larger font size text display. HTML tags not allowed", 'plethora-framework'),      
                      "value"            => '',                                     
                  ),
                   array(
                      "param_name"       => "cycle",                                  
                      "type"             => "textfield",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "holder"           => "h5",                                               
                      "class"            => "",                                                    
                      "heading"          => esc_html__("Price cycle", 'plethora-framework'),
                      "description"      => esc_html__("Smaller font size text display. HTML tags not allowed", 'plethora-framework'),      
                      "value"            => '',                                     
                  ),
                   array(
                        "param_name"       => "pricing_colorset",
                        "type"             => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Price section color set", 'plethora-framework'),
                        "value"            => Plethora_Module_Style::get_options_array( array( 
                                              'type'           => 'color_sets', 
                                              'use_in'         => 'vc', 
                                              'append_options' => array( 'transparent_section' => 'Transparent' )
                                               )),
                        "admin_label"      => false, 
                    ),
                   array(
                        "param_name"       => "features",                                  
                        "type"             => "textarea",                                        
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Features", 'plethora-framework'),
                        "description"      => esc_html__("Add each feature on different line, may use some styling html tags", 'plethora-framework'),
                        "value"            => '',                                     
                  ),
                    array(
                        "param_name"       => "features_colorset",
                        "type"             => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Features section color set", 'plethora-framework'),
                        "value"            => Plethora_Module_Style::get_options_array( array( 
                                              'type'           => 'color_sets', 
                                              'use_in'         => 'vc', 
                                              'append_options' => array( 'transparent_section' => 'Transparent' )
                                               )),
                        "admin_label"       => false, 
                    ),
                    array(
                        "param_name"       => "button",                                  
                        "type"             => "dropdown",                                        
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Display button on bottom", 'plethora-framework'),      
                        "value"            => array( 
                                                esc_html__('No', 'plethora-framework') => 0,
                                                esc_html__('Yes', 'plethora-framework')  => 1,
                                              ),
                    ),
                    array(
                        "param_name"       => "special",                                  
                        "type"             => "dropdown",                                        
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Add beating heart icon", 'plethora-framework'),      
                        "value"            => array( 
                                                esc_html__('No', 'plethora-framework') => 0,
                                                esc_html__('Yes', 'plethora-framework')  => 1,
                                              ),
                    ),
                    array(
                        "param_name"       => "button_text",
                        "type"             => "textfield",                                        
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Button text", 'plethora-framework'),
                        "description"      => esc_html__("HTML tags not allowed", 'plethora-framework'),      
                        "value"            => esc_html__("More", 'plethora-framework'),                               
                        'dependency'       => array( 
                                                'element' => 'button',  
                                                'value'   => array('1'),   
                                              )
                    ),
                    array(
                        "param_name"       => "button_link",
                        "type"             => "vc_link",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Button link", 'plethora-framework'),
                        "value"            => '#',
                        'dependency'       => array( 
                                          'element' => 'button',  
                                          'value'   => array('1'),   
                                          )
                    ),
                    array(
                        "param_name"       => "button_size",                                  
                        "type"             => "dropdown",                                        
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Button size", 'plethora-framework'),      
                        "value"            => array(
                                              'Default'          =>'btn',
                                              'Small'            =>'btn-sm',
                                              'Extra Small'      =>'btn-xs'
                                              ),
                        'dependency'       => array( 
                                              'element' => 'button', 
                                              'value'   => array('1'),   
                                              )
                    ),
                    array(
                        "param_name"       => "button_style",                                  
                        "type"             => "dropdown",                                        
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__("Button style", 'plethora-framework'),      
                        "value"            => array(
                                              'Default'   => 'btn-default',
                                              'Primary'   => 'btn-primary',
                                              'Secondary' => 'btn-secondary',
                                              'White'     => 'btn-white',
                                              'Success'   => 'btn-success',
                                              'Info'      => 'btn-info',
                                              'Warning'   => 'btn-warning',
                                              'Danger'    => 'btn-danger',
                                              'Inverse'    => 'btn-inverse',
                                              ),
                        'dependency'       => array( 
                                              'element' => 'button', 
                                              'value'   => array('1'),   
                                              )
                    ),
                    array(
                        "param_name"       => "button_with_icon",
                        "type"             => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__('Button icon', 'plethora-framework'),
                        "value"            => array( 
                                                esc_html__('No', 'plethora-framework') => 0,
                                                esc_html__('Yes', 'plethora-framework')  => 'with-icon',
                                              ),
                    ),
                    array(
                        "param_name"       => "button_icon",
                        "type"             => "iconpicker",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__('Select icon', 'plethora-framework'),
                        'settings'         => array(
                                                'type'         => 'plethora',
                                                'iconsPerPage' => 56, // default 100, how many icons per/page to display
                                              ),
                        'dependency'       => array( 
                                                'element' => 'button_with_icon', 
                                                'value'   => array('with-icon'),  
                                              )
                    ),
                    array(
                        "param_name"       => "button_icon_align",
                        "type"             => "dropdown",
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"          => esc_html__('Button icon align', 'plethora-framework'),
                        "description"      => ' ',
                        "value"            => array( 
                                                esc_html__('Right', 'plethora-framework')  => '',
                                                esc_html__('Left', 'plethora-framework') =>'icon-left',
                                              ),
                        'dependency'       => array( 
                                                'element' => 'button_with_icon', 
                                                'value'   => array('with-icon'),  
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
       * Returns shortcode content OR content template
       *
       * @return array
       * @since 1.0
       *
       */
       public function content( $atts, $content = null ) {

          // Extract user input
          extract( shortcode_atts( array( 
            'image'             => '',
            'image_url'         => '',
            'image_ratio'       => 'stretchy_wrapper ratio_16-9',
            'title'             => '',
            'subtitle'          => '',
            'heading_colorset'  => 'skincolored_section',
            'heading_vectorbcg' => 0,
            'amount'            => '',
            'cycle'             => '',
            'pricing_colorset'  => 'skincolored_section',
            'features'          => '',
            'features_colorset' => 'skincolored_section',
            'button'            => '',
            'special'           => '',
            'button_text'       => 'More',
            'button_link'       => '',
            'button_size'       => 'btn',
            'button_style'      => 'btn-primary',
            'button_with_icon'  => '',
            'button_icon'       => 'fa fa-caret-right',
            'button_icon_align' => 'icon-right',
            'el_class'          => '',
            'css'               => ''
            ), $atts ) );

          // PREPARE TEMPLATE DATA
          $image_url          = ( $image == '1' && !empty($image_url)) ? wp_get_attachment_image_src( $image_url, 'full' ) : '';
          $image_url          =  $image == '1' && isset($image_url[0]) ? $image_url[0] : '';
          $button_link        =  self::vc_build_link($button_link);
          $button_link['url'] = !empty( $button_link['url'] ) ? $button_link['url'] : '#';
          $heading_vectorbcg  = $heading_vectorbcg == '1' ? PLE_THEME_FEATURES_URI .  '/shortcode/'.$this->wp_slug.'/GPlay.svg' : '';

          // PACK VALUES IN ARRAY VAR
          $shortcode_atts = array (
                                  'features'           => $features,
                                  'image'              => $image,
                                  'image_url'          => esc_url( $image_url ),
                                  'image_ratio'        => esc_attr( $image_ratio ), 
                                  'title'              => $title,
                                  'subtitle'           => $subtitle,
                                  'heading_colorset'   => esc_attr( $heading_colorset ),
                                  'heading_vectorbcg'  => !empty( $heading_vectorbcg ) ? 'style="background-image:url('. esc_url( $heading_vectorbcg ) .');"' : '',
                                  'amount'             => $amount,
                                  'cycle'              => $cycle,
                                  'pricing_colorset'   => esc_attr( $pricing_colorset ),
                                  'features_colorset'  => esc_attr( $features_colorset ),
                                  'button'             => $button,
                                  'special'            => $special,
                                  'button_text'        => esc_attr( $button_text ),
                                  'button_link_url'    => esc_url( $button_link['url'] ),
                                  'button_link_title'  => esc_attr( $button_link['title'] ),
                                  'button_link_target' => !empty( $button_link['target'] ) ? esc_attr( $button_link['target'] ) : '_self',
                                  'button_style'       => esc_attr( $button_style ),
                                  'button_size'        => esc_attr( $button_size ),
                                  'button_with_icon'   => esc_attr( $button_with_icon ),
                                  'button_icon'        => esc_attr( $button_icon ),
                                  'button_icon_align'  => esc_attr( $button_icon_align ),
                                  'el_class'           => esc_attr( $el_class ),
                                  'css'                => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),
                                 );

          if ( $button_icon_align === 'icon-left' ){
            $shortcode_atts["button_icon_left"] = "TRUE";
          } else {
            $shortcode_atts["button_icon_right"] = "TRUE";
          }

          if ( !$features && !$button ) $shortcode_atts['the_offerings'] = "hidden";

          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );
     
       }
  }
  
 endif;