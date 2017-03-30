<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				      (c) 2013-2015

File Description: Image Post Shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_ImagePost') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_ClassicPost extends Plethora_Shortcode { 

      public static $feature_title         = "Classic Post";          // FEATURE DISPLAY TITLE 
      public static $feature_description   = "";                      // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                    // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                    // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array();                 // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                    // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;                   // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public $wp_slug                      = 'classic';               // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $assets                = array(
                                                array( 'script' => 'svgloader-snap' ),  
                                                array( 'script' => 'svgloader' ),       
                                                array( 'script' => 'svgloader-init' )       
                                             );

      public function __construct() {

          // MAP SHORTCODE SETTINGS ACCORDING TO VC DOCUMENTATION ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'        => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'        => esc_html__('Classic Post', 'plethora-framework'),
                      'description' => esc_html__('Image/icon and content', 'plethora-framework'),
                      'class'       => '',
                      'weight'      => 1,
                      'category'    => esc_html__('Plethora Shortcodes', 'plethora-framework'),
                      'icon'        => $this->vc_icon(), 
                      'params'      => $this->params(), 
                      );
          $this->add( $map );          // ADD SHORTCODE

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
                  "param_name" => "layout_styling",                                  
                  "type"       => "dropdown",                                                                                
                  "heading"    => esc_html__("Layout Styling", 'plethora-framework'),      
                  "value"      => array(
                                    'Default'     =>'',
                                    'Split 33'    =>'split_33'
                                  ),
                ),
                array(
                  "param_name"       => "post_category",
                  "type"             => "dropdown",
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__("Post Category", 'plethora-framework'),
                  "value"            => self::getCategories(), 
                  "description"      => esc_html__("Select the Category from where to fetch the post.", 'plethora-framework'),
                ),
                array(
                  "param_name"       => "post_offset",
                  "type"             => "dropdown",
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__("Post Offset", 'plethora-framework'),
                  "description"      => esc_html__("Display most recent post, last post or use a custom offset.", 'plethora-framework'),
                  "value"            => array(
                                          esc_html__('Most Recent','plethora-framework')   => '1',
                                          esc_html__('Last','plethora-framework')          => 'last',
                                          esc_html__('Custom Offset','plethora-framework') => 'custom'
                                        ), 
                ),
                array(
                  "param_name"       => "post_offset_custom",                                  
                  "type"             => "textfield",                                        
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  'heading'          => esc_html__("Custom Post Offset", 'plethora-framework'),
                  'description'      => esc_html__('Use inverse numbers to get last resulst: 2 will get the third post, -2 will get the third post from the end.','plethora-themes'),
                  'dependency'       => array(
                                          'element' => 'post_offset',
                                          'value'   => array('custom')
                                        )
                ),
                array(
                  "param_name"       => "subtitle_option",
                  "type"             => "dropdown",
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__("Display Subtitle", 'plethora-framework'),
                  "description"      => esc_html__("Display Post Subtitle, if exists on selected post", 'plethora-framework'),
                  "value"            => array(
                                          esc_html__('Yes','plethora-framework') => 'post_subtitle',
                                          esc_html__('No','plethora-framework')  => 'none'
                                        ),
                ),
                array(
                  "param_name"       => "media_ratio",
                  "type"             => "value_picker",
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__('Media Display Ratio', 'plethora-framework'),
                  "description"      => esc_html__( 'Force a specific display ratio for post image', 'plethora-framework'),
                  "picker_type"      => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                  "picker_cols"      => "3",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                  "value"            => 'stretchy_wrapper ratio_16-9',     
                  "values_index"     => Plethora_Module_Style::get_options_array( array( 
                                          'type'   => 'stretchy_ratios', 
                                          'use_in' => 'vc', 
                                        )),            
                ),
                array(
                  "param_name"       => "image_valign",
                  "type"             => "dropdown",
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__("Image Vertical Align", 'plethora-framework'),
                  "description"      => esc_html__("Select from Top, Center, Bottom vertical alignment for the photo.", 'plethora-framework'),
                  "value"            => Plethora_Module_Style::get_options_array( array( 
                                        'type'   =>'bgimage_valign',
                                        'use_in' => 'vc'
                                        )), 
                ),
                array(
                  "param_name"       => "show_post_meta",
                  "type"             => "dropdown",
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__("Show Post Meta", 'plethora-framework'),
                  "description"      => esc_html__("Show Post meta such as Date, Author, etc..", 'plethora-framework'),
                  "value"            => array( 
                                            esc_html__('Show Date and Author', 'plethora-framework') => 'date_author', 
                                            esc_html__('Show Date', 'plethora-framework')            => 'date',
                                            esc_html__('Show Author', 'plethora-framework')          => 'author',
                                            esc_html__('None', 'plethora-framework')                 => ''
                                        ),
                ),
                array(
                  "param_name"       => "target_type",
                  "type"             => "dropdown",
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__("Post Link Behavior", 'plethora-framework'),
                  "description"      => esc_html__("Open Image Post in a new window, on the same page or without page reload via Ajax.", 'plethora-framework'),
                  "value"            => array( 
                                            esc_html__('Open in new Winow', 'plethora-framework')            => '_blank', 
                                            esc_html__('Open on same page', 'plethora-framework')            => '',
                                            esc_html__('Open on same page using Ajax', 'plethora-framework') => 'ajax'
                                        ),
                ),

               // BUTTON OPTIONS
                array(
                  "param_name"    => "button_text",
                  "type"          => "textfield",                                        
                  "class"         => "vc_hidden",                                                    
                  'group'         => esc_html__( 'Button', 'plethora-framework' ),
                  "heading"       => esc_html__("Button text ( no HTML please )", 'plethora-framework'),
                  "value"         => esc_html__( 'More', 'plethora-framework' ),                            
                ),
                array(
                  "param_name"       => "button_size",                                  
                  "type"             => "dropdown",                                        
                  "class"            => "vc_hidden",                                          
                  'group'            => esc_html__( 'Button', 'plethora-framework' ),
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__("Button size", 'plethora-framework'),      
                  "value"            => array(
                                          'Default'     =>'btn',
                                          'Large'       =>'btn-lg',
                                          'Small'       =>'btn-sm',
                                          'Extra Small' =>'btn-xs'
                                        ),
                ),
                array(
                  "param_name"       => "button_style",                                  
                  "type"             => "dropdown",                                        
                  'group'            => esc_html__( 'Button', 'plethora-framework' ),
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__("Button styling", 'plethora-framework'),      
                  "value"            => array(
                                          'Default'   => 'btn-default',
                                          'Primary'   => 'btn-primary',
                                          'Secondary' => 'btn-secondary',
                                          'White'     => 'btn-white',
                                          'Success'   => 'btn-success',
                                          'Info'      => 'btn-info',
                                          'Warning'   => 'btn-warning',
                                          'Danger'    => 'btn-danger',
                                          'Text-Link' => 'btn-link',
                                        ),
                ),
                array(
                  "param_name"       => "button_with_icon",
                  "type"             => "dropdown",
                  'group'            => esc_html__( 'Button', 'plethora-framework' ),
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__('Button icon', 'plethora-framework'),
                  "value"            => array( 
                                        esc_html__('No', 'plethora-framework') => 0,
                                        esc_html__('Yes', 'plethora-framework')  => 'with-icon',
                    ),
                ),
                array(
                  "param_name"       => "button_icon",
                  "type"             => "iconpicker",
                  "value"            => 'fa fa-ambulance',
                  'group'            => esc_html__( 'Button', 'plethora-framework' ),
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__('Select icon', 'plethora-framework'),
                  'settings'         => array(
                                            'type'         => 'plethora',
                                            'iconsPerPage' => 56, // default 100, how many icons per/page to display
                                        ),
                  'dependency'       => array( 
                                            'element' => 'button_with_icon', 
                                            'value'   => array('with-icon'),  
                                        )
                ),
                array(
                  "param_name"       => "button_icon_align",
                  "type"             => "dropdown",
                  'group'            => esc_html__( 'Button', 'plethora-framework' ),
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  "heading"          => esc_html__('Button icon align', 'plethora-framework'),
                  "description"      => ' ',
                  "value"            => array( 
                                            esc_html__('Right', 'plethora-framework') => '',
                                            esc_html__('Left', 'plethora-framework')  =>'icon-left',
                                        ),
                  'dependency'       => array( 
                                            'element' => 'button_with_icon', 
                                            'value'   => array('with-icon'),  
                                        )
                ),
                array(
                  "param_name"  => "title",                                  
                  "type"        => "textfield",                                        
                  "holder"      => "h3",                                               
                  "class"       => "plethora_vc_title",
                  'group'       => esc_html__( 'Customize Elements', 'plethora-framework' ),
                  "heading"     => esc_html__("Custom Title*", 'plethora-framework'),
                  "description" => esc_html__("* no HTML please. Overrides Post Title", 'plethora-framework'),
                  "value"       => '',                                     
                ),
                array(
                  "param_name"       => "subtitle",                                  
                  "type"             => "textfield",                                        
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  'group'            => esc_html__( 'Customize Elements', 'plethora-framework' ),
                  "heading"          => esc_html__("Custom Subtitle *", 'plethora-framework'),
                  "description"      => esc_html__("* No HTML please. Overrides Post Subtitle.", 'plethora-framework'),
                ),
                array(
                  "param_name"       => "description",                                  
                  "type"             => "textfield",                                        
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  'group'            => esc_html__( 'Customize Elements', 'plethora-framework' ),
                  "heading"          => esc_html__("Custom Description *", 'plethora-framework'),
                  "description"      => esc_html__("* Accepts HTML. Overrides Post Excerpt.", 'plethora-framework'),
                ),
                // CUSTOM BACKGROUND IMAGE
                array(
                  "param_name"       => "image",                                  
                  "type"             => "attach_image",                                        
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  'group'            => esc_html__( 'Customize Elements', 'plethora-framework' ),
                  "heading"          => esc_html__("Custom Image", 'plethora-framework'),      
                  "description"      => esc_html__('Overrides Post featured image. Related image display options set on "General Tab" will be applied.', 'plethora-framework'),      
                ),
                array(
                  'param_name'       => 'el_class',
                  'type'             => 'textfield',
                  'edit_field_class' => 'vc_col-sm-6 vc_column',
                  'heading'          => esc_html__('Extra Class', 'plethora-framework'),
                  'description'      => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
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

       public function getCategories(){

          $output = array( 'All' => '' );
          foreach ( Plethora_WP::categories() as $key => $value) $output[$key] = $value;
          return $output;

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
            'layout_styling'     => '',
            'post_category'      => '',
            'post_offset'        => '1',
            'post_offset_custom' => '1',
            'show_post_meta'     => 'date_author',
            'image'              => '',
            'image_valign'       => '',
            'media_ratio'        => 'stretchy_wrapper ratio_16-9',
            'icon'               => '',
            'target_type'        => '_blank',
            'title'              => '',
            'subtitle'           => '',
            'subtitle_option'    => 'post_subtitle',
            'description'        => '',
            'button_text'        => 'More',
            'button_size'        => 'btn',
            'button_style'       => 'btn-primary',
            'button_with_icon'   => '',
            'button_icon'        => '',
            'button_icon_align'  => '',
            'el_class'           => '',
            'css'                => ''
            ), $atts ) );

          // PREPARE TEMPLATE VALUES

          $image = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
          $image = isset($image[0]) ? $image[0] : '';

          $shortcode_atts = array (
                                  //'class'               => esc_attr( $class ) . ' ' . $image_valign,
                                  'layout_styling' => $layout_styling,
                                  'image_valign'   => $image_valign
                                 );

          /* QUERYING POSTS */

          $args = array(
            'posts_per_page'      => 1,
            'ignore_sticky_posts' => 1,
            'cat'                 => $post_category
          );

          switch ($post_offset) {

            case '1':
              $args['order'] = 'DESC';
              break;

            case 'last':
              $args['order'] = 'ASC';
              break;

            case 'custom':
              $post_offset_custom      = intval($post_offset_custom);
              $post_offset_custom_sign = ( $post_offset_custom > 0 ) - ( $post_offset_custom < 0 );
              $post_offset_order       = ( $post_offset_custom_sign == 0 )? 1 : $post_offset_custom_sign;  // 1, -1, 0
              $args['order']           = ( $post_offset_order == 1 )? 'DESC' : 'ASC';
              $args['offset']          = abs($post_offset_custom);
              break;
           
          }

          $post_query = new WP_Query($args);

          if ( $post_query->have_posts() ) {

              $post_query->the_post();
              $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );  // CACHE get_the_ID() IF USED MORE THAN ONCE

              /* FILTERING VALUES FOR MUSTACHE TEMPLATE */

              $title                         = esc_attr( trim( $title ));
              $subtitle                      = esc_attr( trim( $subtitle ));
              $description                   = esc_attr( trim( $description ));
              $shortcode_atts['link_url']    = get_the_permalink();
              $shortcode_atts['link_title']  = get_the_title();
              $shortcode_atts['link_target'] = ( $target_type == '_blank' )? '_blank' : '';
              $shortcode_atts['ajax']        = ( $target_type == 'ajax' )? 'linkify' : '';
              $shortcode_atts['image']       = ( !empty($image) ) ? esc_url( $image ) : esc_url( $thumb_url[0] );
              $shortcode_atts['title']       = ( $title == "" )? get_the_title() : $title;


              switch ( $show_post_meta ) {
                case 'date_author':
                  $post_meta = get_the_date() . esc_html__( " By ", "plethora-theme" ) . get_the_author();
                  break;
                case 'date':
                  $post_meta = get_the_date();
                  break;
                case 'author':
                  $post_meta = esc_html__( "By ", "plethora-theme" ) . get_the_author();
                  break;
                case '':
                  $post_meta = "";
                  break;
              }

              $post_subtitle = get_post_meta ( get_the_ID(), "ple-post-subtitle-text", true );

              $shortcode_atts['post_meta']   = $post_meta;

              if ( empty( $subtitle ) ) {

                switch ( $subtitle_option ) {
                   case 'post_subtitle':
                    $shortcode_atts['subtitle'] = $post_subtitle;
                     break;
                   case 'none':
                     $shortcode_atts['subtitle'] = '';
                     break;
                 } 

              } else {

                     $shortcode_atts['subtitle'] = $subtitle;
              }

              $shortcode_atts['description'] = ( $description == "") ? get_the_excerpt() : $description;

          } else {

              $shortcode_atts['title'] = esc_html__('Post not found','plethora-framework');

          } 

          $shortcode_atts['media_ratio'] = $media_ratio;

          // BUTTON OPTIONS

          $shortcode_atts['button_text']       = $button_text;
          $shortcode_atts['button_size']       = $button_size;
          $shortcode_atts['button_style']      = $button_style;
          $shortcode_atts['button_with_icon']  = $button_with_icon;
          $shortcode_atts['button_icon']       = $button_icon;
          $shortcode_atts['button_icon_align'] = $button_icon_align;

          if ( $button_with_icon === 'with-icon' ){
            if ( $button_icon_align === 'icon-left' ){
              $shortcode_atts["button_icon_align_left"] = TRUE;
            } else {
              $shortcode_atts["button_icon_align_right"] = TRUE;
            }
          }

          // CSS OPTIONS
          $shortcode_atts['el_class'] = esc_attr( $el_class );
          $shortcode_atts['css'] = esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) );
          
          // RESET QUERY AND RETURN TEMPLATE
          wp_reset_postdata();  
          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

       }
	}
	
 endif;