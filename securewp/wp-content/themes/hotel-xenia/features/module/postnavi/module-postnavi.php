<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

Post Navigation Module Extension Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Postnavi') && !class_exists('Plethora_Module_Postnavi_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/postnavi/module-postnavi.php
   */
  class Plethora_Module_Postnavi_Ext extends Plethora_Module_Postnavi { 

    public $exclude_single_post_types  = array( 'page' );

    /** 
    * Returns single options configuration for 'Theme Options > Content > Single { Post Type }' tab 
    * and the single post edit metabox. 
    */
    public function single_options_config( $post_type ) {

      $single_options_config = array(
          array( 
            'id'                    => 'status', 
            'theme_options'         => true, 
            'theme_options_default' => true,
            'metabox'               => true,
            'metabox_default'       => NULL
            ),
          array( 
            'id'                    => 'navigation-labels', 
            'theme_options'         => true, 
            'theme_options_default' => 'custom',
            'metabox'               => false,
            'metabox_default'       => NULL
            ),
          array( 
            'id'                    => 'navigation-label-previous', 
            'theme_options'         => true, 
            'theme_options_default' => sprintf( esc_html__( 'Previous %s', 'hotel-xenia' ), ucfirst( $post_type ) ),
            'metabox'               => false,
            'metabox_default'       => NULL
            ),
          array( 
            'id'                    => 'navigation-label-next', 
            'theme_options'         => true, 
            'theme_options_default' => sprintf( esc_html__( 'Next %s', 'hotel-xenia' ), ucfirst( $post_type ) ),
            'metabox'               => false,
            'metabox_default'       => NULL
            ),
      );

      return $single_options_config;
    }
  }
}