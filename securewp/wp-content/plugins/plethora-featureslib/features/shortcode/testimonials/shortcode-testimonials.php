<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2013

File Description: Entry shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Testimonial') && class_exists('Plethora_Posttype_Testimonial') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Testimonials extends Plethora_Shortcode { 

      public $wp_slug                      = 'testimonials';                   // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $feature_name          = "Testimonials Slider";                   // FEATURE DISPLAY TITLE 
      public static $feature_title         = "Testimonials Slider Shortcode";  // FEATURE DISPLAY TITLE 
      public static $feature_description   = "";           // FEATURE DISPLAY DESCRIPTION 
      public static $shortcode_category    = "Testimonials";
      public static $theme_option_control  = true;                             // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                             // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array('posttype'=>'testimonial'); // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                             // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;                            // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public static $assets                = array(
                                                    array( 'script' => array( 'owlcarousel2')),       // Scripts files - wp_enqueue_script
                                                    array( 'style'  => array( 'owlcarousel2-theme')), // Style files - wp_register_style
                                                    array( 'style'  => array( 'owlcarousel2')),       // Style files - wp_register_style
                                            );

      public function __construct() {

          // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'          => sprintf( esc_html__( '%s', 'plethora-framework' ), self::$feature_name ),
                      'description'   => sprintf( esc_html__( '%s', 'plethora-framework'), self::$feature_description ),
                      'class'         => '',
                      'weight'        => 1,
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( self::$feature_name ), 
                      'params'        => $this->params(), 
                      );
          $this->add( $map );         // ADD ΤΗΕ SHORTCODE
      }

       /** 
       * Returns shortcode settings (compatible with Visual composer)
       *
       * @return array
       * @since 1.0
       *
       */
       public function params() {

          $tax_terms = get_terms( "testimonial-category", array( 'hide_empty' => false ) );
          $available_cats = array( esc_html__("All Testimonials", 'plethora-framework') => "--" );
          if ( !is_wp_error( $tax_terms )) {
            foreach ( $tax_terms as $term ) { 

              $available_cats[ esc_html__("Category", 'plethora-framework' ) .': '. $term->name] = $term->slug;  
            } 
          }

          $params = array(

                  array(
                      "param_name"    => "testimonial_category",                                  
                      "type"          => "dropdown",                                        
                      "heading"       => esc_html__("Testimonial Category", 'plethora-framework'),      
                      "description"   => esc_html__("Select Testimonial Category to choose specific testimonial posts or leave empty to get uncategorized testimonials.", 'plethora-framework'),       
                      "value"         => $available_cats
                  ),
                  array(
                      'param_name'  => 'el_class',
                      'type'        => 'textfield',
                      'heading'     => esc_html__('Extra Class', 'plethora-framework'),
                      'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
                  ),

                  array(
                      "param_name" => "autoplay",                                  
                      "type"       => "dropdown",                                        
                      "heading"    => esc_html__("Autoplay", 'plethora-framework'),      
                      "group"      => esc_html__("Slider Options", 'plethora-framework'),
                      "value"      => array( esc_html__("Yes", 'plethora-framework') => 'true' , esc_html__("No", 'plethora-framework') => 'false' )
                  ),

                  array(
                      "param_name"       => "autoplaytimeout",                                  
                      "type"             => "textfield",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Slide Change Timeout", 'plethora-framework'),      
                      "description"      => esc_html__("In milliseconds, in example 5000 defines 5 seconds timeout.", 'plethora-framework'),       
                      "group"            => esc_html__("Slider Options", 'plethora-framework'),
                      "value"            => '5000'
                  ),
                  array(
                      "param_name"       => "loop",                                  
                      "type"             => "dropdown",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Slider Loop", 'plethora-framework'),      
                      "group"            => esc_html__("Slider Options", 'plethora-framework'),
                      "value"            => array( esc_html__("Yes", 'plethora-framework') => 'true' , esc_html__("No", 'plethora-framework') => 'false' )
                  ),

                  array(
                      "param_name"       => "autoplayhoverpause",                                  
                      "type"             => "dropdown",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Pause On Hover", 'plethora-framework'),      
                      "group"            => esc_html__("Slider Options", 'plethora-framework'),
                      "value"            => array( esc_html__("No", 'plethora-framework') => 'false' , esc_html__("Yes", 'plethora-framework') => 'true' )
                  ),

                  array(
                      "param_name"       => "dots",                                  
                      "type"             => "dropdown",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Dots Navigation", 'plethora-framework'),      
                      "group"            => esc_html__("Slider Options", 'plethora-framework'),
                      "value"            => array( esc_html__("Yes", 'plethora-framework') => 'true' , esc_html__("No", 'plethora-framework') => 'false' )
                  ),

                  array(
                      "param_name"       => "rtl",                                  
                      "type"             => "dropdown",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Right To Left Placement", 'plethora-framework'),      
                      "group"            => esc_html__("Slider Options", 'plethora-framework'),
                      "value"            => array( esc_html__("No", 'plethora-framework') => 'false' , esc_html__("Yes", 'plethora-framework') => 'true' )
                  ),

                  array(
                      "param_name"       => "id",                                  
                      "type"             => "textfield",                                        
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Slider ID", 'plethora-framework'),      
                      "description"      => esc_html__("Leave the default value, or create a unique one for this element. Leaving this empty, will cause problems on testimonial slider functionality.", 'plethora-framework'),       
                      "group"            => esc_html__("Slider Options", 'plethora-framework'),
                      "value"            => uniqid()
                  ),

                  array(
                      "param_name" => "css",
                      "type"       => "css_editor",
                      'group'      => esc_html__( 'Design options', 'plethora-framework' ),
                      "heading"    => esc_html__('CSS box', 'plethora-framework'),
                  ),

          );

          return $params;
       }


       /** 
       * Returns shortcode content
       *
       * @return array
       * @since 1.0
       *
       */
       public function content( $atts, $content = null ) {

        // Extract user input
        extract( shortcode_atts( array( 
          'testimonial_category' => '--',
          'id'                   => uniqid(),
          'loop'                 => 'true',
          'autoplay'             => 'true',
          'autoplaytimeout'      => 5000,
          'autoplayhoverpause'   => 'false',
          'dots'                 => 'true',
          'rtl'                  => 'false',
          'el_class'             => '',
          'css'                  => '',
          ), $atts ) );

         //fixed query args
         $args = array(
          'post_type'      => 'testimonial',
          'posts_per_page' => -1,
          'order'          => 'ASC',
          'orderby'        => 'date'
         );
         if ( $testimonial_category !== "--"){    // DISPLAY TESTIMONIALS FROM SELECTED CATEGORY

            $args['tax_query'] = array(
              array(
                'taxonomy'         => 'testimonial-category',                
                'field'            => 'slug', 
                'terms'            => array( $testimonial_category ),
                'include_children' => true
                )
              );

         } 

         $testimonial_posts = new WP_Query($args);   // ACCESS QUERY OBJET METHODS, PAGINATION, STICKY POSTS

         if ( $testimonial_posts->have_posts() ) { 

            $shortcode_atts['id'] = esc_attr( $id );
            $shortcode_atts['testimonials'] = array();

            while( $testimonial_posts->have_posts() ) { 

               $testimonial_posts->the_post();

               array_push( $shortcode_atts['testimonials'], array(
                 'title'       => get_the_title(),
                 'content'     => get_the_content(),     
                 'person_name' => Plethora_Theme::option( METAOPTION_PREFIX .'testimonial-person-name' ,'', get_the_id() ),     
                 'person_role' => Plethora_Theme::option( METAOPTION_PREFIX .'testimonial-person-role','', get_the_id()),      
                  )
               );

            };

          wp_reset_postdata();

          // Add init script for OwlCarousel 2
          $slider['loop']               = $loop;
          $slider['autoplay']           = $autoplay;
          $slider['autoplaytimeout']    = $autoplaytimeout;
          $slider['autoplayhoverpause'] = $autoplayhoverpause;
          $slider['dots']               = $dots;
          $slider['rtl']                = $rtl;

          Plethora_Theme::enqueue_init_script( array(
                      'multiple' => true,
                      'handle'   => 'owlcarousel2',
                      'script'   => $this->init_script_owlslider( $id, $slider )
          ));

          $shortcode_atts['el_class'] = esc_attr( $el_class );
          $shortcode_atts['css']      = esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) );


          // Return the mustache template
          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );
         } 
      }

      public static function init_script_owlslider( $id, $slider ) {

        return '
        <script>
        jQuery(function($) {

            "use strict";

          var $testimonials     = $("#'. $id .'");
          if ( $testimonials.length ){

              var loop = false;

              if ( $testimonials.find("li").length > 1){
                loop = '. $slider['loop'] .';
              }

              $testimonials.owlCarousel({  
                items               : 1,
                loop                : loop,
                autoplay            : '. $slider['autoplay'] .',
                autoplayTimeout     : '. $slider['autoplaytimeout'] .',
                autoplayHoverPause  : '. $slider['autoplayhoverpause'] .',
                dots                : '. $slider['dots'] .',
                rtl                 : '. $slider['rtl'] .',
              });
          }
        });
        </script>
        ';
      }
  }
  
 endif;