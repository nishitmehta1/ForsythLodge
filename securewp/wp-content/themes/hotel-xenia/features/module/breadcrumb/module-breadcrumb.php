<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

Breadcrumb Module Extension Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Breadcrumb') && !class_exists('Plethora_Module_Breadcrumb_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/breadcrumb/module-breadcrumb.php
   */
  class Plethora_Module_Breadcrumb_Ext extends Plethora_Module_Breadcrumb { 

    public $exclude_single_post_types      = array();
    public $exclude_archive_post_types     = array();
    public $exclude_special_pages          = array( '404', 'search' );
    public $status_true_single_post_types  = array();
    public $status_true_archive_post_types = array();

    /** 
    * Returns the global options configuration for the 'Theme Options > General' tab
    */
    public function global_options_config( ) {

      $options_config = array(

          array( 
            'id'      => 'prefix-text', 
            'default' => '',
            ),
          array( 
            'id'      => 'home-anchor-text', 
            'default' => esc_html__( 'Home', 'hotel-xenia'),
            ),
          array( 
            'id'      => 'separator', 
            'default' => '/',
            ),
          array( 
            'id'      => 'current-link', 
            'default' => false,
            ),
          array( 
            'id'      => 'current-paged', 
            'default' => true,
            ),
          array( 
            'id'      => 'current-paged-pattern', 
            'default' => esc_html__( '- Page %1$s of %2$s', 'hotel-xenia' ),
            ),
          // array( 
          //   'id'      => 'prepend-taxonomy', 
          //   'default' => false,
          //   ),
          array( 
            'id'      => 'prepend-taxonomy-term', 
            'default' => NULL,
            ),
          array( 
            'id'      => 'current-extra-class', 
            'default' => '',
            ),
          array( 
            'id'      => 'extra-class', 
            'default' => '',
            ),
      );

      return $options_config;
    }

    /** 
    * Returns single options configuration for 'Theme Options > Content > Single { Post Type }' tab 
    * and the single post edit metabox. 
    */
    public function single_options_config( $post_type ) {

      $status_per_post_type = in_array( $post_type, $this->status_true_single_post_types ) ? true : false; 
      $single_options_config = array(
          array( 
            'id'                    => 'breadcrumb-status', 
            'theme_options'         => true, 
            'theme_options_default' => $status_per_post_type,
            'metabox'               => true,
            'metabox_default'       => NULL
            ),
          array( 
            'id'                    => 'prepend-taxonomy', 
            'theme_options'         => true, 
            'theme_options_default' => true,
            'metabox'               => true,
            'metabox_default'       => NULL
            ),
          array( 
            'id'                    => 'prepend-taxonomy-term', 
            'theme_options'         => false, 
            'theme_options_default' => false,
            'metabox'               => true,
            'metabox_default'       => self::get_term_options( $post_type )
            ),
      );

      return $single_options_config;
    }

    /** 
    * Returns single options configuration for 'Theme Options > Content > { Post Type } Archive' tab
    */
    public function archive_options_config( $post_type ) {

      $status_per_post_type = in_array( $post_type, $this->status_true_archive_post_types ) ? true : false; 
      $archive_options_config = array(
          array( 
            'id'                    => 'breadcrumb-status', 
            'theme_options'         => true, 
            'theme_options_default' => $status_per_post_type,
            'metabox'               => false,
            'metabox_default'       => NULL
            ),
      );

      return $archive_options_config;
    }

    /** 
    * Returns options configuration for 'Theme Options > Content > 404 Page / Search Page' tabs
    */
    public function specialpage_options_config( $special_page ) {

      if ( $special_page === 'search' ) {

        $specialpage_options_config = array(
            array( 
              'id'                    => 'breadcrumb-status', 
              'theme_options'         => true, 
              'theme_options_default' => false,
              ),
        );

      } elseif ( $special_page === '404' ) {

        $specialpage_options_config = array(
            array( 
              'id'                    => 'breadcrumb-status', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              ),
        );
      }

      return $specialpage_options_config;
    }
  }
}