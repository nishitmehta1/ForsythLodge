<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Sidebars Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Sidebars') && !class_exists('Plethora_Module_Sidebars_Ext') ) {

  /**
   * Extend base class
   * Base class file: /includes/core/features/module/sidebars/module-sidebars.php
   */
  class Plethora_Module_Sidebars_Ext extends Plethora_Module_Sidebars { 

	      /**
	       * Set default sidebars 
	       * @since 1.0
	       *
	       */
	    public function default_sidebars() {


	   		// echo '<div align="center">FRAMEWORK!</div>' . get_called_class();
			// Execute this only on first page load
			$default_sidebars = array();
			// IMPORTANT: this is necessary for repeater field...add a line for each sidebar record
			$default_sidebars['redux_repeater_data'] = array(
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			                     array( 'title'=> '' ),
			               );
			$default_sidebars['sidebar_name'] = array(
			                  esc_html__('Blog Sidebar', 'hotel-xenia'),
			                  esc_html__('Pages Sidebar', 'hotel-xenia'),
			                  esc_html__('Rooms Sidebar', 'hotel-xenia'),
			                  esc_html__('Services Sidebar', 'hotel-xenia'),
			                  esc_html__('Mobile View Sidebar', 'hotel-xenia'),
			                  esc_html__('Shop Sidebar', 'hotel-xenia'),
			                  esc_html__('Events Calendar Sidebar', 'hotel-xenia'),
			                  esc_html__('Footer Widgets Area 1-1', 'hotel-xenia'),
			                  esc_html__('Footer Widgets Area 1-2', 'hotel-xenia'),
			                  esc_html__('Footer Widgets Area 1-3', 'hotel-xenia'),
			                  esc_html__('Footer Widgets Area 1-4', 'hotel-xenia'),
			                  esc_html__('Footer Widgets Area 2-1', 'hotel-xenia'),
			                  esc_html__('Footer Widgets Area 2-2', 'hotel-xenia'),
			                  esc_html__('Footer Widgets Area 2-3', 'hotel-xenia'),
			                  esc_html__('Footer Widgets Area 2-4', 'hotel-xenia'),
			                );
			$default_sidebars['sidebar_desc'] = array(
			                  esc_html__('Default sidebar to add widgets on blog archives & posts', 'hotel-xenia'),
			                  esc_html__('Default sidebar to add widgets on single pages', 'hotel-xenia'),
			                  esc_html__('Default sidebar to add widgets on single rooms', 'hotel-xenia'),
			                  esc_html__('Default sidebar to add widgets on single services', 'hotel-xenia'),
			                  esc_html__('Default sidebar to add widgets on mobile menu section', 'hotel-xenia'),
			                  esc_html__('Default sidebar to add widgets on shop pages', 'hotel-xenia'),
			                  esc_html__('Default sidebar to add widgets on the Event Calendar plugin views', 'hotel-xenia'),
			                  esc_html__('Footer widgets area 1-1', 'hotel-xenia'),
			                  esc_html__('Footer widgets area 1-2', 'hotel-xenia'),
			                  esc_html__('Footer widgets area 1-3', 'hotel-xenia'),
			                  esc_html__('Footer widgets area 1-4', 'hotel-xenia'),
			                  esc_html__('Footer widgets area 2-1', 'hotel-xenia'),
			                  esc_html__('Footer widgets area 2-2', 'hotel-xenia'),
			                  esc_html__('Footer widgets area 2-3', 'hotel-xenia'),
			                  esc_html__('Footer widgets area 2-4', 'hotel-xenia'),
			                );
			$default_sidebars['sidebar_slug'] = array(
                          	  'sidebar-default',
                          	  'sidebar-pages',
                          	  'sidebar-rooms',
                          	  'sidebar-services',
                          	  'sidebar-mobile',
                          	  'sidebar-shop',
                          	  'sidebar-eventscalendar',
			                  'sidebar-footer-1-1',
			                  'sidebar-footer-1-2',
			                  'sidebar-footer-1-3',
			                  'sidebar-footer-1-4',
			                  'sidebar-footer-2-1',
			                  'sidebar-footer-2-2',
			                  'sidebar-footer-2-3',
			                  'sidebar-footer-2-4'
			                );
	        $default_sidebars['sidebar_class'] = array( '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' );
	        return $default_sidebars;
	    }

  }
}