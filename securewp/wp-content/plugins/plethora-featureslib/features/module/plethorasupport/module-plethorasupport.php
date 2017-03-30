<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Plethora Support module ( Plethora Themes in-house customer support module )
Hooks > Filters

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Module') && !class_exists('Plethora_Module_Plethorasupport') ) {  
 
	/**
	 * @package Plethora Framework
	 */

	class Plethora_Module_Plethorasupport {

        // Feature display title  (string)
        public static $feature_title        = "Plethora Support Module";
        // Feature display description (string)
        public static $feature_description  = "Contains all Documentation related taxonomies configuration";
        // Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_control = true;
        // Default activation option status ( boolean )
        public static $theme_option_default = true;
        // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $theme_option_requires= array();
        // Dynamic class construction ? ( boolean )
        public static $dynamic_construct	= true;
        // Additional method invocation ( string/boolean | method name or false )
        public static $dynamic_method		= false;


		public function __construct() {


			/////// Add Module Options To Theme Panel
			add_filter( 'plethora_themeoptions_modules', array( $this, 'theme_options'), 100);

			/////// Add Custom Post Types
			add_action('init', array($this, 'register_cpt_knowledgebase'));	// Knowledgebase CPT
			add_action('init', array($this, 'register_cpt_documentation'));	// Documentation CPT )
			/////// Add Taxonomies
			add_action('init', array($this, 'register_taxonomy_topics'));		// Topics taxonomy ( for Knowledgebase CPT )
			add_action('init', array($this, 'register_taxonomy_chapters'));	// Chapters taxonomy ( for Documentation CPTs )
			add_action('init', array($this, 'register_taxonomy_tags'));		// Tags taxonomy ( for Knowledgebase/Documentation CPTs )

			// Add Customized template for documentation pages only
			add_action('wp', array($this, 'documentation_template'), 1); 

		}


        /**
         * Sets documentation section on theme tabs
         * @return array
         */
		static function theme_options( $sections ) { 

			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Plethora Customer Support', 'plethora-framework'),
				'heading'      => esc_html__('PLETHORA CUSTOMER SUPPORT', 'plethora-framework'),
				// 'desc'       => '<iframe src="http://doc.plethorathemes.com/cleanstart-wp/" style="width:100%; height:auto;"></iframe>' ,
				'fields' 	=> array(
					array(
						'id'    =>'header-cpts',
						'type'  => 'info',
						'title' => '<center>'. esc_html__('KNOWLEDGEBASE', 'plethora-framework') .'</center>',
				        ),
					array(
						'id'    =>'header-doc',
						'type'  => 'info',
						'title' => '<center>'. esc_html__('DOCUMENTATION', 'plethora-framework') .'</center>',
				        ),


				)			
			);

			return $sections;
		}

		/**
		 * Registers 'product' custom post type
		 * @since 1.0
		 */
		public function register_cpt_product() {

			// Slug
			$post_type_slug = 'product';
			// Get user defined CPT configuration
			$url_rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'support-product-urlrewrite', 'product' );
			// Set CPT labels ( will be added to args later )
			$labels = array(

				'name'				=>	esc_html__('Products', 'plethora-framework'),		// general name for the post type, usually plural. The same as, and overridden by $post_type_object->label
				'singular_name'		=>	esc_html__('Product', 'plethora-framework'),		// name for one object of this post type. Defaults to value of 'name'.
				'menu_name' 		=>	esc_html__('Products', 'plethora-framework'),		// the menu name text. This string is the name to give menu items. Defaults to value of 'name'.
			    'name_admin_bar' 	=>  esc_html__('Products', 'plethora-framework'),			// name given for the "Add New" dropdown on admin bar. Defaults to 'singular_name' if it exists, 'name' otherwise.
			    'all_items' 		=>  esc_html__('Products', 'plethora-framework'),		// the all items text used in the menu. Default is the value of 'name'.
			    'add_new' 			=>  esc_html__('Add New Product', 'plethora-framework'), 			// the add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type. Example: _x('Add New', 'product');
			    'add_new_item'		=>  esc_html__('Add New Product', 'plethora-framework'), // the add new item text. Default is Add New Post/Add New Page
			    'edit_item' 		=>  esc_html__('Edit Product', 'plethora-framework'), 	// the edit item text. In the UI, this label is used as the main header on the post's editing panel. Default is "Edit Post" for non-hierarchical and "Edit Page" for hierarchical post types.
			    'new_item' 			=>  esc_html__('New Product', 'plethora-framework'), 	// the new item text. Default is "New Post" for non-hierarchical and "New Page" for hierarchical post types.
			    'view_item' 		=>  esc_html__('View Product', 'plethora-framework'), 	// the view item text. Default is View Post/View Page
			    'search_items' 		=>  esc_html__('Search Products', 'plethora-framework'), // the search items text. Default is Search Posts/Search Pages
			    'not_found' 		=>  esc_html__('No Products Found', 'plethora-framework'), // the not found text. Default is No posts found/No pages found
			    'not_found_in_trash'=>  esc_html__('No Products Found In Trash', 'plethora-framework'), // the not found in trash text. Default is No posts found in Trash/No pages found in Trash.
			    // 'parent_item_colon' =>  esc_html__('Parent Page', 'plethora-framework'), 	// the parent text. This string is used only in hierarchical post types. Default is "Parent Page".
			);
			// Set CPT arguments
			$args = array(

	            'label' 				=>	$post_type_slug, // Labels
	            'labels' 				=>	$labels, // Labels
				'description'			=> esc_html__('Create product presentations', 'plethora-framework'),	// A short descriptive summary of what the post type is. 
				'public'				=> true,		// Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
				'exclude_from_search'	=> true,		// Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
				'publicly_queryable'	=> true,		// Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
				'show_ui' 			  	=> true,		// Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
				'show_in_nav_menus'		=> true,		// Whether post_type is available for selection in navigation menus ( default: value of public argument )
				'show_in_menu'			=> true,		// Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
				'show_in_admin_bar'		=> true,		// Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
				'menu_position'			=> 5, 			// The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
				'menu_icon' 			=> 'dashicons-cart', // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
				'hierarchical' 		  	=> false, 		// Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
				// 'taxonomies' 		  	=> array(),		// An array of registered taxonomies like category or post_tag that will be used with this post type. This can be used in lieu of calling register_taxonomy_for_object_type() directly. Custom taxonomies still need to be registered with register_taxonomy(). 
	 			'has_archive' 		  	=> true,		// Enables post type archives. Will use $post_type as archive slug by default (default: false)
				'query_var' 		  	=> true,		// Sets the query_var key for this post type.  (Default: true - set to $post_type )
	 			'can_export' 		  	=> true, 		// Can this post_type be exported. ( Default: true )
		    	'supports' 				=> array( 
						    					'title', 
						    					'editor', 
						    					'author', 	
						    					'thumbnail', 	
						    					'excerpt', 	
						    					// 'trackbacks', 	
						    					// 'custom-fields', 	
						    					// 'comments', 	
						    					'revisions', 	
						    					// 'page-attributes', 	
						    					// 'post-formats' 	
						    				 ), // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
			    'rewrite' 			  	=> array( 
			    								'slug'		=> $url_rewrite, // string: Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable, that's why we use _x
			    								'with_front'=> true, 		// bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
			    								// 'feeds'		=> true, 	// bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
			    								// 'pages'		=> true, 	// bool: Should the permalink structure provide for pagination. Defaults to true 
			    							 )  // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )
			);

			// Filter arguments
			$args 	= apply_filters( 'plethora_support_cpt_'. $post_type_slug .'_args', $args );
			// Create the post type
			register_post_type( $post_type_slug, $args );
		}

		/**
		 * Registers 'knowledgebase' custom post type
		 * @since 1.0
		 */
		public function register_cpt_knowledgebase() {

			// Slug
			$post_type_slug = 'knowledgebase';
			// Get user defined CPT configuration
			$url_rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'support-knowledgebase-urlrewrite', 'knowledgebase' );
			// Set CPT labels ( will be added to args later )
			$labels = array(

				'name'				=>	esc_html__('Knowledgebase', 'plethora-framework'),		// general name for the post type, usually plural. The same as, and overridden by $post_type_object->label
				'singular_name'		=>	esc_html__('Knowledgebase', 'plethora-framework'),		// name for one object of this post type. Defaults to value of 'name'.
				'menu_name' 		=>	esc_html__('Knowledgebase', 'plethora-framework'),		// the menu name text. This string is the name to give menu items. Defaults to value of 'name'.
			    'name_admin_bar' 	=>  esc_html__('KB Article', 'plethora-framework'),			// name given for the "Add New" dropdown on admin bar. Defaults to 'singular_name' if it exists, 'name' otherwise.
			    'all_items' 		=>  esc_html__('KB Articles', 'plethora-framework'),		// the all items text used in the menu. Default is the value of 'name'.
			    'add_new' 			=>  esc_html__('Add New Article', 'plethora-framework'), 			// the add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type. Example: _x('Add New', 'product');
			    'add_new_item'		=>  esc_html__('Add New Article', 'plethora-framework'), // the add new item text. Default is Add New Post/Add New Page
			    'edit_item' 		=>  esc_html__('Edit Article', 'plethora-framework'), 	// the edit item text. In the UI, this label is used as the main header on the post's editing panel. Default is "Edit Post" for non-hierarchical and "Edit Page" for hierarchical post types.
			    'new_item' 			=>  esc_html__('New Article', 'plethora-framework'), 	// the new item text. Default is "New Post" for non-hierarchical and "New Page" for hierarchical post types.
			    'view_item' 		=>  esc_html__('View Article', 'plethora-framework'), 	// the view item text. Default is View Post/View Page
			    'search_items' 		=>  esc_html__('Search Articles', 'plethora-framework'), // the search items text. Default is Search Posts/Search Pages
			    'not_found' 		=>  esc_html__('No Article Found In Knowledgebase', 'plethora-framework'), // the not found text. Default is No posts found/No pages found
			    'not_found_in_trash'=>  esc_html__('No Article Found In Trash', 'plethora-framework'), // the not found in trash text. Default is No posts found in Trash/No pages found in Trash.
			    // 'parent_item_colon' =>  esc_html__('Parent Page', 'plethora-framework'), 	// the parent text. This string is used only in hierarchical post types. Default is "Parent Page".
			);
			// Set CPT arguments
			$args = array(

	            'label' 				=>	$post_type_slug, // Labels
	            'labels' 				=>	$labels, // Labels
				'description'			=> esc_html__('Build a Knowledge Base on your customer support', 'plethora-framework'),	// A short descriptive summary of what the post type is. 
				'public'				=> true,		// Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
				'exclude_from_search'	=> true,		// Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
				'publicly_queryable'	=> true,		// Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
				'show_ui' 			  	=> true,		// Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
				'show_in_nav_menus'		=> true,		// Whether post_type is available for selection in navigation menus ( default: value of public argument )
				'show_in_menu'			=> true,		// Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
				'show_in_admin_bar'		=> true,		// Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
				'menu_position'			=> 5, 			// The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
				'menu_icon' 			=> 'dashicons-welcome-learn-more', // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
				'hierarchical' 		  	=> false, 		// Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
				// 'taxonomies' 		  	=> array(),		// An array of registered taxonomies like category or post_tag that will be used with this post type. This can be used in lieu of calling register_taxonomy_for_object_type() directly. Custom taxonomies still need to be registered with register_taxonomy(). 
	 			'has_archive' 		  	=> true,		// Enables post type archives. Will use $post_type as archive slug by default (default: false)
				'query_var' 		  	=> true,		// Sets the query_var key for this post type.  (Default: true - set to $post_type )
	 			'can_export' 		  	=> true, 		// Can this post_type be exported. ( Default: true )
		    	'supports' 				=> array( 
						    					'title', 
						    					'editor', 
						    					'author', 	
						    					// 'thumbnail', 	
						    					'excerpt', 	
						    					// 'trackbacks', 	
						    					// 'custom-fields', 	
						    					// 'comments', 	
						    					'revisions', 	
						    					// 'page-attributes', 	
						    					// 'post-formats' 	
						    				 ), // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
			    'rewrite' 			  	=> array( 
			    								'slug'		=> $url_rewrite, // string: Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable, that's why we use _x
			    								'with_front'=> true, 		// bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
			    								// 'feeds'		=> true, 	// bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
			    								// 'pages'		=> true, 	// bool: Should the permalink structure provide for pagination. Defaults to true 
			    							 )  // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )
			);

			// Filter arguments
			$args 	= apply_filters( 'plethora_support_cpt_'. $post_type_slug .'_args', $args );
			// Create the post type
			register_post_type( $post_type_slug, $args );
		}

		/**
		 * Registers 'documentation' custom post type
		 * @since 1.0
		 */
		public function register_cpt_documentation() {

			// Slug
			$post_type_slug = 'documentation';
			// Get user defined CPT configuration
			$url_rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'support-documentation-urlrewrite', 'documentation' );
			// Set CPT labels ( will be added to args later )
			$labels = array(

				'name'				=>	esc_html__('Documentation', 'plethora-framework'),				// general name for the post type, usually plural. The same as, and overridden by $post_type_object->label
				'singular_name'		=>	esc_html__('Documentation', 'plethora-framework'),				// name for one object of this post type. Defaults to value of 'name'.
				'menu_name' 		=>	esc_html__('Documentation', 'plethora-framework'),				// the menu name text. This string is the name to give menu items. Defaults to value of 'name'.
			    'name_admin_bar' 	=>  esc_html__('Doc Page', 'plethora-framework'),			// name given for the "Add New" dropdown on admin bar. Defaults to 'singular_name' if it exists, 'name' otherwise.
			    'all_items' 		=>  esc_html__('Doc Pages', 'plethora-framework'),		// the all items text used in the menu. Default is the value of 'name'.
			    'add_new' 			=>  esc_html__('Add New Doc Page', 'plethora-framework'), // the add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type. Example: _x('Add New', 'product');
			    'add_new_item'		=>  esc_html__('Add New Doc Page', 'plethora-framework'), // the add new item text. Default is Add New Post/Add New Page
			    'edit_item' 		=>  esc_html__('Edit Doc Page', 'plethora-framework'), // the edit item text. In the UI, this label is used as the main header on the post's editing panel. Default is "Edit Post" for non-hierarchical and "Edit Page" for hierarchical post types.
			    'new_item' 			=>  esc_html__('New Doc Page', 'plethora-framework'), // the new item text. Default is "New Post" for non-hierarchical and "New Page" for hierarchical post types.
			    'view_item' 		=>  esc_html__('View Doc Page', 'plethora-framework'), // the view item text. Default is View Post/View Page
			    'search_items' 		=>  esc_html__('Search Doc Pages', 'plethora-framework'), // the search items text. Default is Search Posts/Search Pages
			    'not_found' 		=>  esc_html__('No Page Found In Documentation', 'plethora-framework'), // the not found text. Default is No posts found/No pages found
			    'not_found_in_trash'=>  esc_html__('No Page Found In Trash', 'plethora-framework'), // the not found in trash text. Default is No posts found in Trash/No pages found in Trash.
			    // 'parent_item_colon' =>  esc_html__('Parent Page', 'plethora-framework'), // the parent text. This string is used only in hierarchical post types. Default is "Parent Page".
			);
			// Set CPT arguments
			$args = array(

	            'label' 				=>	$post_type_slug, // Labels
	            'labels' 				=>	$labels, // Labels
				'description'			=> esc_html__('Build documentation pages for your products', 'plethora-framework'),	// A short descriptive summary of what the post type is. 
				'public'				=> true,		// Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
				'exclude_from_search'	=> true,		// Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
				'publicly_queryable'	=> true,		// Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
				'show_ui' 			  	=> true,		// Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
				'show_in_nav_menus'		=> true,		// Whether post_type is available for selection in navigation menus ( default: value of public argument )
				'show_in_menu'			=> true,		// Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
				'show_in_admin_bar'		=> true,		// Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
				'menu_position'			=> 5, 			// The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
				'menu_icon' 			=> 'dashicons-book', // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
				'hierarchical' 		  	=> false, 		// Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
				// 'taxonomies' 		  	=> array(),		// An array of registered taxonomies like category or post_tag that will be used with this post type. This can be used in lieu of calling register_taxonomy_for_object_type() directly. Custom taxonomies still need to be registered with register_taxonomy(). 
	 			'has_archive' 		  	=> true,		// Enables post type archives. Will use $post_type as archive slug by default (default: false)
				'query_var' 		  	=> true,		// Sets the query_var key for this post type.  (Default: true - set to $post_type )
	 			'can_export' 		  	=> true, 		// Can this post_type be exported. ( Default: true )
		    	'supports' 				=> array( 
						    					'title', 
						    					'editor', 
						    					'author', 	
						    					// 'thumbnail', 	
						    					'excerpt', 	
						    					// 'trackbacks', 	
						    					// 'custom-fields', 	
						    					// 'comments', 	
						    					'revisions', 	
						    					// 'page-attributes', 	
						    					// 'post-formats' 	
						    				 ), // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
			    'rewrite' 			  	=> array( 
			    								'slug'		=> $url_rewrite, // string: Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable, that's why we use _x
			    								'with_front'=> true, 		// bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
			    								// 'feeds'		=> true, 	// bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
			    								// 'pages'		=> true, 	// bool: Should the permalink structure provide for pagination. Defaults to true 
			    							 )  // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )
			);

			// Filter arguments
			$args 	= apply_filters( 'plethora_support_cpt_'. $post_type_slug .'_args', $args );
			// Create the post type
			register_post_type( $post_type_slug, $args );

		}


		/**
		 * Registers 'topics' taxonomy
		 * @since 1.0
		 */
		public function register_taxonomy_topics() {

			// Slug
			$taxonomy_slug = 'topic';
			// Get user defined CPT configuration
			$url_rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'support-'. $taxonomy_slug .'-urlrewrite', $taxonomy_slug );
			// 'Topic' Taxonomy options
	        $args = array(
	 
	            'labels' 			=> array(
								        'name'                       => esc_html__( 'Topics', 'plethora-framework' ),
								        'singular_name'              => esc_html__( 'Topic', 'plethora-framework' ),
								        'menu_name'                  => esc_html__( 'Topics', 'plethora-framework' ),
								        'all_items'                  => esc_html__( 'All Topics', 'plethora-framework' ),
								        'edit_item'                  => esc_html__( 'Edit Topic', 'plethora-framework' ),
								        'view_item'                  => esc_html__( 'View Topic', 'plethora-framework' ),
								        'update_item'                => esc_html__( 'Update Topic', 'plethora-framework' ),
								        'add_new_item'               => esc_html__( 'Add New Topic', 'plethora-framework' ),
								        'new_item_name'              => esc_html__( 'New Topic Name', 'plethora-framework' ),
								        'parent_item'                => esc_html__( 'Parent Topic', 'plethora-framework' ),
								        'parent_item_colon'          => esc_html__( 'Parent Topic:', 'plethora-framework' ),
								        'search_items'               => esc_html__( 'Search Topics', 'plethora-framework' ),     
								        'popular_items'              => esc_html__( 'Popular Topics', 'plethora-framework' ),
								        'separate_items_with_commas' => esc_html__( 'Separate Topics with commas', 'plethora-framework' ),
								        'add_or_remove_items'        => esc_html__( 'Add or remove Topics', 'plethora-framework' ),
								        'choose_from_most_used'      => esc_html__( 'Choose from most used Topics', 'plethora-framework' ),
								        'not_found'                  => esc_html__( 'No Topics found', 'plethora-framework' ),
										),
	            'public' 			=> true, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
	            'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
	            'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
	            'show_tagcloud' 	=> false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
	            'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
	            'hierarchical' 		=> false, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
	            'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
	            // 'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> $url_rewrite, // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )

	        );

			// Register 'Topic' Taxonomy
			$args 	= apply_filters( 'plethora_support_taxonomy_'. $taxonomy_slug .'_options', $args );
			register_taxonomy( $taxonomy_slug, array('knowledgebase'), $args );

		}


		/**
		 * Registers 'chapter' taxonomy
		 * @since 1.0
		 */
		public function register_taxonomy_chapters() {

			// Slug
			$taxonomy_slug = 'chapter';
			// Get user defined CPT configuration
			$url_rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'support-'. $taxonomy_slug .'-urlrewrite', $taxonomy_slug );
			// 'Chapter' Taxonomy options
	        $args = array(
	 
	            'labels' 			=> array(
								        'name'                       => esc_html__( 'Doc Chapters', 'plethora-framework' ),
								        'singular_name'              => esc_html__( 'Doc Chapter', 'plethora-framework' ),
								        'menu_name'                  => esc_html__( 'Doc Chapters', 'plethora-framework' ),
								        'all_items'                  => esc_html__( 'All Doc Chapters', 'plethora-framework' ),
								        'edit_item'                  => esc_html__( 'Edit Doc Chapter', 'plethora-framework' ),
								        'view_item'                  => esc_html__( 'View Doc Chapter', 'plethora-framework' ),
								        'update_item'                => esc_html__( 'Update Doc Chapter', 'plethora-framework' ),
								        'add_new_item'               => esc_html__( 'Add New Doc Chapter', 'plethora-framework' ),
								        'new_item_name'              => esc_html__( 'New Doc Chapter Name', 'plethora-framework' ),
								        'parent_item'                => esc_html__( 'Parent Doc Chapter', 'plethora-framework' ),
								        'parent_item_colon'          => esc_html__( 'Parent Doc Chapter:', 'plethora-framework' ),
								        'search_items'               => esc_html__( 'Search Doc Chapters', 'plethora-framework' ),     
								        'popular_items'              => esc_html__( 'Popular Doc Chapters', 'plethora-framework' ),
								        'separate_items_with_commas' => esc_html__( 'Separate Doc Chapters with commas', 'plethora-framework' ),
								        'add_or_remove_items'        => esc_html__( 'Add or remove Doc Chapters', 'plethora-framework' ),
								        'choose_from_most_used'      => esc_html__( 'Choose from most used Doc Chapters', 'plethora-framework' ),
								        'not_found'                  => esc_html__( 'No Doc Chapters found', 'plethora-framework' ),
										),
	            'public' 			=> true, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
	            'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
	            'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
	            'show_tagcloud' 	=> false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
	            'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
	            'hierarchical' 		=> false, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
	            'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
	            // 'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> $url_rewrite , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )

	        );

			// Register 'Topic' Taxonomy
			$args 	= apply_filters( 'plethora_support_taxonomy_'. $taxonomy_slug .'_options', $args );
			register_taxonomy( $taxonomy_slug, array('documentation'), $args );
		}

		/**
		 * Registers 'index-tag' taxonomy
		 * @since 1.0
		 */
		public function register_taxonomy_tags() {

			// Slug
			$taxonomy_slug = 'search-tag';
			// Get user defined CPT configuration
			$url_rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'support-'. $taxonomy_slug .'-urlrewrite', $taxonomy_slug );
			// 'kb-tag' Taxonomy options
	        $args = array(
	 
	            'labels' 			=> array(
								        'name'                       => esc_html__( 'Search Tags', 'plethora-framework' ),
								        'singular_name'              => esc_html__( 'Search Tag', 'plethora-framework' ),
								        'menu_name'                  => esc_html__( 'Search Tags', 'plethora-framework' ),
								        'all_items'                  => esc_html__( 'All Search Tags', 'plethora-framework' ),
								        'edit_item'                  => esc_html__( 'Edit Search Tag', 'plethora-framework' ),
								        'view_item'                  => esc_html__( 'View Search Tag', 'plethora-framework' ),
								        'update_item'                => esc_html__( 'Update Search Tag', 'plethora-framework' ),
								        'add_new_item'               => esc_html__( 'Add New Search Tag', 'plethora-framework' ),
								        'new_item_name'              => esc_html__( 'New Search Tag Name', 'plethora-framework' ),
								        'parent_item'                => esc_html__( 'Parent Search Tag', 'plethora-framework' ),
								        'parent_item_colon'          => esc_html__( 'Parent Search Tag:', 'plethora-framework' ),
								        'search_items'               => esc_html__( 'Index Search Tags', 'plethora-framework' ),     
								        'popular_items'              => esc_html__( 'Popular Search Tags', 'plethora-framework' ),
								        'separate_items_with_commas' => esc_html__( 'Separate Search Tags with commas', 'plethora-framework' ),
								        'add_or_remove_items'        => esc_html__( 'Add or remove Search Tags', 'plethora-framework' ),
								        'choose_from_most_used'      => esc_html__( 'Choose from most used Search Tags', 'plethora-framework' ),
								        'not_found'                  => esc_html__( 'No Search Tags found', 'plethora-framework' ),
										),
	            'public' 			=> true, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
	            'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
	            'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
	            'show_tagcloud' 	=> false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
	            'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
	            'hierarchical' 		=> false, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
	            'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
	            // 'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> $url_rewrite , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )

	        );

			// Register 'Topic' Taxonomy
			$args 	= apply_filters( 'plethora_support_taxonomy_'. $taxonomy_slug .'_options', $args );
			register_taxonomy( $taxonomy_slug, array( 'knowledgebase', 'documentation' ), $args );

		}


		/**
		 * Registers 'product-type' taxonomy
		 * @since 1.0
		 */
		public function register_taxonomy_producttypes() {

			// Slug
			$taxonomy_slug = 'product-type';
			// Get user defined CPT configuration
			$url_rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'support-'. $taxonomy_slug .'-urlrewrite', $taxonomy_slug );
			// 'kb-tag' Taxonomy options
	        $args = array(
	 
	            'labels' 			=> array(
								        'name'                       => esc_html__( 'Product Types', 'plethora-framework' ),
								        'singular_name'              => esc_html__( 'Product Type', 'plethora-framework' ),
								        'menu_name'                  => esc_html__( 'Product Types', 'plethora-framework' ),
								        'all_items'                  => esc_html__( 'All Product Types', 'plethora-framework' ),
								        'edit_item'                  => esc_html__( 'Edit Product Type', 'plethora-framework' ),
								        'view_item'                  => esc_html__( 'View Product Type', 'plethora-framework' ),
								        'update_item'                => esc_html__( 'Update Product Type', 'plethora-framework' ),
								        'add_new_item'               => esc_html__( 'Add New Product Type', 'plethora-framework' ),
								        'new_item_name'              => esc_html__( 'New Product Type Name', 'plethora-framework' ),
								        'parent_item'                => esc_html__( 'Parent Product Type', 'plethora-framework' ),
								        'parent_item_colon'          => esc_html__( 'Parent Product Type:', 'plethora-framework' ),
								        'search_items'               => esc_html__( 'Index Product Types', 'plethora-framework' ),     
								        'popular_items'              => esc_html__( 'Popular Product Types', 'plethora-framework' ),
								        'separate_items_with_commas' => esc_html__( 'Separate Product Types with commas', 'plethora-framework' ),
								        'add_or_remove_items'        => esc_html__( 'Add or remove Product Types', 'plethora-framework' ),
								        'choose_from_most_used'      => esc_html__( 'Choose from most used Product Types', 'plethora-framework' ),
								        'not_found'                  => esc_html__( 'No Product Types found', 'plethora-framework' ),
										),
	            'public' 			=> true, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
	            'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
	            'show_in_nav_menus' => true, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
	            'show_tagcloud' 	=> false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
	            'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
	            'hierarchical' 		=> true, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
	            'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
	            // 'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> $url_rewrite , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )

	        );

			// Register 'Topic' Taxonomy
			$args 	= apply_filters( 'plethora_support_taxonomy_'. $taxonomy_slug .'_options', $args );
			register_taxonomy( $taxonomy_slug, array('product'), $args );

		}
		public static function metabox_productselect( $metaboxes ){

	    	$section = array(
		        'fields'        => array(
					array(
					    'id'       => METAOPTION_PREFIX .'support-product',
					    'type'     => 'select',
					    'data'	   => 'posts',
					    'args'	   => array('post_type' => 'product', 'posts_per_page' => -1, 'suppress_filters' => false ),
					    'multi' => true,
					),

				)
	        );
			
			$sections[] = $section;
		    $metaboxes[] = array(
		        'id'            => METAOPTION_PREFIX .'related-product',
		        'title'         => esc_html__( 'Related Product(s)', 'plethora-framework' ),
		        'post_types'    => array( 'knowledgebase', 'documentation'),
		        'position'      => 'side', // normal, advanced, side
		        'priority'      => 'core', // high, core, default, low
		        'sections'      => $sections,
		    );

	    	return $metaboxes;
	  	}


		public static function metabox_singleproduct_additionaldata( $sections ){

	    	$section = array(
		        'title' => esc_html__('Additional Data', 'plethora-framework'),
				'icon_class'    => 'icon-large',
		        'icon' => 'el-icon-info-sign',
		        'fields'        => array(
					array(
					    'id'       => METAOPTION_PREFIX .'product-demourl',
						'type' => 'text',
						'title' => esc_html__('Preview Url', 'plethora-framework'),
						'subtitle' => esc_html__('Give a short job description', 'plethora-framework'),
						'validate' => 'url',
						'default' => ''
					),
					array(
					    'id'       => METAOPTION_PREFIX .'product-demourl',
						'type' => 'text',
						'title' => esc_html__('Job position', 'plethora-framework'),
						'subtitle' => esc_html__('Give a short job description', 'plethora-framework'),
						'validate' => 'url',
						'default' => ''
					),

				)
	        );
			
			// Use array_unshift to order this section first!
			array_unshift( $sections, $section );

	    	return $sections;
	  	}

	  	// Removes every theme templates tasks. Must be hooked on 'wp'
	  	public function documentation_template() { 

			$this_post_type = get_post_type();
			if (  $this_post_type === 'documentation' ) { 

				// Remove every theme related templating first
				global $plethora_template;
				remove_action( 'wp_enqueue_scripts', array( $plethora_template, 'enqueue_skin_stylesheet'), 1 );
				remove_action( 'wp_enqueue_scripts', array( $plethora_template, 'enqueue_scripts'), 1 );
				remove_action( 'wp_enqueue_scripts', array( $plethora_template, 'enqueue_styles'), 2 );
				remove_action( 'wp', array( $plethora_template, 'global_parts'));  // Template parts for any page type
				remove_action( 'wp', array( $plethora_template, 'archive_post'));  // Template parts for blog page

				// Now, add documentation 

				// Scripts & Styles
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue'), 1 );

				// Must hook them on 'wp'. That will allow native WP conditionals to work properly
				add_action( 'wp', array( $this, 'global_parts'));  // Template parts for any page type
				add_action( 'wp', array( $this, 'archive_post'));  // Template parts for blog page
				add_action( 'wp', array( $this, 'single_post'));   // Template parts for single pages
			}
	  	}

	  	public function enqueue() {


	  	}

	  	public function global_parts() {


			// Head Hooks ( 'plethora_head_before' | 'plethora_head_after' )
			add_action( 'plethora_head_before', array( 'Plethora_Template', 'head_meta_icons'), 10);         // Page meta: icons
			add_action( 'plethora_head_before', array( 'Plethora_Template', 'head_stylesheet'), 20);         // Main stylesheet
			add_action( 'plethora_head_before', array( 'Plethora_Template', 'head_commentsjs'), 30);         // WP comments ajax fix
			add_action( 'wp_head', array( 'Plethora_Template', 'head_outputcss'), 998);                      // Inline CSS output that derives from options & meta
			add_action( 'wp_head', array( 'Plethora_Template', 'head_customcss'), 999);                      // Custom CSS field export
			add_action( 'plethora_head_after', array( 'Plethora_Template', 'head_analytics'), 10);           // Analytics script ( on header )

			// Opening body tag hooks ( 'body_class' | 'plethora_body_open' )
			add_filter( 'body_class', array( 'Plethora_Template', 'body_class'));                            // Body class filter ( WP Hook )
			add_action( 'plethora_body_open', array( 'Plethora_Template', 'wrapper_overflow_open'), 10);     // Overflow wrapper open

			// Header Hooks ( 'plethora_header_class' | plethora_header_before' | 'plethora_header' | 'plethora_header_after' )
			add_filter( 'plethora_header_class', array( 'Plethora_Template', 'header_class'));               // Header class filter 
			add_action( 'plethora_header', array( 'Plethora_Template', 'header_toolbar'), 10);               // Header toolbar
			add_action( 'plethora_header', array( 'Plethora_Template', 'header_navigation'), 20);            // Header main navigation
			add_action( 'plethora_header_after', array( 'Plethora_Module_Mediapanel', 'mediapanel'), 10);    // Media Panel module
			add_action( 'plethora_header_after', array( 'Plethora_Template', 'wrapper_main_open'), 20);      // Main wrapper open

			// Footer Hooks
			add_action( 'plethora_footer_before', array( 'Plethora_Template', 'twitter_feed'), 10);          // Twitter Feed module     
			add_action( 'plethora_footer', array( 'Plethora_Template', 'footer_widgets'), 10);               // Footer widget areas
			add_action( 'plethora_footer', array( 'Plethora_Template', 'footer_infobar'), 20);               // Footer info bar
			add_action( 'plethora_footer_after', array( 'Plethora_Template', 'wrapper_main_close'), 30);     // Main wrapper close
			add_action( 'plethora_footer_after', array( 'Plethora_Template', 'wrapper_overflow_close'), 40); // Overflow wrapper close

			// Closing body tag
			add_action( 'plethora_body_close', array( 'Plethora_Template', 'footer_analytics'), 10);         // Analytics script ( on footer )

			// WP Footer hook
			add_action( 'wp_footer', array( 'Plethora_Template', 'head_customjs'));                          // Custom JS field export

	  	}

	  	public function archive_post() {

	  	}

	  	public function single_post() {

	  	}
	}
}	
