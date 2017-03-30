<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2013

File Description: Latest Portfolio Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && class_exists('Plethora_Posttype_Portfolio') && !class_exists('Plethora_Widget_Latestportfolio') ) {
 
      /**
      * @package Plethora Framework
      */
     class Plethora_Widget_Latestportfolio extends WP_Widget  {

          public static $feature_title         = "Latest Portfolio Posts";              // FEATURE DISPLAY TITLE
          public static $feature_description   = "Display your latest portfolio posts"; // FEATURE DISPLAY DESCRIPTION
          public static $theme_option_control  = true;                                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
          public static $theme_option_default  = true;                                  // DEFAULT ACTIVATION OPTION STATUS
          public static $theme_option_requires = array( 'posttype'=> 'portfolio');      // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
          public static $dynamic_construct     = false;                                 // DYNAMIC CLASS CONSTRUCTION ?
          public static $dynamic_method        = false;                                 // THIS A PARENT METHOD, FOR ADDING ACTION. ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE )
          public static $dynamic_action        = false;                                 // DYNAMIC WP HOOK INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ). THIS A PARENT METHOD, FOR ADDING ACTION
          public static $wp_slug               = 'latestportfolio-widget';              // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
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

            $project_type = ( ! empty( $instance['project_type'] ) ) ? $instance['project_type'] : 0;
            $number   = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 10;
            if ( !$number ){ $number = 10; }

            $ln_query_args = array( 
                'post_type'           => 'portfolio',
                'posts_per_page'      => $number, 
                'no_found_rows'       => true, 
                'post_status'         => 'publish', 
                'ignore_sticky_posts' => true 
            ); 

            if ( !empty($project_type)) { 
              $ln_query_args['tax_query'] = array( 
                array( 
                  'taxonomy' => 'project-type',
                  'field'    => 'id',
                  'terms'    => $project_type 
                  )
                );
            }

            $ln_query_args = apply_filters( 'widget_posts_args', $ln_query_args );

            $widget_atts = array_merge( $widget_atts, array(

              'title' => apply_filters('widget_title', $instance['title'])  

            ));

           // PREPARING DATA FOR MUSTACHE

           $custom_posts = get_posts( $ln_query_args );  

            // FORMAT POST VALUES
            foreach ( $custom_posts as $custom_post ) {

                $custom_post->title         = $custom_post->post_title;
                $custom_post->permalink     = get_permalink( $custom_post->ID );
                $custom_post->thumbnail     = ( has_post_thumbnail( $custom_post->ID ))? wp_get_attachment_image_src( get_post_thumbnail_id( $custom_post->ID ) ) : false;
                $custom_post->thumbnail_url = esc_url( $custom_post->thumbnail[0] );
                $custom_post->content       = wp_trim_words( strip_shortcodes( $custom_post->post_content ), 10 );
                $date = new DateTime( $custom_post->post_date_gmt );
                $custom_post->date          = $date->format('M j');

            };

            $widget_atts["posts"] = $custom_posts;

            echo Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => __FILE__) );

          }

          function update( $new_instance, $old_instance ) {

               $instance                 = $old_instance;
               $instance['title']        = strip_tags($new_instance['title']);
               $instance['project_type'] = strip_tags($new_instance['project_type']);
               $instance['number']       = (int) $new_instance['number'];

               // $this->flush_widget_cache();
               // PLEFIXME: Undefined method flush_widget_cache() produces a fatal PHP error. 
               // Replacing with code taken from: wp-includes/default-widgets.php
               wp_cache_delete( WIDGETS_PREFIX . 'latestportfolio-widget', 'widget' ); 

               $alloptions = wp_cache_get( 'alloptions', 'options' );
               if ( isset($alloptions['widget_latestportfolio_entries']) )
                    delete_option('widget_latestportfolio_entries');

               return $instance;

          }

          function form( $instance ) {

               $title            = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
               $selected_project = isset( $instance['project_type'] ) ? esc_attr( $instance['project_type'] ) : 0;
               $number           = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
               ?>
               <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'plethora-framework' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
               </p>

               <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'project_type' ) ); ?>"><?php esc_html_e( 'Project Type:', 'plethora-framework' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'project_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'project_type' ) ); ?>">
                <option id="0" value="">--</option>
                 <?php 
                 $project_types = get_terms('portfolio-cat', array('hide_empty' => false));
                 foreach ( $project_types as $project_type ) {
                      $selected = ($selected_project == $project_type->term_id ) ? ' selected="selected"' : '';
                      echo '<option id="' . esc_attr( $project_type->term_id ) . '" value="' . esc_attr( $project_type->term_id )  . '"'.$selected.'>' . $project_type->name . '</option>';
                 }
                 ?>
                 </select>
               </p>

               <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of portfolio items to show:', 'plethora-framework' ); ?></label>
                <input id="<?php echo esc_attr( $this->get_field_id( 'number' ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
               </p>
          <?php               
          }
     }  
 }