<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			       (c) 2017

Color Sets Module Base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Colorsets') ) {

	class Plethora_Module_Colorsets {

		public static $feature_title        = "Color Sets Module";
		public static $feature_description  = "Manages color sets";
		public static $theme_option_control  = false;	// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;	// Default activation option status ( boolean )
		public static $theme_option_requires = array();	// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;	// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;	// Additional method invocation ( string/boolean | method name or false )

		public function __construct() {
			
			// Add theme options tab to General Section
			add_filter( 'plethora_themeoptions_general', array( $this, 'theme_options'), 10 );
			// Prepare and add variables to the LESS variables index
			add_filter('plethora_module_wpless_variables', array( $this, 'less_variables' ) );
		}

		/** 
		* Returns theme options tab configuration
		* Hooked @ 'plethora_themeoptions_general'
		* @return array()
		*/
		public function theme_options( $sections ) {

			$theme_options = Plethora_Module_Themeoptions::get_themeoptions_fields( $this );
			if ( is_array( $theme_options ) && !empty( $theme_options ) ) {

				$sections[] = array(
					'title'      => esc_html__('Basic Colors & Sets', 'plethora-framework'),
					'heading'    => esc_html__('BASIC COLORS & COLOR SET OPTIONS', 'plethora-framework'),
					'subsection' => true,
					'fields'     => $theme_options
				);
			}
			return $sections;
		}

		/** 
		* Prepares and returns all LESS variable options
		* Hooked @ 'plethora_module_wpless_variables'
		* @return array
		*/
		public function less_variables( $less_vars ) { 

			// Basic Colors ( ok )
			$less_vars['wp-brand-primary']    = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-brand-primary', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-brand-primary' ), 0, false);
			$less_vars['wp-brand-secondary']  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-brand-secondary', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-brand-secondary' ), 0, false);
			$less_vars['wp-body-bg']          = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-body-bg', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-body-bg' ), 0, false);
			$less_vars['wp-text-color']       = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-text-color', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-text-color' ), 0, false);
			$link                             = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-link-color', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-link-color' ), 0, false);
			$less_vars['wp-link-color']       = $link['regular'];
			$less_vars['wp-link-hover-color'] = $link['hover'];
			
			// Color Sets > Primary ( ok )
			$less_vars['wp-primary-section-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-primary-section-txtcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-primary-section-txtcolor' ), 0, false);
			$link                                            = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-primary-section-linkcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-primary-section-linkcolor' ), 0, false);
			$less_vars['wp-primary-section-linkcolor']       = $link['regular'];
			$less_vars['wp-primary-section-linkcolor-hover'] = $link['hover'];
			
			// Color Sets > Secondary ( ok )
			$less_vars['wp-secondary-section-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-secondary-section-txtcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-secondary-section-txtcolor' ), 0, false);
			$link                                              = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-secondary-section-linkcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-secondary-section-linkcolor' ), 0, false);
			$less_vars['wp-secondary-section-linkcolor']       = $link['regular'];
			$less_vars['wp-secondary-section-linkcolor-hover'] = $link['hover'];
			
			// Rest Color Sets ( light, dark, white, black )
			$colorsets = array( 'light', 'dark', 'white', 'black' );
			foreach ( $colorsets as $colorset ) {

				$less_vars['wp-'.$colorset.'-section-bgcolor']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-'.$colorset.'-section-bgcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-'.$colorset.'-section-bgcolor' ), 0, false);
				$less_vars['wp-'.$colorset.'-section-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-'.$colorset.'-section-txtcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-'.$colorset.'-section-txtcolor' ), 0, false);
				$link                                                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-light-'.$colorset.'-linkcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-'.$colorset.'-section-linkcolor' ), 0, false);
				$less_vars['wp-'.$colorset.'-section-linkcolor']       = $link['regular'];
				$less_vars['wp-'.$colorset.'-section-linkcolor-hover'] = $link['hover'];
			}
			
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
			// Background & Body styling
			$options_index['heading-basic-colors'] = array(
				'id'       => 'heading-basic-colors',
				'type'     => 'section',
				'title'    => esc_html__('Basic Colors', 'plethora-framework'),
				'subtitle' => esc_html__('Basic color choices that affect several elements within the theme.', 'plethora-framework'),
				'indent'   => true,
			);

			$options_index['less-body-bg'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-body-bg',
				'type'        => 'color',
				'title'       => esc_html__('Body Background Color', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-text-color'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-text-color',
				'type'        => 'color',
				'title'       => esc_html__('Text Color', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-link-color'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-link-color',
				'type'     => 'link_color',
				'title'    => esc_html__('Link Text Color', 'plethora-framework'), 
				'visited'  => false,
				'active'   => false,
				'validate' => 'color',
			);
			// Primary Color Set
			$options_index['heading-primary'] = array(
				'id'       => 'heading-primary',
				'type'     => 'section',
				'title'    => esc_html__('Primary Color Set', 'plethora-framework'),
				'subtitle' => esc_html__('Options for primary colored elements. Background & other design elements are colored according to chosen primary color.', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['less-brand-primary'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-brand-primary',
				'type'        => 'color',
				'title'       => esc_html__('Primary Brand Color', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-primary-section-txtcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-primary-section-txtcolor',
				'type'        => 'color',
				'title'       => esc_html__('Text', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-primary-section-linkcolor'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-primary-section-linkcolor',
				'type'     => 'link_color',
				'title'    => esc_html__('Link Text', 'plethora-framework'), 
				'visited'  => false,
				'active'   => false,
				'validate' => 'color',
			);

			// Secondary Color Set
			$options_index['heading-secondary'] = array(
				'id'       => 'heading-secondary',
				'type'     => 'section',
				'title'    => esc_html__('Secondary Color Set', 'plethora-framework'),
				'subtitle' => esc_html__('Color options for secondary colored elements. Background & other design elements are colored according to chosen secondary color ( check above ).', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['less-brand-secondary'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-brand-secondary',
				'type'        => 'color',
				'title'       => esc_html__('Secondary Brand Color', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-secondary-section-txtcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-secondary-section-txtcolor',
				'type'        => 'color',
				'title'       => esc_html__('Text', 'plethora-framework'), 
				'default'     => '#ffffff',
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-secondary-section-linkcolor'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-secondary-section-linkcolor',
				'type'     => 'link_color',
				'title'    => esc_html__('Link Text', 'plethora-framework'), 
				'visited'  => false,
				'active'   => false,
				'validate' => 'color',
			);

			// Light Color Set
			$options_index['heading-light'] = array(
				'id'       => 'heading-light',
				'type'     => 'section',
				'title'    => esc_html__('Light Color Set', 'plethora-framework'),
				'subtitle' => esc_html__('Color options for light colored elements.', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['less-light-section-bgcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-light-section-bgcolor',
				'type'        => 'color',
				'title'       => esc_html__('Background', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-light-section-txtcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-light-section-txtcolor',
				'type'        => 'color',
				'title'       => esc_html__('Text', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-light-section-linkcolor'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-light-section-linkcolor',
				'type'     => 'link_color',
				'title'    => esc_html__('Link Text', 'plethora-framework'), 
				'visited'  => false,
				'active'   => false,
				'validate' => 'color',
			);

			// Dark Color Set
			$options_index['heading-dark'] = array(
				'id'       => 'heading-dark',
				'type'     => 'section',
				'title'    => esc_html__('Dark Color Set', 'plethora-framework'),
				'subtitle' => esc_html__('Color options for dark colored elements.', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['less-dark-section-bgcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-dark-section-bgcolor',
				'type'        => 'color',
				'title'       => esc_html__('Background', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-dark-section-txtcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-dark-section-txtcolor',
				'type'        => 'color',
				'title'       => esc_html__('Text', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-dark-section-linkcolor'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-dark-section-linkcolor',
				'type'     => 'link_color',
				'title'    => esc_html__('Link Text', 'plethora-framework'), 
				'visited'  => false,
				'active'   => false,
				'validate' => 'color',
			);

			// White Color Set
			$options_index['heading-white'] = array(
				'id'       => 'heading-white',
				'type'     => 'section',
				'title'    => esc_html__('White Color Set', 'plethora-framework'),
				'subtitle' => esc_html__('Color options for white colored elements.', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['less-white-section-bgcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-white-section-bgcolor',
				'type'        => 'color',
				'title'       => esc_html__('Background', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-white-section-txtcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-white-section-txtcolor',
				'type'        => 'color',
				'title'       => esc_html__('Text', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-white-section-linkcolor'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-white-section-linkcolor',
				'type'     => 'link_color',
				'title'    => esc_html__('Link', 'plethora-framework'), 
				'visited'  => false,
				'active'   => false,
				'validate' => 'color',
			);

			// Black colored sections styling
			$options_index['heading-black'] = array(
				'id'       => 'heading-black',
				'type'     => 'section',
				'title'    => esc_html__('Black Color Set', 'plethora-framework'),
				'subtitle' => esc_html__('Color options for black colored elements.', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['less-black-section-bgcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-black-section-bgcolor',
				'type'        => 'color',
				'title'       => esc_html__('Background', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-black-section-txtcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-black-section-txtcolor',
				'type'        => 'color',
				'title'       => esc_html__('Text', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['less-black-section-linkcolor'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-black-section-linkcolor',
				'type'     => 'link_color',
				'title'    => esc_html__('Link', 'plethora-framework'), 
				'visited'  => false,
				'active'   => false,
				'validate' => 'color',
			);
			return $options_index;
		}

		/** 
		* MUST HAVE METHOD FOR ALL MODULES USING OPTIONS
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