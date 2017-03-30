<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2014-2015

Post Navigation module base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Postnavi') ) {


  /**
   */
  class Plethora_Module_Postnavi {

    public static $feature_title         = "Post Navigation Module";   // FEATURE DISPLAY TITLE
    public static $feature_description   = "";                    // FEATURE DISPLAY DESCRIPTION 
    public static $theme_option_control  = true;                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
    public static $theme_option_default  = true;                  // DEFAULT ACTIVATION OPTION STATUS 
    public static $theme_option_requires = array();               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                  // DYNAMIC CLASS CONSTRUCTION? 
    public static $dynamic_method        = false;                 // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

    public $exclude_single_post_types  = array();

    public function __construct() {

      // Should hook on init, to have available all the supported post types list
      add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {

      if ( is_admin() ) { 

        // Add breadcrumb options on given single post theme option tabs & metaboxes ( on section: 'Auxiliary Navigation' )
        $single_post_types = Plethora_Theme::get_supported_post_types( array( 'type' => 'singles', 'exclude' => $this->exclude_single_post_types ) );
        foreach ( $single_post_types as $post_type ) {

          add_filter( 'plethora_themeoptions_single_'. $post_type .'_auxiliary-navigation_fields', array( $this, 'add_single_options'), 15, 2 );
          add_filter( 'plethora_metabox_single_'. $post_type .'_auxiliary-navigation_fields', array( $this, 'add_single_options'), 15, 2 );
        }
      }
    }

    /**
    * Returns user set post navigation status
    */
    public static function get_status() {

        $post_type = Plethora_Theme::get_this_view_post_type();
        return Plethora_Theme::option( METAOPTION_PREFIX .$post_type.'-postnavi', 1 );
    }

    /**
    * Returns complete post navigation configuration according to theme options
    */
    public static function get_configuration() {

      $post_type      = Plethora_Theme::get_this_view_post_type();
      $prev_post_obj  = get_adjacent_post( false, '', false );
      $next_post_obj  = get_adjacent_post( false, '', true );
      $prev_permalink = get_permalink( $prev_post_obj );
      $next_permalink = get_permalink( $next_post_obj );
      $this_permalink = get_permalink();

      $config['postnavi_labels']     = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-postnavi-labels', 'custom' );
      $config['postnavi_prev_url']   = ( $prev_permalink != $this_permalink ) ? $prev_permalink : '';
      $config['postnavi_prev_label'] = ( $config['postnavi_labels'] === 'custom' ) ? Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-postnavi-label-previous', sprintf( esc_html__( 'Previous %s', 'plethora-framework' ), ucfirst( $post_type ) ) ) : get_the_title( $prev_post_obj );
      $config['postnavi_next_url']   = ( $next_permalink != $this_permalink ) ? $next_permalink : '';
      $config['postnavi_next_label'] = ( $config['postnavi_labels'] === 'custom' ) ? Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-postnavi-label-next', sprintf( esc_html__( 'Next %s', 'plethora-framework' ), ucfirst( $post_type ) ) ) : get_the_title( $next_post_obj );
      return $config;
    }     

    /**
    * Returns full html markup, according to theme options
    * No display status is applied,
    */
    public static function get_html() {

        $config = self::get_configuration();
        $html = '';
        $html .= ( !empty( $config['postnavi_prev_url'] ) ) ? '<a href="'. esc_url( $config['postnavi_prev_url'] ).'"><span data-toggle="tooltip" data-placement="left" title="" class="previous_icon" data-original-title="'. $config['postnavi_prev_label'] .'"></span></a>' : '';
        $html .= ( !empty( $config['postnavi_next_url'] ) ) ? '<a href="'. esc_url( $config['postnavi_next_url'] ).'"><span data-toggle="tooltip" data-placement="right" title="" class="next_icon" data-original-title="'. $config['postnavi_next_label'] .'"></span></a>' : '';
        return $html;
    }

   /**
    * Adds the same option fields under the 'Theme Options > Content > Single { Post } > Auxiliary Navigation' section
    * and the Auxiliary Navigation section of the single post metabox
    * Hooked on 'plethora_metabox_single_{ post_type }_auxiliary-navigation_fields'
    */
    public function add_single_options( $fields, $post_type ) {

      // setup theme options according to configuration
      $opts        = $this->single_options( $post_type );
      $opts_config = $this->single_options_config( $post_type  );
      foreach ( $opts_config as $opt_config ) {

        $id          = $opt_config['id'];
        $status      = ( current_filter() === 'plethora_themeoptions_single_'. $post_type .'_auxiliary-navigation_fields' ) ? $opt_config['theme_options'] : $opt_config['metabox'] ;
        $default_val = ( current_filter() === 'plethora_themeoptions_single_'. $post_type .'_auxiliary-navigation_fields' ) ? $opt_config['theme_options_default'] : $opt_config['metabox_default'];
        if ( $status && array_key_exists( $id, $opts ) ) {

          if ( !is_null( $default_val ) ) { // will add only if not NULL }
            
            $opts[$id]['default'] = $default_val;
          }
          $fields[] = $opts[$id];
        }
      }

      return $fields;
    }

   /** 
    * Returns single options index for 'Theme Options > Content > Single { Post Type }' tab 
    * and the single post edit metabox. 
    */
    public function single_options( $post_type ) {
      
      $single_options['status'] = array(
          'id'      => METAOPTION_PREFIX . $post_type .'-postnavi',
          'type'    => 'switch', 
          'title'   => sprintf( esc_html__('Next/Previous %s Navigation', 'plethora-framework'), ucfirst( $post_type ) ) ,
      );
      $single_options['navigation-labels'] = array(
          'id'      => METAOPTION_PREFIX . $post_type .'-postnavi-labels',
          'type'    => 'button_set', 
          'title'   => esc_html__('Labels Type', 'plethora-framework'),
          'options' => array(
              'custom'     => esc_html__('Custom Labels', 'plethora-framework'),
              'post_title' => esc_html__('Post Titles', 'plethora-framework'),
          ),
          'required'  => array( array( METAOPTION_PREFIX . $post_type .'-postnavi','=',1) ),
     );

      $single_options['navigation-label-previous'] = array(
          'id'        => METAOPTION_PREFIX . $post_type .'-postnavi-label-previous',
          'type'      => 'text', 
          'title'     => esc_html__('Previous Label Text', 'plethora-framework'),
          'translate' => true,
          'required'  => array( 
            array( METAOPTION_PREFIX . $post_type .'-postnavi','=',1),
            array( METAOPTION_PREFIX . $post_type .'-postnavi-labels','=', 'custom') 
          ),
      );
      $single_options['navigation-label-next'] = array(
          'id'        => METAOPTION_PREFIX . $post_type .'-postnavi-label-next',
          'type'      => 'text', 
          'title'     => esc_html__('Next Label Text', 'plethora-framework'),
          'translate' => true,
          'required'  => array( 
            array( METAOPTION_PREFIX . $post_type .'-postnavi','=',1),
            array( METAOPTION_PREFIX . $post_type .'-postnavi-labels','=', 'custom') 
          ),
      );
                    
      return $single_options;
    }

    /** 
    * Returns single options configuration for 'Theme Options > Content > Single { Post Type }' tab 
    * and the single post edit metabox. 
    * You should override this method on the extension class
    */
    public function single_options_config( $post_type ) {

      return array();
    }
  }
}