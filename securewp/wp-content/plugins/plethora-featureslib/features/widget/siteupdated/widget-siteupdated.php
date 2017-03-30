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

if ( !class_exists('Plethora_Module_Siteupdated') ) {

	/**
	 * @package Plethora Framework
	 */
	class Plethora_Module_Siteupdated extends WP_Widget  {

    public static $feature_title         = "Latest Update";						// FEATURE DISPLAY TITLE
    public static $feature_description   = "";	  // FEATURE DISPLAY DESCRIPTION (STRING)
    public static $theme_option_control  = true;        						          // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
    public static $theme_option_default  = true;        						          // DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
    public static $theme_option_requires = array( 'module' => 'editorcontentindex');        					          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = false;        						          // DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
    public static $dynamic_method        = false; 								            // ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ) | THIS A PARENT METHOD, FOR ADDING ACTION
    public static $wp_slug               = 'siteupdated-widget';					  // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
    public static $assets;

    // public $all_letters_index;
    // public $filtered_results;

		public function __construct() { 

      /* LEAVE INTACT ACROSS WIDGET CLASSES */

      $id_base     = WIDGETS_PREFIX . self::$wp_slug;
      $name        = '> PL | ' . self::$feature_title;
      $widget_ops  = array( 
        'classname'   => self::$wp_slug, 
        'description' => esc_html__('Let your visitors know the last date the website updated', 'plethora-framework')
        );
      $control_ops = array( 'id_base' => $id_base );

      $this->test = parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT
    }

		public function params() {

			$params['title'] = array(
            "param_name"      => "title",
            "type"            => "textfield",
            "heading"         => esc_html__("Title", 'plethora-framework'),
            "is_widget_title" => true,
      );
      $post_types = Plethora_Theme::get_supported_post_types( array( 'output' => 'objects' ) );
      $value[esc_html__('Any single post type update', 'plethora-framework')] = '';
      foreach ( $post_types as $post_type => $post_type_obj ) {
          
        $value[sprintf( esc_html__('Latest %s update', 'plethora-framework'), mb_strtolower( $post_type_obj->labels->singular_name ) )] = $post_type;
      }
      $value[sprintf( esc_html__('Custom date: Today', 'plethora-framework') )] = 0;
      $value[sprintf( esc_html__('Custom date: Yesterday', 'plethora-framework') )] = 1;
      for ($i = 2; $i < 15; $i++) {

        $value[sprintf( esc_html__('Custom date: %s days ago', 'plethora-framework'), $i )] = $i;
      }

      $params['update_source'] = array(
            "param_name"    => "update_source",
            "type"          => "dropdown",
            "heading"       => esc_html__("Display date according to:", 'plethora-framework'),
            "value"         => $value,
      );
      $desc = sprintf( esc_html__('Check PHP\'s %1$sdate format characters documentation%2$s', 'plethora-framework'), '<a href="http://php.net/manual/en/function.date.php#refsect1-function.date-parameters" target="_blank">', '</a>' );
      $params['date_format'] = array(
            "param_name" => "date_format",
            "type"       => "textfield",
            "heading"    => esc_html__("Date Format )", 'plethora-framework'),
            "value"      => esc_html__("l, j M Y", 'plethora-framework'),
            "desc"       => $desc,
      );

      $params['notice_textpattern'] = array(
            "param_name" => "notice_textpattern",
            "type"       => "textfield",
            "heading"    => esc_html__("Notice Display Pattern ( %s = lastest update date )", 'plethora-framework'),
            "value"       => esc_html__("Latest update: %s", 'plethora-framework'),
      );

      $params['animation'] = array(
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
      $params['extra_class'] = array(
            "param_name"    => "extra_class",                                  
            "type"          => "textfield",                                    
            "heading"       => esc_html__("Extra class(es)", 'plethora-framework'),       
            "description"   => esc_html__("Separate classes ONLY with space", 'plethora-framework'),
            "value"         => '',                                   
      );

      return $params;
		}

		function widget( $args, $instance ) {

      $params                 = $this->params();                                                // GET PARAMETERS
      $widget_atts            = Plethora_Widget::get_widget_atts( $params, $args, $instance );  // GET WIDGET ATTRIBUTES
      $widget_atts['id_base'] = $this->id_base;                                                // ADD id_base FOR MAIN WRAPPER CLASS OUTPUT
      if ( is_numeric( $widget_atts['update_source'] ) ) {
      
        $date_now = !empty( $widget_atts['date_format'] ) ? date_i18n( $widget_atts['date_format'] ) : date_i18n( 'l, j M Y' );
        $date = new DateTime();
        $date->modify( '-'. $widget_atts['update_source'] );
        $date_show = date_i18n( $widget_atts['date_format'], $date->getTimestamp() );


      } else {

        $args = array( 
          'posts_per_page'      => 1, 
          'post_type'           => empty( $widget_atts['update_source'] ) ? Plethora_Theme::get_supported_post_types() : $widget_atts['update_source'],
          'ignore_sticky_posts' => true
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) {

          while ( $query->have_posts() ) {

            $query->the_post();
            $date_show = get_the_date( $widget_atts['date_format'] );
          }
        }
        wp_reset_postdata();
      }


      $widget_atts['updated_text'] = !empty( $date_show ) ? sprintf( $widget_atts['notice_textpattern'], $date_show ) : '';


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