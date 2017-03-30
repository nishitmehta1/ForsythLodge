<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               	   (c) 2017

Navigation Mini Tools module base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Navminitools') ) {


  /**
   */
  class Plethora_Module_Navminitools {

	public static $feature_title         = "Navigation Mini Tools";   // FEATURE DISPLAY TITLE
	public static $feature_description   = "";                    // FEATURE DISPLAY DESCRIPTION 
	public static $theme_option_control  = true;                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
	public static $theme_option_default  = true;                  // DEFAULT ACTIVATION OPTION STATUS 
	public static $theme_option_requires = array();               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
	public static $dynamic_construct     = true;                  // DYNAMIC CLASS CONSTRUCTION? 
	public static $dynamic_method        = false;                 // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

	public function __construct() {

		// Should hook on init, to have available all the supported post types list
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {

		if ( is_admin() ) { 

			// Produce a backwards compatibility notice for Avoir / Xenia
			Plethora::add_admin_notice( 'navminitools_update', array(
				'title'             => esc_html__( 'Nav Mini Tools feature needs your attention!', 'plethora-framework' ),
				'notice'            => sprintf( esc_html__( 'This version includes a major update regarding the functionality of the Nav Mini Tool features. Don\'t worry, your data are safe, but you should just have a look at your %1$sTheme Options > Header > Nav Mini Tools%2$s section to make sure that your mini tools are displayed properly, according to the latest changes.', 'plethora-framework' ), '<strong>', '</strong>' ),
				'theme'             => array( 'avoir' => '1.2', 'hotel-xenia' => '1.2'),
				'theme_update_only' => true,
				'type'              => 'warning',
				'links'             => array(
					array( 
						'href'        => admin_url( 'admin.php?page=plethora_options' ),
						'anchor_text' => esc_html__( 'Get me to Theme Options', 'plethora-framework' ),
						'target'      => '_self',
					)
				),
			));

			// Create a tab with basic feature fields on theme options
			add_filter( 'plethora_themeoptions_header', array($this, 'add_theme_options'), 11);		// Add Theme Options subsection with basic fields
		}
			
		// Register all the default minitools that come with this class
		$this->register_minitool_custom();
		
	}

	// Checks all minitool element status...returns true if at least one is displayed
	public static function get_minitools_status() {

		$minitools = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-navminitools-status',  apply_filters( 'plethora_navminitools_default_status', array() ) );
		foreach ( $minitools as $minitool_slug => $status ) {

			if ( $status ) { return true; } // if at least one is true, return true
		}

		return false;
	}

	// Returns genaral configuration, along with all minitool separate configuration
	public static function get_minitools_output() {
		$output = '';
		$minitools = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-navminitools-status',  apply_filters( 'plethora_navminitools_default_status', array() ) );
		foreach ( $minitools as $minitool_slug => $status ) {
			
			$output .= $status ? self::get_minitool_output( $minitool_slug ) : '';
		}

		return $output;
	}

	// Checks given minitool element status
	public static function get_minitool_status( $minitool_slug  ) {

		$minitools = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-navminitools-status',  apply_filters( 'plethora_navminitools_default_status', array() ) );
		if ( !empty( $minitools[$minitool_slug] ) ) {

			return true;
		}

		return false;
	}

	/**
	* Returns user set minitool configuration
	*/
	public static function get_minitool_output( $minitool_slug ) {

		$output = '';
		$output_methods = apply_filters( 'plethora_navminitools_output_methods', array() );
		if ( ! empty( $output_methods[$minitool_slug] )  ) {

			$output_method = $output_methods[$minitool_slug];
			$output_method = !is_array( $output_method ) ? array( $output_method ) : $output_method;
			
			if ( !empty( $output_method[1] ) && method_exists( $output_method[0], $output_method[1] ) ) { // class method call

				$output = call_user_func( $output_method );

			} elseif ( empty( $output_method[1] ) && function_exists( $output_method ) ) { // function call

				$output = call_user_func( $output_method );
			}
		}

		return $output;
	}     

	/**
	* Setups a nav mini tool, using the appropriate filters
	* Returns true/false for succesfull/unsuccesfull registration
	*/
	public static function register_minitool( $args ) {

		$default_args = array(
			'slug'           => '',
			'title'          => '',
			'theme_options'  => array(),
			'output_method'  => array(),
		);

		$args = wp_parse_args( $args, $default_args );
		if ( !empty( $args['slug'] ) && !empty( $args['title'] ) && !empty( $args['theme_options'] ) && !empty( $args['output_method'] ) ) {

			// Some sanitization
			$args['slug'] = sanitize_key( $args['slug'] );
			// Add it to the status/order field
			add_filter( 'plethora_navminitools_title', function( $minitools = array() ) use ( $args ) { 

				$minitools[$args['slug']] = $args['title'];
				ksort( $minitools ); 
				return $minitools;
			});
			// Set Options
			add_filter( 'plethora_navminitools_theme_options', function( $theme_options = array() ) use ( $args ) { 

				return array_merge( $theme_options, $args['theme_options'] ); 
			});

			// Set Config method
			add_filter( 'plethora_navminitools_output_methods', function( $output_methods = array() ) use ( $args ) { 

				$output_methods[$args['slug']] = $args['output_method']; 
				return $output_methods;
			});

			return true;
		}

		return false;
	}

   /**
	* Adds the same option fields under the 'Theme Options > Content > Single { Post } > Auxiliary Navigation' section
	* and the Auxiliary Navigation section of the single post metabox
	* Hooked on 'plethora_metabox_single_{ post_type }_auxiliary-navigation_fields'
	*/
	public function add_theme_options( $sections ) {

		// setup theme options according to configuration
		$opts        = $this->theme_options();
		$opts_config = $this->theme_options_config();
		$fields      = array();
		foreach ( $opts_config as $opt_config ) {

			$id          = $opt_config['id'];
			$status      = $opt_config['theme_options'];
			$default_val = $opt_config['theme_options_default'];
			if ( $status && array_key_exists( $id, $opts ) ) {

		  		if ( !is_null( $default_val ) ) { // will add only if not NULL }
			
					$opts[$id]['default'] = $default_val;
		  		}

		  		$fields[] = $opts[$id];
			}
	  	}

	  	if ( !empty( $fields ) ) {
			$sections[] = array(
				'title'      => esc_html__( 'Nav Mini Tools', 'plethora-framework'),
				'heading'    => esc_html__( 'HEADER SECTION // NAV MINI TOOLS', 'plethora-framework'),
				'desc'       => sprintf( esc_html__( 'Mini tools, usually, are icons or small text parts displayed on the top header section of each page. In example, if WooCommerce is installed, an %1$sAjax Cart%2$s and %1$sMy Account%2$s icons will be available as mini tool items for display. You may use the %1$sCustom Markup%2$s mini tool to display your custom implementation.', 'plethora-framework'), '<strong>', '</strong>' ),
				'subsection' => true,
				'fields'     => $fields,
			);
		}

	  	return $sections;
	}

   /** 
	* Returns single options index for 'Theme Options > Content > Single { Post Type }' tab 
	* and the single post edit metabox. 
	*/
	function theme_options() {

		$theme_options['tools-status'] = array(
			'id'       => THEMEOPTION_PREFIX .'header-navminitools-status',
			'type'     => 'sortable',
			'title'    => esc_html__('Mini Tools Display & Order', 'plethora-framework'),
			'subtitle' => esc_html__('Set display status and order for all mini tools available', 'plethora-framework'),
			'mode'     => 'checkbox',
			'options'  => apply_filters( 'plethora_navminitools_title', array() ),
		);
		$theme_options['switch-to-mobile'] = array(
			'id'       => THEMEOPTION_PREFIX .'less-header-navminitools-switch-to-mobile',
			'type'     => 'spinner', 
			'title'    => esc_html__('Mini Tools To Mobile Menu Threshold', 'hotel-xenia'),
			'subtitle' => esc_html__('Default: 0px', 'hotel-xenia'),
			'desc'     => esc_html__('Set the monitor width threshold for the mini tools to be moved under mobile menu. You may set from 0px to 3840x', 'hotel-xenia'),
			"min"      => 0,
			"step"     => 1,
			"max"      => 3840,
		);

	  	return apply_filters( 'plethora_navminitools_theme_options', $theme_options );
	}

	/** 
	* Returns single options configuration for 'Theme Options > Content > Single { Post Type }' tab 
	* and the single post edit metabox. 
	* You should override this method on the extension class
	*/
	public function theme_options_config() {

	  return array();
	}

	/**
	* Returns full html markup, according to theme options
	* No display status is applied,
	*/
	public function register_minitool_custom() {

		$theme_options['custom-section'] = array(
			'id'       => THEMEOPTION_PREFIX .'header-navminitools-custom-section',
			'type'     => 'section',
			'title'    => esc_html__('Mini Tool > Custom Markup', 'hotel-xenia'),
			'indent'   => true,
		);

		$theme_options['custom-markup'] = array(
			'id'           => THEMEOPTION_PREFIX .'header-navminitools-custom-markup',
			'type'         => 'textarea',
			'title'        => esc_html__( 'Your custom markup here', 'plethora-framework' ),
			'validate'     => 'html_custom',
			'allowed_html' => Plethora_Theme::allowed_html_for( 'post' ),
			'desc'         => esc_html__('You may enter shortcode markup.', 'plethora-framework' ) .' '. Plethora_Theme::allowed_html_for( 'post', true ), 
			'translate'    => true,
		);

		$args = array(
			'slug'           => 'custom',
			'title'          => esc_html__( 'Custom Markup', 'plethora-framework' ),
			'desc'           => esc_html__( 'Custom Markup', 'plethora-framework' ),
			'theme_options'  => $theme_options,
			'output_method'  => array( $this, 'get_minitool_output_custom' ),
		);

		self::register_minitool( $args );
	}

	public function get_minitool_output_custom() {

		$output = '<div class="nav_mini_tool_container">';
		$output .= do_shortcode( Plethora_Theme::option( METAOPTION_PREFIX .'header-navminitools-custom-markup', '' ) );
		$output .= '</div>';
		return $output;
	}
  }
}