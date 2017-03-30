<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Profile Post Type Config Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Posttype_Slider') && !class_exists('Plethora_Posttype_Slider_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/posttype/slider/posttype-slider.php
   */
  class Plethora_Posttype_Slider_Ext extends Plethora_Posttype_Slider { 


    /** 
    * Slide options configuration for repeater field
    */
    public function slide_options_config() {

      $slides_config = array(
        array( 
          'id'                    => 'status', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'image', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => ''
          ),
        array( 
          'id'                    => 'colorset', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'foo'
          ),
        array( 
          'id'                    => 'transparentfilm', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'foo'
          ),
        array( 
          'id'                    => 'section-captions', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => ''
          ),
        array( 
          'id'                    => 'caption-title', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => ''
          ),
        array( 
          'id'                    => 'caption-subtitle', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => ''
          ),
        array( 
          'id'                    => 'caption-secondarytitle', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => ''
          ),
        array( 
          'id'                    => 'caption-secondarytext', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => ''
          ),
        array( 
          'id'                    => 'caption-colorset', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'foo'
          ),
        array( 
          'id'                    => 'caption-transparentfilm-xenia', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'foo'
          ),
        array( 
          'id'                    => 'caption-size', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'foo'
          ),
        array( 
          'id'                    => 'caption-align', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'foo'
          ),
        array( 
          'id'                    => 'caption-textalign', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'centered'
          ),
        array( 
          'id'                    => 'caption-neutralizetext', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'foo'
          ),
        // TEMPORARILY REMOVED UNTIL WE HAVE MORE STYLES
        // array( 
        //   'id'                    => 'caption-headingstyle', 
        //   'theme_options'         => false, 
        //   'theme_options_default' => NULL,
        //   'metabox'               => true,
        //   'metabox_default'       => 'caption_flat'
        //   ),
        array( 
          'id'                    => 'caption-animation', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'section-button', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'buttonlinktext', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => esc_html__('Learn More', 'hotel-xenia')
          ),
        array( 
          'id'                    => 'buttonlinkurl', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => '#'
          ),
        array( 
          'id'                    => 'buttonlinktarget', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => '_self'
          ),
        array( 
          'id'                    => 'buttonstyle-xenia', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'btn-link'
          ),
        array( 
          'id'                    => 'buttoncolor', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'btn-default'
          ),
        array( 
          'id'                    => 'buttonsize', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 'btn'
          ),
      );

      return $slides_config;
    }

    /** 
    * Setting options configuration
    */
    public function settings_options_config() {

      $single_options_config = array(
        array( 
          'id'                    => 'autoplay', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'autoplaytimeout', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 5000
          ),
        array( 
          'id'                    => 'autoplayspeed', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => 1000
          ),
        array( 
          'id'                    => 'autoplayhoverpause', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'nav', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'dots', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'loop', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => false
          ),
        array( 
          'id'                    => 'mousedrag', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'touchdrag', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'lazyload', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => true
          ),
        array( 
          'id'                    => 'rtl', 
          'theme_options'         => false, 
          'theme_options_default' => NULL,
          'metabox'               => true,
          'metabox_default'       => false
          ),
      );
      return $single_options_config;
    }
  }
}