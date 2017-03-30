<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2016

Knowledge Base Post Type Feature Class
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Kb') ) {  
 
	/**
	 * @package Plethora Framework
	 */

	class Plethora_Posttype_Kb {

		// Plethora Index variables
		public static $feature_title         = "Knowledge Base Post Type";		// Feature display title  (string)
		public static $feature_description   = "Contains all KB related post configuration";		// Feature display description (string)
		public static $theme_option_control  = true;		// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;		// Default activation option status ( boolean )
		public static $theme_option_requires = array();	// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;		// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;	// Additional method invocation ( string/boolean | method name or false )

		// Auxilliary variables
		public $post_type_slug = 'kb';
	
		public function __construct() {

			// Create basic post type object
			add_action( 'init', array( $this, 'register_post_type' ), 0 );

			// Add taxonomies 
			add_action( 'init', array( $this, 'register_taxonomies' ), 0 );

			// Make client and type columns sortable

			if ( is_admin() ) {
				// kb Category terms meta configuration	
				add_action( 'admin_init', array( $this, 'taxonomy_terms_options' ) );

				// Archive KB Posts Theme Options
				add_filter( 'plethora_themeoptions_content', array($this, 'archive_themeoptions'), 10);

				// Single KB Post Theme Options
				add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 130);

				// Single Portfolio Metabox		
				add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));
				add_filter( 'plethora_metabox_add', array($this, 'single_metabox_audio'));
				add_filter( 'plethora_metabox_add', array($this, 'single_metabox_video'));
			}

			// Save static page option separate
			$staticpage = Plethora_Theme::option( THEMEOPTION_PREFIX .'archive-page_for_'.$this->post_type_slug.'s', 0);
			update_option( 'plethora_page_for_'. $this->post_type_slug .'s', $staticpage );			

			// Redirect to static page
			// add_action( 'pre_get_posts', array( $this, 'customize_staticpage' ) );
			// add_action( 'template_redirect', array( $this, 'redirect_to_staticpage' ) );	

		}


		public function redirect_to_staticpage() {

			if ( is_admin() ) { return; }
			// Get user defined static page option
			$staticpage = Plethora_Theme::option( THEMEOPTION_PREFIX .'archive-page_for_'.$this->post_type_slug.'s', 0);

		    if ( !empty( $staticpage ) && is_post_type_archive( $this->post_type_slug ) ) {
		        
		        $staticpage_link = get_permalink( $staticpage );
		        wp_redirect( $staticpage_link, 302 );
		        exit();
		    }
		}

		/**
		 * Load custom post type archive on home page
		 *
		 * Reference: http://www.wpaustralia.org/wordpress-forums/topic/pre_get_posts-and-is_front_page/
		 * Reference: http://wordpress.stackexchange.com/questions/30851/how-to-use-a-custom-post-type-archive-as-front-page
		 */
		function customize_staticpage( $query ) {

		    // Only filter the main query on the front-end
		    if ( is_admin() ) {
		    	return $query;
		    }

			// Get user defined static page option
			$staticpage = Plethora_Theme::option( THEMEOPTION_PREFIX .'archive-page_for_'.$this->post_type_slug.'s', 0);
			$thispage   = Plethora_Theme::get_this_page();
		    if( $staticpage == $thispage ) {

		        $query->set( 'post_type', $this->post_type_slug );
		        $query->set( 'page_id', '' );

		        // Set properties to match an archive
		        $query->is_page = 0;
		        $query->is_singular = 0;
		        $query->is_post_type_archive = 1;
		        $query->is_archive = 1;
		    }
		}


		public function register_post_type() {

			// Get user defined url rewrite option
			$rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'kb-urlrewrite', $this->post_type_slug );
			// Names
			$labels = array(
				'name'                  => esc_html__('Knowledge Base', 'plethora-framework'), //general name for the post type, usually plural. The same and overridden by $post_type_object->label. Default is Posts/Pages
				'singular_name'         => esc_html__('KB Article', 'plethora-framework'), //name for one object of this post type. Default is Post/Page
				'add_new'               => esc_html__('Add New', 'plethora-framework'), //the add new text. The default is "Add New" for both hierarchical and non-hierarchical post types. When internationalizing this string, please use a gettext context matching your post type. Example: _x('Add New', 'product');
				'add_new_item'          => esc_html__('Add New KB Article', 'plethora-framework'), //Default is Add New Post/Add New Page.
				'edit_item'             => esc_html__('Edit KB Article', 'plethora-framework'), //Default is Edit Post/Edit Page.
				'new_item'              => esc_html__('New KB Article', 'plethora-framework'), //Default is New Post/New Page.
				'view_item'             => esc_html__('View KB Article', 'plethora-framework'), //Default is View Post/View Page.
				'search_items'          => esc_html__('Search KB Articles', 'plethora-framework'), //Default is Search Posts/Search Pages.
				'not_found'             => esc_html__('No KB Article found', 'plethora-framework'), //Default is No posts found/No pages found.
				'not_found_in_trash'    => esc_html__('No KB Article found in Trash', 'plethora-framework'), //Default is No posts found in Trash/No pages found in Trash.
				'parent_item_colon'     => esc_html__('Parent KB Article', 'plethora-framework'), //This string isn't used on non-hierarchical types. In hierarchical ones the default is 'Parent Page:'.
				'all_items'             => esc_html__('All KB Articles', 'plethora-framework'), //String for the submenu. Default is All Posts/All Pages.
				'archives'              => esc_html__('Knowledge Base Archive', 'plethora-framework'), //String for use with archives in nav menus. Default is Post Archives/Page Archives.
				'insert_into_item'      => esc_html__('Insert Into KB Article', 'plethora-framework'), //String for the media frame button. Default is Insert into post/Insert into page.
				'uploaded_to_this_item' => esc_html__('Uploaded to this KB Article', 'plethora-framework'), //String for the media frame filter. Default is Uploaded to this post/Uploaded to this page.
			);

			// Options
			$options = array(

	            'labels' 		=> $labels, // Title prompt text 
	            'enter_title_here' 		=> 'Knowledge base article title', // Title prompt text 
				'description'			=> '',	// A short descriptive summary of what the post type is. 
				'public'				=> true,		// Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
				'exclude_from_search'	=> true,		// Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
				'publicly_queryable'	=> true,		// Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
				'show_ui' 			  	=> true,		// Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
				'show_in_nav_menus'		=> true,		// Whether post_type is available for selection in navigation menus ( default: value of public argument )
				'show_in_menu'			=> true,		// Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
				'show_in_admin_bar'		=> true,		// Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
				'menu_position'			=> 5, 			// The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
				'menu_icon' 			=> 'dashicons-book-alt', // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
				'hierarchical' 		  	=> false, 		// Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
	 			'has_archive' 		  	=> false,		// Enables post type archives. Will use $post_type as archive slug by default (default: false)
				'query_var' 		  	=> true,		// Sets the query_var key for this post type.  (Default: true - set to $post_type )
	 			'can_export' 		  	=> true, 		// Can this post_type be exported. ( Default: true )
		    	'supports' 				=> array( 
						    					'title', 
						    					'editor', 
						    					'author', 	
						    					'thumbnail', 	
						    					'excerpt', 	
						    					'trackbacks', 	
						    					'custom-fields', 	
						    					// 'comments', 	
						    					'revisions', 	
						    					// 'page-attributes', 	
						    					'post-formats' 	
						    				 ), // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
			    'rewrite' 			  	=> array( 
			    								'slug'		=> sanitize_key( $rewrite ), // string: Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable, that's why we use _x
			    								'with_front'=> true, 		// bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
			    								// 'feeds'		=> true, 	// bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
			    								// 'pages'		=> true, 	// bool: Should the permalink structure provide for pagination. Defaults to true 
			    							 ), // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )

			);

			// Hooks to apply
			$options 	= apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_options', $options );
			// Create the post type
			register_post_type( $this->post_type_slug, $options );
		}

		function add_taxonomies( $post_type_obj ) {


			// Register Topics Taxonomy
			$options 	= apply_filters( 'plethora_posttype_taxonomy_kb-topic_options', $options );
			$post_type_obj->register_taxonomy('kb-topic', $options );
			return $post_type_obj;
		}

		/**
		* KB Posts archive (blog) view theme options configuration for REDUX
		* Filter hook @ 'plethora_themeoptions_content'
		*/
        public function archive_themeoptions( $sections  ) {

        	// setup theme options according to configuration
			$opts        = $this->archive_options();
			$opts_config = $this->archive_options_config();
			$fields      = array();
        	foreach ( $opts_config as $opt_config ) {

				$id          = $opt_config['id'];
				$status      = $opt_config['theme_options'];
				$default_val = $opt_config['theme_options_default'];
        		if ( $status && array_key_exists( $id, $opts ) ) {

        			if ( !is_null( $default_val ) ) { // will add only if not NULL }
						$opts[$id]['default'] = $default_val;
					}
					
					// a smal workaround to remove subtitles that HAVE to be displayed on CPT
        			if ( isset( $opts[$id]['subtitle'] ) ) { 
						unset( $opts[$id]['subtitle'] );
					}

					$fields[] = $opts[$id];
        		}
        	}

        	if ( !empty( $fields ) ) {

				$page_for_posts	= Plethora_Theme::option( THEMEOPTION_PREFIX .'archive-page_for_'.$this->post_type_slug.'s', 0 );
				$desc_1 = esc_html__('These options affect your KB posts catalog display.', 'plethora-framework');
				$desc_2 = esc_html__('These options affect your KB posts catalog display...however it seems that you', 'plethora-framework'); 
				$desc_2 .= ' <span style="color:red">';
				$desc_2 .= esc_html__('have not set a static KB posts page yet!.', 'plethora-framework');
				$desc_2 .= '</span>';
				$desc_2 .= esc_html__('You can go for it under \'Settings > Reading\'', 'plethora-framework');
				$desc = $page_for_posts === 0 || empty($page_for_posts) ? $desc_2 :  $desc_1 ;
				$desc .= '<br>'. sprintf( esc_html__('If you are using a speed optimization plugin, don\'t forget to %1$sclear cache%2$s after options update', 'plethora-framework'), '<strong>', '</strong>' );

				$sections[] = array(
					'title'      => esc_html__('KB Archive', 'plethora-framework'),
					'heading'    => esc_html__('KB ARCHIVE OPTIONS', 'plethora-framework'),
					'desc'       => $desc,
					'subsection' => true,
					'fields'     => $fields
				);
			}
			return $sections;
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
						'title'      => esc_html__('KB Articles', 'plethora-framework'),
						'heading'    => esc_html__('SINGLE KB ARTICLES OPTIONS', 'plethora-framework'),
						'desc'       => esc_html__('These will be the default values for a new KB article post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
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
			if ( has_filter( 'plethora_metabox_singlekb') ) {

				$sections = apply_filters( 'plethora_metabox_singlekb', $sections );
			}
		    $metaboxes[] = array(
		        'id'            => 'metabox-single-kb',
		        'title'         => esc_html__( 'KB Article Options', 'plethora-framework' ),
		        'post_types'    => array( 'kb' ),
		        'position'      => 'normal', // normal, advanced, side
		        'priority'      => 'high', // high, core, default, low
		        'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
		        'sections'      => $sections,
		    );

		    return $metaboxes;
        }

		/** 
		* Returns ARCHIVE OPTIONS INDEX
		* It contains ALL possible archive options, no matter which theme OR CPT
		*/
        public function archive_options( $post_type_obj = '' ) {

        	if ( ! is_object( $post_type_obj ) ) { 

        		$post_type_obj = get_post_type_object( 'kb' );
        	}

			$post_type                = $post_type_obj->name;
			$post_type_label          = $post_type_obj->label;
			$post_type_label_singular = $post_type_obj->labels->singular_name;

		    $archive_options['page-start'] = array(
							'id'     => 'archive'.$post_type.'-page-start',
							'type'   => 'section',
							'title'  => $post_type_label_singular . ' '. esc_html__('Archive Page Options', 'plethora-framework'),
							'indent' => true,
			);

		    $archive_options['page_for_posts'] = array(
							'id'      => THEMEOPTION_PREFIX .'archive-page_for_'.$post_type.'s',
							'title'   => esc_html__('Static Archive Display Page', 'plethora-framework' ),
							'type'    => 'select',
							'data'    => 'pages',
			);

		    $archive_options['layout'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-layout',
							'title'   => esc_html__('Page Layout', 'plethora-framework' ),
							'type'    => 'image_select',
							'options' => Plethora_Module_Style::get_options_array( array( 
																						'type'   => 'page_layouts',
																						'use_in' => 'redux',
																				   )
										 ),
			);

		    $archive_options['sidebar'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-sidebar',
							'required' => array(METAOPTION_PREFIX .'archive'.$post_type.'-layout','equals',array('right_sidebar','left_sidebar')),  
							'type'     => 'select',
							'data'     => 'sidebars',
							'multi'    => false,
							'title'    => esc_html__('Sidebar', 'plethora-framework'), 
			);

		    $archive_options['colorset'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-colorset',
							'type'    => 'button_set',
							'title'   => esc_html__('Content Section Color Set', 'plethora-framework' ),
							'desc'    => esc_html__('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
							'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																						  'use_in'          => 'redux',
																						  'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
			);

		    $archive_options['title'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-title',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Title On Content', 'plethora-framework'),
							'desc'    => esc_html__('Enable/disable titles section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
			);

		    $archive_options['title-text'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-title-text',
							'type'      => 'text',
							'title'     => esc_html__('Default Title', 'plethora-framework'), 
							'translate' => true,
			);

		    $archive_options['title-tax'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-title-tax',
							'type'    => 'button_set', 
							'title'   => esc_html__('Selected Taxonomy Title', 'plethora-framework'),
							'desc'    => esc_html__('Title behavior when a taxonomy archive ( category, tag, etc ) is displayed', 'plethora-framework'),
							'options' => array(
											0 => esc_html__('Default Title', 'plethora-framework'),
											1 => esc_html__('Taxonomy Title', 'plethora-framework'),
										),
			);

		    $archive_options['subtitle'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Subtitle On Content', 'plethora-framework'),
			);

		    $archive_options['subtitle-text'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-subtitle-text',
							'type'      => 'text',
							'title'     => esc_html__('Default Subtitle', 'plethora-framework'), 
							'translate' => true,
			);

		    $archive_options['tax-subtitle'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-tax-subtitle',
							'type'    => 'button_set', 
							'title'   => esc_html__('Selected Taxonomy Subtitle', 'plethora-framework'),
							'desc'    => esc_html__('Subtitle behavior when a category OR tag archive is displayed', 'plethora-framework'),
							'options' => array(
											0 => esc_html__('Default Subtitle', 'plethora-framework'),
											1 => esc_html__('Taxonomy Description', 'plethora-framework'),
										),
			);

		    $archive_options['listings-start'] = array(
						'id'     => 'archive'.$post_type.'-listings-start',
						'type'   => 'section',
						'title'  => esc_html__('KB Posts Listings Options', 'plethora-framework'),
						'indent' => true,
			);

		    $archive_options['mediadisplay'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-mediadisplay',
							'type'     => 'button_set', 
							'title'    => esc_html__('Featured Media Display', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html__('This post type supports feature image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support featured image', 'plethora-framework') .'</div>',
							'options'  => array(
									'inherit'       => 'According To Post Format',
									'featuredimage' => 'Force Featured Image Display',
									'hide'          => 'Do Not Display',
									),
			);

		    $archive_options['listing-content'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-listing-content',
							'type'     => 'button_set', 
							'title'    => esc_html__('Content/Excerpt Display', 'plethora-framework'), 
							'subtitle' => post_type_supports( $post_type, 'editor' ) ? '<span style="color:green">'. esc_html__('This post type supports editor content', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support editor content', 'plethora-framework') .'</div>',
							'desc'     => esc_html__('Displaying content will allow you to display posts containing the WP editor "More" tag.', 'plethora-framework'),
							'options'  => array(
								'excerpt' => esc_html__('Display Excerpt', 'plethora-framework'), 
								'content' => esc_html__('Display Content', 'plethora-framework') 
							)
			);

		    $archive_options['listing-subtitle'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-listing-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Subtitle', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html__('This post type supports subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support subtitle', 'plethora-framework') .'</div>',
							'options' => array(
											1 => esc_html__('Display', 'plethora-framework'),
											0 => esc_html__('Hide', 'plethora-framework'),
										),
			);
		    $archive_options['info-primarytax'] = array(
							'id'    => METAOPTION_PREFIX.'archive'. $post_type .'-info-primarytax',
							'type'  => 'switch', 
							'title' => sprintf( esc_html__('Display Primary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
							'desc'  => sprintf( esc_html__('You may choose the primary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
			);

		    // use only for cpts
		    $archive_options['info-secondarytax'] = array(
							'id'      => METAOPTION_PREFIX.'archive'. $post_type .'-info-secondarytax',
							'type'    => 'switch', 
							'title'   => sprintf( esc_html__('Display Secondary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
							'desc'    => sprintf( esc_html__('You may choose the secondary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
			);

		    $archive_options['info-author'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-info-author',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Author Info', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'author' ) ? '<span style="color:green">'. esc_html__('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support authors', 'plethora-framework') .'</div>',
							'desc' 	   => $post_type !== 'post' ? esc_html__('Display a non linked author label', 'plethora-framework') : '',
			);

		    $archive_options['info-date'] = array(
							'id'    => METAOPTION_PREFIX .'archive'.$post_type.'-info-date',
							'type'  => 'switch', 
							'title' => esc_html__('Display Date Info', 'plethora-framework'),
							'desc'  => $post_type !== 'post' ? esc_html__('Display a non linked date label', 'plethora-framework') : '',
			);

		    $archive_options['info-comments'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-info-comments',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Comments Count Info', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'comments' ) ? '<span style="color:green">'. esc_html__('This post type supports comments', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support comments', 'plethora-framework') .'</div>',
			);

		    $archive_options['show-linkbutton'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton',
							'type'    => 'switch', 
							'title'   => esc_html__('Display "Read More" Button', 'plethora-framework'),
			);

		    $archive_options['show-linkbutton-text'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton-text',
							'type'      => 'text',
							'required'  => array(METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton', '=', 1),
							'title'     => esc_html__('Button Text', 'plethora-framework'),
							'translate' => true,
			);

		    $archive_options['noposts-title'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-noposts-title',
							'type'      => 'text', 
							'title'     => sprintf( esc_html__('No %1s Title Text', 'plethora-framework'), $post_type_label ),
							'translate' => true,
			);

		    $archive_options['noposts-description'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-noposts-description',
							'type'      => 'textarea', 
							'title'     => sprintf( esc_html__('No %1s Description Text', 'plethora-framework'), $post_type_label ),
							'translate' => true,
			);

			return $archive_options;
        }

		/** 
		* Archive view CONFIGURATION for theme options and metabox panels
		* Common for native posts and user set CPTs
		*/
		public function archive_options_config() {

			$config = array(
						array( 
							'id'                    => 'page-start', 
							'theme_options'         => true, 
							'theme_options_default' => NULL,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'page_for_posts', 
							'theme_options'         => true, 
							'theme_options_default' => '',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'layout', 
							'theme_options'         => true, 
							'theme_options_default' => 'right_sidebar',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'sidebar', 
							'theme_options'         => true, 
							'theme_options_default' => 'sidebar-default',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'colorset', 
							'theme_options'         => true, 
							'theme_options_default' => 'foo',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title-text', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'The Blog', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title-tax', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle-text', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Articles & News', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'tax-subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listings-start', 
							'theme_options'         => true, 
							'theme_options_default' => NULL,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listtype', 
							'theme_options'         => true, 
							'theme_options_default' => 'classic',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'mediadisplay', 
							'theme_options'         => true, 
							'theme_options_default' => 'inherit',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listing-content', 
							'theme_options'         => true, 
							'theme_options_default' => 'content',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listing-subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 0,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-primarytax', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-secondarytax', 
							'theme_options'         => true, 
							'theme_options_default' => 0,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-author', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-date', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-comments', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'show-linkbutton', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'show-linkbutton-text', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Read More', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'noposts-title', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'No posts where found!', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'noposts-description', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Unfortunately, no posts were found! Please try again soon!', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
			);

			return $config;
		}


		/** 
		* Returns single options index for final configuration
		*/
        public function single_options() {

		    $single_options['layout'] = array(
						'id'      =>  METAOPTION_PREFIX .'kb-layout',
						'title'   => esc_html__( 'Select Layout', 'plethora-framework' ),
						'type'    => 'image_select',
						'options' => Plethora_Module_Style::get_options_array( array( 
																					'type'   => 'page_layouts',
																					'use_in' => 'redux',
																			   )
									 ),
		                );
		    $single_options['sidebar'] = array(
						'id'       => METAOPTION_PREFIX .'kb-sidebar',
						'type'     => 'select',
						'required' => array(METAOPTION_PREFIX .'kb-layout','equals',array('right_sidebar','left_sidebar')),  
						'data'     => 'sidebars',
						'multi'    => false,
						'title'    => esc_html__('Select Sidebar', 'plethora-framework'), 
		                );

		    $single_options['colorset'] = array(
						'id'      => METAOPTION_PREFIX .'kb-colorset',
						'type'    => 'button_set',
						'title'   => esc_html__( 'Content Section Color Set', 'plethora-framework' ),
						'desc'    => esc_html__( 'Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
						'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																					  'use_in'          => 'redux',
																					  'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
		                );

		    $single_options['featured'] = array(
							'id'    => METAOPTION_PREFIX . 'kb-featured',
							'type'  => 'switch', 
							'title' => esc_html__('Featured KB article', 'plethora-framework'),
							'desc'  => esc_html__('Setting this KB post as featured, will give it special treatment on several shortcode displays ( i.e. KB Articles Loop shortcode ).', 'plethora-framework'),
			);

		    $single_options['title'] = array(
						'id'      => METAOPTION_PREFIX .'kb-title',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Title', 'plethora-framework'),
						'desc'    => esc_html__('Enable/disable titles section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
		                );
		    $single_options['subtitle'] = array(
						'id'      => METAOPTION_PREFIX .'kb-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Subtitle', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
		                );

		    $single_options['subtitle-text'] = array(
						'id'       => METAOPTION_PREFIX .'kb-subtitle-text',
						'required' => array( METAOPTION_PREFIX .'kb-subtitle','equals',array( 1 )),  
						'type'     => 'text',
						'title'    => esc_html__('Subtitle', 'plethora-framework'), 
						'translate' => true,
			);

		    $single_options['mediadisplay'] = array(
						'id'      => METAOPTION_PREFIX . 'kb-mediadisplay',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Feautured Media', 'plethora-framework'),
		                );

		    $single_options['media-stretch'] = array(
						'id'      => METAOPTION_PREFIX . 'kb-media-stretch',
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
						'id'    => METAOPTION_PREFIX . 'kb-info-primarytax',
						'type'  => 'switch', 
						'title' => esc_html__('Display Primary Taxonomy Info', 'plethora-framework'), 

			);
		    $single_options['info-primarytax-slug'] = array(
						'id'       => METAOPTION_PREFIX . 'kb-info-primarytax-slug',
						'required' => array( METAOPTION_PREFIX . 'kb-info-primarytax','=', 1),						
						'type'     => 'select', 
						'title'    => esc_html__('Set Primary Taxonomy', 'plethora-framework'),
						'desc'     => esc_html__('Topics is set by default as the primary taxonomy. Use this only in case you need to display a custom taxonomy associated with KB articles post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
						'data'     => 'taxonomies',
						'args'     => array( 'public' => 1 ),
			);

		    // THIS SHOULD BE ADDED EVEN IF POST TYPE IS NOT ASSOCIATED WITH OTHER TAXONOMY
		    $single_options['info-secondarytax'] = array(
						'id'    => METAOPTION_PREFIX . 'kb-info-secondarytax',
						'type'  => 'switch', 
						'title' => esc_html__('Display Secondary Taxonomy Info', 'plethora-framework'), 

			);

		    // THIS SHOULD BE ADDED EVEN IF POST TYPE IS NOT ASSOCIATED WITH OTHER TAXONOMY
		    $single_options['info-secondarytax-slug'] = array(
						'id'       => METAOPTION_PREFIX . 'kb-info-secondarytax-slug',
						'required' => array( METAOPTION_PREFIX . 'kb-info-secondarytax','=', 1),						
						'type'     => 'select', 
						'title'    => esc_html__('Set Secondary Taxonomy', 'plethora-framework'),
						'desc'     => esc_html__('No taxonomy is set by default as the secondary taxonomy. Use this only in case you need to display a custom taxonomy associated with KB articles post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
						'data'     => 'taxonomies',
						'args'     => array( 'public' => 1 ),
			);

		    // THIS SHOULD BE ADDED EVEN IF POST TYPE IS NOT ASSOCIATED WITH OTHER TAXONOMY
		    $single_options['info-thirdtax'] = array(
						'id'    => METAOPTION_PREFIX . 'kb-info-thirdtax',
						'type'  => 'switch', 
						'title' => esc_html__('Display Third Taxonomy Info', 'plethora-framework'), 

			);

		    // THIS SHOULD BE ADDED EVEN IF POST TYPE IS NOT ASSOCIATED WITH OTHER TAXONOMY
		    $single_options['info-thirdtax-slug'] = array(
						'id'       => METAOPTION_PREFIX . 'kb-info-thirdtax-slug',
						'required' => array( METAOPTION_PREFIX . 'kb-info-thirdtax','=', 1),						
						'type'     => 'select', 
						'title'    => esc_html__('Set Third Taxonomy', 'plethora-framework'),
						'desc'     => esc_html__('No taxonomy is set by default as the third taxonomy. Use this only in case you need to display a custom taxonomy associated with KB articles post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
						'data'     => 'taxonomies',
						'args'     => array( 'public' => 1 ),
			);

		    $single_options['info-author'] = array(
							'id'       => METAOPTION_PREFIX .'kb-info-author',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Author Info', 'plethora-framework'),
							'subtitle' => post_type_supports( 'kb', 'author' ) ? '<span style="color:green">'. esc_html__('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support authors', 'plethora-framework') .'</div>',
			);

		    $single_options['date'] = array(
						'id'      => METAOPTION_PREFIX . 'kb-date',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Date Info', 'plethora-framework'),
		                );

		    $single_options['urlrewrite'] = array(
						'id'               => THEMEOPTION_PREFIX .'kb-urlrewrite',
						'type'             => 'text',
						'title'            => esc_html__('URL Rewrite', 'plethora-framework'), 
						'desc'             => esc_html__('Specify a custom permalink for KB articles ( i.e.: http://yoursite.com/portfolio/sample-portfolio). NOTICE: Updating this will probably result a 404 page error on every portfolio post. This can be easily fixed with a simple click on "Save Changes" button, on the "Settings > Permalinks" screen', 'plethora-framework'),
						'flush_permalinks' => true,
		                );

		    $single_options['excerpt'] = array(
						'id'      => METAOPTION_PREFIX . 'kb-excerpt',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Excerpt', 'plethora-framework'),
		                );

		    $single_options['extraclass'] = array(
						'id'      => METAOPTION_PREFIX .'kb-extraclass',
						'type'    => 'text', 
						'title'   => esc_html__('Extra Classes', 'plethora-framework'),
						'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
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
							'id'                    => 'colorset', 
							'theme_options'         => true, 
							'theme_options_default' => 'foo',
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
							'theme_options_default' => 'kb-topic',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-secondarytax', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-secondarytax-slug', 
							'theme_options'         => true, 
							'theme_options_default' => 'kb-product',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-thirdtax', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-thirdtax-slug', 
							'theme_options'         => true, 
							'theme_options_default' => 'kb-tag',
							'metabox'               => false,
							'metabox_default'       => NULL
							),

						array( 
							'id'                    => 'info-author', 
							'theme_options'         => true, 
							'theme_options_default' => false,
							'metabox'               => true,
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
							'id'                    => 'extraclass', 
							'theme_options'         => true, 
							'theme_options_default' => '',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'urlrewrite', 
							'theme_options'         => true, 
							'theme_options_default' => 'kb',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
			);

			return $config;
		}

		/** 
		* Returns additional AUDIO METABOX options configuration for single post views
		* Filter hook @ 'plethora_metabox_add'
		*/
        public function single_metabox_audio( $metaboxes ) {

		    $sections = array();

		    $sections[] = array(
		        'icon_class'    => 'icon-large',
		        'icon'          => 'el-icon-website',
		        'fields'        => array(


					array(
						'id'       => METAOPTION_PREFIX .'content-audio',
						'type'     => 'text', 
						'title'    => esc_html__('Audio Link', 'plethora-framework'),
						'desc'     => sprintf( esc_html__('Enter audio url/share link from: %1$sSoundCloud | Spotify | Rdio%2$s', 'plethora-framework'), '<strong>', '</strong>' ),
						'validate' => 'url',
						),

		        )
		    );

		    $metaboxes[] = array(
		        'id'            => 'metabox-single-kb-audio',
		        'title'         => esc_html__('Featured Audio', 'plethora-framework' ),
		        'post_types'    => array( 'kb'),
		        'post_format'    => array( 'audio'),
		        'position'      => 'side', // normal, advanced, side
		        'priority'      => 'low', // high, core, default, low
		        'sections'      => $sections,
		    );

		    return $metaboxes;
		}

		/** 
		* Returns additional VIDEO METABOX options configuration for single post views
		* Filter hook @ 'plethora_metabox_add'
		*/
        public function single_metabox_video( $metaboxes ) {

		    $sections = array();

		    $sections[] = array(
		        'icon_class'    => 'icon-large',
		        'icon'          => 'el-icon-website',
		        'fields'        => array(


					array(
						'id'=> METAOPTION_PREFIX .'content-video',
						'type' => 'text', 
						'title' => esc_html__('Video Link', 'plethora-framework'),
						'desc' => sprintf( esc_html__('Enter video url/share link from: %1$sYouTube | Vimeo | Dailymotion | Blip | Wordpress.tv%2$s', 'plethora-framework'), '<strong>', '</strong>' ),
						'validate' => 'url',
						),

		        )
		    );

		    $metaboxes[] = array(
		        'id'            => 'metabox-single-kb-video',
		        'title'         => esc_html__('Featured Video', 'plethora-framework' ),
		        'post_types'    => array( 'kb'),
		        'post_format'    => array( 'video'),
		        'position'      => 'side', // normal, advanced, side
		        'priority'      => 'low', // high, core, default, low
		        'sections'      => $sections,
		    );

		    return $metaboxes;
        }


		/** 
		* Terms meta configuration
		* Can use this for extension classes
		*/
		public function taxonomy_terms_options() {

			$taxonomy = 'kb-topic';
			$opts = array(
						array(
							'id'               => TERMSMETA_PREFIX . $taxonomy .'-colorset',	// * Unique ID identifying the field. Must be different from all other field IDs.
							'type'             => 'select',      							// * Value identifying the field type.
							'title'            => esc_html__( 'Color Set', 'plethora-framework' ),  // Displays title of the option.
							'desc'             => esc_html__( 'This will be the default color settings for various elements that display Topic terms', 'plethora-framework' ),      // Description of the option, usualy appearing beneath the field control.
							'default'          => 'black_section',	// Value set as default
							'options'          => Plethora_Module_Style_Ext::get_options_array( array( 'type'=> 'color_sets', 'prepend_default' => true ) ),
							'admin_col'        => true, // Add it to terms table
							'admin_col_markup' => '<div class="%1$s">%2$s</div>',      // Column markup ( %1$s: value / %2$s: Option title ( if supported by field ), %3$s: field title, %4$s: term name )
						)
			);

			$kb_topic_terms = new Plethora_Fields_Termsmeta( $taxonomy, $opts );


			$taxonomy = 'kb-product';
			$opts = array(
						array(
							'id'       => TERMSMETA_PREFIX . $taxonomy .'-productpageurl',	// * Unique ID identifying the field. Must be different from all other field IDs.
							'type'     => 'text',      							// * Value identifying the field type.
							'title'    => esc_html__( 'Product Page URL', 'plethora-framework' ),  // Displays title of the option.
							'desc'     => esc_html__( 'The URL where customers can buy this product', 'plethora-framework' ),      // Description of the option, usualy appearing beneath the field control.
							'default'  => '',	// Value set as default
							'priority' => 1,	// Value set as default
						),
						array(
							'id'       => TERMSMETA_PREFIX . $taxonomy .'-onlinedoc',	// * Unique ID identifying the field. Must be different from all other field IDs.
							'type'     => 'text',      							// * Value identifying the field type.
							'title'    => esc_html__( 'Online Documentation URL', 'plethora-framework' ),  // Displays title of the option.
							'desc'     => esc_html__( 'The URL for the online doc page', 'plethora-framework' ),      // Description of the option, usualy appearing beneath the field control.
							'default'  => '',	// Value set as default
							'priority' => 2,	// Value set as default
						)
			);

			$kb_product_terms = new Plethora_Fields_Termsmeta( $taxonomy, $opts );
		


		}

		// These taxonomies registration must be done in separate
		// as they should be assigned to both kb and doc post types
	  	public function register_taxonomies() {

			// Topics Taxonomy labels
			$labels = array(

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
		        'separate_items_with_commas' => esc_html__( 'Seperate Topics with commas', 'plethora-framework' ),
		        'add_or_remove_items'        => esc_html__( 'Add or remove Topics', 'plethora-framework' ),
		        'choose_from_most_used'      => esc_html__( 'Choose from most used Topics', 'plethora-framework' ),
		        'not_found'                  => esc_html__( 'No Topics found', 'plethora-framework' ),
			);

			// Topics Taxonomy options
	        $options = array(
	 
	            'labels' => $labels,
	            'public' 			=> false, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
	            'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
	            'show_in_nav_menus' => false, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
	            'show_tagcloud' 	=> false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
	            'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
	            'hierarchical' 		=> true, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
	            'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
	            // 'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> esc_html__('kb-topic','plethora-framework') , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> true,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> true,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
	        );

			// Register Products Taxonomy
			$options 	= apply_filters( 'plethora_posttype_taxonomy_kb-topic_options', $options );
			register_taxonomy( 'kb-topic', array( 'kb' ), $options );

			// Products Taxonomy labels
			$labels = array(

		        'name'                       => esc_html__( 'Products', 'plethora-framework' ),
		        'singular_name'              => esc_html__( 'Product', 'plethora-framework' ),
		        'menu_name'                  => esc_html__( 'Products', 'plethora-framework' ),
		        'all_items'                  => esc_html__( 'All Products', 'plethora-framework' ),
		        'edit_item'                  => esc_html__( 'Edit Product', 'plethora-framework' ),
		        'view_item'                  => esc_html__( 'View Product', 'plethora-framework' ),
		        'update_item'                => esc_html__( 'Update Product', 'plethora-framework' ),
		        'add_new_item'               => esc_html__( 'Add New Product', 'plethora-framework' ),
		        'new_item_name'              => esc_html__( 'New Product Name', 'plethora-framework' ),
		        'parent_item'                => esc_html__( 'Parent Product', 'plethora-framework' ),
		        'parent_item_colon'          => esc_html__( 'Parent Product:', 'plethora-framework' ),
		        'search_items'               => esc_html__( 'Search Product', 'plethora-framework' ),     
		        'popular_items'              => esc_html__( 'Popular Product', 'plethora-framework' ),
		        'separate_items_with_commas' => esc_html__( 'Seperate Product with commas', 'plethora-framework' ),
		        'add_or_remove_items'        => esc_html__( 'Add or remove Product', 'plethora-framework' ),
		        'choose_from_most_used'      => esc_html__( 'Choose from most used Product', 'plethora-framework' ),
		        'not_found'                  => esc_html__( 'No Product found', 'plethora-framework' ),
			);

			// Products Taxonomy options
		    $options = array(

		        'labels' => $labels,
		        'public' 			=> false, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
		        'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
		        'show_in_nav_menus' => false, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
		        'show_tagcloud' 	=> true, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
		        'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
		        'hierarchical' 		=> false, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
		        'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
		        // 'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> esc_html__('kb-product','plethora-framework') , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> false,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> false,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
		    );

			// Register Products Taxonomy
			$options 	= apply_filters( 'plethora_posttype_taxonomy_kb-product_options', $options );
			register_taxonomy( 'kb-product', array( 'kb', 'doc' ), $options );

			// Tags Taxonomy labels
			$labels = array(

		        'name'                       => esc_html__( 'KB Tags', 'plethora-framework' ),
		        'singular_name'              => esc_html__( 'KB Tag', 'plethora-framework' ),
		        'menu_name'                  => esc_html__( 'KB Tags', 'plethora-framework' ),
		        'all_items'                  => esc_html__( 'All KB Tags', 'plethora-framework' ),
		        'edit_item'                  => esc_html__( 'Edit KB Tag', 'plethora-framework' ),
		        'view_item'                  => esc_html__( 'View KB Tag', 'plethora-framework' ),
		        'update_item'                => esc_html__( 'Update KB Tag', 'plethora-framework' ),
		        'add_new_item'               => esc_html__( 'Add New KB Tag', 'plethora-framework' ),
		        'new_item_name'              => esc_html__( 'New Tag KB Name', 'plethora-framework' ),
		        'parent_item'                => esc_html__( 'Parent KB Tag', 'plethora-framework' ),
		        'parent_item_colon'          => esc_html__( 'Parent KB Tag:', 'plethora-framework' ),
		        'search_items'               => esc_html__( 'Search KB Tag', 'plethora-framework' ),     
		        'popular_items'              => esc_html__( 'Popular KB Tag', 'plethora-framework' ),
		        'separate_items_with_commas' => esc_html__( 'Seperate KB Tag with commas', 'plethora-framework' ),
		        'add_or_remove_items'        => esc_html__( 'Add or remove KB Tag', 'plethora-framework' ),
		        'choose_from_most_used'      => esc_html__( 'Choose from most used KB Tag', 'plethora-framework' ),
		        'not_found'                  => esc_html__( 'No KB Tag found', 'plethora-framework' ),
			);

			// Tags Taxonomy options
		    $options = array(

		        'labels' => $labels,
		        'public' 			=> false, 	// (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
		        'show_ui' 			=> true, 	// (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
		        'show_in_nav_menus' => false, 	// (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
		        'show_tagcloud' 	=> false, 	// (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
		        'show_admin_column' => true, 	// (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
		        'hierarchical' 		=> false, 	// (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
		        'query_var' 		=> true, 	// (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
		        // 'sort' 				=> true,	// (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
				'rewrite'			=> array( 
								  		'slug'			=> esc_html__('kb-tag','plethora-framework') , // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
				                  		'with_front'	=> false,    // allowing permalinks to be prepended with front base - defaults to true 
				                  		'hierarchical'	=> false,  	// true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
				                  	   ), 		// (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
		    );

			// Tags Taxonomy
			$options = apply_filters( 'plethora_posttype_taxonomy_kb-tag_options', $options );
			register_taxonomy( 'kb-tag', array( 'kb', 'doc' ), $options );
	  	}

	}
}	
