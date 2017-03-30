<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Custom sidebars manager

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Sidebars') ) {

	/**
	 */
	class Plethora_Module_Sidebars {

		public static $feature_title        = "Custom Sidebars Manager";	// Feature display title  (string)
		public static $feature_description  = "Integration module for Plethora custom sidebars manager"; // Feature display description (string)
		public static $theme_option_control = false;	// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default	= false;	// Default activation option status ( boolean )
		public static $theme_option_requires= array();	// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct	= true;		// Dynamic class construction ? ( boolean )
		public static $dynamic_method		= false;	// Additional method invocation ( string/boolean | method name or false )

		function __construct(){

		  if ( is_admin() ) { 
		      // Set theme options tab for media panel
		      add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_options_tab'), 30);
		  }

          add_action( 'widgets_init', array( $this, 'theme_sidebars' ), 5);  // User defined sidebars. Inherited from Plethora class
		}

		/**
		* Custom sidebars setup
		* @since 1.0
		*
		*/
		public function theme_sidebars(){

			$default_sidebars = $this->default_sidebars();
		    $sidebars   = Plethora_Theme::option( THEMEOPTION_PREFIX .'sidebars', $default_sidebars );
		    if ( isset( $sidebars['sidebar_slug'] ) ) { 
			    foreach ($sidebars['sidebar_slug'] as $key => $sidebar_slug ) {
			     if ( !empty( $sidebars['sidebar_name'][$key] ) ) { 

			          $sidebar = array(
			              'name'          => esc_html( $sidebars['sidebar_name'][$key] ),
			              'id'            => !empty( $sidebars['sidebar_slug'][$key] ) ?  sanitize_title_with_dashes( $sidebars['sidebar_slug'][$key] ) : sanitize_title_with_dashes( $sidebars['sidebar_name'][$key] ),
			              'description'   => esc_html( $sidebars['sidebar_desc'][$key] ),
			              'class'         => esc_attr( $sidebars['sidebar_class'][$key] ),
			              'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			              'after_widget'  => '</aside>',
			              'before_title'  => '<h4>',
			              'after_title'   => '</h4>' );  
			          register_sidebar( $sidebar );
			      } 
			    }
		    }
	    }

	      /**
	       * Set default sidebars 
	       * @since 1.0
	       *
	       */
	    public function default_sidebars() {


	   		// echo '<div align="center">FRAMEWORK!</div>' . get_called_class();
			// Execute this only on first page load
			$default_sidebars = array();
			// IMPORTANT: this is necessary for repeater field...add a line for each sidebar record
			$default_sidebars['redux_repeater_data'] = array(
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			               );
			$default_sidebars['sidebar_name'] = array(
			                  esc_html__('Blog Sidebar', 'plethora-framework'),
			                  esc_html__('Pages Sidebar', 'plethora-framework'),
			                  esc_html__('Shop Sidebar', 'plethora-framework'),
			                  esc_html__('Footer Widgets Area #1', 'plethora-framework'),
			                  esc_html__('Footer Widgets Area #2', 'plethora-framework'),
			                  esc_html__('Footer Widgets Area #3', 'plethora-framework'),
			                  esc_html__('Footer Widgets Area #4', 'plethora-framework'),
			                );
			$default_sidebars['sidebar_desc'] = array(
			                  esc_html__('Default sidebar to add widgets for blog archives & posts', 'plethora-framework'),
			                  esc_html__('Default sidebar to add widgets for single pages', 'plethora-framework'),
			                  esc_html__('Default sidebar to add widgets for shop pages', 'plethora-framework'),
			                  esc_html__('Footer widgets area #1', 'plethora-framework'),
			                  esc_html__('Footer widgets area #2', 'plethora-framework'),
			                  esc_html__('Footer widgets area #3', 'plethora-framework'),
			                  esc_html__('Footer widgets area #4', 'plethora-framework'),
			                );
			$default_sidebars['sidebar_slug'] = array(
                          	  'sidebar-default',
                          	  'sidebar-pages',
                          	  'sidebar-shop',
			                  'sidebar-footer-one',
			                  'sidebar-footer-two',
			                  'sidebar-footer-three',
			                  'sidebar-footer-four'
			                );
	        $default_sidebars['sidebar_class'] = array( '', '', '', '', '', '', '' );
	        return $default_sidebars;
	    }


	    public function theme_options_tab( $sections ) { 

			$adv_settings = array();
			$adv_settings[] = array(
				'id'            =>  THEMEOPTION_PREFIX .'sidebars',
				'type'          => 'repeater',
				'title'         => esc_html__( 'Sidebars', 'plethora-framework' ),
				'subtitle'      => esc_html__('Add as many sidebars as you need', 'plethora-framework'),
				'group_values'  => true, // Group all fields below within the repeater ID
				'item_name'     => 'sidebar', // Add a repeater block name to the Add and Delete buttons
				//'bind_title' => 'sidebar', // Bind the repeater block title to this field ID
				//'static'      => 2, // Set the number of repeater blocks to be output
				'limit'         => 100, // Limit the number of repeater blocks a user can create
				'sortable'      => true, // Allow the users to sort the repeater blocks or not
				'fields'        => array(
	                array(
						'id'          => 'sidebar_name',
						'type'        => 'text',
						'title'       => esc_html__( 'Title', 'plethora-framework' ),
						'placeholder' => esc_html__( 'Sidebar title', 'plethora-framework' ),
	                ),
	                array(
						'id'          => 'sidebar_desc',
						'type'        => 'text',
						'title'       => esc_html__( 'Description ( not necessary )', 'plethora-framework' ),
						'placeholder' => esc_html__( 'Sidebar description', 'plethora-framework' ),
	                ),
	                array(
						'id'          => 'sidebar_slug',
						'type'        => 'text',
						'title'       => esc_html__( 'Sidebar slug ( latin characters only, dashes instead of spaces, not use the same slug on other sidebar(s) )', 'plethora-framework' ),
						'placeholder' => esc_html__( 'your-sidebar-slug', 'plethora-framework' ),
	                ),
	                array(
						'id'    => 'sidebar_class',
						'type'  => 'text',
						'title' => esc_html__( 'CSS class ( not necessary )', 'plethora-framework' ),
	                ),
	            ),
				'default' => $this->default_sidebars()
			);

			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Sidebars', 'plethora-framework'),
				'heading'    => esc_html__('CUSTOM SIDEBARS', 'plethora-framework'),
				'desc'       => esc_html__('This tool allows you to create sidebars dynamically. In combination with this theme\'s flexible sidebar options, you have the possibility to create pages with totally different widgetized areas', 'plethora-framework'),
				'fields'     => $adv_settings
				);

			return $sections;
	    }
	}
}