<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Icons Module Base Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( !class_exists('Plethora_Module_Mediapanel') ) {

	class Plethora_Module_Icons {

		public static $feature_title        = "Font Icons Library v2";
		public static $feature_description  = "Font icon libraries management";
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		private static $assets_dir;
		private static $assets_url;
		private static $css_dir;
		private static $css_url;
		private static $fonts_dir;
		private static $fonts_url;
		private static $minified = true;
		private static $db_option = 'plethora_icon_libraries';
		private static $global_stylesheet = 'plethora_icons.css';
		private static $libraries = array();
		private static $temp_icons;
		private static $temp_vc_library_slug;

		public $theme_options_tab_static = 2;

		public $fontawesome_status           = true;
		public $lineabasic_status            = false;
		public $lineamusic_status            = false;
		public $lineaecommerce_status        = false;
		public $lineasoftware_status         = false;
		public $lineaarrows_status           = false;
		public $lineaweather_status          = false;
		public $webfont_medical_icons_status = false;
		public $weather_icons_status         = false;
		public $stroke_gap_icons_status      = false;
		public $plethora_hotel_status        = false;

		public function __construct() {

            // Theme adjustments if the library plugin is inactive
            if ( ! Plethora_Theme::is_library_active() ) {

                add_action( 'wp_enqueue_scripts', array($this, 'library_inactive_font_awesome' ), 5);
                return;
            }

			$assets_directory = $this->initialize_assets_directory();
			
			if ( $assets_directory ) { 

				// Recompile libraries ( if asked by user )
				add_action( 'redux/options/'.THEME_OPTVAR.'/reset', array( $this, 'recompile_libraries' )); 			// fix for redux ajax save on reset
				add_action( 'redux/options/'.THEME_OPTVAR.'/section/reset', array( $this, 'recompile_libraries' ));		// fix for redux ajax save on section reset
				add_action( 'redux/options/'.THEME_OPTVAR.'/saved', array( $this, 'recompile_libraries' ));				// fix for redux ajax save on normal save
				// Recompile libraries ( if file still not there! File_exists check is done on recompile_libraries() method )
				add_action( 'wp_loaded', array( $this, 'recompile_libraries' ));				// fix for redux ajax save on normal save

				// Initialize libraries
				// Must be hooked on 'init', otherwise the redux icon selector will not be able to work
				// The side-effects would be that it will show the correct updated icons list RIGHT after the first pageload
				add_action( 'init', array( $this, 'initialize' ), 0);	// Initialize icons list

				// Compatibility with VC iconpickers
    			add_filter( 'wp_loaded', array( $this, 'vc_iconpicker_add' ) );
    			add_filter( 'vc_after_init', array( $this, 'vc_icon_append_libraries_to_dropdown' ) );
				add_action( 'init', array( $this, 'vc_icon_add_libraries_iconpickers' ));
    			add_filter( 'wp_loaded', array( $this, 'vc_icon_iconpickers_add' ) );


				// Enqueue libraries, both in front/admin
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_libraries' ), 20);  // All features should enqueue on 20 priority
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_libraries' ), 1); 
            }

			// Set theme options tab for media panel
			add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_options_tab'), 40);

			// Some diagnostics and admin notices
			add_action( 'admin_notices', array( $this, 'diagnostics_check_directory' ) );
			add_action( 'admin_notices', array( $this, 'diagnostics_check_version' ) );
			if ( method_exists( $this, 'admin_notices_ext' ) ) { $this->admin_notices_ext(); }
		}


	    public function library_inactive_font_awesome() {

	        wp_enqueue_style( 'font-awesome', PLE_THEME_ASSETS_URI .'/fonts/font-awesome/css/font-awesome.min.css' );
	    }
		
		public function diagnostics_check_directory() {

			$check_directory = $this->initialize_assets_directory();
			if ( $check_directory != true ) { 

				$notice = '<div class="notice notice-error is-dismissible" style="padding-bottom:10px;">';
				$notice .= '<p>';
				$notice .= '<h4 style="margin-bottom:0;">'. esc_html__( 'Icon Libraries feature is not working.', 'plethora-framework') .'</h4>';
				$notice .= esc_html__('It seems that your uploads directory is not writable.', 'plethora-framework' );
				$notice .= esc_html__('We suggest solving this issue before start working on your website.', 'plethora-framework' );
				$notice .= esc_html__('After setting up the correct file permissions for your uploads directory, you should go to Theme Options page and click on the \'Reset All\' button. ', 'plethora-framework' );
				$notice .= esc_html__('This will recover icon libraries and all related functionality...and eliminate this message too!', 'plethora-framework' );
				$notice .= '</p>';
				$notice .= '</div>';
				echo wp_kses_post( $notice );
			}
		}

		public function diagnostics_check_version() {

			$check_version  = get_option( GENERALOPTION_PREFIX .'module_icons_version', 'v1' );
			if ( $check_version === 'v1' ) { 

				$notice = '<div class="notice notice-error is-dismissible" style="padding-bottom:10px;">';
				$notice .= '<h4 style="margin-bottom:0;">'. esc_html__( 'Icon Libraries need reset!', 'plethora-framework') .'</h4>';
				$notice .= '<p>';
				$notice .= esc_html__('Due to a major update in the Icon Libraries feature, you should make sure that your installation is in tune with these updates ', 'plethora-framework' )  .'<br>';
				$notice .= sprintf( esc_html__('So, we advise you to visit %1$sTheme Options > Advanced > Icons Library%2$s panel and click on the %1$sReset Section%2$s button. ', 'plethora-framework' ), '<strong>', '</strong>' )  .'<br>';
				$notice .= esc_html__('This action will recompile the icon library stylesheet according to the latest changes. ', 'plethora-framework' );
				$notice .= esc_html__('After reseting the section, this message will disappear.', 'plethora-framework' );
				$notice .= '</p>';
				// $notice .= '<a href="'. esc_url( admin_url( 'admin.php/?page='.THEME_OPTVAR.'') ) .'&plethora_module_icons_diagnostics_v2=hide">'. esc_html__( 'Dismiss this notice', 'plethora-framework' ) .'</a>';
				$notice .= '</div>';
				
				if ( isset( $_GET['plethora_module_icons_diagnostics_v2'] ) && sanitize_key( $_GET['plethora_module_icons_diagnostics_v2'] ) === 'hide' ) {

					update_option( GENERALOPTION_PREFIX .'plethora_module_icons_diagnostics_v2', 0 );
				}

				$diagnostics = get_option( GENERALOPTION_PREFIX .'plethora_module_icons_diagnostics_v2', 1 );

				if ( $diagnostics ) {

					echo wp_kses_post( $notice );
				}
			}
		}

		/**
		* Recompile libraries if necessary
		*/
		public function recompile_libraries() {

			if ( self::is_on_recompile() || ! file_exists( self::$assets_dir .'/'. self::$global_stylesheet ) && Plethora_Theme::is_library_active()  ) {
				
				update_option( GENERALOPTION_PREFIX .'module_icons_uniqeid', uniqid() );
				// Recompile libraries
				self::register_libraries();
				// Set v2 option, we are fine now!
				update_option( GENERALOPTION_PREFIX .'module_icons_version', 'v2' );
			} 
		}

		/**
		* Gets saved libraries and updates related class attributes accordingly
		*/
		public function initialize() {

			$db_libraries = get_option( self::$db_option, array() );
			$db_icons = array();
			if ( is_array( $db_libraries )) { 
				foreach ( $db_libraries as $library_slug => $library_args ) {
					$db_icons[$library_slug] = $library_args['icons'];
				}
			}
			self::$libraries  = $db_libraries;
			self::$temp_icons = $db_icons;
		}


// Font Libraries Management Methods ---> START

		/**
		* Create/check if assets directory is there!
		* @return boolean
		*/
		public function initialize_assets_directory() {

			$upload_folder = wp_upload_dir();

			// Check & create directories
			$assets_dir =  $upload_folder['basedir'] .'/plethora';
			$assets_url =  $upload_folder['baseurl'] .'/plethora';
			$css_dir    =  $upload_folder['basedir'] .'/plethora/css';
			$css_url    =  $upload_folder['baseurl'] .'/plethora/css';
			$fonts_dir  =  $upload_folder['basedir'] .'/plethora/webfonts';
			$fonts_url  =  $upload_folder['baseurl'] .'/plethora/webfonts';
			// validate-create and set folder dir/url attributes
			if (wp_mkdir_p( $assets_dir )) {
			  
			  // Set class attributes
			  self::$assets_dir = $assets_dir;
			  self::$assets_url = $assets_url;

			  // Create 'css' subdirectory
			  if (wp_mkdir_p( $css_dir )) {

				  self::$css_dir = $css_dir;
				  self::$css_url = $css_url;
			  }

			  // Create 'fonts' subdirectory
			  if (wp_mkdir_p( $fonts_dir )) {

				  self::$fonts_dir = $fonts_dir;
				  self::$fonts_url = $fonts_url;
			  }

			  return true;
			}
			return false;
		}

		private function initialize_global_stylesheet() {

			  // If theme options are saved OR file not exist, then overwrite/create an empty global stylesheet
			  if ( self::is_on_recompile() || ! file_exists( self::$assets_dir .'/'. self::$global_stylesheet ) ) {

		  		$css = "/*\n";
		  		$css .= "PLETHORA ICONS LIBRARY\n";
		  		$css .= "This file was created automatically by Plethora Icons Module class\n";
		  		$css .= "*/\n";
				Plethora_WP::write_to_file( self::$assets_dir .'/'. self::$global_stylesheet, $css );
			  }
			  
			  return file_exists( self::$assets_dir .'/'. self::$global_stylesheet );
		}

		private function register_libraries() {

			// First of all, recompile and check the stylesheet
			$stylesheet_is_ok      = self::initialize_global_stylesheet();
			if ( !$stylesheet_is_ok ) { return; } // if global stylesheet not exists, then no point to continue
			
			// Empty db option
			self::update_libraries( array() );

			/* Prepare the preset option set libraries. 
			   Note: Preset options for user are restricted to status,slug and title. 
					 The rest value fields are merged with the public ones on this point,
					 by calling the self::preset_iconlibraries( 'all' ) method )
			*/
			$preset_iconlibraries = $this->preset_iconlibraries();
			$preset_iconlibraries = Plethora_Theme::option( THEMEOPTION_PREFIX .'iconlibraries-preset', $preset_iconlibraries );
			$preset_iconlibraries = array_merge( $preset_iconlibraries, $this->preset_iconlibraries( 'all' ) ); 
			
			// Merge preset and user defined libraries
			$user_iconlibraries = Plethora_Theme::option( THEMEOPTION_PREFIX .'iconlibraries', array() );
			$user_iconlibraries = is_array($user_iconlibraries) ? $user_iconlibraries : array();
			$iconlibraries = array_merge_recursive( $user_iconlibraries, $preset_iconlibraries );
			
			// Start verification/registration procedure;
			$css = '';
			$libraries = array();
		    if (  isset( $iconlibraries['id'] ) ) { 
			    foreach ($iconlibraries['id'] as $key => $library_slug ) {

					// Register saved library
					$stylesheets   = array();
					$stylesheets[] = isset($iconlibraries['stylesheet1'][$key]) ? $iconlibraries['stylesheet1'][$key] : '';
					$stylesheets[] = isset($iconlibraries['stylesheet2'][$key]) ? $iconlibraries['stylesheet2'][$key] : '';
					$fontfiles     = array();
					$fontfiles['src_eot']   = $iconlibraries['src_eot'][$key];
					$fontfiles['src_otf']   = $iconlibraries['src_otf'][$key];
					$fontfiles['src_svg']   = $iconlibraries['src_svg'][$key];
					$fontfiles['src_ttf']   = $iconlibraries['src_ttf'][$key];
					$fontfiles['src_woff']  = $iconlibraries['src_woff'][$key];
					$fontfiles['src_woff2'] = $iconlibraries['src_woff2'][$key];
					$sanitized_library_slug = sanitize_title($library_slug) ;
					self::update_libraries_option_args( $library_slug, array('id' => $sanitized_library_slug ));
					$libraries[$sanitized_library_slug] = array( 
							'status'          => $iconlibraries['status'][$key],     
							'title'           => $iconlibraries['title'][$key],
							'id'              => $iconlibraries['id'][$key],
							'class_prefix'    => $iconlibraries['class_prefix'][$key],
							'selector_prefix' => $iconlibraries['selector_prefix'][$key], 		// stylesheet class prefix ( used for auto-scan )
							'selector_suffix' => $iconlibraries['selector_suffix'][$key],	// stylesheet class suffix ( used for auto-scan )
							'stylesheets'     => $stylesheets,
							'fontfiles'       => $fontfiles,
							'font-family'     => $iconlibraries['font-family'][$key],
							'font-style'      => $iconlibraries['font-style'][$key],
							'font-weight'     => $iconlibraries['font-weight'][$key],
							'font-stretch'    => $iconlibraries['font-stretch'][$key],
					);
			     }
			}

			// Apply hooks
			$libraries = apply_filters('plethora_module_icons_libraries', $libraries );

			// Register libraries after validation
			foreach ( $libraries as $library_slug => $library_args ) {

				// If status is true, we should register it
				if ( $library_args['status'] ) {

					// Validate styles and fonts
					// Note: during this procedure, the global stylesheet is compiled and font files are transferred to uploads/plethora/fonts folder
					$validated_fontfiles   = self::validate_fontfiles( $library_slug, $library_args );
					$validated_stylesheets = self::validate_stylesheets( $library_slug, $library_args );

					// if something went wrong with validation, keep this library off
					if ( $validated_stylesheets === false || $validated_fontfiles === false  ) {

						self::update_libraries_option_args( $library_slug, array('status' => false ));
						$library_args['status'] = false;

					}
					// Register this library
					self::register_library( $library_slug, $library_args );

				} elseif ( ! $library_args['status'] ) {

					self::unregister_library( $library_slug );
				}
			}

			self::remove_junk_css_files();
			self::remove_junk_font_files();
		}

		private function validate_fontfiles( $library_slug, $library_args ) {

			// Transfer web font files to assets directory
			$fontfiles       = $library_args['fontfiles'];
			$validated_files = array();
			foreach ( $fontfiles as $key => $fontfile ) {

				if ( !empty( $fontfile )) { 

					$fontfile_dir = self::$fonts_dir .'/'. basename($fontfile);
					$validated    = self::storeToUploadsDir( $fontfile, $fontfile_dir );
					if ( $validated ) { 

						$validated_files[$key] = self::$fonts_url .'/'. basename($fontfile);
					}
				}
			}

			// Append font-face rules to global stylesheet
			$global_css = '';
			if ( !empty($validated_files) && !empty($library_args['font-family']) && $library_args['status'] ) { 

				$global_css .= Plethora_WP::get_file_contents( self::$assets_dir .'/'. self::$global_stylesheet );
		  		$global_css .= "\n\n/* ";
		  		$global_css .= $library_args['title'] ." ";
		  		$global_css .= "*/";

				// Remove @font-face rules
				$regex = '/\@';
				$regex .= 'font-face[^}]*\}/';
				$found = preg_match_all( $regex, $global_css, $matches);
				foreach ( $matches[0] as $key => $fontface ) {

					$css = str_replace($fontface, '', $global_css);
				}
				$font_css = '';
				$font_css .= "\n@font-face { \n";
				$font_css .= "	    font-family: '".$library_args['font-family']."';\n";

				foreach ( $validated_files as $key => $fontfile ) {

					  $fontfile = './webfonts/'. basename( $fontfile );

					  if ( $key === 'src_eot' && !empty( $fontfile ) ) { 
					  $font_css .= "	    src: url('". $fontfile ."');\n";
					  $font_css .= "		src: url('". $fontfile ."?#iefix') format('embedded-opentype'),\n";
					  } 
					  if ( $key === 'src_otf' && !empty( $fontfile ) ) { 
					  $font_css .= "			 url('". $fontfile ."') format('opentype'),\n";
					  }
					  if ( $key === 'src_svg' && !empty( $fontfile ) ) { 
					  $font_css .= "		     url('". $fontfile ."') format('svg'),\n";
					  }
					  if ( $key === 'src_ttf' && !empty( $fontfile ) ) { 
					  $font_css .= "		     url('". $fontfile ."') format('truetype'),\n";
					  }
					  if ( $key === 'src_woff' && !empty( $fontfile ) ) { 
					  $font_css .= "		     url('". $fontfile ."') format('woff'),\n";
					  }
					  if ( $key === 'src_woff2' && !empty( $fontfile ) ) { 
					  $font_css .= "		     url('". $fontfile ."') format('woff2'),\n";
					  } 
				}
				$font_css = rtrim($font_css, "\n,") . ";\n";
			    $font_css .= "		font-style: ".$library_args['font-style']."\n";
			    $font_css .= "		font-weight: ".$library_args['font-weight'].";\n";
			    $font_css .= "		font-stretch: ".$library_args['font-stretch'].";\n";
			    $font_css .= "}\n";


				$font_css .= ".".trim($library_args['class_prefix'])." {\n";
				$font_css .= "  display: inline-block;\n";
				$font_css .= "  font-family: '".$library_args['font-family']."';\n";
				$font_css .= "  font-style: ".$library_args['font-style'].";\n";
				$font_css .= "  font-weight: ".$library_args['font-weight'].";\n";
				$font_css .= "  font-stretch: ".$library_args['font-stretch'].";\n";
				$font_css .= "  font-size: inherit;\n";
				$font_css .= "  text-rendering: auto;\n";
				$font_css .= "  -webkit-font-smoothing: antialiased;\n";
				$font_css .= "  -moz-osx-font-smoothing: grayscale;\n";
				$font_css .= "  transform: translate(0, 0);\n";
				$font_css .= "}\n";

				$global_css .= $font_css;
				return Plethora_WP::write_to_file( self::$assets_dir .'/'. self::$global_stylesheet , $global_css );
			}

			return false;

		}

		/**
		* Stylesheets validation: striping CSS content from font-face rules and save it to uploads dir
		* @param library_slug, $library_args
		* @return library's arguments, with 'stylesheets' argument updated according to validation ( validated urls OR false)
		*
		*/
		public function validate_stylesheets( $library_slug, $library_args ) {

			$cssfiles = $library_args['stylesheets'];
			$validated_files = array();
			foreach ( $cssfiles as $key => $cssfile ) {

				if ( !empty( $cssfile )) { 

					$cssfile_dir = self::$css_dir .'/'. basename($cssfile);
					$validated   = self::storeToUploadsDir( $cssfile, $cssfile_dir );
					if ( $validated ) { 

						$validated_files[$key] = $cssfile_dir;
					}
				}
			}

			$global_css = '';
	  		if ( !empty($validated_files) && $library_args['status'] ) { 
				// Get global stylesheet contents
				$global_css .= Plethora_WP::get_file_contents( self::$assets_dir .'/'. self::$global_stylesheet );
				$library_icons = array();
				$icon_css = '';
				foreach ( $validated_files as $key => $stylesheet ) { 
		  			$css = '';
					if ( !empty($stylesheet) ) {

						// Get stylesheet contents
						$css = Plethora_WP::get_file_contents( $stylesheet, array( 'context' => PLE_CORE_FEATURES_DIR ) );
						if ( empty( $css ) ) { 
							continue; 
						}
						// Remove @font-face rules
						$regex = '/\@';
						$regex .= 'font-face[^}]*\}/';
						$found = preg_match_all( $regex, $css, $matches);
						foreach ( $matches[0] as $key2 => $fontface ) {

							$css = str_replace($fontface, '', $css);
						}

						// Remove any comments
						$regex = array(
						"`^([\t\s]+)`ism"=>'',
						"`^\/\*(.+?)\*\/`ism"=>"",
						"`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
						"`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
						"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
						);
						$css = preg_replace(array_keys($regex),$regex,$css);

						// Scan for CSS icons
						$regex = '/\.';
						$regex .= preg_quote( $library_args['selector_prefix'] );
						$regex .= '(.*?)';
						$regex .= ''.  preg_quote( $library_args['selector_suffix'] ).'/';
						$found = preg_match_all( $regex, $css, $matches);

						if ( $found && isset( $matches[1] ) ) {
							foreach ( $matches[1] as $icon_key => $icon_slug ) {

								if ( strlen( $icon_slug ) <= 36 ) { 
									$icon_regex = "/". preg_quote($library_args['selector_prefix']) . $icon_slug . preg_quote( $library_args['selector_suffix'] ) ."[^}]*\}/" ;
									$found2 = preg_match( $icon_regex , $css, $icons);
									$icon_css .= !empty($icons[0]) ? '.'.$icons[0] ."\n" : ''; 
									$library_icons[] = array(
																'id' => $icon_slug,
																'title' => $icon_slug,
																'class' => trim( $library_args['class_prefix'] ) .' '. trim( $library_args['selector_prefix'] ) . $icon_slug . str_replace(':before', '', $library_args['selector_suffix'] ),
																'unprefixed_class' => trim( $library_args['selector_prefix'] ) . $icon_slug . str_replace(':before', '', $library_args['selector_suffix'] ),
																'data-glyph' => $icon_slug,
																);

								}
							}
						}
						
						$global_css .= preg_replace('/\s+/', '', $icon_css) ;
					}
				}
				// Update temporary icons index
				$icons_index = self::$temp_icons;
				$icons_index[$library_slug] = $library_icons;
				self::$temp_icons = $icons_index;
				// Update global stylesheet
				return Plethora_WP::write_to_file( self::$assets_dir .'/'. self::$global_stylesheet , $global_css );
			}

			return false;

		}


		private function remove_junk_font_files() {

			// Build an active font files list
			$libraries = self::get_libraries();
			$active_fonts = array();
			foreach ( $libraries as $library_slug => $library_args ) {
				foreach ( $library_args['fontfiles'] as $key => $file ) {

					if ( !empty($file) ) { 

						$active_fonts[] = basename( $file );
					}
				}
			}
			$active_fonts = array_unique($active_fonts);

			// Build a font files list of the 'fonts' directory and remove whatever is not on active list
			$existing_files = self::dirlist( self::$fonts_dir );
			foreach ( $existing_files as $file => $file_data ) {
				
				if ( !is_dir( self::$css_dir .'/'. $file ) && ! in_array($file, $active_fonts)  ) { 

					$removed[] = unlink( self::$fonts_dir .'/'. $file );
				}
			}
		}

		private function remove_junk_css_files() {

			// Build an active font files list
			$libraries = self::get_libraries();
			$active_css = array();
			foreach ( $libraries as $library_slug => $library_args ) {
				foreach ( $library_args['stylesheets'] as $key => $file ) {

					if ( !empty($file) ) { 

						$active_css[] = basename( $file );
					}
				}
			}
			$active_css = array_unique($active_css);

			// Build a font files list of the 'fonts' directory and remove whatever is not on active list
			$existing_files = self::dirlist( self::$css_dir );
			foreach ( $existing_files as $file => $file_data ) {
				
				if ( !is_dir( self::$css_dir .'/'. $file ) && ! in_array( self::$css_dir .'/'. $file, $active_css)  ) { 

					$removed[] = unlink( self::$css_dir .'/'. $file );
				}
			}
		}

		public static function dirlist( $path ) {

		    /** Let's try to setup WP_Filesystem */
		    if ( false === ( $creds = request_filesystem_credentials( $path ) ) )
		        /** A form has just been output asking the user to verify file ownership */
		        return true;
		     
		    /** If the user enters the credentials but the credentials can't be verified to setup WP_Filesystem, output the form again */
		    if ( ! WP_Filesystem( $creds ) ) {
		        /** This time produce the error that tells the user there was an error connecting */
		        request_filesystem_credentials( $path );
		        return true;
		    }

		    global $wp_filesystem;
		    $filelist = $wp_filesystem->dirlist( $path, false );
		    return $filelist;
		}

// Font Libraries Management Methods <--- END

// Admin User Interaction Methods ---> START

	    public function theme_options_tab( $sections ) { 

			$subtitle_text = esc_html__('You may enable/disable any of those libraries. Nevertheless, you should know that Font Awesome icons are broadly used on this theme and you should not disactivate them.', 'plethora-framework');
			$subtitle_text .= '<div style="margin-top:10px; font-weight:bold;">'. esc_html__('Preview installed libraries:', 'plethora-framework') .'</div>';
			$subtitle_text .= $this->get_preset_libraries_desc();
			$adv_settings = array();
			$adv_settings[] = array(
				'id'           =>  THEMEOPTION_PREFIX .'iconlibraries-preset',
				'type'         => 'repeater',
				'title'        => esc_html__( 'Preset Icon Libraries', 'plethora-framework' ),
				'subtitle'     => $subtitle_text ,
	    		'desc'    	   => '',
				'group_values' => true, // Group all fields below within the repeater ID
				// 'item_name'    => 'font icon library', // Add a repeater block name to the Add and Delete buttons
				'bind_title'   => 'title', // Bind the repeater block title to this field ID
				'static'       => $this->theme_options_tab_static, // Set the number of repeater blocks to be output
				'limit'        => 0, // Limit the number of repeater blocks a user can create
				'sortable'     => false, // Allow the users to sort the repeater blocks or not
				'fields'       => array(
	                array(
						'id'      => 'status',
						'type'    => 'switch',
						'title'   => esc_html__( 'Activate Library', 'plethora-framework' ),
						'default' => 1,
	                ),
	                array(
	                    'id'          => 'title',
	                    'type'        => 'text',
	                    'title' => esc_html__( 'Reference Title', 'plethora-framework' ),
						'readonly' => true,
	                ),
	                array(
						'id'          => 'id',
						'type'        => 'text',
						'title'       => esc_html__( 'Reference ID', 'plethora-framework' ),
						'validate' => 'no_special_chars',
						'readonly' => true,
	                ),
	                array(
						'id'          => 'class_prefix',
						'type'        => 'text',
						'title'       => esc_html__( 'Class prefix', 'plethora-framework' ),
						'validate' => 'no_special_chars',
						'placeholder' => esc_html__( 'No prefix needed', 'plethora-framework' ),
						'readonly' => true,
	                ),
	            ),
				'default' => $this->preset_iconlibraries()
			);

	    	// Intro text -> Basic text
	    	$desc = esc_html__('Manage all your font icon resources. All parsed icons will be available on every icon picker field you will meet on various features ( theme options, shortcodes, etc. ). ', 'plethora-framework') ;
			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Icon Libraries', 'plethora-framework'),
				'heading'      => esc_html__('ICON LIBRARIES', 'plethora-framework'),
				'desc'      => $desc,
				'fields'     => $adv_settings
				);

			return $sections;
	    }

		public function get_preset_libraries_desc() {

			$subtitle_text = '<ol style="margin-top:10px; line-height:24px;">';
			$subtitle_text .= '<li><a href="'. esc_url( 'https://fortawesome.github.io/Font-Awesome/icons/ .' ) .'" target="_blank">Font Awesome</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://linea.io/#Basic .' ) .'" target="_blank">Linea Basic</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://linea.io/#Ecommerce .' ) .'" target="_blank">Linea Ecommerce</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://linea.io/#Music .' ) .'" target="_blank">Linea Music</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://linea.io/#Software .' ) .'" target="_blank">Linea Software</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://linea.io/#Arrows .' ) .'" target="_blank">Linea Arrows</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://linea.io/#Weather .' ) .'" target="_blank">Linea Weather</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://samcome.github.io/webfont-medical-icons/#content .' ) .'" target="_blank">Webfont Medical Icons</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://erikflowers.github.io/weather-icons/ .' ) .'" target="_blank">Weather Icons</a></li>';
			$subtitle_text .= '<li><a href="'. esc_url( 'http://graphicburger.com/stroke-gap-icons-webfont/ .' ) .'" target="_blank">Stroke Gap Icons</a></li>';
			$subtitle_text .= '</ol>';
			return $subtitle_text;
		}

		// Returns the preset libraries ( remember to update the 'static' attribute according to libraries number )
		public function preset_iconlibraries( $return = '' ) {

			$preset_iconlibraries = array();

			if ( $return !== 'all' ) { 

				// IMPORTANT: this is necessary for repeater field...add a line for each record
				$preset_iconlibraries['redux_repeater_data'] = array(
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				                     array( 'title' => '' ),
				               );
				$preset_iconlibraries['title'] = array(
				                  esc_html__('Font Awesome', 'plethora-framework'),
	                          	  esc_html__('Linea Basic', 'plethora-framework'),
	                          	  esc_html__('Linea Ecommerce', 'plethora-framework'),
	                          	  esc_html__('Linea Music', 'plethora-framework'),
	                          	  esc_html__('Linea Software', 'plethora-framework'),
	                          	  esc_html__('Linea Arrows', 'plethora-framework'),
	                          	  esc_html__('Linea Weather', 'plethora-framework'),
	                          	  esc_html__('Webfont Medical Icons', 'plethora-framework'),
	                          	  esc_html__('Weather Icons', 'plethora-framework'),
	                          	  esc_html__('Stroke Back Icons', 'plethora-framework'),
	                          	  esc_html__('Hotel Icons', 'plethora-framework'),
				                );
				$preset_iconlibraries['status'] = array(
				                  $this->fontawesome_status,
				                  $this->lineabasic_status,
				                  $this->lineaecommerce_status,
				                  $this->lineamusic_status,
				                  $this->lineasoftware_status,
				                  $this->lineaarrows_status,
				                  $this->lineaweather_status,
				                  $this->webfont_medical_icons_status,
				                  $this->weather_icons_status,
				                  $this->stroke_gap_icons_status,
				                  $this->plethora_hotel_status,
				                );
				$preset_iconlibraries['id'] = array(
				                  'fontawesome',
				                  'linea_basic',
				                  'linea_ecommerce',
				                  'linea_music',
				                  'linea_software',
				                  'linea_arrows',
				                  'linea_weather',
				                  'webfont_medical_icons',
				                  'weather_icons',
				                  'stroke_gap_icons',
				                  'hotel_icons',
				                );
				$preset_iconlibraries['class_prefix'] = array(
				                  'fa',
				                  'lin',
				                  'lin-ecommerce',
				                  'lin-music',
				                  'lin-software',
				                  'lin-arrows',
				                  'lin-weather',
				                  'wmi',
				                  'wi',
				                  'sgi',
				                  'hi',
				                );
			
			} elseif ( $return === 'all') { 

				$preset_iconlibraries['selector_prefix'] = array(
				                  'fa-',
	                          	  'icon-',
	                          	  'icon-',
	                          	  'icon-',
	                          	  'icon-',
	                          	  'icon-',
	                          	  'icon-',
	                          	  'icon-',
	                          	  'wi-',
	                          	  'icon-',
	                          	  'hi-',
				                );
				$preset_iconlibraries['selector_suffix'] = array(
				                  ':before',
	                          	  ':before',
	                          	  ':before',
	                          	  ':before',
	                          	  ':before',
	                          	  ':before',
	                          	  ':before',
	                          	  ':before',
	                          	  ':before',
	                          	  ':before',
	                          	  ':before',
				                );
				$preset_iconlibraries['stylesheet1'] = array(
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/font-awesome.css',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/linea-basic.css',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/linea-ecommerce.css',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/linea-music.css',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/linea-software.css',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/linea-arrows.css',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/linea-weather.css',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/wfmi-style.css',
	                          	  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/weather-icons.css',
	                          	  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/stroke-gap-icons.css',
	                          	  PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/plethora-hotel.css',
				                );

				$preset_iconlibraries['font-family'] = array(
				                  'FontAwesome',
				                  'linea-basic-10',
				                  'linea-ecommerce-10',
				                  'linea-music-10',
				                  'linea-software-10',
				                  'linea-arrows-10',
				                  'linea-weather-10',
				                  'webfont-medical-icons',
				                  'weathericons',
				                  'Stroke-Gap-Icons',
				                  'plethora-hotel',
				                );
				$preset_iconlibraries['src_eot'] = array(
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-basic-10.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-ecommerce-10.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-music-10.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-software-10.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-arrows-10.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-weather-10.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/webfont-medical-icons.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/weathericons-regular-webfont.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/Stroke-Gap-Icons.eot',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/plethora-hotel.eot',
				                );
				$preset_iconlibraries['src_svg'] = array(
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-basic-10.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-ecommerce-10.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-music-10.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-software-10.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-arrows-10.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-weather-10.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/webfont-medical-icons.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/weathericons-regular-webfont.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/Stroke-Gap-Icons.svg',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/plethora-hotel.svg',
				                );
				$preset_iconlibraries['src_ttf'] = array(
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-basic-10.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-ecommerce-10.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-music-10.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-software-10.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-arrows-10.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-weather-10.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/webfont-medical-icons.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/weathericons-regular-webfont.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/Stroke-Gap-Icons.ttf',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/plethora-hotel.ttf',
				                );
				$preset_iconlibraries['src_woff'] = array(
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-basic-10.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-ecommerce-10.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-music-10.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-software-10.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-arrows-10.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/linea-weather-10.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/webfont-medical-icons.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/weathericons-regular-webfont.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/Stroke-Gap-Icons.woff',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/plethora-hotel.woff',
				                );
				$preset_iconlibraries['src_woff2'] = array(
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.woff2',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/weathericons-regular-webfont.woff2',
				                  '',
				                  '',
				                );
				$preset_iconlibraries['src_otf'] = array(
				                  PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/FontAwesome.otf',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                  '',
				                );
				$preset_iconlibraries['font-style'] = array(
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                );
				$preset_iconlibraries['font-weight'] = array(
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                );
				$preset_iconlibraries['font-stretch'] = array(
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                  'normal',
				                );
			}
	        return $preset_iconlibraries;	

		}



// Admin User Interaction Methods <--- END


// Enqueing Methods and other filters ---> START

		public function enqueue_libraries() {

			if ( Plethora_Theme::is_library_active() ) { 

				$refresh_var = get_option( GENERALOPTION_PREFIX .'module_icons_uniqeid', '' );
				wp_enqueue_style( ASSETS_PREFIX .'-icons' , self::$assets_url .'/'. self::$global_stylesheet .'?uniqeid='. $refresh_var  ); 
			
			} else { // if PFL is not installed, work on safe mode...load just FontAwesome

				wp_enqueue_style( 'fa' , PLE_CORE_FEATURES_URI .'/module/icons/default_styles/font-awesome.css' ); 
			}
		}
		/**
		* Intermediary method to hook on 'init', when the whole things starts ( sic! )
		* @param $icons
		* @return array
		*/
		public function vc_iconpicker_add() {

			if ( Plethora_Theme::is_library_active() ) { 

				add_filter( 'vc_iconpicker-type-plethora', array( $this, 'vc_iconpicker_plethora_icons' ) );
			}
		}

		/**
		* Intermediary method to hook on 'init', when the whole things starts ( sic! )
		* @param $icons
		* @return array
		*/
		public function vc_icon_iconpickers_add() {

			if ( Plethora_Theme::is_library_active() ) { 

				$libraries = self::get_options_array( array( 'use_in' => 'vc', 'libraries_only' => true, 'exclude' => array( 'fontawesome' ) ) );

				foreach ( $libraries as $title => $id) {
					add_filter( 'vc_iconpicker-type-'. $id, array( $this, 'vc_iconpicker_plethora_icons' ) );
				}
			}
		}

		/**
		* Method to hook on 'vc_iconpicker-type-plethora' hook
		* @param $icons
		* @return array
		*/
		public static function vc_iconpicker_plethora_icons( $icons ) {

			$current_filter = current_filter();

			if ( $current_filter === 'vc_iconpicker-type-plethora' ) {

				$plethora_icons = self::get_options_array( array( 'use_in' => 'vc', 'grouped_results' => true ) );
        	
        	} else {

        		$library_id = str_replace( 'vc_iconpicker-type-', '', $current_filter );
				$plethora_icons = self::get_options_array( array( 'use_in' => 'vc', 'grouped_results' => true, 'library' => $library_id ) );

        	}
        	return array_merge( $icons, $plethora_icons );
		}

		/**
		* Intermediary method to hook on 'init', when the whole things starts ( sic! )
		* @param $icons
		* @return array
		*/
		public function vc_icon_append_libraries_to_dropdown() {

			if ( Plethora_Theme::is_library_active() ) { 

				// Get our libraries
				$libraries = self::get_options_array( array( 'use_in' => 'vc', 'libraries_only' => true, 'exclude' => array( 'fontawesome' ) ) );

				// Add our libraries to 'Libraries' Dropdown
				$param_type = WPBMap::getParam( 'vc_icon', 'type' );
				foreach  ( $libraries as $title => $id ) {

					$param_type['value'][$title] = $id ;
					$param_type['weight'] = 3 ; // additional setup for option weight
				}

				if ( function_exists('vc_update_shortcode_param') && shortcode_exists( 'vc_icon' ) && !empty( $param_type ) ) {

					vc_update_shortcode_param( 'vc_icon', $param_type );
				}

				if ( function_exists('vc_update_shortcode_param') && shortcode_exists( 'vc_icon' ) ) {

					// additional fix for options weight ( weight: display priority )
					$existing_iconpickers = array( 'fontawesome', 'openiconic', 'typicons', 'entypo', 'linecons', 'monosocial' );
					foreach ( $existing_iconpickers as $existing_iconpicker ) {
						
						$param_iconpicker = WPBMap::getParam( 'vc_icon', 'icon_'.$existing_iconpicker );
						if ( !empty( $param_iconpicker ) ) {

							$param_iconpicker['weight'] = 1 ;
							vc_update_shortcode_param( 'vc_icon', $param_iconpicker );
						}
					}					
				}
			}
		}

		public function vc_icon_add_libraries_iconpickers( ){

			if ( Plethora_Theme::is_library_active() ) { 

				// Get our libraries
				$libraries = self::get_options_array( array( 'use_in' => 'vc', 'libraries_only' => true, 'exclude' => array( 'fontawesome' ) ) );
				foreach ( $libraries as $title => $id ) {
					$params[] = array(
						'type'       => 'iconpicker',
						'heading'    => __( 'Icon', 'plethora-framework' ),
						'param_name' => 'icon_'.$id,
						'value'      => '', // default value to backend editor admin_label
						'settings'   => array(
							'emptyIcon'    => false, // default true, display an "EMPTY" icon?
							'type'         => $id,
							'iconsPerPage' => 100, // default 100, how many icons per/page to display
						),
						'dependency' => array(
										'element' => 'type',
										'value'   => $id,
						),						
						'description' => __( 'Select icon from library.', 'plethora-framework' ),
						'weight' => 3,
					);

					add_filter( 'vc_iconpicker-type-plethora', array( $this, 'vc_iconpicker_plethora_icons' ) );
				}

				if ( function_exists('vc_add_params') && shortcode_exists( 'vc_icon' ) &&  !empty( $params ) ) {

					vc_add_params( 'vc_icon', $params );
				}
			}
		}




// Enqueing Methods <--- END

// Routine Methods & Conditionals ---> START


		/**
		* Register single library ( solely used by register_libraries() method)
		* @return NULL
		*/
		private function register_library( $library_slug, $library_args ) {

		    $default_library_args = array( 
					'status'          => false,     
					'title'           => esc_html__('Unidentified Icons Library', 'plethora-framework'),
					'id'              => '', 		// stylesheet class prefix ( used for auto-scan )
					'class_prefix'    => '', 		// stylesheet class prefix ( used for auto-scan )
					'selector_prefix' => '', 		// stylesheet class prefix ( used for auto-scan )
					'selector_suffix' => ':before',	// stylesheet class suffix ( used for auto-scan )
					'stylesheets'     => array(),
					'fontfiles'       => array(),
					'font-family'     => '',
					'font-style'      => 'normal',
					'font-weight'     => 'normal',
					'font-stretch'    => 'normal',
					'icons'           => isset(  self::$temp_icons[$library_slug] ) && !empty( self::$temp_icons[$library_slug] ) ? self::$temp_icons[$library_slug] : array()  ,
			);

		    // Merge user given arguments with default & some validation
		    $library_args = wp_parse_args( $library_args, $default_library_args);

		    // Update the index
			$libraries = self::get_libraries();
			$libraries[$library_slug] = $library_args;
			self::update_libraries( $libraries );
		}

		/**
		* Unregister single library ( solely used by register_libraries() method)
		* @return NULL
		*/
		private function unregister_library( $library_slug ) {

		    // Update the index
			$libraries = self::get_libraries();
			if ( isset( $libraries[$library_slug] ) ) {

				unset( $libraries[$library_slug] );
			}

			self::update_libraries( $libraries );
		}


		/**
		* Returns registered libraries
		* @return array()
		*/
		private static function get_libraries() {

			$libraries = self::$libraries;
			return $libraries;
		}

		/**
		* Updates registered libraries db option
		*/
		private static function update_libraries( $libraries ) {

			self::$libraries = $libraries;
			
			// Save option on db only during recompile...no need
			if ( self::is_on_recompile() ) {

				update_option( self::$db_option, self::get_libraries() );
			}
		}

		/**
		* Returns a library attribute value if exists
		* @param $slug, $args
		* @return array/string/boolean ( depending on attribute value )
		*/
		private static function get_library_attr( $library_slug, $library_attr ) {

			$libraries = self::get_libraries();
			$return = isset( $libraries[$library_slug][$library_attr] ) ? $libraries[$library_slug][$library_attr] : '';
			return $return;
		}

		/**
		* Updates saved libraries option values ( used mostly to set library status to 'off' )
		* @return NULL
		*/
		private function update_libraries_option_args( $library_slug, $args ) {

			$preset_iconlibraries = $this->preset_iconlibraries();
		    $iconlibraries   = Plethora_Theme::option( THEMEOPTION_PREFIX .'iconlibraries', $preset_iconlibraries );
		    if ( isset( $iconlibraries['id'] ) ) { 
			    foreach ($iconlibraries['id'] as $key => $slug ) {

			    	if ( $slug === $library_slug ) { 

			    		foreach ( $args as $arg_key => $arg_val ) { 

							$iconlibraries[$arg_key][$key] = $arg_val;
						}
					}
				}
			}

			$plethora_options = get_option(THEME_OPTVAR);
			if ( isset( $plethora_options[THEMEOPTION_PREFIX .'iconlibraries'] ) ) {

				$plethora_options[THEMEOPTION_PREFIX .'iconlibraries'] = $iconlibraries;
				update_option( THEME_OPTVAR, $plethora_options );
			}
		}

		/**
		* Helper method that saves a url to a file
		* @param $url, $file 
		* @return boolean 
		*/
		private function storeToUploadsDir( $file_origin, $file_destination ) {

			// Remote call
			$contents = Plethora_WP::get_file_contents( $file_origin );
			// Write the contents in file
			if ( ! empty( $contents )  ) {

					update_option( GENERALOPTION_PREFIX .'module_icons_diagnostics_wpremote', '' ) ;
					return Plethora_WP::write_to_file( $file_destination , $contents );

			} else {

				update_option( GENERALOPTION_PREFIX .'module_icons_diagnostics_wpremote', 'Cannot create file in uploads directory' ) ;
				return false;
			}
		}

		/**
		* Checks if current filter executed is a redux save action. If so, it will return true, otherwise false
		* @return boolean 
		*/
		private static function is_on_recompile() {

			// Force recompiling if icons library option is empty...most possibly this means that this is the first loading after Plethora Framework activation
			$db_option = get_option( self::$db_option, array() );
			$force_recompile = empty( $db_option ) ? true : false;

			if ( $force_recompile || current_filter() === 'redux/options/'.THEME_OPTVAR.'/reset' || current_filter() === 'redux/options/'.THEME_OPTVAR.'/section/reset' || current_filter() === 'redux/options/'.THEME_OPTVAR.'/saved' ) {

				return true;
			}
			return false;
		}
// Routine Methods <--- END


// PUBLIC Methods to be used outside class  ---> START

		/**
		* Check if library is already registered
		* @param $stylesheets 
		* @return boolean 
		*/
		public static function is_library_registered( $slug ) {

			return array_key_exists( $slug, self::get_libraries() );  
		}

		/**
		* Returns icons in array, mainly for field option values use ( Redux, Visual Composer, etc. )
		* @param $args
		* @return array
		*/
		public static function get_options_array( $args = array() ) {

		    $default_args = array( 
					'use_in'            => 'redux',		// 'redux', 'vc' // different option outputs depending on the form
					'library'           => array(),		// library slug(s) filter, can be array with multiple library slugs
					'exclude'           => array(),		// library slug(s) filter, can be array with multiple library slugs
					'libraries_only' 	=> false,		// if true, it will return an array only with libraries info
					'add_library_title' => true,		// if true, it will add the library title along with icon title
					'grouped_results'   => false,		// returns an array with each library as a group with its own icon array()
					'key_type'          => 'class',		// 'slug', class', 'unprefixed_class', 'data-glyph', 'title' // key type 
			);
		    $args = wp_parse_args( $args, $default_args);
		    $args['library'] = is_array($args['library']) ? $args['library'] : array( $args['library'] );
		    $args['exclude'] = is_array($args['exclude']) ? $args['exclude'] : array( $args['exclude'] );
			
			$return_redux = array();
			$return_vc = array();
			$libraries = self::get_libraries();
			foreach ( $libraries as $library_slug => $library_args ) { 

				if ( $library_args['status'] && !in_array( $library_slug, $args['exclude'] ) && ( empty( $args['library'] ) || in_array( $library_slug, $args['library'] ) ) ) {
					
					if ( $args['libraries_only'] === false ) { // if needed full information
						
						$redux_icons = array();
						$vc_icons = array();
						foreach ( $library_args['icons'] as $icon_slug => $icon_args ) {

							$redux_icons[ $icon_args[$args['key_type']] ] = $args['add_library_title'] ? ucfirst( $icon_args['title'] ) .' ( '. $library_args['title'] .' library )' : ucfirst( $icon_args['title'] );
							$vc_icons[] = array( $icon_args[$args['key_type']] => $args['add_library_title'] ? ucfirst( $icon_args['title'] ) .' ( '. $library_args['title'] .' library )' : ucfirst( $icon_args['title'] ) );
						}
						// Configure grouped results
						if ( $args['grouped_results'] ) {

							$return_redux[$library_args['title']] = $redux_icons;
							$return_vc[$library_args['title'] .' ( Plethora Library )'] = $vc_icons;

						} else {

							$return_redux = array_merge($return_redux, $redux_icons);
							$return_vc = array_merge($return_vc, $vc_icons);
						}

					} elseif ( $args['libraries_only'] ) { // if needed only library information

							$return_redux[$library_args['title']] = strtolower( $library_args['id'] );
							$return_vc[$library_args['title'] .' ( Plethora Library )'] = strtolower( $library_args['id'] );

					}
				}
			}
			return $args['use_in'] === 'vc' ? $return_vc : $return_redux ;
		}

		/**
		* Returns icon(s) markup for use in HTML source
		* @param $args
		* @return array
		*/
		public function get( $args ) {

		    $default_args = array( 
					'icon'  => '',	    	// icon term
					'tag'   => 'i',			// wrapper HTML tag
					'class' => array(),		// additional classes
					'id'    => false,		// boolean/string / if not string, id with icon slug value will be added according to boolean
					'style' => array(),		// other attributes given in key=>value pairs
					'attrs' => array(),		// other attributes given in key=>value pairs
					'demo'  => false,		// demo mode will return title next to tag
					'feel'  => array(),		// other attributes given in key=>value pairs
			);
		    $args = wp_parse_args( $args, $default_args);
		    $args['class'] = is_array($args['class']) ? $args['class'] : array($args['class']);
		    $args['style'] = is_array($args['style']) ? $args['style'] : array($args['style']);

		    // Get the icon we need
		    $libraries = self::get_libraries();
		    $icon = array();
		    foreach ( $libraries as $library_slug => $library_args ) {

			    foreach ( $library_args['icons'] as $key => $find_icon )	{

			    	if ( $find_icon['class'] === $args['icon'] ) {

			    		 $icon = $find_icon;
			    	}
			    }
		    }
		    if ( empty( $icon ) ) { return; }
		    // Prepare the output									
			$output  = '<'. $args['tag'] .' ';	// starting tag
			
			$classes = !empty( $args['class'] ) ? ' '. implode(' ', $args['class']) : '';
			$output .= 'class="'. $icon['class'] . $classes .'"';	// class attr
			
		    if ( is_bool( $args['id'] ) &&  $args['id'] === true ) {	// id attr
				
				$output .= 'id="'. $icon['id'] .'" ';
		    } elseif ( is_string( $args['id'] ) ) { 

				$output .= 'id="'. $args['id'] .'" ';
		    }

			$output .= ' data-glyph="'. $icon['data-glyph'] .'"';	// data-glyph attr ( used for compatibility with some libraries )
			
			foreach ( $args['attrs'] as $attr_key => $attr_val ) {	// additional attributes

				$output .= $attr_key .'="'. $attr_key .'" ';
			}

			$style = !empty( $args['style'] ) ? ' style="'. implode(';', $args['style']) .'"' : '';
			$output .= $style;	// style attr

			$output .= '>';	// close starting html tag
			$output .= '</'. $args['tag'] .'>';	// ending html tag

			$output .= $args['demo'] ? ' '. $icon['title'] : '';
			return $output;
		}
// PUBLIC Methods to be used outside class <--- END
	}
}