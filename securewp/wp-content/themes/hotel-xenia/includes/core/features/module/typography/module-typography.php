<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			       (c) 2017

Typography Module Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Typography') ) {

	class Plethora_Module_Typography {

		public static $feature_title        = "Typography Header Module";
		public static $feature_description  = "Manages typography configuration";
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
					'title'      => esc_html__('Typography', 'plethora-framework'),
					'heading'    => esc_html__('TYPOGRAPHY OPTIONS', 'plethora-framework'),
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

			// Get saved values ( or defaults if not saved yet )
			$font_serif         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-font-family-sans-serif', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-font-family-sans-serif' ), 0, false);
			$font_alt           = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-font-family-alternative', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-font-family-alternative' ), 0, false);
			$font_size_base     = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-font-size-base', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-font-size-base' ), 0, false);
			$font_size_base_alt = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-font-size-alternative-base', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-font-size-alternative-base' ), 0, false);
			$body_font_weight   = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-body-font-weight', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-body-font-weight' ), 0, false);
			$heading_trans      = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-headings-text-transform', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-headings-text-transform' ), 0, false);
			$heading_weight     = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-headings-font-weight', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-headings-font-weight' ), 0, false);
			$button_text_trans  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-btn-text-transform', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-btn-text-transform' ), 0, false);
			// Add 'em to LESS variables and return
			$less_vars['wp-font-family-sans-serif']     = $font_serif['font-family'];
			$less_vars['wp-font-family-alternative']    = $font_alt['font-family'];
			$less_vars['wp-font-size-base']             = $font_size_base['font-size'];
			$less_vars['wp-font-size-alternative-base'] = $font_size_base_alt['font-size'];
			$less_vars['wp-font-weight']                = $body_font_weight;
			$less_vars['wp-headings-text-transform']    = $heading_trans['text-transform'];
			$less_vars['wp-headings-font-weight']       = $heading_weight;
			$less_vars['wp-btn-text-transform']         = $button_text_trans['text-transform'];
			return $less_vars;
		}

		/** 
		* MUST HAVE METHOD FOR ALL MODULES USING OPTIONS
		* Returns theme options / metabox fields index
		* Options configuration should not contain 'default' value ( anyway, it will be ignored on the late configuration)
		* @return array()
		*/
		public function options_index() { 

			$options_index  = array();
			$options_index['less-font-family-sans-serif'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-font-family-sans-serif',
				'type'           => 'typography', 
				'title'          => esc_html__('Primary Font', 'plethora-framework'),
				'desc'           => esc_html__('Primary font is used in content texts', 'plethora-framework'),
				'google'         => true, 
				'font-style'     => false,
				'font-weight'    => false,
				'font-size'      => false,
				'line-height'    => false,
				'word-spacing'   => false,
				'letter-spacing' => false,
				'text-align'     => false,
				'text-transform' => false,
				'color'          => false,
				'subsets'        => true,
				'preview'        => true, 
				'all_styles'     => true, // import all google font weights
			);	
			$options_index['less-font-family-alternative'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-font-family-alternative',
				'type'           => 'typography', 
				'title'          => esc_html__('Secondary Font', 'plethora-framework'),
				'desc'           => esc_html__('Secondary font is used in headings and buttons', 'plethora-framework'),
				'google'         => true, 
				'font-style'     => false,
				'font-weight'    => false,
				'font-size'      => false,
				'line-height'    => false,
				'word-spacing'   => false,
				'letter-spacing' => false,
				'text-align'     => false,
				'text-transform' => false,
				'color'          => false,
				'subsets'        => true,
				'preview'        => true, 
				'all_styles'     => true, // import all google font weights
			);	

			$options_index['less-font-size-base'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-font-size-base',
				'type'           => 'typography', 
				'title'          => esc_html__('Primary Font Size Base', 'plethora-framework'),
				'desc'           => esc_html__('All text sizes for body & paragraph elements will be adjusted according to this base.', 'plethora-framework'),
				'google'         => false, 
				'font-family'    => false,
				'font-style'     => false,
				'font-weight'    => false,
				'font-size'      => true,
				'line-height'    => false,
				'word-spacing'   => false,
				'letter-spacing' => false,
				'text-align'     => false,
				'text-transform' => false,
				'color'          => false,
				'subsets'        => false,
				'preview'        => false, 
				'all_styles'     => false, // import all google font weights
			);	
			$options_index['less-body-font-weight'] = array(
				'id'      => THEMEOPTION_PREFIX .'less-body-font-weight',
				'type'    => 'select', 
				'title'   => esc_html__('Primary Font Weight', 'plethora-framework'),
				'desc'    => esc_html__('Font weight for body & paragraph elements.', 'plethora-framework'),
				'options' => array(
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
			$options_index['less-font-size-alternative-base'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-font-size-alternative-base',
				'type'           => 'typography', 
				'title'          => esc_html__('Secondary Font Size Base', 'plethora-framework'),
				'desc'           => esc_html__('All text sizes for heading elements will be adjusted according to this base.', 'plethora-framework'),
				'google'         => false, 
				'font-family'    => false,
				'font-style'     => false,
				'font-weight'    => false,
				'font-size'      => true,
				'line-height'    => false,
				'word-spacing'   => false,
				'letter-spacing' => false,
				'text-align'     => false,
				'text-transform' => false,
				'color'          => false,
				'subsets'        => false,
				'preview'        => false, 
				'all_styles'     => false, // import all google font weights
			);	
			$options_index['less-headings-text-transform'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-headings-text-transform',
				'type'           => 'typography', 
				'title'          => esc_html__('Heading Text Transform', 'plethora-framework'),
				'google'         => false, 
				'font-family'    => false,
				'font-style'     => false,
				'font-weight'    => false,
				'font-size'      => false,
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

			$options_index['less-headings-font-weight'] = array(
				'id'      => THEMEOPTION_PREFIX .'less-headings-font-weight',
				'type'    => 'select', 
				'title'   => esc_html__('Headings Font Weight', 'plethora-framework'),
				'options' => array(
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

			$options_index['less-btn-text-transform'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-btn-text-transform',
				'type'           => 'typography', 
				'title'          => esc_html__('Buttons Text Transform', 'plethora-framework'),
				'google'         => false, 
				'font-family'    => false,
				'font-style'     => false,
				'font-weight'    => false,
				'font-size'      => false,
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