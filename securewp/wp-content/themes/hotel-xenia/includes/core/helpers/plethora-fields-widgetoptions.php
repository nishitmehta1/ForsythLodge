<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2017

Extension class for Plethora_Fields, it handles the widget option fields

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
class Plethora_Fields_Widgetoptions extends Plethora_Fields {

	public $taxonomy;
	public $term_id;

    public function __construct( $taxonomy, $fields_mapping ) {

		$this->taxonomy       = $taxonomy;
		$this->fields_mapping = $fields_mapping;
    	add_action( 'admin_init', array( $this, 'initialize' ), 20 );
    }

    public function initialize() {

    	// Check if taxonomy is valid first
    	if ( is_admin() && taxonomy_exists( $this->taxonomy ) ) {

	    	// Set term ID ( is a valid value, if we are in an edit page, otherise is set to 0 )
    		$this->term_id = !empty( $_GET['tag_ID'] ) ? intval( $_GET['tag_ID'] ) : 0;

			// Build form field options according to given configuration
	    	$this->init( $this->fields_mapping );

	    	// ...the rest config here

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

    	// These options will be appended on all fields configuration
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
}