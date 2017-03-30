<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2013

File Description: Timetable Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

// PRESUBMIT: Remove Credentials
if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_Timetable') ) {
 

      /**
      * @package Plethora Framework
      */
      class Plethora_Widget_Timetable extends WP_Widget  {

          public static $wp_slug                = 'timetable-widget';               // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
          public static $feature_title          = "Timetable";                      // FEATURE DISPLAY TITLE 
          public static $feature_description    = "Display working hours";          // FEATURE DISPLAY DESCRIPTION 
          public static $theme_option_control   = true;                             // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL
          public static $theme_option_default   = true;                             // DEFAULT ACTIVATION OPTION STATUS
          public static $theme_option_requires  = array();                          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
          public static $dynamic_construct      = false;                            // DYNAMIC CLASS CONSTRUCTION ?
          public static $dynamic_method         = false;                            // THIS A PARENT METHOD, FOR ADDING ACTION. ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE )
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

            Plethora_Widget::enableMedia();   /* MEDIA MANAGER */
            add_action( 'admin_enqueue_scripts', array( $this, 'enableSortable' ) );

          }

          /*======== SORTABLE LIST ========*/

          public function enableSortable(){

            $screen = get_current_screen();
            
            if ( !empty( $screen->base ) && $screen->base === 'widgets' ) {


              wp_enqueue_script('widget_timetable_sortable', PLE_FLIB_FEATURES_URI . '/widget/timetable/js/widget-timetable.js', array('upload_media_widget'), false, true);
            } 
          }

          /*======== SORTABLE LIST ========*/


          function widget( $args, $instance ) {

            extract( $args );   // EXTRACT USER INPUT

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
                            'image_uri'     => esc_url( apply_filters( 'widget_image_uri', $instance['image_uri'] ) ),    /* MEDIA MANAGER */
                            'rowData'       => json_decode( urldecode( $instance['rowData'] ) ) 

            ));

            /* EMBEDING BUTTON SHORTCODE */
            if ( isset($instance['button_text']) && $instance['button_text'] !== "" ){

              $do_shorcode_content = '[plethora_button button_text="' . $instance['button_text'] . '" ';
              $button_link         = ( isset($instance['button_link']) ) ? $instance['button_link'] : "";
              $button_link         = urlencode($button_link);
              $button_align        = ( isset($instance['button_align'])) ? "text-" . esc_attr($instance['button_align']) : "text-center";
              $do_shorcode_content .= 'button_link="url:' . $button_link . '|title:' . $instance['button_text'] . '|target:%20_blank" button_size="btn" button_style="btn-primary" ';

              if ( isset($instance['button_icon']) && $instance['button_icon'] !== "" ){
                $do_shorcode_content .= 'button_align="'. $button_align .'" button_with_icon="with-icon" button_icon="fa ' . $instance['button_icon'] . '" ';
              }
              $do_shorcode_content .= "]";

              $widget_atts["button"] = do_shortcode( $do_shorcode_content );

            }
            /* EMBEDING BUTTON SHORTCODE */

            echo Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => __FILE__) );

          }

          function update( $new_instance, $old_instance ) {

            $instance              = $old_instance;
            $instance['title']     = strip_tags($new_instance['title']);
            $instance['image_uri'] = ( ! empty( $new_instance['image_uri'] ) ) ? strip_tags( $new_instance['image_uri'] ) : ''; /* MEDIA MANAGER */
            $instance['rowData']   = $new_instance['rowData'];
            $alloptions            = wp_cache_get( 'alloptions', 'options' );

            if ( isset($alloptions['widget_timetable_entries']) ){  delete_option('widget_timetable_entries');  }

            $instance['button_text']  = strip_tags($new_instance['button_text']);
            $instance['button_link']  = strip_tags($new_instance['button_link']);
            $instance['button_align'] = strip_tags($new_instance['button_align']);
            $instance['button_icon']  = strip_tags($new_instance['button_icon']);

            return $instance;

          }

          function form( $instance ) {

            $title        = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
            $image_uri    = ( isset( $instance[ 'image_uri' ] ) ) ? $instance[ 'image_uri' ] : '';   /* MEDIA MANAGER */
            $rowData      = ( isset( $instance[ 'rowData' ] ) ) ? $instance[ 'rowData' ] : '';         /* MEDIA MANAGER */
            $button_text  = ( isset($instance['button_text']) )? esc_attr($instance['button_text']) : "";
            $button_link  = ( isset($instance['button_link']) )? esc_url($instance['button_link']) : "";
            $button_align = ( isset($instance['button_align']) )? esc_attr($instance['button_align']) : "";
            $button_icon  = ( isset($instance['button_icon']) )? esc_attr($instance['button_icon']) : "";

            ?>

            <p>
              <div><label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php echo sprintf( esc_html__( '%s:', 'plethora-framework' ), "Title" ) ?></label></div>
              <div><input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $title ); ?>" /></div>
            </p>

            <p class="media-manager">
              <label for="<?php echo esc_attr( $this->get_field_id('image_uri') ); ?>"><?php echo sprintf( esc_html__( '%s:', 'plethora-framework' ), "Background Image" ) ?></label><br />
              <img class="<?php echo esc_attr( $this->id ); ?>_thumbnail" src="<?php echo esc_url( $image_uri ); ?>" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" />
              <input type="text" class="widefat <?php echo esc_attr( $this->id ); ?>_url" name="<?php echo esc_attr( $this->get_field_name('image_uri') ); ?>" id="<?php echo esc_attr( $this->get_field_id('image_uri') ); ?>" value="<?php echo esc_attr( $image_uri ); ?>">
              <input type="button" value="<?php esc_attr_e( 'Upload Image', 'plethora-framework' ); ?>" class="button custom_media_upload" id="<?php echo esc_attr( $this->id ); ?>"/>
            </p>

            <!-- BUTTON SHORTCODE FIELDS -->

            <p>
              <label for="<?php echo esc_attr( $this->get_field_id('button_text') ); ?>"><?php echo esc_html__('Button Text (leave empty to remain hidden):', 'plethora-framework'); ?></label>
              <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('button_text') ); ?>" name="<?php echo esc_attr( $this->get_field_name('button_text') ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>" />
            </p>

            <p>
              <label for="<?php echo esc_attr( $this->get_field_id('button_link') ); ?>"><?php echo esc_html__('Button Link:', 'plethora-framework'); ?></label>
              <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('button_link') ); ?>" name="<?php echo esc_attr( $this->get_field_name('button_link') ); ?>" type="text" value="<?php echo esc_attr( $button_link ); ?>" />
            </p>

            <p>
              <label for="<?php echo esc_attr( $this->get_field_id('button_align') ); ?>"><?php echo esc_html__('Button Align: left, center, right (Default: center)', 'plethora-framework'); ?></label>
              <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('button_align') ); ?>" name="<?php echo esc_attr( $this->get_field_name('button_align') ); ?>" type="text" value="<?php echo esc_attr( $button_align ); ?>" />
            </p>


            <p>
              <label for="<?php echo esc_attr( $this->get_field_id('button_icon') ); ?>"><?php echo esc_html__('Button Icon (FontAwesome):', 'plethora-framework'); ?></label>
              <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('button_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('button_icon') ); ?>" type="text" value="<?php echo esc_attr( $button_icon ); ?>" />
            </p>

            <!-- /BUTTON SHORTCODE FIELDS -->

            <!-- DRAG AND DROP SORTABLE WORKTABLE LIST -->

            <p>
              <ul class='widgetTimetableControls'></ul>
              <input type="button" name="widgetTimetableAddRow" value="<?php echo esc_attr__('Add Day Row', 'plethora-framework'); ?>" class="widgetTimetableAddRow" class="button" />
              <input type="hidden" class="widgetTimetableDataHolder" id="<?php echo esc_attr( $this->get_field_id( 'rowData' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rowData' ) ); ?>" value="<?php echo esc_attr( $rowData ); ?>" />
            </p>

            <script id="widgetTimetableTemplate" type="text/html">
                <input type="text" value="{{ day }}" name="day" style="width:45%;" />
                <input type="text" value="{{ time }}" name="time" style="width:20%;" />
                <input type="hidden" value="{{ ordinal }}" name="ordinal" />
                <button class="button button-small"><?php echo esc_html__('REMOVE', 'plethora-framework'); ?></button>
            </script>
            <!-- /DRAG AND DROP SORTABLE WORKTABLE LIST -->
  <?php  }
     }
 }