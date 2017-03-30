<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Controller class for widgets

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Widget') ) {
 
	/**
	 * @package Plethora Controllers
	 */
	class Plethora_Widget {

		public static $controller_title             = 'Widgets Manager';                        // CONTROLLER TITLE
		public static $controller_description       = 'Activate/deactivate any Plethora widget available. Notice that on deactivation, all dependent features will be deactivated automatically.';
		public static $controller_dynamic_construct = false;                       // DYNAMIC CLASS CONSTRUCTION 
		public static $controller_dynamic_method    = false;                          // INVOKE ANY METHOD AFTER DYNAMIC CONSTRUCTION? ( method || false )
		public static $dynamic_features_loading     = true;                             // LOAD FEATURES DYNAMICALLY ( always true, false if stated so in controller variables )

		/**
		* Enable Media Manager
		* @return string
		*/
		public static function enableMedia(){

			global $pagenow;
			if ( $pagenow === 'widgets.php') {

				add_action( 'admin_enqueue_scripts', create_function( "", "wp_enqueue_script('upload_media_widget', PLE_CORE_JS_URI . '/upload-media.js', array('jquery'), false, true);") );  
				wp_enqueue_media();

			} elseif ( $pagenow === 'customizer.php' ) {

				add_action( 'customize_controls_print_footer_scripts', create_function( "", "wp_enqueue_script('upload_media_widget', PLE_CORE_JS_URI . '/upload-media.js', array('jquery'), false, true);") );  
				wp_enqueue_media();
			}
		}

		/**
		* Return form default values ( use in widget_class::form() method )
		* @return string
		*/
		public static function get_form_defaults( $params ) {

			$defaults = array();
			foreach ( $params as $key => $param ) {

				$defaults[$param['param_name']] = isset( $param['value'] ) && ! is_array($param['value']) ? $param['value'] : '';
				$defaults[$param['param_name']] = isset( $param['default'] ) ? $param['default'] : $defaults[$param['param_name']];

			}

			return $defaults;
		}


		/**
		* Return widget values for template or shortcode outputs ( use in widget_class::widget() method )
		* @return string
		*/
		public static function get_widget_atts( $params, $args, $instance ) {

			extract( $args );   // EXTRACT USER INPUT
			// PACK DEFAULT VALUES
			$widget_atts = array (
								'widget_id'     => $widget_id,  
								'before_widget' => $before_widget,  
								'after_widget'  => $after_widget,  
								'before_title'  => $before_title,  
								'after_title'   => $after_title
								);
			
			// PACK ADDITIONAL VALUES 
			$widget_add_atts = array();
			foreach ( $params as $key => $param ) {

				$widget_add_atts[$key] = '';

				if ( isset( $param['is_widget_title'] ) &&  $param['is_widget_title'] ) { // title should be filtered
					
					if ( isset( $instance[$key] ) ) { // if a value is saved in db

						$widget_add_atts[$key] = apply_filters('widget_title', $instance[$key], $instance, $widget_id );
					
					} else {

						if ( isset( $param['default'] ) && !is_array( $param['default'] ) ) {

							$widget_add_atts[$key] = $param['default'];
						}
					}
				
				} else {

					if ( isset( $instance[$key] ) ) { // if a value is saved in db

						$widget_add_atts[$key] = $instance[$key] ;

					} else { // if value is not saved in db

						if ( isset( $param['default'] ) && !is_array( $param['default'] ) ) {

							$widget_add_atts[$key] = $param['default'];
						}
					}
				}
			}

			// MERGE AND GO!
			$widget_atts = array_merge( $widget_atts, $widget_add_atts );
			return $widget_atts;
		}

		/**
		* Returns a field dependencies
		* @return string
		*/
		public static function add_field_to_dependencies_list( $field_dependencies, $field_args ) {

			if ( empty( $field_args['dependency'] ) ) { return $field_dependencies; }
			if ( empty( $field_dependencies ) || !is_array( $field_dependencies ) ) { $field_dependencies = array(); }

			$affected_element_class   = 'ple_container_' . $field_args['param_name']; // always hide the full field container
			$depended_element_class   = isset( $field_args['dependency']['element'] ) ? 'ple_'. $field_args['dependency']['element'] : '';
			$depended_element_vals = isset( $field_args['dependency']['value'] ) && is_array( $field_args['dependency']['value'] )? $field_args['dependency']['value'] : array();
			if ( !empty( $depended_element_class ) && !empty( $depended_element_vals ) ) {

				foreach ( $depended_element_vals as $val ) {

					$field_dependencies[$depended_element_class][$val][] = $affected_element_class;
				}
			}
			return $field_dependencies;
		}
		/**
		* Returns a field dependencies
		* @return string
		*/
		public static function set_dependencies_script( $sel_class, $ruleset_prefix, $field_dependencies ) {

			if ( empty( $sel_class ) || empty( $ruleset_prefix ) || empty( $field_dependencies ) ) { return; }
			add_action( 'admin_print_footer_scripts', function() use( &$sel_class, &$ruleset_prefix, &$field_dependencies ) { self::enqueue_dependencies_ruleset_script( $sel_class, $ruleset_prefix, $field_dependencies ); });
			add_action( 'customize_controls_print_footer_scripts', function() use( &$sel_class, &$ruleset_prefix, &$field_dependencies ) { self::enqueue_dependencies_ruleset_script( $sel_class, $ruleset_prefix, $field_dependencies ); });
		}

		public static function enqueue_dependencies_ruleset_script( $sel_class, $ruleset_prefix, $field_dependencies ) {

				$dev_mode = Plethora_Theme::is_developermode() ? 'true' : 'false';
				echo '
<script type="text/javascript">

(function($){

	"use strict";

	// Returns ruleset for our widget
	function buildRuleset_'. esc_js( $sel_class ) .'() {
		var ruleset = $.deps.createRuleset();
';
			$rule_count = 0;
			foreach ( $field_dependencies as $depended_element_class => $depended_element_vals ) {
				foreach ( $depended_element_vals as $depended_element_val => $affected_element_classes) {
					$rule_count++;
					if ( is_bool( $depended_element_val ) || $depended_element_val == 'true' || $depended_element_val == 'false' ) {

						$depended_element_val = $depended_element_val === true || $depended_element_val == 'true' ? 'true' : 'false';
					}
					elseif ( is_string( $depended_element_val ) ) {
						$depended_element_val = "'".$depended_element_val."'";
					} 

// echo "			// Make these fields visible when user checks hotel accomodation\n";
echo "\n";
echo "			var ". $ruleset_prefix .'_'. $rule_count." = ruleset.createRule('.".$depended_element_class."', '==', ". $depended_element_val .");\n";
					foreach ( $affected_element_classes as $affected_element_class ) {

echo "				". $ruleset_prefix .'_'. $rule_count.".include('.".$affected_element_class."');\n";
// echo "			// Make the ruleset effective on the whole page\n";
					}
				}
			}
echo '
		return ruleset;
	}
	
	// Enables ruleset for our widget
	function enable_ruleset() {

		var ruleset = buildRuleset_'. esc_js( $sel_class ) .'();
		var cfg = { log : '. esc_js( $dev_mode ) .', checkTargets : true };

		$( ".'. esc_js( $sel_class ) .'" ).each(function( index ) {

			$.deps.enable( $( this ), ruleset, cfg);
		});
	}

	// Apply ruleset for our widget on load, and once on any widget drag events
	$(window).ready(function(){ enable_ruleset() })
	$(window).on("widget-added", function(){ enable_ruleset() })	// for widget addition ( WP trigger )
	$(window).on("widget-updated", function(){ enable_ruleset() })	// for widget update  ( WP trigger )
}(jQuery));
</script>';

		}
	   
		/**
		* Returns a field
		* @return string
		*/
		public static function get_field( $field_args ) {

			$output = '';

			if ( isset( $field_args['type'] ) && method_exists( 'Plethora_Widget', 'field_'. $field_args['type'] ) ) {

				$output .= '<p class="ple_container_' . $field_args['param_name'] .'">';
				$output .= $field_args['type'] !== 'checkbox' ? '<label for="' . self::get_field_id( $field_args['param_name'], $field_args['obj'] ) . '"><strong>' . $field_args['heading'] . '</strong></label>' : '';
				$output .= call_user_func( array( 'Plethora_Widget', 'field_'. $field_args['type'] ), $field_args );
				$output .= $field_args['type'] === 'checkbox' ? '<span style="display:block;">' : '';
				$output .= !empty( $field_args['desc'] ) ? '<small>'. $field_args['desc'] .'</small>' : '';
				$output .= !empty( $field_args['description'] ) ? '<small>'. $field_args['description'] .'</small>' : '';
				$output .= $field_args['type'] === 'checkbox' ? '</span>' : '';
				$output .= '</p>';
			}                
			return $output;
		}


		/**
		* Returns a text option field
		* @return string
		*/
		public static function field_textfield( $args ) {

			$obj      = $args['obj'];
			$instance = $args['instance'];
			$value    = !empty( $instance[$args['param_name']] ) ? $instance[$args['param_name']] : '';
			$output   =  '<input type="text" class="widefat ple_'. esc_attr( $args['param_name'] ).'" id="' . esc_attr( self::get_field_id( $args['param_name'], $args['obj'] ) ) . '" name="' . esc_attr( self::get_field_name( $args['param_name'], $args['obj'] ) ) .'" value="' . esc_attr( $value ) . '" />';
			return $output;
		}

		/**
		* Returns a textarea option field
		* @return string
		*/
		public static function field_textarea( $args ) {

			$obj      = $args['obj'];
			$instance = $args['instance'];
			$value    = !empty( $instance[$args['param_name']] ) ? $instance[$args['param_name']] : '';
			$output   =  '<textarea rows="5" class="widefat ple_'. esc_attr( $args['param_name'] ).'" id="' . esc_attr( self::get_field_id( $args['param_name'], $args['obj'] ) ) . '" name="' . esc_attr( self::get_field_name( $args['param_name'], $args['obj'] ) ) .'">' . wp_kses_post( $instance[$args['param_name']] )  . '</textarea>';
			return $output;
		}

		/**
		* Returns a select option field
		* @return string
		*/
		public static function field_dropdown( $args ) {

			$obj      = $args['obj'];
			$instance = $args['instance'];
			$value    = !empty( $instance[$args['param_name']] ) ? $instance[$args['param_name']] : '';
			$multiple = !empty( $args['multi'] ) ? ' multiple' : '';
			$output   = '<select'.$multiple.' class="widefat ple_'. esc_attr( $args['param_name'] ).'" id="' . esc_attr( self::get_field_id( $args['param_name'], $args['obj'] ) ) . '" name="' . esc_attr( self::get_field_name( $args['param_name'], $args['obj'] ) ) .'" >';
			foreach ( $args['value'] as $opt_title => $opt_val ) {

				$select = ( $value == $opt_val ) ? ' selected' : '';
				$output .= '<option value="'. esc_attr( $opt_val ).'"'.$select.'>'. $opt_title.'</option>';
			}
			$output .= '</select>';
			return $output;
		}

		/**
		* Returns an radio option field ( not used yet )
		* @return string
		*/
		public static function field_radio( $args ) {

			$obj = $args['obj'];
			$instance = $args['instance'];
			$output = '<div>';
			$count = 0;
			$saved_val = $instance[$args['param_name']];
			$default_val = $saved_val === '' ? true : false;
			foreach ( $args['value'] as $opt_title => $opt_val ) {
				$count++;
				$checked = ! $default_val && $instance[$args['param_name']] == $opt_val ? ' checked' : '';
				$checked = $default_val && $count === 1 ? ' checked' : $checked;
				$output .= '<label><input type="radio" class="widefat ple_'. esc_attr( $args['param_name'] ).'" id="' . esc_attr( self::get_field_id( $args['param_name'], $args['obj'] ) ) . $count . '" name="' . esc_attr( self::get_field_name( $args['param_name'], $args['obj'] ) )  .'" value="'.esc_attr( $opt_val ).'" '.$checked.' /> '.$opt_title.' </label>';
			}
			$output .= '</div>';
			return $output;
		}

		/**
		* Returns a simple checkbox option field
		* @return string
		*/
		public static function field_checkbox( $args ) {

			$obj           = $args['obj'];
			$instance      = $args['instance'];

			# Now we need to know all the possible options, using the value argument
			
			// if no 'value' arg set, this is a simple boolean checkbox, using the title as label
			if ( ! isset( $args['value'] ) || !is_array( $args['value'] ) ) { 

				$args['value'] = array( $args['heading'] );
			}

			// At this point, we should have $args['value'] as an options array...otherwise we abandon function
			if ( !is_array( $args['value'] ) ) { return ''; }

			$output = '';
			$default_val = isset( $args['default'] ) ? $args['default'] : false;
			$checked_val = isset( $instance[$args['param_name']] ) ? $instance[$args['param_name']] : $default_val ;

			foreach ( $args['value'] as $opt_key => $opt_title ) {

				$checked = in_array( $checked_val, array( 'true', true, '1', 1 ) ) ? ' checked' : '';
				$output .= '<input type="checkbox" class="widefat ple_'. esc_attr( $args['param_name'] ).'" id="' . esc_attr( self::get_field_id( $args['param_name'], $args['obj'] ) ) .'" name="' . esc_attr( self::get_field_name( $args['param_name'], $args['obj'] ) )  .'" value="1" '.$checked.' />';
				$output .= '<label for="' . esc_attr( self::get_field_id( $args['param_name'], $args['obj'] ) ).'"><strong>'.$opt_title.'</strong></label>';
			}
			return $output;
		}


		/**
		* Returns an icon picker option field
		* @return string
		*/
		public static function field_iconpicker( $args ) {

			$obj = $args['obj'];
			$instance = $args['instance'];
			$libraries = isset( $args['settings']['type'] ) ? array( $args['settings']['type'] ) : array();
			$libraries = Plethora_Module_Icons::get_options_array( array( 'use_in' => 'vc', 'library' => $libraries ) );
			$output = '<select class="widefat ple_'. esc_attr( $args['param_name'] ).'" id="' . self::get_field_id( $args['param_name'], $args['obj'] ) . '" name="' . self::get_field_name( $args['param_name'], $args['obj'] ) .'" >';
			$output .= '<option value="">'. esc_html__('No icon selected', 'plethora-framework' ).'</option>';
			foreach ( $libraries as $key => $icons ) {
				asort($icons, SORT_STRING);
				foreach ( $icons as $icon_val => $icon_title ) {

					$select = $instance[$args['param_name']] === $icon_val ? ' selected' : '';
					$output .= '<option value="'. esc_attr( $icon_val ).'"'.$select.'>'. $icon_title.'</option>';
				}
			}
			$output .= '</select>';
			return $output;
		}

		/**
		* Returns an image attachment option field
		* @return string
		*/
		public static function field_attach_image( $args ) {
			
			$obj = $args['obj'];
			$instance = $args['instance'];
			$output = '';

			$output .= '<p class="media-manager">';
			$output .= '  <span style="background:#f1f1f1; display:inline-block"><img class="'. esc_attr( $obj->id ).'_thumbnail" src="'.  esc_url( $instance[$args['param_name']] ) .'" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" /></span>';
			$output .= '  <input type="text" class="widefat '.esc_attr( $obj->id ).'_url  ple_'. esc_attr( $args['param_name'] ).'" name="'.  esc_attr( self::get_field_name( $args['param_name'], $args['obj'] ) ) .'" id="'. esc_attr( self::get_field_id( $args['param_name'], $args['obj'] ) ) .'" value="'. esc_url( $instance[$args['param_name']] ) .'">';
			$output .= '  <input type="button" value="'. esc_html__('Upload Image', 'plethora-framework') .'" class="button custom_media_upload" id="'. esc_attr( $obj->id ) .'"/>';
			$output .= '</p>';

			return $output;
		}

		/**
		* Returns a link field ( just a plain text field for the moment )
		* @return string
		*/
		public static function field_link( $args ) {
			
			$obj      = $args['obj'];
			$instance = $args['instance'];
			$value    = !empty( $instance[$args['param_name']] ) ? $instance[$args['param_name']] : '#';
			$output   = '<input type="text" class="widefat ple_'. esc_attr( $args['param_name'] ).'" id="' . self::get_field_id( $args['param_name'], $args['obj'] ) . '" name="' . self::get_field_name( $args['param_name'], $args['obj'] ) .'" value="' . esc_attr( $value ) . '" />';
			return $output;
		}

		/**
		 * Returns output content in shortcode mode...used for widgets that act as a shortcode replica
		 * @param $params $instance
		 * @return string
		 */
		public static function get_shortcode_output( $shortcode_tag, $widget_atts ) {

			$shortcode = '['.$shortcode_tag.' ';
			foreach ( $widget_atts as $att_key => $att_val ) {
			   
				if ( !in_array($att_key, array('widget_id', 'before_widget', 'after_widget', 'before_title', 'after_title', 'id_base') ) ) { // exclude general widget args

					if ( $att_key === 'content') { // get content attr separately

						$content = $att_val;

					} else {

						$shortcode .= ' '. $att_key .'="'. $att_val .'"';
					}
				}
			}
			$shortcode .= ']';
			// If $content has contents, then this must be an enclosed shortcode
			if ( isset($content) ) {

				$content    = !empty($content) ? wpautop( $content, true ) : '';
				$shortcode .= !empty($content) ? do_shortcode( $content ) : '';
				$shortcode .= '[/'.$shortcode_tag.']';
			}

			$output  = '<div class="'.$widget_atts['id_base'].'">'; // PLENOTE: we should prototype each widget class according to id_base
			$output .= do_shortcode( $shortcode );
			$output .= '</div>';
			return $output;

		}

		public static function get_templatepart_output( $widget_atts, $file ) {

			if ( !empty( $widget_atts['content'] ) ) {

				// $widget_atts['content'] = wpautop( $widget_atts['content'], true );
				$widget_atts['content'] = do_shortcode( $widget_atts['content'] );
			}

			$output = Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => $file ) );
			return $output;
		}

		/**
		 * Constructs name attributes for use in form() fields
		 * @param string $field_name Field name
		 * @return string Name attribute for $field_name
		 */
		public static function get_field_name( $field_name, $obj ) {
			return 'widget-' . $obj->id_base . '[' . $obj->number . '][' . $field_name . ']';
		}

		/**
		 * Constructs id attributes for use in {@see WP_Widget::form()} fields.
		 * @param string $field_name Field name.
		 * @return string ID attribute for `$field_name`.
		 */
		public static function get_field_id( $field_name, $obj ) {
			return 'widget-' . $obj->id_base . '-' . $obj->number . '-' . $field_name;
		}
	}
 }