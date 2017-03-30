<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

Filter Posts By Letter Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Widget_Filterbyletter') ) {

	/**
	 * @package Plethora Framework
	 */
	class Plethora_Widget_Filterbyletter extends WP_Widget  {

    public static $feature_title         = "Filter Posts By Letter";						// FEATURE DISPLAY TITLE
    public static $feature_description   = "";	  // FEATURE DISPLAY DESCRIPTION (STRING)
    public static $theme_option_control  = true;        						          // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
    public static $theme_option_default  = true;        						          // DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
    public static $theme_option_requires = array();        					          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = false;        						          // DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
    public static $dynamic_method        = false; 								            // ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ) | THIS A PARENT METHOD, FOR ADDING ACTION
    public static $wp_slug               = 'filterbyletter-widget';					  // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
    public static $assets;

    // public $all_letters_index;
    // public $filtered_results;

		public function __construct() { 

      /* LEAVE INTACT ACROSS WIDGET CLASSES */

      $id_base     = WIDGETS_PREFIX . self::$wp_slug;
      $name        = '> PL | ' . self::$feature_title;
      $widget_ops  = array( 
        'classname'   => self::$wp_slug, 
        'description' => esc_html__('Filter any post results by given letter', 'plethora-framework')
        );
      $control_ops = array( 'id_base' => $id_base );

      $this->test = parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT
		  
      // get/set filter variables
      add_action( 'init', array( $this, 'set_filter_vars' ) );
      // change main title/subtitle if needed
      add_action( 'plethora_get_title', array( $this, 'filter_view_title' ), 10, 2 );
      add_action( 'plethora_get_subtitle', array( $this, 'filter_view_subtitle' ), 10, 2 );
      // modify main query, according to letter filter
      add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
    }

		public function params() {

      $params_index['title'] = array( 
            "param_name"      => "title",
            "type"            => "textfield",
            "heading"         => esc_html__("Title", 'plethora-framework'),
            "is_widget_title" => true,
      );
      $params_index['post_type'] = array( 
            "param_name"    => "post_type",
            "type"          => "dropdown",
            "heading"       => esc_html__('Select Posts To Filter', 'plethora-framework'),
            "value"         => $this->get_post_type_options()
      );
      $params_index['col_size'] = array( 
            "param_name"    => "col_size",
            "type"          => "dropdown",
            "heading"       => esc_html__('Letters Displayed Per Row', 'plethora-framework'),
            "value"         => $this->get_colsize_options()
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

      $params                 = $this->params();                                                // GET PARAMETERS
      $widget_atts            = Plethora_Widget::get_widget_atts( $params, $args, $instance );  // GET WIDGET ATTRIBUTES
      $widget_atts['id_base'] = $this->id_base;                                                // ADD id_base FOR MAIN WRAPPER CLASS OUTPUT
      if ( empty( $widget_atts['post_type'] ) ) { return; }

      $widget_atts['letters'] = false;

      foreach ( $this->get_letters_index( $widget_atts['post_type'] ) as $letter => $letter_posts ) {

        $widget_atts['letters'][] = array(
            'title_lcase'   => mb_strtolower( $letter, get_bloginfo( 'charset' ) ),
            'title_ucase'   => mb_strtoupper( $letter, get_bloginfo( 'charset' ) ),
            'col_size'      => !empty( $widget_atts['col_size'] ) ? $widget_atts['col_size'] : 3 ,
            'current_class' => $this->filter_letter === $letter && $this->filter_post_type === $widget_atts['post_type'] ? 'current_letter' : '' ,
            'link'          => add_query_arg( 
                                    array(
                                      'ple_filter_by_letter' => $letter,
                                      'ple_filter_post_type' => $widget_atts['post_type'],
                                    ),
                                    get_post_type_archive_link( $widget_atts['post_type'] )
                               ),
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


    function set_filter_vars() {

      $this->filter_letter    = isset( $_GET['ple_filter_by_letter'] ) ? mb_strtolower( strip_tags( $_GET['ple_filter_by_letter'] ), get_bloginfo( 'charset' ) ) : '' ;
      $this->filter_post_type = isset( $_GET['ple_filter_post_type'] ) ? sanitize_key( $_GET['ple_filter_post_type'] ) : '' ;
    }

    function is_filter_view() {

      if ( !is_admin() && Plethora_Theme::is_archive_page() && !empty( $this->filter_letter ) &&  !empty( $this->filter_post_type ) ) { return true; }
      return false;
    }

    function filter_view_title( $title, $args ) {


      if ( $this->is_filter_view() && !$args['listing'] ) {

        $title_status      = Plethora_Theme::option( THEMEOPTION_PREFIX .'filterbyletter-title', 1 );
        $title_text_pattern = Plethora_Theme::option( THEMEOPTION_PREFIX .'filterbyletter-title-text', '%1$s // start with %2$s' );

        if ( $title_status ) {

          $title = sprintf( $title_text_pattern, $title, mb_strtoupper( $this->filter_letter, get_bloginfo( 'charset' ) ), $this->filter_letter );
        }
      }

      return $title;
    }

    function filter_view_subtitle( $subtitle, $args ) {

      if ( $this->is_filter_view() && !$args['listing'] ) {

        $subtitle_status       = Plethora_Theme::option( THEMEOPTION_PREFIX .'filterbyletter-subtitle', 1 );
        $subtitle_text_pattern = Plethora_Theme::option( THEMEOPTION_PREFIX .'filterbyletter-subtitle-text', 'All posts starting with the %2$s letter' );

        if ( $subtitle_status ) {

          $subtitle = sprintf( $subtitle_text_pattern, $subtitle, mb_strtoupper( $this->filter_letter, get_bloginfo( 'charset' ) ), $this->filter_letter );
        }
      }

      return $subtitle;
    }


    function pre_get_posts( $query ){

      if (  $query->is_main_query() && $this->is_filter_view() ) {

        $query->set( 'post__in', $this->get_filtered_posts() );
      }
    }

    function get_filtered_posts() {

      $filtered_posts  = array();

      if ( $this->is_filter_view() ) {

        $all_posts = $this->get_all_posts( $this->filter_post_type );
        foreach ( $all_posts as $post ) {

          $this_letter = !empty( $post->post_title ) ? mb_substr( $post->post_title, 0, 1 ) : '' ;
          $fixed_letter = self::replace_special_char( $this_letter );
          // check both given letter version AND FIXED LETTER VERSION
          if ( !empty( $this_letter ) && mb_strtolower( $this_letter, get_bloginfo( 'charset' ) ) === $this->filter_letter ) { 
            
            $filtered_posts[] = $post->ID;
          
          } elseif ( !empty( $fixed_letter ) && mb_strtolower( $fixed_letter, get_bloginfo( 'charset' ) ) === $this->filter_letter ) { 

            $filtered_posts[] = $post->ID;
          }
        }
      }   

      return $filtered_posts;
    }

    function get_letters_index( $post_type ) {

      $letters_index = array();

      // No need to query if widget is inactive
      if ( is_active_widget( false, false, $this->id_base ) ) {

        $all_posts = $this->get_all_posts( $post_type );
        foreach ( $all_posts as $post ) {

          $this_letter = !empty( $post->post_title ) ? mb_substr( $post->post_title, 0, 1 ) : '' ;
          if ( !empty( $this_letter ) ) {
            $letter_key = mb_strtolower( $this_letter, get_bloginfo( 'charset' ) );
            $letter_key = self::replace_special_char( $letter_key ); // fix special chars
            $letters_index[$letter_key][] = $post->ID;
          }
        }
        ksort( $letters_index );

      }   

      return $letters_index;
    }

    function get_all_posts( $post_type ) {

      $args = array(                                
            'posts_per_page'      => "-1",
            'ignore_sticky_posts' => 1,
            'post_type'           => $post_type,
            'orderby'             => "name",            
            'order'               => "ASC",
      );
      $the_posts = get_posts( $args );
      wp_reset_postdata(); 
      return $the_posts;
    }

    function get_post_type_options() {
      
      $options        = array( '-' => 0);
      $post_type_objs = Plethora_Theme::get_supported_post_types( array( 'type' => 'archives', 'output' => 'objects' ) );

      foreach ( $post_type_objs as $post_type_obj ) {

        $options[$post_type_obj->labels->name] = $post_type_obj->name;
      }

      return $options;
    }

    function get_colsize_options() {
      
      $options = array( 
        '1'  => 'col-md-12 col-sm-3 col-xs-3',
        '2'  => 'col-md-6 col-sm-3 col-xs-3',
        '3'  => 'col-md-4 col-sm-3 col-xs-3',
        '4'  => 'col-md-3 col-sm-3 col-xs-3',
        '6'  => 'col-md-2 col-sm-3 col-xs-3',
        '12' => 'col-md-1 col-sm-3 col-xs-3',
      );

      return $options;
    }

    function replace_special_char( $char ) {

      if ( empty( $char ) ) { return ''; }

      $special_chars_replace_list = self::special_chars_replace_list();

      if ( array_key_exists( $char, $special_chars_replace_list ) ) {

        return $special_chars_replace_list[$char];
      }

      return $char;
    }

    function special_chars_replace_list() {

      $special_chars_replace_list = array(
          'ά' => 'α',
          'έ' => 'ε',
          'ύ' => 'υ',
          'ί' => 'ι',
          'ό' => 'ο',
          'ή' => 'η',
          'ώ' => 'ω',
          'Ά' => 'Α',
          'Έ' => 'Ε',
          'Ύ' => 'Υ',
          'Ί' => 'Ι',
          'Ό' => 'Ο',
          'Ή' => 'Η',
          'Ώ' => 'Ω',
      );

      return apply_filters( 'plethora_widget_filterbyletter_special_chars_list', $special_chars_replace_list );
    }
	}
 }