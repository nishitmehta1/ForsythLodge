<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               	   (c) 2017

Theme Options Module base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists( 'Plethora_Module_Themeoptions' ) ):

	class Plethora_Module_Themeoptions {

		public static $feature_title         = "Theme Options module";   // FEATURE DISPLAY TITLE
		public static $feature_description   = "";                    // FEATURE DISPLAY DESCRIPTION 
		public static $theme_option_control  = false;                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
		public static $theme_option_default  = true;                  // DEFAULT ACTIVATION OPTION STATUS 
		public static $theme_option_requires = array();               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = false;                  // DYNAMIC CLASS CONSTRUCTION? 
		public static $dynamic_method        = false;                 // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

		public $args        = array();
		public $sections    = array();
		public $theme;
		public $ReduxFramework;


		public function __construct() { 
			
			if ( !class_exists('ReduxFramework') && is_admin() ) { return; }

			add_action('init', array( $this, 'initSettings'), 20);
			add_action('init', array( $this, 'removeDemoModeLink' ) );
			add_filter('plethora_module_wpless_variables', array( $this, 'less_variables' ) ); // LESS VARIABLES
		}

		
		public function removeDemoModeLink() { 

			if ( class_exists('ReduxFrameworkPlugin') ) {
				remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2 );
			}
			if ( class_exists('ReduxFrameworkPlugin') ) {
				remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );    
			}
		
		}		

		public function setArguments() {

			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$args = array();
			$args['opt_name']			= THEME_OPTVAR;				// Theme options name & the global variable in which data options are retrieved via code
			$args['display_name']	    = THEME_DISPLAYNAME;		// Set the title appearing at the top of the options panel 
			$args['display_version']	= 'ver.'. THEME_VERSION ;	// Set the version number that appears after the title at the top of the options panel.
			$args['menu_type']			= 'menu';					// Set whether or not the admin menu is displayed.  Accepts either menu (default) or submenu.
			$args['allow_sub_menu']		= true;						// Enable/disable labels display below the admin menu
			$args['menu_title']			= THEME_OPTIONSPAGEMENU; // Set the WP admin menu title 
			$args['page_title']		    = THEME_OPTIONSPAGETITLE ; // Set the WP admin page title (appearing on browsers page title)
			// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth to generate a Google API key to use this feature.
			// $args['google_api_key']  	= 'AIzaSyAhCFO56k_xL212g8j2LK88wK0I_CRwzDE';	// Set an API key for Google Webfonts usage (more on: https://developers.google.com/fonts/docs/developer_api)
			$args['google_update_weekly'] = false;					// In case this is set to true, you HAVE to set your own private API key...I suppose that you don't want your website fail to display its fonts!  (more on: https://developers.google.com/fonts/docs/developer_api)
			$args['async_typography']  	= false;                    // Use a asynchronous font on the front end or font string
			$args['admin_bar']			= true;						// Enable/disable Plethora settings menu on admin bar
			$args['admin_bar_icon']		= 'dashicons-admin-generic';	// Set the icon appearing in the admin bar, next to the menu title
			$args['dev_mode']			= false;					// Enable/disable Dev Tab (view class settings / info in panel)
			$args['customizer']			= false;						// Enable/disable basic WordPress customizer support
			// $args['open_expanded']		= true;						// Allow you to start the panel in an expanded way initially.
			// $args['disable_save_warn']	= true;						// Disable the save warning when a user changes a field

		// ARGUMENTS --> EXTRA FEATURES
			$args['page_priority']		= '990'; 					// Set the order number specifying where the menu will appear in the admin area
			$args['page_parent']		= ''; 						// Set where the options menu will be placed on the WordPress admin sidebar. For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
			$args['page_permissions']	= 'manage_options';			// Set the permission level required to access the options panel.  For a complete list of roles and capabilities, please visit this page:  https://codex.wordpress.org/Roles_and_Capabilities
			$args['menu_icon']			= PLE_CORE_ASSETS_URI .'/images/plethora/plethora20x20.png'; 						// Set the WP admin menu icon 
			// $args['last_tab']			= '0';						// Set the default tab to open when the page is loaded
			$args['page_icon']			= 'icon-themes';			// Set the icon appearing in the admin panel, next to the menu title
			$args['page_slug']			= THEME_OPTIONSPAGE;		// Set the page slug (i.e. wp-admin/themes.php?page=plethora_settings)
			$args['save_defaults']		= true;						// Set whether or not the default values are saved to the database on load, before Save Changes is clicked
			$args['default_show']		= false;					// Enable/disable default value display by the field title.
			$args['default_mark']		= '*';						// Setup symbol to be displayed on default valued fields (e.g an asterisk *)
			$args['show_import_export']	= true;						// Enable/disable Import/Export Tab

		// ARGUMENTS --> ADMIN BAR LINKS
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-demo', 'href' => 'http://plethorathemes.com/'. THEME_SLUG .'/', 'title' => esc_html__( 'Online demo pages', 'plethora-framework' ));
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-documentation', 'href' => 'http://doc.plethorathemes.com/'. THEME_SLUG .'/', 'title' => esc_html__( 'Online documentation', 'plethora-framework' ));
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-support', 'href' => 'https://plethorathemes.zendesk.com', 'title' => esc_html__( 'Create Support Ticket', 'plethora-framework' ));

		// ARGUMENTS --> ADVANCED FEATURES
			$args['transient_time']		= 60 * MINUTE_IN_SECONDS;	// Set the amount of time to assign to transient values used.
			$args['output']				= true;						// Enable/disable dynamic CSS output. When set to false, Google fonts are also disabled
			$args['output_tag'] 		= true;                     // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
			$args['footer_credit']		= esc_html__('Plethora Theme Options panel. Based on Redux Framework', 'plethora-framework');						// Set the text to be displayed at the bottom of the options panel, in the footer across from the WordPress version (where it normally says 'Thank you for creating with WordPress') (HTML is allowed)

		// NEW ARGUMENTS
			$args['ajax_save']     = true;                     
			$args['use_cdn']       = false;                    
			$args['update_notice'] = false;                    
			$args['disable_tracking'] = false;                    

		// ARGUMENTS --> FUTURE ( Not in use yet, but reserved or partially implemented. Use at your own risk. )
			// $args['database']			= '';						// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
			// $args['system_info']		= false;					// Remove

			
		// ARGUMENTS --> PLETHORA SOCIAL ICONS (displayed in footer)

			$args['share_icons'][]   = array( 'url' => 'https://twitter.com/plethorathemes', 'title' => esc_html__('Follow Plethora on Twitter', 'plethora-framework'), 'icon' => 'el-icon-twitter' );
			$args['share_icons'][]   = array( 'url' => 'https://www.facebook.com/plethorathemes', 'title' => esc_html__('Find Plethora on Facebook', 'plethora-framework'), 'icon' => 'el-icon-facebook' );
			$args['share_icons'][]   = array( 'url' => 'https://www.youtube.com/channel/UCRk3LXfZj7CpEwTjaI0BLDQ', 'title' => esc_html__('Watch Plethora channel on YouTube', 'plethora-framework'), 'icon' => 'el-icon-youtube' );

			$this->args = $args;
				
		}

//// THEME OPTIONS PANEL CONFIGURATION BEGINS

		public function initSettings() {

			// ARGUMENTS --> GENERAL CONFIGURATION
			$this->setArguments();
			if ( !isset( $this->args['opt_name'] ) ) { return; } // No errors please
			$this->set_theme_options_tab_hooks(); // Always first in order for hook points to work
			$this->set_theme_options_hookpoints();
			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);

		}
		
	// SET SECTION HOOKPOINTS -> START

		public function set_theme_options_tab_hooks() {

		// SECTIONS CONFIGURATION
			// General Section ( adding filters applied to 'plethora_themeoptions_general')
			add_filter( 'plethora_themeoptions_general', array($this, 'subsection_misc'), 999);				// Other subsection
			// Header Section ( adding filters applied to 'plethora_themeoptions_header')
			add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headergeneral'), 5);		// Header Layout subsection

			// Footer Section ( adding filters applied to 'plethora_themeoptions_footer')
			add_filter( 'plethora_themeoptions_footer', array($this, 'subsection_footergeneral'), 5);		// Main Section
			// Content Section ( adding filters applied to 'plethora_themeoptions_content' / Use 1 or 2 digit priority for archives and 3-digit for singles )
			add_filter( 'plethora_themeoptions_content', array($this, 'subsection_404'), 999);				// 404 page subsection
			add_filter( 'plethora_themeoptions_content', array($this, 'subsection_search'), 999);			// Search page subsection
		}

		public function set_theme_options_hookpoints() {

		// SECTIONS CONFIGURATION
			$sections = array();

			// GENERAL SECTION ( developers may hook here! )              
			if ( has_filter( 'plethora_themeoptions_general') ) {
				$sections[] = $this->section_general();		// Set General section tab first!
				$sections = apply_filters( 'plethora_themeoptions_general', $sections );
			}

			// HEADER SECTION ( developers may hook here! )              
			if ( has_filter( 'plethora_themeoptions_header') ) {
				$sections[] = $this->section_header();		// Set General section tab first!
				$sections = apply_filters( 'plethora_themeoptions_header', $sections );
			}

			// MEDIA PANEL SECTION ( developers may hook here! )              
			// Note: options are given via Plethora_Module_Mediapanel class
			if ( has_filter( 'plethora_themeoptions_mediapanel') ) {

				$sections[] = $this->section_mediapanel();		// Set General section tab first!
				$sections = apply_filters( 'plethora_themeoptions_mediapanel', $sections );
			}

			// CONTENT SECTION  ( developers may hook single post options here! )               
			if ( has_filter( 'plethora_themeoptions_content') ) {
				$sections[] = $this->section_content();		// Set content section tab first!
				$sections = apply_filters( 'plethora_themeoptions_content', $sections );
			}

			// FOOTER SECTION ( developers may hook here! )              
			if ( has_filter( 'plethora_themeoptions_footer') ) {
				$sections[] = $this->section_footer();		// Set General section tab first!
				$sections = apply_filters( 'plethora_themeoptions_footer', $sections );
			}

			// ADD-ONS & MODULES SECTION  ( developers may hook plugin supports, APIs and modules here! )               
			if ( has_filter( 'plethora_themeoptions_modules') ) {
				$sections[] = $this->section_modules();		// Set supported APIs section tab first!
				$sections = apply_filters( 'plethora_themeoptions_modules', $sections );
			} 

			// ADVANCED SECTION  ( developers may hook here! )               
			if ( has_filter( 'plethora_themeoptions_advanced') ) {
				$sections[] = $this->section_advanced();		// Set advanced section tab first!
				$sections = apply_filters( 'plethora_themeoptions_advanced', $sections );
			}

			// HELP SECTION  ( developers may hook here! )               
			if ( has_filter( 'plethora_themeoptions_help') ) {
				$sections[] = $this->section_help();		// Set advanced section tab first!
				$sections = apply_filters( 'plethora_themeoptions_help', $sections );
			}

			$this->sections = $sections;

		}

		function section_general() { 

			$return = array(
				'title'      => esc_html__('General', 'plethora-framework'),
				'icon'       => 'el-icon-globe-alt',
				);
			return $return;
		}

		function section_header() { 

			$return = array(
				'title'      => esc_html__('Header', 'plethora-framework'),
				'icon'       => 'el-icon-circle-arrow-up',
				);
			return $return;
		}

		function section_mediapanel() { 

			$return = array(
				'title'      => esc_html__('Media Panel', 'plethora-framework'),
				'icon'       => 'el-icon-photo',
				);
			return $return;
		}

		function section_footer() { 

			$return = array(
				'title'      => esc_html__('Footer', 'plethora-framework'),
				'icon'       => 'el-icon-circle-arrow-down',
				);
			return $return;
		}

		function section_content() { 

			$return = array(
				'title'      => esc_html__('Content', 'plethora-framework'),
				'icon'       => 'el-icon-folder-open',
				);
			return $return;
		}

		function section_modules() { 

			$return = array(
				'title'      => esc_html__('Add-ons & Modules', 'plethora-framework'),
				'icon'       => 'el-icon-puzzle',
				'icon_class' => ''
				);
			return $return;
		}

		function section_advanced() { 

			$return = array(
				'title'      => esc_html__('Advanced', 'plethora-framework'),
				'icon'       => 'el-icon-cogs',
				'icon_class' => ''
				);
			return $return;
		}

		function section_help() { 

			$return = array(
				'icon'       => 'el-icon-question',
				'title'      => esc_html__('Help', 'plethora-framework'),
				// 'heading'      => esc_html__('SEND A TICKET TO PLETHORA SUPPORT', 'plethora-framework'),
				// 'desc'       => self::get_system_info() ,
				);

			return $return;
		}

	// SET SECTION HOOKPOINTS -> FINISH

	// SET THEME SPECIFIC OPTION TABS -> START   

		function subsection_misc( $sections ) { 

			$misc_fields = array();
			// A hook for modules that want to add options to MISC tab              
			if ( has_filter( 'plethora_themeoptions_general_misc_fields') ) {

				$misc_fields = apply_filters( 'plethora_themeoptions_general_misc_fields', $misc_fields );
			}

			$sections[] = array(
				'title'      => esc_html__('Misc', 'plethora-framework'),
				'heading'     => esc_html__('MISCELLANEOUS ELEMENTS', 'plethora-framework'),
				'subsection' => true,
				'fields'     => array_merge( 
									$misc_fields,
									array(
										array(
											'id'      => THEMEOPTION_PREFIX .'less-container-fluid-max-width',
											'type'    => 'button_set', 
											'title'   => esc_html__( 'Fluid Container Max Width', 'plethora-framework' ),
											'desc'    => esc_html__( 'When fluid is chosen the default width of the container is set to occupy the full width of the browser window ( auto ). This setting allows you to configure a max width for the fluid container mainly to optimize for larger screens.', 'plethora-framework' ),
											'default' => 'auto', 
											'options' => array(
																'auto'   => esc_html__('Auto', 'plethora-framework'),
																'custom' => esc_html__('Set Custom', 'plethora-framework'),
															),
											),
										array(
											'id'       => THEMEOPTION_PREFIX .'less-container-fluid-max-width-custom',
											'type'     => 'spinner', 
											'required' => array( THEMEOPTION_PREFIX .'less-container-fluid-max-width','=','custom'),						
											'title'    => esc_html__('Fluid Container Max Width // Custom Value', 'plethora-framework'),
											'subtitle' => esc_html__('Default: 1600px | Max: 1920px', 'plethora-framework'),
											"min"      => 480,
											"step"     => 20,
											"max"      => 1920,
											"default"  => 1600,
											),	
										array(
											'id'       => THEMEOPTION_PREFIX .'less-section-background-transparency',
											'type'     => 'spinner', 
											'title'    => esc_html__('Global Transparency Level', 'plethora-framework'),
											'subtitle' => esc_html__('Default: 50%', 'plethora-framework'),
											'desc'     => esc_html__('This is the transparency level for the overlay film applied on various elements ( i.e. row element ).', 'plethora-framework'),
											"min"      => 1,
											"step"     => 1,
											"max"      => 100,
											"default"  => 50,
											),	
										// Header styling
										array(
										   'id' => 'page-loader-start',
										   'type' => 'section',
										   'title' => esc_html__('Page Loader Effect', 'plethora-framework'),
										   'subtitle' => esc_html__('Page loader effect options', 'plethora-framework'),
										   'indent' => true,
										 ),
											array(
												'id'      => THEMEOPTION_PREFIX .'page-loader',
												'type'    => 'switch', 
												'title'   => esc_html__('Enable Page Loader', 'plethora-framework'),
												'default' => 0, 
												),

											array(
												'id'       => THEMEOPTION_PREFIX .'page-loader-image-logo',
												'type'     => 'media', 
												'url'      => true,			
												'title'    => esc_html__('Page Loader Logo Image', 'plethora-framework'),
												'desc'     => esc_html__('Use a transparent PNG image for better results', 'plethora-framework'),
												'default'  => array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/logo-white.png'),
												'required' => array( 
																array( THEMEOPTION_PREFIX .'page-loader','=',1 ),
																),						
												),
											array(
												'id'       => THEMEOPTION_PREFIX .'page-loader-image-loader',
												'type'     => 'media', 
												'url'      => true,			
												'title'    => esc_html__('Page Loader Logo Image', 'plethora-framework'),
												'desc'     => esc_html__('Use a rotating GIF image for better results', 'plethora-framework'),
												'default'  => array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/loader.gif'),
												'required' => array( 
																array( THEMEOPTION_PREFIX .'page-loader','=',1 ),
																),						
												),

											array(
												'id'          => THEMEOPTION_PREFIX .'less-page-loader-bgcolor',
												'type'        => 'color',
												'title'       => esc_html__('Page Loader Background Color', 'plethora-framework'), 
												'subtitle'    => esc_html__('default: #000000', 'plethora-framework'),
												'default'     => '#000000',
												'transparent' => false,
												'validate'    => 'color',
												'required' => array( 
																array( THEMEOPTION_PREFIX .'page-loader','=',1 ),
																),						
												),

		
										array(
										   'id' => 'page-loader-end',
										   'type' => 'section',
										   'indent' => false,
										 ),

									)
								)
			);

			return $sections;
		}

		function subsection_headergeneral( $sections ) { 

			$sections[] = array(
				'title'      => esc_html__('General', 'plethora-framework'),
				'heading'	 => esc_html__('HEADER GENERAL OPTIONS', 'plethora-framework'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'      => METAOPTION_PREFIX .'header-container-type',
						'type'    => 'button_set', 
						'title'   => esc_html__('Container Type', 'plethora-framework'),
						'default' => 'container-fluid',
						'options' => array(
										'container'       => esc_html__( 'Default', 'plethora-framework'),
										'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
							)
					),

					array(
						'id'       => METAOPTION_PREFIX .'header-layout',
						'type'     => 'image_select',
						'title'    => esc_html__('Logo & Main Navigation Layout', 'plethora-framework'), 
						'subtitle' => esc_html__('Click to the icon according to the desired logo / Main navigation layout. ', 'plethora-framework'),
						'default'  => '',
						'options'  => array(
								''                      => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Right', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-right.png'),
								'nav_left'              => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Left', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-left.png'),
								'nav_centered'          => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Centered', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-center.png'),
								'logo_centered_in_menu' => array('alt' => esc_html__( 'Logo Centered Inside Main Navigation', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-center-in-menu.png'),
								'header_centered'       => array('alt' => esc_html__( 'Logo & Main Navigation: Centered', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-center-menu-center.png'),
							),
						),

					array(
						'id'      => METAOPTION_PREFIX .'header-trans',
						'type'    => 'switch', 
						'title'   => esc_html__('Transparency', 'plethora-framework'),
						'desc'    => sprintf( esc_html__('Enable the header transparency ( make sure you have set the %1$sTheme Options > Header > General > Transparency Opacity Level%2$s option to a value less than 100 ).', 'plethora-framework'), '<strong>', '</strong>' ), 
						"default" => 0,
					),	

					array(
						'id'            => THEMEOPTION_PREFIX .'less-header-trans-opacity',
						'type'          => 'slider',
						'title'         => esc_html__('Transparency Opacity Level', 'plethora-framework'), 
						'desc'          => esc_html__('Set the header opacity level. 100% means that the header will remain solid, while 0% means that it will be complely transparent / default: 100%', 'plethora-framework'), 
						"default"       => 100,
						"min"           => 0,
						"step"          => 1,
						"max"           => 100,
						'display_value' => 'text'
					),
					array(
						'id'       => METAOPTION_PREFIX .'header-extraclass',
						'type'     => 'text', 
						'title'    => esc_html__('Extra Classes', 'plethora-framework'),
						'desc'     => esc_html__('Style header differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
						'validate' => 'no_special_chars',
						"default"  => '',
					),	
					array(
						'id'       => 'header-colorset',
						'type'     => 'section',
						'title'    => esc_html__('Header Color Set', 'plethora-framework'),
						'subtitle' => esc_html__('Page loader effect options', 'plethora-framework'),
						'indent'   => true,
					),
					array(
						'id'          => THEMEOPTION_PREFIX .'less-header-bgcolor',
						'type'        => 'color',
						'title'       => esc_html__('Background Color', 'plethora-framework'), 
						'subtitle'    => esc_html__('default: #000000', 'plethora-framework'),
						'desc'        => esc_html__('The default background color', 'plethora-framework'),
						'default'     => '#000000',
						'transparent' => false,
						'validate'    => 'color',
						),
					array(
						'id'          => THEMEOPTION_PREFIX .'less-header-txtcolor',
						'type'        => 'color',
						'title'       => esc_html__('Text Color', 'plethora-framework'), 
						'subtitle'    => esc_html__('default: #ffffff', 'plethora-framework'),
						'desc'        => esc_html__('Text color for non linked texts ( i.e. logo title/subtitle )', 'plethora-framework'),
						'default'     => '#ffffff',
						'transparent' => false,
						'validate'    => 'color',
						),
					array(
						'id'       => THEMEOPTION_PREFIX .'less-header-linkcolor',
						'type'     => 'link_color',
						'title'    => esc_html__('Link Color', 'plethora-framework'), 
						'desc'     => esc_html__('Color for navigation items and other link anchor texts', 'plethora-framework'),
						'subtitle' => esc_html__('default: #ffffff / #ffffff', 'plethora-framework'),
						'visited'  => false,
						'active'   => false,
						'default'  => array(
							'regular'  => '#ffffff', 
							'hover'    => '#ffffff',
							),
						'validate'    => 'color',
						),
				   )
				);
			return $sections;
		}

		function subsection_footergeneral( $sections ) {

			$sections[] = array(
				'title'      => esc_html__('General', 'plethora-framework'),
				'heading'      => esc_html__('FOOTER SECTION', 'plethora-framework'),
				'subsection' => true,
				'fields'     => array(

						array(
							'id'       => METAOPTION_PREFIX .'footer-extraclass',
							'type'     => 'text', 
							'title'    => esc_html__('Extra Classes', 'plethora-framework'),
							'desc'     => esc_html__('Style footer differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
							'validate' => 'no_special_chars',
							"default"  => '',
						),	

						// Footer styling
						array(
						   'id' => 'footer-colorset-start',
						   'type' => 'section',
						   'title' => esc_html__('Footer Color Set', 'plethora-framework'),
						   'subtitle' => esc_html__('Color options for footer section.', 'plethora-framework'),
						   'indent' => true,
						 ),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-bgcolor',
									'type'        => 'color',
									'title'       => esc_html__('Footer Background', 'plethora-framework'), 
									'subtitle'    => esc_html__('default: #16161D.', 'plethora-framework'),
									'default'     => '#16161D',
									'transparent' => false,
									'validate'    => 'color',
									),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-txtcolor',
									'type'        => 'color',
									'title'       => esc_html__('Footer Text', 'plethora-framework'), 
									'subtitle'    => esc_html__('default: #fdfdfd.', 'plethora-framework'),
									'default'     => '#fdfdfd',
									'transparent' => false,
									'validate'    => 'color',
									),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-linkcolor',
									'type'        => 'link_color',
									'title'       => esc_html__('Footer Link Text', 'plethora-framework'), 
									'subtitle'    => esc_html__('default: #6FB586/#7CC293', 'plethora-framework'),
									'visited'     => false,
									'active'     => false,
									'default'  => array(
										'regular'  => '#6FB586', 
										'hover'    => '#7CC293',
										),
									'validate'    => 'color',
									),

						array(
							'id'     => 'footer-colorset-end',
							'type'   => 'section',
							'indent' => false,
						),	
					)
				);
			return $sections;
		}

		function subsection_404( $sections ) {

			$sections[] = array(
				'title'      => esc_html__('404 Page', 'plethora-framework'),
				'heading'      => esc_html__('404 PAGE OPTIONS', 'plethora-framework'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       =>THEMEOPTION_PREFIX .'mediapanel-404-image',
						'type'     => 'media', 
						'title'    => esc_html__('Featured Image', 'plethora-framework'),
						'url'      => true,
						'default'  =>array('url'=> PLE_THEME_ASSETS_URI .'/images/404_alt.jpg'),
						),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-title-text',
						'type'    => 'text',
						'title'   => esc_html__('Title', 'plethora-framework'),
						'default' => esc_html__('OMG! ERROR 404', 'plethora-framework'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-subtitle-text',
						'type'    => 'text',
						'title'   => esc_html__('Subtitle', 'plethora-framework'),
						'default' => esc_html__('The requested page cannot be found!', 'plethora-framework'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-contenttitle',
						'type'    => 'text',
						'title'   => esc_html__('Additional Title On Content', 'plethora-framework'),
						'default' => esc_html__('ERROR 404 IS NOTHING TO REALLY WORRY ABOUT...', 'plethora-framework'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-content',
						'type'    => 'textarea',
						'title'   => esc_html__('Content', 'plethora-framework'), 
						'default' => esc_html__('You may have mis-typed the URL, please check your spelling and try again.', 'plethora-framework'), 
						'translate' => true
					),
					array(
						'id'      => THEMEOPTION_PREFIX .'404-search',
						'type'    => 'switch', 
						'title'   => esc_html__('Display search field', 'plethora-framework'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-search-btntext',
						'required'     => array(THEMEOPTION_PREFIX .'404-search','=',1),						
						'type'    => 'text',
						'title'   => esc_html__('Search Button Text', 'plethora-framework'), 
						'default' => esc_html__('Search', 'plethora-framework'), 
						'translate' => true
					),
				)
			);
			return $sections;
		}

		function subsection_search( $sections ) {

			$sections[] = array(
				'title'      => esc_html__('Search Page', 'plethora-framework'),
				'heading'      => esc_html__('SEARCH PAGE OPTIONS', 'plethora-framework'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'      => METAOPTION_PREFIX .'search-layout',
						'title'   => esc_html__( 'Page Layout', 'plethora-framework' ),
						'type'    => 'image_select',
						'default' => 'right_sidebar',
						'options' => Plethora_Module_Style::get_options_array( array( 
																					'type'   => 'page_layouts',
																					'use_in' => 'redux',
																			   )
									 ),
					),
					array(
						'id'      => METAOPTION_PREFIX .'search-sidebar',
						'type'    => 'select',
						'data'    => 'sidebars',
						'multi'   => false,
						'default' => 'sidebar-default',
						'title'   => esc_html__('Sidebar', 'plethora-framework'), 
					),

					array(
						'id'      => METAOPTION_PREFIX .'search-containertype',
						'type'    => 'button_set', 
						'title'   => esc_html__('Container Type', 'plethora-framework'),
						'default' => 'container',
						'options' => array(
										'container'       => esc_html__( 'Default', 'plethora-framework'),
										'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
							)
					),

					array(
						'id'      => METAOPTION_PREFIX .'search-colorset',
						'type'    => 'button_set',
						'title'   => esc_html__( 'Content Color Set', 'plethora-framework' ),
						'options' => Plethora_Module_Style_Ext::get_options_array( array( 'type'=> 'color_sets', 'prepend_default' => true ) ),
						'default' => '',
					),

					array(
						'id'      => THEMEOPTION_PREFIX .'search-title',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Title On Content', 'plethora-framework'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'        => THEMEOPTION_PREFIX .'search-title-text',
						'type'      => 'text',
						'title'     => esc_html__('Title Prefix', 'plethora-framework'),
						'desc'      => esc_html__('Will be displayed before search keyword', 'plethora-framework'),
						'default'   => esc_html__('Search For:', 'plethora-framework'),
						'translate' => true,
					),
					array(
						'id'      => THEMEOPTION_PREFIX .'search-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Subtitle On Content', 'plethora-framework'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'      =>THEMEOPTION_PREFIX .'search-subtitle-text',
						'type'    => 'text',
						'title'   => esc_html__('Subtitle', 'plethora-framework'),
						'default' => esc_html__('This is the default search subtitle', 'plethora-framework')
					),
				)
			);
			return $sections;
		}	    
	// SET THEME SPECIFIC OPTION TABS -> FINISH   

//// THEME OPTIONS PANEL CONFIGURATION ENDS

//// METABOXES CONFIGURATION BEGINS

	  /**
	   * A static method that returns metabox configuration
	   * It is hooked on early priority 'init' by Plethora_Optionsframework class
	   *
	   * @since 1.0
	   */
		public static function metabox_hooks() {

			// Add Header / Footer metaboxes to single post types
			$single_post_types = Plethora_Theme::get_supported_post_types();
			foreach ( $single_post_types as $key=>$post_type) {

				add_filter( 'plethora_metabox_single_'. $post_type .'_header_fields', array('Plethora_Module_Themeoptions', 'metabox_header_fields'), 5 );
				add_filter( 'plethora_metabox_single_'. $post_type .'_footer_fields', array('Plethora_Module_Themeoptions', 'metabox_footer_fields'), 5 );
			}

			// Add Header / Footer metaboxes to archive post types
			$archive_post_types = Plethora_Theme::get_supported_post_types( array( 'type'=>'archives' ) );
			foreach ( $archive_post_types as $key=>$post_type) {

				add_filter( 'plethora_metabox_archive_'. $post_type .'_header_fields', array('Plethora_Module_Themeoptions', 'metabox_header_fields'), 5 );
				add_filter( 'plethora_metabox_archive_'. $post_type .'_footer_fields', array('Plethora_Module_Themeoptions', 'metabox_footer_fields'), 5 );
			}
		}

	  /**
	   * Header elements tab for metaboxes
	   *
	   * @since 2.0
	   */
		public static function metabox_header_fields( $sections ) {

			$fields = array(
				array(
					'id' => 'header-section-general',
					'type' => 'section',
					'title' => esc_html__('General Options', 'plethora-framework'),
					'subtitle' => esc_html__('General header options', 'plethora-framework'),
					'indent' => true,
				),
				array(
					'id'      => METAOPTION_PREFIX .'header-container-type',
					'type'    => 'button_set', 
					'title'   => esc_html__('Container Type', 'plethora-framework'),
					'options' => array(
									'container'       => esc_html__( 'Default', 'plethora-framework'),
									'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
						)
				),
				array(
					'id'       => METAOPTION_PREFIX .'header-layout',
					'type'     => 'image_select',
					'title'    => esc_html__('Logo & Main Navigation Layout', 'plethora-framework'), 
					'subtitle' => esc_html__('Click to the icon according to the desired logo / Main navigation layout. ', 'plethora-framework'),
					'options'  => array(
							''                      => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Right', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-right.png'),
							'nav_left'              => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Left', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-left.png'),
							'nav_centered'          => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Centered', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-center.png'),
							'logo_centered_in_menu' => array('alt' => esc_html__( 'Logo Centered Inside Main Navigation', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-center-in-menu.png'),
							'header_centered'       => array('alt' => esc_html__( 'Logo & Main Navigation: Centered', 'plethora-framework' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-center-menu-center.png'),
						),
					),

				array(
					'id'      => METAOPTION_PREFIX .'header-trans',
					'type'    => 'switch', 
					'title'   => esc_html__('Transparency', 'plethora-framework'),
					'desc'    => sprintf( esc_html__('Enable the header transparency ( IMPORTANT: make sure you have set the %1$sTheme Options > Header > General > Transparency Opacity Level%2$s option to a value less than 100% ).', 'plethora-framework'), '<strong>', '</strong>' ), 
				),	

				array(
					'id'       => METAOPTION_PREFIX .'header-extraclass',
					'type'     => 'text', 
					'title'    => esc_html__('Extra Classes', 'plethora-framework'),
					'desc'     => esc_html__('Style header differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
					'validate' => 'no_special_chars',
					"default"  => '',
				),	
			);
			return apply_filters( 'plethora_metabox_header_fields_edit', $fields );
		}

	  /**
	   * Header elements tab for metaboxes
	   *
	   * @since 2.0
	   */
		public static function metabox_footer_fields( $sections ) {

			$fields = array(
				// Footer general
				array(
					'id'       => 'footer-general-start',
					'type'     => 'section',
					'title'    => esc_html__('General Options', 'plethora-framework'),
					'subtitle' => esc_html__('General footer options', 'plethora-framework'),
					'indent'   => true,
				 ),
				array(
					'id'       => METAOPTION_PREFIX .'footer-extraclass',
					'type'     => 'text', 
					'title'    => esc_html__('Extra Classes', 'plethora-framework'),
					'desc'     => esc_html__('Style footer differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
					'validate' => 'no_special_chars',
					"default"  => '',
				),	
			);
			return apply_filters( 'plethora_metabox_footer_fields_edit', $fields );
		}

//// METABOXES CONFIGURATION ENDS

//// LESS CONFIGURATION BEGINS
		public static function less_variables( $vars ) { 

		// THEME OPTIONS > GENERAL > BASIC COLORS & COLOR SET OPTIONS


		// THEME OPTIONS > GENERAL > TYPOGRAPHY


		// THEME OPTIONS > GENERAL > MISC

			$vars['wp-container-fluid-max-width']       = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-container-fluid-max-width', 'auto', 0, false) == 'custom' ? Plethora_Theme::option(THEMEOPTION_PREFIX .'less-container-fluid-max-width-custom', '1600', 0, false) .'px' : 'auto';
			$vars['wp-section-background-transparency'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-section-background-transparency', 50, 0, false);
			$vars['wp-loader-bgcolor']                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-page-loader-bgcolor', '#000000', 0, false);

		// THEME OPTIONS > MEDIA PANEL

			$hgroup_padding                                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding', array( 'padding-top'=>'166', 'padding-bottom'=>'170'), 0, false );
			$hgroup_padding_sm                               = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding-sm', array( 'padding-top'=>'150', 'padding-bottom'=>'60'), 0, false );
			$hgroup_padding_xs                               = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding-xs', array( 'padding-top'=>'120', 'padding-bottom'=>'40'), 0, false );
			$hgroup_font                                     = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font', array( 'font-size' => '86px' ), 0, false );
			$hgroup_font_sm                                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font-sm', array( 'font-size' => '60px' ), 0, false );
			$hgroup_font_xs                                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font-xs', array( 'font-size' => '36px' ), 0, false );
			$vars['wp-head-panel-hgroup-padding-top']        = !empty( $hgroup_padding['padding-top'] ) ? $hgroup_padding['padding-top'].'px' : '120px';
			$vars['wp-head-panel-hgroup-padding-bottom']     = !empty( $hgroup_padding['padding-bottom'] ) ? $hgroup_padding['padding-bottom'].'px' : '120px';
			$vars['wp-head-panel-hgroup-padding-top-sm']     = !empty( $hgroup_padding_sm['padding-top'] ) ? $hgroup_padding_sm['padding-top'].'px' : '100px';
			$vars['wp-head-panel-hgroup-padding-bottom-sm']  = !empty( $hgroup_padding_sm['padding-bottom'] ) ? $hgroup_padding_sm['padding-bottom'].'px' : '100px';
			$vars['wp-head-panel-hgroup-padding-top-xs']     = !empty( $hgroup_padding_xs['padding-top'] ) ? $hgroup_padding_xs['padding-top'].'px' : '80px';
			$vars['wp-head-panel-hgroup-padding-bottom-xs']  = !empty( $hgroup_padding_xs['padding-bottom'] ) ? $hgroup_padding_xs['padding-bottom'].'px' : '80px';
			$vars['wp-head-panel-title-font-size']           = !empty( $hgroup_font['font-size'] ) ? $hgroup_font['font-size'] : '110px';
			$vars['wp-head-panel-title-font-size-sm']        = !empty( $hgroup_font_sm['font-size'] ) ? $hgroup_font_sm['font-size'] : '80px';
			$vars['wp-head-panel-title-font-size-xs']        = !empty( $hgroup_font_xs['font-size'] ) ? $hgroup_font_xs['font-size'] : '50px';
			$vars['wp-full-width-photo-min-panel-height']    = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height', '380', 0, false) .'px';
			$vars['wp-full-width-photo-min-panel-height-sm'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height-sm', '280', 0, false) .'px';
			$vars['wp-full-width-photo-min-panel-height-xs'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height-xs', '80', 0, false) .'px';
			$vars['wp-map-panel-height']                     = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-map-panel-height', '480', 0, false) .'px';
			$vars['wp-map-panel-height-sm']                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-map-panel-height-sm', '380', 0, false) .'px';
			$vars['wp-map-panel-height-xs']                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-map-panel-height-xs', '280', 0, false) .'px';

		// THEME OPTIONS > HEADER > MAIN SECTION

			// Header ( ok )
			$vars['wp-header-background-transparency'] = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-trans-opacity', 100, 0, false);
			$vars['wp-header-bgcolor']                 = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-bgcolor', '#000000', 0, false);
			$vars['wp-header-txtcolor']                = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-txtcolor', '#ffffff', 0, false);
			$link_color                                = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-linkcolor', array('regular'=>'#ffffff', 'hover'=>'#ffffff'), 0, false);
			$vars['wp-header-linkcolor']               = $link_color['regular'];
			$vars['wp-header-linkcolor-hover']         = $link_color['hover'];



			// Nav Mini Tools
			$navminitools_switch_to_mobile            = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-navminitools-switch-to-mobile', '0', 0, false);
			$vars['wp-navminitools-switch-to-mobile'] = $navminitools_switch_to_mobile . 'px';

		// THEME OPTIONS > FOOTER > MAIN SECTION

			// Main Section ( ok )
			$vars['wp-footer-bgcolor']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-footer-bgcolor', '#16161D', 0, false );
			$vars['wp-footer-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-footer-txtcolor', '#fdfdfd', 0, false );
			$link_color                        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-footer-linkcolor', array('regular'=>'#6FB586', 'hover'=>'#7CC293'), 0, false );
			$vars['wp-footer-linkcolor']       = $link_color['regular'];
			$vars['wp-footer-linkcolor-hover'] = $link_color['hover'];

		// MISC
			
			// WooCommerce MiniCart
			$vars['wp-woo-minicart-cart-icon-size']     = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-woo-minicart-cart-icon-size', 16, 0, false) .'px';
			$vars['wp-woo-minicart-cart-count-color']   = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-woo-minicart-cart-count-color', '#ffffff', 0, false);
			$vars['wp-woo-minicart-cart-count-bgcolor'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-woo-minicart-cart-count-bgcolor', '#2ecc71', 0, false);
			$vars['wp-woo-minicart-account-icon-size']  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-woo-minicart-account-icon-size', 16, 0, false) .'px';

		// RETURN
			return $vars;
		}
//// LESS CONFIGURATION ENDS

//// STATIC HELPER METHODS START

		/** 
		* Returns the final theme options fields configuration 
		* for the given feature class object, based on the existence
		* of the options_index() and options_config() methods 
		* @return array
		*/
		public static function get_themeoptions_fields( $feature_obj ) {

			$fields = array();
			if ( ! is_object( $feature_obj ) && ( method_exists( $feature_obj, 'options_index') && method_exists( $feature_obj, 'options_config') ) ) {

				return $fields;
			}
			// setup theme options according to configuration
			$opts        = $feature_obj->options_index();
			$opts_config = $feature_obj->options_config();
			foreach ( $opts_config as $opt_config ) {

				$id          = isset( $opt_config['id'] ) ? $opt_config['id'] : null;
				$status      = isset( $opt_config['theme_options'] ) ? $opt_config['theme_options'] : false ;
				$default_val = isset( $opt_config['theme_options_default'] ) ? $opt_config['theme_options_default'] : null;
				if ( !is_null( $id ) && $status && array_key_exists( $id, $opts ) ) {

					if ( !is_null( $default_val ) ) { // will add default value only if not NULL
					
						// set default value
						$opts[$id]['default'] = $default_val;

						// add defaults on desc description ( only for field types specified below )
						if ( in_array( $opts[$id]['type'], array( 'color', 'link_color', 'slider', 'spinner' ) ) ) {

							$defaults = ! is_array( $default_val ) ? array( '' => $default_val ) : $default_val;
							$desc = 'Default:';
							$count = 0;
							foreach ( $defaults as $key => $val ) {
								$count++;
								$text =  !empty( $key ) ? ' '.$key.': <strong>'. $val .'</strong>' : ' <strong>'. $val .'</strong>';
								$desc .= $count === 1 ? $text : ', '. $text;
							}
							$opts[$id]['desc'] = !empty( $opts[$id]['desc'] ) ? $opts[$id]['desc'] .'<br>'. $desc : $desc;
						}
					}
					$fields[] = $opts[$id];
				}
			}
			return apply_filters( strtolower( get_parent_class( $feature_obj ) ) . '_themeoptions_fields', $fields );
		}

		/** 
		* Returns the final metabox options fields configuration 
		* for the given feature class object, based on the existence
		* of the theme_options_index() and options_config() methods 
		* @return array
		*/
		public static function get_metabox_fields( $feature_obj ) {

			$fields = array();
			if ( is_object( $feature_obj ) && ( method_exists( $feature_obj, 'options_index') && method_exists( $feature_obj, 'options_config') ) ) {

				// setup theme options according to configuration
				$opts        = $feature_obj->options_index();
				$opts_config = $feature_obj->options_config();
				foreach ( $opts_config as $opt_config ) {

					$id          = isset( $opt_config['id'] ) ? $opt_config['id'] : null;
					$status      = isset( $opt_config['metabox'] ) ? $opt_config['metabox'] : false ;
					$default_val = isset( $opt_config['metabox_default'] ) ? $opt_config['metabox_default'] : null;
					if ( !is_null( $id ) && $status && array_key_exists( $id, $opts ) ) {

						if ( !is_null( $default_val ) ) { // will add default value only if not NULL
						
							// set default value
							$opts[$id]['default'] = $default_val;
						}
						$fields[] = $opts[$id];
					}
				}

				$fields = apply_filters( strtolower( get_parent_class( $feature_obj ) ) . '_metabox_fields', $fields );
			}
			return $fields;
		}

		/** 
		* Returns the default value for the given option out of the given object context. 
		* Used mainly for LESS variables management of theme option panel related modules
		* @return array|string|null
		*/
		public static function get_option_default_value( $feature_obj, $option_id ) {

			$val = null;
			if ( is_object( $feature_obj ) && ( method_exists( $feature_obj, 'options_index') && method_exists( $feature_obj, 'options_config') ) ) {

				$options = self::get_themeoptions_fields( $feature_obj ); // all default values
				foreach ( $options as $option_config ) {

					if ( ! empty( $option_config['id'] ) && $option_config['id'] === $option_id && ! empty( $option_config['default'] ) ) {

						return $option_config['default'];
					}
				}
			}
			return null;
		}		
	}
endif;