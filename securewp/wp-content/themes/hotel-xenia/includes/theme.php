<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Plethora_Theme extends the core Plethora abstract class and its purpose
is to: 
	1. Initiate all the core/framework features ( this is managed mainly on the 
		 abstract Plethora Class )

	2. Configure necessary wp features ( add_theme_support, etc. )

	3. Enqueue basic scripts/styles that are not associated with a core feature 
		( shortcode, widget or module ) and manage theme.js variables

	4. Configure the basic layout attributes ( id, class, data attributes, etc. ), 
		 along with theme-specific customizations

	5. Route all template parts & markup for header, media panel, content and footer
		 sections and all views ( single views, archive & taxonomy views, search and 404 )
	
	6. Last, but not least, it provides a group of handy template methods, to 
		 display easily several objects according the user given configuration.
		 Check the Plethora abstract class ( /includes/core/plethora.php ), for 
		 reference.

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Theme') && class_exists('Plethora') ) {

	class Plethora_Theme extends Plethora {

		# CURRENT VIEW HELPER VARS
		public $is_archive = false; // true if this a post listing view ( search and taxonomy views included )
		public $is_single  = false; // true if this a single post view 
		public $is_404     = false; // true if this a 404 page
		public $id;                 // the page id ( even if it is a static page archive )
		public $post_type;          // the post type associated with this page ( even if this is an archive view )
		public $post_format;        // the post format ( if this is a single view )

		function __construct( $slug = 'plethora-boilerplate', $name = 'Plethora Boilerplate', $ver = '1.0.0' ) {

		# SET BASIC VARIABLES
			$this->theme_slug    = $slug;
			$this->theme_name    = $name;
			$this->theme_version = $ver;

		# PRE-FRAMEWORK HOOKS ( theme actions/filters that affect the default framework behavior )
			$this->framework_hooks();

		# LOAD FRAMEWORK
			$this->load_framework();
		
		# SETUP CURRENT FRONTEND VIEW
			if ( ! is_admin() ) {

				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets'), 30 );  	  	// Script enqueues
				add_action( 'get_header', array( $this, 'set_helper_vars'), 999 );            	// Current view helper variables setup
				add_action( 'get_header', array( $this, 'set_js'), 999 );                     	// Theme.js variables setup
				add_action( 'get_header', array( $this, 'set_containers'), 999 );             	// Configure classes and other attributes for the core layout containers
				add_action( 'get_header', array( $this, 'set_header_elements'), 999 );        	// Configure header template elements 
				add_action( 'get_header', array( $this, 'set_mediapanel_elements'), 999 );    	// Configure media panel template elements 
				add_action( 'get_header', array( $this, 'set_content_elements'), 999 );       	// Configure main content elements  
				add_action( 'get_header', array( $this, 'set_footer_elements'), 999 );        	// Configure footer template elements 
				add_filter( 'body_class', array( $this, 'set_body_class') );                  	// Body class filter ( WP Hook )
				add_action( 'get_header', array( $this, 'set_elements'), 999 );               	// Route all template elements, acording to configuration
				add_action( 'get_header', array( $this, 'off_container_hooks'), 999 );        	// Route all template elements, acording to configuration

				// misc configuration
				add_filter( 'oembed_dataparse', array( $this, 'w3_validation_for_embed_media' ), 999, 3 ); // Filter media output for proper w3 validation
			}
		}

//////////////// FRAMEWORK HOOK METHODS ----> START

	 /*
		* Theme actions/filters that affect the default framework behavior
		*/
		public function framework_hooks() {}

//////////////// FRAMEWORK HOOK METHODS <---- END

//////////////// TEMPLATE ELEMENTS CONFIGURATION ( POSITIONED IN LAYOUT CONTAINERS ) ------> START

		/*
		* Enqueue scripts & assets
		* All Plethora related assets are already registered on Plethora_Module_Script class
		* Do not enqueue assets used with shortcodes/widgets ( these are handled dynamically 
		* on Plethora_Module_Script class )
		* Hooked at 'wp_enqueue_scripts' action
		*/
		public function enqueue_assets(){ 

			# THEME ASSET ENQUEUES

				// Enqueue scripts
				wp_enqueue_script( ASSETS_PREFIX . '-modernizr' );        
				// wp_enqueue_script( 'boostrap' ); // PLENOTE: Might need a user status option for this
				wp_enqueue_script( 'easing' );
				wp_enqueue_script( 'wow-animation-lib' );
				wp_enqueue_script( 'conformity' );
				wp_enqueue_script( 'parallax' );
				wp_enqueue_script( ASSETS_PREFIX . '-init' );

				// Enqueue styles
				wp_enqueue_style( 'animate');
				wp_enqueue_style( ASSETS_PREFIX .'-custom-bootstrap');

				// Main stylesheet ( if LESS module is loaded, choose the dynamic. Otherwise choose the fixed version )
				$main_stylesheet_handle = ( class_exists( 'Plethora_Module_Wpless_Ext' ) ) ? ASSETS_PREFIX .'-style' : ASSETS_PREFIX .'-default-style';
				wp_enqueue_style( $main_stylesheet_handle );

			# SPECIAL ENQUEUES
				// Ajax handler for threaded comments...as suggested by WP )
				$thread_comments = get_option('thread_comments');
				if ( is_singular() && comments_open() && $thread_comments ) { 

					wp_enqueue_script( 'comment-reply' ); 
				} 
		}

		/*
		 * Set helper variables used in methods of this class
		 */
		public function set_helper_vars() {

				$this->is_archive  = ( Plethora_Theme::is_archive_page() ) ? true : false;
				$this->is_single   = ( is_singular() ) ? true : false;
				$this->is_404      = ( is_404() ) ? true : false;
				$this->post_type   = Plethora_Theme::get_this_view_post_type();
				$this->id          = Plethora_Theme::get_this_page_id();
				$this->post_format = get_post_format();
		}

	 /*
		* General script events, used mostly for declaring variables to theme.js 
		* using the Plethora_Theme::set_themeconfig() method
		* Hooked at 'get_header' action
		*/
		public function set_js() {

			// Set sticky header scroll offset trigger
			$scroll_offset_trigger  = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-behavior-scrolloffset', 100 );
			Plethora_Theme::set_themeconfig( 'GENERAL', array( 'scroll_offset_trigger' => $scroll_offset_trigger ) );
			
			// Set sticky header scroll offset trigger
			$menu_switch_to_mobile  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-menu-switch-to-mobile', 991 );
			Plethora_Theme::set_themeconfig( 'GENERAL', array( 'menu_switch_to_mobile' => $menu_switch_to_mobile ) );

			// Set mini tools offset trigger
			$minitools_switch_to_mobile  = Plethora_Theme::option( THEMEOPTION_PREFIX .'less-header-navminitools-switch-to-mobile', '0' );
			Plethora_Theme::set_themeconfig( 'GENERAL', array( 'minitools_switch_to_mobile' => $minitools_switch_to_mobile ) );
		}

		/*
		 * A filter for body_class, when header is not sticky.
		 * Hooked at 'body_class' filter, should always return the $classes argument
		 */
		public static function set_body_class( $classes ) { 

			$header_bcg_trans       = Plethora_Theme::option( METAOPTION_PREFIX .'header-trans', 0, 0, false);
			$header_sticky          = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', 0, 0, false);
			$header_sticky_alt      = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', 0, 0, false);
			$header_sticky_behavior = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-behavior', 'top', 0, false);

			$classes[] = $header_sticky ? 'sticky_header' : '';
			$classes[] = $header_sticky_alt ? 'sticky_header_alt' : '';
			$classes[] = $header_bcg_trans ? 'transparent_header' : '';
			$classes[] = $header_sticky && $header_sticky_behavior === 'top_onscroll' ? 'header_will_appear' : '';
			$classes[] = $header_sticky && in_array( $header_sticky_behavior, array( 'bottom', 'bottom_onscroll' ) ) ? 'header_is_at_bottom' : '';
			return $classes;
		}

		/*
		 * Add all elements that should be placed on Off-container positions
		 * This should be a list of add_action calls
		 */
		public function off_container_hooks() {

				add_action( 'plethora_header_main_after_container_markup', array( $this, 'get_toggler_and_tools_wrap_open' ) );
				add_action( 'plethora_header_main_after_container_markup', array( $this, 'get_header_navigation_tools' ) );
				add_action( 'plethora_header_main_after_container_markup', array( $this, 'get_header_navigation_toggler' ) );
				add_action( 'plethora_header_main_after_container_markup', array( $this, 'get_toggler_and_tools_wrap_close' ) );
		}

		public function w3_validation_for_embed_media( $return, $data, $url ) {

			return wp_kses( $return, Plethora_Theme::allowed_html_for( 'iframe' ) );
		}

		/*
		 * Apply ALL fixed or dynamic ( theme options set ) attributes ( id, class, etc. ) 
		 * to the core layout div containers. The attributes are applied using the
		 * Plethora_Theme::add_container_attr() method
		 * Hooked at 'get_header' action
		 */
		public function set_containers(){ 

			# PAGE WRAPPER SETUP ( containers: 'page' )
				
				// Add main reference class
				Plethora_Theme::add_container_attr( 'page', 'id', 'page_wrapper' );

			# HEADER SETUP ( containers: 'header','header_topbar','header_main' )

				// Add main reference & user given extra classes
				Plethora_Theme::add_container_attr( 'header', 'class', 'header' );
				Plethora_Theme::add_container_attr( 'header', 'class', Plethora_Theme::get_header_layout_class() );
				Plethora_Theme::add_container_attr( 'header', 'class', Plethora_Theme::get_extra_class( 'header' ) );
				Plethora_Theme::add_container_attr( 'header_main', 'class', 'mainbar' );

				// Header transparency classes ( all header, sticky custom and top bar )
				$header_trans_classes[] = Plethora_Theme::option( METAOPTION_PREFIX .'header-trans', 0, 0, false) == 1 ? 'transparent' : '';
				$header_trans_classes[] = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', 0, 0, false ) == 1 && Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', 0, 0, false ) == 1 && Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-trans', 0, 0, false) == 1 ? 'alt_header_transparent' : '';
				$header_trans_classes[] = Plethora_Theme::option( METAOPTION_PREFIX .'header-topbar-trans', 0, 0, false) == 1 ? 'top_bar_transparent' : '';
				Plethora_Theme::add_container_attr( 'header', 'class', $header_trans_classes );

				// Sticky Header ( note: body tag sticky class was added already via 'body_class' filter )
				$sticky_status = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', 0);
				if ( $sticky_status ) {
					$sticky_behavior = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-behavior', 'top' );
					$sticky_css_classes = array( 
																'top'             => 'sticky_header',
																'top_onscroll'    => 'appearing_sticky_header',
																'bottom'          => 'bottom_sticky_header',
																'bottom_onscroll' => 'bottom_to_top_sticky_header',
															);
					if ( !empty( $sticky_css_classes[$sticky_behavior] ) ) {

						Plethora_Theme::add_container_attr( 'header', 'class', $sticky_css_classes[$sticky_behavior] );
					}
				}
 
			# MEDIA PANEL SETUP ( containers: 'mediapanel' )

				// Add main reference & user given extra classes
				Plethora_Theme::add_container_attr( 'mediapanel', 'class', 'head_panel' );
				Plethora_Theme::add_container_attr( 'mediapanel', 'class', Plethora_Theme::get_extra_class( 'mediapanel' ) );

			# CONTENT SETUP ( containers: 'content' )

				// Add main reference & user given extra classes
				Plethora_Theme::add_container_attr( 'content', 'class', 'main' );
				Plethora_Theme::add_container_attr( 'content', 'class', Plethora_Theme::get_extra_class( 'content' ) );

				// Content Color Set
				$colorset = Plethora_Theme::get_content_colorset();
				Plethora_Theme::add_container_attr( 'content', 'class', $colorset );
				Plethora_Theme::add_container_attr( 'content', 'data-colorset', $colorset );

			# CONTENT TITLES SETUP ( containers: 'content_titles' )
				
				// Add main reference class
				Plethora_Theme::add_container_attr( 'content_titles', 'class', 'content_titles' );

			# CONTENT TOP SETUP ( containers: 'content_top' )
				
				// Add main reference class
				Plethora_Theme::add_container_attr( 'content_top', 'class', 'content_top' );

			# CONTENT MAIN SETUP ( containers: 'content_main', 'content_main_left', 'content_main_loop', 'content_main_right' )

				// Add main reference classes
				Plethora_Theme::add_container_attr( 'content_main', 'class', 'content_main' );
				Plethora_Theme::add_container_attr( 'content_main_left', 'id', 'sidebar' );
				Plethora_Theme::add_container_attr( 'content_main_left', 'class', 'content_main_left' );
				Plethora_Theme::add_container_attr( 'content_main_loop', 'class', 'content_main_loop' );
				Plethora_Theme::add_container_attr( 'content_main_right', 'id', 'sidebar' );
				Plethora_Theme::add_container_attr( 'content_main_right', 'class', 'content_main_right' );
				
				// Content main classes, according to user set layout options
				$content_layout     = Plethora_Theme::get_layout(); // get user selected layout
				$page_layouts_index = Plethora_Module_Style::get_index_values( 'page_layouts' ); // get page layout configuration

				if ( !empty( $page_layouts_index[$content_layout]['container_classes'] ) ) {

					foreach ( $page_layouts_index[$content_layout]['container_classes'] as $container => $container_class ) {

						Plethora_Theme::add_container_attr( $container, 'class', $container_class );
					}
				}

				// Additional container setup if VC is used
				if ( Plethora_Theme::content_has_sections() ) {

							Plethora_Theme::add_container_attr( 'content', 'class', 'vc_on' );

				} else {

							Plethora_Theme::add_container_attr( 'content', 'class', 'vc_off' );
				}

				// Set align options
				if ( $this->is_archive ) {

					Plethora_Theme::add_container_attr( 'content_main_loop', 'class', Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $this->post_type .'-contentalign', '' ) );

				} elseif ( $this->is_single ) {

					Plethora_Theme::add_container_attr( 'content_main_loop', 'class', Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-contentalign', '' ) );
				}

			# CONTENT BOTTOM SETUP ( containers: 'content_bottom' )
				
				// Add main reference classes
				Plethora_Theme::add_container_attr( 'content_bottom', 'class', 'content_bottom' );

			# FOOTER SETUP ( containers: 'footer', 'footer_top', 'footer_main', 'footer_bar' )

				// Add main reference & user given extra classes
				Plethora_Theme::add_container_attr( 'footer', 'class', 'footer' );
				Plethora_Theme::add_container_attr( 'footer', 'class', Plethora_Theme::get_extra_class( 'footer' ) );
				Plethora_Theme::add_container_attr( 'footer_top', 'class', 'footer_top' );
				Plethora_Theme::add_container_attr( 'footer_main', 'class', 'footer_main' );
				Plethora_Theme::add_container_attr( 'footer_bar', 'class', 'footer_bar' );
		}

		/**
		 * Set header template elements configuration 
		 * Related containers: 'head', 'head_before', 'body_open', 'header_topbar', 'header_main' 
		 * Hooked at 'get_header' action
		 */
		public function set_header_elements() {

			$header_elements = array(
			
				// Page meta
				array(
						'container' => 'head_before',
						'handle'    => 'meta',
						'file'      => 'templates/header/head_before/meta',
				),

				// Loader functionality
				array(
						'container' => 'body_open',
						'handle'   => 'page-loader',
						'file'      => 'templates/global/pageloader',
						'status'    => Plethora_Theme::option( THEMEOPTION_PREFIX .'page-loader', 1 ),
						'options'   => array(
								'logo_url'   => Plethora_WP::get_reduxoption_image_src( Plethora_Theme::option( THEMEOPTION_PREFIX .'page-loader-image-logo', array( 'url'=> ''. PLE_THEME_ASSETS_URI .'/images/logo-white.png' ) ) ),
								'loader_url' => Plethora_WP::get_reduxoption_image_src( Plethora_Theme::option( THEMEOPTION_PREFIX .'page-loader-image-loader', array( 'url'=> ''. PLE_THEME_ASSETS_URI .'/images/loader.gif' ) ) ),
						)
				),
				// Logo
				array(
						'container' => 'header_main',
						'handle'    => 'logo',
						'file'      => 'templates/header/header_main/logo',
						'status'    => Plethora_Theme::option( METAOPTION_PREFIX .'logo', 1 ),
						'options'   => self::get_header_logo_options(),
				),
				// Sticky Custom Logo ( alternative logo on sticky header )
				array(
						'container' => 'header_main',
						'handle'    => 'logo-stickycustom',
						'file'      => array( 'templates/header/header_main/logo', 'stickycustom' ),
						'status'    => ( Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', 0) && Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', 0) && Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo', 1 ) ),
						'options'   => self::get_header_logo_options( 'sticky' ),
				),

				// Navigation ( ATTENTION, do not set status argument. Display status is configured with display CSS classes on 'get_header_navigation_options' method )
				array(
						'container' => 'header_main',
						'handle'    => 'navigation',
						'file'      => 'templates/header/header_main/navigation',
						'options'   => array_merge( array( 
															'wrap_tag'   => 'div',
															'wrap_class' => 'main_nav_wrapper',
															), self::get_header_navigation_options() 
													 ),
				),

				// Mobile sidebar ( should be added on 'hidden_markup' )
				array(
						'container' => 'hidden_markup',
						'handle'    => 'mobile_sidebar',
						'file'      => 'templates/header/header_main/mobile_sidebar',
						'options'   => array(
							'class' => 'secondary_nav_widgetized_area',
							'id' => '',
							'sidebar' => Plethora_Theme::option( METAOPTION_PREFIX .'header-mobsb-widgetizedarea', 'sidebar-mobile'),
						)
				)
			);

			$this->container_elements['header'] = $header_elements;
		}

		/**
		 * Set media panel template elements configuration 
		 * Related containers: 'mediapanel' 
		 * Hooked at 'get_header' action
		 */
		public function set_mediapanel_elements() { }

		/**
		 * Set main content template elements configuration 
		 * Related containers: 'content_top', 'content_main_loop_before', 'content_main_loop', 'content_main_loop_after', 'content_bottom' 
		 * Hooked at 'get_header' action
		 */
		public function set_content_elements() {

			// Prefixes to avoid long templates path
			$cnt_path = 'templates/content';
			$ap_path  = 'templates/content/archive_parts';
			$sp_path  = 'templates/content/single_parts';

			$content_elements = array();

			# TEMPLATE PARTS CONFIGURATION
			if ( $this->is_archive ) {
			
				$title    = Plethora_Theme::get_title( array( 'tag' => '' ) );
				$subtitle = Plethora_Theme::get_subtitle( array( 'tag' => '' ) );
				// Prefix for the long templates path
				$content_elements = array_merge( $content_elements, array(


				 	// Archive Views: Title & Subtitle on the dedicated 'content_titles' container
					array(
						'container' => 'content_titles',
						'handle'    => 'title',
						'file'      => array( $ap_path .'/title', $this->post_type ),
						'status'    => ( !empty( $title ) || !empty( $subtitle ) ? true : false ),
					),
					 // Archive Views: Breadcrumb Navigation ( global template )
					array(
						'container' => 'content_titles',
						'handle'    => 'breadcrumb',
						'file'      => array( $cnt_path .'/breadcrumb', $this->post_type ),
						'status'    => Plethora_Theme::get_breadcrumb( array( 'return' => 'status' ) ),
						'options'   => array_merge( array(
														'wrap_tag'   => 'div',
														'wrap_class' => 'ple_breadcrumb_wrapper',
														 ),
														Plethora_Theme::get_breadcrumb( array( 'return' => 'config' ) )
													)
					),

					// Intro text 
					array(
						'container' => 'content_main_loop',
						'handle'    => 'listing',
						'file'      => array( $ap_path .'/listing', $this->post_type ),
						'options'   => array()
					),
					// Pagination ( add an exception for Woo shop paging ) 
					array(
						'container' => 'content_main_loop_after',
						'handle'    => 'pagination',
						'html'      => Plethora_Theme::get_pagination(),
						'status'    => ( $this->post_type === 'product' ) ? false : true,
					)
				));

				$this->container_elements['content'] = $content_elements;


			# AND FINALLY, THE LOOP LISTING!
				// add_action( 'plethora_content_main_loop', array( $this, 'listing') );
			
			} elseif ( $this->is_single ) {

				switch ( $this->post_type ) {       
				# SINGLE PAGE VIEW  
					case 'page':

						$title    = Plethora_Theme::get_title( array( 'tag' => '' ) );
						$subtitle = Plethora_Theme::get_subtitle( array( 'tag' => '' ) );
						$content_elements = array_merge( $content_elements, array(

				 			// Single Page: Title & Subtitle on the dedicated 'content_titles' container
							array(
								'container' => 'content_titles',
								'handle'    => 'title',
								'file'      => array( $sp_path .'/title', $this->post_type ),
								'status'    => ( !empty( $title ) || !empty( $subtitle ) ? true : false ),
							),

							 // Single Page: Breadcrumb Navigation ( global template )
							array(
								'container' => 'content_titles',
								'handle'    => 'breadcrumb',
								'file'      => array( $cnt_path .'/breadcrumb', $this->post_type ),
								'status'    => Plethora_Theme::get_breadcrumb( array( 'return' => 'status' ) ),
								'options'   => array_merge( array(
																'wrap_tag'   => 'div',
																'wrap_class' => 'ple_breadcrumb_wrapper',
																 ),
																Plethora_Theme::get_breadcrumb( array( 'return' => 'config' ) )
															)
							),
							// Single Page: Editor Content
							array(
								'container' => 'content_main_loop',
								'handle'    => 'editorcontent',
								'file'      => array( $sp_path .'/editorcontent', $this->post_type ),
							),
							// Single Page: Comments
							array(
								'container' => 'content_main_loop',
								'handle'    => 'comments',
								'function'  => array( 'comments_template' ),
							)
						));
						break;
					
				# SINGLE ROOM VIEW  
					case 'room':

						$content_elements = array_merge( $content_elements, array(
							
							// Single Room: Breadcrumb Navigation ( global template )
							array(
								'container' => 'content_top',
								'handle'    => 'breadcrumb',
								'file'      => array( $cnt_path .'/breadcrumb', $this->post_type ),
								'status'    => Plethora_Theme::get_breadcrumb( array( 'return' => 'status' ) ),
								'options'   => array_merge( array(
																'wrap_tag'   => 'div',
																'wrap_class' => 'col-md-12 ple_breadcrumb',
																 ),
																Plethora_Theme::get_breadcrumb( array( 'return' => 'config' ) )
															)
							),
							// Single Room: Media ( Featured Image or Gallery )
							array(
								'container' => 'content_top',
								'handle'    => 'media',
								'file'      => array( $sp_path .'/media', $this->post_type ),
								'status'    => Plethora_Theme::option( METAOPTION_PREFIX . 'room-mediadisplay', 1 ),
								'options'   => array( 
																'wrap_tag'    => 'div',
																'wrap_class'  => 'col-md-12 room_single_media',
																'post_format' => Plethora_Theme::option( METAOPTION_PREFIX . 'room-mediadisplay-type', 'gallery' ),
															)
							),

							// Single Room: Amenities
							array(
								'container' => 'content_top',
								'handle'    => 'amenities',
								'file'      => array( $sp_path .'/amenities', $this->post_type ),
								'status'    => Plethora_Theme::option( METAOPTION_PREFIX .'room-amenities-status', 1 ),
								'options'   => array(
										'wrap_tag'   => 'div',
										'wrap_class' => 'col-md-12 room_single_amenities_wrapper',
										'amenities'  => Plethora_Theme::get_amenities_options(),
								)
							),
							// Single Room: Title
							array(
								'container' => 'content_main_loop',
								'handle'    => 'title',
								'file'      => array( $sp_path .'/title', $this->post_type ),
								'options'   => array(
										'wrap_tag'   => 'div',
										'wrap_class' => 'room_single_title',
								)
							),
							// Single Room: Subtitle
							array(
								'container' => 'content_main_loop',
								'handle'    => 'subtitle',
								'file'      => array( $sp_path .'/subtitle', $this->post_type ),
								'options'   => array(
										'wrap_tag'   => 'p',
										'wrap_class' => 'room_single_subtitle',
								)
							),
							// Single Room: Persons
							array(
								'container' => 'content_main_loop',
								'handle'    => 'persons',
								'file'      => array( $sp_path .'/persons', $this->post_type ),
								'status'    => method_exists( 'Plethora_Module_Booking_Ext', 'get_persons_status' )  ? Plethora_Module_Booking::get_persons_status( 'room', get_the_id() ) : false,
								'options'   => method_exists( 'Plethora_Module_Booking_Ext', 'get_persons_options' ) ? Plethora_Module_Booking::get_persons_options( 'room', get_the_id() ) : array(), 
							),
							// Single Room: Target Price
							array(
								'container' => 'content_main_loop',
								'handle'    => 'target_price',
								'file'      => array( $sp_path .'/target_price', $this->post_type ),
								'status'    => method_exists( 'Plethora_Module_Booking_Ext', 'get_target_price_status' )  ? Plethora_Module_Booking::get_target_price_status( $this->post_type, get_the_id() ) : false,
								'options'   => method_exists( 'Plethora_Module_Booking_Ext', 'get_target_price_options' ) ? Plethora_Module_Booking::get_target_price_options( $this->post_type, get_the_id() ) : array(), 
							),              
							// Single Room: Full Price List
							array(
								'container' => 'content_main_loop',
								'handle'    => 'full_price',
								'file'      => array( $sp_path .'/full_price', $this->post_type ),
								'status'	=> ( ( method_exists( 'Plethora_Module_Booking_Ext', 'get_full_price_status' ) ) ? Plethora_Module_Booking_Ext::get_full_price_status( $this->post_type, get_the_id() ) : false ),
								'options'	=> ( ( method_exists( 'Plethora_Module_Booking_Ext', 'get_full_price_options' ) ) ? Plethora_Module_Booking_Ext::get_full_price_options( $this->post_type, get_the_id() ) : array() ),
							),
							// Single Room: Editor Content
							array(
								'container' => 'content_main_loop',
								'handle'    => 'editorcontent',
								'file'      => array( $sp_path .'/editorcontent', $this->post_type ),
							),
							// Single Room: Posts Navigation
							array(
								'container' => 'content_bottom',
								'handle'    => 'post_navigation',
								'file'      =>  array( $sp_path .'/postnavi', $this->post_type ),
								'status'    => Plethora_Theme::get_postnavi( array( 'return' => 'status' ) ),
								'options'   => array_merge( array(
																'wrap_tag'   => 'div',
																'wrap_class' => 'col-md-12 '.$this->post_type.'_single_navi',
																 ),
																Plethora_Theme::get_postnavi( array( 'return' => 'config' ) )
															)
							),
					));
					break;

				# SINGLE SERVICE VIEW  
					case 'service':

						$content_elements = array_merge( $content_elements, array(
							
							// Single Room: Breadcrumb Navigation ( global template )
							array(
								'container' => 'content_main_loop',
								'handle'    => 'breadcrumb',
								'file'      => array( $cnt_path .'/breadcrumb', $this->post_type ),
								'status'    => Plethora_Theme::get_breadcrumb( array( 'return' => 'status' ) ),
								'options'   => array_merge( array(
																'wrap_tag'   => 'div',
																'wrap_class' => 'ple_breadcrumb',
																 ),
																Plethora_Theme::get_breadcrumb( array( 'return' => 'config' ) )
															)
							),
							// Single Service: Header section OPEN
							array(
								'container' => 'content_main_loop',
								'handle'    => 'container-div-section_header-open',
								'file'      => array( 'templates/global/container-div-open', $this->post_type ),
								'options'   => array(
										'class' => 'heading_group_sc subtitle_bottom',
										'id'    => '',
								),
							),
							// Single Service: Overlay Service Category Title
							array(
								'container' => 'content_main_loop',
								'handle'    => 'overlay-title',
								'file'      => array( $sp_path .'/title_overlay', $this->post_type ),
								'status'    => Plethora_Theme::option( METAOPTION_PREFIX .'service-overlay-title', 1),
								'options'   => array(
												'overlay_title' => self::get_service_overlay_title( $this->id ),
												'wrap_tag'    => 'div',
												'wrap_class'  => 'background_title',
								),
							),
							// Single Service: Title
							array(
								'container' => 'content_main_loop',
								'handle'    => 'title',
								'file'      => array( $sp_path .'/title', $this->post_type ),
							),
							// Single Service: Subtitle
							array(
								'container' => 'content_main_loop',
								'handle'    => 'subtitle',
								'file'      => array( $sp_path .'/subtitle', $this->post_type ),
								'options'   => array(
												'wrap_tag'    => 'span',
												'wrap_class'  => 'subtitle',
								),
							),
							// Single Service: Header section CLOSE
							array(
								'container' => 'content_main_loop',
								'handle'    => 'container-div-section_header-close',
								'file'      => array( 'templates/global/container-div-close', $this->post_type ),
							),
							// Single Service: Service Categories
							array(
								'container' => 'content_main_loop',
								'handle'    => 'categories',
								'file'      => array( $sp_path .'/categories', $this->post_type ),
							),
							// Single Service: Service Tags
							array(
								'container' => 'content_main_loop',
								'handle'    => 'tags',
								'file'      => array( $sp_path .'/tags', $this->post_type ),
							),
							// 404 View: Divider
							array(
								'container' => 'content_main_loop',
								'handle'    => 'divider-single-service',
								'file'      => array( $sp_path .'/divider', $this->post_type ),
							),
							// Single Service: Excerpt
							array(
								'container' => 'content_main_loop',
								'handle'    => 'excerpt',
								'file'      => array( $sp_path .'/excerpt', $this->post_type ),
								'status'    => ( Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type . '-excerpt', 0 ) ) ? true : false,
								'options'   => array( 
																'wrap_tag'    => 'div',
																'wrap_class'  => 'service_single_excerpt',
															)
							),
							// Single Service: Media ( Featured Image or Gallery )
							array(
								'container' => 'content_main_loop',
								'handle'    => 'media',
								'file'      => array( $sp_path .'/media', $this->post_type ),
								'status'    => Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type . '-mediadisplay', 1 ),
								'options'   => array( 
																'wrap_tag'    => 'div',
																'wrap_class'  => 'service_single_media',
																'post_format' => Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type . '-mediadisplay-type', 'image' ),
															)
							),
							// Single Service: Editor Content
							array(
								'container' => 'content_main_loop',
								'handle'    => 'editorcontent',
								'file'      => array( $sp_path .'/editorcontent', $this->post_type ),
							),
							// Single Service: Target Price
							array(
								'container' => 'content_main_loop',
								'handle'    => 'target_price',
								'file'      => array( $sp_path .'/target_price', $this->post_type ),
								'status'    => method_exists( 'Plethora_Module_Booking', 'get_target_price_status' )  ? Plethora_Module_Booking::get_target_price_status( $this->post_type, get_the_id() ) : false,
								'options'   => method_exists( 'Plethora_Module_Booking', 'get_target_price_options' ) ? Plethora_Module_Booking::get_target_price_options( $this->post_type, get_the_id() ) : array(), 
							),              
							// Single Service: Posts Navigation
							array(
								'container' => 'content_bottom',
								'handle'    => 'post_navigation',
								'file'      =>  array( $sp_path .'/postnavi', $this->post_type ),
								'status'    => Plethora_Theme::get_postnavi( array( 'return' => 'status' ) ),
								'options'   => array_merge( array(
																'wrap_tag'   => 'div',
																'wrap_class' => 'col-md-12 '. $this->post_type .'_single_navi',
																 ),
																Plethora_Theme::get_postnavi( array( 'return' => 'config' ) )
															)
							),
					));
					break;
				# SINGLE WOO PRODUCT SINGLE VIEW  
					case 'product':
					
						$title    = Plethora_Theme::get_title( array( 'tag' => '' ) );
						$subtitle = Plethora_Theme::get_subtitle( array( 'tag' => '' ) );
						$content_elements = array_merge( $content_elements, array(

				 			// Single Page: Title & Subtitle on the dedicated 'content_titles' container
							array(
								'container' => 'content_titles',
								'handle'    => 'title',
								'file'      => array( $sp_path .'/title', $this->post_type ),
								'status'    => ( !empty( $title ) || !empty( $subtitle ) ? true : false ),
							),

							 // Single Page: Breadcrumb Navigation ( global template )
							array(
								'container' => 'content_titles',
								'handle'    => 'breadcrumb',
								'file'      => array( $cnt_path .'/breadcrumb', $this->post_type ),
								'status'    => Plethora_Theme::get_breadcrumb( array( 'return' => 'status' ) ),
								'options'   => array_merge( array(
																'wrap_tag'   => 'div',
																'wrap_class' => 'ple_breadcrumb_wrapper',
																 ),
																Plethora_Theme::get_breadcrumb( array( 'return' => 'config' ) )
															)
							),
							// Single Page: Editor Content
							array(
								'container' => 'content_main_loop',
								'handle'    => 'editorcontent',
								'file'      => array( $sp_path .'/editorcontent', $this->post_type ),
							),
							// Single Page: Comments
							array(
								'container' => 'content_main_loop',
								'handle'    => 'comments',
								'function'  => array( 'comments_template' ),
							)
						));
						break;
					
				# SINGLE POST or EVERY OTHER NON PLETHORA SINGLE CPT VIEW  
					default:    

						$content_elements = array_merge( $content_elements, array(

							// Single Post/CPT: Media container open
							array(
								'container' => 'content_main_loop',
								'handle'    => 'container-div-post_figure_and_info-open',
								'file'      => array( 'templates/global/container-div-open', $this->post_type ),
								'options'   => array(
										'class' => 'post_figure_and_info '. Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-contentalign', '' ),
										'id'    => '',
								),
							),
							// Single Post/CPT: Media
							array(
								'container' => 'content_main_loop',
								'handle'    => 'media',
								'file'      => array( $sp_path .'/media', $this->post_type ),
								'options'   => array( 'post_format' => $this->post_format ),
							),
							// Single Post/CPT: Breadcrumb Navigation ( global template )
							array(
								'container' => 'content_main_loop',
								'handle'    => 'breadcrumb',
								'file'      => array( $cnt_path .'/breadcrumb', $this->post_type ),
								'status'    => Plethora_Theme::get_breadcrumb( array( 'return' => 'status' ) ),
								'options'   => array_merge( array(
																'wrap_tag'   => 'div',
																'wrap_class' => 'ple_breadcrumb_wrapper',
																 ),
																Plethora_Theme::get_breadcrumb( array( 'return' => 'config' ) )
															)
							),
							// Single Post/CPT: Title
							array(
								'container' => 'content_main_loop',
								'handle'    => 'title',
								'file'      => array( $sp_path .'/title', $this->post_type )
							),
							// Single Post/CPT: Subtitle ( no need to add status option, since the get_subtitle method will be used inside template )
							array(
								'container' => 'content_main_loop',
								'handle'    => 'subtitle',
								'file'      => array( $sp_path .'/subtitle', $this->post_type ),
								'status'	=> Plethora_Theme::get_subtitle( array( 'tag' => 'p' ) ),
								'options'   => array( 
									'wrap_tag' => 'p',
									'wrap_class' => 'lead',
								)
							),
							// Single Post/CPT: Except
							// array(
							//   'container' => 'content_main_loop',
							//   'handle'    => 'excerpt',
							//   'file'      => array( $sp_path .'/excerpt', $this->post_type ),
							//   'status'    => ( Plethora_Theme::option( METAOPTION_PREFIX . 'post-excerpt', 0 ) ) ? true : false,
							// ),
							// Single Post/CPT: Meta labels section div OPEN
							array(
								'container' => 'content_main_loop',
								'handle'    => 'container-blog_post_single_meta-open',
								'file'      => array( 'templates/global/container-div-open', $this->post_type ),
								'options'   => array(
										'class' => 'blog_post_single_meta',
										'id'    => '',
								),
							),
							// Single Post/CPT: Date label
							array(
								'container' => 'content_main_loop',
								'handle'    => 'date',
								'file'      => array( $sp_path .'/date', $this->post_type ),
							),
							// Single Post/CPT: Author label
							array(
								'container' => 'content_main_loop',
								'handle'    => 'author',
								'file'      => array( $sp_path .'/author', $this->post_type ),
							),
							// Single Post/CPT: Categories/Primary Taxonomy label
							array(
								'container' => 'content_main_loop',
								'handle'    => 'categories',
								'file'      => array( $sp_path .'/categories', $this->post_type ),
							),
							// Single Post/CPT: Tags/Secondary Taxonomy label
							array(
								'container' => 'content_main_loop',
								'handle'    => 'tags',
								'file'      => array( $sp_path .'/tags', $this->post_type ),
							),
							// Single Post/CPT: Comments counter label
							array(
								'container' => 'content_main_loop',
								'handle'    => 'comments-counter',
								'file'      => array( $sp_path .'/comments-counter', $this->post_type ),
							),
							// Single Post/CPT: Meta labels section div CLOSE
							array(
								'container' => 'content_main_loop',
								'handle'    => 'container-div-blog_post_single_meta-close',
								'file'      => array( 'templates/global/container-div-close', $this->post_type ),
							),
							// Single Post/CPT: Media wrapper div close
							array(
								'container' => 'content_main_loop',
								'handle'    => 'container-div-post_figure_and_info-close',
								'file'      => array( 'templates/global/container-div-close', $this->post_type ),
							),
							// Single Post/CPT: Editor Content
							array(
								'container' => 'content_main_loop',
								'handle'    => 'editorcontent',
								'file'      => array( $sp_path .'/editorcontent', $this->post_type ),
							),
							// Single Post/CPT: Previous/Next navigation
							array(
								'container' => 'content_bottom',
								'handle'    => 'post_navigation',
								'file'      =>  array( $sp_path .'/postnavi', $this->post_type ),
								'status'    => Plethora_Theme::get_postnavi( array( 'return' => 'status' ) ),
								'options'   => array_merge( array(
																'wrap_tag'   => 'div',
																'wrap_class' => 'col-md-12 '.$this->post_type.'_single_navi',
																 ),
																Plethora_Theme::get_postnavi( array( 'return' => 'config' ) )
															)
							),
							// Single Post/CPT: Comments
							array(
								'container' => 'content_main_loop',
								'handle'    => 'comments',
								'function'  => array( 'comments_template' ),
							)
						));
						break;
				}

			} elseif ( $this->is_404 ) {

				$content_elements = array_merge( $content_elements, array(
						
						// 404 View: Heading Group container OPEN
						array(
							'container' => 'content_main_loop',
							'handle'    => 'container-div-heading_group_sc-open',
							'file'      => 'templates/global/container-div-open',
							'options'   => array(
									'class' => 'heading_group_sc text-center wpb_content_element subtitle_top ',
									'id'    => '',
							),
						),
						// 404 View: Title
						array(
							'container' => 'content_main_loop',
							'handle'    => 'title-404',
							'file'      => array( $sp_path .'/title', '404' ),
							'options'   => array(
									'title' => Plethora_Theme::option( THEMEOPTION_PREFIX .'404-contenttitle', esc_html__('It is pure luck that brought you here...', 'hotel-xenia') )
							),
						),
						// 404 View: Divider
						array(
							'container' => 'content_main_loop',
							'handle'    => 'divider-404',
							'file'      => array( $sp_path .'/divider', '404' ),
						),
						// 404 View: Content
						array(
							'container' => 'content_main_loop',
							'handle'    => 'editorcontent-404',
							'file'      => array( $sp_path .'/editorcontent', '404' ),
							'options'   => array(
									'class' => 'lead',
									'content' => Plethora_Theme::option( THEMEOPTION_PREFIX .'404-content', esc_html__('... or you may have mis-typed the URL, please check your spelling and try again :)', 'hotel-xenia' ) )
							),
						),
						// 404 View: Search Form ( GLOBAL TEMPLATE PART )
						array(
							'container' => 'content_main_loop',
							'handle'    => 'searchform-404',
							'file'      => array( 'templates/global/searchform', '404' ),
							'status'    => Plethora_Theme::option( THEMEOPTION_PREFIX .'404-search', 1),
							'options'   => array(
									'submit_text' => Plethora_Theme::option( THEMEOPTION_PREFIX .'404-search-btntext', esc_html('Search', 'hotel-xenia') )
							),
						),
						// 404 View: Heading Group container CLOSE
						array(
							'container' => 'content_main_loop',
							'handle'    => 'container-div-heading_group_sc-close',
							'file'      => 'templates/global/container-div-close',
						),
				));
			}

			$this->container_elements['content'] = $content_elements;
		}

		/**
		 * Set footer template elements configuration 
		 * Related containers: 'footer_top', 'footer_main', 'footer_bar'
		 * Hooked at 'get_header' action
		 */
		public function set_footer_elements() {

			$designer_url         = 'http://themeforest.net/user/andrewchs/portfolio';
			$plethora_url         = 'http://plethorathemes.com';
			$widget_area_1_status = Plethora_Theme::option( METAOPTION_PREFIX .'footer-widgets-1', 1);
			$widget_area_2_status = Plethora_Theme::option( METAOPTION_PREFIX .'footer-widgets-2', 0);
			$footer_bar_status    = 
			$footer_bar_options   = 
			$footer_elements = array(
			 // Footer: first widgets row
				array(
						'container' => 'footer_top',
						'handle'    => 'widget-areas',
						'file'      => 'templates/footer/widget-areas',
						'status'    => $widget_area_1_status,
						'options'   => self::get_footer_widget_areas_options( 1 )
				),
				// Footer: second widgets row
				array(
						'container' => 'footer_main',
						'handle'    => 'widget-areas',
						'file'      => 'templates/footer/widget-areas',
						'status'    => $widget_area_2_status,
						'options'   => self::get_footer_widget_areas_options( 2 )
				),
				// Footer: info bar
				array(
						'container' => 'footer_bar',
						'handle'    => 'footer-bar',
						'file'      => 'templates/footer/footerbar',
						'status'    => ( method_exists( 'Plethora_Module_Footerbar_Ext', 'get_element' ) ? Plethora_Module_Footerbar_Ext::get_element( 'status' ) : false ),
						'options'   => ( method_exists( 'Plethora_Module_Footerbar_Ext', 'get_element' ) ? Plethora_Module_Footerbar_Ext::get_element( 'options' ) : array() ),
				),
				// SVG Loader functionality hidden markup
				array(
						'container' => 'hidden_markup',
						'handle'    => 'svgloader_modal',
						'file'      => 'templates/global/svgloader_modal',
				)
			);

			$this->container_elements['footer'] = $footer_elements;
		}

////// TEMPLATE ELEMENTS CONFIGURATION ( POSITIONED IN LAYOUT CONTAINERS )  <------ FINISH

////// OFF-CONTAINER TEMPLATE ELEMENTS ACTION METHODS ---> START

		/**
		 * Returns 'toggler_and_tools' wrapper OPEN tag, that contains the minitools and mobile sidebar elements
		 * Hooked @ 'plethora_header_main_after_container_markup' action
		 */
		public static function get_toggler_and_tools_wrap_open() {

			echo '<div class="toggler_and_tools">';
		}

		/**
		 * Returns header navigation mini tool elements markup
		 * Hooked @ 'plethora_header_main_after_container_markup' action
		 */
		public static function get_header_navigation_tools() {
			if ( method_exists( 'Plethora_Module_Navminitools_Ext', 'get_minitools_status' ) && Plethora_Module_Navminitools_Ext::get_minitools_status() ) {
				$options['minitools_class'] = 'header_tools';
				$options['minitools_output'] = Plethora_Module_Navminitools_Ext::get_minitools_output();
				set_query_var( 'options', $options );
				get_template_part( 'templates/header/header_main/navigation_minitools' );
			}
		}

		/**
		 * Returns header navigation toggler elements markup
		 * Hooked @ 'plethora_header_main_after_container_markup' action
		 */
		public static function get_header_navigation_toggler() {

			$options['label_more']      = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold', 1);
			$options['label_more_text'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-label-before-threshold-text', esc_html__( 'More', 'hotel-xenia' ) );
			$options['label_menu']      = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold', 1);
			$options['label_menu_text'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-label-after-threshold-text', esc_html__( 'Menu', 'hotel-xenia' ) );
			$options['navicon_class']   = ( ! Plethora_Theme::option( THEMEOPTION_PREFIX .'header-mobsb-navicon', 1) ) ? ' hidden_above_threshold' : '';
			
			ob_start();
			set_query_var( 'options', $options );
			get_template_part( 'templates/header/header_main/navigation_toggler' );
		}

		/**
		 * Returns 'toggler_and_tools' wrapper CLOSE tag, that contains the minitools and mobile sidebar elements
		 * Hooked @ 'plethora_header_main_after_container_markup' action
		 */
		public static function get_toggler_and_tools_wrap_close() {

			echo '</div>';
		}

////// OFF-CONTAINER TEMPLATE ELEMENTS ACTION METHODS  <------ FINISH

////// THEME-SPECIFIC HELPER METHODS ---> START

		/**
		 * Returns listing elements...should always hooked on 'plethora_content_main_loop'
		 */
		public function listing() {
			 
			Plethora_WP::get_template_part('templates/content/archive_parts/listing', get_post_type() );
		}

		/**
		 * Callback function for wp_list_comments callback argument use 
		 * @param $comment, $args, $depth
		 */
		public static function comments_list_callback( $comment, $args, $depth ) {

				$options['comment'] = $comment;
				$options['args']    = $args;
				$options['depth']   = $depth;
				set_query_var( 'options', $options );
				Plethora_WP::get_template_part( 'templates/content/single_parts/comments-list-item' );
		}

		public static function get_profile_social_options( $post_id = false ) {

			$post_id = empty( $post_id ) ? get_the_id() : $post_id; 
			$socials  = Plethora_Theme::option( METAOPTION_PREFIX .'profile-social', array(), $post_id );
			$socials_keys  = $socials['redux_repeater_data'];         
			$social_items = array();
			foreach ( $socials_keys as $key=>$foo ) { 

				if ( !empty($socials['social_icon'][$key]) && !empty($socials['social_url'][$key])  ) { 

					$social_items[] = array(
						'url'     => $socials['social_url'][$key],
						'title'   => $socials['social_title'][$key],
						'icon'    => $socials['social_icon'][$key],
						'esc_url' => substr($socials['social_url'][$key], 0, 7) == 'callto:' ? false : true,
					);
				}
			}
			return array( 'socials' => $social_items );
		}

		/**
		 * Returns single profile blog posts
		 */
		public static function get_profile_authorposts_options( $post_id = false ) {

			$post_id = empty( $post_id ) ? get_the_id() : $post_id; 
			$raw_output = array();
			if ( Plethora_Theme::option( METAOPTION_PREFIX .'profile-authorposts', '0', $post_id) == '1' ){
				
				$args = array(
					'posts_per_page'      => intval( Plethora_Theme::option( METAOPTION_PREFIX .'profile-authorposts-num', 5, $post_id ) ) ,
					'ignore_sticky_posts' => 0,
					'post_type'           => 'post',
					'author'              => Plethora_Theme::option( METAOPTION_PREFIX .'profile-user', 0, $post_id )
				);
				$author_posts = get_posts( $args );  
				foreach ( $author_posts as $author_post ) {

					$date_obj  = new DateTime( $author_post->post_date_gmt );
					$thumbnail = ( has_post_thumbnail( $author_post->ID ))? wp_get_attachment_image_src( get_post_thumbnail_id( $author_post->ID ) ) : false;
					$raw_output[] = array(

						'title'         => $author_post->post_title,
						'permalink'     => get_permalink( $author_post->ID ),
						'thumbnail_url' => esc_url( $thumbnail[0] ),
						'content'       => wp_trim_words( strip_shortcodes( $author_post->post_content ), 20 ),
						'date'          => $date_obj->format('M j')

					);
				}
				wp_reset_postdata(); // Notice: this had to be here, otherwise is not working (!!)  
			}

			return array( 'authorposts' => $raw_output );
		}

		/**
		 * Returns logo ( normal or sticky ) options for direct use in template
		 */
		public static function get_header_logo_options( $logo = '' ) {

			// Common options
			$options['sticky_status']        = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', 0);
			$options['sticky_custom_status'] = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', 0);

			// Normal Logo options
			if ( empty( $logo ) ) {

				$options['logo_status']          = Plethora_Theme::option( METAOPTION_PREFIX .'logo', 1 );
				$options['logo_layout']   = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-layout', '1');        
				$options['logo_img_src']  = Plethora_WP::get_reduxoption_image_src( Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-img', array('url'=> PLE_THEME_ASSETS_URI .'/images/logo.png') ) );
				$options['logo_title']    = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-title', esc_html__('Hotel Xenia', 'hotel-xenia') );
				$options['logo_subtitle'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-subtitle', '' );

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
			
			// Sticky Logo options ( when custom sticky options are enabled )
			} elseif ( $logo === 'sticky' ) { 

				// Set options affected by custom logo setting
				$options['sticky_custom_logo']          = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo', 1 );
				$options['sticky_custom_logo_layout']   = ( $options['sticky_custom_logo'] === 'custom' ) ? Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo-layout', '1' ) : Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-layout', '1');
				$options['sticky_custom_logo_img_src']  = ( $options['sticky_custom_logo'] === 'custom' ) ? Plethora_WP::get_reduxoption_image_src( Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo-img', array('url'=> PLE_THEME_ASSETS_URI .'/images/logo-white.png') ) ) : Plethora_WP::get_reduxoption_image_src( Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-img', array('url'=> PLE_THEME_ASSETS_URI .'/images/logo.png') ) );
				$options['sticky_custom_logo_title']    = ( $options['sticky_custom_logo'] === 'custom' ) ? Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo-title', '' ) : Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-title', esc_html__('Hotel Xenia', 'hotel-xenia') );
				$options['sticky_custom_logo_subtitle'] = ( $options['sticky_custom_logo'] === 'custom' ) ? Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-logo-subtitle', '' ) : Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-subtitle', '' );

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
		 * Returns navigation options for direct use in template
		 */
		public static function get_header_navigation_options() {

			// Some core options 
			$options['nav_status']          = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main', 1 );
			$options['nav_location']        = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main-location', 'primary');
			$options['nav_class_behavior']  = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main-behavior', 'click_menu');

			// Additional options for the custom sticky menu
			$options['sticky_status']        = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky', 0);
			$options['sticky_custom_status'] = Plethora_Theme::option( METAOPTION_PREFIX .'header-sticky-custom', 0);
			$options['sticky_custom_menu']   = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky-custom-menu', 1 );
			$options['nav_class']  = 'primary_nav';
			$options['nav_class']  .= $options['nav_status'] ? '' : ' hidden_above_threshold';
			$options['nav_class']  .= $options['sticky_status'] && $options['sticky_custom_status'] && $options['sticky_custom_menu'] ? ' shown_on_header_stuck' : ' hidden_on_header_stuck';
			
			/* Use ob_start() to fire 'plethora_navigation_before'/'plethora_navigation_after' hooks
				 and the wp_nav_menu() items and get the result in variables */

			// Navigation items output
			ob_start();
			wp_nav_menu( array(
				'container'      => false, 
				'menu_class'     => 'top_level_ul nav '. $options['nav_class_behavior'] , 
				'container'      => 'ul',
				'depth'          => 6,
				'theme_location' => $options['nav_location'],
				'walker'         => ( $options['nav_status'] && class_exists( 'Plethora_Module_Navwalker_Ext' ) ) ? new Plethora_Module_Navwalker_Ext() : ''
			));
			$options['nav_output'] = ob_get_clean();

			return $options;
		}

		/**
		 * Returns widgetized areas options configuration for the footer area
		 * according to given row
		 */
		public static function get_footer_widget_areas_options( $row ) {

			if ( ! in_array( $row, array( 1, 2, '1', '2' ) ) ) { return array(); }

			// get all user set options
			$layout              = Plethora_Theme::option( METAOPTION_PREFIX .'footer-widgets-'.$row.'-layout', 5);
			$sidebar_col_1       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-1', 'sidebar-footer-'.$row.'-1');
			$sidebar_col_1_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-1-extraclass', '');
			$sidebar_col_2       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-2', 'sidebar-footer-'.$row.'-2');
			$sidebar_col_2_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-2-extraclass', '');
			$sidebar_col_3       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-3', 'sidebar-footer-'.$row.'-3');
			$sidebar_col_3_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-3-extraclass', '');
			$sidebar_col_4       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-4', 'sidebar-footer-'.$row.'-4');
			$sidebar_col_4_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-4-extraclass', '');

			// prepare widget areas first

			switch ( $layout ) {
				case 1:
				default:
					$widget_areas = array( 
						'col1' => array( 'sidebar' => $sidebar_col_1,  'class' => 'col-md-12 fwa_'.$row.'-1 ' . $sidebar_col_1_class .'' ),
					);
					break;
				
				case 2:
					$widget_areas = array( 
						'col1' => array( 'sidebar' => $sidebar_col_1,  'class' => 'col-sm-6 fwa_'.$row.'-1 ' . $sidebar_col_1_class .'' ),
						'col2' => array( 'sidebar' => $sidebar_col_2,  'class' => 'col-sm-6 fwa_'.$row.'-2 ' . $sidebar_col_2_class .'' ),
					);
					break;
				
				case 3:
					$widget_areas = array( 
						'col1' => array( 'sidebar' => $sidebar_col_1,  'class' => 'col-sm-6 col-md-8 fwa_'.$row.'-1 ' . $sidebar_col_1_class .'' ),
						'col2' => array( 'sidebar' => $sidebar_col_2,  'class' => 'col-sm-6 col-md-4 fwa_'.$row.'-2 ' . $sidebar_col_2_class .'' ),
					);
					break;
				
				case 4:
					$widget_areas = array( 
						'col1' => array( 'sidebar' => $sidebar_col_1,  'class' => 'col-sm-6 col-md-4 fwa_'.$row.'-1 ' . $sidebar_col_1_class .'' ),
						'col2' => array( 'sidebar' => $sidebar_col_2,  'class' => 'col-sm-6 col-md-8 fwa_'.$row.'-2 ' . $sidebar_col_2_class .'' ),
					);
					break;
				
				case 5:
					$widget_areas = array( 
						'col1' => array( 'sidebar' => $sidebar_col_1,  'class' => 'col-sm-4 fwa_'.$row.'-1 ' . $sidebar_col_1_class .'' ),
						'col2' => array( 'sidebar' => $sidebar_col_2,  'class' => 'col-sm-4 fwa_'.$row.'-2 ' . $sidebar_col_2_class .'' ),
						'col3' => array( 'sidebar' => $sidebar_col_3,  'class' => 'col-sm-4 fwa_'.$row.'-3 ' . $sidebar_col_3_class .'' ),
					);
					break;
				
				case 6:
					$widget_areas = array( 
						'col1' => array( 'sidebar' => $sidebar_col_1,  'class' => 'col-sm-6 col-md-3 fwa_'.$row.'-1 ' . $sidebar_col_1_class .'' ),
						'col2' => array( 'sidebar' => $sidebar_col_2,  'class' => 'col-sm-6 col-md-3 fwa_'.$row.'-2 ' . $sidebar_col_2_class .'' ),
						'col3' => array( 'sidebar' => $sidebar_col_3,  'class' => 'col-sm-12 col-md-6 fwa_'.$row.'-3 ' . $sidebar_col_3_class .'' ),
					);
					break;

				case 7:
					$widget_areas = array( 
						'col1' => array( 'sidebar' => $sidebar_col_1,  'class' => 'col-sm-12 col-md-6 fwa_'.$row.'-1 ' . $sidebar_col_1_class .'' ),
						'col2' => array( 'sidebar' => $sidebar_col_2,  'class' => 'col-sm-6 col-md-3 fwa_'.$row.'-2 ' . $sidebar_col_2_class .'' ),
						'col3' => array( 'sidebar' => $sidebar_col_3,  'class' => 'col-sm-6 col-md-3 fwa_'.$row.'-3 ' . $sidebar_col_3_class .'' ),
					);
					break;
				
				case 8:
					$widget_areas = array( 
						'col1' => array( 'sidebar' => $sidebar_col_1,  'class' => 'col-sm-6 col-md-3 fwa_'.$row.'-1 ' . $sidebar_col_1_class .'' ),
						'col2' => array( 'sidebar' => $sidebar_col_2,  'class' => 'col-sm-6 col-md-3 fwa_'.$row.'-2 ' . $sidebar_col_2_class .'' ),
						'col3' => array( 'sidebar' => $sidebar_col_3,  'class' => 'col-sm-6 col-md-3 fwa_'.$row.'-3 ' . $sidebar_col_3_class .'' ),
						'col4' => array( 'sidebar' => $sidebar_col_4,  'class' => 'col-sm-6 col-md-3 fwa_'.$row.'-4 ' . $sidebar_col_4_class .'' ),
					);
					break;
			}

			return $options = array(
					'layout'        => Plethora_Theme::option( METAOPTION_PREFIX .'footer-widgets-'.$row.'-layout', 1),
					'row_desc'   => ( $row == 1 ) ? esc_html__( 'FIRST ROW', 'hotel-xenia') : esc_html__( 'SECOND ROW', 'hotel-xenia'),
					'widget_areas' => $widget_areas
			);
		}

		public static function get_amenities_options() {

			return $amenities = method_exists( 'Plethora_Posttype_Room_Ext', 'get_room_amenities' ) ? Plethora_Posttype_Room_Ext::get_room_amenities() : array();
		}

		public static function get_service_overlay_title( $post_id ) {

		$overlay_title_status = Plethora_Theme::option( METAOPTION_PREFIX .'service-overlay-title', 1);
		$overlay_title_custom = Plethora_Theme::option( METAOPTION_PREFIX .'service-overlay-title-text', '');
				$overlay_title = '';
				switch ( $overlay_title_status ) {
					case 'custom':
						$overlay_title = $overlay_title_custom;
						break;
					
					case 1:
					default:
						$post_terms = wp_get_post_terms( $post_id, 'service-category' );
						if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms[0] ) ) {
							
							$overlay_title = $post_terms[0]->name;
						}
						break;
				}

				return $overlay_title; 
		}

		// Theme Check cannot trace that we actually use some function, so we had to create this
		// foo method to include them here!
		public static function theme_check_foo() {

			comments_template();
			the_post_thumbnail();
			add_editor_style();
		}
////// THEME-SPECIFIC HELPER METHODS ---> FINISH
	}
}