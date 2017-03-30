<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				          (c) 2016

File Description: Instagram Feed shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Instagramfeed') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Instagramfeed extends Plethora_Shortcode { 

    public static $feature_title         = "Instagram Feed Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public $wp_slug                      = 'instagram_feed';    // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
    public static $assets;

  
    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'          => esc_html__("Instagram Feed", 'plethora-framework'), 
                    'description'   => esc_html__('Requires Instagram Feed plugin activated', 'plethora-framework'), 
                    'class'         => '', 
                    'weight'        => 1, 
                    'icon'          => $this->vc_icon(), 
                    // 'custom_markup' => $this->vc_custom_markup( 'Button' ), 
                    'params'        => $this->params(), 
                    );
        // Add the shortcode
        $this->add( $map );

    }

      /** 
      * Returns shortcode parameters INDEX for VC panel
      *
      * @return array
      * @since 1.0
      *
      */
      public function params_index() {

        $params_index['id'] = array( 
                      "param_name"    => "id",                                  
                      "type"          => "textfield",
                      "heading"       => esc_html__('Instagram User ID(s)', 'plethora-framework'),
                      "description"   => sprintf( esc_html__('An Instagram User ID or more. Separate multiple IDs by commas. If left blank, it will be used the one given on the plugin settings. %1$sNote that, in any case this shortcode will not work unless you have set at least an access token on the plugin\'s settings.%2$s', 'plethora-framework'), '<span style="color:darkorange">', '</span>' ),
        );

        $params_index['num'] = array( 
                      "param_name"  => "num",                                  
                      "type"        => "textfield",
                      "heading"     => esc_html__( 'Image Results On Load', 'plethora-framework' ),
                      "description" => esc_html__( "The number of images to display initially. Max: 33", 'plethora-framework'),
        );

        $params_index['cols'] = array( 
                      "param_name"  => "cols",                                  
                      "type"        => "textfield",
                      "heading"     => esc_html__( 'Columns', 'plethora-framework' ),
                      "description" => esc_html__( "The number of columns in your feed. Max: 10", 'plethora-framework'),
        );

        $params_index['height'] = array( 
                      "param_name"  => "height",                                  
                      "type"        => "textfield",
                      "heading"     => esc_html__( 'Height', 'plethora-framework' ),
                      "description" => esc_html__( "The height of your feed in pixels ( default: 250 )", 'plethora-framework'),
        );

        $params_index['imageres'] = array( 
                      "param_name"  => "imageres",                                  
                      "type"        => "dropdown",
                      "heading"     => esc_html__( 'Image Quality', 'plethora-framework' ),
                      "description" => esc_html__( "The quality ( resolution ) of the displayed images.", 'plethora-framework'),
                      "value"       => array( 
                                            esc_html__('Full', 'plethora-framework')   => 'full',
                                            esc_html__('Medium', 'plethora-framework') => 'medium',
                                            esc_html__('Low', 'plethora-framework')    => 'thumb',
                                            esc_html__('Auto', 'plethora-framework')   => 'auto',
                      ),
        );

        $params_index['imagepadding'] = array( 
                      "param_name"  => "imagepadding",                                  
                      "type"        => "dropdown",
                      "heading"     => esc_html__( 'Image Padding', 'plethora-framework' ),
                      "description" => esc_html__( "The spacing around your photos", 'plethora-framework'),
                      "value"       => array( 
                                            esc_html__('No Padding', 'plethora-framework')   => '0',
                                            esc_html__('Medium Padding', 'plethora-framework') => '10',
                                            esc_html__('Full Padding', 'plethora-framework')    => '20',
                      ),
        );

        $params_index['sortby'] = array( 
                      "param_name"  => "sortby",                                  
                      "type"        => "dropdown",
                      "heading"     => esc_html__( 'Sort Images', 'plethora-framework' ),
                      "description" => esc_html__( "Sort the posts by newest to oldest or random", 'plethora-framework'),
                      "value"       => array( 
                                            esc_html__('Newest to oldest', 'plethora-framework') => 'none',
                                            esc_html__('Random', 'plethora-framework') => 'random',
                      ),
        );

        $params_index['showheader'] = array( 
                      "param_name"  => "showheader",                                  
                      "type"        => "dropdown",
                      "group"       => esc_html__('Display Elements', 'plethora-framework'),
                      "heading"     => esc_html__('Display Header', 'plethora-framework'),
                      "description" => esc_html__("Whether to show the feed header", 'plethora-framework'),
                      "value"       => array( 
                                            esc_html__('No', 'plethora-framework') => 'false',
                                            esc_html__('Yes', 'plethora-framework') => 'true',
                      ),
        );

        $params_index['showbutton'] = array( 
                      "param_name"  => "showbutton",                                  
                      "type"        => "dropdown",
                      "group"       => esc_html__('Display Elements', 'plethora-framework'),
                      "heading"     => esc_html__('Display Load More Button', 'plethora-framework'),
                      "description" => esc_html__("Whether to show the 'Load More' button", 'plethora-framework'),
                      "value"       => array( 
                                            esc_html__('No', 'plethora-framework') => 'false',
                                            esc_html__('Yes', 'plethora-framework') => 'true',
                      ),
        );

        $params_index['buttontext'] = array( 
                      "param_name"  => "buttontext",                                  
                      "type"        => "textfield",
                      "group"       => esc_html__('Display Elements', 'plethora-framework'),
                      "heading"     => esc_html__( 'Load More Button Text', 'plethora-framework' ),
                      "description" => esc_html__( "The text used for the Load More button.", 'plethora-framework'),
                      'dependency'  => array( 
                                          'element' => 'showbutton',  
                                          'value'   => array( 'true' ),   
                                          )
        );

        $params_index['showfollow'] = array( 
                      "param_name"  => "showfollow",                                  
                      "type"        => "dropdown",
                      "group"       => esc_html__('Display Elements', 'plethora-framework'),
                      "heading"     => esc_html__('Display Follow Button', 'plethora-framework'),
                      "description" => esc_html__("Whether to show the 'Follow on Instagram' button.", 'plethora-framework'),
                      "value"       => array( 
                                            esc_html__('No', 'plethora-framework') => 'false',
                                            esc_html__('Yes', 'plethora-framework') => 'true',
                      ),
        );

        $params_index['followtext'] = array( 
                      "param_name"  => "followtext",                                  
                      "type"        => "textfield",
                      "group"       => esc_html__('Display Elements', 'plethora-framework'),
                      "heading"     => esc_html__( 'Follow Button Text', 'plethora-framework' ),
                      "description" => esc_html__( "The text used for the Follow button.", 'plethora-framework'),
                      'dependency'  => array( 
                                          'element' => 'showfollow',  
                                          'value'   => array( 'true' ),   
                                          )
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
              'id'         => 'id', 
              'default'    => '',
              'field_size' => '',
              ),
            array( 
              'id'         => 'num', 
              'default'    => 4,
              'field_size' => '6',
              ),
            array( 
              'id'         => 'cols', 
              'default'    => 4,
              'field_size' => '6',
              ),
            array( 
              'id'         => 'height', 
              'default'    => 250,
              'field_size' => '6',
              ),
            array( 
              'id'         => 'imageres', 
              'default'    => 'full',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'imagepadding', 
              'default'    => '10',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'sortby', 
              'default'    => 'none',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'showheader', 
              'default'    => 'false',
              'field_size' => '',
              ),
            array( 
              'id'         => 'showbutton', 
              'default'    => 'false',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'buttontext', 
              'default'    => esc_html__( 'Load More', 'plethora-framework' ),
              'field_size' => '6',
              ),
            array( 
              'id'         => 'showfollow', 
              'default'    => 'false',
              'field_size' => '6',
              ),
            array( 
              'id'         => 'followtext', 
              'default'    => esc_html__( 'Follow Us', 'plethora-framework' ),
              'field_size' => '6',
              ),
            array( 
              'id'         => 'el_class', 
              'default'    => '',
              'field_size' => '6',
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

      if ( is_plugin_active( 'instagram-feed/instagram-feed.php' ) ) {

          // Merge user input with defaults
          $atts = shortcode_atts( $this->get_default_param_values(), $atts ) ;

          // Prepare Instagram Feed shortcode tag
          $shortcode_atts = '';
          foreach ( $atts as $att_key => $att_val ) {

              if ( $att_key === 'css' && empty( $att_val ) ) { continue; }
              if ( $att_key === 'id' && empty( $att_val ) ) { continue; }
              if ( $att_key === 'imagepadding' ) { 

                $shortcode_atts .= ' imagepaddingunit="px"';
              }
              if ( $att_key === 'height' ) { 

                $shortcode_atts .= ' heightunit="px"';
              }

              $shortcode_atts .= ' '. $att_key .'="'. $att_val .'"';
          }

          // Extract all attributes and prepare final output
          extract( $atts );
          $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), $this->wp_slug, $atts );
          $return = '<div class="ple_instagram_feed wpb_content_element '. esc_attr( $el_class ) .' '. esc_attr( $css_class ) .'">';
          $return .= do_shortcode( '[instagram-feed'.$shortcode_atts.']' );
          $return .= '</div>';
          return $return;

      } else {

        return '<div class="text-center">'. sprintf( esc_html__( '%1$sInstagram Feed%2$s plugin is not active! You should activate it OR just remove this shortcode%3$s...unless you are font of this charming message!', 'plethora-framework'), '<strong>', '</strong>', '<br>' ) .'</div>';
      }
    }
	}
	
 endif;