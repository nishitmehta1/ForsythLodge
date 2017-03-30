<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015
*/
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 
/**
 * Handles all features related functionality
 * 
 * @package Plethora Framework
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c) 2014 - 2015
 *
 */
class Plethora_Index {

	public $controllers = array( 'posttype', 'module', 'shortcode', 'widget' );  // THEME SUPPORTED CONTROLLERS
	public $base_features;
	public $parent_features;
	public $child_features;
	public $index;

	public function __construct() {

      	$this->load_controllers();		// Load Controllers
		$this->set_base_features();		// Define Core Features
		$this->set_parent_features();	// Define Parent Features
		$this->set_child_features();	// Define Child Features
		$this->set();					// Set index info
		$this->set_theme_options();		// Set index info

	}

	// Set all base classes
	private function set_base_features() {

		// Locate controllers
		$controllers = $this->controllers;
		$base_features = array();

		// Framework library first
		foreach ( $controllers as $controller ) {

			// Set base classes from Plethora Core
			$base_features = array_merge_recursive( $base_features, self::locate_feature_files( $controller, 'core' ) );	// Locate master feature classes from core

			// Set base classes from Plethora Library plugin, if active
			if ( Plethora_Theme::is_library_active() ) {

				$base_features = array_merge_recursive( $base_features, self::locate_feature_files( $controller, 'library' ) );	// Locate master feature classes from library
			}
		}

		$this->base_features = $base_features;
	}

/// PRIVATE METHODS 

  	/**
  	* Locates supported Plethora theme features
  	*
  	* @return array 
  	* @since 2.0
  	*/
	private function set_parent_features() {

		// Locate controllers
		$controllers = $this->controllers;
		$parent_features = array();

		foreach ( $controllers as $controller ) {

			$parent_features = array_merge_recursive( $parent_features, self::locate_feature_files( $controller, 'parent' ) );	// Locate master feature classes
		}

		$this->parent_features = $parent_features;
	}

  	/**
  	* Locates supported Plethora theme features overrides on child theme
  	*
  	* @return array 
  	* @since 2.0
  	*/
	private function set_child_features() {

		if ( ! is_child_theme() ) { $this->child_features = array(); return; }
		$controllers = $this->controllers;
		$child_features = array();

		foreach ( $controllers as $controller ) {

			$child_features = array_merge_recursive( $child_features, self::locate_feature_files( $controller, 'child' ) );	// Locate master feature classes
		}

		$this->child_features = $child_features;
	}

  	/**
  	* Set features index
  	*
  	* @return array 
  	* @since 2.0
  	*/
  	private function set() {

		$base_features  = $this->base_features;

		$unfiltered_theme_features = array_merge( $this->parent_features, $this->child_features );
		$index = array();
		foreach ( $unfiltered_theme_features as $key => $theme_feature ) {

			$index[$key] = $theme_feature;
			// if exists on framework features, then this MUST be a Plethora feature extension class
			if ( array_key_exists( $key, $base_features ) ) {

				$index[$key]['plethora_supported'] = true;
				$index[$key]['class']              = $base_features[$key]['class'] .'_Ext';
				$index[$key]['base_class']         = $base_features[$key]['class'];
				$index[$key]['base_path']          = $base_features[$key]['path'];
			
			// if  NOT exists on base features, then this is a THEME-ONLY ( if in parent ) OR a CUSTOM ( if in child ) Plethora feature class
			} else {

				$index[$key]['plethora_supported'] = $index[$key]['location'] === 'parent' ? true : false;
				$index[$key]['class']              = $unfiltered_theme_features[$key]['class'];
				$index[$key]['base_class']         = false;
				$index[$key]['base_path']          = false;
			}

			// Load feature classes files to collect the rest index information
			$feature_base_path = $index[$key]['base_path'];
			$feature_path      = $index[$key]['path'];
			$feature_class     = $index[$key]['class'];
			if ( $feature_base_path ) { require_once( $feature_base_path ); }  // load base class
			if ( $feature_path ) { require_once( $feature_path ); }  // load feature class
			if ( class_exists( $feature_class ) ) {

				$feature_class_info                         = get_class_vars( $feature_class );
				$index[$key]['verified']                    = true; // VERY IMPORTANT...if not set, the feature will not work!
				$index[$key]['feature_title']               = isset( $feature_class_info['feature_title'] ) ? $feature_class_info['feature_title'] : esc_html__('No title found', 'plethora-framework') ;
				$index[$key]['feature_description']         = isset( $feature_class_info['feature_description'] ) ? $feature_class_info['feature_description'] : esc_html__('No description found', 'plethora-framework');
				$index[$key]['theme_option_control']        = isset( $feature_class_info['theme_option_control'] ) ? $feature_class_info['theme_option_control'] : true;
				$index[$key]['theme_option_default']        = isset( $feature_class_info['theme_option_default'] ) ? $feature_class_info['theme_option_default'] : true;
				$index[$key]['theme_option_requires']       = isset( $feature_class_info['theme_option_requires'] )? $feature_class_info['theme_option_requires']	: array();
				$index[$key]['wp_slug']                     = isset( $feature_class_info['wp_slug'] ) ? $feature_class_info['wp_slug'] : '';
				$index[$key]['assets']                      = isset( $feature_class_info['assets'] ) ? $feature_class_info['assets'] : array();
				$index[$key]['dynamic_construct']           = isset( $feature_class_info['dynamic_construct'] ) ? $feature_class_info['dynamic_construct'] : true;
				$index[$key]['dynamic_method']              = isset( $feature_class_info['dynamic_method'] ) ? $feature_class_info['dynamic_method'] : false;
				$index[$key]['theme_option_status']         = $index[$key]['theme_option_control'] ? self::get_status_option( $index[$key]['controller'], $index[$key]['slug'] ) : true;
			
			} else {

				$index[$key]['verified'] = false;
			}
		}
		ksort( $index );
		$this->update( $index );
  	}

  	/**
  	* Set theme options controls for each feature
  	* Notice: is done after set() method, as it utilizes the features index
  	*
  	* @return array 
  	* @since 2.0
  	*/
  	private function set_theme_options() {

  		if ( is_admin() ) {
	  		$index = $this->get();

	  		foreach ( $index as $key => $feature ) {
				if ( isset( $index[$key]['theme_option_control']  ) ) {

					$index[$key]['theme_option_control_config'] = $index[$key]['theme_option_control'] ? $this->set_feature_redux_option( $index[$key] ) : array();
				}
	  		}

			$this->update( $index );
		}
  	}

  	/**
  	* INDEX | Update features index 
  	*
  	* @since 2.0
  	*/
  	private function update( $index ) {

  		$this->index = $index;
  	}

    /**
    * Returns option configuration for theme settings status DB option. Must comply with Redux framwork 
    * @return boolean
    */
    private function set_feature_redux_option( $feature ) {

    	if ( is_admin() ) {	
	      	// Prepare description
			$subtitle  = ! $feature['plethora_supported'] ?  esc_html__('Custom Feature / ', 'plethora-framework') : esc_html__('Plethora Library Feature / ', 'plethora-framework');
			$subtitle  .=  $feature['location'] === 'parent' ?  esc_html__('Parent Theme', 'plethora-framework') : esc_html__('Child Theme', 'plethora-framework');
			$required_text  ='';
			if (!empty( $feature['theme_option_requires'] ) ) { 

				$required_text  .=  esc_html__('Requires activation of: ', 'plethora-framework');
				$required_arg = array();
				$counter = 0;
				$index = $this->get();
				foreach ( $feature['theme_option_requires'] as $required_controller_slug => $required_feature_slug ) {

					$counter = $counter + 1;
					$required_text .= $counter > 1 ? ' | ' : '';
					$required_text .= '<b>'. $index[$required_controller_slug .'-'. $required_feature_slug ]['feature_title'] .'</b>';
					// for later 'required' argument use
					$required_arg[] = array( THEMEOPTION_PREFIX . $required_controller_slug .'-'. $required_feature_slug .'-status','equals','1');
				}
			}

			// Set option configuration array
			$option = array(
				'id'       =>''. THEMEOPTION_PREFIX . $feature['controller'] .'-'. $feature['slug'] .'-status',
				'type'     => 'switch',
				'title'    => $feature['feature_title'],
				'subtitle' => $subtitle,
				'desc'     => $required_text,
				'on'       => esc_html__('Activated', 'plethora-framework'),
				'off'      => esc_html__('Deactivated', 'plethora-framework'),
				'default'  => $feature['theme_option_default'],
			);
			// Add 'required' argument, if not empty
			if ( !empty( $required_arg ) ) { $option['required'] = $required_arg; }

			return $option;
		}  
    }

    /**
    * Loads framework controllers
    * @return boolean
    */
	private function load_controller( $controller_slug ) {

		$return = array();
		$controller_classname = self::get_classname_by_slug( $controller_slug ); 
		$controller_filepath  = self::get_filepath_by_slug( $controller_slug ); 

		$return['slug'] = $controller_slug;
		$return['path'] = $controller_filepath;
		$return['class'] = $controller_classname;

		if ( !empty( $controller_filepath )) {	

			require_once( $controller_filepath );

			if ( class_exists( $controller_classname )) { 

				$class_info                         = get_class_vars( $controller_classname );
				$return['title']                    = isset( $class_info['controller_title'] ) ? $class_info['controller_title'] : esc_html__( 'No title found', 'plethora-framework' ) ;
				$return['description']              = isset( $class_info['controller_description'] ) ? $class_info['controller_description'] : esc_html__( 'No description found', 'plethora-framework' );
				$return['construct']                = isset( $class_info['controller_dynamic_construct'] ) ? $class_info['controller_dynamic_construct'] : false;
				$return['method']                   = isset( $class_info['controller_dynamic_method'] ) ? $class_info['controller_dynamic_method'] : false;
				$return['file_loaded']              = false;
				$return['features_location_parent'] = isset( $class_info['controller_dynamic_method'] ) ? $class_info['controller_dynamic_method'] : false;
				$return['features']                 = isset( $class_info['dynamic_features_loading'] ) ? $class_info['dynamic_features_loading'] : true;

				// Instantiate class
				if ( $return['construct'] ) {

					$controller = new $controller_classname;
					// Call a class method 
					if ( $return['method'] !== false ) { 

						if ( method_exists( $controller, $return['method'] ) ) {

							call_user_func( array( $controller, $return['method'] ) );
						} 
					}
				}
			} 
		}

		return $return;
	}

    /**
    * Handles all theme features loading
    * @return boolean
    */
	public function load_feature( $feature ) {

		extract( $feature );
		// Load file & class and extract related info
		if ( !empty( $path ) && $theme_option_status ) {

			if ( $base_path ) { require_once( $base_path ); }  // load base class file first!
			require_once( $path ); 							   // load main class file

			if ( class_exists( $class )) { 

				// Hook right after class file loaded succesfully
				// do_action( strtolower( $class ) .'_class_loaded');

				global $plethora;
				// Inititate class dynamically
				if ( $dynamic_construct ) {

					$instance = new $class;
					$plethora['instances'][$class] = $instance;
					// $this->events_log[] = $feature_class . esc_html__( ' class was instantiated dynamically.', 'plethora-framework' );

					// Invoke requested method
					if ( $dynamic_method !== false && !empty($dynamic_method) ) { 

						if ( method_exists( $instance, $dynamic_method ) ) {

							call_user_func( array( $instance, $dynamic_method ) );
						} 
					}
				} 
			} 
		}  
	}

/// PRIVATE METHODS END

/// PUBLIC METHODS START

  	/**
  	* INDEX | Get features index
  	*
  	* @return array 
  	* @since 2.0
  	*/
  	public function get() {

  		$index = $this->index;
   		return $index;
  	}

    /**
    * Returns activation status saved in theme settings DB option 
    * @return boolean
    */
    public static function get_status_option( $controller_slug, $feature_slug ) {    	

        $option_name = THEMEOPTION_PREFIX . $controller_slug .'-'. $feature_slug .'-status';    // SET OPTION NAME
        $status      = Plethora_Theme::option( $option_name, true );                            // GET OPTION SAVED VALUE FROM DB
    	return $status;
    }

    /**
    * Loads framework controllers
    * @return boolean
    */
	public function load_controllers() {

		$return = array();
		$controllers = $this->controllers;
		foreach ( $controllers as $controller_slug ) {

			$return[$controller_slug] = $this->load_controller( $controller_slug );
		}

		$this->controllers = $return;
	}

    /**
    * Handles all theme features loading
    * @return boolean
    */
	public function load_features() {

		$index = $this->get();
		foreach ( $index as $key => $feature ) {

			// No need to send it if inactive
			if ( $feature['verified'] ) {

				$this->load_feature( $feature );
			}
		}
	}
/// PUBLIC METHODS END 

/// HELPER METHODS START
	public static function get_classname_by_slug( $controller_slug, $feature_slug = '') {

		if ( empty( $controller_slug ) ) { return; }
		if ( empty( $feature_slug ) ) {

	    	return CC_PREFIX . ucfirst( strtolower( $controller_slug ));

		} else {

	    	return CC_PREFIX . ucfirst( $controller_slug ) .'_'. ucfirst( $feature_slug );
		}
	}

	public static function get_filename_by_slug( $controller_slug, $feature_slug = '') {

		if ( empty( $controller_slug ) ) { return; }
		if ( empty( $feature_slug ) ) {

			$controller_slug = strtolower($controller_slug);
			return CF_PREFIX . lcfirst( $controller_slug ) .'.php';

		} else {

	    	return strtolower( $controller_slug .'-'. $feature_slug .'.php' );
		}
	}

	public static function get_filepath_by_slug( $controller_slug, $feature_slug = '') {

		if ( empty( $controller_slug ) ) { return; }
		if ( empty( $feature_slug ) ) {

			$filename = self::get_filename_by_slug( $controller_slug );

			// check features library plugin first
			if ( Plethora_Theme::is_library_active() && file_exists( PLE_FLIB_CONTROLLERS_DIR . '/'. $filename )) {

				return PLE_FLIB_CONTROLLERS_DIR . '/'. $filename;
			}

			// check core on theme
			if ( file_exists( PLE_CORE_CONTROLLERS_DIR . '/'. $filename )) {

				return PLE_CORE_CONTROLLERS_DIR . '/'. $filename;
			}

			return;

		} else {

			$filename = self::get_filename_by_slug( $controller_slug, $feature_slug );                   // GET FEATURE FILENAME
			$location = self::get_location( $controller_slug, $feature_slug );
	        $filepath = '/' . $controller_slug . '/' . $feature_slug . '/' . $filename;

			if ( $location === 'child') return PLE_CHILD_FEATURES_DIR . $filepath;              // CHECK CHILD THEME FIRST
			if ( $location === 'parent') return PLE_THEME_FEATURES_DIR . $filepath;             // CHECK PARENT THEME
			if ( $location === 'core') return PLE_CORE_FEATURES_DIR . $filepath;   // CHECK CORE
			if ( $location === 'library') return PLE_FLIB_FEATURES_DIR . $filepath;   // CHECK FEATURES LIB

			return '';
		}
	}

    /**
    * Returns feature file full path VALID location. Checks in order the child, parent and framework feature folders
    * @return string
    */
	public static function get_location( $controller_slug, $feature_slug ) {

		$filename = self::get_file_name( $controller_slug, $feature_slug );       // GET FEATURE FILENAME
        $filepath = '/' . $controller_slug . '/' . $feature_slug . '/' . $filename;

		if ( file_exists( PLE_CHILD_FEATURES_DIR . $filepath ) && is_child_theme() ) return 'child';    // CHECK CHILD THEME FIRST
		if ( file_exists( PLE_THEME_FEATURES_DIR . $filepath )) return 'parent';                        // CHECK PARENT THEME

		return esc_html__('Location not found', 'plethora-framework');
	}

	/**
	 * (needs description)
	 * 
	 * @param 
	 * 
	 */
	public static function locate_feature_files( $controller, $location ) {

		switch ( $location ) {
		 	
		 	case 'library':
				$features_folder = PLE_FLIB_FEATURES_DIR . '/'. $controller['slug'];
		 		break;
		 	
		 	case 'core':
				$features_folder = PLE_CORE_FEATURES_DIR . '/'. $controller['slug'];
		 		break;
		 	
		 	case 'parent':
				$features_folder = PLE_THEME_FEATURES_DIR . '/'. $controller['slug'];
		 		break;
		 	
		 	case 'child':
				$features_folder = PLE_CHILD_FEATURES_DIR . '/'. $controller['slug'];
		 		break;
		 } 

		if ( ! is_dir( $features_folder ) ) return array();

		$features = scandir( $features_folder );

		$return = array();

		foreach ( $features as $feature ) {

			$feature_folder = $features_folder .'/'. $feature;
			$feature_file = $features_folder .'/'. $feature .'/'. $controller['slug'] .'-'. $feature .'.php';
			if ( is_dir( $feature_folder ) && file_exists( $feature_file ) && ( $feature !== '.' && $feature !== '..' ) ) {

				$return[$controller['slug'].'-'.$feature]['location']   = $location;
				$return[$controller['slug'].'-'.$feature]['controller'] = $controller['slug'];
				$return[$controller['slug'].'-'.$feature]['slug']       = $feature;
				$return[$controller['slug'].'-'.$feature]['class']      = CC_PREFIX . ucfirst( strtolower( $controller['slug'] ) ) . '_' . ucfirst( strtolower( $feature ) );
				$return[$controller['slug'].'-'.$feature]['folder']		= $feature_folder;
				$return[$controller['slug'].'-'.$feature]['path']       = $feature_file;
			}
		}

		return $return ;
	}
/// HELPER FUNCTIONS END
}