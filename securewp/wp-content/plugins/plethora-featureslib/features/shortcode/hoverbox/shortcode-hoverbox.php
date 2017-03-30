<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Hover Box Shortcode Base Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Hoverbox') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Hoverbox extends Plethora_Shortcode { 

    public static $feature_title         = "Hover Box Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public $wp_slug                      =  'hoverbox';           // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
    public static $assets;

    public $default_param_values;
   
    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'        => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'        => esc_html__("Hover Box", 'plethora-framework'), 
                    'description' => esc_html__('with image background', 'plethora-framework'), 
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

        $params_index['bcg_img'] = array( 
              "param_name"       => "bcg_img",
              "type"             => "attach_image",                                        
              "heading"          => esc_html__("Background Image", 'plethora-framework'),
        );
        $params_index['logo_img'] = array( 
              "param_name"       => "logo_img",
              "type"             => "attach_image",                                        
              "heading"          => esc_html__("Logo Image", 'plethora-framework'),
        );
        $params_index['title'] = array( 
              'param_name'  => 'title',
              'type'        => 'textfield',
              'heading'     => esc_html__('Title', 'plethora-framework'),
        );
        $params_index['content'] = array( 
              'param_name'  => 'content',
              'type'        => 'textarea_html',
              'heading'     => esc_html__('Description', 'plethora-framework'),
        );
        $params_index['link'] = array( 
              "param_name"       => "link",
              "type"             => "vc_link",
              "class"            => "vc_hidden", 
              "heading"          => esc_html__("Box Link", 'plethora-framework'),
        );
        $params_index['stretchy_ratio'] = array( 
              "param_name"    => "stretchy_ratio",
              "type"          => "dropdown",
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"       => esc_html__('Box Display Ratio', 'plethora-framework'),
              "value"         => Plethora_Module_Style::get_options_array( array( 
                                'type'            => 'stretchy_ratios', 
                                'use_in'          => 'vc',
                                ) 
              ),
              "description"   => esc_html__('The box will be displayed using the ratio selected.', 'plethora-framework'),       
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

        $params_config = array();
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

      // Prepare link for template
      $link        =  self::vc_build_link($link);
      $link_url    = !empty( $link['url'] ) ? trim($link['url']) : '#';
      $link_target = !empty( $link['target'] ) ? trim($link['target']) : '';
      $link_rel    = !empty( $link['rel'] ) ? trim($link['rel']) : '';
      // Prepare background image for template
      $bcg     = (!empty($bcg_img)) ? wp_get_attachment_image_src( $bcg_img, 'full' ) : '';
      $bcg_url = isset($bcg[0]) ? $bcg[0] : '';
      // Prepare logo image for template
      $logo     = (!empty($logo_img)) ? wp_get_attachment_image_src( $logo_img, 'full' ) : '';
      $logo_url = isset($logo[0]) ? $logo[0] : '';

      // Place all values in 'shortcode_atts' variable
      $shortcode_atts = array (
                              'title'          => wp_kses($title, Plethora_Theme::allowed_html_for( 'heading' ) ),  
                              'content'        => $content,
                              'bcg_url'        => esc_url( $bcg_url ),
                              'logo_url'       => esc_url( $logo_url ),
                              'link_url'       => esc_url( $link_url ),
                              'link_target'    => esc_attr( $link_target ),
                              'link_rel'       => esc_attr( $link_rel ),
                              'stretchy_ratio' => esc_attr( $stretchy_ratio ),
                              'el_class'       => esc_attr( $el_class ),
                              'css'            => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),
                             );

      return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );
    }
	}
	
 endif;