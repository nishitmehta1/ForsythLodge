<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               	   (c) 2017

Booking Management Module Base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Booking') ) {

	/**
	*/
	class Plethora_Module_Booking {

	public static $feature_title         = "Booking Management Module";   // FEATURE DISPLAY TITLE
	public static $feature_description   = "";                    // FEATURE DISPLAY DESCRIPTION 
	public static $theme_option_control  = true;                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
	public static $theme_option_default  = true;                  // DEFAULT ACTIVATION OPTION STATUS 
	public static $theme_option_requires = array();               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
	public static $dynamic_construct     = true;                  // DYNAMIC CLASS CONSTRUCTION? 
	public static $dynamic_method        = false;                 // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

	public $supported_post_types  = array( 'room', 'service' );
	public $post_type;

		public function __construct() {

		  // Should hook on init, to have available all the supported post types list
		  add_action( 'init', array( $this, 'init' ) );

		}

		public function init( ) {

			if ( is_admin() ) { 

				// Add Booking Info tabs on all supported post types
				foreach ( $this->supported_post_types as $supported_post_type ) {

					add_filter( 'plethora_single_'.$supported_post_type.'_options_sections_index', array( $this, 'add_sections_to_single_options_index'), 10 );
				}

				// Add Booking Info options on given single post theme option tabs & metaboxes ( on section: 'Auxiliary Navigation' )
				foreach ( $this->supported_post_types as $supported_post_type ) {
					add_filter( 'plethora_themeoptions_single_'. $supported_post_type .'_booking_fields', array( $this, 'add_single_options'), 15, 2 );
					add_filter( 'plethora_metabox_single_'. $supported_post_type .'_booking_fields', array( $this, 'add_single_options'), 15, 2 );
				}
			}
		}

		public static function add_sections_to_single_options_index( $sections_index ) {
			$sections_index['booking'] = array(
				'title'    => esc_html__('Booking Info', 'plethora-framework'),
				'icon'     => 'fa fa-calendar-check-o',
				'class'    => 'ple_metabox_special_tab',
			);
			return $sections_index;
		}

		/**
		* Returns user set target price status for a single post
		*/
		public static function get_target_price_status( $post_type, $post_id ) {

			$status = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-booking-targetprice', false, $post_id );
			return $status ;
		}

		/**
		* Returns target price display options for a single post
		*/
		public static function get_target_price_options( $post_type, $post_id ) {

			$options['target_price_text']        = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-booking-targetprice-text', '', $post_id );
			$options['target_price_text_before'] = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-booking-targetprice-text-before', '', $post_id );
			$options['target_price_text_after']  = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-booking-targetprice-text-after', '', $post_id );
			return apply_filters( 'plethora_booking_target_price_options', $options, $post_type, $post_id ) ;
		}


		/**
		* Returns user set full price field status for a single room post
		*/
		public static function get_full_price_status( $post_type, $post_id ) {

			$status = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-booking-pricelist', false, $post_id );
			return $status ;
		}

		/**
		* Returns full price field display options for a single post
		*/
		public static function get_full_price_options( $post_type, $post_id ) {

			$options['full_price_text']	= Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-booking-pricelist-text', '', $post_id );
			return apply_filters( 'plethora_booking_full_price_options', $options, $post_type, $post_id ) ;
		}

		/**
		* Returns user set booking status for a single post
		*/
		public static function get_persons_status( $post_type, $post_id ) {

			$status = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-booking-persons', false, $post_id );
			return $status ;
		}

		/**
		* Returns user set booking status for a single post
		*/
		public static function get_persons_options( $post_type, $post_id ) {

			$options['persons_text']        = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-booking-persons-text', '', $post_id );
			return apply_filters( 'plethora_booking_persons_options', $options, $post_type, $post_id ) ;
		}

		/**
		* Returns complete booking configuration for a single post
		*/
		public static function get_template_options() {

			$template_options = array();
			return apply_filters( 'plethora_booking_template_options', $template_options ) ;
		}     

		public static function get_third_party_methods( $return_options = false ) {

			$return              = array();
			$third_party_methods = apply_filters( 'plethora_booking_methods', $return );
			if ( $return_options ) {

				foreach ( $third_party_methods as $key => $method ) {

					$return[$key] = $method['title'];
				}

			} else {

				$return = $third_party_methods;
			}
			return $return;
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
				$status      = ( current_filter() === 'plethora_themeoptions_single_'. $post_type .'_booking_fields' ) ? $opt_config['theme_options'] : $opt_config['metabox'] ;
				$default_val = ( current_filter() === 'plethora_themeoptions_single_'. $post_type .'_booking_fields' ) ? $opt_config['theme_options_default'] : $opt_config['metabox_default'];
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
		public static function single_options( $post_type ) {
		  	
		  	$supported_plugins = self::get_third_party_methods();
			$desc = sprintf( esc_html__( '%s includes a simple pricing display functionality. ', 'plethora-framework'), '<strong>'. THEME_DISPLAYNAME .'</strong>' ); 
			$desc .= esc_html__( 'If you need something more advanced, have in mind that the theme provides full support for the plugins below. After activating any of these plugins, it will become available for selection on this option.', 'plethora-framework');
			$desc .= '<ul>'; 
		  	foreach ( $supported_plugins as $plugin ) {

				$desc .= sprintf( '<li style="background-color:#f1f1f1; padding: 10px; margin-bottom: 5px !important;"><strong>'. $plugin['title'] .'</strong> by <a href="%1$s" target="_blank">%2$s</a><br><small><strong>'. $plugin['desc'].'</strong></small></li>', $plugin['link'], $plugin['author']  );
		  	}
			$desc .= '</ul>';
			$single_options['booking']  = array(
				'id'      =>  METAOPTION_PREFIX . $post_type .'-booking',
				'type'    => 'button_set',
				'title'   => esc_html__( 'Book Pricing Management', 'plethora-framework' ),
				'desc'    => $desc, 
				'options' => array_merge( 
					array( 
						false    => esc_html__( 'Deactivate', 'plethora-framework' ),
						'simple' => esc_html__( 'Simple Pricing Info Display', 'plethora-framework' ),
					),
					self::get_third_party_methods( true ) 
				)
			);

			$single_options['targetprice']  = array(
				'id'       =>  METAOPTION_PREFIX . $post_type .'-booking-targetprice',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display Target Price Tag', 'plethora-framework' ),
				'desc'     => sprintf( esc_html__( 'Display a target price tag on single %s post view', 'plethora-framework' ), $post_type ),
				'required' => array(
					array( METAOPTION_PREFIX . $post_type .'-booking', 'equals', array( 'simple' ) ),
				)
			);

			$single_options['targetprice-text']  = array(
				'id'           =>  METAOPTION_PREFIX . $post_type .'-booking-targetprice-text',
				'type'         => 'text',
				'title'        => esc_html__( 'Target Price Tag', 'plethora-framework' ),
				'desc'         => sprintf( esc_html( 'Note that even if you choose not to display the target price tag on the single %s view, this can be also used, as is, on several other theme elements ( shortcodes, widgets, etc. ). ', 'plethora-framework'), $post_type ) . Plethora_Theme::allowed_html_for( 'paragraph', true ),
				'validate'     => 'html',
				'html_allowed' => Plethora_Theme::allowed_html_for( 'paragraph' ),
				'required'     => array(
					array( METAOPTION_PREFIX . $post_type .'-booking', 'equals', array( 'simple' ) ),
				)
			);

			$single_options['targetprice-text-before']  = array(
				'id'           =>  METAOPTION_PREFIX . $post_type .'-booking-targetprice-text-before',
				'type'         => 'text',
				'title'        => esc_html__( 'Target Price Tag // Before Text', 'plethora-framework' ),
				'desc'         => Plethora_Theme::allowed_html_for( 'paragraph', true ),
				'validate'     => 'html',
				'html_allowed' => Plethora_Theme::allowed_html_for( 'paragraph' ),
				'required'     => array(
					array( METAOPTION_PREFIX . $post_type .'-booking', 'equals', array( 'simple' ) ),
					array( METAOPTION_PREFIX . $post_type .'-booking-targetprice', 'equals', array( true ) ),
				)
			);


			$single_options['targetprice-text-after']  = array(
				'id'           =>  METAOPTION_PREFIX . $post_type .'-booking-targetprice-text-after',
				'type'         => 'text',
				'title'        => esc_html__( 'Target Price Tag // After Text', 'plethora-framework' ),
				'desc'         => Plethora_Theme::allowed_html_for( 'paragraph', true ),
				'validate'     => 'html',
				'html_allowed' => Plethora_Theme::allowed_html_for( 'paragraph' ),
				'required'     => array(
					array( METAOPTION_PREFIX . $post_type .'-booking', 'equals', array( 'simple' ) ),
					array( METAOPTION_PREFIX . $post_type .'-booking-targetprice', 'equals', array( true ) ),
				)
			);

			$single_options['pricelist']  = array(
				'id'       =>  METAOPTION_PREFIX . $post_type .'-booking-pricelist',
				'type'     => 'switch',
				'title'    => esc_html__( 'Display Full Price List', 'plethora-framework' ),
				'desc'     => sprintf( esc_html__( 'Display a complete price list with your own markup on single %s post view', 'plethora-framework' ), $post_type ),
				'required' => array(
					array( METAOPTION_PREFIX . $post_type .'-booking', 'equals', array( 'simple' ) ),
				)
			);

			$single_options['pricelist-text']  = array(
				'id'           =>  METAOPTION_PREFIX . $post_type .'-booking-pricelist-text',
				'type'         => 'textarea',
				'title'        => esc_html__( 'Full Price List', 'plethora-framework' ),
				'desc'         => Plethora_Theme::allowed_html_for( 'post', true ),
				'validate'     => 'html',
				'html_allowed' => Plethora_Theme::allowed_html_for( 'post' ),
				'required'     => array(
					array( METAOPTION_PREFIX . $post_type .'-booking', 'equals', array( 'simple' ) ),
					array( METAOPTION_PREFIX . $post_type .'-booking-pricelist', 'equals', array( true ) ),
				)
			);
			return $single_options;
		}

		public function get_single_option_config_default( $post_type, $option_id ) {

			// setup theme options according to configuration
			$opts_config = $this->single_options_config( $post_type );
			if ( array_key_exists( $option_id, $opts_config ) ) {

				$default_val = ! is_null( $opt_config['theme_options_default'] )  ? $opt_config['theme_options_default'] : $opt_config['metabox_default'];
				return $default_val;
			}
			return '';
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