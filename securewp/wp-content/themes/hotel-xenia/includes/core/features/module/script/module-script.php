<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Scripts manager

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Script') ) {

    /**
     * Manages all themes' scripts and styles functionality
     * @since 2.0
     */
	class Plethora_Module_Script {

		public static $feature_title        = "Scripts Manager";							// Feature display title  (string)
		public static $feature_description  = "Global & custom scripts & styles manager";	// Feature display description (string)
		public static $theme_option_control = false;										// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default	= false;										// Default activation option status ( boolean )
		public static $theme_option_requires= array();										// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct	= true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method		= false;										// Additional method invocation ( string/boolean | method name or false )

		public $dir_js;		// Core JS assets directory URL
		public $libdir_js;	// Core JS libraries directory URL
		public $libdir_css;	// Core CSS libraries directory URL
		public $min_suffix;	// Suffix added on files to load uminified versions on developer mode

		public function __construct(){

			$this->dir_js       = PLE_CORE_ASSETS_URI . '/js/';
			$this->libdir_js    = PLE_CORE_ASSETS_URI . '/js/libs/';
			$this->libdir_css   = PLE_CORE_ASSETS_URI . '/css/libs/';
			$this->min_suffix   = Plethora_Theme::is_developermode() ? '' : '.min';

      		# LIBRARY SCRIPTS & STYLES REGISTRATIONS
      		add_action( 'wp_enqueue_scripts', array( $this, 'register_plethora_libs' ), 1);  	// Customized library assets registration ( register early )
      		add_action( 'wp_enqueue_scripts', array( $this, 'register_thirdparty_libs' ), 1);  	// Third party library assets registration ( register early )

      		# THEME SCRIPTS & STYLES REGISTRATIONS ( All standar assets expected to be in theme )
      		add_action( 'wp_enqueue_scripts', array( $this, 'register_theme_assets' ), 2);  	// Basic libraries ( ie. Bootstrap, etc. )
      		
      		# SHORTCODE & WIDGETS SCRIPTS & STYLES ENQUEUES
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_shortcodes_assets' ), 20);	// On demand shortcode assets ( always enqueue features on 20 )
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_widgets_assets' ), 20);	// On demand widget assets ( always enqueue features on 20 )
      		
      		# JS INITS
			add_action( 'wp_head', array( $this, 'assets_inits' ), 999 );						// Init scripts ( enqueued to header with Plethora_Theme::enqueue_init_script() )
			add_action( 'wp_footer', array( $this, 'assets_inits' ), 999 );              		// Init scripts ( enqueued to footer with Plethora_Theme::enqueue_init_script() )
			
			# CUSTOM CSS/JS/ANALYTICS
			add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_options_custom_scripts_tab'), 20);	// Set Custom JS theme options tab
			add_action( 'wp_head', array( $this, 'output_customcss'), 999999);					// Custom CSS field export     
			add_action( 'wp_head', array( $this, 'output_analyticsjs'), 999999);				// Analytics JS output / before <head> close placement
			add_action( 'wp_footer', array( $this, 'output_customjs'), 999999);					// JS output
			add_action( 'wp_footer', array( $this, 'output_analyticsjs'), 999999);				// Analytics JS output / before <body> close placement
		}

	   /**
	    * Register all global JS/CSS libraries. Each item using those, will enqueue just using the slug ( i.e. in shortcodes )
	    */
	    public function register_plethora_libs() {

		    # JS LIBRARIES  
		      // One pager ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-one-pager', $this->dir_js . '/onepager.js' ); 
		      // Modernizr ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX . '-modernizr', $this->libdir_js .'modernizr/modernizr.custom.48287.js', array('jquery'), '', FALSE ); 
		      // Twenty-twenty ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-jquery-event-move', $this->libdir_js .'twentytwenty/js/jquery.event.move'.$this->min_suffix.'.js', array( 'jquery' ), false, true );
		      wp_register_script( ASSETS_PREFIX .'-twentytwenty', $this->libdir_js .'twentytwenty/js/jquery.twentytwenty'.$this->min_suffix.'.js', array( 'jquery' ), false, true );
		      wp_register_style( ASSETS_PREFIX .'-twentytwenty', $this->libdir_js .'twentytwenty/css/twentytwenty.css', array(), false, 'all' );
		      // To Top ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-totop', $this->libdir_js .'totop/jquery.ui.totop.js', array( 'jquery' ), false, true  );
		      // SVG Modal Loader ( linkify ) ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX . '-svgloader-init', $this->libdir_js . 'svgloader/loader'.$this->min_suffix.'.js',   array( 'svgloader-snap', 'svgloader'),  '', TRUE ); 
		      wp_register_script( ASSETS_PREFIX . '-imagelightbox', $this->libdir_js . 'imagelightbox/imagelightbox'.$this->min_suffix.'.js',   array(),  '', TRUE ); 
		      // MailChimp Newsletter ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-newsletter_form', $this->libdir_js . 'newsletter/newsletter'.$this->min_suffix.'.js', array('jquery', ASSETS_PREFIX . '-init' ), '1.0', true ); 
		      wp_register_script( ASSETS_PREFIX .'-newsletter_form_svg', $this->libdir_js . 'newsletter/newsletter-svg.js', array('jquery', ASSETS_PREFIX . '-init', ASSETS_PREFIX . '-newsletter_form'), '1.0', true ); 
		      // Animated headlines ( Initial use: Avoir, based on https://codyhouse.co/gem/css-animated-headlines/ ) 
		      wp_register_script( ASSETS_PREFIX .'-animated-headline', $this->libdir_js . 'animated-headline/animated-headline'.$this->min_suffix.'.js', array('jquery' ), '1.0', true ); 
		      // HideSeek jQuery plugin ( Initial use: documentation shortcode for plethorathemes.com ) 
		      wp_register_script( ASSETS_PREFIX .'-hideseek', $this->libdir_js . 'hideseek/jquery.hideseek'.$this->min_suffix.'.js', array('jquery' ), '1.0', true ); 
		      // HideSeek jQuery plugin ( Initial use: documentation shortcode for plethorathemes.com ) 
		      wp_register_script( ASSETS_PREFIX .'-unveil', $this->libdir_js . 'unveil/jquery.unveil'.$this->min_suffix.'.js', array('jquery' ), '1.0', true ); 
		      // Custom Doc script ( Initial use: documentation shortcode for plethorathemes.com ) 
		      wp_register_script( ASSETS_PREFIX .'-doc', $this->libdir_js . 'plethoradoc/doc'.$this->min_suffix.'.js', array('jquery' ), '1.0', true ); 

		    # CSS LIBRARIES
		      // Image Lightbox
		      wp_register_style( ASSETS_PREFIX .'-imagelightbox', $this->libdir_js .'imagelightbox/imagelightbox'. $this->min_suffix .'.css' );                          
		      // Woocommerce Plethora styles ( note that this should be always on theme 'css' folder )
    		  wp_register_style( ASSETS_PREFIX .'-woocommerce', PLE_THEME_ASSETS_URI . '/css/woocommerce.css' );
		      // Custom Doc style ( Initial use: documentation shortcode for plethorathemes.com ) 
		      wp_register_style( ASSETS_PREFIX .'-doc', $this->libdir_js .'plethoradoc/doc.css' );                          
	    }

		public function register_thirdparty_libs() {

		    # JS LIBRARIES  
		      // Easing ( Initial use: HealthFlex )
		      wp_register_script( 'easing', $this->libdir_js .'easing/easing'. $this->min_suffix .'.js',   array(),  '', TRUE ); 
		      // Wow ( Initial use: HealthFlex )
		      wp_register_script( 'wow-animation-lib', $this->libdir_js .'wow/wow'. $this->min_suffix .'.js',   array(),  '', TRUE ); 
		      // Parallax ( Initial use: HealthFlex )
		      wp_register_script( 'parallax', $this->libdir_js .'parallax/parallax'. $this->min_suffix .'.js', array('jquery'),  '', TRUE ); 
		      // Conformity ( Initial use: HealthFlex )
		      wp_register_script( 'conformity', $this->libdir_js .'conformity/dist/conformity'. $this->min_suffix .'.js',   array(),  '', TRUE ); 
		      // Isotope ( Initial use: HealthFlex / current version: 2.2.2, updated 20/04/2016 )
		      // we had to use another registration with plethora prefix here, to avoid conflicts with 
		      // Visual Composer on some elements, such as any post loop shortcodes
		      wp_register_script( 'isotope', $this->libdir_js .'isotope/jquery.isotope'.$this->min_suffix.'.js', array( 'jquery' ), '2.2.2', true  );
		      wp_register_script( 'plethora-isotope', $this->libdir_js .'isotope/jquery.isotope'.$this->min_suffix.'.js', array( 'jquery' ), '2.2.2', true  );
		      // wp_register_style( 'isotope', $this->libdir_js .'isotope/css/style.css', array(), false, 'all' );
		      // OwlCarousel 2 ( Initial use: HealthFlex )
		      wp_register_style( 'owlcarousel2', $this->libdir_js .'owl.carousel.2.0.0-beta.2.4/css/owl.carousel.css' );                     // STYLE - Owl Carousel 2 main stylesheet
		      wp_register_style( 'owlcarousel2-theme', $this->libdir_js .'owl.carousel.2.0.0-beta.2.4/css/owl.theme.default.css' );          // STYLE - Owl Carousel 2 theme stylesheet
		      wp_register_script( 'owlcarousel2', $this->libdir_js .'owl.carousel.2.0.0-beta.2.4/owl.carousel.min.js', array(),  '2.4', TRUE  ); // SCRIPT - Owl Carousel 2
		      // SVG Modal Loader ( linkify ) ( Initial use: HealthFlex )
		      wp_register_script( 'svgloader-snap', $this->libdir_js . 'svgloader/snap.svg-min.js',   array(),  '', TRUE ); 
		      wp_register_script( 'svgloader', $this->libdir_js . 'svgloader/svgLoader'.$this->min_suffix.'.js',   array(),  '', TRUE ); 
		      // Waypoint + Counter Up ( Initial use: Avoir )
		      wp_register_script( 'waypoint', $this->libdir_js .'waypoint/waypoint'.$this->min_suffix.'.js', array( 'jquery' ), '4.0', true  );
		      wp_register_script( 'counter-up', $this->libdir_js .'counter-up/jquery.counterup'.$this->min_suffix.'.js', array( 'jquery', 'waypoint' ), '1.0', true  );
			  // TweenMax Animation Lib by GSAP ( Initial use: Avoir / current version: 1.18.3, updated 20/04/2016 / https://greensock.com/docs/#/HTML5/GSAP/TweenMax/ )		    
		      wp_register_script( 'tweenmax', $this->libdir_js .'gsap/TweenMax'.$this->min_suffix.'.js', array( 'jquery' ), '1.18.3', false  );
		    
		    # CSS LIBRARIES
		      // Animate
		      wp_register_style( 'animate', $this->libdir_css .'animate/animate'. $this->min_suffix .'.css' );                          // Animation library
		}

		public function register_theme_assets() {

		    # JS LIBRARIES  
			  // Bootstrap JS
			  wp_register_script( 'boostrap', PLE_THEME_ASSETS_URI . '/js/libs/bootstrap'. $this->min_suffix .'.js',   array( 'jquery' ),  '', TRUE ); 
			  // General Theme JS configuration file
			  wp_register_script( ASSETS_PREFIX . '-init', PLE_THEME_ASSETS_URI . '/js/theme.js',   array(),  '', TRUE ); 
	        
		    # CSS LIBRARIES
			  // Bootstrap CSS
	          wp_register_style( ASSETS_PREFIX .'-custom-bootstrap',  PLE_THEME_ASSETS_URI . '/css/theme_custom_bootstrap.css'); 
	        
		    # MAIN STYLESHEET ( LESS created OR default one if LESS not used )
			  if ( class_exists( 'Plethora_Module_Wpless_Ext' ) ) {

			  	  // loads the default style.css, with the LESS compiled version as requirement
		          wp_register_style( ASSETS_PREFIX .'-style', get_stylesheet_uri(), array( ASSETS_PREFIX .'-dynamic-style' ) );

		      } else {

	        	  /* This implementation makes sure that the basic theme design 
	        	     will remain the same even if PFL is not active yet. It loads 
	        	     a fixed version with Bootstrap library as requirement */
		          wp_register_style( ASSETS_PREFIX .'-default-style', PLE_THEME_ASSETS_URI.'/css/default_stylesheet.css', array( ASSETS_PREFIX .'-custom-bootstrap' ) ); // Default static style.css
			  }
		}
		
	  	/**
	   	* Dynamic registers & enqueues for shortcode...working only when a shortcode is present!
	   	*/
	   	public function enqueue_shortcodes_assets() {

	      if ( is_singular() ) { 
	        // Get page content
	        global $post;
	        $content = $post->post_content;

	        // Get a list with all shortcode asset triggers ( shortcode slugs )
	        $shortcode_features = Plethora_Theme::get_features( array('controller' => 'shortcode' ) );
            foreach ( $shortcode_features as $key => $feature) {
             // Prepare shortcode slug
              $shortcode_slug = SHORTCODES_PREFIX . $feature['wp_slug'];
              // Enqueue 'em!
              if ( !empty($shortcode_slug) && has_shortcode( $content,  $shortcode_slug )) {
                // Enqueue scripts
                $assets  = $feature['assets'];

                if ( !empty($assets) ) {
                  foreach ( $assets as $asset ) {

                  	$asset_type = key($asset);
                  	$handlers = $asset[$asset_type];
                  	$handlers = is_array( $handlers ) ? $handlers : array( $handlers ); // predict multi arrays

                  	foreach ( $handlers as $handler ) {

						if ( !empty( $handler ) ) { 
			                if ( $asset_type === 'script' && wp_script_is( $handler, 'registered' ) ) {

			                  wp_enqueue_script( $handler );

			                } elseif ( $asset_type === 'script' && wp_script_is( ASSETS_PREFIX .'-'. $handler, 'registered' ) ) {

			                  wp_enqueue_script( ASSETS_PREFIX .'-'. $handler );

			                } elseif ( $asset_type === 'style' && wp_style_is( $handler, 'registered' ) ) {

			                  wp_enqueue_style( $handler );

			                } elseif ( $asset_type === 'style' && wp_style_is( ASSETS_PREFIX .'-'. $handler, 'registered' ) ) {

			                  wp_enqueue_style( ASSETS_PREFIX .'-'. $handler );
			                }
		                }
                    }
                  }
                }
              }
            }
	      }      
	   }

	  /**
	   * Dynamic registers & enqueues for widgets...working only when a widget is present!
	   */
	   public function enqueue_widgets_assets() {

		// Get a list with all widget asset triggers ( widget slugs )
		$widget_features = Plethora_Theme::get_features( array('controller' => 'widget' ) );

        foreach ( $widget_features as $key => $feature) {
         // Prepare shortcode slug
          $widget_slug = SHORTCODES_PREFIX . $feature['wp_slug'];
          // Enqueue 'em!
          if ( !empty($widget_slug) && ! is_active_widget( false, false, WIDGETS_PREFIX.$widget_slug , false ) ) {
            // Enqueue scripts
            $assets  = $feature['assets'];

            if ( !empty($assets) ) {
              foreach ( $assets as $asset ) {

              	$asset_type = key($asset);
              	$handlers = $asset[$asset_type];
              	$handlers = is_array( $handlers ) ? $handlers : array( $handlers ); // predict multi arrays

				foreach ( $handlers as $handler ) {

					if ( ! empty( $handler ) ) { 
		                if ( $asset_type === 'script' && wp_script_is( $handler, 'registered' ) ) {

		                  wp_enqueue_script( $handler );

		                } elseif ( $asset_type === 'script' && wp_script_is( ASSETS_PREFIX .'-'. $handler, 'registered' ) ) {

		                  wp_enqueue_script( ASSETS_PREFIX .'-'. $handler );

		                } elseif ( $asset_type === 'style' && wp_style_is( $handler, 'registered' ) ) {

		                  wp_enqueue_style( $handler );

		                } elseif ( $asset_type === 'style' && wp_style_is( ASSETS_PREFIX .'-'. $handler, 'registered' ) ) {

		                  wp_enqueue_style( ASSETS_PREFIX .'-'. $handler );
		                }
	                }
            	}
              }
            }
          }
        }
	   }

	  /**
	   * Echoes all init scripts given with Plethora_Theme::enqueue_init_script method
	   * Notice: all Plethora_Theme::enqueue_init_script calls aim to enqueue on header should be set
	   *         before wp_head action occurs
	   */
	   public function assets_inits() {

	      global $plethora_init_scripts;
	      if ( !empty( $plethora_init_scripts ) ) {

	        foreach ( $plethora_init_scripts as $handle => $inits ) {

	          foreach ( $inits as $key => $args ) {

	            $init_script = '';
	            
	            if ( current_filter() === 'wp_head' && $args['position'] === 'header' ) {

	              $init_script = $args['callback_type'] === 'function' ? call_user_func( $args['callback'] ) : $args['callback'];

	            } elseif ( current_filter() === 'wp_footer' && $args['position'] === 'footer' ) { 

	              $init_script = $args['callback_type'] === 'function' ? call_user_func( $args['callback'] ) : $args['callback'];
	            }
	            // Echo init script only if handle is enqueued in this page
	            if ( !empty( $init_script ) && wp_script_is( $handle ) ) {

	              echo Plethora_Theme::is_developermode() ? "\n". '<!-- START  /// INIT SCRIPT FOR HANDLE: '. $handle .' -->'."\n" : "\n";
	              echo trim( $init_script );
	              echo Plethora_Theme::is_developermode() ? "\n". '<!-- FINISH /// INIT SCRIPT FOR HANDLE: '. $handle .' -->'."\n" : "\n";
	            
	            } elseif ( !empty( $init_script ) && wp_script_is( ASSETS_PREFIX .'-'. $handle ) ) {

	              echo Plethora_Theme::is_developermode() ? "\n". '<!-- START  /// INIT SCRIPT FOR HANDLE: '. ASSETS_PREFIX .'-'. $handle .' -->'."\n" : "\n";
	              echo trim( $init_script );
	              echo Plethora_Theme::is_developermode() ? "\n". '<!-- FINISH /// INIT SCRIPT FOR HANDLE: '. ASSETS_PREFIX .'-'. $handle .' -->'."\n" : "\n";
	            }
	          }
	        }
	      }
	   }

// START: CUSTOM SCRIPT/STYLE OUTPUT METHODS

	    /**
	     * Adds custom JS tab to theme options
	     */
	    static function theme_options_custom_scripts_tab( $sections ) { 

			$adv_settings = array();

		    $adv_settings[] = array(
					'id'    =>'header-customcss-start',
					'type'  => 'section',
					'indent' => true,
					'title' =>  esc_html__('Custom Style Options (custom CSS)', 'plethora-framework')
			);
		    $adv_settings[] = array(
						'id'          =>THEMEOPTION_PREFIX .'customcss',
						'type'        => 'textarea',
						'title'       => esc_html__('Custom CSS', 'plethora-framework'), 
						'subtitle'    => esc_html__('Paste your CSS code here.', 'plethora-framework'),
						'description' => '<span style="color:red;"><strong>'. esc_html__('Do not use &lt;style&gt; tags.', 'plethora-framework') .'</strong></span>',
						'default'     => '',
						);

		    $adv_settings[] = array(
					'id'    =>'header-customcss-end',
					'type'  => 'section',
					'indent' => false,
			);
		    $adv_settings[] = array(
					'id'    =>'header-customjs-start',
					'type'  => 'section',
					'indent' => true,
					'title' =>  esc_html__('Custom Javascript Options', 'plethora-framework')
			);
		    $adv_settings[] = array(
					'id'          =>THEMEOPTION_PREFIX .'customjs',
					'type'        => 'textarea',
					'title'       => esc_html__('Custom JS (added on footer)', 'plethora-framework'),
					'subtitle'    => esc_html__('Paste your JS code here.', 'plethora-framework'),
					'description' => '<span style="color:red;"><strong>'. esc_html__('Do not use &lt;script&gt; tags.', 'plethora-framework') .'</strong>.</span>',
					'default'     => '',
			);

		    $adv_settings[] = array(
					'id'    =>'header-customjs-end',
					'type'  => 'section',
					'indent' => false,
			);

		    $adv_settings[] = array(
				'id'    =>'header-googleanalytics-start',
				'type'  => 'section',
				'indent' => true,
				'title' =>  esc_html__('Google Analytics Options', 'plethora-framework')
			);

		    $adv_settings[] = array(
					'id'       =>THEMEOPTION_PREFIX .'analyticsscript',
					'type'     => 'textarea',
					'title'    => esc_html__('Analytics tracking code', 'plethora-framework'),
					'subtitle' => esc_html__('Paste your Google Analytics or other code here.', 'plethora-framework'),
					'description' => '<span style="color:red;"><strong>'. esc_html__('Do not use &lt;script&gt; tags.', 'plethora-framework') .'</strong>.</span>',
					'default'     => '',
			);
		    $adv_settings[] = array(
					'id'          =>THEMEOPTION_PREFIX .'analyticsposition',
					'type'        => 'button_set',
					'title'       => esc_html__('Analytics tracking code placement', 'plethora-framework'),
					'options'     => array('header' => esc_html__('Head', 'plethora-framework'),'footer' => esc_html__('Footer', 'plethora-framework')),//Must provide key => value pairs for radio options
					'default'     => 'footer'
			);

		    $adv_settings[] = array(
					'id'    =>'header-googleanalytics-end',
					'type'  => 'section',
					'indent' => false,
			);

			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Scripts & Styles', 'plethora-framework'),
				'heading'      => esc_html__('SCRIPTS & STYLES', 'plethora-framework'),
				'fields'     => $adv_settings
				);

			return $sections;
	    }

	    /**
	     * Adds custom JS script to footer
	     *
	     * @param
	     * @return string
	     *
	     */
	    public static function output_customjs() {

			$custom_js = Plethora_Theme::option(THEMEOPTION_PREFIX .'customjs');
			if ( !empty( $custom_js ) ) { ?>
				<script type='text/javascript'>
					<?php echo trim($custom_js); ?>
				</script>
				<?php 
			} 
	    } 

	    /**
	     * Returns analytics script for header section
	     *
	     */
	    public static function output_analyticsjs() {

			$analytics_code_placement = Plethora_Theme::option(THEMEOPTION_PREFIX .'analyticsposition');

			$analytics_code = '';

			if ( current_filter() === 'wp_head' && $analytics_code_placement ===  'header' ) {

				$analytics_code = Plethora_Theme::option(THEMEOPTION_PREFIX .'analyticsscript');

			} elseif  ( current_filter() === 'wp_footer' && $analytics_code_placement ===  'footer' ) {

				$analytics_code = Plethora_Theme::option(THEMEOPTION_PREFIX .'analyticsscript');
			}
	        
			if ( !empty( $analytics_code ) ) { ?>
				<script type='text/javascript'>
					<?php echo trim($analytics_code); ?>
				</script>
				<?php 
			} 	        
	    } 

	    /**
	     * Adds custom CSS style field contents to head
	     *
	     */
	    public static function output_customcss() {

			$custom_css = Plethora_Theme::option( THEMEOPTION_PREFIX .'customcss' );
			$custom_css = apply_filters( 'plethora_inline_css', trim( $custom_css ) );
			if ( !empty( $custom_css ) ) { ?>
			<!-- USER DEFINED IN-LINE CSS -->
			<style>
				<?php echo trim( $custom_css ); ?>
			</style><?php
	    	}    
	    }
	    

// FINISH: CUSTOM SCRIPT/STYLE OUTPUT METHODS


/// JS INIT PATTERNS AND OTHER AUXILIARY SCRIPT PATTERNS RETURNED WITH Plethora_Theme::get_init_script() wrapper method


	}
}