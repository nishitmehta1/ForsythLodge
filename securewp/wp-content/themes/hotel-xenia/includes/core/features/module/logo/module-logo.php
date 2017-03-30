<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			       (c) 2017

Logo Module Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Logo') ) {

	class Plethora_Module_Logo {

		public static $feature_title        = "Logo Module";
		public static $feature_description  = "Manages logo instances sections";
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		public function __construct() {
			
			// Add theme options fields tab to Footer Section
			add_filter( 'plethora_themeoptions_header', array( $this, 'theme_options'), 10 );
			// Add metabox options to single post's Footer Section
			add_filter( 'plethora_metabox_header_fields_edit', array( $this, 'metabox_options'), 10 );
			// Prepare and add variables to the LESS variables index
			add_filter('plethora_module_wpless_variables', array( $this, 'less_variables' ) );
		}

		/**
		 * Returns widgetized areas template status for the footer area according to given row
		 */
		public function get_template_status() {
			
			$status = Plethora_Theme::option( METAOPTION_PREFIX .'logo', false );
			return $status;
		}

		/**
		 * Returns widgetized areas template options configuration according to given row
		 */
		public function get_template_config() {

			// Common options with sticky logo module ( WE NEED A BETTER WORKAROUND HERE )
			$options['sticky_status']        = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'stickyheader' ), METAOPTION_PREFIX .'header-sticky' ) );
			$options['sticky_custom_status'] = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'stickyheader' ), METAOPTION_PREFIX .'header-sticky-custom' ) );

			// Normal Logo options
			$options['logo_status']   = Plethora_Theme::option( METAOPTION_PREFIX .'logo', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'logo' ) );
			$options['logo_layout']   = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-layout', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'logo-layout' ) );        
			$options['logo_img_src']  = Plethora_WP::get_reduxoption_image_src( Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-img', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'logo-img' ) ) );
			$options['logo_title']    = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-title', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'logo-title' ) );
			$options['logo_subtitle'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-subtitle', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'logo-subtitle' ) );

			// Some additional options for LOGO template part routing
			$options['logo_class']        = $options['sticky_status'] && $options['sticky_custom_status'] ? 'logo hidden_on_header_stuck' : 'logo';
			$options['logo_url']          = home_url();
			$options['logo_url_class']    = 'brand';
			$options['logo_output_title'] = '';
			
			if ( ( $options['logo_layout'] == '1' || $options['logo_layout'] == '2') ) { 
				
				$options['logo_output_title'] = '<img src="'. esc_url( $options['logo_img_src'] ) .'" alt="'. esc_attr( $options['logo_title'] ) .'">';
			
			} elseif (( $options['logo_layout'] == '3' || $options['logo_layout'] == '4') ) {
				
				$options['logo_output_title'] = '<span class="site_title">'. esc_html( $options['logo_title'] ) .'</span>';
			}

			$options['logo_output_subtitle'] = ( $options['logo_layout'] == '2' || $options['logo_layout'] == '3' ) && ( !empty( $options['logo_subtitle'] ) ) ? '<p>'. esc_html( $options['logo_subtitle'] ) .'</p>' : '';
			
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
					'title'      => esc_html__('Logo', 'plethora-framework'),
					'heading'	 => esc_html__('HEADER SECTION // LOGO OPTIONS', 'plethora-framework'),
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

			// Logo ( ok )
			$logo_vert_margin       = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-vertical-margin', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-vertical-margin' ), 0, false);
			$logo_vert_margin_sm    = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-vertical-margin-sm', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-vertical-margin-sm' ), 0, false);
			$logo_vert_margin_xs    = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-vertical-margin-xs', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-vertical-margin-xs' ), 0, false);
			$logo_img_max_height    = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-img-max-height', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-img-max-height' ), 0, false);
			$logo_img_max_height_sm = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-img-max-height-sm', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-img-max-height-sm' ), 0, false);
			$logo_img_max_height_xs = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-img-max-height-xs', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-img-max-height-xs' ), 0, false);
			$logo_font_size         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-font-size', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-font-size' ), 0, false);
			$logo_font_size_sm      = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-font-size-sm', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-font-size-sm' ), 0, false);
			$logo_font_size_xs      = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-logo-font-size-xs', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-logo-font-size-xs' ), 0, false);
			$less_vars['wp-logo-vertical-margin']    = intval( $logo_vert_margin['height'] ) . 'px';
			$less_vars['wp-logo-vertical-margin-sm'] = intval( $logo_vert_margin_sm['height'] ) . 'px';
			$less_vars['wp-logo-vertical-margin-xs'] = intval( $logo_vert_margin_xs['height'] ) . 'px';
			$less_vars['wp-logo-img-max-height']     = intval( $logo_img_max_height['height'] ) . 'px';
			$less_vars['wp-logo-img-max-height-sm']  = intval( $logo_img_max_height_sm['height'] ) . 'px';
			$less_vars['wp-logo-img-max-height-xs']  = intval( $logo_img_max_height_xs['height'] ) . 'px';
			$less_vars['wp-logo-font-size']          = intval( $logo_font_size['font-size'] );
			$less_vars['wp-logo-font-size-sm']       = intval( $logo_font_size_sm['font-size'] );
			$less_vars['wp-logo-font-size-xs']       = intval( $logo_font_size_xs['font-size'] );
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

			$options_index['header-logo'] = array(
				'id'     => 'header-logo',
				'type'   => 'section',
				'title'  => esc_html__('Logo Options', 'plethora-framework'),
				'indent' => true,
			);
			$options_index['logo'] = array(
				'id'      => METAOPTION_PREFIX .'logo',
				'type'    => 'switch', 
				'title'   => esc_html__('Display Logo', 'plethora-framework'),
				'on'      => esc_html__('Display', 'plethora-framework') ,
				'off'     => esc_html__('Hide', 'plethora-framework'),
			);
			$options_index['layout'] = array(
				'id'       => THEMEOPTION_PREFIX .'logo-layout',
				'type'     => 'button_set',
				'title'    => esc_html__('Logo layout', 'plethora-framework'), 
				'options'  => array(
					'1' => esc_html__('Image only', 'plethora-framework'), 
					'2' => esc_html__('Image + Subtitle', 'plethora-framework'), 
					'3' => esc_html__('Title + Subtitle', 'plethora-framework'), 
					'4' => esc_html__('Title only', 'plethora-framework')), 
				'required' => array( METAOPTION_PREFIX .'logo','=', 1),						
			);
			$options_index['img'] = array(
				'id'       => THEMEOPTION_PREFIX .'logo-img',
				'type'     => 'media', 
				'url'      => true,			
				'title'    => esc_html__('Image', 'plethora-framework'),
				'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
			);
			$options_index['title'] = array(
				'id'        => THEMEOPTION_PREFIX .'logo-title',
				'type'      => 'text',
				'title'     => esc_html__('Title', 'plethora-framework'),
				'translate' => true,
				'required'  => array( THEMEOPTION_PREFIX .'logo-layout','=', array('3', '4')),						
			);
			$options_index['subtitle'] = array(
				'id'        =>THEMEOPTION_PREFIX .'logo-subtitle',
				'type'      => 'text',
				'title'     => esc_html__('Subtitle', 'plethora-framework'),
				'translate' => true,
				'required'  => array( THEMEOPTION_PREFIX .'logo-layout','=', array('2', '3')),						
			);
			$options_index['heading-heights'] = array(
				'id'       => 'logo-heights-start',
				'type'     => 'section',
				'title'    => esc_html__('Logo Dimensions', 'plethora-framework'),
				'subtitle' => esc_html__('The dimensions of the logo are set proportionally according to the logo\'s max height', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['img-max-height'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-logo-img-max-height',
				'type'     => 'dimensions',
				'units'    => false,
				'title'    => esc_html__('Image Max Height (large/medium devices)',  'plethora-framework'),
				'desc'     => esc_html__('Displays: >991px / default: 50px', 'plethora-framework'),
				'width'    => false,
			);

			$options_index['img-max-height-sm'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-logo-img-max-height-sm',
				'type'     => 'dimensions',
				'units'    => false,
				'title'    => esc_html__('Image Max Height (small devices)',  'plethora-framework'),
				'desc'     => esc_html__('Displays: 768px - 991px / default: 44px', 'plethora-framework'),
				'width'    => false,
			);
			$options_index['img-max-height-xs'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-logo-img-max-height-xs',
				'type'     => 'dimensions',
				'units'    => false,
				'title'    => esc_html__('Image Max Height (x-small devices)',  'plethora-framework'),
				'desc'     => esc_html__('Displays: <768px / default: 38px', 'plethora-framework'),
				'width'    => false,
			);
			$options_index['heading-spacing'] = array(
				'id'       => 'logo-spacing-start',
				'type'     => 'section',
				'title'    => esc_html__('Logo Spacing', 'plethora-framework'),
				'subtitle' => esc_html__('You can set an equal space above and below the logo, by setting its vertical margin in all responsive states', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['vertical-margin'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-logo-vertical-margin',
				'type'     => 'dimensions',
				'title'    => esc_html__('Vertical Spacing ( large/medium devices )', 'plethora-framework'),
				'subtitle' => esc_html__('Displays: >991px / default: 24px', 'plethora-framework'),
				'units'    => false,
				'width'    => false,
			);
			$options_index['vertical-margin-sm'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-logo-vertical-margin-sm',
				'type'     => 'dimensions',
				'title'    => esc_html__('Vertical Spacing ( small devices )', 'plethora-framework'),
				'subtitle' => esc_html__('Displays: 768px - 991px / default: 20px', 'plethora-framework'),
				'units'    => false,
				'width'    => false,
			);
			$options_index['vertical-margin-xs'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-logo-vertical-margin-xs',
				'type'     => 'dimensions',
				'title'    => esc_html__('Vertical Spacing ( x-small devices )', 'plethora-framework'),
				'subtitle' => esc_html__('Displays: <768px / default: 16px', 'plethora-framework'),
				'width'    => false,
				'units'    => false,
			);
			$options_index['heading-font'] = array(
				'id'       => 'logo-spacing-start',
				'type'     => 'section',
				'title'    => esc_html__('Logo Title Font Options', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['font-size'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-logo-font-size',
				'type'           => 'typography', 
				'title'          => esc_html__('Title Font Size ( large/medium devices )', 'plethora-framework'),
				'subtitle'       => esc_html__('Displays: >991px / default: 26px', 'plethora-framework'),
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
			$options_index['font-size-sm'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-logo-font-size-sm',
				'type'           => 'typography', 
				'title'          => esc_html__('Title Font Size ( small devices )', 'plethora-framework'),
				'subtitle'       => esc_html__('Displays: 768px - 991px / default: 24px', 'plethora-framework'),
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
			$options_index['font-size-xs'] = array(
				'id'             => THEMEOPTION_PREFIX .'less-logo-font-size-xs',
				'type'           => 'typography', 
				'title'          => esc_html__('Title Font Size ( x-small devices )', 'plethora-framework'),
				'subtitle'       => esc_html__('Displays: <768px / default: 22px', 'plethora-framework'),
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