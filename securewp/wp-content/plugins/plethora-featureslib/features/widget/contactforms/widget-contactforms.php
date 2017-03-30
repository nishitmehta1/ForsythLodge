<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2013

File Description: Contact Forms Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_ContactForms') ) {
 
      /**
      * @package Plethora Framework
      */
      class Plethora_Widget_ContactForms extends WP_Widget  {

          public static $feature_title          = "Contact Forms";                                  // FEATURE TITLE 
          public static $feature_description    = "Display a Quick Contact or Appointment Form";    // FEATURE DESCRIPTION 
          public static $theme_option_control   = true;                                             // CONTROLLED VIA THEME OPTIONS PANEL ?
          public static $theme_option_default   = true;                                             // DEFAULT ACTIVATION OPTION STATUS 
          public static $theme_option_requires  = array();                                          // REQUIRED FEATURES FOR ACTIVATION ? ( array: $controller_slug => $feature_slug )
          public static $dynamic_construct      = false;                                            // DYNAMIC CLASS CONSTRUCTION ? 
          public static $dynamic_method         = false;                                            // THIS A PARENT METHOD, FOR ADDING ACTION. ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE )
          public static $wp_slug                =  'contactforms-widget';                           // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
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

          }

          function widget( $args, $instance ) {

            extract( $args );   // EXTRACT USER INPUT

            // PACK DEFAULT TEMPLATE VALUES [ LEAVE INTACT ]
            $widget_atts = array(
                            'widget_id'     => $widget_id,
                            'before_widget' => $before_widget,  
                            'after_widget'  => $after_widget,  
                            'before_title'  => $before_title,  
                            'after_title'   => $after_title
                           );

          // PACK ADDITIONAL TEMPLATE VALUES 
          $widget_atts = array_merge( $widget_atts, array(

                            'contact_type'  => empty($instance['contact_type']) ? '' : $instance['contact_type'],   /* SELECT */
                            'title'         => apply_filters('widget_title', $instance['title']),  
                            'contact_id'    => apply_filters('widget_title', $instance['contact_id']),  

                         ));

            /* EMBEDING CONTACT FORM 7 APPOINTMENT FORM */
            if ( isset($instance['contact_id']) && $instance['contact_id'] !== "" ){

              $do_shorcode_content = '[contact-form-7 id="' . $instance['contact_id'] . '"]';
              $widget_atts["cf7"] = do_shortcode( $do_shorcode_content );

            }
            /* EMBEDING CONTACT FORM 7 APPOINTMENT FORM */

            echo Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => "widget-" . $instance["contact_type"] . ".php" ) );

          }

          function update( $new_instance, $old_instance ) {

            $instance                 = $old_instance;
            $instance['contact_type'] = $new_instance['contact_type'];            /* SELECT */
            $instance['title']        = strip_tags($new_instance['title']);
            $instance['contact_id']   = strip_tags($new_instance['contact_id']);

            return $instance;

          }

          function form( $instance ) {

            $contact_type = ( isset($instance['contact_type']) )? esc_attr( $instance['contact_type'] ) : "";   /* SELECT */
            $title        = ( isset( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
            $contact_id   = ( isset( $instance['contact_id'] ) ) ? esc_attr( $instance['contact_id'] ) : '';

          ?>

           <!-- SELECT -->
           <p>
            <label for="<?php echo esc_attr( $this->get_field_id('contact_type') ); ?>">Form Type: 
              <select class='widefat' id="<?php echo esc_attr( $this->get_field_id('contact_type') ); ?>" name="<?php echo esc_attr( $this->get_field_name('contact_type') ); ?>" type="text">
                <option value='quickcontact'<?php echo ( $contact_type == 'quickcontact') ? 'selected' : ''; ?>><?php echo esc_html__('Quick Contact', 'plethora_framework') ?></option>
                <option value='appointmentform'<?php echo ( $contact_type == 'appointmentform' ) ? 'selected' : ''; ?>><?php echo esc_html__('Appointment Form', 'plethora_framework') ?></option> 
              </select>                
            </label>
           </p>
           <!-- /SELECT -->

          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'plethora-framework' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
          </p>

          <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'contact_id' ) ); ?>"><?php esc_html_e( 'Contact Form ID:', 'plethora-framework' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'contact_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'contact_id' ) ); ?>" type="text" value="<?php echo esc_attr( $contact_id ); ?>" />
          </p>
          <?php               
          }
     }
 }