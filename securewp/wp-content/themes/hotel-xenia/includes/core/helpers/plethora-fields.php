<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2017

Base class class for: Plethora_Fields_Termsmeta | Plethora_Fields_Widgetoptions
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


/**
 * @package Plethora Framework
 * @version 1.0
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c) 2017
 *
 */
abstract class Plethora_Fields {

	public $fields_mapping         = array();     // Form configuration given on class instance
	public $fields                 = array();     // Full fields info list ( contains everything needed for rendering too )
	public $scripts                = array();     // List with scripts that must be enqueued
	public $styles                 = array();     // List with styles that must be enqueued
	public $admin_monitors         = array();     // List with related admin monitors. Used for global $pagenow conditionals

	public function init( $fields_mapping ) {

		// Well...there is no point without attributes!
		if ( !is_array( $fields_mapping ) || empty( $fields_mapping ) ) { return; }

		$this->fields_mapping = $fields_mapping;

		// Turn all options into field array items with complete attrs configuration
		$this->set_fields();

		// Set scripts & styles according to the set fields configuration
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_styles' ) );
	}

	/**
	* Sets all fields assets ( script, styles and output )
	* @return array()
	*/
	private function set_fields() {

		$fields = array();
		foreach ( $this->get_approved_fields() as $field_id => $field_attrs ) {
			// get field basic configuration first...if nothing returned, no need to continue
			$field = $this->get_field_config( $field_attrs );
			if ( ! empty( $field ) ) { 

				$fields[$field_id] = $field; 
			}
		}

		$this->fields = $fields;
	}


	/**
	* Checks if fields_mapping includes the correct basic attributes
	* Returns only those options approved ( should have at least id and a supported field type ) 
	* @return array()
	*/
	private function get_approved_fields() {

		$fields_approved = array();
		foreach ( $this->fields_mapping as $field ) {

			// Check field type first
			if ( !empty( $field['type'] ) ) {
				// Check field id...if not set, then move on
				if ( empty( $field['id'] ) ) { continue; }

				// Sanitize id
				$field['id'] = sanitize_key( $field['id'] );

				// add to approved
				$fields_approved[$field['id']] = $field;
			}
		}

		// Sort opts according to priority given and returns all approved options
		return $fields_approved;
	}

	public function wp_enqueue_scripts( $hook ) {

		if ( in_array( $hook, $this->admin_monitors ) ) {

			wp_enqueue_media();
		}

		// ADD STYLE ENQUEUES HERE
		foreach ( $this->scripts as $script ) {

			if ( is_array( $script ) ) {

				wp_enqueue_script( $script[0], $script[1], $script[2], $script[3], $script[4] );

			} else {

				wp_enqueue_script( $script );
			}
		}
	}

	public function wp_enqueue_styles() {

		// ADD STYLE ENQUEUES HERE
		foreach ( $this->styles as $style ) {

			wp_enqueue_style( $style );
		}
	}


// BASIC FIELDS CONSTRUCTION METHOD

	public function get_field_config( $field_attrs ) {

		$field_config = array();


		if ( empty( $field_attrs ) ) { return $field_config;  }
 
		if ( method_exists( $this, 'get_field_config_'. $field_attrs['type'] ) ) { // field method exists in this class

			$set_field_func = 'get_field_config_'. $field_attrs['type'];
			$field_config = $this->$set_field_func( $field_attrs );	
		} 

		// Set field scripts
		$field_config['scripts'] = !empty( $field_config['scripts'] ) ? $field_config['scripts'] : array();
		foreach ( $field_config['scripts'] as $script ) {

			$this->scripts[] = $script;
		}

		// Set field styles
		$field_config['styles'] = !empty( $field_config['styles'] ) ? $field_config['styles'] : array();
		foreach ( $field_config['styles'] as $style ) {

			$this->styles[] = $style;
		}

		return $field_config;
	}

	/**
	* Returns an array with all field attributes that MUST be included on the final configuration
	* This way, it won't be necessary to perform checks for possible missing attributes on fields_mapping
	* Each attribute should be follow a $attr_name => '' pattern
	* @return array()
	*/
	public function get_fields_attribute_index() {

		// Default values should be given according to fields config
		$fields_attribute_index['id']      = '';      // * Unique ID identifying the field. Must be different from all other field IDs.
		$fields_attribute_index['type']    = '';      // * Value identifying the field type.
		$fields_attribute_index['title']   = '';      // Displays title of the option.
		$fields_attribute_index['desc']    = '';      // Description of the option, usualy appearing beneath the field control.
		$fields_attribute_index['default'] = '';      // Value set as default
		$fields_attribute_index['class']   = '';      // Append class(es) to field class attribute
		return $fields_attribute_index;
	}


// BASIC FIELDS CONSTRUCTION METHOD ENDED

// FIELD METHODS START

	/**
	* Global field attributes setup
	* Called using wp_parse_args within each set_field_{field} method
	* @return array()
	*/
	public function get_field_basic_config( $field_attrs ) {

		$field_config = wp_parse_args( $field_attrs, $this->get_fields_attribute_index() );

		$field_config['output_label']      = !empty( $field_config['title'] ) ? '<label for="'. $field_config['id'] .'">'. $field_config['title'] .'</label>' : '';
		$field_config['output_desc']       = !empty( $field_config['desc'] ) ? '<p for="'. $field_config['id'] .'">'. $field_config['desc'] .'</p>' : '';
		return $field_config;
	}

	/**
	* TEXT field attrEibutes setup
	* @return array()
	*/
	public function get_field_config_text( $field_attrs ) { 

		// Parse with global default attributes
		$field_config  = $this->get_field_basic_config( $field_attrs );
		// Additional field-specific attributes
		$field_attrs['scripts'] = array(); 	//  JS assets ( similar to wp_enqueue_script )
		$field_attrs['styles']  = array(); 	//  CSS assets ( similar to wp_enqueue_style )

		// Prepare and return the rest 'output_' attributes
		$saved_val                    = $this->get_field_value( $field_config['id'], $field_config['default'] );
		$field_config['output_field'] = '<input name="'. $field_config['id'] .'" id="'. $field_config['id'] .'" class="'. $field_config['id'] .'-field'.' '. $field_config['class'] .'" value="'. $saved_val .'" type="text">';
		return $field_config;
	}

	/**
	* TEXTAREA field attributes setup
	* @return array()
	*/
	public function get_field_config_textarea( $field_attrs ) { 

		// Parse with global default attributes
		$field_config  = $this->get_field_basic_config( $field_attrs );
		// Additional field-specific attributes
		$field_attrs['scripts'] = array(); 	//  JS assets ( similar to wp_enqueue_script )
		$field_attrs['styles']  = array(); 	//  CSS assets ( similar to wp_enqueue_style )

		// Prepare and return the rest 'output_' attributes
		$saved_val                     = $this->get_field_value( $field_config['id'], $field_config['default'] );
		$field_config['output_field']  = '<textarea name="'. $field_config['id'] .'" id="'. $field_config['id'] .'" class="'. $field_config['id'] .'-field'.' '. $field_config['class'] .'">';
		$field_config['output_field'] .= wp_kses_post( $saved_val );
		$field_config['output_field'] .= '</textarea>';
		return $field_config;
	}

	/**
	* SELECT field attributes setup
	* @return array()
	*/
	public function get_field_config_select( $field_attrs ) { 

		// Parse with global default attributes
		$field_config  = $this->get_field_basic_config( $field_attrs );
		// Additional field-specific attributes
		$field_attrs['scripts'] = array(); 	//  JS assets ( similar to wp_enqueue_script )
		$field_attrs['styles']  = array(); 	//  CSS assets ( similar to wp_enqueue_style )

		// Prepare and return the rest 'output_' attributes
		$field_config['output_field']  = '<select name="'. $field_config['id'] .'" id="'. $field_config['id'] .'" class="'. $field_config['id'] .'-field'.' '. $field_config['class'] .'">';
		$selected_option_val = $this->get_field_value( $field_config['id'], $field_config['default'] );

		foreach ( $field_config['options'] as $option_val => $option_title  ) {

			$selected = $option_val == $selected_option_val ? ' selected="selected"' : '';
			$field_config['output_field']  .= '<option value="'. $option_val .'"'. $selected .'>'. $option_title .'</option>';
		}
		$field_config['output_field']  .= '</select>';
		return $field_config;
	}

	/**
	* CHECKBOX field attributes setup
	* @return array()
	*/
	public function get_field_config_checkbox( $field_attrs ) { 

		// Parse with global default attributes
		$field_config  = $this->get_field_basic_config( $field_attrs );
		// Additional field-specific attributes
		$field_attrs['scripts'] = array(); 	//  JS assets ( similar to wp_enqueue_script )
		$field_attrs['styles']  = array(); 	//  CSS assets ( similar to wp_enqueue_style )

		// Prepare and return the rest 'output_' attributes
		if ( empty( $field_config['options'] ) || !is_array( $field_config['options'] ) ) { // if no 'options' arg set, this is a single boolean checkbox, using the title as label

			$field_config['options'] = array( 0 => esc_html__( 'Yes', 'plethora-framework' ) );
			$saved_option_val        = $this->get_field_value( $field_config['id'], $field_config['default'] );
			$saved_option_val        = in_array($saved_option_val, array( 'true', true, '1', 1 ) ) ? true : false;
			$saved_option_val        = array( 1 => $saved_option_val );
			$single_checkbox         = true;
		
		} else { // multiple checkboxes

			$saved_option_vals = $this->get_field_value( $field_config['id'], $field_config['default'] );
			$saved_option_val  = array();
			foreach ( $saved_option_vals as $key => $val ) {

				$saved_option_val[$key] = in_array( $val, array( 'true', true, '1', 1 ) ) ? true : false;
			}
			$single_checkbox = false;
		}

		$field_config['output_field'] = '';
		foreach ( $field_config['options'] as $option_key => $option_title  ) {

			$checked                       = !empty( $saved_option_val[$option_key] ) && $saved_option_val[$option_key] ? ' checked' : '';
			$field_config['output_field'] .= '<label for="'. $field_config['id'] .'-'. $option_key .'">';
			$field_config['output_field'] .= '<input type="checkbox" name="'. $field_config['id'].'['. $option_key .']" id="'. $field_config['id'] .'-'. $option_key .'" class="'. $field_config['id'] .'-field'.' '. $field_config['class'] .'"  value="1"'.$checked.'>';
			$field_config['output_field'] .= $option_title .'</label>';
		}
		return $field_config;
	}

	/**
	* COLOR field attributes setup
	* @return array()
	*/
	public function get_field_config_color( $field_attrs ) { 

		// Parse with global default attributes
		$field_config  = $this->get_field_basic_config( $field_attrs );
		// Additional field-specific attributes
		$field_config['scripts'] = array( 'wp-color-picker' ); //  JS assets ( similar to wp_enqueue_script )
		$field_config['styles']  = array( 'wp-color-picker' ); //  CSS assets ( similar to wp_enqueue_style )

		// Prepare and return the rest 'output_' attributes
		$option_val                     = $this->get_field_value( $field_config['id'], $field_config['default'] );
		$field_config['output_field']   = '<input type="text" name="'. $field_config['id'] .'" id="'. $field_config['id'] .'" value="'.$option_val.'" class="'. $field_config['id'] .'-field'.' '. $field_config['class'] .'" data-default-color="'. $field_config['default'] .'" />';
		$field_config['output_js_init'] ='<script type="text/javascript">jQuery(document).ready(function($){ $("#'. $field_config['id'] .'").wpColorPicker(); });</script>';
		return $field_config;
	}

	/**
	* MEDIA field attributes setup
	* @return array()
	*/
	public function get_field_config_media( $field_attrs ) {

		// Parse with global default attributes
		$field_config  = $this->get_field_basic_config( $field_attrs );
		// Additional field-specific attributes
		$field_config['scripts'] = array(); //  JS assets ( similar to wp_enqueue_script )
		$field_config['styles']  = array(); //  CSS assets ( similar to wp_enqueue_style )

		// Prepare and return the rest 'output_' attributes
		$option_val                     = $this->get_field_value( $field_config['id'], $field_config['default'] );
		$field_config['output_field']   = '<span style="background:#f1f1f1; display:inline-block"><img class="'. esc_attr( $field_config['id'] ).'_thumbnail" src="'.  esc_url( $option_val ) .'" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" /></span>';
		$field_config['output_field']  .= '<input type="text" class="'. $field_config['id'] .'-field'.' '. $field_config['class'] .' '.esc_attr( $field_config['id'] ).'_url" name="'.  esc_attr( $field_config['id'] ) .'" id="'. esc_attr( $field_config['id'] ) .'" value="'. esc_url( $option_val ) .'">';
		$field_config['output_field']  .= '<input type="button" value="'. esc_html__('Upload Image', 'plethora-framework') .'" class="button plethora_media_upload " id="'. esc_attr( $field_config['id'] ) .'"/>';
		$field_config['output_js_init'] = '<script type="text/javascript"> jQuery(function($){function media_upload( button_class) {var _custom_media = true, _orig_send_attachment = wp.media.editor.send.attachment; $("body").on("click", button_class, function(e) {var button = $(this).attr("id"); var send_attachment_bkp = wp.media.editor.send.attachment; var id = document.getElementById(button).getAttribute("id").replace("_button", ""); _custom_media = true; wp.media.editor.send.attachment = function(props, attachment){if ( _custom_media  ) {$( "." + id + "_url" ).val( attachment.url ); $("." + id + "_thumbnail").attr( "src", attachment.url ).css("display","block"); } else {return _orig_send_attachment.apply( button, [props, attachment] ); } }; wp.media.editor.open(button); return false; }); } media_upload( ".plethora_media_upload"); }); </script>';
		return $field_config;
	}

	/**
	* LINK field attributes setup ( similar to 'text' for the moment )
	* @return array()
	*/
	public function get_field_config_link( $field_attrs ) {

		// Parse with global default attributes
		$field_config  = $this->get_field_basic_config( $field_attrs );
		// Additional field-specific attributes
		$field_attrs['scripts'] = array(); 	//  JS assets ( similar to wp_enqueue_script )
		$field_attrs['styles']  = array(); 	//  CSS assets ( similar to wp_enqueue_style )

		// Prepare and return the rest 'output_' attributes
		$saved_val                    = $this->get_field_value( $field_config['id'], $field_config['default'] );
		$field_config['output_field'] = '<input name="'. $field_config['id'] .'" id="'. $field_config['id'] .'" class="'. $field_config['id'] .'-field'.' '. $field_config['class'] .'" value="'. $saved_val .'" type="text">';
		return $field_config;
	}
// FIELD METHODS END
}