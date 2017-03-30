<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Controller class for shortcodes

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Shortcode')) {
 
    /**
     * @package Plethora Controllers
     */
	class Plethora_Shortcode {

		public $shortcode_slug; 	    										// SHORTCODE SLUG 
		public $shortcode_map; 	    											// SHORTCODE MAPPING 
		public $shortcode_extras; 												// SHORTCODE VC EXTRAS (Design Options Tab, etc.)
		public static $controller_title             = 'Shortcodes Manager';		// CONTROLLER TITLE
        public static $controller_description       = 'Activate/deactivate any Plethora shortcode available. Notice that on deactivation, all dependent features will be deactivated automatically.';
		public static $controller_dynamic_construct = true;						// DYNAMIC CLASS CONSTRUCTION
		public static $controller_dynamic_method    = 'init';					// INVOKE ANY METHOD AFTER DYNAMIC CONSTRUCTION? ( METHOD NAME OR FALSE )
		public static $dynamic_features_loading     = true;						// LOAD FEATURES DYNAMICALLY ( ALWAYS TRUE, FALSE IF STATED SO IN CONTROLLER VARIABLES )
   	  	
        /** 
         * Initializes Shortcode controller customization
         * @since 1.0
         */
   	  	public function init() {

			# AVOID SHORTCODE PHP NOTICES IF VC IS NOT ENABLED
   	  		add_action ( 'init', array( $this, 'vc_not_activated' ) );

			# VISUAL COMPOSER CUSTOMIZATION

			// SET VC TO BEHAVE AS INSTALLED ON THEME + REMOVE VC DEFAULT TEMPLATES
		    add_filter( 'vc_load_default_templates', array( 'Plethora_Shortcode', 'remove_default_templates' ));	

			// SET PLETHORA CUSTOM TEMPLATES DIRECTORY
			if ( function_exists( 'vc_set_shortcodes_templates_dir' ) ) { vc_set_shortcodes_templates_dir( PLE_THEME_TEMPLATES_DIR .'/shortcodes' ); }

			// ADD CUSTOM VC FIELDS
   	  		if ( is_admin() && function_exists( 'vc_map' ) ) { 

			    self::add_shortcode_param( 'dropdown_posts', array( 'Plethora_Shortcode', 'vc_field_select_posts'), '');			// Add the post dropdown field function, via add_shortcode_param
			    self::add_shortcode_param( 'dropdown_post_types', array( 'Plethora_Shortcode', 'vc_field_select_post_types'), '');	// Add the post dropdown field function, via add_shortcode_param
			    self::add_shortcode_param( 'value_picker', array( 'Plethora_Shortcode', 'vc_field_value_picker'), '');				// Add value_picker parameter, via add_shortcode_param
			    self::add_shortcode_param( 'switcher', array( 'Plethora_Shortcode', 'vc_field_switcher'), '');						// Add vc_field_switcher parameter, via add_shortcode_param

				// Add the icon dropdown field function, via add_shortcode_param
			    // $this->add_shortcode_param('iconpicker', array( 'Plethora_Shortcode', 'vc_field_select_icons' ), PLE_CORE_LIBS_URI . '/extend_vc/iconpicker/icon-picker.js' );

		        // add Prettyphoto functionality to single image shortcode                                                                                   
		        add_action('vc_after_init', array( $this, 'vc_single_image_prettyphoto' )); 

				// Apply CUSTOM script/style ONLY on admin post edit/new pages that use VC
				add_action( 'admin_enqueue_scripts', array( 'Plethora_Shortcode', 'vc_enqueues' ));
			}	
  		}

        /** 
         * If VC is not active, place all workarounds here
         * @since 1.0
         */
        public function vc_not_activated() {

			if ( ! defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {

				define( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG', '' );
			}
		}

//// VISUAL COMPOSER METHODS START HERE

        /** 
         * Add shortcode action
         * @since 1.0
         */
        public function add( $map, $vc_options = array() ) {

        	// PREPARE SETTINGS VARIABLES FOR FILTERING
			$tag = !empty( $map['base'] ) ? $map['base'] : strtolower( get_called_class() ) ;   // If no slug name given, shortcode slug will be the shortcode class name
			$map['category'] =  esc_html__('Plethora Shortcodes', 'plethora-framework');	// VC EXTRAS
			$this->shortcode_slug   = $tag;   		// SET SHORTCODE SLUG
			$this->shortcode_map    = $map;			// SET PARAMS
			$this->shortcode_extras = $vc_options;	// VC EXTRAS

            // Add the shortcode. content() must prepare attributes/content and return an output OR a template part file
            if ( ! shortcode_exists( $tag ) ) {

            	add_shortcode( $tag, array( $this, 'content' ) );
            }
			// VC panel options mapping
   	  		if ( is_admin() ) { 

	            // Map shortcode options on 'init'...this will allow filter application
	        	add_action( 'init', array( $this, 'map_vcpanel'), 50 );
        	}
        }

	    /**
         * Mapping shortcode parameters for Visual Composer Panel
	     * @since 1.0
	     */
        public function map_vcpanel() {

        	$map = $this->shortcode_map;
			if ( !isset($map) || empty($map) || !is_array($map) ) { return; }
        	// Filter hook to override shortcode parameters
			$filter_name     = strtolower( get_called_class() ) .'_map';		// Set the class name variable as a hook prefix
			$shortcode_vcmap = apply_filters( $filter_name, $map );				// Filter mapping using a hook name pattern ( ie. 'plethora_shortcode_button_map' will filter Button shortcode mapping )
	        self::vc_map( $shortcode_vcmap, $this->shortcode_extras );
		}

	    /**
         * Mapping shortcode parameters for Visual Composer Panel
	     * @since 1.0
	     */
        public static function vc_enqueues() {

			// Apply the script/style on specific post types
			$post_types = get_option( 'wpb_js_content_types', array( 'page' ) );
        	if( $post_types ==! null ) {
        		
        		if( in_array( get_post_type(), $post_types ) ) {

		            wp_enqueue_style(  'plethora-vc-admin', PLE_CORE_LIBS_URI . '/extend_vc/vc_custom.css', array() );
		            wp_enqueue_script( 'plethora-vc-admin', PLE_CORE_LIBS_URI . '/extend_vc/vc_custom.js',  array(), null, true);
					// Icon picker extension styling ( js is already enqueued earlier with add_shortcode_param )
		            wp_enqueue_style(  'plethora-vc-iconpicker', PLE_CORE_LIBS_URI . '/extend_vc/iconpicker/icon-picker.css', array() );
					// ...other extensions scripts/styles here please!
			
		        }
			}
		}


	    /**
         * Mapping shortcode parameters for Visual Composer Panel ( statically )
         * VC wrapper method 
	     * @since 1.0
	     */
        public static function vc_map( $map, $vc_options = '' ) {

			global $vc_add_css_animation;

			$vc_design_options_tab = array(
					'type'           => 'css_editor',
					'heading'        => esc_html__( 'CSS box', 'plethora-framework' ),
					'param_name'     => 'css',
				 // 'description' 	 => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework' ),
					'group'          => esc_html__( 'Design Options', 'plethora-framework' )
	        );

			$vc_options_array = array(
				"design_options" => $vc_design_options_tab,
				"css_animation"  => $vc_add_css_animation
			);

			if ( !empty($vc_options) ){

				foreach ( $vc_options as $value ) array_push( $map['params'], $vc_options_array[$value] );

			}

		    if ( function_exists( 'vc_map' )) vc_map( $map );

		}


	    /**
         * Remove wpautop from shortcode content
         * VC wrapper method 
	     * @since 1.0
	     */
		public static function vc_shortcode_custom_css_class( $param_value, $prefix = '' ) {

			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {

				return vc_shortcode_custom_css_class( $param_value, $prefix );
			}

			return $param_value;
		}

	    /**
         * Remove wpautop from shortcode content
         * VC wrapper method 
	     * @since 1.0
	     */
		public static function remove_wpautop( $content ) {

			if ( function_exists('wpb_js_remove_wpautop') ) {

				$content = wpb_js_remove_wpautop($content, true);

			}

			return $content;
		}

		public static function remove_default_templates( $data ) {

			return array(); // This will remove all default templates
		}

	    /**
	     * Add Prettyphoto functionality to single image shortcode
	     * @since 1.3
	     */
		static function vc_single_image_prettyphoto() {

			if ( ! shortcode_exists( 'vc_single_image' ) ) { return; }

			// Update extra class parameter...'prettyphoto' value is a must here!
			$param                = WPBMap::getParam('vc_single_image', 'el_class');
			$param['value']       = 'prettyphoto';
			$param['description'] = esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file. Leave "prettyphoto" class for popup functionality, if you want link to larger image.', 'plethora-framework');
			WPBMap::mutateParam('vc_single_image', $param);

			// Update "Link To Larger Image" description
			$param = WPBMap::getParam('vc_single_image', 'img_link_large');
			$param['description'] = esc_html__('If checked, image will be linked to a popup displaying a larger version', 'plethora-framework');
			WPBMap::mutateParam('vc_single_image', $param);
	    }

		static function vc_field_select_posts( $settings, $value) {

			$post_type  = (isset($settings['type_posts']) && !empty($settings['type_posts'])) ? $settings['type_posts'] : array('post') ;
			$return     = '<div class="'.$settings['param_name'].'_block">';
			$paramName  = $settings['param_name'];

			$return .= "<script type='text/javascript'>
		   			  function loopSelected_$paramName()
		   			  {
						var i;
						var txtSelectedValuesObj = document.getElementById('$paramName');
						var selectedArray        = new Array();
						var selObj               = document.getElementById('$paramName'+'_select');
						var count                = 0;
						var sLength              = selObj.options.length;
		   			    for ( i=0; i < sLength; i++ ) {
		   			      if ( selObj.options[i].selected ) {
		   			        selectedArray[count] = selObj.options[i].value;
		   			        count++;
		   			      }
		   			    }
		   			    txtSelectedValuesObj.value = selectedArray;
		   			  }
		   			  </script>";

		   $return  .= '<select multiple id="'. $paramName .'_select" class="wpb_vc_param_value wpb-input wpb-select layout dropdown classic" style="width:100%;" onchange="loopSelected_'. $paramName .'();">';
		 		    // Set selected values array from saved string
		 		    $sel_value = ( is_string($value) ) ? explode (',', $value) : $value;
					$args = array( 
						'posts_per_page'   => -1,
						'orderby'          => 'post_date',
						'order'            => 'DESC',
						'post_type'        => $post_type,
						'post_status'      => 'publish',
						);
					$posts = get_posts( $args );
					foreach ($posts as $post) { 

						if ( is_array($sel_value) && in_array($post->ID, $sel_value) ) { 

							$selected = ' selected="selected"';
							
						} elseif (!is_array($sel_value) && $sel_value == $post->ID) { 

							$selected = ' selected="selected"';

						} else {

							$selected = '';
						}
						$return  .= '<option value="'. $post->ID .'"'. $selected .'>'. $post->post_title .'</option>';
					}           
		   $return .= '</select>';
		   $return .= '<input name="'.$paramName.'" id="'.$paramName.'" class="wpb_vc_param_value '.$paramName.' '.$settings['type'].'" type="hidden" value="'. $value .'"/>';
		   $return .= '</div>';
           return $return;
		}

		static function vc_field_select_post_types( $settings, $value) {
   
		   $return  = '<div class="'.$settings['param_name'].'_block">';
		   // Prepare arguments for get_supported_post_types();
		   $args  = (isset($settings['args']) && !empty($settings['args'])) ? $settings['args'] : array() ;
    	   $args['output'] = 'objects'; // make sure that this will return objects
		   $return  .= '<select name="'.$settings['param_name'].'" id="'.$settings['param_name'].'" class="wpb_vc_param_value '.$settings['param_name'].' '.$settings['type'].'">';
				
		   $post_types = Plethora_Theme::get_supported_post_types( $args );
		   foreach ($post_types as $slug => $post_type ) {

				$selected =  $value === $slug ? ' selected="selected"' : '';
				$return  .= '<option value="'.  esc_attr( $slug ).'"'. $selected .'>'. $post_type->labels->name .'</option>';
		   }           
		   $return .= '</select>';
		   $return .= '</div>';
           return $return;
		}

		static function vc_field_value_picker( $settings, $value) {

			$values_index    = ( !empty($settings['values_index']) ) ? $settings['values_index'] : array();	// GET CLASSES INDEX FOR THE CHECKBOXES SETUP ( $settings['values_index'] )
			$selected_values = ( is_string($value) ) ? explode (',', $value) : array($value);				// SET SELECTED VALUES ARRAY FROM SAVED STRING
			$picker_type     = ( isset($settings['picker_type'] ) && $settings['picker_type'] === 'multiple') ? 'checkbox' : 'radio';	// SET SELECTED VALUES ARRAY FROM SAVED STRING
			$col_width       = ( isset($settings['picker_cols'] ) && is_numeric($settings['picker_cols']) ) ? intval($settings['picker_cols']): 4;     // CALCULATE COLUMN SIZE ( ACCORDING TO GIVEN WIDTH )
			$col_width       = floor( 12 / $col_width );

			$paramName = $settings['param_name'];	

			$return  = '<div id="'.$paramName.'_block" class="'.$paramName.'_block">';
			foreach ( $values_index as $name=>$element) {
				$checked =  in_array($element, $selected_values) ? ' checked' : '';
				$return .= '<div class="vc_col-sm-'. $col_width .'" style="padding:0; margin-bottom:10px;">';
				$return .= '<label style="display:table; width:100%;">';
				$return .= '	<span style="display:table-cell;width:16px; text-align:left !important;"><input'. $checked .' type="'.$picker_type.'" name="'.$paramName.'_values_index" value="'.$element.'" onchange="updateValIndex_'. $paramName .'();" style="width:16px;"></span>';
				$return .= '	<span style="display:table-cell;width:auto; padding-left:5px; text-align:left !important;" >'. $name .'</span>';
				$return .= '</label>';
				$return .= '</div>';
			}
			$return .= '<input name="'.$paramName.'" id="'.$paramName.'" class="wpb_vc_param_value '.$paramName.' '.$settings['type'].'" type="hidden" value="'. $value .'"/>';
			$return .= '</div>';

			$return .= "<script type='text/javascript'>
			 		   function updateValIndex_$paramName()
			 		   {
						var i;
						var txtSelectedValuesObj = document.getElementById('$paramName');
						var selectedArray        = new Array();
						var checkBoxes           = document.getElementsByName('$paramName'+'_values_index');
						var count                = 0;
						var checkBoxesLength     = checkBoxes.length;
			 		    
			 		     for ( i = 0; i < checkBoxesLength; i++ ) {
			 		       if ( checkBoxes[i].checked ) {
			 		         selectedArray[count] = checkBoxes[i].value;
			 		         count++;
			 		       }
			 		     }
			 		     txtSelectedValuesObj.value = selectedArray;
			 		   }
			 		   </script>";

			return $return;
		}

		static function vc_field_switcher( $settings, $value) {

			$values_index    = ( !empty($settings['value']) ) ? $settings['value'] : array() ;	// GET CLASSES INDEX FOR THE CHECKBOXES SETUP ( $settings['values_index'] )
			$selected_values = $value;													// SET SELECTED VALUES ARRAY FROM SAVED STRING
			$paramName    = $settings['param_name']; 
			$settingsType = $settings["type"];

			$return  = '<div id="'.$paramName.'_block" class="'.$paramName.'_block">';
			foreach ( $values_index as $name=>$element) {
				$checked =  $element == $value ? ' checked="checked"' : '';
				$return .= '<div class="vc_col-sm-3" style="padding:0; margin-bottom:0px;">';
				$return .= '<label style="display:table; width:100%;">';
				$return .= '	<span style="display:table-cell;width:16px; text-align:left !important;"><input'. $checked .' type="radio" name="'.$paramName.'_values_index" value="'.$element.'" onchange="updateValIndex_'. $paramName .'();" style="width:16px;"></span>';
				$return .= '	<span style="display:table-cell;width:auto; padding-left:5px; text-align:left !important;" >'. $name .'</span>';
				$return .= '</label>';
				$return .= '</div>';
			}

			$return .= "<input name='$paramName' id='$paramName' class='wpb_vc_param_value $paramName $settingsType' type='hidden' value='$value' />
					   </div>
					   <script type='text/javascript'>
					   function updateValIndex_$paramName()
					   {
						var i;
						var txtSelectedValuesObj = document.getElementById('$paramName');
						var selectedArray        = new Array();
						var checkBoxes           = document.getElementsByName('$paramName'+'_values_index');
						var count                = 0;
						var checkBoxesLength     = checkBoxes.length;
					     for ( i = 0; i < checkBoxesLength; i++ ) {
					       if ( checkBoxes[i].checked ) {
					         selectedArray[count] = checkBoxes[i].value;
					         count++;
					       }
					     }
					     txtSelectedValuesObj.value = selectedArray;
					   }
					   </script>";

			return $return;
		}
	    /**
	     * Loads custom VC parameter types
	     * @param 
	     * @return string
	     * @since 1.2
	     *
	     */
	    static function vc_field_select_icons( $settings, $value ) {

	      if( isset( $value ) && $value !== "" ) { 

			$input_value   = esc_attr( $value ); 
			$ev            = explode( '|', $value ); 
			$selected_icon = $ev[0].' '.$ev[1];

	      } else {

	        $input_value = ''; 
	        $selected_icon = "fa fa-plus-circle";

	      }

	       return '<div class="iconpicker_block">'
	       // name="icon_picker_settings[icon1]"
	        .'<input id="icon_picker" name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-textinput '
	        .$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'
	        .$input_value.'" />'
	        .'<div id="preview_icon_picker" data-target="#icon_picker" class="button icon-picker '.$selected_icon.'"></div>'
	        .'</div>';
	    }		

	    /**
	     * Add shortcode parameter method ( extends Visual Composer parameters ).
	     * @since 1.0
	     */
		public static function add_shortcode_param( $name, $callback_function, $js = '') {

			if ( function_exists('vc_add_shortcode_param') ) { 

				if ( empty($js) ) { 

					vc_add_shortcode_param( $name, $callback_function );

				} else { 
					vc_add_shortcode_param( $name, $callback_function, $js );

				}
			}
		}

	    /**
         * Handles Visual Composer's vc_build_link, in case VC is not installed
	     * @since 1.0
	     */
		static function vc_build_link( $string ) {

			if ( function_exists('vc_build_link')) { 

            	$link = vc_build_link( $string );

			} else { 

	            $array = explode('|', $string);
	            $link['url'] 	= ( isset($array[0]) && !empty($array[0]) ) ? rawurldecode(substr($array[0], 4)) : '';
	            $link['title'] 	= ( isset($array[1]) && !empty($array[1]) ) ? rawurldecode(substr($array[1], 6)) : '';
	            $link['target'] = ( isset($array[2]) && !empty($array[2]) ) ? rawurldecode( substr($array[2], 7)) : '';
			}
			
			return $link;
		}

	    /**
         * Handles custom markup display on admin editor for all shortcodes
	     * @since 2.0
	     */
		public function vc_custom_markup( $title, $image_url = false ) {
	        // PLENOTE: This is a new VC feature that allows to render a custom view for the admin editor. However, we have still to figure a way to receive the attribute values for better display
			if ( $image_url === false ) { 

				$folder       = $folder = Plethora_Theme::get_feature( 'shortcode', $this->wp_slug, 'folder' );
				$image_url    = $folder .'/thumb.png';
			} 
	        $markup  = '<div style="display:inline-block;"><img src="'. esc_url($image_url) .'" style="height:48px;" /></div>';
	        $markup .= '<div style="display:inline-block; vertical-align:top; padding-left:5%;"><h4 style="margin-top:0;">'. $title .'</h4></div>';
			return $markup;
		}

	    /**
         * Handles custom icon display on admin editor for all shortcodes
	     * @since 2.0
	     */
		public function vc_icon( $icon_url = false ) {

			if ( $icon_url === false && is_admin() ) { 

				$reflector = new ReflectionClass( get_called_class() );
				$fn = $reflector->getFileName();
				$folder = dirname($fn);				
				$icon_file = $folder .'/icon.png';

				// Get correct URL and path to wp-content
				$content_url = untrailingslashit( dirname( dirname( get_stylesheet_directory_uri() ) ) );
				$content_dir = untrailingslashit( dirname( dirname( get_stylesheet_directory() ) ) );

				// Fix path on Windows
				$icon_file = str_replace( '\\', '/', $icon_file );
				$content_dir = str_replace( '\\', '/', $content_dir );

				$icon_url = str_replace( $content_dir, $content_url, $icon_file );
				unset($reflector);
			}

			return $icon_url;
		}
//// VISUAL COMPOSER METHODS END HERE

//// GENERAL HELPER METHODS START HERE

	    /**
         * Returns all shortcodes detailed information ( slug, attributes, content ) contained in given content
	     * @since 2.0
	     */
		public static function get_content_shortcodes( $content ) {

		  // let's get healthy
		  $return = array();
		  if ( false === strpos( $content, '[' ) ) { return $return; }

		  // Get the matches
		  $all_shortcodes_regex = '\[(\[?)(\w+)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
		  preg_match_all( '/' . $all_shortcodes_regex . '/U', $content, $matches, PREG_SET_ORDER );
		  if ( empty( $matches ) ) { return $return; }


		  // Compose return array
		  foreach ( $matches as $shortcode ) {

		      $item['slug'] = $shortcode[2];
		      $item['content'] = $shortcode[5];
		      $nested_shortcodes = self::get_content_shortcodes($item['content']); // Repeat procedure for nested shortcodes
		      $item['has_nested'] = !empty( $nested_shortcodes ) ? true : false;

		      $atts = trim($shortcode[3]);
		      // $att_keys = preg_split( '/(\w+)(?!\B"[^"]*)=(?![^"]*"\B)/', $atts, NULL,   PREG_SPLIT_DELIM_CAPTURE ); // match all shortcode attribute keys ( in odd key ) & values ( on even key )
		      $att_keys = preg_split( '/(\w+)(?!\b"[^"]*)=(?![^"]*"b)/', $atts, NULL,   PREG_SPLIT_DELIM_CAPTURE ); // match all shortcode attribute keys ( in odd key ) & values ( in even key )
		      unset($att_keys[0]); // remove first occurence..always empty due to regex pattern

		      foreach ( $att_keys as $key => $att_val ) {

		        if ($key % 2 == 0) { // add attribute on even key number
		          $att_key = $key - 1;
		          $att_val = trim( $att_val );
		          $att_val = trim( $att_val, '"' );
		          $item['atts'][$att_keys[$att_key]] = $att_val;
		        }
		      }

		      $return[] =  $item;

		      // Add nested shortcodes occurencies
		      if ( $item['has_nested'] ) { 

		        $return = array_merge( $return, $nested_shortcodes );
		      }
		 }

		  return $return;
		}

	    /**
         * Check if a specific shortcode contained in content. 
         * Can return true, even if shortcode is not registered
         * Useful for checks before add_shortcode actions
	     * @since 2.0
	     */
		public static function has_shortcode( $content, $tag, $validate = false ) {

			$shortcodes = self::get_content_shortcodes( $content );

			if ( !empty( $shortcodes ) ) {
				foreach ( $shortcodes as $shortcode ) {

					if ( $shortcode['slug'] === $tag ) {

						if ( ( $validate && shortcode_exists( $tag ) ) || ! $validate  ) {

							return true;
						} 
					}
				}
			}

			return false;
		}

	    /** 
         * Returns parameter mapping AND default values
	     */
	    public function params() {

        	// setup theme options according to configuration
			$params_index  = method_exists( $this, 'params_index' ) ? $this->params_index() : array();
			$params_config = method_exists( $this, 'params_config' ) ? $this->params_config() : array();
			$params        = array();

        	foreach ( $params_config as $param_config ) {

        		if ( array_key_exists( $param_config['id'], $params_index ) ) {

					$id          = $param_config['id'];
					$default_val = isset( $param_config['default'] ) ? $param_config['default'] : $params_index[$id]['default'] ;
					$field_size  = isset( $param_config['field_size'] ) ? $param_config['field_size'] : '';

        			if ( $params_index[$id]['type'] === 'help'  ) { // if this is a help field, set the 'default' argument
					
						$params_index[$id]['default'] = $default_val;
					
					} else {

						$params_index[$id]['std'] = $default_val;
					}

        			if ( !empty( $field_size ) ) { 

						$field_size_class = sprintf( 'vc_col-sm-%1$d vc_column', $field_size );
						$params_index[$id]['edit_field_class'] = $field_size_class;
					}
					$params[] = $params_index[$id];
        		}
        	}

			return $params;
	    }

	    /** // TEMP ( has to be abandoned after applying the new param system to all shortcodes )
         * Returns default parameter values as defined on $default_param_values class variables 
	     */
	    public function get_default_param_value( $param_name ) {

	    	$value = '';
	    	$default_param_values = !empty( $this->default_param_values ) ? $this->default_param_values : array();
	    	if ( isset( $default_param_values[$param_name] ) ) {

	    		$value = $default_param_values[$param_name];
	    	}
	    	return $value;
	    }

	    /** 
         * Returns default parameter values as defined on $default_param_values class variables 
	     */
	    public function get_default_param_values() {

        	// setup theme options according to configuration
			$params_index         = method_exists( $this, 'params_index' ) ? $this->params_index() : array();
			$params_config        = method_exists( $this, 'params_config' ) ? $this->params_config() : array();
			$default_param_values = array();

        	foreach ( $params_config as $param_config ) {

				$id = $param_config['id'];
        		if ( $id !== 'content' && array_key_exists( $id, $params_index ) ) {

					$default_val               = isset( $param_config['default'] ) ? $param_config['default'] : $params_index[$id]['default'];
					$default_param_values[$id] = $default_val;
        		}
        	}

			return $default_param_values;
	    }

//// GENERAL HELPER METHODS END HERE
	}
 }