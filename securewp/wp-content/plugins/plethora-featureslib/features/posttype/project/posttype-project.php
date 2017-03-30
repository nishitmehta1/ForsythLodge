<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2016

File Description: Project Post Type Feature Class
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Project') ) {  
 
	/**
	 * @package Plethora Framework
	 */

	class Plethora_Posttype_Project {

		// Plethora Index variables
		public static $feature_title         = "Project/Portfolio Post Type";		// Feature display title  (string)
		public static $feature_description   = "Contains all project related post configuration";		// Feature display description (string)
		public static $theme_option_control  = true;		// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;		// Default activation option status ( boolean )
		public static $theme_option_requires = array();	// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;		// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;	// Additional method invocation ( string/boolean | method name or false )

		// Auxilliary variables
		private $post_type_slug = 'project';
	
		public function __construct() {

			// Create basic post type object
			$posttype_obj = $this->register_post_type();

			// Add taxonomies to object
			$posttype_obj = $this->add_taxonomies( $posttype_obj );

			// Make client and type columns sortable
			$posttype_obj->sortable(array( 'project-cat' => array('project_type', true) ));

			if ( is_admin() ) {
				// Project Category terms meta configuration	
				add_action( 'admin_init', array( $this, 'taxonomy_terms_options' ) );

				// Single Portfolio Theme Options
				add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 130);

				// Single Portfolio Metabox		
				add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));
			}	
		}

		public function register_post_type() {

			// Get user defined url rewrite option
			$rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'project-urlrewrite', $this->post_type_slug );
			// Names
			$names = array(

				'post_type_name' =>	 $this->post_type_slug, // Carefull...this must be filled with custom post type's slug
				'slug' 			 =>	 $this->post_type_slug, 
				'menu_item_name' =>	 esc_html__('Projects', 'plethora-framework'),
			    'singular' 		 =>  esc_html__('Project', 'plethora-framework'),
			    'plural' 		 =>  esc_html__('Projects', 'plethora-framework'),

			);

			// Options
			$options = array(

	            'enter_title_here' 		=> 'Project title', // Title prompt text 
				'description'			=> '',	// A short descriptive summary of what the post type is. 
				'public'				=> true,		// Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
				'exclude_from_search'	=> false,		// Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
				'publicly_queryable'	=> true,		// Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
				'show_ui' 			  	=> true,		// Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
				'show_in_nav_menus'		=> true,		// Whether post_type is available for selection in navigation menus ( default: value of public argument )
				'show_in_menu'			=> true,		// Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
				'show_in_admin_bar'		=> true,		// Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
				'menu_position'			=> 5, 			// The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
				'menu_icon' 			=> 'dashicons-portfolio', // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
				'hierarchical' 		  	=> false, 		// Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
	 			'has_archive' 		  	=> true,		// Enables post type archives. Will use $post_type as archive slug by default (default: false)
				'query_var' 		  	=> true,		// Sets the query_var key for this post type.  (Default: true - set to $post_type )
	 			'can_export' 		  	=> true, 		// Can this post_type be exported. ( Default: true )
		    	'supports' 				=> array( 
						    					'title', 
						    					'editor', 
						    					// 'author', 	
						    					'thumbnail', 	
						    					'excerpt', 	
						    					'trackbacks', 	
						    					'custom-fields', 	
						    					// 'comments', 	
						    					'revisions', 	
						    					// 'page-attributes', 	
						    					// 'post-formats' 	
						    				 ), // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
			    'rewrite' 			  	=> array( 
			    								'slug'		=> sanitize_key( $rewrite ), // string: Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable, that's why we use _x
			    								'with_front'=> true, 		// bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
			    								// 'feeds'		=> true, 	// bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
			    								// 'pages'		=> true, 	// bool: Should the permalink structure provide for pagination. Defaults to true 
			    							 ), // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )

			);

			// Hooks to apply
			$names 		= apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_names', $names );
			$options 	= apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_options', $options );
			// Create the post type
			$portfolio = new Plethora_Posttype( $names, $options );

			return $portfolio;
		}

		function add_taxonomies( $portfolio ) {

			// Taxonomy labels
			$labels = array(

		        'name'                       => esc_html__( 'Project Categories', 'plethora-framework' ),
		        'singular_name'              => esc_html__( 'Project Category', 'plethora-framework' ),
		        'menu_name'                  => esc_html__( 'Project Categories', 'plethora-framework' ),
		        'all_items'                  => esc_html__( 'All Project Categories', 'plethora-framework' ),
		        'edit_item'                  => esc_html__( 'Edit Project Category', 'plethora-framework' ),
		        'view_item'                  => esc_html__( 'View Project Category', 'plethora-framework' ),
		        'update_item'                => esc_html__( 'Update Project Category', 'plethora-framework' ),
		        'add_new_item'               => esc_html__( 'Add New Project Category', 'plethora-framework' ),
		        'new_item_name'              => esc_html__( 'New Project Category Name', 'plethora-framework' ),
		        'parent_item'                => esc_html__( 'Parent Project Category', 'plethora-framework' ),
		        'parent_item_colon'          => esc_html__( 'Parent Project Category:', 'plethora-framework' ),
		        'search_items'               => esc_html__( 'Search Project Categories', 'plethora-framework' ),     
		        'popular_items'              => esc_html__( 'Popular Project Categories', 'plethora-framework' ),
		        'separate_items_with_commas' => esc_html__( 'Seperate Project Categories with commas', 'plethora-framework' ),
		        'add_or_remove_items'        => esc_html__( 'Add or remove Project Categories', 'plethora-framework' ),
		        'choose_from_most_used'      => esc_html__( 'Choose from most used Project Categories', 'plethora-framework' ),
		        'not_found'                  => esc_html__( 'No Project Categories found', 'plethora-framework' ),

			);

			// Taxonomy options
	        $options = array(
	 
	            'labels' => $labels,
	            'public' 			=> true, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
	            'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
	            'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
	            'show_tagcloud' 	=> false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
	            'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
	            'hierarchical' 		=> true, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
	            'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
	            // 'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> esc_html__('project-cat','plethora-framework') , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
	        );

			// Register Taxonomy
			$options 	= apply_filters( 'plethora_posttype_taxonomy_project-cat_options', $options );
			$portfolio->register_taxonomy('project-cat', $options );

			return $portfolio;
		}


		/** 
		* Returns theme options configuration. Collects global and theme-specific fields
		* Hooked @ 'plethora_themeoptions_content'
		*/
        public function single_themeoptions( $sections ) {

        	// setup theme options according to configuration
			$opts        = $this->single_options();
			$opts_config = $this->single_options_config();
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
						'title'      => esc_html__('Single Project', 'plethora-framework'),
						'heading'    => esc_html__('SINGLE PROJECT VIEW OPTIONS', 'plethora-framework'),
						'desc'       => esc_html__('These will be the default values for a new project post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
						'subsection' => true,
						'fields'     => $fields
				);
			}
			return $sections;
        }

		/** 
		* Returns single options configuration. Collects global and theme-specific fields
		* Hooked @ 'plethora_metabox_add'
		*/
        public function single_metabox( $metaboxes ) {

        	// setup theme options according to configuration
			$opts        = $this->single_options();
			$opts_config = $this->single_options_config();
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

			$sections_content = array(
				'title'      => esc_html__('Content', 'plethora-framework'),
				'heading'    => esc_html__('CONTENT OPTIONS', 'plethora-framework'),
				'icon_class' => 'icon-large',
				'icon'       => 'el-icon-lines',
				'fields'     => $fields
			);

			$sections = array();
			$sections[] = $sections_content;


			// This filter is used to hook additional option sections...LEAVE IT THERE!
			if ( has_filter( 'plethora_metabox_singleproject') ) {

				$sections = apply_filters( 'plethora_metabox_singleproject', $sections );
			}
		    $metaboxes[] = array(
		        'id'            => 'metabox-single-project',
		        'title'         => esc_html__( 'Page Options', 'plethora-framework' ),
		        'post_types'    => array( 'project'),
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

		    $single_options['layout'] = array(
						'id'      =>  METAOPTION_PREFIX .'project-layout',
						'title'   => esc_html__( 'Select Layout', 'plethora-framework' ),
						'type'    => 'image_select',
						'options' => Plethora_Module_Style::get_options_array( array( 
																					'type'   => 'page_layouts',
																					'use_in' => 'redux',
																			   )
									 ),
		                );
		    $single_options['sidebar'] = array(
						'id'       => METAOPTION_PREFIX .'project-sidebar',
						'type'     => 'select',
						'required' => array(METAOPTION_PREFIX .'project-layout','equals',array('right_sidebar','left_sidebar')),  
						'data'     => 'sidebars',
						'multi'    => false,
						'title'    => esc_html__('Select Sidebar', 'plethora-framework'), 
		                );

		    $single_options['containertype'] = array(
						'id'      => METAOPTION_PREFIX .'project-containertype',
						'type'    => 'button_set', 
						'title'   => esc_html__('Container Type', 'plethora-framework'),
						'options' => array(
										'container'       => esc_html__( 'Default', 'plethora-framework'),
										'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
							)
		                );

		    $single_options['colorset'] = array(
						'id'      => METAOPTION_PREFIX .'project-colorset',
						'type'    => 'button_set',
						'title'   => esc_html__( 'Content Section Color Set', 'plethora-framework' ),
						'desc'    => esc_html__( 'Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
						'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																					  'use_in'          => 'redux',
																					  'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
		                );

		    $single_options['content-align'] = array(
							'id'      => METAOPTION_PREFIX .'project-contentalign',
							'type'    => 'button_set', 
							'title'   => esc_html__('Content Section Align', 'plethora-framework'),
							'desc'    => esc_html__('Affects all content section text alignment, except editor text.', 'plethora-framework'),
							'options' => array(
											''            => esc_html__( 'Left', 'plethora-framework'),
											'text-center' => esc_html__( 'Center', 'plethora-framework'),
											'text-right'  => esc_html__( 'Right', 'plethora-framework'),
										 )
			);

		    $single_options['featured'] = array(
							'id'    => METAOPTION_PREFIX . 'project-featured',
							'type'  => 'switch', 
							'title' => esc_html__('Featured Project', 'plethora-framework'),
							'desc'  => esc_html__('Setting this project post as featured, will give it special treatment on several shortcode displays ( i.e. Projects Loop shortcode ).', 'plethora-framework'),
			);

		    $single_options['title'] = array(
						'id'      => METAOPTION_PREFIX .'project-title',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Title', 'plethora-framework'),
						'desc'    => esc_html__('Enable/disable titles section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
		                );
		    $single_options['subtitle'] = array(
						'id'      => METAOPTION_PREFIX .'project-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Subtitle', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
		                );

		    $single_options['subtitle-text'] = array(
						'id'       => METAOPTION_PREFIX .'project-subtitle-text',
						'required' => array( METAOPTION_PREFIX .'project-subtitle','equals',array( 1 )),  
						'type'     => 'text',
						'title'    => esc_html__('Subtitle', 'plethora-framework'), 
						'translate' => true,
			);

		    $single_options['mediadisplay'] = array(
						'id'      => METAOPTION_PREFIX . 'project-mediadisplay',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Feautured Media', 'plethora-framework'),
		                );

		    $single_options['media-stretch'] = array(
						'id'      => METAOPTION_PREFIX . 'project-media-stretch',
						'type'    => 'button_set', 
						'title'   => esc_html__('Media Display Ratio', 'plethora-framework'),
						'desc'    => esc_html__('Will be applied on single AND listing view', 'plethora-framework'),
						'options' => Plethora_Module_Style::get_options_array( array( 
	                                        'type' => 'stretchy_ratios',
	                                        'prepend_options' => array( 'foo_stretch' => esc_html__('Native Ratio', 'plethora-framework' ) ),
	                                        )),            
		                );
		    // only for CPTs
		    $single_options['info-primarytax'] = array(
						'id'    => METAOPTION_PREFIX . 'project-info-primarytax',
						'type'  => 'switch', 
						'title' => esc_html__('Display Primary Taxonomy Info', 'plethora-framework'), 

			);
		    $single_options['info-primarytax-slug'] = array(
						'id'       => METAOPTION_PREFIX . 'project-info-primarytax-slug',
						'required' => array( METAOPTION_PREFIX . 'project-info-primarytax','=', 1),						
						'type'     => 'select', 
						'title'    => esc_html__('Set Primary Taxonomy', 'plethora-framework'),
						'desc'     => esc_html__('Project categories is set by default as the primary taxonomy. Use this only in case you need to display a custom taxonomy associated with projects post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
						'data'     => 'taxonomies',
						'args'     => array( 'public' => 1 ),
			);

		    // THIS SHOULD BE ADDED EVEN IF POST TYPE IS NOT ASSOCIATED WITH OTHER TAXONOMY
		    $single_options['info-secondarytax'] = array(
						'id'    => METAOPTION_PREFIX . 'project-info-secondarytax',
						'type'  => 'switch', 
						'title' => esc_html__('Display Secondary Taxonomy Info', 'plethora-framework'), 

			);

		    // THIS SHOULD BE ADDED EVEN IF POST TYPE IS NOT ASSOCIATED WITH OTHER TAXONOMY
		    $single_options['info-secondarytax-slug'] = array(
						'id'       => METAOPTION_PREFIX . 'project-info-secondarytax-slug',
						'required' => array( METAOPTION_PREFIX . 'project-info-secondarytax','=', 1),						
						'type'     => 'select', 
						'title'    => esc_html__('Set Secondary Taxonomy', 'plethora-framework'),
						'desc'     => esc_html__('No taxonomy is set by default as the primary taxonomy. Use this only in case you need to display a custom taxonomy associated with projects post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
						'data'     => 'taxonomies',
						'args'     => array( 'public' => 1 ),
			);

		    $single_options['date'] = array(
						'id'      => METAOPTION_PREFIX . 'project-date',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Date Info', 'plethora-framework'),
		                );

		    $single_options['urlrewrite'] = array(
						'id'               => THEMEOPTION_PREFIX .'project-urlrewrite',
						'type'             => 'text',
						'title'            => esc_html__('URL Rewrite', 'plethora-framework'), 
						'desc'             => esc_html__('Specify a custom permalink for project posts ( i.e.: http://yoursite.com/portfolio/sample-portfolio). NOTICE: Updating this will probably result a 404 page error on every portfolio post. This can be easily fixed with a simple click on "Save Changes" button, on the "Settings > Permalinks" screen', 'plethora-framework'),
						'validate'         => 'unique_slug',
						'flush_permalinks' => true,
		                );

		    $single_options['excerpt'] = array(
						'id'      => METAOPTION_PREFIX . 'project-excerpt',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Excerpt', 'plethora-framework'),
		                );

		    $single_options['divider'] = array(
						'id'      => METAOPTION_PREFIX . 'project-divider',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Divider', 'plethora-framework'),
		                );

		    $single_options['extraclass'] = array(
						'id'      => METAOPTION_PREFIX .'project-extraclass',
						'type'    => 'text', 
						'title'   => esc_html__('Extra Classes', 'plethora-framework'),
						'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
		                );
		    $single_options['navigation'] = array(
						'id'      => METAOPTION_PREFIX .'project-navigation',
						'type'    => 'switch', 
						'title'   => esc_html__('Enable Navigation', 'plethora-framework'),
						'desc'    => esc_html__('Enable/disable projects navigation', 'plethora-framework'),
		                );
		    $single_options['navigation-label-previous'] = array(
						'id'        => METAOPTION_PREFIX .'project-navigation-label-previous',
						'type'      => 'text', 
						'title'     => esc_html__('Navigation: Previous Label Text', 'plethora-framework'),
						'translate' => true,
						'required'  => array(
											array( METAOPTION_PREFIX .'project-navigation','=',1),
									  ),
		                );
		    $single_options['navigation-label-next'] = array(
						'id'        => METAOPTION_PREFIX .'project-navigation-label-next',
						'type'      => 'text', 
						'title'     => esc_html__('Navigation: Next Label Text', 'plethora-framework'),
						'translate' => true,
						'required'  => array(
											array( METAOPTION_PREFIX .'project-navigation','=',1),
									  ),
		                );

			return $single_options;
        }

		/** 
		* Single view options_config for theme options and metabox panels
		*/
		public function single_options_config() {

			$config = array(
						array( 
							'id'                    => 'layout', 
							'theme_options'         => true, 
							'theme_options_default' => 'no_sidebar',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'sidebar', 
							'theme_options'         => true, 
							'theme_options_default' => 'sidebar-pages',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'containertype', 
							'theme_options'         => true, 
							'theme_options_default' => 'container',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'colorset', 
							'theme_options'         => true, 
							'theme_options_default' => 'foo',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'content-align', 
							'theme_options'         => true, 
							'theme_options_default' => '',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'featured', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => false
							),
						array( 
							'id'                    => 'title', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 0,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle-text', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => ''
							),
						array( 
							'id'                    => 'mediadisplay', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'media-stretch', 
							'theme_options'         => true, 
							'theme_options_default' => 'stretchy_wrapper ratio_16-9',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-primarytax', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-primarytax-slug', 
							'theme_options'         => true, 
							'theme_options_default' => 'project-cat',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'date', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'excerpt', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'divider', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'navigation', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'navigation-label-previous', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Previous Project', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'navigation-label-next', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Next Project', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),

						array( 
							'id'                    => 'extraclass', 
							'theme_options'         => true, 
							'theme_options_default' => '',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'urlrewrite', 
							'theme_options'         => true, 
							'theme_options_default' => 'project',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
			);

			return $config;
		}


		/** 
		* Terms meta configuration
		* Can use this for extension classes
		*/
		public function taxonomy_terms_options() {

			$taxonomy = 'project-cat';
			$opts = array(
						array(
							'id'               => TERMSMETA_PREFIX . $taxonomy .'-colorset',	// * Unique ID identifying the field. Must be different from all other field IDs.
							'type'             => 'select',      							// * Value identifying the field type.
							'title'            => esc_html__( 'Color Set', 'plethora-framework' ),  // Displays title of the option.
							'desc'             => esc_html__( 'This will be the default color settings for various elements that display Project category terms', 'plethora-framework' ),      // Description of the option, usualy appearing beneath the field control.
							'default'          => 'black_section',      // Value set as default
							'options'          => Plethora_Module_Style_Ext::get_options_array( array( 'type'=> 'color_sets', 'prepend_default' => true ) ),
							'admin_col'        => true,      // Value set as default
							'admin_col_markup' => '<div class="%1$s">%2$s</div>',      // Column markup ( %1$s: value / %2$s: Option title ( if supported by field ), %3$s: field title, %4$s: term name )
						)
			);
			if ( class_exists( 'Plethora_Fields_Termsmeta' ) ) { // avoid backward compatibility issues

				$project_terms = new Plethora_Fields_Termsmeta( $taxonomy, $opts );
			}
		}
	}
}	
