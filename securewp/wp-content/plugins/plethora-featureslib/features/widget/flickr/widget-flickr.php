<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Flickr widdget class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_Flickr') ) {
 
	/**
	 * @package Plethora Framework
	 */
	class Plethora_Widget_Flickr extends WP_Widget  {

		public static $feature_title         = "Flickr Feed Grid";		        		// FEATURE DISPLAY TITLE
		public static $feature_description   = "Display images from a Flickr profile";	// FEATURE DISPLAY DESCRIPTION
		public static $theme_option_control  = true;        							// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL? ( boolean )
		public static $theme_option_default  = true;        							// DEFAULT ACTIVATION OPTION STATUS ( boolean )
		public static $theme_option_requires = array();        							// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = false;        							// DYNAMIC CLASS CONSTRUCTION? 
		public static $dynamic_method        = false; 									// PARENT METHOD FOR ADDING ACTION. ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
		public static $wp_slug               =  'flickr-widget'; 						// SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
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

		function widget($args, $instance) {

            extract( $args ); // EXTRACT USER INPUT

			$photos_to_display = $instance['photos_to_display'];
			if ( is_numeric($photos_to_display) && !empty($photos_to_display)) { 
				$photos_to_display = $photos_to_display - 1;
			} else { 
				$photos_to_display = 7;
			}

            // PACK DEFAULT TEMPLATE VALUES [ LEAVE INTACT ]
	        $widget_atts = array(
								'widget_id'         => $widget_id,
								'before_widget'     => $before_widget,  
								'after_widget'      => $after_widget,  
								'before_title'      => $before_title,  
								'after_title'       => $after_title
                            );

            // PACK ADDITIONAL TEMPLATE VALUES 
            $widget_atts = array_merge( $widget_atts, array(

				'title'             => apply_filters('widget_title', $instance['title']),  
				'screen_name'       => $instance['screen_name'],
				'photos_to_display' => $photos_to_display

            ));

            // PACK VALUES FOR TEMPLATE
	        set_query_var( 'widget_atts', $widget_atts );
            // GET AND ECHO THE TEMPLATE PART
	        ob_start();
	        Plethora_WP::get_template_part( 'templates/widgets/flickr' );
	        echo ob_get_clean();       

		}

		function update($new_instance, $old_instance) {
			$instance = $old_instance;

			$instance['title'] = strip_tags($new_instance['title']);
			$instance['screen_name'] = $new_instance['screen_name'];
			$instance['photos_to_display'] = $new_instance['photos_to_display'];
			
			return $instance;
		}

		function form($instance) {
			$defaults = array('title' => esc_html__('Photos from Flickr', 'plethora-framework'), 'screen_name' => '', 'photos_to_display' => 7);
			$instance = wp_parse_args((array) $instance, $defaults); ?>
			
			<p>
				<div><label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php echo esc_html__('Title', 'plethora-framework'); ?></label></div>
				<div><input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" /></div>
			</p>

			<p>
				<div><label for="<?php echo esc_attr( $this->get_field_id('photos_to_display') ); ?>"><?php echo esc_html__('How many photos to display (default:8)', 'plethora-framework'); ?></label></div>
				<div><input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('photos_to_display') ); ?>" name="<?php echo esc_attr( $this->get_field_name('photos_to_display') ); ?>" value="<?php echo esc_attr( $instance['photos_to_display'] ); ?>" /></div>
			</p>
			
			<p>
				<div><label for="<?php echo esc_attr( $this->get_field_id('screen_name') ); ?>"><?php echo esc_html__('Flickr ID', 'plethora-framework') .' '. esc_html__('( Find with', 'plethora-framework') .' <a href=\'http://idgettr.com\' target=\'_blank\' style=\'text-decoration:none;color:#0063DC;font-weight:bold;\'>idGett<span style=\'color:#FF0084;\'>r</span></a>'; ?></label></div>
				<div><input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id('screen_name') ); ?>" name="<?php echo esc_attr( $this->get_field_name('screen_name') ); ?>" value="<?php echo esc_attr( $instance['screen_name'] ); ?>" /></div>
			</p>
			
		<?php
		}
	}
 }