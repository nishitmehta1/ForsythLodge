<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2017

Extension class for Plethora_Fields, it handles the taxonomy meta fields

CLASS DOCUMENTATION ( latest update: 12/12/2016 )

	BASIC USAGE:

		$category_terms = new Plethora_Fields_Termsmeta( 'category' )

	DESCRIPTION:

		You may add term meta fields on new/edit forms on any taxonomy using this class.
		Apart from new/edit screen form fields, it can handle column values displays.

	PARAMETERS:
 		
 		$taxonomy 		: the taxonomy slug ( required )
		$fields_mapping	: the field configuration options ( required, check below for more info )

	FIELDS MAPPING CONFIGURATION:

		$opts must be a set of arrays, each array represents a field configuration.
		All fields share some common configuration, while there are some field type specific ones.
		
		These are the common attributes:

			// Core mandatory attrs ( handled by Plethora_Fields )
				'id'            => string					// * Unique ID identifying the field. Must be different from all other field IDs.
				'type'          => string					// * Value identifying the field type.
			// Core attrs ( handled by Plethora_Fields )
				'title'         => string					// Displays title of the option.
				'desc'          => string,					// Description of the option, usualy appearing beneath the field control.
				'default'       => string|array|boolean		// Value set as default
				'class'			=> string					// Append class(es) to field class attribute
			// Tax terms meta core attrs ( handled by Plethora_Fields_Termsmeta )
				'admin_col'          => boolean				// Display this option value on terms list table ( default: false )
				'admin_col_sortable' => boolean				// Display this option value on terms list table and make it sortable ( default: false )
				'admin_col_markup'   => string				// Terms list table column markup ( Use %1$s for value, %2$s for the title )

		These are additional field-specific attributes:

			'text' field:
				'placeholder' => string		// Text to display in the input when no value is present.

			'textarea' field"
				'placeholder' => string		// Text to display in the input when no value is present.
				'rows'        => string		// Numbers of text rows to display

			'select' field"
				'options' => array()		// Array of options in key pair format.  The key represents the ID of the option.  The value represents the text to appear in the selector.
				'multi'   => boolean		// Set true for multi-select variation of the field  ( default: false )
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

/**
 * @package Plethora Framework
 * @version 1.0
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c) 2017
 *
 */
class Plethora_Fields_Termsmeta extends Plethora_Fields {

	public $taxonomy;
	public $term_id;

    public function __construct( $taxonomy, $fields_mapping ) {

		$this->taxonomy       = $taxonomy;
		$this->fields_mapping = $fields_mapping;
		$this->admin_monitors = array( 'edit-tags.php', 'term.php' );
    	add_action( 'admin_init', array( $this, 'initialize' ), 20 );
    }

    public function initialize() {

    	// Check if taxonomy is valid first
    	if ( is_admin() && taxonomy_exists( $this->taxonomy ) ) {

	    	// Set term ID ( is a valid value, if we are in an edit page, otherise is set to 0 )
    		$this->term_id = !empty( $_GET['tag_ID'] ) ? intval( $_GET['tag_ID'] ) : 0;

			// Build form field options according to given configuration
	    	$this->init( $this->fields_mapping );

	    	// Add fields to new term form
	    	add_action( $this->taxonomy .'_add_form_fields', array( $this, 'taxonomy_add_form_fields' ), 10, 2 );

	    	// Save the new created meta
	    	add_action( 'created_'. $this->taxonomy , array( $this, 'taxonomy_save_meta' ), 10, 2 );

	    	// Add fields to update term form
			add_action( $this->taxonomy .'_edit_form_fields', array( $this, 'taxonomy_edit_form_fields' ), 10, 2 );

	    	// Save the updated meta
	    	add_action( 'edited_'. $this->taxonomy , array( $this, 'taxonomy_update_meta' ), 10, 2 );

	    	// Add columns on terms list table headers
	    	add_action( 'manage_edit-'. $this->taxonomy .'_columns' , array( $this, 'taxonomy_add_columns' ) );

	    	// Add columns on terms list table headers
	    	add_action( 'manage_edit-'. $this->taxonomy .'_sortable_columns' , array( $this, 'taxonomy_add_columns_sortable' ) );

	    	// Add columns content on terms list table 
			add_filter('manage_'. $this->taxonomy .'_custom_column', array( $this, 'taxonomy_add_columns_content' ), 10, 3 );
		}
    }

	/**
	* Returns an array with all field attributes that should be included on the final configuration
	* Each attribute should be follow a $attr_name => '' pattern
	* NOTICE: Always get parent method first!
	* @return array()
	*/
    public function get_fields_attribute_index(){

    	$fields_attribute_index = parent::get_fields_attribute_index();

    	// These options will be appended on Plethora_Fields opts_core_attrs
		$fields_attribute_index['admin_col']          = false;	// Display this option value on terms list table ( default: false )
		$fields_attribute_index['admin_col_sortable'] = false;	// Display this option value on terms list table and make it sortable ( default: false )
		$fields_attribute_index['admin_col_markup']   = false; 	// Display markup for terms list table ( admin_col OR admin_col_sortable must be true )
		return $fields_attribute_index;
    }

    /**
    * This method is used by the base class to determin the saved field value
    * @return multi ( string, array, boolean )
    */
    public function get_field_value( $field_id, $default ) {

     	$field_value = $default; 

     	if ( !empty( $field_id ) && $this->term_id  ) {

     		$field_value = get_term_meta( $this->term_id, $field_id, true );
     	} 

     	return $field_value;
    }

    /**
    * Adds form fields to add new term admin screen
    * Hooked @ $this->taxonomy .'_add_form_fields'
    * @return NULL
    */
    public function taxonomy_add_form_fields() {

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {

     		$meta_key = $field['id'];

     		echo '<div class="form-field term-'.$meta_key.'-wrap">';
     		echo trim( $field['output_label'] );
     		echo trim( $field['output_field'] );
     		echo trim( $field['output_desc'] );
     		echo isset( $field['output_js_init'] ) ? trim( $field['output_js_init'] ) : '';
     		echo '</div>';
     	}
     }


    /**
    * Saves new term form meta fields
    * Hooked @ 'created_'. $this->taxonomy
    * @return NULL
    */
     public function taxonomy_save_meta( $term_id, $tt_id ) {

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {
     		
     		$meta_key = $field['id'];
     		$meta_value = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : '';
	        add_term_meta( $term_id, $meta_key, $meta_value, true );
     	}
    }

	/**
	* Adds form fields to edit term admin screen
    * Hooked @ $this->taxonomy .'_edit_form_fields'
	* @return NULL
	*/
    public function taxonomy_edit_form_fields( $term, $taxonomy  ) {

     	$term_id = $term->term_id;
     	$fields = $this->fields;
     	foreach ( $fields as $field ) {

     		$meta_key = $field['id'];
     		$meta_value = get_term_meta( $term_id, $meta_key, true  );

     		echo '<tr class="form-field form-required term-name-wrap">';
     		echo '<th scope="row">';
     		echo trim( $field['output_label'] );
     		echo '</th>';
     		echo '<td>';
			echo trim( $field['output_field'] );
     		echo trim( $field['output_desc'] );
     		echo isset( $field['output_js_init'] ) ? trim( $field['output_js_init'] ) : '';
     		echo '</td>';
     		echo '</tr>';
     	}
    }

    /**
    * Saves update term form meta fields
    * Hooked @ 'edited_'. $this->taxonomy
    * @return NULL
    */
    public function taxonomy_update_meta( $term_id, $tt_id ) {

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {
     		
     		if ( empty( $_POST['_inline_edit'] ) ) { // make sure that will not update plethora fields on quick edit updates
	     		$meta_key = $field['id'];
	     		$meta_value = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : '';
				update_term_meta( $term_id, $meta_key, $meta_value );
			}
     	}
    }

	public function taxonomy_add_columns( $columns ){

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {

     		if ( $field['admin_col'] ) {

	    		$columns[$field['id']] = $field['title'];
	    	}
	    }
	    return $columns;
	}

	public function taxonomy_add_columns_sortable( $columns ){

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {

     		if ( $field['admin_col'] && $field['admin_col_sortable'] ) {

	    		$columns[$field['id']] = $field['id'];
	    	}
	    }
	    return $columns;
	}

	public function taxonomy_add_columns_content( $content, $column_name, $term_id ){
	    
		$fields = $this->fields;
		foreach ( $fields as $field ) {

		    if( $column_name !== $field['id'] ){
		        continue;
		    }

			$term = get_term( $term_id, $this->taxonomy );
			$saved_meta_value = get_term_meta( $term_id, $field['id'], true );
			$field_type = $field['type'];
			
			if ( !empty( $saved_meta_value ) && in_array( $field_type, array( 'checkbox', 'radio', 'select' ) ) ) {

				$opt_title = '';
				if ( is_array( $saved_meta_value ) ) {
					$count_this = 0;
					foreach ( $saved_meta_value as $key => $title ) {
						
						$opt_title .= $count_this && isset( $field['options'][$key] ) ? ', '. $field['options'][$key] : $field['options'][$key];
						$count_this++;
					}
				} else {

					$opt_title .= $saved_meta_value;
				}

			} elseif ( !empty( $saved_meta_value ) && in_array( $field_type, array( 'text', 'textarea' ) ) ) {

				$opt_title = $saved_meta_value;

			} elseif ( !empty( $saved_meta_value ) && in_array( $field_type, array( 'media' ) ) ) {

				$opt_title = '<center><span style="background:#f1f1f1; display:inline-block"><img src="'. $saved_meta_value .'" width="80"/></span></center>';

			} elseif ( !empty( $saved_meta_value ) && in_array( $field_type, array( 'color' ) ) ) {

				$opt_title = '<center><div style="background-color:'. $saved_meta_value .'; height:30px; width:30px;"></div></center>';

			} else {

				$opt_title = '';
			}

		    if ( !empty( $field['admin_col_markup'] ) ) {

		        $content .= sprintf( $field['admin_col_markup'], $saved_meta_value, $opt_title, $field['title'], $term->name );
		   
		    } else {

		        $content .= $opt_title;
		    }
		}
	    return $content;
	}
}

// Keep Plethora_TermsMeta class only to avoid fatal errors on theme updates ( when new PFL is not installed yet )
class Plethora_TermsMeta {

    public function __construct( $taxonomy, $fields_mapping ) {

		$obj = new Plethora_Fields_Termsmeta( $taxonomy, $fields_mapping );
		return $obj;
    }
}