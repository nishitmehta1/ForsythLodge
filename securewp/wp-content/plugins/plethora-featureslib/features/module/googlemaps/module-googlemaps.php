<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2014-2015

File Description: Google Maps API

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Googlemaps') ) {


  /**
   */
  class Plethora_Module_Googlemaps {

      public static $feature_title         = "Google Maps API Module";                                             // FEATURE DISPLAY TITLE
      public static $feature_description   = "Loads Google Maps scripts and handles API information, used in several features"; // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                                                               // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
      public static $theme_option_default  = true;                                                               // DEFAULT ACTIVATION OPTION STATUS 
      public static $theme_option_requires = array();                                                            // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                                                               // DYNAMIC CLASS CONSTRUCTION? 
      public static $dynamic_method        = false;                                                              // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

      public $googlemaps_apikey;
      /* WORDPRESS STATIC PROPERTIES ***/

      /**
       * Create a new instance
       * @param string $api_key Your MailChimp API key
       */
      function __construct() {

          // Add theme options tab
          add_filter( 'plethora_themeoptions_modules', array( $this, 'theme_options_tab'), 10);

          // Get saved GMap API key value
          $this->googlemaps_apikey = Plethora_Theme::option( THEMEOPTION_PREFIX .'googlemaps_apikey', '' );

          // Add admin notice in case the Google Map API option is empty
          $notice  = esc_html__( 'Note that as of June 22, 2016 the Google Maps demands an API key to function properly. ', 'plethora-framework' );
          $notice .= sprintf( esc_html__( 'Please visit %1$sTheme Options > Add Ons & Modules > Google Maps API%2$s to set an API Key.', 'plethora-framework' ), '<strong>', '</strong>' );
          $args = array(
              'condition' => ( ( empty( $this->googlemaps_apikey ) ) ? true : false ),
              'title'     => esc_html__( 'Google Maps API key is missing', 'plethora-framework' ),
              'notice'    => $notice,
              'type'    => 'warning',
              'links'     => array(
                array( 
                  'href'        => 'http://googlegeodevelopers.blogspot.gr/2016/06/building-for-scale-updates-to-google.html',
                  'anchor_text' => esc_html__( 'Read more on Google Geo Developers blog', 'plethora-framework' ),
                ),
                array( 
                  'href'        => 'https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key',
                  'anchor_text' => esc_html__( 'Read more on Google Geo Developers blog', 'plethora-framework' ),
                )
              )
          );
          Plethora_Theme::add_admin_notice( 'module_googlemaps_apikey_not_found', $args );
          
           // Add related scripts
         add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1);   
      }

      function enqueue_scripts() {

          $libdir_js    = PLE_CORE_ASSETS_URI . '/js/libs/';
          $min_suffix   = Plethora_Theme::is_developermode() ? '' : '.min';

          // Google Maps ( Initial use: HealthFlex )
          wp_register_script( 'gmap', 'https://maps.googleapis.com/maps/api/js?key='. $this->googlemaps_apikey .'', array(), NULL, true);                    // SCRIPT - Google Maps 
          // Google Maps ( Initial use: HealthFlex )
          wp_register_script( ASSETS_PREFIX .'-gmap-init', $libdir_js .'googlemaps/googlemaps' . $min_suffix . '.js', array( 'gmap', ASSETS_PREFIX . '-init'), NULL, true);
      }

      /*** WORDPRESS OPTIONS */

      function theme_options_tab( $sections ) {

        $getapi_link = 'https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key';

        $sections[] = array(
          'subsection' => true,
          'title'      => esc_html__('Google Maps API', 'plethora-framework'),
          'heading'    => esc_html__('GOOGLE MAPS API', 'plethora-framework'),
          'fields'     => array(

          // MAILCHIMP API SETTINGS

            array(
              'id'       => THEMEOPTION_PREFIX .'googlemaps_apikey',
              'type'     => 'text',
              'title'    => esc_html__('Google Maps API Key', 'plethora-framework'),
              'description' => '<a href="'. esc_url( $getapi_link ) .'" target="_blank"><strong>'. esc_html__( 'Get a Google Maps API key', 'plethora-framework' ) .'</strong></a>',
              'validate' => 'no_special_chars',
              'default'  => ''
              ),
          )
        );

        return $sections;
      }
  }
}