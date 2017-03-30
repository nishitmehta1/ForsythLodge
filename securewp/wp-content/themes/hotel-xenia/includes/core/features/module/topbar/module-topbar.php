<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Top Bar Module

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Topbar') ) {

	/**
	 */
	class Plethora_Module_Topbar {

		public static $feature_title        = "Top Bar";	// Feature display title  (string)
		public static $feature_description  = "Integration module for Top Bar functionality ( Notice: if disabled, the two additional navigation positions will also disappear )"; // Feature display description (string)
		public static $theme_option_control = true;			// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default	= true;			// Default activation option status ( boolean )
		public static $theme_option_requires= array();		// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct	= true;			// Dynamic class construction ? ( boolean )
		public static $dynamic_method		= false;		// Additional method invocation ( string/boolean | method name or false )

		function __construct(){

		  // Global actions
          add_action( 'init', array( $this, 'topbar_navigation_positions' ), 20); // Navigation positions ( set prioriy to 20 for better menu display order)

		  // Set admin functionality
		  if ( is_admin() ) { 
		      // Set theme options tab for media panel
		      add_filter( 'plethora_themeoptions_header', array( $this, 'theme_options_tab'), 30);
		      add_filter( 'plethora_themeoptions_metabox_header_elements', array( $this, 'metabox_header_options'));

		  // Set frontend functionality
	      } else { 

			  add_action( 'plethora_header', array( $this, 'top_bar'), 10);
      	  }

      	  $this->init();
		}

	    /**
	     * Only for extension class use
	     */
		public function init() {}

		static function topbar_navigation_positions() { 

	      register_nav_menu( 'topbar1', esc_html__( 'Top Bar Navigation ( column 1 )', 'plethora-framework') );
	      register_nav_menu( 'topbar2', esc_html__( 'Top Bar Navigation ( column 2 )', 'plethora-framework') );
		}

	    /**
	     * Prepare values and loads the desired template part
	     */
	    public static function top_bar() {

      	  // Add top bar template to the header
	      $top_bar   = Plethora_Theme::option( METAOPTION_PREFIX .'topbar', 1);
	      if ( $top_bar ) {

	      	$module_atts = array( 'layout', 'col1', 'col2' );
	        // Get layout first
	        $module_atts['layout']  = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-layout', '2'); 

	        // Column sizes
	        switch ( $module_atts['layout'] ) { 

	        	case '1': 
	        		$module_atts['col1']['size'] = '12';
	        		$module_atts['col2']['size'] = '';
	        		break;
	        	case '2': 
	        		$module_atts['col1']['size'] = '6';
	        		$module_atts['col2']['size'] = '6';
	        		break;
	        	case '3': 
	        		$module_atts['col1']['size'] = '9';
	        		$module_atts['col2']['size'] = '3';
	        		break;
	        	case '4': 
	        		$module_atts['col1']['size'] = '3';
	        		$module_atts['col2']['size'] = '9';
	        		break;
	        	default: 
	        		$module_atts['col1']['size'] = '6';
	        		$module_atts['col2']['size'] = '6';
	        		break;
	        }

	        // Column content 
			$module_atts['col1']['content_type'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col1', 'menu');
			$module_atts['col1']['content_menu'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col1-menu', 'topbar1');
			$module_atts['col1']['content_text'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col1-text', '');
			$module_atts['col1']['content_customtext'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col1-customtext', '');
			$module_atts['col1']['content_langswither'] = function_exists('icl_get_languages') ?  Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col1-wpmlswitcher', 0) : 0;
			$module_atts['col2']['content_type'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col2', 'text');
			$module_atts['col2']['content_menu'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col2-menu', 'topbar2');
			$module_atts['col2']['content_text'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col2-text', '<i class="fa fa-ambulance"></i> '. esc_html__('Emergency Line', 'plethora-framework') .' <strong>(+555) 959-595-959</strong>' );
			$module_atts['col2']['content_customtext'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col2-customtext', '<i class="fa fa-ambulance"></i> '. esc_html__('Emergency Line', 'plethora-framework') .' <strong>(+555) 959-595-959</strong>' );
			$module_atts['col2']['content_langswither'] = function_exists('icl_get_languages') ?  Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col2-wpmlswitcher', 0) : 0;

	        // Column visibility & alignment classes 
			$module_atts['col1']['visibility_classes'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col1-visibility', '');
			$module_atts['col2']['visibility_classes'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col2-visibility', '');
			$module_atts['col1']['align_class'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col1-align', 'text-left');
			$module_atts['col2']['align_class'] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-col2-align', 'text-right');

			// General Styling

			$module_atts['style'][] = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-transparency', 0) == '1' ? 'transparent' : '' ;

	        // Transfer prepared values using the 'set_query_var' ( this will make them available via 'get_query_var' to the template part file )
	        set_query_var( 'module_atts', $module_atts );
	        // Get the template part
	        Plethora_WP::get_template_part( 'templates/modules/topbar' ); 
	       }
	    }


	   /**
	    * Theme options tab setup
	    * @since 1.0
	    *
	    */
	    static function theme_options_tab( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Top Bar', 'plethora-framework'),
				'heading'	 => esc_html__('TOP BAR OPTIONS', 'plethora-framework'),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'       => METAOPTION_PREFIX .'topbar',
						'type'     => 'switch', 
						'title'    => esc_html__('Top Bar', 'plethora-framework'),
						'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
						"default"  => 1,
						'on'       => esc_html__('Display', 'plethora-framework') ,
						'off'      => esc_html__('Hide', 'plethora-framework'),
						),

					array(
						'id'      => METAOPTION_PREFIX .'topbar-transparency',
						'type'    => 'switch', 
						'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
						'title'   => esc_html__('Transparency', 'plethora-framework'),
						"default" => 1,
					),	

					array(
						'id'       => THEMEOPTION_PREFIX .'topbar-layout',
						'type'     => 'image_select',
						'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
						'title'    => esc_html__('Top Bar Layout', 'plethora-framework'), 
						'subtitle' => esc_html__('Click to the icon according to the desired top bar layout. ', 'plethora-framework'),
						'options'  => array(
								'1' => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
								'2' => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
								'3' => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
								'4' => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
							),
						'default' => '2'
						),

					// 1st Column setup
					array(
				       'id' => 'topbar-col1-start',
				       'type' => 'section',
					   'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
				       'title' => esc_html__('1st Top Bar Column', 'plethora-framework'),
				       'indent' => true,
					),

						array(
							'id'      => METAOPTION_PREFIX .'topbar-col1',
							'type'    => 'button_set', 
							'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
							'title'   => esc_html__('Content Display', 'plethora-framework'),
							'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'default' => 'menu',
							'options' => array(
									'menu' => esc_html__('Menu', 'plethora-framework'),
									'text'    => esc_html__('Default Text', 'plethora-framework'),
									),
							),	

						array(
							'id'       => METAOPTION_PREFIX .'topbar-col1-menu',
							'type'     => 'select',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-col1','=','menu') ),						
							'title'    => esc_html__('Menu Location', 'plethora-framework'), 
							'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'data'  => 'menu_locations',
							'default'  => 'topbar1',
							),
						array(
							'id'        => METAOPTION_PREFIX .'topbar-col1-text',
							'type'      => 'textarea',
							'required'  => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-col1','=','text') ),						
							'title'     => esc_html__('Default Text', 'plethora-framework'), 
							'subtitle'  => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'desc'      => esc_html__('HTML tags allowed', 'plethora-framework'),
							'default'   => '',
							'translate' => true,
							),

						array(
							'id'      => METAOPTION_PREFIX .'topbar-col1-align',
							'type'    => 'button_set', 
							'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
							'title'   => esc_html__('Content Alignment', 'plethora-framework'),
							'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'default' => 'text-left',
							'options' => array(
									'text-left'   => esc_html__('Left', 'plethora-framework'),
									'text-center' => esc_html__('Center', 'plethora-framework'),
									'text-right'  => esc_html__('Right', 'plethora-framework'),
									),
							),	
						array( 
							'id'          => METAOPTION_PREFIX .'topbar-col1-visibility',
							'type'     => 'select',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1')),						
							'title'       => esc_html__('Visibility Behaviour', 'plethora-framework'),
							'description' => esc_html__('Select which is the MINIMUM screen for this column to be visible. Leave this empty to display on all screens', 'plethora-framework') ,
							'options'	  => array(
												'hidden-xs hidden-sm hidden-md ' => 'Large screens only ( Equal or over 1200px )',
												'hidden-xs hidden-sm' => 'Medium devices and up (  Equal or over 992px )',
												'hidden-xs' => 'Small devices and up (  Equal or over 768px )',
											 ),
							'default'     => 'hidden-xs hidden-sm',
							),

					array(
					    'id'     => 'topbar-col1-end',
					    'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
					    'type'   => 'section',
					    'indent' => false,
					),	

					// 2nd Column setup
					array(
				       'id' => 'topbar-col2-start',
				       'type' => 'section',
					   'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
				       'title' => esc_html__('2nd Top Bar Column', 'plethora-framework'),
				       'indent' => true,
					),
						array(
							'id'      => METAOPTION_PREFIX .'topbar-col2',
							'type'    => 'button_set', 
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
							'title'   => esc_html__('Content Display', 'plethora-framework'),
							'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'default' => 'text',
							'options' => array(
									'menu' => esc_html__('Menu', 'plethora-framework'),
									'text'    => esc_html__('Default Text', 'plethora-framework'),
									),
							),	

						array(
							'id'       => METAOPTION_PREFIX .'topbar-col2-menu',
							'type'     => 'select',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-col2','=','menu'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
							'title'    => esc_html__('Menu Location', 'plethora-framework'), 
							'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'data'  => 'menu_locations',
							'default'  => 'topbar2',
							),
						array(
							'id'        => METAOPTION_PREFIX .'topbar-col2-text',
							'type'      => 'textarea',
							'required'  => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-col2','=','text'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
							'title'     => esc_html__('Default Text', 'plethora-framework'), 
							'subtitle'  => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'desc'      => esc_html__('HTML tags allowed', 'plethora-framework'),
							'default'   => '<i class="fa fa-ambulance"></i> '. esc_html__('Emergency Line', 'plethora-framework') .' <strong>(+555) 959-595-959</strong>',
							'translate' => true,
							),

						array( 
							'id'          => METAOPTION_PREFIX .'topbar-col2-visibility',
							'type'     => 'select',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),					
							'title'       => esc_html__('Visibility Behaviour', 'plethora-framework'),
							'description' => esc_html__('Select which is the MINIMUM screen for this column to be visible. Leave this empty to display on all screens', 'plethora-framework') ,
							'width'		  => '80%',
							'options'	  => array(
												'hidden-xs hidden-sm hidden-md ' => 'Large screens only ( Equal or over 1200px )',
												'hidden-xs hidden-sm' => 'Medium devices and up ( Equal or over 992px )',
												'hidden-xs' => 'Small devices and up ( Equal or over 768px )',
											 ),
							'default'     => 'hidden-xs hidden-sm',
							),

						array(
							'id'      => METAOPTION_PREFIX .'topbar-col2-align',
							'type'    => 'button_set', 
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),					
							'title'   => esc_html__('Content Alignment', 'plethora-framework'),
							'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'default' => 'text-right',
							'options' => array(
									'text-left'   => esc_html__('Left', 'plethora-framework'),
									'text-center' => esc_html__('Center', 'plethora-framework'),
									'text-right'  => esc_html__('Right', 'plethora-framework'),
									),
							),	
					array(
					    'id'     => 'topbar-col2-end',
						'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
					    'type'   => 'section',
					    'indent' => false,
					),	

					// Top Bar styling
					array(
				       'id' => 'topbar-section-start',
					   'required' => array( array( METAOPTION_PREFIX .'topbar','=','1') ),					
				       'type' => 'section',
				       'title' => esc_html__('Top Bar Color Set', 'plethora-framework'),
				       'subtitle' => esc_html__('Color options for top bar section.', 'plethora-framework'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-topbar-bgcolor',
								'type'        => 'color',
					   			'required' => array( array( METAOPTION_PREFIX .'topbar','=','1') ),					
								'title'       => esc_html__('Background Color', 'plethora-framework'), 
								'subtitle'    => esc_html__('default: #efefef', 'plethora-framework'),
								'desc'    => esc_html__('The default top bar background', 'plethora-framework'),
								'default'     => '#efefef',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-topbar-txtcolor',
								'type'        => 'color',
					   			'required' => array( array( METAOPTION_PREFIX .'topbar','=','1') ),					
								'title'       => esc_html__('Text Color', 'plethora-framework'), 
								'subtitle'    => esc_html__('default: #555555.', 'plethora-framework'),
								'desc'    => esc_html__('Text color for non linked texts ( i.e. logo title/subtitle )', 'plethora-framework'),
								'default'     => '#555555',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-topbar-linkcolor',
								'type'        => 'link_color',
					   			'required' => array( array( METAOPTION_PREFIX .'topbar','=','1') ),					
								'title'       => esc_html__('Link Text Color', 'plethora-framework'), 
								'desc'    => esc_html__('Text color for navigation items and other link anchor texts', 'plethora-framework'),
								'subtitle'    => esc_html__('default: #555555/#4EABF9', 'plethora-framework'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#555555', 
							        'hover'    => '#4EABF9',
							    	),
							    'validate'    => 'color',
								),

					array(
					    'id'     => 'topbar-section-end',
						'required' => array( array( METAOPTION_PREFIX .'topbar','=','1') ),					
					    'type'   => 'section',
					    'indent' => false,
					),	


				)
			);
			return $sections;
	    }


	   /**
	    * Hook For Metabox Header Elements tab.
	    * @since 1.0
	    *
	    */
	    static function metabox_header_options( $section ) { 

	    	// Always hook on $section['metabox_header_elements']['fields'] array key
	    	if ( isset($section['metabox_header_elements']['fields']) ) { 
		    	$section['metabox_header_elements']['fields'] = array_merge( $section['metabox_header_elements']['fields'], array(

					array(
				       'id' => 'header-topbar-start',
				       'type' => 'section',
				       'title' => esc_html__('Top Bar Options', 'plethora-framework'),
				       'indent' => true,
				     ),
						array(
							'id'       => METAOPTION_PREFIX .'topbar',
							'type'     => 'switch', 
							'title'    => esc_html__('Top Bar', 'plethora-framework'),
							'on'       => esc_html__('Display', 'plethora-framework') ,
							'off'      => esc_html__('Hide', 'plethora-framework'),
							),

						array(
							'id'      => METAOPTION_PREFIX .'topbar-transparency',
							'type'    => 'switch', 
							'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
							'title'   => esc_html__('Transparency', 'plethora-framework'),
						),	
						array(
							'id'       => THEMEOPTION_PREFIX .'topbar-layout',
							'type'     => 'image_select',
							'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
							'title'    => esc_html__('Top Bar Layout', 'plethora-framework'), 
							'subtitle' => esc_html__('Click to the icon according to the desired top bar layout. ', 'plethora-framework'),
							'options'  => array(
									'1' => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
									'2' => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
									'3' => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
									'4' => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
								),
							),
						array(
							'id'      => METAOPTION_PREFIX .'topbar-col1',
							'type'    => 'button_set', 
							'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
							'title'   => esc_html__('Col 1: Content Display', 'plethora-framework'),
							'options' => array(
									'menu'       => esc_html__('Menu', 'plethora-framework'),
									'text'       => esc_html__('Default Text', 'plethora-framework'),
									'customtext' => esc_html__('Custom Text', 'plethora-framework'),
									),
							),	
						array(
							'id'       => METAOPTION_PREFIX .'topbar-col1-menu',
							'type'     => 'select',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-col1','=','menu') ),						
							'title'    => esc_html__('Col 1: Menu Location', 'plethora-framework'), 
							'data'  => 'menu_locations',
							),
						array(
							'id'           => METAOPTION_PREFIX .'topbar-col1-customtext',
							'type'         => 'textarea',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-col1','=','customtext') ),						
							'title'        => esc_html__('Col 1: Custom Text', 'plethora-framework'), 
							'desc'         => esc_html__('HTML tags allowed', 'plethora-framework'),
							),
						array( 
							'id'          => METAOPTION_PREFIX .'topbar-col1-wpmlswitcher',
							'type'        => 'switch', 
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1')),						
							'title'       => esc_html__('Col 1: WPML Language Switcher', 'plethora-framework'),
							'description' => esc_html__('Displayed on the left side, before content display ( WPML must be installed & activated ).', 'plethora-framework') ,
							'on'          => esc_html__('Display', 'plethora-framework'),
							'off'         => esc_html__('Hide', 'plethora-framework'),
							),

						array(
							'id'      => METAOPTION_PREFIX .'topbar-col1-align',
							'type'    => 'button_set', 
							'required' => array( METAOPTION_PREFIX .'topbar','=','1'),						
							'title'   => esc_html__('Col 1: Content Alignment', 'plethora-framework'),
							'options' => array(
									'text-left'   => esc_html__('Left', 'plethora-framework'),
									'text-center' => esc_html__('Center', 'plethora-framework'),
									'text-right'  => esc_html__('Right', 'plethora-framework'),
									),
							),	
						array( 
							'id'          => METAOPTION_PREFIX .'topbar-col1-visibility',
							'type'     => 'select',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1')),						
							'title'       => esc_html__('Col 1: Visibility Behaviour', 'plethora-framework'),
							'description' => esc_html__('Select which is the MINIMUM screen for this column to be visible. Leave this empty to display on all screens', 'plethora-framework') ,
							'width'		  => '80%',
							'options'	  => array(
												'' => 'All screens',
												'hidden-xs hidden-sm hidden-md ' => 'Large screens only ( Equal or over 1200px )',
												'hidden-xs hidden-sm' => 'Medium devices and up ( Equal or over 992px )',
												'hidden-xs' => 'Small devices and up ( Equal or over 768px )',
											 ),
							),


						array(
							'id'      => METAOPTION_PREFIX .'topbar-col2',
							'type'    => 'button_set', 
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
							'title'   => esc_html__('Col 2: Content Display', 'plethora-framework'),
							'options' => array(
									'menu'       => esc_html__('Menu', 'plethora-framework'),
									'text'       => esc_html__('Default Text', 'plethora-framework'),
									'customtext' => esc_html__('Custom Text', 'plethora-framework'),
									),
							),	

						array(
							'id'       => METAOPTION_PREFIX .'topbar-col2-menu',
							'type'     => 'select',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-col2','=','menu'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
							'title'    => esc_html__('Col 2: Menu Location', 'plethora-framework'), 
							'data'  => 'menu_locations',
							),
						array(
							'id'           => METAOPTION_PREFIX .'topbar-col2-customtext',
							'type'         => 'textarea',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-col2','=','customtext'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
							'title'        => esc_html__('Col 2: Custom Text', 'plethora-framework'), 
							'desc'         => esc_html__('HTML tags allowed', 'plethora-framework'),
							),
						array( 
							'id'          => METAOPTION_PREFIX .'topbar-col2-wpmlswitcher',
							'type'        => 'switch', 
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),						
							'title'       => esc_html__('Col 2: WPML Language Switcher', 'plethora-framework'),
							'description' => esc_html__('Displayed on the right side, after content display ( WPML must be installed & activated ).', 'plethora-framework') ,
							'on'          => esc_html__('Display', 'plethora-framework'),
							'off'         => esc_html__('Hide', 'plethora-framework'),
							),

						array( 
							'id'          => METAOPTION_PREFIX .'topbar-col2-visibility',
							'type'     => 'select',
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),					
							'title'       => esc_html__('Col 2: Visibility Behaviour', 'plethora-framework'),
							'description' => esc_html__('Select which is the MINIMUM screen for this column to be visible. Leave this empty to display on all screens', 'plethora-framework') ,
							'width'		  => '80%',
							'options'	  => array(
												'' => 'All screens',
												'hidden-xs hidden-sm hidden-md ' => 'Large screens only ( Equal or over 1200px )',
												'hidden-xs hidden-sm' => 'Medium devices and up ( Equal or over 992px )',
												'hidden-xs' => 'Small devices and up ( Equal or over 768px )',
											 ),
							),

						array(
							'id'      => METAOPTION_PREFIX .'topbar-col2-align',
							'type'    => 'button_set', 
							'required' => array( array( METAOPTION_PREFIX .'topbar','=','1'), array( METAOPTION_PREFIX .'topbar-layout','!=','1') ),					
							'title'   => esc_html__('Col 2: Content Alignment', 'plethora-framework'),
							'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
							'options' => array(
									'text-left'   => esc_html__('Left', 'plethora-framework'),
									'text-center' => esc_html__('Center', 'plethora-framework'),
									'text-right'  => esc_html__('Right', 'plethora-framework'),
									),
							),	
					array(
				       'id' => 'header-topbar-end',
				       'type' => 'section',
				       'indent' => false,
				     ),
					)
				);
			}
			
			return $section;
	    }	    
	}
}