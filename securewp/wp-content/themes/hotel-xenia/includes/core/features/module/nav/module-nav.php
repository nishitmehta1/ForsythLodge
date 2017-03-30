<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			       (c) 2017

Main Navigation Module Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Nav') ) {

	class Plethora_Module_Nav {

		public static $feature_title        = "Main Navigation";
		public static $feature_description  = "Manages main navigation section";
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
		}

		/**
		 * Returns widgetized areas template status for the footer area according to given row
		 */
		public function get_template_status( $args = array() ) {

			$status =  Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'navigation-main' ) );
			return $status;
		}

		/**
		 * Returns widgetized areas template options configuration according to given row
		 */
		public function get_template_config( $args = array() ) {

			// Some core options 
			$options['nav_status']          = $this->get_template_status();
			$options['nav_location']        = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main-location', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'navigation-main-location' ) );
			$options['nav_class_behavior']  = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main-behavior', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'navigation-main-behavior' ) );

			// Additional options for the custom sticky menu
			$options['sticky_status']        = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'stickyheader' ), THEMEOPTION_PREFIX .'navigation-sticky' ) );
			$options['sticky_custom_status'] = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'stickyheader' ), THEMEOPTION_PREFIX .'navigation-sticky-custom' ) );
			$options['sticky_custom_menu']   = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-menu', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'stickyheader' ), THEMEOPTION_PREFIX .'header-sticky-custom-menu' ) );
			$options['nav_class']  = 'primary_nav';
			$options['nav_class']  .= $options['nav_status'] ? '' : ' hidden_above_threshold';
			$options['nav_class']  .= $options['sticky_status'] && $options['sticky_custom_status'] && $options['sticky_custom_menu'] ? ' shown_on_header_stuck' : ' hidden_on_header_stuck';
			
			/* Use ob_start() to fire 'plethora_navigation_before'/'plethora_navigation_after' hooks
				 and the wp_nav_menu() items and get the result in variables */

			// Navigation items output
			ob_start();
			wp_nav_menu( 
				array(
					'container'      => false, 
					'menu_class'     => 'top_level_ul nav '. $options['nav_class_behavior'] , 
					'container'      => 'ul',
					'depth'          => 6,
					'theme_location' => $options['nav_location'],
					'walker'         => class_exists( 'Plethora_Module_Navwalker_Ext' ) ? new Plethora_Module_Navwalker_Ext() : ''
				)
			);
			$options['nav_output'] = ob_get_clean();

			return $options;
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
					'title'      => esc_html__('Main Navigation', 'plethora-framework'),
					'heading'	 => esc_html__('HEADER SECTION // MAIN NAVIGATION OPTIONS', 'plethora-framework'),
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

			// Main Navigation ( ok )
			$menu_font            = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-font', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-menu-font' ), 0, false);
			$menu_font_weight     = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-font-weight', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-menu-font-weight' ), 0, false);
			$menu_padd            = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-item-padding', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-menu-font-padding' ), 0, false);
			$menu_padd_md         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-item-padding-md', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-menu-font-padding-md' ), 0, false);
			$menu_padd_sm         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-item-padding-sm', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-menu-font-padding-sm' ), 0, false);
			$less_vars['wp-menu-font-size']                  = $menu_font['font-size'];
			$less_vars['wp-menu-text-transform']             = $menu_font['text-transform'];
			$less_vars['wp-menu-font-weight']                = $menu_font_weight;
			$less_vars['wp-menu-item-vertical-padding']      = $menu_padd['height'] . 'px';
			$less_vars['wp-menu-item-vertical-padding-md']   = $menu_padd_md['height'] . 'px';
			$less_vars['wp-menu-item-vertical-padding-sm']   = $menu_padd_sm['height'] . 'px';
			$less_vars['wp-menu-item-horizontal-padding']    = $menu_padd['width'] . 'px';
			$less_vars['wp-menu-item-horizontal-padding-md'] = $menu_padd_md['width'] . 'px';
			$less_vars['wp-menu-item-horizontal-padding-sm'] = $menu_padd_sm['width'] . 'px';
			// $menu_widg_area_width = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-secondary-widgetized-area-width', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-menu-font' ), 0, false);
			// $less_vars['wp-secondary-widgetized-area-width'] = $menu_widg_area_width['width'] . 'px';
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

			$options_index['nav-main'] = array(
				'id'      => METAOPTION_PREFIX .'navigation-main',
				'type'    => 'switch', 
				'title'   => esc_html__('Display Main Menu', 'plethora-framework'),
				'on'      => esc_html__('Display', 'plethora-framework') ,
				'off'     => esc_html__('Hide', 'plethora-framework'),
			);
			$options_index['nav-main-location'] = array(
				'id'       => METAOPTION_PREFIX .'navigation-main-location',
				'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
				'type'     => 'select',
				'title'    => esc_html__('Main Menu Location', 'plethora-framework'), 
				'desc'     => esc_html__('Select the default location to be displayed as your main menu. You have the option to change the main navigation location for every page. ', 'plethora-framework'),
				'data'     => 'menu_locations',
			);
			$options_index['nav-main-behavior'] = array(
				'id'          => METAOPTION_PREFIX .'navigation-main-behavior',
				'required'    => array( METAOPTION_PREFIX .'navigation-main','=',1),						
				'type'        => 'button_set', 
				'title'       => esc_html__('Multi Level Menu Behavior', 'plethora-framework'),
				'description' => esc_html__('Choose action to trigger child menu items display', 'plethora-framework') ,
				'options'     => array(
					'hover_menu' => esc_html__('Mouse Hover', 'plethora-framework'),
					'click_menu' => esc_html__('Click', 'plethora-framework'),
				),
			);
			$options_index['nav-main-font'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-menu-font',
				'required'       => array( METAOPTION_PREFIX .'navigation-main','=',1),						
				'type'           => 'typography', 
				'title'          => esc_html__('Menu Item Font Options', 'plethora-framework'),
				'google'         => false, 
				'font-family'    => false,
				'font-style'     => false,
				'font-weight'    => false,
				'font-size'      => true,
				'line-height'    => false,
				'word-spacing'   => false,
				'letter-spacing' => false,
				'text-align'     => false,
				'text-transform' => true,
				'color'          => false,
				'subsets'        => false,
				'preview'        => false, 
				'all_styles'     => false, // import all google font weights
			);
			$options_index['nav-main-font-weight'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-menu-font-weight',
				'required'       => array( METAOPTION_PREFIX .'navigation-main','=',1),						
				'type'           => 'select', 
				'title'          => esc_html__('Menu Item Font Weight', 'plethora-framework'),
				'options' 		 => array(
					'light'     => esc_html__('Light', 'plethora-framework'),
					'normal'    => esc_html__('Normal', 'plethora-framework'),
					'semi-bold' => esc_html__('Semi Bold', 'plethora-framework'),
					'bold'      => esc_html__('Bold', 'plethora-framework'),
					'bolder'    => esc_html__('Bolder', 'plethora-framework'),
					'100'       => esc_html__('100', 'plethora-framework'),
					'200'       => esc_html__('200', 'plethora-framework'),
					'300'       => esc_html__('300', 'plethora-framework'),
					'400'       => esc_html__('400', 'plethora-framework'),
					'500'       => esc_html__('500', 'plethora-framework'),
					'600'       => esc_html__('600', 'plethora-framework'),
					'700'       => esc_html__('700', 'plethora-framework'),
					'800'       => esc_html__('800', 'plethora-framework'),
					'900'       => esc_html__('900', 'plethora-framework'),
				)
			);

			$options_index['nav-main-item-padding'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-menu-item-padding',
				'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
				'type'     => 'dimensions',
				'units'    => false,
				'title'    => esc_html__('Menu Item Padding (large devices)',  'plethora-framework'),
				'desc'     => esc_html__('Displays: >1200px / default: 24px / 12px', 'plethora-framework'),
			);
			$options_index['nav-main-item-padding-md'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-menu-item-padding-md',
				'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
				'type'     => 'dimensions',
				'units'    => false,
				'title'    => esc_html__('Menu Item Padding (medium devices)',  'plethora-framework'),
				'desc'     => esc_html__('Displays: 992px - 1199px / default: 10px / 10px', 'plethora-framework'),
			);
			$options_index['nav-main-item-padding-sm'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-menu-item-padding-sm',
				'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
				'type'     => 'dimensions',
				'units'    => false,
				'title'    => esc_html__('Menu Item Vertical Padding (small devices)',  'plethora-framework'),
				'desc'     => esc_html__('Displays: <992px / default: 15px / 10x', 'plethora-framework'),
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