<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Visual Composer Configuration

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Vc') && !class_exists('Plethora_Module_Vc_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/module-vc.php
   */
  class Plethora_Module_Vc_Ext extends Plethora_Module_Vc { 


      /**
       * Set post types where the VC editor is enabled by default
       * Used this override to add VC editor on single room and service posts
       */
      public function set_default_editor_post_types() {

        if ( function_exists( 'vc_set_default_editor_post_types') ) {

            vc_set_default_editor_post_types( array( 'page', 'post', 'project', 'room', 'service' ) ); 
        }
      }
  }
}