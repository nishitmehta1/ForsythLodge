<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

Custom Taxonomy List Widget Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_Customtaxlist') ) {

	/**
	 * @package Plethora Framework
	 */
	class Plethora_Widget_Customtaxlist extends WP_Widget  {

		public static $feature_title         = "Custom Taxonomy List";		// FEATURE DISPLAY TITLE
		public static $feature_description   = "";							// FEATURE DISPLAY DESCRIPTION (STRING)
		public static $theme_option_control  = true;						// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
		public static $theme_option_default  = true;						// DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
		public static $theme_option_requires = array();						// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = false;						// DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
		public static $dynamic_method        = false;						// ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ) | THIS A PARENT METHOD, FOR ADDING ACTION
		public static $wp_slug               = 'customtaxlist-widget';		// SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
		public static $assets;

		public function __construct() { 

		  /* LEAVE INTACT ACROSS WIDGET CLASSES */

		  $id_base     = WIDGETS_PREFIX . self::$wp_slug;
		  $name        = '> PL | ' . self::$feature_title;
		  $widget_ops  = array( 
			'classname'   => self::$wp_slug, 
			'description' => esc_html__('A list of custom taxonomy categories, similar to Categories widget.', 'plethora-framework')
			);
		  $control_ops = array( 'id_base' => $id_base );

		  $this->test = parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT
		}

		public function params() {

			$params_index['title'] = array( 
					"param_name"      => "title",
					"type"            => "textfield",
					"heading"         => esc_html__("Title", 'plethora-framework'),
					"is_widget_title" => true,
			);
			$params_index['taxonomy'] = array( 
					"param_name"    => "taxonomy",
					"type"          => "dropdown",
					"heading"       => esc_html__('Select Taxonomy', 'plethora-framework'),
					"value"         => $this->get_taxonomies_options()
			);
			$params_index['show_option_all'] = array( 
					"param_name"    => "show_option_all",
					"type"          => "dropdown",
					"heading"       => esc_html__('Show All Title', 'plethora-framework'),
					"value"         => array( esc_html__( 'No','plethora-framework' ) => false , esc_html__( 'Yes','plethora-framework' ) => true )
			);
			$params_index['show_count'] = array( 
					"param_name"    => "show_count",
					"type"          => "dropdown",
					"heading"       => esc_html__('Show post counts', 'plethora-framework'),
					"value"         => array( esc_html__( 'No','plethora-framework' ) => false , esc_html__( 'Yes','plethora-framework' ) => true )
			);
			$params_index['show_hovertitle'] = array( 
					"param_name"    => "show_hovertitle",
					"type"          => "dropdown",
					"heading"       => esc_html__('Show category description as hover title', 'plethora-framework'),
					"value"         => array( esc_html__( 'No','plethora-framework' ) => false , esc_html__( 'Yes','plethora-framework' ) => true )
			);
			$params_index['animation'] = array( 
					"param_name"    => "animation",
					"type"          => "dropdown",
					"heading"       => esc_html__("Animation", 'plethora-framework'),
					"value"         => Plethora_Module_Style::get_options_array( array( 
												'type'              => 'animations', 
												'use_in'            => 'vc',
												'prefix_all_values' => 'wow',
												'prepend_default'   => true,
												'default_title'     => esc_html__('None', 'plethora-framework')
									   		)
										),
			);
			$params_index['extra_class'] = array( 
					"param_name"    => "extra_class",                                  
					"type"          => "textfield",                                    
					"heading"       => esc_html__("Extra class(es)", 'plethora-framework'),       
					"description"   => esc_html__("Separate classes ONLY with space", 'plethora-framework'),
					"value"         => '',                                   
			);

			return $params_index;
		}

		function widget( $args, $instance ) {

			$params                 = $this->params();                                                // GET PARAMETERS
			$widget_atts            = Plethora_Widget::get_widget_atts( $params, $args, $instance );  // GET WIDGET ATTRIBUTES
			$widget_atts['id_base'] = $this->id_base;                                                // ADD id_base FOR MAIN WRAPPER CLASS OUTPUT

			$widget_atts['list_terms'] = false;
			if ( $widget_atts['taxonomy'] ) {
				// Get taxonomy object
				$tax_obj = get_taxonomy( $widget_atts['taxonomy'] );
				// Get taxonomy terms list according to Walker_Category format
				$args = array(
					'echo'               => false,
					'taxonomy'           => $tax_obj->name,
					'show_option_all'    => $widget_atts['show_option_all'] ? $tax_obj->labels->all_items : '',
					'show_count'         => $widget_atts['show_count'],
					'title_li'           => '',
					'use_desc_for_title' => $widget_atts['show_hovertitle']
				);
				
				$widget_atts['list_terms'] = wp_list_categories($args);
			}

			// Get output from template
			echo Plethora_Widget::get_templatepart_output( $widget_atts, __FILE__ );
			// OR get ouput from shortcode
			// echo Plethora_Widget::get_shortcode_output( 'vc_column', $widget_atts );
		}

		function update( $new_instance, $old_instance ) {

				return $new_instance;
		}

		function form( $instance ) {

			$field_params = $this->params();	                                    // GET OPTIONS PARAMETERS
			$defaults     = Plethora_Widget::get_form_defaults( $field_params );  // GET DEFAULT 
			$instance     = wp_parse_args((array) $instance, $defaults);          // PARSE INSTANCE ARGUMENTS

			foreach( $field_params as $key => $field_args ){                      // CREATE THE FORM!

				$field_args['obj']      = $this;
				$field_args['instance'] = $instance;
				echo Plethora_Widget::get_field( $field_args ) ;
			}
		}

		function get_taxonomies_options() {
		  
			$options    = array( '-' => 0);
			$post_types = Plethora_Theme::get_supported_post_types( array( 'type' => 'archives', 'exclude' => 'post' ) );
			$taxonomies = array();
			foreach ( $post_types as $post_type ) {

				// get the taxonomy objects
				$args = array(
				  'public'      => true,
				  'object_type' => array( $post_type )
				);
				$taxonomies[$post_type] = get_taxonomies( $args, 'objects' );

				// set the options return
				foreach ( $taxonomies[$post_type] as $tax_obj ) {

					// we need only hierarchical taxonomies
					if ( $tax_obj->hierarchical ) {

						$options[$tax_obj->labels->name] = $tax_obj->name;
					}
				}
			}

			return $options;
		}
	}
 }