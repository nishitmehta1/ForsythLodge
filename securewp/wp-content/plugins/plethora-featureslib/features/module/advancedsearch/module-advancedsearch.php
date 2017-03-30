<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2014-2015

Advanced Search Module base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Advancedsearch') ) {


  /**
   */
  class Plethora_Module_Advancedsearch {

    public static $feature_title         = "Advanced Search Options Module";   // FEATURE DISPLAY TITLE
    public static $feature_description   = "";                    // FEATURE DISPLAY DESCRIPTION 
    public static $theme_option_control  = true;                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
    public static $theme_option_default  = true;                  // DEFAULT ACTIVATION OPTION STATUS 
    public static $theme_option_requires = array();               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                  // DYNAMIC CLASS CONSTRUCTION? 
    public static $dynamic_method        = false;                 // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

    public $supports = array( 'navsearch', 'widget' );

    public function __construct() {

      if ( is_admin() ) { 
          
        add_action( 'init', array( $this, 'init' ) );

      } else {

        // Apply post type filters configuration
        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

        // Add navigation search element
        add_filter( 'wp_nav_menu_items', array( $this, 'add_navsearch_element'), 10, 2 );
      }
    }
    
    public function init() {

      // Add navigation search related theme/metabox options
      if ( $this->is_supported( 'navsearch' ) ) {

        add_filter( 'plethora_themeoptions_header', array( $this, 'theme_options_header_navsearch'), 25);
        add_filter( 'plethora_themeoptions_metabox_header_elements', array( $this, 'metabox_options_header_navsearch'));
      }
    }

    /**
    * Adds navigation search element to the current view
    */
    public function add_navsearch_element( $items, $args ) {

      if ( Plethora_Theme::option( METAOPTION_PREFIX .'advancedsearch-navsearch', 0 ) ) {
 
        // No need to change anything beyond here ( apart from styling classes or simple text maybe )
        if( $args->theme_location == Plethora_Theme::option( METAOPTION_PREFIX .'advancedsearch-navsearch-menulocation', 'primary' ) ) {
          
          $placeholder  = Plethora_Theme::option( METAOPTION_PREFIX .'advancedsearch-navsearch-placeholder', 'fa fa-search' );
          $icon         = Plethora_Theme::option( METAOPTION_PREFIX .'advancedsearch-navsearch-icon', 'fa fa-search' );
          $icon_pos     = Plethora_Theme::option( METAOPTION_PREFIX .'advancedsearch-navsearch-icon-position', 'right' );
          $icon_output  = '          <span class="input-group-btn">';
          $icon_output .= '              <button class="form-control btn btn-default btn-sm" type="submit"><i class="'. esc_attr( $icon ) .'"></i></button>';
          $icon_output .= '          </span>';

          $menu = '<li class="menu-item menu-item-type-post_type">';
          $menu .= '  <form method="get" name="s" id="s" action="'.  esc_url( get_site_url() ) .'" style="display:inline-table;">';
          $menu .= '      <span class="input-group form-group-sm">';
          $menu .= ( $icon && $icon_pos === 'left' ) ? $icon_output : '';
          $menu .= '          <input name="s" id="search" class="form-control" type="text" placeholder="'. esc_attr( $placeholder  ) .'">';
          $menu .= '          <input name="plethora_search" type="hidden" value="navsearch">';
          $menu .= ( $icon && $icon_pos === 'right' ) ? $icon_output : '';
          $menu .= '      </span>';
          $menu .= '  </form>';
          $menu .= '</li>';
          $items = Plethora_Theme::option( METAOPTION_PREFIX .'advancedsearch-navsearch-position', 'after' ) === 'before' ? $menu . $items : $items . $menu;
        }
      }

      return $items;
    }

    public function is_supported( $element ) {

      if ( in_array( $element, $this->supports ) ) {

        return true;
      }

      return false;
    }

    function pre_get_posts( $query ){

      if (  $query->is_search() && $query->is_main_query() && $query->get( 's' ) ) {

        $search_field = !empty( $_GET['plethora_search'] ) ? sanitize_key( $_GET['plethora_search'] ) : false;
        // Navigation search field
        if (  $this->is_supported( 'navsearch' ) && $search_field === 'navsearch' ) {

          $post_types = Plethora_Theme::option( THEMEOPTION_PREFIX .'advancedsearch-navsearch-posttypes', array( 'post' ) );
          if ( !empty( $post_types ) ) {

            $query->set('post_type', $post_types );
          }
        }
        // Plethora Search Widget field
        if (  $this->is_supported( 'widget' ) && $search_field === 'widget' ) {

          $post_types = !empty( $_GET['plethora_search_post_types'] ) ? sanitize_key( $_GET['plethora_search_post_types'] ) : false;
          $post_types = explode(',', $post_types );
          $query->set('post_type', $post_types );
        }
      }

      return $query;
    }

    /**
    * Products archive (shop) view theme options configuration for REDUX
    * Hooked on 'plethora_themeoptions_content'
    */
    public function theme_options_header_navsearch( $sections ) {

      // setup theme options according to configuration
      $opts        = $this->header_navsearch_options_index();
      $opts_config = $this->header_navsearch_options_config();
      $fields      = array();
      foreach ( $opts_config as $opt_config ) {

        $id          = $opt_config['id'];
        $status      = $opt_config['theme_options'];
        $default_val = $opt_config['theme_options_default'];
        if ( $status && array_key_exists( $id, $opts ) ) {

          if ( !is_null( $default_val ) ) { // will add only if not NULL }
            $opts[$id]['default'] = $default_val;
          }
          
          // a smal workaround to remove subtitles that HAVE to be displayed on CPT
          if ( isset( $opts[$id]['subtitle'] ) ) { 
            unset( $opts[$id]['subtitle'] );
          }

          $fields[] = $opts[$id];
        }
      }

      if ( !empty( $fields ) ) {

        $sections[] = array(
        'title'      => esc_html__('Main Section // Nav Search', 'plethora-framework'),
        'heading'    => esc_html__('MAIN SECTION // Nav Search', 'plethora-framework'),
        'desc'       => '',
        'subsection' => true,
        'fields'     => $fields
        );
      }

      return $sections;
    }


   /**
    * Hook For Metabox Header Elements tab.
    * Hooked on 'plethora_metabox_add'
    */
    public function metabox_options_header_navsearch( $section ) {

      // setup theme options according to configuration
      $opts        = $this->header_navsearch_options_index();
      $opts_config = $this->header_navsearch_options_config();
      $fields        = array();
      foreach ( $opts_config as $opt_config ) {

        $id          = $opt_config['id'];
        $status      = $opt_config['metabox'];
        $default_val = $opt_config['metabox_default'];
        if ( $status && array_key_exists( $id, $opts ) ) {

          if ( !is_null( $default_val ) ) { // will add only if not NULL }
            
            $opts[$id]['default'] = $default_val;
          }
          $fields[] = $opts[$id];
        }
      }

      // Always hook on $section['metabox_header_elements']['fields'] array key
      if ( isset($section['metabox_header_elements']['fields']) ) { 
        
        $section['metabox_header_elements']['fields'] = array_merge( 
              $section['metabox_header_elements']['fields'], 
              $fields 
        );
      }
      return $section;
    }


    /** 
    * Returns THEME OPTIONS INDEX for theme and metabox
    * It contains ALL possible breadcrumb theme options, no matter which theme
    */
    public function header_navsearch_options_index() {

      $options_index['metabox-heading']  = array(
       'id'     => 'header-breadcrumb-start',
       'type'   => 'section',
       'title'  => esc_html__('Search Field', 'plethora-framework'),
       'indent' => true,
      );

      $options_index['status']  = array(
        'id'    =>  METAOPTION_PREFIX .'advancedsearch-navsearch',
        'title' => esc_html__( 'Navigation Search Field', 'plethora-framework' ),
        'desc'  => esc_html__('Enable Search field display on navigation. For general search options, please visit Theme Options > Advanced > Search', 'plethora-framework'), 
        'type'  => 'switch', 
        'on'    => esc_html__( 'Display', 'plethora-framework' ),
        'off'   => esc_html__( 'Hide', 'plethora-framework' ),
      );

      $options_index['menulocation']  = array(
        'id'    =>  METAOPTION_PREFIX .'advancedsearch-navsearch-menulocation',
        'type'     => 'select',
        'data'     => 'menu_locations',
        'title'    => esc_html__('Display On Menu', 'plethora-framework'), 
        'desc'     => esc_html__('On which menu items to append/prepend the search field', 'plethora-framework'), 
        'required' => array( 
          array( METAOPTION_PREFIX .'advancedsearch-navsearch','equals', true ),
         ),
      );

      $options_index['position']  = array(
        'id'    =>  METAOPTION_PREFIX .'advancedsearch-navsearch-position',
        'type'     => 'button_set',
        'title'    => esc_html__('Field Position', 'plethora-framework'), 
        'desc'     => esc_html__('Place the search field before or after the selected menu items', 'plethora-framework'), 
        'options'  => array(
                        'before' => esc_html__( 'Before'),
                        'after' => esc_html__( 'After'),
                      ),
        'required' => array( 
          array( METAOPTION_PREFIX .'advancedsearch-navsearch','equals', true ),
         ),
      );

      $options_index['placeholder'] = array(
        'id'           => METAOPTION_PREFIX .'advancedsearch-navsearch-placeholder',
        'type'         => 'text',
        'title'        => esc_html__('Field Placeholder Text', 'plethora-framework'), 
        'desc'         => esc_html__('Text to be displayed in field when empty', 'plethora-framework'), 
        'translate'    => true,
        'validate'     => 'no_html',
        'required'     => array( METAOPTION_PREFIX .'advancedsearch-navsearch','equals', true ),
      );
      $options_index['icon'] = array(
        'id'       => METAOPTION_PREFIX .'advancedsearch-navsearch-icon',
        'type'     => 'icons',
        'title'    => esc_html__('Field Icon', 'plethora-framework'), 
        'options'  => ( method_exists( 'Plethora_Module_Icons', 'get_options_array' ) ) ? Plethora_Module_Icons::get_options_array() : array(),
        'required' => array( 
          array( METAOPTION_PREFIX .'advancedsearch-navsearch','equals', true ),
         ),
      );
      $options_index['icon-position'] = array(
        'id'       => METAOPTION_PREFIX .'advancedsearch-navsearch-icon-position',
        'type'     => 'button_set',
        'title'    => esc_html__('Field Icon Position', 'plethora-framework'), 
        'options'  => array(
                        'left' => esc_html__( 'Left'),
                        'right' => esc_html__( 'Right'),
                      ),
        'required' => array( 
          array( METAOPTION_PREFIX .'advancedsearch-navsearch','equals', true ),
         ),
      );
      $options_index['posttypes'] = array(
        'id'       => METAOPTION_PREFIX .'advancedsearch-navsearch-posttypes',
        'type'     => 'select',
        'title'    => esc_html__('Post Type Filter', 'plethora-framework'), 
        'desc'     => esc_html__('Limit the search results to one or more post type filters', 'plethora-framework'), 
        'options'  => $this->get_post_type_options(),
        'multi' => true,
        'required' => array( 
          array( METAOPTION_PREFIX .'advancedsearch-navsearch','equals', true ),
         ),
      );

      return $options_index;
    }

    /** 
    * Returns the default OPTIONS CONFIGURATION for theme and metabox
    * If needed different configuration for a specific theme, 
    * you should override this method on the extension class
    */
    public function header_navsearch_options_config() {

      $options_config = array(

            array( 
              'id'                    => 'metabox-heading', 
              'theme_options'         => false, 
              'theme_options_default' => NULL,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'status', 
              'theme_options'         => true, 
              'theme_options_default' => false,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'menulocation', 
              'theme_options'         => true, 
              'theme_options_default' => 'primary',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'position', 
              'theme_options'         => true, 
              'theme_options_default' => 'after',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'placeholder', 
              'theme_options'         => true, 
              'theme_options_default' => esc_html__( 'Search', 'plethora-framework' ),
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'icon', 
              'theme_options'         => true, 
              'theme_options_default' => 'fa fa-search',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'icon-position', 
              'theme_options'         => true, 
              'theme_options_default' => 'right',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'posttypes', 
              'theme_options'         => true, 
              'theme_options_default' => 'post',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
      );

      return $options_config;
    }

    public function get_post_type_options () {

      $options = array();
      $types = Plethora_Theme::get_supported_post_types( array( 'type' => 'archives', 'output' => 'objects' ) );
      foreach ( $types as $post_type => $post_type_obj ) {

        $options[$post_type] = $post_type_obj->labels->name;
      }

      return $options;

    }
  }
}