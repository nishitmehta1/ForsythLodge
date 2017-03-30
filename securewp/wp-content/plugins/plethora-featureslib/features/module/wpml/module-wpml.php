<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			      (c) 2017

WPML Configuration Module Base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Wpml') ) {

	/**
	 */
	class Plethora_Module_Wpml {

		public static $feature_title        = "WPML Compatibility Module";
		public static $feature_description  = "Used for WPML compatibility workarounds";
		public static $theme_option_control  = true;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		
		// wpml-config.xml helper variables
		public $xml_file = 'wpml-config.xml';
		public $xml_obj;
		public $config;

		// Plethora Dev only functionality ( creating the wpml-config.xml file )
		public $dev_mode = false;

		public function __construct() {

			// Add theme options tab to General Section
			add_filter( 'plethora_themeoptions_modules', array( $this, 'theme_options'), 10 );

			add_action('init', array( $this, 'update_config_file'), 999999);
		}

		/** 
		* Returns theme options tab configuration
		* Hooked @ 'plethora_themeoptions_general'
		* @return array()
		*/
		public function theme_options( $sections ) {

			$theme_options = Plethora_Module_Themeoptions::get_themeoptions_fields( $this );
			if ( is_array( $theme_options ) && !empty( $theme_options ) ) {

				$sections[] = array(
					'title'      => esc_html__('WPML', 'plethora-framework'),
					'heading'    => esc_html__('WPML COMPATIBILITY OPTIONS', 'plethora-framework'),
					'subsection' => true,
					'fields'     => $theme_options
				);
			}
			return $sections;
		}

		public function update_config_file() {

			if ( $this->dev_mode ) {
				// Export the XML file
				$this->prepare_document();
				$this->set_custom_fields();
				$this->set_custom_types();
				$this->set_taxonomies();
				$this->set_admin_texts();
				$this->save_file();
			}
		}

		public function get_metabox_translatable_fields(){

			$metabox_texts = array();
			$plethora_metaboxes = array();
			$plethora_metaboxes = apply_filters( 'plethora_metabox_add', $plethora_metaboxes );
			foreach ( $plethora_metaboxes as $metabox ) {
				$sections = isset( $metabox['sections'] ) ? $metabox['sections'] : array();
				foreach ( $sections as $section ) {
					$fields = isset( $section['fields'] ) ? $section['fields'] : array();
					foreach ($fields as $field ) {

						if ( !empty( $field['id'] ) && !array_key_exists( $field['id'], $metabox_texts) ) {

							if ( !empty( $field['translate'] ) && $field['translate'] ) {
								
								$metabox_texts[$field['id']] = 'translate';

							} elseif ( empty( $field['translate'] ) && $field['type'] !== 'section' ) {

								$metabox_texts[$field['id']] = 'copy';

							} else {

								$metabox_texts[$field['id']] = 'ignore';
							}
						} 
					}
				}
			}
			// asort($metabox_texts);
			return array_filter($metabox_texts);

		}

		public function get_post_types(){

			$public_post_types    = Plethora_Theme::get_supported_post_types( array('exclude' => array('post', 'page')) );
			$nonpublic_post_types = Plethora_Theme::get_supported_post_types( array('exclude' => array('post', 'page'), 'public' => false) );
			$post_types           = array_merge($public_post_types, $nonpublic_post_types );
			asort($post_types);
			return array_filter($post_types);
		}

		public function get_taxonomies(){

			$taxonomies = array();
			$post_types = $this->get_post_types();
			foreach ( $post_types as $post_type ) {

				$the_taxonomies = get_object_taxonomies( $post_type );
				$taxonomies     = array_merge($taxonomies, $the_taxonomies );
			}
			asort($taxonomies);
			return array_filter($taxonomies);
		}

		public function get_themeoptions_translatable_fields(){

			$admin_texts = array();
			global $plethora_options_config;
			$sections = $plethora_options_config->sections;
			foreach ( $sections as $section ) {

				$fields = isset( $section['fields'] ) ? $section['fields'] : array();
				foreach ($fields as $field ) {

					if ( !empty($field['type']) && !empty($field['translate']) && $field['translate'] && ( $field['type'] === 'text' || $field['type'] === 'textarea' )) {

						$admin_texts[]['id'] = $field['id'];
					}
				}
			}
			return $admin_texts;
		}

		public function prepare_document() {

			$xml_obj                     = new DOMDocument('1.0', 'UTF-8');
			$xml_obj->preserveWhiteSpace = false;
			$xml_obj->formatOutput       = true;
			$xml_elem                    = $xml_obj->createElement('wpml-config');
			$xml_obj->appendChild($xml_elem);
			$this->xml_obj  = $xml_obj;
			$this->xml_elem = $xml_elem;

		}

		public function set_custom_fields() {

			$xml_obj       = $this->xml_obj;
			$xml_elem      = $this->xml_elem;
			$custom_fields = $xml_obj->createElement('custom-fields');
			$xml_elem->appendChild($custom_fields);
			$metabox_texts = $this->get_metabox_translatable_fields();
			foreach( $metabox_texts as $id_val=> $action_val )  {

				// create the field
				$custom_field = $xml_obj->createElement('custom-field', $id_val );
				// add action attribute
				$action_attr  = $xml_obj->createAttribute ('action');
				$action_attr->value = $action_val;
				$custom_field->appendChild($action_attr);
				// add field to document
				$custom_fields->appendChild($custom_field);

			}
			$this->xml_obj  = $xml_obj;
			$this->xml_elem = $xml_elem;

		}
		public function set_custom_types() {

			$xml_obj      = $this->xml_obj;
			$xml_elem     = $this->xml_elem;
			$custom_types = $xml_obj->createElement('custom-types');
			$types        = $this->get_post_types();

			foreach( $types as $type )  {

				// create the field
				$custom_type = $xml_obj->createElement('custom-type', $type);
				// add 'translate' attribute
				$translate_attr  = $xml_obj->createAttribute ('translate');
				$translate_attr->value = '1';
				$custom_type->appendChild($translate_attr);
				// add field to document
				$custom_types->appendChild($custom_type);
			}

			// add fields to document
			$xml_elem->appendChild($custom_types);
			$this->xml_obj = $xml_obj;

		}
		public function set_taxonomies() {

			$xml_obj    = $this->xml_obj;
			$xml_elem   = $this->xml_elem;
			$taxonomies = $xml_obj->createElement('taxonomies');
			$taxs       = $this->get_taxonomies();

			foreach( $taxs as $tax )  {

				// create the field
				$taxonomy = $xml_obj->createElement('taxonomy', $tax );
				// add 'translate' attribute
				$translate_attr  = $xml_obj->createAttribute ('translate');
				$translate_attr->value = '1';
				$taxonomy->appendChild($translate_attr);
				// add field to document
				$taxonomies->appendChild($taxonomy);
			}
			$xml_elem->appendChild($taxonomies);

			$this->xml_obj = $xml_obj;
			$this->xml_elem = $xml_elem;
		}

		public function set_admin_texts() {

			$xml_obj               = $this->xml_obj;
			$xml_elem              = $this->xml_elem;
			$admin_texts           = $xml_obj->createElement('admin-texts');
			$wrapkey               = $xml_obj->createElement('key');
			$wrap_name_attr        = $xml_obj->createAttribute ('name');
			$wrap_name_attr->value = THEME_OPTVAR;
			$wrapkey->appendChild($wrap_name_attr);
			$admin_texts->appendChild($wrapkey);
			$texts = $this->get_themeoptions_translatable_fields();
			foreach ( $texts as $admin_text) {
				// create the 'key' field
				$key = $xml_obj->createElement('key');
				// add 'name' attribute
				$name_attr  = $xml_obj->createAttribute ('name');
				$name_attr->value = $admin_text['id'];
				$key->appendChild($name_attr);
				// add field to document
				$wrapkey->appendChild($key);
			}
			$xml_elem->appendChild($admin_texts);

			$this->xml_obj = $xml_obj;
			$this->xml_elem = $xml_elem;

		}

		public function save_file(){

			$xml_obj = $this->xml_obj;
			$xml_obj->save( PLE_THEME_DIR .'/'. $this->xml_file );
		}

		/** 
		* MUST HAVE METHOD FOR ALL MODULES USING OPTIONS
		* Returns theme options / metabox fields index
		* Options configuration should not contain 'default' value ( anyway, it will be ignored on the late configuration)
		* @return array()
		*/
		public function options_index() { 

			$options_index  = array();
			return $options_index;
		}

		/** 
		* MUST HAVE METHOD FOR ALL MODULES USING OPTIONS
		* ONLY FOR EXTENSION CLASS USE, THIS IS PLACED HERE FOR REFERENCE & CONSISTENCY
		*
		* Sets a configuration pattern for theme options / metabox fields. You can set the display order
		* ( according to the order given here ) and whether you want a field to be displayed on theme options
		* or the metabox view and finally its default value on both views.
		*
		* 'id': 					the option index key ( don't confuse this with the actual DB saved id )
		* 'theme_options': 			display this field on theme options ( true|false )
		* 'theme_options_default': 	default value, null if we don't need one ( multi|null )
		* 'metabox': 				display this field on metabox options ( true|false )
		* 'metabox_default': 		default value for metabox option, null if we want to inherit the theme options default value ( multi|null)
		*
		* @return array()
		*/
		public function options_config() {

			return array();
		}
	}
}