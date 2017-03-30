<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2016

File Description: Room Post Type Feature Class
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Room') ) {  
 
	/**
	 * @package Plethora Framework
	 */

	class Plethora_Posttype_Room {

		// Plethora Index variables
		public static $feature_title         = "Room Post Type";		// Feature display title  (string)
		public static $feature_description   = "Contains all room related post configuration";		// Feature display description (string)
		public static $theme_option_control  = true;		// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;		// Default activation option status ( boolean )
		public static $theme_option_requires = array();	// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;		// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;	// Additional method invocation ( string/boolean | method name or false )


		// Auxiliary variables
		public $posttype_obj;
		public $post_type                      = 'room';
		public $post_type_plural               = 'rooms'; // plural ( lowercase, only for text display use )
		public $post_type_archive              = false;
		public $post_type_public               = true;
		public $post_type_supports             = array( 'title', 'editor', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions', 'page-attributes' );
		public $post_type_primary_tax          = 'room-category';
		public $post_type_primary_tax_public   = false;
		public $post_type_secondary_tax        = 'room-tag';
		public $post_type_secondary_tax_public = false;

	
		public function __construct() {

			// Create basic post type object
			$names              = $this->get_post_type_options( 'names' );
			$options            = $this->get_post_type_options( 'options' );
			$this->posttype_obj = new Plethora_Posttype( $names, $options );

			// Add taxonomies to object
			$this->posttype_obj->register_taxonomy( $this->post_type_primary_tax, $this->get_primary_taxonomy_options() );
			$this->posttype_obj->register_taxonomy( $this->post_type_secondary_tax, $this->get_secondary_taxonomy_options() );

			// Theme & metabox option hooks
			if ( is_admin() ) {
				// Add amenities and client testimonal tabs on the section index
				add_filter( 'plethora_single_'.$this->post_type.'_options_sections_index', array( $this, 'add_sections_to_single_options_index'), 10 );

				// Single Portfolio Theme Options ( hook with >100 priority )
				add_filter( 'plethora_themeoptions_content', array( $this, 'single_themeoptions'), 130);

				// Single Portfolio Metabox		
				add_filter( 'plethora_metabox_add', array( $this, 'single_metabox'), 10 );

			} else {

				add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ));

			}
		}

		public function get_post_type_options( $type = 'names' ) {

			$return = array();

			if ( $type === 'names' ) {
				// Names
				$return = array(

					'post_type_name' =>	 $this->post_type, // Carefull...this must be filled with custom post type's slug
					'slug' 			 =>	 $this->post_type, 
					'menu_item_name' =>	 sprintf( esc_html_x('%s', 'Post type menu item', 'plethora-framework'), Plethora_Theme::mb_ucfirst( $this->post_type_plural ) ),
				    'singular' 		 =>  sprintf( esc_html_x('%s', 'Post type singular label', 'plethora-framework'), Plethora_Theme::mb_ucfirst( $this->post_type ) ),
				    'plural' 		 =>  sprintf( esc_html_x('%s', 'Post type plural label', 'plethora-framework'), Plethora_Theme::mb_ucfirst( $this->post_type_plural ) ),


				);
				// Hook to apply
				$return = apply_filters( strtolower( get_class() ) . '_names', $return );
			
			} elseif ( $type === 'options' ) {

				// Options
				$return = array(

					'enter_title_here' 		=> sprintf( esc_html__('%s Title', 'plethora-framework'), ucfirst( $this->post_type ) ), // Title prompt text 
					'description'			=> '',	// A short descriptive summary of what the post type is. 
					'public'				=> $this->post_type_public,		// Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
					'exclude_from_search'	=> false,		// Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
					'publicly_queryable'	=> true,		// Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
					'show_ui' 			  	=> true,		// Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
					'show_in_nav_menus'		=> true,		// Whether post_type is available for selection in navigation menus ( default: value of public argument )
					'show_in_menu'			=> true,		// Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
					'show_in_admin_bar'		=> true,		// Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
					'menu_position'			=> 5, 			// The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
					'menu_icon' 			=> 'dashicons-admin-network', // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
					'hierarchical' 		  	=> false, 		// Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
					'has_archive' 		  	=> $this->post_type_archive,		// Enables post type archives. Will use $post_type as archive slug by default (default: false)
					'query_var' 		  	=> true,		// Sets the query_var key for this post type.  (Default: true - set to $post_type )
					'can_export' 		  	=> true, 		// Can this post_type be exported. ( Default: true )
					'supports' 				=> $this->post_type_supports, // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
					'rewrite' 			  	=> array( 
													'slug'			=> sprintf( esc_html_x( '%s', 'Rewrite slug for service post type', 'plethora-framework'), Plethora_Theme::option( THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite', $this->post_type ) ) , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
													'with_front'=> true, 		// bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
													// 'feeds'		=> true, 	// bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
													// 'pages'		=> true, 	// bool: Should the permalink structure provide for pagination. Defaults to true 
												 ), // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )

				);
				// Hook to apply
				$return = apply_filters( strtolower( get_class() ) . '_names', $return );
			}

			return $return;
		}

		public function get_primary_taxonomy_options() {

			// Taxonomy Labels
			$labels = array(
				'name'                       => esc_html__( 'Room Categories', 'plethora-framework' ),
				'singular_name'              => esc_html__( 'Room Category', 'plethora-framework' ),
				'menu_name'                  => esc_html__( 'Room Categories', 'plethora-framework' ),
				'all_items'                  => esc_html__( 'All Room Categories', 'plethora-framework' ),
				'edit_item'                  => esc_html__( 'Edit Room Category', 'plethora-framework' ),
				'view_item'                  => esc_html__( 'View Room Category', 'plethora-framework' ),
				'update_item'                => esc_html__( 'Update Room Category', 'plethora-framework' ),
				'add_new_item'               => esc_html__( 'Add New Room Category', 'plethora-framework' ),
				'new_item_name'              => esc_html__( 'New Room Category Name', 'plethora-framework' ),
				'parent_item'                => esc_html__( 'Parent Room Category', 'plethora-framework' ),
				'parent_item_colon'          => esc_html__( 'Parent Room Category:', 'plethora-framework' ),
				'search_items'               => esc_html__( 'Search Room Categories', 'plethora-framework' ),     
				'popular_items'              => esc_html__( 'Popular Room Categories', 'plethora-framework' ),
				'separate_items_with_commas' => esc_html__( 'Seperate Room Categories with commas', 'plethora-framework' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Room Categories', 'plethora-framework' ),
				'choose_from_most_used'      => esc_html__( 'Choose from most used Room Categories', 'plethora-framework' ),
				'not_found'                  => esc_html__( 'No Room Categories found', 'plethora-framework' ),
			);

			// Taxonomy options
			$options = array(
	 
				'labels'            => $labels,
				'public'            => false, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
				'show_ui'           => true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
				'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
				'show_tagcloud'     => false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
				'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
				'hierarchical'      => true, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
				'query_var'         => true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
				// 'sort'           => true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'           => array( 
										'slug'			=> sprintf( esc_html_x( '%s', 'Rewrite slug for room category taxonomy', 'plethora-framework'), Plethora_Theme::option( THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite-primary-tax', $this->post_type_primary_tax ) ) , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
										'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
										'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
									   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
			);

			// Register Room Category Taxonomy
			$options = apply_filters( strtolower( get_class() ) .'_'.$this->post_type_primary_tax.'_options', $options );
			return $options;
		}


		function get_secondary_taxonomy_options() {

			// Taxonomy Labels
			$labels = array(
				'name'                       => esc_html__( 'Room Tags', 'plethora-framework' ),
				'singular_name'              => esc_html__( 'Room Tag', 'plethora-framework' ),
				'menu_name'                  => esc_html__( 'Room Tags', 'plethora-framework' ),
				'all_items'                  => esc_html__( 'All Room Tags', 'plethora-framework' ),
				'edit_item'                  => esc_html__( 'Edit Room Tag', 'plethora-framework' ),
				'view_item'                  => esc_html__( 'View Room Tag', 'plethora-framework' ),
				'update_item'                => esc_html__( 'Update Room Tag', 'plethora-framework' ),
				'add_new_item'               => esc_html__( 'Add New Room Tag', 'plethora-framework' ),
				'new_item_name'              => esc_html__( 'New Room Tag Name', 'plethora-framework' ),
				'parent_item'                => esc_html__( 'Parent Room Tag', 'plethora-framework' ),
				'parent_item_colon'          => esc_html__( 'Parent Room Tag:', 'plethora-framework' ),
				'search_items'               => esc_html__( 'Search Room Tags', 'plethora-framework' ),     
				'popular_items'              => esc_html__( 'Popular Room Tags', 'plethora-framework' ),
				'separate_items_with_commas' => esc_html__( 'Seperate Room Tags with commas', 'plethora-framework' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Room Tags', 'plethora-framework' ),
				'choose_from_most_used'      => esc_html__( 'Choose from most used Room Tags', 'plethora-framework' ),
				'not_found'                  => esc_html__( 'No Room Tags found', 'plethora-framework' ),
			);

			// Taxonomy options
			$options = array(
	 
				'labels'            => $labels,
				'public'            => false, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
				'show_ui'           => true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
				'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
				'show_tagcloud'     => true, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
				'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
				'hierarchical'      => false, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
				'query_var'         => true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
				// 'sort'           => true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'           => array( 
										'slug'			=> sprintf( esc_html_x( '%s', 'Rewrite slug for room tag taxonomy', 'plethora-framework'), Plethora_Theme::option( THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite-secondarytax', $this->post_type_secondary_tax ) ) , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
										'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
										'hierarchical'	=> false,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
									   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
			);

			// Register Room Tag Taxonomy
			$options = apply_filters( strtolower( get_class() ) .'_'.$this->post_type_secondary_tax.'_options', $options );
			return $options;
		}


		public static function add_sections_to_single_options_index( $sections_index ) {

			$sections_index['amenities'] = array(
				'title'      => esc_html__('Amenities', 'plethora-framework'),
				'icon'       => 'fa fa-list-ul',
				'class'		 => 'ple_metabox_special_tab'
			);
			$sections_index['booking'] = array(
				'title'    => esc_html__('Booking Info', 'plethora-framework'),
				'icon'     => 'fa fa-calendar-check-o',
				'class'    => 'ple_metabox_special_tab',
			);
			return $sections_index;
		}


		/** 
		* Returns theme options configuration. Collects global and theme-specific fields
		* Hooked @ 'plethora_themeoptions_content'
		*/
		public function single_themeoptions( $sections ) {

			$fields = array();
			$sections_index = Plethora_Posttype::single_options_sections_index_for( $this->post_type );
			foreach ( $sections_index as $section => $section_config ) {

				$section_fields = Plethora_Posttype::get_single_themeoptions_section_fields( $this, $section );
				if ( !empty( $section_fields ) ) { 
					$fields[] = array(
						'id'     => METAOPTION_PREFIX . 'single'. $this->post_type .'-'.$section.'-section',
						'type'   => 'section', 
						'title'  => ( !empty( $section_config['title'] ) ) ? $section_config['title'] : '',
						'desc'   => ( !empty( $section_config['desc'] ) ) ? $section_config['desc'] : '',
						'indent' => true
					);
					$fields = array_merge( $fields, $section_fields );
				}
			}

			if ( !empty( $fields ) ) {

				$sections[] = array(
						'title'      => esc_html__('Single Room Post', 'plethora-framework'),
						'heading'    => esc_html__('SINGLE ROOM POST VIEW OPTIONS', 'plethora-framework'),
						'desc'       => esc_html__('These will be the default values for a new room post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
						'subsection' => true,
						'fields'     => $fields
				);
			}
			return $sections;

		}

		/** 
		* Returns single options configuration. Collects all metabox fields
		* Hooked @ 'plethora_metabox_add'
		*/
		public function single_metabox( $metaboxes ) {

			$sections_index = Plethora_Posttype::single_options_sections_index_for( $this->post_type );
			$sections = array();
			$priority = 10;
			foreach ( $sections_index as $section => $section_config ) {

				$fields = Plethora_Posttype::get_single_metabox_section_fields( $this, $section );
				if ( !empty( $fields ) ) {

					$section_config['fields'] =  $fields;
					$sections[] = $section_config;
				}
			}

			$metaboxes[] = array(
				'id'            => 'metabox-single-'. $this->post_type,
				'title'         => esc_html__( 'Page Options', 'plethora-framework' ),
				'post_types'    => array( $this->post_type ),
				'position'      => 'normal', // normal, advanced, side
				'priority'      => 'high', // high, core, default, low
				'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
				'sections'      => $sections,
			);
			return $metaboxes;
		}


		/** 
		* Returns single options index for final configuration
		*/
		public function single_options() {

		# Layout & Styling section options
			$single_options['layout'] = array(
						'id'      =>  METAOPTION_PREFIX .$this->post_type .'-layout',
						'title'   => esc_html__( 'Select Layout', 'plethora-framework' ),
						'type'    => 'image_select',
						'options' => Plethora_Module_Style::get_options_array( array( 
																					'type'   => 'page_layouts',
																					'use_in' => 'redux',
																			   )
									 ),
						);
			$single_options['sidebar'] = array(
						'id'       => METAOPTION_PREFIX .$this->post_type .'-sidebar',
						'type'     => 'select',
						'required' => array( METAOPTION_PREFIX .$this->post_type .'-layout','equals',array('right_sidebar','left_sidebar')),  
						'data'     => 'sidebars',
						'multi'    => false,
						'title'    => esc_html__('Select Sidebar', 'plethora-framework'), 
						);

			$single_options['containertype'] = array(
						'id'      => METAOPTION_PREFIX .$this->post_type .'-containertype',
						'type'    => 'button_set', 
						'title'   => esc_html__('Container Type', 'plethora-framework'),
						'options' => array(
										'container'       => esc_html__( 'Default', 'plethora-framework'),
										'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
							)
						);

			$single_options['colorset'] = array(
						'id'      => METAOPTION_PREFIX .$this->post_type .'-colorset',
						'type'    => 'button_set',
						'title'   => esc_html__( 'Content Section Color Set', 'plethora-framework' ),
						'desc'    => esc_html__( 'Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
						'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																					  'use_in'          => 'redux',
																					  'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
						);

			$single_options['content-align'] = array(
							'id'      => METAOPTION_PREFIX .$this->post_type .'-contentalign',
							'type'    => 'button_set', 
							'title'   => esc_html__('Content Section Align', 'plethora-framework'),
							'desc'    => esc_html__('Affects all content section text alignment, except editor text.', 'plethora-framework'),
							'options' => array(
											''            => esc_html__( 'Left', 'plethora-framework'),
											'text-center' => esc_html__( 'Center', 'plethora-framework'),
											'text-right'  => esc_html__( 'Right', 'plethora-framework'),
										 )
			);
			$single_options['extraclass'] = array(
						'id'      => METAOPTION_PREFIX .$this->post_type .'-extraclass',
						'type'    => 'text', 
						'title'   => esc_html__('Extra Classes', 'plethora-framework'),
						'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
						);
		# Text elements
			$single_options['title'] = array(
						'id'      => METAOPTION_PREFIX .$this->post_type .'-title',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Title', 'plethora-framework'),
						'desc'    => esc_html__('Enable/disable title section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
						);
			$single_options['subtitle'] = array(
						'id'      => METAOPTION_PREFIX .$this->post_type .'-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Subtitle', 'plethora-framework'),
						'desc'    => esc_html__('Enable/disable subtitle section display. You might want to disable this in case you are using media panel for subtitles display.', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
						);

			$single_options['subtitle-text'] = array(
						'id'       => METAOPTION_PREFIX .$this->post_type .'-subtitle-text',
						'required' => array( METAOPTION_PREFIX .$this->post_type .'-subtitle','equals',array( 1 )),  
						'type'     => 'text',
						'title'    => esc_html__('Subtitle', 'plethora-framework'), 
						'translate' => true,
			);
			$single_options['info-primarytax'] = array(
						'id'    => METAOPTION_PREFIX . $this->post_type .'-info-primarytax',
						'type'  => 'switch', 
						'title' => esc_html__('Room Category Label(s)', 'plethora-framework'), 

			);
			$single_options['info-primarytax-slug'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-info-primarytax-slug',
						'required' => array( METAOPTION_PREFIX . $this->post_type .'-info-primarytax','=', 1),						
						'type'     => 'select', 
						'title'    => esc_html__('Custom Primary Taxonomy', 'plethora-framework'),
						'desc'     => esc_html__('Room category is set by default as the primary taxonomy. Use this only in case you need to display a custom taxonomy associated with rooms post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
						'data'     => 'taxonomies',
			);
			$single_options['info-secondarytax'] = array(
						'id'    => METAOPTION_PREFIX . $this->post_type .'-info-secondarytax',
						'type'  => 'switch', 
						'title' => esc_html__('Room Tag Label(s)', 'plethora-framework'), 

			);
			$single_options['info-secondarytax-slug'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-info-secondarytax-slug',
						'required' => array( METAOPTION_PREFIX . $this->post_type .'-info-secondarytax','=', 1),						
						'type'     => 'select', 
						'title'    => esc_html__('Custom Secondary Taxonomy', 'plethora-framework'),
						'desc'     => esc_html__('Room tag is set by default as the secondary taxonomy. Use this only in case you need to display a custom taxonomy associated with rooms post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
						'data'     => 'taxonomies',
			);
			$single_options['excerpt'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-excerpt',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Excerpt', 'plethora-framework'),
						);
			$single_options['divider'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-divider',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Divider', 'plethora-framework'),
						);

		# Media section options
			$single_options['mediadisplay'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-mediadisplay',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Images', 'plethora-framework'),
						'desc'   => esc_html__('Display featured photo or gallery', 'plethora-framework'),
						);
			$single_options['mediadisplay-type'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-mediadisplay-type',
						'type'    => 'button_set', 
						'title'   => esc_html__('Media Type', 'plethora-framework'),
						'options' => array(
										'image' => esc_html__('Featured Image', 'plethora-framework'),
										'gallery' => esc_html__('Image Gallery', 'plethora-framework')
									 ),
						'required' => array(
										array( METAOPTION_PREFIX .$this->post_type .'-mediadisplay', 'equals', array( true ) ),
									  )  
						);
			$single_options['gallery'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-content-gallery',
						'type'     => 'gallery', 
						'title'    => esc_html__('Image Gallery', 'plethora-framework'),
						'required' => array(
										array( METAOPTION_PREFIX .$this->post_type .'-mediadisplay', 'equals', array( true ) ),
										array( METAOPTION_PREFIX .$this->post_type .'-mediadisplay-type', 'equals', array( 'gallery' ) ),
									  )  
						);
			$single_options['media-stretch'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-media-stretch',
						'type'    => 'button_set', 
						'title'   => esc_html__('Media Display Ratio', 'plethora-framework'),
						'desc'    => esc_html__('Will be applied on single AND listing view', 'plethora-framework'),
						'options' => Plethora_Module_Style::get_options_array( array( 
											'type' => 'stretchy_ratios',
											'prepend_options' => array( 'foo_stretch' => esc_html__('Native Ratio', 'plethora-framework' ) ),
											)),            
						'required' => array(
										array( METAOPTION_PREFIX .$this->post_type .'-mediadisplay', 'equals', array( true ) ),
									  )  
						);

		# Media Gallery options
			$single_options['gallery-autoplay'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-gallery-autoplay',
						'type'    => 'switch', 
						'title'   => esc_html__('Auto Play', 'plethora-framework'),
			);  
			$single_options['gallery-autoplaytimeout'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-gallery-autoplaytimeout',
						'type'     => 'slider', 
						'required' => array( METAOPTION_PREFIX . $this->post_type .'-gallery-autoplay', '=', 1),
						'title'    => esc_html__('Autoplay Interval Timeout', 'plethora-framework'),
						'desc'     => esc_html__('Display time of this slide', 'plethora-framework'),
						"min"      => 100,
						"step"     => 100,
						"max"      => 20000,
			);  
			$single_options['gallery-autoplayspeed'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-gallery-autoplayspeed',
						'type'     => 'slider', 
						'required' => array( METAOPTION_PREFIX . $this->post_type .'-gallery-autoplay', '=', 1),
						'title'    => esc_html__('Autoplay Speed', 'plethora-framework'),
						'desc'     => esc_html__('Time to switch to the next slide', 'plethora-framework'),
						"min"      => 100,
						"step"     => 100,
						"max"      => 10000,
			);  
			$single_options['gallery-autoplayhoverpause'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-gallery-autoplayhoverpause',
						'type'     => 'switch', 
						'required' => array( METAOPTION_PREFIX . $this->post_type .'-gallery-autoplay', '=', 1),
						'title'    => esc_html__('Pause On Mouse Hover', 'plethora-framework'),
			);  
			$single_options['gallery-nav'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-gallery-nav',
						'type'    => 'switch', 
						'title'   => esc_html__('Show navigation buttons', 'plethora-framework'),
			);  
			$single_options['gallery-dots'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-gallery-dots',
						'type'    => 'switch', 
						'title'   => esc_html__('Show navigation bullets', 'plethora-framework'),
			);  
			$single_options['gallery-loop'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-gallery-loop',
						'type'    => 'switch', 
						'title'   => esc_html__('Slideshow Loop', 'plethora-framework'),
			);  
			$single_options['gallery-mousedrag'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-gallery-mousedrag',
						'type'    => 'switch', 
						'title'   => esc_html__('Mouse drag', 'plethora-framework'),
			);  
			$single_options['gallery-touchdrag'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-gallery-touchdrag',
						'type'    => 'switch', 
						'title'   => esc_html__('Touch drag', 'plethora-framework'),
			);  
			$single_options['gallery-lazyload'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-gallery-lazyload',
						'type'    => 'switch', 
						'title'   => esc_html__('Lazy Load Images', 'plethora-framework'),
			);  
			$single_options['gallery-rtl'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-gallery-rtl',
						'type'    => 'switch', 
						'title'   => esc_html__('Right To Left', 'plethora-framework'),
						'desc'   => esc_html__('Change elements direction from Right to left', 'plethora-framework'),
			);  

		# Amenities section options
			$single_options['amenities-status'] = array(
						'id'      => METAOPTION_PREFIX .$this->post_type .'-amenities-status',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Amenities', 'plethora-framework'),
						'desc'    => esc_html__('Whether to display or not the amenities list', 'plethora-framework'),
						);
			$single_options['amenities-index'] = array(
						'id'           => THEMEOPTION_PREFIX .$this->post_type .'-amenities-index',
						'type'         => 'repeater',
						'title'        => esc_html__( 'Amenities Index', 'plethora-framework' ),
						'subtitle'     => esc_html__('This is the amenities index, including the amenities list that has to be filled on each single room edit screen.', 'plethora-framework'),
						'group_values' => true, // Group all fields below within the repeater ID
						'item_name'    => 'amenity', // Add a repeater block name to the Add and Delete buttons
						'bind_title'   => 'title', // Bind the repeater block title to this field ID
						// 'static'    => 2, // Set the number of repeater blocks to be output
						'limit'        => 1000, // Limit the number of repeater blocks a user can create
						'sortable'     => true, // Allow the users to sort the repeater blocks or not
						'translate'    => true,
						'fields'       => $this->get_amenities_index_field_options(),
						);
			$single_options['amenities-autoplay'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-amenities-autoplay',
						'type'    => 'switch', 
						'title'   => esc_html__('Amenities Carousel / Auto Play', 'plethora-framework'),
			);  
			$single_options['amenities-autoplaytimeout'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-amenities-autoplaytimeout',
						'type'     => 'slider', 
						'required' => array( METAOPTION_PREFIX . $this->post_type .'-amenities-autoplay', '=', 1),
						'title'    => esc_html__('Amenities Carousel / Autoplay Interval Timeout', 'plethora-framework'),
						'desc'     => esc_html__('Display time interval for before each amenity switch', 'plethora-framework'),
						"min"      => 100,
						"step"     => 100,
						"max"      => 20000,
			);  
			$single_options['amenities-autoplayspeed'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-amenities-autoplayspeed',
						'type'     => 'slider', 
						'required' => array( METAOPTION_PREFIX . $this->post_type .'-amenities-autoplay', '=', 1),
						'title'    => esc_html__('Amenities Carousel / Autoplay Speed', 'plethora-framework'),
						'desc'     => esc_html__('Switch to next amenity speed', 'plethora-framework'),
						"min"      => 100,
						"step"     => 100,
						"max"      => 10000,
			);  
			$single_options['amenities-autoplayhoverpause'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-amenities-autoplayhoverpause',
						'type'     => 'switch', 
						'required' => array( METAOPTION_PREFIX . $this->post_type .'-amenities-autoplay', '=', 1),
						'title'    => esc_html__('Amenities Carousel / Pause On Mouse Hover', 'plethora-framework'),
			);  
			$single_options['amenities-dots'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-amenities-dots',
						'type'    => 'switch', 
						'title'   => esc_html__('Amenities Carousel / Nav bullets', 'plethora-framework'),
			);  
			$single_options['amenities-loop'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-amenities-loop',
						'type'    => 'switch', 
						'title'   => esc_html__('Amenities Carousel / Loop', 'plethora-framework'),
			);  
			$single_options['amenities-mousedrag'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-amenities-mousedrag',
						'type'    => 'switch', 
						'title'   => esc_html__('Amenities Carousel / Mouse drag', 'plethora-framework'),
			);  
			$single_options['amenities-touchdrag'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-amenities-touchdrag',
						'type'    => 'switch', 
						'title'   => esc_html__('Amenities Carousel / Touch drag', 'plethora-framework'),
			);  
			$single_options['amenities-rtl'] = array(
						'id'      => METAOPTION_PREFIX . $this->post_type .'-amenities-rtl',
						'type'    => 'switch', 
						'title'   => esc_html__('Amenities Carousel / Right To Left', 'plethora-framework'),
						'desc'   => esc_html__('Change amenity icons direction from Right to left', 'plethora-framework'),
			);  

			$amenities_opts = $this->get_amenities_index( 'options' );
			$single_options['amenities-single-view'] = array(
						'id'       => METAOPTION_PREFIX .$this->post_type .'-amenities',
						'type'     => !empty( $amenities_opts ) ? 'checkbox' : 'raw',
						'title'    => esc_html__( 'Amenities', 'plethora-framework' ),
						'subtitle' => esc_html__('Check the amenities for this room. You may edit this list under:', 'plethora-framework') .'<br><strong>'. esc_html__('Theme Options > Content > Single Room Post > Amenities', 'plethora-framework') .'</strong>',
						'options'  => !empty( $amenities_opts ) ? $amenities_opts : array(),
						'content'  => empty( $amenities_opts ) ? sprintf( esc_html__( 'It seems that you have not created any records on the amenities index.%3$sPlease visit %1$sTheme Options > Content > Single Room Post > Room Amenities%2$s to edit the amenities list and try again!', 'plethora-framework' ), '<strong>', '</strong>', '<br>' ) : '',
						'required' => array( 
										array( METAOPTION_PREFIX .$this->post_type .'-amenities-status', 'equals', true )
									  ),
					  );

		# Booking section options
			$single_options['persons']  = array(
				'id'    =>  METAOPTION_PREFIX . $this->post_type .'-booking-persons',
				'type'  => 'switch',
				'title' => esc_html__( 'Display Persons Tag', 'plethora-framework' ),
			);

			$single_options['persons-text']  = array(
				'id'           =>  METAOPTION_PREFIX . $this->post_type .'-booking-persons-text',
				'type'         => 'text',
				'title'        => esc_html__( 'Persons', 'plethora-framework' ),
				'desc'         => Plethora_Theme::allowed_html_for( 'paragraph', true ),
				'validate'     => 'html',
				'html_allowed' => Plethora_Theme::allowed_html_for( 'paragraph' ),
				'required'     => array(
					array( METAOPTION_PREFIX . $this->post_type .'-booking-persons', 'equals', array( true ) ),
				)
			);

		# Advanced section options
			$single_options['urlrewrite'] = array(
						'id'               => THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite',
						'type'             => 'text',
						'title'            => sprintf( esc_html__('URL Rewrite / %s post type', 'plethora-framework'), ucfirst( $this->post_type ) ), 
						'desc'             => sprintf( esc_html__('Specify a custom permalink for %1$s post type ( i.e.: http://yoursite.com/%1$s/sample-%1$s). NOTICE: Updating this will probably result a 404 page error on every %1$s post. This can be easily fixed with a simple click on "Save Changes" button, on the "Settings > Permalinks" screen', 'plethora-framework'), $this->post_type ),
						'validate'         => 'unique_slug',
						'flush_permalinks' => true,
						);
			if ( $this->post_type_primary_tax_public ){
				$single_options['urlrewrite-primarytax'] = array(
							'id'               => THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite-primarytax',
							'type'             => 'text',
							'title'            => sprintf( esc_html__('URL Rewrite / %s taxonomy', 'plethora-framework'), $this->post_type_primary_tax ), 
							'desc'             => sprintf( esc_html__('Specify a custom permalink for %1$s taxonomy ( i.e.: http://yoursite.com/%1$s/). NOTICE: Updating this will probably result a 404 page error on every %1$s taxonomy view. This can be easily fixed with a simple click on "Save Changes" button, on the "Settings > Permalinks" screen', 'plethora-framework'), $this->post_type_primary_tax ),
							'validate'         => 'unique_slug',
							'flush_permalinks' => true,
							);
			}
			if ( $this->post_type_secondary_tax_public ){
				$single_options['urlrewrite-secondarytax'] = array(
							'id'               => THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite-secondarytax',
							'type'             => 'text',
							'title'            => sprintf( esc_html__('URL Rewrite / %s taxonomy', 'plethora-framework'), $this->post_type_secondary_tax ), 
							'desc'             => sprintf( esc_html__('Specify a custom permalink for %1$s taxonomy ( i.e.: http://yoursite.com/%1$s/). NOTICE: Updating this will probably result a 404 page error on every %1$s taxonomy view. This can be easily fixed with a simple click on "Save Changes" button, on the "Settings > Permalinks" screen', 'plethora-framework'), $this->post_type_secondary_tax ),
							'validate'         => 'unique_slug',
							'flush_permalinks' => true,
							);
			}
			return $single_options;
		}

		/** 
		* Returns amenities options configuration. 
		*/
		public function get_amenities_index_field_options() {

			// setup theme options according to configuration
			$opts        = $this->amenities_index_field_options();
			$opts_config = $this->amenities_index_field_options_config();
			$fields      = array();
			foreach ( $opts_config as $opt_config ) {

				$id	= $opt_config['id'];
				if ( array_key_exists( $id, $opts ) ) {

					if ( isset( $opt_config['default'] ) && !is_null( $opt_config['default'] ) ) { // will add only if not NULL }
						$opts[$id]['default'] = $opt_config['default'];
					}
					$fields[] = $opts[$id];
				}
			}
			return $fields;
		}

		/** 
		* Amenities index options
		*/
		public function amenities_index_field_options() {

			$amenities_options['status'] = array(
						'id'    => 'status',
						'type'  => 'switch', 
						'title' => esc_html__('Default Amenity Status', 'plethora-framework'),
						'desc' => esc_html__('Set the checkbox status default behavior when you create a new room post', 'plethora-framework'),
						'on'    => esc_html__('Checked', 'plethora-framework'),
						'off'   => esc_html__('Not Checked', 'plethora-framework'),
						);

			$amenities_options['id'] = array(
						'id'    => 'id',
						'type'  => 'text', 
						'title' => esc_html__('Unique ID', 'plethora-framework'),
						'desc'  => sprintf( esc_html__('Must be a whole word and unique in this index. If necessary use dashes instead of spaces. %1$sNOTICE: After you add this amenity on single posts, do not modify this value!%2$s', 'plethora-framework'), '<br><strong style="color:red;">', '</strong>' ),
						);

			$amenities_options['title'] = array(
						'id'    => 'title',
						'type'  => 'text', 
						'title' => esc_html__('Title', 'plethora-framework'),
						);

			$amenities_options['desc'] = array(
						'id'    => 'desc',
						'type'  => 'text', 
						'title' => esc_html__('Description', 'plethora-framework'),
						);

			$amenities_options['icon_source'] = array(
						'id'      => 'icon_source',
						'type'    => 'button_set', 
						'title'   => esc_html__('Icon Source', 'plethora-framework'),
						'options' => array( 
										'library_icon' => esc_html__('Library Icon', 'plethora-framework'),
										'custom_icon'  => esc_html__('Custom Icon', 'plethora-framework'),
									 ),
						);
			$amenities_options['library_icon'] = array(
						'id'          => 'library_icon',
						'type'        => 'icons',
						'title'       => esc_html__( 'Library Icon', 'plethora-framework' ),
						'description' => esc_html__( 'Will be displayed only if icon source is set to Library Icon', 'plethora-framework' ),
						'options'     => Plethora_Module_Icons::get_options_array(),
						);
			$amenities_options['custom_icon'] = array(
						'id'          => 'custom_icon',
						'type'        => 'media',
						'title'       => esc_html__( 'Custom Icon', 'plethora-framework' ),
						'description' => esc_html__( 'Will be displayed only if icon source is set to Custom Icon', 'plethora-framework' ),
						);

			return $amenities_options;
		}


		/** 
		* Amenities index options config ( for easier extension overrides )
		*/
		public function amenities_index_field_options_config() {

		  $config = array(
				array( 
				  'id'      => 'status', 
				  'default' => true
				  ),
				array( 
				  'id'      => 'id', 
				  'default' => '',
				  ),
				array( 
				  'id'      => 'title', 
				  'default' => esc_html__( 'New amenity', 'plethora-framework' )
				  ),
				array( 
				  'id'      => 'desc', 
				  'default' => esc_html__( 'Amenity description', 'plethora-framework' )
				  ),
				array( 
				  'id'      => 'icon_source', 
				  'default' => 'library_icon'
				  ),
				array( 
				  'id'      => 'library_icon', 
				  'default' => 'fa fa-hotel'
				  ),
				array( 
				  'id'      => 'custom_icon', 
				  'default' => ''
				  ),
		  );

		  return $config;
		}

		/** 
		* Default values for the amenities option field( use only on extension class )
		*/
		public static function get_amenities_index_field_default_value() {

			$def_amenities = array();
			$def_amenities_config = array(

				array(  
					'status'       => 0,
					'id'           => 'amenity-air-conditioner',
					'title'        => esc_html__( 'Air Conditioner', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room is equipped with an air conditioner', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-air-conditioner', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-alarm-clock',
					'title'        => esc_html__( 'Alarm Clock Service', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room has alarm clock service', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-alarm-clock-1', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-bar',
					'title'        => esc_html__( 'Mini Bar', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room is equipped with a mini bar', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-bar', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-bathtub',
					'title'        => esc_html__( 'Bathtub', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room has a bathtub', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-bathtub', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-coffee-maker',
					'title'        => esc_html__( 'Coffee Maker', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room is equipped with a coffee maker machine', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-coffee-maker', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-hair-dryer',
					'title'        => esc_html__( 'Hair Dryer', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room is equipped with a hair dryer', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-hair-dryer', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-laundry-sign',
					'title'        => esc_html__( 'Laundry Service', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room has a laundry service', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-laundry-sign', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-no-smoking',
					'title'        => esc_html__( 'Non Smoking', 'plethora-framework' ),
					'desc'         => esc_html__( 'This is a non smoking room', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-no-smoking', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-room-service-1',
					'title'        => esc_html__( 'Room Service', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room is equipped with a room service', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-room-service-1', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-safebox-1',
					'title'        => esc_html__( 'Safebox', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room is equipped with a safebox', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-safebox-1', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
				array(  
					'status'       => 0,
					'id'           => 'amenity-widescreen',
					'title'        => esc_html__( 'Widescreen TV', 'plethora-framework' ),
					'desc'         => esc_html__( 'This room is equipped with a widescreen TV', 'plethora-framework' ),
					'icon_source'  => 'library_icon', 
					'library_icon' => 'hi hi-widescreen', 
					'custom_icon'  => array( 'url'=>'', 'id'=>'', 'height'=>'', 'width'=>'', 'thumbnail' => '' ) ,
				),
			);

			foreach ( $def_amenities_config as $def_amenity ) {

				$def_amenities['redux_repeater_data'][] = array( 'title' => ''  );
				$def_amenities['status'][]              = $def_amenity['status'];
				$def_amenities['id'][]                  = $def_amenity['id'];
				$def_amenities['title'][]               = $def_amenity['title'];
				$def_amenities['desc'][]                = $def_amenity['desc'];
				$def_amenities['icon_source'][]         = $def_amenity['icon_source'];
				$def_amenities['library_icon'][]        = $def_amenity['library_icon'];
				$def_amenities['custom_icon'][]         = $def_amenity['custom_icon'];
			}
			
			return $def_amenities;
		}

		/** 
		* Single view options_config for theme options and metabox panels ( use only on extension class )
		*/
		public function single_options_config( $section = 'all' ) {

			return array();
		}

		/** 
		* Returns amenities index options, as set by administrator on theme options,
		* ready for use with the single room view checkbox field 'options' attribute
		*/
		public static function get_amenities_index( $return = '' ) {

			$amenities_index = Plethora_Theme::option( THEMEOPTION_PREFIX .'room-amenities-index', self::get_amenities_index_field_default_value() );
			$options = array();
			if ( isset( $amenities_index['id'] ) ) {
				foreach ( $amenities_index['id'] as $key => $val ) {

						# Prepare icon config first
						$icon_type  = $amenities_index['icon_source'][$key];
						$icon_class = '';
						$icon_url   = '';
						switch  ( $icon_type ) {
							case 'library_icon':
							default:

								$icon_adminonly = '<i class="amenities '. $amenities_index['library_icon'][$key] .'"></i>';
								$icon_class     = 'amenities '. $amenities_index['library_icon'][$key];
								break;
							case 'custom_icon':

								$img            = $amenities_index['custom_icon'][$key];
								$icon_class     = 'amenities';
								$icon_url       = $img['url'];
								$icon_adminonly = '<img src="'. $img['url'] .'" style="width:18px !important"/>';
								break;
						}

						# Set return value according to return preference
						if ( $return === 'options' ) {

							$options[$val]  = '<span style="display:inline-block; width: 32px; text-align:center">'. $icon_adminonly . '</span>'.  $amenities_index['title'][$key] .'  | <small>'. $amenities_index['desc'][$key] . '</small>';
						
						} elseif ( $return === 'defaults' ) {

							$options[$val]  = $amenities_index['status'][$key];
						
						} else {

							$options[$val] = array( 
								'status'     => $amenities_index['status'][$key],
								'id'         => $amenities_index['id'][$key],
								'title'      => $amenities_index['title'][$key],
								'desc'       => $amenities_index['desc'][$key],
								'icon_type'  => $icon_type,
								'icon_class' => $icon_class,
								'icon_url'   => $icon_url,
							);	
						}
				}
			}
			return $options;
		}

		/**
		 * Enqueues Owlslider scripts/styles according to media settings
		 *
		 */
		public function load_assets() {

			if ( is_singular( $this->post_type ) ) {

				$media_status     = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-mediadisplay', true );
				$media_type       = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-mediadisplay-type', 'gallery' );
				$amenities_status = Plethora_Theme::option( METAOPTION_PREFIX .$this->post_type .'-amenities-status', true );
				
				if ( ( $media_status && $media_type === 'gallery' ) || ( $amenities_status ) ) {

					// OwlCarousel scripts/styles
					wp_enqueue_style( 'owlcarousel2' );
					wp_enqueue_style( 'owlcarousel2-theme' );
					wp_enqueue_script( 'owlcarousel2' );
					if ( $media_status && $media_type === 'gallery' ) { 
						// OwlCarousel init script for single room gallery
						$owlslider_config_for_gallery_args = array( 'handle' => 'owlcarousel2', 'script' => $this->get_owlslider_init_for_gallery(), 'multiple' => true );
						Plethora_Theme::enqueue_init_script( $owlslider_config_for_gallery_args );
					}
					if ( $amenities_status ) { 
						// OwlCarousel init script for single room amenities
						$owlslider_config_for_amenities_args = array( 'handle' => 'owlcarousel2', 'script' => $this->get_owlslider_init_for_amenities(), 'multiple' => true );
						Plethora_Theme::enqueue_init_script( $owlslider_config_for_amenities_args );
					}
				}
			}
		}

		/**
		 * Returns slider options user configuration for room gallery
		 * @return array
		 */
		public function get_owlslider_config_for_gallery() {

			$owlslider_config['autoplay']           = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-autoplay', true );
			$owlslider_config['nav']                = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-nav', true );
			$owlslider_config['dots']               = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-dots', true );
			$owlslider_config['loop']               = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-loop', false );
			$owlslider_config['mousedrag']          = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-mousedrag', true );
			$owlslider_config['touchdrag']          = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-touchdrag', true );
			$owlslider_config['autoplaytimeout']    = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-autoplaytimeout', 5000 );
			$owlslider_config['autoplayspeed']      = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-autoplayspeed', 1000 );
			$owlslider_config['autoplayhoverpause'] = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-autoplayhoverpause', true );
			$owlslider_config['lazyload']           = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-lazyload', true );
			$owlslider_config['rtl']                = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-gallery-rtl', false );
			return $owlslider_config;
		}


		/**
		 * Returns Owlcarousel init script for room gallery
		 * @return string
		 */
		public function get_owlslider_init_for_gallery() {
			
			$slider = $this->get_owlslider_config_for_gallery();
			$output = '
<script type="text/javascript">
jQuery(function($) {

	"use strict";
	var $owl = $(".owl-room-single-carousel");			  
	$owl.owlCarousel({
			items              : 1,
			autoplay           : _p.checkBool('. $slider["autoplay"] .'),
			autoplayTimeout    : '.  intval( $slider["autoplaytimeout"] ) .',
			autoplaySpeed      : '.  intval( $slider["autoplayspeed"] ) .',
			autoplayHoverPause : _p.checkBool('.  $slider["autoplayhoverpause"] .'),
			nav                : _p.checkBool('.  $slider["nav"] .'),
			dots               : _p.checkBool('.  $slider["dots"] .'),
			loop               : _p.checkBool('.  $slider["loop"] .'),
			mouseDrag		   : _p.checkBool('.  $slider["mousedrag"] .'),
			touchDrag		   : _p.checkBool('.  $slider["touchdrag"] .'),
			lazyLoad      	   : _p.checkBool('.  $slider["lazyload"] .'),
			rtl      	   	   : _p.checkBool('.  $slider["rtl"] .'),
	});
});
</script>';
			return $output;

		}

		/**
		 * Returns slider options user configuration for room amenities
		 * @return array
		 */
		public function get_owlslider_config_for_amenities() {

			$owlslider_config['autoplay']           = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-autoplay', true );
			$owlslider_config['autoplaytimeout']    = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-autoplaytimeout', 1500 );
			$owlslider_config['autoplayspeed']      = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-autoplayspeed', 1000 );
			$owlslider_config['autoplayhoverpause'] = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-autoplayhoverpause', true );
			$owlslider_config['dots']               = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-dots', false );
			$owlslider_config['loop']               = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-loop', true );
			$owlslider_config['mousedrag']          = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-mousedrag', true );
			$owlslider_config['touchdrag']          = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-touchdrag', true );
			$owlslider_config['rtl']                = Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-amenities-rtl', false );
			return $owlslider_config;
		}

		/**
		 * Returns Owlcarousel init script for room amenities
		 * @return string
		 */
		public function get_owlslider_init_for_amenities() {
			
			$slider = $this->get_owlslider_config_for_amenities();
			$output = '
<script type="text/javascript">
jQuery(function($) {

	"use strict";
	var $owl = $(".owlcarousel-singleroom-amenities");			  
	$owl.owlCarousel({
			items              : 7,
			autoplay           : _p.checkBool('. $slider["autoplay"] .'),
			autoplayTimeout    : '.  intval( $slider["autoplaytimeout"] ) .',
			autoplaySpeed      : '.  intval( $slider["autoplayspeed"] ) .',
			autoplayHoverPause : _p.checkBool('.  $slider["autoplayhoverpause"] .'),
			dots               : _p.checkBool('.  $slider["dots"] .'),
			loop               : _p.checkBool('.  $slider["loop"] .'),
			mouseDrag		   : _p.checkBool('.  $slider["mousedrag"] .'),
			touchDrag		   : _p.checkBool('.  $slider["touchdrag"] .'),
			rtl      	   	   : _p.checkBool('.  $slider["rtl"] .'),
	});
});
</script>';
			return $output;

		}

		/**
		 * Returns amenities configuration for direct use within a template
		 * @return array()
		 */
		public static function get_room_amenities( $post_id = 0, $limit = 0 ) {

			$limit = $limit == 0 || $limit == '-1' ? false : intval( $limit );
			$amenities       = array();
			$amenities_index = self::get_amenities_index();
			$room_amenities  = Plethora_Theme::option( METAOPTION_PREFIX .'room-amenities', self::get_amenities_index( 'defaults' ), $post_id );
			$count = 0;
			foreach ( $room_amenities as $amenity_key => $amenity_status ) {

				if ( $amenity_status && array_key_exists( $amenity_key, $amenities_index ) ) {
						$count++;
						$amenities[] = $amenities_index[$amenity_key];
				}

				if ( $limit && $limit === $count ) { break; }
			}
			return $amenities; 
		}
	}
}	
