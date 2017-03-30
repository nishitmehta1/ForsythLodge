<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Row shortcode

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Vcrow') ):

	/**
	 * @package Plethora
	 */

	class Plethora_Shortcode_Vcrow extends Plethora_Shortcode { 

        public static $feature_title         = "Row Shortcode";       // Feature display title  (string)
        public static $feature_description   = "";                    // Feature display description (string)
        public static $theme_option_control  = false;                 // Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_default  = true;                  // Default activation option status ( boolean )
        public static $theme_option_requires = array();               // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                  // Dynamic class construction ? ( boolean )
        public static $dynamic_method        = false;                 // Additional method invocation ( string/boolean | method name or false )

        public function __construct() {

          if ( function_exists('vc_add_params') ) { 
            // Add Plethora parameters setup
            vc_add_params( 'vc_row', $this->add_params() );
            // Modify classes
            add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, array( $this, 'modify_classes' ), 20, 3 );             

            add_action('wp_insert_post', array($this, 'set_content_has_sections'), 999, 3);
          }

        }

       /** 
       * Returns ADDITIONAL shortcode options for Visual composer 'vc_row' shortcode
       *
       * The following option IDs exist already in VC...do not use them ( as in VC 4.11 )
       * Plethora Devs: this list should be updated on each VC upate
       *    Row stretch:            'full_width'
       *    Columns gap:            'gap'
       *    Full height row:        'full_height'
       *    Columns position:       'columns_placement' ( applied if 'full_height' row is checked )
       *    Equal height:           'equal_height'
       *    Content position:       'content_placement'
       *    Use video background?:  'video_bg'
       *    YouTube link:           'video_bg_url'
       *    Parallax:               'video_bg_parallax' ( applied ONLY on video background )
       *    Parallax:               'parallax ' ( applied ONLY on image OR the 'Design options' background image )
       *    Parallax Image:         'parallax_image' ( image for Parallax...if users don't want parallax, they should use the 'Design Options' background image )
       *    Parallax speed:         'parallax_speed_bg'
       *    Row ID:                 'el_id'
       *    Extra class:            'el_class'
       *    Css Editor options:     'css' ( in general, 'css' is reserved...don't use it! )
       *    Content:                'content' ( in general, 'content' is reserved...don't use it! )
       * 
       * The following option IDs are added by Plethora Themes ( as in VC 4.11 )
       *    Color Set:              'color_set'
       *    Background:             'transparent' ( select between color set bcg color and transparency )
       *    Transparent Overlay:    'transparent_overlay'
       *    Text Align:             'align'
       *    Row Padding Top:        'row_padding_top'
       *    Row Padding Bottom:     'row_padding_bottom'
       *    Top Separator:          'sep_top'
       *    Bottom Separator:       'sep_bottom'
       *
       * @return array
       */
       public function add_params() {

          $params =  array(

                    // Start -> Theme Options
                    array(
                        "param_name"    => "color_set",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        "heading"       => esc_html__("Color Set", 'hotel-xenia'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true
                                           )),
                        "description"   => esc_html__("Choose a color setup for this section. Remember: all colors in above options can be configured via the theme options panel", 'hotel-xenia'),
 
                    ),
                   array(
                      "param_name"    => "transparent",
                      "type"          => "dropdown",
                      'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"       => esc_html__("Background Type", 'hotel-xenia'),
                      "value"         => array( 
                                            esc_html__('Color Set Background', 'hotel-xenia') => '', 
                                            esc_html__('Transparent', 'hotel-xenia')          => 'transparent' ,
                                        ),
                      "description"   => esc_html__("You may set a custom background color under 'Design Options' tab ", 'hotel-xenia'),
                   ),
                    array(
                        "param_name"    => "transparent_overlay",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Transparent Overlay", 'hotel-xenia'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'transparent_overlay', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true
                                           )),
                        "description"   => esc_html__("The transparency percentage can be configured on theme options panel", 'hotel-xenia'),
 
                    ),                    
                   array(
                        "param_name"    => "align",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Text Align", 'hotel-xenia'),
                        "value"         => array( 
                                              esc_html('Default', 'hotel-xenia')     =>'',
                                              esc_html('Left', 'hotel-xenia')     =>'text-left',
                                              esc_html('Centered', 'hotel-xenia') => 'text-center',
                                              esc_html('Right', 'hotel-xenia')    => 'text-right',
                                              esc_html('Justify', 'hotel-xenia')  => 'text-justify',
                          ),
 
                    ),
                    array(
                        "param_name"    => "row_padding_top",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Row Padding: Top", 'hotel-xenia'),
                        "value"         => array(
                                                esc_html('Default top padding', 'hotel-xenia')        => '', 
                                                esc_html('No top padding', 'hotel-xenia')             => 'padding_top_none', 
                                                esc_html('1/4 of default top padding', 'hotel-xenia') => 'padding_top_1-4', 
                                                esc_html('1/3 of default top padding', 'hotel-xenia') => 'padding_top_1-3',
                                                esc_html('1/2 of default top padding', 'hotel-xenia') => 'padding_top_1-2',
                                                esc_html('2/3 of default top padding', 'hotel-xenia') => 'padding_top_2-3', 
                                            ),
                        "description"   => esc_html__("Affects the row's top spacings", 'hotel-xenia'),
 
                    ),
                    array(
                        "param_name"    => "row_padding_bottom",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('General', 'hotel-xenia'),                                              
                        'edit_field_class' => 'vc_col-sm-6 vc_column',
                        "heading"       => esc_html__("Row Padding: Bottom", 'hotel-xenia'),
                        "value"         => array(
                                                esc_html('Default bottom padding', 'hotel-xenia')        => '', 
                                                esc_html('No bottom padding', 'hotel-xenia')             => 'padding_bottom_none', 
                                                esc_html('1/4 of default bottom padding', 'hotel-xenia') => 'padding_bottom_1-4', 
                                                esc_html('1/3 of default bottom padding', 'hotel-xenia') => 'padding_bottom_1-3',
                                                esc_html('1/2 of default bottom padding', 'hotel-xenia') => 'padding_bottom_1-2', 
                                                esc_html('2/3 of default bottom padding', 'hotel-xenia') => 'padding_bottom_2-3',  
                                            ),
                        "description"   => esc_html__("Affects the row's bottom spacings", 'hotel-xenia'),
 
                    ),
                    // End -> Color Set & Image Background Options

                    // Start -> Separators Options
                    array(
                        "param_name"    => "sep_top",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('Separators', 'hotel-xenia'),                                              
                        "heading"       => esc_html__("Top Separator", 'hotel-xenia'),
                        "value"         => array(
                                                esc_html('No', 'hotel-xenia') => '', 
                                                esc_html('Angled Positive', 'hotel-xenia') => 'separator_top sep_angled_positive_top',
                                                esc_html('Angled Negative', 'hotel-xenia') => 'separator_top sep_angled_negative_top',
                                            ),
                        "description"   => esc_html__("Will put an angled separator on top of the section", 'hotel-xenia'),
 
                    ),
                    array(
                        "param_name"    => "sep_bottom",
                        "type"          => "dropdown",
                        'group'         => THEME_DISPLAYNAME . ': '. esc_html__('Separators', 'hotel-xenia'),                                              
                        "heading"       => esc_html__("Bottom Separator", 'hotel-xenia'),
                        "value"         => array(
                                                esc_html('No', 'hotel-xenia') => '', 
                                                esc_html('Angled Positive', 'hotel-xenia') => 'separator_bottom sep_angled_positive_bottom',
                                                esc_html('Angled Negative', 'hotel-xenia') => 'separator_bottom sep_angled_negative_bottom',
                                            ),
                        "description"   => esc_html__("Will put an angled separator on the bottom of the section", 'hotel-xenia'),
 
                    ),
                    // End -> Separators Options

                    // Start -> Effects Options
                    // End -> Effects Options
          );

          return $params;
       }

       /** 
       * This is used for modifying/adding classes to vc_row shortcode output
       */
       public function modify_classes( $vc_classes, $base, $atts ) {

            // Make sure this is vc_row
            if ( $base !== 'vc_row' ) { return $vc_classes; }

            // Get user set attributes...use always vc_map_get_attributes()
            $atts = vc_map_get_attributes( 'vc_row', $atts );

            // Set our class variables, according to atts
            extract( $atts );
            $plethora_classes   = array();
            $plethora_classes[] = $color_set;
            $plethora_classes[] = $transparent;
            $plethora_classes[] = $transparent_overlay;
            $plethora_classes[] = $align;
            $plethora_classes[] = $row_padding_top;
            $plethora_classes[] = $row_padding_bottom;
            $plethora_classes[] = $sep_top;
            $plethora_classes[] = $sep_bottom;
            // $plethora_classes[] = $effects;
            
            // Transform $vc_classes into array for easier management
            $vc_classes = is_array( $vc_classes ) ? $vc_classes : explode(' ', $vc_classes );
            // Merge all class variables and send 'em back to VC
            $plethora_classes   = array_filter( $plethora_classes, 'esc_attr' );
            $vc_classes         = array_merge( $vc_classes, $plethora_classes );
            $vc_classes         = array_unique( $vc_classes );
            return  implode(' ', $vc_classes );
       }

       /** 
       * Save the 'content_has_sections' post meta option, that affects the markup exported. 
       *
       * @return array
       * @since 1.0
       *
       */
       public function set_content_has_sections( $post_id, $post, $update ) {

      // If this is a revision, get real post ID
        if ( $parent_id = wp_is_post_revision( $post_id ) ) {

                $post_id = $parent_id;
        }

        $supported_post_types = Plethora_Theme::get_supported_post_types();
        if ( in_array( $post->post_type, $supported_post_types ) ) {

            $content = ( !empty( $post->post_content ) ) ? $post->post_content : '' ;
            
            // delete first, to avoid duplicate values that might be created from import process
            delete_post_meta( $post_id, METAOPTION_PREFIX .'content_has_sections' );
            // VERY IMPORTANT: Will use our own has_shortcode implementation, as we want to make a check
            // even if the shortcode has not been registered yet ( i.e. this happens during import process)
            if ( Plethora_Shortcode::has_shortcode( $content, 'vc_row' )) { 

              update_post_meta( $post_id, METAOPTION_PREFIX .'content_has_sections', true );

            } else {

              update_post_meta( $post_id, METAOPTION_PREFIX .'content_has_sections', false );
            }
        }
       }
	}
	
 endif;