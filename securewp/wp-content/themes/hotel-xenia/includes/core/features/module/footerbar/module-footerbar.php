<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2017

File Description: Footer Bar Module

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Footerbar') ) {

	/**
	 */
	class Plethora_Module_Footerbar {

		public static $feature_title         = "Footer Bar";	// Feature display title  (string)
		public static $feature_description   = "Integration module for Bottom Bar functionality ( Notice: if disabled, the two additional navigation positions will also disappear )"; // Feature display description (string)
		public static $theme_option_control  = true;			// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;			// Default activation option status ( boolean )
		public static $theme_option_requires = array();		// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;			// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;		// Additional method invocation ( string/boolean | method name or false )

		function __construct(){

			// Global actions
			add_action( 'init', array( $this, 'footerbar_navigation_positions' ), 20); // Navigation positions ( set prioriy to 20 for better menu display order)

			// Set admin functionality
			if ( is_admin() ) {

				add_filter( 'plethora_themeoptions_footer', array( $this, 'theme_options'), 11);
				add_filter( 'plethora_metabox_footer_fields_edit', array( $this, 'metabox'), 11 );
			} 
		}

		// Add exclusive navigation position(s)
		public static function footerbar_navigation_positions() { 

			register_nav_menu( 'footerbar', esc_html__( 'Footer Bar Navigation', 'plethora-framework') );
		}

		/**
		 * Returns status and configuration for footer bar
		 */
		public static function get_element( $return = 'options' ) {

			  // Add Footer Bar template to the header
			$status   = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar', 1);
			if ( $return === 'status' ) {

				return $status; 

			} else {

				// Add classes to footer_bar container
				Plethora_Theme::add_container_attr( 'footer_bar', 'class', Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-colorset' ) );
				Plethora_Theme::add_container_attr( 'footer_bar', 'class', Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-transparentfilm' ) );

				// Start working on options return
				$options['cols'] = array();

				// Get layout first
				$layout_config = array(
					1 => array(	'cols' => 1, 'col_size' => array( 1 => 'col-sm-12', 2 => '', 3 => '' ) ),
					2 => array( 'cols' => 2, 'col_size' => array( 1 => 'col-sm-6', 2 => 'col-sm-6', 3 => '' ) ),
					3 => array( 'cols' => 2, 'col_size' => array( 1 => 'col-sm-9', 2 => 'col-sm-3', 3 => '' ) ),
					4 => array( 'cols' => 2, 'col_size' => array( 1 => 'col-sm-3', 2 => 'col-sm-9', 3 => '' ) ),
					5 => array( 'cols' => 3, 'col_size' => array( 1 => 'col-sm-4', 2 => 'col-sm-4', 3 => 'col-sm-4' ) ),
					6 => array( 'cols' => 3, 'col_size' => array( 1 => 'col-sm-3', 2 => 'col-sm-3', 3 => 'col-sm-6' ) ),
					7 => array( 'cols' => 3, 'col_size' => array( 1 => 'col-sm-6', 2 => 'col-sm-3', 3 => 'col-sm-3' ) ),
				);
				$layout = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-layout', 5 ); 
				$cols   = array_key_exists( $layout , $layout_config ) ? $layout_config[$layout]['cols'] : 3;
				for ( $i = 1; $i <= $cols; $i++ ) {

					$col_classes = array();
					$col_classes[] = $layout_config[$layout]['col_size'][$i];
					$col_classes[] = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-col'.$i .'-visibility' );
					$col_classes[] = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-col'.$i .'-align');
					$col_classes[] = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-col'.$i .'-extraclass');
					$content  = '';
					$content_type  = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-col'.$i );
					switch ( $content_type ) {
						case 'menu':
							$menu = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-col'.$i .'-menu' );
							ob_start();
							wp_nav_menu( array(
									'container'       => false, 
									'menu_class'      => 'footerbar_menu', 
									'depth'           => 6,
									'theme_location' => $menu,
									'walker'          => ( class_exists( 'Plethora_Module_Navwalker_Ext' )) ? new Plethora_Module_Navwalker_Ext() : '', 
								)
							);
							$content = ob_get_clean();
							break;
						
						case 'custom_text':
							$content = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-col'.$i .'-customtext' );
							$content = do_shortcode( wp_kses( $content, Plethora_Theme::allowed_html_for( 'paragraph' ) ) );
							break;
						
						case 'text':
						default:
							$content = Plethora_Theme::option( METAOPTION_PREFIX .'footerbar-col'.$i .'-text' );
							$content = do_shortcode( wp_kses( $content, Plethora_Theme::allowed_html_for( 'paragraph' ) ) );
							break;
					}

					$options['cols'][$i] = array(
						'col_class'           => esc_attr( implode(' ', $col_classes ) ),
						'col_content'         => $content,
					);

				} 
				
				return $options;
			}
		}

		/** 
		* Returns theme options configuration. Collects global and theme-specific fields
		* Hooked @ 'plethora_themeoptions_footer'
		*/
		public function theme_options( $sections ) {

			// setup theme options according to configuration
			$opts        = $this->options();
			$opts_config = $this->options_config();
			$fields      = array();
			foreach ( $opts_config as $opt_config ) {

				$id          = $opt_config['id'];
				$status      = $opt_config['theme_options'];
				$default_val = $opt_config['theme_options_default'];
				if ( $status && array_key_exists( $id, $opts ) ) {

					if ( !is_null( $default_val ) ) { // will add only if not NULL }
						$opts[$id]['default'] = $default_val;
					}
					$fields[] = $opts[$id];
				}
			}
			if ( !empty( $fields ) ) {

				$sections[] = array(
						'title'      => esc_html__('Footer Bar', 'plethora-framework'),
						'heading'	 => esc_html__('FOOTER BAR OPTIONS', 'plethora-framework'),
						'subsection' => true,
						'fields'     => $fields
				);
			}
			return $sections;
		}


		/** 
		* Returns single options configuration. Collects all metabox fields
		* Hooked @ 'plethora_metabox_footer_fields_edit'
		*/
		public function metabox( $fields ) {

			// setup theme options according to configuration
			$opts        = $this->options();
			$opts_config = $this->options_config();
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
		* Theme options tab setup
		* @since 1.0
		*
		*/
		public static function options() { 

			$options['footerbar-section'] = array(
				'id'     => 'footerbar-section',
				'type'   => 'section',
				'title'  => esc_html__('Footer Bar / General Options', 'plethora-framework'),
				'indent' => true,
			);

			$options['footerbar'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar',
				'type'     => 'switch', 
				'title'    => esc_html__('Footer Bar', 'plethora-framework'),
				'subtitle' => esc_html__('Can be overriden on page/post settings', 'plethora-framework'),
				'on'       => esc_html__('Display', 'plethora-framework') ,
				'off'      => esc_html__('Hide', 'plethora-framework'),
			);
			$options['container-type'] = array(
				'id'       => METAOPTION_PREFIX .'footer_bar-container-type',
				'type'     => 'button_set', 
				'required' => array( METAOPTION_PREFIX .'footerbar','=','1' ),						
				'title'    => esc_html__('Container Type', 'plethora-framework'),
				'options'  => array(
								'container'       => esc_html__( 'Default', 'plethora-framework'),
								'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
					)
			);

			$options['colorset'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-colorset',
				'type'     => 'button_set',
				'required' => array(METAOPTION_PREFIX .'footerbar','=','1'),						
				'title'    => esc_html__( 'Color Set', 'plethora-framework' ),
				'options'  => Plethora_Module_Style_Ext::get_options_array( array( 'type'=> 'color_sets', 'prepend_default' => true ) ),
			);

			$options['transparentfilm'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-transparentfilm',
				'type'     => 'switch', 
				'required' => array(METAOPTION_PREFIX .'footerbar','=','1'),						
				'title'    => esc_html__('Transparency Film', 'plethora-framework'),
			);

			$options['layout'] = array(
				'id'       => THEMEOPTION_PREFIX .'footerbar-layout',
				'type'     => 'image_select',
				'required' => array( METAOPTION_PREFIX .'footerbar','=','1'),						
				'title'    => esc_html__('Footer Bar Layout', 'plethora-framework'), 
				'subtitle' => esc_html__('Click to the icon according to the desired Footer Bar layout. ', 'plethora-framework'),
				'options'  => array(
								1 => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
								2 => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
								3 => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
								4 => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
								5 => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
								6 => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
								7 => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
							),
			);

			// 1st Column setup
			$options['col1-section'] = array(
				'id'       => 'footerbar-col1-section',
				'type'     => 'section',
				'required' => array( METAOPTION_PREFIX .'footerbar','=','1'),						
				'title'    => esc_html__('1st Footer Bar Column', 'plethora-framework'),
				'indent'   => true,
			);

			$options['col1'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col1',
				'type'     => 'button_set', 
				'required' => array( METAOPTION_PREFIX .'footerbar','=','1'),						
				'title'    => esc_html__('Content Display', 'plethora-framework'),
				'options'  => array(
								'menu' => esc_html__('Menu', 'plethora-framework'),
								'text' => esc_html__('Default Text', 'plethora-framework'),
							),
			);
			$options['col1-metabox'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col1',
				'type'     => 'button_set', 
				'required' => array( METAOPTION_PREFIX .'footerbar','=','1'),						
				'title'    => esc_html__('Content Display', 'plethora-framework'),
				'options'  => array(
								'menu'        => esc_html__('Menu', 'plethora-framework'),
								'text'        => esc_html__('Theme Options Default Text', 'plethora-framework'),
								'custom_text' => esc_html__('Other Text', 'plethora-framework'),
							),
			);

			$options['col1-menu'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col1-menu',
				'type'     => 'select',
				'title'    => esc_html__('Menu Location', 'plethora-framework'), 
				'data'     => 'menu_locations',
				'required' => array( 
					array( METAOPTION_PREFIX .'footerbar','=','1'), 
					array( METAOPTION_PREFIX .'footerbar-col1','=','menu') 
				),						
			);

			$options['col1-text'] = array(
				'id'           => METAOPTION_PREFIX .'footerbar-col1-text',
				'type'         => 'textarea',
				'required'     => array( array( METAOPTION_PREFIX .'footerbar','=','1'), array( METAOPTION_PREFIX .'footerbar-col1','=','text') ),						
				'title'        => esc_html__('Default Text', 'plethora-framework'), 
				'desc'         => Plethora_Theme::allowed_html_for( 'post', true ) . ' '. esc_html__('You may also use shortcode tags', 'plethora-framework'),
				'allowed_html' => Plethora_Theme::allowed_html_for( 'post' ),
				'translate'    => true,
			);

			$options['col1-customtext'] = array(
				'id'           => METAOPTION_PREFIX .'footerbar-col1-customtext',
				'type'         => 'textarea',
				'title'        => esc_html__('Other Text', 'plethora-framework'), 
				'desc'         => Plethora_Theme::allowed_html_for( 'post', true ) . ' '. esc_html__('You may also use shortcode tags', 'plethora-framework'),
				'allowed_html' => Plethora_Theme::allowed_html_for( 'post' ),
				'translate'    => true,
				'required'     => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-col1','=','custom_text') 
								),						
			);

			$options['col1-align'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col1-align',
				'type'     => 'button_set', 
				'required' => array( METAOPTION_PREFIX .'footerbar','=','1'),						
				'title'    => esc_html__('Content Alignment', 'plethora-framework'),
				'options'  => array(
								'text-left'   => esc_html__('Left', 'plethora-framework'),
								'text-center' => esc_html__('Center', 'plethora-framework'),
								'text-right'  => esc_html__('Right', 'plethora-framework'),
							),
			);
			$options['col1-visibility'] = array(
				'id'          => METAOPTION_PREFIX .'footerbar-col1-visibility',
				'type'        => 'select',
				'required'    => array( array( METAOPTION_PREFIX .'footerbar','=','1')),						
				'title'       => esc_html__('Visibility Behaviour', 'plethora-framework'),
				'description' => esc_html__('Select which is the MINIMUM screen for this column to be visible. Leave this empty to display on all screens', 'plethora-framework') ,
				'options'     => array(
									''                              => esc_html__( 'All screens', 'plethora-framework' ),
									'hidden-xs hidden-sm hidden-md' => esc_html__( 'Large screens only ( Equal or over 1200px )', 'plethora-framework' ),
									'hidden-xs hidden-sm'           => esc_html__( 'Medium devices and up ( Equal or over 992px )', 'plethora-framework' ),
									'hidden-xs'                     => esc_html__( 'Small devices and up ( Equal or over 768px )', 'plethora-framework' ),
								 ),
			);
			$options['col1-extraclass'] = array(
				'id'           => METAOPTION_PREFIX .'footerbar-col1-extraclass',
				'type'         => 'text',
				'title'        => esc_html__('Extra Class(es)', 'plethora-framework'), 
				'desc'         => esc_html__('Style the 1st footer bar column differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				'required'     => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
								),						
			);

			// 2nd Column setup
			$options['col2-section'] = array(
				'id'       => 'footerbar-col2-section',
				'type'     => 'section',
				'title'    => esc_html__('2nd Footer Bar Column', 'plethora-framework'),
				'indent'   => true,
				'required' => array( 
								array( METAOPTION_PREFIX .'footerbar','=','1'), 
								array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal', 2 ),
							)
			);
			$options['col2'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col2',
				'type'     => 'button_set', 
				'title'    => esc_html__('Content Display', 'plethora-framework'),
				'options'  => array(
									'menu' => esc_html__('Menu', 'plethora-framework'),
									'text' => esc_html__('Default Text', 'plethora-framework'),
								),
				'required' => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
								),						
			);
			$options['col2-metabox'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col2',
				'type'     => 'button_set', 
				'title'    => esc_html__('Content Display', 'plethora-framework'),
				'options'  => array(
								'menu'        => esc_html__('Menu', 'plethora-framework'),
								'text'        => esc_html__('Theme Options Default Text', 'plethora-framework'),
								'custom_text' => esc_html__('Other Text', 'plethora-framework'),
								),
				'required' => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
								),						
			);

			$options['col2-menu'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col2-menu',
				'type'     => 'select',
				'title'    => esc_html__('Menu Location', 'plethora-framework'), 
				'data'     => 'menu_locations',
				'required' => array( 
					array( METAOPTION_PREFIX .'footerbar','=','1'), 
					array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
					array( METAOPTION_PREFIX .'footerbar-col2','=','menu') 
				),						
			);
			$options['col2-text'] = array(
				'id'        => METAOPTION_PREFIX .'footerbar-col2-text',
				'type'      => 'textarea',
				'title'     => esc_html__('Default Text', 'plethora-framework'), 
				'desc'         => Plethora_Theme::allowed_html_for( 'post', true ) . ' '. esc_html__('You may also use shortcode tags', 'plethora-framework'),
				'allowed_html' => Plethora_Theme::allowed_html_for( 'post' ),
				'translate' => true,
				'required'  => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
									array( METAOPTION_PREFIX .'footerbar-col2','=','text') 
								),						
			);
			$options['col2-customtext'] = array(
				'id'           => METAOPTION_PREFIX .'footerbar-col2-customtext',
				'type'         => 'textarea',
				'title'        => esc_html__('Other Text', 'plethora-framework'), 
				'desc'         => Plethora_Theme::allowed_html_for( 'post', true ) . ' '. esc_html__('You may also use shortcode tags', 'plethora-framework'),
				'allowed_html' => Plethora_Theme::allowed_html_for( 'post' ),
				'translate'    => true,
				'required'     => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
									array( METAOPTION_PREFIX .'footerbar-col2','=','custom_text') 
								),						
			);

			$options['col2-align'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col2-align',
				'type'     => 'button_set', 
				'title'    => esc_html__('Content Alignment', 'plethora-framework'),
				'options'  => array(
								'text-left'   => esc_html__('Left', 'plethora-framework'),
								'text-center' => esc_html__('Center', 'plethora-framework'),
								'text-right'  => esc_html__('Right', 'plethora-framework'),
							 ),
				'required' 	  => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
								),						
			);
			$options['col2-visibility'] = array(
				'id'          => METAOPTION_PREFIX .'footerbar-col2-visibility',
				'type'        => 'select',
				'title'       => esc_html__('Visibility Behaviour', 'plethora-framework'),
				'description' => esc_html__('Select which is the MINIMUM screen for this column to be visible. Leave this empty to display on all screens', 'plethora-framework') ,
				'width'       => '80%',
				'options'     => array(
									''                              => esc_html__( 'All screens', 'plethora-framework' ),
									'hidden-xs hidden-sm hidden-md' => esc_html__( 'Large screens only ( Equal or over 1200px )', 'plethora-framework' ),
									'hidden-xs hidden-sm'           => esc_html__( 'Medium devices and up ( Equal or over 992px )', 'plethora-framework' ),
									'hidden-xs'                     => esc_html__( 'Small devices and up ( Equal or over 768px )', 'plethora-framework' ),
								 ),
				'required' 	  => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
								),						
			);
			$options['col2-extraclass'] = array(
				'id'           => METAOPTION_PREFIX .'footerbar-col2-extraclass',
				'type'         => 'text',
				'title'        => esc_html__('Extra Class(es)', 'plethora-framework'), 
				'desc'         => esc_html__('Style the 2nd footer bar column differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				'required'     => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
								),						
			);

			// 3rd Column setup
			$options['col3-section'] = array(
				'id'       => 'footerbar-col3-section',
				'type'     => 'section',
				'title'    => esc_html__('3rd Footer Bar Column', 'plethora-framework'),
				'indent'   => true,
				'required' => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',5),
								),						
			);
			$options['col3'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col3',
				'type'     => 'button_set', 
				'title'    => esc_html__('Content Display', 'plethora-framework'),
				'options'  => array(
								'menu' => esc_html__('Menu', 'plethora-framework'),
								'text' => esc_html__('Default Text', 'plethora-framework'),
							),
				'required' => array( 
								array( METAOPTION_PREFIX .'footerbar','=','1'), 
								array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',5),
				),	
			);

			$options['col3-metabox'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col3',
				'type'     => 'button_set', 
				'title'    => esc_html__('Content Display', 'plethora-framework'),
				'options'  => array(
								'menu'        => esc_html__('Menu', 'plethora-framework'),
								'text'        => esc_html__('Theme Options Default Text', 'plethora-framework'),
								'custom_text' => esc_html__('Other Text', 'plethora-framework'),
								),
				'required' => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',5),
								),						
			);

			$options['col3-menu'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col3-menu',
				'type'     => 'select',
				'title'    => esc_html__('Menu Location', 'plethora-framework'), 
				'data'     => 'menu_locations',
				'required' => array( 
					array( METAOPTION_PREFIX .'footerbar','=','1'), 
					array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',2),
					array( METAOPTION_PREFIX .'footerbar-col3','=','menu') 
				),						
			);
			$options['col3-text'] = array(
				'id'        => METAOPTION_PREFIX .'footerbar-col3-text',
				'type'      => 'textarea',
				'title'     => esc_html__('Default Text', 'plethora-framework'), 
				'desc'         => Plethora_Theme::allowed_html_for( 'post', true ) . ' '. esc_html__('You may also use shortcode tags', 'plethora-framework'),
				'allowed_html' => Plethora_Theme::allowed_html_for( 'post' ),
				'translate' => true,
				'required' => array( 
								array( METAOPTION_PREFIX .'footerbar','=','1'), 
								array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',5),
								array( METAOPTION_PREFIX .'footerbar-col3','=','text') 
							),						
			);
			$options['col3-customtext'] = array(
				'id'           => METAOPTION_PREFIX .'footerbar-col3-customtext',
				'type'         => 'textarea',
				'title'        => esc_html__('Other Text', 'plethora-framework'), 
				'desc'         => Plethora_Theme::allowed_html_for( 'post', true ) . ' '. esc_html__('You may also use shortcode tags', 'plethora-framework'),
				'allowed_html' => Plethora_Theme::allowed_html_for( 'post' ),
				'translate'    => true,
				'required'     => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',5),
									array( METAOPTION_PREFIX .'footerbar-col3','=','custom_text') 
								),						
			);

			$options['col3-visibility'] = array(
				'id'          => METAOPTION_PREFIX .'footerbar-col3-visibility',
				'type'        => 'select',
				'title'       => esc_html__('Visibility Behaviour', 'plethora-framework'),
				'description' => esc_html__('Select which is the MINIMUM screen for this column to be visible. Leave this empty to display on all screens', 'plethora-framework') ,
				'width'       => '80%',
				'options'     => array(
									''                              => esc_html__( 'All screens', 'plethora-framework' ),
									'hidden-xs hidden-sm hidden-md' => esc_html__( 'Large screens only ( Equal or over 1200px )', 'plethora-framework' ),
									'hidden-xs hidden-sm'           => esc_html__( 'Medium devices and up ( Equal or over 992px )', 'plethora-framework' ),
									'hidden-xs'                     => esc_html__( 'Small devices and up ( Equal or over 768px )', 'plethora-framework' ),
								 ),
				'required' 	  => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',5),
								),						
			);

			$options['col3-align'] = array(
				'id'       => METAOPTION_PREFIX .'footerbar-col3-align',
				'type'     => 'button_set', 
				'title'    => esc_html__('Content Alignment', 'plethora-framework'),
				'options'  => array(
						'text-left'   => esc_html__('Left', 'plethora-framework'),
						'text-center' => esc_html__('Center', 'plethora-framework'),
						'text-right'  => esc_html__('Right', 'plethora-framework'),
						),
				'required' => array( 
								array( METAOPTION_PREFIX .'footerbar','=','1'), 
								array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',5),
							),						
			);

			$options['col3-extraclass'] = array(
				'id'           => METAOPTION_PREFIX .'footerbar-col3-extraclass',
				'type'         => 'text',
				'title'        => esc_html__('Extra Class(es)', 'plethora-framework'), 
				'desc'         => esc_html__('Style the 3rd footer bar column differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
				'required'     => array( 
									array( METAOPTION_PREFIX .'footerbar','=','1'), 
									array( METAOPTION_PREFIX .'footerbar-layout','is_larger_equal',5),
								),						
			);
			return $options;
		}
	}
}