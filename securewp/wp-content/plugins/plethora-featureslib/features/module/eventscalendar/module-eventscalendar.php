<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                     (c)2016

Events Calendar Support module

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Eventscalendar') ) {


  /**
   */
  class Plethora_Module_Eventscalendar {

    public static $feature_title         = "The Events Calendar support module";                                             // FEATURE DISPLAY TITLE
    public static $feature_description   = "Support module for the Events Calender plugin by Modern Tribe"; // FEATURE DISPLAY DESCRIPTION 
    public static $theme_option_control  = true;                                                               // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
    public static $theme_option_default  = true;                                                               // DEFAULT ACTIVATION OPTION STATUS 
    public static $theme_option_requires = array();                                                            // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                                                               // DYNAMIC CLASS CONSTRUCTION? 
    public static $dynamic_method        = false;                                                              // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

    public $post_type;
    public $archive_view_css;
    public $single_view_css;
    /**
     * Core configuration
     */
    public function __construct() {

      if ( class_exists( 'Tribe__Events__Main' ) ) {
        // Set basic variables
          $this->post_type = 'tribe_events';

        # Basic support for events archive
          add_filter( 'plethora_supported_post_types', array( $this, 'add_to_supported_post_types'), 10, 2 ); // declare frontend support manually ( this is mandatory, since there is not Plethora_Posttype_Product class )

        # Admin: theme Options & metaboxes
          add_filter( 'plethora_themeoptions_content', array($this, 'archive_themeoptions'), 10);       // Theme Options // Archive
          add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 120);       // Theme Options // Single Post
          add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));                // Metabox // Single Post 

        # Front: Containers Setup ( hook on 'get_header' please )
        # Front: Templates Setup ( hook on 'get_header' please )
        # Front: Catalog Setup             

        # Front: Single Product Setup
          add_action( 'wp', array($this, 'single_content_align') );
          add_action( 'wp', array($this, 'single_intro_text') );
          add_action( 'wp', array($this, 'single_back_to_all') );
          add_action( 'wp', array($this, 'single_notices') );
          add_action( 'wp', array($this, 'single_event_title') );
          add_action( 'wp', array($this, 'single_event_subtitle') );
          add_action( 'wp', array($this, 'single_nav') );
          add_filter( 'plethora_get_title', array($this, 'title'), 10, 2 );
          add_filter( 'plethora_get_subtitle', array($this, 'subtitle'), 10, 2 );
          add_filter( 'plethora_inline_css', array( $this, 'single_view_css' ) );
          add_filter( 'plethora_inline_css', array( $this, 'archive_view_css' ) );

        # Front: Other configuration
          // remove admin stylesheets to avoid conflicts with our panel
          add_action( 'init', array($this, 'remove_admin_stylesheets') );

          // add admin notice to inform about plugin support
          Plethora_Theme::add_admin_notice( 'events_calendar', array(
            'condition'    => $this->is_tribe_admin_page(),
            'title'        => THEME_DISPLAYNAME . ' '. esc_html__( 'supports the Events Calendar plugin!', 'plethora-framework' ),
            'notice'       => array(
                                esc_html__( 'Have some good news for you, as  Avoir provides special support for the Events Calendar plugin.', 'plethora-framework' ),
                                sprintf( esc_html__( 'Visit %1$sTheme Options > Content%2$s section to check the additional configuration provided for the calendar and single event views.', 'plethora-framework' ), '<strong>', '</strong>' ),
                                esc_html__( 'Please note that the plugin has its own field where you have to re-enter your Google Maps API key.', 'plethora-framework' )
                              ),
            'links'        => array(
                                array( 'href' => esc_url( admin_url( '/admin.php') ) .'?page=plethora_options&tab=0', 'anchor_text' => esc_html__( 'Go To Theme Options', 'plethora-framework' ) ),
                                array( 'href' => esc_url( admin_url( '/edit.php') ) .'?page=tribe-common&tab=addons&post_type=tribe_events', 'anchor_text' => esc_html__( 'Set Google Maps Key for the Events Calendar', 'plethora-framework' ) ),
                              ),
            )
          );

          /* Add an empty method for easier overrides from extension class
             This way, we can avoid replicating all the __construct() method
             if we want to add something on the extension class.
          */
          $this->construct_ext();
      }
    } 

    public function construct_ext() {}


    public function remove_admin_stylesheets() {

      if ( ! $this->is_tribe_admin_page() ) {

        wp_deregister_style( 'tribe-events-admin-menu' );
        wp_deregister_style( 'tribe-common-admin' );
      }
    }

    /**
     * Returns true if this is an event calendar admin screen
     */
    public function is_tribe_admin_page() {

      return ( is_admin() && isset( $_GET['post_type'] ) && in_array( sanitize_key( $_GET['post_type'] ), array( 'tribe_events','tribe_venue', 'tribe_organizer' ) ) ) ? true : false;
    }

    /**
     * Returns true if this is an event calendar view
     * Set $single to true if you want to check specifically
     * for single event view
     */
    public function is_events_calendar( $view = '' ) {

      // leave if this is the blog page
      if ( is_post_type_archive( 'post' ) ) { return false; }

      global $wp_query;
      $is_events_query = $wp_query->tribe_is_event_query;

      if ( $view === 'archive' && $is_events_query && !is_singular() ) {

          return true;

      } elseif ( $view === 'single' && $is_events_query && is_singular() ) {

          return true;

      } elseif ( empty( $view ) && $is_events_query ) {

          return true;
      }

      return false;
    }

    /**
     * Add 'tribe_events' CPT to Plethora supported post types
     * Hooked on 'plethora_supported_post_types' filter
     */
    public function add_to_supported_post_types( $supported, $args ) {

        // Add this only when the call asks for plethora_only post types
        $supported[$this->post_type] = $args['output'] === 'objects' ? get_post_type_object( $this->post_type ) : $this->post_type ;

        return $supported;
    }

    // Set align options for archive ( if exist for this page )
    public function single_intro_text() {

      if ( $this->is_events_calendar( 'single' ) ) {

        $intro_text = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-introtext', '' );
        $args   = array(
            'container' => 'content_main_loop_before',
            'html'      => $intro_text,
            'status'    => empty( $intro_text ) ? false : true,
        );
        Plethora_Theme::add_container_part( $args );
      }
    }


    // Set align options for archive ( if exist for this page )
    public function single_content_align() {

      if ( $this->is_events_calendar( 'single' ) ) {

        $content_text_align = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-contentalign', '' );
        Plethora_Theme::add_container_attr( 'content_main', 'class', $content_text_align );
      }
    }

    // Set title to display the event date
    public function title( $title, $args ) {

      if ( $this->is_events_calendar() ) {

        $args['tag']           = '';
        $args['post_type']     = $this->post_type;
        $args['apply_filters'] = false;
        $title                 = Plethora_Theme::get_title( $args );
      }

      return $title;
    }

    // Set subtitle to display the event date
    public function subtitle( $subtitle, $args ) {

      if ( $this->is_events_calendar( 'single' ) ) {

        $subtitle = '';
        $display = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-subtitle', false );
        if ( $display === '1' ) {

          $subtitle = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-subtitle-text', esc_html__( 'This is the default event subtitle', 'plethora-framework' ) );

        } elseif ( $display === '2' && function_exists( 'tribe_events_event_schedule_details' ) ) {

          $subtitle = tribe_events_event_schedule_details();
        }

        if ( !empty( $subtitle ) && !empty( $args['tag'] ) ) { 

          $class    = !empty($args['class']) ? ' class="'. esc_attr( implode( ' ', $args['class'] ) ) .'"' : '';
          $id       = !empty($args['id']) ? ' id="'. esc_attr( $args['id'] ) .'"' : '';
          $subtitle = '<'. $args['tag'] . $class . $id .'>'. wp_strip_all_tags( $subtitle ) .'</'. $args['tag'] .'>';
        }
      
      } elseif ( $this->is_events_calendar( 'archive' ) ) {

        $args['tag']           = '';
        $args['post_type']     = $this->post_type;
        $args['apply_filters'] = false;
        $subtitle              = Plethora_Theme::get_subtitle( $args );
      }

      return $subtitle;
    }

    // 'All Events' link display
    public function single_back_to_all() {

      if ( $this->is_events_calendar( 'single' ) ) {

        $display = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-eventcal-back-to-all', true );
        if ( ! $display ) {

          $this->single_view_css = $this->single_view_css . '.tribe-events-back { display:none !important; visibility: hidden !important; }';
        }
      }
    }

    // Event notices display
    public function single_notices() {

      if ( $this->is_events_calendar( 'single' ) ) {

        $display = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-eventcal-notices', true );
        if ( ! $display ) {

          $this->single_view_css = $this->single_view_css . '.tribe-events-notices { display:none !important; visibility: hidden !important; }';
        }
      }
    }

    // Native event title display
    public function single_event_title() {

      if ( $this->is_events_calendar( 'single' ) ) {

        $display = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-eventcal-title', true );
        if ( ! $display ) {

          $this->single_view_css = $this->single_view_css . '.tribe-events-single-event-title { display:none !important; visibility: hidden !important; }';
        }
      }
    }

    // Event date display
    public function single_event_subtitle() {

      if ( $this->is_events_calendar( 'single' ) ) {

        $display = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-eventcal-date', true );
        if ( ! $display ) {

          $this->single_view_css = $this->single_view_css . '.tribe-events-schedule { display:none !important; visibility: hidden !important; }';
        }
      }
    }

    // Previous/Next navigation display
    public function single_nav() {

      if ( $this->is_events_calendar( 'single' ) ) {

        $display = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-eventcal-nav', '3' );
        if ( $display === '0' ) {

          $this->single_view_css = $this->single_view_css . '.tribe-events-schedule { display:none !important; visibility: hidden !important; }';
        
        } elseif ( $display === '2' ) {

          $this->single_view_css = $this->single_view_css . '#tribe-events-footer .tribe-events-sub-nav { display:none !important; visibility: hidden !important; }';

        } elseif ( $display === '3' ) {

          $this->single_view_css = $this->single_view_css . '#tribe-events-header .tribe-events-sub-nav { display:none !important; visibility: hidden !important; }';
        }
      }
    }

    public function single_view_css( $inline_css ) {

      if ( $this->is_events_calendar( 'single' ) ) {

          $inline_css .= $this->single_view_css;
      }
      return $inline_css;     
    }

    public function archive_view_css( $inline_css ) {

      if ( $this->is_events_calendar( 'archive' ) ) {

          $inline_css .= $this->archive_view_css;
      }
      return $inline_css;     
    }


    /**
    * Archive view theme options configuration for REDUX
    * Hooked on 'plethora_themeoptions_content'
    */
    public function archive_themeoptions( $sections ) {

          // setup theme options according to configuration
      $opts        = $this->archive_options();
      $opts_config = $this->archive_options_config();
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

        $desc = esc_html__('These options affect the events calendar views ( month, day, list and selected taxonomy terms ).', 'plethora-framework');
        $desc .= '<br>'. esc_html__('If you are using a speed optimization plugin, don\'t forget to clear cache after options update', 'plethora-framework');

          $sections[] = array(
          'title'      => esc_html__('Events Calendar', 'plethora-framework'),
          'heading'    => esc_html__('CALENDAR VIEWS OPTIONS // EVENTS CALENDAR PLUGIN', 'plethora-framework'),
          'desc'       => $desc,
          'subsection' => true,
          'fields'     => $fields
          );
      }

      return $sections;

    }

    /** 
    * Single view theme options configuration for REDUX
    * Filter hook @ 'plethora_themeoptions_content'
    */
    public function single_themeoptions( $sections ) {

      // setup theme options according to configuration
      $opts        = $this->single_options();
      $opts_config = $this->single_options_config();
      $fields      = array();
      foreach ( $opts_config as $opt_config ) {

        $id          = $opt_config['id'];
        $status      = $opt_config['theme_options'];
        $default_val = $opt_config['theme_options_default'];
        if ( $status && array_key_exists( $id, $opts ) ) {

          if ( !is_null( $default_val ) ) { // will add only if not NULL 

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
          'title'   => 'Single Event',
          'heading' => esc_html__('SINGLE EVENT VIEW OPTIONS // EVENTS CALENDAR PLUGIN', 'plethora-framework'),
          'desc'    => esc_html__('These will be the default values for a new event post you create. You have the possibility to override most of these settings on each event post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
          'subsection' => true,
                  'fields'     => $fields
        );
      }

      return $sections;
    }

    /** 
    * Returns METABOX options configuration for single post views
    * Filter hook @ 'plethora_metabox_add'
    */
    public function single_metabox( $metaboxes ) {

          // setup theme options according to configuration
      $opts          = $this->single_options();
      $opts_config   = $this->single_options_config();
      $fields        = array();
          foreach ( $opts_config as $opt_config ) {

        $id          = $opt_config['id'];
        $status      = $opt_config['metabox'];
        $default_val = $opt_config['metabox_default'];
            if ( $status && array_key_exists( $id, $opts ) ) {

              if ( !is_null( $default_val ) ) { // will add only if not NULL }
            $opts[$id]['default'] = $default_val;
          }
          if ( isset( $opts[$id]['subtitle'] ) ) {

            unset( $opts[$id]['subtitle'] );
          }
          
          $fields[] = $opts[$id];
            }
          }

      $sections_content = array(
        'title'      => esc_html__('Content', 'plethora-framework'),
        'heading'    => esc_html__('CONTENT OPTIONS', 'plethora-framework'),
        'icon_class' => 'icon-large',
        'icon'       => 'el-icon-lines',
        'fields'     => $fields
      );

      $sections = array();
      $sections[] = $sections_content;


      // This filter is used to hook additional option sections...LEAVE IT THERE!
      if ( has_filter( 'plethora_metabox_single'. $this->post_type ) ) {

        $sections = apply_filters( 'plethora_metabox_single'. $this->post_type, $sections );
      }
      if ( !empty( $fields ) ) {

          $metaboxes[] = array(
              'id'            => 'metabox-single-post',
              'title'         => THEME_DISPLAYNAME . ' '. esc_html__( 'Event Options', 'plethora-framework' ),
              'post_types'    => array( $this->post_type ),
              'position'      => 'normal', // normal, advanced, side
              'priority'      => 'high', // high, core, default, low
              'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
              'sections'      => $sections,
          );
        }

      return $metaboxes;
    }


    /** 
    * Returns ARCHIVE OPTIONS INDEX
    * It contains ALL possible archive options, no matter which theme
    */
    public function archive_options() {

      $single_options['singleview-titles'] = array(
              'id'       => $this->post_type .'-singleview-titles',
              'type'     => 'section',
              'title'    => esc_html__('Titles & Intro', 'plethora-framework'),
              'indent'   => true,
      );

      $archive_options['layout']  = array(
              'id'      =>  METAOPTION_PREFIX .'archive'.$this->post_type.'-layout',
              'title'   => esc_html__( 'Catalog Layout', 'plethora-framework' ),
              'type'    => 'image_select',
              'options' => Plethora_Module_Style::get_options_array( array( 
                                            'type'   => 'page_layouts',
                                            'use_in' => 'redux',
                                           )
              )
      );

      $archive_options['sidebar'] = array(
              'id'       => METAOPTION_PREFIX .'archive'.$this->post_type.'-sidebar',
              'required' => array( METAOPTION_PREFIX .'archive'.$this->post_type.'-layout','equals',array('right_sidebar','left_sidebar') ),
              'type'     => 'select',
              'data'     => 'sidebars',
              'multi'    => false,
              'title'    => esc_html__('Catalog Sidebar', 'plethora-framework'), 
      );
      
      $archive_options['colorset'] = array(
            'id'      => METAOPTION_PREFIX .'archive'.$this->post_type.'-colorset',
            'type'    => 'button_set',
            'title'   => esc_html__('Content Section Color Set', 'plethora-framework' ),
            'desc'    => esc_html__('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
            'options' => Plethora_Module_Style::get_options_array( array( 'type'      => 'color_sets',
                                            'use_in'          => 'redux',
                                            'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
      );

      $archive_options['title'] = array(
              'id'      => METAOPTION_PREFIX .'archive'.$this->post_type.'-title',
              'type'    => 'switch', 
              'title'   => esc_html__('Display Title On Content', 'plethora-framework'),
              'desc'    => esc_html__('Will display title on content view', 'plethora-framework'),
      );

      $archive_options['title-text']  = array(
              'id'        => METAOPTION_PREFIX .'archive'.$this->post_type.'-title-text',
              'type'      => 'text',
              'title'     => esc_html__('Default Title', 'plethora-framework'), 
              'translate' => true,
      );

        $archive_options['title-tax'] = array(
              'id'      => METAOPTION_PREFIX .'archive'.$this->post_type.'-title-tax',
              'type'    => 'button_set', 
              'title'   => esc_html__('Event Category Title', 'plethora-framework'),
              'desc'    => esc_html__('Title behavior when an event category view is displayed', 'plethora-framework'),
              'options' => array(
                      0 => esc_html__('Default Title', 'plethora-framework'),
                      1 => esc_html__('Event Category Title', 'plethora-framework'),
                    ),
      );

      $archive_options['subtitle']  = array(
              'id'      => METAOPTION_PREFIX .'archive'.$this->post_type.'-subtitle',
              'type'    => 'switch', 
              'title'   => esc_html__('Display Subtitle On Content', 'plethora-framework'),
              'desc'    => esc_html__('Will display subtitle on content view', 'plethora-framework'),
      );

      $archive_options['subtitle-text'] = array(
              'id'        => METAOPTION_PREFIX .'archive'.$this->post_type.'-subtitle-text',
              'type'      => 'text',
              'title'     => esc_html__('Default Subtitle', 'plethora-framework'), 
              'desc'      => esc_html__('This is used ONLY as default subtitle for the headings section of the Media Panel', 'plethora-framework'), 
              'translate' => true,
      );

      $archive_options['subtitle-tax'] = array(
              'id'      => METAOPTION_PREFIX .'archive'.$this->post_type.'-tax-subtitle',
              'type'    => 'button_set', 
              'title'   => esc_html__('Event Category Subtitle', 'plethora-framework'),
              'desc'    => esc_html__('Subtitle behavior when an event category view is displayed', 'plethora-framework'),
              'options' => array(
                      0 => esc_html__('Default Subtitle', 'plethora-framework'),
                      1 => esc_html__('Event Category Description', 'plethora-framework'),
                    ),
      );

      // Additional archive options added on Avoir >>> START
      $archive_options['containertype'] = array(
              'id'      => METAOPTION_PREFIX.'archive'.$this->post_type.'-containertype',
              'type'    => 'button_set', 
              'title'   => esc_html__('Container Type', 'plethora-framework'),
              'options' => array(
                      'container'       => esc_html__( 'Default', 'plethora-framework'),
                      'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
                    )
      );
      $archive_options['extraclass'] = array(
              'id'      => METAOPTION_PREFIX .'archive'.$this->post_type.'-extraclass',
              'type'    => 'text', 
              'title'   => esc_html__('Extra Classes', 'plethora-framework'),
              'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
      );

      $archive_options['content-align'] = array(
              'id'      => METAOPTION_PREFIX .'archive'.$this->post_type.'-contentalign',
              'type'    => 'button_set', 
              'title'   => esc_html__('Content Section Align', 'plethora-framework'),
              'desc'    => esc_html__('Affects all content section text alignment, except intro text ( you can set it as you like using the editor options ).', 'plethora-framework'),
              'options' => array(
                      ''            => esc_html__( 'Left', 'plethora-framework'),
                      'text-center' => esc_html__( 'Center', 'plethora-framework'),
                      'text-right'  => esc_html__( 'Right', 'plethora-framework'),
                     )
      );

      $archive_options['intro-text'] = array(
              'id'      => METAOPTION_PREFIX .'archive'.$this->post_type.'-introtext',
              'type'    => 'editor', 
              'title'   => esc_html__('Intro Text', 'plethora-framework'),
              'desc'    => esc_html__('This will be displayed right before the calendar', 'plethora-framework'),
              'args'   => array(
                    'teeny'            => false,
                    'textarea_rows'    => 7
              )     
      );

      return $archive_options;
    }

    /** 
    * Archive view options_config for theme options
    */
    public function archive_options_config() {

      $archive_options_config = array(
            array( 
              'id'                    => 'layout', 
              'theme_options'         => true, 
              'theme_options_default' => 'no_sidebar',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'sidebar', 
              'theme_options'         => true, 
              'theme_options_default' => 'sidebar-eventscalendar',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'containertype', 
              'theme_options'         => true, 
              'theme_options_default' => 'container',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'content-align', 
              'theme_options'         => true, 
              'theme_options_default' => 'container',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'colorset', 
              'theme_options'         => true, 
              'theme_options_default' => 'foo',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'title', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'title-text', 
              'theme_options'         => true, 
              'theme_options_default' => esc_html__('Events Calendar', 'plethora-framework'),
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'title-tax', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'subtitle', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'subtitle-text', 
              'theme_options'         => true, 
              'theme_options_default' => esc_html__('This is the default calendar view subtitle', 'plethora-framework'),
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'subtitle-tax', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              'metabox'               => false,
              'metabox_default'       => NULL
              ),

            array( 
              'id'                    => 'intro-text', 
              'theme_options'         => true, 
              'theme_options_default' => esc_html__( 'This is a little intro displayed before the calendar', 'plethora-framework' ),
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'extraclass', 
              'theme_options'         => true, 
              'theme_options_default' => '',
              'metabox'               => false,
              'metabox_default'       => NULL
              ),
      );

      return $archive_options_config;
    }

    /** 
    * Returns single options index
    * It contains ALL possible single options, no matter which theme OR CPT
    */
    public function single_options( $post_type_obj = '' ) {

      $single_options['singleview-basic'] = array(
              'id'       => $this->post_type .'-singleview-basic',
              'type'     => 'section',
              'title'    => esc_html__('Layout & Colors', 'plethora-framework'),
              'indent'   => true,
      );

      $single_options['singleview-content'] = array(
              'id'       => $this->post_type .'-singleview-content',
              'type'     => 'section',
              'title'    => esc_html__('Event elements', 'plethora-framework'),
              'indent'   => true,
      );

      $single_options['layout'] = array(
              'id'      =>  METAOPTION_PREFIX . $this->post_type .'-layout',
              'title'   => esc_html__('Layout', 'plethora-framework' ),
              'type'    => 'image_select',
              'options' => Plethora_Module_Style::get_options_array( array( 
                                              'type'   => 'page_layouts',
                                              'use_in' => 'redux',
                                             )
                    ),
      );

      $single_options['sidebar'] = array(
              'id'       => METAOPTION_PREFIX . $this->post_type .'-sidebar',
              'required' => array(METAOPTION_PREFIX . $this->post_type.'-layout','equals',array('right_sidebar','left_sidebar')),  
              'type'     => 'select',
              'data'     => 'sidebars',
              'multi'    => false,
              'title'    => esc_html__('Sidebar', 'plethora-framework'), 
      );

      $single_options['content-align'] = array(
              'id'      => METAOPTION_PREFIX . $this->post_type .'-contentalign',
              'type'    => 'button_set', 
              'title'   => esc_html__('Content Section Align', 'plethora-framework'),
              'desc'    => esc_html__('Affects all content section text alignment, except editor text.', 'plethora-framework'),
              'options' => array(
                      ''            => esc_html__( 'Left', 'plethora-framework'),
                      'text-center' => esc_html__( 'Center', 'plethora-framework'),
                      'text-right'  => esc_html__( 'Right', 'plethora-framework'),
                     )
      );

      $single_options['colorset'] = array(
              'id'      => METAOPTION_PREFIX . $this->post_type  .'-colorset',
              'type'    => 'button_set',
              'title'   => esc_html__('Content Section Color Set', 'plethora-framework' ),
              'desc'    => esc_html__('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
              'options' => Plethora_Module_Style::get_options_array( array( 
                                                                            'type'            => 'color_sets',
                                                                            'use_in'          => 'redux',
                                                                            'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
      );

      $single_options['title'] = array(
              'id'       => METAOPTION_PREFIX . $this->post_type  .'-title',
              'type'     => 'switch', 
              'title'    => esc_html__('Title', 'plethora-framework'),
              'desc'    => esc_html__('Enable/disable title section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
              'options'  => array(
                      1 => esc_html__('Display', 'plethora-framework'),
                      0 => esc_html__('Hide', 'plethora-framework'),
                    ),
      );

      $single_options['subtitle'] = array(
              'id'      => METAOPTION_PREFIX . $this->post_type  .'-subtitle',
              'type'     => 'button_set', 
              'title'   => esc_html__('Subtitle', 'plethora-framework'),
              'desc'    => esc_html__('Enable/disable subtitle section display. You might want to disable this in case you are using media panel for subtitles display.', 'plethora-framework'),
              'options' => array(
                      '1' => esc_html__('Display Custom Text', 'plethora-framework'),
                      '2' => esc_html__('Display Event Date', 'plethora-framework'),
                      '0' => esc_html__('Off', 'plethora-framework'),
                    ),
      );

      $single_options['subtitle-text'] = array(
              'id'       => METAOPTION_PREFIX . $this->post_type  .'-subtitle-text',
              'required' => array(METAOPTION_PREFIX . $this->post_type.'-subtitle','equals', '1'),  
              'type'     => 'text',
              'title'    => esc_html__('Subtitle', 'plethora-framework'), 
              'translate' => true,
      );

      $single_options['intro-text'] = array(
              'id'      => METAOPTION_PREFIX . $this->post_type.'-introtext',
              'type'    => 'editor', 
              'title'   => esc_html__('Intro Text', 'plethora-framework'),
              'desc'    => esc_html__('This will be displayed right before the event info', 'plethora-framework'),
              'args'   => array(
                    'teeny'            => false,
                    'textarea_rows'    => 7
              )     
      );
      // These options affect TEC plugin elements  >>> START
      $single_options['event-title'] = array(
              'id'       => METAOPTION_PREFIX . $this->post_type  .'-eventcal-title',
              'type'     => 'switch', 
              'title'    => esc_html__('Native Event Title', 'plethora-framework'),
      );

      $single_options['event-back-to-all'] = array(
              'id'       => METAOPTION_PREFIX . $this->post_type  .'-eventcal-back-to-all',
              'type'     => 'switch', 
              'title'    => esc_html__('All Events Link', 'plethora-framework'),
      );

      $single_options['event-notices'] = array(
              'id'       => METAOPTION_PREFIX . $this->post_type  .'-eventcal-notices',
              'type'     => 'switch', 
              'title'    => esc_html__('Notices Box', 'plethora-framework'),
              'desc'    => esc_html__( 'The plugin might produce some notices on the event ( such as the event has passed, etc )', 'plethora-framework'),
      );

      $single_options['event-date'] = array(
              'id'       => METAOPTION_PREFIX . $this->post_type  .'-eventcal-date',
              'type'     => 'switch', 
              'title'    => esc_html__('Date & Price Section', 'plethora-framework'),
      );

      $single_options['event-nav'] = array(
              'id'       => METAOPTION_PREFIX . $this->post_type  .'-eventcal-nav',
              'type'     => 'button_set', 
              'title'    => esc_html__('Previous/Next Navigation', 'plethora-framework'),
              'options' => array(
                      '1' => esc_html__('On', 'plethora-framework'),
                      '2' => esc_html__('Only On Top', 'plethora-framework'),
                      '3' => esc_html__('Only On Bottom', 'plethora-framework'),
                      '0' => esc_html__('Off', 'plethora-framework'),
              ),
      );

      // <<< END
      // Additional fields added on Avoir >>> START
      $single_options['divider'] = array(
              'id'      => METAOPTION_PREFIX . $this->post_type .'-divider',
              'type'    => 'switch', 
              'title'   => esc_html__('Divider', 'plethora-framework'),
      );

      $single_options['extraclass'] = array(
              'id'      => METAOPTION_PREFIX . $this->post_type .'-extraclass',
              'type'    => 'text', 
              'title'   => esc_html__('Extra Classes', 'plethora-framework'),
              'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
      );
      // Additional fields added on Avoir >>> END

      return $single_options;
    }

    /** 
    * Posts single view options_config for theme options and metabox panels
    */
    public function single_options_config() {

      $config = array(
            array( 
              'id'                    => 'singleview-basic', 
              'theme_options'         => true, 
              'theme_options_default' => NULL,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'layout', 
              'theme_options'         => true, 
              'theme_options_default' => 'no_sidebar_narrow',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'sidebar', 
              'theme_options'         => true, 
              'theme_options_default' => 'sidebar-eventscalendar',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'content-align', 
              'theme_options'         => true, 
              'theme_options_default' => '',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'colorset', 
              'theme_options'         => true, 
              'theme_options_default' => 'foo',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'singleview-content', 
              'theme_options'         => true, 
              'theme_options_default' => NULL,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'title', 
              'theme_options'         => true, 
              'theme_options_default' => false,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'subtitle', 
              'theme_options'         => true, 
              'theme_options_default' => '0',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'subtitle-text', 
              'theme_options'         => false, 
              'theme_options_default' => NULL,
              'metabox'               => true,
              'metabox_default'       => esc_html__( 'This is the default event subtitle', 'plethora-framework' ),
              ),
            array( 
              'id'                    => 'intro-text', 
              'theme_options'         => true, 
              'theme_options_default' => esc_html__( 'This is a little intro displayed before the event', 'plethora-framework' ),
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'event-back-to-all', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'event-notices', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'event-title', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'event-date', 
              'theme_options'         => true, 
              'theme_options_default' => true,
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'event-nav', 
              'theme_options'         => true, 
              'theme_options_default' => '3',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
            array( 
              'id'                    => 'extraclass', 
              'theme_options'         => true, 
              'theme_options_default' => '',
              'metabox'               => true,
              'metabox_default'       => NULL
              ),
      );

      return $config;
    }
  }
}
