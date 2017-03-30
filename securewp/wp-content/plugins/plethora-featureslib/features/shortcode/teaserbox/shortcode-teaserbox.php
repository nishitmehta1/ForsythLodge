<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Features Teaser shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Teaserbox') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Teaserbox extends Plethora_Shortcode { 

      public static $feature_title          = "Teaser Box Shortcode"; // FEATURE DISPLAY TITLE 
      public static $feature_description    = "";                     // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control   = true;                   // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default   = true;                   // DEFAULT ACTIVATION OPTION STATUS 
      public static $theme_option_requires  = array();                // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct      = true;                   // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method         = false;                  // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public $wp_slug                       = 'teaserbox';            // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $assets;

      public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                      'base'             => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'              => esc_html__('Teaser Box', 'plethora-framework'),
                      'description'       => esc_html__('Image/icon, title, subtitle and content', 'plethora-framework'),
                      'class'             => '',
                      'weight'            => 1,
                      'admin_enqueue_js'  => array(), 
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( 'Teaser Box' ), 
                      'params'        => $this->params(), 
                      );
        // Add the shortcode
        $this->add( $map );
      }

      /** 
      * Returns shortcode parameters INDEX for VC panel
      *
      * @return array
      * @since 1.0
      *
      */
      public function params_index() {

        $params_index['title'] = array( 
                      "param_name"    => "title",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Title ( no HTML please )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
        );

        $params_index['subtitle'] = array( 
                      "param_name"    => "subtitle",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h4",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Subtitle ( no HTML please )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
        );

        $params_index['content'] = array( 
                      "param_name"    => "content",                                  
                      "type"          => "textarea",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      "heading"       => esc_html__("Paragraph ( may use basic HTML elements )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
        );

        $params_index['teaser_link'] = array( 
                        "param_name"    => "teaser_link",
                        "type"          => "vc_link",
                        "holder"        => "",
                        "class"         => "vc_hidden", 
                        "heading"       => esc_html__("Teaser Link", 'plethora-framework'),
                        "value"         => '#',
                        "admin_label"   => false                                               
        );

        $params_index['link_title'] = array( 
                        "param_name"    => "link_title",
                        "type"          => "checkbox",
                        "heading"       => esc_html__('Link Title', 'plethora-framework'),
                        "value"         => array( 
                                              esc_html__('Yes', 'plethora-framework') => '1',
                         ),
                        "description"   => esc_html__("Check this if you want a linked title", 'plethora-framework'),
        );

        $params_index['boxed_styling'] = array( 
                      "param_name"    => "boxed_styling",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Boxed styling', 'plethora-framework'),
                      "value"         => array( 
                                            esc_html__('No boxed styling', 'plethora-framework') =>'',
                                            esc_html__('Normal boxed', 'plethora-framework')  => 'boxed',
                                            esc_html__('Special boxed', 'plethora-framework')  => 'boxed_special',
                        ),
                      "description"   => esc_html__("Depending on the selection, it affects padding and or border lines of the whole box", 'plethora-framework'),
        );

        $params_index['media_type'] = array( 
                        "param_name"    => "media_type",
                        "type"          => "dropdown",
                        "heading"       => esc_html__('Select media type', 'plethora-framework'),
                        "holder"        => "",                                               
                        "class"         => "vc_hidden", 
                        "admin_label"   => false,                                             
                        "value"         => array( 
                          esc_html__('Image', 'plethora-framework') =>'image',
                          esc_html__('Icon', 'plethora-framework')  => 'icon'
                          ),
        );

        $params_index['icon'] = array( 
                        "param_name"    => "icon",
                        "type"          => "iconpicker",
                        "holder"        => "",                                               
                        "class"         => "vc_hidden", 
                        "admin_label"   => false,                                             
                        "heading"       => esc_html__('Select icon', 'plethora-framework'),
                        "description"   => esc_html__("Select icon to display.", 'plethora-framework'),
                        'settings'   => array(
                          'type'         => 'plethora',
                          'iconsPerPage' => 56, // default 100, how many icons per/page to display
                        ),
                        'dependency'    => array( 
                                            'element' => 'media_type', 
                                            'value'   => array('icon'),  
                                        )
        );

        $params_index['image'] = array( 
                      "param_name"    => "image",                                  
                      "type"          => "attach_image",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Image", 'plethora-framework'),      
                      "value"         => '',
                      "admin_label"   => false,                                              
                      'dependency'    => array( 
                                            'element' => 'media_type', 
                                            'value'   => array('image'),  
                                        )
        );

        $params_index['image_hover_effect'] = array( 
                        "param_name"    => "image_hover_effect",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Enable Image Hover Effect", 'plethora-framework'),
                        "value"         => array( 
                                          'Disabled' => 'disabled', 
                                          'Enabled'  => 'enabled'
                                           ),
                        "description"   => esc_html__("Enable a subtle opacity change and vertical movement effect when hovered", 'plethora-framework'),
                        "admin_label"   => false, 
        );

        $params_index['media_colorset'] = array( 
                        "param_name"    => "media_colorset",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Media section color set", 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc', 
                                          'prepend_default' => true, 
                                          'append_options'  => array( 'transparent' => esc_html__('Transparent', 'plethora-framework') )
                                           )),
                        "description"   => esc_html__("Choose a color setup ONLY for the icon section. Remember: all color sets above can be configured via the theme options panel", 'plethora-framework'),
                        "admin_label"   => false, 
        );

        $params_index['media_ratio'] = array( 
                      "param_name"    => "media_ratio",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Media display ratio', 'plethora-framework'),
                      "value"         => Plethora_Module_Style::get_options_array( array( 
                                        'type' => 'stretchy_ratios', 
                                        'use_in' => 'vc', 
                                        'prepend_default' => true 
                                        )),            
        );

        $params_index['text_colorset'] = array( 
                        "param_name"    => "text_colorset",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Text section color set", 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc', 
                                          'prepend_default' => true, 
                                          'append_options'  => array( 'transparent' => esc_html__('Transparent', 'plethora-framework') )
                                           )),
                        "description"   => esc_html__("Choose a color setup for this element. Remember: all color sets above can be configured via the theme options panel", 'plethora-framework'),
                        "admin_label"   => false, 
        );

        $params_index['text_boxed_styling'] = array( 
                      "param_name"    => "text_boxed_styling",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Text section boxed styling', 'plethora-framework'),
                      "value"         => array( 
                                            esc_html__('No boxed styling', 'plethora-framework') =>'',
                                            esc_html__('Boxed', 'plethora-framework')  => 'boxed',
                                            esc_html__('Boxed Special', 'plethora-framework')  => 'boxed_special',
                        ),
                      "description"   => esc_html__("Depending on the selection, it affects inner padding of the text section of the box", 'plethora-framework'),
        );

        $params_index['text_align'] = array( 
                      "param_name"    => "text_align",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Contents align', 'plethora-framework'),
                      "value"         => array( 
                                              esc_html__('Centered', 'plethora-framework') => 'text-center',
                                              esc_html__('Left', 'plethora-framework')     =>'text-left',                                              
                                              esc_html__('Right', 'plethora-framework')    => 'text-right',
                                              esc_html__('Inherit', 'plethora-framework')  =>'',
                        ),
        );

        $params_index['button_display'] = array( 
                      "param_name"    => "button_display",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h4",                                               
                      "class"         => "vc_hidden",                                         
                      "heading"       => esc_html__("Display button on bottom", 'plethora-framework'),      
                      "value"         => array('No'=>0,'Yes'=>1),
                      "admin_label"   => false,                                              
        );

        $params_index['button_text'] = array( 
                      "param_name"    => "button_text",
                      "type"          => "textfield",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      "heading"       => esc_html__("Button text ( no HTML please )", 'plethora-framework'),
                      "value"         => 'More',                                     
                      "admin_label"   => false,                                             
                       'dependency'    => array( 
                                          'element' => 'button_display',  
                                          'value'   => array('1'),   
                                          )
        );

        $params_index['button_style'] = array( 
                      "param_name"    => "button_style",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h4",                                               
                      "class"         => "vc_hidden",                                         
                      "heading"       => esc_html__("Button styling", 'plethora-framework'),      
                      "value"         => array(
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
                      "admin_label"   => false,                                              
                      'dependency'    => array( 
                                          'element' => 'button_display', 
                                          'value'   => array('1'),   
                                      )
        );

        $params_index['same_height'] = array( 
                        "param_name"    => "same_height",
                        "type"          => "dropdown",
                        "heading"       => esc_html__('Same Height', 'plethora-framework'),
                        "holder"        => "",                                               
                        "class"         => "vc_hidden", 
                        "admin_label"   => false,                                             
                        "value"         => array( 
                          esc_html__('No', 'plethora-framework') =>'',
                          esc_html__('Yes', 'plethora-framework')  => 'same_height_col'
                          ),
                        "description"   => esc_html__("Turn this to Yes if you want this box to be of equal height to any other box in its row that has this also turned to Yes.", 'plethora-framework'),
        );

        $params_index['el_class'] = array( 
              'param_name'  => 'el_class',
              'type'        => 'textfield',
              'heading'     => esc_html__('Extra Class', 'plethora-framework'),
              'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
        );

        $params_index['css'] = array( 
                      "param_name"    => "css",
                      "type"          => "css_editor",
                      'group'         => esc_html__( 'Design options', 'plethora-framework' ),
                      "heading"       => esc_html__('CSS box', 'plethora-framework'),
        );

        // Fields added for use on Avoir theme -> START
        $params_index['orientation_style'] = array( 
                      "param_name"    => "orientation_style",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Orientation Style', 'plethora-framework'),
                      "value"         => array( 
                                            esc_html__('Vertical', 'plethora-framework') =>'vertical',
                                            esc_html__('Horizontal', 'plethora-framework')  => 'horizontal',
                        ),
                      "description"   => esc_html__("Two different kinds of styling", 'plethora-framework'),
        );

        $params_index['box_colorset'] = array( 
                        "param_name"    => "box_colorset",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Box color set", 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc', 
                                          'prepend_default' => true, 
                                          'append_options'  => array( 'transparent' => esc_html__('Transparent', 'plethora-framework') )
                                           )),
                        "description"   => esc_html__("Choose a color setup for the whole box.", 'plethora-framework'),
                        "admin_label"   => false, 
        );

        $params_index['media_style'] = array( 
                      "param_name"    => "media_style",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Media section styling', 'plethora-framework'),
                      "value"         => array( 
                                            esc_html__('No styling', 'plethora-framework') =>'',
                                            esc_html__('Rounded', 'plethora-framework')  => 'rounded',
                                            esc_html__('Circle', 'plethora-framework')  => 'circled',
                        ),
        );
        // Fields added for use on Avoir theme -> END

        return $params_index;
     }

     /** 
     * Configure parameters displayed
     * Will be displayed all items from params_index() with identical 'id'
     * This method should be used for extension class overrides
     *
     * @return array
     */
     public function params_config() {

        $params_config = array(
            array( 
              'id'         => 'orientation_style', 
              'default'    => '',
              'field_size' => '',
              ),
            array( 
              'id'         => 'title', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'subtitle', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'content', 
              'default'    => '',
              'field_size' => '',
              ),
            array( 
              'id'         => 'box_colorset', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'text_align', 
              'default'    => 'text-center',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'boxed_styling', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'teaser_link', 
              'default'    => '#',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'media_type', 
              'default'    => 'image',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'icon', 
              'default'    => 'fa fa-th',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'image', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'media_colorset', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'media_ratio', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'media_style', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'el_class', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'css', 
              'default'    => '',
              'field_size' => '',
              ),
        );

        return $params_config;
     }

     /** 
     * Returns shortcode content OR content template
     */
     public function content( $atts, $content = null ) {

        // EXTRACT USER INPUT
        extract( shortcode_atts( $this->get_default_param_values(), $atts ) );

        // Prepare final values that will be used in template
        $image       = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
        $image       = isset($image[0]) ? $image[0] : '';
        $teaser_link = !empty($teaser_link) ? self::vc_build_link($teaser_link) : array();
        // Place all values in 'shortcode_atts' variable
        $shortcode_atts = array (
                                'content'            => wp_kses_post( $content ), 
                                'title'              => esc_html( $title ), 
                                'subtitle'           => esc_html( $subtitle ), 
                                'orientation_style'  => esc_attr( $orientation_style ),
                                'box_colorset'       => esc_attr( $box_colorset ),
                                'icon'               => esc_attr( $icon ), 
                                'image'              => esc_url( $image ),
                                'media_type_image'   => $media_type === 'image' && ! empty( $image ) ? true : false, 
                                'media_type_icon'    => $media_type === 'icon' && ! empty( $icon ) ? true : false, 
                                'media_colorset'     => esc_attr( $media_colorset ), 
                                'media_ratio'        => esc_attr( $media_ratio ), 
                                'no_media_ratio'     => empty( $media_ratio ) ? true : false, 
                                'media_style'        => esc_attr( $media_style ),
                                'text_align'         => esc_attr( $text_align ), 
                                'boxed_styling'      => esc_attr( $boxed_styling ),
                                'teaser_link_url'    => ! empty( $teaser_link['url'] ) ? esc_url( $teaser_link['url'] ) : '',
                                'teaser_link_title'  => ! empty( $teaser_link['url'] ) ? esc_attr( trim( $teaser_link['title']) ) : '',
                                'teaser_link_target' => ! empty( $teaser_link['url'] ) ? esc_attr( trim( $teaser_link['target']) ) : '',
                                'el_class'           => esc_attr( $el_class ),
                                'css'                => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),
                               );

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );
     }
  }
  
 endif;