<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Team Posttype Feature Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Profile') ) {  
 
	/**
	 * @package Plethora Base
	 */

	class Plethora_Posttype_Profile {

		// Plethora Index variables
		public static $feature_title         = "Profile Post Type";	        				// FEATURE DISPLAY TITLE
		public static $feature_description   = "Contains all profile CPT configuration"; 	// FEATURE DISPLAY DESCRIPTION 
		public static $theme_option_control  = true;        								// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
		public static $theme_option_default  = true;        								// DEFAULT ACTIVATION OPTION STATUS 
		public static $theme_option_requires = array();        								// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;        								// DYNAMIC CLASS CONSTRUCTION ? 
		public static $dynamic_method        = false;        								// Additional method invocation ( string/boolean | method name or false )

		// Auxilliary variables
		private $post_type_slug = 'profile';

		// Default option values ( for easy ext class overrides )

		public function __construct() {

			// Create basic post type object
			$posttype_obj = $this->register_post_type();

			// Add taxonomies to object
			$posttype_obj = $this->add_taxonomies( $posttype_obj );

			// Make client and type columns sortable
			$posttype_obj->sortable( array( 'group' => array( 'group', true ) ) );

			if ( is_admin() ) {
				// Single Portfolio Theme Options
				add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 150);
				// Single profile Metabox		
				add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));		

				/*** COLUMNS SETTINGS ***/
				// THUMBNAIL COLUMN IN POSTS SCREEN
				add_filter('manage_edit-profile_columns', array( $this, 'add_column_image_header'));
				add_action('manage_profile_posts_custom_column', array( $this, 'add_column_image_content') , 10, 2 );
			}	
		}

		public function register_post_type() {

			// Get user defined url rewrite option
			$rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'profile-urlrewrite', $this->post_type_slug );

			// Names
			$names = array(

				'post_type_name'  =>  $this->post_type_slug, // Carefull...this must be filled with custom post type's slug
				'slug'            =>  $this->post_type_slug, 
				'menu_item_name'  =>  esc_html__('Profiles', 'plethora-framework'),
				'singular'        =>  esc_html__('Profile', 'plethora-framework'),
				'plural'          =>  esc_html__('Profiles', 'plethora-framework'),

			);

			// Options
			$options = array(

				'enter_title_here' => 'Profile\'s Name', // Title prompt text 
				'description'         => '',  // A short descriptive summary of what the post type is. 
				'public'              => true,    		// Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
				'exclude_from_search' => true,    		// Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
				'publicly_queryable'  => true,    		// Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
				'show_ui'             => true,    		// Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
				'show_in_nav_menus'   => true,    		// Whether post_type is available for selection in navigation menus ( default: value of public argument )
				'show_in_menu'        => true,    		// Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
				'show_in_admin_bar'   => true,    		// Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
				'menu_position'       => 5,       		// The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
				'menu_icon'           => 'dashicons-id-alt', // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
				'hierarchical'        => false,    		// Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
				// 'taxonomies'          => array(),   	// An array of registered taxonomies like category or post_tag that will be used with this post type. This can be used in lieu of calling register_taxonomy_for_object_type() directly. Custom taxonomies still need to be registered with register_taxonomy(). 
				// 'has_archive'         => false,    	// Enables post type archives. Will use $post_type as archive slug by default (default: false)
				// 'query_var'           => true,    	// Sets the query_var key for this post type.  (Default: true - set to $post_type )
				// 'can_export'          => true     	// Can this post_type be exported. ( Default: true )
				'supports'			  => array(			// An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
											'title', 
											'editor', 
											// 'author',  
											'thumbnail',  
											'excerpt',  
											// 'trackbacks',  
											// 'custom-fields',   
											// 'comments',   
											'revisions',   
											'page-attributes',  
											// 'post-formats'   
						                 ), 					
				'rewrite'		  => array( 		// Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )
											'slug'			=> sanitize_key( $rewrite ),		// string: Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable, that's why we use _x
											'with_front'	=> true,	// bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
											'feeds'    	=> false,	// bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
											// 'pages'    	=> true,	// bool: Should the permalink structure provide for pagination. Defaults to true 
											), 				
			);    


			// Create the post type object
			$names 		= apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_names', $names );
			$options 	= apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_options', $options );
			$posttype_obj = new Plethora_Posttype( $names, $options );

			return $posttype_obj;
		}

		function add_taxonomies( $posttype_obj ) {

			// Taxonomy labels
			$labels = array(

		        'name'                       => esc_html__( 'Groups', 'plethora-framework' ),
		        'singular_name'              => esc_html__( 'Group', 'plethora-framework' ),
		        'menu_name'                  => esc_html__( 'Groups', 'plethora-framework' ),
		        'all_items'                  => esc_html__( 'All Groups', 'plethora-framework' ),
		        'edit_item'                  => esc_html__( 'Edit Group', 'plethora-framework' ),
		        'view_item'                  => esc_html__( 'View Group', 'plethora-framework' ),
		        'update_item'                => esc_html__( 'Update Group', 'plethora-framework' ),
		        'add_new_item'               => esc_html__( 'Add New Group', 'plethora-framework' ),
		        'new_item_name'              => esc_html__( 'New Group Name', 'plethora-framework' ),
		        'parent_item'                => esc_html__( 'Parent Group', 'plethora-framework' ),
		        'parent_item_colon'          => esc_html__( 'Parent Group:', 'plethora-framework' ),
		        'search_items'               => esc_html__( 'Search Groups', 'plethora-framework' ),     
		        'popular_items'              => esc_html__( 'Popular Groups', 'plethora-framework' ),
		        'separate_items_with_commas' => esc_html__( 'Separate Groups with commas', 'plethora-framework' ),
		        'add_or_remove_items'        => esc_html__( 'Add or remove Groups', 'plethora-framework' ),
		        'choose_from_most_used'      => esc_html__( 'Choose from most used Groups', 'plethora-framework' ),
		        'not_found'                  => esc_html__( 'No Groups found', 'plethora-framework' ),

			);

			// Taxonomy options
	        $options = array(
	 
	            'labels' 			=> $labels,
	            'public' 			=> true, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
	            'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
	            'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
	            'show_tagcloud' 	=> false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
	            'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
	            'hierarchical' 		=> true, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
	            'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
	            'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> esc_html__('group','plethora-framework') , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )

	        );
			
			// Register the taxonomy
			$posttype_obj->register_taxonomy( 'group', $options );

			return $posttype_obj;
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
						'title'      => esc_html__('Single Profile', 'plethora-framework'),
						'heading'    => esc_html__('SINGLE PROFILE VIEW OPTIONS', 'plethora-framework'),
						'desc'       => esc_html__('These will be the default values for a new profile post you create. You have the possibility to override most of these settings on each single post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
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
			if ( has_filter( 'plethora_metabox_singleprofile') ) {

				$sections = apply_filters( 'plethora_metabox_singleprofile', $sections );
			}
		    $metaboxes[] = array(
		        'id'            => 'metabox-single-profile',
		        'title'         => esc_html__( 'Page Options', 'plethora-framework' ),
		        'post_types'    => array( 'profile'),
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
						'id'      =>  METAOPTION_PREFIX .'profile-layout',
						'title'   => esc_html__( 'Select Layout', 'plethora-framework' ),
						'type'    => 'image_select',
						'options' => Plethora_Module_Style::get_options_array( array( 
																					'type'   => 'page_layouts',
																					'use_in' => 'redux',
																			   )
									 ),
			);

		    $single_options['sidebar'] = array(
						'id'       => METAOPTION_PREFIX .'profile-sidebar',
						'type'     => 'select',
						'required' => array( METAOPTION_PREFIX .'profile-layout','equals',array('right_sidebar','left_sidebar')),  
						'data'     => 'sidebars',
						'multi'    => false,
						'title'    => esc_html__('Select Sidebar', 'plethora-framework'), 
			);

		    $single_options['colorset'] = array(
						'id'      => METAOPTION_PREFIX .'profile-colorset',
						'type'    => 'button_set',
						'title'   => esc_html__( 'Content Section Color Set', 'plethora-framework' ),
						'desc'    => esc_html__( 'Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
						'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																					  'use_in'          => 'redux',
																					  'prepend_options' => array(  'foo' => __('Default', 'plethora-framework') ) ) ),
			);

		    $single_options['layout-content'] = array(
		                'id'        =>  METAOPTION_PREFIX .'profile-layout-content',
		                'title'     => esc_html__( 'Main Content Layout', 'plethora-framework' ),
		                'type'      => 'image_select',
		                'options'   => array( 
						                'default'	=> PLE_CORE_ASSETS_URI . '/images/redux/profile_layout.png',
						                'invert'	=> PLE_CORE_ASSETS_URI . '/images/redux/profile_layout_invert.png',
		                			   )
			);

		    $single_options['featured'] = array(
							'id'    => METAOPTION_PREFIX . 'profile-featured',
							'type'  => 'switch', 
							'title' => esc_html__('Featured Profile', 'plethora-framework'),
							'desc'  => esc_html__('Setting this profile post as featured, will give it special treatment on several shortcode displays ( i.e. Profiles Loop shortcode ).', 'plethora-framework'),
			);

		    $single_options['title'] = array(
						'id'      => METAOPTION_PREFIX .'profile-title',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Title', 'plethora-framework'),
						'desc'    => esc_html__('Enable/disable titles section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
			);

		    $single_options['subtitle'] = array(
						'id'      => METAOPTION_PREFIX .'profile-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Subtitle', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
			);

		    $single_options['subtitle-text'] = array(
						'id'       => METAOPTION_PREFIX .'profile-subtitle-text',
						'required' => array( METAOPTION_PREFIX .'profile-subtitle','equals',array( 1 )),  
						'type'     => 'text',
						'title'    => esc_html__('Subtitle', 'plethora-framework'), 
						'subtitle' => esc_html__('Job title or any other role title', 'plethora-framework'),
						'translate' => true,
			);

		    $single_options['media-stretch'] = array(
						'id'      => METAOPTION_PREFIX .'profile-media-stretch',
						'type'    => 'button_set', 
						'title'   => esc_html__('Media Display Ratio', 'plethora-framework'),
						'options' => Plethora_Module_Style::get_options_array( array( 
	                                        'type' => 'stretchy_ratios',
	                                        'prepend_options' => array( 'foo_stretch' => esc_html__( 'Native Ratio', 'plethora-framework' ) ),
	                                        )),            
			);

		    $single_options['user'] = array(
						'id'          => METAOPTION_PREFIX .'profile-user',
						'type'        => 'select',
						'title'       => esc_html__('User Profile', 'plethora-framework'), 
						'desc'        => esc_html__('Pair this profile with a specific user ( subscribers not included ). This will allow you to display user generated content', 'plethora-framework'),
						'options'     => self::get_author_users(),
						'placeholder' => esc_html__('Select user to associate.', 'plethora-framework'),
			);

		    $single_options['authorposts'] = array(
						'id'      => METAOPTION_PREFIX .'profile-authorposts',
						'type'    => 'switch', 
						'title'   => esc_html__('Author Posts', 'plethora-framework'),
						'desc'    => esc_html__('Will enable author posts display on profile page', 'plethora-framework'),
			);

		    $single_options['authorposts-heading'] = array(
						'id'        => METAOPTION_PREFIX .'profile-authorposts-heading',
						'required'  => array(METAOPTION_PREFIX .'profile-authorposts','=','1'),						
						'type'      => 'text',
						'title'     => esc_html__('Author Posts Heading', 'plethora-framework'), 
						'translate' => true,
			);

		    $single_options['authorposts-num'] = array(
						'id'            => METAOPTION_PREFIX .'profile-authorposts-num',
						'required'      => array(METAOPTION_PREFIX .'profile-authorposts','=','1'),						
						'type'          => 'slider',
						'title'         => esc_html__('Author Posts Results', 'plethora-framework'), 
						'desc'          => esc_html__('How many author post to display on presentation. Leave 0 to display all.', 'plethora-framework'), 
						"min"           => 0,
						"step"          => 1,
						"max"           => 50,
						'display_value' => 'text'
			);

		    $single_options['quote'] = array(
						'id'=> METAOPTION_PREFIX .'profile-quote',
						'type' => 'text',
						'title' => esc_html__('Featured quote', 'plethora-framework'),
						'validate' => 'no_special_chars',
						'translate' => true,
			);

		    $single_options['social'] = array(
			            'id'         =>  METAOPTION_PREFIX .'profile-social',
			            'type'       => 'repeater',
			            'title'      => esc_html__( 'Profiles Social Icons', 'plethora-framework' ),
						'subtitle'    => esc_html__('Add default social icons for profiles. Those will be the social fields that have to be filled for each person presentation', 'plethora-framework'),
			            'group_values' => true, // Group all fields below within the repeater ID
			            'item_name' => 'social icon', // Add a repeater block name to the Add and Delete buttons
			            // 'bind_title' => 'sidebar', // Bind the repeater block title to this field ID
			            //'static'     => 2, // Set the number of repeater blocks to be output
			            //'limit' => 2, // Limit the number of repeater blocks a user can create
			            'sortable' => true, // Allow the users to sort the repeater blocks or not
			            'fields'     => array(
			                array(
								'id'          => 'social_title',
								'type'        => 'text',
								'title'       => esc_html__( 'Title', 'plethora-framework' ),
								'placeholder' => esc_html__( 'Icon title', 'plethora-framework' ),
			                ),
			                array(
								'id'    => 'social_icon',
								'type'  => 'icons',
								'title' => esc_html__( 'Icon', 'plethora-framework' ),
								'options' => Plethora_Module_Icons_Ext::get_options_array(),
			                ),
			                array(
								'id'    => 'social_url',
								'type'        => 'text',
								'title'       => esc_html__( 'URL', 'plethora-framework' ),
								'placeholder' => esc_html__( 'HTTP address or mailto/callto actions', 'plethora-framework' ),
			                ),
			            ),
			);

		    $single_options['urlrewrite'] = array(
						'id'       => METAOPTION_PREFIX .'profile-urlrewrite',
						'type'     => 'text',
						'title'    => esc_html__('URL Rewrite', 'plethora-framework'), 
						'desc'     => esc_html__('Specify a custom permalink for profile posts ( i.e.: http://yoursite.com/<b>profile</b>/sample-profile). <br><span style="color:red">NOTICE: Updating this will probably result a 404 page error on every profile post. This can be easily fixed with a simple click on "Save Changes" button, on the <b>Settings > Permalinks</b> screen</span>', 'plethora-framework'),
						'validate'  => 'unique_slug',
						'flush_permalinks'  => true,
			);

			// Additional fields added on Avoir >>> START
		    $single_options['containertype'] = array(
							'id'      => METAOPTION_PREFIX .'profile-containertype',
							'type'    => 'button_set', 
							'title'   => esc_html__('Container Type', 'plethora-framework'),
							'options' => array(
											'container'       => esc_html__( 'Default', 'plethora-framework'),
											'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
										)
			);
		    $single_options['extraclass'] = array(
							'id'      => METAOPTION_PREFIX .'profile-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Extra Classes', 'plethora-framework'),
							'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
			);
			// Additional fields added on Avoir >>> END

			return $single_options;
        }


	  	public static function get_author_users() {

	  		$authors  = get_users( array( 'who' => 'authors', 'fields' => array('ID', 'display_name') ) );
	  		$return = array();
	  		foreach ( $authors as $key=>$author ) { 
	  			$return[$author->ID] = $author->display_name;
	  		}
	  		return $return;
	  	}
	      /**
	       * Set default icon set 
	       * @since 1.0
	       *
	       */
	    public function default_user_socialicons() {

	        $default_user_socialicons = array();
	        // IMPORTANT: this is necessary for repeater field...add a line for each record
	        $default_user_socialicons['redux_repeater_data'] = array(
                               array( 'title'=> 'twitter' ),
                               array( 'title'=> 'facebook' ),
                               array( 'title'=> 'googleplus' ),
                               array( 'title'=> 'linkedin' ),
                               array( 'title'=> 'instagram' ),
                               array( 'title'=> 'skype' ),
                               array( 'title'=> 'email' )
	                         );
	        $default_user_socialicons['social_title'] = array(
	                            'Twitter',
	                            'Facebook',
	                            'Google+',
	                            'LinkedIn',
	                            'Instagram',
	                            'Skype',
	                            'Send Me An Email',
	                          );
	        $default_user_socialicons['social_icon'] = array(
	                            'fa fa-twitter',
	                            'fa fa-facebook',
	                            'fa fa-google-plus',
	                            'fa fa-linkedin',
	                            'fa fa-instagram',
	                            'fa fa-skype',
	                            'fa fa-envelope',
	                          );
	        $default_user_socialicons['social_url'] = array(
	                            '',
	                            '',
	                            '',
	                            '',
	                            '',
	                            '',
	                            '',
	                          );
	        return $default_user_socialicons;
	    }

		public function add_column_image_header( $columns ) { 

			unset( $columns['date'] );
			$columns['post_thumbs'] = esc_html__( 'Image', 'plethora-framework' );
			return $columns;
		}

		public function add_column_image_content( $column_name, $id ) { 

			if ( $column_name === 'post_thumbs' && has_post_thumbnail()  ){

				$featured_image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');
				if ( !empty($featured_image_src[0])) { 
			    echo "<img width='50px' src='" . $featured_image_src[0] . "'>";
				}
			}
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
							'id'                    => 'colorset', 
							'theme_options'         => true, 
							'theme_options_default' => 'secondary_section',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'layout-content', 
							'theme_options'         => true, 
							'theme_options_default' => 'default',
							'metabox'               => true,
							'metabox_default'       => NULL
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
							'theme_options_default' => 1,
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
							'id'                    => 'media-stretch', 
							'theme_options'         => true, 
							'theme_options_default' => 'stretchy_wrapper ratio_1-1',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'quote', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'user', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'authorposts', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'authorposts-heading', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Latest Posts', 'plethora-framework' ),
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'authorposts-num', 
							'theme_options'         => true, 
							'theme_options_default' => 5,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'social', 
							'theme_options'         => true, 
							'theme_options_default' => $this->default_user_socialicons(),
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'urlrewrite', 
							'theme_options'         => true, 
							'theme_options_default' => 'profile',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
			);

			return $config;
		}
	}
}