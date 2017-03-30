<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				      (c) 2015-2016

File Description: Terminology Grid Shortcode

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Termsindex') && class_exists('Plethora_Posttype_Terminology') ):

	/**
	 * @package Plethora Framework
	 */

	class Plethora_Shortcode_Termsindex extends Plethora_Shortcode { 

      public static $feature_title         = "Terms Index Shortcode";           // Feature display title  (string)
      public static $feature_description   = "";                                // Feature display description (string)
      public static $theme_option_control  = true;                              // Will this feature be controlled in theme options panel ( boolean )
      public static $theme_option_default  = true;                              // Default activation option status ( boolean )
      public static $theme_option_requires = array('posttype'=>'terminology');  // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                              // Dynamic class construction ? ( boolean )
      public static $dynamic_method        = false;                             // Additional method invocation ( string/boolean | method name or false )
      public $wp_slug                      =  'termsindex';                     // This should be the WP slug of the content element ( WITHOUT the prefix constant )
      public static $assets                = array (                            // Script & style files. 
                                                array( 'script' => 'isotope-plethora' ), // had to use plethora version, due to VC conflicts
                                                array( 'style'  => 'isotope' )
                                             );

    	public function __construct() {

          // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'        => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'        => esc_html__('Terms Index', 'plethora-framework'),
                      'description' => esc_html__('Build a terms index alphabetically or by topic', 'plethora-framework'),
                      'class'       => '',
                      'weight'      => 1,
                      'category'    => 'Posts Grids',
                      'icon'        => $this->vc_icon(), 
                      'params'      => $this->params(), 
                      );
          // Add the shortcode
          $this->add( $map );

    	 }

       /** 
       * Returns shortcode settings (compatible with Visual composer)
       *
       * @return array
       * @since 1.0
       *
       */
      public function params() {

          $params = array(

                  array(
                      'param_name'    => 'filter_topic',
                      'type'          => 'dropdown',
                      'heading'       => esc_html__('Terms Topic Filter', 'plethora-framework'),
                      'description'   => esc_html__('Select a terms topic', 'plethora-framework'),
                      'value'         => self::get_topics_array(),
                   ),
                  array(
                      'param_name'       => 'link_target',
                      'type'             => 'dropdown',
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Term Posts Link Behavior', 'plethora-framework'),
                      'value'            => array( 
                                              // esc_html__('Display Popup Window (Retrieve term page via Ajax)', 'plethora-framework') => 'ajax', // PLENOTE: To be implemented in upcoming version
                                              esc_html__('Link to term post page', 'plethora-framework')                             => 'link', 
                                              esc_html__('Do not link terms', 'plethora-framework')                                  => 'none',
                                            ) 
                  ),
                  array(
                      'param_name'       => 'link_target_blank',
                      'type'             => 'dropdown',
                      'heading'          => esc_html__('Open in new window?', 'plethora-framework'),
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'description'      => esc_html__('Open term post in new window.', 'plethora-framework'),
                      'value'            => array(
                                              "Yes" => "yes",
                                              "No"  => "no"
                                          ),
                      'dependency'       => array(
                                              "element" => "link_target",
                                              "value"   => "link"
                                            )
                  ),
                  array(
                      'param_name'       => 'el_class',
                      'type'             => 'textfield',
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Extra class name', 'plethora-framework'),
                  ),

                  // DESIGN OPTIONS TAB STARTS >>>>
                  array(
                      'param_name'  => 'css',
                      'type'        => 'css_editor',
                      'group'       => esc_html__('Design Options', 'plethora-framework'),                                              
                      'heading'     => esc_html__('Design Options', 'plethora-framework'),
                  ),

          );

          return $params;
      }

      private static function get_topics_array() {

          // Get the topics (use wp_get_object_terms for custom post type)
          $args = array(
              'orderby'           => 'name', 
              'order'             => 'ASC',
              'hide_empty'        => false, 
              'fields'            => 'id=>name', 
              'hierarchical'      => false, 
          );
          $topics = get_terms( 'term-topic', $args);

          $return['No filter'] = '';

          if ( !empty( $topics ) && !is_wp_error( $topics ) ) { 

             foreach ($topics as $id => $name) { 

                $return[$name] = $id;
             }
          }

          return $return;
      }

       /** 
       * Returns shortcode content
       *
       * @return array
       * @since 1.0
       *
       */
      public function content( $atts, $content = null ) {

          // EXTRACT USER INPUT
          extract( shortcode_atts( array( 
            'filter_topic'      => '',
            'link_target'       => 'link',
            'link_target_blank' => 'yes',
            'el_class'          => 'el_class',
            'css'               => 'yes'
            ), $atts ) );

          $link_target_blank = ( $link_target_blank === "yes" && $link_target !== "none" ) ? "_blank" : "";
          $css_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts );
          $css_classes = !empty( $el_class ) ? $css_classes .' '. $el_class : $css_classes ;
          
          // CORE QUERY ARGUMENTS
          $args = array(                                
            'posts_per_page'      => "-1",
            'ignore_sticky_posts' => 1,
            'post_type'           => 'terminology',
            'orderby'             => "title",            
            'order'               => "ASC"
          );
                    
          // TAX_QUERY ARGUMENTS
          if ( ! empty( $filter_topic ) && is_numeric( $filter_topic ) ) { 

              $args['tax_query'] = array(                   
                array( 
                  'taxonomy' => 'term-topic', 
                  'field'    => 'id',           
                  'terms'    => $filter_topic   
                  )
                );        
          }

          $terms_query = new WP_Query($args);   // GET RESULTS
          $index = array();
          if ( $terms_query->have_posts() ) {

            while ( $terms_query->have_posts() ) : $terms_query->the_post(); 

              $title = get_the_title();
              $letter = !empty( $title ) ? mb_substr( $title, 0, 1 ) : '' ;
              if ( !empty( $letter ) ) { 
                
                //IMPORTANT: pass letter through the special chars replacement function ( good for punctuation chars, etc. )
                $letter = self::replace_special_char( $letter );
                $index[strtoupper($letter)]['letter']  = $letter;
                $index[strtoupper($letter)]['terms'][] = array(
                                                                          'term_id'          => get_the_id(),
                                                                          'term_title'       => get_the_title(),
                                                                          'term_title_attr'  => esc_attr( get_the_title() ),
                                                                          'term_permalink'   => get_permalink(),
                                                                          'term_link_target' => $link_target_blank
                                                                          );
              }

            endwhile;
          }
          wp_reset_postdata();    
          $terms_index['items'] = array_values( $index );
          $terms_index['css']   = $css_classes;
          return Plethora_WP::renderMustache( array( "data" => $terms_index, "file" => __FILE__ ) );
      }


      static function replace_special_char( $char ) {

        if ( empty( $char ) ) { return ''; }

        $special_chars_replace_list = self::special_chars_replace_list();

        if ( array_key_exists( $char, $special_chars_replace_list ) ) {

          return $special_chars_replace_list[$char];
        }

        return $char;
      }

      static function special_chars_replace_list() {

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

        return apply_filters( 'plethora_shortcode_termsindex_special_chars_list', $special_chars_replace_list );
      }

	}
	
 endif;