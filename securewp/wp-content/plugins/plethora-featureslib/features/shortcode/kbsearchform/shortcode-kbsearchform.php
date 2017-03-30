<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Knowledgebase Search Form shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Kbsearchform') ):

	/**
	 * @package Plethora Framework
	 */

	class Plethora_Shortcode_Kbsearchform extends Plethora_Shortcode { 

		public static $feature_title         = "KB Search Shortcode";  // Feature display title  (string)
		public static $feature_description   = "";                  // Feature display description (string)
		public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;                // Default activation option status ( boolean )
		public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
		public $wp_slug                      =  'kbsearchform';           // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
		public static $assets;

		public function __construct() {

			add_action( 'init', array( $this, 'map' ) );
		}

		public function map() {

				// Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
				$map = array( 
										'base'          => SHORTCODES_PREFIX . $this->wp_slug,
										'name'          => esc_html__("KB Search Form", 'plethora-framework'), 
										'description'   => esc_html__('for plethorathemes.com', 'plethora-framework'), 
										'class'         => '', 
										'weight'        => 1, 
										'category'      => 'Content', 
										'icon'          => $this->vc_icon(), 
										// 'custom_markup' => $this->vc_custom_markup( 'Button' ), 
										'params'        => $this->params(), 
										);
				// Add the shortcode
				$this->add( $map );
		}

		/** 
		* Returns shortcode parameters INDEX for VC panel
		* @return array
		*/
		public function params_index() {

				$params_index['input_placeholder'] = array( 
							"param_name" => "input_placeholder",
							"type"       => "textfield",                                        
							"holder"     => "h3",                                               
							"class"      => "plethora_vc_title",                                                    
							"heading"    => esc_html__("Input Placeholder Text ( no HTML )", 'plethora-framework'),
				);

				$params_index['form_url'] = array( 
							"param_name"  => "form_url",
							"type"        => "vc_link",
							"class"       => "vc_hidden", 
							"heading"     => esc_html__("Form link", 'plethora-framework'),
				);

				$params_index['submit_button'] = array( 
							"param_name" => "submit_button",
							"type"       => "checkbox",                                        
							"class"      => "",                                                    
							"heading"    => esc_html__( "Display Submit Button", 'plethora-framework'),
							'value'      => array( __( 'Yes', 'plethora-framework' ) => '1' ),
				);

				$params_index['submit_button_text'] = array( 
							"param_name" => "submit_button_text",
							"type"       => "textfield",                                        
							"class"      => "",                                                    
							"heading"    => esc_html__("Submit Button Text ( no HTML )", 'plethora-framework'),
							'dependency' => array( 
																		'element' => 'submit_button', 
																		'value'   => '1',  
							)
				);

				$params_index['submit_button_colorset'] = array( 
							"param_name" => "submit_button_colorset",                                  
							"type"       => "dropdown",                                        
							"holder"     => "",                                               
							"class"      => "vc_hidden",                                         
							"heading"    => esc_html__("Button Color Set", 'plethora-framework'),      
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
							'dependency' => array( 
										'element' => 'submit_button', 
										'value'   => '1',  
							)
				);

				$params_index['tax_filter'] = array( 
							"param_name" => "tax_filter",
							"type"       => "checkbox",                                        
							"class"      => "",                                                    
							"heading"    => esc_html__( "Display Products Filter", 'plethora-framework'),
							'value'      => array( esc_html__( 'Yes', 'plethora-framework' ) => '1' ),
				);

				$params_index['tax_filter_checked'] = array( 
							"param_name"  => "tax_filter_checked",
							"type"        => "dropdown",                                        
							"class"       => "",                                                    
							"heading"     => esc_html__( "Default Product Filter", 'plethora-framework'),
							"description" => esc_html__( "Note that this will be applied even if no filters are displayed", 'plethora-framework'),
							'value'       => $this->get_products(),
				);

				$params_index['el_class'] = array( 
							'param_name'  => 'el_class',
							'type'        => 'textfield',
							'heading'     => esc_html__('Extra Class', 'plethora-framework'),
							'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
				);

				$params_index['css'] = array( 
							"param_name"    => "css",
							"type"          => "css_editor",
							'group'         => esc_html__( 'Design options', 'plethora-framework' ),
							"heading"       => esc_html__('CSS box', 'plethora-framework'),
				);

			return $params_index;
		}

			 /** 
			 * Returns taxonomy terms by user value
			 * Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_callback
			 * @return array
			 */
			 public function get_products( $default_val = true ) {

					$values = $default_val ? array( esc_html__( 'None', 'plethora-framework' ) ) : array();
					$post_taxonomy_terms = get_terms( array( 'taxonomy' => 'kb-product' ) );
					if ( ! is_wp_error( $post_taxonomy_terms ) ) {

						foreach ( $post_taxonomy_terms as $term  ) {
							$values[$term->name] = $term->slug; 
						}
					}
					return $values;
			 }

		 /** 
		 * Configure parameters displayed
		 * Will be displayed all items from params_index() with identical 'id'
		 * This method should be used for extension class overrides
		 *
		 * @return array
		 */
		 public function params_config() {

				$params_config = array(
						array( 
							'id'         => 'input_placeholder', 
							'default'    => esc_html__('Search Knowledge Base', 'plethora-framework'),
							'field_size' => '',
							),
						array( 
							'id'         => 'form_url', 
							'default'    => '#',
							'field_size' => '',
							),

						array( 
							'id'         => 'submit_button', 
							'default'    => '1',
							'field_size' => '6',
							),
						array( 
							'id'         => 'submit_button_text', 
							'default'    => esc_html__( 'Search', 'plethora-framework' ),
							'field_size' => '6',
							),
						array( 
							'id'         => 'submit_button_colorset', 
							'default'    => 'btn-default',
							'field_size' => '6',
							),
						array( 
							'id'         => 'tax_filter', 
							'default'    => '1',
							'field_size' => '6',
							),
						 array( 
							'id'         => 'tax_filter_checked', 
							'default'    => '',
							'field_size' => '6',
							),
						array( 
							'id'         => 'el_class', 
							'default'    => '',
							'field_size' => '6',
							),
						array( 
							'id'         => 'css', 
							'default'    => '',
							'field_size' => '',
							),
				);

				return $params_config;
		 }


		/** 
		* Returns shortcode content OR content template
		*
		* @return array
		* @since 1.0
		*
		*/
		public function content( $atts, $content = null ) {

				// Extract user input
				extract( shortcode_atts( $this->get_default_param_values(), $atts ) );

				// Place all values in 'shortcode_atts' variable
				$tax_filter_terms = array();
				$filters = $this->get_products( false );
				$tax_filter_checked = !empty( $_GET['kb-product'] ) ? urldecode( $_GET['kb-product'] ) : $tax_filter_checked;
				foreach ( $filters as $filter_name => $filter_slug ) {

					$tax_filter_terms[] = array(
							'filter_name'    => $filter_name,
							'filter_val'     => $filter_slug,
							'filter_checked' => $tax_filter_checked === $filter_slug ? ' checked' : '',
					);
				}

				$form_url =  self::vc_build_link( $form_url );
				$form_url = !empty( $form_url['url'] ) ? $form_url['url'] : '#';

				$shortcode_atts = array (
																'form_url'               => esc_url( $form_url ),
																'input_placeholder'      => esc_attr( $input_placeholder ),
																'input_value'            => !empty( $_GET['search_term'] ) ? esc_attr( urldecode( $_GET['search_term'] ) ) : '',
																'submit_button'          => $submit_button,
																'submit_button_text'     => esc_attr( $submit_button_text ),
																'submit_button_colorset' => esc_attr( $submit_button_colorset ),
																'tax_filter'             => $tax_filter,
																'tax_filter_terms'       => $tax_filter_terms,
																'tax_filter_hidden'      => $tax_filter ? false : true,
																'tax_filter_hidden_val'  => $tax_filter_checked,
															 );
				$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), $this->wp_slug, $atts );
				$return = '<div class="ple_kbsearchform wpb_content_element '. esc_attr( $el_class ) .' '. esc_attr( $css_class ) .'">';
				$return .= Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "force_template_part" => array( 'templates/shortcodes/kbsearchform' ) ) );
				$return .= '</div>';
				return $return;
		}

	}
	
 endif;