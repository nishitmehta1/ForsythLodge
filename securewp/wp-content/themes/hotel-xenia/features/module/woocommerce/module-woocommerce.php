<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

WooCommerce Plugin Support Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Woocommerce') && !class_exists('Plethora_Module_Woocommerce_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/module-woocommerce.php
   */
  class Plethora_Module_Woocommerce_Ext extends Plethora_Module_Woocommerce { 

	/** 
	* Archive view options_config for theme options
	*/
	public function archive_options_config() {

		$archive_options_config = array(
			array( 
				'id'                    => 'layout', 
				'theme_options'         => true, 
				'theme_options_default' => 'right_sidebar',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'sidebar', 
				'theme_options'         => true, 
				'theme_options_default' => 'sidebar-shop',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'contentalign', 
				'theme_options'         => true, 
				'theme_options_default' => '',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'perpage', 
				'theme_options'         => true, 
				'theme_options_default' => 12,
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'title', 
				'theme_options'         => true, 
				'theme_options_default' => true,
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'title-text', 
				'theme_options'         => true, 
				'theme_options_default' => esc_html__('Shop Title', 'hotel-xenia'),
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'subtitle', 
				'theme_options'         => true, 
				'theme_options_default' => true,
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'subtitle-text', 
				'theme_options'         => true, 
				'theme_options_default' => esc_html__('Shop subtitle here', 'hotel-xenia'),
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'columns', 
				'theme_options'         => true, 
				'theme_options_default' => 3,
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'categorydescription', 
				'theme_options'         => true, 
				'theme_options_default' => 'display',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'breadcrumbs', 
				'theme_options'         => true, 
				'theme_options_default' => 'display',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'resultscount', 
				'theme_options'         => true, 
				'theme_options_default' => 'display',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'orderby', 
				'theme_options'         => true, 
				'theme_options_default' => 'display',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'rating', 
				'theme_options'         => true, 
				'theme_options_default' => 'display',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'price', 
				'theme_options'         => true, 
				'theme_options_default' => 'display',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'addtocart', 
				'theme_options'         => true, 
				'theme_options_default' => 'display',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
			array( 
				'id'                    => 'salesflash', 
				'theme_options'         => true, 
				'theme_options_default' => 'display',
				'metabox'               => false,
				'metabox_default'       => NULL
				),
		);

		return $archive_options_config;
	}

	/** 
	* Single view options_config for theme options / metaboxes
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
					'theme_options_default' => 'sidebar-shop',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
			    if ( $section !== 'all' ) { break; }

			case 'content-elements':
			case 'all':

				$config[] = array( 
					'id'                    => 'title', 
					'theme_options'         => true, 
					'theme_options_default' => true,
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'subtitle', 
					'theme_options'         => true, 
					'theme_options_default' => true,
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
					'id'                    => 'wootitle', 
					'theme_options'         => true, 
					'theme_options_default' => true,
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'breadcrumbs', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'rating', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'price', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'addtocart', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'meta', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'sale', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'tab-description', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'tab-reviews', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'tab-attributes', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'related', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'upsell', 
					'theme_options'         => true, 
					'theme_options_default' => 'display',
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'related-number', 
					'theme_options'         => true, 
					'theme_options_default' => 3,
					'metabox'               => true,
					'metabox_default'       => NULL
				);
				$config[] = array( 
					'id'                    => 'related-columns', 
					'theme_options'         => true, 
					'theme_options_default' => 3,
					'metabox'               => true,
					'metabox_default'       => NULL
				);
			    if ( $section !== 'all' ) { break; }
		}

		return $config;
	}
  }
}