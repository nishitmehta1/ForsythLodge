<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

Description: Inlcudes theme options, metaboxes and LESS configuration methods.

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists( 'Plethora_Themeoptions' ) ):

	class Plethora_Themeoptions {

	    public $args        = array();
	    public $sections    = array();
	    public $theme;
	    public $ReduxFramework;


		public function __construct() { 

	        if (!class_exists('ReduxFramework') && is_admin() ) {
	            return;
	        }
	        add_action('init', array( $this, 'initSettings'), 20);
	        add_action('init', array( $this, 'removeDemoModeLink' ) );
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
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-demo', 'href' => 'http://plethorathemes.com/'. THEME_SLUG .'/', 'title' => esc_html__( 'Online demo pages', 'hotel-xenia' ));
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-documentation', 'href' => 'http://doc.plethorathemes.com/'. THEME_SLUG .'/', 'title' => esc_html__( 'Online documentation', 'hotel-xenia' ));
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-support', 'href' => 'https://plethorathemes.zendesk.com', 'title' => esc_html__( 'Create Support Ticket', 'hotel-xenia' ));

		// ARGUMENTS --> ADVANCED FEATURES
			$args['transient_time']		= 60 * MINUTE_IN_SECONDS;	// Set the amount of time to assign to transient values used.
			$args['output']				= true;						// Enable/disable dynamic CSS output. When set to false, Google fonts are also disabled
	        $args['output_tag'] 		= true;                     // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
			$args['footer_credit']		= esc_html__('Plethora Theme Options panel. Based on Redux Framework', 'hotel-xenia');						// Set the text to be displayed at the bottom of the options panel, in the footer across from the WordPress version (where it normally says 'Thank you for creating with WordPress') (HTML is allowed)

	    // NEW ARGUMENTS
			$args['ajax_save']     = true;                     
			$args['use_cdn']       = false;                    
			$args['update_notice'] = false;                    
			$args['disable_tracking'] = false;                    

		// ARGUMENTS --> FUTURE ( Not in use yet, but reserved or partially implemented. Use at your own risk. )
			// $args['database']			= '';						// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
			// $args['system_info']		= false;					// Remove

			
		// ARGUMENTS --> PLETHORA SOCIAL ICONS (displayed in footer)

			$args['share_icons'][]   = array( 'url' => 'https://twitter.com/plethorathemes', 'title' => esc_html__('Follow Plethora on Twitter', 'hotel-xenia'), 'icon' => 'el-icon-twitter' );
			$args['share_icons'][]   = array( 'url' => 'https://www.facebook.com/plethorathemes', 'title' => esc_html__('Find Plethora on Facebook', 'hotel-xenia'), 'icon' => 'el-icon-facebook' );
			$args['share_icons'][]   = array( 'url' => 'https://www.youtube.com/channel/UCRk3LXfZj7CpEwTjaI0BLDQ', 'title' => esc_html__('Watch Plethora channel on YouTube', 'hotel-xenia'), 'icon' => 'el-icon-youtube' );

			$this->args = $args;
				
		}

//// THEME OPTIONS PANEL CONFIGURATION BEGINS

		public function initSettings() {

			// ARGUMENTS --> GENERAL CONFIGURATION
			    $this->setArguments();

		    if (!isset($this->args['opt_name'])) { // No errors please
		                return;
			}

			$this->set_theme_options_tab_hooks(); // Always first in order for hook points to work
			$this->set_theme_options_hookpoints();
			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);

		}
	    
	// SET SECTION HOOKPOINTS -> START

		public function set_theme_options_tab_hooks() {

		// SECTIONS CONFIGURATION
		    // General Section ( adding filters applied to 'plethora_themeoptions_general')
		    add_filter( 'plethora_themeoptions_general', array($this, 'subsection_colorsets'), 10);			// Color sets subsection
		    add_filter( 'plethora_themeoptions_general', array($this, 'subsection_typography'), 10);		// Typography subsection
		    add_filter( 'plethora_themeoptions_general', array($this, 'subsection_misc'), 999);				// Other subsection
		    // Header Section ( adding filters applied to 'plethora_themeoptions_header')
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headergeneral'), 10);		// Header Layout subsection
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headercolors'), 10);		// Header Colors subsection
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headerlogo'), 10);			// Logo subsection
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headernav'), 10);			// Navigation subsection
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headersticky'), 10);		// Sticky header subsection
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headermobilesidebar'), 10);// Sticky header subsection

		    // Footer Section ( adding filters applied to 'plethora_themeoptions_footer')
		    add_filter( 'plethora_themeoptions_footer', array($this, 'subsection_footergeneral'), 10);		// Main Section
		    add_filter( 'plethora_themeoptions_footer', array($this, 'subsection_footerwidgets'), 10);		// Main Section
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
				'title'      => esc_html__('General', 'hotel-xenia'),
				'icon'       => 'el-icon-globe-alt',
				);
	    	return $return;
	    }

		function section_header() { 

	    	$return = array(
				'title'      => esc_html__('Header', 'hotel-xenia'),
				'icon'       => 'el-icon-circle-arrow-up',
				);
	    	return $return;
	    }

		function section_mediapanel() { 

	    	$return = array(
				'title'      => esc_html__('Media Panel', 'hotel-xenia'),
				'icon'       => 'el-icon-photo',
				);
	    	return $return;
	    }

		function section_footer() { 

	    	$return = array(
				'title'      => esc_html__('Footer', 'hotel-xenia'),
				'icon'       => 'el-icon-circle-arrow-down',
				);
	    	return $return;
	    }

		function section_content() { 

	    	$return = array(
				'title'      => esc_html__('Content', 'hotel-xenia'),
				'icon'       => 'el-icon-folder-open',
				);
	    	return $return;
	    }

		function section_modules() { 

	    	$return = array(
				'title'      => esc_html__('Add-ons & Modules', 'hotel-xenia'),
				'icon'       => 'el-icon-puzzle',
				'icon_class' => ''
				);
	    	return $return;
	    }

		function section_advanced() { 

	    	$return = array(
				'title'      => esc_html__('Advanced', 'hotel-xenia'),
				'icon'       => 'el-icon-cogs',
				'icon_class' => ''
				);
	    	return $return;
	    }

		function section_help() { 

			$return = array(
				'icon'       => 'el-icon-question',
				'title'      => esc_html__('Help', 'hotel-xenia'),
				// 'heading'      => esc_html__('SEND A TICKET TO PLETHORA SUPPORT', 'hotel-xenia'),
				// 'desc'       => self::get_system_info() ,
				);

	    	return $return;
	    }

	// SET SECTION HOOKPOINTS -> FINISH

	// SET THEME SPECIFIC OPTION TABS -> START   

	    function subsection_headerlogo( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Logo', 'hotel-xenia'),
				'heading'	 => esc_html__('HEADER SECTION // LOGO OPTIONS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       => METAOPTION_PREFIX .'logo',
						'type'     => 'switch', 
						'title'    => esc_html__('Display Logo', 'hotel-xenia'),
						"default"  => 1,
						'on'       => esc_html__('Display', 'hotel-xenia') ,
						'off'      => esc_html__('Hide', 'hotel-xenia'),
						),
					array(
						'id'      => THEMEOPTION_PREFIX .'logo-layout',
						'required' => array( METAOPTION_PREFIX .'logo','=', 1),						
						'type'    => 'button_set',
						'title'   => esc_html__('Logo layout', 'hotel-xenia'), 
						'options' => array(
							'1' => esc_html__('Image only', 'hotel-xenia'), 
							'2' => esc_html__('Image + Subtitle', 'hotel-xenia'), 
							'3' => esc_html__('Title + Subtitle', 'hotel-xenia'), 
							'4' => esc_html__('Title only', 'hotel-xenia')), 
						'default' => '1'
						),
					array(
						'id'       =>THEMEOPTION_PREFIX .'logo-img',
						'type'     => 'media', 
						'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
						'url'      => true,			
						'title'    => esc_html__('Image', 'hotel-xenia'),
						'default'  =>array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/logo.png'),
						),
					array(
						'id'        =>THEMEOPTION_PREFIX .'logo-title',
						'type'      => 'text',
						'required'  => array( THEMEOPTION_PREFIX .'logo-layout','=', array('3', '4')),						
						'title'     => esc_html__('Title', 'hotel-xenia'),
						'default'   => esc_html__('hotel-xenia', 'hotel-xenia'),
						'translate' => true,
						),
					array(
						'id'       =>THEMEOPTION_PREFIX .'logo-subtitle',
						'type'     => 'text',
						'required' => array( THEMEOPTION_PREFIX .'logo-layout','=', array('2', '3')),						
						'title'    => esc_html__('Subtitle', 'hotel-xenia'),
						'default'  =>  '',
						'translate' => true,
						),
					array(
						'id'       => 'logo-heights-start',
						'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
						'type'     => 'section',
						'title'    => esc_html__('Logo Dimensions', 'hotel-xenia'),
						'subtitle' => esc_html__('The dimensions of the logo are set proportionally according to the logo\'s max height', 'hotel-xenia'),
						'indent'   => true,
				     ),
						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-img-max-height',
							'type'     => 'dimensions',
							'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
							'units'    => false,
							'title'    => esc_html__('Image Max Height (large/medium devices)',  'hotel-xenia'),
							'desc'     => esc_html__('Displays: >991px / default: 50px', 'hotel-xenia'),
							'width'    => false,
							'default'  => array('height'=>'50', 'units'=>'px')
							),												

						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-img-max-height-sm',
							'type'     => 'dimensions',
							'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
							'units'    => false,
							'title'    => esc_html__('Image Max Height (small devices)',  'hotel-xenia'),
							'desc' => esc_html__('Displays: 768px - 991px / default: 44px', 'hotel-xenia'),
							'width'    => false,
							'default'  => array('height'=>'44', 'units'=>'px')
							),												
						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-img-max-height-xs',
							'type'     => 'dimensions',
							'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
							'units'    => false,
							'title'    => esc_html__('Image Max Height (x-small devices)',  'hotel-xenia'),
							'desc'     => esc_html__('Displays: <768px / default: 38px', 'hotel-xenia'),
							'width'    => false,
							'default'  => array('height'=>'38', 'units'=>'px')
							),												
					array(
						'id'       => 'logo-spacing-start',
						'type'     => 'section',
						'title'    => esc_html__('Logo Spacing', 'hotel-xenia'),
						'subtitle'    => esc_html__('You can set an equal space above and below the logo, by setting its vertical margin in all responsive states', 'hotel-xenia'),
						'indent'   => true,
				     ),
						array(
							'id'       => THEMEOPTION_PREFIX .'less-logo-vertical-margin',
							'type'     => 'dimensions',
							'title'    => esc_html__('Vertical Spacing ( large/medium devices )', 'hotel-xenia'),
							'subtitle' => esc_html__('Displays: >991px / default: 24px', 'hotel-xenia'),
							'units'    => false,
							'width'    => false,
							'default'  => array('height' => '24', 'units'=>'px')
							),												
						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-vertical-margin-sm',
							'type'     => 'dimensions',
							'title'    => esc_html__('Vertical Spacing ( small devices )', 'hotel-xenia'),
							'subtitle' => esc_html__('Displays: 768px - 991px / default: 20px', 'hotel-xenia'),
							'units'    => false,
							'width'   => false,
							'default'  => array('height' => '20', 'units'=>'px')
							),												
						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-vertical-margin-xs',
							'type'     => 'dimensions',
							'title'    => esc_html__('Vertical Spacing ( x-small devices )', 'hotel-xenia'),
							'subtitle' => esc_html__('Displays: <768px / default: 16px', 'hotel-xenia'),
							'width'   => false,
							'units'    => false,
							'default'  => array('height' => '16', 'units'=>'px')
							),												
					array(
						'id'       => 'logo-spacing-start',
						'required' => array( METAOPTION_PREFIX .'logo','=', 1),						
						'type'     => 'section',
						'title'    => esc_html__('Logo Title Font Options', 'hotel-xenia'),
						'indent'   => true,
				     ),
						array(
							'id'             => THEMEOPTION_PREFIX .'less-logo-font-size',
							'type'           => 'typography', 
							'title'          => esc_html__('Title Font Size ( large/medium devices )', 'hotel-xenia'),
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
							'subtitle' => esc_html__('Displays: >991px / default: 26px', 'hotel-xenia'),
							'default'        => array( 'font-size' => '26px' ),
							),	
						array(
							'id'             => THEMEOPTION_PREFIX .'less-logo-font-size-sm',
							'type'           => 'typography', 
							'title'          => esc_html__('Title Font Size ( small devices )', 'hotel-xenia'),
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
							'subtitle' => esc_html__('Displays: 768px - 991px / default: 24px', 'hotel-xenia'),
							'default'        => array( 'font-size' => '24px' )
							),	
						array(
							'id'             => THEMEOPTION_PREFIX .'less-logo-font-size-xs',
							'type'           => 'typography', 
							'title'          => esc_html__('Title Font Size ( x-small devices )', 'hotel-xenia'),
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
							'subtitle' => esc_html__('Displays: <768px / default: 22px', 'hotel-xenia'),
							'default'        => array( 'font-size' => '22px' )
							),	
					array(
					    'id'     => 'logo-layout-end',
						'required' => array( METAOPTION_PREFIX .'logo','=', 1),						
					    'type'   => 'section',
					    'indent' => false,
					),						

					)
				);

			return $sections;
	    }

	    function subsection_colorsets( $sections ) { 

			$sections[] = array(
				'title'      => esc_html__('Basic Colors & Sets', 'hotel-xenia'),
				'heading'     => esc_html__('BASIC COLORS & COLOR SET OPTIONS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(
					// Background & Body styling
					array(
				       'id' => 'body-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Basic Colors', 'hotel-xenia'),
				       'subtitle' => esc_html__('Basic color choices that affect several elements within the theme.', 'hotel-xenia'),
				       'indent' => true,
				     ),

							array(
								'id'          => THEMEOPTION_PREFIX .'less-body-bg',
								'type'        => 'color',
								'title'       => esc_html__('Body Background Color', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #f4f4f4.', 'hotel-xenia'),
								'default'     => '#f4f4f4',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-text-color',
								'type'        => 'color',
								'title'       => esc_html__('Text Color', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #323232.', 'hotel-xenia'),
								'default'     => '#323232',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-link-color',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text Color', 'hotel-xenia'), 
								'subtitle'    => esc_html__('defaults: #6FB586 / #498F60', 'hotel-xenia'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#6FB586', 
							        'hover'    => '#498F60',
							    	),
							    'validate'    => 'color',
								),

					array(
					    'id'     => 'body-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),					

					// Primary Color Set
					array(
				       'id' => 'primary-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Primary Color Set', 'hotel-xenia'),
				       'subtitle' => esc_html__('Options for primary colored elements. Background & other design elements are colored according to chosen primary color.', 'hotel-xenia'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-brand-primary',
								'type'        => 'color',
								'title'       => esc_html__('Primary Brand Color', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #6FB586.', 'hotel-xenia'),
								'default'     => '#6FB586',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-primary-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #fdfdfd .', 'hotel-xenia'),
								'default'     => '#fdfdfd ',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-primary-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #fdfdfd/#ffffff', 'hotel-xenia'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#fdfdfd', 
							        'hover'    => '#ffffff',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'primary-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),					

					// Secondary Color Set
					array(
				       'id' => 'secondary-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Secondary Color Set', 'hotel-xenia'),
				       'subtitle' => esc_html__('Color options for secondary colored elements. Background & other design elements are colored according to chosen secondary color ( check above ).', 'hotel-xenia'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-brand-secondary',
								'type'        => 'color',
								'title'       => esc_html__('Secondary Brand Color', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #c5bc58.', 'hotel-xenia'),
								'default'     => '#c5bc58',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-secondary-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'hotel-xenia'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-secondary-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #fdfdfd/#ffffff', 'hotel-xenia'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#fdfdfd', 
							        'hover'    => '#ffffff',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'secondary-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),		

					// Light Color Set
					array(
				       'id' => 'light-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Light Color Set', 'hotel-xenia'),
				       'subtitle' => esc_html__('Color options for light colored elements.', 'hotel-xenia'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-light-section-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #efefef.', 'hotel-xenia'),
								'default'     => '#efefef',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-light-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #323232.', 'hotel-xenia'),
								'default'     => '#323232',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-light-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #6FB586/#498F60', 'hotel-xenia'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#6FB586', 
							        'hover'    => '#498F60',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'light-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),		

					// Dark Color Set
					array(
				       'id' => 'dark-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Dark Color Set', 'hotel-xenia'),
				       'subtitle' => esc_html__('Color options for dark colored elements.', 'hotel-xenia'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-dark-section-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #274c33', 'hotel-xenia'),
								'default'     => '#274c33',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-dark-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'hotel-xenia'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-dark-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #fdfdfd/#ffffff', 'hotel-xenia'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#fdfdfd', 
							        'hover'    => '#ffffff',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'dark-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),						

					// White Color Set
					array(
				       'id' => 'white-section-start',
				       'type' => 'section',
				       'title' => esc_html__('White Color Set', 'hotel-xenia'),
				       'subtitle' => esc_html__('Color options for white colored elements.', 'hotel-xenia'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-white-section-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'hotel-xenia'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-white-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #323232.', 'hotel-xenia'),
								'default'     => '#323232',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-white-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #6FB586/#498F60', 'hotel-xenia'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#6FB586', 
							        'hover'    => '#498F60',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'white-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),						

					// Black colored sections styling
					array(
				       'id' => 'black-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Black Color Set', 'hotel-xenia'),
				       'subtitle' => esc_html__('Color options for black colored elements.', 'hotel-xenia'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-black-section-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #000000.', 'hotel-xenia'),
								'default'     => '#000000',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-black-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'hotel-xenia'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-black-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #6FB586/#498F60', 'hotel-xenia'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array( 
									'regular' =>'#6FB586', 
									'hover'   =>'#498F60'
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'black-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),						
				)
			);
			return $sections;

	    }

	    function subsection_typography( $sections ) { 

			$sections[] = array(
				'title'      => esc_html__('Typography', 'hotel-xenia'),
				'heading'     => esc_html__('TYPOGRAPHY OPTIONS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'             => THEMEOPTION_PREFIX .'less-font-family-sans-serif',
						'type'           => 'typography', 
						'title'          => esc_html__('Primary Font', 'hotel-xenia'),
						'desc'           => esc_html__('Primary font is used in content texts', 'hotel-xenia'),
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
						'default'        => array( 'font-family'=>'Source Sans Pro', 'subsets'=> 'latin' ),
						),	
					array(
						'id'             => THEMEOPTION_PREFIX .'less-font-family-alternative',
						'type'           => 'typography', 
						'title'          => esc_html__('Secondary Font', 'hotel-xenia'),
						'desc'           => esc_html__('Secondary font is used in headings and buttons', 'hotel-xenia'),
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
						'default'        => array( 'font-family'=>'Playfair Display', 'subsets'=> 'latin' ),
						),	

					array(
						'id'             => THEMEOPTION_PREFIX .'less-font-size-base',
						'type'           => 'typography', 
						'title'          => esc_html__('Primary Font Size Base', 'hotel-xenia'),
						'desc'          => esc_html__('All text sizes for body & paragraph elements will be adjusted according to this base.', 'hotel-xenia'),
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
						'default'        => array( 'font-size' => '18px' ),
						),	
					array(
						'id'             => THEMEOPTION_PREFIX .'less-body-font-weight',
						'type'           => 'select', 
						'title'          => esc_html__('Primary Font Weight', 'hotel-xenia'),
						'desc'          => esc_html__('Font weight for body & paragraph elements.', 'hotel-xenia'),
						'default'        => 'normal',
						'options' 		 => array(
											'light'     => esc_html__('Light', 'hotel-xenia'),
											'normal'    => esc_html__('Normal', 'hotel-xenia'),
											'semi-bold' => esc_html__('Semi Bold', 'hotel-xenia'),
											'bold'      => esc_html__('Bold', 'hotel-xenia'),
											'bolder'    => esc_html__('Bolder', 'hotel-xenia'),
											'100'       => esc_html__('100', 'hotel-xenia'),
											'200'       => esc_html__('200', 'hotel-xenia'),
											'300'       => esc_html__('300', 'hotel-xenia'),
											'400'       => esc_html__('400', 'hotel-xenia'),
											'500'       => esc_html__('500', 'hotel-xenia'),
											'600'       => esc_html__('600', 'hotel-xenia'),
											'700'       => esc_html__('700', 'hotel-xenia'),
											'800'       => esc_html__('800', 'hotel-xenia'),
											'900'       => esc_html__('900', 'hotel-xenia'),
							)
						),	
					array(
						'id'             => THEMEOPTION_PREFIX .'less-font-size-alternative-base',
						'type'           => 'typography', 
						'title'          => esc_html__('Secondary Font Size Base', 'hotel-xenia'),
						'desc'          => esc_html__('All text sizes for heading elements will be adjusted according to this base.', 'hotel-xenia'),
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
						'default'        => array( 'font-size' => '16px' )
						),	
					array(
						'id'             => THEMEOPTION_PREFIX .'less-headings-text-transform',
						'type'           => 'typography', 
						'title'          => esc_html__('Heading Text Transform', 'hotel-xenia'),
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
						'default'        => array( 'text-transform' => 'uppercase' )
						),	

					array(
						'id'             => THEMEOPTION_PREFIX .'less-headings-font-weight',
						'type'           => 'select', 
						'title'          => esc_html__('Headings Font Weight', 'hotel-xenia'),
						'default'        => '700',
						'options' 		 => array(
											'light'     => esc_html__('Light', 'hotel-xenia'),
											'normal'    => esc_html__('Normal', 'hotel-xenia'),
											'semi-bold' => esc_html__('Semi Bold', 'hotel-xenia'),
											'bold'      => esc_html__('Bold', 'hotel-xenia'),
											'bolder'    => esc_html__('Bolder', 'hotel-xenia'),
											'100'       => esc_html__('100', 'hotel-xenia'),
											'200'       => esc_html__('200', 'hotel-xenia'),
											'300'       => esc_html__('300', 'hotel-xenia'),
											'400'       => esc_html__('400', 'hotel-xenia'),
											'500'       => esc_html__('500', 'hotel-xenia'),
											'600'       => esc_html__('600', 'hotel-xenia'),
											'700'       => esc_html__('700', 'hotel-xenia'),
											'800'       => esc_html__('800', 'hotel-xenia'),
											'900'       => esc_html__('900', 'hotel-xenia'),
							)
						),	

					array(
						'id'             => THEMEOPTION_PREFIX .'less-btn-text-transform',
						'type'           => 'typography', 
						'title'          => esc_html__('Buttons Text Transform', 'hotel-xenia'),
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
						'default'        => array( 'text-transform' => 'uppercase' )
						),	

					)
				);
			return $sections;

	    }


	    function subsection_misc( $sections ) { 

	    	$misc_fields = array();
			// A hook for modules that want to add options to MISC tab              
	    	if ( has_filter( 'plethora_themeoptions_general_misc_fields') ) {

				$misc_fields = apply_filters( 'plethora_themeoptions_general_misc_fields', $misc_fields );
			}

			$sections[] = array(
				'title'      => esc_html__('Misc', 'hotel-xenia'),
				'heading'     => esc_html__('MISCELLANEOUS ELEMENTS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array_merge( 
									$misc_fields,
									array(
										array(
											'id'      => THEMEOPTION_PREFIX .'less-container-fluid-max-width',
											'type'    => 'button_set', 
											'title'   => esc_html__( 'Fluid Container Max Width', 'hotel-xenia' ),
											'desc'    => esc_html__( 'When fluid is chosen the default width of the container is set to occupy the full width of the browser window ( auto ). This setting allows you to configure a max width for the fluid container mainly to optimize for larger screens.', 'hotel-xenia' ),
											'default' => 'auto', 
											'options' => array(
																'auto'   => esc_html__('Auto', 'hotel-xenia'),
																'custom' => esc_html__('Set Custom', 'hotel-xenia'),
															),
											),
										array(
											'id'       => THEMEOPTION_PREFIX .'less-container-fluid-max-width-custom',
											'type'     => 'spinner', 
											'required' => array( THEMEOPTION_PREFIX .'less-container-fluid-max-width','=','custom'),						
											'title'    => esc_html__('Fluid Container Max Width // Custom Value', 'hotel-xenia'),
											'subtitle' => esc_html__('Default: 1600px | Max: 1920px', 'hotel-xenia'),
											"min"      => 480,
											"step"     => 20,
											"max"      => 1920,
											"default"  => 1600,
											),	
										array(
											'id'       => THEMEOPTION_PREFIX .'less-section-background-transparency',
											'type'     => 'spinner', 
											'title'    => esc_html__('Global Transparency Level', 'hotel-xenia'),
											'subtitle' => esc_html__('Default: 50%', 'hotel-xenia'),
											'desc'     => esc_html__('This is the transparency level for the overlay film applied on various elements ( i.e. row element ).', 'hotel-xenia'),
											"min"      => 1,
											"step"     => 1,
											"max"      => 100,
											"default"  => 50,
											),	
										// Header styling
										array(
									       'id' => 'page-loader-start',
									       'type' => 'section',
									       'title' => esc_html__('Page Loader Effect', 'hotel-xenia'),
									       'subtitle' => esc_html__('Page loader effect options', 'hotel-xenia'),
									       'indent' => true,
									     ),
											array(
												'id'      => THEMEOPTION_PREFIX .'page-loader',
												'type'    => 'switch', 
												'title'   => esc_html__('Enable Page Loader', 'hotel-xenia'),
												'default' => 0, 
												),

											array(
												'id'       => THEMEOPTION_PREFIX .'page-loader-image-logo',
												'type'     => 'media', 
												'url'      => true,			
												'title'    => esc_html__('Page Loader Logo Image', 'hotel-xenia'),
												'desc'     => esc_html__('Use a transparent PNG image for better results', 'hotel-xenia'),
												'default'  => array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/logo-white.png'),
												'required' => array( 
																array( THEMEOPTION_PREFIX .'page-loader','=',1 ),
																),						
												),
											array(
												'id'       => THEMEOPTION_PREFIX .'page-loader-image-loader',
												'type'     => 'media', 
												'url'      => true,			
												'title'    => esc_html__('Page Loader Logo Image', 'hotel-xenia'),
												'desc'     => esc_html__('Use a rotating GIF image for better results', 'hotel-xenia'),
												'default'  => array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/loader.gif'),
												'required' => array( 
																array( THEMEOPTION_PREFIX .'page-loader','=',1 ),
																),						
												),

											array(
												'id'          => THEMEOPTION_PREFIX .'less-page-loader-bgcolor',
												'type'        => 'color',
												'title'       => esc_html__('Page Loader Background Color', 'hotel-xenia'), 
												'subtitle'    => esc_html__('default: #000000', 'hotel-xenia'),
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
				'title'      => esc_html__('General', 'hotel-xenia'),
				'heading'	 => esc_html__('HEADER GENERAL OPTIONS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'      => METAOPTION_PREFIX .'header-container-type',
						'type'    => 'button_set', 
						'title'   => esc_html__('Container Type', 'hotel-xenia'),
						'default' => 'container-fluid',
						'options' => array(
										'container'       => esc_html__( 'Default', 'hotel-xenia'),
										'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia'),
							)
					),

					array(
						'id'       => METAOPTION_PREFIX .'header-layout',
						'type'     => 'image_select',
						'title'    => esc_html__('Logo & Main Navigation Layout', 'hotel-xenia'), 
						'subtitle' => esc_html__('Click to the icon according to the desired logo / Main navigation layout. ', 'hotel-xenia'),
						'default'  => '',
						'options'  => array(
								''                      => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Right', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-right.png'),
								'nav_left'              => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Left', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-left.png'),
								'nav_centered'          => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Centered', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-center.png'),
								'logo_centered_in_menu' => array('alt' => esc_html__( 'Logo Centered Inside Main Navigation', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-center-in-menu.png'),
								'header_centered'       => array('alt' => esc_html__( 'Logo & Main Navigation: Centered', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-center-menu-center.png'),
							),
						),

					array(
						'id'      => METAOPTION_PREFIX .'header-trans',
						'type'    => 'switch', 
						'title'   => esc_html__('Transparency', 'hotel-xenia'),
						'desc'    => sprintf( esc_html__('Enable the header transparency ( make sure you have set the %1$sTheme Options > Header > General > Transparency Opacity Level%2$s option to a value less than 100 ).', 'hotel-xenia'), '<strong>', '</strong>' ), 
						"default" => 0,
					),	

					array(
						'id'            => THEMEOPTION_PREFIX .'less-header-trans-opacity',
						'type'          => 'slider',
						'title'         => esc_html__('Transparency Opacity Level', 'hotel-xenia'), 
						'desc'          => esc_html__('Set the header opacity level. 100% means that the header will remain solid, while 0% means that it will be complely transparent / default: 100%', 'hotel-xenia'), 
						"default"       => 100,
						"min"           => 0,
						"step"          => 1,
						"max"           => 100,
						'display_value' => 'text'
					),
					array(
						'id'       => METAOPTION_PREFIX .'header-extraclass',
						'type'     => 'text', 
						'title'    => esc_html__('Extra Classes', 'hotel-xenia'),
						'desc'     => esc_html__('Style header differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
						'validate' => 'no_special_chars',
						"default"  => '',
					),	
				   )
				);
			return $sections;
	    }

	    function subsection_headercolors( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Colors', 'hotel-xenia'),
				'heading'	 => esc_html__('HEADER COLOR SET OPTIONS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'          => THEMEOPTION_PREFIX .'less-header-bgcolor',
						'type'        => 'color',
						'title'       => esc_html__('Background Color', 'hotel-xenia'), 
						'subtitle'    => esc_html__('default: #000000', 'hotel-xenia'),
						'desc'        => esc_html__('The default background color', 'hotel-xenia'),
						'default'     => '#000000',
						'transparent' => false,
						'validate'    => 'color',
						),
					array(
						'id'          => THEMEOPTION_PREFIX .'less-header-txtcolor',
						'type'        => 'color',
						'title'       => esc_html__('Text Color', 'hotel-xenia'), 
						'subtitle'    => esc_html__('default: #ffffff', 'hotel-xenia'),
						'desc'        => esc_html__('Text color for non linked texts ( i.e. logo title/subtitle )', 'hotel-xenia'),
						'default'     => '#ffffff',
						'transparent' => false,
						'validate'    => 'color',
						),
					array(
						'id'       => THEMEOPTION_PREFIX .'less-header-linkcolor',
						'type'     => 'link_color',
						'title'    => esc_html__('Link Color', 'hotel-xenia'), 
						'desc'     => esc_html__('Color for navigation items and other link anchor texts', 'hotel-xenia'),
						'subtitle' => esc_html__('default: #ffffff / #ffffff', 'hotel-xenia'),
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

	    function subsection_headernav( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Navigation', 'hotel-xenia'),
				'heading'	 => esc_html__('HEADER SECTION // NAVIGATION MENU OPTIONS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       => METAOPTION_PREFIX .'navigation-main',
						'type'     => 'switch', 
						'title'    => esc_html__('Display Main Menu', 'hotel-xenia'),
						"default"  => 1,
						'on'       => esc_html__('Display', 'hotel-xenia') ,
						'off'      => esc_html__('Hide', 'hotel-xenia'),
						),
					array(
						'id'       => METAOPTION_PREFIX .'navigation-main-location',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'select',
						'title'    => esc_html__('Main Menu Location', 'hotel-xenia'), 
						'desc'     => esc_html__('Select the default location to be displayed as your main menu. You have the option to change the main navigation location for every page. ', 'hotel-xenia'),
						'data'     => 'menu_locations',
						'default'  => 'primary',
					),
					array( 
						'id'          => METAOPTION_PREFIX .'navigation-main-behavior',
						'required'    => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'        => 'button_set', 
						'title'       => esc_html__('Multi Level Menu Behavior', 'hotel-xenia'),
						'description' => esc_html__('Choose action to trigger child menu items display', 'hotel-xenia') ,
						"default"     => 'hover_menu',
						'options'     => array(
											'hover_menu' => esc_html__('Mouse Hover', 'hotel-xenia'),
											'click_menu' => esc_html__('Click', 'hotel-xenia'),
										),
						),
					array(
						'id'             => THEMEOPTION_PREFIX .'less-menu-font',
						'required'       => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'           => 'typography', 
						'title'          => esc_html__('Menu Item Font Options', 'hotel-xenia'),
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
						'default'        => array( 'font-size' => '14px', 'text-transform' => 'uppercase' )
						),	
					array(
						'id'             => THEMEOPTION_PREFIX .'less-menu-font-weight',
						'required'       => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'           => 'select', 
						'title'          => esc_html__('Menu Item Font Weight', 'hotel-xenia'),
						'default'        => '500',
						'options' 		 => array(
											'light'     => esc_html__('Light', 'hotel-xenia'),
											'normal'    => esc_html__('Normal', 'hotel-xenia'),
											'semi-bold' => esc_html__('Semi Bold', 'hotel-xenia'),
											'bold'      => esc_html__('Bold', 'hotel-xenia'),
											'bolder'    => esc_html__('Bolder', 'hotel-xenia'),
											'100'       => esc_html__('100', 'hotel-xenia'),
											'200'       => esc_html__('200', 'hotel-xenia'),
											'300'       => esc_html__('300', 'hotel-xenia'),
											'400'       => esc_html__('400', 'hotel-xenia'),
											'500'       => esc_html__('500', 'hotel-xenia'),
											'600'       => esc_html__('600', 'hotel-xenia'),
											'700'       => esc_html__('700', 'hotel-xenia'),
											'800'       => esc_html__('800', 'hotel-xenia'),
											'900'       => esc_html__('900', 'hotel-xenia'),
							)
						),	

					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-padding',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Padding (large devices)',  'hotel-xenia'),
						'desc'     => esc_html__('Displays: >1200px / default: 24px / 12px', 'hotel-xenia'),
						'default'  => array( 'width'=>'24', 'height'=>'12', 'units'=>'px' )
						),												
					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-padding-md',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Padding (medium devices)',  'hotel-xenia'),
						'desc'     => esc_html__('Displays: 992px - 1199px / default: 10px / 10px', 'hotel-xenia'),
						'default'  => array( 'width'=>'10', 'height'=>'10', 'units'=>'px' )
						),												
					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-padding-sm',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Vertical Padding (small devices)',  'hotel-xenia'),
						'desc'     => esc_html__('Displays: <992px / default: 15px / 10x', 'hotel-xenia'),
						'default'  => array( 'width'=>'10', 'height'=>'10', 'units'=>'px')
						),												
					)
			);

			return $sections;
	    }

	    function subsection_headersticky( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Sticky Header', 'hotel-xenia'),
				'heading'    => esc_html__('HEADER SECTION // STICKY HEADER OPTIONS', 'hotel-xenia'),
				'desc'       => esc_html__('Set it to ON if you want your header to remain visible on top when you scroll down a page. All options here are applied exclusively on sticky header section', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'      => METAOPTION_PREFIX .'header-sticky',
						'type'    => 'switch', 
						'title'   => esc_html__('Sticky Header On Scroll', 'hotel-xenia'),
						'desc'    => esc_html__('Set the default header on scroll behavior for all page. You can override this on each page in separate.', 'hotel-xenia'),
						"default" => 0,
					),	
					array( 
						'id'          => THEMEOPTION_PREFIX .'header-sticky-behavior',
						'type'        => 'select', 
						'title'       => esc_html__('Sticky Header Behavior', 'hotel-xenia'),
						'description' => esc_html__('Choose the behavior of sticky header. You can override this on each page in separate.', 'hotel-xenia') ,
						"default"     => 'top',
						'options'     => array(
											'top'             => esc_html__('On top, always visible', 'hotel-xenia'),
											'top_onscroll'    => esc_html__('On top, visible only after scroll starts', 'hotel-xenia'),
											'bottom'          => esc_html__('On bottom, always visible', 'hotel-xenia'),
											'bottom_onscroll' => esc_html__('Starts on bottom and sticks on top after scrolling', 'hotel-xenia'),
										),
						),
					array(
						'id'       => THEMEOPTION_PREFIX .'header-sticky-behavior-scrolloffset',
						'type'     => 'spinner', 
						'title'    => esc_html__('Scroll Offset Trigger', 'hotel-xenia'),
						'subtitle' => esc_html__('Default: 100px', 'hotel-xenia'),
						'desc'     => esc_html__('Set a scrolling point in pixels, beyond which the appearance of "Alternative Sticky Header" will be triggered. This point also applies for the appearance of the Default Header if you choose the "On top, visible only after scroll starts" setting from above.', 'hotel-xenia'),
						"min"      => 0,
						"step"     => 1,
						"max"      => 1200,
						"default"  => 100,
						),

					// Sticky header color set
					array(
						'id'       => 'sticky-header-section-start',
						'type'     => 'section',
						'title'    => esc_html__('Alternative Sticky Header Options ( after scroll )', 'hotel-xenia'),
						'subtitle' => esc_html__('Enable and setup options for an alternative Sticky Header section to be displayed when the user scrolls down the page.', 'hotel-xenia'),
						'indent'   => true,
				     ),

							array(
								'id'      => METAOPTION_PREFIX .'header-sticky-custom',
								'type'    => 'switch', 
								'title'   => esc_html__('Alternative Sticky Header After Scroll', 'hotel-xenia'),
								'desc'    => esc_html__('Set the default behavior. You can enable/disble them per page.', 'hotel-xenia'),
								"default" => 0,
							),	

							array(
								'id'      => THEMEOPTION_PREFIX .'header-sticky-custom-logo',
								'type'    => 'button_set', 
								'title'   => esc_html__('Sticky Header Logo', 'hotel-xenia'),
								'desc'    => esc_html__('Set to on, if you want your logo to be visible on the Alternative sticky header. Set it to custom, if you need a different logo version on scroll.', 'hotel-xenia'),
								'default' => 1,
								'options' => array(
												0        => esc_html__('Off', 'hotel-xenia'),
												1        => esc_html__('On', 'hotel-xenia'),
												'custom' => esc_html__('Custom', 'hotel-xenia'),
											  ),
							),	
							array(
								'id'       	=> THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout',
								'required' => array( 
												array( THEMEOPTION_PREFIX .'header-sticky-custom-logo','equals', 'custom' ),
												),						
								'type'    	=> 'button_set',
								'title'   	=> esc_html__('Sticky Header Logo layout', 'hotel-xenia'), 
								'default' 	=> '1',
								'options' 	=> array(
												'1' => esc_html__('Image only', 'hotel-xenia'), 
												'2' => esc_html__('Image + Subtitle', 'hotel-xenia'), 
												'3' => esc_html__('Title + Subtitle', 'hotel-xenia'), 
												'4' => esc_html__('Title only', 'hotel-xenia')
												), 
								),
							array(
								'id'       => THEMEOPTION_PREFIX .'header-sticky-custom-logo-img',
								'type'     => 'media', 
								'required' => array( 
												array( THEMEOPTION_PREFIX .'header-sticky-custom-logo','equals', 'custom' ),
												array( THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout','=',array('1', '2') ),
												),						
								'url'      => true,			
								'title'    => esc_html__('Sticky Header Logo Image', 'hotel-xenia'),
								'default'  =>array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/logo-white.png'),
								),
							array(
								'id'        => THEMEOPTION_PREFIX .'header-sticky-custom-logo-title',
								'type'      => 'text',
								'required' => array( 
												array( THEMEOPTION_PREFIX .'header-sticky-custom-logo','equals', 'custom' ),
												array( THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout', '=', array('3', '4') ),
												),						
								'title'     => esc_html__('Sticky Header Logo Title', 'hotel-xenia'),
								'default'   => esc_html__('hotel-xenia', 'hotel-xenia'),
								'translate' => true,
								),
							array(
								'id'        => THEMEOPTION_PREFIX .'header-sticky-custom-logo-subtitle',
								'type'      => 'text',
								'required' => array( 
												array( THEMEOPTION_PREFIX .'header-sticky-custom-logo','equals', 'custom' ),
												array( THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout', '=', array('2', '3') ),
												),						
								'title'     => esc_html__('Sticky Header Logo Subtitle', 'hotel-xenia'),
								'default'   =>  '',
								'translate' => true,
								),
							array(
								'id'       => THEMEOPTION_PREFIX .'header-sticky-custom-menu',
								'type'     => 'switch', 
								'title'    => esc_html__('Sticky Header Menu', 'hotel-xenia'),
								'desc'     => esc_html__('Set to on, if you want your main navigation to be visible on sticky header', 'hotel-xenia'),
								"default"  => 1,
							),	
							array(
								'id'       => THEMEOPTION_PREFIX .'header-sticky-custom-trans',
								'type'     => 'switch', 
								'title'    => esc_html__('Sticky Header Transparency', 'hotel-xenia'),
								'desc'     => esc_html__('Set to on, if you want a transparent sticky header', 'hotel-xenia'),
								"default"  => 0,
							),	
							array(
								'id'            => THEMEOPTION_PREFIX .'less-header-sticky-custom-trans-opacity',
								'type'          => 'slider',
								'title'         => esc_html__('Sticky Header Opacity Level', 'hotel-xenia'), 
								'desc'          => esc_html__('Set the opacity level for the sticky header transparency.', 'hotel-xenia'), 
								'subtitle'      => esc_html__('default: 100', 'hotel-xenia'),
								"default"       => 100,
								"min"           => 0,
								"step"          => 1,
								"max"           => 100,
								'display_value' => 'text'
							),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-header-sticky-custom-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Sticky Header Background Color', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #000000', 'hotel-xenia'),
								'default'     => '#000000',
								'transparent' => false,
								'validate'    => 'color',
							),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-header-sticky-custom-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Sticky Header Text Color', 'hotel-xenia'), 
								'subtitle'    => esc_html__('default: #ffffff', 'hotel-xenia'),
								'desc'        => esc_html__('Text color for non linked texts ( i.e. logo title/subtitle )', 'hotel-xenia'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
							),
							array(
								'id'       => THEMEOPTION_PREFIX .'less-header-sticky-custom-linkcolor',
								'type'     => 'link_color',
								'title'    => esc_html__('Sticky Header Link Color', 'hotel-xenia'), 
								'desc'     => esc_html__('Color for navigation items and other link anchor texts', 'hotel-xenia'),
								'subtitle' => esc_html__('default: #ffffff / #ffffff', 'hotel-xenia'),
								'visited'  => false,
								'active'   => false,
								'validate' => 'color',
								'default'  => array(
							        'regular'  => '#ffffff', 
							        'hover'    => '#ffffff',
							    	),
								),
					array(
						'id'       => 'sticky-header-section-end',
						'type'     => 'section',
						'indent'   => false,
					),	
				)
			);

			return $sections;
	    }

	    function subsection_headermobilesidebar( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Mobile Nav Sidebar', 'hotel-xenia'),
				'heading'    => esc_html__('HEADER SECTION // MOBILE NAVIGATION SIDEBAR OPTIONS', 'hotel-xenia'),
				'desc'       => '',
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-switch-to-mobile',
						'type'     => 'spinner', 
						'title'    => esc_html__('Switch To Mobile Menu Threshold', 'hotel-xenia'),
						'subtitle' => esc_html__('Default: 991px', 'hotel-xenia'),
						'desc'     => esc_html__('Set the monitor width threshold for the mobile menu to be enabled. You may set from 0px to 3840x', 'hotel-xenia'),
						"min"      => 0,
						"step"     => 1,
						"max"      => 3840,
						"default"  => 991,
						),	

					array(
						'id'        => THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold',
						'type'      => 'switch',
						'title'     => esc_html__('Label Display ( above threshold )', 'hotel-xenia'),
						'default'   => 1,
						),
					array(
						'id'           => THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold-text',
						'type'         => 'text',
						'title'        => esc_html__('Label Text ( above threshold )', 'hotel-xenia'),
						'desc'         => Plethora_Theme::allowed_html_for( 'button', true),
						'default'      => esc_html__( 'More', 'hotel-xenia' ),
						'validate'     => 'html_custom',
						'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
						'translate'    => true,
						'required'     => array( 
										array( THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold','=', 1 ),
										),						
						),
					array(
						'id'        => THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold',
						'type'      => 'switch',
						'title'     => esc_html__('Label Display  ( below threshold )', 'hotel-xenia'),
						'default'   => 1,
						),
					array(
						'id'           => THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold-text',
						'type'         => 'text',
						'title'        => esc_html__('Label Text ( below threshold )', 'hotel-xenia'),
						'desc'         => Plethora_Theme::allowed_html_for( 'button', true),
						'default'      =>  esc_html__( 'Menu', 'hotel-xenia' ),
						'validate'     => 'html_custom',
						'allowed_html' => Plethora_Theme::allowed_html_for( 'button' ),
						'translate'    => true,
						'required'     => array( 
										array( THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold','=', 1 ),
										),						
						),

					array(
						'id'        => THEMEOPTION_PREFIX .'header-mobsb-navicon',
						'type'      => 'switch',
						'title'     => esc_html__( 'Nav Icon Display ( above threshold )', 'hotel-xenia' ),
						'default'   => 1,
						),

				 	array(
						'id'       => METAOPTION_PREFIX .'header-mobsb-widgetizedarea',
						'type'     => 'select',
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',1),
									  ),
						'title'    => esc_html__('Widgets Area', 'hotel-xenia'), 
						'desc'    => esc_html__('Select the widgets area ( sidebar ) that you want to be displayed on the mobile sidebar', 'hotel-xenia'), 
						'data'	   => 'sidebars',
						'default'  => 'sidebar-mobile'
					),
				)
			);

			return $sections;
	    }


	    function subsection_footergeneral( $sections ) {

	    	$sections[] = array(
				'title'      => esc_html__('General', 'hotel-xenia'),
				'heading'      => esc_html__('FOOTER SECTION', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

						array(
							'id'       => METAOPTION_PREFIX .'footer-extraclass',
							'type'     => 'text', 
							'title'    => esc_html__('Extra Classes', 'hotel-xenia'),
							'desc'     => esc_html__('Style footer differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							'validate' => 'no_special_chars',
							"default"  => '',
						),	

						// Footer styling
						array(
					       'id' => 'footer-colorset-start',
					       'type' => 'section',
					       'title' => esc_html__('Footer Color Set', 'hotel-xenia'),
					       'subtitle' => esc_html__('Color options for footer section.', 'hotel-xenia'),
					       'indent' => true,
					     ),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-bgcolor',
									'type'        => 'color',
									'title'       => esc_html__('Footer Background', 'hotel-xenia'), 
									'subtitle'    => esc_html__('default: #16161D.', 'hotel-xenia'),
									'default'     => '#16161D',
									'transparent' => false,
									'validate'    => 'color',
									),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-txtcolor',
									'type'        => 'color',
									'title'       => esc_html__('Footer Text', 'hotel-xenia'), 
									'subtitle'    => esc_html__('default: #fdfdfd.', 'hotel-xenia'),
									'default'     => '#fdfdfd',
									'transparent' => false,
									'validate'    => 'color',
									),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-linkcolor',
									'type'        => 'link_color',
									'title'       => esc_html__('Footer Link Text', 'hotel-xenia'), 
									'subtitle'    => esc_html__('default: #6FB586/#7CC293', 'hotel-xenia'),
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

	    function subsection_footerwidgets( $sections ) {

	    	$sections[] = array(
				'title'      => esc_html__('Widgetized Areas', 'hotel-xenia'),
				'heading'      => esc_html__('FOOTER WIDGETIZED AREAS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

					array(
				       'id' => 'footer-start',
				       'type' => 'section',
				       'title' => esc_html__('Footer Widgets Area // 1st Row', 'hotel-xenia'),
				       'subtitle' => esc_html__('Options for the 1st row of footer widgets', 'hotel-xenia'),
				       'indent' => true,
				     ),
						array(
							'id'       => METAOPTION_PREFIX .'footer-widgets-1',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Widgets', 'hotel-xenia'),
							'subtitle' => esc_html__('Display/hide footer widgets 1st row', 'hotel-xenia'),
							"default"  => 1,
							'on'       => esc_html__('Display', 'hotel-xenia'),
							'off'      => esc_html__('Hide', 'hotel-xenia'),
							),
						array(
							'id'       => METAOPTION_PREFIX .'footer_top-container-type',
							'type'     => 'button_set', 
							'required' => array(METAOPTION_PREFIX .'footer-widgets-1','=','1'),						
							'title'    => esc_html__('Container Type', 'hotel-xenia'),
							'default'  => 'container',
							'options'  => array(
											'container'       => esc_html__( 'Default', 'hotel-xenia'),
											'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia'),
								)
							),
						array(
							'id'       => METAOPTION_PREFIX .'footer-widgets-1-layout',
							'type'     => 'image_select',
							'required'     => array(METAOPTION_PREFIX .'footer-widgets-1','=','1'),						
							'title'    => esc_html__('Widget Columns Layout', 'hotel-xenia'), 
							'subtitle' => esc_html__('Click to the icon according to the desired widget columns layout. ', 'hotel-xenia'),
							'options'  => array(
									1 => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
									2 => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
									3 => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
									4 => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
									5 => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
									6 => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
									7 => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
									8 => array('alt' => '4 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_4.png'),
								),
							'default' => 5
							),
						 	array(
								'id'       => METAOPTION_PREFIX .'footer-sidebar-1-1',
								'type'     => 'select',
								'required' => array(
													array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
													array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',1),
											  ),
								'title'    => esc_html__('Column 1-1 Sidebar', 'hotel-xenia'), 
								'data'	   => 'sidebars',
								'default'  => 'sidebar-footer-1-1'
							),
							array(
								'id'      => METAOPTION_PREFIX .'footer-sidebar-1-1-extraclass',
								'type'    => 'text', 
								'title'   => esc_html__('Column 1-1 Sidebar Extra Classes', 'hotel-xenia'),
								'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
								"default" => '',
								'required' => array(
													array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
													array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',1),
											  ),
							),	
						 	array(
								'id'       => METAOPTION_PREFIX .'footer-sidebar-1-2',
								'type'     => 'select',
								'required' => array(
													array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
													array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',2),
											  ),
								'title'    => esc_html__('Column 1-2 Sidebar', 'hotel-xenia'), 
								'data'	   => 'sidebars',
								'default'  => 'sidebar-footer-1-2'
							),
							array(
								'id'      => METAOPTION_PREFIX .'footer-sidebar-1-2-extraclass',
								'type'    => 'text', 
								'title'   => esc_html__('Column 1-2 Sidebar Extra Classes', 'hotel-xenia'),
								'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
								"default" => '',
								'required' => array(
													array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
													array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',2),
											  ),
							),	
						 	array(
								'id'       => METAOPTION_PREFIX .'footer-sidebar-1-3',
								'type'     => 'select',
								'required' => array(
													array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
													array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',5),
											  ),
								'title'    => esc_html__('Column 1-3 Sidebar', 'hotel-xenia'), 
								'data'	   => 'sidebars',
								'default'  => 'sidebar-footer-1-3'
							),
							array(
								'id'      => METAOPTION_PREFIX .'footer-sidebar-1-3-extraclass',
								'type'    => 'text', 
								'title'   => esc_html__('Column 1-3 Sidebar Extra Classes', 'hotel-xenia'),
								'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
								"default" => '',
								'required' => array(
													array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
													array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',5),
											  ),
							),	
						 	array(
								'id'       => METAOPTION_PREFIX .'footer-sidebar-1-4',
								'type'     => 'select',
								'required' => array(
													array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
													array( METAOPTION_PREFIX .'footer-widgets-1-layout','=',8),
											  ),
								'title'    => esc_html__('Column 1-4 Sidebar', 'hotel-xenia'), 
								'data'	   => 'sidebars',
								'default'  => 'sidebar-footer-1-4'
							),
							array(
								'id'      => METAOPTION_PREFIX .'footer-sidebar-1-4-extraclass',
								'type'    => 'text', 
								'title'   => esc_html__('Column 1-4 Sidebar Extra Classes', 'hotel-xenia'),
								'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
								"default" => '',
								'required' => array(
													array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
													array( METAOPTION_PREFIX .'footer-widgets-1-layout','=',8),
											  ),
							),	
					array(
					    'id'     => 'footer-end',
					    'type'   => 'section',
					    'indent' => false,
					),	
					array(
				       'id' => 'footer-row2-start',
				       'type' => 'section',
				       'title' => esc_html__('Footer Widgets Area // 2nd Row', 'hotel-xenia'),
				       'subtitle' => esc_html__('Options for the 2nd row of footer widgets', 'hotel-xenia'),
				       'indent' => true,
				     ),
						array(
							'id'       => METAOPTION_PREFIX .'footer-widgets-2',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Widgets', 'hotel-xenia'),
							'subtitle' => esc_html__('Display/hide footer widgets 2nd row', 'hotel-xenia'),
							"default"  => 0,
							'on'       => esc_html__('Display', 'hotel-xenia'),
							'off'      => esc_html__('Hide', 'hotel-xenia'),
						),
						array(
							'id'       => METAOPTION_PREFIX .'footer_main-container-type',
							'type'     => 'button_set', 
							'required' => array(METAOPTION_PREFIX .'footer-widgets-2','=','1'),						
							'title'    => esc_html__('Container Type', 'hotel-xenia'),
							'default'  => 'container',
							'options'  => array(
											'container'       => esc_html__( 'Default', 'hotel-xenia'),
											'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia'),
								)
							),
						array(
							'id'       => METAOPTION_PREFIX .'footer-widgets-2-layout',
							'type'     => 'image_select',
							'required' => array(METAOPTION_PREFIX .'footer-widgets-2','=','1'),						
							'title'    => esc_html__('Widget Columns Layout', 'hotel-xenia'), 
							'subtitle' => esc_html__('Click to the icon according to the desired widget columns layout. ', 'hotel-xenia'),
							'options'  => array(
									1 => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
									2 => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
									3 => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
									4 => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
									5 => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
									6 => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
									7 => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
									8 => array('alt' => '4 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_4.png'),
								),
							'default' => 1
						),
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-2-1',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',1),
										  ),
							'title'    => esc_html__('Column 2-1 Sidebar', 'hotel-xenia'), 
							'data'	   => 'sidebars',
							'default'  => 'sidebar-footer-2-1'
						),
						array(
							'id'      => METAOPTION_PREFIX .'footer-sidebar-2-1-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Column 2-1 Sidebar Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							"default" => '',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',1),
										  ),
						),	
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-2-2',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',2),
										  ),
							'title'    => esc_html__('Column 2-2 Sidebar', 'hotel-xenia'), 
							'data'	   => 'sidebars',
							'default'  => 'sidebar-footer-2-2'
						),
						array(
							'id'      => METAOPTION_PREFIX .'footer-sidebar-2-2-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Column 2-2 Sidebar Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							"default" => '',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',2),
										  ),
						),	
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-2-3',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',5),
										  ),
							'title'    => esc_html__('Column 2-3 Sidebar', 'hotel-xenia'), 
							'data'	   => 'sidebars',
							'default'  => 'sidebar-footer-2-3'
						),
						array(
							'id'      => METAOPTION_PREFIX .'footer-sidebar-2-3-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Column 2-3 Sidebar Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							"default" => '',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',5),
										  ),
						),	
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-2-4',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-2-layout','=',8),
										  ),
							'title'    => esc_html__('Column 2-4 Sidebar', 'hotel-xenia'), 
							'data'	   => 'sidebars',
							'default'  => 'sidebar-footer-2-4'
						),
						array(
							'id'      => METAOPTION_PREFIX .'footer-sidebar-2-4-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Column 2-4 Sidebar Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							"default" => '',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-2-layout','=',8),
										  ),
						),	

					array(
					    'id'     => 'footer-row2-end',
					    'type'   => 'section',
					    'indent' => false,
					),	

					)
				);
			return $sections;
	    }

	    function subsection_404( $sections ) {

			$sections[] = array(
				'title'      => esc_html__('404 Page', 'hotel-xenia'),
				'heading'      => esc_html__('404 PAGE OPTIONS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       =>THEMEOPTION_PREFIX .'mediapanel-404-image',
						'type'     => 'media', 
						'title'    => esc_html__('Featured Image', 'hotel-xenia'),
						'url'      => true,
						'default'  =>array('url'=> PLE_THEME_ASSETS_URI .'/images/404_alt.jpg'),
						),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-title-text',
						'type'    => 'text',
						'title'   => esc_html__('Title', 'hotel-xenia'),
						'default' => esc_html__('OMG! ERROR 404', 'hotel-xenia'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-subtitle-text',
						'type'    => 'text',
						'title'   => esc_html__('Subtitle', 'hotel-xenia'),
						'default' => esc_html__('The requested page cannot be found!', 'hotel-xenia'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-contenttitle',
						'type'    => 'text',
						'title'   => esc_html__('Additional Title On Content', 'hotel-xenia'),
						'default' => esc_html__('ERROR 404 IS NOTHING TO REALLY WORRY ABOUT...', 'hotel-xenia'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-content',
						'type'    => 'textarea',
						'title'   => esc_html__('Content', 'hotel-xenia'), 
						'default' => esc_html__('You may have mis-typed the URL, please check your spelling and try again.', 'hotel-xenia'), 
						'translate' => true
					),
					array(
						'id'      => THEMEOPTION_PREFIX .'404-search',
						'type'    => 'switch', 
						'title'   => esc_html__('Display search field', 'hotel-xenia'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-search-btntext',
						'required'     => array(THEMEOPTION_PREFIX .'404-search','=',1),						
						'type'    => 'text',
						'title'   => esc_html__('Search Button Text', 'hotel-xenia'), 
						'default' => esc_html__('Search', 'hotel-xenia'), 
						'translate' => true
					),
				)
			);
			return $sections;
	    }

	    function subsection_search( $sections ) {

			$sections[] = array(
				'title'      => esc_html__('Search Page', 'hotel-xenia'),
				'heading'      => esc_html__('SEARCH PAGE OPTIONS', 'hotel-xenia'),
				'subsection' => true,
				'fields'     => array(

		            array(
						'id'      => METAOPTION_PREFIX .'search-layout',
						'title'   => esc_html__( 'Page Layout', 'hotel-xenia' ),
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
						'title'   => esc_html__('Sidebar', 'hotel-xenia'), 
					),

					array(
						'id'      => METAOPTION_PREFIX .'search-containertype',
						'type'    => 'button_set', 
						'title'   => esc_html__('Container Type', 'hotel-xenia'),
						'default' => 'container',
						'options' => array(
										'container'       => esc_html__( 'Default', 'hotel-xenia'),
										'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia'),
							)
					),

					array(
						'id'      => METAOPTION_PREFIX .'search-colorset',
						'type'    => 'button_set',
						'title'   => esc_html__( 'Content Color Set', 'hotel-xenia' ),
						'options' => Plethora_Module_Style_Ext::get_options_array( array( 'type'=> 'color_sets', 'prepend_default' => true ) ),
						'default' => '',
					),

					array(
						'id'      => THEMEOPTION_PREFIX .'search-title',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Title On Content', 'hotel-xenia'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'        => THEMEOPTION_PREFIX .'search-title-text',
						'type'      => 'text',
						'title'     => esc_html__('Title Prefix', 'hotel-xenia'),
						'desc'      => esc_html__('Will be displayed before search keyword', 'hotel-xenia'),
						'default'   => esc_html__('Search For:', 'hotel-xenia'),
						'translate' => true,
					),
					array(
						'id'      => THEMEOPTION_PREFIX .'search-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Subtitle On Content', 'hotel-xenia'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'      =>THEMEOPTION_PREFIX .'search-subtitle-text',
						'type'    => 'text',
						'title'   => esc_html__('Subtitle', 'hotel-xenia'),
						'default' => esc_html__('This is the default search subtitle', 'hotel-xenia')
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

				add_filter( 'plethora_metabox_single_'. $post_type .'_header_fields', array('Plethora_Themeoptions', 'metabox_header_fields'), 5 );
				add_filter( 'plethora_metabox_single_'. $post_type .'_footer_fields', array('Plethora_Themeoptions', 'metabox_footer_fields'), 5 );
      		}

			// Add Header / Footer metaboxes to archive post types
			$archive_post_types = Plethora_Theme::get_supported_post_types( array( 'type'=>'archives' ) );
     		foreach ( $archive_post_types as $key=>$post_type) {

				add_filter( 'plethora_metabox_archive_'. $post_type .'_header_fields', array('Plethora_Themeoptions', 'metabox_header_fields'), 5 );
				add_filter( 'plethora_metabox_archive_'. $post_type .'_footer_fields', array('Plethora_Themeoptions', 'metabox_footer_fields'), 5 );
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
			       'title' => esc_html__('General Options', 'hotel-xenia'),
			       'subtitle' => esc_html__('General header options', 'hotel-xenia'),
			       'indent' => true,
			     ),
					array(
						'id'      => METAOPTION_PREFIX .'header-container-type',
						'type'    => 'button_set', 
						'title'   => esc_html__('Container Type', 'hotel-xenia'),
						'options' => array(
										'container'       => esc_html__( 'Default', 'hotel-xenia'),
										'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia'),
							)
					),
					array(
						'id'       => METAOPTION_PREFIX .'header-layout',
						'type'     => 'image_select',
						'title'    => esc_html__('Logo & Main Navigation Layout', 'hotel-xenia'), 
						'subtitle' => esc_html__('Click to the icon according to the desired logo / Main navigation layout. ', 'hotel-xenia'),
						'options'  => array(
								''                      => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Right', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-right.png'),
								'nav_left'              => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Left', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-left.png'),
								'nav_centered'          => array('alt' => esc_html__( 'Logo: Left | Main Navigation: Centered', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-left-menu-center.png'),
								'logo_centered_in_menu' => array('alt' => esc_html__( 'Logo Centered Inside Main Navigation', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-center-in-menu.png'),
								'header_centered'       => array('alt' => esc_html__( 'Logo & Main Navigation: Centered', 'hotel-xenia' ), 'img' => PLE_CORE_ASSETS_URI.'/images/redux/header/header-logo-center-menu-center.png'),
							),
						),

					array(
						'id'      => METAOPTION_PREFIX .'header-trans',
						'type'    => 'switch', 
						'title'   => esc_html__('Transparency', 'hotel-xenia'),
						'desc'    => sprintf( esc_html__('Enable the header transparency ( IMPORTANT: make sure you have set the %1$sTheme Options > Header > General > Transparency Opacity Level%2$s option to a value less than 100% ).', 'hotel-xenia'), '<strong>', '</strong>' ), 
					),	

					array(
						'id'       => METAOPTION_PREFIX .'header-extraclass',
						'type'     => 'text', 
						'title'    => esc_html__('Extra Classes', 'hotel-xenia'),
						'desc'     => esc_html__('Style header differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
						'validate' => 'no_special_chars',
						"default"  => '',
					),	

				array(
			       'id' => 'header-logo-start',
			       'type' => 'section',
			       'title' => esc_html__('Logo Options', 'hotel-xenia'),
			       'indent' => true,
			     ),
					array(
						'id'       => METAOPTION_PREFIX .'logo',
						'type'     => 'switch', 
						'title'    => esc_html__('Display Logo', 'hotel-xenia'),
						'on'       => esc_html__('Display', 'hotel-xenia') ,
						'off'      => esc_html__('Hide', 'hotel-xenia'),
						),

				array(
			       'id' => 'header-navigation-start',
			       'type' => 'section',
			       'title' => esc_html__('Main Menu Options', 'hotel-xenia'),
			       'indent' => true,
			     ),
					array(
						'id'       => METAOPTION_PREFIX .'navigation-main',
						'type'     => 'switch', 
						'title'    => esc_html__('Display Main Menu', 'hotel-xenia'),
						'on'       => esc_html__('Display', 'hotel-xenia') ,
						'off'      => esc_html__('Hide', 'hotel-xenia'),
						),

					array(
						'id'      => METAOPTION_PREFIX .'navigation-main-location',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'    => 'select',
						'title'   => esc_html__('Main Menu Location', 'hotel-xenia'), 
						'desc'    => esc_html__('Select the default location to be displayed as your main menu. You have the possibility to change the main navigation location for every page. ', 'hotel-xenia'),
						'data'    => 'menu_locations',
					),

				array(
			       'id' => 'header-sticky-start',
			       'type' => 'section',
			       'title' => esc_html__('Sticky Header Options', 'hotel-xenia'),
			       'indent' => true,
			     ),
					array(
						'id'      => METAOPTION_PREFIX .'header-sticky',
						'type'    => 'switch', 
						'title'   => esc_html__('Sticky Header On Scroll', 'hotel-xenia'),
					),	
					array( 
						'id'          => METAOPTION_PREFIX .'header-sticky-behavior',
						'required'    => array( METAOPTION_PREFIX .'header-sticky','=',1),						
						'type'        => 'select', 
						'title'       => esc_html__('Sticky Header Behavior', 'hotel-xenia'),
						'description' => esc_html__('Choose the behavior of sticky header', 'hotel-xenia') ,
						'options'     => array(
											'top'             => esc_html__('On top, always visible', 'hotel-xenia'),
											'top_onscroll'    => esc_html__('On top, visible only after scroll starts', 'hotel-xenia'),
											'bottom'          => esc_html__('On bottom, always visible', 'hotel-xenia'),
											'bottom_onscroll' => esc_html__('On bottom, visible only after scroll starts', 'hotel-xenia'),
										),
						),
					array(
						'id'      => METAOPTION_PREFIX .'header-sticky-custom',
						'required' => array( array( METAOPTION_PREFIX .'header-sticky','=',1 ) ),						
						'type'    => 'switch', 
						'title'   => esc_html__('Custom Sticky Header', 'hotel-xenia'),
						'description' => esc_html__('If set to on, all the custom sticky header configuration will be applied ( more under: "Theme Options > Header > Sticky Header > Custom Sticky Header Options" )', 'hotel-xenia') ,
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
			       'id' => 'footer-general-start',
			       'type' => 'section',
			       'title' => esc_html__('General Options', 'hotel-xenia'),
			       'subtitle' => esc_html__('General footer options', 'hotel-xenia'),
			       'indent' => true,
			     ),
					array(
						'id'      => METAOPTION_PREFIX .'footer-extraclass',
						'type'    => 'text', 
						'title'   => esc_html__('Extra Classes', 'hotel-xenia'),
						'desc'    => esc_html__('Style footer differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
						'validate' => 'no_special_chars',
						"default" => '',
					),	

					array(
						'id'       => 'footer-start',
						'type'     => 'section',
						'title'    => esc_html__('Footer Widgets // 1st Row', 'hotel-xenia'),
						'subtitle' => esc_html__('Options for the 1st row of footer widgets', 'hotel-xenia'),
						'indent'   => true,
				     ),
					array(
						'id'       => METAOPTION_PREFIX .'footer-widgets-1',
						'type'     => 'switch', 
						'title'    => esc_html__('Display Widgets', 'hotel-xenia'),
						'subtitle' => esc_html__('Display/hide footer widgets 1st row', 'hotel-xenia'),
						'on'       => esc_html__('Display', 'hotel-xenia'),
						'off'      => esc_html__('Hide', 'hotel-xenia'),
						),
					array(
						'id'       => METAOPTION_PREFIX .'footer_top-container-type',
						'type'     => 'button_set', 
						'required' => array(METAOPTION_PREFIX .'footer-widgets-1','=','1'),						
						'title'    => esc_html__('Container Type', 'hotel-xenia'),
						'options'  => array(
										'container'       => esc_html__( 'Default', 'hotel-xenia'),
										'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia'),
							)
						),

					array(
						'id'       => METAOPTION_PREFIX .'footer-widgets-1-layout',
						'type'     => 'image_select',
						'required'     => array(METAOPTION_PREFIX .'footer-widgets-1','=','1'),						
						'title'    => esc_html__('Widget Columns Layout', 'hotel-xenia'), 
						'subtitle' => esc_html__('Click to the icon according to the desired widget columns layout. ', 'hotel-xenia'),
						'options'  => array(
								1 => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
								2 => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
								3 => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
								4 => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
								5 => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
								6 => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
								7 => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
								8 => array('alt' => '4 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_4.png'),
							),
						),
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-1-1',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',1),
										  ),
							'title'    => esc_html__('Column 1-1 Sidebar', 'hotel-xenia'), 
							'data'	   => 'sidebars',
						),
						array(
							'id'      => METAOPTION_PREFIX .'footer-sidebar-1-1-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Column 1-1 Sidebar Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',1),
										  ),
						),	
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-1-2',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',2),
										  ),
							'title'    => esc_html__('Column 1-2 Sidebar', 'hotel-xenia'), 
							'data'	   => 'sidebars',
						),
						array(
							'id'      => METAOPTION_PREFIX .'footer-sidebar-1-2-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Column 1-2 Sidebar Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',2),
										  ),
						),	
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-1-3',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',5),
										  ),
							'title'    => esc_html__('Column 1-3 Sidebar', 'hotel-xenia'), 
							'data'	   => 'sidebars',
						),
						array(
							'id'      => METAOPTION_PREFIX .'footer-sidebar-1-3-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Column 1-3 Sidebar Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',5),
										  ),
						),	
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-1-4',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-1-layout','=',8),
										  ),
							'title'    => esc_html__('Column 1-4 Sidebar', 'hotel-xenia'), 
							'data'	   => 'sidebars',
						),
						array(
							'id'      => METAOPTION_PREFIX .'footer-sidebar-1-4-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Column 1-4 Sidebar Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
												array( METAOPTION_PREFIX .'footer-widgets-1-layout','=',8),
										  ),
						),	
				array(
			       'id' => 'footer-row2-start',
			       'type' => 'section',
			       'title' => esc_html__('Footer Widgets // 2nd Row', 'hotel-xenia'),
			       'subtitle' => esc_html__('Options for the 2nd row of footer widgets', 'hotel-xenia'),
			       'indent' => true,
			     ),
					array(
						'id'       => METAOPTION_PREFIX .'footer-widgets-2',
						'type'     => 'switch', 
						'title'    => esc_html__('Display Widgets', 'hotel-xenia'),
						'subtitle' => esc_html__('Display/hide footer widgets 2nd row', 'hotel-xenia'),
						'on'       => esc_html__('Display', 'hotel-xenia'),
						'off'      => esc_html__('Hide', 'hotel-xenia'),
					),
					array(
						'id'       => METAOPTION_PREFIX .'footer_main-container-type',
						'type'     => 'button_set', 
						'required' => array(METAOPTION_PREFIX .'footer-widgets-2','=','1'),						
						'title'    => esc_html__('Container Type', 'hotel-xenia'),
						'options'  => array(
										'container'       => esc_html__( 'Default', 'hotel-xenia'),
										'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia'),
							)
						),
					array(
						'id'       => METAOPTION_PREFIX .'footer-widgets-2-layout',
						'type'     => 'image_select',
						'required'     => array(METAOPTION_PREFIX .'footer-widgets-2','=','1'),						
						'title'    => esc_html__('Widget Columns Layout', 'hotel-xenia'), 
						'subtitle' => esc_html__('Click to the icon according to the desired widget columns layout. ', 'hotel-xenia'),
						'options'  => array(
								1 => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
								2 => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
								3 => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
								4 => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
								5 => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
								6 => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
								7 => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
								8 => array('alt' => '4 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_4.png'),
							),
					),
				 	array(
						'id'       => METAOPTION_PREFIX .'footer-sidebar-2-1',
						'type'     => 'select',
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',1),
									  ),
						'title'    => esc_html__('Column 2-1 Sidebar', 'hotel-xenia'), 
						'data'	   => 'sidebars',
					),
					array(
						'id'      => METAOPTION_PREFIX .'footer-sidebar-2-1-extraclass',
						'type'    => 'text', 
						'title'   => esc_html__('Column 2-1 Sidebar Extra Classes', 'hotel-xenia'),
						'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',1),
									  ),
					),	
				 	array(
						'id'       => METAOPTION_PREFIX .'footer-sidebar-2-2',
						'type'     => 'select',
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',2),
									  ),
						'title'    => esc_html__('Column 2-2 Sidebar', 'hotel-xenia'), 
						'data'	   => 'sidebars',
					),
					array(
						'id'      => METAOPTION_PREFIX .'footer-sidebar-2-2-extraclass',
						'type'    => 'text', 
						'title'   => esc_html__('Column 2-2 Sidebar Extra Classes', 'hotel-xenia'),
						'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',2),
									  ),
					),	
				 	array(
						'id'       => METAOPTION_PREFIX .'footer-sidebar-2-3',
						'type'     => 'select',
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',5),
									  ),
						'title'    => esc_html__('Column 2-3 Sidebar', 'hotel-xenia'), 
						'data'	   => 'sidebars',
					),
					array(
						'id'      => METAOPTION_PREFIX .'footer-sidebar-2-3-extraclass',
						'type'    => 'text', 
						'title'   => esc_html__('Column 2-3 Sidebar Extra Classes', 'hotel-xenia'),
						'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',5),
									  ),
					),	
				 	array(
						'id'       => METAOPTION_PREFIX .'footer-sidebar-2-4',
						'type'     => 'select',
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-2-layout','=',8),
									  ),
						'title'    => esc_html__('Column 2-4 Sidebar', 'hotel-xenia'), 
						'data'	   => 'sidebars',
					),
					array(
						'id'      => METAOPTION_PREFIX .'footer-sidebar-2-4-extraclass',
						'type'    => 'text', 
						'title'   => esc_html__('Column 2-4 Sidebar Extra Classes', 'hotel-xenia'),
						'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
						'required' => array(
											array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
											array( METAOPTION_PREFIX .'footer-widgets-2-layout','=',8),
									  ),
					),	
		    );
			return apply_filters( 'plethora_metabox_footer_fields_edit', $fields );
		}

//// METABOXES CONFIGURATION ENDS

//// LESS CONFIGURATION BEGINS
		public static function less_variables( $vars ) { 

		// THEME OPTIONS > GENERAL > BASIC COLORS & COLOR SET OPTIONS

			// Basic Colors ( ok )
			$vars['wp-brand-primary']    = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-brand-primary', '#6FB586', 0, false);
			$vars['wp-brand-secondary']  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-brand-secondary', '#c5bc58', 0, false);
			$vars['wp-body-bg']          = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-body-bg', '#f4f4f4', 0, false);
			$vars['wp-text-color']       = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-text-color', '#323232', 0, false);
			$link                        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-link-color', array( 'regular'=>'#6FB586', 'hover'=>'#498F60' ), 0, false);
			$vars['wp-link-color']       = $link['regular'];
			$vars['wp-link-hover-color'] = $link['hover'];
			
			// Color Sets > Primary ( ok )
			$vars['wp-primary-section-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-primary-section-txtcolor', '#fdfdfd', 0, false);
			$link                                       = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-primary-section-linkcolor', array('regular'=>'#fdfdfd', 'hover'=>'#ffffff'), 0, false);
			$vars['wp-primary-section-linkcolor']       = $link['regular'];
			$vars['wp-primary-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > Secondary ( ok )
			$vars['wp-secondary-section-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-secondary-section-txtcolor', '#ffffff', 0, false);
			$link                                         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-secondary-section-linkcolor', array('regular'=>'#fdfdfd', 'hover'=>'#ffffff'), 0, false);
			$vars['wp-secondary-section-linkcolor']       = $link['regular'];
			$vars['wp-secondary-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > Light ( ok )
			$vars['wp-light-section-bgcolor']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-light-section-bgcolor', '#efefef', 0, false);
			$vars['wp-light-section-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-light-section-txtcolor', '#323232', 0, false);
			$link                                     = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-light-section-linkcolor', array('regular'=>'#6FB586', 'hover'=>'#498F60'), 0, false);
			$vars['wp-light-section-linkcolor']       = $link['regular'];
			$vars['wp-light-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > Dark ( ok )
			$vars['wp-dark-section-bgcolor']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-dark-section-bgcolor', '#274c33', 0, false);
			$vars['wp-dark-section-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-dark-section-txtcolor', '#ffffff', 0, false);
			$link                                    = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-dark-section-linkcolor', array('regular'=>'#fdfdfd', 'hover'=>'#ffffff'), 0, false);
			$vars['wp-dark-section-linkcolor']       = $link['regular'];
			$vars['wp-dark-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > White ( ok )
			$vars['wp-white-section-bgcolor']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-white-section-bgcolor', '#ffffff', 0, false);
			$vars['wp-white-section-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-white-section-txtcolor', '#323232', 0, false);
			$link                                     = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-white-section-linkcolor', array('regular'=>'#6FB586', 'hover'=>'#498F60'), 0, false);
			$vars['wp-white-section-linkcolor']       = $link['regular'];
			$vars['wp-white-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > Black ( ok )
			$vars['wp-black-section-bgcolor']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-black-section-bgcolor', '#000000', 0, false);
			$vars['wp-black-section-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-black-section-txtcolor', '#ffffff', 0, false);
			$link                                     = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-black-section-linkcolor', array('regular'=>'#6FB586', 'hover'=>'#498F60'), 0, false);
			$vars['wp-black-section-linkcolor']       = $link['regular'];
			$vars['wp-black-section-linkcolor-hover'] = $link['hover'];

		// THEME OPTIONS > GENERAL > TYPOGRAPHY

			// Typography ( ok )
			$font_serif                            = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-font-family-sans-serif', array('font-family'=>'Source Sans Pro'), 0, false);
			$font_alt                              = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-font-family-alternative', array('font-family'=>'Playfair Display'), 0, false);
			$font_size_base                        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-font-size-base', array('font-size'=>'18px'), 0, false);
			$font_size_base_alt                    = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-font-size-alternative-base', array('font-size'=>'16px'), 0, false);
			$body_font_weight                      = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-body-font-weight', 'normal', 0, false);
			$heading_trans                         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-headings-text-transform', array('text-transform'=>'uppercase'), 0, false);
			$heading_weight                        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-headings-font-weight', '700', 0, false);
			$button_text_trans                     = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-btn-text-transform', array('text-transform'=>'uppercase'), 0, false);
			$vars['wp-font-family-sans-serif']     = $font_serif['font-family'];
			$vars['wp-font-family-alternative']    = $font_alt['font-family'];
			$vars['wp-font-size-base']             = $font_size_base['font-size'];
			$vars['wp-font-size-alternative-base'] = $font_size_base_alt['font-size'];
			$vars['wp-font-weight']                = $body_font_weight;
			$vars['wp-headings-text-transform']    = $heading_trans['text-transform'];
			$vars['wp-headings-font-weight']       = $heading_weight;
			$vars['wp-btn-text-transform']         = $button_text_trans['text-transform'];

		// THEME OPTIONS > GENERAL > MISC

			$vars['wp-container-fluid-max-width']       = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-container-fluid-max-width', 'auto', 0, false) == 'custom' ? Plethora_Theme::option(THEMEOPTION_PREFIX .'less-container-fluid-max-width-custom', '1600', 0, false) .'px' : 'auto';
			$vars['wp-section-background-transparency'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-section-background-transparency', 50, 0, false);
			$vars['wp-loader-bgcolor']                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-page-loader-bgcolor', '#000000', 0, false);

		// THEME OPTIONS > MEDIA PANEL

			$hgroup_padding                                 = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding', array( 'padding-top'=>'166', 'padding-bottom'=>'170'), 0, false );
			$hgroup_padding_sm                              = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding-sm', array( 'padding-top'=>'150', 'padding-bottom'=>'60'), 0, false );
			$hgroup_padding_xs                              = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding-xs', array( 'padding-top'=>'120', 'padding-bottom'=>'40'), 0, false );
			$hgroup_font                                    = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font', array( 'font-size' => '86px' ), 0, false );
			$hgroup_font_sm                                 = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font-sm', array( 'font-size' => '60px' ), 0, false );
			$hgroup_font_xs                                 = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font-xs', array( 'font-size' => '36px' ), 0, false );
			$vars['wp-head-panel-hgroup-padding-top']       = !empty( $hgroup_padding['padding-top'] ) ? $hgroup_padding['padding-top'].'px' : '120px';
			$vars['wp-head-panel-hgroup-padding-bottom']    = !empty( $hgroup_padding['padding-bottom'] ) ? $hgroup_padding['padding-bottom'].'px' : '120px';
			$vars['wp-head-panel-hgroup-padding-top-sm']    = !empty( $hgroup_padding_sm['padding-top'] ) ? $hgroup_padding_sm['padding-top'].'px' : '100px';
			$vars['wp-head-panel-hgroup-padding-bottom-sm'] = !empty( $hgroup_padding_sm['padding-bottom'] ) ? $hgroup_padding_sm['padding-bottom'].'px' : '100px';
			$vars['wp-head-panel-hgroup-padding-top-xs']    = !empty( $hgroup_padding_xs['padding-top'] ) ? $hgroup_padding_xs['padding-top'].'px' : '80px';
			$vars['wp-head-panel-hgroup-padding-bottom-xs'] = !empty( $hgroup_padding_xs['padding-bottom'] ) ? $hgroup_padding_xs['padding-bottom'].'px' : '80px';
			$vars['wp-head-panel-title-font-size']          = !empty( $hgroup_font['font-size'] ) ? $hgroup_font['font-size'] : '110px';
			$vars['wp-head-panel-title-font-size-sm']       = !empty( $hgroup_font_sm['font-size'] ) ? $hgroup_font_sm['font-size'] : '80px';
			$vars['wp-head-panel-title-font-size-xs']       = !empty( $hgroup_font_xs['font-size'] ) ? $hgroup_font_xs['font-size'] : '50px';
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

			// Logo ( ok )
			$logo_vert_margin                   = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-vertical-margin', array('height'=>'24'), 0, false);
			$logo_vert_margin_sm                = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-vertical-margin-sm', array('height'=>'20'), 0, false);
			$logo_vert_margin_xs                = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-vertical-margin-xs', array('height'=>'16'), 0, false);
			$logo_img_max_height                = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-img-max-height', array('height'=>'50'), 0, false);
			$logo_img_max_height_sm             = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-img-max-height-sm', array('height'=>'44'), 0, false);
			$logo_img_max_height_xs             = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-img-max-height-xs', array('height'=>'38'), 0, false);
			$logo_font_size                     = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-font-size', array('font-size'=>'26px'), 0, false);
			$logo_font_size_sm                  = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-font-size-sm', array('font-size'=>'24px'), 0, false);
			$logo_font_size_xs                  = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-font-size-xs', array('font-size'=>'22px'), 0, false);
			$vars['wp-logo-vertical-margin']    = $logo_vert_margin['height'] . 'px';
			$vars['wp-logo-vertical-margin-sm'] = $logo_vert_margin_sm['height'] . 'px';
			$vars['wp-logo-vertical-margin-xs'] = $logo_vert_margin_xs['height'] . 'px';
			$vars['wp-logo-img-max-height']     = $logo_img_max_height['height'] . 'px';
			$vars['wp-logo-img-max-height-sm']  = $logo_img_max_height_sm['height'] . 'px';
			$vars['wp-logo-img-max-height-xs']  = $logo_img_max_height_xs['height'] . 'px';
			$vars['wp-logo-font-size']          = $logo_font_size['font-size'];
			$vars['wp-logo-font-size-sm']       = $logo_font_size_sm['font-size'];
			$vars['wp-logo-font-size-xs']       = $logo_font_size_xs['font-size'];

			// Main Navigation ( ok )
			$menu_font                                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-font', array( 'font-size' => '14px', 'text-transform' => 'uppercase' ), 0, false);
			$menu_font_weight                           = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-font-weight', '500', 0, false);
			$menu_padd                                  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-item-padding', array( 'width'=>'24', 'height'=>'12' ), 0, false);
			$menu_padd_md                               = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-item-padding-md', array( 'width'=>'10', 'height'=>'10' ), 0, false);
			$menu_padd_sm                               = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-item-padding-sm', array( 'width'=>'10', 'height'=>'10' ), 0, false);
			$menu_widg_area_width                       = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-secondary-widgetized-area-width', array( 'width'=>'320' ), 0, false);
			$vars['wp-menu-font-size']                  = $menu_font['font-size'];
			$vars['wp-menu-text-transform']             = $menu_font['text-transform'];
			$vars['wp-menu-font-weight']                = $menu_font_weight;
			$vars['wp-menu-item-vertical-padding']      = $menu_padd['height'] . 'px';
			$vars['wp-menu-item-vertical-padding-md']   = $menu_padd_md['height'] . 'px';
			$vars['wp-menu-item-vertical-padding-sm']   = $menu_padd_sm['height'] . 'px';
			$vars['wp-menu-item-horizontal-padding']    = $menu_padd['width'] . 'px';
			$vars['wp-menu-item-horizontal-padding-md'] = $menu_padd_md['width'] . 'px';
			$vars['wp-menu-item-horizontal-padding-sm'] = $menu_padd_sm['width'] . 'px';
			$vars['wp-secondary-widgetized-area-width'] = $menu_widg_area_width['width'] . 'px';

			// Sticky Header ( custom sticky header color set )
			$vars['wp-stickyheader-opacity']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-sticky-custom-trans-opacity', 100, 0, false );
			$vars['wp-stickyheader-bgcolor']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-sticky-custom-bgcolor', '#000000', 0, false );
			$vars['wp-stickyheader-txtcolor']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-sticky-custom-txtcolor', '#ffffff', 0, false );
			$link_color                              = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-sticky-custom-linkcolor', array('regular'=>'#ffffff', 'hover'=>'#ffffff'), 0, false );
			$vars['wp-stickyheader-linkcolor']       = $link_color['regular'];
			$vars['wp-stickyheader-linkcolor-hover'] = $link_color['hover'];

			// Mobile Menu Navigation 
			$menu_switch_to_mobile            = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-switch-to-mobile', '991', 0, false);
			$vars['wp-menu-switch-to-mobile'] = $menu_switch_to_mobile . 'px';

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
	}
endif;