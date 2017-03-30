<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2016

File Description: Service Post Type Feature Class
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Service') ) {  
 
	/**
	 * @package Plethora Framework
	 */

	class Plethora_Posttype_Service {

		// Plethora Index variables
		public static $feature_title         = "Service Post Type";		// Feature display title  (string)
		public static $feature_description   = "Contains all service related post configuration";		// Feature display description (string)
		public static $theme_option_control  = true;		// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;		// Default activation option status ( boolean )
		public static $theme_option_requires = array();	// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;		// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;	// Additional method invocation ( string/boolean | method name or false )

		// Auxiliary variables
		public $posttype_obj;
		public $post_type                      = 'service';
		public $post_type_plural               = 'services'; // plural ( lowercase, only for text display use )
		public $post_type_has_archive          = false;
		public $post_type_public               = true;
		public $post_type_exclude_from_search  = false;
		public $post_type_hierarchical         = false;
		public $post_type_menuicon             = 'dashicons-star-filled';
		public $post_type_supports             = array( 'title', 'editor', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions' );
		public $post_type_primary_tax          = 'service-category';
		public $post_type_primary_tax_public   = true;
		public $post_type_secondary_tax        = 'service-tag';
		public $post_type_secondary_tax_public = true;
	
		public function __construct() {

			// Create basic post type object
			$names              = $this->get_post_type_options( 'names' );
			$options            = $this->get_post_type_options( 'options' );
			$this->posttype_obj = new Plethora_Posttype( $names, $options );

			// Add taxonomies to object
			if ( $this->post_type_primary_tax ) { $this->posttype_obj->register_taxonomy( $this->post_type_primary_tax, $this->get_primary_taxonomy_options() ) ; }
			if ( $this->post_type_secondary_tax ) { $this->posttype_obj->register_taxonomy( $this->post_type_secondary_tax, $this->get_secondary_taxonomy_options() ); }

			// Theme & metabox option hooks
			if ( is_admin() ) {

				// Single Portfolio Theme Options ( hook with >100 priority )
				add_filter( 'plethora_themeoptions_content', array( $this, 'single_themeoptions'), 130);

				// Single Portfolio Metabox		
				add_filter( 'plethora_metabox_add', array( $this, 'single_metabox'), 10 );
			}	
		}

		public function get_post_type_options( $type = 'names' ) {

			$return = array();

			if ( $type === 'names' ) {
				// Names
				$return = array(

					'post_type_name' =>	 $this->post_type, // Carefull...this must be filled with custom post type's slug
					'slug' 			 =>	 $this->post_type, 
					'menu_item_name' =>	 sprintf( esc_html_x('%s', 'Post type menu item', 'plethora-framework'), ucfirst( $this->post_type_plural ) ),
				    'singular' 		 =>  sprintf( esc_html_x('%s', 'Post type singular label', 'plethora-framework'), ucfirst( $this->post_type ) ),
				    'plural' 		 =>  sprintf( esc_html_x('%s', 'Post type plural label', 'plethora-framework'), ucfirst( $this->post_type_plural ) ),

				);
				// Hook to apply
				$return = apply_filters( strtolower( get_class() ) . '_names', $return );
			
			} elseif ( $type === 'options' ) {

				// Options
				$return = array(

					'enter_title_here'    => sprintf( esc_html__('%s Title', 'plethora-framework'), ucfirst( $this->post_type ) ), // Title prompt text 
					'description'         => '',	// A short descriptive summary of what the post type is. 
					'public'              => $this->post_type_public,		// Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
					'exclude_from_search' => $this->post_type_exclude_from_search,		// Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
					'publicly_queryable'  => true,		// Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
					'show_ui'             => true,		// Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
					'show_in_nav_menus'   => true,		// Whether post_type is available for selection in navigation menus ( default: value of public argument )
					'show_in_menu'        => true,		// Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
					'show_in_admin_bar'   => true,		// Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
					'menu_position'       => 5, 			// The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
					'menu_icon'           => $this->post_type_menuicon, // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
					'hierarchical'        => $this->post_type_hierarchical, 		// Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
					'has_archive'         => $this->post_type_has_archive,		// Enables post type archives. Will use $post_type as archive slug by default (default: false)
					'query_var'           => true,		// Sets the query_var key for this post type.  (Default: true - set to $post_type )
					'can_export'          => true, 		// Can this post_type be exported. ( Default: true )
					'supports'            => $this->post_type_supports, // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
					'rewrite'             => ( ( $this->post_type_public ) ? array( 
								  					'slug'			=> sprintf( esc_html_x( '%s', 'Rewrite slug for service post type', 'plethora-framework'), Plethora_Theme::option( THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite', $this->post_type ) ) , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				    								'with_front'=> true, 		// bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
				    								// 'feeds'		=> true, 	// bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
				    								// 'pages'		=> true, 	// bool: Should the permalink structure provide for pagination. Defaults to true 
											 ) : array() ), // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )

				);
				// Hook to apply
				$return = apply_filters( strtolower( get_class() ) . '_names', $return );
			}

			return $return;
		}

		public function get_primary_taxonomy_options() {

			// Taxonomy Labels
			$labels = array(
		        'name'                       => sprintf( esc_html__( '%s Categories', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'singular_name'              => sprintf( esc_html__( '%s Category', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'menu_name'                  => sprintf( esc_html__( '%s Categories', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'all_items'                  => sprintf( esc_html__( 'All %s Categories', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'edit_item'                  => sprintf( esc_html__( 'Edit %s Category', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'view_item'                  => sprintf( esc_html__( 'View %s Category', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'update_item'                => sprintf( esc_html__( 'Update %s Category', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'add_new_item'               => sprintf( esc_html__( 'Add New %s Category', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'new_item_name'              => sprintf( esc_html__( 'New %s Category Name', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'parent_item'                => sprintf( esc_html__( 'Parent %s Category', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'parent_item_colon'          => sprintf( esc_html__( 'Parent %s Category:', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'search_items'               => sprintf( esc_html__( 'Search %s Categories', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'popular_items'              => sprintf( esc_html__( 'Popular %s Categories', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'separate_items_with_commas' => sprintf( esc_html__( 'Seperate %s Categories with commas', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'add_or_remove_items'        => sprintf( esc_html__( 'Add or remove %s Categories', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'choose_from_most_used'      => sprintf( esc_html__( 'Choose from most used %s Categories', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'not_found'                  => sprintf( esc_html__( 'No %s Categories found', 'plethora-framework' ), ucfirst( $this->post_type ) ),
			);

			// Taxonomy options
	        $options = array(
	 
				'labels'            => $labels,
				'public'            => $this->post_type_primary_tax_public, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
				'show_ui'           => true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
				'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
				'show_tagcloud'     => false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
				'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
				'hierarchical'      => true, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
				'query_var'         => true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
				// 'sort'           => true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'           => ( ( $this->post_type_public ) ? array( 
								  		'slug'			=> sprintf( esc_html_x( '%s', 'Rewrite slug for custom taxonomy', 'plethora-framework'), Plethora_Theme::option( THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite-primary-tax', $this->post_type_primary_tax ) ) , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ) : array() ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
	        );

			// Register Service Category Taxonomy
			$options = apply_filters( strtolower( get_class() ) .'_'.$this->post_type_primary_tax.'_options', $options );
			return $options;
		}


		function get_secondary_taxonomy_options() {

			// Taxonomy Labels
			$labels = array(
		        'name'                       => sprintf( esc_html__( '%s Tags', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'singular_name'              => sprintf( esc_html__( '%s Tag', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'menu_name'                  => sprintf( esc_html__( '%s Tags', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'all_items'                  => sprintf( esc_html__( 'All %s Tags', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'edit_item'                  => sprintf( esc_html__( 'Edit %s Tag', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'view_item'                  => sprintf( esc_html__( 'View %s Tag', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'update_item'                => sprintf( esc_html__( 'Update %s Tag', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'add_new_item'               => sprintf( esc_html__( 'Add New %s Tag', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'new_item_name'              => sprintf( esc_html__( 'New %s Tag Name', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'parent_item'                => sprintf( esc_html__( 'Parent %s Tag', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'parent_item_colon'          => sprintf( esc_html__( 'Parent %s Tag:', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'search_items'               => sprintf( esc_html__( 'Search %s Tags', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'popular_items'              => sprintf( esc_html__( 'Popular %s Tags', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'separate_items_with_commas' => sprintf( esc_html__( 'Seperate %s Tags with commas', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'add_or_remove_items'        => sprintf( esc_html__( 'Add or remove %s Tags', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'choose_from_most_used'      => sprintf( esc_html__( 'Choose from most used %s Tags', 'plethora-framework' ), ucfirst( $this->post_type ) ),
		        'not_found'                  => sprintf( esc_html__( 'No %s Tags found', 'plethora-framework' ), ucfirst( $this->post_type ) ),
			);

			// Taxonomy options
	        $options = array(
	 
				'labels'            => $labels,
				'public'            => $this->post_type_secondary_tax_public, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
				'show_ui'           => true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
				'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
				'show_tagcloud'     => true, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
				'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
				'hierarchical'      => false, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
				'query_var'         => true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
				// 'sort'           => true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'           => array( 
								  		'slug'			=> sprintf( esc_html_x( '%s', 'Rewrite slug for custom taxonomy', 'plethora-framework'), Plethora_Theme::option( THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite-secondarytax', $this->post_type_secondary_tax ) ) , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> false,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
	        );

			// Register Service Tag Taxonomy
			$options = apply_filters( strtolower( get_class() ) .'_'.$this->post_type_secondary_tax.'_options', $options );
			return $options;
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
						'title'      => sprintf( esc_html__('Single %s Post', 'plethora-framework'), ucfirst( $this->post_type ) ),
						'heading'    => sprintf( esc_html__('SINGLE %s POST VIEW OPTIONS', 'plethora-framework'), strtoupper( $this->post_type ) ),
						'desc'       => sprintf( esc_html__('These will be the default values for a new %s post you create. You have the possbility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework'), ucfirst( $this->post_type ) ),
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

			$primary_tax_obj   = get_taxonomy( Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-info-primarytax-slug' , $this->post_type_primary_tax ) );
			$secondary_tax_obj = get_taxonomy( Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type .'-info-secondarytax-slug' , $this->post_type_secondary_tax ) );

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
						'required' => array(METAOPTION_PREFIX .$this->post_type .'-layout','equals',array('right_sidebar','left_sidebar')),  
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
						'title' => sprintf( esc_html__('%s Tax Label(s)', 'plethora-framework'), $primary_tax_obj->labels->singular_name ), 
						'desc'  => sprintf( esc_html__('In case you need a different taxonomy for these labels, check the %1$sTheme Options > Content > Single %3$s Post > Advanced > Primary Tax Labels Taxonomy%2$s option', 'plethora-framework'), '<strong>', '</strong>', ucfirst( $this->post_type ) ),
						
			);
		    $single_options['info-secondarytax'] = array(
						'id'    => METAOPTION_PREFIX . $this->post_type .'-info-secondarytax',
						'type'  => 'switch', 
						'title' => sprintf( esc_html__('%s Tax Label(s)', 'plethora-framework'), $secondary_tax_obj->labels->singular_name ), 
						'desc'  => sprintf( esc_html__('In case you need a different taxonomy for these labels, check the %1$sTheme Options > Content > Single %3$s Post > Advanced > Secondary Tax Labels Taxonomy%2$s option', 'plethora-framework'), '<strong>', '</strong>', ucfirst( $this->post_type ) ),

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

		# Photos section options
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
		    // Only for Hotel Xenia design
		    $single_options['overlay-title'] = array(
						'id'      => METAOPTION_PREFIX .$this->post_type .'-overlay-title',
						'type'    => 'button_set', 
						'title'   => esc_html__('Overlay Title', 'plethora-framework'),
						'desc'    => esc_html__('Set overlay title section display. If you choose Service Category, it will display the first category you entered on this post.', 'plethora-framework'),
						'options' => array(
										0        => esc_html__('Do Not Display', 'plethora-framework'),
										1        => esc_html__('Display Service Category', 'plethora-framework'),
										'custom' => esc_html__('Display Custom', 'plethora-framework'),
									),
		                );

		    // Only for Hotel Xenia design
		    $single_options['overlay-title-text'] = array(
						'id'       => METAOPTION_PREFIX .$this->post_type .'-overlay-title-text',
						'required' => array( METAOPTION_PREFIX .$this->post_type .'-overlay-title','equals',array( 'custom' )),  
						'type'     => 'text',
						'title'    => esc_html__('Custom Overlay Title', 'plethora-framework'), 
						'translate' => true,
			);

		# Testimonials section options
		    $single_options['testimonials-status'] = array(
						'id'      => METAOPTION_PREFIX .$this->post_type .'-testimonials-status',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Testimonials', 'plethora-framework'),
						'desc'    => esc_html__('Whether to display or not the testimonials list', 'plethora-framework'),
		                );

		# Advanced section options
		    $single_options['info-primarytax-slug'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-info-primarytax-slug',
						'type'     => 'select', 
						'title'    => esc_html__('Primary Tax Labels Taxonomy ', 'plethora-framework'),
						'subtitle' => sprintf( esc_html__('Default: / %s', 'plethora-framework'), $this->post_type_primary_tax ), 
						'desc'     => sprintf( esc_html__('%1$s is set as the primary tax labels taxonomy. Change this only in case you need to display different primary taxonomy labels associated with %2$s post type. Naturally, non associated taxonomies with the %2$s post type will not be displayed.', 'plethora-framework'), '<strong>'. $primary_tax_obj->labels->singular_name .'</strong>', '<strong>'. $this->post_type .'</strong>' ),
						'data'     => 'taxonomies',
			);
		    $single_options['info-secondarytax-slug'] = array(
						'id'       => METAOPTION_PREFIX . $this->post_type .'-info-secondarytax-slug',
						'type'     => 'select', 
						'title'    => esc_html__('Secondary Tax Labels Taxonomy ', 'plethora-framework'),
						'subtitle' => sprintf( esc_html__('Default: / %s', 'plethora-framework'), $this->post_type_secondary_tax ), 
						'desc'     => sprintf( esc_html__('%1$s is set as the secondary tax labels taxonomy. Change this only in case you need to display different secondary taxonomy labels associated with %2$s post type. Naturally, non associated taxonomies with the %2$s post type will not be displayed.', 'plethora-framework'), '<strong>'. $secondary_tax_obj->labels->singular_name .'</strong>', '<strong>'. $this->post_type .'</strong>' ),
						'data'     => 'taxonomies',
			);

		    if ( $this->post_type_public ){
		    $single_options['urlrewrite'] = array(
						'id'               => THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite',
						'type'             => 'text',
						'title'            => sprintf( esc_html__('Post Type URL Rewrite for %s', 'plethora-framework'), $this->post_type ), 
						'desc'             => sprintf( esc_html__('Specify a custom permalink for %1$s post type ( i.e.: http://yoursite.com/%1$s/sample-%1$s). NOTICE: Updating this will probably result a 404 page error on every %1$s post. This can be easily fixed with a simple click on "Save Changes" button, on the "Settings > Permalinks" screen', 'plethora-framework'), $this->post_type ),
						'validate'         => 'unique_slug',
						'flush_permalinks' => true,
		                );
			}
		    if ( $this->post_type_primary_tax_public ){
			    $single_options['urlrewrite-primarytax'] = array(
							'id'               => THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite-primarytax',
							'type'             => 'text',
							'title'            => sprintf( esc_html__('Taxonomy URL Rewrite for %s ', 'plethora-framework'), $this->post_type_primary_tax ), 
							'subtitle'         => sprintf( esc_html__('Default: %s', 'plethora-framework'), $this->post_type_primary_tax ), 
							'desc'             => sprintf( esc_html__('Specify a custom permalink for %1$s taxonomy ( i.e.: http://yoursite.com/%1$s/). NOTICE: This works only with taxonomies that are associating by default with this post type. Updating this will probably result a 404 page error on every %1$s taxonomy view. This can be easily fixed with a simple click on "Save Changes" button, on the "Settings > Permalinks" screen', 'plethora-framework'), $this->post_type_primary_tax ),
							'validate'         => 'unique_slug',
							'flush_permalinks' => true,
			                );
			}
		    if ( $this->post_type_secondary_tax_public  ){
			    $single_options['urlrewrite-secondarytax'] = array(
							'id'               => THEMEOPTION_PREFIX .$this->post_type .'-urlrewrite-secondarytax',
							'type'             => 'text',
							'title'            => sprintf( esc_html__('Taxonomy URL Rewrite for %s', 'plethora-framework'), $this->post_type_secondary_tax ), 
							'subtitle'         => sprintf( esc_html__('Default: %s', 'plethora-framework'), $this->post_type_secondary_tax ), 
							'desc'             => sprintf( esc_html__('Specify a custom permalink for %1$s taxonomy ( i.e.: http://yoursite.com/%1$s/). NOTICE: This works only with taxonomies that are associating by default with this post type. Updating this will probably result a 404 page error on every %1$s taxonomy view. This can be easily fixed with a simple click on "Save Changes" button, on the "Settings > Permalinks" screen', 'plethora-framework'), $this->post_type_secondary_tax ),
							'validate'         => 'unique_slug',
							'flush_permalinks' => true,
			                );
			}
			return $single_options;
        }

	    /** 
	    * Single view options_config for theme options and metabox panels ( use only on extension class )
	    */
	    public function single_options_config( $section = 'all' ) {

	    	return array();
	    }
	}
}	
