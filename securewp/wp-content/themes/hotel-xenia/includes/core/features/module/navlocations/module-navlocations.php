<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Nav Locations manager

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Navlocations') ) {

    class Plethora_Module_Navlocations {

        public static $feature_title         = "Custom Navigation Manager";                                             // Feature display title  (string)
        public static $feature_description   = "Integration module for Plethora custom navigation locations manager";   // Feature display description (string)
        public static $theme_option_control  = false;                                                                   // Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_default  = false;                                                                   // Default activation option status ( boolean )
        public static $theme_option_requires = array();                                                                 // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                                                                    // Dynamic class construction ? ( boolean )
        public static $dynamic_method        = false;                                                                   // Additional method invocation ( string/boolean | method name or false )

        function __construct(){

          if ( is_admin() ) { 
              // Set theme options tab for media panel
              add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_options_tab'), 30);
          }

          add_action( 'init', array( $this, 'theme_navigation_locations' ), 5);  // User defined sidebars.
        }

        /**
        * Custom sidebars setup
        * @since 1.0
        *
        */
        public function theme_navigation_locations(){
            $default_nav_locations = $this->default_nav_locations();
            $nav_locations   = Plethora_Theme::option( THEMEOPTION_PREFIX .'navlocations', $default_nav_locations );
            if ( isset( $nav_locations['navpos_location'] )  && isset( $nav_locations['navpos_desc'] ) ) { 
                foreach ($nav_locations['navpos_location'] as $key => $navpos_slug ) {
                 if ( !empty($navpos_slug) && !empty( $nav_locations['navpos_desc'][$key] ) ) { 

                    $location = $nav_locations['navpos_location'][$key]; 
                    $description = $nav_locations['navpos_desc'][$key]; 
                    register_nav_menu( $location, $description );
                  } 
                }
            }
        }

        /**
         * Set default navlocations 
         * @since 1.0
         *
         */
        public function default_nav_locations() {

          // Execute this only on first page load
          $default_nav_locations = array();
          // IMPORTANT: this is necessary for repeater field...add a line for each sidebar record
          $default_nav_locations['redux_repeater_data'] = array(
                                 array( 'title'=> '' ),
                                 array( 'title'=> '' ),
                           );
          $default_nav_locations['navpos_location'] = array(
                              'primary',
                              'onepager'
                            );
          $default_nav_locations['navpos_desc'] = array(
                              esc_html__('Primary Navigation', 'plethora-framework'),
                              esc_html__('Single Landing Page Navigation', 'plethora-framework'),
                            );
          return $default_nav_locations;
       }


        public function theme_options_tab( $sections ) { 

            $adv_settings = array();
            $adv_settings[] = array(
                'id'           =>  THEMEOPTION_PREFIX .'navlocations',
                'type'         => 'repeater',
                'title'        => esc_html__( 'Navigation locations', 'plethora-framework' ),
                'subtitle'     => esc_html__('Add as many navigation locations as you need', 'plethora-framework'),
                'group_values' => true, // Group all fields below within the repeater ID
                'item_name'    => 'navigation location', // Add a repeater block name to the Add and Delete buttons
                'sortable'     => true, // Allow the users to sort the repeater blocks or not
                'fields'       => array(
                    array(
                        'id'    => 'navpos_desc',
                        'type'  => 'text',
                        'title' => esc_html__( 'Nav Location Description', 'plethora-framework' ),
                    ),
                    array(
                        'id'          => 'navpos_location',
                        'type'        => 'text',
                        'title'       => esc_html__( 'Nav Location Slug ( latin characters only, dashes instead of spaces, not use the same slug on other locations(s) )', 'plethora-framework' ),
                        'placeholder' => esc_html__( 'navigation-location', 'plethora-framework' ),
                    ),
                ),
                'default' => $this->default_nav_locations()
            );

            $sections[] = array(
                'subsection' => true,
                'title'      => esc_html__('Nav Locations', 'plethora-framework'),
                'heading'    => esc_html__('CUSTOM NAVIGATION LOCATIONS', 'plethora-framework'),
                'desc'       => esc_html__('This tool allows you to create navigation locations dynamically. In combination with this theme\'s flexible menu location options, you have the possibility to create pages with totally different navigation', 'plethora-framework'),
                'fields'     => $adv_settings
                );

            return $sections;
        }
    }
}