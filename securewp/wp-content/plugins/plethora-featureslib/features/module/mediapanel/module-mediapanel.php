<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Media panel module ( Notice: should add)

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Mediapanel') ) {

	/**
	 */
	class Plethora_Module_Mediapanel {

		public static $feature_title         = "Media Panel Module";							// FEATURE DISPLAY TITLE
		public static $feature_description   = "Integration module for Plethora media panel";	// FEATURE DISPLAY DESCRIPTION
		public static $theme_option_control  = true;											// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
		public static $theme_option_default  = true;											// DEFAULT ACTIVATION OPTION STATUS
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// DYNAMIC CLASS CONSTRUCTION ? 
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		public $mediapanel_status  = false;
		public $default_panel_type = 'color';
		public $skincolor_support  = true;
		public $image_support      = true;
		public $slider_support     = false;
		public $gmap_support       = true;
		public $revslider_support  = true;
		public $video_support      = false;

		function __construct(){

			add_action( 'init', array( $this, 'init' ) );

		}

		public function init() {

			if ( is_admin() ) {
				// Set metabox tab for supported single post types pages
				$single_post_types = Plethora_Theme::get_supported_post_types();
				foreach ( $single_post_types as $post_type ) {

					add_filter( 'plethora_metabox_single'. $post_type, array( $this, 'get_metabox'), 20);
					add_filter( 'plethora_metabox_single'. $post_type .'_mediapanel_fields', array( $this, 'get_metabox_fields' ) );
					
					// After Xenia hook
					add_filter( 'plethora_metabox_single_'. $post_type .'_mediapanel_fields', array( $this, 'get_metabox_fields' ) );
				}
				// Set metabox tab for supported archives pages
				$archive_post_types = Plethora_Theme::get_supported_post_types( array( 'archives' => true ) );
				foreach ( $archive_post_types as $post_type ) {

					add_filter( 'plethora_metabox_archive'. $post_type, array( $this, 'get_metabox'), 20);
					add_filter( 'plethora_metabox_single'. $post_type .'_mediapanel_fields', array( $this, 'get_metabox_fields') );
					
					// After Xenia hook
					add_filter( 'plethora_metabox_archive_'. $post_type .'_mediapanel_fields', array( $this, 'get_metabox_fields' ) );
				}

				// Set theme options
				add_filter( 'plethora_themeoptions_mediapanel', array( $this, 'get_themeoptions'), 20);
			}
			// Hook template actions on init ( should be hooked on 'wp' for wp conditionals to take effect )
			add_action( 'wp', array( $this, 'config') );
		}

		/**
		* Manages all container attribute & template related hooks
		* Hooked @ 'wp'
		* @return string
		*/
		public function config() {

			// Add all setup to $plethora_mediapanel global
			$config['status']              = is_404() ? 1 : Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-status', $this->mediapanel_status, 0, false);
			if ( $config['status'] ) { 
				
				$config['type']                 = is_404() ? '404' : Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel', 'color');
				// Mediapanel global config ( added only for reference in template parts )
				$config['colorset']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-colorset', 'primary_section');
				$config['transparentoverlay'] = $config['type'] !== 'revslider' ? Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-transparentoverlay', '') : '';
				$config['fadeonscroll']       = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-fadeonscroll', 'fade_on_scroll');
				$config['fullheight']         = $config['type'] !== 'revslider' ? Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-fullheight', '') : '';
				// Mediapanel global config  ( the actuall container setup, applied on all panel types )
				$config['container']['class'][] = $config['colorset'];
				$config['container']['class'][] = $config['transparentoverlay'];
				$config['container']['class'][] = $config['fadeonscroll'];
				$config['container']['class'][] = $config['fullheight'];
				// Headings group configuration ( applied on all panel types )
				$title    = $this->title();
				$subtitle = $this->subtitle();
				$config['hgroup']               = empty( $title ) && empty( $subtitle ) ? false : true;
				$config['hgroup_title']         = $title;
				$config['hgroup_subtitle']      = $subtitle;
				$config['hgroup_textalign']     = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-textalign', 'text-center');
				
				// Additional background configuration for image / slider / revslider and 404 types
				$config['template_name']        = '';
				switch ( $config['type'] ) {
					case 'color':
						$config['container']['style'][] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-color', 'color_set') === 'custom_color' ? 'background-color: '. Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-color-custom', '#2ecc71') .';' : '';
						$config['template_name']        = '';
						break;

					case 'image':
						$image                           = $this->background( 'image' );
						$image_parallax                  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-image-parallax', '');
						$image_valign                    = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-image-valign', 'bg_vcenter');
						$config['image']                 = !empty( $image ) ? $image : ''; // add this only for reference in template parts
						$config['container']['style'][]  = !empty( $image ) ? 'background-image: url( '. $image .' );' : '' ;
						$config['container']['class'][]  = $image_parallax;
						$config['container']['class'][]  = empty( $image_parallax ) ? $image_valign : '';
						$config['enqueues']['scripts'][] = empty( $image_parallax ) ? '' : 'parallax';
						$config['template_name']         = 'image';
						break;

					case 'slider':
						$image                           = $this->background( 'image' );
						$slider                          = $this->background( 'slider' );
						$config['slides']                = !empty( $slider['slides'] ) ? $slider['slides'] : 'background-image: '. $image .';';
						$config['enqueues']['scripts'][] = 'owlcarousel2';
						$config['enqueues']['inits'][]   = array('handle' => 'owlcarousel2', 'script' => $this->init_script_owlslider(), 'multiple' => true );
						$config['enqueues']['styles'][]  = 'owlcarousel2';
						$config['enqueues']['styles'][]  = 'owlcarousel2-theme';
						$config['template_name']         = 'slider';
						break;

					case 'revslider':
						$revslider               = $this->background( 'revslider' );
						$config['revslider']     =  $revslider ;
						$config['template_name'] = 'revslider';
						break;

					case 'googlemap':
						$config['enqueues']['scripts'][]      = ASSETS_PREFIX .'-gmap-init';
						$map_vars['maps'][]                   = $this->gmap_options();
						$config['themeconfig']['GOOGLE_MAPS'] = $map_vars;
						$config['template_name']              = 'map';
						break;
					
					case '404':
						$image_404                      = $this->background( '404' );
						$config['image']                = !empty( $image_404 ) ? $image_404 : ''; // add this only for reference in template parts
						$config['container']['style'][] = !empty( $image_404 ) ? 'background-image: url( '. $image_404 .' );' : '' ;
						$config['container']['class']   = array(); // empty previous config first
						$config['container']['class'][] = !empty( $image_404 ) ? 'black_section transparent_film' : '' ;
						$config['template_name']        = 'image';
						break;
				}

				// Just remove empty classes...just because I'm nuts
				$config['container']['class'] = array_filter( $config['container']['class'] );
				// Apply filters for media panel configuration
				$config = apply_filters( 'plethora_mediapanel_config', $config );

				// Set 'mediapanel' container attributes according to configuration
				foreach ( $config['container'] as $attr => $attr_values ) {

					foreach ( $attr_values as $attr_value ) {

						Plethora_Theme::add_container_attr( 'mediapanel', $attr, $attr_value );
					}
				}

				// Enqueue related scrips/styles according to configuration
				add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ));

				// Hook template part
		        add_action( 'plethora_mediapanel', array( $this, 'load_template') );
			}

			global $plethora_mediapanel;
			$plethora_mediapanel = $config;
		}

	    /**
	     * Enqueues scripts/styles according to settings
	     *
	     */
		public function load_assets() {

			global $plethora_mediapanel;
			$status       = $plethora_mediapanel['status'];
			$load_scripts = !empty( $plethora_mediapanel['enqueues']['scripts'] ) ? $plethora_mediapanel['enqueues']['scripts'] : array() ;
			$load_styles  = !empty( $plethora_mediapanel['enqueues']['styles'] ) ? $plethora_mediapanel['enqueues']['styles'] : array();
			$load_inits   = !empty( $plethora_mediapanel['enqueues']['inits'] ) ? $plethora_mediapanel['enqueues']['inits'] : array() ;
			$themeconfigs = !empty( $plethora_mediapanel['themeconfig'] ) ? $plethora_mediapanel['themeconfig'] : array() ;

			if ( $plethora_mediapanel['status'] ) {

				// Enqueue scripts
				foreach ( $load_scripts as $script_handle ) {

					if ( !empty( $script_handle ) ) { wp_enqueue_script( $script_handle ); }
				}

				// Enqueue styles
				foreach ( $load_styles as $style_handle ) {

					if ( !empty( $style_handle ) ) { wp_enqueue_style( $style_handle ); }
				}

				// Enqueue inits
				foreach ( $load_inits as $init_config ) {

					if ( !empty( $init_config['handle'] ) ) { Plethora_Theme::enqueue_init_script( $init_config ); }
				}

				// Set theme.js variables
				foreach ( $themeconfigs as $var_group => $vars ) {

					if ( !empty( $vars ) ) { Plethora_Theme::set_themeconfig( $var_group, $vars ); }
				}

			}
		}

	    /**
	     * The main method...prepares all variables and loads the correct template part. 
	     * It's not triggered automaticaly, is called Plethora_Template class
	     * @return string
	     */
	    public function load_template() {

			global $plethora_mediapanel;
			$status        = $plethora_mediapanel['status'];
			$template_name = $plethora_mediapanel['template_name'];
			
			if ( $status ) {

		 		if ( $template_name === 'revslider' ) {

					$revslider = $plethora_mediapanel['revslider'];
					ob_start();
					Plethora_Module_Revslider_Ext::get_slider_output( $revslider );
					$output = ob_get_clean();
					if ( !empty( $output ) ) {

			 			echo '<div class="rev_slider_wrapper">';
						echo trim( $output );
			 			echo '</div>';

		 			} else { // we need this fallback, in case Rev slider output is empty OR a dev might want it in a template part file

						echo Plethora_WP::renderMustache( array( "data" => $plethora_mediapanel, "force_template_part" => array( 'templates/mediapanel/mediapanel', $template_name ) ) );
		 			}

		 		} else {

					echo Plethora_WP::renderMustache( array( "data" => $plethora_mediapanel, "force_template_part" => array( 'templates/mediapanel/mediapanel', $template_name ) ) );
		 		}
			}
	    }    

	   /** 
	   * Returns alternative button selection for header media option, when Rev Slider plugin is installed
	   *
	   * @return array
	   * @since 1.0
	   *
	   */
		public function media_types() {

			$media_types = array();
			if ( $this->skincolor_support ) {

				$media_types['color'] = esc_html__('Color', 'plethora-framework');
			}

			if ( $this->image_support ) {

				$media_types['image'] = esc_html__('Image', 'plethora-framework');
			}

			if ( $this->slider_support ) {

				$media_types['slider'] = esc_html__('Slider', 'plethora-framework');
			}

			if ( $this->gmap_support ) {

				$media_types['googlemap'] = esc_html__('Map', 'plethora-framework');
			}

			if ( $this->revslider_support && class_exists( 'RevSliderAdmin' ) ) {

				$media_types['revslider'] = esc_html__('Revolution Slider', 'plethora-framework');
			}

			if ( $this->video_support ) {

				$media_types['video'] = esc_html__('Video', 'plethora-framework');
			}

			$media_types = apply_filters( 'plethora_mediapanel_types', $media_types );
			return $media_types;
		}


		/** 
		* Returns media panel title
		*
		* @return string
		* @since 1.0
		*
		*/
		public function title() { 

	      $title_behavior = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-title', 'posttitle');

		  $title = '';
	      
	      if ( $title_behavior === 'posttitle' ) { 

		      $title = Plethora_Theme::get_title( array( 'tag' => '', 'force_display' => true ) );

	      } elseif ( $title_behavior === 'customtitle' ) {

		      $title = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-title-custom', '');
	      
	      } 
	      $title = apply_filters( 'plethora_mediapanel_title', $title );

	      return $title;
	    }

		/** 
		* Returns media panel subtitle
		*
		* @return string
		* @since 1.0
		*
		*/
		public function subtitle() { 

		  $title_behavior = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-subtitle', 'postsubtitle');
		  
		  if ( $title_behavior === 'postsubtitle' ) { 

		      $subtitle = Plethora_Theme::get_subtitle( array( 'tag' => '', 'force_display' => true ) );

		  } elseif ( $title_behavior === 'customsubtitle' ) {

		      $subtitle = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-subtitle-custom', '');
		  
		  } else {

		      $subtitle = '';
		  }

		  $subtitle = apply_filters( 'plethora_mediapanel_subtitle', $subtitle );
		  return $subtitle;
		}


		 /**
		 * Returns media content according to the type
		 *
		 * @param $type ( 'skincolor', featuredimage', 'otherimage', '404', 'slider' )
		 * @return string
		 *
		 */
		public function background( $type = 'color' ) {

		  $background = '';

		  if ( $type === 'image' ) {

		  	  $image_type = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-image', 'featuredimage' ); 
			  if ( $image_type === 'featuredimage' ) { 

			  	$postid = Plethora_Theme::get_this_page_id();
			    if ( has_post_thumbnail( $postid ) ) { 
					
					$image_size = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-image-size', 'full' );
					$attachment = wp_get_attachment_image_src( get_post_thumbnail_id( $postid ), $image_size );
			      	$background = isset( $attachment[0] ) && !empty( $attachment[0] ) ? $attachment[0] : '' ;
			    }

			  } elseif ( $image_type === 'otherimage' ) { 

					$otherimage = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-bcg-image-other' );			
			      	$background = isset( $otherimage['url'] ) && !empty( $otherimage['url'] ) ? $otherimage['url'] : '' ;		  
			  }

		  } elseif ( $type === 'slider') { 

				$sliderid = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-bcg-slider', 0);
				$background = method_exists( 'Plethora_Posttype_Slider', 'get_slides') ? array( "slides" => Plethora_Posttype_Slider::get_slides( $sliderid ) ) : array();

		  } elseif ( $type === 'revslider') {

				$background = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-revslider', '');

		  } elseif ( $type === '404' ) { 

		  		$four04_image = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-404-image');
		      	$background = isset( $four04_image['url'] ) && !empty( $four04_image['url'] ) ? $four04_image['url'] : '' ;
		  } 

		  $background = apply_filters( 'plethora_mediapanel_background', $background, $type );
		  return $background;
		}
		
	    /**
	     * Returns slider options array
	     *
	     * @param $sliderid
	     * @return array
	     * @since 1.0
	     *
	     */
	    public function slider_options() {

			$sliderid       = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-bcg-slider', 0);
			$slider_options = method_exists( 'Plethora_Posttype_Slider', 'get_owlslider_config') ? Plethora_Posttype_Slider::get_owlslider_config( $sliderid ) : array();
	        return $slider_options;
	    }


	    /**
	     * Returns google maps options array
	     *
	     * @param ( not needed...taken automatically depending on the page )
	     * @return array
	     * @since 1.0
	     *
	     */
	    public function gmap_options() {
	        
			$gmap                        = array();
			$gmap['id']                  = 'map';

			// Basic options
			$gmap['lat']  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-lat');
			$gmap['lon']  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-lon');
			$gmap['type'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-type', 'ROADMAP'); // "SATELLITE", ROADMAP", "HYBRID", "TERRAIN"
			$zoom         = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-zoom', 14 );
			$gmap['zoom'] = is_numeric( $zoom ) ? intval( $zoom ) : 14;
			// $gmap['streetView']          = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-streetview', 0); 
			// $gmap['streetView_position'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-streetview-position', 'LEFT_CENTER'); 

			// Marker Image settings
			$gmap['marker']            = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-marker', true ); 
			$gmap['markerTitle']       = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-markertitle', 'We are right here!');
			$gmap['infoWindow']        = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-markerwindow', '');
			$markerImage               = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-marker-customimage', array( 'url' => '', 'height' => 0, 'width' => 0 ));

			$gmap['markerImageSrc']    = $markerImage['url'];
			$gmap['markerImageWidth']  = $markerImage['width'];
			$gmap['markerImageHeight'] = $markerImage['height'];
			$gmap['markerAnchorX']     = $markerImage['width']; // not sure if this is correct 
			$gmap['markerAnchorY']     = $markerImage['height']; // not sure if this is correct 
			$gmap['markerType']  	   = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-markertype', "animated" );
			
			// ADVANCED MAP STYLING
			$gmap['type_switch']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-type-switch', true);  
			$gmap['type_switch_style']     = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-type-switch-style', 'DROPDOWN_MENU'); // "DROPDOWN_MENU", "HORIZONTAL_BAR", "DEFAULT"
			$gmap['type_switch_position']  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-type-switch-position', 'TOP_RIGHT');  // POSITIONS: https://developers.google.com/maps/documentation/javascript/images/control-positions.png
			$gmap['pan_control']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-pan-control', true); 

			if ( $gmap['pan_control'] == "0" ) $gmap['pan_control'] = false;

			$gmap['pan_control_position']  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-pan-control-position', 'RIGHT_CENTER'); // POSITIONS: https://developers.google.com/maps/documentation/javascript/images/control-positions.png
			$gmap['zoom_control']          = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-zoom-control', true ); 
			$gmap['zoom_control_style']    = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-zoom-control-style', 'SMALL' ); // "SMALL", "LARGE", "DEFAULT"
			$gmap['zoom_control_position'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-zoom-control-position', 'LEFT_CENTER' ); // POSITIONS: https://developers.google.com/maps/documentation/javascript/images/control-positions.png
			$gmap['scrollWheel']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-scrollwheel', false ); 
			$gmap['styles']                = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-snazzy', false ) == true ? Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-snazzy-config', null) : null ; 

			// FIXED
			$gmap['disableDefaultUI'] = false; 
			$gmap['scale_control']    = false;  
			$gmap['animatedMarker']	  = ( Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-map-markertype', true ) == "animated" )? true : false;

	        return $gmap;
	    }

	   	// Owl Slider js init output
	   	public function init_script_owlslider() {
			
			$slider = $this->slider_options();
			$output = '
	<script>
	jQuery(function($) {

	    "use strict";

	    var $owl = $("#head_panel_slider");			  
	    $owl.owlCarousel({
				items              : 1,
				autoplay           : _p.checkBool('. $slider["autoplay"] .'),
				autoplayTimeout    : '.  $slider["autoplaytimeout"] .',
				autoplaySpeed      : '.  $slider["autoplayspeed"] .',
				autoplayHoverPause : _p.checkBool('.  $slider["autoplayhoverpause"] .'),
				nav                : _p.checkBool('.  $slider["nav"] .'),
				dots               : _p.checkBool('.  $slider["dots"] .'),
				loop               : _p.checkBool('.  $slider["loop"] .'),
				mouseDrag		   : _p.checkBool('.  $slider["mousedrag"] .'),
				touchDrag		   : _p.checkBool('.  $slider["touchdrag"] .'),
				lazyLoad      	   : _p.checkBool('.  $slider["lazyload"] .'),
				rtl      	   	   : _p.checkBool('.  $slider["rtl"] .'),
	    });
	    var $headPanelSliderOwlCarousel = $("#head_panel_slider.owl-carousel");
	    $headPanelSliderOwlCarousel.find(".item .container .caption .inner").addClass("hide pause_animation");
	    $headPanelSliderOwlCarousel.find(".active .item .container .caption .inner").removeClass("hide pause_animation");
	    $owl.on("translated.owl.carousel", function(event) {
	        $headPanelSliderOwlCarousel.find(".item .container .caption .inner").addClass("hide pause_animation");
	        $headPanelSliderOwlCarousel.find(".active .item .container .caption .inner").removeClass("hide pause_animation");
	    })
	});
	</script>';
			return $output;

		}

	// THEME OPTIONS START

	    /**
	     * Wrapper tag for returning theme options configuration for the media panel
	     * Hooked on 'plethora_themeoptions_mediapanel' filter
	     * @return array
	     *
	     */
		public function get_themeoptions( $sections ) { 

			$sections[] = array(
				'subsection' => true,
							'title'   => esc_html__('Media Panel', 'plethora-framework'),
							'heading' => esc_html__('BASIC CONFIGURATION', 'plethora-framework'),
							'desc'    => esc_html__('Set the default values for the basic options of the Media Panel. All these values can be overriden per page. Note that there are additional configuration options per page.', 'plethora-framework'),
							'fields'  => $this->themeoptions(),
						  );
			return $sections;
		}

	    /**
	     * Returns theme options configuration for the media panel
	     * @return array
	     */
	    public function themeoptions() {

	    		$themeoptions = array();

				$themeoptions[] = array(
						'id'      => METAOPTION_PREFIX .'mediapanel-status',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Media Panel', 'plethora-framework'),
						"default" => $this->mediapanel_status,
						"on"      => 'On',
						"off"     => 'Off',
				);
				$themeoptions[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel',
						'type'     => 'button_set',
						'title'    => esc_html__('Default Panel Type', 'plethora-framework'), 
						'options'  => $this->media_types(),
						'default'  => $this->default_panel_type,
						'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
				);
				$themeoptions[] = array(
						'id'          => METAOPTION_PREFIX .'mediapanel-colorset',
						'type'        => 'button_set', 
						'title'       => esc_html__( 'Color Set', 'plethora-framework' ),
						'description' => esc_html__('Color sets affect background and simple/linked text colors. You may edit them under "Theme Options > General > Color Sets" tab.', 'plethora-framework'),
						'default'     => 'primary_section',
						'options'     => Plethora_Module_Style_Ext::get_options_array( array( 'type'	=> 'color_sets', ) ),
						'required'    => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
				);
				$themeoptions[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-transparentoverlay',
						'type'     => 'button_set', 
						'title'    => esc_html__('Transparent Overlay', 'plethora-framework'),
						'desc'     => sprintf( esc_html__('This will add a transparent filter. %1$s WILL NOT BE APPLIED ON REVOLUTION SLIDERS! %2$s', 'plethora-framework'), '<span style="color:red">' , '</span>' ),
						'default'  => '',
						'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
						'options'  => array( 
										'transparent_film' => esc_html__( 'Yes', 'plethora-framework' ), 
										''                 => esc_html__( 'No', 'plethora-framework' ), 
									  ),
						);
				$themeoptions[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-fadeonscroll',
						'type'     => 'button_set', 
						'title'    => esc_html__( 'Fade Effect On Page Scroll', 'plethora-framework' ),
						'default'  => 'fade_on_scroll',
						'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
						'options'  => array( 
										'fade_on_scroll'     => esc_html__( 'Yes', 'plethora-framework' ), 
										'fade_on_scroll_off' => esc_html__( 'No', 'plethora-framework' ), 
									  ),
						);
				$themeoptions[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-fullheight',
						'type'     => 'button_set', 
						'title'    => esc_html__('Full Height', 'plethora-framework'),
						'desc'     => sprintf( esc_html__('This will produce a full height display for the media panel. %1$s WILL NOT BE APPLIED ON REVOLUTION SLIDERS! %2$s', 'plethora-framework'), '<span style="color:red">' , '</span>' ),
						"default"  => '',
						'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
						'options'  => array(
										'full_height' => esc_html__( 'Yes', 'plethora-framework'),
										''            => esc_html__( 'No', 'plethora-framework'),
							),
				   		);

				$themeoptions = array_merge($themeoptions, $this->themeoptions_general_misc_fields() );
				
				return $themeoptions;
		}

	// MISC THEME OPTIONS START ( applied on HealthFlex )

	    public function themeoptions_general_misc_fields() {

			    $misc_fields = array(
		
						array(
							'id'       => 'mediapanel-hgroup-styling-start',
							'type'     => 'section',
							'title'    => esc_html__('Headings Group Styling', 'plethora-framework'),
							'subtitle' => esc_html__('These Options are applied on media panel headings container. You may consider the sum of vertical padding values as a minimum media panel height ( when headings are present of course ). So if you set a 120px for top and bottom, the minimum panel height will be 240px. These are global options, therefore they CANNOT be overriden per page. ', 'plethora-framework'), 
							'indent'   => true, 
							'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
						),
							array(
								'id'            => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding',
								'type'          => 'spacing',
								'left'          => false,
								'right'         => false,
								'display_units' => false,
								'title'         => esc_html__('Vertical Padding (medium and large devices)',  'plethora-framework'),
								'subtitle'      => esc_html__('default: 120px / 120px', 'plethora-framework'),
								'desc'          => esc_html__('Padding for medium displays, from 992px to 1200px and up', 'plethora-framework'), 
								'default'       => array( 'padding-top'=>'120', 'padding-bottom'=>'120' ),
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),												

							array(
								'id'            => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding-sm',
								'type'          => 'spacing',
								'left'          => false,
								'right'         => false,
								'display_units' => false,
								'title'         => esc_html__('Vertical Padding (small devices)',  'plethora-framework'),
								'subtitle'      => esc_html__('default: 100px / 100px', 'plethora-framework'),
								'desc'          => esc_html__('Padding for small displays, from 768px to 991px', 'plethora-framework'), 
								'default'       => array( 'padding-top'=>'100', 'padding-bottom'=>'100' ),
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),												

							array(
								'id'            => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding-xs',
								'type'          => 'spacing',
								'left'          => false,
								'right'         => false,
								'display_units' => false,
								'title'         => esc_html__('Vertical Padding (extra small devices)',  'plethora-framework'),
								'subtitle'      => esc_html__('default: 80px / 80px', 'plethora-framework'),
								'desc'          => esc_html__('Padding for extra small displays, below 768px', 'plethora-framework'), 
								'default'       => array( 'padding-top'=>'80', 'padding-bottom'=>'80' ),
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),												

							array(
								'id'             => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font',
								'type'           => 'typography', 
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
								'title'          => esc_html__('Font Size (medium and large devices)', 'plethora-framework'),
								'subtitle'       => esc_html__('default: 110px', 'plethora-framework'),
								'desc'           => esc_html__('Font size for medium -992px to 1200px- and large displays, over 1200px', 'plethora-framework'), 
								'default'        => array( 'font-size' => '110px'),
								'required'       => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
							array(
								'id'             => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font-sm',
								'type'           => 'typography', 
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
								'title'          => esc_html__('Font Size (small devices)', 'plethora-framework'),
								'subtitle'       => esc_html__('default: 80px', 'plethora-framework'),
								'desc'           => esc_html__('Font size for small displays, from 768px to 992px', 'plethora-framework'), 
								'default'        => array( 'font-size' => '80px'),
								'required'       => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
							array(
								'id'             => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-font-xs',
								'type'           => 'typography', 
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
								'title'          => esc_html__('Font Size (extra small devices)', 'plethora-framework'),
								'subtitle'       => esc_html__('default: 50px', 'plethora-framework'),
								'desc'           => esc_html__('Font size for extra small displays, below 768px', 'plethora-framework'), 
								'default'        => array( 'font-size' => '50px'),
								'required'       => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
						array(
							'id'       => 'mediapanel-image-start',
							'type'     => 'section',
							'title'    => esc_html__('Image Background Height', 'plethora-framework'),
							'subtitle' => esc_html__('Note: these options are applied only when "Featured Image" OR "Other Image" background type is displayed. These are global options, therefore they CANNOT be overriden per page', 'plethora-framework'),
							'indent'   => true, 
							'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
						),
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height (large devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) when a featured or other image is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 380px', 'plethora-framework'),
								"default"       => 380,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text',
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height-sm',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height (small devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) for small devices when a featured or other image is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 280px', 'plethora-framework'),
								"default"       => 280,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text',
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height-xs',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height (extra small devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) for extra small devices when a featured or other image is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 80px', 'plethora-framework'),
								"default"       => 80,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text',
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
						array(
							'id'       => 'mediapanel-image-end',
							'type'     => 'section',
							'indent'   => false, 
							'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
						),
						array(
							'id'       => 'misc-mediapanel-bcg-map-start',
							'type'     => 'section',
							'title'    => esc_html__('Map Background Height', 'plethora-framework'),
							'subtitle' => esc_html__('Note: these options are applied only when "Map" background type is displayed. These are global options, therefore they CANNOT be overriden per page', 'plethora-framework'),
							'indent'   => true,
							'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
						),
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-map-panel-height',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height (large devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) when a map is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 480px', 'plethora-framework'),
								"default"       => 480,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text',
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-map-panel-height-sm',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height (small devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) for small devices when a map is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 280px', 'plethora-framework'),
								"default"       => 380,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text',
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-map-panel-height-xs',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height (extra small devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) for extra small devices when a map is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 180px', 'plethora-framework'),
								"default"       => 280,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text',
								'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
								),	
						array(
							'id'       => 'misc-mediapanel-bcg-map-end',
							'type'     => 'section',
							'indent'   => false, 
							'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
						),

				);

				return $misc_fields;

	    }
	// MISC THEME OPTIONS END ( applied on HealthFlex )

	// THEME OPTIONS END

	// METABOX OPTIONS START

	    /**
	     * Returns metabox configuration for single page metabox tab
	     * Hooked on 'plethora_metabox_singlepage' filter
	     * @return array
	     *
	     */
		public function get_metabox( $sections ) { 

			$sections[] =  array(
			        'title'         => esc_html__('Media Panel', 'plethora-framework'),
			        'heading'		=> esc_html__('BASIC CONFIGURATION', 'plethora-framework'),
			        'icon_class'    => 'icon-large',
			        'icon'          => 'el-icon-website',
			        'fields'        => $this->get_metabox_fields()
				);

			return $sections;
		}

		public function get_metabox_fields() {

			$metabox_fields = array_merge(
								$this->metabox_basic(),
								$this->metabox_headings(),
								$this->metabox_color(),
								$this->metabox_image(),
								$this->metabox_map(),
								$this->metabox_slider(),
								$this->metabox_revslider()
							  );
			return $metabox_fields;			
		}
	    public function metabox_basic() {

			$metabox_basic = array();

			$metabox_basic[] = array(
					'id'      => METAOPTION_PREFIX .'mediapanel-status',
					'type'    => 'switch', 
					'title'   => esc_html__('Display Media Panel', 'plethora-framework'),
					"on"      => 'On',
					"off"     => 'Off',
			);
			$metabox_basic[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel',
					'type'     => 'button_set',
					'title'    => esc_html__('Default Panel Type', 'plethora-framework'), 
					'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
					'options'  => $this->media_types(),
			);
			$metabox_basic[] = array(
					'id'          => METAOPTION_PREFIX .'mediapanel-colorset',
					'type'        => 'button_set', 
					'title'       => esc_html__( 'Color Set', 'plethora-framework' ),
					'description' => esc_html__('Color sets affect background and simple/linked text colors. You may edit them under "Theme Options > General > Color Sets" tab.', 'plethora-framework'),
					'required'    => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
					'options'     => Plethora_Module_Style_Ext::get_options_array( array( 'type'	=> 'color_sets', ) ),
			   		);
			$metabox_basic[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-transparentoverlay',
					'type'     => 'button_set', 
					'title'    => esc_html__('Transparent Overlay', 'plethora-framework'),
					'desc'     => sprintf( esc_html__('This will add a transparent filter. %1$s WILL NOT BE APPLIED ON REVOLUTION SLIDERS! %2$s', 'plethora-framework'), '<span style="color:red">' , '</span>' ),
					'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
					'options'  => array( 
									'transparent_film' => esc_html__( 'Yes', 'plethora-framework' ), 
									''                 => esc_html__( 'No', 'plethora-framework' ), 
								  ),
					);
			$metabox_basic[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-fadeonscroll',
					'type'     => 'button_set', 
					'title'    => esc_html__( 'Fade Effect On Page Scroll', 'plethora-framework' ),
					'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
					'options'  => array( 
									'fade_on_scroll'     => esc_html__( 'Yes', 'plethora-framework' ), 
									'fade_on_scroll_off' => esc_html__( 'No', 'plethora-framework' ), 
								  ),
					);
			$metabox_basic[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-fullheight',
					'type'     => 'button_set', 
					'title'    => esc_html__('Full Height', 'plethora-framework'),
					'desc'     => sprintf( esc_html__('This will produce a full height display for the media panel. %1$s WILL NOT BE APPLIED ON REVOLUTION SLIDERS! %2$s', 'plethora-framework'), '<span style="color:red">' , '</span>' ),
					'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
					'options'  => array(
									'full_height' => esc_html__( 'Yes', 'plethora-framework'),
									''            => esc_html__( 'No', 'plethora-framework'),
						),
			   		);
			return $metabox_basic;
	   	
	    }
	    public function metabox_headings() { 
			$required_attrs = array( 
				    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
									array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
									array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
								  );
			$metabox_headings[] = array(
						'id'       => 'mp-headings-start',
						'type'     => 'section',
						'title'    => esc_html__('HEADINGS GROUP SECTION', 'plethora-framework'),
						'required' => $required_attrs,
						'indent'    => true 
			);
			$metabox_headings[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-title',
						'type'     => 'button_set', 
						'title'    => esc_html__('Title Display', 'plethora-framework'),
						'required' => $required_attrs,
						'default'  => 'posttitle',
						'options'  => array(
								'posttitle'   => 'Post/Page Title',
								'customtitle' => 'Custom Title',
								'notitle'     => 'Do Not Display'
							),
			);
			$metabox_headings[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-title-custom',
						'type'     => 'text', 
						'title' => esc_html__('Custom Title', 'plethora-framework'),
	      				'translate' => true,
						'required' =>  array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
										array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
										array( METAOPTION_PREFIX .'mediapanel-hgroup-title','=', array('customtitle')),
								  ),
			);

			$metabox_headings[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-subtitle',
						'type'     => 'button_set', 
						'title'    => esc_html__('Subtitle Display', 'plethora-framework'),
						'required' => $required_attrs,
						'default'  => 'postsubtitle',
						'options'  => array(
								'postsubtitle'   => 'Post/Page Subtitle',
								'customsubtitle' => 'Custom Subtitle',
								'nosubtitle'     => 'Do Not Display'
							),
			);

			$metabox_headings[] = array(
						'id'        => METAOPTION_PREFIX .'mediapanel-hgroup-subtitle-custom',
						'type'      => 'text', 
						'title'     => esc_html__('Custom Subtitle', 'plethora-framework'),
						'translate' => true,
						'required'  =>  array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
										array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
										array( METAOPTION_PREFIX .'mediapanel-hgroup-subtitle','=', array('customsubtitle')),
								  ),
			);


			$metabox_headings[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-textalign',
						'type'     => 'button_set', 
						'title'    => esc_html__('Headings Group Align', 'plethora-framework'),
						'default'  => 'text-center',
						'options'  => array(
								'text-left'   => esc_html__( 'Left', 'plethora-framework' ),
								'text-center' => esc_html__( 'Center', 'plethora-framework' ),
								'text-right'  => esc_html__( 'Right', 'plethora-framework' ),
							),
						'required'  =>  array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
										array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
								  ),
			);

			return $metabox_headings;
	    }

	    public function metabox_color() { 

			$metabox_color[] = array(
						'id'       => 'mp-color-start',
						'type'     => 'section',
						'title'    => esc_html__('BACKGROUND SECTION // COLOR', 'plethora-framework'),
						'indent'   => true, 
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('color')),
									  ),
			);

			$metabox_color[] =	array(
						'id'       => METAOPTION_PREFIX .'mediapanel-bcg-color',
						'type'     => 'button_set', 
						'title'    => esc_html__('Background Color Settings', 'plethora-framework'),
						'desc'    => esc_html__('Can be the color set background ( check "Color Set" options above ) OR you can set a custom color.', 'plethora-framework'),
						"default"  => 'color_set',
						"options"  => array( 'color_set' => esc_html__( 'According To Color Set', 'plethora-framework' ), 'custom_color' => esc_html__( 'Custom Color', 'plethora-framework' ) ),
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('color')),
									  ),
						);
			
			$metabox_color[] =	array(
						'id'          => METAOPTION_PREFIX .'mediapanel-bcg-color-custom',
						'type'        => 'color',
						'title'       => esc_html__('Select Background Color', 'plethora-framework'), 
						'subtitle'    => esc_html__('default: #2ecc71', 'plethora-framework'),
						'default'     => '#2ecc71',
						'transparent' => false,
						'validate'    => 'color',
						'required'    => array( 
						    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
											array( METAOPTION_PREFIX .'mediapanel','=', array('color')),
											array( METAOPTION_PREFIX .'mediapanel-bcg-color','=', array('custom_color'))
										  ),
						);

			return $metabox_color;

	    }

	    public function metabox_image() { 

			$metabox_image[] = array(
						'id'       => 'mp-image-start',
						'type'     => 'section',
						'title'    => esc_html__('BACKGROUND SECTION // IMAGE', 'plethora-framework'),
						'indent'   => true, 
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('image')),
									  ),
			);

			$metabox_image[] = array( 
						'id'       => METAOPTION_PREFIX .'mediapanel-bcg-image',
						'type'     => 'button_set', 
						'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('featuredimage')),
						'title'    => esc_html__('Background Image', 'plethora-framework'),
						'options'  => array( 
							'featuredimage'  => esc_html__( 'Use Featured Image', 'plethora-framework' ),
							'otherimage' => esc_html__( 'Use Other Image', 'plethora-framework' ),
							),
						'default'  => 'featuredimage',
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('image')),
									  ),
			);

			$metabox_image[] = array( 
						'id'       => METAOPTION_PREFIX .'mediapanel-bcg-image-other',
						'type'     => 'media', 
						'url'      => true,
						'title'    => esc_html__('Other Image', 'plethora-framework'),
						'desc'     => esc_html__('Upload an image other than your featured image. Note that media panel will display by default the original image size. For optimum page speed, we suggest that the original image file size should not exceed 300KB', 'plethora-framework'),
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('image')),
										array( METAOPTION_PREFIX .'mediapanel-bcg-image','=', array('otherimage')),
									  ),
			);
			$metabox_image[] = array( 
						'id'       => METAOPTION_PREFIX .'mediapanel-bcg-image-size',
						'type'     => 'button_set', 
						'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('featuredimage')),
						'title'    => esc_html__('Featured Image Size', 'plethora-framework'),
						'desc'     => esc_html__('For optimum page speed, we suggest that the original image file size should not exceed 300KBs', 'plethora-framework'),
						'options'  => array( 
							'full'  => 'Original Size', 
							'large' => 'Large Size ( optimized by WP )', 
							),
						'default'  => 'full',
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('image')),
										array( METAOPTION_PREFIX .'mediapanel-bcg-image','=', array('featuredimage')),
									  ),
			);

			$metabox_image[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-bcg-image-parallax',
						'type'     => 'button_set', 
						'title'   => esc_html__('Parallax Effect', 'plethora-framework'),
						"default" => '',
						'options'  => array(
										'parallax-window' => esc_html__( 'Yes', 'plethora-framework'),
										''         => esc_html__( 'No', 'plethora-framework'),
							),
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('image')),
									  ),
				   		);

			$metabox_image[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-bcg-image-valign',
						'type'     => 'button_set', 
						'title'   => esc_html__('Image Vertical Align', 'plethora-framework'),
						"default" => 'bg_vcenter',
						'options'  => array(
							'bg_vtop'   => 'Top',
							'bg_vcenter' => 'Center',
							'bg_vbottom'  => 'Bottom'
							),
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('image')),
										array( METAOPTION_PREFIX .'mediapanel-bcg-image-parallax','=', ''),
									  ),
				   		);

			return $metabox_image;

	    }

	    public function metabox_slider() {

			$metabox_slider[] = array(
						'id'       => 'mp-slider-start',
						'type'     => 'section',
						'title'    => esc_html__('BACKGROUND SECTION // PLETHORA SLIDER', 'plethora-framework'),
						'indent'   => true, 
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('slider')),
									  ),
			);
			$metabox_slider[] = array( 
						'id'       => METAOPTION_PREFIX .'mediapanel-bcg-slider',
						'type'     => 'select',
						'data'     => 'posts',
						'title'    => esc_html__('Select Slider', 'plethora-framework'), 
						'desc'     => esc_html__('Select a slider to be displayed. You should create one first! Slider settings will be applied here too!', 'plethora-framework'),
						'args'     => array(
										'posts_per_page'   => -1,
										'post_type'        => 'slider',
										'suppress_filters' => true									 				
									  ),
						'required' => array( 
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
										array( METAOPTION_PREFIX .'mediapanel','=', array('slider')),
									  ),
			);
			return $metabox_slider;
	    }

	    public function metabox_revslider() {

	    	$metabox_revslider = array(); 
	    	
	    	if ( class_exists( 'RevSliderAdmin' ) ) { // NOTICE: ONLY IF REVSLIDER PLUGIN IS ACTIVE

				$metabox_revslider[] = array(
							'id'       => 'mp-revslider-start',
							'type'     => 'section',
							'title'    => esc_html__('BACKGROUND SECTION // REVOLUTION SLIDER', 'plethora-framework'),
							'indent'   => true, 
							'required' => array( 
						    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
											array( METAOPTION_PREFIX .'mediapanel','=', array('revslider')),
										  ),
				);

				$metabox_revslider[] = array( 
							'id'       => METAOPTION_PREFIX .'mediapanel-bcg-revslider',
							'type'     => 'select',
							'title'    => esc_html__('Select Slider', 'plethora-framework'), 
							'desc'     => esc_html__('Select a slider to be displayed. You should use Slider Revolution plugin to create one first!', 'plethora-framework'),
							'options'  => method_exists( 'Plethora_Module_Revslider_Ext', 'get_sliders_array' ) ? Plethora_Module_Revslider_Ext::get_sliders_array() : array(),
							'required' => array( 
						    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
											array( METAOPTION_PREFIX .'mediapanel','=', array('revslider')),
										  ),
				);
			}
			return $metabox_revslider;
	    }

	    public function metabox_map() { 

			$latlong_url = 'http://www.latlong.net/';
			$snazzy_url  = 'https://snazzymaps.com/';
			$snazzy2_url  = 'https://snazzymaps.com/editor';
			$metabox_map = array(
							array(
								'id'       => 'mp-map-start',
								'type'     => 'section',
								'title'    => esc_html__('BACKGROUND SECTION // GOOGLE MAP', 'plethora-framework'),
								'indent'   => true, 
								'required' => array( 
							    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
												array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
											  ),
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-lat',
								'type'     => 'text', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Latitude', 'plethora-framework'),
								'desc'     => esc_html__('Example:', 'plethora-framework') .'<strong>51.50852</strong>. Use <a href="'. esc_url( $latlong_url ) .'" target="_blank">LatLong</a> to find easily your location coords.',
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-lon',
								'type'     => 'text', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Longtitude', 'plethora-framework'),
								'desc'     => esc_html__('Example:', 'plethora-framework') .'<strong>-0.1254</strong>. Use <a href="'. esc_url( $latlong_url ) .'" target="_blank">LatLong</a> to find easily your location coords.',
								),

							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-type',
								'type'     => 'button_set',
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Type', 'plethora-framework'),
								'options'  => array( 
									'ROADMAP'    => 'Roadmap', 
									'SATELLITE'  => 'Satellite', 
									'HYBRID'     => 'Hybrid', 
									'TERRAIN'    => 'Terrain',
									'STREETVIEW' => 'Streetview' 
									),
								'default'  => 'ROADMAP',
								),

							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-zoom',
								'type'     => 'slider', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Zoom', 'plethora-framework'),
								"default"  => 14,
								"min"      => 1,
								"step"     => 1,
								"max"      => 18,
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-marker',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Marker', 'plethora-framework'),
								'desc'     => esc_html__('Show a mark over the given location', 'plethora-framework'),
								"default"  => true
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-markertype',
								'type'     => 'select',
								'required' => array( 
									array( METAOPTION_PREFIX .'mediapanel-bcg-map-marker','=', true), 
									array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  
									),						
								'title'    => esc_html__('Marker Type', 'plethora-framework'),
								'options'  => array(
									'animated' => 'Animated',
									'standard' => 'Standard',
									'image'    => 'Custom Image'
							    ),
							    'default'  => 'animated',
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-marker-customimage',
								'type'     => 'media', 
								'required' => array( 
									array( METAOPTION_PREFIX .'mediapanel-bcg-map-marker','=', 1), 
									array( METAOPTION_PREFIX .'mediapanel-bcg-map-markertype','=', 'image'), 
									array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  
									),						
								'title'    => esc_html__('Map Marker Image (custom)', 'plethora-framework'),
								'desc'     => esc_html__('Use a custom image marker. Upload a PNG/GIF transparent image.', 'plethora-framework'),
								'url'      => false,
								),
							array(
								'id'       => THEMEOPTION_PREFIX .'mediapanel-bcg-map-markertitle',
								'type'     => 'text',
								'required' => array( 
									array( METAOPTION_PREFIX .'mediapanel-bcg-map-markertype','!=', array('image')),  
									array( METAOPTION_PREFIX .'mediapanel-bcg-map-markertype','!=', array('animated')),  
									array( METAOPTION_PREFIX .'mediapanel-bcg-map-marker','=', 1), 
									array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap'))
									),
								'title'    => esc_html__('Map Marker Hover Title', 'plethora-framework'),
								'default'  => esc_html__('We are right here!', 'plethora-framework'),
	              				'translate' => true,
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-markerwindow',
								'type'     => 'textarea',
								'required' => array( 
									array( METAOPTION_PREFIX .'mediapanel-bcg-map-markertype','!=', array('image')),  
									array( METAOPTION_PREFIX .'mediapanel-bcg-map-marker','=', 1), 
									array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap'))
									),						
								'title'    => esc_html__('Map Marker Click Window', 'plethora-framework'),
								'desc'     => esc_html__('Edit infromation window that appears on marker click ( HTML )', 'plethora-framework'),
	              				'translate' => true,
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-type-switch',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Type Control', 'plethora-framework'),
								'desc'     => esc_html__('Display map type selection control', 'plethora-framework'),
								"default"  => true
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-type-switch-position',
								'type'     => 'select',
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-bcg-map-type-switch','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Type Control Position', 'plethora-framework'),
								'options'  => array(
									'TOP_LEFT'      => 'Top Left',
									'TOP_CENTER'    => 'Top Center',
									'TOP_RIGHT'     => 'Top Right',
									'LEFT_CENTER'   => 'Middle Left',
									'RIGHT_CENTER'  => 'Middle Right',
									'BOTTOM_LEFT'   => 'Bottom Left',
									'BOTTOM_CENTER' => 'Bottom Center',
									'BOTTOM_RIGHT'  => 'Bottom Right',
							    ),
							    'default'  => 'TOP_RIGHT',
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-type-switch-style',
								'type'     => 'select', 
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-bcg-map-type-switch','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Type Control Style', 'plethora-framework'),
								'options'  => array(
									'DROPDOWN_MENU'  => 'Dropdown menu',
									'HORIZONTAL_BAR' => 'Horizontal bar',
							    ),
							    'default'  => 'DROPDOWN_MENU',
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-pan-control',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Pan Control', 'plethora-framework'),
								'desc'     => esc_html__('Display pan control', 'plethora-framework'),
								"default"  => true
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-pan-control-position',
								'type'     => 'select',
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-bcg-map-pan-control','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Pan Control Position', 'plethora-framework'),
								'options'  => array(
									'TOP_LEFT'      => 'Top Left',
									'TOP_CENTER'    => 'Top Center',
									'TOP_RIGHT'     => 'Top Right',
									'LEFT_CENTER'   => 'Middle Left',
									'RIGHT_CENTER'  => 'Middle Right',
									'BOTTOM_LEFT'   => 'Bottom Left',
									'BOTTOM_CENTER' => 'Bottom Center',
									'BOTTOM_RIGHT'  => 'Bottom Right',
							    ),
							    'default'  => 'TOP_RIGHT',
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-zoom-control',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Zoom Control', 'plethora-framework'),
								'desc'     => esc_html__('Display zoom control', 'plethora-framework'),
								"default"  => true
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-zoom-control-position',
								'type'     => 'select',
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-bcg-map-zoom-control','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Zoom Control Position', 'plethora-framework'),
								'options'  => array(
									'TOP_LEFT'      => 'Top Left',
									'TOP_CENTER'    => 'Top Center',
									'TOP_RIGHT'     => 'Top Right',
									'LEFT_CENTER'   => 'Middle Left',
									'RIGHT_CENTER'  => 'Middle Right',
									'BOTTOM_LEFT'   => 'Bottom Left',
									'BOTTOM_CENTER' => 'Bottom Center',
									'BOTTOM_RIGHT'  => 'Bottom Right',
							    ),
							    'default'  => 'TOP_RIGHT',
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-zoom-control-style',
								'type'     => 'select', 
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-bcg-map-zoom-control','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Zoom Control Style', 'plethora-framework'),
								'options'  => array(
									'DROPDOWN_MENU'  => 'Dropdown menu',
									'HORIZONTAL_BAR' => 'Horizontal bar',
							    ),
							    'default'  => 'DROPDOWN_MENU',
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-scrollwheel',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Scrolling Wheel', 'plethora-framework'),
								'desc'     => esc_html__('Disable the default scrolling wheel zooming functionality', 'plethora-framework'),
								"default"  => false,
								"on"       => 'Enable',
								"off"      => 'Disable',
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-snazzy',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Snazzy Map Styling', 'plethora-framework'),
								'desc'     => esc_html__('Enable Snazzy map styling plugin. Check Snazzy maps:', 'plethora-framework') . ' <a href="'. esc_url( $snazzy_url ) .'" target="_blank">'. $snazzy_url .'</a>',
								"default"  => false
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-bcg-map-snazzy-config',
								'type'     => 'textarea',
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-bcg-map-snazzy','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Snazzy Map Style Array', 'plethora-framework'), 
								'desc'     => esc_html__('You can create your own Snazzy map style array here:', 'plethora-framework') . ' <a href="'. esc_url( $snazzy2_url ) .'" target="_blank">'. $snazzy2_url .'</a>',
								'default'  => "[{'featureType':'water','stylers':[{'visibility':'on'},{'color':'#428BCA'}]},{'featureType':'landscape','stylers':[{'color':'#f2e5d4'}]},{'featureType':'road.highway','elementType':'geometry','stylers':[{'color':'#c5c6c6'}]},{'featureType':'road.arterial','elementType':'geometry','stylers':[{'color':'#e4d7c6'}]},{'featureType':'road.local','elementType':'geometry','stylers':[{'color':'#fbfaf7'}]},{'featureType':'poi.park','elementType':'geometry','stylers':[{'color':'#c5dac6'}]},{'featureType':'administrative','stylers':[{'visibility':'on'},{'lightness':33}]},{'featureType':'road'},{'featureType':'poi.park','elementType':'labels','stylers':[{'visibility':'on'},{'lightness':20}]},{},{'featureType':'road','stylers':[{'lightness':20}]}]",
							)
						);
			return $metabox_map;
	    }

	}
}