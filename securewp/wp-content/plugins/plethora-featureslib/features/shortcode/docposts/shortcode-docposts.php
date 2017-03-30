<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Knowledgebase Search Form shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Docposts') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Docposts extends Plethora_Shortcode { 

    public static $feature_title         = "Doc Posts Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public $wp_slug                      = 'docposts';           // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
    public static $assets                = array(
                                              array( 'script' => array( 'hideseek' ) ),
                                              array( 'script' => array( 'unveil' ) ),
                                              array( 'script' => array( 'doc' ) ), 
                                              array( 'style'  => array( 'doc' ) ), 
                                          );

    public function __construct() {

      add_action( 'init', array( $this, 'map' ) );
    }

    public function map() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'          => esc_html__("Doc Posts", 'plethora-framework'), 
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

        $params_index['product'] = array( 
              "param_name" => "product",
              "type"       => "dropdown",                                        
              "class"      => "",                                                    
              "heading"    => esc_html__( "Select Product", 'plethora-framework'),
              'value'      => $this->get_products(),
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
    * Returns products list
    * @return array
    */
    public function get_products( $default_val = true ) {

        $values = $default_val ? array( esc_html__( 'None', 'plethora-framework' ) ) : array();
        $post_taxonomy_terms = get_terms( 'kb-product' );
        if ( ! is_wp_error( $post_taxonomy_terms ) ) {

          foreach ( $post_taxonomy_terms as $term  ) {
            $values[$term->name] = $term->slug; 
          }
        }
        return $values;
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
              'id'         => 'product', 
              'default'    => '',
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

    public function get_menu_index() {

        $index      = $this->get_index();
        $menu_index = array();
        foreach ( $index as $section ) {

          $menu_index[$section['slug']]['level'] = 1;
          $menu_index[$section['slug']]['title'] = $section['title'];
          $menu_index[$section['slug']]['icon'] = ! empty( $doc['icon'] ) ? $doc['icon'] : 'fa fa-th-large';
          $docs = $section['docs'];
          foreach ( $docs as $doc ) {

            $menu_index[$doc['slug']]['level'] = 2;
            $menu_index[$doc['slug']]['title'] = $doc['title'];
            $menu_index[$doc['slug']]['icon'] = '';
          }
        }
        return $menu_index;
    }

    public function get_sections( $doc_obj ) {

      $index = $doc_obj->get_index();
      $sections = array();
      foreach ( $index as $section ) {

        $docs = array();
        foreach ( $section['docs'] as $doc ) {
          $content = apply_filters( 'the_content', $doc['doc_content'] );
          $content = str_replace(']]>', ']]&gt;', $content);
          $docs[] = array(
            'doc_id'       => $doc['doc_id'],
            'doc_slug'     => $doc['doc_slug'],
            'doc_title'    => $doc['doc_title'],
            'doc_excerpt'  => $doc['doc_excerpt'],
            'doc_content'  => $content,
            'doc_sidenote' => do_shortcode( $doc['doc_sidenote'] ),
          );
        }
        
        $sections[] = array(
          'section_id'    => $section['id'],
          'section_slug'  => 'section-'.$section['slug'],
          'section_title' => $section['title'],
          'section_desc'  => $section['desc'],
          'section_icon'  => esc_attr( $section['icon'] ),
          'section_docs'  => $docs,
        );
      }

      return $sections;
    }

    function get_side_menu( $doc_obj ) {

      $index      = $doc_obj->get_index();
      $menu_index = array();
      foreach ( $index as $section ) {

        $menu_index['section-'.$section['slug']]['level'] = 1;
        $menu_index['section-'.$section['slug']]['title'] = $section['title'];
        $menu_index['section-'.$section['slug']]['icon'] = ! empty( $section['icon'] ) ? $section['icon'] : 'fa fa-th-large';
        $docs = $section['docs'];
        foreach ( $docs as $doc ) {

          $menu_index[$doc['doc_slug']]['level'] = 2;
          $menu_index[$doc['doc_slug']]['title'] = $doc['doc_title'];
          $menu_index[$doc['doc_slug']]['icon'] = '';
        }
      }
      $html = '<ul class="nav">'."\n" ;
      $level_2_li = false;
      $level_3_li = false;
      foreach ( $menu_index as $id => $item ) {
        switch ($item['level']) {
          case 1:
            $html .= $level_3_li ? '    </ul>'."\n"  : '';
            $html .= $level_2_li ? '  </li>'."\n"  : '';
            $html .= '  <li><a href="#'.$id.'">' ;
            $html .= !empty( $item['icon'] ) ? '<i class="'. $item['icon'] .'"></i> ' : '';
            $html .= !empty($item['title']) ? $item['title'] : '' ;
            $html .= '</a>' ;
            $level_2_li = true;
            $level_3_li = false;
            break;

          case 2:
            $html .= $level_3_li == false ? "\n".'   <ul>'."\n"  : '';
            $html .= '      <li><a href="#'.$id.'">' ;
            $html .= !empty($item['title']) ? $item['title'] : '' ;
            $html .= '</a></li>'."\n" ;
            $level_3_li = true;
            break;
        }
      }
      $html .= $level_3_li ? '    </ul>'."\n"  : '';
      $html .= $level_2_li ? '  </li>'."\n"  : '';
      $html .= '</ul>'."\n" ;
      return $html;
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

      // Place all values in 'shortcode_atts' variable
      if ( !empty( $product ) ) {

        $atts = array();
        // Add product information
        $product_term = get_term_by( 'slug', $product, 'kb-product', ARRAY_A );
        $product_tax  = get_term_meta( $product_term['term_id'], '', true );
        $atts['product_title']       = $product_term['name'];
        $atts['product_desc']        = $product_term['description'];
        $atts['product_purchaseurl'] = isset( $product_tax[TERMSMETA_PREFIX .'kb-product-productpageurl'] ) ? $product_tax[TERMSMETA_PREFIX .'kb-product-productpageurl'][0] : '';
        $atts['product_docurl']      = isset( $product_tax[TERMSMETA_PREFIX .'kb-product-onlinedoc'] ) ? $product_tax[TERMSMETA_PREFIX .'kb-product-onlinedoc'][0] : '';
        // Add documentation information
        $doc_obj = new Plethora_Doc( $product );
        $atts['sections']  = $this->get_sections( $doc_obj );
        $atts['side_menu'] = $this->get_side_menu( $doc_obj );
        $atts['el_class']  = esc_attr( $el_class );
        $atts['css']       = esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) );
        return Plethora_WP::renderMustache( array( "data" => $atts, "force_template_part" => array( 'templates/shortcodes/docposts' ) ) );
      }
    }
	}
	
 endif;