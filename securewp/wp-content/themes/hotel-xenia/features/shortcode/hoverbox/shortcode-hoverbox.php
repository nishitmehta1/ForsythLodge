<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Hover Box Shortcode Extension Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Shortcode_Hoverbox') && !class_exists('Plethora_Shortcode_Hoverbox_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/shortcode/shortcode-button.php
   */
  class Plethora_Shortcode_Hoverbox_Ext extends Plethora_Shortcode_Hoverbox { 

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
              'id'         => 'title', 
              'default'    => '',
              'field_size' => '',
              ),
            array( 
              'id'         => 'content', 
              'default'    => '',
              'field_size' => '',
              ),
            array( 
              'id'         => 'bcg_img', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'logo_img', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'link', 
              'default'    => '',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'stretchy_ratio', 
              'default'    => 'stretchy_wrapper ratio_2-3',
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
  }
}