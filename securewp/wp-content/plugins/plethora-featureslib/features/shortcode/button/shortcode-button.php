<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Button shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Button') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Button extends Plethora_Shortcode { 

    public static $feature_title         = "Button Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public $wp_slug                      =  'button';           // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
    public static $assets;

    public $default_param_values;
   
    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'        => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'        => esc_html__("PL Button", 'plethora-framework'), 
                    'description' => esc_html__('with icon and styling settings', 'plethora-framework'), 
                    'class'       => '', 
                    'weight'      => 1, 
                    'icon'        => $this->vc_icon(), 
                    // 'custom_markup' => $this->vc_custom_markup( 'Button' ), 
                    'params'      => $this->params(), 
                    );
        // Add the shortcode
        $this->add( $map );

    }

    /** 
    * Returns shortcode parameters INDEX for VC panel
    * @return array
    */
    public function params_index() {

        $params_index['button_text'] = array( 
              "param_name"       => "button_text",
              "type"             => "textfield",                                        
              "holder"           => "h3",                                               
              "class"            => "plethora_vc_title",                                                    
              "heading"          => esc_html__("Button text ( no HTML please )", 'plethora-framework'),
        );

        $params_index['button_link'] = array( 
              "param_name"       => "button_link",
              "type"             => "vc_link",
              "class"            => "vc_hidden", 
              "heading"          => esc_html__("Button link", 'plethora-framework'),
        );

        $params_index['button_size'] = array( 
              "param_name"       => "button_size",                                  
              "type"             => "dropdown",                                        
              "heading"          => esc_html__("Button size", 'plethora-framework'),      
              "value"            => array(
                                      'Default'     =>'',
                                      'Large'       =>'btn-lg',
                                      'Small'       =>'btn-sm',
                                      'Extra Small' =>'btn-xs'
                                      ),
        );

        $params_index['button_align'] = array( 
              "param_name"       => "button_align",                                  
              "type"             => "dropdown",                                        
              "heading"          => esc_html__("Button align", 'plethora-framework'),      
              "value"            => array(
                                      'Left'   => 'text-left',
                                      'Center' => 'text-center',
                                      'Right'  => 'text-right'
                                      ),
        );

        $params_index['button_style'] = array( 
              "param_name"       => "button_style",                                  
              "type"             => "dropdown",                                        
              "holder"           => "",                                               
              "class"            => "vc_hidden",                                         
              "heading"          => esc_html__("Button Color", 'plethora-framework'),      
              "value"            => array(
                    esc_html__( 'Default', 'plethora-framework' )   => 'btn-default',
                    esc_html__( 'Primary', 'plethora-framework' )   => 'btn-primary',
                    esc_html__( 'Secondary', 'plethora-framework' ) => 'btn-secondary',
                    esc_html__( 'Dark', 'plethora-framework' )      => 'btn-dark',
                    esc_html__( 'Light', 'plethora-framework' )     => 'btn-light',
                    esc_html__( 'White', 'plethora-framework' )     => 'btn-white',
                    esc_html__( 'Black', 'plethora-framework' )     => 'btn-black',
                    esc_html__( 'Success', 'plethora-framework' )   => 'btn-success',
                    esc_html__( 'Info', 'plethora-framework' )      => 'btn-info',
                    esc_html__( 'Warning', 'plethora-framework' )   => 'btn-warning',
                    esc_html__( 'Danger', 'plethora-framework' )    => 'btn-danger',
                                      ),
        );

        $params_index['button_style2'] = array( 
              "param_name"       => "button_style2",                                  
              "type"             => "dropdown",                                        
              "class"            => "vc_hidden",                                         
              "heading"          => esc_html__("Button Style", 'plethora-framework'),      
              "value"            => array(
                                    esc_html__( 'Default', 'plethora-framework' )     => 'btn',
                                    esc_html__( 'Inverted', 'plethora-framework' )    => 'btn btn-inv',
                                    esc_html__( 'Link Button', 'plethora-framework' ) => 'btn-link',
                                    ),
        );

        $params_index['button_inline'] = array( 
              "param_name"       => "button_inline",                                  
              "type"             => "dropdown",                                        
              "class"            => "vc_hidden",                                         
              "heading"          => esc_html__("Inline/Block placement", 'plethora-framework'),      
              "value"            => array(
                                      esc_html__('Inline-Block', 'plethora-framework') => '',
                                      esc_html__('Inline', 'plethora-framework')  => 'btn_inline',
                                      esc_html__('Block', 'plethora-framework')  => 'btn_block',
                                    ),
        );

        $params_index['button_with_icon'] = array( 
              "param_name"    => "button_with_icon",
              "type"          => "dropdown",
              "heading"       => esc_html__('Button icon', 'plethora-framework'),
              "value"         => array( 
                                    esc_html__('No', 'plethora-framework') => 0,
                                    esc_html__('Yes', 'plethora-framework')  => 'with-icon',
                                ),
        );

        $params_index['button_icon'] = array( 
              "param_name" => "button_icon",
              "type"       => "iconpicker",
              "class"      => "", 
              'group'      => esc_html__( 'Icon', 'plethora-framework' ),
              "heading"    => esc_html__('Select icon', 'plethora-framework'),
              'settings'   => array(
                                'type'         => 'plethora',
                                'iconsPerPage' => 56, // default 100, how many icons per/page to display
                              ),
              'dependency' => array( 
                                  'element' => 'button_with_icon', 
                                  'value'   => array('with-icon'),  
                                        )
        );

        $params_index['button_icon_align'] = array( 
              "param_name"  => "button_icon_align",
              "type"        => "dropdown",
              'group'       => esc_html__( 'Icon', 'plethora-framework' ),
              "heading"     => esc_html__('Button icon align', 'plethora-framework'),
              "value"       => array( 
                  esc_html__('Left', 'plethora-framework')   => 'icon-left',
                  esc_html__('Center', 'plethora-framework') => '',
                  esc_html__('Right', 'plethora-framework')  => 'icon-right',
                ),
              'dependency'  => array( 
                                    'element' => 'button_with_icon', 
                                    'value'   => array('with-icon'),  
                                )
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
              'id'         => 'button_text', 
              'default'    => esc_html__('More', 'plethora-framework'),
              'field_size' => '',
              ),
            array( 
              'id'         => 'button_link', 
              'default'    => '#',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'button_size', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'button_align', 
              'default'    => 'text-left',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'button_style', 
              'default'    => 'btn-default',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'button_style2', 
              'default'    => 'btn',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'button_inline', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'button_with_icon', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'el_class', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'button_icon', 
              'default'    => '',
              'field_size' => '',
              ),
            array( 
              'id'         => 'button_icon_align', 
              'default'    => '',
              'field_size' => '',
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
    *
    * @return array
    * @since 1.0
    *
    */
    public function content( $atts, $content = null ) {

        // Extract user input
        extract( shortcode_atts( $this->get_default_param_values(), $atts ) );

        // Prepare final values that will be used in template
        $button_link        =  self::vc_build_link($button_link);
        $button_link['url'] = !empty( $button_link['url'] ) ? $button_link['url'] : '#';
        $button_with_icon   = $button_with_icon != '0' && !empty($button_with_icon) ? $button_with_icon : '';
        //$button_inline      = $button_inline != '0' && !empty($button_inline) ? $button_inline : '';

        // Place all values in 'shortcode_atts' variable
        $shortcode_atts = array (
                                'btn_text'       => esc_attr($button_text),  
                                'btn_url'        => esc_url( $button_link['url'] ),
                                'btn_title'      => esc_attr( $button_link['title'] ),
                                'btn_align'      => esc_attr( $button_align ),
                                'btn_target'     => !empty( $button_link['target'] ) ? esc_attr( $button_link['target'] ) : '_self',
                                'btn_style'      => esc_attr( $button_style ), 
                                'btn_style2'     => esc_attr( $button_style2 ), 
                                'button_inline'  => esc_attr( $button_inline ), 
                                'btn_size'       => esc_attr( $button_size ), 
                                'btn_with_icon'  => esc_attr( $button_with_icon ), 
                                'btn_icon'       => esc_attr( $button_icon ),
                                'btn_icon_align' => esc_attr( $button_icon_align ),
                                'el_class'       => esc_attr( $el_class ),
                                'css'            => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),
                               );

        if ( $button_with_icon === 'with-icon' ){
          if ( $button_icon_align === 'icon-left' ){
            $shortcode_atts["btn_icon_align_left"] = TRUE;
          } else {
            $shortcode_atts["btn_icon_align_right"] = TRUE;
          }
        }

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

    }

	}
	
 endif;