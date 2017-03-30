<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Icons Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Icons') && !class_exists('Plethora_Module_Icons_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-featureslib/features/module/icons/module-icons.php
   */
  class Plethora_Module_Icons_Ext extends Plethora_Module_Icons {

	public $theme_options_tab_static = 1;
	public $fontawesome_status           = true;
	public $plethora_hotel_status        = true;


	public function get_preset_libraries_desc() {

	  $subtitle_text = '<ol style="margin-top:10px; line-height:24px;">';
	  $subtitle_text .= '<li><a href="'. esc_url( 'https://fortawesome.github.io/Font-Awesome/icons/ .' ) .'" target="_blank">Font Awesome</a></li>';
	  $subtitle_text .= '</ol>';
	  return $subtitle_text;
	}

	// Returns the preset libraries ( remember to update the 'static' attribute according to libraries number )
	public function preset_iconlibraries( $return = '' ) {

	  $preset_iconlibraries = array();

	  if ( $return !== 'all' ) { 

		// IMPORTANT: this is necessary for repeater field...add a line for each record
		$preset_iconlibraries['redux_repeater_data'] = array(
			array( 'title' => '' ),
			array( 'title' => '' ),
		);
		$preset_iconlibraries['title'] = array(
			esc_html__('Font Awesome', 'plethora-framework'),
			esc_html__('Hotel Icons', 'plethora-framework'),
		);
		$preset_iconlibraries['status'] = array(
			$this->fontawesome_status,
			$this->plethora_hotel_status,
		);
		$preset_iconlibraries['id'] = array(
			'fontawesome',
			'hotel_icons',
		);
		$preset_iconlibraries['class_prefix'] = array(
			'fa',
			'hi',
		);
	  
	  } elseif ( $return === 'all') { 

		$preset_iconlibraries['selector_prefix'] = array(
			'fa-',
			'hi-',
		);
		$preset_iconlibraries['selector_suffix'] = array(
			':before',
			':before',
		);
		$preset_iconlibraries['stylesheet1'] = array(
			PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/font-awesome.css',
			PLE_CORE_FEATURES_DIR .'/module/icons/default_styles/plethora-hotel.css',
		);

		$preset_iconlibraries['font-family'] = array(
			'FontAwesome',
			'plethora-hotel',
		);
		$preset_iconlibraries['src_eot'] = array(
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.eot',
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/plethora-hotel.eot',
		);
		$preset_iconlibraries['src_svg'] = array(
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.svg',
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/plethora-hotel.svg',
		);
		$preset_iconlibraries['src_ttf'] = array(
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.ttf',
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/plethora-hotel.ttf',
		);
		$preset_iconlibraries['src_woff'] = array(
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.woff',
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/plethora-hotel.woff',
		);
		$preset_iconlibraries['src_woff2'] = array(
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/fontawesome-webfont.woff2',
			'',
		);
		$preset_iconlibraries['src_otf'] = array(
			PLE_CORE_FEATURES_DIR .'/module/icons/default_fonts/FontAwesome.otf',
			'',
		);
		$preset_iconlibraries['font-style'] = array(
			'normal',
			'normal',
		);
		$preset_iconlibraries['font-weight'] = array(
			'normal',
			'normal',
		);
		$preset_iconlibraries['font-stretch'] = array(
			'normal',
			'normal',
		);
	  }
		  return $preset_iconlibraries; 

	}
  }
}