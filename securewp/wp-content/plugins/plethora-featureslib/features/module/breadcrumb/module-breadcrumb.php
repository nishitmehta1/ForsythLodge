<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2014-2015

Breadcrumb module base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Breadcrumb') ) {


  /**
   */
  class Plethora_Module_Breadcrumb {

    public static $feature_title         = "Breadcrumb Module";   // FEATURE DISPLAY TITLE
    public static $feature_description   = "";                    // FEATURE DISPLAY DESCRIPTION 
    public static $theme_option_control  = true;                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
    public static $theme_option_default  = true;                  // DEFAULT ACTIVATION OPTION STATUS 
    public static $theme_option_requires = array();               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                  // DYNAMIC CLASS CONSTRUCTION? 
    public static $dynamic_method        = false;                 // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

    public $exclude_single_post_types      = array();
    public $exclude_archive_post_types     = array();
    public $exclude_special_pages          = array();
    public $status_true_single_post_types  = array();
    public $status_true_archive_post_types = array();

    public function __construct() {

      // Should hook on init, to have available all the supported post types list
      add_action( 'init', array( $this, 'init' ) );
    }

    public function init( ) {

      if ( is_admin() ) { 
        
        // Add general theme option fields tab under the 'Theme Options > General' section
        add_filter( 'plethora_themeoptions_general', array( $this, 'add_global_options'), 45);

        // Add breadcrumb options on given single post theme option tabs & metaboxes ( on section: 'Auxiliary Navigation' )
        $single_post_types = Plethora_Theme::get_supported_post_types( array( 'type' => 'singles', 'exclude' => $this->exclude_single_post_types ) );
        foreach ( $single_post_types as $post_type ) {

          add_filter( 'plethora_themeoptions_single_'. $post_type .'_auxiliary-navigation_fields', array( $this, 'add_single_options'), 15, 2 );
          add_filter( 'plethora_metabox_single_'. $post_type .'_auxiliary-navigation_fields', array( $this, 'add_single_options'), 15, 2 );
        }

        // Add breadcrumb options on given archive theme option tabs & metaboxes ( on section: 'Auxiliary Navigation' )
        $archive_post_types = Plethora_Theme::get_supported_post_types( array( 'type' => 'archives', 'exclude' => $this->exclude_archive_post_types ) );
        foreach ( $archive_post_types as $post_type ) {

          add_filter( 'plethora_themeoptions_archive_'. $post_type .'_auxiliary-navigation_fields', array( $this, 'add_archive_options'), 15, 2 );
          add_filter( 'plethora_metabox_archive_'. $post_type .'_auxiliary-navigation_fields', array( $this, 'add_archive_options'), 15, 2 );
        }

        $special_pages = array( 'search', '404' );
        // Add breadcrumb options on given dynamic pages ('404' or 'search' )
        foreach ( $special_pages as $special_page ) {

          if ( ! in_array( $special_page, $this->exclude_special_pages ) ) {

            add_filter( 'plethora_themeoptions_'. $special_page .'_auxiliary-navigation_fields', array( $this, 'add_specialpage_options'), 15, 2 );
            add_filter( 'plethora_metabox_single_'. $special_page .'_auxiliary-navigation_fields', array( $this, 'add_specialpage_options'), 15, 2 );
          }
        }
      }
    }

    /**
    * Returns user set breadcrumb status
    */
    public static function get_status() {

      if ( is_404() ) {

        return Plethora_Theme::option( THEMEOPTION_PREFIX .'404-breadcrumb', 0 );

      } elseif ( is_search() ) {

        return Plethora_Theme::option( THEMEOPTION_PREFIX .'search-breadcrumb', 0 );

      } elseif ( Plethora_Theme::is_archive_page() ) {

        $post_type = Plethora_Theme::get_this_view_post_type();
        $post_type = !empty( $post_type ) ? $post_type : 'post';
        return Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-breadcrumb', 0 );

      } else {

        $post_type = Plethora_Theme::get_this_view_post_type();
        $post_type = !empty( $post_type ) ? $post_type : 'post';
        return Plethora_Theme::option( METAOPTION_PREFIX .$post_type.'-breadcrumb', 0 );
      }
    }

    /**
    * Returns complete breadcrumb configuration according to theme options
    */
    public static function get_configuration() {
        
        // Get helper variables
        $post_type                            = Plethora_Theme::get_this_view_post_type();
        $this_id                              = Plethora_Theme::get_this_page_id();
        $this_title                           = Plethora_Theme::get_title( array( 'tag' => '', 'force_display' => true ));
        $user_config['home_anchor_text']      = Plethora_Theme::option( METAOPTION_PREFIX .'breadcrumb-home-anchor-text', esc_html__( 'Home', 'plethora-framework') );
        $user_config['separator']             = Plethora_Theme::option( METAOPTION_PREFIX .'breadcrumb-separator', esc_html__( '>', 'plethora-framework') );
        $user_config['current-link']          = Plethora_Theme::option( METAOPTION_PREFIX .'breadcrumb-current-link', 1 );
        $user_config['current-paged']         = Plethora_Theme::option( METAOPTION_PREFIX .'breadcrumb-current-paged', 1 );
        $user_config['current-paged-pattern'] = Plethora_Theme::option( METAOPTION_PREFIX .'breadcrumb-current-paged-pattern', esc_html__( '// page: %s', 'plethora-framework' ) );
        $user_config['current-extra-class']   = Plethora_Theme::option( METAOPTION_PREFIX .'breadcrumb-current-extra-class', '' );
        $user_config['prepend-taxonomy']      = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-breadcrumb-prepend-taxonomy', 0 );
        $user_config['prepend-taxonomy-term'] = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-breadcrumb-prepend-taxonomy-term', self::get_term_options( $post_type, 'default') );

        // Start building final config
        $config['prefix_text'] = Plethora_Theme::option( METAOPTION_PREFIX .'breadcrumb-prefix-text', '' );
        $config['extra_class'] = Plethora_Theme::option( METAOPTION_PREFIX .'breadcrumb-extra-class', '' );

        $config['items']['home'] = array (
            'link'        => is_front_page() && ! $user_config['current-link'] ? false : get_home_url(),
            'anchor_text' => $user_config['home_anchor_text'],
            'separator'   => is_front_page() ? '' : $user_config['separator'],
            'class'       => is_front_page() ? $user_config['current-extra-class'] : '',
        );

        // Check if this is is an archive, or needs to be prepended with an archive item
        $archive_link = get_post_type_archive_link( $post_type );
        if ( $archive_link && !is_search() ) {
            $archive_is_last   = Plethora_Theme::is_archive_page() && ! ( is_tax() || is_category() || is_tag() || is_author() || is_date() || is_search() ) ? true : false;
            $archive_is_linked = $archive_is_last && ! $user_config['current-link'] ? true : false;
            $archive_key       = $archive_is_last ? 'last' : 'archive_link';
            $config['items'][$archive_key] = array (
                'link'        => $archive_is_linked ? false : $archive_link,
                'anchor_text' => Plethora_Theme::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-text', esc_html__('The Blog', 'plethora-framework' ) ) ,
                'separator'   => $archive_is_last ? '' : $user_config['separator'],
                'class'       => $archive_is_last ? '' : $user_config['current-extra-class'],
            );
        }
        
        // Any single post, cpt or page
        if ( is_singular() && !is_front_page() ) {

            // have to find the post's category ( we have to pick one )
            if ( !empty( $user_config['prepend-taxonomy'] ) && !empty( $user_config['prepend-taxonomy-term'] ) ) {

              // Select a term object
              $term      = explode('|', $user_config['prepend-taxonomy-term'] );
              $taxonomy  = isset( $term[0] ) ? $term[0] : '';
              $term_slug = isset( $term[1] ) ? $term[1] : '';
              $term_obj  = get_term_by( 'slug', $term_slug, $taxonomy );
              if ( $term_obj && !is_wp_error( $term_obj ) ) { 
                // Get ancestors first
                $ancestors = array_reverse( get_ancestors( $term_obj->term_id, $term_obj->taxonomy ) );
                foreach ( $ancestors as $ancestor ) {

                     $config['items'][] = array (
                        'link'        => get_term_link( $ancestor ),
                        'anchor_text' => $ancestor->name,
                        'separator'   => '',
                        'class'       => $user_config['current-extra-class'],
                    );
                }
                // And finally, get the tax
                $config['items'][] = array (
                    'link'        => get_term_link( $term_obj ),
                    'anchor_text' => $term_obj->name,
                    'separator'   => $user_config['separator'],
                    'class'       => $user_config['current-extra-class'],
                );
              }
            }

            // Get ancestors first
            $single_ancestors = array_reverse( get_post_ancestors( $this_id ) );
            foreach ( $single_ancestors as $ancestor ) {

                $config['items'][] = array (
                    'link'        => get_permalink( $ancestor ),
                    'anchor_text' => get_the_title( $ancestor ),
                    'separator'   => $user_config['separator'],
                    'class'       => '',
                );
            }
            // And finally, get the single
            $config['items']['last'] = array (
                'link'        => $user_config['current-link'] ? get_post_permalink() : false,
                'anchor_text' => $this_title,
                'separator'   => '',
                'class'       => $user_config['current-extra-class'],
            );

        // Any custom taxonomy
        } elseif ( is_tax() || is_category() || is_tag() ) {

            $term_obj = get_queried_object();
            // Get ancestors first
            $ancestors = array_reverse( get_ancestors( $term_obj->term_id, $term_obj->taxonomy ) );
            foreach ( $ancestors as $ancestor ) {

                 $config['items'][] = array (
                    'link'        => get_term_link( $ancestor ),
                    'anchor_text' => $ancestor->name,
                    'separator'   => '',
                    'class'       => $user_config['current-extra-class'],
                );
            }
            // And finally, get the tax
            $config['items']['last'] = array (
                'link'        => $user_config['current-link'] ? get_term_link( $term_obj ) : false,
                'anchor_text' => $this_title,
                'separator'   => '',
                'class'       => $user_config['current-extra-class'],
            );

        // author, date, 404 and search page
        } elseif ( is_author() || is_date() || is_404() || is_search() ) {

            $config['items']['last'] = array (
                'link'        => false,
                'anchor_text' => $this_title,
                'separator'   => '',
                'class'       => $user_config['current-extra-class'],
            );

        } 

        // Add paging info to the last element
        global $wp_query;
        $paged             = get_query_var( 'paged', 1 ); // for native wp archives
        $static_paged      = get_query_var( 'page', 1 ); // for static page archives
        $page_number       = ( int ) $paged > 1  ? $paged : 1; 
        $page_number       = ( int ) $static_paged > 1 ? $static_paged : $page_number; 
        $pages_total       = $wp_query->max_num_pages; 
        $paged_text        = $user_config['current-paged'] && ( int ) $page_number > 1 ? sprintf( $user_config['current-paged-pattern'], $page_number, $pages_total ) : ''; 
        if ( !empty( $paged_text) ) {
          $config['items']['last']['anchor_text'] = $config['items']['last']['anchor_text'] . ' '. $paged_text;

        }
        return apply_filters( 'plethora_breadcrumb_config', $config ) ;
    }     

    /**
    * Returns full html markup, according to theme options
    * No display status is applied,
    */
    public static function get_html() {

        $config = self::get_configuration();
        $html = '<ul';
        $html .= ( empty( $config['extra_class'] ) ) ? ' class="ple_breadcrumb"' : ' class="ple_breadcrumb '. esc_attr( $config['extra_class'] ) .'"';
        $html .= '>';
        if ( !empty( $config['prefix_text'] ) ) {

         $html .= '<li class="ple_breadcrumb_item ple_breadcrumb_prefix">'.  wp_kses( $config['prefix_text'], Plethora_Theme::allowed_html_for( 'heading' ) ) .'</li>';
        }
        foreach ( $config['items'] as $item ) {
          
          $html .= '<li class="ple_breadcrumb_item '. esc_attr( $item['class'] ) .'">';
          $html .= ( $item['link'] ) ? '<a href="'. esc_url( $item['link'] ) .'" title="'. esc_attr( $item['anchor_text'] ) .'">' : '';
          $html .= '<span>'. wp_kses( $item['anchor_text'], Plethora_Theme::allowed_html_for( 'heading' ) ) .'</span>';
          $html .= ( $item['link'] ) ? '</a>' : '';
          $html .= ( !empty( $item['separator'] ) ) ? '<span class="ple_breadcrumb_sep">' : '';
          $html .= esc_html( $item['separator'] );
          $html .= ( !empty( $item['separator'] ) ) ? '</span>' : '';
          $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

   /**
    * Adds option fields under the 'Theme Options > General' section
    * Hooked on 'plethora_themeoptions_general' 
    */
    public function add_global_options( $sections ) {

      // setup theme options according to configuration
      $opts        = $this->global_options();
      $opts_config = $this->global_options_config( );
      $fields      = array();
      foreach ( $opts_config as $opt_config ) {

        $id          = $opt_config['id'];
        $default_val = $opt_config['default'];
        if ( array_key_exists( $id, $opts ) ) {

          if ( !is_null( $default_val ) ) { // will add only if not NULL }
            $opts[$id]['default'] = $default_val;
          }
          
          $fields[] = $opts[$id];
        }
      }

      if ( !empty( $fields ) ) {

        $sections[] = array(
        'title'      => esc_html__('Breadcrumb', 'plethora-framework'),
        'heading'    => esc_html__('BREADCRUMB', 'plethora-framework'),
        'desc'       => sprintf( esc_html__( 'These are the global configuration for the breadcrumb element. You may control the display separately page view under %1$sTheme Options > Content%2$s or on each single page/post edit view', 'plethora-framework' ), '<strong>', '</strong>' ),
        'subsection' => true,
        'fields'     => $fields
        );
      }

      return $sections;
    }

   /**
    * Adds option fields under the 'Theme Options > Content > { Post } Archive > Auxiliary Navigation' section
    * Hooked on 'plethora_themeoptions_archive_{post_type}_auxiliary-navigation_fields' 
    */
    public function add_archive_options( $fields, $post_type ) {

      // setup theme options according to configuration
      $opts        = $this->archive_options( $post_type );
      $opts_config = $this->archive_options_config( $post_type );
      foreach ( $opts_config as $opt_config ) {

        $id          = $opt_config['id'];
        $status      = ( current_filter() === 'plethora_themeoptions_archive_'. $post_type .'_auxiliary-navigation_fields' ) ? $opt_config['theme_options'] : $opt_config['metabox'];
        $default_val = ( current_filter() === 'plethora_themeoptions_archive_'. $post_type .'_auxiliary-navigation_fields' ) ? $opt_config['theme_options_default'] : $opt_config['metabox_default'];
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
    * Adds the same option fields under the 'Theme Options > Content > Single { Post } > Auxiliary Navigation' section
    * and the Auxiliary Navigation section of the single post metabox
    * Hooked on 'plethora_metabox_single_{ post_type }_auxiliary-navigation_fields'
    */
    public function add_single_options( $fields, $post_type ) {

      // setup theme options according to configuration
      $opts        = $this->single_options( $post_type );
      $opts_config = $this->single_options_config( $post_type );
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
    * Returns the global options index for the 'Theme Options > General' tab
    */
    public function global_options() {
      
      $global_options['prefix-text'] = array(
        'id'           => METAOPTION_PREFIX .'breadcrumb-prefix-text',
        'type'         => 'text',
        'title'        => esc_html__('Prefix Text', 'plethora-framework'), 
        'desc'         => sprintf( esc_html__('Text to be displayed before the breadcrumb. %s', 'plethora-framework'), Plethora_Theme::allowed_html_for( 'button', true ) ), 
        'translate'    => true,
        'validate'     => 'html_custom',
        'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
      );

      $global_options['home-anchor-text'] = array(
        'id'           => METAOPTION_PREFIX .'breadcrumb-home-anchor-text',
        'type'         => 'text',
        'title'        => esc_html__('Home Page Anchor Text', 'plethora-framework'), 
        'desc'         => sprintf( esc_html__('Anchor text for the home page link. %s', 'plethora-framework'), Plethora_Theme::allowed_html_for( 'button', true ) ), 
        'validate'     => 'html_custom',
        'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
        'translate'    => true,
      );

      $global_options['separator'] = array(
        'id'           => METAOPTION_PREFIX .'breadcrumb-separator',
        'type'         => 'text',
        'title'        => esc_html__('Separator', 'plethora-framework'), 
        'desc'         => sprintf( esc_html__('Text that will be used to separate links. %s', 'plethora-framework'), Plethora_Theme::allowed_html_for( 'button', true ) ), 
        'validate'     => 'html_custom',
        'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
        'translate'    => true,
      );
      $global_options['current-link'] = array(
        'id'       =>  METAOPTION_PREFIX .'breadcrumb-current-link',
        'title'    => esc_html__( 'Link Current Page', 'plethora-framework' ),
        'desc'     => esc_html__('Add link to the current page breadcrumb part', 'plethora-framework'), 
        'type'     => 'switch', 
        'on'       => esc_html__( 'Yes', 'plethora-framework' ),
        'off'      => esc_html__( 'No', 'plethora-framework' ),
      );
      $global_options['current-paged'] = array(
        'id'       =>  METAOPTION_PREFIX .'breadcrumb-current-paged',
        'title'    => esc_html__( 'Display Current Archive Page Text', 'plethora-framework' ),
        'desc'     => esc_html__('Whether to display current page info text or not  ( works only on archive views, ie. the blog )', 'plethora-framework'), 
        'type'     => 'switch', 
        'on'       => esc_html__( 'Yes', 'plethora-framework' ),
        'off'      => esc_html__( 'No', 'plethora-framework' ),
      );
      $global_options['current-paged-pattern'] = array(
        'id'           => METAOPTION_PREFIX .'breadcrumb-current-paged-pattern',
        'type'         => 'text',
        'title'        => esc_html__('Current Page Text Pattern', 'plethora-framework'), 
        'desc'         => sprintf( esc_html__('Set the desired text pattern for page text. This will be appended to the current archive title, after the second page - %s : current page number | %s : total pages number. %s', 'plethora-framework'), '<strong>%1$s</strong>', '<strong>%2$s</strong>', Plethora_Theme::allowed_html_for( 'button', true ) ), 
        'translate'    => true,
        'validate'     => 'html_custom',
        'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
        'required'     => array( 
                            array( METAOPTION_PREFIX .'breadcrumb-current-paged','equals', true ),
                          ),                        
      );
      $global_options['current-extra-class'] = array(
        'id'           => METAOPTION_PREFIX .'breadcrumb-current-extra-class',
        'type'         => 'text',
        'title'        => esc_html__('Current Page Extra Class', 'plethora-framework'), 
        'desc'         => esc_html__('Extra CSS class for the current item breadcrumb part, to make things easier for your custom styling', 'plethora-framework'), 
        'validate'     => 'no_special_chars',
        'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
      );
      $global_options['extra-class'] = array(
        'id'           => METAOPTION_PREFIX .'breadcrumb-extra-class',
        'type'         => 'text',
        'title'        => esc_html__('Breadcrumb Container Extra Class', 'plethora-framework'), 
        'desc'         => esc_html__('Extra CSS class for the breadcrumb container, to make things easier for your custom styling', 'plethora-framework'), 
        'validate'     => 'no_special_chars',
        'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
      );

      return $global_options;
    }

    /** 
    * Returns the global options configuration for the 'Theme Options > General' tab
    * You should override this method on the extension class
    */
    public function global_options_config() {

      return array();
    }

   /** 
    * Returns single options index for 'Theme Options > Content > Single { Post Type }' tab 
    * and the single post edit metabox. 
    */
    public function single_options( $post_type ) {
      
      $single_options['breadcrumb-status']  = array(
        'id'    =>  METAOPTION_PREFIX . $post_type .'-breadcrumb',
        'title' => esc_html__( 'Display Breadcrumb', 'plethora-framework' ),
        'desc'  => esc_html__('Enable breadcrumb element on this post.', 'plethora-framework'), 
        'type'  => 'switch', 
        'on'    => esc_html__( 'Display', 'plethora-framework' ),
        'off'   => esc_html__( 'Hide', 'plethora-framework' ),
      );
      $post_type_obj = get_post_type_object( $post_type );
      $post_type_taxonomies = get_object_taxonomies( $post_type );
      if ( $post_type_obj->has_archive && !empty( $post_type_taxonomies ) ) {
        $single_options['prepend-taxonomy'] = array(
          'id'       =>  METAOPTION_PREFIX . $post_type .'-breadcrumb-prepend-taxonomy',
          'title'    => esc_html__( 'Breadcrumb // Prepend Taxonomy Term', 'plethora-framework' ),
          'desc'     => esc_html__('Prepend a category or custom taxonomy term link on the single view breadcrumb. ', 'plethora-framework'), 
          'type'     => 'switch', 
          'on'       => esc_html__( 'Yes', 'plethora-framework' ),
          'off'      => esc_html__( 'No', 'plethora-framework' ),
          'required' => array( 
            array( METAOPTION_PREFIX . $post_type .'-breadcrumb','equals', true ),
           ),
        );
        $single_options['prepend-taxonomy-term'] = array(
          'id'       => METAOPTION_PREFIX . $post_type .'-breadcrumb-prepend-taxonomy-term',
          'type'     => 'select',
          'title'    => esc_html__('Breadcrumb // Select Term To Prepend', 'plethora-framework'), 
          'desc'     => esc_html__('Note that you should have already saved term selections on this post', 'plethora-framework'), 
          'options'  => self::get_term_options( $post_type ),
          'required' => array( 
            array( METAOPTION_PREFIX . $post_type .'-breadcrumb','equals', true ),
            array( METAOPTION_PREFIX . $post_type .'-breadcrumb-prepend-taxonomy','equals', true ),
           ),
        );
      }

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

     /** 
    * Returns single options for 'Theme Options > Content > Single { Post Type }' tab and the single post edit metabox
    * Hooked at 
    */
    public function archive_options( $post_type ) {
      
      $archive_options['breadcrumb-status']  = array(
        'id'    =>  METAOPTION_PREFIX .'archive'. $post_type .'-breadcrumb',
        'title' => esc_html__( 'Display Breadcrumb', 'plethora-framework' ),
        'desc'  => esc_html__('Enable breadcrumb element on this archive views.', 'plethora-framework'), 
        'type'  => 'switch', 
        'on'    => esc_html__( 'Display', 'plethora-framework' ),
        'off'   => esc_html__( 'Hide', 'plethora-framework' ),
      );

      return $archive_options;
    }

    /** 
    * Returns single options configuration for 'Theme Options > Content > { Post Type } Archive' tab
    * 'Theme Options > General' tab theme options and metabox.
    * You should override this method on the extension class
    */
    public function archive_options_config( $post_type ) {

      return array();
    }

     /** 
    * Returns single options for 'Theme Options > Content > Single { Post Type }' tab and the single post edit metabox
    * Hooked at 
    */
    public function specialpage_options( $special_page ) {
      
      $specialpage_options['status']  = array(
        'id'    =>  METAOPTION_PREFIX . $special_page .'-breadcrumb',
        'title' => esc_html__( 'Display Breadcrumb', 'plethora-framework' ),
        'desc'  => sprintf( esc_html__('Enable breadcrumb element on the %s view.', 'plethora-framework'), $special_page ), 
        'type'  => 'switch', 
        'on'    => esc_html__( 'Display', 'plethora-framework' ),
        'off'   => esc_html__( 'Hide', 'plethora-framework' ),
      );

      return $specialpage_options;
    }

    /** 
    * Returns single options configuration for 'Theme Options > Content > { Post Type } Archive' tab
    * 'Theme Options > General' tab theme options and metabox.
    * You should override this method on the extension class
    */
    public function specialpage_options_config( $special_page ) {

      return array();
    }


    public static function get_term_options( $post_type, $return = 'all' ) {

      $options = array();
      $taxonomies = get_object_taxonomies( $post_type , 'objects' );
      $post_id = isset( $_GET['post'] ) ? $_GET['post'] : false;
      if ( $post_id ) {
        foreach ( $taxonomies as $tax_key => $taxonomy ) {
          if ( $taxonomy->public && $taxonomy->hierarchical ) {
            $tax_terms = wp_get_post_terms( $post_id, $tax_key );
            if ( is_array( $tax_terms ) ) { 
              foreach ( $tax_terms as $term ) {

                $options[$tax_key .'|'. $term->slug] = $taxonomy->labels->singular_name .' > '. $term->name;
                if ( $return === 'default' ) { return $options; }
              }
            }
          }
        }
      }
      return $options;
    }
  }
}