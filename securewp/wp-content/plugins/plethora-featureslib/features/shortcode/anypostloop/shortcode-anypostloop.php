<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Handles any posts Grid & Slider shortcodes

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS


if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Anypostloop') ):

	/**
	* @package Plethora Framework
	*/
	class Plethora_Shortcode_Anypostloop extends Plethora_Shortcode { 

		public static $feature_title         = 'Post Loop Shortcodes';       // Feature display title  (string)
		public static $feature_description   = 'Manages all posts Grid & Slider shortcodes ( "Posts Grid", "Posts Slider", etc )';                              // Feature display description (string)
		public static $theme_option_control  = true;                            // Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;                            // Default activation option status ( boolean )
		public static $theme_option_requires = array();                         // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = false;                            // Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;                           // Additional method invocation ( string/boolean | method name or false )
		public static $assets                = array(
			array( 'script' => array( 'plethora-isotope' ) ), // had to use plethora version, due to VC conflicts
			array( 'script' => array( 'tweenmax' ) ), 
			array( 'script' => array( 'svgloader-snap', 'svgloader' ) ),  
			array( 'script' => array( 'owlcarousel2')),       // Scripts files - wp_enqueue_script
			array( 'style'  => array( 'owlcarousel2-theme')), // Style files - wp_register_style
			array( 'style'  => array( 'owlcarousel2')),       // Style files - wp_register_style
		);
		// HELPER CLASS VARIABLES
		public $wp_slug;				// the shortcode slug
		public $post_type;				// post type object
		public $non_public_post_types = array();	// Includes non public post type slug ( used for Plethora_Theme::get_supported_post_types() call )
		public $types;					// supported types information
		public $enqueues;				// script & styles enqueues
		public $templates;				// available templates information
		public $default_param_values;	// default parameter values 
		public $display_patterns;		// display patterns index
		public $sc_type;				// shortcode type ( grid or slider )
		public $sc_name;				// shortcode name               
		public $sc_desc;				// shortcode description 


		public function __construct( $post_type_obj, $type = 'grid' ) {

			// Set basic shortcode info
			$this->sc_type   = $type;
			$this->post_type = $post_type_obj;

			// If post type is not public, set the $non_public_post_types variable
			if ( ! $post_type_obj->public ) {

				$this->non_public_post_types = array( $this->post_type->name );
			}
			if ( $this->sc_type === 'slider' ) {

				$this->wp_slug   = SHORTCODES_PREFIX . 'slider_'. $this->post_type->name;
				$this->sc_name   = sprintf( esc_html__('%1$s Slider', 'plethora-framework'), $this->post_type->labels->name );
				$this->sc_desc   = sprintf( esc_html__('Create a slider with %1$s items', 'plethora-framework'), $this->post_type->labels->singular_name );

			} else {

				$this->wp_slug   = SHORTCODES_PREFIX . 'loop_'. $this->post_type->name;
				$this->sc_name   = sprintf( esc_html__('%1$s Grid', 'plethora-framework'), $this->post_type->labels->name );
				$this->sc_desc   = sprintf( esc_html__('Create a grid/masonry/list with %1$s items', 'plethora-framework'), $this->post_type->labels->singular_name );
			}

			
			$this->display_patterns     = $this->set_display_patterns();		// Set display patterns
			$this->types                = $this->set_types(); 					// Set supported types
			$this->templates            = $this->set_templates(); 				// Set supported templates ( on admin_init, for Plethora_WP::get_file_contents() to work )
			$this->init();														// Initialize mapping

			// Add CALLBACK filters for autocomplete fields
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_posts_selection_callback', array( $this, 'search_data_posts' ), 10 );
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_posts_exclude_callback', array( $this, 'search_data_posts' ), 10 );
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_tax_include_callback', array( $this, 'search_data_tax' ), 10 );
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_tax_exclude_callback', array( $this, 'search_data_tax' ), 10 );
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_filterbar_tax_exclude_callback', array( $this, 'search_data_tax' ), 10 );

			// Add RENDER filters for autocomplete fields
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_posts_selection_render', array( $this, 'render_data_posts' ), 10 );
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_posts_exclude_render', array( $this, 'render_data_posts' ), 10 );
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_tax_include_render', array( $this, 'render_data_tax' ), 10 );
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_tax_exclude_render', array( $this, 'render_data_tax' ), 10 );
			add_filter( 'vc_autocomplete_'. $this->wp_slug.'_filterbar_tax_exclude_render', array( $this, 'render_data_tax' ), 10 );

			// Add scripts/styles
		}

		public function add_to_plethora_features( $features ) {

			if ( !empty( $features['shortcode-anypostloop'] ) ) {

				$add_feature['slug']                = $this->wp_slug;
				$add_feature['feature_title']       = $this->sc_name;
				$add_feature['feature_description'] = $this->sc_desc;
				$add_feature['wp_slug']             = $this->sc_type === 'slider' ? 'slider_'. $this->post_type->name : 'loop_'. $this->post_type->name;
				// all the rest should be filled accordng to master 'anypostloop' mapping
				$master_option                       = $features['shortcode-anypostloop'];
				$add_feature['location']             = $master_option['location'];
				$add_feature['controller']           = $master_option['controller'];
				$add_feature['class']                = $master_option['class'];
				$add_feature['folder']               = $master_option['folder'];
				$add_feature['path']                 = $master_option['path'];
				$add_feature['plethora_supported']   = $master_option['plethora_supported'];
				$add_feature['base_class']           = $master_option['base_class'];
				$add_feature['base_path']            = $master_option['base_path'];
				$add_feature['verified']             = $master_option['verified'];
				$add_feature['theme_option_control'] = $master_option['theme_option_control'];
				$add_feature['theme_option_default'] = $master_option['theme_option_default'];
				$add_feature['dynamic_construct']    = $master_option['dynamic_construct'];
				$add_feature['dynamic_method']       = $master_option['dynamic_method'];
				$add_feature['theme_option_status']  = $master_option['theme_option_status'];
				$add_feature['assets']               = $master_option['assets'];
				// add shortcode mapping to $features
				$key            = $this->sc_type === 'slider' ? 'slider_'. $this->post_type->name : 'loop_'. $this->post_type->name  ;
				$features[$key] = $add_feature;
				ksort( $features );
			}
			return $features;
		}
		/** 
		* Set supported loop types
		* @return array
		*/
		public function set_types() {

			if ( $this->sc_type === 'slider' ) {

				$types = array( 
							esc_html__('Slider View', 'plethora-framework') => 'slider',
				);
			} else {

				$types = array( 
							esc_html__('Grid View', 'plethora-framework')    => 'grid',
							esc_html__('Masonry View', 'plethora-framework') => 'masonry',
							esc_html__('List View', 'plethora-framework')    => 'list'
				);

			}
			return $types;
		}

		/** 
		* Scan installation for files following the naming pattern: shortcode-postsgrid-{type}-{style}.php
		* @return array
		*/
		public function set_templates() {

			if ( ! is_admin() ) { return array();  } // avoid overhead for front end

			$types                      = $this->types;
			$this->child_templates_dir  = PLE_CHILD_TEMPLATES_DIR . '/shortcodes';
			$this->parent_templates_dir = PLE_THEME_TEMPLATES_DIR . '/shortcodes';
			$templates_list            = array();
			// We got the full contents of both dirs. Now we filter the results
			foreach ( $types as $type_label => $type ) {

				$type_templates_list = $this->get_template_files_by_type( $type, $type_label );

				if ( !empty( $type_templates_list ) ) {

					$templates_list = array_merge_recursive( $templates_list, $type_templates_list );
				}
			}

			$templates = $this->set_templates_default( $templates_list );
			return $templates;
		}

		/** 
		* Returns templates list with default template configuration
		* @return array
		*/
		public function set_templates_default( $templates_list ) {

			$templates           = array();
			$default_mark        = 'default';
			$default_mark_length = strlen( $default_mark );
			$count_templates     = 0;
			foreach ( $templates_list as $slug => $template_found ) {
			
				$count_templates++;
				$template_found['default'] = false; // will remain true only if this is the default ( marked with 'default' OR first value if default mark is mising )

				$default_mark_start = strlen( $slug ) - $default_mark_length;
				if ( substr( $slug, $default_mark_start, $default_mark_length) === $default_mark ) {

					$template_found['default'] = true;
					$template_found['title']   = rtrim( $template_found['title'], ucwords( $default_mark ) ) .'( DEFAULT )';         
				}
				$templates[$slug] = $template_found;
			}

			return $templates;
		}

		/** 
		* Returns all template files ( with priority to child theme template, if exists )
		* @return array
		*/
		public function get_all_template_files() {

			// To avoid double scans, we should save/check class variables
			if ( !empty( $this->all_template_files ) ) { return $this->all_template_files; }

			// if child exists, check its templates folder first
			$child_scandir = array();
			if ( is_child_theme() && file_exists( $this->child_templates_dir ) ) {

				$child_scandir = scandir( $this->child_templates_dir );
				$child_scandir = $child_scandir !== false ? $child_scandir : array();
			}

			// check parent templates now...
			$parent_scandir = array();
			if ( file_exists( $this->parent_templates_dir ) ) {

				$parent_scandir = scandir( $this->parent_templates_dir );
				$parent_scandir = $parent_scandir !== false ? $parent_scandir : array();
			}

			$this->all_template_files = array_merge( $parent_scandir, $child_scandir );
			return $this->all_template_files;
		}

		/** 
		* Returns all template files ( with priority to child theme template, if exists )
		* @return array
		*/
		public function get_template_files_by_type( $type, $type_label ) {

			$template_files_by_type = array();
			if ( empty( $type ) ) { return $template_files_by_type; }

			foreach ( $this->get_all_template_files() as $key => $template_file ) {

				$full_filepath_parent = $this->parent_templates_dir .'/'. $template_file;
				$full_filepath_child = $this->child_templates_dir .'/'. $template_file;
				$full_filepath = is_file( $full_filepath_child ) ? $full_filepath_child : $full_filepath_parent;
				if ( is_file( $full_filepath ) && !is_dir( $full_filepath ) ) {

					$template_file = basename( $template_file );

					// get global templates ( will appear in all loop shortcodes )
					$global_templates_prefix = 'loop_' . $type .'-anypost-';
					$string_length           = strlen( $global_templates_prefix );
					if ( substr( $template_file, 0, $string_length) === $global_templates_prefix ) {
			  
						$locate_template                                            = str_replace( $global_templates_prefix, '', $template_file );
						$locate_template                                            = rtrim($locate_template, '.php');
						$template_label                                             = ucwords( str_replace('-', ' ', $locate_template) );
						$template_slug                                              = $type .'-anypost-'. $locate_template;
						$template_files_by_type[$template_slug]['slug']               = $template_slug;
						$template_files_by_type[$template_slug]['title']              = $template_label;
						$template_files_by_type[$template_slug]['display_type_title'] = $type;
						$template_files_by_type[$template_slug]['type_title']         = $type_label;
						$template_files_by_type[$template_slug]['post_type']          = 'anypost';
						$template_files_by_type[$template_slug]['params_supported']   = $this->get_supported_template_params( $template_slug );
					}

					// get post templates ( will appear only on selected loop shortcode )
					$posttype_templates_pattern = 'loop_' . $type .'-'. $this->post_type->name .'-';
					$string_length              = strlen( $posttype_templates_pattern );
					if ( substr( $template_file, 0, $string_length) === $posttype_templates_pattern ) {

						$locate_template                                            = str_replace( $posttype_templates_pattern, '', $template_file );
						$locate_template                                            = rtrim($locate_template, '.php');
						$template_label                                             = ucwords( str_replace('-', ' ', $locate_template) );
						$template_slug                                              = $type .'-'. $this->post_type->name .'-'. $locate_template;
						$template_files_by_type[$template_slug]['slug']               = $template_slug;
						$template_files_by_type[$template_slug]['title']              = $template_label;
						$template_files_by_type[$template_slug]['display_type']       = $type;
						$template_files_by_type[$template_slug]['display_type_title'] = $type_label;
						$template_files_by_type[$template_slug]['post_type']          = $this->post_type->name;
						$template_files_by_type[$template_slug]['params_supported']   = $this->get_supported_template_params( $template_slug );
					}
				}
			}

			return $template_files_by_type;
		}

		/** 
		* All default values for shortcode parameters
		* Built like this, for easier override on extension classes
		* @return array
		*/
		public function params_config() {

			$params_config[] = array( 'id' => 'data', 							'default' => $this->post_type->name, 	'field_size' => '' );
			$params_config[] = array( 'id' => 'items_template', 				'default' => $this->get_default_template(), 'field_size' => '6', );
			$params_config[] = array( 'id' => 'filterbar', 						'default' => '0', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'el_class', 						'default' => '', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'ref_id', 						'default' => uniqid(), 					'field_size' => '6', );
			
			$params_config[] = array( 'id' => 'data_tax_include', 				'default' => '', 						'field_size' => '', ); 
			$params_config[] = array( 'id' => 'data_tax_exclude', 				'default' => '', 						'field_size' => '', );
			$params_config[] = array( 'id' => 'data_posts_selection', 			'default' => '', 						'field_size' => '', );
			$params_config[] = array( 'id' => 'data_posts_exclude', 			'default' => '', 						'field_size' => '', );
			$params_config[] = array( 'id' => 'data_posts_per_page', 			'default' => '12', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'data_offset', 					'default' => '', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'data_orderby', 					'default' => 'date', 					'field_size' => '6', );
			$params_config[] = array( 'id' => 'data_orderby_metakey', 			'default' => '', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'data_order', 					'default' => 'DESC',					'field_size' => '6', );
			
			$params_config[] = array( 'id' => 'items_per_row', 					'default' => 'col-md-4 col-sm-6', 		'field_size' => '', );
			$params_config[] = array( 'id' => 'items_display_pattern', 			'default' => '', 						'field_size' => '', );
			$params_config[] = array( 'id' => 'isotope_transitionduration', 	'default' => '500', 					'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_gutter', 					'default' => '', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_featuredmedia', 			'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_media_ratio', 				'default' => '', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_hover_transparency', 		'default' => '', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_title', 					'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_subtitle', 				'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_excerpt', 					'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_excerpt_trim', 			'default' => '15', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_editorcontent', 			'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_date', 					'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_author', 					'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_commentscount', 			'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_primarytax', 				'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_primarytax_slug', 			'default' => 'category', 				'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_secondarytax', 			'default' => '0', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_secondarytax_slug', 		'default' => 'post_tag', 				'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_link_behavior', 			'default' => 'normal', 					'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_link_label', 				'default' => esc_html__('Read More', 'plethora-framework' ), 'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_woo_price', 				'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_woo_addtocart', 			'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_woo_saleicon', 			'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_testimonial_author', 		'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_testimonial_author_role',	'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_booking_target_price', 	'default' => '4', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_colorset', 				'default' => '', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_room_amenities',			'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_room_amenities_max',		'default' => '3', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_socials', 					'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'items_extraclass',				'default' => '', 						'field_size' => '6', );

			$params_config[] = array( 'id' => 'autoplay', 						'default' => '1', 						'field_size' => '12', );
			$params_config[] = array( 'id' => 'autoplaytimeout', 				'default' => '5000', 					'field_size' => '6', );
			$params_config[] = array( 'id' => 'loop', 							'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'autoplayhoverpause', 			'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'dots', 							'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'nav',							'default' => '0', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'rtl', 							'default' => '0', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'margin',							'default' => '0', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'responsive_xs',					'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'responsive_sm',					'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'responsive_md',					'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'responsive_lg',					'default' => '1', 						'field_size' => '6', );



			$params_config[] = array( 'id' => 'filterbar_tax', 					'default' => 'category', 				'field_size' => '', );
			$params_config[] = array( 'id' => 'filterbar_tax_exclude', 			'default' => '', 						'field_size' => '', );
			$params_config[] = array( 'id' => 'filterbar_orderby', 				'default' => 'ASC', 					'field_size' => '6', );
			$params_config[] = array( 'id' => 'filterbar_order', 				'default' => '1', 						'field_size' => '6', );
			$params_config[] = array( 'id' => 'filterbar_resettitle', 			'default' => esc_html__('Show All', 'plethora-framework'), 'field_size' => '', );
			$params_config[] = array( 'id' => 'css', 							'default' => '', 						'field_size' => '', );

			return $params_config;
		}


		/** 
		* Performs a regex search for mustache values used in template and returns supported fields index       
		* @return array
		*/
		public function get_supported_template_params( $template ) {

			if ( empty( $template ) ) { return array(); }

			ob_start();
			get_template_part( 'templates/shortcodes/loop_'. $template );
			$body = ob_get_clean();

			$field_to_values_mapping = array(
				'items_featuredmedia'           => array( 'item_media' ) ,
				'items_media_ratio'             => array( 'items_media_ratio' ) ,
				'items_hover_transparency'      => array( 'items_hover_transparency' ) ,
				'items_title'                   => array( 'item_title' ) ,
				'items_subtitle'                => array( 'item_subtitle' ) ,
				'items_excerpt'                 => array( 'item_excerpt' ) ,
				'items_editorcontent'           => array( 'item_editorcontent' ) ,                   
				'items_date'                    => array( 'item_date_day_num', 'item_date_day_txt', 'item_date_month_num', 'item_date_month_txt', 'item_date_year_abr', 'item_date_year_full' ) ,
				'items_author'                  => array( 'item_author_name' , 'item_author_link' ),
				'items_commentscount'           => array( 'item_author_link', 'item_author_name' ),
				'items_primarytax'              => array( 'item_primarytax_terms' ),
				'items_secondarytax'            => array( 'item_secondarytax_terms' ),
				'items_link_behavior'           => array( 'item_link', 'item_link_target', 'item_link_class' ),
				'items_link_label'           	=> array( 'item_link_label' ),
				'items_woo_price'               => array( 'item_woo_price' ),
				'items_woo_addtocart'           => array( 'item_woo_addtocart_url', 'item_woo_addtocart_text' ),
				'items_woo_saleicon'            => array( 'item_woo_saleicon_class', 'item_woo_saleicon_text' ),
				'items_socials'                 => array( 'item_socials' ),
				'items_testimonial_author'      => array( 'item_testimonial_author' ),
				'items_testimonial_author_role' => array( 'item_testimonial_author_role' ),
				'items_booking_target_price'    => array( 'item_target_price_text', 'item_target_price_text_before', 'item_target_price_text_after' ),
				'items_testimonial_author_role' => array( 'item_testimonial_author_role' ),
				'items_colorset'                => array( 'item_colorset' ),
				'items_room_amenities'          => array( 'item_room_amenities' ),
			);

			$supported_fields = array();
			foreach ( $field_to_values_mapping  as $field => $template_values_search ) {

				foreach ( $template_values_search as $value_search ) { 

					$pattern = '/\b';
					$pattern .= $value_search;
					$pattern .= '\b/';
					preg_match( $pattern, $body, $matches );
					if ( !empty( $matches ) ) {

						$supported_fields[] = $field;
					}
				}
			}
			return array_unique( $supported_fields );
		}

		/** 
		* Registers the shortcode configuration
		*
		* @return array
		* @since 1.0
		*
		*/
		public function init() {

			// Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
			$map = array( 
				'base'              => $this->wp_slug,
				'name'              => $this->sc_name,
				'description'       => $this->sc_desc,
				'class'             => '',
				'weight'            => 1,
				'category'          => esc_html__('Plethora Shortcodes', 'plethora-framework'),
				'admin_enqueue_js'  => array(), 
				'admin_enqueue_css' => array(),
				'icon'              => $this->vc_icon(), 
				// 'custom_markup'     => $this->vc_custom_markup( 'Profiles Grid' ), 
				'params'            => $this->params(), 
			);

			$this->add( $map );
			/* 
			Add this shortcode to 'plethora_features' mapping. This action is necessary
			if we want to use the automatic assets enqueueing     
			*/
			add_filter('plethora_features', array( $this, 'add_to_plethora_features') );
		}

		/** 
		* Set display patterns for use with grid/masonry layout types
		* @return array
		*/
		public function set_display_patterns() {

			$patterns['pattern_1'] = array(  
				'id'          => 'pattern_1',
				'title'       => esc_html__( '1st row: 6+6 | 2nd row: 12 | 3rd row: 6+6', 'plethora-framework' ),
				'col_classes' => array(
									'col-sm-6',
									'col-sm-6',
									'col-sm-12',
									'col-sm-6',
									'col-sm-6'
				)
			);
			$patterns['pattern_2'] = array(  
				'id'          => 'pattern_2',
				'title'       => esc_html__( '1st row: 4+4+4 | 2nd row: 6+6 | 3rd row: 4+4+4', 'plethora-framework' ),
				'col_classes' => array(
									'col-md-4 col-sm-6',
									'col-md-4 col-sm-6',
									'col-md-4 col-sm-6',
									'col-sm-6',
									'col-sm-6',
									'col-md-4 col-sm-6',
									'col-md-4 col-sm-6',
									'col-md-4 col-sm-6'
				)
			);

			return $patterns;
		}

		/** 
		* Returns display patterns for parameter use
		* @return array
		*/
		public function get_display_patterns_param_values() {

			// get patterns defined in set_display_patterns() method
			$display_patterns = $this->display_patterns;

			// set default option
			$default_title                = esc_html__( 'No special display pattern', 'plethora-framework' );
			$param_values[$default_title] = '';

			// add pattern options
			$count = 0;
			foreach ( $display_patterns as $pattern ) {

				$count++;
				$title                = sprintf("%02d", $count) .'. '. $pattern['title'];
				$value                = $pattern['id'];
				$param_values[$title] = $value;
			}
			return $param_values;
		}

		/** 
		* Returns shortcode settings (compatible with Visual composer)
		*
		* @return array
		* @since 1.0
		*
		*/
		public function get_display_pattern( $pattern_id ) {

			// get patterns defined in set_display_patterns() method
			$display_patterns = $this->display_patterns;

			return !empty( $display_patterns[$pattern_id]['col_classes'] ) ? $display_patterns[$pattern_id]['col_classes'] : array();
		}

	   /** 
	   * Returns shortcode settings (compatible with Visual composer)
	   *
	   * @return array
	   * @since 1.0
	   *
	   */
	   public function params_index() {

			$params['data'] = array(
				'param_name'  => 'data',
				'type'        => 'dropdown',
				'heading'     => esc_html__('Data Source', 'plethora-framework'),
				'description' => esc_html__('Select if you want to get all items automatically, or you want a custom selection. Check "Data" tab for more options.', 'plethora-framework'),
				'value'       => $this->get_datasource_options(),
			);

			$params['items_template'] = array(
				'param_name'  => 'items_template',
				'type'        => 'dropdown',
				'heading'     => sprintf( esc_html__('%s Template', 'plethora-framework'), Plethora_Theme::mb_ucfirst( $this->sc_type ) ),
				'description' => sprintf( esc_html__('Select %s template to be applied. Check "Items Styling" tab for more options.', 'plethora-framework'), $this->sc_type ),
				'value'       => $this->get_supported_templates(),
				'save_always' => true,
			);                  
			$params['filterbar'] = array(
				'param_name'  => 'filterbar',
				'type'        => 'checkbox',
				'heading'     => esc_html__('Filter Bar', 'plethora-framework'),
				'description' => esc_html__('Enabled bar for filtering displayed items. Check "Filter" tab for more options.', 'plethora-framework'),
				'value' => array( __( 'Yes', 'plethora-framework' ) => 1 ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'grid', 'masonry' ), 
					))
				)
			);                  
			$params['el_class'] = array(
				'param_name'  => 'el_class',
				'type'        => 'textfield',
				'heading'     => esc_html__('Extra Class', 'plethora-framework'),
				'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
			);                  
			$params['ref_id'] = array(
				'param_name'  => 'ref_id',
				'type'        => 'textfield',
				'heading'     => esc_html__('Reference ID', 'plethora-framework'),
				'description' => esc_html__('Leave the default value, or create a unique one for this element. Leaving this empty or identical with other instances of this shorcode in same page, will cause display issues.', 'plethora-framework'),
				'save_always' => true,
				'value'       => $this->get_default_param_value( 'ref_id' ),
			);                  
			// DATA TAB STARTS >>>>
			$params['data_tax_include'] = array(
				'param_name'  => 'data_tax_include',
				'type'        => 'autocomplete',
				'group'       => esc_html__('Data', 'plethora-framework'),                                              
				'heading'     => sprintf( esc_html__('Filter %1$s By Term', 'plethora-framework'), $this->post_type->label ) ,
				'description' => sprintf( esc_html__('Filter results by specific %1$s taxonomy term(s). Leave empty to show all.', 'plethora-framework'), $this->post_type->labels->singular_name ) ,
				'settings'    => array(
					'multiple'       => true,
					'min_length'     => 1,
					'groups'         => true,
					'display_inline' => true,
					'delay'          => 500,
					'auto_focus'     => true,
					'sortable'       => false,
				),
				'dependency'    => array( 
					'element' => 'data', 
					'value'   => array_values( Plethora_Theme::get_supported_post_types( array( 'include' => $this->non_public_post_types ) ) ),  
				)
			);                  
			$params['data_tax_exclude'] = array(
				'param_name'  => 'data_tax_exclude',
				'type'        => 'autocomplete',
				'group'       => esc_html__('Data', 'plethora-framework'),                                              
				'heading'     => sprintf( esc_html__('Exclude %1$s By Term', 'plethora-framework'), $this->post_type->label ) ,
				'description' => sprintf( esc_html__('Exclude results by specific %1$s taxonomy term(s). Leave empty to show all.', 'plethora-framework'), $this->post_type->labels->singular_name ) ,
				'settings'    => array(
					'multiple'       => true,
					'min_length'     => 1,
					'groups'         => true,
					'display_inline' => true,
					'delay'          => 500,
					'auto_focus'     => true,
					'sortable'       => false,
				),
				'dependency'    => array( 
					'element' => 'data', 
					'value'   => array_values( Plethora_Theme::get_supported_post_types( array( 'include' => $this->non_public_post_types ) ) ),  
				)
			);                  
			$params['data_posts_selection'] = array(
				'param_name'  => 'data_posts_selection',
				'type'        => 'autocomplete',
				'group'       => esc_html__('Data', 'plethora-framework'),                                              
				'heading'     => sprintf( esc_html__('%1$s Selection', 'plethora-framework'), $this->post_type->label ) ,
				'description' => sprintf( esc_html__('Add %1$s by title', 'plethora-framework'), $this->post_type->labels->name ),
				'settings'    => array(
					'multiple'       => true,
					'min_length'     => 2,
					'groups'         => true,
					'unique_values'  => true,
					'display_inline' => false,
					'delay'          => 500,
					'auto_focus'     => true,
					'sortable'       => true,
				),
				'dependency'    => array( 
					'element' => 'data', 
					'value'   => array( $this->post_type->name .'_selection' ),  
				)
			);                  
			$params['data_posts_exclude'] = array(
				'param_name'  => 'data_posts_exclude',
				'type'        => 'autocomplete',
				'group'       => esc_html__('Data', 'plethora-framework'),                                              
				'heading'     => sprintf( esc_html__('Exclude Specific %1$s', 'plethora-framework'), $this->post_type->label ) ,
				'description' => sprintf( esc_html__('Exclude %1$s by title', 'plethora-framework'), $this->post_type->labels->name ),
				'settings'    => array(
					'multiple'       => true,
					'min_length'     => 2,
					'groups'         => true,
					'unique_values'  => true,
					'display_inline' => false,
					'delay'          => 500,
					'auto_focus'     => true,
					'sortable'       => false,
				),
				'dependency'  => array( 
					'element' => 'data', 
					'value'   => array_values( Plethora_Theme::get_supported_post_types( array( 'include' => $this->non_public_post_types ) ) ),  
				)
			);                  
			$params['data_posts_per_page'] = array(
				'param_name'       => 'data_posts_per_page',
				'type'             => 'textfield',
				'group'            => esc_html__('Data', 'plethora-framework'),                                              
				'heading'          => esc_html__('Items Limit', 'plethora-framework'),
				'description'      => esc_html__('Set max limit for displayed items or enter -1 to display all.', 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'data', 
					'value'   => array_values( Plethora_Theme::get_supported_post_types( array( 'include' => $this->non_public_post_types ) ) ),  
				)
			);                  
			$params['data_offset'] = array(
				'param_name'       => 'data_offset',
				'type'             => 'textfield',
				'group'            => esc_html__('Data', 'plethora-framework'),                                              
				'heading'          => esc_html__('Offset', 'plethora-framework'),
				'description'      => esc_html__('Number of items to displace or pass over ( ignored if there is no items limit ).', 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'data', 
					'value'   => array_values( Plethora_Theme::get_supported_post_types( array( 'include' => $this->non_public_post_types ) ) ),  
				)
			);                  
			$params['data_orderby'] = array(
				'param_name'       => 'data_orderby',
				'type'             => 'dropdown',
				'group'            => esc_html__('Data', 'plethora-framework'),                                              
				'heading'          => esc_html__('Order By', 'plethora-framework'),
				'description'      => esc_html__('Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'plethora-framework'),
				'value'            => array( 
					esc_html__('Date', 'plethora-framework')                                                      => 'date',
					esc_html__('Post ID', 'plethora-framework')                                                   => 'ID',
					esc_html__('Author', 'plethora-framework')                                                    => 'author',
					esc_html__('Title', 'plethora-framework')                                                     => 'title',
					esc_html__('Last Modified Date', 'plethora-framework')                                        => 'modified',
					esc_html__('Post/Page Parent ID', 'plethora-framework')                                       => 'parent',
					esc_html__('Comments Number', 'plethora-framework')                                           => 'comment_count',
					esc_html__('Menu/Page Order', 'plethora-framework')                                           => 'menu_order',
					esc_html__('Meta Value', 'plethora-framework')                                                => 'meta_value',
					esc_html__('Meta Value (Number)', 'plethora-framework')                                       => 'meta_value_num',
					esc_html__('Random Order', 'plethora-framework')                                              => 'rand',
					esc_html__('Posts Selection Order ( only for custom posts selection )', 'plethora-framework') => 'post__in',
				)
			);                  
			$params['data_order'] = array(
				'param_name'       => 'data_order',
				'type'             => 'dropdown',
				'group'            => esc_html__('Data', 'plethora-framework'),                                              
				'heading'          => esc_html__('Sort Order', 'plethora-framework'),
				'description'      => esc_html__('Select descending/ascending order', 'plethora-framework'),
				'value'            => array( 
					esc_html__('Descending', 'plethora-framework') => 'DESC',
					esc_html__('Ascending', 'plethora-framework')  => 'ASC',
				)
			);                  
			$params['data_orderby_metakey'] = array(
				'param_name'  => 'data_orderby_metakey',
				'type'        => 'textfield',
				'group'       => esc_html__('Data', 'plethora-framework'),                                              
				'heading'     => esc_html__('Order By: Meta Key', 'plethora-framework'),
				'description' => esc_html__('Input meta key for items ordering.', 'plethora-framework'),
				'dependency'  => array( 
					'element' => 'data_orderby', 
					'value'   => array( 'meta_value', 'meta_value_num' ),  
				)
			);                  
			// <<<< DATA TAB ENDS

			// DISPLAY ITEMS TAB STARTS >>>>
			$params['items_per_row'] = array(
				'param_name'  => 'items_per_row',
				'type'        => 'dropdown',
				'group'       => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'     => esc_html__('Items Per Row', 'plethora-framework'),
				'description' => esc_html__('Select number of single items ( columns ) per row.', 'plethora-framework'),
				'value'       => array( 
					esc_html__('6 items', 'plethora-framework') => 'grid_sizer col-md-2 col-sm-4',
					esc_html__('4 items', 'plethora-framework') => 'grid_sizer col-md-3 col-sm-4',
					esc_html__('3 items', 'plethora-framework') => 'grid_sizer col-md-4 col-sm-6',
					esc_html__('2 items', 'plethora-framework') => 'grid_sizer col-md-6 col-sm-6',
					esc_html__('1 item', 'plethora-framework')  => 'grid_sizer col-md-12 col-sm-12',
				),
				'dependency'   => array( 
					'element' => 'items_template', 
					'value' => $this->get_templates_dependency( array( 'display_type' => array( 'grid', 'masonry' ) ) )
				)
			);                  
			$params['items_display_pattern'] = array(
				'param_name'  => 'items_display_pattern',
				'type'        => 'dropdown',
				'group'       => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'     => esc_html__('Special Display Pattern', 'plethora-framework'),
				'description' => esc_html__('Apply a special display pattern only for the first rows of items. Numbers in pattern titles represent item sizes, i.e 6+6 displays 2 equal sized items in row, while 4+4+4 displays 3 equal sized items in row. After the special display pattern, the rest items will be displayed according to "Items per row" value, set above.', 'plethora-framework'),
				'value'       => $this->get_display_patterns_param_values(),
				'dependency'  => array( 
					'element' => 'items_template', 
					'value' => $this->get_templates_dependency( array( 'display_type' => array( 'grid', 'masonry' ) ) )
				)
			);
			$params['isotope_transitionduration'] = array(
				'param_name'       => 'isotope_transitionduration',
				'type'             => 'textfield',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Transition Duration', 'plethora-framework'),
				'description'      => esc_html__('Set duration, in milliseconds, for the grid/masonry transitions.', 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'grid', 'masonry' ), 
					))
				)
			);
			$params['items_gutter'] = array(
				'param_name'       => 'items_gutter',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Remove Gutter', 'plethora-framework'),
				'description'      => esc_html__('Will remove blank space between items', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => 'no_gutter' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'grid', 'masonry' ), 
					))
				)
			);
			$params['items_featuredmedia'] = array(
				'param_name'       => 'items_featuredmedia',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Featured Image/Media', 'plethora-framework'),
				'description'      => esc_html__('Will display featured image or any other Plethora featured media set for each post', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 'param_name' => 'items_featuredmedia' ) )
				)
			);
			$params['items_media_ratio'] = array(
				'param_name'       => 'items_media_ratio',
				'type'             => 'dropdown',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Featured Image Box Ratio', 'plethora-framework'),
				'description'      => esc_html__('Choose a ratio for all images on the grid', 'plethora-framework'),
				"value"            => Plethora_Module_Style_Ext::get_options_array( array( 
					'type'            => 'stretchy_ratios', 
					'use_in'          => 'vc',
					'prepend_default' => true,
					'default_title'   => esc_html__( 'Native ratio', 'plethora-framework')
				)),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 'param_name' => 'items_media_ratio' ) )
				)
			);
			$params['items_hover_transparency'] = array(
				'param_name'       => 'items_hover_transparency',
				'type'             => 'dropdown',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Items Hover Transparency', 'plethora-framework'),
				'description'      => esc_html__('Choose the transparency style on hover.', 'plethora-framework'),
				'value'            => array( 
					esc_html__('No Transparency', 'plethora-framework')   => '',
					esc_html__('Transparent Film', 'plethora-framework')  => 'transparent_film',
					esc_html__('Fully Transparent', 'plethora-framework') => 'transparent',
				),
				'dependency'   => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'grid', 'masonry' ),
						'param_name'   => 'items_hover_transparency',
					) )
				)
			);
			$params['items_title'] = array(
				'param_name'       => 'items_title',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Title', 'plethora-framework'),
				'description'      => esc_html__('Will display post title', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_title',
					) )
				)
			);
			$params['items_subtitle'] = array(
				'param_name'       => 'items_subtitle',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Subtitle', 'plethora-framework'),
				'description'      => esc_html__('Will display post subtitle.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_subtitle',
					) )
				)
			);
			$params['items_excerpt'] = array(
				'param_name'       => 'items_excerpt',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Excerpt', 'plethora-framework'),
				'description'      => esc_html__('Will display post excerpt.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_excerpt',
					) )
				)
			);
			$params['items_excerpt_trim'] = array(
				'param_name'       => 'items_excerpt_trim',
				'type'             => 'textfield',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Trim Excerpt', 'plethora-framework'),
				'description'      => esc_html__('Trims excerpt text to a certain number of words ( default is 55, even if left empty or 0 ) ', 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'items_excerpt', 
					'value'   => array( '1' ),  
				)
			);
			$params['items_editorcontent'] = array(
				'param_name'       => 'items_editorcontent',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Editor Content', 'plethora-framework'),
				'description'      => esc_html__('Will display post editor content.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_editorcontent',
					) )
				)
			);

			$params['items_date'] = array(
				'param_name'       => 'items_date',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Date', 'plethora-framework'),
				'description'      => esc_html__('Will display post creation date.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_date',
					) )
				)
			);
			$params['items_author'] = array(
				'param_name'       => 'items_author',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Author', 'plethora-framework'),
				'description'      => esc_html__('Will display post author.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_author',
					) )
				)
			);
			$params['items_commentscount'] = array(
				'param_name'       => 'items_commentscount',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Comments Count', 'plethora-framework'),
				'description'      => esc_html__('Will display post comments number.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_commentscount',
					) )
				)
			);
			$params['items_primarytax'] = array(
				'param_name'       => 'items_primarytax',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Primary Taxonomy Terms', 'plethora-framework'),
				'description'      => esc_html__('Will display primary taxonomy terms. Enable this to set primary taxonomy.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_primarytax',
					) )
				)
			);
			$params['items_primarytax_slug'] = array(
				'param_name'       => 'items_primarytax_slug',
				'type'             => 'dropdown',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Primary Taxonomy', 'plethora-framework'),
				'description'      => esc_html__('Select primary taxonomy to be displayed', 'plethora-framework'),
				'value'            => $this->get_supported_taxonomies(),
				'dependency'       => array( 
					'element' => 'items_primarytax', 
					'value'   => array( '1' ),  
				)
			);
			$params['items_secondarytax'] = array(
				'param_name'       => 'items_secondarytax',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Secondary Taxonomy Terms', 'plethora-framework'),
				'description'      => esc_html__('Will display secondary taxonomy terms. Enable this to set secondary taxonomy.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_secondarytax',
					) )
				)
			);
			$params['items_secondarytax_slug'] = array(
				'param_name'       => 'items_secondarytax_slug',
				'type'             => 'dropdown',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Secondary Taxonomy', 'plethora-framework'),
				'description'      => esc_html__('Select secondary taxonomy to be displayed', 'plethora-framework'),
				'value'            => $this->get_supported_taxonomies(),
				'dependency'       => array( 
					'element' => 'items_secondarytax', 
					'value'   => array( '1' ),  
				)
			);

			$params['items_link_behavior'] = array(
				'param_name'       => 'items_link_behavior',
				'type'             => 'dropdown',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Link Behavior', 'plethora-framework'),
				'description'      => esc_html__('Select secondary taxonomy to be displayed', 'plethora-framework'),
				'value'            => array( 
					sprintf( esc_html__('Link to %1$s page', 'plethora-framework'), mb_strtolower( $this->post_type->labels->singular_name ) )                 => 'normal',
					sprintf( esc_html__('Link to %1$s page, in new tab', 'plethora-framework'), mb_strtolower( $this->post_type->labels->singular_name ) )     => 'blank',
					sprintf( esc_html__('Link to %1$s page, in ajax window', 'plethora-framework'), mb_strtolower( $this->post_type->labels->singular_name ) ) => 'ajax',
					sprintf( esc_html__('Do not link', 'plethora-framework'), mb_strtolower( $this->post_type->labels->singular_name ) )                       => 'no_link',
				),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_link_behavior',
					) )
				)
			);

			$params['items_link_label'] = array(
				'param_name'  => 'items_link_label',
				'type'        => 'textfield',
				'group'       => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'     => esc_html__('Link Label', 'plethora-framework'),
				'description' => esc_html__('For button or other link label use', 'plethora-framework'),
				'dependency'  => array( 
						'element' => 'items_template', 
						'value'   => $this->get_templates_dependency( array( 
							'param_name' => 'items_link_label',
						) 
					)
				)
			);

			$params['items_colorset'] = array(
				'param_name'       => 'items_colorset',
				'type'             => 'dropdown',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Color Set', 'plethora-framework'),
				'description'      => esc_html__('Select color set configuration', 'plethora-framework'),
              	'value'            => Plethora_Module_Style::get_options_array( array( 
						'type'   => 'color_sets', 
						'use_in' => 'vc',
						'prepend_default' => true,
					)
              	),
				'dependency'       => array( 
						'element' => 'items_template', 
						'value'   => $this->get_templates_dependency( array( 
							'param_name' => 'items_colorset',
						) 
					)
				)
			);

				# SPECIAL CPT FIELDS: WOOCOMMERCE PRODUCTS / PRICE               
			$params['items_woo_price'] = array(
				'param_name'       => 'items_woo_price',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Price ( Woo Only )', 'plethora-framework'),
				'description'      => esc_html__('Will display price.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'post_type'  => array( 'product' ),
						'param_name' => 'items_woo_price',
					) )
				)
			);
				# SPECIAL CPT FIELDS: WOOCOMMERCE PRODUCTS / CART BUTTON               
			$params['items_woo_addtocart'] = array(
				'param_name'       => 'items_woo_addtocart',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Cart Button ( Woo Only )', 'plethora-framework'),
				'description'      => esc_html__('Will display "Add To Cart" button.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'post_type'  => array( 'product' ),
						'param_name' => 'items_woo_addtocart',
					) )
				)
			);

				# SPECIAL CPT FIELDS: WOOCOMMERCE PRODUCTS / SALE ICON               
			$params['items_woo_saleicon'] = array(
				'param_name'       => 'items_woo_saleicon',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Display Sale Icon ( Woo Only )', 'plethora-framework'),
				'description'      => esc_html__('Will display sale icon.', 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'post_type'  => array( 'product' ),
						'param_name' => 'items_woo_saleicon',
					) )
				)
			);

				# SPECIAL CPT FIELDS: PROFILES / SOCIAL ICONS               
			$params['items_socials'] = array(
				'param_name'       => 'items_socials',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				"heading"          => esc_html__("Display Social Icons ", 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				"description"      => esc_html__("Will display related social icons for each profile post.", 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'post_type'  => array( 'profile' ),
						'param_name' => 'items_socials',
					) )
				)
			);

				# SPECIAL CPT FIELDS: TESTIMONIALS / AUTHOR               
			$params['items_testimonial_author'] = array(
				'param_name'       => 'items_testimonial_author',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				"heading"          => esc_html__("Display Testimonial Author", 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				"description"      => esc_html__("Will display author name on each testimonial post.", 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'post_type'  => array( 'testimonial' ),
						'param_name' => 'items_testimonial_author',
					) )
				)
			);

				# SPECIAL CPT FIELDS: TESTIMONIALS / AUTHOR ROLE               
			$params['items_testimonial_author_role'] = array(
				'param_name'       => 'items_testimonial_author_role',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				"heading"          => esc_html__("Display Testimonial Author Role", 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				"description"      => esc_html__("Will display author role on each testimonial post.", 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'post_type'  => array( 'testimonial' ),
						'param_name' => 'items_testimonial_author_role',
					))
				)
			);

				# SPECIAL FIELDS: TESTIMONIALS / AUTHOR ROLE               
			$params['items_booking_target_price'] = array(
				'param_name'       => 'items_booking_target_price',
				'type'             => 'dropdown',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				"heading"          => esc_html__("Display Booking Target Price", 'plethora-framework'),
				'value'            => array(  
					esc_html__( 'No', 'plethora-framework' )                               => 0,
					esc_html__( 'Price Only', 'plethora-framework' )                       => '1',
					esc_html__( 'Price + After Text', 'plethora-framework' )               => '2',
					esc_html__( 'Before Text + Price', 'plethora-framework' )              => '3', 
					esc_html__( 'Before Text + Price + After Text', 'plethora-framework' ) => '4' 
				),
				"description"      => esc_html__("Will display booking target price.", 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'param_name' => 'items_booking_target_price',
					))
				)
			);

				# SPECIAL CPT FIELDS: TESTIMONIALS / AUTHOR ROLE               
			$params['items_room_amenities'] = array(
				'param_name'       => 'items_room_amenities',
				'type'             => 'checkbox',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				"heading"          => esc_html__("Display Amenity Icons", 'plethora-framework'),
				'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				"description"      => esc_html__("Will display author role on each testimonial post.", 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'post_type'  => array( 'room' ),
						'param_name' => 'items_room_amenities',
					))
				)
			);

			$params['items_room_amenities_max'] = array(
				'param_name'       => 'items_room_amenities_max',
				'type'             => 'textfield',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				"heading"          => esc_html__("Max Amenity Icons", 'plethora-framework'),
				"description"      => esc_html__("Limit the number of icons displayed. Leave 0 or -1 to display it all", 'plethora-framework'),
				'dependency'       => array( 
					'element' => 'items_room_amenities', 
					'value'   => array( '1', 1 )
				)
			);


			$params['items_extraclass'] = array(
				'param_name'       => 'items_extraclass',
				'type'             => 'textfield',
				'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
				'heading'          => esc_html__('Item Extra Class', 'plethora-framework'),
				'description'      => esc_html__('Add an special item class name and refer to it in custom CSS.', 'plethora-framework'),
			);
			// <<<< DISPLAY ITEMS TAB ENDS

			// SLIDER OPTIONS TAB STARTS >>>>

			$params['autoplay'] = array(
				"param_name" => "autoplay",                                  
				"type"       => "dropdown",                                        
				'group'      => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"    => esc_html__("Autoplay", 'plethora-framework'),      
				"value"      => array( esc_html__("Yes", 'plethora-framework') => 1 , esc_html__("No", 'plethora-framework') => 0 ),
				'dependency' => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['autoplaytimeout'] = array(
				"param_name"       => "autoplaytimeout",                                  
				"type"             => "textfield",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Slide Change Timeout", 'plethora-framework'),      
				"description"      => esc_html__("In milliseconds, in example 5000 defines 5 seconds timeout.", 'plethora-framework'),       
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);
			$params['dropdown'] = array(
				"param_name"       => "loop",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Slider Loop", 'plethora-framework'),      
				"value"            => array( esc_html__("Yes", 'plethora-framework') => 'true' , esc_html__("No", 'plethora-framework') => 'false' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['autoplayhoverpause'] = array(
				"param_name"       => "autoplayhoverpause",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Pause On Hover", 'plethora-framework'),      
				"value"            => array( esc_html__("No", 'plethora-framework') => 'false' , esc_html__("Yes", 'plethora-framework') => 'true' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['dots'] = array(
				"param_name"       => "dots",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Dots Navigation", 'plethora-framework'),      
				"value"            => array( esc_html__("Yes", 'plethora-framework') => 'true' , esc_html__("No", 'plethora-framework') => 'false' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['nav'] = array(
				"param_name"       => "nav",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Arrows Navigation", 'plethora-framework'),      
				"value"            => array( esc_html__("No", 'plethora-framework') => 'false', esc_html__("Yes", 'plethora-framework') => 'true' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);


			$params['rtl'] = array(
				"param_name"       => "rtl",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Right To Left Placement", 'plethora-framework'),      
				"value"            => array( esc_html__("No", 'plethora-framework') => 'false' , esc_html__("Yes", 'plethora-framework') => 'true' ),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['margin'] = array(
				"param_name"       => "margin",                                  
				"type"             => "textfield",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Slide Margin", 'plethora-framework'),      
				"description"      => esc_html__("Margin in pixels for the slides ( not applied on first/last slide )", 'plethora-framework'),       
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['responsive_xs'] = array(
				"param_name"       => "responsive_xs",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Items Per Slide / 480px", 'plethora-framework'),      
				"description"      => esc_html__("Items per slide on smart mobile monitors", 'plethora-framework'),       
				"value"            => array( 
					esc_html__("1", 'plethora-framework') => 1, 
					esc_html__("2", 'plethora-framework') => 2, 
					esc_html__("3", 'plethora-framework') => 3, 
					esc_html__("4", 'plethora-framework') => 4, 
					esc_html__("5", 'plethora-framework') => 5, 
					esc_html__("6", 'plethora-framework') => 6, 
				),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['responsive_sm'] = array(
				"param_name"       => "responsive_sm",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Items Per Slide / 768px", 'plethora-framework'),      
				"description"      => esc_html__("Items per slide on tablet monitors", 'plethora-framework'),       
				"value"            => array( 
					esc_html__("1", 'plethora-framework') => 1, 
					esc_html__("2", 'plethora-framework') => 2, 
					esc_html__("3", 'plethora-framework') => 3, 
					esc_html__("4", 'plethora-framework') => 4, 
					esc_html__("5", 'plethora-framework') => 5, 
					esc_html__("6", 'plethora-framework') => 6, 
				),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['responsive_md'] = array(
				"param_name"       => "responsive_md",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Items Per Slide / 992px", 'plethora-framework'),      
				"description"      => esc_html__("Items per slide on desktop monitors", 'plethora-framework'),       
				"value"            => array( 
					esc_html__("1", 'plethora-framework') => 1, 
					esc_html__("2", 'plethora-framework') => 2, 
					esc_html__("3", 'plethora-framework') => 3, 
					esc_html__("4", 'plethora-framework') => 4, 
					esc_html__("5", 'plethora-framework') => 5, 
					esc_html__("6", 'plethora-framework') => 6, 
				),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);

			$params['responsive_lg'] = array(
				"param_name"       => "responsive_lg",                                  
				"type"             => "dropdown",                                        
				'group'            => esc_html__('Slider Options', 'plethora-framework'),                                              
				"heading"          => esc_html__("Items Per Slide / 1200px", 'plethora-framework'),      
				"description"      => esc_html__("Items per slide on wide desktop monitors", 'plethora-framework'),       
				"value"            => array( 
					esc_html__("1", 'plethora-framework') => 1, 
					esc_html__("2", 'plethora-framework') => 2, 
					esc_html__("3", 'plethora-framework') => 3, 
					esc_html__("4", 'plethora-framework') => 4, 
					esc_html__("5", 'plethora-framework') => 5, 
					esc_html__("6", 'plethora-framework') => 6, 
				),
				'dependency'       => array( 
					'element' => 'items_template', 
					'value'   => $this->get_templates_dependency( array( 
						'display_type' => array( 'slider' ),
					) )
				)
			);
			// <<<< SLIDER OPTIONS TAB  TAB ENDS


			// FILTER BAR TAB STARTS >>>>
			$params['filterbar_tax'] = array(
				'param_name'  => 'filterbar_tax',
				'type'        => 'dropdown',
				'group'       => esc_html__('Filter Bar', 'plethora-framework'),                                              
				'heading'     => esc_html__('Term Filters By Taxonomy', 'plethora-framework'),
				'description' => esc_html__('Select category, tags or custom taxonomy. The selected taxonomy terms will be displayed as selections in the filters bar. Note that the terms should be associating with the displayed post type data source.', 'plethora-framework'),
				'value'       => $this->get_supported_taxonomies(),
				'dependency'  => array( 
					'element' => 'filterbar', 
					'value'   => array( '1' ),  
				)
			);
			$params['filterbar_tax_exclude'] = array(
				'param_name'  => 'filterbar_tax_exclude',
				'type'        => 'autocomplete',
				'group'       => esc_html__('Filter Bar', 'plethora-framework'),                                              
				'heading'     => esc_html__('Exclude Terms From Filters', 'plethora-framework'),
				'description' => esc_html__('Exclude specific categories, tags or custom terms from filter bar.', 'plethora-framework'),
				'settings'    => array(
					'multiple'       => true,
					'min_length'     => 1,
					'groups'         => true,
					'display_inline' => true,
					'delay'          => 500,
					'auto_focus'     => true,
					'sortable'       => false,
				),
				'dependency'  => array( 
					'element' => 'filterbar', 
					'value'   => array( '1' ),  
				)
			);

			$params['filterbar_orderby'] = array(
				'param_name'       => 'filterbar_orderby',
				'type'             => 'dropdown',
				'group'            => esc_html__('Filter Bar', 'plethora-framework'),                                              
				'heading'          => esc_html__('Order Filters By', 'plethora-framework'),
				'description'      => esc_html__('Select filter order type.', 'plethora-framework'),
				'value'            => array( 
					esc_html__('Name', 'plethora-framework')                                       => 'name',
					esc_html__('Description', 'plethora-framework')                                => 'description',
					esc_html__('Count ( how many posts include this term )', 'plethora-framework') => 'count',
					esc_html__('Term ID', 'plethora-framework')                                    => 'term_id',
				),
				'dependency'  => array( 
					'element' => 'filterbar', 
					'value'   => array( '1' ),  
				)
			);

			$params['filterbar_order'] = array(
				'param_name'       => 'filterbar_order',
				'type'             => 'dropdown',
				'group'            => esc_html__('Filter Bar', 'plethora-framework'),                                              
				'heading'          => esc_html__('Sort Filters Order', 'plethora-framework'),
				'description'      => esc_html__('Select descending/ascending order', 'plethora-framework'),
				'value'            => array( 
												esc_html__('Descending', 'plethora-framework') => 'DESC',
												esc_html__('Ascending', 'plethora-framework')  => 'ASC',
											),
				'dependency' 	   => array( 
					'element' => 'filterbar', 
					'value'   => array( '1' ),  
				)
			);

			$params['filterbar_resettitle'] = array(
				'param_name'  => 'filterbar_resettitle',
				'type'        => 'textfield',
				'group'       => esc_html__('Filter Bar', 'plethora-framework'),                                              
				'heading'     => esc_html__('Reset Button Title', 'plethora-framework'),
				'description' => esc_html__('Set title for the reset button. If empty, button will not be displayed.', 'plethora-framework'),
				'dependency'  => array( 
					'element' => 'filterbar', 
					'value'   => array( '1' ),  
				)
			);
			// <<<< FILTER BAR TAB ENDS

			// DESIGN OPTIONS TAB STARTS >>>>
			$params['css'] = array(
				'param_name' => 'css',
				'type'       => 'css_editor',
				'group'      => esc_html__('Design Options', 'plethora-framework'),                                              
				'heading'    => esc_html__('Design Options', 'plethora-framework'),
			);
			// <<<< DESIGN OPTIONS TAB ENDS
			return $params;
		}

		/** 
		* Returns data source option values
		* @return array
		*/
		public function get_datasource_options() {

			$values                      = array();
			$all_posts_label             = sprintf( esc_html__( 'All %1$s', 'plethora-framework' ), $this->post_type->label );  
			$custom_posts_label          = sprintf( esc_html__( '%1$s selection', 'plethora-framework' ), $this->post_type->label );  
			$values[$all_posts_label]    = $this->post_type->name;
			$values[$custom_posts_label] = $this->post_type->name .'_selection';
			return $values;
		}

		/** 
		* Returns supported taxonomies option value
		* @return array
		*/
		public function get_supported_taxonomies() {

			$values = array();
			$taxonomies = get_object_taxonomies( $this->post_type->name, 'objects' );
			foreach ( $taxonomies as $tax_slug => $tax_obj ) {
			
				$values[$tax_obj->label .' ( '.$this->post_type->label .' )'] = $tax_slug;
			}
			$values = !empty( $values ) ? array_unique($values) : array();
			return $values;
		}

		/** 
		* Returns supported templates option value, according to template parts found on child/parent theme
		* @return array
		*/
		public function get_supported_types() {

			$types  = $this->types;
			$values = array();
			foreach ( $types as $label => $value ) {
			
				$values[$label] = $value;
			}
			return $values;
		}

		/** 
		* Returns supported templates option value, according to template parts found on child/parent theme
		* @return array
		*/
		public function get_supported_templates() {

			$templates  = $this->templates;
			foreach ( $templates as $slug => $attrs ) {
			
				$label                             = $attrs['title'];
				$type_label                        = $attrs['display_type_title'];
				$values[$type_label .': '. $label] = $slug;
			}
			$values = !empty( $values ) ? array_unique( $values ) : array( esc_html__( 'No templates found!', 'plethora-framework' ) => 'foofoo' );
			return $values;
		}

		/** 
		* Returns templates according to post_type, display_type or control field value
		* Is used to return templates for param 'dependency' attribute
		* @return array
		*/
		public function get_templates_dependency( $args = array() ) {

			$default_args = array( 
				  'post_type'    => array(),   // post type slug // will return all templates according to post type ( accepts multiple post type slugs )
				  'display_type' => array(),   // 'grid'|'masonry'|'list'|'slider' // will return all templates according to display type ( accepts multiple display types )
				  'param_name'   => '',        // supported param name // will return  return all templates according to supported parameter ( accepts single param name )
			);
			$args = wp_parse_args( $args, $default_args); // Merge user given arguments with default
			extract( $args );
			$templates                = $this->templates;
			$templates_dependency     = array();
			$templates_by_posttype    = array();
			$templates_by_displaytype = array();
			$templates_by_param       = array();
			foreach ( $templates as $slug => $template ) {

				if ( empty( $post_type ) || in_array( $template['post_type'], $post_type ) ) {

					$templates_by_posttype[] = $slug;
				}

				if ( empty( $display_type ) || in_array( $template['display_type'], $display_type ) ) {

					$templates_by_displaytype[] = $slug;
				}

				if ( empty( $param_name ) || in_array( $param_name, $template['params_supported'] ) ) {

					$templates_by_param[] = $slug;
				}
			}
			$templates_dependency = array_intersect(
				$templates_by_posttype,
				$templates_by_displaytype,
				$templates_by_param
			);

			// Use array_unique to remove duplicates AND array_values, to reset key numbering ( IMPORTANT )
			return !empty( $templates_dependency ) ? array_values( array_unique( $templates_dependency ) ) : array( 'foo' );
		}


		/** 
		* Returns default template slug value, according to template parts found on child/parent theme
		* @return array
		*/
		public function get_default_template( $type = 'grid' ) {
			
			$default_template = '';
			$templates  = $this->templates;
			if ( !empty( $templates[$type]['default'] ) ) {
			
				$default_template = $templates[$type]['default'];
			}
			return $default_template;
		}


		/** 
		* Searches and returns posts by user value ( 'Posts Selection' / 'Exclude Posts' fields )
		* Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_callback
		* @return array
		*/
		public function search_data_posts( $user_val ) {

			$values = array();
			$args   = array(
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_type'      => $this->post_type->name,
			);
			$posts = get_posts( $args );
			foreach ( $posts as $post ) {

				// note: stripos MUST be checked against false with the identical comparison operator !==
				if ( stripos( $post->post_title, $user_val ) !== false || stripos( $post->post_name, $user_val ) !== false || stripos( $posttype->label, $user_val ) !== false || stripos( $posttype->name, $user_val ) !== false ) {
					$values[] = array(
						'label' => $post->post_title, 
						'value' => $post->ID, 
						'group' => $post->post_type,
					);
				}
			}
			wp_reset_postdata();
			return $values;
		}

		/** 
		* Renders saved posts selection ( 'Posts Selection' / 'Exclude Posts' fields )
		* Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_render
		* @return mixed ( array, bool )
		*/
		public function render_data_posts( $value ) {
		
			$post = get_post( $value['value'] );
			return is_null( $post ) ? false : array(
				'label' => $post->post_title,
				'value' => $post->ID,
				'group' => $post->post_type,
			);
		}

		/** 
		* Returns taxonomy terms by user value ( 'Narrow Results By Term' field )
		* Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_callback
		* @return array
		*/
		public function search_data_tax( $user_val ) {

			$values = array();
			$post_taxonomies = get_object_taxonomies( $this->post_type->name, 'objects' );
			foreach ( $post_taxonomies as $tax_slug => $tax_obj ) {

				$post_taxonomy_terms = get_terms( $tax_slug, array( 'hide_empty' => false ) );
				if ( ! is_wp_error( $post_taxonomy_terms ) ) {
					
					foreach ( $post_taxonomy_terms as $term  ) {
						// note: stripos MUST be checked against false with the identical comparison operator !==
						if ( stripos( $term->name, $user_val ) !== false || stripos( $term->slug, $user_val ) !== false || stripos( $tax_obj->label, $user_val ) !== false ) {
							
							$values[] = array(
								'label' => $term->name, 
								'value' => $tax_slug .'|'. $term->term_id, 
								'group' => $tax_obj->label
							);
						}
					}
				}
			}
			return $values;
		}

		/** 
		* Renders saved taxonomy terms ( 'Narrow Results By Term' field )
		* Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_render
		* @return mixed ( array, bool )
		*/
		public function render_data_tax( $saved_terms ) {
		
			$value = false;
			$post_taxonomies = get_object_taxonomies( $this->post_type->name, 'objects' );
			foreach ( $post_taxonomies as $tax_slug => $tax_obj ) {

				$saved_value = !empty( $saved_terms['value'] ) ? explode('|', $saved_terms['value'] ) : array();
				$saved_value = !empty( $saved_value[1] ) ? $saved_value[1] : array() ;
				$terms_args  = array(
					'include'    => $saved_value,
					'hide_empty' => false,
				);
				$post_taxonomy_terms = get_terms( $tax_slug, $terms_args );
				if ( is_array( $post_taxonomy_terms ) && 1 === count( $post_taxonomy_terms ) ) {

					$term  = $post_taxonomy_terms[0];
					$value = array(
						'label' => $term->name, 
						'value' => $tax_slug .'|'. $term->term_id, 
						'group' => $tax_obj->label
					);
				}
			}
			return $value;
		}

		/** 
		* Returns shortcode content
		*/
		public function content( $atts, $content = null ) {

			$atts    = $this->parse_atts( $atts );
			$return  = $this->get_filterbar_template( $atts );
			$return .= $this->get_main_template( $atts );
			$this->set_scripts( $atts );

			// CSS WRAPPER START >>>
			$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $atts['css'], ' ' ), $this->wp_slug, $atts );
			$return = '<div class="ple_anypostloop_shortcode wpb_content_element '. esc_attr( $atts['el_class'] ) .' '. esc_attr( $css_class ) .'">'. $return . '</div>';
			// CSS WRAPPER ENDS >>>
			return $return;
		}

		public function parse_atts( $atts ) {

			// EXTRACT USER INPUT
			$atts = shortcode_atts( $this->get_default_param_values(), $atts );
			// Add ID variation is $atts, for better implementation
			$atts['filterbar_id'] = 'filterbar_'. $atts['ref_id'];
			$atts['grid_id']      =  $this->get_template_file_type( $atts['items_template'] ) .'_'. $atts['ref_id'];

			/* Add item display type info in $atts ( grid, masonry, list, etc).
			Since we don't have template scan info on frontend, we have 
			to extract the items_display type using the template name */
			$atts['items_display'] = $this->get_template_file_type( $atts['items_template'] );
			return $atts;
		}

		public function get_template_file_type( $items_template ) {

			$type = '';
			$display_types = $this->types;
			foreach ( $display_types as $display_type ) {
				
				$type_len  = strlen( $display_type );
				if ( substr( $items_template, 0, $type_len ) === $display_type ) {
				
					$type = $display_type;
				}
			}
			return $type;
		}

		public function get_filterbar_template( $atts ) {

			// Render filter bar template
			if ( $atts['filterbar'] ) {

				$filterbar_atts = $this->prepare_atts_for_filterbar_template( $atts );
				return Plethora_WP::renderMustache( array( "data" => $filterbar_atts, "force_template_part" => array( 'templates/shortcodes/loop__filterbar' ) ) );
			}

			return '';
		}

		public function get_main_template( $atts ) {

			// Render main grid/list/masonry template
			if ( !empty( $atts['items_template'] ) ) {
				$main_atts = $this->prepare_atts_for_main_template( $atts );
				return Plethora_WP::renderMustache( array( "data" => $main_atts, "force_template_part" => array( 'templates/shortcodes/loop_'. $atts['items_template'] ) ) );
			}
			return '';
		}

		public function set_scripts( $atts ) {

			extract( $atts );

			// Create init script for SVG loader, if link behavior set to ajax window
			if ( $items_link_behavior === 'ajax' ) {

				Plethora_Theme::enqueue_init_script( array(
						'handle'   => 'svgloader',
						'script'   => $this->svgloader_init_script(),
						'multiple' => false
					)
				);
			}

			if ( in_array( $this->get_template_file_type( $atts['items_template'] ), array( 'grid', 'masonry' ) ) ) {
				// Create init script for Isotope
				$isotope_layoutmode = 'fitRows';
				switch ( $items_display ) {
					case 'masonry':
						$isotope_layoutmode = 'masonry';
						break;
				  
					case 'list':
						$isotope_layoutmode = 'vertical';
						break;
				}
				$isotope_attrs = array(
					'selector'                => '#'.$grid_id.'',
					'grid_layoutMode'         => ''.$isotope_layoutmode.'',
					'grid_transitionDuration' => ''.$isotope_transitionduration.'ms',
					'filterbar_selector'      => '#'.$filterbar_id.'',
				);

				Plethora_Theme::enqueue_init_script( array(
						'handle'   => 'isotope',
						'script'   => $this->isotope_init_script( $isotope_attrs ),
						'multiple' => true
					)
				);
			}
			
			// Add init script for OwlCarousel 2, if this is a slider shortcode
			if ( in_array( $this->get_template_file_type( $atts['items_template'] ), array( 'slider' ) ) ) {

				// Add init script for OwlCarousel 2
				$slider['selector']           = '#'.$grid_id.'';
				$slider['loop']               = 1;
				$slider['autoplay']           = $autoplay;
				$slider['autoplaytimeout']    = $autoplaytimeout;
				$slider['autoplayhoverpause'] = $autoplayhoverpause;
				$slider['dots']               = $dots;
				$slider['nav']                = $nav;
				$slider['rtl']                = $rtl;
				$slider['margin']             = $margin;
				$slider['responsive_xs']      = $responsive_xs;
				$slider['responsive_sm']      = $responsive_sm;
				$slider['responsive_md']      = $responsive_md;
				$slider['responsive_lg']      = $responsive_lg;

				Plethora_Theme::enqueue_init_script( array(
					'multiple' => true,
					'handle'   => 'owlcarousel2',
					'script'   => $this->init_script_owlslider( $slider )
				));
			}
		}

		/** 
		* Prepares attributes for use with main template file
		* @return array
		*/
		public function prepare_atts_for_main_template( $atts ) {

			// Set general values first
			$return['grid_id'] = $atts['grid_id'];
			$return['gutter']  = $atts['items_gutter'];
			
			// Merge with items
			$return = array_merge( $return, $this->prepare_atts_items_for_main_template( $atts ) );
			return $return;
		}

		/** 
		* Prepares item attributes for the main template file
		* @return array
		*/
		public function prepare_atts_items_for_main_template( $atts ) {

			extract( $atts );

			// Set column classes and special display patterns
			$items_per_row_class = '' ;
			if ( in_array( $atts['items_display'], array( 'grid', 'masonry') ) ) {
			
				$items_per_row_class = $atts['items_per_row'] ;
			} 

			$pattern             = $this->get_display_pattern( $items_display_pattern );
			$count_pattern_items = 0; // count for pattern classes matching

			// Query args and other config are ready...now we get the posts
			$items       = array();
			$count_items = 1; // count for all items
			$post_query  = new WP_Query( $this->prepare_atts_for_main_template_query_args( $atts ) );

			if ( $post_query->have_posts() ) {

				while ( $post_query->have_posts() ) : $post_query->the_post();

					$item_id        = get_the_id();
					$item_post_type = get_post_type();
					// Prepare main item classes first
					$item_attr_class  = '';
					$item_attr_class .= !empty( $pattern[$count_pattern_items] ) ? $pattern[$count_pattern_items] : $items_per_row_class;
					$item_attr_class .= Plethora_Theme::option( METAOPTION_PREFIX . $item_post_type .'-featured', 0, $item_id ) ? ' featured_in_loop' : '';
					
					// Prepare item data for template
					$media                            =  wp_get_attachment_image_src( get_post_thumbnail_id( $item_id ), 'large' );
					$item                             = array();
					$item['item_count']               = $count_items++;
					$item['item_id']                  = $item_id;
					$item['item_attr_class']          = $item_attr_class;
					$item['item_attr_extraclass']     = ' '. $items_extraclass;
					$item['items_media_ratio']        = $items_media_ratio;
					$item['items_hover_transparency'] = $items_hover_transparency;
					$item['item_post_type']           = $item_post_type;
					$item['item_link']                = $items_link_behavior !== 'no_link' ? get_permalink( $item_id ) : '';
					$item['item_link_target']         = $items_link_behavior === 'blank' ? '_blank' : '_parent';
					$item['item_link_class']          = $items_link_behavior === 'ajax' ? 'linkify' : '';
					$item['item_link_label']          = esc_attr( $items_link_label );
					$item['item_media']               = $items_featuredmedia && !empty( $media[0] ) ? $media[0] : '';
					$item['item_title']               = $items_title ? get_the_title() : '';
					$item['item_subtitle']            = $items_subtitle ? Plethora_Theme::option( METAOPTION_PREFIX . $this->post_type->name .'-subtitle-text', '', $item_id ) : '';
					$item['item_date_day_num']        = $items_date ? get_the_date( 'd' ) : '';
					$item['item_date_day_txt']        = $items_date ? get_the_date( 'D' ) : '';
					$item['item_date_month_num']      = $items_date ? get_the_date( 'm' ) : '';
					$item['item_date_month_txt']      = $items_date ? get_the_date( 'M' ) : '';
					$item['item_date_year_abr']       = $items_date ? get_the_date( 'y' ) : '';
					$item['item_date_year_full']      = $items_date ? get_the_date( 'Y' ) : '';
					$item['item_author_name']         = $items_author ? get_the_author() : '';
					$item['item_author_link']         = $items_author ? get_author_posts_url( get_the_author_meta( 'ID' ) ) : '';
					$item['item_comments_number']     = $items_commentscount && get_comments_number() > 0 ? get_comments_number() : '';
					$item['item_comments_link']       = $items_commentscount && get_comments_number() > 0 ? get_comments_link() : '';
					$item['item_excerpt']             = $items_excerpt ? wp_trim_words( get_the_excerpt(), $items_excerpt_trim, null ) : '';
					$item['item_editorcontent']       = $items_editorcontent ? apply_filters( 'the_content', get_the_content() ) : '';
					$item['item_colorset']            = $items_colorset;

					$count_pattern_items++;

					// All taxonomy terms for styling classes use
					$item['item_term_classes'] = array();
					$post_taxonomies           = get_object_taxonomies( $this->post_type->name );
					foreach ( $post_taxonomies as $post_taxonomy ) {

						$all_terms = get_the_terms( $item_id, $post_taxonomy );
						$all_terms = is_array( $all_terms ) ? $all_terms : array();
						foreach ( $all_terms as $term ) {

							 $item['item_term_classes'][] = array( 
								'term_class_slug'     => esc_attr( $term->slug ),
								'term_class_colorset' => esc_attr( get_term_meta( $term->term_id, TERMSMETA_PREFIX . $post_taxonomy .'-colorset', true ) )
							);
						}
					}

					// Filter bar classes
					$item['item_filter_classes'] = array();
					if ( $filterbar && !empty( $filterbar_tax ) ) {

						$filterbar_terms = get_the_terms( $item_id, $filterbar_tax );
						$filterbar_terms = is_array( $filterbar_terms ) ? $filterbar_terms : array();
						foreach ( $filterbar_terms as $term ) {

							$item['item_filter_classes'][] = array( 
								'filter_class' => 'filter_'. esc_attr( $term->slug )
							);
						}
					}

					// primary tax terms
					$item['item_primarytax_terms'] = array();
					if ( $items_primarytax && !empty( $items_primarytax_slug ) ) {

						$primarytax_terms = get_the_terms( $item_id, $items_primarytax_slug );
						$primarytax_terms = is_array( $primarytax_terms ) ? $primarytax_terms : array();
						foreach ( $primarytax_terms as $term ) {

							$item['item_primarytax_terms'][] = array( 
								'term_id'       => $term->term_id,
								'term_slug'     => $term->slug,
								'term_link'     => get_term_link( $term->term_id ),
								'term_name'     => wp_kses( $term->name, Plethora_Theme::allowed_html_for( 'paragraph' ) ),
								'term_colorset' => esc_attr( get_term_meta( $term->term_id, TERMSMETA_PREFIX . $items_primarytax_slug .'-colorset', true ) ) ,
							);
						}
					}
					// secondary tax terms
					$item['item_secondarytax_terms'] = array();
					if ( $items_secondarytax && !empty( $items_secondarytax_slug ) ) {

						$secondarytax_terms = get_the_terms( $item_id, $items_secondarytax_slug );
						$secondarytax_terms = is_array( $secondarytax_terms ) ? $secondarytax_terms : array();
						foreach ( $secondarytax_terms as $term ) {

							$item['item_secondarytax_terms'][] = array( 
								'term_id'       => $term->term_id,
								'term_slug'     => $term->slug,
								'term_link'     => get_term_link( $term->term_id ),
								'term_name'     => wp_kses( $term->name, Plethora_Theme::allowed_html_for( 'paragraph' ) ),
								'term_colorset' => esc_attr( get_term_meta( $term->term_id, TERMSMETA_PREFIX . $items_secondarytax_slug .'-colorset', true ) ) ,
							);
						}
					}

					// special woo fields
					$item['item_woo_price']          = '';
					$item['item_woo_price_currency'] = '';
					$item['item_woo_addtocart_url']  = '';
					$item['item_woo_addtocart_text'] = '';
					$item['item_woo_saleicon_class'] = '';
					$item['item_woo_saleicon_text']  = '';
					if ( class_exists( 'woocommerce' ) && $item['item_post_type'] === 'product' ) { 

						$product                         = wc_get_product( $item_id );
						$item['item_woo_price']          = $items_woo_price ? $product->get_price() : '';
						$item['item_woo_price_currency'] = $items_woo_price ? get_woocommerce_currency_symbol() : '';
						$item['item_woo_addtocart_url']  = $items_woo_addtocart ? $product->add_to_cart_url() : '';
						$item['item_woo_addtocart_text'] = $items_woo_addtocart ? $product->add_to_cart_text() : '';
						$item['item_woo_saleicon_class'] = $items_woo_saleicon && $product->is_on_sale() ? 'onsale' : '';
						$item['item_woo_saleicon_text']  = $items_woo_saleicon && $product->is_on_sale() ? __( 'Sale!', 'woocommerce' ) : '';
					}

					// special social icon fields
					$item['item_socials'] = array();
					if ( $items_socials ) {

						$socials = Plethora_Theme::option( METAOPTION_PREFIX .''.$this->post_type->name.'-social', array(), $item_id );
						if ( !empty( $socials['social_url'] ) ) {

							foreach ( $socials['social_url'] as $key => $value) {

								if ( $value != "" ){

									$item['item_socials'][] = array( 
										'social_title'      =>  $socials["social_title"][$key],
										'social_url'        =>  $socials["social_url"][$key],
										'social_icon'       =>  $socials["social_icon"][$key],
										'social_url_target' =>  '_blank',
									);
								}
							}
						}
					}
					// special testimonial CPT fields
					$item['item_testimonial_author']      =   $items_testimonial_author ?  Plethora_Theme::option( METAOPTION_PREFIX .'testimonial-person-name',  '', $item_id ) : '';
					$item['item_testimonial_author_role'] =   $items_testimonial_author ?  Plethora_Theme::option( METAOPTION_PREFIX .'testimonial-person-role',  '', $item_id ) : '';

					// special booking module price fields
					$item['item_target_price_text']        = '';
					$item['item_target_price_text_before'] = '';
					$item['item_target_price_text_after']  = '';
					if ( !empty( $items_booking_target_price ) && method_exists( 'Plethora_Module_Booking', 'get_target_price_options' ) ) {

						$booking_target_price_options          = Plethora_Module_Booking::get_target_price_options( $this->post_type->name, $item_id );
						$item['item_target_price_text']        = $booking_target_price_options['target_price_text'];
						$item['item_target_price_text_before'] = $booking_target_price_options['target_price_text_before'];
						$item['item_target_price_text_after']  = $booking_target_price_options['target_price_text_after'];
					}

					// special room amenities field
					$item['item_room_amenities'] = $items_room_amenities && method_exists( 'Plethora_Posttype_Room_Ext', 'get_room_amenities' ) ? Plethora_Posttype_Room_Ext::get_room_amenities( $item_id, $items_room_amenities_max ) : array();

					// add it to $items
					$items[] = $item;

				endwhile;
			}
			wp_reset_postdata();    

			$return['items']  = $items;
		  

			return $return;
		}

		public function prepare_atts_for_main_template_query_args( $atts ) {

			extract( $atts );

			// Build WP Query Arguments
			$args = array();
			
			if ( $data !== $this->post_type->name .'_selection' ) { // if not a custom selection

				$args['post_type']    = $data;
				$args['post__not_in'] = !empty( $data_posts_exclude ) ? explode(',', $data_posts_exclude ) : array();
				// $args['tax_query'] = array( 'relation' => 'AND' );
				
				$data_tax_include = !empty( $data_tax_include ) ? explode(',', $data_tax_include ) : array();
				foreach ( $data_tax_include as $tax_term ) { // include posts with terms
				
					$tax_term            = explode('|', $tax_term );
					$args['tax_query'][] = array( 'taxonomy' => trim( $tax_term[0] ), 'field' => 'term_id', 'terms' => trim( $tax_term[1] ) );
				}

				$data_tax_exclude = !empty( $data_tax_exclude ) ? explode(',', $data_tax_exclude ) : array();
				foreach ( $data_tax_exclude as $tax_term ) { // exclude posts with terms
				
					$tax_term            = explode('|', $tax_term );
					$args['tax_query'][] = array( 'taxonomy' => $tax_term[0], 'field' => 'term_id', 'terms' => $tax_term[1], 'operator' => 'NOT IN' );
				}

			} else { // if custom selection

				$args['post_type'] = $this->post_type->name;
				$args['post__in']  = !empty( $data_posts_selection ) ? explode(',', $data_posts_selection ) : array();
			}

			$args['posts_per_page']      = $data_posts_per_page;
			$args['offset']              = $data_offset;
			$args['order']               = $data_order;
			$args['orderby']             = $data_orderby;
			$args['paged']               = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ;
			$args['ignore_sticky_posts'] = 1;
			if ( $data_orderby === 'meta_value' || $data_orderby === 'meta_value_num' ) {
			
				$args['meta_key'] = $data_orderby_metakey;
			}

			return $args;
		}

		/** 
		* Prepares attributes for the filterbar template file
		* @return array
		*/
		public function prepare_atts_for_filterbar_template( $atts ) {

			$exclude_term_ids = explode('|', $atts['filterbar_tax_exclude'] );
			$exclude_term_ids = !empty( $exclude_term_ids[1] ) ? $exclude_term_ids[1] : '' ;

			$args = array(
				'orderby' => 'name',
				'order'   => 'ASC',
				'exclude' => !empty( $exclude_term_ids ) ? explode(',', $exclude_term_ids ) : array(),
			);
				
			$taxonomy_terms = get_terms( $atts['filterbar_tax'], $args );
			$filters        = array();
			foreach ( $taxonomy_terms as $term_obj ) {

				$filters[]     = array(
					'id'           => esc_attr( $term_obj->term_id ),
					'slug'         => esc_attr( $term_obj->slug ),
					'name'         => wp_kses( $term_obj->name, Plethora_Theme::allowed_html_for( 'button' ) ),
					'taxonomy'     => esc_attr( $term_obj->taxonomy ),
					'description'  => esc_attr( $term_obj->description ),
					'count'        => $term_obj->count,
					'filter_class' => esc_attr( 'filter_'. $term_obj->slug )
				);
			}

			$return = array(
				'filterbar_id' => $atts['filterbar_id'],
				'filters_tax'  => $atts['filterbar_tax'],
				'resettitle'   => $atts['filterbar_resettitle'],
				'filters'      => $filters,
			);

			return $return;
		}

		/** 
		* Returns the svgloader initialization script
		* @return string
		*/
		public function svgloader_init_script() {

		  return '
<script>
//========================== SVG AJAX LOADER =====================================================

(function($) {
  var init;
  init = function() {

	/* LOADER MODAL FOR TEAM MEMBERS, SHOWCASE, PORTFOLIO AND BLOG SECTIONS */
	var $loaderModal, loadProgressIndicator, loader, loaderLauncher, loaderModal;
	loadProgressIndicator = function() {
	  var prog;
	  prog = $(".progress_ball");
	  return prog.toggleClass("show");
	};
	loader = new SVGLoader(document.getElementById("loader"), {
	  speedIn: 150,
	  easingIn: mina.easeinout,
	  onEnd: loadProgressIndicator
	});
	loaderModal = document.querySelector(".loader-modal");
	$loaderModal = $(loaderModal);
	$loaderModal.on("click", ".close-handle", function(e) {
	  $loaderModal.scrollTop(0);
	  $loaderModal.fadeOut(500, function() {
		return $loaderModal.attr("class", "loader-modal");
	  });
	  loader.hide();
	  $("body").removeClass("modal-open");
	});
	loaderLauncher = function(options) {
	  var className, content, inject;
	  content = options.content;
	  className = options.className;
	  inject = options.inject;
	  loader.show();
	  setTimeout(function() {
		if (className !== "undefined") {
		  $loaderModal.addClass(className);
		}
		$loaderModal.html("").append($("<span class=\'close-handle\' />"));
		return (function(content, inject) {
		  return $.ajax({
			url: content,
			error: function(data) {
			  return $loaderModal.append(themeConfig.ajaxErrorMessage.open + content + themeConfig.ajaxErrorMessage.close).fadeIn(500, function() {
				loader.hide();
				return loadProgressIndicator();
			  });
			},
			success: function(data) {
			  var $main, colorSet, injectable, window_height;
			  window_height = $(window).height();
			  $head_panel = $(data).find(".head_panel");
			  $main = $(data).find(".main");
			  colorSet = $main.find("").find("[data-colorset]").data("colorset") || "";
			  injectable = $main.addClass("ajaxed " + colorSet).css("min-height", window_height);
			  $("body").addClass("modal-open");
			  return $loaderModal.append($head_panel).append(injectable).fadeIn(250, function() {
				loadProgressIndicator();
				//loader.hide();
				return (function(selector) {
				  if (!(document.body.style["webkitPerspective"] !== void 0 || document.body.style["MozPerspective"] !== void 0)) {
					return;
				  }
				  // _p.slice(document.querySelectorAll("a.roll")).forEach(function(a) {
				  //   a.innerHTML = "<span data-title="" + a.text + "">" + a.innerHTML + "</span>";
				  // });
				})();
			  });
			}
		  });
		})(content, inject);
	  }, 250);
	};

	return $(".linkify").on("click", function(e) {
	  var content;
	  e.preventDefault();
	  _p.debugLog("Class \'ajax-call\' detected.");
	  content = e.currentTarget.href;
	  return loaderLauncher({
		content: content,
		className: "loader-modal-content"
	  });
	});
  };
  return (document.getElementById("loader")) && (document.querySelector(".loader-modal")) && init();
})(jQuery);

//END------------------------------------------------------------------------------ SVG AJAX LOADER
</script>';
	  
		}

		/** 
		* Returns the isotope initialization script
		* @return string
		*/
		public function isotope_init_script( $attrs ) {
			extract( $attrs );
			return '
<script>
//========================== ISOTOPE GRID ===========================================================

(function($){

  "use strict";

	$(window).load(function(){

		// init Isotope
		var $grid = $("'. $selector .'").isotope({

		  // main isotope options
		  itemSelector      : ".grid_item",
		  isOriginLeft      : true,
		  transitionDuration: "'. $grid_transitionDuration .'",
		  percentPosition   : true,
		  layoutMode        : "'. $grid_layoutMode .'",
		  // options for layout modes
		  masonry           : { columnWidth: ".grid_sizer", gutter: 0 },
		  fitRows           : { columnWidth: ".grid_sizer", gutter: 0 },
		  vertical          : { horizontalAlignment: 0.5 },
		  packery           : { gutter: 0, columnWidth: ".grid_item", rowHeight: 60, isHorizontal: true },
		  cellsByRow        : { columnWidth: ".grid_item", rowHeight: 150 },
		  masonryHorizontal : { rowHeight: 0, gutter: 0 },
		  cellsByColumn     : { columnWidth: ".grid_item", rowHeight: ".grid_item" },
		  horizontal        : { verticalAlignment: 0.5 }
		});

		// filter items on button click
		$("'.$filterbar_selector.'").on( "click", ".filter_button", function() {

		  var filterValue = $(this).attr("data-filter");
		  $grid.isotope({ filter: filterValue });
		});
	});
}(jQuery));

//END-------------------------------------------------------------------------------- ISOTOPE GRID
</script>';

		}

		public static function init_script_owlslider( $slider ) {
			return '
<script>
(function($){

	"use strict";

	$(window).load(function(){

		var $slider	= $("'. $slider['selector'] .'");
		$slider.owlCarousel({  
			loop               : _p.checkBool('.  $slider["loop"] .'),
			autoplay           : _p.checkBool('. $slider["autoplay"] .'),
			autoplayTimeout    : '.  intval( $slider["autoplaytimeout"] ) .',
			autoplayHoverPause : _p.checkBool('.  $slider["autoplayhoverpause"] .'),
			dots               : _p.checkBool('.  $slider["dots"] .'),
			nav                : _p.checkBool('.  $slider["nav"] .'),
			rtl      	   	   : _p.checkBool('.  $slider["rtl"] .'),
			margin			   : '. intval( $slider["margin"] ) .',
			responsive 		   : { 0:{ items:'. intval( $slider["responsive_xs"] ) .' }, 480:{ items:'. intval( $slider["responsive_sm"] ) .' }, 768:{ items:'. intval( $slider["responsive_md"] ) .' }, 992:{ items:'. intval( $slider["responsive_lg"] ) .' }	}
		});
	});
}(jQuery));
</script>
		';
		}

	}
endif;