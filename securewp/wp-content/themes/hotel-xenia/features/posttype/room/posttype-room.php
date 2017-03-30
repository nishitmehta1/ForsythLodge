<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Room Post Type Config Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Posttype_Room') && !class_exists('Plethora_Posttype_Room_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-featureslib/features/posttype/room/posttype-room.php
   */
  class Plethora_Posttype_Room_Ext extends Plethora_Posttype_Room { 

	/** 
	* Single view options_config for theme options and metabox panels
	*/
	public function single_options_config( $section = 'all' ) {

		$config = array();
		switch ( $section ) {
			case 'layout-styling':
			case 'all':

				$config[] = array( 
				  'id'                    => 'layout', 
				  'theme_options'         => true, 
				  'theme_options_default' => 'right_sidebar',
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'sidebar', 
				  'theme_options'         => true, 
				  'theme_options_default' => 'sidebar-rooms',
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'containertype', 
				  'theme_options'         => true, 
				  'theme_options_default' => 'container',
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'colorset', 
				  'theme_options'         => true, 
				  'theme_options_default' => 'foo',
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'content-align', 
				  'theme_options'         => true, 
				  'theme_options_default' => '',
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'extraclass', 
				  'theme_options'         => true, 
				  'theme_options_default' => '',
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
			    if ( $section !== 'all' ) { break; }

			case 'content-elements':
			case 'all':

				$config[] = array( 
				  'id'                    => 'title', 
				  'theme_options'         => true, 
				  'theme_options_default' => 1,
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'subtitle', 
				  'theme_options'         => true, 
				  'theme_options_default' => 0,
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'subtitle-text', 
				  'theme_options'         => false, 
				  'theme_options_default' => NULL,
				  'metabox'               => true,
				  'metabox_default'       => ''
				);
				$config[] = array( 
				  'id'                    => 'info-primarytax', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'info-secondarytax', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
			    if ( $section !== 'all' ) { break; }

			case 'media':
			case 'all':

				$config[] = array( 
				  'id'                    => 'mediadisplay', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'mediadisplay-type', 
				  'theme_options'         => true, 
				  'theme_options_default' => 'gallery',
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery', 
				  'theme_options'         => false, 
				  'theme_options_default' => NULL,
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'media-stretch', 
				  'theme_options'         => true, 
				  'theme_options_default' => 'foo_stretch',
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
			    if ( $section !== 'all' ) { break; }

			case 'media-gallery':
			case 'all':

				$config[] = array( 
				  'id'                    => 'gallery-autoplay', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-autoplaytimeout', 
				  'theme_options'         => true, 
				  'theme_options_default' => 5000,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-autoplayspeed', 
				  'theme_options'         => true, 
				  'theme_options_default' => 1000,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-autoplayhoverpause', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-nav', 
				  'theme_options'         => true, 
				  'theme_options_default' => false,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-dots', 
				  'theme_options'         => true, 
				  'theme_options_default' => false,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-loop', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-mousedrag', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-touchdrag', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-lazyload', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'gallery-rtl', 
				  'theme_options'         => true, 
				  'theme_options_default' => false,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
			    if ( $section !== 'all' ) { break; }

			case 'amenities':
			case 'all':

				$config[] = array( 
				  'id'                    => 'amenities-status', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => true,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-index', 
				  'theme_options'         => true, 
				  'theme_options_default' => self::get_amenities_index_field_default_value(),
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-autoplay', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-autoplaytimeout', 
				  'theme_options'         => true, 
				  'theme_options_default' => 2000,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-autoplayspeed', 
				  'theme_options'         => true, 
				  'theme_options_default' => 1000,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-autoplayhoverpause', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-dots', 
				  'theme_options'         => true, 
				  'theme_options_default' => false,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-loop', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-mousedrag', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-touchdrag', 
				  'theme_options'         => true, 
				  'theme_options_default' => true,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-rtl', 
				  'theme_options'         => true, 
				  'theme_options_default' => false,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'amenities-single-view', 
				  'theme_options'         => false, 
				  'theme_options_default' => NULL,
				  'metabox'               => true,
				  'metabox_default'       => $this->get_amenities_index( 'defaults' )
				);
			   if ( $section !== 'all' ) { break; }

			case 'booking':
			case 'all':

				$config[] = array( 
					'id'                    => 'persons', 
					'theme_options'         => true, 
					'theme_options_default' => true,
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'persons-text', 
					'theme_options'         => false, 
					'theme_options_default' => NULL,
					'metabox'               => true,
					'metabox_default'       => ''
				);
			   if ( $section !== 'all' ) { break; }

			case 'advanced':
			case 'all':

				$config[] = array( 
				  'id'                    => 'info-primarytax-slug', 
				  'theme_options'         => true, 
				  'theme_options_default' => $this->post_type_primary_tax,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'info-secondarytax-slug', 
				  'theme_options'         => true, 
				  'theme_options_default' => $this->post_type_secondary_tax,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);

				$config[] = array( 
				  'id'                    => 'urlrewrite', 
				  'theme_options'         => true, 
				  'theme_options_default' => $this->post_type,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'urlrewrite-primarytax', 
				  'theme_options'         => true, 
				  'theme_options_default' => $this->post_type_primary_tax,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
				$config[] = array( 
				  'id'                    => 'urlrewrite-secondarytax', 
				  'theme_options'         => true, 
				  'theme_options_default' => $this->post_type_secondary_tax,
				  'metabox'               => false,
				  'metabox_default'       => NULL
				);
			    if ( $section !== 'all' ) { break; }
		}
		return $config;
	}
  }
}