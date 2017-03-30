<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2014-2015

Visual Composer Configuration Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Vc') ) {


  /**
   */
  class Plethora_Module_Vc {

      public static $feature_title         = "Visual Composer Compatibility Module";                                             // FEATURE DISPLAY TITLE
      public static $feature_description   = "Visual Composer Additional Configuration"; // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                                                               // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
      public static $theme_option_default  = true;                                                               // DEFAULT ACTIVATION OPTION STATUS 
      public static $theme_option_requires = array();                                                            // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                                                               // DYNAMIC CLASS CONSTRUCTION? 
      public static $dynamic_method        = false;                                                              // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

      public $update_source   = 'tgm';
      public $fronteditor     = false;
      public $remove_elements = array();

      function __construct() {

          if ( function_exists( 'Vc_Manager' ) ) {

            // Get user preference
            $this->update_source = Plethora_Theme::option( THEMEOPTION_PREFIX .'visualcomposer_update', $this->update_source );
            $this->fronteditor   = Plethora_Theme::option( THEMEOPTION_PREFIX .'visualcomposer-fronteditor', $this->fronteditor );
            // Add theme options tab
            add_filter( 'plethora_themeoptions_modules', array( $this, 'theme_options_tab'), 10);
            
            if ( $this->update_source === 'tgm' ) {

              # BASIC CONFIGURATION
                // Set theme bundled plugin behaviour
                add_action( 'vc_before_init',  array( $this, 'set_as_theme' ) );

                // Remove product license tab 
                add_filter( 'vc_settings_tabs', array( $this, 'remove_product_license_tab' ) );

                // Remove license activation reminder by updating related cookie
                setcookie( 'vchideactivationmsg_vc11', WPB_VC_VERSION, time() + ( 86400 * 300 ) );
                
                // Extra workaround to remove all VC's interventions to WP core update and TGM
                remove_all_actions( 'in_plugin_update_message-' . vc_plugin_name() );
                global $vc_manager;
                if ( is_object( $vc_manager ) && isset( $vc_manager->disableUpdater ) ) {
                  
                  $vc_manager->disableUpdater( true );
                }

              # OTHER CONFIGURATION

                // Post types that VC is enabled by default
                $this->set_frontend_editor_status();

                // Remove elements
                $this->remove_elements();

                // Post types that VC is enabled by default
                $this->set_default_editor_post_types();

            } else {

              add_filter( 'plethora_theme_plugins', array( $this, 'remove_vc_from_tgm_plugins' ) );
            }
          }
      } 

      /**
       * Set VC's behavior as theme bundled plugin
       * Will just remove 'Design Options' / 'Custom CSS' admin screens
       */
      public function set_as_theme() {

        vc_set_as_theme();
      }

      /**
       * Filter to remove product license tab
       */
      public function remove_product_license_tab( $tabs ) {

        if ( isset( $tabs['vc-updater'] ) ) {

          unset( $tabs['vc-updater'] );
        }

        return $tabs;
      }

      /**
       * If the user chooses direct VC updates, don't annoy him with TGM
       * This function filters the registered theme plugins before they are
       * sent to TGM ( filter is applied on Plethora_Setup class )
       */
      public function remove_vc_from_tgm_plugins() {

        // Get user preference for VC
        if ( isset( $this->theme_plugins['js_composer'] ) && $this->update_source === 'direct' ) {

            unset( $this->theme_plugins['js_composer'] );
        }
      }

      /**
       * Set the status of the frontend editor ( )
       */
      public function set_frontend_editor_status() {

        if ( function_exists( 'vc_disable_frontend') && !$this->fronteditor ) {

            vc_disable_frontend( true );
        }
      }

      /**
       * Set post types where the VC editor is enabled by default
       */
      public function set_default_editor_post_types() {

        if ( function_exists( 'vc_set_default_editor_post_types') ) {

            vc_set_default_editor_post_types( array( 'page', 'post', 'project' ) ); 
        }
      }

      /**
       * Remove native or third party VC elements that don't fit to our configuration
       */
      public function remove_elements() {

      if ( function_exists( 'vc_remove_element' ) ) {

           $remove_elements = Plethora_Theme::option( THEMEOPTION_PREFIX .'visualcomposer-non-supported-elements', $this->get_elements_defaults() );

            if ( is_array($remove_elements) ){

              foreach ( $remove_elements as $key => $status ) {

                if ( $status == '0' ) {

                  vc_remove_element( $key );
                }
              }

            }
        }          

      }

      /**
       * Returns ALL VC parameters index
       * Please do not include third party VC implementations ( i.e. WP, Woo, CF7, etc. )
       * All VC deprecated elements should be set to 'vc_status' => false
       * Latest version check: 4.12.1
       */
      public function get_elements_index() {

        $elements_index = array(

          'vc_column_text'        => array( 'desc' => esc_html__( 'Text Block', 'plethora-framework' ), 'vc_status' => true ),
          'vc_icon'               => array( 'desc' => esc_html__( 'Icon', 'plethora-framework' ), 'vc_status' => true ),
          'vc_separator'          => array( 'desc' => esc_html__( 'Separator', 'plethora-framework' ), 'vc_status' => true ),
          'vc_text_separator'     => array( 'desc' => esc_html__( 'Separator With Text', 'plethora-framework' ), 'vc_status' => true ),
          'vc_message'            => array( 'desc' => esc_html__( 'Message Box', 'plethora-framework' ), 'vc_status' => true ),
          'vc_facebook'           => array( 'desc' => esc_html__( 'Facebook Like', 'plethora-framework' ), 'vc_status' => true ),
          'vc_tweetmeme'          => array( 'desc' => esc_html__( 'Tweetmeme Button', 'plethora-framework' ), 'vc_status' => true ),
          'vc_googleplus'         => array( 'desc' => esc_html__( 'Google Plus Button', 'plethora-framework' ), 'vc_status' => true ),
          'vc_pinterest'          => array( 'desc' => esc_html__( 'Pinterest', 'plethora-framework' ), 'vc_status' => true ),
          'vc_toggle'             => array( 'desc' => esc_html__( 'FAQ', 'plethora-framework' ), 'vc_status' => true ),
          'vc_single_image'       => array( 'desc' => esc_html__( 'Single Image', 'plethora-framework' ), 'vc_status' => true ),
          'vc_gallery'            => array( 'desc' => esc_html__( 'Image Gallery', 'plethora-framework' ), 'vc_status' => true ),
          'vc_images_carousel'    => array( 'desc' => esc_html__( 'Image Carousel', 'plethora-framework' ), 'vc_status' => true ),
          'vc_tta_accordion'      => array( 'desc' => esc_html__( 'Accordion', 'plethora-framework' ), 'vc_status' => true ),
          'vc_tta_pageable'       => array( 'desc' => esc_html__( 'Pageable Content', 'plethora-framework' ), 'vc_status' => true ),
          'vc_tta_tabs'           => array( 'desc' => esc_html__( 'Tabs', 'plethora-framework' ), 'vc_status' => true ),
          'vc_custom_heading'     => array( 'desc' => esc_html__( 'Custom Heading', 'plethora-framework' ), 'vc_status' => true ),
          'vc_btn'                => array( 'desc' => esc_html__( 'Button', 'plethora-framework' ), 'vc_status' => true ),
          'vc_cta'                => array( 'desc' => esc_html__( 'Call To Action', 'plethora-framework' ), 'vc_status' => true ),
          'vc_widget_sidebar'     => array( 'desc' => esc_html__( 'Widgetized Sidebar', 'plethora-framework' ), 'vc_status' => true ),
          'vc_posts_slider'       => array( 'desc' => esc_html__( 'Posts Slider', 'plethora-framework' ), 'vc_status' => true ),
          'vc_video'              => array( 'desc' => esc_html__( 'Video Player', 'plethora-framework' ), 'vc_status' => true ),
          'vc_gmaps'              => array( 'desc' => esc_html__( 'Google Maps', 'plethora-framework' ), 'vc_status' => true ),
          'vc_raw_html'           => array( 'desc' => esc_html__( 'Raw HTML', 'plethora-framework' ), 'vc_status' => true ),
          'vc_raw_js'             => array( 'desc' => esc_html__( 'Raw JS', 'plethora-framework' ), 'vc_status' => true ),
          'vc_flickr'             => array( 'desc' => esc_html__( 'Flickr Widget', 'plethora-framework' ), 'vc_status' => true ),
          'vc_progress_bar'       => array( 'desc' => esc_html__( 'Progress Bar', 'plethora-framework' ), 'vc_status' => true ),
          'vc_pie'                => array( 'desc' => esc_html__( 'Pie Chart', 'plethora-framework' ), 'vc_status' => true ),
          'vc_round_chart'        => array( 'desc' => esc_html__( 'Round Chart', 'plethora-framework' ), 'vc_status' => true ),
          'vc_line_chart'         => array( 'desc' => esc_html__( 'Line Chart', 'plethora-framework' ), 'vc_status' => true ),
          'vc_empty_space'        => array( 'desc' => esc_html__( 'Empty Space', 'plethora-framework' ), 'vc_status' => true ),
          'vc_basic_grid'         => array( 'desc' => esc_html__( 'Posts Grid', 'plethora-framework' ), 'vc_status' => true ),
          'vc_media_grid'         => array( 'desc' => esc_html__( 'Media Grid', 'plethora-framework' ), 'vc_status' => true ),
          'vc_masonry_grid'       => array( 'desc' => esc_html__( 'Post Masonry Grid', 'plethora-framework' ), 'vc_status' => true ),
          'vc_masonry_media_grid' => array( 'desc' => esc_html__( 'Media Masonry Grid', 'plethora-framework' ), 'vc_status' => true ),
          'vc_tta_tour'           => array( 'desc' => esc_html__( 'Tour', 'plethora-framework' ), 'vc_status' => true ),
          // Deprecated
          'vc_tabs'               => array( 'desc' => esc_html__( 'Old Tabs', 'plethora-framework' ), 'vc_status' => false ),
          'vc_tour'               => array( 'desc' => esc_html__( 'Old Tour', 'plethora-framework' ), 'vc_status' => false ),
          'vc_accordion'          => array( 'desc' => esc_html__( 'Old Accordion', 'plethora-framework' ), 'vc_status' => false ),
          // Deprecated ( still working, but no longer mentioned)
          // 'vc_button2'            => array( 'desc' => esc_html__( 'Old Button 1', 'plethora-framework' ), 'vc_status' => false ),
          // 'vc_button2'            => array( 'desc' => esc_html__( 'Old Button 2', 'plethora-framework' ), 'vc_status' => false ),
          // 'vc_carousel'           => array( 'desc' => esc_html__( 'Old Post Carousel', 'plethora-framework' ), 'vc_status' => false ),
          // 'vc_cta_button'         => array( 'desc' => esc_html__( 'Old Call To Action', 'plethora-framework' ), 'vc_status' => false ),
          // 'vc_cta_button2'        => array( 'desc' => esc_html__( 'Old Call To Action Button 2', 'plethora-framework' ), 'vc_status' => false ),
          // 'vc_posts_grid'         => array( 'desc' => esc_html__( 'Old Posts Grid', 'plethora-framework' ), 'vc_status' => false ),

        );
        // sort index according to desc
        uasort( $elements_index, function( $a, $b ) { return strcmp($a["desc"], $b["desc"]); } );
        return $elements_index;
      }

      /**
       * Returns all elements configuration for direct use with related 
       * option on theme options panel. 
       */
      public function get_elements_options() {
        
        $options = array();
        $all_elements = $this->get_elements_index();

        foreach ( $all_elements as $elem_key => $element_data ) {
         
         $desc1   = ( ! $element_data['vc_status'] ) ? '<small style="color:red;"> '. esc_html__( '// Deprecated by VC author', 'plethora-framework' ) .'</small>' : '';
         $desc2   = ( in_array( $elem_key, $this->remove_elements ) ) ? '<small style="color:darkred;"> '. esc_html__( '// Replaced with a Plethora element or not working seamlessly on this theme', 'plethora-framework' ) .'</small>' : '';
         $full_desc          = $element_data['desc'] . $desc1 . $desc2 ;
         $options[$elem_key] = $full_desc;
        }
        return $options;
      }

      /**
       * Returns all elements default values for direct use with related 
       * option on theme options panel. Deprecated items are deactivated
       * by default, along with all elements included on $this->remove_elements
       * class variable
       */
      public function get_elements_defaults() {
        
        $default_elements = array();
        $all_elements = $this->get_elements_index();

        foreach ( $all_elements as $elem_key => $element_data ) {

         $default_value = ( in_array( $elem_key, $this->remove_elements ) || ! $element_data['vc_status'] ) ? '0' : '1';
         $default_elements[$elem_key] = $default_value ;
        }
        return $default_elements;
      }

      /**
       * Returns all options configuration for the theme options panel.
       * Hooked at 'plethora_themeoptions_modules' filter
       */
      public function theme_options_tab( $sections ) {

        $description  = '<p style="margin:15px 0 0 0;">'. sprintf( esc_html__('%1$sTheme ( recommended ):%2$s update VC using this theme packaged version and get technical support from Plethora Themes. The latest fully featured version of the plugin comes always bundled on each theme update. %3$sNo additional plugin license is required as this is covered by the regular theme license you have already purchased%4$s.', 'plethora-framework' ), '<strong>', '</strong>', '<u>', '</u>' ) .'</p>';
        $description .= '<p style="margin:15px 0 0 0;">'. sprintf( esc_html__('%1$sPlugin:%2$s update VC and get technical support directly from the plugin author. %3$sThis requires a license key purchase by the plugin author%4$s ( WPBakery ) and further activation via the %1$sVisual Composer > Product License%2$s tab ( tab will be accessible right after this update method is applied ). ', 'plethora-framework'), '<strong>', '</strong>', '<u>', '</u>' ) .'</p>';

        $sections[] = array(
          'subsection' => true,
          'title'      => esc_html__('Visual Composer', 'plethora-framework'),
          'heading'    => esc_html__('VISUAL COMPOSER', 'plethora-framework'),
          'fields'     => array(

            // MAILCHIMP API SETTINGS
              array(
                'id'          => THEMEOPTION_PREFIX .'visualcomposer_update',
                'type'        => 'button_set',
                'title'       => esc_html__('Plugin Update Method', 'plethora-framework'),
                'subtitle'    => esc_html__('Choose the Visual Composer plugin update method that fits to your needs', 'plethora-framework'),
                'description' => $description,
                'default'     => 'tgm',
                'options'     => array(
                  'tgm'    => esc_html__('Theme ( recommended )', 'plethora-framework'), 
                  'direct' => esc_html__('Plugin', 'plethora-framework'), 
                ),
              ),

              array(
                'id'          => THEMEOPTION_PREFIX .'visualcomposer-fronteditor',
                'type'        => 'switch',
                'title'       => esc_html__('Allow Front Editor Use', 'plethora-framework'),
                'description' => esc_html__('Please note that we will not provide technical support for possible front-end editor issues.', 'plethora-framework'),
                'default'     => 0,
              ),

              array(
                'id'       => THEMEOPTION_PREFIX .'visualcomposer-non-supported-elements',
                'type'     => 'checkbox',
                'title'    => esc_html__('VC Elements', 'plethora-framework'),
                'subtitle' => esc_html__('All VC packed elements ( non Plethora elements ) that are deprecated or replaced by a Plethora element or not working seamlessly with this theme are deactivated by default. If needed, you can activate them, but please note that we will not provide technical support for these elements.', 'plethora-framework'),
                'options'  => $this->get_elements_options(),
                'default'  => $this->get_elements_defaults(),
              )
            )
        );

        return $sections;
      }
  }
}