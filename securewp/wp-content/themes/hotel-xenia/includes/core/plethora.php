<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

File Description: Contains Plethora abstract class methods. 
This class is extended by Plethora_Theme class...do not call directly!

*/
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


/**
 * This class is the core theme class for every Plethora theme.
 */

abstract class Plethora {

	public static $framework_slug    = 'plethora';      // FRAMEWORK SLUG
	public static $framework_abbr    = 'ple';           // FRAMEWORK PREFIX
	public static $framework_name    = 'Plethora';      // FRAMEWORK DISPLAY TITLE
	public static $framework_version = '1.0.0';         // FRAMEWORK VERSION
	public $theme_slug;                                 // THEME SLUG ( MUST BE DEFINED ON PLETHORA_THEME )
	public $theme_name;                                 // THEME DISPLAY NAME ( MUST BE DEFINED ON PLETHORA_THEME )
	public $theme_version;                              // THEME VERSION  ( MUST BE DEFINED ON PLETHORA_THEME )
	private $index;                                     // FEATURES INDEX
	public $container_parts;                            // holds the template parts configuration

	/**
	 * It initiates all necessary framework methods for loading and initiating the theme. 
	 * NOTICE: Always invoked from Plethora_Theme class
	 *
	 * @since 1.0
	 */
	public function load_framework() {

		# GENERAL CONSTANTS
		define( 'THEME_SLUG',             $this->theme_slug);                               // Theme slug 
		define( 'THEME_VERSION',          $this->theme_version );                               // Theme version 
		define( 'THEME_TXTDOMAIN',        $this->theme_slug );                              // Theme textdomain 
		define( 'THEME_OPTVAR',           self::$framework_slug .'_options' );              // Theme options variable 
		define( 'THEME_OPTIONSPAGE',      self::$framework_slug .'_options' );              // Theme options page slug
		define( 'THEME_OPTIONSPAGETITLE', $this->theme_name . __(' Theme Options Panel', 'plethora-framework') );   // Theme options page title, displayed on browser title
		define( 'THEME_OPTIONSPAGEMENU',  __('Theme Options', 'plethora-framework') );                              // Theme options page menu title
		define( 'THEME_DISPLAYNAME',      $this->theme_name );                              // Theme display name
		define( 'THEME_DOCURL',           'http://doc.plethorathemes.com/'. $this->theme_slug .'/' );    // Theme documentation URL
		define( 'THEME_AUTHOR',        	  'Plethora Themes' );                              // Author Name 
		define( 'THEME_AUTHORURL',		  'http://plethorathemes.com' );					// Author URL

		# OPTION NAMES
		define( 'OPTNAME_FEATURES_INDEX', self::$framework_slug .'_features_index' );              // Plethora features index option 
		define( 'OPTNAME_CORE_VER',       self::$framework_slug .'_framework_ver_installed' );     // Framework installed theme version
		define( 'OPTNAME_THEME_VER',      self::$framework_slug .'_theme_ver_installed' );     // Theme installed theme version

		# FILE & CLASS PREFIXES
		define( 'CC_PREFIX',              ucfirst(self::$framework_slug ) .'_'); // Controller/Feature Classes Prefix
		define( 'CF_PREFIX',              self::$framework_slug .'-' );          // Controller/Feature Classes Filename Prefix

		# OPTION PREFIXES
		define( 'GENERALOPTION_PREFIX',   self::$framework_slug .'_' );          // Prefix used for all helper options
		define( 'THEMEOPTION_PREFIX',     self::$framework_abbr .'-' );          // Prefix used for all theme options
		define( 'METAOPTION_PREFIX',      self::$framework_abbr .'-' );          // Prefix used for all meta options
		define( 'TERMSMETA_PREFIX',       self::$framework_abbr .'-' );          // Prefix used for all term meta options
		define( 'USEROPTION_PREFIX',      self::$framework_abbr .'-' );          // Prefix used for all user options
		define( 'SHORTCODES_PREFIX',      self::$framework_slug .'_' );          // Prefix used for dynamic shortcode slugs ( e.g. shortcodes )
		define( 'WIDGETS_PREFIX',         self::$framework_slug .'-' );          // Prefix used for dynamic widgets slugs
		define( 'ASSETS_PREFIX',          self::$framework_slug );               // Prefix used for dynamic widgets slugs

		# CORE URIs
		define( 'PLE_CORE_URI',             PLE_THEME_INCLUDES_URI . '/core' );   // CORE folder
		define( 'PLE_CORE_ASSETS_URI',      PLE_CORE_URI . '/assets' );           // Framework assets folder (scripts, styles & images)
		define( 'PLE_CORE_HELPERS_URI',     PLE_CORE_URI . '/helpers' );          // Framework helpers folder
		define( 'PLE_CORE_CONTROLLERS_URI', PLE_CORE_URI . '/controllers' );      // Framework controllers folder
		define( 'PLE_CORE_FEATURES_URI',    PLE_CORE_URI . '/features' );         // Framework features folder
		define( 'PLE_CORE_LIBS_URI',        PLE_CORE_URI . '/libs' );             // Framework library folder
		define( 'PLE_CORE_JS_URI',          PLE_CORE_ASSETS_URI . '/js' );        // Framework JavaScript folder

		# CORE DIRs
		define( 'PLE_CORE_DIR',             PLE_THEME_INCLUDES_DIR . '/core'  );          // Framework folder
		define( 'PLE_CORE_ASSETS_DIR',      PLE_CORE_DIR . '/assets' );            // Framework assets folder (scripts, styles & images)
		define( 'PLE_CORE_HELPERS_DIR',     PLE_CORE_DIR . '/helpers' );           // Framework library folder
		define( 'PLE_CORE_CONTROLLERS_DIR', PLE_CORE_DIR . '/controllers' );       // Framework controllers folder
		define( 'PLE_CORE_FEATURES_DIR',    PLE_CORE_DIR . '/features' );          // Framework features folder
		define( 'PLE_CORE_LIBS_DIR',        PLE_CORE_DIR . '/libs' );              // Framework library folder
		define( 'PLE_CORE_JS_DIR',          PLE_CORE_ASSETS_DIR . '/js' );        // Framework JavaScript folder
		define( 'PLE_CORE_JS_LIBS_DIR',     PLE_CORE_JS_DIR . '/libs' );          // Framework JavaScript Libraries folder

		# FEATURES LIBRARY PLUGIN URIs
		if ( Plethora_Theme::is_library_active() ) { // this makes it easier to track issues!

			# FEATURES LIBRARY PLUGIN URIs
			define( 'PLE_FLIB_URI',             WP_PLUGIN_URL .'/plethora-featureslib' );   // Framework folder
			define( 'PLE_FLIB_ASSETS_URI',      PLE_FLIB_URI . '/assets' );           // Framework assets folder (scripts, styles & images)
			define( 'PLE_FLIB_FEATURES_URI',    PLE_FLIB_URI . '/features' );         // Framework features folder
			define( 'PLE_FLIB_LIBS_URI',        PLE_FLIB_URI . '/libs' );               // Framework JavaScript folder
			define( 'PLE_FLIB_JS_URI',          PLE_FLIB_ASSETS_URI . '/js' );               // Framework JavaScript folder

			# FEATURES LIBRARY PLUGIN DIRs
			define( 'PLE_FLIB_DIR',             WP_PLUGIN_DIR .'/plethora-featureslib' );   // Framework folder
			define( 'PLE_FLIB_ASSETS_DIR',      PLE_FLIB_DIR . '/assets' );           // Framework assets folder (scripts, styles & images)
			define( 'PLE_FLIB_CONTROLLERS_DIR', PLE_FLIB_DIR . '/controllers' );         // Framework features folder
			define( 'PLE_FLIB_FEATURES_DIR',    PLE_FLIB_DIR . '/features' );         // Framework features folder
			define( 'PLE_FLIB_LIBS_DIR',        PLE_FLIB_DIR . '/libs' );              // Framework library folder
			define( 'PLE_FLIB_JS_DIR',          PLE_FLIB_ASSETS_DIR . '/js' );               // Framework JavaScript folder
		}

		# CORE INCLUDES
		require_once( PLE_CORE_HELPERS_DIR .'/plethora-wp.php' );
		require_once( PLE_CORE_HELPERS_DIR .'/plethora-index.php' );
		require_once( PLE_CORE_HELPERS_DIR .'/plethora-optionsframework.php' );
		require_once( PLE_CORE_HELPERS_DIR .'/plethora-system.php' );
		require_once( PLE_CORE_HELPERS_DIR .'/plethora-fields.php' );
		require_once( PLE_CORE_HELPERS_DIR .'/plethora-fields-termsmeta.php' );
		require_once( PLE_CORE_HELPERS_DIR .'/plethora-doc.php' );

		# THEME INCLUDES
		if ( file_exists( PLE_THEME_INCLUDES_DIR . '/template.php' ) ) {
			
			// this class should be merged with Plethora_Theme ( only for HealthFlex )
			require_once( PLE_THEME_INCLUDES_DIR . '/template.php' );
		}

		# LOAD FEATURES AND GET THE INDEX INFO
		global $plethora;
		$plethora = array( 'instances' => array() , 'controllers' => array(), 'features' => array() );
		$index = new Plethora_Index();
		$index->load_features();
		$plethora['controllers'] = $index->controllers;
		$plethora['features']    = $index->get();

		# ADMIN ASSETS REGISTRATION
		add_action( 'admin_enqueue_scripts', array( $this, 'assets_admin' ));         // Enqueue admin assets

		# OPTIONS FRAMEWORK HOOK
		add_action( 'init', array( $this, 'load_options_framework' ), 5);        // Enqueue admin assets 

		# OTHER CONFIG
		// Load theme's gettext strings
		add_action('after_setup_theme', array( $this, 'textdomain'));

		// Send basic variables to theme.js
		$this->set_themeconfig( "GENERAL", array('debug' => false));
		add_action( 'wp_footer', array( $this, 'localize_themeconfig' ));  // notice...priority should be 2, after theme.js registration  

		// Display admin notices added with Plethora_Theme::add_admin_notice() method
		add_action('admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Loads Options Framework, Options & Metabox configuration
	 */
	 public function load_options_framework() {

		if ( Plethora_Theme::is_library_active() && class_exists( 'Plethora_Optionsframework' ) ) { // this makes it easier to track issues!

			# LOAD OPTIONS FRAMEWORK ( notice: this class does not invoke theme options, rather than loading only the class file and some static methods)
			new Plethora_Optionsframework;

			# INSTANTIATE THEME OPTIONS CLASS ( class file already included on Plethora_Optionsframework class )
			if ( class_exists('Plethora_Module_Themeoptions') ) { // theme options as module feature

				global $plethora_options_config;
				$plethora_options_config = new Plethora_Module_Themeoptions;

			} elseif ( class_exists('Plethora_Themeoptions') ) { // old method

				global $plethora_options_config;
				$plethora_options_config = new Plethora_Themeoptions;
			}
		}
	 }

	/**
	 * Register & Enqueue fixed admin only scripts/styles
	 */
	 public function assets_admin() {

		wp_register_style( 'plethora-admin', PLE_CORE_ASSETS_URI . '/admin.css' );
		wp_enqueue_style( 'plethora-admin' );

		wp_register_script( 'plethora-admin', PLE_CORE_ASSETS_URI . '/admin.js' );
		wp_enqueue_script('plethora-admin');
	 }

	/**
	 * Load theme's gettext strings
	 */
	public function textdomain() { 

		if ( is_child_theme() ) {

			load_child_theme_textdomain( THEME_TXTDOMAIN, get_stylesheet_directory() . '/languages' );

		} else {

			load_theme_textdomain( THEME_TXTDOMAIN, get_template_directory() . '/languages' );
		}

		if ( Plethora_Theme::is_library_active() ) { 
			
			load_plugin_textdomain('plethora-framework', false, PLE_FLIB_DIR . '/languages' );
		}
	}

	/**
	 * Pasing script variables ( in CDATA format ) to theme.js
	 * @since 1.0
	 */
	public function localize_themeconfig() {

		global $plethora_themeconfig;
		$plethora_themeconfig = is_array( $plethora_themeconfig ) ? $plethora_themeconfig : array();
		wp_localize_script( ASSETS_PREFIX .'-init', 'themeConfig', $plethora_themeconfig );
	}

	/**
	 * Assigns all template parts, according to configuration given on extension class,
	 * plus the user configuration set with Plethora_Theme add_element(), remove_element()
	 * and update_element() methods.
	 * This is always initiated on extension class
	 */
	 public function set_elements() {

		// Get Plethora's default configuration
		$container_elements_raw = $this->container_elements;

		// Get all user configuration from $plethora global
		global $plethora;
		$add_elements    = isset( $plethora['add_elements'] ) ? $plethora['add_elements'] : array();
		$remove_elements = isset( $plethora['remove_elements'] ) ? $plethora['remove_elements'] : array();
		$update_elements = isset( $plethora['update_elements'] ) ? $plethora['update_elements'] : array();

		// We have to prepare the raw configuration for the final routing
		$container_elements = array();
		foreach ( $container_elements_raw as $container_section => $elements ) {

			// Merge Plethora's default with user added elements first
			if ( !empty( $add_elements[$container_section] ) ) {
				$elements = array_merge( $elements, $add_elements[$container_section] );
			}

			foreach ( $elements as $element ) {

				// Set element key
				$key = $element['container'] .'-'. $element['handle'];
				
				// if $key is in the $remove_elements array, don't included it here
				if ( in_array( $key, $remove_elements ) ) { continue; }

				// if $key is in the $update_elements array, just merge the arguments
				if ( array_key_exists( $key, $update_elements ) ) { 

					$element = self::parse_multi_args( $update_elements[$key], $element );
				}

				// Set element priority ( if not already set on configuration )
				if ( ! isset( $priority_index ) ) { $priority_index = array(); }
				if ( ! isset( $priority_index[$element['container']] ) ) { $priority_index[$element['container']] = 0; }
				$priority_index[$element['container']]+= 10;
				if ( ! isset( $element['priority'] ) ) { $element['priority'] = $priority_index[$element['container']]; }


				// Add wrapper container open/close tags
				if ( empty( $element['options']['wrap_auto'] ) || ( ! empty( $element['options']['wrap_auto'] ) && $element['options']['wrap_auto'] !== false ) ) {

					$element['wrapper_html_open'] = self::get_html_tag_open( array(
								'tag'   => ( !empty( $element['options']['wrap_tag'] ) ) ? $element['options']['wrap_tag'] : '',
								'class' => ( !empty( $element['options']['wrap_class'] ) ) ? $element['options']['wrap_class'] : '',
								'id'    => ( !empty( $element['options']['wrap_id'] ) ) ? $element['options']['wrap_id'] : '',
								'attrs' => ( !empty( $element['options']['wrap_attrs'] ) ) ? $element['options']['wrap_attrs'] : array(),
						)
					);
					$element['wrapper_html_close'] = ( !empty( $element['options']['wrap_tag'] ) ) ? self::get_html_tag_close( $element['options']['wrap_tag'] ) : '';
				
				} else {

					$element['wrapper_html_open']  = '';
					$element['wrapper_html_close'] = '';
				}

				// And finally, add element to container after addubg developer notes ( if in dev mode )
				$container_elements[$key] = $this->set_element_dev_notes( $element );
			}
		}

		// Now we are ready for the final routing!
		$this->container_elements = $container_elements;
		foreach ( $this->container_elements as $key => $element ) {

			$this->add_container_part( $element );
		}
	}

	public function set_element_dev_notes( $element ) {

		// Add dev notes
		if ( self::is_developermode() ) {

			$element_type = ( !empty( $element['file'] ) ) ? 'Template part file' : '';
			$element_type = ( !empty( $element['function'] ) ) ? 'Class method or simple function call' : $element_type;
			$element_type = ( !empty( $element['html'] ) ) ? 'HTML' : $element_type;
			$element['dev_notes_before'] = "'". strtoupper( $element['handle'] ) ."' ELEMENT STARTS HERE \n";       
			$element['dev_notes_before'] .= "- Layout container: '". $element['container'] . "'\n";       
			$element['dev_notes_before'] .= "- Display order: ".$element['priority']."\n";       

			switch ( $element_type ) {

				case 'Template part file':
					$file        = is_array( $element['file'] ) ? THEME_SLUG . '/'. $element['file'][0] .'-'. $element['file'][1] .'.php' : THEME_SLUG . '/'. $element['file'] .'.php' ;
					$file_parent = is_array( $element['file'] ) ? THEME_SLUG . '/'. $element['file'][0] .'.php' : '' ;
					$element['dev_notes_before'] .= "- Type: ".$element_type."\n";       
					$element['dev_notes_before'] .= ( is_array( $element['file'] ) ) ? "- EDIT template part file: copy-paste the '". $file ."' OR the '". $file_parent ."' file using the same path on your child theme and edit.\n" : "- EDIT template part file: copy-paste the '". $file ."' file using the same path on your child theme and edit.\n";       
					break;
				
				case 'Class method or simple function call':
					$func_type = is_array( $element['function'] ) && isset( $element['function'][1] ) ? 'class_method' : 'function';
					$func      = is_array( $element['function'] ) && $func_type === 'class_method' ? $element['function'][0] .'::'. $element['function'][1] : $element['function'][0] ;
					$element['dev_notes_before'] .= "- Type: ".$element_type." >>> ". $func ."() \n";       
					break;
				
				case 'HTML':
					$element['dev_notes_before'] .= "- Type: ".$element_type."\n";       
					break;
			}
			
			$priority_before = $element['priority'] - 5;
			$priority_after  = $element['priority'] + 5;
			$priority_change = $element['priority'] + 15;
			$element['dev_notes_before'] .= "- UPDATE this element's display ORDER within the same container: use the method Plethora_Theme::update_element_order( '". $element['container'] ."', '". $element['handle'] ."', ".$priority_change." ); . Consider the neighbour elements order values inside the container for the correct placement\n";
			$element['dev_notes_before'] .= "- REMOVE this element: use the method Plethora_Theme::remove_element( '". $element['container'] ."', '". $element['handle'] ."' );\n";   
			$element['dev_notes_before'] .= "- ADD a template part file BEFORE this: use the method Plethora_Theme::add_element( '". $element['container'] ."', 'your_element_slug', array( 'file' => 'templates/my/template/file', 'priority' => ". $priority_before ." ) );\n";
			$element['dev_notes_before'] .= "- ADD a template part file AFTER this: use the method Plethora_Theme::add_element( '". $element['container'] ."', 'your_element_slug', array( 'file' => 'templates/my/template/file', 'priority' => ". $priority_after ." ) );\n";
			$element['dev_notes_before'] .= "- ADD html BEFORE this element: use the method Plethora_Theme::add_element( '". $element['container'] ."', 'your_element_slug', array( 'html' => '<div>Your HTML here</div>', 'priority' => ". $priority_before ." ) );\n";
			$element['dev_notes_before'] .= "- ADD html AFTER this element: use the method Plethora_Theme::add_element( '". $element['container'] ."', 'your_element_slug', array( 'html' => '<div>Your HTML here</div>', 'priority' => ". $priority_after ." ) );\n";
			if ( !empty( $element['options'] ) ) {

				$element['dev_notes_before'] .= "- UPDATE/ADD OPTION VALUES for this element: use the method Plethora_Theme::update_element_option( '". $element['container'] ."', '". $element['handle'] ."', 'option_name', 'option_value' );\n";
				$element['dev_notes_before'] .= "  Options available in the template part file:";       
				foreach ( $element['options'] as $opt_key => $opt_val ) {

					$opt_type        = is_object( $opt_val ) ? 'object of '. get_class( $opt_val ) : ( is_numeric( $opt_val ) ? 'integer/boolean' : ( is_array( $opt_val ) ? 'array' : 'string' ) );
					$opt_val         = is_object( $opt_val ) || is_array( $opt_val ) ? json_encode( $opt_val ) : $opt_val;
					$opt_val_display = is_numeric( $opt_val ) ? $opt_val : '\''. $opt_val .'\'';
					$element['dev_notes_before'] .= "\n     $". $opt_key ." ( ".$opt_type." ): ". $opt_val_display ."";       
				}
			} 

			$element['dev_notes_before']  .= "\n";       
			$element['dev_notes_after']  = "'". strtoupper( $element['handle'] ) ."' ELEMENT ENDS HERE";              

		} else {

			$element['dev_notes_before']  = '';       
			$element['dev_notes_after'] = '';       
		}

		return $element;
	}

/*
    ___ _   _ _____ _____ ____  _   _    _    _          _    ____ ___ 
   |_ _| \ | |_   _| ____|  _ \| \ | |  / \  | |        / \  |  _ \_ _|
	| ||  \| | | | |  _| | |_) |  \| | / _ \ | |       / _ \ | |_) | | 
	| || |\  | | | | |___|  _ <| |\  |/ ___ \| |___   / ___ \|  __/| | 
   |___|_| \_| |_| |_____|_| \_\_| \_/_/   \_\_____| /_/   \_\_|  |___|
*/

	/**
	 * INTERNAL | Returns supported feature controllers info
	 * @since 1.0
	 */
	public static function get_controllers() {

		global $plethora;
		$controllers = $plethora['controllers'];
		return $controllers;
	}

	/**
	 * INTERNAL | Returns supported features info
	 * @since 1.0
	 */
	public static function get_features( $args ) {

		$default_args = array( 
						'controller'   => '',    // Controller slug returns all controller features...empty returns ALL features info
						'output'   => 'all'     // 'all', 'slugs' ( returns feature slugs )
		);
		$args = wp_parse_args( $args, $default_args);          // MERGE GIVEN ARGS WITH DEFAULTS
		extract( $args );

		global $plethora;
		$controllers = self::get_controllers();
		$all_features = apply_filters( 'plethora_features', $plethora['features'] );
		$features = array();

		foreach ( $all_features as $key => $feature ) {

			if ( empty( $controller ) || ( !empty( $controller ) && array_key_exists( $controller, $controllers ) && $feature['controller'] === $controller ) ) {

					if ( $feature['verified'] ) {

						$features[$feature['slug']] = $output === 'slugs' ? $feature['slug'] : $feature;
					}
			}
		}

		return $features;
	}

	/**
	 * INTERNAL | Returns supported features info
	 * 'attr' argument can take any the following values:
	 *    'assets'                        // returns JS / CSS asset registrations
	 *    'base_class'                    // returns base class name
	 *    'base_path'                     // returns base class file path
	 *    'class'                         // returns class name
	 *    'controller'                    // returns the feature's controller
	 *    'dynamic_construct'             // returns TRUE if this feature's class is instanciated dynamically
	 *    'dynamic_method'                // returns additional dynamic method name
	 *    'feature_title'                 // returns feature's title
	 *    'feature_description'           // returns feature's description
	 *    'folder'                        // returns feature's folder path
	 *    'plethora_supported'            // returns true if feature is Plethora, false if a custom one
	 *    'path'                          // returns class file path
	 *    'slug'                          // returns the slug
	 *    'theme_option_control'          // returns TRUE if this is an option controlled feature
	 *    'theme_option_control_config'   // returns theme options activation/deactivation field configuration
	 *    'theme_option_default'          // returns initial activation status for option controlled features ( TRUE / FALSE )
	 *    'theme_option_requires'         // returns array with other required features
	 *    'theme_option_status'           // returns feature's activation status ( TRUE / FALSE )
	 *    'verified'                      // returns 'true' if feature is working properly, or 'false' if not loaded for some reason
	 *    'wp_slug'                       // returns wp_slug ( this is for shortcodes / widgets only )
	 *   
	 */
	public static function get_feature( $args ) {

		$default_args = array( 
			'controller' => false,   // Controller slug filter ( MANDATORY )
			'feature'    => false,   // Feature slug filter ( MANDATORY )
			'attr'       => '',      // will return ONLY the specified feature's attribute value ( check method description above )
		);
		$args = wp_parse_args( $args, $default_args);          // MERGE GIVEN ARGS WITH DEFAULTS
		extract( $args );

		if ( $controller && $feature ) { 

			global $plethora;
			$features = apply_filters( 'plethora_features', $plethora['features'] );

			if ( isset( $features[$controller .'-'. $feature ] ) && !empty( $attr ) && isset( $features[$controller .'-'. $feature ][$attr] ) ) {

				return $features[$controller .'-'. $feature ][$attr];
			
			} elseif ( isset( $features[$controller .'-'. $feature ] ) && empty( $attr ) ) {

				return $features[$controller .'-'. $feature ];
			}
		}

		return false;
	}

	/**
	 * INTERNAL | Returns feature class instance
	 * @return feature class object | empty object
	 */
	public static function get_feature_instance( $controller, $feature ) {

		if ( ! empty( $controller ) && ! empty( $feature ) ) {
			// prepare class extension name
			$class_name = 'Plethora_'. ucfirst( $controller ) .'_'. ucfirst( $feature ) .'_Ext';

			global $plethora;
			if ( is_object( $plethora['instances'][$class_name] ) ) {

				return $plethora['instances'][$class_name];
			}
		}

		return new stdClass(); // return empty object
	}

	/**
	 * INTERNAL | Handles script variables for theme.js
	 */
	public static function themeconfig( $var_group, $vars ) { 

		global $plethora_themeconfig;
		if ( empty( $plethora_themeconfig ) ) { $plethora_themeconfig = array(); }
		
		if ( isset( $plethora_themeconfig[$var_group] ) ) { // merge if var group exists

			$vars = array_merge_recursive( $plethora_themeconfig[$var_group], $vars );
			$plethora_themeconfig[$var_group] = $vars;

		} else {

			$plethora_themeconfig[$var_group] = $vars; // add vars to new var group
		}

		ksort( $plethora_themeconfig );
	}

	/**
	* INTERNAL
	* Return the comment text for the special 'page_info' comment group
	*/
	public static function get_dev_comment_page_info() {

		$theme                    = wp_get_theme( THEME_SLUG );
		$info['wp_get_theme']     = array( 'cond' => true, 'desc' => $theme->get( 'Name' ) .' '. $theme->get( 'Version' ) );
		$info['is_child_theme()=== false'] = array( 'cond' => is_child_theme() ? false : true, 'desc' => esc_html__( 'This is a parent theme', 'plethora-framework' ) );
		$info['is_child_theme()'] = array( 'cond' => is_child_theme(), 'desc' => esc_html__( 'This is a child theme', 'plethora-framework' ) );

		// Static front page and blog setup
		if ( is_front_page() && is_home() ) {
			
			$info['is_front_page() && is_home()'] = array( 'cond' => true, 'desc' => esc_html__( 'This is the default home page which displays the blog ( set automatically by WP )', 'plethora-framework' ) );

		} elseif ( is_front_page() ) {
			
			$info['is_front_page()'] = array( 'cond' => true, 'desc' => esc_html__( 'This is the static front page of the website, displaying editor content ( set manually by admin )', 'plethora-framework' ) );

		} elseif ( is_home() ) {

			$info['is_home()'] = array( 'cond' => true, 'desc' => esc_html__( 'This is the static blog page of the website ( set manually by admin )', 'plethora-framework' ));
		}

		// Singles
		$info['is_page()']        = array( 'cond' => is_page(), 'desc' => esc_html__( 'This is a single WP native page', 'plethora-framework' ) );
		$info['is_single()']        = array( 'cond' => is_single(), 'desc' => esc_html__( 'This is a single post ( WP native or CPT post )', 'plethora-framework' ) );
		$post_type_singles = self::get_supported_post_types();
		foreach ( $post_type_singles as $post_type_single ) {

			$info['is_singular( \''. $post_type_single .'\' )'] = array( 'cond' => is_singular( $post_type_single ), 'desc' => sprintf( esc_html__( 'This is a single %1$s post', 'plethora-framework' ), $post_type_single !== 'post' ? $post_type_single : 'WP native' ) );
		}

		// Archives
		$info['is_archive()']    = array( 'cond' => is_archive(), 'desc' => esc_html__( 'This is an archive page, ( displaying a WP native or custom posts catalog )', 'plethora-framework' ) );
		$post_type_archives = self::get_supported_post_types( array( 'type' => 'archives' ) );
		foreach ( $post_type_archives as $post_type_archive ) {

			$info['is_post_type_archive( \''. $post_type_archive .'\' )'] = array( 'cond' => is_post_type_archive( $post_type_archive ), 'desc' => sprintf( esc_html__( 'This is a %1$s archive page ( displaying the %1s posts catalog )', 'plethora-framework' ), $post_type_archive ) );
		}

		// Native and custom taxonomies
		$info['is_category()'] = array( 'cond' => is_category(), 'desc' => esc_html__( 'This is a category archive page, displaying native WP posts catalog results', 'plethora-framework' ) );
		$info['is_tag()']      = array( 'cond' => is_tag(), 'desc' => esc_html__( 'This is a tag archive page, displaying native WP posts catalog results', 'plethora-framework' ) );
		$info['is_tax()']      = array( 'cond' => is_tax(), 'desc' => esc_html__( 'This is a custom taxonomy archive page, displaying native or CPT posts catalog results', 'plethora-framework' ) );
		$info['is_author()']   = array( 'cond' => is_author(), 'desc' => esc_html__( 'This is a author archive page, displaying native WP posts catalog results', 'plethora-framework' ) );
		$info['is_date()']     = array( 'cond' => is_date(), 'desc' => esc_html__( 'This is a date archive page, displaying native WP posts catalog results', 'plethora-framework' ) );
		// Search page
		$info['is_search()']   = array( 'cond' => is_search(), 'desc' => esc_html__( 'This is the native WP search page', 'plethora-framework' ) );
		// Other
		$info['is_404()']      = array( 'cond' => is_404(), 'desc' => esc_html__( 'This is the 404 page...well done!', 'plethora-framework' ) );
		$info['is_sticky()']   = array( 'cond' => is_sticky(), 'desc' => esc_html__( 'This is a sticky post', 'plethora-framework' ) );
		return $info;
	}

	/**
	 * INTERNAL | Manages add_layout_attr filtering
	 * @since 1.0
	 */
	public static function filter_container_attr( $attrs ) {

		// get container name
		$this_filter = current_filter();
		$this_filter = str_replace('plethora_container_', '', $this_filter );
		$container   = str_replace('_atts', '', $this_filter );

		// make sure that attrs is an array
		$attrs = is_array( $attrs ) ? $attrs : array( $attrs );
		$filtered_attrs = array();

		global $plethora;
		if ( !empty( $plethora['layout'][$container] ) ) {
			
			$unfiltered_attrs = $plethora['layout'][$container];

			foreach ( $unfiltered_attrs as $attr_name => $attr_values ) {

				if ( !empty( $attr_values ) ) {

					if ( is_string( $attr_values ) ) {

						 $attr_value_key = sanitize_key( $attr_values );
						 $filtered_attrs[$attr_name][$attr_value_key] = $attr_values;
					 
					} elseif ( is_array( $attr_values ) ) {

						 $attr_values = array_unique( $attr_values );

						 foreach ( $attr_values as $attr_value_key => $attr_value ) {

							$filtered_attrs[$attr_name][$attr_value_key] = $attr_value;
						 }
					}
				}
			}
		}
		$attrs = array_merge( $filtered_attrs, $attrs );

		return $attrs;
	}

	/**
	 * INTERNAL | Returns container attributes as set with
	 * 'add_container_attr' / 'remove_container_attr' methods
	 */
	public static function get_container_attrs( $container ) {
		
		if ( !empty( $container ) ) {

			$atts         = apply_filters( 'plethora_container_'. $container .'_atts' , array() );
			$container_atts = '';
			foreach ( $atts as $att_key => $att_val ) {

				if ( is_array( $att_val ) ) {

					$container_atts .= ' '. $att_key .'="';
					foreach ( $att_val as $val ) {

						$container_atts .= esc_attr( $val ) .' ';
					}
					$container_atts = rtrim( $container_atts ) . '"';

				} else {

					$container_atts .= ' '. $att_key .'="'. esc_attr( $att_val ) .'"';
				}
			}

			return $container_atts;
		}
	}

	/**
	 * PUBLIC | Assigns template parts or Html to the container specified ( ie. class, id, style, etc. ) to core layout container tags
	 * This should be used on 'wp' hook or after.
	 */
	public static function add_container_part( $args ) {

		# Default arguments configuration
		$default_args = array(
						'container' => '',       // Container to add the output ( MUST USE )
						'status'    => true,     // This is used to add on/off conditions on method call
						'file'      => false,    // Template part file  output ( use path similar to get_template_part() ). 
						'function'  => false,    // Function call output ( Not used if 'file' has a value. will be called when in place ). 
						'html'      => false,    // HTML output ( Not used if 'file' OR 'function' has a value. Also it should be the final output )
						'priority'  => 10,       // Output display priority
						'options'   => array(),  // Options available for file output use ( not for html )
		);
		$args = wp_parse_args( $args, $default_args);  // MERGE GIVEN ARGS WITH DEFAULTS
		# Some additional checks on $args
		if ( empty( $args['container'] ) || ! $args['status'] ) { return false; } // If 'container' is empty, no point to continue 
		if ( $args['file'] !== false ) { $args['file'] = is_array( $args['file'] ) ? $args['file'] : array( $args['file'], '' ); }  // make sure that 'file' is array
		if ( $args['function'] !== false ) { $args['function'] = is_array( $args['function'] ) ? $args['function'] : array( $args['function'], '' ); } // make sure that 'function' is array
		$args['options'] = is_array( $args['options'] ) ? $args['options'] : array();   // make sure that 'options' is array
		extract( $args );
		# Add this to $plethora global
		global $plethora;
		if ( ! isset( $plethora['layout_parts'][$container] ) ) {

			$plethora['layout_parts'][$container] = array();
		}

		// Get the new template part's array key and set it as an argument ( will be used for the multisort below )
		$args['array_key_val'] = count( $plethora['layout_parts'][$container] );
		$plethora['layout_parts'][$container][] = $args;

		// Multi sort by priority first and then by array key
		$priority = array();
		$array_key = array();
		foreach ( $plethora['layout_parts'][$container] as $key => $part ) {
				$priority[$key]  = $part['priority'];
				$array_key[$key] = $part['array_key_val'];
		}
		array_multisort($priority, SORT_ASC, $array_key, SORT_ASC, $plethora['layout_parts'][$container] );

		// finally add the action
		return add_action( 'plethora_'. $container , array( 'Plethora_Theme', 'container_part_output' ), $args['priority'] );
	}

	/**
	 * INTERNAL | Manages add_layout_part filtering
	 * @since 1.0
	 */
	public static function container_part_output() {

		// get container name
		$this_filter = current_filter();
		$container = str_replace('plethora_', '', $this_filter );

		global $plethora;
		$container_parts = !empty( $plethora['layout_parts'][$container] ) ? $plethora['layout_parts'][$container] : array();
		foreach ( $container_parts as $key => $part ) {

			// allow more than one parts with same handle only if this is NOT a singular main loop
			if ( empty( $part['loaded'] ) || ( !is_singular() && !is_404() && $this_filter === 'plethora_content_main_loop' ) ) { 

				// Display developer notes opening ( check first if set, to maintain compatibility with themes before Xenia )
				if ( isset( $part['dev_notes_before'] ) ) { self::dev_comment( $part['dev_notes_before'], 'customization_info' ); }

				// Display wrapper html open tag ( if any )
				echo isset( $part['wrapper_html_open'] ) ? $part['wrapper_html_open'] : '';

				// Display the part output, depending on its type ( file, function or straight html )
				if ( is_array( $part['file'] ) ) {

					set_query_var( 'options', $part['options'] );
					Plethora_WP::get_template_part( $part['file'][0], $part['file'][1] );

				} elseif ( is_array( $part['function'] ) ) {

					// get arguments first
					$function_args = array();
					$count = 0;
					foreach ( $part['function'] as $function_arg ) {
						$count++;
						if ( $count > 2 ) {

							$function_args[] = $function_arg;
						}
					}
					// call the function/method
					if ( !empty( $part['function'][1] ) ) {

						call_user_func_array( array( $part['function'][0], $part['function'][1] ), $function_args );

					} else {

						call_user_func_array( $part['function'][0], $function_args );
					}

				} elseif ( $part['html'] ) {

						echo trim( $part['html'] );
				}

				// Display wrapper html closing tag ( if any )
				echo isset( $part['wrapper_html_close'] ) ? $part['wrapper_html_close'] : '';

				// Display developer notes opening( check first if set, to maintain compatibility with themes before Xenia )
				if ( isset( $part['dev_notes_after'] ) ) { self::dev_comment( $part['dev_notes_after'], 'customization_info' ); }

				$plethora['layout_parts'][$container][$key]['loaded'] = true;
			}
		}
	}

	/**
	 * INTERNAL | Returns the supported post type(s) that the given taxonomy is associated
	 * Notice: Will work as expected after 'init' hook 
	 */
	public static function get_post_type_by_taxonomy( $taxonomy = false ) {
		
		// Retrieve taxonomies as per given post_type
		if ( $taxonomy ) {

			$supported_posttypes = self::get_supported_post_types( array( 'type'=> 'archives' ) );

			foreach ( $supported_posttypes as $post_type ) {

				$taxonomies = get_object_taxonomies( $post_type, 'names' );
				foreach ( $taxonomies as $tax ) {

					if ( $tax === $taxonomy ) {
					$return[] = $post_type; 

					}
				}
			}

			return $return;
		}

		return '';
	}

	/**
	 * INTERNAL | Displays all admin notices declared with 'add_admin_notice' method
	 * Hooked on 'admin_notices'
	 * @since 1.0
	 */
	public static function admin_notices() {

		global $plethora;
		$admin_notices = !empty( $plethora['admin_notices'] ) ? $plethora['admin_notices'] : array();
		foreach ( $admin_notices as $handle => $args ) {

			if ( array_key_exists( THEME_SLUG, $args['theme'] ) && $args['condition']  ) {

				$notice_version_since    = $args['theme'][THEME_SLUG];
				$version_current         = self::get_version( 'current' );
				$version_first_installed = self::get_version( 'first_installed' );
				$version_since_check     = version_compare( $version_current, $notice_version_since, '>=' );
				$is_this_an_update       = version_compare( $version_current, $version_first_installed, '>' );
				$version_update_check    = $args['theme_update_only'] ? $is_this_an_update : true;
		
				if ( $version_since_check && $version_update_check  ) {

					$handle = 'plethora_admin_notice_' . sanitize_title_with_dashes( $handle );
					if ( isset( $_GET[$handle]) && sanitize_key( $_GET[$handle] ) === 'hide' ) {

							update_option( $handle, 'hide' );
					}

					$notice_status = get_option( $handle, 'show' );

					if ( $notice_status !== 'hide' ) {

							$dismiss_link = add_query_arg( $handle, 'hide', $_SERVER['REQUEST_URI']  );
							$output = !empty( $args['title'] ) ? '<h4 style="margin:0 0 10px;">'. $args['title'] .'</h4>' : '' ;
							$output .= is_array( $args['notice'] ) ? implode('<br>', $args['notice'] ) : $args['notice'];
							$output .= '<p>';
							$output .= '<a href="'. esc_url( $dismiss_link ) .'"><strong>'. $args['dismiss_text'] .'</strong></a>';
							
							foreach ( $args['links'] as $link ) {
								$href        = !empty( $link['href'] ) ? $link['href'] : '#';
								$target      = !empty( $link['target'] ) ? $link['target'] : '_blank';
								$class       = !empty( $link['class'] ) ? $link['class'] : '';
								$id          = !empty( $link['id'] ) ? $link['id'] : '';
								$anchor_text = !empty( $link['anchor_text'] ) ? $link['anchor_text'] : '';
								if ( !empty( $anchor_text ) ) {
									$output .= ' | <a href="'. esc_url( $link['href'] ) .'" class="'. esc_attr( $class ).'" id="'.esc_attr( $id ).'" target="'.esc_attr( $target ).'"><strong>'. $link['anchor_text'] .'</strong></a>';
								}
							}
							$output .= '</p>';
							echo '<div class="notice notice-'.$args['type'].' is-dismissible"><p>'. $output .'</p></div>'; 
					}
				}
			}
		}
	}
/*
	 ____  _   _ ____  _     ___ ____      _    ____ ___ 
	|  _ \| | | | __ )| |   |_ _/ ___|    / \  |  _ \_ _|
	| |_) | | | |  _ \| |    | | |       / _ \ | |_) | | 
	|  __/| |_| | |_) | |___ | | |___   / ___ \|  __/| | 
	|_|    \___/|____/|_____|___\____| /_/   \_\_|  |___|

*/

// PUBLIC REUSABLE STATIC METHODS ----> START

	/**
	 * PUBLIC | Checks if the installation is in development mode ( set by user on theme options )
	 * @since 1.0
	 */
	public static function is_developermode() {

		// Notice...comments MUST be disabled in this option call, to avoid endless loop between methods
		$development = self::option( THEMEOPTION_PREFIX . 'dev', 0, 0, 0 );
		if ( $development == 1 ) { 

			return 1;
		}
		
		return 0;
	}

	public static function get_version( $what_version = 'current' ) {
		
		$version = '1.0';
		switch ( $what_version ) {
			case 'first_installed':
				$version = get_option( 'plethora_theme_ver_installed_initial', $version );
				break;
			
			case 'current':
			default:
				$version = get_option( OPTNAME_THEME_VER, $version );
				break;		
		}

		return $version;
	}

	/**
	 * PUBLIC | Checks if the Plethora Library plugin is active
	 * @since 1.0
	 */
	public static function is_library_active() {

		if ( ! function_exists( 'is_plugin_active ') ) {

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active( 'plethora-featureslib/plethora_featureslib.php' ) ) {

			return true;
		}

		return false;
	}


	/**
	 * PUBLIC | Checks if the given feature is active
	 * @param $group ( feature group slug ), $slug ( feature slug )
	 * @since 1.0
	 */
	public static function is_feature_activated( $controller, $feature ) {
		
		// no margin for errors!
		if ( empty( $controller ) || empty( $feature ) ) { return false ; }

		$feature = self::get_feature( array( 'controller' => $controller, 'feature' => $feature ) );
		if ( ! empty( $feature ) ) {

			return isset( $feature['theme_option_status'] ) ? $feature['theme_option_status'] : false;
		}

		return false;
	}

	/**
	 * PUBLIC
	 * Will return the page id for the current view. 
	 * On blog, author, date, search and any taxonomy view, it will 
	 * return the static page id ( if this is set by the user ). 
	 * Notice: You should not treat this as an alternative of native WP get_the_id() function
	 */
	public static function get_this_page_id() {

		$page_id = 0;

		if ( is_home() || is_search() || is_category() || is_tag() || is_author() || is_date() ) {

			$page_id = get_option('page_for_posts');

		} else { 

			$page_id = get_queried_object_id();
		}    

		return apply_filters( 'plethora_this_page_id', $page_id );
	}    

	/**
	 * PUBLIC
	 * Will return the associated post type for the current view. 
	 * On blog, author, date, and search view, it will return 'post'. 
	 * On a CPT archive or taxonomy view, it will return the associating CPT slug. 
	 * Notice: You should not treat this as an alternative of native WP get_post_type() function. 
	 */
	public static function get_this_view_post_type() {

		$object = get_queried_object();
		$post_type = !empty( $object->post_type ) ? $object->post_type : get_post_type();
 

		if ( is_home() || is_search() || is_category() || is_tag() || is_author() || is_date() ) {

			$post_type = 'post';
		
		} elseif ( is_tax() ) {

			// find which taxonomy using the queried object
			if ( isset( $object->taxonomy ) ) {

				$get_post_type = self::get_post_type_by_taxonomy( $object->taxonomy );
				$post_type     = isset( $get_post_type[0] ) ? $get_post_type[0] : 'post';
			}

		} elseif ( is_archive() ) {

			$post_types = self::get_supported_post_types();
			foreach ( $post_types as $type ) {

				if ( is_post_type_archive( $type ) ) {
					$post_type = $type;
				}
			}
		}

		return apply_filters( 'plethora_this_view_post_type', $post_type );
	}    

	/**
	 * PUBLIC | Returns true if the current post_type is a archive view. 
	 * Notice: We consider all listing display views ( post or CPT ) as archive pages
	 * Notice: Will work as expected after 'init' hook 
	 * @since 1.0
	 */
	public static function is_archive_page() {
		
		$post_type = self::get_this_view_post_type();

		if ( !empty( $post_type ) ) {

			if ( is_home() ) { return true; } // This is blog
			if ( is_category() || is_tag() || is_tax() || is_author() || is_date() ) { return true; } // if any type of taxonomy
			if ( is_search() ) { return true; } // This is blog
			if ( is_post_type_archive( $post_type ) ) { return true; } // if is a CPT archive
		
		} else {

			 if ( is_post_type_archive() ) { return true; } // if is a CPT archive
		}

		return false;
	}

	/**
	 * PUBLIC
	 * It will return the saved DB value for the given option ID. 
	 * The method check first if a post meta value exists. If nothing is found 
	 * in post meta, it checks for the master value of the given ID on theme options. 
	 * If again nothing is found, it returns the default value set on call.
	 * Notice: you should always give a specific postid when used in loop!
	 * The previously 4th 'comment' argument is deprecated after Xenia Theme
	 */
	public static function option( $option_id, $user_value = '', $postid = 0, $deprecated = 1 ) {

		$postid          = $postid == 0 ? self::get_this_page_id() : $postid;                            // if no postid is given, use self::get_the_ID(). 
		$post_option_val = is_numeric( $postid ) ? get_post_meta( $postid, $option_id, true ) : '';   // If $postid is a number, then search first if post has saved a value for this option on its metaboxes

		// If nothing is found on this post meta, then check theme defaults for this option, otherwise use value that set on option call
		if ( ( is_array($post_option_val) && empty($post_option_val) ) || ( !is_array($post_option_val) && $post_option_val == '' )) { 

			$theme_options    = get_option( THEME_OPTVAR ); // Use this please...NOT SAFE TO USE the global redux option
			$theme_option_val = ( isset( $theme_options[$option_id] )) ? $theme_options[$option_id] : $user_value;
			$source           = ( isset( $theme_options[$option_id] )) ? 'Theme options value' : 'Value given on option call';
			$option_val       = $theme_option_val;

		} else { 

			$option_val = $post_option_val;
			$source     = 'Post meta value';

		}

		// Return the value
		return $option_val;
	}

	/**
	* PUBLIC
	* Echoes the given comment. Is activated only on frontend, 
	* while it will display the comment only if the installation 
	* runs in developer mode, and the specific comments group is activated.
	*/
	public static function dev_comment( $comment = '', $commentgroup = '' ) {

		$commentgroup_status =  self::option( THEMEOPTION_PREFIX . 'dev-'. $commentgroup, 'disable' );

		// All comment types ( except 'layout' and page_info )
		if ( !is_admin() && !is_feed() && self::is_developermode() && did_action( 'get_header' ) && current_user_can('manage_options') && $commentgroup_status === 'enable' ) { 

			switch ( $commentgroup ) {
				case 'customization_info':

					if ( !empty( $comment ) ) { 

						print_r( "\n". '<!-- '. $comment .'  -->'."\n" );
					}
					break;

				case 'page_info':

					$comment_parts = self::get_dev_comment_page_info();
					if ( ! empty( $comment_parts ) ) {

						print_r ( "\n" .'<!-- '. "\n" );
						foreach ( $comment_parts as $checked_with => $check_results ) {
							if ( $check_results['cond'] ) {

								print_r( $check_results['desc'] .' || checked with '.$checked_with."\n" );
							}
						}
						print_r ( ' -->'. "\n" );
					}
					break;
				
				case 'layout':

					if ( !empty( $comment ) ) { 

						print_r( "\n". '<!-- '. $comment .'  -->'."\n" );
					}
					break;
			}
		} 
	}

	/**
	 * PUBLIC | Returns native, Plethora CPTs and any non Plethora CPT that has frontend archive/single views. 
	 * Notice: Will work as expected after 'init' hook 
	 * @since 1.0
	 */
	public static function get_supported_post_types( $args = array() ) {
		
		$default_args = array( 
			'type'          => 'singles',   // 'singles', 'archives'
			'output'        => 'names',     // 'names' | 'objects'    
			'public'        => true,        // true/false for post types that have single post pages on frontend
			'include'       => array(),     // include those post types in output
			'exclude'       => array(),     // exclude those post types from output
			'plethora_only' => false,       // return only those that don't have Plethora frontend implementation ( this is checked according to Plethora_Posttype_ class )
		);

		// Merge user given arguments with default
		$args = wp_parse_args( $args, $default_args);
		$args['include'] = is_array($args['include']) ? $args['include'] : array($args['include']); // Make sure that this will be an array
		$args['exclude'] = is_array($args['exclude']) ? $args['exclude'] : array($args['exclude']); // Make sure that this will be an array

		// get the built in first!
		$builtin_post_types['post'] = get_post_type_object( 'post' );
		$builtin_post_types['page'] = get_post_type_object( 'page' );
		// now we want the CPT ones
		$query_args = array( 'public' => $args['public'], '_builtin' => false );
		$unfiltered_results = get_post_types( $query_args, 'objects' );
		$unfiltered_results = array_merge( $builtin_post_types, $unfiltered_results );

		// Filtering according to arguments
		$supported_posttypes = array();
		foreach ( $unfiltered_results as $post_type => $post_type_obj ) {

			// Check if this is excluded
			if ( ! in_array( $post_type, $args['exclude'] ) ) {

				// Get singles / archives
				if ( $args['type'] === 'singles' || ( ( $args['type'] === 'archives' && !empty( $post_type_obj->has_archive ) && $post_type_obj->has_archive ) || $post_type === 'post' ) ) {

					// Get plethora / non plethora
					if ( $args['plethora_only'] && class_exists( 'Plethora_Posttype_'. ucfirst( $post_type ) ) ) {

						$supported_posttypes[$post_type] = $args['output'] === 'objects' ? $post_type_obj : $post_type;
					
					} elseif ( ! $args['plethora_only'] ) {

						$supported_posttypes[$post_type] = $args['output'] === 'objects' ? $post_type_obj : $post_type;
					}
				}
			}
		}

		if ( !empty( $args['include'] ) ) {

			foreach ( $args['include'] as $post_type ) {

				$post_type_obj = get_post_type_object( $post_type );
				
				if ( !is_null( $post_type_obj ) ) {

					$supported_posttypes[$post_type] = $args['output'] === 'objects' ? $post_type_obj : $post_type;
				}
			}
		}

		return apply_filters( 'plethora_supported_post_types', $supported_posttypes, $args ) ;
	}

	/**
	 * PUBLIC | Returns native, Plethora Taxonomies and any non Plethora CPT associated with supported public post types 
	 * Notice: Will work as expected after 'init' hook 
	 * @since 1.0
	 */
	public static function get_supported_taxonomies( $args = array() ) {
		
		$default_args = array( 
			'output'        => 'names',     // 'names' | 'objects'    
			'public'        => 'all',       // true/false for public, 'all' for all taxonomies
			'post_type'     => array(),   // array of post types, associating with the desired output
			'include'       => array(),     // exclude those post types from output
			'exclude'       => array(),     // include those post types in output
			'plethora_only' => false,       // return only those that don't have Plethora frontend implementation ( this is checked according to Plethora_Posttype_ class )
		);

		// Merge user given arguments with default
		$args              = wp_parse_args( $args, $default_args);
		$args['include']   = is_array($args['include']) ? $args['include'] : array($args['include']); // Make sure that this will be an array
		$args['exclude']   = is_array($args['exclude']) ? $args['exclude'] : array($args['exclude']); // Make sure that this will be an array
		$args['post_type'] = is_array($args['post_type']) ? $args['post_type'] : array($args['post_type']); // Make sure that this will be an array

		// get the post types first!
		$post_types           = empty( $args['post_type'] ) ? self::get_supported_post_types() : $args['post_type'];
		$supported_taxonomies = array();

		// get taxonomies, after excluding according to 'exclude' argument
		foreach ( $post_types as $post_type ) {

			$post_type_taxonomies = get_object_taxonomies( $post_type, 'objects' );
			foreach ( $post_type_taxonomies as $taxonomy_slug => $taxonomy_obj ) {

				if ( ! in_array( $taxonomy_slug, $args['exclude'] ) && ( $args['public'] === 'all' || $args['public'] === $taxonomy_obj->public ) ) {

					if ( $taxonomy_obj )
					$supported_taxonomies[$taxonomy_slug] = $taxonomy_obj;
				}
			}
		}

		// include taxomies requested on 'include' argument ( if are actual taxonomies and matching 'public' argument )
		if ( !empty( $args['include'] ) ) {

			foreach ( $args['include'] as $taxonomy_slug ) {

				$taxonomy_obj = get_taxonomy( $taxonomy_slug );
				
				if ( !is_null( $taxonomy_obj ) && ( $args['public'] === 'all' || $args['public'] === $taxonomy_obj->public ) ) {

					$supported_taxonomies[$taxonomy_slug] = $args['output'] === 'objects' ? $taxonomy_obj : $taxonomy_slug;
				}
			}
		}

		return apply_filters( 'plethora_supported_taxonomies', $supported_taxonomies, $args ) ;
	}

	/**
	 * PUBLIC | Passing script variables to theme.js file
	 * @param $var_group ( variables group name
	 * @param $vars ( array with values in key=>value format )
	 * @since 1.0
	 */
	public static function set_themeconfig( $var_group, $vars = array() ) {

		if ( ! empty( $var_group ) && ! empty( $vars ) ) {

			self::themeconfig( $var_group, $vars );
		}
	}

	/**
	 * PUBLIC | Will enqueue a handle's init script only if this is present on page  
	 * @since 1.0
	*/
	public static function enqueue_init_script( $args = array() ) {

		$default_args = array( 
			'handle'   => '',       // Main script(s) handle(s) WITHOUT  PREFIX ( main script is the script that will be initialized )
			'function' => '',       // Function/Class method that returns the markup ( class method should be arrays )
			'script'   => '',       // Ready script to enqueue...useful for scripts that have variable values
			'header'   => false,    // If true, script will be included on header, otherwise on footer
			'multiple' => false     // If true, script will be allowed to enqueued more than once
		);

		$args = wp_parse_args( $args, $default_args);          // MERGE GIVEN ARGS WITH DEFAULTS

		// extract & verify arguments
		extract( $args );
		$handle = !is_array( $handle ) ? $handle : array( $handle );
		$callback = empty( $function ) ? $script : $function;                      // select between func or normal script
		if ( empty( $handle ) || ( empty( $callback )  ) ) { return false; }       // no point to continue

		// Add init script to global array variable 'plethora_init_scripts'
		global $plethora_init_scripts;
		$plethora_init_scripts = empty($plethora_init_scripts) ? array() : $plethora_init_scripts; // avoid php warning if empty
		if ( ! $multiple && array_key_exists($handle, $plethora_init_scripts ) ) { return true; }  // exit if multiple not allowed and script exists
		$plethora_init_scripts[$handle][] = array( 
			'callback_type' => empty( $function ) ? 'script' : 'function',
			'callback'      => $callback,
			'position'      => $header ? 'header' : 'footer',
		);
		return true;
	}


	/**
	 * PUBLIC
	 * Returns frontend feature template status, as set on the get_template_status method 
	 * of the given feature class
	 * @return boolean
	 */
	public static function get_template_status( $controller, $feature, $args = array() ) {

		$status = false;
		if ( ! empty( $controller ) && ! empty( $feature ) ) {

			$feature_instance = self::get_feature_instance( $controller, $feature );
			
			if ( method_exists( $feature_instance, 'get_template_status' ) ) {

				return ( ! empty( $args ) ) ? $feature_instance->get_template_status( $args ) : $feature_instance->get_template_status();
			}
		}

		return $status;
	}

	/**
	 * PUBLIC
	 * Returns frontend feature template configuration, as set on the get_template_config method 
	 * of the given feature class
	 * @return array
	 */
	public static function get_template_config( $controller, $feature, $args = array() ) {

		$config = array();
		if ( ! empty( $controller ) && ! empty( $feature ) ) {

			$feature_instance = self::get_feature_instance( $controller, $feature );
			
			if ( method_exists( $feature_instance, 'get_template_config' ) ) {

				return ( ! empty( $args ) ) ? $feature_instance->get_template_config( $args ) : $feature_instance->get_template_config();
			}
		}

		return $config;
	}

	/**
	 * PUBLIC | Adds attributes ( ie. class, id, style, etc. ) to core layout container tags
	 * @since 1.0
	 */
	public static function add_container_attr( $container, $attr_name, $attr_values ) {

		if ( !empty( $container ) && !empty( $attr_name ) && !empty( $attr_values )  ) {

			// make sure that $attr_value is array
			$attr_values_arr = is_array( $attr_values ) ? $attr_values : array( $attr_values );
			
			// make sure that an array record exists on $plethora global
			global $plethora;
			if ( ! isset( $plethora['layout'][$container][$attr_name] ) ) {

				$plethora['layout'][$container][$attr_name] = array();
			}

			// add the user given values to the $plethora global
			foreach ( $attr_values_arr as $attr_value ) {

				$attr_value_key = sanitize_key( $attr_value );
				$plethora['layout'][$container][$attr_name][$attr_value_key] = $attr_value;
			}

			// finally add the filter
			return add_filter( 'plethora_container_'. $container .'_atts', array( 'Plethora_Theme', 'filter_container_attr' ) );
		}

		return false;
	}

	/**
	 * PUBLIC | Remove attributes ( ie. class, id, style, etc. ) from core layout container tags
	 * @since 1.0
	 */
	public static function remove_container_attr( $container, $attr_name, $attr_values = array() ) {

		if ( !empty( $container ) && !empty( $attr_name )  ) {

			// make sure that $attr_value is array
			$attr_values_arr = is_array( $attr_values ) ? $attr_values : array( $attr_values );

			global $plethora;
			if ( !empty( $attr_values_arr ) ) {

				foreach ( $attr_values_arr as $attr_value ) {
					 
					 if ( !empty( $attr_value ) ) {

							$attr_value_key = sanitize_key( $attr_value );
							unset( $plethora['layout'][$container][$attr_name][$attr_value_key] );

							// if attribute is empty of values, remove it completely
							if ( empty( $plethora['layout'][$container][$attr_name] ) ) {

								unset( $plethora['layout'][$container][$attr_name] );
							}
					 }
				}

			} else {

				unset( $plethora['layout'][$container][$attr_name] );
			}

			// add the filter
			return add_filter( 'plethora_container_'. $container .'_atts', array( 'Plethora_Theme', 'filter_container_attr' ), 20 );
		}

		return false;
	}


	/**
	 * PUBLIC | Will return page layout settings according to given post type. 
	 * Works with all WP native and Plethora created single/archive pages
	 * Notice: you should follow the same layout option naming pattern for this to work with your CPTs
	 * Notice: Will work after 'init' hook 
	 * @since 1.0
	*/
	public static function get_layout( $post_type = '' ) {

		$post_type = empty( $post_type ) ? self::get_this_view_post_type() : $post_type ;

		if ( is_search() ) {

			$layout = Plethora_Theme::option( METAOPTION_PREFIX . 'search-layout', 'right_sidebar'); 

		} elseif ( is_singular() ) {

			$layout = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-layout', 'right_sidebar'); 

		} elseif ( self::is_archive_page() ) {

			$layout = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-layout', 'right_sidebar');

		} elseif ( is_404() ) {

			$layout = 'no_sidebar'; // layout for 404 is full by default

		} else {

			$layout = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-layout', 'right_sidebar');
		}
	 
		// Filter and return layout key
		return apply_filters( 'plethora_layout', $layout );
	}


	/**
	 * PUBLIC | Add a new template element.
	 */
	public static function add_element( $container, $handle, $args ) {

		if ( empty( $container ) || empty( $handle ) ) { return false; }

		$default_args = array( 
			'container' => $container,
			'handle'    => $handle,
			'file'      => false,
			'function'  => false,
			'html'      => false,
			'status'    => true,
			'priority'  => 10,
			'options'   => array(),
		);
		$args = wp_parse_args( $args, $default_args );

		// Find the correct container section
		if ( in_array( $container, array( 'head', 'head_before', 'body_open', 'header_topbar', 'header_main' ) ) ) {

			$container_section = 'header';

		} elseif ( in_array( $container, array( 'content_top', 'content_main_loop_before', 'content_main_loop', 'content_main_loop_after', 'content_bottom' ) ) ) {

			$container_section = 'content';

		} elseif ( in_array( $container, array( 'footer_top', 'footer_main', 'footer_bar' ) ) ) {

			$container_section = 'footer';

		} elseif ( $container === 'mediapanel' ) {

			$container_section = 'mediapanel';
		}

		global $plethora;
		$plethora['add_elements'][$container_section][] = $args ;
		return true;
	}

	/**
	 * PUBLIC | Update an existing a template element.
	 * If the template element exists, the arguments will be merged.
	 * If the template element does not exist, nothing will happen.
	 */
	public static function update_element( $container, $handle, $args ) {

		if ( empty( $container ) || empty( $handle ) || empty( $args ) ) { return false; }

		global $plethora;
		$plethora['update_elements'][$container.'-'.$handle] = $args ;
		return true;
	}

	/**
	 * PUBLIC | Update a template element's option.
	 * If the option exists, it will be replaced with value given.
	 * If the option does not exists, it will be created.
	 */
	public static function update_element_option( $container, $handle, $opt_name, $opt_val = '' ) {

		if ( empty( $container ) || empty( $handle ) || empty( $opt_name ) ) { return false; }

		return self::update_element( $container, $handle, array(
			'options' => array( $opt_name => $opt_val )
		));
	}

	/**
	 * PUBLIC | Update a template element's display order.
	 * If the option exists, it will be replaced with value given.
	 * If the option does not exists, it will be created.
	 */
	public static function update_element_order( $container, $handle, $order ) {

		if ( empty( $container ) || empty( $handle ) || empty( $order ) ) { return false; }

		return self::update_element( $container, $handle, array(
			'priority' => $order
		));
	}

	/**
	 * PUBLIC | Remove a template element
	 */
	public static function remove_element( $container, $handle ) {

		if ( empty( $container ) || empty( $handle ) ) { return false; }

		global $plethora;
		$plethora['remove_elements'][] = $container.'-'.$handle;
		return true;
	}

	/**
	 * PUBLIC | Will return container type settings according to container
	 * $container: 'header', 'content' ( default ), 'footer', 'mediapanel'
	 * Works with all WP native and Plethora created single/archive pages
	 * Notice: you should follow the same container option naming pattern for this to work with your CPTs
	 * Notice: Will work after 'init' hook 
	 * @since 1.4
	*/
	public static function get_container_type( $container = 'content' ) {

		$container = empty( $container ) ? 'content' : $container ;

		switch ( $container ) {
			case 'content':
			default:

				$post_type = self::get_this_view_post_type();
				if ( is_search() ) {

					$type = Plethora_Theme::option( METAOPTION_PREFIX . 'search-containertype', 'container', 0, 0); 

				} elseif ( is_singular() ) {

					$type = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-containertype', 'container', 0, 0); 

				} elseif ( self::is_archive_page() ) {

					$type = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-containertype', 'container', 0, 0); 

				} elseif ( is_404() ) {

					$type = Plethora_Theme::option( METAOPTION_PREFIX .'404-containertype', 'container', 0, 0); 

				} else {

					$type = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-containertype', 'container', 0, 0); 
				}

				break;
			
			case 'header':
				$type = Plethora_Theme::option( METAOPTION_PREFIX .'header-container-type', 'container', 0, 0); 
				break;

			case 'mediapanel':
				$type = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-container-type', 'container', 0, 0); 
				break;

			case 'footer_top': 
				$type = Plethora_Theme::option( METAOPTION_PREFIX .'footer_top-container-type', 'container', 0, 0); 
				break;

			case 'footer_main': 
				$type = Plethora_Theme::option( METAOPTION_PREFIX .'footer_main-container-type', 'container', 0, 0); 
				break;

			case 'footer_bar': 
				$type = Plethora_Theme::option( METAOPTION_PREFIX .'footer_bar-container-type', 'container', 0, 0); 
				break;

			case 'footer': // deprecated after Hotel Xenia
				$type = Plethora_Theme::option( METAOPTION_PREFIX .'footer-container-type', 'container', 0, 0); 
				break;
		}
	 
		// Filter and return escaped value
		$type = apply_filters( 'plethora_container_type', $type, $container ); 
		return esc_attr( $type ) ;
	}

	/**
	 * PUBLIC | Prepares admin notice to be displayed on admin_notices() method call
	 */
	public static function add_admin_notice( $handle, $args ) {

		# Use only on admin views
		if ( !is_admin() ) { return; }
		# Default arguments configuration
		$default_args = array(
			'condition'         => true,      // Bool value..practicaly, this should be the admin display condition result
			'theme'             => array( THEME_SLUG => '1.0' ), // Set the themes/versions that this notice should be displayed. The version should be the initial version
			'theme_update_only' => false, 	  // Display this notice only in case of theme update
			'title'             => '',        // Notice title ( may be empty )
			'notice'            => '',        // The main notice text
			'type'              => 'info',    // 'info', 'success', 'warning', 'error'
			'dismiss_text'      => esc_html__( 'Dismiss this notice', 'plethora-framework' ),
			'links'             => array(),   // Array of arrays with links configuration
		);
		$args = wp_parse_args( $args, $default_args);  // MERGE GIVEN ARGS WITH DEFAULTS

		//Check handle and add it to $plethora global variable
		if ( empty( $handle ) ) { return; }
		global $plethora;
		$handle = ! empty( $plethora['admin_notices'] ) && array_key_exists( $handle, $plethora['admin_notices'] ) ? $handle .'-2' : $handle;
		$plethora['admin_notices'][$handle] = $args;
	}

	/**
	 * PUBLIC | Will return container extra classes ( given by users on theme options / metaboxes ) 
	 * $container: 'header', 'content' ( default ), 'footer', 'mediapanel'
	 * Works with all WP native and Plethora created single/archive pages
	 * Notice: you should follow the same container option naming pattern for this to work with your CPTs
	 * Notice: Will work after 'init' hook 
	 * @since 1.4
	*/
	public static function get_extra_class( $container = 'content' ) {

		$container = empty( $container ) ? 'content' : $container ;

		switch ( $container ) {

			case 'content':
			default:
			
				$post_type = self::get_this_view_post_type();
				if ( is_search() ) {

					$extra_class = Plethora_Theme::option( METAOPTION_PREFIX . 'search-extraclass', ''); 

				} elseif ( is_singular() ) {

					$extra_class = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-extraclass', ''); 

				} elseif ( self::is_archive_page() ) {

					$extra_class = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-extraclass', '');

				} elseif ( is_404() ) {

					$extra_class = Plethora_Theme::option( METAOPTION_PREFIX .'404-extraclass', '');

				} else {

					$extra_class = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-extraclass', '');
				}

				break;
			
			case 'header':
				$extra_class = Plethora_Theme::option( METAOPTION_PREFIX .'header-extraclass', '' );
				break;

			case 'mediapanel':
				$extra_class = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-extraclass', '' );
				break;

			case 'footer':
				$extra_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-extraclass', '' );
				break;
		}
	 
		// Filter and return escaped value
		$extra_class = apply_filters( 'plethora_extra_class', $extra_class, $container ); 
		return !empty( $extra_class ) ? esc_attr( $extra_class ) : '' ;
	}

	/**
	 * PUBLIC | Will return header layout_class
	 * $container: 'header', 'content' ( default ), 'footer', 'mediapanel'
	 * Works with all WP native and Plethora created single/archive pages
	 * Notice: you should follow the same container option naming pattern for this to work with your CPTs
	 * Notice: Will work after 'init' hook 
	 * @since 1.4
	*/
	public static function get_header_layout_class() {

		$header_layout_class = Plethora_Theme::option( METAOPTION_PREFIX . 'header-layout', ''); 
	 
		// Filter and return escaped value
		$header_layout_class = apply_filters( 'plethora_header_layout_class', $header_layout_class ); 
		return !empty( $header_layout_class ) ? esc_attr( $header_layout_class ) : '' ;
	}

	/**
	 * PUBLIC | Will return main page layout settings according to given post type. 
	 * Works with all WP native and Plethora created single/archive pages
	 * Notice: you should follow the same sidebar option naming pattern for this to work with your CPTs
	 * Notice: Will work as expected after 'init' hook 
	 * @since 1.0
	 */
	public static function get_main_sidebar( $post_type = '' ) {

		$post_type = empty( $post_type ) ? self::get_this_view_post_type() : $post_type ;
		// if showing a single page/post/cpt
		if ( is_search() ) {

			$sidebar  = Plethora_Theme::option( METAOPTION_PREFIX .'search-sidebar', 'sidebar-default'); 

		} elseif ( is_singular() ) {

			$sidebar = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-sidebar', 'sidebar-default'); 
		
		} elseif ( self::is_archive_page() ) {

			$sidebar  = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-sidebar', 'sidebar-default'); 

		} else {

			$sidebar = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-sidebar', 'sidebar-default'); 
		}
		// Filter and return sidebar key
		return apply_filters( 'plethora_main_sidebar', $sidebar );
	}

	/**
	 * PUBLIC | Will return archive grid class according to settings. 
	 * Works with all WP native and Plethora created archive pages
	 * Notice: you should follow the same grid type option naming pattern for this to work with your CPTs
	 * Notice: Will work as expected after 'init' hook 
	 * @since 1.0
	 */
	public static function get_archive_list( $args = array() ) {

		$default_args = array( 
			'output'      => 'option',    // 'option' | 'class'    
			'add_classes' => array(),     // APPEND ADDITIONAL CLASSES WHEN 'output' == 'class'. In example, this will output a 'masonry boxed_children' class ( 'add_classes' => array( 'masonry' => array( 'boxed_children' ) ) ) 
		);

		$args   = wp_parse_args( $args, $default_args);  // MERGE GIVEN ARGS WITH DEFAULTS
		$output = '';

		if ( self::is_archive_page() ) {

			$post_type  = self::get_this_view_post_type();
			$list_type = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-listtype', 'classic');
			
			if ( $args['output'] === 'class') {
				$output[] = $list_type;
				if ( isset($args['add_classes'][$list_type] ) ) {

					foreach ( $args['add_classes'][$list_type] as $key=>$addclass ) {

						$output[] = $addclass;  // output classes setup
					}
				}

			} else {

				$output = $list_type; // output the option value
			}
		} 

		// APPLY FILTER ONLY ON CLASSES OUTPUT !!! )
		if ( $args['output'] === 'class' ) {

			$output = apply_filters( 'plethora_archive_list_class', $output ); // apply filters ONLY on classes output
		}
		return $output;
	}

	/**
	 * PUBLIC | Returns any Plethora post/archive title ( views: singular, archive, 404, search  )
	 * @since 1.0
	 */
	public static function get_title( $args = array() ) {

		$default_args = array( 
			'tag'           => 'h2',       // HTML tag ( leave empty for raw output )    
			'class'         => array('post_title'), // HTML tag class(es)    
			'id'            => '',         // HTML tag id    
			'listing'       => false,      // this MUST be set 'true' for listing view requests ( will add a link too )   
			'link'          => true,       // adds a post link ( requires 'listing' set to true )   
			'force_display' => false,      // force title display without checking for options    
			'post_type'     => '',         // force specific post type    
			'apply_filters' => true,      // don't apply filters    
		);

		$args      = wp_parse_args( $args, $default_args); // Merge user given arguments with default
		$post_type = empty( $args['post_type'] ) ? self::get_this_view_post_type() : $args['post_type'] ;
		$title     = '';

		if ( is_404() ) {

			$title  = self::option( METAOPTION_PREFIX .'404-title-text', 1 );

		} elseif ( is_search() && !$args['listing']) {

			$display = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'search-title', 1 );
			$title   = $display ? self::option( THEMEOPTION_PREFIX .'search-title-text', esc_html__( 'Search For:', 'plethora-framework' ) ) .' "'. get_search_query() .'"' : '' ;

		} elseif ( is_singular() || ( self::is_archive_page() && $args['listing'] ) ) {

			$display_single = $args['force_display'] ? true : self::option( METAOPTION_PREFIX . $post_type .'-title', 1 );
			$display        = self::is_archive_page() ? 1 : $display_single; // Special workaround for title display control ( different when in blog view )
			if ( $display && is_singular() ) {
				
				$title = get_the_title( get_queried_object_id() );

			} elseif ( $display && $args['listing'] ) {

				$title = get_the_title();
			}

		} elseif ( self::is_archive_page() && ! $args['listing'] || !self::is_library_active() ) { // archive title ( contains a fix when PFL is inactive in order to display blog title out of the box )

			$display        = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title', 1 );
			$display_tax    = self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-tax', 1 );
			$display_author = self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-author', 1 );
			$display_date   = self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-date', 1 );

			if ( ( is_category() || is_tag() || is_tax() ) && $display_tax ) { // taxonomy term

				$title = $display ? strip_tags( single_term_title( '', false ) ) : '';

			} elseif ( is_author() && $display_author ) { // author display name

				$title = $display ? strip_tags( get_the_author() ) : '';

			} elseif ( is_date() && $display_date ) { // month title

				$title = $display ? strip_tags( single_month_title(' ', false ) ) : '';

			} else { // default title

				$title = $display ? self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-text', esc_html__('The Blog', 'plethora-framework' ) ) : '';
			}
		}

		// apply filters before final html tasks
		$title = $args['apply_filters'] ? apply_filters( 'plethora_get_title', $title, $args ) : $title;

		if ( !empty( $title ) && !empty( $args['tag'] ) ) { 

			$class      = !empty($args['class']) ? ' class="'. esc_attr( implode(' ', $args['class']) ) .'"' : '';
			$id         = !empty($args['id']) ? ' id="'. esc_attr( $args['id'] ).'"' : '';
			$link_open  = $args['listing'] && $args['link'] ? '<a href="'. get_permalink() .'">' : '';
			$link_close = $args['listing'] && $args['link'] ? '</a>' : '';
			$title      = '<'. $args['tag'] . $class . $id .'>'. $link_open . esc_html( $title ) . $link_close .'</'. $args['tag'] .'>';
		}

		return $title;
	}

	/**
	 * PUBLIC | Returns any Plethora post/archive subtitle ( views: singular, archive, 404, search )
	 * @since 1.0
	 */
	public static function get_subtitle( $args = array() ) {

		$default_args = array( 
			'tag'           => 'p',       // HTML tag ( leave empty for raw output )    
			'class'         => array('post_subtitle'),    // HTML tag class(es)    
			'id'            => '',         // HTML tag id    
			'listing'       => false,      // this MUST be set 'true' for listing view requests    
			'force_display' => false,      // force title display without checking for options    
			'post_type'     => '',         // force specific post type    
			'apply_filters' => true,       // don't apply filters    
		);

		// Merge user given arguments with default
		$args = wp_parse_args( $args, $default_args);
		$post_type = empty( $args['post_type'] ) ? self::get_this_view_post_type() : $args['post_type'] ;
		$subtitle = '';

		if ( is_404() ) {

			$subtitle  = self::option( METAOPTION_PREFIX .'404-subtitle-text', '' );

		} elseif ( is_search() && !$args['listing'] ) {

			$display  = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'search-subtitle', 1 );
			$subtitle = $display ? self::option( THEMEOPTION_PREFIX .'search-subtitle-text', '' ) : '';

		} elseif ( is_singular() || $args['listing'] ) {

			$display_single  = $args['force_display'] ? true : self::option( METAOPTION_PREFIX . $post_type .'-subtitle', 1 );
			$display_archive = $args['force_display'] ? true : self::option( METAOPTION_PREFIX .'archive'. $post_type .'-listing-subtitle', 1, get_the_id() );
			$display         = $args['listing'] ? $display_archive : $display_single;
			$subtitle        = $display ? self::option( METAOPTION_PREFIX . $post_type .'-subtitle-text', '', get_the_id() ) : '';

		} elseif ( self::is_archive_page() && ! $args['listing'] ) { // archive subtitle

			$display        = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle', 0 );
			$display_tax    = self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-tax-subtitle', 1 );
			$display_author = self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-author-subtitle', 1 );
			$display_date   = self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-date-subtitle', 0 );

			if ( ( is_category() || is_tag() || is_tax() ) && $display_tax ) { // taxonomy term description

				$subtitle = $display ? strip_tags( term_description() ) : '';

			} elseif ( is_author() && $display_author ) { // author bio

				$subtitle = $display ? strip_tags( get_the_author_meta('description') ) : '';

			} elseif ( is_date() && $display_date ) { // author bio

				$subtitle = $display ? self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle-text', '' ) : '';

			} else { // default subtitle

				$subtitle = $display ? self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle-text', '' ) : '';
			}
		}

		// apply filters before final html tasks
		$subtitle = $args['apply_filters'] ? apply_filters( 'plethora_get_subtitle', $subtitle, $args ) : $subtitle;

		if ( !empty( $subtitle ) && !empty( $args['tag'] ) ) { 

			$class    = !empty($args['class']) ? ' class="'. esc_attr( implode( ' ', $args['class'] ) ) .'"' : '';
			$id       = !empty($args['id']) ? ' id="'. esc_attr( $args['id'] ) .'"' : '';
			$subtitle = '<'. $args['tag'] . $class . $id .'>'. esc_html( $subtitle ) .'</'. $args['tag'] .'>';
		}

		return $subtitle;
	}

	/**
	 * PUBLIC | Returns any content section colorset
	 * @since 1.0
	 */
	public static function get_content_colorset( $post_type = '' ) {

		$post_type = empty( $post_type ) ? self::get_this_view_post_type() : $post_type ;
		$colorset = '';

		if ( is_404() ) {

			$colorset = Plethora_Theme::option( THEMEOPTION_PREFIX . '404-colorset' ); 

		} elseif ( is_search() ) {

			$colorset = Plethora_Theme::option( THEMEOPTION_PREFIX . 'search-colorset' ); 

		} elseif ( is_singular() ) {

			$colorset = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-colorset' ); 

		} elseif ( self::is_archive_page() ) {

			$colorset = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-colorset' );
		}

		return apply_filters( 'plethora_get_content_colorset', $colorset, $post_type );
	}

	/**
	 * PUBLIC | Returns Plethora listing content according to theme options
	 * Notice: Will not work outside the loop 
	 * @since 1.0
	 */
	public static function get_listing_content( $args = array() ) {

		if ( ! in_the_loop() ) { return; } // not working outside loop

		// those will by applied only on excerpt output
		$default_args = array( 
			'listing'       => false,     // this MUST be set 'true' for listing view requests    
			'tag'           => 'p',       // HTML tag ( leave empty for raw output )    
			'class'         => array(),   // HTML tag class(es)    
			'id'            => '',        // HTML tag id    
			'force_display' => '',        // 'excerpt'/'content' force a view without checking for options    
		);

		// Merge user given arguments with default
		$args                  = wp_parse_args( $args, $default_args);
		$args['force_display'] = $args['force_display'] === 'excerpt' || $args['force_display'] === 'content' ? $args['force_display'] : '';
		
		$post_type             = self::get_this_view_post_type();
		$option                = empty( $args['force_display'] ) ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-listing-content', 'content', get_the_id()) : $args['force_display']; // Display Excerpt or Content?   

		switch ( $option ) {
			case 'excerpt':
				$output = get_the_excerpt('');
				if ( !empty( $output ) && !empty( $args['tag'] ) ) { 

					$class    = !empty($args['class']) ? ' class="'. implode(' ', $args['class']) .'"' : '';
					$id       = !empty($args['id']) ? ' id="'. $args['id'] .'"' : '';
					$output = '<'. $args['tag'] . $class . $id .'>'. $output .'</'. $args['tag'] .'>';
				}
				return $output;
				break;
			
			default:
				$output = get_the_content();
				return apply_filters('the_content', $output );
				break;
		}
	}

	public static function get_post_content( $args = array() ) {

		if ( ! in_the_loop() ) { return; } // not working outside loop
		$default_args = array(
			'listing'                        => false,
			'tag'                            => 'p', // HTML tag ( leave empty for raw output )    
			'class'                          => array(),  // HTML tag class(es)    
			'id'                             => '',  // HTML tag id    
			'force_display'                  => '',  // 'excerpt'/'content' force a view without checking for options    
			'echo'                           => false,  // true if you need to echo this output, false for return
			'wp_link_pages'                  => true,
			'wp_link_pages_before'           => '<div class="page-links post_pagination_wrapper"><span class="page-links-title">' . esc_html__( 'Pages:', 'plethora-framework' ) . '</span>',
			'wp_link_pages_after'            => '</div>',
			'wp_link_pages_link_before'      => '<span class="post_pagination_page">',
			'wp_link_pages_link_after'       => '</span>',
			'wp_link_pages_next_or_number'   => 'number',
			'wp_link_pages_separator'        => ' ',
			'wp_link_pages_nextpagelink'     => esc_html__( 'Next page', 'plethora-framework' ),
			'wp_link_pages_previouspagelink' => esc_html__( 'Previous page', 'plethora-framework' ),
			'wp_link_pages_pagelink'         => '%',
		);
		// Merge user given arguments with default
		$args                  = wp_parse_args( $args, $default_args);
		$args['force_display'] = $args['force_display'] === 'excerpt' || $args['force_display'] === 'content' ? $args['force_display'] : '';
		
		extract($args);
		if ( $listing ) {

			if ( $echo ) { 

				echo self::get_listing_content( $args ); 
				return true;

			 } else {

				return self::get_listing_content( $args );
			 }

		} else {

			$output = apply_filters( 'the_content', get_the_content() );
			if ( $wp_link_pages ) {

				$output .= wp_link_pages( 
					array(
						'before'           => $wp_link_pages_before,
						'after'            => $wp_link_pages_after,
						'link_before'      => $wp_link_pages_link_before,
						'link_after'       => $wp_link_pages_link_after,
						'next_or_number'   => $wp_link_pages_next_or_number,
						'separator'        => $wp_link_pages_separator,
						'nextpagelink'     => $wp_link_pages_nextpagelink,
						'previouspagelink' => $wp_link_pages_previouspagelink,
						'pagelink'         => $wp_link_pages_pagelink,
						'echo'             => false,
					)
				);
			}

			if ( $echo ) { 

				echo trim( $output );
				return true;

			} else {

				return $output;
			}
		}
	}

	// Will return featured media on single/listing view
	public static function get_post_media( $args = array() ) {

		$default_args = array( 
			'listing'       => false,
			'post_id'       => get_the_id(),  // Post id...default use inside loop
			'type'          => 'image',       // Media type ( 'image', 'video', 'audio', gallery )
			'return'        => 'html',        // Return url OR HTML ( 'html', 'config' ). 
			'stretch'       => false,         // Apply streching wrapper technique options ( only for HTML returns )
			'link_to_post'  => false,         // Return image wrapped in a tag ( only for HTML image returns )
			'force_display' => false,         // If set to true, it will ignore featured media display options
			'echo'          => false,         // If true, it will echo the output    
			'post_type'     => '',            // force specific post type
			'media_size'    => 'large'           // thumbnail, medium, large, full OR custom size defined via add_image_size OR array( width, height )
		);

		// Merge user given arguments with default and extract
		$args = wp_parse_args( $args, $default_args);
		extract($args);
		$post_type = empty( $post_type ) ? self::get_this_view_post_type() : $post_type ;
		$type      = empty( $type ) ? 'image' : $type;
		
		// Apply featured media display options // ARCHIVE
		if ( ! $force_display && $listing ) {
			
			if ( ! in_the_loop() ) { return ( $return === 'html' ) ? '' : array(); } // not working outside loop
			$mediadisplay = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-mediadisplay', 'inherit', $post_id );
			if ( $mediadisplay === 'hide' ) { return ( $return === 'html' ) ? '' : array(); }  // if set to hide, no need to continue
			$type = $mediadisplay === 'featuredimage' ? 'image' : $type;  // display featured image or according to post format
		
		// Apply featured media display options // SINGLE
		} elseif ( ! $force_display && ! $listing ) {

			$mediadisplay = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-mediadisplay', 1, $post_id );
			if ( ! $mediadisplay ) { return ( $return === 'html' ) ? '' : array(); } // if set to hide, no need to continue
		}
		// Start working with the output
		$return_val = array( 
			'wrap_tag'   => 'div',
			'wrap_class' => '',
			'wrap_id'    => '',
			'wrap_attrs' => array(),
			'media'      => ( ( $type === 'gallery' ) ? array( 'images' => array() ) : array( 'url' => '' ) ), 
			'html'       => ''
		);
		switch ($type) {
				case 'video':

					$video_url  = Plethora_Theme::option( METAOPTION_PREFIX .'content-video', '', $post_id );
					if ( empty( $video_url ) ) { return ( $return === 'html' ) ? '' : array(); }
					$return_val['media'][0]['url'] = $video_url;
					$return_val['html']            .= wp_oembed_get( $video_url ) ; 
					break;

				case 'audio':

					$audio_url  = Plethora_Theme::option( METAOPTION_PREFIX .'content-audio', '', $post_id );
					if ( empty( $audio_url ) ) { return ( $return === 'html' ) ? '' : array(); }
					$return_val['media'][0]['url'] = $audio_url;
					$return_val['html']            .= wp_oembed_get( $audio_url ); 
					break;

				case 'image':

					if ( ! has_post_thumbnail( $post_id ) ) { return ( $return === 'html' ) ? '' : array(); }
					$featured_image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $media_size );
					if ( empty( $featured_image_src[0] ) ) { return ( $return === 'html' ) ? '' : array(); }
					$return_val['media'][0]['url'] = $featured_image_src[0];
					$return_val['html']            .= '<img src="'. esc_url( $featured_image_src[0] ) .'" alt="'. esc_attr( get_the_title( $post_id ) ) .'">';
					break;

				case 'gallery':

					$images = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-content-gallery', '' );
					if ( empty( $images ) ) { return ( $return === 'html' ) ? '' : array(); }
					$images = explode(',', $images );
					foreach ( $images as $image_id ) {

						$image_src  = wp_get_attachment_image_src( $image_id, $media_size );
						 if ( empty( $image_src[0] ) ) { continue; }
						$return_val['media'][]['url'] = $image_src[0];
						$return_val['html']          .= '<img alt="'. esc_attr( get_the_title( $post_id ) ) .'" src="'. esc_url( $image_src[0] ) .'">';
					}
					break;          
					
				default:
					return ( $return === 'html' ) ? '' : array(); 
					break;
		}
		// Apply stretching wrapper markup/config
		if ( $stretch ) {

			$strech_class = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-media-stretch', '', $post_id ); 
			if ( $type !== 'gallery' && !empty( $strech_class ) && $strech_class !== 'foo_stretch' ) {

				$classes[]                               = $type !== 'image' ? 'video_iframe' : '';
				$classes[]                               = $strech_class;
				$styles[]                                = $type === 'image' ? 'background-image: url(\''. esc_url( $return_val['media'][0]['url'] ).'\')' : '';
				$return_val['media']['media_wrap_tag']   = 'figure';
				$return_val['media']['media_wrap_class'] = implode(' ', array_filter( $classes ) );
				$return_val['media']['media_wrap_style'] = implode('; ', array_filter( $styles ) );
				$return_val['html']                      = '<figure class="'. esc_attr( implode(' ', array_filter( $classes ) ) ).'" style="'. esc_attr( implode('; ', array_filter( $styles )) ).'">' . $return_val['html'] .'</figure>';
			
			} elseif ( $type === 'gallery'  ) { // Not sure, ask Leo for this

				
			}
		}

		// If this is an image and asked on call arguments, apply a Link tag
		if ( $type === 'image' && $link_to_post ) {
				
			$return_val['media'][0]['link_href']  = get_permalink( $post_id );
			$return_val['media'][0]['link_title'] = get_the_title( $post_id );
			$return_val['html']                   = '<a href="'. esc_url( get_permalink( $post_id ) ) .'" title="'. esc_attr( get_the_title( $post_id ) ) .'">'. $return_val['html'] .'</a>';
		}
		// Ready!
		if ( $return === 'html' && $echo ) {

			echo trim( $return_val['html'] );
			return;

		} elseif ( $return === 'html' && ! $echo ) {
			
			return $return_val['html'];

		} else {

			return $return_val;
		}
	}

	/**
	 * PUBLIC | Returns 'Read More' link for archive listings
	 * @since 1.3.1
	 */
	public static function get_post_infolabel( $args = array() ) {

		$default_args = array( 
			'post_type'    => get_post_type(),   // Post type
			'post_id'      => get_the_id(),      // Post id...default use inside loop
			'type'         => '',                // 'categories', 'tags', 'author', 'date', 'comments' 
			'listing'      => false,             // this MUST be set 'true' for listing view requests    
			'link'         => true,              // adds the proper link, depending on the info type   
			'sep'          => ', ',              // separator for taxonomy displays   
			'tag'          => 'span',            // HTML tag ( leave empty for raw output )    
			'class'        => array(),           // HTML tag class(es)    
			'id'           => '',                // HTML tag id
			'prepend_html' => '',
			'append_html'  => ''
		);
		$args = wp_parse_args( $args, $default_args);
		extract($args);
		if ( empty( $type ) || ! in_array( $type, array( 'categories', 'tags', 'author', 'date', 'comments' ) ) ) { return ''; }

		// common configuration
		$output    = '';
		$class     = is_array( $class ) ? $class : array( $class );
		$tag_attrs = !empty( $class ) ? ' class="'. implode(' ', $class ) .'"' : '';
		$tag_attrs .= !empty( $id ) ? ' id="'. $id .'"' : '';

		// native post categories
		if ( $type === 'categories' && $post_type === 'post' ) {

			$show_categories = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-category', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type  .'-categories', 1, $post_id );
			if ( $show_categories ) {

				$count = 0;
				$categories = get_the_category();
				foreach( $categories as $key => $category ) {

					$count   = $count + 1;
					$output .= $count > 1 ? $sep : '';
					$output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
					$output .= $link ? '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in category: %s", 'plethora-framework' ), $category->name ) ) . '">' : '';
					$output .= $category->cat_name;
					$output .= $link ? '</a>' : '';
					$output .= !empty( $tag ) ? '</'.$tag.'>' : '';
				}
			}

		// primary taxonomy for CPTs
		} elseif ( $type === 'categories' && $post_type !== 'post' ) {

			$show_primary_tax = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-info-primarytax', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-info-primarytax', 1, $post_id );
			$primary_tax      = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-info-primarytax-slug', 'category', $post_id );
			if ( $show_primary_tax && !empty( $primary_tax ) ) { 

				$terms = get_the_terms( $post_id, $primary_tax );

				if ( ! is_wp_error( $terms ) && !empty( $terms ) ) {

					$count = 0;
					foreach( $terms as $key => $term ) {

						$count   = $count + 1;
						$output .= $count > 1 ? $sep : '';
						$output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
						$output .= $link ? '<a href="'.get_term_link( $term ).'" title="' . esc_attr( sprintf( esc_html__( "View all in category: %s", 'plethora-framework' ), $term->name ) ) . '">' : '';
						$output .= $term->name;
						$output .= $link ? '</a>' : '';
						$output .= !empty( $tag ) ? '</'.$tag.'>' : '';
					}
				}
			}

		// native post tags
		} elseif ( $type === 'tags' && $post_type === 'post' ) {

			$show_tags = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archivepost-info-tags', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX .'post-tags', 1);
			if ( $show_tags ) { 

				$tags = get_the_tags();
				if ( $tags ) {

					$count = 0;
					foreach( $tags as $key => $the_tag ) {

						$count   = $count + 1;
						$output .= $count > 1 ? $sep : '';
						$output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
						$output .= $link ? '<a href="'.get_tag_link( $the_tag->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts tagged with: %s", 'plethora-framework' ), $the_tag->name ) ) . '">' : '';
						$output .= $the_tag->name;
						$output .= $link ? '</a>' : '';
						$output .= !empty( $tag ) ? '</'.$tag.'>' : '';
					}
				}
			}

		// secondary taxonomy for CPTs
		} elseif ( $type === 'tags' && $post_type !== 'post' ) {

			$show_secondary_tax = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-secondarytax', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-info-secondarytax', 1, $post_id );
			$secondary_tax      = Plethora_Theme::option( METAOPTION_PREFIX .$post_type.'-info-secondarytax-slug', 'post_tag', $post_id );
			if ( $show_secondary_tax && !empty( $secondary_tax ) ) { 

				$terms = get_the_terms( $post_id, $secondary_tax );

				if ( ! is_wp_error( $terms ) && !empty( $terms ) ) {

					$count = 0;
					foreach($terms as $key=>$term) {

						$count   = $count + 1;
						$output .= $count > 1 ? $sep : '';
						$output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
						$output .= $link ? '<a href="'.get_term_link( $term ).'" title="' . esc_attr( sprintf( esc_html__( "View all in category: %s", 'plethora-framework' ), $term->name ) ) . '">' : '';
						$output .= $term->name;
						$output .= $link ? '</a>' : '';
						$output .= !empty( $tag ) ? '</'.$tag.'>' : '';
					}
				}
			}

		// author for native posts and CPTs
		} elseif ( $type === 'author' ) {

			$show_author  = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-info-author', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-author', 1, $post_id );
			if ( $show_author ) {

				$output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
				$output .= $link ? '<a href="'. esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ).'" title="' . esc_attr( sprintf( get_the_author() )) . '">' : '';
				$output .= get_the_author();
				$output .= $link ? '</a>' : '';
				$output .= !empty( $tag ) ? '</'.$tag.'>' : '';
			}

		// date for native posts and CPTs
		} elseif ( $type === 'date' ) {

			$show_date = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-info-date', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-date', 1, $post_id);
			if ( $show_date ) {

				$output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
				$output .= get_the_date();
				$output .= !empty( $tag ) ? '</'.$tag.'>' : '';
			}

		// comments count for native posts and CPTs
		} elseif ( $type === 'comments' ) {

			$show_comments = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-comments', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-comments', 1, $post_id );
			if ( $show_comments && comments_open()  ) { 

				$num_comments = get_comments_number( $post_id );
				if ( $num_comments > 0 ) {

					$output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
					$output .= $link ? '<a href="'. esc_url( get_permalink() .'#post_comments').'">' : '' ;
					$output .= $num_comments ;
					$output .= $link ? '</a>' : '';
					$output .= !empty( $tag ) ? '</'.$tag.'>' : '';
				} 
			}
		}

		// Prepend / append HTML
		$output = ! empty( $output ) ? $prepend_html . $output . $append_html : '';
		return $output;
	}

	/**
	 * PUBLIC | Returns 'Read More' link for archive listings
	 * @since 1.3.1
	 */
	public static function get_post_linkbutton( $args = array() ) {

		$default_args = array( 
			'post_type'    => get_post_type(),        // Post type
			'post_id'      => get_the_id(),           // Post id...default use inside loop
			'class'        => array( 'btn-primary' ),  // HTML tag class(es)    
			'id'           => '',                     // HTML tag id
			'prepend_html' => '',
			'append_html'  => ''
		);
		$args = wp_parse_args( $args, $default_args);
		extract($args);

		$output          = '';
		$blog_linkbutton = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton', 1, $post_id ); // Show Post Link Button
		$blog_linktext   = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton-text', esc_html__('Read More', 'plethora-framework'), $post_id ); // Link Button Text
		if ( $blog_linkbutton ) {

			$class     = is_array( $class ) ? $class : array( $class );
			$tag_attrs = !empty( $class ) ? ' class="'. implode(' ', $class ) .'"' : '';
			$tag_attrs .= !empty( $id ) ? ' id="'. $id .'"' : '';

			$output .= '<a href="'. get_permalink( $post_id ) .'"'.$tag_attrs.'>';
			$output .= wp_strip_all_tags( $blog_linktext );
			$output .= '</a>';
		}

		// Prepend / append HTML
		$output = ! empty( $output ) ? $prepend_html . $output . $append_html : '';
		return $output;
	}

	public static function get_pagination( $args = array() ) {

		$default_args = array( 
			'post_type'      => get_post_type(),    // Post type
			'post_id'        => get_the_id(),       // Post id...default use inside loop
			'range'          => 5,       // Post id...default use inside loop
			'class'          => 'pagination pagination-centered',   // Class for previous page    
			'class_previous' => 'pagination-btn',   // Class for previous page    
			'class_next'     => 'pagination-btn',   // Class for next page 
			'class_number'   => 'number',           // Class for page  
			'prepend_html'   => '',
			'append_html'    => ''
		);
		$args = wp_parse_args( $args, $default_args);
		extract($args);

		$pages = '';
		$showitems = ($range * 2)+1;  
		global $paged;
		$paged = empty( $paged ) ? 1 : $paged;

		if ( empty( $pages ) ) {

			global $wp_query;
			$pages = $wp_query->max_num_pages;
			$pages = !$pages ? 1 : $pages;
		}

		$output = '';
		if ( $pages != 1 ) {

				$output .= '  <ul class="'.$class.'">';
				$output .= '    <li class="'.$class_previous.'">'. get_previous_posts_link( esc_html__('Prev', 'plethora-framework') ).'</li>';
				
				if ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) { 

					$output .= '    <li class="'.$class_previous.'"><a href="'.get_pagenum_link(1).'">&laquo;</a></li>'; 
				}

				if ( $paged > 1 && $showitems < $pages ) { 

					$output .= '    <li class="'.$class_previous.'"><a href="'.get_pagenum_link($paged - 1).'">&lsaquo;</a></li>'; 
				}

				for ( $i=1; $i <= $pages; $i++ ) { 

					if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {

						$active_class = $paged == $i ? ' active' : '';
						$output .= '    <li class="'.$class_number.$active_class.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
					}
				}

				if ($paged < $pages && $showitems < $pages) {

					$output .= '    <li class="'.$class_next.'"><a href="' .get_pagenum_link($paged + 1). '">&rsaquo;</a></li>';  
				}

				if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {

					$output .= '    <li class="'.$class_next.'"><a href="' .get_pagenum_link($pages).'">&raquo;</a></li>';
				}
				
				$output .= '    <li class="'.$class_next.'">'. get_next_posts_link( esc_html__('Next', 'plethora-framework') ).'</li>';
				$output .= '  </ul>';
		}

		// Prepend / append HTML
		$output = ! empty( $output ) ? $prepend_html . $output . $prepend_html : '';
		return $output;
	}

	/**
	 * PUBLIC | Returns breadcrumb display status or configuration or html markup, 
	 * depending on the 'return' argument
	 * @return mixed ( string / array() )
	 */
	public static function get_breadcrumb( $args = array() ) {

		$default_args = array( 
			'return'  => 'html',  // 'html': returns basic markup ( default ) | 'status': returns user set status | 'config': returns configuration 
		);

		$args = wp_parse_args( $args, $default_args);
		extract($args);

		$breadcrumb = $return === 'config' ? array() : '' ;
		if ( class_exists( 'Plethora_Module_Breadcrumb_Ext' ) ) {

			switch ( $return ) {

				case 'html':
					
					$breadcrumb = Plethora_Module_Breadcrumb_Ext::get_html();
					break;

				case 'config':

					$breadcrumb = Plethora_Module_Breadcrumb_Ext::get_configuration();
					break;
				
				case 'status':
				default:
					$breadcrumb = Plethora_Module_Breadcrumb_Ext::get_status();
					break;
			}
		}
		return $breadcrumb;
	}

	/**
	 * PUBLIC | Returns post navigation display status or configuration or html markup, 
	 * depending on the 'return' argument
	 * @return mixed ( string / array() )
	 */
	public static function get_postnavi( $args = array() ) {

		$default_args = array( 
			'return'  => 'html',  // 'html': returns basic markup ( default ) | 'status': returns user set status | 'config': returns configuration 
		);

		$args = wp_parse_args( $args, $default_args);
		extract($args);

		$postnavi = $return === 'config' ? array() : '' ;
		if ( class_exists( 'Plethora_Module_Postnavi_Ext' ) ) {

			switch ( $return ) {

				case 'html':
					
					$postnavi = Plethora_Module_Postnavi_Ext::get_html();
					break;

				case 'config':

					$postnavi = Plethora_Module_Postnavi_Ext::get_configuration();
					break;
				
				case 'status':
				default:
					$postnavi = Plethora_Module_Postnavi_Ext::get_status();
					break;
			}
		}
		return $postnavi;
	}

	/**
	 * PUBLIC | Returns true/false if current page has/has not VC sections in content
	 * Does nothing more than returning the post meta 'content_has_sections', a value
	 * saved by Plethora_Shortcode_Vcrow class produced on each post's edit screen.
	 * @since 1.0
	 */
	public static function content_has_sections() {

		if ( self::is_library_active() ) { // should be checked ONLY when PFL plugin is active

			$content_has_sections = self::option( METAOPTION_PREFIX . 'content_has_sections', 0);
		
		} else { // if PFL is inactive, then always return false

			$content_has_sections = false;
		}
		return $content_has_sections; 
	}

	/**
	 * PUBLIC | Returns true/false if current page has a title/subtitle text
	 *
	 * @since 1.0
	 */
	public static function content_has_titles( $which_title = false ) {

		$content_has_titles = false;

		if ( ! $which_title || $which_title === 'both' ) {
			
			$content_has_titles = self::get_title() != '' || self::get_subtitle() != '' ? 1 : 0;

		} elseif ( $which_title === 'title' ) {

			$content_has_titles = self::get_title() != ''  ? 1 : 0;

		} elseif ( $which_title === 'subtitle' ) {
		 
			$content_has_titles = self::get_subtitle() != '' ? 1 : 0;

		}

		return $content_has_titles; 
	}

	/**
	 * PUBLIC | Similar to wp_parse_args() just a bit extended to work with multidimensional arrays :) 
	 */
	public static function parse_multi_args( &$a, $b ) {
		$a = (array) $a;
		$b = (array) $b;
		$result = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) ) {
				$result[ $k ] = self::parse_multi_args( $v, $result[ $k ] );
			
			} else {

				$result[ $k ] = $v;
			}
		}
		return $result;
	}

	/**
	 * PUBLIC | Returns allowed html configuration for several content elements,
	 * ready to use as an wp_kses() function argument
	 */
	public static function allowed_html_for( $for, $to_display = false ) {

	 $allowed_html = array();

		switch ( $for ) {

			case 'post':
				$allowed_html = wp_kses_allowed_html( 'post' );
				break;

			case 'heading':
				$allowed_html['a']      = array( 'href' => array(), 'id' => array(), 'class' => array(), 'title' => array(), 'style' => array() );
				$allowed_html['span']   = array( 'class' => array(), 'title' => array(), 'style' => array() );
				$allowed_html['i']      = array( 'class' => array(), 'title' => array() );
				$allowed_html['br']     = array();
				$allowed_html['em']     = array();
				$allowed_html['strong'] = array();
				$allowed_html['b']      = array();
				break;

			case 'paragraph':
				$allowed_html['a']      = array( 'href' => array(), 'id' => array(), 'class' => array(), 'title' => array(), 'style' => array(), 'width' => array(), 'height' => array() );
				$allowed_html['img']    = array( 'class' => array(), 'title' => array(), 'style' => array(), 'src' => array(), 'width' => array(), 'height' => array(), 'alt' => array()  );
				$allowed_html['span']   = array( 'class' => array(), 'title' => array(), 'style' => array() );
				$allowed_html['i']      = array( 'class' => array(), 'title' => array() );
				$allowed_html['br']     = array();
				$allowed_html['em']     = array();
				$allowed_html['strong'] = array();
				$allowed_html['b']      = array();
				break;
			
			case 'button':
				$allowed_html['span']   = array( 'class' => array(), 'title' => array(), 'style' => array() );
				$allowed_html['i']      = array( 'class' => array(), 'title' => array() );
				$allowed_html['em']     = array();
				$allowed_html['strong'] = array();
				$allowed_html['b']      = array();
				break;

			case 'link':
				$allowed_html['img']    = array( 'class' => array(), 'title' => array(), 'style' => array(), 'src' => array(), 'width' => array(), 'height' => array(), 'alt' => array()  );
				$allowed_html['span']   = array( 'class' => array(), 'title' => array(), 'style' => array() );
				$allowed_html['i']      = array( 'class' => array(), 'title' => array() );
				$allowed_html['em']     = array();
				$allowed_html['strong'] = array();
				$allowed_html['b']      = array();
				break;

			case 'iframe':
				$allowed_html['iframe'] = array('class' => array(), 'style' => array(), 'height' => array(), 'name' => array(), 'sandbox' => array(), 'src' => array(), 'srcdoc' => array(), 'width' => array(), ); 
				break; 
			}

		if ( $to_display ) {
			
			$display_allowed_tags = esc_html__( 'Allowed HTML tags: ', 'plethora-framework' );
			$count = 0;
			foreach ( $allowed_html as $tag => $attrs ) {

				$count++;
				$display_allowed_tags .= $count === 1 ? '<strong>'. $tag .'</strong>' : ' | <strong>'. $tag .'</strong>';
			}

			$allowed_html = $display_allowed_tags;
		}

		return $allowed_html;
	}

	/**
	 * PUBLIC | Returns html tag opening part OR self-closing tag, according to given arguments
	 * Use when the display of a tag is set dynamically. Used mostly
	 * for template part inner containers.
	 */
	public static function get_html_tag_open( $args ) {

		$default_args = array( 
			'tag'          => 'div',   // Any non self-closing html tag ( default: 'div' )
			'class'        => '',      // Tag class attribute value
			'id'           => '',      // Tag id attribute value
			'attrs'        => array(), // Any other tag attribute(s), in $name => $value array
			'self_closing' => false,   // Set to true, if this is a self-closing tag
		);
		$args = wp_parse_args( $args, $default_args);
		extract($args);

		if ( empty( $tag ) ) { return ''; } // return empty string if not tag is given

		$return  =  '<'. esc_attr( $tag );
		$return .=  ( ! empty( $class ) ) ? ' class="'. esc_attr( $class ) .'"' : '';
		$return .=  ( ! empty( $id ) ) ? ' id="'. esc_attr( $id ) .'"' : '';
		foreach ( $attrs as $attr_name => $attr_val ) {

			$return .=  ( ! empty( $attr_val ) ) ? ' '. $attr_name .'="'. esc_attr( $attr_val ) .'"' : '';
		}
		$return .=  ( $self_closing ) ? '/>' : '>';
		return $return;
	}

	/**
	 * PUBLIC | Returns html tag closing part according to given argument
	 * Use when the display of a tag is set dynamically. Used mostly
	 * for template part inner containers.
	 */
	public static function get_html_tag_close( $tag ) {

		if ( empty( $tag ) ) { return ''; } // return empty string if not tag is given
		$return =  '</'. esc_attr( $tag ) .'>';
		return $return;
	}

	/**
	 * PUBLIC | Multibyte version for PHP's ucfirst() function
	 * Use when the display of a tag is set dynamically. Used mostly
	 */
	public static function mb_ucfirst($string, $encoding = 'utf8') {

		$strlen    = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$restChars      = mb_substr($string, 1, $strlen - 1, $encoding);
	    return mb_strtoupper($firstChar, $encoding) . $restChars;
	}	
// THEME RELATED STATIC METHODS <---- FINISH
}