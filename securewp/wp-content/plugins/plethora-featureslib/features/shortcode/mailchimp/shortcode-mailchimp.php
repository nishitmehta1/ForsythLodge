<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

MailChimp Form Shortcode ( alias for MailChimp for Wordpress plugin shortcode )

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_NewsletterForm') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Mailchimp extends Plethora_Shortcode { 

      public static $feature_name          = "MailChimp Form";                // FEATURE DISPLAY TITLE 
      public static $feature_title         = "MailChimp Form Shortcode";      // FEATURE DISPLAY TITLE 
      public static $feature_description   = "Requires MailChimp For WP plugin";        // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                             // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                             // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array();                          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                             // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;                            // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public $wp_slug                      = 'mailchimp';                     // THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $assets;


      public function __construct() {

                  // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'          => sprintf( esc_html__( '%s', 'plethora-framework' ), self::$feature_name ),
                      'description'   => sprintf( esc_html__( '%s', 'plethora-framework'), self::$feature_description ),
                      'class'         => '',
                      'weight'        => 1,
                      'category'      => esc_html__('Plethora Shortcodes', 'plethora-framework'),
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( self::$feature_name ), 
                      'params'        => $this->params(), 
                      );
          $this->add( $map );         // ADD ΤΗΕ SHORTCODE
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
                      "param_name"    => "mailchimp_form",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      "heading"       => esc_html__("MailChimp Form", 'plethora-framework'),
                      "description"   => esc_html__("Select a form you created with MailChimp for WP plugin", 'plethora-framework'),      
                      "value"         => $this->get_mailchip_forms(),                                 
                      "admin_label"   => false,  
                      "save_always"   => true
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

    public function get_mailchip_forms() {

      if ( function_exists( 'mc4wp_get_forms' ) ) {

        $forms = mc4wp_get_forms();
        $return = array();
        if ( !empty( $forms ) ) {

          foreach ( $forms as $form ) {

            $label          = $form->name;
            $value          = $form->ID;
            $return[$label] = $value;
          }
          
        } else {

            $label          = esc_html__( 'No forms found. Go to "MailChimp For WP > Forms" to create one!', 'plethora-framework' ) ;
            $value          = '';
            $return[$label] = $value;
        }

      } else {

            $label          = esc_html__( 'You have to activate MailChimp For WP plugin and create a form to display.', 'plethora-framework' ) ;
            $value          = '';
            $return[$label] = $value;
      }

      return $return;
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
          'mailchimp_form' => '',
          'css'            => '',
        ), $atts ) );


        $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), $this->wp_slug, $atts );
        $return = '<div class="ple_mailchimp wpb_content_element '. esc_attr( $css_class ) .'">';
        if ( !empty( $mailchimp_form ) && function_exists( 'mc4wp_get_forms' ) ) {
          $return .= do_shortcode( '[mc4wp_form id="'.$mailchimp_form.'"]' );
        
        } else {

          return '<div class="text-center">'. sprintf( esc_html__( '%1$sMailChimp For WP%2$s plugin is not active! You should activate it OR just remove this shortcode%3$s...unless you are font of this charming message!', 'plethora-framework'), '<strong>', '</strong>', '<br>' ) .'</div>';
        }
       $return .= '</div>';
       return $return;
    }
  }
 
 endif;