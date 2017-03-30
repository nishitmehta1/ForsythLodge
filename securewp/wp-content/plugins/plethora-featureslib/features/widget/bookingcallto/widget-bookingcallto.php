<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				           (c) 2017

Call To Booking Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && class_exists('Plethora_Shortcode_Bookingcallto') && !class_exists('Plethora_Widget_Bookingcallto') ) {

	/**
	 * @package Plethora Framework
	 */
	class Plethora_Widget_Bookingcallto extends WP_Widget  {

		public static $feature_title          = "Room Booking Request";						// FEATURE DISPLAY TITLE
		public static $feature_description    = "Styled box with text or HTML.";	// FEATURE DISPLAY DESCRIPTION (STRING)
		public static $theme_option_control   = true;        						// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
		public static $theme_option_default   = true;        						// DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
		public static $theme_option_requires  = array( 'shortcode' => 'bookingcallto' );        					// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct      = false;        						// DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
		public static $dynamic_method         = false; 								// ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ) | THIS A PARENT METHOD, FOR ADDING ACTION
		public static $wp_slug 				  = 'roombooking-widget';					// SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )

		public $field_dependencies          = array();
		public $dependencies_selector_class;
		public $dependencies_rule_prefix;

		public function __construct() { 

			/* LEAVE INTACT ACROSS WIDGET CLASSES */

			$id_base     = WIDGETS_PREFIX . self::$wp_slug;
			$name        = '> PL | ' . self::$feature_title;
			$widget_ops  = array( 
				'classname'   => self::$wp_slug, 
				'description' => esc_html__('Styled box with text or HTML', 'plethora-framework')
			);
			$control_ops = array( 'id_base' => $id_base );

			parent::__construct( $id_base, $name, $widget_ops, $control_ops );     // INSTANTIATE PARENT OBJECT

			// Field dependencies settings
			$this->dependencies_selector_class = 'plethora_dep_'. str_replace('-', '_', self::$wp_slug ) .'';
			$this->dependencies_rule_prefix = 'ruleset_'. str_replace('-', '_', self::$wp_slug ) .'';
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dependencies_script' ) );	
			// Media upload settings
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_upload_media_script' ) );	
			add_action( 'customize_controls_init', array( $this, 'enqueue_upload_media_script' ) );	// only for customizer preview
		}

		function widget( $args, $instance ) {

			$params                 = $this->params();                                                // GET PARAMETERS
			$widget_atts            = Plethora_Widget::get_widget_atts( $params, $args, $instance );  // GET WIDGET ATTRIBUTES
			$widget_atts['id_base'] = $this->id_base;                                                 // ADD id_base FOR MAIN WRAPPER CLASS OUTPUT

			extract( $widget_atts );

			// if dates are on, we need a js init
			if ( $date_arrival || $date_departure ) {

				$script_args = array(
				  'date_format'   => empty( $date_format ) ? 'yy-mm-dd' : $date_format,
				);

				$enqueue_args = array(

					'handle' => 'jquery-ui-datepicker',
					'script' => Plethora_Shortcode_Bookingcallto::get_datepicker_script( $script_args ),
					'multiple' => false,
				);
				Plethora_Theme::enqueue_init_script( $enqueue_args );           
			}  
			
			$fields = array();

			// Fields: arrival date 
			if ( $date_arrival ) {

				$fields[] = Plethora_Shortcode_Bookingcallto::get_field_config( $date_arrival_name, $date_arrival_title, '', '', 'date_check_in',  $date_arrival_placeholder ); 
			}
			// Fields: departure date 
			if ( $date_departure ) { 

				$fields[] = Plethora_Shortcode_Bookingcallto::get_field_config( $date_departure_name, $date_departure_title, '', '', 'date_check_out', $date_departure_placeholder ); 
			}

			// Fields: adults 
			if ( $adults ) {

				$fields[] = Plethora_Shortcode_Bookingcallto::get_field_config( $adults_name, $adults_title, Plethora_Shortcode_Bookingcallto::get_field_countval_options( $adults_max, 1, 2 ), '', 'adults' ); 
			}

			// Fields: children 
			if ( $children ) {

				$fields[] = Plethora_Shortcode_Bookingcallto::get_field_config( $children_name, $children_title, Plethora_Shortcode_Bookingcallto::get_field_countval_options( $children_max, 0, 0 ), '', 'children' ); 
			}

			// Fields: rooms 
			if ( $rooms ) {

				$this_room_val = $rooms_hidden && is_singular( 'room' ) ? Plethora_Theme::get_title( array( 'tag' => '', 'link' => false, 'force_display' => true ) ) : Plethora_Shortcode_Bookingcallto::get_field_post_options( 'room' );
				$fields[] = Plethora_Shortcode_Bookingcallto::get_field_config( $rooms_name, $rooms_title, $this_room_val, '', 'selected_room', '', $rooms_multiple, $rooms_hidden ); 
			}

			// Fields: services 
			if ( $services ) {

				$fields[] = Plethora_Shortcode_Bookingcallto::get_field_config( $services_name, $services_title, Plethora_Shortcode_Bookingcallto::get_field_post_options( 'service', true ), '', 'selected_service', '', $services_multiple ); 
			}

			// Fields: hidden_1
			if ( $hidden_1 ) {

				$fields[] = Plethora_Shortcode_Bookingcallto::get_field_config( $hidden_1_name, '', $hidden_1_value, '', '', '', false, true ); 
			}

			// Fields: hidden_2
			if ( $hidden_2 ) {

				$fields[] = Plethora_Shortcode_Bookingcallto::get_field_config( $hidden_2_name, '', $hidden_2_value, '', '', '', false, true ); 
			}


			// Form submission opts 
			$form_action_url = !empty( $form_action ) ? $form_action : '#';
			$form_bgimage_url = !empty( $form_bgimage ) ? $form_bgimage : '';

			$widget_atts['target_price_text']        = '';
			$widget_atts['target_price_text_before'] = '';
			$widget_atts['target_price_text_after']  = '';
			if ( !empty( $booking_target_price ) && is_singular() && method_exists( 'Plethora_Module_Booking', 'get_target_price_options' ) ) {

				$booking_target_price_options     = Plethora_Module_Booking::get_target_price_options( get_post_type(), get_the_id() );
				$widget_atts['target_price_text']        = $booking_target_price_options['target_price_text'];
				$widget_atts['target_price_text_before'] = $booking_target_price_options['target_price_text_before'];
				$widget_atts['target_price_text_after']  = $booking_target_price_options['target_price_text_after'];
			}
			$widget_atts['form_action_url']  = esc_url( $form_action_url );  
			$widget_atts['form_bgimage_url'] = esc_url( $form_bgimage_url );  
			$widget_atts['form_target']      = esc_attr( $form_target );  
			$widget_atts['submit_title']     = esc_attr( $submit_title );  
			$widget_atts['submit_style']     = esc_attr( $submit_style );  
			$widget_atts['submit_size']      = esc_attr( $submit_size );  
			$widget_atts['submit_colorset']  = esc_attr( $submit_colorset );  
			$widget_atts['submit_class']     = esc_attr( $submit_class );  
			$widget_atts['fields']           = $fields;
			$widget_atts['id']               = '';
			$widget_atts['el_class']         = esc_attr( $el_class );

			echo Plethora_Widget::get_templatepart_output( $widget_atts, __FILE__ );
		}

		function update( $new_instance, $old_instance ) {

			return $new_instance;
		}

		function form( $instance ) {

			$field_params = $this->params();	                                    // GET OPTIONS PARAMETERS
			$defaults     = Plethora_Widget::get_form_defaults( $field_params );  // GET DEFAULT 
			// VERY IMPORTANT: parse with default arguments ONLY if instance is empty ( meaning no values are saved in db )
			$instance = empty( $instance ) ? wp_parse_args( $instance, $defaults) : $instance;        
			$fields = '';
			foreach( $field_params as $key => $field_args ){                      // CREATE THE FORM!

				$field_args['obj']      = $this;
				$field_args['instance'] = $instance;
				$fields .= Plethora_Widget::get_field( $field_args );
			}
			
			echo '<div class="'. esc_attr( $this->dependencies_selector_class ) .'">';
			echo $fields;
			echo '</div>';

			// Prepare and enqueue dependencies script
			$this->set_field_dependencies();

		}

		public function enqueue_dependencies_script( $hook ) {

			if ( $hook === 'widgets.php' || $hook === 'customize.php' ) {

				wp_register_script( 'plethora-field-dependencies', PLE_CORE_ASSETS_URI . '/js/libs/field-interdependencies/deps.js', array( 'jquery' ),  '1.4.7', TRUE  );
				wp_enqueue_script( 'plethora-field-dependencies' );			
			}
		}		

		public function enqueue_upload_media_script( $hook = '' ) {

			if ( $hook === 'widgets.php' || current_filter() === 'customize_controls_init' ) {

				add_action( 'admin_enqueue_scripts', create_function( "", "wp_enqueue_script('upload_media_widget', PLE_CORE_JS_URI . '/upload-media.js', array('jquery'), false, true);") );  
				wp_enqueue_media();
			}
		}		

		public function set_field_dependencies() {

			if ( empty( $this->field_dependencies ) ) {

				$field_params       = $this->params();  // GET OPTIONS PARAMETERS
				$field_dependencies = $this->field_dependencies;
				foreach( $field_params as $key => $field_args ){                      // CREATE THE FORM!

					$field_dependencies = Plethora_Widget::add_field_to_dependencies_list( $field_dependencies, $field_args );
				}
				$this->field_dependencies = $field_dependencies;
				// Prepare and enqueue dependencies script
				Plethora_Widget::set_dependencies_script( $this->dependencies_selector_class, $this->dependencies_rule_prefix, $this->field_dependencies );
			}
		}

		public function params() {

			$params_index['booking_target_price'] = array( 
				'param_name' => 'booking_target_price',
				'type'       => 'dropdown',
				'heading'    => esc_html__('Show Target Price Label', 'plethora-framework'),
				'desc'       => esc_html__('Will be displayed only on single views', 'plethora-framework'),
				'value'      => array(  
					esc_html__( 'No', 'plethora-framework' )                               => 0,
					esc_html__( 'Price Only', 'plethora-framework' )                       => '1',
					esc_html__( 'Price + After Text', 'plethora-framework' )               => '2',
					esc_html__( 'Before Text + Price', 'plethora-framework' )              => '3', 
					esc_html__( 'Before Text + Price + After Text', 'plethora-framework' ) => '4' 
				),
				'default'    => 0
			);
			
			$params_index['form_action'] = array( 
				'param_name'  => 'form_action',
				'type'        => 'link',
				'heading'     => esc_html__('Booking Request Form Page', 'plethora-framework'),
				'description' => esc_html__('This must be the link of the page where the Contact Form 7 Booking Request form is displayed.', 'plethora-framework'),
				'default'     => '#'
			);

			$params_index['form_method'] = array( 
				'param_name'  => 'form_method',
				'type'        => 'dropdown',
				'heading'     => esc_html__('Booking Request Form Method', 'plethora-framework'),
				'description' => esc_html__( 'If you want to point this to a Contact Form 7 booking form page in this website, then you must use GET method.', 'plethora-framework' ),
				'default'     => 'get',
				'value'       => array( 
					esc_html__('Get method', 'plethora-framework')  => 'get',
					esc_html__('Post method', 'plethora-framework') => 'post',
				),
			);

			$params_index['date_format'] = array( 
				'param_name' => 'date_format',
				'type'       => 'textfield',
				'heading'     => esc_html__('Date Format', 'plethora-framework'),
				'description' => sprintf( esc_html__('Day, month, year format for date fields. Default: yy-mm-dd | %sOther date format examples%s', 'plethora-framework'), '<a href="https://jqueryui.com/resources/demos/datepicker/date-formats.html" target="_blank">', '</a>' ),
				'default'    => 'yy-mm-dd'
			);

			$params_index['date_arrival'] = array( 
				'param_name' => 'date_arrival',
				'type'       => 'checkbox',
				'heading'    => esc_html__('Start Date Field', 'plethora-framework'),
				'default'    => false
			);
			$params_index['date_arrival_name'] = array( 
				'param_name'  => 'date_arrival_name',
				'type'        => 'textfield',                                        
				'heading'     => esc_html__('Start Date Field Name', 'plethora-framework'),
				'description' => esc_html__('This field name should match the name of targeted CF7 Booking Request form field', 'plethora-framework'),
				'default'     => 'date_arrival',                                        
				'dependency'  => array( 
									'element' => 'date_arrival', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['date_arrival_title'] = array( 
				'param_name'  => 'date_arrival_title',
				'type'        => 'textfield',
				'heading'     => esc_html__('Start Date Title', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Check in', 'plethora-framework'),                                        
				'dependency'  => array( 
									'element' => 'date_arrival', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['date_arrival_placeholder'] = array( 
				'param_name'  => 'date_arrival_placeholder',
				'type'        => 'textfield',
				'heading'     => esc_html__('Start Date Placeholder', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Select Arrival Date', 'plethora-framework'),                                        
				'dependency'  => array( 
									'element' => 'date_arrival', 
									'value'   => array( 'true' ),  
								)
			);

			$params_index['date_departure'] = array( 
				'param_name' => 'date_departure',
				'type'       => 'checkbox',
				'heading'    => esc_html__('End Date Field', 'plethora-framework'),
				'default'    => false
			);
			$params_index['date_departure_name'] = array( 
				'param_name'  => 'date_departure_name',
				'type'        => 'textfield',
				'heading'     => esc_html__('End Date Field Name', 'plethora-framework'),
				'description' => esc_html__('This field name should match the name of targeted CF7 Booking Request form field', 'plethora-framework'),
				'default'     => 'date_departure',                                        
				'dependency'  => array( 
									'element' => 'date_departure', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['date_departure_title'] = array( 
				'param_name'  => 'date_departure_title',
				'type'        => 'textfield',
				'heading'     => esc_html__('End Date Title', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Check out', 'plethora-framework'),                                      
				'dependency'  => array( 
									'element' => 'date_departure', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['date_departure_placeholder'] = array( 
				'param_name'  => 'date_departure_placeholder',
				'type'        => 'textfield',
				'heading'     => esc_html__('End Date Placeholder', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Select Departure Date', 'plethora-framework'),                                      
				'dependency'  => array( 
									'element' => 'date_departure', 
									'value'   => array( 'true' ),  
								)
			);

			$params_index['adults'] = array( 
				'param_name' => 'adults',
				'type'       => 'checkbox',
				'heading'    => esc_html__('Adults Field', 'plethora-framework'),
				'default'    => false
			);
			$params_index['adults_name'] = array( 
				'param_name'  => 'adults_name',
				'type'        => 'textfield',
				'heading'     => esc_html__('Adults Field Name', 'plethora-framework'),
				'description' => esc_html__('This field name should match the name of targeted CF7 Booking Request form field', 'plethora-framework'),
				'default'     => 'adults',                                        
				'dependency'  => array( 
									'element' => 'adults', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['adults_title'] = array( 
				'param_name'  => 'adults_title',
				'type'        => 'textfield',
				'heading'     => esc_html__('Adults Title', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Adults', 'plethora-framework'),                                       
				'dependency'  => array( 
									'element' => 'adults', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['adults_max'] = array( 
				'param_name'  => 'adults_max',
				'type'        => 'textfield',
				'heading'     => esc_html__('Max Adults', 'plethora-framework'),
				'description' => esc_html__('Only numbers', 'plethora-framework'),
				'default'     => 4,                                        
				'dependency'  => array( 
									  'element' => 'adults', 
									  'value'   => array( 'true' ),  
											)
			);

			$params_index['children'] = array( 
				'param_name' => 'children',
				'type'       => 'checkbox',
				'heading'    => esc_html__('Children Field', 'plethora-framework'),
				'default'    => false
			);
			$params_index['children_name'] = array( 
				'param_name'  => 'children_name',
				'type'        => 'textfield',
				'heading'     => esc_html__('Children Field Name', 'plethora-framework'),
				'description' => esc_html__('This field name should match the name of targeted CF7 Booking Request form field', 'plethora-framework'),
				'default'     => 'children',                                        
				'dependency'  => array( 
									'element' => 'children', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['children_title'] = array( 
				'param_name'  => 'children_title',
				'type'        => 'textfield',
				'heading'     => esc_html__('Children Title', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Children', 'plethora-framework'),                                       
				'dependency'  => array( 
									'element' => 'children', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['children_max'] = array( 
				'param_name'  => 'children_max',
				'type'        => 'textfield',
				'heading'     => esc_html__('Max Children', 'plethora-framework'),
				'description' => esc_html__('Only numbers', 'plethora-framework'),
				'default'     => 4,                                        
				'dependency'  => array( 
									'element' => 'children', 
									'value'   => array( 'true' ),  
								)
			);

			$params_index['rooms'] = array( 
				'param_name' => 'rooms',
				'type'       => 'checkbox',
				'heading'    => esc_html__('Rooms Selection Field', 'plethora-framework'),
			);

			$params_index['rooms_name'] = array( 
				'param_name'  => 'rooms_name',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Rooms Field Name', 'plethora-framework'),
				'description' => sprintf( esc_html__('Must match the %1$sroom field name%2$s attribute on the targeted %1$sCF7 booking form%2$s.%3$sCF7 form field sample configuration: %1$s[select_posts selected_room post_type:room default:get]%2$s', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
				'default'     => 'selected_room',                                        
				'dependency'  => array( 
									'element' => 'rooms', 
									'value'   => array( 'true' ),  
								)
			);

			$params_index['rooms_hidden'] = array( 
				'param_name' => 'rooms_hidden',
				'type'       => 'checkbox',
				'default'     => true,                                        
				'heading'    => esc_html__('Automatic Room Selection', 'plethora-framework'),
				'description' => sprintf( esc_html__('If this widget is used on single room view, you have the option to pass the selected room to the booking form automatically, without using the room selection field. Uncheck this, if you need to use a room selection field.', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
				'dependency'  => array( 
									'element' => 'rooms', 
									'value'   => array( 'true' ),  
								)
			);


			$params_index['rooms_title'] = array( 
				'param_name'  => 'rooms_title',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Rooms Field Title', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Room Type', 'plethora-framework'),                                        
				'dependency'  => array( 
									'element' => 'rooms_hidden', 
									'value'   => array( 'false' ),  
								)
			);
			$params_index['rooms_multiple'] = array( 
				'param_name' => 'rooms_multiple',
				'type'       => 'checkbox',
				'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'    => esc_html__('Multi Rooms Selection', 'plethora-framework'),
				'dependency'  => array( 
									'element' => 'rooms_hidden', 
									'value'   => array( 'false' ),  
								)
			);

			$params_index['services'] = array( 
				'param_name' => 'services',
				'type'       => 'checkbox',
				'heading'    => esc_html__('Services Selection Field', 'plethora-framework'),
			);
			$params_index['services_name'] = array( 
				'param_name'  => 'services_name',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Services Field Name', 'plethora-framework'),
				'description' => sprintf( esc_html__('Must match the %1$sservices field name%2$s attribute on the targeted %1$sCF7 booking form%2$s.%3$sCF7 form field sample configuration: %1$s[select_categories selected_service post_type:service default:get]%2$s', 'plethora-framework'), '<strong>', '</strong>', '<br>' ),
				'default'     => 'selected_service',                                        
				'dependency'  => array( 
									'element' => 'services', 
									'value'   => array( 'true' ),  
								)
			);
			$params_index['services_title'] = array( 
				'param_name'  => 'services_title',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Services Field Title', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Interested In', 'plethora-framework'),
				'dependency'  => array( 
									  'element' => 'services', 
									  'value'   => array( 'true' ),  
								)
			);

			$params_index['hidden_1'] = array( 
				'param_name'  => 'hidden_1',
				'type'        => 'checkbox',
				'heading'     => esc_html__('Hidden Field 1', 'plethora-framework'),
				'description' => esc_html__('Enable this, if you need a  hidden field to use this form with external booking systems', 'plethora-framework'),
			);
			$params_index['hidden_1_name'] = array( 
				'param_name'  => 'hidden_1_name',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Hidden Field 1 Name', 'plethora-framework'),
				'description' => sprintf( esc_html__('Apparently you need to use this hidden field for an external booking service. This field name must match one the %1$shidden field name%2$s attribute on the targeted booking form. No HTML here.', 'plethora-framework'), '<strong>', '</strong>' ),
				'dependency'  => array( 
					'element' => 'hidden_1', 
					'value'   => array( 'true' ),  
				)
			);
			$params_index['hidden_1_value'] = array( 
				'param_name'  => 'hidden_1_value',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Hidden Field 1 Value', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'dependency'  => array( 
					'element' => 'hidden_1', 
					'value'   => array( 'true' ),  
				)
			);

			$params_index['hidden_2'] = array( 
				'param_name'  => 'hidden_2',
				'type'        => 'checkbox',
				'heading'     => esc_html__('Hidden Field 2', 'plethora-framework'),
				'description' => esc_html__('Enable this, if you need an additional hidden field to use this form with external booking systems', 'plethora-framework'),
			);
			$params_index['hidden_2_name'] = array( 
				'param_name'  => 'hidden_2_name',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Hidden Field 2 Name', 'plethora-framework'),
				'description' => sprintf( esc_html__('Apparently you need to use this hidden field for an external booking service. This field name must match one the %1$shidden field name%2$s attribute on the targeted booking form. No HTML here.', 'plethora-framework'), '<strong>', '</strong>' ),
				'dependency'  => array( 
					'element' => 'hidden_2', 
					'value'   => array( 'true' ),  
				)
			);
			$params_index['hidden_2_value'] = array( 
				'param_name'  => 'hidden_2_value',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Hidden Field 2 Value', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'dependency'  => array( 
					'element' => 'hidden_2', 
					'value'   => array( 'true' ),  
				)
			);





			$params_index['services_multiple'] = array( 
				'param_name' => 'services_multiple',
				'type'       => 'checkbox',
				'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'    => esc_html__('Multi Services Selection', 'plethora-framework'),
				'dependency' => array( 
									'element' => 'services', 
									'value'   => array( 'true' ),  
								)
			);

			$params_index['form_target'] = array( 
				'param_name' => 'form_target',
				'type'       => 'dropdown',
				'heading'    => esc_html__('Submit Behavior', 'plethora-framework'),
				'value'      => array( 
								  esc_html__('Pass values to selected booking form page', 'plethora-framework')                 => '_self',
								  esc_html__('Pass values to selected booking form page, in new tab', 'plethora-framework')     => '_blank',
								  // esc_html__('Link to %1$s page, in ajax window', 'plethora-framework') => 'ajax',
								),
			);
			$params_index['submit_title'] = array( 
				'param_name'  => 'submit_title',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				'heading'     => esc_html__('Submit Field Title', 'plethora-framework'),
				'description' => esc_html__('No HTML', 'plethora-framework'),
				'default'     => esc_html__('Book Now', 'plethora-framework'),
			);

			$params_index['submit_style'] = array( 
				"param_name" => "submit_style",                                  
				"type"       => "dropdown",                                        
				'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				"class"      => "vc_hidden",                                         
				"heading"    => esc_html__("Submit Style", 'plethora-framework'),      
				'default'    => 'btn',
				"value"      => array(
									esc_html__( 'Default', 'plethora-framework' )     => 'btn',
									esc_html__( 'Inverted', 'plethora-framework' )    => 'btn btn-inv',
									esc_html__( 'Link Button', 'plethora-framework' ) => 'btn-link',
								),
			);
			$params_index['submit_size'] = array( 
				"param_name" => "submit_size",                                  
				"type"       => "dropdown",                                        
				'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				"heading"    => esc_html__("Submit size", 'plethora-framework'),      
				'default'    => '',
				"value"      => array(
									'Default'     =>'',
									'Large'       =>'btn-lg',
									'Small'       =>'btn-sm',
									'Extra Small' =>'btn-xs'
								),
			);

			$params_index['submit_colorset'] = array( 
				"param_name" => "submit_colorset",                                  
				"type"       => "dropdown",                                        
				'group'      => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				"holder"     => "",                                               
				"class"      => "vc_hidden",                                         
				"heading"    => esc_html__("Submit Color", 'plethora-framework'),      
				'default'    => 'btn-default',
				"value"      => array(
									esc_html__( 'Default', 'plethora-framework' )   => 'btn-default',
									esc_html__( 'Primary', 'plethora-framework' )   => 'btn-primary',
									esc_html__( 'Secondary', 'plethora-framework' ) => 'btn-secondary',
									esc_html__( 'Dark', 'plethora-framework' )      => 'btn-dark',
									esc_html__( 'Light', 'plethora-framework' )     => 'btn-light',
									esc_html__( 'White', 'plethora-framework' )     => 'btn-white',
									esc_html__( 'Black', 'plethora-framework' )     => 'btn-black',
									esc_html__( 'Success', 'plethora-framework' )   => 'btn-success',
									esc_html__( 'Info', 'plethora-framework' )      => 'btn-info',
									esc_html__( 'Warning', 'plethora-framework' )   => 'btn-warning',
									esc_html__( 'Danger', 'plethora-framework' )    => 'btn-danger',
								),
			);

			$params_index['submit_class'] = array( 
				'param_name'  => 'submit_class',
				'type'        => 'textfield',
				'group'       => esc_html__('Fields Configuration', 'plethora-framework'),                                       
				"heading"     => esc_html__("Submit Extra Class", 'plethora-framework'),      
				'description' => esc_html__('Style submit button differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
			);

			$params_index['form_bgimage'] = array( 
				"param_name"  => "form_bgimage",
				"type"        => "attach_image",
				"heading"     => esc_html__("Background Image", 'plethora-framework'),
				"default"     => PLE_THEME_ASSETS_URI .'/images/booking-form-widget-bgimage.jpg',
				"description" => esc_html__("Upload/select a background image", 'plethora-framework'),
			);

			$params_index['el_class'] = array( 
				'param_name'  => 'el_class',
				'type'        => 'textfield',
				'heading'     => esc_html__('Extra Class', 'plethora-framework'),
				'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
			);

			return $params_index;
		}
	}
 }