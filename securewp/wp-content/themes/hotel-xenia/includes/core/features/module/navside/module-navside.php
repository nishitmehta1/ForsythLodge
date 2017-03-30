<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			       (c) 2017

Side Navigation ( mobile sidebar ) Module Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Navside') ) {

	/**
	 */
	class Plethora_Module_Navside {

		public static $feature_title        = "Side Navigation ( mobile sidebar ) Module";
		public static $feature_description  = "Manages side navigation section";
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		public function __construct() {
			
			// Add theme options fields tab to Footer Section
			add_filter( 'plethora_themeoptions_header', array( $this, 'theme_options'), 11 );
			// Add metabox options to single post's Footer Section
			add_filter( 'plethora_metabox_header_fields_edit', array( $this, 'metabox_options'), 11 );
			// Prepare and add variables to the LESS index
			add_filter('plethora_module_wpless_variables', array( $this, 'less_variables' ) );
			// Theme.js variables setup
			add_action( 'get_header', array( $this, 'set_js'), 999 );
			// SPECIAL: add off container markup
			add_action( 'get_header', array( $this, 'off_container_hooks'), 999 );
		}

		/**
		 * Returns widgetized areas template status for the footer area according to given row
		 */
		public function get_template_status( $args = array() ) {

			$status = true; 
			return $status;
		}

		/**
		 * Returns widgetized areas template options configuration according to given row
		 */
		public function get_template_config( $args = array() ) {
			$options = array();

			if ( empty( $args['return'] ) || $args['return'] === 'sidebar' ) {
				$sidebar_slug = Plethora_Theme::option( METAOPTION_PREFIX .'header-mobsb-widgetizedarea', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-mobsb-widgetizedarea' ) );
				ob_start();
				dynamic_sidebar( esc_attr( $sidebar_slug ) ); 
				$sidebar            = ob_get_clean();
				$options['class']   = 'secondary_nav_widgetized_area';
				$options['sidebar'] = $sidebar;

			}
			return $options;
		}

		/**
		 * Returns header navigation toggler elements markup
		 * Hooked @ 'plethora_header_main_after_container_markup' action
		 */
		public function get_toggler() {

			$options['label_more']      = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-mobsb-label-before-threshold' ) );
			$options['label_more_text'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold-text', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-mobsb-label-before-threshold-text' ) );
			$options['label_menu']      = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-mobsb-label-after-threshold' ) );
			$options['label_menu_text'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold-text', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-mobsb-label-after-threshold-text' ) );
			$options['navicon_class']   = ( ! Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-navicon', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-mobsb-navicon' ) ) ) ? ' hidden_above_threshold' : '';
			set_query_var( 'options', $options );
			get_template_part( 'templates/header/header_main/navside_toggler' );
		}

		/*
		 * Add all elements that should be placed on Off-container positions
		 * This should be a list of add_action calls
		 */
		public function off_container_hooks() {

			add_action( 'plethora_header_main_after_container_markup', array( $this, 'get_toggler' ) );
		}

	 	/*
		* Script events, used mostly for declaring variables to theme.js 
		* using the Plethora_Theme::set_themeconfig() method
		* Hooked at 'get_header' action
		*/
		public function set_js() {

			$menu_switch_to_mobile  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-switch-to-mobile', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'less-menu-switch-to-mobile' ) );
			Plethora_Theme::set_themeconfig( 'GENERAL', array( 'menu_switch_to_mobile' => $menu_switch_to_mobile ) );
		}

		/** 
		* Returns theme options tab configuration
		* Hooked @ 'plethora_themeoptions_header'
		* @return array()
		*/
		public function theme_options( $sections ) {

			$theme_options = Plethora_Module_Themeoptions::get_themeoptions_fields( $this );
			if ( is_array( $theme_options ) && !empty( $theme_options ) ) {

				$sections[] = array(
					'title'      => esc_html__('Side Nav', 'plethora-framework'),
					'heading'    => esc_html__('HEADER SECTION // SIDE NAVIGATION OPTIONS', 'plethora-framework'),
					'desc'       => '',
					'subsection' => true,
					'fields'     => $theme_options
				);
			}
			return $sections;
		}

		/** 
		* Returns theme options tab configuration
		* Hooked @ 'plethora_metabox_header_fields_edit'
		* @return array()
		*/
		public function metabox_options( $fields ) {

			$metabox_fields = Plethora_Module_Themeoptions::get_metabox_fields( $this );
			if ( ! empty( $metabox_fields ) ) {

				$fields = array_merge( $fields, $metabox_fields );
			}
			return $fields;
		}

		/** 
		* Prepares and returns all LESS variable options
		* Hooked @ 'plethora_module_wpless_variables'
		* @return array
		*/
		public function less_variables( $less_vars ) { 

			$menu_switch_to_mobile                 = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-switch-to-mobile', '991', 0, false);
			$less_vars['wp-menu-switch-to-mobile'] = $menu_switch_to_mobile . 'px';
			return $less_vars;
		}

		/** 
		* MUST HAVE METHOD FOR ALL MODULES USING OPTIONS
		* Returns theme options / metabox fields index
		* Options configuration should not contain 'default' value ( anyway, it will be ignored on the late configuration)
		* @return array()
		*/
		public function options_index() { 

			$options_index = array();

			$options_index['menu-switch-to-mobile'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-menu-switch-to-mobile',
				'type'     => 'spinner', 
				'title'    => esc_html__('Switch To Mobile Menu Threshold', 'plethora-framework'),
				'subtitle' => esc_html__('Default: 991px', 'plethora-framework'),
				'desc'     => esc_html__('Set the monitor width threshold for the mobile menu to be enabled. You may set from 0px to 3840x', 'plethora-framework'),
				"min"      => 0,
				"step"     => 1,
				"max"      => 3840,
			);

			$options_index['mobsb-label-before-threshold'] = array(
				'id'      => THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold',
				'type'    => 'switch',
				'title'   => esc_html__('Label Display ( above threshold )', 'plethora-framework'),
			);

			$options_index['mobsb-label-before-threshold-text'] = array(
				'id'           => THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold-text',
				'type'         => 'text',
				'title'        => esc_html__('Label Text ( above threshold )', 'plethora-framework'),
				'desc'         => Plethora_Theme::allowed_html_for( 'button', true),
				'validate'     => 'html_custom',
				'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
				'translate'    => true,
				'required'     => array( 
					array( THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold','=', 1 ),
				),						
			);

			$options_index['mobsb-label-after-threshold'] = array(
				'id'      => THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold',
				'type'    => 'switch',
				'title'   => esc_html__('Label Display  ( below threshold )', 'plethora-framework'),
			);

			$options_index['mobsb-label-after-threshold-text'] = array(
				'id'           => THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold-text',
				'type'         => 'text',
				'title'        => esc_html__('Label Text ( below threshold )', 'plethora-framework'),
				'desc'         => Plethora_Theme::allowed_html_for( 'button', true),
				'validate'     => 'html_custom',
				'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
				'translate'    => true,
				'required'     => array( 
					array( THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold','=', 1 ),
				),						
			);

			$options_index['mobsb-navicon'] = array(
				'id'      => THEMEOPTION_PREFIX .'header-mobsb-navicon',
				'type'    => 'switch',
				'title'   => esc_html__( 'Nav Icon Display ( above threshold )', 'plethora-framework' ),
			);

			$options_index['mobsb-widgetizedarea'] = array(
				'id'       => METAOPTION_PREFIX .'header-mobsb-widgetizedarea',
				'type'     => 'select',
				'title'    => esc_html__('Widgets Area', 'plethora-framework'), 
				'desc'     => esc_html__('Select the widgets area ( sidebar ) that you want to be displayed on the mobile sidebar', 'plethora-framework'), 
				'data'     => 'sidebars',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',1),
				),
			);

			return $options_index;
		}

		/** 
		* ONLY FOR EXTENSION CLASS USE, THIS IS PLACED HERE FOR REFERENCE & CONSISTENCY
		*
		* Sets a configuration pattern for theme options / metabox fields. You can set the display order
		* ( according to the order given here ) and whether you want a field to be displayed on theme options
		* or the metabox view and finally its default value on both views.
		*
		* 'id': 					the option index key ( don't confuse this with the actual DB saved id )
		* 'theme_options': 			display this field on theme options ( true|false )
		* 'theme_options_default': 	default value, null if we don't need one ( multi|null )
		* 'metabox': 				display this field on metabox options ( true|false )
		* 'metabox_default': 		default value for metabox option, null if we want to inherit the theme options default value ( multi|null)
		*
		* @return array()
		*/
		public function options_config() {

			return array();
		}
	}
}