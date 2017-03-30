<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Controller class for dashboard widgets

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Dashboard') ) {

    /**
     * @package Plethora Controllers
     */
	class Plethora_Dashboard {

        public static $controller_title             = 'Dashboard Widgets Manager';      // CONTROLLER TITLE
        public static $controller_description       = 'Activate/deactivate any Plethora dashboard available. Notice that on deactivation, all dependent features will be deactivated automatically.';       // Controller description
        public static $controller_dynamic_construct = false;                            // DYNAMIC CLASS CONSTRUCTION 
        public static $controller_dynamic_method    = false;                            // INVOKE ANY METHOD AFTER DYNAMIC CONSTRUCTION? ( method name OR false )
        public static $dynamic_features_loading     = true;                             // LOAD FEATURES DYNAMICALLY ( always true, false if stated so in controller variables )

	    public $widget_id;                         // Widget ID
	    public $widget_name;                       // Dashboard widget name
	    public $dashboard_scripts;                 // Dashboard widget related script files
	    public $dashboard_styles;                  // Dashboard widget style files
	    public $add_script;                        // Dynamic scripts switch value. It is checked inside callback(). If set to true, it triggers script printing in footer


        /** 
        * Add shortcode action
        */
        public function add( $widget_id, $widget_name) {

        	$this->widget_id	= $widget_id;
        	$this->widget_name	= $widget_name;
        	add_action('wp_dashboard_setup', array( $this, 'action' ) );

        }

        /** 
        * Add shortcode action
        */
        public function action() {

        	$widget_id			= $this->widget_id;
        	$widget_name		= $this->widget_name;
        	$control_callback	= method_exists($this, 'control_callback') ? array( $this, 'control_callback' ) : null;
        	$callback_args 		= method_exists($this, 'callback_args') ? array( $this, 'callback_args' ) : null;

        	wp_add_dashboard_widget( $widget_id, $widget_name, array( $this, 'callback'), $control_callback, $callback_args );

		 	global $wp_meta_boxes;
		 	
            $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];          // GET THE REGULAR DASHBOARD WIDGETS ARRAY (WHICH HAS OUR NEW WIDGET ALREADY BUT AT THE END)
            $widget_backup    = array( $widget_id => $normal_dashboard[$widget_id] );   // BACKUP AND DELETE OUR NEW DASHBOARD WIDGET FROM THE END OF THE ARRAY
		 	unset( $normal_dashboard[$widget_id] );
		 
		 	$sorted_dashboard = array_merge( $widget_backup, $normal_dashboard );       // MERGE THE TWO ARRAYS SO OUR WIDGET IS AT THE BEGINNING
		 	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;          // SAVE SORTED ARRAY BACK INTO THE ORIGINAL METABOXES 

        }
 
        /** 
        * It contains the dashboard widget's content. Should be overriden on extending classes
        */
        public function callback() {

        	echo esc_html__('This is the default dashboard content! You can override this content using the <strong>content</strong> method of your dashboard class', 'plethora-framework');

        }

		/**
		* Dashboard widget script actions
		*/
		public function add_script( $scripts ) {

			$this->dashboard_scripts = $scripts;                                     // Assign $scripts to shortocode_scripts variable
			add_action( 'init', array( $this, 'register_script' ));     // Add script registration hooks

		}		

		/**
		 * Dashboard widget script registrations
		 */
		public function register_script() {

			$scripts = $this->dashboard_scripts;         // GET SCRIPTS PARAMETERS

			if (is_array($scripts)) { 

				foreach ($scripts as $script) { 

					// Make sure that required parameters are ok
					if ( !empty($script['handle']) ) { 

						if ( !empty($script['src']) ){

							// Fixing parameters for wp_register_script ( https://codex.wordpress.org/Function_Reference/wp_register_script )
							$handle 	= $script['handle']; 
							$src 		= $script['src']; 
							$deps 		= ( !isset( $script['deps'] ) || !is_array( $script['deps'] )) ? array() : $script['deps'];
							$ver 		= ( !isset( $script['ver'] ) || !is_string( $script['ver'] )) ? false : $script['ver'] ;
							$in_footer 	= ( !isset( $script['in_footer'] ) || !is_bool( $script['in_footer'] )) ? false : $script['in_footer'] ;

			        		wp_register_script( $handle, $src, $deps, $ver, $in_footer );     // REGISTER SCRIPT
			        		wp_enqueue_script( $handle );

						} elseif ( isset( $script['type'] ) && $script['type'] === 'localized_script' ){

							$handle   = $script['handle']; 
							$variable = $script['variable']; 
							$data     = ( !isset( $script['data'] ) || !is_array( $script['data'] )) ? array() : $script['data'];

							wp_localize_script( $handle, $variable, $data );    // LOCALIZE SCRIPT

						} 


		        	}
		        }
			}
		}

	   /**
        * Dashboard widget style action
	    *
	    * @since 1.0
	    *
	    */

       public function add_style( $style ) {

			$this->dashboard_styles = $style;                                    // ASSIGN $SCRIPTS TO SHORTOCODE_STYLE VARIABLE
			add_action( 'init', array( $this, 'register_style' ));  // ADD STYLE REGISTRATION HOOKS

       }		


	   /**
	    * Dashboard widget styles registration
	    *
	    * @since 1.0
	    *
	    */
       public function register_style() {

			$styles = $this->dashboard_styles;

			if ( is_array($styles) ) { 

				foreach ($styles as $style) { 

					if ( !empty($style['handle']) && !empty($style['src']) ) { 

						// Fixing parameters for wp_register_style ( http://codex.wordpress.org/Function_Reference/wp_register_style )
						$handle = $style['handle']; 
						$src	= $style['src']; 
						$deps	= ( !isset( $style['deps'] ) || !is_array( $style['deps'] )) ? array() : $style['deps'];
						$ver	= ( !isset( $style['ver'] ) || !is_string( $style['ver'] )) ? false : $style['ver'] ;
						$media 	= ( !isset( $style['media'] ) || !is_string( $style['media'] )) ? 'all' : $style['media'] ;

		        		wp_register_style ( $handle, $src, $deps, $ver, $media );     // REGISTER STYLE
		         		wp_enqueue_style  ( $handle );                                // ENQUEUE STYLE

		        	}
		        }
			}
       }


	}

}