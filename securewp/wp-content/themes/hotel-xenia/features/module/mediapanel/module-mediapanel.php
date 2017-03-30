<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Media Panel Module Extension Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Mediapanel') && !class_exists('Plethora_Module_Mediapanel_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/mediapanel/module-mediapanel.php
   */
  class Plethora_Module_Mediapanel_Ext extends Plethora_Module_Mediapanel { 

		public $gmap_support       = false;
		public $slider_support     = true;

	// Override default, as we want to add Headings Style option
	public function config() {

		// We need enabled as default status for archive and single pages...the rest will follow the 

		$config['status'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-status', $this->mediapanel_status);
		$config['status'] = is_404() ? true : $config['status'];
		// Add all setup to $plethora_mediapanel global
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
			$config['hgroup']                 = empty( $title ) && empty( $subtitle ) ? false : true;
			$config['hgroup_title']           = $title;
			$config['hgroup_subtitle']        = $subtitle;
			$config['hgroup_breadcrumb']      = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-breadcrumb', 0 ) ? Plethora_Theme::get_breadcrumb() : '';
			$config['hgroup_textalign']       = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-textalign', 'text-center');
			$config['hgroup_container']       = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-container', 'container');
			$config['hgroup_width']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-width', 'width_100pc');
			$config['hgroup_style']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-hgroup-style', 'default_hgroup_styling');
			$config['hgroup_subtitle_before'] = ( $config['hgroup_style'] === 'large_hgroup_styling' && !empty( $config['hgroup_subtitle'] ) ) ? true : false;
			$config['hgroup_subtitle_after']  = ( $config['hgroup_style'] === 'default_hgroup_styling' && !empty( $config['hgroup_subtitle'] ) ) ? true : false;
			// Additional background configuration for image / slider / revslider and 404 types
			$config['template_name']        = '';
			switch ( $config['type'] ) {
				case 'color':
					$config['container']['style'][] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-color', 'color_set') === 'custom_color' ? 'background-color: '. Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-bcg-color-custom', '#2ecc71') .' !important;' : '';
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
					$config['enqueues']['inits'][]   = array('handle' => 'owlcarousel2', 'script' => $this->init_script_owlslider() );
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

	// Override default, as we want to modify the defaults and remove map options
    public function themeoptions_general_misc_fields() {

	    $misc_fields = array(

			array(
				'id'       => 'mediapanel-hgroup-styling-start',
				'type'     => 'section',
				'title'    => esc_html__('Headings Group Styling', 'hotel-xenia'),
				'subtitle' => esc_html__('These Options are applied on media panel headings container. You may consider the sum of vertical padding values as a minimum media panel height ( when headings are present of course ). So if you set a 120px for top and bottom, the minimum panel height will be 240px. These are global options, therefore they CANNOT be overriden per page. ', 'hotel-xenia'), 
				'indent'   => true, 
				'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
			),
				array(
					'id'            => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding',
					'type'          => 'spacing',
					'left'          => false,
					'right'         => false,
					'display_units' => false,
					'title'         => esc_html__('Vertical Padding (medium and large devices)',  'hotel-xenia'),
					'subtitle'      => esc_html__('default: 166px / 170px', 'hotel-xenia'),
					'desc'          => esc_html__('Padding for medium displays, from 992px to 1200px and up', 'hotel-xenia'), 
					'default'       => array( 'padding-top'=>'166', 'padding-bottom'=>'170' ),
					'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
					),												

				array(
					'id'            => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding-sm',
					'type'          => 'spacing',
					'left'          => false,
					'right'         => false,
					'display_units' => false,
					'title'         => esc_html__('Vertical Padding (small devices)',  'hotel-xenia'),
					'subtitle'      => esc_html__('default: 150px / 60px', 'hotel-xenia'),
					'desc'          => esc_html__('Padding for small displays, from 768px to 991px', 'hotel-xenia'), 
					'default'       => array( 'padding-top'=>'150', 'padding-bottom'=>'60' ),
					'required'      => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
					),												

				array(
					'id'            => THEMEOPTION_PREFIX .'mediapanel-less-hgroup-padding-xs',
					'type'          => 'spacing',
					'left'          => false,
					'right'         => false,
					'display_units' => false,
					'title'         => esc_html__('Vertical Padding (extra small devices)',  'hotel-xenia'),
					'subtitle'      => esc_html__('default: 120px / 40px', 'hotel-xenia'),
					'desc'          => esc_html__('Padding for extra small displays, below 768px', 'hotel-xenia'), 
					'default'       => array( 'padding-top'=>'120', 'padding-bottom'=>'40' ),
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
					'title'          => esc_html__('Large Style / Title Font Size (medium and large devices)', 'hotel-xenia'),
					'subtitle'       => esc_html__('default: 86px', 'hotel-xenia'),
					'desc'           => esc_html__('Font size for medium -992px to 1200px- and large displays, over 1200px', 'hotel-xenia'), 
					'default'        => array( 'font-size' => '86px'),
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
					'title'          => esc_html__('Large Style / Title Font Size (small devices)', 'hotel-xenia'),
					'subtitle'       => esc_html__('default: 60px', 'hotel-xenia'),
					'desc'           => esc_html__('Font size for small displays, from 768px to 992px', 'hotel-xenia'), 
					'default'        => array( 'font-size' => '60px'),
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
					'title'          => esc_html__('Large Style / Title Font Size (extra small devices)', 'hotel-xenia'),
					'subtitle'       => esc_html__('default: 36px', 'hotel-xenia'),
					'desc'           => esc_html__('Font size for extra small displays, below 768px', 'hotel-xenia'), 
					'default'        => array( 'font-size' => '36px'),
					'required'       => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
					),	
			array(
				'id'       => 'mediapanel-image-start',
				'type'     => 'section',
				'title'    => esc_html__('Image Background Height', 'hotel-xenia'),
				'subtitle' => esc_html__('Note: these options are applied only when "Featured Image" OR "Other Image" background type is displayed. These are global options, therefore they CANNOT be overriden per page', 'hotel-xenia'),
				'indent'   => true, 
				'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),
			),
				array(
					'id'            => METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height',
					'type'          => 'spinner', 
					'title'         => esc_html__('Media Panel Height (large devices)', 'hotel-xenia'), 
					'desc'          => esc_html__('Panel height (in pixels) when a featured or other image is displayed', 'hotel-xenia'), 
					'subtitle'      => esc_html__('default: 380px', 'hotel-xenia'),
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
					'title'         => esc_html__('Media Panel Height (small devices)', 'hotel-xenia'), 
					'desc'          => esc_html__('Panel height (in pixels) for small devices when a featured or other image is displayed', 'hotel-xenia'), 
					'subtitle'      => esc_html__('default: 280px', 'hotel-xenia'),
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
					'title'         => esc_html__('Media Panel Height (extra small devices)', 'hotel-xenia'), 
					'desc'          => esc_html__('Panel height (in pixels) for extra small devices when a featured or other image is displayed', 'hotel-xenia'), 
					'subtitle'      => esc_html__('default: 80px', 'hotel-xenia'),
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
		);
		return $misc_fields;
	}
	// Override default, as we want to add Headings Style option
    public function metabox_headings() { 

		$required_attrs = array( 
			    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
								array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
								array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
							  );
		$metabox_headings[] = array(
					'id'       => 'mp-headings-start',
					'type'     => 'section',
					'title'    => esc_html__('HEADINGS GROUP SECTION', 'hotel-xenia'),
					'required' => $required_attrs,
					'indent'    => true 
		);
		$metabox_headings[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-title',
					'type'     => 'button_set', 
					'title'    => esc_html__('Title Display', 'hotel-xenia'),
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
					'title' => esc_html__('Custom Title', 'hotel-xenia'),
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
					'title'    => esc_html__('Subtitle Display', 'hotel-xenia'),
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
					'title'     => esc_html__('Custom Subtitle', 'hotel-xenia'),
					'translate' => true,
					'required'  =>  array( 
				    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
									array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
									array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
									array( METAOPTION_PREFIX .'mediapanel-hgroup-subtitle','=', array('customsubtitle')),
							  ),
		);

		$metabox_headings[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-breadcrumb',
					'type'     => 'switch', 
					'title'    => esc_html__('Breadcrumb Display', 'hotel-xenia'),
					'required' => $required_attrs,
					'on'       => esc_html__('Yes', 'hotel-xenia'),
					'off'      => esc_html__('No', 'hotel-xenia'),
					'default'  => 0,
		);

		$metabox_headings[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-container',
					'type'     => 'button_set', 
					'title'    => esc_html__('Container Type', 'hotel-xenia'),
					'default'  => 'container',
					'options'  => array(
							'container'       => esc_html__( 'Default', 'hotel-xenia' ),
							'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia' ),
						),
					'required'  =>  array( 
				    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
									array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
									array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
							  ),
		);

		$metabox_headings[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-width',
					'type'     => 'button_set', 
					'title'    => esc_html__('Container Width', 'hotel-xenia'),
					'default'  => 'width_100pc',
					'options'  => array(
							'width_100pc' => esc_html__( '100%', 'hotel-xenia' ),
							'width_80pc'  => esc_html__( '80%', 'hotel-xenia' ),
							'width_50pc'  => esc_html__( '50%', 'hotel-xenia' ),
						),
					'required'  =>  array( 
				    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
									array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
									array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
							  ),
		);

		$metabox_headings[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-style',
					'type'     => 'button_set', 
					'title'    => esc_html__('Headings Style', 'hotel-xenia'),
					'default'  => 'default_hgroup_styling',
					'options'  => array(
							'default_hgroup_styling' => esc_html__( 'Default', 'hotel-xenia' ),
							'large_hgroup_styling'   => esc_html__( 'Large', 'hotel-xenia' ),
						),
					'required'  =>  array( 
				    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
									array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
									array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
							  ),
		);


		$metabox_headings[] = array(
					'id'       => METAOPTION_PREFIX .'mediapanel-hgroup-textalign',
					'type'     => 'button_set', 
					'title'    => esc_html__('Align', 'hotel-xenia'),
					'default'  => 'text-center',
					'options'  => array(
							'text-left'   => esc_html__( 'Left', 'hotel-xenia' ),
							'text-center' => esc_html__( 'Center', 'hotel-xenia' ),
							'text-right'  => esc_html__( 'Right', 'hotel-xenia' ),
						),
					'required'  =>  array( 
				    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
									array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
									array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
							  ),
		);

		return $metabox_headings;
    }
  }
}