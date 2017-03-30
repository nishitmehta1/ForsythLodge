<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				          (c) 2016

Editor Content Navigation Widget Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Widget_Editorcontentnav') ) {

	/**
	 * @package Plethora Framework
	 */
	class Plethora_Widget_Editorcontentnav extends WP_Widget  {

    public static $feature_title         = "Editor Content Navigation";						// FEATURE DISPLAY TITLE
    public static $feature_description   = "";	  // FEATURE DISPLAY DESCRIPTION (STRING)
    public static $theme_option_control  = true;        						          // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
    public static $theme_option_default  = true;        						          // DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
    public static $theme_option_requires = array( 'module' => 'editorcontentindex');        					          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = false;        						          // DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
    public static $dynamic_method        = false; 								            // ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ) | THIS A PARENT METHOD, FOR ADDING ACTION
    public static $wp_slug               = 'editorcontentnav-widget';					  // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
    public static $assets;

    // public $all_letters_index;
    // public $filtered_results;

		public function __construct() { 

      /* LEAVE INTACT ACROSS WIDGET CLASSES */

      $id_base     = WIDGETS_PREFIX . self::$wp_slug;
      $name        = '> PL | ' . self::$feature_title;
      $widget_ops  = array( 
        'classname'   => self::$wp_slug, 
        'description' => esc_html__('Navigation menu according to editor content headings', 'plethora-framework')
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
      $params_index['animation'] = array( 
            "param_name"    => "animation",
            "type"          => "dropdown",
            "heading"       => esc_html__("Animation", 'plethora-framework'),
            "value"         => Plethora_Module_Style::get_options_array( array( 
                              'type'            => 'animations', 
                              'use_in'          => 'vc',
                              'prefix_all_values' => 'wow',
                              'prepend_default' => true,
                              'default_title'   => esc_html__('None', 'plethora-framework')
                               )),
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

      $nav_items = Plethora_Theme::option( METAOPTION_PREFIX .'contentindex_items', array() );

      if ( !is_singular() || ! $nav_items ) { return; }

      $params                 = $this->params();                                                // GET PARAMETERS
      $widget_atts            = Plethora_Widget::get_widget_atts( $params, $args, $instance );  // GET WIDGET ATTRIBUTES
      $widget_atts['id_base'] = $this->id_base;                                                // ADD id_base FOR MAIN WRAPPER CLASS OUTPUT
      $widget_atts['nav_items'] = false;

      // print_r( $nav_items );
      foreach ( $nav_items as $nav_item ) {

        $widget_atts['nav_items'][] = array(
            'link'        => '#'. $nav_item['id'],
            'class'       => 'nav_item depth-'. $nav_item['level'],
            'fontweight'    => 800 - ( ( int ) $nav_item['level'] * 100 ),
            'fontsize'    => 15 - ( int ) $nav_item['level'],
            'padding'     => ( ( $nav_item['level'] == 1 || $nav_item['level'] == 2 ) ? 0 : ( ( int ) ( $nav_item['level']-1 ) * 15 ) - 15 ) ,
            'depth'       => 'nav_item depth-'. $nav_item['level'],
            'anchor_text' => $nav_item['anchor_text'],
        );
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
	}
 }