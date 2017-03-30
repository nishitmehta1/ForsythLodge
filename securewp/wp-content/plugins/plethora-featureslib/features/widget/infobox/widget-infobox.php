<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2013-2015

File Description: Infobox Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_Infobox') ) {
 
      /**
      * @package Plethora Framework
      */
      class Plethora_Widget_Infobox extends WP_Widget  {

          public static $feature_title          = "Infobox";              // FEATURE TITLE 
          public static $feature_description    = "Display an Infobox";   // FEATURE DESCRIPTION 
          public static $theme_option_control   = true;                   // CONTROLLED VIA THEME OPTIONS PANEL ?
          public static $theme_option_default   = true;                   // DEFAULT ACTIVATION OPTION STATUS 
          public static $theme_option_requires  = array();                // REQUIRED FEATURES FOR ACTIVATION ? ( array: $controller_slug => $feature_slug )
          public static $dynamic_construct      = false;                  // DYNAMIC CLASS CONSTRUCTION ? 
          public static $dynamic_method         = false;                  // THIS A PARENT METHOD, FOR ADDING ACTION. ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE )
          public static $wp_slug =  'infobox-widget';                     // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
          public static $assets;

          public function __construct() { 

            /* LEAVE INTACT ACROSS WIDGET CLASSES */

            $id_base     = WIDGETS_PREFIX . self::$wp_slug;
            $name        = '> PL | ' . self::$feature_title;
            $widget_ops  = array( 
              'classname'   => self::$wp_slug, 
              'description' => self::$feature_title 
              );
            $control_ops = array( 'id_base' => $id_base );

            parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT

            /* ADDITIONAL WIDGET CODE STARTS HERE */

            Plethora_Widget::enableMedia();     /* MEDIA MANAGER */

          }

          function widget( $args, $instance ) {

            extract( $args ); // EXTRACT USER INPUT

            // PACK DEFAULT TEMPLATE VALUES [ LEAVE INTACT ]
            $widget_atts = array (
                                  'widget_id'     => $widget_id,
                                  'before_widget' => $before_widget,  
                                  'after_widget'  => $after_widget,  
                                  'before_title'  => $before_title,  
                                  'after_title'   => $after_title
                                );

            // PACK ADDITIONAL TEMPLATE VALUES 
            $widget_atts = array_merge( $widget_atts, array(

              'title'         => apply_filters('widget_title', $instance['title']),  
              'textarea'      => ( isset($instance['textarea']) )? $instance['textarea'] : "",
              'image_uri'     => apply_filters( 'widget_image_uri', $instance['image_uri'] ),   /* MEDIA MANAGER */
              'animation'     => apply_filters('widget_title', $instance['animation']),
              'title_align'   => empty($instance['title_align']) ? '' : $instance['title_align'],   /* SELECT */

            ));

            /* EMBEDING BUTTON SHORTCODE */
            if ( isset($instance['button_text']) && $instance['button_text'] !== "" ){

              $do_shorcode_content = '[plethora_button button_text="' . $instance['button_text'] . '" ';
              $button_link         = ( isset($instance['button_link']) ) ? $instance['button_link'] : "";
              $do_shorcode_content .= 'button_link="url:' . urlencode($button_link) . '|title:' . $instance['button_text'] . '|target:%20_self" button_size="btn" button_style="btn-success" ';
              if ( isset($instance['button_icon']) && $instance['button_icon'] !== "" ){
                $do_shorcode_content .= 'button_with_icon="with-icon" button_icon="fa ' . $instance['button_icon'] . '" ';
              }
              $do_shorcode_content .= "]";
              $widget_atts["button"] = do_shortcode( $do_shorcode_content );

            }
            /* EMBEDING BUTTON SHORTCODE */

            echo Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => __FILE__) );

          }

          function update( $new_instance, $old_instance ) {

            $instance                = $old_instance;
            $instance['title']       = strip_tags($new_instance['title']);
            $instance['textarea']    = strip_tags($new_instance['textarea']);
            $instance['button_text'] = strip_tags($new_instance['button_text']);
            $instance['button_link'] = strip_tags($new_instance['button_link']);
            $instance['button_icon'] = strip_tags($new_instance['button_icon']);
            $instance['image_uri']   = ( ! empty( $new_instance['image_uri'] ) ) ? strip_tags( $new_instance['image_uri'] ) : ''; /* MEDIA MANAGER */
            $instance['animation']   = strip_tags($new_instance['animation']);
            $instance['title_align'] = $new_instance['title_align'];            /* SELECT */

            return $instance;

          }

          function form( $instance ) {

            $title       = ( isset( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
            $title_align = ( isset( $instance['title_align']) )? esc_attr( $instance['title_align'] ) : "";   /* SELECT */
            $textarea    = ( isset( $instance['textarea']) ) ? esc_textarea($instance['textarea']) : "";
            $button_text = ( isset( $instance['button_text']) )? esc_attr($instance['button_text']) : "";
            $button_link = ( isset( $instance['button_link']) )? esc_url($instance['button_link']) : "";
            $button_icon = ( isset( $instance['button_icon']) )? esc_attr($instance['button_icon']) : "";
            $image_uri   = ( isset( $instance[ 'image_uri' ] ) ) ? $instance[ 'image_uri' ] : '';   /* MEDIA MANAGER */
            $animation   = ( isset( $instance['animation'] ) ) ? esc_attr( $instance['animation'] ) : '';

          ?>

          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'plethora-framework' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
          </p>

           <!-- SELECT -->
           <p>
            <label for="<?php echo esc_attr( $this->get_field_id('title_align') ); ?>"><?php esc_html_e( 'Title Align:', 'plethora-framework' ); ?>
              <select class='widefat' id="<?php echo esc_attr( $this->get_field_id('title_align') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title_align') ); ?>" type="text">
                <option value='text-left'<?php echo ( $title_align == 'text-left') ? 'selected' : ''; ?>><?php esc_html_e( 'Left', 'plethora-framework' ); ?></option>
                <option value='text-center'<?php echo ( $title_align == 'text-center' ) ? 'selected' : ''; ?>><?php esc_html_e( 'Center', 'plethora-framework' ); ?></option> 
                <option value='text-right'<?php echo ( $title_align == 'text-right' ) ? 'selected' : ''; ?>><?php esc_html_e( 'Right', 'plethora-framework' ); ?></option> 
              </select>                
            </label>
           </p>
           <!-- /SELECT -->

          <p>
            <label for="<?php echo esc_attr( $this->get_field_id('textarea') ); ?>"><?php esc_html_e('Textarea:', 'plethora-framework'); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id('textarea') ); ?>" name="<?php echo esc_attr( $this->get_field_name('textarea') ); ?>"><?php echo esc_textarea( $textarea ); ?></textarea>
          </p>

          <p class="media-manager">
            <label for="<?php echo esc_attr( $this->get_field_id('image_uri') ); ?>"><?php echo sprintf( esc_html__( '%s:', 'plethora-framework' ), "Side Image" ) ?></label><br />
            <img class="<?php echo esc_attr( $this->id ); ?>_thumbnail" src="<?php echo esc_url( $image_uri ); ?>" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" />
            <input type="text" class="widefat <?php echo esc_attr( $this->id ); ?>_url" name="<?php echo esc_attr( $this->get_field_name('image_uri') ); ?>" id="<?php echo esc_attr( $this->get_field_id('image_uri') ); ?>" value="<?php echo esc_attr( $image_uri ); ?>">
            <input type="button" value="<?php esc_html_e( 'Upload Image', 'plethora-framework' ); ?>" class="button custom_media_upload" id="<?php echo esc_attr( $this->id ); ?>"/>
          </p>

          <!-- BUTTON SHORTCODE FIELDS -->

          <p>
            <label for="<?php echo esc_attr( $this->get_field_id('button_text') ); ?>"><?php esc_html_e('Button Text (leave empty to remain hidden):', 'plethora-framework'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('button_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('button_text') ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>" />
          </p>

          <p>
            <label for="<?php echo esc_attr( $this->get_field_id('button_link') ); ?>"><?php esc_html_e('Button Link:', 'plethora-framework'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('button_link') ); ?>" name="<?php echo esc_attr( $this->get_field_name('button_link') ); ?>" type="text" value="<?php echo esc_attr( $button_link ); ?>" />
          </p>

          <p>
            <label for="<?php echo esc_attr( $this->get_field_id('button_icon') ); ?>"><?php esc_html_e('Button Icon (FontAwesome):', 'plethora-framework'); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('button_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('button_icon') ); ?>" type="text" value="<?php echo esc_attr( $button_icon ); ?>" />
          </p>

          <!-- /BUTTON SHORTCODE FIELDS -->

          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'animation' ) ); ?>"><a href="http://daneden.github.io/animate.css/" target="_blank"><?php esc_html_e( 'Wow Animation Type:', 'plethora-framework' ); ?></a></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'animation' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'animation' ) ); ?>" type="text" value="<?php echo esc_attr( $animation ); ?>" />
          </p>
          <?php               
          }
     }
 }