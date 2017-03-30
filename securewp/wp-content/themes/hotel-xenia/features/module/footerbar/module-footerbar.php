<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Footer Bar Module Extension Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Footerbar') && !class_exists('Plethora_Module_Footerbar_Ext') ) {

	/**
	* Extend base class
	* Base class file: /plugins/plethora-framework/features/module/footerbar/module-footerbar.php
	*/
	class Plethora_Module_Footerbar_Ext extends Plethora_Module_Footerbar { 

		/** 
		* Single view options_config for theme options and metabox panels
		*/
		public function options_config() {

			$config   = array();
			$config[] = array( 
				'id'                    => 'footerbar-section', 
				'theme_options'         => false, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'footerbar', 
				'theme_options'         => true, 
				'theme_options_default' => true,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'container-type', 
				'theme_options'         => true, 
				'theme_options_default' => 'container-fluid',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'colorset', 
				'theme_options'         => true, 
				'theme_options_default' => '',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			// Removed on 1.1 ver., we don't need it
			// $config[] = array( 
			// 	'id'                    => 'transparentfilm', 
			// 	'theme_options'         => true, 
			// 	'theme_options_default' => false,
			// 	'metabox'               => true,
			// 	'metabox_default'       => NULL
			// );
			$config[] = array( 
				'id'                    => 'layout', 
				'theme_options'         => true, 
				'theme_options_default' => 5,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1-section', 
				'theme_options'         => true, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1', 
				'theme_options'         => true, 
				'theme_options_default' => 'text',
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1-metabox', 
				'theme_options'         => false, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1-menu', 
				'theme_options'         => true, 
				'theme_options_default' => 'footerbar',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1-text', 
				'theme_options'         => true, 
				'theme_options_default' => esc_html__('Copyright 2016 Hotel Xenia', 'hotel-xenia'),
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1-customtext', 
				'theme_options'         => false, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1-align', 
				'theme_options'         => true, 
				'theme_options_default' => 'text-left',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1-visibility', 
				'theme_options'         => true, 
				'theme_options_default' => 'hidden-xs hidden-sm',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col1-extraclass', 
				'theme_options'         => true, 
				'theme_options_default' => '',
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2-section', 
				'theme_options'         => true, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2', 
				'theme_options'         => true, 
				'theme_options_default' => 'text',
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2-metabox', 
				'theme_options'         => false, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2-menu', 
				'theme_options'         => true, 
				'theme_options_default' => 'footerbar',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2-text', 
				'theme_options'         => true, 
				'theme_options_default' => '<div class="award"><img src="'.PLE_THEME_ASSETS_URI.'/images/award.png" alt="Image" class="center-block" width="120"></div>',
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2-customtext', 
				'theme_options'         => false, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2-align', 
				'theme_options'         => true, 
				'theme_options_default' => 'text-center',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2-visibility', 
				'theme_options'         => true, 
				'theme_options_default' => 'hidden-xs hidden-sm',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col2-extraclass', 
				'theme_options'         => true, 
				'theme_options_default' => '',
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3-section', 
				'theme_options'         => true, 
				'theme_options_default' => '',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3', 
				'theme_options'         => true, 
				'theme_options_default' => 'text',
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3-metabox', 
				'theme_options'         => false, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3-menu', 
				'theme_options'         => true, 
				'theme_options_default' => 'footerbar',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3-text', 
				'theme_options'         => true, 
				'theme_options_default' => '<p>'. esc_html__('Crafted by ', 'hotel-xenia') .' <a href="'. esc_url( 'http://themeforest.net/user/andrewchs/portfolio' ) .'" target="_blank">AndrewChs</a>' .' | '. esc_html__('Developed by ', 'hotel-xenia') .' <a href="'. esc_url( 'http://plethorathemes.com' ) .'" target="_blank">Plethora Themes</a>' .'</p>',
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3-customtext', 
				'theme_options'         => false, 
				'theme_options_default' => NULL,
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3-align', 
				'theme_options'         => true, 
				'theme_options_default' => 'text-right',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3-visibility', 
				'theme_options'         => true, 
				'theme_options_default' => 'hidden-xs hidden-sm',
				'metabox'               => true,
				'metabox_default'       => NULL
			);
			$config[] = array( 
				'id'                    => 'col3-extraclass', 
				'theme_options'         => true, 
				'theme_options_default' => '',
				'metabox'               => false,
				'metabox_default'       => NULL
			);
			return $config;
		}

	}
}