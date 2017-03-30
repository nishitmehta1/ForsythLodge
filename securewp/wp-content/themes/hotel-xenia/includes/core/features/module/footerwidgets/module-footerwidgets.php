<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			       (c) 2017

Footer Widgets Module Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Footerwidgets') ) {

	class Plethora_Module_Footerwidgets {

		public static $feature_title        = "Footer Widgets Module";
		public static $feature_description  = "Manages footer widgets section";
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		public function __construct() {
			
			// Add theme options fields tab to Footer Section
			add_filter( 'plethora_themeoptions_footer', array( $this, 'theme_options'), 10 );
			// Add metabox options to single post's Footer Section
			add_filter( 'plethora_metabox_footer_fields_edit', array( $this, 'metabox_options'), 10 );
		}

		/**
		 * Returns widgetized areas template status for the footer area according to given row
		 */
		public function get_template_status( $row ) {

			$status = false; 
			if ( in_array( $row, array( 1, 2, '1', '2' ) ) ) { 

				$status = Plethora_Theme::option( METAOPTION_PREFIX .'footer-widgets-'. $row, Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-widgets-'. $row ) );
			}
			return $status;
		}
		
		/**
		 * Returns widgetized areas template options configuration according to given row
		 */
		public function get_template_config( $row ) {

			if ( ! in_array( $row, array( 1, 2, '1', '2' ) ) ) { return array(); }

			// get all user set options
			$layout              = Plethora_Theme::option( METAOPTION_PREFIX .'footer-widgets-'.$row.'-layout', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-widgets-'.$row.'-layout' ) );
			$sidebar_col_1       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-1', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-sidebar-'.$row.'-1' ) );
			$sidebar_col_1_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-1-extraclass', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-sidebar-'.$row.'-1-extraclass' ) );
			$sidebar_col_2       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-2', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-sidebar-'.$row.'-2' ) );
			$sidebar_col_2_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-2-extraclass', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-sidebar-'.$row.'-2-extraclass' ) );
			$sidebar_col_3       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-3', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-sidebar-'.$row.'-3' ) );
			$sidebar_col_3_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-3-extraclass', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-sidebar-'.$row.'-3-extraclass' ) );
			$sidebar_col_4       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-4', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-sidebar-'.$row.'-4' ) );
			$sidebar_col_4_class = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-'.$row.'-4-extraclass', Plethora_Module_Themeoptions::get_option_default_value( $this, METAOPTION_PREFIX .'footer-sidebar-'.$row.'-4-extraclass' ) );

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
				'layout'       => $layout,
				'row_desc'     => ( $row == 1 ) ? esc_html__( 'FIRST ROW', 'plethora-framework') : esc_html__( 'SECOND ROW', 'plethora-framework'),
				'widget_areas' => $widget_areas
			);
		}

		/** 
		* Returns theme options tab configuration
		* Hooked @ 'plethora_themeoptions_general'
		* @return array()
		*/
		public function theme_options( $sections ) {

			$theme_options = Plethora_Module_Themeoptions::get_themeoptions_fields( $this );
			if ( is_array( $theme_options ) && !empty( $theme_options ) ) {

				$sections[] = array(
					'title'      => esc_html__('Widgetized Areas', 'plethora-framework'),
					'heading'    => esc_html__('FOOTER WIDGETIZED AREAS', 'plethora-framework'),
					'subsection' => true,
					'fields'     => $theme_options
				);
			}
			return $sections;
		}

		/** 
		* Returns theme options tab configuration
		* Hooked @ 'plethora_themeoptions_general'
		* @return array()
		*/
		public function metabox_options( $fields ) {

			$metabox_fields = Plethora_Module_Themeoptions::get_metabox_fields( $this );
			if ( ! empty( $metabox_fields ) ) {

				$fields = array_merge( $fields, $metabox_fields );
			}
			return $fields;
		}

		/** 
		* MUST HAVE METHOD FOR ALL MODULES USING OPTIONS
		* Returns theme options / metabox fields index
		* Options configuration should not contain 'default' value ( anyway, it will be ignored on the late configuration)
		* @return array()
		*/
		public function options_index() { 

			$options_index = array();

			$options_index['heading-widgets-1'] = array(
				'id'       => 'heading-first-row',
				'type'     => 'section',
				'title'    => esc_html__('Footer Widgets Area // 1st Row', 'plethora-framework'),
				'subtitle' => esc_html__('Options for the 1st row of footer widgets', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['widgets-1'] = array(
				'id'       => METAOPTION_PREFIX .'footer-widgets-1',
				'type'     => 'switch', 
				'title'    => esc_html__('Display Widgets', 'plethora-framework'),
				'subtitle' => esc_html__('Display/hide footer widgets 1st row', 'plethora-framework'),
				'on'       => esc_html__('Display', 'plethora-framework'),
				'off'      => esc_html__('Hide', 'plethora-framework'),
			);
			$options_index['widgets-1-container-type'] = array(
				'id'       => METAOPTION_PREFIX .'footer_top-container-type',
				'type'     => 'button_set', 
				'required' => array(METAOPTION_PREFIX .'footer-widgets-1','=','1'),						
				'title'    => esc_html__('Container Type', 'plethora-framework'),
				'options'  => array(
					'container'       => esc_html__( 'Default', 'plethora-framework'),
					'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
				)
			);
			$options_index['widgets-1-layout'] = array(
				'id'       => METAOPTION_PREFIX .'footer-widgets-1-layout',
				'type'     => 'image_select',
				'title'    => esc_html__('Widget Columns Layout', 'plethora-framework'), 
				'subtitle' => esc_html__('Click to the icon according to the desired widget columns layout. ', 'plethora-framework'),
				'options'  => array(
					1 => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
					2 => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
					3 => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
					4 => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
					5 => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
					6 => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
					7 => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
					8 => array('alt' => '4 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_4.png'),
				),
				'required' => array(METAOPTION_PREFIX .'footer-widgets-1','=','1'),						
			);
			$options_index['widgets-1-sidebar-1'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-1-1',
				'type'     => 'select',
				'title'    => esc_html__('Column 1-1 Sidebar', 'plethora-framework'), 
				'data'	   => 'sidebars',
				'default'  => 'sidebar-footer-1-1',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',1),
				),
			);
			$options_index['widgets-1-sidebar-1-extraclass'] = array(
				'id'      => METAOPTION_PREFIX .'footer-sidebar-1-1-extraclass',
				'type'    => 'text', 
				'title'   => esc_html__('Column 1-1 Sidebar Extra Classes', 'plethora-framework'),
				'desc'    => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				"default" => '',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',1),
				),
			);
			$options_index['widgets-1-sidebar-2'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-1-2',
				'type'     => 'select',
				'title'    => esc_html__('Column 1-2 Sidebar', 'plethora-framework'), 
				'data'	   => 'sidebars',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',2),
				),
			);
			$options_index['widgets-1-sidebar-2-extraclass'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-1-2-extraclass',
				'type'     => 'text', 
				'title'    => esc_html__('Column 1-2 Sidebar Extra Classes', 'plethora-framework'),
				'desc'     => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',2),
				),
			);
			$options_index['widgets-1-sidebar-3'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-1-3',
				'type'     => 'select',
				'title'    => esc_html__('Column 1-3 Sidebar', 'plethora-framework'), 
				'data'	   => 'sidebars',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',5),
				),
			);
			$options_index['widgets-1-sidebar-3-extraclass'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-1-3-extraclass',
				'type'     => 'text', 
				'title'    => esc_html__('Column 1-3 Sidebar Extra Classes', 'plethora-framework'),
				'desc'     => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','is_larger_equal',5),
				),
			);
			$options_index['widgets-1-sidebar-4'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-1-4',
				'type'     => 'select',
				'title'    => esc_html__('Column 1-4 Sidebar', 'plethora-framework'), 
				'data'	   => 'sidebars',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','=',8),
				),
			);
			$options_index['widgets-1-sidebar-4-extraclass'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-1-4-extraclass',
				'type'     => 'text', 
				'title'    => esc_html__('Column 1-4 Sidebar Extra Classes', 'plethora-framework'),
				'desc'     => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-1','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-1-layout','=',8),
				),
			);
			$options_index['heading-widgets-2'] = array(
				'id'       => 'footer-row2-start',
				'type'     => 'section',
				'title'    => esc_html__('Footer Widgets Area // 2nd Row', 'plethora-framework'),
				'subtitle' => esc_html__('Options for the 2nd row of footer widgets', 'plethora-framework'),
				'indent'   => true,
			);
			$options_index['widgets-2'] = array(
				'id'       => METAOPTION_PREFIX .'footer-widgets-2',
				'type'     => 'switch', 
				'title'    => esc_html__('Display Widgets', 'plethora-framework'),
				'subtitle' => esc_html__('Display/hide footer widgets 2nd row', 'plethora-framework'),
				"default"  => 0,
				'on'       => esc_html__('Display', 'plethora-framework'),
				'off'      => esc_html__('Hide', 'plethora-framework'),
			);
			$options_index['widgets-2-container-type'] = array(
				'id'       => METAOPTION_PREFIX .'footer_main-container-type',
				'type'     => 'button_set', 
				'title'    => esc_html__('Container Type', 'plethora-framework'),
				'default'  => 'container',
				'options'  => array(
					'container'       => esc_html__( 'Default', 'plethora-framework'),
					'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
				),
				'required' => array(METAOPTION_PREFIX .'footer-widgets-2','=','1'),						
			);
			$options_index['widgets-2-layout'] = array(
				'id'       => METAOPTION_PREFIX .'footer-widgets-2-layout',
				'type'     => 'image_select',
				'title'    => esc_html__('Widget Columns Layout', 'plethora-framework'), 
				'subtitle' => esc_html__('Click to the icon according to the desired widget columns layout. ', 'plethora-framework'),
				'options'  => array(
					1 => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
					2 => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
					3 => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
					4 => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
					5 => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
					6 => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
					7 => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
					8 => array('alt' => '4 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_4.png'),
				),
				'required' => array( METAOPTION_PREFIX .'footer-widgets-2','=','1' ),						
				'default' => 1
			);
			$options_index['widgets-2-sidebar-1'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-2-1',
				'type'     => 'select',
				'title'    => esc_html__('Column 2-1 Sidebar', 'plethora-framework'), 
				'data'	   => 'sidebars',
				'default'  => 'sidebar-footer-2-1',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',1),
				),
			);
			$options_index['widgets-2-sidebar-1-extraclass'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-2-1-extraclass',
				'type'     => 'text', 
				'title'    => esc_html__('Column 2-1 Sidebar Extra Classes', 'plethora-framework'),
				'desc'     => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				"default"  => '',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',1),
				),
			);
			$options_index['widgets-2-sidebar-2'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-2-2',
				'type'     => 'select',
				'title'    => esc_html__('Column 2-2 Sidebar', 'plethora-framework'), 
				'data'     => 'sidebars',
				'default'  => 'sidebar-footer-2-2',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',2),
				),
			);
			$options_index['widgets-2-sidebar-2-extraclass'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-2-2-extraclass',
				'type'     => 'text', 
				'title'    => esc_html__('Column 2-2 Sidebar Extra Classes', 'plethora-framework'),
				'desc'     => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				"default"  => '',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',2),
				),
			);
			$options_index['widgets-2-sidebar-3'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-2-3',
				'type'     => 'select',
				'title'    => esc_html__('Column 2-3 Sidebar', 'plethora-framework'), 
				'data'	   => 'sidebars',
				'default'  => 'sidebar-footer-2-3',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',5),
				),
			);
			$options_index['widgets-2-sidebar-3-extraclass'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-2-3-extraclass',
				'type'     => 'text', 
				'title'    => esc_html__('Column 2-3 Sidebar Extra Classes', 'plethora-framework'),
				'desc'     => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				"default"  => '',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-2-layout','is_larger_equal',5),
				),
			);
			$options_index['widgets-2-sidebar-4'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-2-4',
				'type'     => 'select',
				'title'    => esc_html__('Column 2-4 Sidebar', 'plethora-framework'), 
				'data'	   => 'sidebars',
				'default'  => 'sidebar-footer-2-4',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-2-layout','=',8),
				),
			);
			$options_index['widgets-2-sidebar-4-extraclass'] = array(
				'id'       => METAOPTION_PREFIX .'footer-sidebar-2-4-extraclass',
				'type'     => 'text', 
				'title'    => esc_html__('Column 2-4 Sidebar Extra Classes', 'plethora-framework'),
				'desc'     => esc_html__('Style widgetized area differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				"default"  => '',
				'required' => array(
					array( METAOPTION_PREFIX .'footer-widgets-2','=',1),
					array( METAOPTION_PREFIX .'footer-widgets-2-layout','=',8),
				),
			);
			return $options_index;
		}

		/** 
		* ONLY FOR EXTENSION CLASS USE, THIS IS PLACED HERE FOR REFERENCE & CONSISTENCY
		*
		* Sets a configuration pattern for theme options / metabox fields. You can set the display order
		* ( according to the order given here ) and whether you want a field to be displayed on theme options
		* or the metabox view and finally its default value on both views.
		*
		* 'id': 					the option index key ( don't confuse this with the actual DB saved id )
		* 'theme_options': 			display this field on theme options ( true|false )
		* 'theme_options_default': 	default value, null if we don't need one ( multi|null )
		* 'metabox': 				display this field on metabox options ( true|false )
		* 'metabox_default': 		default value for metabox option, null if we want to inherit the theme options default value ( multi|null)
		*
		* @return array()
		*/
		public function options_config() {

			return array();
		}
	}
}