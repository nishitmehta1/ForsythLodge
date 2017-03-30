<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Knowledgebase Search Results shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Kbsearchresults') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Kbsearchresults extends Plethora_Shortcode { 

    public static $feature_title         = "KB Search Results Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public $wp_slug                      =  'kbsearchresults';           // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
    public static $assets;

    public $search_query;

    public function __construct() {

      add_action( 'init', array( $this, 'map' ) );
    }

    public function map() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'          => esc_html__("KB Search Results", 'plethora-framework'), 
                    'description'   => esc_html__('for plethorathemes.com', 'plethora-framework'), 
                    'class'         => '', 
                    'weight'        => 1, 
                    'category'      => 'Content', 
                    'icon'          => $this->vc_icon(), 
                    // 'custom_markup' => $this->vc_custom_markup( 'Button' ), 
                    'params'        => $this->params(), 
                    );
        // Add the shortcode
        $this->add( $map );
    }

    /** 
    * Returns shortcode parameters INDEX for VC panel
    * @return array
    */
    public function params_index() {

        $params_index['post_type'] = array( 
              "param_name" => "post_type",                                  
              "type"       => "dropdown",                                        
              "holder"     => "h4",                                               
              "class"      => "text-uppercase",                                         
              "heading"    => esc_html__("Results Type", 'plethora-framework'),      
              "value"      => array(
                                esc_html__( 'Knowledge Base Posts', 'plethora-framework' )   => 'kb',
                                esc_html__( 'Documentation Posts', 'plethora-framework' )   => 'doc',
                              ),
        );

        $params_index['el_class'] = array( 
              'param_name'  => 'el_class',
              'type'        => 'textfield',
              'heading'     => esc_html__('Extra Class', 'plethora-framework'),
              'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
        );

        $params_index['css'] = array( 
              "param_name"    => "css",
              "type"          => "css_editor",
              'group'         => esc_html__( 'Design options', 'plethora-framework' ),
              "heading"       => esc_html__('CSS box', 'plethora-framework'),
        );

      return $params_index;
    }

     /** 
     * Configure parameters displayed
     * Will be displayed all items from params_index() with identical 'id'
     * This method should be used for extension class overrides
     *
     * @return array
     */
     public function params_config() {

        $params_config = array(
            array( 
              'id'         => 'post_type', 
              'default'    => 'kb',
              'field_size' => '',
              ),
            array( 
              'id'         => 'el_class', 
              'default'    => '',
              'field_size' => '',
              ),
            array( 
              'id'         => 'css', 
              'default'    => '',
              'field_size' => '',
              ),
        );

        return $params_config;
     }


    /** 
    * Returns shortcode content OR content template
    *
    * @return array
    * @since 1.0
    *
    */
    public function content( $atts, $content = null ) {

        // Extract user input
        extract( shortcode_atts( $this->get_default_param_values(), $atts ) );

        // Set search query
        $s = isset( $_GET['search_term'] ) ? urldecode( $_GET['search_term'] ) : '';
        $this->set_search_query( $post_type, $s );

        // Place all values in 'shortcode_atts' variable
        $shortcode_atts = array (
                                'search_term'     => $s,
                                'search_type'     => $post_type === 'kb' ? esc_html__( 'Knowledge Base', 'plethora-framework' ) : esc_html__( 'Documentation', 'plethora-framework' ) ,
                                'results'         => $this->get_results_items( $post_type, $s ),
                                'results_count'   => $this->get_results_count(),
                                'results_nofound' => $this->get_results_count() ? false : true ,
                               );

        // Reset search query
        $this->reset_search_query();

        $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), $this->wp_slug, $atts );
        $return = '<div class="ple_kbsearchresults wpb_content_element '. esc_attr( $el_class ) .' '. esc_attr( $css_class ) .'">';
        $return .= Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "force_template_part" => array( 'templates/shortcodes/kbsearchresults' ) ) );
        $return .= '</div>';
        return $return;
    }

    public function set_search_query( $post_type, $s ) {

      $args['s'] = $s;
      $args['post_type'] = $post_type;
      $args['posts_per_page'] = -1;
      $args['orderby'] = 'none';
      $args['tax_query'] = array(
                  array(
                    'taxonomy' => 'kb-product',
                    'field'    => 'slug',
                    'terms'    => isset( $_GET['kb-product'] ) ? urldecode( $_GET['kb-product'] ) : '',
                  )
      );
      if ( isset( $_GET['kb-product'] ) && !empty( $s ) ) {

        $this->search_query = new WP_Query($args);

      }
    }

    public function reset_search_query() {

      wp_reset_postdata();
    }

    public function get_results_items( $post_type, $s ) {

      $results = array(); 

      if ( is_object( $this->search_query ) && $this->search_query->have_posts() ) {

        while ( $this->search_query->have_posts() ) {
          
          $this->search_query->the_post();

          $title = get_the_title();
          $content = $post_type==='doc' ? get_the_content() : get_the_excerpt() ;
          $content = str_replace( '[&hellip;]', '', $content );
          if ( $post_type === 'doc' ) {

            $product = urldecode( $_GET['kb-product'] );
            $url     = get_permalink( get_page_by_path( 'support-center/'. $product .'/'. $product .'-documentation' ) );
            $url     = $url.'#'. $this->search_query->post->post_name;

          } else {

            $url = get_permalink( get_the_id() );
          }

          $results[] = array(
             'id'      => get_the_id(),
             'title'   => $this->get_highlighted( 'title', $title, $s ),
             'excerpt' => $this->get_highlighted( 'content', $content, $s ),
             'url'     => $url,
          );
        }
      }

      return $results;
    }

    public function get_results_count() {

      $count = 0;

      if ( is_object( $this->search_query ) ) {

        $count = $this->search_query->found_posts;
        return $count;
      }
    }

    public function get_highlighted( $type, $str, $search ) {

      $string = '';
      if ( trim($search) == '' ) { return '';}
      $search_terms = explode(" ", trim($search));
      $newstring = wp_trim_words( $str, 60, '' );
      foreach ( $search_terms as $search_item){ 
          $occurrences = substr_count(strtolower($newstring), strtolower($search_item));
          $match = array();
       
          for ($i=0;$i<$occurrences;$i++) {
              $match[$i] = stripos($newstring, $search_item, $i);
              $match[$i] = substr($newstring, $match[$i], strlen($search_item));
              $newstring = str_replace($match[$i], '[#]'.$match[$i].'[@]', $newstring);
          }
       
          $newstring = str_replace('[#]', '<mark>', $newstring);
          $newstring = str_replace('[@]', '</mark>', $newstring);
      }

      $pattern = "([^.]*?";
      $pattern .= $search;
      $pattern .= "[^.]*[\.*\?*\!])";
      $pre_worked = preg_match( $pattern, $newstring, $matches );
      if ( $type === 'content' && $pre_worked ) {

        return $matches[0];
      }

      return $newstring;
    }
	}
	
 endif;