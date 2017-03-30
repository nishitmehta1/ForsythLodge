<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Testimonial Post Type Feature Class
Hooks > Filters

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Testimonial') ) {  
 
    /**
     * @package Plethora Framework
     */

    class Plethora_Posttype_Testimonial {

        /*** SETUP: Configure your Custom Post Type here ***/

        public static $feature_title         = "Testimonial";                                         // FEATURE DISPLAY TITLE  (STRING)
        public static $feature_description   = "Contains all testimonial related post configuration"; // FEATURE DISPLAY DESCRIPTION (STRING)
        public static $feature_icon          = "dashicons-format-chat";                               // SIDEBAR ICON [https://developer.wordpress.org/resource/dashicons/]
        public static $theme_option_control  = true;                                                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
        public static $theme_option_default  = true;                                                  // DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
        public static $theme_option_requires = array();                                               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? array( $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                                                  // DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
        public static $dynamic_method        = false;                                                 // ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE )
        // Auxilliary post type variables
        public $post_type_obj;
        public $post_type_slug    = 'testimonial';
        public $post_type_rewrite;                              // should be replaced with the user given value
        public $post_type_label_singular;                       // should be replaced with a gettext string                                         
        public $post_type_label_plural;                         // should be replaced with a gettext string                                     
        public $post_type_supports = array( 'title', 'editor' );
        // Auxilliary taxonomy variables
        public $tax_slug          = 'testimonial-category';
        public $tax_rewrite       = 'testimonial-category';     // can be replaced with the user given value
        public $tax_label_singular;                             // should be updated with a gettext string
        public $tax_label_plural;                               // should be replaced with a gettext string
        /*** SETUP ***/

        public function __construct() {

            // Create basic post type object
            if ( !empty( $this->post_type_slug ) ) {

                $this->post_type_rewrite        = Plethora_Theme::option( THEMEOPTION_PREFIX . $this->post_type_slug . '-urlrewrite', $this->post_type_slug );
                $this->post_type_label_singular = esc_html__( 'Testimonial', 'plethora-framework' );
                $this->post_type_label_plural   = esc_html__( 'Testimonials', 'plethora-framework' );
                $options = array(

                    'enter_title_here'      => $this->post_type_label_singular . ' title', // TITLE PROMPT TEXT 
                    'description'           => '',                              // SHORT POST TYPE DESCRIPTION 
                    'public'                => false,                            // AVAILABLE FOR PUBLICLY FOR FRONT-END OR ADMIN INTERFACE ONLY (default: false)
                    'exclude_from_search'   => true,                            // EXCLUDE CPT POSTS FROM FRONT END SEARCH RESULTS ( default: value of the opposite of the public argument)
                    'publicly_queryable'    => false,                            // Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
                    'show_ui'               => true,                            // Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
                    'show_in_nav_menus'     => false,                            // Whether post_type is available for selection in navigation menus ( default: value of public argument )
                    'show_in_menu'          => true,                            // Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
                    'show_in_admin_bar'     => true,                            // Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
                    'menu_position'         => 5,                               // The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
                    'menu_icon'             => 'dashicons-format-chat',             // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
                    'hierarchical'          => false,                           // Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
                    'has_archive'           => false,                            // Enables post type archives. Will use $post_type as archive slug by default (default: false)
                    'query_var'             => true,                            // Sets the query_var key for this post type.  (Default: true - set to $post_type )
                    'can_export'            => true,                            // Can this post_type be exported. ( Default: true )
                 // 'taxonomies'            => array(),                         // An array of registered taxonomies like category or post_tag that will be used with this post type. This can be used in lieu of calling register_taxonomy_for_object_type() directly. Custom taxonomies still need to be registered with register_taxonomy(). 
                    'supports'              => $this->post_type_supports,
                    'rewrite'               => array( 
                                                    'slug'      => sanitize_key( $this->post_type_rewrite ), // string: Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable, that's why we use _x
                                                    'with_front'=> false,        // bool: Should the permalink structure be prepended with the front base. (example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/). Defaults to true
                                                    // 'feeds'      => true,    // bool: Should a feed permalink structure be built for this post type. Defaults to has_archive value.
                                                    // 'pages'      => true,    // bool: Should the permalink structure provide for pagination. Defaults to true 
                                                 )  // Triggers the handling of rewrites for this post type. To prevent rewrites, set to false. (Default: true and use $post_type as slug )
                );

                $this->post_type_obj   = $this->register_post_type( $options );

           }

            // Add taxonomy to object
            if ( !empty( $this->post_type_obj ) && !empty( $this->tax_slug ) ) {

                $this->tax_rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX . $this->post_type_slug . '-'. $this->tax_slug .'-urlrewrite', $this->tax_rewrite );
                $this->tax_label_singular = esc_html__( 'Testimonial Category', 'plethora-framework' );
                $this->tax_label_plural   = esc_html__( 'Testimonial Categories', 'plethora-framework' );
                $options = array(
         
                    'public'            => false,    // (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
                    'show_ui'           => true,    // (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
                    'show_in_nav_menus' => false,   // (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
                    'show_tagcloud'     => false,   // (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
                    'show_admin_column' => true,    // (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
                    'hierarchical'      => false,   // (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
                    'query_var'         => true,    // (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
                    'sort'              => true,    // (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
                    'rewrite'           => array( 
                                            'slug'          => sanitize_key( $this->tax_rewrite ), // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
                                            'with_front'    => false,    // allowing permalinks to be prepended with front base - defaults to true 
                                            // 'hierarchical'  => true,    // true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
                                           ),       // (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
                );
                $this->add_post_type_taxonomy( $this->post_type_obj, $options );
            }

            // Single testimonial Metabox    
            add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));   

        }

        public function register_post_type( $options ) {

            // GET USER DEFINED URL REWRITE OPTION
            $names   = array(

                'post_type_name' =>  $this->post_type_slug, 
                'slug'           =>  $this->post_type_slug, 
                'menu_item_name' =>  sprintf( '%s', $this->post_type_label_plural ), 
                'singular'       =>  sprintf( esc_html__( '%s Item', 'plethora-framework' ), $this->post_type_label_singular ), 
                'plural'         =>  sprintf( '%s', $this->post_type_label_plural ), 

            );
            // Hooks
            $names         = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_names', $names );
            $options       = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_options', $options );
            $post_type_obj = new Plethora_Posttype( $names, $options );     // CREATE THE POST TYPE

            // Return object
            return $post_type_obj;
        }

        function add_post_type_taxonomy( $post_type_obj, $options ) {


            // TAXONOMY LABELS ( should be added to $options )
            $labels = array(

                'name'                       => sprintf( '%s', $this->tax_label_plural ),
                'singular_name'              => sprintf( '%s', $this->tax_label_singular ),
                'menu_name'                  => sprintf( '%s', $this->tax_label_plural ),
                'all_items'                  => sprintf( esc_html__( 'All %s', 'plethora-framework' ), $this->tax_label_plural ),
                'edit_item'                  => sprintf( esc_html__( 'Edit %s', 'plethora-framework' ), $this->tax_label_singular ),
                'view_item'                  => sprintf( esc_html__( 'View %s', 'plethora-framework' ), $this->tax_label_singular ),
                'update_item'                => sprintf( esc_html__( 'Update %s', 'plethora-framework' ), $this->tax_label_singular ),
                'add_new_item'               => sprintf( esc_html__( 'Add New %s', 'plethora-framework' ), $this->tax_label_singular ),
                'new_item_name'              => sprintf( esc_html__( 'New %s Name', 'plethora-framework' ), $this->tax_label_singular ),
                'parent_item'                => sprintf( esc_html__( 'Parent %s', 'plethora-framework' ), $this->tax_label_singular ),
                'parent_item_colon'          => sprintf( esc_html__( 'Parent %s:', 'plethora-framework' ), $this->tax_label_singular ),
                'search_items'               => sprintf( esc_html__( 'Search %s', 'plethora-framework' ), $this->tax_label_plural ),     
                'popular_items'              => sprintf( esc_html__( 'Popular %s', 'plethora-framework' ), $this->tax_label_plural ),
                'separate_items_with_commas' => sprintf( esc_html__( 'Seperate %s with commas', 'plethora-framework' ), $this->tax_label_plural ),
                'add_or_remove_items'        => sprintf( esc_html__( 'Add or remove %s', 'plethora-framework' ), $this->tax_label_plural ),
                'choose_from_most_used'      => sprintf( esc_html__( 'Choose from most used %s', 'plethora-framework' ), $this->tax_label_plural ),
                'not_found'                  => sprintf( esc_html__( 'No %s found', 'plethora-framework' ), $this->tax_label_plural )

            );
            $options['labels'] = $labels;

            // Hooks
            $options    = apply_filters( 'plethora_posttype_taxonomy_'.$this->tax_slug.'_options', $options );
            $this->post_type_obj->register_taxonomy( $this->tax_slug, $options );
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
            if ( has_filter( 'plethora_metabox_singletestimonial') ) {

                $sections = apply_filters( 'plethora_metabox_singletestimonial', $sections );
            }

            $metaboxes[] = array(
                'id'            => 'metabox-single-testimonial',
                'title'         => esc_html__( 'Testimonial Options', 'plethora-framework' ),
                'post_types'    => array( 'testimonial' ),
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

            $single_options['person-name'] = array(
                  'id'        => METAOPTION_PREFIX .'testimonial-person-name',
                  'type'      => 'text', 
                  'title'     => esc_html__('Author Name', 'plethora-framework'),
                  'desc'      => esc_html__('The name of the person who gave this testimonial', 'plethora-framework'),
                  'translate' => true,
            );
            $single_options['person-role'] = array(
                  'id'        => METAOPTION_PREFIX .'testimonial-person-role',
                  'type'      => 'text', 
                  'title'     => esc_html__('Author Role', 'plethora-framework'),
                  'desc'      => esc_html__('The role of the person who gave this testimonial', 'plethora-framework'),
                  'translate' => true,
            );

            return $single_options;
        }
    }
}   
