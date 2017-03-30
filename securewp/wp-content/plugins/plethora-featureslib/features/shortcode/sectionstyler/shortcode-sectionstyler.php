<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2017

File Description: Call To Booking shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Sectionstyler') ):

/**
* @package Plethora Framework
*/

class Plethora_Shortcode_Sectionstyler extends Plethora_Shortcode { 

	public static $feature_title         = "Section Styling";   // Feature display title  (string)
	public static $feature_description   = "";                  // Feature display description (string)
	public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
	public static $theme_option_default  = true;                // Default activation option status ( boolean )
	public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
	public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
	public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
	public $wp_slug                      =  'sectionstyler';
	public $default_param_values;
   
	public function __construct() {

		// Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
		$map = array( 
					'base'        => SHORTCODES_PREFIX . $this->wp_slug,
					'name'        => esc_html__("Section Styling", 'plethora-framework'), 
					'description' => esc_html__('Get and transfer basic info to booking form page', 'plethora-framework'), 
					'class'       => '', 
					'weight'      => 1, 
					'icon'        => $this->vc_icon(), 
					// 'custom_markup' => $this->vc_custom_markup( 'Button' ), 
					'params'      => $this->params(), 
					);
		// Add the shortcode
		$this->add( $map );

		// Render triangles markup via shortcode content filter to make them available right under vc_row
		add_filter( 'vc_shortcode_content_filter', array( $this, 'renderTriangles'), 10, 2 );

	}

	public function renderTriangles( $content, $shortcode_slug ){

		if ( $shortcode_slug === 'vc_row' ) {

			$pattern = "\[\[?plethora_sectionstyler(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\])?\]?";

			// Search for [plethora_sectionstyler ...] shortcode inside vc_row content
			if ( preg_match_all( '/'. $pattern .'/s', $content, $matches ) ){

				$triangle_holder = "";							  // This will hold all the triangle shaped divs
				$triangles       = explode(" ", $matches[1][0]);  // Turn shortcode params into an array

				foreach ( $triangles as $value ) {
					if ( empty($value) ) continue;
					$triangle    = explode( "=", $value ); // Split param into key/value ~ position/color
					$pos         = $triangle[0];
					$color       = preg_replace("/\"|\'/", "", $triangle[1]); 
					$small_class = "";
					if ( strlen($pos) > 2 ){
						$small_class = "tri_sm";
						$pos         = substr($pos, 0, 2);
					} 
					$triangle_holder .= "<i class='tri_$pos $small_class' style='border-top-color:$color;'></i>";
				}

				$content   = $triangle_holder . $content;
			}

		}
		return $content;

	}

	/** 
	* Returns shortcode parameters INDEX for VC panel
	* @return array
	*/
	public function params_index() {

		#GENERAL TAB
		$params_index['ul'] = array(
			"param_name"       => "ul",
			"type"             => "colorpicker",
			"group"			   => THEME_DISPLAYNAME . ': '. esc_html__('Styling', 'plethora-boilerplate-theme'),
			'edit_field_class' => 'vc_col-sm-6 vc_column tri_ul',
			"heading"          => esc_html__('Top Left Color', 'plethora-framework')
		);

		$params_index['uls'] = array(
			"param_name"       => "uls",
			"type"             => "colorpicker",
			"group"			   => THEME_DISPLAYNAME . ': '. esc_html__('Styling', 'plethora-boilerplate-theme'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"heading"          => esc_html__('Top Left Small Color', 'plethora-framework'),
		);

		$params_index['ur'] = array(
			"param_name"       => "ur",
			"type"             => "colorpicker",
			"group"			   => THEME_DISPLAYNAME . ': '. esc_html__('Styling', 'plethora-boilerplate-theme'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"heading"          => esc_html__('Top Right Color', 'plethora-framework'),
		);

		$params_index['urs'] = array(
			"param_name"       => "urs",
			"type"             => "colorpicker",
			"group"			   => THEME_DISPLAYNAME . ': '. esc_html__('Styling', 'plethora-boilerplate-theme'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"heading"          => esc_html__('Top Right Small Color', 'plethora-framework'),
		);

		$params_index['bl'] = array(
			"param_name"       => "bl",
			"type"             => "colorpicker",
			"group"			   => THEME_DISPLAYNAME . ': '. esc_html__('Styling', 'plethora-boilerplate-theme'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"heading"          => esc_html__('Bottom Left Color', 'plethora-framework'),
		);

		$params_index['bls'] = array(
			"param_name"       => "bls",
			"type"             => "colorpicker",
			"group"			   => THEME_DISPLAYNAME . ': '. esc_html__('Styling', 'plethora-boilerplate-theme'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"heading"          => esc_html__('Bottom Left Small Color', 'plethora-framework'),
		);

		$params_index['br'] = array(
			"param_name"       => "br",
			"type"             => "colorpicker",
			"group"			   => THEME_DISPLAYNAME . ': '. esc_html__('Styling', 'plethora-boilerplate-theme'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"heading"          => esc_html__('Bottom Right Color', 'plethora-framework'),
		);

		$params_index['brs'] = array(
			"param_name"       => "brs",
			"type"             => "colorpicker",
			"group"			   => THEME_DISPLAYNAME . ': '. esc_html__('Styling', 'plethora-boilerplate-theme'),
			'edit_field_class' => 'vc_col-sm-6 vc_column',
			"heading"          => esc_html__('Bottom Right Small Color', 'plethora-framework'),
		);

		#DESIGN OPTIONS TAB
		$params_index['css'] = array( 
			  "param_name"    => "css",
			  "type"          => "css_editor",
			  'group'         => esc_html__( 'Design options', 'plethora-framework' ),
			  "heading"       => esc_html__('CSS box', 'plethora-framework'),
		);

	  return $params_index;
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
		// extract( shortcode_atts( $this->get_default_param_values(), $atts ) );
		return "";

	}

}
	
endif;