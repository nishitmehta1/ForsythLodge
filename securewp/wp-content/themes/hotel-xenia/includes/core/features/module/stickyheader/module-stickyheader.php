<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			       (c) 2017

Sticky Header Module Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Stickyheader') ) {

	class Plethora_Module_Stickyheader {

		public static $feature_title         = "Sticky Header Module";
		public static $feature_description   = "Manages sticky header section";
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		public function __construct() {
			
			// Add theme options fields tab to Footer Section
			add_filter( 'plethora_themeoptions_header', array( $this, 'theme_options'), 12 );
			// Add metabox options to single post's Footer Section
			add_filter( 'plethora_metabox_header_fields_edit', array( $this, 'metabox_options'), 12 );
			// Prepare and add variables to the LESS index
			add_filter('plethora_module_wpless_variables', array( $this, 'less_variables' ) );
			// Body tag class filtering
			add_filter( 'body_class', array( $this, 'filter_body_class') );
			// Settings for the frontend core layout containers
			add_action( 'get_header', array( $this, 'set_containers'), 999 );
			// Theme.js variables setup
			add_action( 'get_header', array( $this, 'set_js'), 999 );
		}

		/**
		 * Returns widgetized areas template status for the footer area according to given row
		 */
		public function get_template_status( $args = array( 'return' => 'logo' ) ) {

			$status = false; 
			if ( !empty( $args['return'] ) && $args['return'] === 'logo' ) {

				$status = ( Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky' ) ) && 
							Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky-custom' ) ) && 
							Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky-custom-logo' ) ) ) ? true : false;
			}
			return $status;
		}

		/**
		 * Returns widgetized areas template options configuration according to given row
		 */
		public function get_template_config( $args = array( 'return' => 'logo' ) ) {

			$options = array();
			if ( !empty( $args['return'] ) && $args['return'] === 'logo' ) {
				// Common options with sticky logo module ( WE NEED A BETTER WORKAROUND HERE )
				$options['sticky_status']        = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky' ) );
				$options['sticky_custom_status'] = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky-custom' ) );

				// Set options affected by custom logo setting
				$options['sticky_custom_logo']          = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'header-sticky-custom-logo' ) );
				$options['sticky_custom_logo_layout']   = ( $options['sticky_custom_logo'] === 'custom' ) ? Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout' ) ) : Plethora_Theme::option( METAOPTION_PREFIX .'logo-layout', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'logo' ), METAOPTION_PREFIX .'logo-layout' ) );
				$options['sticky_custom_logo_img_src']  = ( $options['sticky_custom_logo'] === 'custom' ) ? Plethora_WP::get_reduxoption_image_src( Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo-img', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'header-sticky-custom-logo-img' ) ) ) : Plethora_WP::get_reduxoption_image_src( Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-img', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'logo' ), THEMEOPTION_PREFIX .'logo-img' ) ) );
				$options['sticky_custom_logo_title']    = ( $options['sticky_custom_logo'] === 'custom' ) ? Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo-title', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'header-sticky-custom-logo-title' ) ) : Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-title', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'logo' ), THEMEOPTION_PREFIX .'logo-title' ) );
				$options['sticky_custom_logo_subtitle'] = ( $options['sticky_custom_logo'] === 'custom' ) ? Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo-subtitle', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'header-sticky-custom-logo-subtitle' ) ) : Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-subtitle', Plethora_Module_Themeoptions::get_option_default_value( Plethora_Theme::get_feature_instance( 'module', 'logo' ), THEMEOPTION_PREFIX .'logo-subtitle' ) );

				// Some additional options for STICKY CUSTOM LOGO template part routing
				$options['sticky_custom_logo_class']        = $options['sticky_status'] && $options['sticky_custom_status']  ? 'logo shown_on_header_stuck' : 'logo';
				$options['sticky_custom_logo_url']          = home_url();
				$options['sticky_custom_logo_url_class']    = 'brand';
				$options['sticky_custom_logo_output_title'] = '';
				if ( ( $options['sticky_custom_logo_layout'] == '1' || $options['sticky_custom_logo_layout'] == '2') ) { 
					
					$options['sticky_custom_logo_output_title'] = '<img src="'. esc_url( $options['sticky_custom_logo_img_src'] ) .'" alt="'. esc_attr( $options['sticky_custom_logo_title'] ) .'">';
				
				} elseif (( $options['sticky_custom_logo_layout'] == '3' || $options['sticky_custom_logo_layout'] == '4') ) {
					
					$options['sticky_custom_logo_output_title'] = '<span class="site_title">'. esc_html( $options['sticky_custom_logo_title'] ) .'</span>';
				}

				$options['sticky_custom_logo_output_subtitle'] = ( $options['sticky_custom_logo_layout'] == '2' || $options['sticky_custom_logo_layout'] == '3' )  ? '<p>'. esc_html( $options['sticky_custom_logo_subtitle'] ) .'</p>' : '';
			}
			return $options;
		}

		/**
		* A filter for body_class, when header is sticky.
		* Hooked at 'body_class' filter, should always return the $classes argument
		*/
		public function filter_body_class( $classes ) { 

			$header_sticky          = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky' ) );
			$header_sticky_alt      = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky-custom' ) );
			$header_sticky_behavior = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-behavior', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky-behavior' ) );

			$classes[] = $header_sticky ? 'sticky_header' : '';
			$classes[] = $header_sticky_alt ? 'sticky_header_alt' : '';
			$classes[] = $header_sticky && $header_sticky_behavior === 'top_onscroll' ? 'header_will_appear' : '';
			$classes[] = $header_sticky && in_array( $header_sticky_behavior, array( 'bottom', 'bottom_onscroll' ) ) ? 'header_is_at_bottom' : '';
			return $classes;
		}

		/** 
		* Configure classes and other attributes for the core layout containers
		* Hooked at 'get_header' action
		*/
		public function set_containers() {

			// Sticky Header ( note: body tag sticky class was added already via 'body_class' filter )
			$sticky_status = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky' ) );
			if ( $sticky_status ) {
				$sticky_behavior = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-behavior', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky-behavior' ) );
				$sticky_css_classes = array( 
					'top'             => 'sticky_header',
					'top_onscroll'    => 'appearing_sticky_header',
					'bottom'          => 'bottom_sticky_header',
					'bottom_onscroll' => 'bottom_to_top_sticky_header',
				);
				if ( !empty( $sticky_css_classes[$sticky_behavior] ) ) {

					Plethora_Theme::add_container_attr( 'header', 'class', $sticky_css_classes[$sticky_behavior] );
				}

				$trans_class = 	Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky' ) ) == 1 && 
								Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'header-sticky-custom' ) ) == 1 && 
								Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-trans', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'header-sticky-custom-trans' ) ) == 1 ? 'alt_header_transparent' : '';
				Plethora_Theme::add_container_attr( 'header', 'class', $trans_class );

			}
		}

	 	/*
		* Script events, used mostly for declaring variables to theme.js 
		* using the Plethora_Theme::set_themeconfig() method
		* Hooked at 'get_header' action
		*/
		public function set_js() {

			// Set sticky header scroll offset trigger
			$scroll_offset_trigger  = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-behavior-scrolloffset', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'header-sticky-behavior-scrolloffset' ) );
			Plethora_Theme::set_themeconfig( 'GENERAL', array( 'scroll_offset_trigger' => $scroll_offset_trigger ) );
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
					'title'      => esc_html__('Sticky Header', 'plethora-framework'),
					'heading'    => esc_html__('HEADER SECTION // STICKY HEADER OPTIONS', 'plethora-framework'),
					'desc'       => esc_html__('Set it to ON if you want your header to remain visible on top when you scroll down a page. All options here are applied exclusively on sticky header section', 'plethora-framework'),
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

			$link_color                                   = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-sticky-custom-linkcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-header-sticky-custom-linkcolor' ), 0, false );
			$less_vars['wp-stickyheader-linkcolor']       = $link_color['regular'];
			$less_vars['wp-stickyheader-linkcolor-hover'] = $link_color['hover'];
			$less_vars['wp-stickyheader-opacity']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-sticky-custom-trans-opacity', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-header-sticky-custom-trans-opacity' ), 0, false );
			$less_vars['wp-stickyheader-bgcolor']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-sticky-custom-bgcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-header-sticky-custom-bgcolor' ), 0, false );
			$less_vars['wp-stickyheader-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-sticky-custom-txtcolor', Plethora_Module_Themeoptions::get_option_default_value( $this, THEMEOPTION_PREFIX .'less-header-sticky-custom-txtcolor' ), 0, false );
			
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

			$options_index['heading-sticky'] = array(
				'id'     => 'sticky-header-section-start',
				'type'   => 'section',
				'title'  => esc_html__('Sticky Header Options )', 'plethora-framework'),
				'indent' => true,
			);

			$options_index['sticky'] = array(
				'id'      => METAOPTION_PREFIX .'header-sticky',
				'type'    => 'switch', 
				'title'   => esc_html__('Sticky Header On Scroll', 'plethora-framework'),
				'desc'    => esc_html__('Set the default header on scroll behavior for all page. You can override this on each page in separate.', 'plethora-framework'),
			);
			$options_index['sticky-behavior'] = array(
				'id'          => THEMEOPTION_PREFIX .'header-sticky-behavior',
				'type'        => 'select', 
				'title'       => esc_html__('Sticky Header Behavior', 'plethora-framework'),
				'description' => esc_html__('Choose the behavior of sticky header. You can override this on each page in separate.', 'plethora-framework') ,
				'options'     => array(
					'top'             => esc_html__('On top, always visible', 'plethora-framework'),
					'top_onscroll'    => esc_html__('On top, visible only after scroll starts', 'plethora-framework'),
					'bottom'          => esc_html__('On bottom, always visible', 'plethora-framework'),
					'bottom_onscroll' => esc_html__('Starts on bottom and sticks on top after scrolling', 'plethora-framework'),
				),
			);
			$options_index['sticky-behavior-scrolloffset'] = array(
				'id'       => THEMEOPTION_PREFIX .'header-sticky-behavior-scrolloffset',
				'type'     => 'spinner', 
				'title'    => esc_html__('Scroll Offset Trigger', 'plethora-framework'),
				'desc'     => esc_html__('Set a scrolling point in pixels, beyond which the appearance of "Alternative Sticky Header" will be triggered. This point also applies for the appearance of the Default Header if you choose the "On top, visible only after scroll starts" setting from above.', 'plethora-framework'),
				"min"      => 0,
				"step"     => 1,
				"max"      => 1200,
			);

			// Sticky header color set
			$options_index['heading-sticky-custom'] = array(
				'id'       => 'sticky-header-section-start',
				'type'     => 'section',
				'title'    => esc_html__('Alternative Sticky Header Options ( after scroll )', 'plethora-framework'),
				'subtitle' => esc_html__('Enable and setup options for an alternative Sticky Header section to be displayed when the user scrolls down the page.', 'plethora-framework'),
				'indent'   => true,
			);

			$options_index['sticky-custom'] = array(
				'id'      => METAOPTION_PREFIX .'header-sticky-custom',
				'type'    => 'switch', 
				'title'   => esc_html__('Alternative Sticky Header After Scroll', 'plethora-framework'),
				'desc'    => esc_html__('Set the default behavior. You can enable/disble them per page.', 'plethora-framework'),
			);

			$options_index['sticky-custom-logo'] = array(
				'id'      => THEMEOPTION_PREFIX .'header-sticky-custom-logo',
				'type'    => 'button_set', 
				'title'   => esc_html__('Sticky Header Logo', 'plethora-framework'),
				'desc'    => esc_html__('Set to on, if you want your logo to be visible on the Alternative sticky header. Set it to custom, if you need a different logo version on scroll.', 'plethora-framework'),
				'options' => array(
					0        => esc_html__('Off', 'plethora-framework'),
					1        => esc_html__('On', 'plethora-framework'),
					'custom' => esc_html__('Custom', 'plethora-framework'),
				),
			);
			$options_index['sticky-custom-logo-layout'] = array(
				'id'       	=> THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout',
				'type'    	=> 'button_set',
				'title'   	=> esc_html__('Sticky Header Logo layout', 'plethora-framework'), 
				'options' 	=> array(
					'1' => esc_html__('Image only', 'plethora-framework'), 
					'2' => esc_html__('Image + Subtitle', 'plethora-framework'), 
					'3' => esc_html__('Title + Subtitle', 'plethora-framework'), 
					'4' => esc_html__('Title only', 'plethora-framework')
				), 
				'required' => array( 
					array( THEMEOPTION_PREFIX .'header-sticky-custom-logo','equals', 'custom' ),
				),						
			);
			$options_index['sticky-custom-logo-img'] = array(
				'id'       => THEMEOPTION_PREFIX .'header-sticky-custom-logo-img',
				'type'     => 'media', 
				'url'      => true,			
				'title'    => esc_html__('Sticky Header Logo Image', 'plethora-framework'),
				'required' => array( 
					array( THEMEOPTION_PREFIX .'header-sticky-custom-logo','equals', 'custom' ),
					array( THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout','=',array('1', '2') ),
				),						
			);
			$options_index['sticky-custom-logo-title'] = array(
				'id'        => THEMEOPTION_PREFIX .'header-sticky-custom-logo-title',
				'type'      => 'text',
				'title'     => esc_html__('Sticky Header Logo Title', 'plethora-framework'),
				'translate' => true,
				'required'  => array( 
					array( THEMEOPTION_PREFIX .'header-sticky-custom-logo','equals', 'custom' ),
					array( THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout', '=', array('3', '4') ),
				),						
			);
			$options_index['sticky-custom-logo-subtitle'] = array(
				'id'        => THEMEOPTION_PREFIX .'header-sticky-custom-logo-subtitle',
				'type'      => 'text',
				'title'     => esc_html__('Sticky Header Logo Subtitle', 'plethora-framework'),
				'translate' => true,
				'required'  => array( 
					array( THEMEOPTION_PREFIX .'header-sticky-custom-logo','equals', 'custom' ),
					array( THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout', '=', array('2', '3') ),
				),						
			);
			$options_index['sticky-custom-menu'] = array(
				'id'       => THEMEOPTION_PREFIX .'header-sticky-custom-menu',
				'type'     => 'switch', 
				'title'    => esc_html__('Sticky Header Menu', 'plethora-framework'),
				'desc'     => esc_html__('Set to on, if you want your main navigation to be visible on sticky header', 'plethora-framework'),
			);
			$options_index['sticky-custom-trans'] = array(
				'id'       => THEMEOPTION_PREFIX .'header-sticky-custom-trans',
				'type'     => 'switch', 
				'title'    => esc_html__('Sticky Header Transparency', 'plethora-framework'),
				'desc'     => esc_html__('Set to on, if you want a transparent sticky header', 'plethora-framework'),
			);
			$options_index['sticky-custom-trans-opacity'] = array(
				'id'            => THEMEOPTION_PREFIX .'less-header-sticky-custom-trans-opacity',
				'type'          => 'slider',
				'title'         => esc_html__('Sticky Header Opacity Level', 'plethora-framework'), 
				'desc'          => esc_html__('Set the opacity level for the sticky header transparency.', 'plethora-framework'), 
				"min"           => 0,
				"step"          => 1,
				"max"           => 100,
				'display_value' => 'text'
			);
			$options_index['sticky-custom-bgcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-header-sticky-custom-bgcolor',
				'type'        => 'color',
				'title'       => esc_html__('Sticky Header Background Color', 'plethora-framework'), 
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['sticky-custom-txtcolor'] = array(
				'id'          => THEMEOPTION_PREFIX .'less-header-sticky-custom-txtcolor',
				'type'        => 'color',
				'title'       => esc_html__('Sticky Header Text Color', 'plethora-framework'), 
				'desc'        => esc_html__('Text color for non linked texts ( i.e. logo title/subtitle )', 'plethora-framework'),
				'transparent' => false,
				'validate'    => 'color',
			);
			$options_index['sticky-custom-linkcolor'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-header-sticky-custom-linkcolor',
				'type'     => 'link_color',
				'title'    => esc_html__('Sticky Header Link Color', 'plethora-framework'), 
				'desc'     => esc_html__('Color for navigation items and other link anchor texts', 'plethora-framework'),
				'visited'  => false,
				'active'   => false,
				'validate' => 'color',
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