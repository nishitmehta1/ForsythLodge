<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Portfolio Post Type Feature Class
Hooks > Filters

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Terminology') ) {  
 
    /**
     * @package Plethora Framework
     */

    class Plethora_Posttype_Terminology {

        // Plethora Index variables
        public static $feature_title         = "Terminology Post Type";                                 // Feature display title  (string)
        public static $feature_description   = "Contains all terminology related post configuration";   // Feature display description (string)
        public static $theme_option_control  = true;                                                    // Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_default  = true;                                                    // Default activation option status ( boolean )
        public static $theme_option_requires = array();                                                 // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                                                    // Dynamic class construction ? ( boolean )
        public static $dynamic_method        = false;                                                   // Additional method invocation ( string/boolean | method name or false )

        // Auxilliary variables
        private $post_type_slug              = 'terminology';

        // Default option values ( for easy ext class overrides )
        public $terminology_layout       = 'no_sidebar';
        public $terminology_sidebar      = 'sidebar-default';
        public $terminology_colorset     = 'foo';
        public $terminology_title        = 1;
        public $terminology_subtitle     = 1;
        public $terminology_mediadisplay = 1;
        public $terminology_categories   = 1;
        public $terminology_author       = 1;
        public $terminology_date         = 1;
        public $terminology_urlrewrite   = 'terminology';

        public function __construct() {

            // Create basic post type object
            $posttype_obj = $this->register_post_type();

            // Add taxonomies to object
            $posttype_obj = $this->add_taxonomies( $posttype_obj );

            // Make client and type columns sortable
            $posttype_obj->sortable( array( 'group' => array( 'group', true ) ) );

            // Single terminology post Theme Options
            add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 140);

            // Single terminology post Metaboxes. Hook on 'plethora_metabox_add' filter
            add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));       

        }

        public function register_post_type() {


            // Get user defined url rewrite option
            $rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX .'terminology-urlrewrite', 'terminology' );
            // Names
            $names = array(

                'post_type_name' =>  $this->post_type_slug, // Carefull...this must be filled with custom post type's slug
                'slug'           =>  $this->post_type_slug, 
                'menu_item_name' =>  esc_html__('Terminology', 'plethora-framework'),
                'singular'       =>  esc_html__('Terminology', 'plethora-framework'),
                'plural'         =>  esc_html__('Terminologies', 'plethora-framework'),
                'new_item'       =>  esc_html__('Add New Term', 'plethora-framework'),

            );

            // Options
            $options = array(

                'enter_title_here'      => 'Term', // Title prompt text 
                'description'           => '',  // A short descriptive summary of what the post type is. 
                'public'                => true,        // Whether a post type is intended to be used publicly either via the admin interface or by front-end users (default: false)
                'exclude_from_search'   => true,        // Whether to exclude posts with this post type from front end search results ( default: value of the opposite of the public argument)
                'publicly_queryable'    => true,        // Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
                'show_ui'               => true,        // Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
                'show_in_nav_menus'     => true,        // Whether post_type is available for selection in navigation menus ( default: value of public argument )
                'show_in_menu'          => true,        // Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
                'show_in_admin_bar'     => true,        // Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
                'menu_position'         => 5,           // The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
                'menu_icon'             => 'dashicons-lightbulb', // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
                'hierarchical'          => false,       // Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
                // 'taxonomies'             => array(),     // An array of registered taxonomies like category or post_tag that will be used with this post type. This can be used in lieu of calling register_taxonomy_for_object_type() directly. Custom taxonomies still need to be registered with register_taxonomy(). 
                'has_archive'           => false,       // Enables post type archives. Will use $post_type as archive slug by default (default: false)
                'query_var'             => true,        // Sets the query_var key for this post type.  (Default: true - set to $post_type )
                'can_export'            => true,        // Can this post_type be exported. ( Default: true )
                'supports'              => array( 
                                                'title', 
                                                'editor', 
                                                'author',    
                                                'thumbnail',    
                                                'excerpt',   
                                                // 'trackbacks',    
                                                // 'custom-fields',     
                                                // 'comments',  
                                                // 'revisions',     
                                                // 'page-attributes',   
                                                // 'post-formats'   
                                             ), // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
                'rewrite'               => array( 
                                                'slug'      => sanitize_key( $rewrite ), // string: Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable, that's why we use _x
                                                'with_front'=> true,        // bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
                                                // 'feeds'      => true,    // bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
                                                // 'pages'      => true,    // bool: Should the permalink structure provide for pagination. Defaults to true 
                                             )  // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )
            );

            // Hooks to apply
            $names      = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_names', $names );
            $options    = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_options', $options );
            // Create the post type
            $posttype_obj = new Plethora_Posttype( $names, $options );
            return $posttype_obj;
        }

        function add_taxonomies( $posttype_obj ) {

            // TAXONOMY LABELS
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

            // TAXONOMY OPTIONS
            $options = array(
     
                'labels'            => $labels,
                'public'            => true,    // (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
                'show_ui'           => true,    // (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
                'show_in_nav_menus' => true,    // (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
                'show_tagcloud'     => false,   // (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
                'show_admin_column' => true,    // (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
                'hierarchical'      => false,   // (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
                'query_var'         => true,    // (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
                // 'sort'               => true,    // (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
                'rewrite'           => array( 
                                        'slug'          => 'term-topic', // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
                                        'with_front'    => true,    // allowing permalinks to be prepended with front base - defaults to true 
                                        'hierarchical'  => true,    // true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
                                       ),       // (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )

            );

            // Register Taxonomy
            $options    = apply_filters( 'plethora_posttype_taxonomy_term-topic_options', $options );
            $posttype_obj->register_taxonomy( 'term-topic', $options );
            return $posttype_obj;
        }

        /** 
        * Returns theme options configuration. Collects global and theme-specific fields
        * Hooked @ 'plethora_themeoptions_content'
        */
        public function single_themeoptions( $sections ){

            // Get permanent theme options configuration
            $fields_global  = $this->single_themeoptions_global();

            // Get theme-specific theme options configuration
            $fields_theme = $this->single_themeoptions_ext();

            // Merge fields and return $sections
            $fields = array_merge_recursive( $fields_global, $fields_theme );

            $sections[] = array(
                'title'      => esc_html__('Terminology', 'plethora-framework'),
                'heading' => esc_html__('SINGLE TERMINOLOGY POSTS OPTIONS', 'plethora-framework'),
                'desc'      => esc_html__('These will be the default values for a new post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
                'subsection' => true,
                'fields'     => $fields
            );
            return $sections;
        }

        /** 
        * Returns global fields for the theme options configuration
        */
        public function single_themeoptions_global() {

            $fields = array(
                    array(
                        'id'      =>  METAOPTION_PREFIX .'terminology-layout',
                        'title'   => esc_html__( 'Select Layout', 'plethora-framework' ),
                        'type'    => 'image_select',
                        'default' => $this->terminology_layout,
                        'options' => Plethora_Module_Style::get_options_array( array( 
                                                                                    'type'   => 'page_layouts',
                                                                                    'use_in' => 'redux',
                                                                               )
                                     ),
                        ),
                    array(
                        'id'       => METAOPTION_PREFIX .'terminology-sidebar',
                        'type'     => 'select',
                        'required' => array(METAOPTION_PREFIX .'terminology-layout','equals',array('right_sidebar','left_sidebar')),  
                        'data'     => 'sidebars',
                        'multi'    => false,
                        'default'  => $this->terminology_sidebar,
                        'title'    => esc_html__('Select Sidebar', 'plethora-framework'), 
                        ),
                    array(
                        'id'      => METAOPTION_PREFIX .'terminology-colorset',
                        'type'    => 'button_set',
                        'title'   => esc_html__( 'Content Section Color Set', 'plethora-framework' ),
                        'desc'    => esc_html__( 'Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
                        'default' => $this->terminology_colorset,
                        'options' => Plethora_Module_Style::get_options_array( array( 'type'            => 'color_sets',
                                                                                      'use_in'          => 'redux',
                                                                                      'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
                    ),
                    array(
                        'id'      => METAOPTION_PREFIX .'terminology-title',
                        'type'    => 'switch', 
                        'title'   => esc_html__('Display title', 'plethora-framework'),
                        'default' => $this->terminology_title,
                        ),  
                    array(
                        'id'=> METAOPTION_PREFIX .'terminology-subtitle',
                        'type' => 'switch', 
                        'title' => esc_html__('Display subtitle', 'plethora-framework'),
                        'default' => $this->terminology_subtitle,
                        ),  
                    array(
                        'id'      => METAOPTION_PREFIX .'terminology-mediadisplay',
                        'type'    => 'switch', 
                        'title'   => esc_html__('Display Featured Image', 'plethora-framework'),
                        'default' => $this->terminology_mediadisplay,
                        ),  
                    array(
                        'id'      => METAOPTION_PREFIX .'terminology-categories',
                        'type'    => 'switch', 
                        'title'   => esc_html__('Display Topics Info', 'plethora-framework'),
                        "default" => $this->terminology_categories,
                        ),  
                    array(
                        'id'      => METAOPTION_PREFIX .'terminology-author',
                        'type'    => 'switch', 
                        'title'   => esc_html__('Display Author Info', 'plethora-framework'),
                        "default" => $this->terminology_author,
                        ),  
                    array(
                        'id'      => METAOPTION_PREFIX .'terminology-date',
                        'type'    => 'switch', 
                        'title'   => esc_html__('Display Date Info', 'plethora-framework'),
                        "default" => $this->terminology_date,
                        ),  
                    array(
                        'id'       => THEMEOPTION_PREFIX .'terminology-urlrewrite',
                        'type'     => 'text',
                        'title'    => esc_html__('URL Rewrite', 'plethora-framework'), 
                        'desc'     => esc_html__('Specify a custom permalink for terminology posts ( i.e.: http://yoursite.com/<b>terminology</b>/sample-terminology). <br><span style="color:red">NOTICE: Updating this will probably result a 404 page error on every terminology post. This can be easily fixed with a simple click on "Save Changes" button, on the <b>Settings > Permalinks</b> screen</span>', 'plethora-framework'),
                        'default'  => $this->terminology_urlrewrite,
                        ),
                );

            return $fields;
        }

        /** 
        * Returns theme-specific fields for the theme options configuration
        * Do not use in this class, for extension classes only
        */
        public function single_themeoptions_ext() { return array(); }

        /** 
        * Returns single options configuration. Collects global and theme-specific fields
        * Hooked @ 'plethora_metabox_add'
        */
        public function single_metabox( $metaboxes ) {

            // Get permanent theme options configuration
            $fields_global  = $this->single_metabox_global();

            // Get theme-specific theme options configuration
            $fields_theme = $this->single_metabox_ext();

            // Merge fields and return $sections
            $fields = array_merge_recursive( $fields_global, $fields_theme );
            $sections_content = array(
                'title'      =>esc_html__('Content', 'plethora-framework'),
                'heading' =>esc_html__('CONTENT OPTIONS', 'plethora-framework'),
                'icon_class' => 'icon-large',
                'icon'       => 'el-icon-lines',
                'fields'     => $fields
            );

            $sections = array();
            $sections[] = $sections_content;
            if ( has_filter( 'plethora_metabox_singleterminology') ) {

                $sections = apply_filters( 'plethora_metabox_singleterminology', $sections );
            }

            $metaboxes[] = array(
                'id'            => 'metabox-single-terminology',
                'title'         => esc_html__( 'Page Options', 'plethora-framework' ),
                'post_types'    => array( 'terminology'),
                'position'      => 'normal', // normal, advanced, side
                'priority'      => 'default', // high, core, default, low
                'sections'      => $sections,
            );

            return $metaboxes;
        }

        /** 
        * Returns global fields for the single options configuration
        */
        public function single_metabox_global() {

            $fields = array(

                    array(
                        'id'      =>  METAOPTION_PREFIX .'terminology-layout',
                        'title'   =>esc_html__( 'Select Layout', 'plethora-framework' ),
                        'type'    => 'image_select',
                        'options' => Plethora_Module_Style::get_options_array( array( 
                                                                                    'type'   => 'page_layouts',
                                                                                    'use_in' => 'redux',
                                                                               )
                                     ),
                        ),
                    array(
                        'id'       => METAOPTION_PREFIX .'terminology-sidebar',
                        'type'     => 'select',
                        'required' => array(METAOPTION_PREFIX .'terminology-layout','equals',array('right_sidebar','left_sidebar')),  
                        'data'     => 'sidebars',
                        'multi'    => false,
                        'title'    =>esc_html__('Select Sidebar', 'plethora-framework'), 
                        ),
                    array(
                        'id'      => METAOPTION_PREFIX .'terminology-colorset',
                        'type'    => 'button_set',
                        'title'   =>esc_html__( 'Content Section Color Set', 'plethora-framework' ),
                        'desc'    =>esc_html__( 'Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
                        'options' => Plethora_Module_Style::get_options_array( array( 'type'            => 'color_sets',
                                                                                      'use_in'          => 'redux',
                                                                                      'prepend_options' => array(  'foo' =>esc_html__('Default', 'plethora-framework') ) ) ),
                    ),
                    array(
                        'id'    => METAOPTION_PREFIX .'terminology-title',
                        'type'  => 'switch', 
                        'title' =>esc_html__('Display Title', 'plethora-framework'),
                        ),  
                    array(
                        'id'    => METAOPTION_PREFIX .'terminology-subtitle',
                        'type'  => 'switch', 
                        'title' =>esc_html__('Display Subtitle', 'plethora-framework'),
                        ),  
                    array(
                        'id'        => METAOPTION_PREFIX .'terminology-subtitle-text',
                        'type'      => 'text',
                        'title'     =>esc_html__('Subtitle Text', 'plethora-framework'),
                        'translate' => true,
                    ),
                    array(
                        'id'    => METAOPTION_PREFIX .'terminology-mediadisplay',
                        'type'  => 'switch', 
                        'title' => esc_html__('Display Featured Image', 'plethora-framework'),
                        ),  
                    array(
                        'id'    => METAOPTION_PREFIX .'terminology-categories',
                        'type'  => 'switch', 
                        'title' => esc_html__('Display Topics Info', 'plethora-framework'),
                        ),  
                    array(
                        'id'    => METAOPTION_PREFIX .'terminology-author',
                        'type'  => 'switch', 
                        'title' => esc_html__('Display Author Info', 'plethora-framework'),
                        ),  
                    array(
                        'id'    => METAOPTION_PREFIX .'terminology-date',
                        'type'  => 'switch', 
                        'title' => esc_html__('Display Date Info', 'plethora-framework'),
                        ),  
            );

            return $fields;
        }

        /** 
        * Returns theme-specific fields for the single metabox configuration
        * Do not use in this class, for extension classes only
        */
        public function single_metabox_ext() { return array(); }

    }
}   
