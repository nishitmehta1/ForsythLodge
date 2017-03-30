<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Team Grid shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS


if ( class_exists('Plethora_Shortcode') && class_exists('Plethora_Posttype_Profile') && !class_exists('Plethora_Shortcode_Profilegrid') ):

	/**
	 * @package Plethora Framework
	 */

	class Plethora_Shortcode_Profilegrid extends Plethora_Shortcode { 

      public static $feature_title         = "Profiles Grid Shortcode";       // Feature display title  (string)
      public static $feature_description   = "";                              // Feature display description (string)
      public static $theme_option_control  = true;                            // Will this feature be controlled in theme options panel ( boolean )
      public static $theme_option_default  = true;                            // Default activation option status ( boolean )
      public static $theme_option_requires = array( 'posttype' => 'profile'); // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                            // Dynamic class construction ? ( boolean )
      public static $dynamic_method        = false;                           // Additional method invocation ( string/boolean | method name or false )
      public $wp_slug                      = 'profilegrid';                   // this should be the WP slug of the content element ( WITHOUT the prefix constant )
      public static $assets                = array(
                                                array( 'script' => 'svgloader-snap' ),  
                                                array( 'script' => 'svgloader' ),       
                                                array( 'script' => 'svgloader-init' )       
                                             );

      public  function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                      'base'              => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'              => esc_html__('Profiles Grid', 'plethora-framework'),
                      'description'       => esc_html__('Build a grid with person profiles', 'plethora-framework'),
                      'class'             => '',
                      'weight'            => 1,
                      'category'          => esc_html__('Posts Grids', 'plethora-framework'),
                      'admin_enqueue_js'  => array(), 
                      'admin_enqueue_css' => array(),
                      'icon'              => $this->vc_icon(), 
                      // 'custom_markup'     => $this->vc_custom_markup( 'Profiles Grid' ), 
                      'params'            => $this->params(), 
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
                      "param_name"    => "profiles",
                      "type"          => "dropdown_posts",
                      "type_posts"    => array("profile"), // 'post', 'page' or any custom post type slug. 
                      "heading"       => esc_html__("Select Profiles", 'plethora-framework'),
                      "description"   => esc_html__('Check the profiles you want to be displayed in grid. Use \'Ctrl + click\' to select multiple profiless', 'plethora-framework'),
                      "admin_label"   => false,                                               
                  ),                  
                  array(
                      "param_name"    => "columns",
                      "type"          => "value_picker",
                      "picker_type"   => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                      "picker_cols"   => "4",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                      "heading"       => esc_html__("Grid columns", 'plethora-framework'),
                      "value"         => '4',
                      "values_index"  => array('1'=>'1', '2'=>'2','3'=>'3', '4'=>'4'),
                      "description"   => esc_html__("Select how many profiles to display per row", 'plethora-framework'),
                      "admin_label"   => false,                                               
                  ),
                  array(
                      "param_name"    => "orderby",
                      "type"          => "value_picker",
                      "picker_type"   => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                      "picker_cols"   => "4",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                      "heading"       => esc_html__('Order by', 'plethora-framework'),
                      "value"         => 'title',
                      "values_index"  => array( 
                          esc_html__('Name', 'plethora-framework')=>'title', 
                          esc_html__('Date', 'plethora-framework')=>'date', 
                          esc_html__('Profile order', 'plethora-framework')=>'menu_order', 
                          esc_html__('Random', 'plethora-framework')=>'rand'),
                      "description"   => esc_html__("Select order", 'plethora-framework'),
                      "admin_label"   => false,                                               
                  ),
                  array(
                      "param_name"    => "order",
                      "type"          => "value_picker",
                      "picker_type"   => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                      "picker_cols"   => "4",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                      "heading"       => esc_html__('Ascending/Descending Order', 'plethora-framework'),
                      "value"         => 'ASC',
                      "values_index"   => array( esc_html__('Ascending', 'plethora-framework') => 'ASC' , esc_html__('Descending', 'plethora-framework') => 'DESC' ),
                      "description"   => esc_html__("Select order", 'plethora-framework'),
                      "admin_label"   => false,                                               
                  ),

                  array(
                      "param_name"    => "featuredimage",
                      "type"          => "switcher",
                      "heading"       => esc_html__("Show Featured Image", 'plethora-framework'),
                      "value"         => array( esc_html__('Yes', 'plethora-framework') => '1', esc_html__('No', 'plethora-framework') => '0'),
                      "description"   => esc_html__("Show photo set on each profile post ( if yes, please make sure that you have set an image for all profiles selected )", 'plethora-framework'),
                      "admin_label"   => false,                                               
                  ),
                  array(
                      "param_name"    => "color_set",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Color Set", 'plethora-framework'),
                      "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc', 
                                          'prepend_default' => true
                                           )),
                      "description"   => esc_html__("Choose a color setup for this section.", 'plethora-framework'),
                      "admin_label"   => false 
                  ),
                  array(
                      "param_name"    => "excerpt",
                      "type"          => "switcher",
                      "heading"       => esc_html__("Show Excerpt ", 'plethora-framework'),
                      "value"         => array( esc_html__('Yes', 'plethora-framework') => '1', esc_html__('No', 'plethora-framework') => '0'),
                      "description"   => esc_html__("Show excerpt text set on each profile post", 'plethora-framework'),
                      "admin_label"   => false,                                               
                  ),

                  array(
                      "param_name"    => "socials",
                      "type"          => "switcher",
                      "heading"       => esc_html__("Show Social Icons ", 'plethora-framework'),
                      "value"         => array( esc_html__('Yes', 'plethora-framework') => '1', esc_html__('No', 'plethora-framework') => '0'),
                      "description"   => esc_html__("Show social icons set on each profile post", 'plethora-framework'),
                      "admin_label"   => false,                                               
                  ),
                  array(
                      "param_name"    => "link_to",
                      "type"          => "value_picker",
                      "picker_type"   => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                      "picker_cols"   => "4",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                      "heading"       => esc_html__("Link To", 'plethora-framework'),
                      "value"         => 'author-ajax',
                      "values_index"  => array(
                                          esc_html__('Profile Page ( ajax )', 'plethora-framework')   => 'author-ajax', 
                                          esc_html__('Profile Page ( normal )', 'plethora-framework') => 'author-normal', 
                                          esc_html__('Blog Articles', 'plethora-framework')           => 'author-blog', 
                                          esc_html__('No Link', 'plethora-framework')                 => 'none', 
                                          ),
                      "description"   => esc_html__("Show social icons set on each profile post", 'plethora-framework'),
                      "admin_label"   => false,                                               
                  ),
                  array(
                      "param_name"    => "link_button_text",
                      "type"          => "textfield",
                      "heading"       => esc_html__('Set the Link button text.', 'plethora-framework'),
                      "value"         => 'More',
                      "description"   => esc_html__("This is the text that will be displayed on the Button that links to the profile page.", 'plethora-framework'),
                  ),
                  array(
                      "param_name"    => "button_style",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h4",                                               
                      "class"         => "vc_hidden",                                         
                      "heading"       => esc_html__("Button styling", 'plethora-framework'),      
                      "value"         => array(
                        'Default'   => 'btn-default',
                        'Primary'   => 'btn-primary',
                        'Secondary' => 'btn-secondary',
                        'White'     => 'btn-white',
                        'Success'   => 'btn-success',
                        'Info'      => 'btn-info',
                        'Warning'   => 'btn-warning',
                        'Danger'    => 'btn-danger',
                        'Inverse'    => 'btn-inverse',
                        ),
                      "admin_label"   => false,                                              
                    ),
                    array(
                      "param_name"    => "button_size",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h4",                                               
                      "class"         => "vc_hidden",                                          
                      "heading"       => esc_html__("Button size", 'plethora-framework'),      
                      "value"         => array(
                        'Default'     =>'',
                        'Small'       =>'btn-sm',
                        'Extra Small' =>'btn-xs'
                        ),
                      "admin_label"   => false,                                              
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
       public function content( $atts, $content ) {

          // EXTRACT USER INPUT
          extract( shortcode_atts( array( 
            'profiles'         => '',
            'columns'          => '4',
            'orderby'          => 'title',
            'order'            => 'ASC',
            'featuredimage'    => '1',
            'color_set'        => '',
            'excerpt'          => '1',
            'socials'          => '1',
            'button_style'     => 'btn-default',
            'button_size'      => '',
            'link_to'          => 'author-ajax',
            'link_button_text' => "More"
            ), $atts ) );

          // Set columns valueS
          switch ( $columns ) {
              case '1':
                $col_class = 'col-sm-12 col-md-12';
                  break;
              case '2':
                $col_class = 'col-sm-6 col-md-6';
                  break;
              case '3':
                $col_class = 'col-sm-6 col-md-4';
                  break;
              case '4':
                $col_class = 'col-sm-6 col-md-3';
                  break;
              default:
                $col_class = 'col-sm-6 col-md-3';
          }

          // SET POST IDS 
          $profile_post_ids = ($profiles != '') ? explode(',', $profiles) : array();

          $link_to_class = ( $link_to === "author-ajax" )? "linkify" : "";

          $shortcode_atts = array (
                                  'content'       => $content,  
                                  'profiles'      => $profiles, 
                                  'orderby'       => $orderby, 
                                  'order'         => $order, 
                                  'featuredimage' => $featuredimage == '1' ? true : false, 
                                  'color_set'     => $color_set,
                                  'excerpt'       => $excerpt, 
                                  'socials'       => $socials,
                                  'link_to'       => $link_to_class,
                                  'button_style'  => $button_style, 
                                  'button_size'   => $button_size, 
                                  'col_class'     => $col_class, 
                                 );

          // QUERY ARGUMENTS
          $args = array(
            'posts_per_page'      => '-1',
            'ignore_sticky_posts' => 0,
            'post_type'           => 'profile',
            'order'               => $order,
            'orderby'             => $orderby,
            'post__in'            => $profile_post_ids
            );

          $post_query = new WP_Query($args);
          $shortcode_atts['profiles'] = array();

          /*** POSTS FILTERS SECTION >>> ***/

          // ASSIGN POST RESULTS TO A VARIABLE TO GET ACTIVE CATEGORIES ONLY
          if ( $post_query->have_posts() ) {

            while ( $post_query->have_posts() ) { 

              $post_query->the_post();
              $postID          = get_the_ID();
              $social_settings = Plethora_Theme::option( THEMEOPTION_PREFIX .'profile-social', array(), $postID );
              $social_profiles = array();

              foreach ( $social_settings["social_url"] as $key => $value) {

                if ( $value != "" ){

                  $social_icon = $social_settings["social_icon"][$key];
                  $value       = ( $social_icon == "fa-envelope") ? "mailto:" . $value : $value;
                  $value       = ( $social_icon == "fa-skype") ? "callto:" . $value : $value;
                  array_push( $social_profiles, array( "social_url" => $value, "social_icon" => $social_icon ));

                  }
              }

              // GET EXCERPT OR CONTENT
              if ( $excerpt ) { 
                if ( has_excerpt( $postID) ) {

                  $content = get_the_excerpt();

                }  else {

                  $content = get_the_content();
                  $content = strip_tags( $content );
                  $content = substr( $content, 0, "150" );
                  if ( strlen($content) > 0) $content .= " [...]";

                }
              }

              $permalink = ( $link_to === "none" )? "#team_profile" : get_the_permalink();

              if ( $link_to == "author-blog" ) {
                
                $permalink = "#team_profile"; // just the default, if nothing found here
                $userID    = Plethora_Theme::option( METAOPTION_PREFIX .'profile-user', 0, $postID );
                if ( $userID !== 0 ) {

                  $userNiceName = get_user_by('id', $userID )->user_nicename;
                  $permalink    = site_url() . "/author/" . $userNiceName;
                } 
              }
 
              // PLENOTE: Please clarify why infobar credits should be present here!
              $image = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), 'large' );
              $image = is_array( $image ) ? $image[0] : '';
              array_push( $shortcode_atts['profiles'], 
                array( 
                  "name"            => get_the_title(), 
                  "image"           => $image,
                  "content"         => $content,
                  "permalink"       => $permalink, 
                  "link_button"     => ( $link_to == "none" )? NULL : TRUE,
                  "subtitle_text"   => Plethora_Theme::option( METAOPTION_PREFIX .'profile-subtitle-text', '', $postID ),
                  "infobarcredits"  => Plethora_Theme::option( THEMEOPTION_PREFIX .'footer-infobarcreds', '', $postID ),
                  "profile_quote"   => Plethora_Theme::option( METAOPTION_PREFIX .'profile-quote', '', $postID ),
                  "social_profiles" => $socials && ! empty( $social_profiles  )  ? $social_profiles : NULL,
                  "button_text"     => esc_attr($link_button_text)
                )
              );

            }
          }

        wp_reset_postdata();    

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );
     
       }

	}
	
 endif;