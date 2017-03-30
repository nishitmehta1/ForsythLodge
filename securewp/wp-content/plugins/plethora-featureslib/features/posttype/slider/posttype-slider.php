<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Slider Post Type Feature Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Slider') ) {  
  /**
   * @package Plethora Framework
   */

  class Plethora_Posttype_Slider {

		
	public static $feature_title         = "Slider Post Type";                               // Feature display title  (string)
	public static $feature_description   = "Contains all slider related post configuration"; // Feature display description (string)
	public static $theme_option_control  = true;                                             // Will this feature be controlled in theme options panel ( boolean )
	public static $theme_option_default  = true;                                             // Default activation option status ( boolean )
	public static $theme_option_requires = array();                                          // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
	public static $dynamic_construct     = true;                                             // Dynamic class construction ? ( boolean )
	public static $dynamic_method        = false;                                            // Additional method invocation ( string/boolean | method name or false )

	private $post_type_slug = 'slider';

	public function __construct() {

	  // Create basic post type object
	  $posttype = $this->create();

	  // Single slider Metabox    
	  add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));   

	  // Scripts/styles for post/post-new pages
	  add_action('admin_print_styles-post.php'    , array( $this, 'admin_post_print_css')); 
	  add_action('admin_print_styles-post-new.php', array( $this, 'admin_post_print_css')); 

	}

	public function create() {

		// Names
		$names = array(

		  'post_type_name'  =>  $this->post_type_slug, 
		  'slug'            =>  $this->post_type_slug, 
		  'menu_item_name'  =>  esc_html__('Plethora Sliders', 'plethora-framework'),
		  'singular'        =>  esc_html__('Plethora Slider', 'plethora-framework'),
		  'plural'          =>  esc_html__('Plethora Sliders', 'plethora-framework'),

		);

		// Options
		$options = array(

		  'enter_title_here' => esc_html__( 'Slider title', 'plethora-framework'), 
		  'description'         => '',   
		  'public'              => false,    
		  'exclude_from_search' => true,    
		  'publicly_queryable'  => false,    
		  'show_ui'             => true,    
		  'show_in_nav_menus'   => false,    
		  'show_in_menu'        => true,    
		  'show_in_admin_bar'   => true,    
		  'menu_position'       => 5,       
		  'menu_icon'           => 'dashicons-slides',
		  'hierarchical'        => false,    
		  'supports'        => array( 
							'title', 
						   ), 
		);    

		$names    = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_names', $names );
		$options  = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_options', $options );
		$slider = new Plethora_Posttype( $names, $options );

		return $slider;
	}

	/** 
	* Returns single options configuration. Collects global and theme-specific fields
	* Hooked @ 'plethora_metabox_add'
	*/
	public function single_metabox( $metaboxes ) {

	  $sections[] = array(
		'title'         => esc_html__('Slides', 'plethora-framework'),
		'icon_class'    => 'icon-large',
		'icon'          => 'el-icon-photo',
		'fields'        => array(
			array(
			  'id'           => METAOPTION_PREFIX .'slider-slides',
			  'type'         => 'repeater',
			  'title'        => esc_html__( 'Slides', 'plethora-framework' ),
			  'subtitle'     => esc_html__('Add as many slides as you need. You should be careful though, as too many slides with large sized images may cause slow page loading times', 'plethora-framework'),
			  'group_values' => true, // Group all fields below within the repeater ID
			  'item_name'    => 'slide', // Add a repeater block name to the Add and Delete buttons
			  'bind_title'   => 'slide_caption_title', // Bind the repeater block title to this field ID
			  //'static'     => 2, // Set the number of repeater blocks to be output
			  //'limit'      => 2, // Limit the number of repeater blocks a user can create
			  'sortable'     => true, // Allow the users to sort the repeater blocks or not
			  'translate'    => true,
			  'fields'       => $this->get_slide_options(),
			  'default'      => ''
			)
		)
	  );

	  $sections[] = array(
		'title'      => esc_html__('Settings', 'plethora-framework'),
		'icon_class' => 'icon-large',
		'icon'       => 'el-icon-wrench-alt',
		'fields'     => $this->get_settings_options()
	  );

	  // This filter is used to hook additional option sections...LEAVE IT THERE!
	  if ( has_filter( 'plethora_metabox_singleslider') ) {

		$sections = apply_filters( 'plethora_metabox_singleslider', $sections );
	  }

	  $metaboxes[] = array(
		  'id'            => 'metabox-slider',
		  'title'         => esc_html__( 'Slider Options', 'plethora-framework' ),
		  'post_types'    => array( 'slider'),
		  'position'      => 'normal', // normal, advanced, side
		  'priority'      => 'high', // high, core, default, low
		  'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
		  'sections'      => $sections,
	  );

	  return $metaboxes;
	}


	public function get_slide_options() {

	  // setup theme options according to configuration
	  $opts        = $this->slide_options();
	  $opts_config = $this->slide_options_config();
	  $fields      = array();
	  foreach ( $opts_config as $opt_config ) {

		$id          = $opt_config['id'];
		$status      = $opt_config['metabox'];
		$default_val = $opt_config['metabox_default'];
		if ( $status && array_key_exists( $id, $opts ) ) {

		  if ( !is_null( $default_val ) ) { // will add only if not NULL }
			$opts[$id]['default'] = $default_val;
		  }
		  $fields[] = $opts[$id];
		}
	  }

	  return $fields;
	}

	public function get_settings_options() {

	  // setup settings options tab according to configuration
	  $opts        = $this->settings_options();
	  $opts_config = $this->settings_options_config();
	  $fields      = array();
	  foreach ( $opts_config as $opt_config ) {

		$id          = $opt_config['id'];
		$status      = $opt_config['metabox'];
		$default_val = $opt_config['metabox_default'];
		if ( $status && array_key_exists( $id, $opts ) ) {

		  if ( !is_null( $default_val ) ) { // will add only if not NULL }
			$opts[$id]['default'] = $default_val;
		  }
		  $fields[] = $opts[$id];
		}
	  }
	  return $fields;
	}

	/** 
	* Returns single options index for final configuration
	*/
	public function slide_options() {

	  $slide_options['status'] = array(
		  'id'      =>'slide_status',
		  'type'    => 'checkbox', 
		  'title'   => esc_html__( 'Display Status', 'plethora-framework'),
		  'on'      => esc_html__( 'Display', 'plethora-framework' ),
		  'off'     => esc_html__( 'Do Not Display', 'plethora-framework' ),
	  );
	  $slide_options['image'] = array(
		  'id'    =>'slide_image',
		  'type'  => 'media', 
		  'title' => esc_html__('Image', 'plethora-framework'),
		  'desc'  => esc_html__( 'Slide will not be displayed, if left empty!', 'plethora-framework' ),
		  'url'   => false,
	  );
	  $slide_options['colorset'] = array(
		  'id'      => 'slide_colorset',
		  'type'    => 'select',
		  'title'   => esc_html__( 'Slide Color Set', 'plethora-framework' ),
		  'options' => array( 'foo' => 'Default', 'skincolored_section' => 'Primary', 'secondary_section' => 'Secondary', 'dark_section' => 'Dark', 'light_section' => 'Light', 'black_section' => 'Black', 'white_section' => 'White' ),
	  );
	  $slide_options['transparentfilm'] = array(
		  'id'      => 'slide_transparentfilm',
		  'type'    => 'select',
		  'title'   => esc_html__( 'Slide Overlay Semi-Transparent Film', 'plethora-framework' ),
		  'options' => array( 'transparent_film' => 'Yes', 'foo' => 'No'),
	  );
	  $slide_options['section-captions'] = array(
		  'id'       => 'captions',
		  'type'     => 'raw',
		  'content'   => '<h4 style="padding-top: 20px; font-size:18px;">'. esc_html__('CAPTIONS', 'plethora-framework') .'</h4><hr>',
	  );
	  $slide_options['caption-title'] = array(
		  'id'          => 'slide_caption_title',
		  'type'        => 'text',
		  'title'       => esc_html__( 'Main Caption Title', 'plethora-framework' ),
		  'placeholder' => esc_html__( 'Title', 'plethora-framework' ),
		  'class'       => 'large-text',
	  );
	  $slide_options['caption-subtitle'] = array(
		  'id'          => 'slide_caption_subtitle',
		  'type'        => 'text',
		  'title'       => esc_html__( 'Main Caption Subtitle', 'plethora-framework' ),
		  'placeholder' => esc_html__( 'Subtitle ( main headings section )', 'plethora-framework' ),
		  'class'       => 'large-text',
	  );
	  $slide_options['caption-secondarytitle'] = array(
		  'id'    => 'slide_caption_secondarytitle',
		  'type'  => 'text',
		  'title' => esc_html__( 'Additional Caption Title', 'plethora-framework' ),
		  'class' => 'large-text',
	  );
	  $slide_options['caption-secondarytext'] = array(
		  'id'    => 'slide_caption_secondarytext',
		  'type'  => 'text',
		  'title' => esc_html__( 'Additional Caption Text', 'plethora-framework' ),
		  'class' => 'large-text',
	  );
	  $slide_options['caption-colorset'] = array(
		  'id'      => 'slide_caption_colorset',
		  'type'    => 'select',
		  'title'   => esc_html__( 'Caption Color Set', 'plethora-framework' ),
		  'options' => array( 'foo' => 'Default', 'skincolored_section' => 'Primary', 'secondary_section' => 'Secondary', 'dark_section' => 'Dark', 'light_section' => 'Light', 'black_section' => 'Black', 'white_section' => 'White' ),
	  );
	  $slide_options['caption-transparentfilm'] = array(
		  'id'      => 'slide_caption_transparentfilm',
		  'type'    => 'select',
		  'title'   => esc_html__( 'Caption Background Transparency', 'plethora-framework' ),
		  'options' => array( 'transparent_film' => 'Yes', '' => 'No'),
	  );
	  $slide_options['caption-size'] = array(
		  'id'      => 'slide_caption_size',
		  'type'    => 'select',
		  'title'   => esc_html__( 'Caption Width', 'plethora-framework' ),
		  'options' => array( 'foo' => '50%', 'caption-full' => '80%'),
	  );
	  $slide_options['caption-align'] = array(
		  'id'          => 'slide_caption_align',
		  'type'        => 'select',
		  'title'       => esc_html__( 'Caption Container Align', 'plethora-framework' ),
		  'placeholder' => esc_html__( 'Title', 'plethora-framework' ),
		  'options'     => array( 'caption_left' => 'Left', 'foo' => 'Center', 'caption_right' => 'Right' ),
	  );
	  $slide_options['caption-textalign'] = array(
		  'id'          => 'slide_caption_textalign',
		  'type'        => 'select',
		  'title'       => esc_html__( 'Caption Text Align', 'plethora-framework' ),
		  'placeholder' => esc_html__( 'Title', 'plethora-framework' ),
		  'options'     => array( 'text-left' => 'Left', 'centered' => 'Center', 'text-right' => 'Right' ),
	  );
	  $slide_options['caption-neutralizetext'] = array(
		  'id'      => 'slide_caption_neutralizetext',
		  'type'    => 'select',
		  'title'   => esc_html__('Neutralize Links ( links to be displayed as normal text )', 'plethora-framework'),
		  'options' => array( 
						'foo'              => 'No',
						'neutralize_links' => 'Yes',
						),
	  );
	  $slide_options['caption-headingstyle'] = array(
		  'id'      => 'slide_caption_headingstyle',
		  'type'    => 'select',
		  'title'   => esc_html__( 'Caption Style', 'plethora-framework' ),
		  'options' => array( 
					  ''                => 'Default', 
					  'caption_flat'    => 'Flat', 
					  'caption_fancy'   => 'Fancy', 
					  'caption_elegant' => 'Elegant', 
					  ),
	  );
	  $slide_options['caption-animation'] = array(
		  'id'      => 'slide_caption_animation',
		  'type'    => 'select',
		  'title'   => esc_html__('Caption Animation', 'plethora-framework'),
		  'options' => Plethora_Module_Style::get_options_array( array( 
						  'type'              => 'animations', 
						  'use_in'            => 'redux',
						  'title_alt'         => true,
						  'prefix_all_values' => 'animated'
						   )),
	  );
	  $slide_options['section-button'] = array(
		  'id'       => 'section-button',
		  'type'     => 'raw',
		  'content'   => '<h4 style="padding-top: 20px; font-size:18px;">'. esc_html__('BUTTON', 'plethora-framework') .'</h4><hr>',
	  );
	  $slide_options['buttonlinktext'] = array(
		  'id'      => 'slide_caption_buttonlinktext',
		  'type'    => 'text',
		  'title'   => esc_html__('Button Link Text ( not visible if empty )', 'plethora-framework'),
	  );
	  $slide_options['buttonlinkurl'] = array(
		  'id'      => 'slide_caption_buttonlinkurl',
		  'type'    => 'text',
		  'title'   => esc_html__('Button Link URL ( not visible if empty or \'#\' )', 'plethora-framework'),
		  'validate'=> 'url'
	  );
	  $slide_options['buttonstyle'] = array(
		  'id'       => 'slide_caption_buttonstyle',
		  'type'     => 'select',
		  'title'    => esc_html__('Button Style', 'plethora-framework'),
		  'options'  => array( 
						'btn-link'      => 'Default',
						'btn-primary'   => 'Primary',
						'btn-secondary' => 'Secondary',
						'btn-white'     => 'White',
						'btn-success'   => 'Success',
						'btn-info'      => 'Info',
						'btn-warning'   => 'Warning',
						'btn-danger'    => 'Danger',
						),
	  );
	  $slide_options['buttonlinktarget'] = array(
		  'id'      => 'slide_caption_buttonlinktarget',
		  'type'    => 'select',
		  'title'   => esc_html__('Button Link URL Open', 'plethora-framework'),
		  'options' => array( '_self' => 'Same Window', '_blank' => 'New Window/Tab' ),
	  );
	  $slide_options['buttonsize'] = array(
		  'id'      => 'slide_caption_buttonsize',
		  'type'    => 'select',
		  'title'   => esc_html__('Button Size', 'plethora-framework'),
		  'options' => array( 
						'btn btn-lg' => 'Large',
						'btn'        => 'Normal',
						'btn btn-sm' => 'Small',
						'btn btn-xs' => 'Extra Small',
						),
	  );

	  // OPTIONS INTRODUCED ON XENIA THEME 
	  // All -xenia suffixed options are different version of existing options
	  $slide_options['caption-transparentfilm-xenia'] = array(
		  'id'      => 'slide_caption_transparentfilm',
		  'type'    => 'select',
		  'title'   => esc_html__( 'Caption Background Transparency', 'plethora-framework' ),
		  'options' => array( 'foo' => 'No', 'transparent_film' => esc_html__( 'Semi Transparent' ), 'transparent' => 'Transparent', ),
	  );
	  $slide_options['buttonstyle-xenia'] = array(
		  'id'       => 'slide_caption_buttonstyle',
		  'type'     => 'select',
		  'title'    => esc_html__('Button Style', 'plethora-framework'),
		  'options'  => array( 
						'foo'         => 'Default',
						'btn-inv'  => 'Inverted',
						'btn-link' => 'Link',
						),
	  );
	  $slide_options['buttoncolor'] = array(
		  'id'       => 'slide_caption_buttoncolor',
		  'type'     => 'select',
		  'title'    => esc_html__('Button Color', 'plethora-framework'),
		  'options'  => array( 
						'btn-default'   => 'Default',
						'btn-primary'   => 'Primary',
						'btn-secondary' => 'Secondary',
						'btn-white'     => 'White',
						'btn-success'   => 'Success',
						'btn-info'      => 'Info',
						'btn-warning'   => 'Warning',
						'btn-danger'    => 'Danger',
						),
	  );

	  return $slide_options;
	}

	public function settings_options() {

	  $settings_options['autoplay'] = array(
		'id'      => METAOPTION_PREFIX .'slider-autoplay',
		'type'    => 'switch', 
		'title'   => esc_html__('Auto Play', 'plethora-framework'),
		'default' => true,
	  );  
	  $settings_options['autoplaytimeout'] = array(
		'id'       => METAOPTION_PREFIX .'slider-autoplaytimeout',
		'type'     => 'slider', 
		'required' => array( METAOPTION_PREFIX .'slider-autoplay', '=', 1),
		'title'    => esc_html__('Autoplay Interval Timeout', 'plethora-framework'),
		'desc'     => esc_html__('Display time of this slide', 'plethora-framework'),
		"min"      => 100,
		"step"     => 100,
		"max"      => 20000,
		"default"  => 5000,
	  );  
	  $settings_options['autoplayspeed'] = array(
		'id'       => METAOPTION_PREFIX .'slider-autoplayspeed',
		'type'     => 'slider', 
		'required' => array( METAOPTION_PREFIX .'slider-autoplay', '=', 1),
		'title'    => esc_html__('Autoplay Speed', 'plethora-framework'),
		'desc'     => esc_html__('Time to switch to the next slide', 'plethora-framework'),
		"min"      => 100,
		"step"     => 100,
		"max"      => 10000,
		"default"  => 1000,
	  );  
	  $settings_options['autoplayhoverpause'] = array(
		'id'       => METAOPTION_PREFIX .'slider-autoplayhoverpause',
		'type'     => 'switch', 
		'required' => array( METAOPTION_PREFIX .'slider-autoplay', '=', 1),
		'title'    => esc_html__('Pause On Mouse Hover', 'plethora-framework'),
		'default'  => true,
	  );  
	  $settings_options['nav'] = array(
		'id'      => METAOPTION_PREFIX .'slider-nav',
		'type'    => 'switch', 
		'title'   => esc_html__('Show navigation buttons', 'plethora-framework'),
		'default' => true,
	  );  
	  $settings_options['dots'] = array(
		'id'      => METAOPTION_PREFIX .'slider-dots',
		'type'    => 'switch', 
		'title'   => esc_html__('Show navigation bullets', 'plethora-framework'),
		'default' => true,
	  );  
	  $settings_options['loop'] = array(
		'id'      => METAOPTION_PREFIX .'slider-loop',
		'type'    => 'switch', 
		'title'   => esc_html__('Slideshow Loop', 'plethora-framework'),
		'default' => false,
	  );  
	  $settings_options['mousedrag'] = array(
		'id'      => METAOPTION_PREFIX .'slider-mousedrag',
		'type'    => 'switch', 
		'title'   => esc_html__('Mouse drag', 'plethora-framework'),
		'default' => true,
	  );  
	  $settings_options['touchdrag'] = array(
		'id'      => METAOPTION_PREFIX .'slider-touchdrag',
		'type'    => 'switch', 
		'title'   => esc_html__('Touch drag', 'plethora-framework'),
		'default' => true,
	  );  
	  $settings_options['lazyload'] = array(
		'id'      => METAOPTION_PREFIX .'slider-lazyload',
		'type'    => 'switch', 
		'title'   => esc_html__('Lazy Load Images', 'plethora-framework'),
		'default' => true,
	  );  
	  $settings_options['rtl'] = array(
		'id'      => METAOPTION_PREFIX .'slider-rtl',
		'type'    => 'switch', 
		'title'   => esc_html__('Right To Left', 'plethora-framework'),
		'desc'   => esc_html__('Change elements direction from Right to left', 'plethora-framework'),
		'default' => false,
	  );  

	  return $settings_options;
	}

	/** 
	* Slide options configuration for repeater field
	* This method should be overriden on extension class
	*/
	public function slide_options_config() {

	  $slides_config = array();
	  return $slides_config;
	}

	/** 
	* Setting options configuration
	* This method should be overriden on extension class
	*/
	public function settings_options_config() {

	  $settings_options = array();
	  return $settings_options;
	}

	/** 
	* Returns given slider slides configuration for front end processing
	* @param $sliderid
	* @return array()
	*/
	public static function get_slides( $sliderid ) {

	  $slider = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-slides', array(), $sliderid);
	  if ( empty( $slider ) ) { return array(); }

	  $slides = array();

	  //auxiliary variables
	  $kses_allowed = array('span' => array( 'id' => array(), 'class' => array(), 'style' => array() ), 'i' => array( 'id' => array(), 'class' => array(), 'style' => array() ), 'strong' => array(), 'em' => array() );
	  
	  if ( isset( $slider['slide_image'] ) ) { 
		foreach ($slider['slide_image'] as $key => $slide ) {
		 if ( !empty($slide) ) { 

			// IMPORTANT: USE THE IMAGE ID, NOT THE SAVED URL 
			$savedimg = $slider['slide_image'][$key];
			$fullimg  = wp_get_attachment_image_src( $savedimg['id'], 'full' );
			$status   = ( isset( $slider['slide_status'][$key] ) ) ? $slider['slide_status'][$key] : true;
			if ( $status && isset( $fullimg[0] ) && !empty( $fullimg[0] ) ) { 
			  $slide                             = array();
			  $slide['bg_image']                 = esc_url( $fullimg[0] );
			  $slide['colorset']                 = $slider['slide_colorset'][$key] !== 'foo' ? esc_attr( $slider['slide_colorset'][$key] ) : '';
			  $slide['transparentfilm']          = $slider['slide_transparentfilm'][$key] !== 'foo' ? esc_attr( $slider['slide_transparentfilm'][$key] ) : '';
			  $slide['captions']                 = ( !empty( $slide['caption_title'][$key] ) || !empty( $slide['caption_subtitle'][$key] ) || !empty( $slide['caption_secondarytitle'][$key] ) || !empty( $slide['caption_secondarytext'][$key] ) ) ? true : false; 
			  $slide['caption_title']            = wp_kses( $slider['slide_caption_title'][$key], $kses_allowed );
			  $slide['caption_subtitle']         = wp_kses( $slider['slide_caption_subtitle'][$key], $kses_allowed );
			  $slide['caption_secondarytitle']   = wp_kses( $slider['slide_caption_secondarytitle'][$key], $kses_allowed );
			  $slide['caption_secondarytext']    = wp_kses( $slider['slide_caption_secondarytext'][$key], $kses_allowed );
			  $slide['caption_colorset']         = $slider['slide_caption_colorset'][$key] !== 'foo' ? esc_attr( $slider['slide_caption_colorset'][$key] ) : '';
			  $slide['caption_transparentfilm']  = $slider['slide_caption_transparentfilm'][$key] !== 'foo' ? esc_attr( $slider['slide_caption_transparentfilm'][$key] ) : '';
			  $slide['caption_size']             = $slider['slide_caption_size'][$key] !== 'foo' ? esc_attr( $slider['slide_caption_size'][$key] ) : '';
			  $slide['caption_align']            = $slider['slide_caption_align'][$key] !== 'foo' ? esc_attr( $slider['slide_caption_align'][$key] ) : '';
			  $slide['caption_textalign']        = esc_attr( $slider['slide_caption_textalign'][$key] );
			  $slide['caption_neutralizetext']   = $slider['slide_caption_neutralizetext'][$key] !== 'foo' ? esc_attr( $slider['slide_caption_neutralizetext'][$key] ) : '';
			  $slide['caption_animation']        = esc_attr( $slider['slide_caption_animation'][$key] );
			  $slide['caption_headingstyle']     = ( !empty( $slider['slide_caption_headingstyle'][$key] ) ) ? esc_attr( $slider['slide_caption_headingstyle'][$key] ) : '';
			  $slide['caption_button']           = ( !empty( $slider['slide_caption_buttonlinktext'][$key] ) && !empty( $slider['slide_caption_buttonlinkurl'][$key] ) && trim( $slider['slide_caption_buttonlinkurl'][$key] ) !== '#'  ) ? true : false;
			  $slide['caption_buttonlinktext']   = wp_kses( $slider['slide_caption_buttonlinktext'][$key], $kses_allowed );
			  $slide['caption_buttonlinkurl']    = esc_url( $slider['slide_caption_buttonlinkurl'][$key] );
			  $slide['caption_buttonstyle']      = $slider['slide_caption_buttonstyle'][$key] !== 'foo' ? esc_attr( $slider['slide_caption_buttonstyle'][$key] ) : '';
			  $slide['caption_buttoncolor']      = esc_attr( $slider['slide_caption_buttoncolor'][$key] );

			  $slide['caption_buttonsize']       = esc_attr( $slider['slide_caption_buttonsize'][$key] );
			  $slide['caption_buttonlinktarget'] = esc_attr( $slider['slide_caption_buttonlinktarget'][$key] );
			  $slides[]                          = $slide;
			} 
		  } 
		}
	  }

	  return $slides;
	}

	/** 
	* Returns given owlslider configuration for further front end processing
	* @param $sliderid
	* @return array()
	*/
	public static function get_owlslider_config( $sliderid ) {

	  $owlslider_config = array();

	  if ( $sliderid > 0 ) { 

		$owlslider_config['autoplay']           = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-autoplay', true, $sliderid );
		$owlslider_config['nav']                = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-nav', true, $sliderid );
		$owlslider_config['dots']               = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-dots', true, $sliderid );
		$owlslider_config['loop']               = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-loop', false, $sliderid );
		$owlslider_config['mousedrag']          = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-mousedrag', true, $sliderid );
		$owlslider_config['touchdrag']          = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-touchdrag', true, $sliderid );
		$owlslider_config['autoplaytimeout']    = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-autoplaytimeout', 5000, $sliderid );
		$owlslider_config['autoplayspeed']      = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-autoplayspeed', 1000, $sliderid );
		$owlslider_config['autoplayhoverpause'] = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-autoplayhoverpause', true, $sliderid );
		$owlslider_config['lazyload']           = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-lazyload', true, $sliderid );
		$owlslider_config['urltarget']          = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-urltarget', '_self', $sliderid );
		$owlslider_config['rtl']                = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-rtl', false, $sliderid );
		$owlslider_config['video']              = true;
	  }
	  return $owlslider_config;
	}


	/** 
	* CSS fixes for slider-related admin pages
	*
	* @return array
	* @since 1.0
	*
	*/
	function admin_post_print_css() {

		global $post_type;

		if ( $post_type == 'slider' ) {

		  echo '<style type="text/css">#edit-slug-box { display: none !important; visibility: hidden; }</style>';
		  echo '<style type="text/css">#post-preview { display: none !important; visibility: hidden; }</style>';
		}
	}
  }
}