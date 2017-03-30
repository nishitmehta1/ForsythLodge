<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2014-2015

File Description: MailChimp API
Based on: https://github.com/drewm/mailchimp-api, by Drew McLellan 
Version: 1.0.1

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Mailchimp') ) {

  /**
   * Super-simple, minimum abstraction MailChimp API v2 wrapper
   * 
   * Uses WP Filesystem.
   * This probably has more comments than code.
   *
   * Contributors:
   * Michael Minor <me@pixelbacon.com>
   * Lorna Jane Mitchell, github.com/lornajane
   * 
   * @author Drew McLellan <drew.mclellan@gmail.com> 
   * @version 1.1.1
   */
  class Plethora_Module_Mailchimp
  {
      private $api_key;
      private $api_endpoint = 'https://<dc>.api.mailchimp.com/2.0';
      private $verify_ssl   = false;

      /*** WORDPRESS STATIC PROPERTIES */

      public static $feature_title         = "MailChimp API Module";                                             // FEATURE DISPLAY TITLE
      public static $feature_description   = "Loads MailChimp newsletter service API, used in several features"; // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                                                               // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
      public static $theme_option_default  = true;                                                               // DEFAULT ACTIVATION OPTION STATUS 
      public static $theme_option_requires = array();                                                            // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                                                               // DYNAMIC CLASS CONSTRUCTION? 
      public static $dynamic_method        = false;                                                              // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

      /* WORDPRESS STATIC PROPERTIES ***/

      /**
       * Create a new instance
       * @param string $api_key Your MailChimp API key
       */
      function __construct( $api_key = '' )
      {
          if ( function_exists( "is_admin" ) && is_admin() ) { 
            // Set theme options tab for media panel
            add_filter( 'plethora_themeoptions_modules', array( $this, 'theme_options_tab'), 10);
          }
          if ( $api_key === '' ){  return null;  }
          $this->api_key = $api_key;
          list(, $datacentre) = explode('-', $this->api_key);
          $this->api_endpoint = str_replace('<dc>', $datacentre, $this->api_endpoint);
      }

      /**
       * Call an API method. Every request needs the API key, so that is added automatically -- you don't need to pass it in.
       * @param  string $method The API method to call, e.g. 'lists/list'
       * @param  array  $args   An array of arguments to pass to the method. Will be json-encoded for you.
       * @return array          Associative array of json decoded API response.
       */
      public function call($method, $args=array(), $timeout = 10)
      {
          return $this->wpMakeRequest( $method, $args, $timeout );  
      }

      /**
       * Performs the underlying HTTP request using Wordpress wp_remote_post(). Quite exciting
       * @param  string $method The API method to be called
       * @param  array  $args   Assoc array of parameters to be passed
       * @return array          Assoc array of decoded result
       */
      private function wpMakeRequest($method, $args=array(), $timeout = 10)
      {      
          $args['apikey'] = $this->api_key;
          $url = $this->api_endpoint.'/'.$method.'.json';
          $result = wp_remote_post( $url, array(
            'method'      => 'POST',
            'timeout'     => 45,
            'user-agent'  => "PHP-MCAPI/2.0",
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
              'Content-Type'   => 'application/json',
              'Connection'     => 'close',
              ),
            'body'        => json_encode($args)
              )
          );

          if ( is_wp_error( $result ) ) {
             // $error_message = $result->get_error_message();
             $result = false;
          }
          return $result ? json_decode($result['body'], true) : false;
      }

      /*** WORDPRESS OPTIONS */

      function theme_options_tab( $sections ) {

        $sections[] = array(
        'subsection' => true,
        'title'      => esc_html__('MailChimp API', 'plethora-framework'),
        'heading'      => esc_html__('MAILCHIMP API OPTIONS', 'plethora-framework'),
        'desc'      => esc_html__('Fill in the following fields to connect with your Mailchimp account. On the Mailchimp website, go to \'Mailchimp Dashboard > Account > Extras > API keys\' to obtain an existing or create a new API Key', 'plethora-framework'),
        'fields'     => array(

          // MAILCHIMP API SETTINGS

            array(
              'id'       => THEMEOPTION_PREFIX .'mailchimp_apikey',
              'type'     => 'text',
              'title'    => esc_html__('MailChimp API Key', 'plethora-framework'),
              'validate' => 'no_special_chars',
              'default'  => ''
              ),
            array(
              'id'       => THEMEOPTION_PREFIX .'mailchimp_listid',
              'type'     => 'text',
              'title'    => esc_html__('MailChimp List ID', 'plethora-framework'),
              'validate' => 'no_special_chars',
              'default'  => ''
              ),
            /*
            array(
              'id'           =>  THEMEOPTION_PREFIX .'mailchimp_lists',
              'type'         => 'repeater',
              'title'    => esc_html__('MailChimp Lists', 'plethora-framework'),
              'subtitle'     => esc_html__('You may enable/disable any of those libraries. Nevertheless, you should know that FontAwesome and Social Icons are broadly used on this theme and you should not disactivate them.', 'plethora-framework') ,
              'group_values' => true, // Group all fields below within the repeater ID
              'item_name'    => 'MailChimp List', // Add a repeater block name to the Add and Delete buttons
              'bind_title'   => 'list_title', // Bind the repeater block title to this field ID
              // 'static'       => 1, // Set the number of repeater blocks to be output
              'limit'        => 20, // Limit the number of repeater blocks a user can create
              'sortable'     => false, // Allow the users to sort the repeater blocks or not
              'fields'       => array(
                                  array(
                                    'id'          => 'list_title',
                                    'type'        => 'text',
                                    'title'       => esc_html__( 'List title ( not an api setting, used just for reference )', 'plethora-framework' ),
                                    'placeholder' => '',
                                    'validate' => 'no_special_chars',
                                  ),
                                  array(
                                    'id'          => 'list_id',
                                    'type'        => 'text',
                                    'title'       => esc_html__( 'List ID ( as it given on MailChimp )', 'plethora-framework' ),
                                    'validate' => 'no_special_chars',
                                  ),
                                ),
            )
            */
          )
        );

        return $sections;
      }


      /**
       * Will return an option array for use in VC / REDUX options
       */
      static function get_lists_option_array( $args = array() ) {

        $default_args = array( 
                'use_in'   => 'vc',   // 'vc', 'redux'
                );
        // Merge user given arguments with default
        $args = wp_parse_args( $args, $default_args);
        extract($args);
        $lists = Plethora_Theme::option( THEMEOPTION_PREFIX .'mailchimp_lists', array() );
        $list_ids = isset( $lists['list_id'] ) ? $lists['list_id'] : array();
        $options   = array();
        foreach ( $list_ids as $key => $list_id ) {

          $list_title = isset( $lists['list_title'][$key] ) ? $lists['list_title'][$key] : '';
          if ( $use_in === 'vc' ) { 

            $options[$list_title] = $list_id;

          } elseif ( $use_in === 'redux' ) {

            $options[$list_id] = $list_title;
          }
        }

        return $options;
      }

      /* WORDPRESS OPTIONS ***/

  }

} // << Plethora_Module_Mailchimp Class