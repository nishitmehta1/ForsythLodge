<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M			          (c) 2015

File Description: System Info Dashboard Widget

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Dashboard') && !class_exists('Plethora_Dashboard_Systeminfo')):

	/**
	 * @package Plethora Base
	 */

	class Plethora_Dashboard_Systeminfo extends Plethora_Dashboard { 

        // Feature display title  (string)
        public static $feature_title        = "Plethora System Info";
        // Feature display description (string)
        public static $feature_description  = "Displays system information about this WP installation on the dashboard";
        // Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_control = true;
        // Default activation option status ( boolean )
        public static $theme_option_default       = true;
        // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $theme_option_requires             = array();
        // Dynamic class construction ? ( boolean )
        public static $dynamic_construct       = true;
        // Additional method invocation ( string/boolean | method name or false )
        public static $dynamic_method           = false;

    	function __construct() {

	        if (current_user_can("manage_options") && is_admin() ) { // Only for users that can manage options 
	          // Add Dashboard Widget
	          $this->add('system_info', "Plethora System Info");

	        }
    	 }

       /** 
       * Returns shortcode content
       *
       * @return array
       * @since 1.0
       *
       */
       public function callback() {

          $nonce  = wp_create_nonce("system_info_nonce");

          $form  = '';
          $form .= '<form id="system_info">';
          $form .= '    <div class="textarea-wrap" id="description-wrap">';
          $form .= '      <textarea id="system_info_text" rows="30" cols="15" readonly="readonly">'. esc_textarea( $this->get_system_info() ) .'</textarea>';
          $form .= '    </div>';
          $form .= '    <p>';
          $form .= '      <button  type="button"  class="button button-primary system_info_button"  onclick="document.getElementById(\'system_info_text\').select(); " >Select All</button>';
          $form .= '      <br class="clear">';
          $form .= '    </p>';
          $form .= '</form>';

          echo $form;

       }

      public function get_system_info() { 

        if ( class_exists( 'Plethora_System') ) { 

          $sys_info = Plethora_System::all_info();
          $return   = '### PLETHORA THEME INSTALLATION INFO ###'. "\n\n";
          $return  .= '--- WEBSITE INFO ---' . "\n";
          $return  .= 'Site URL: ' . $sys_info['url_site'] . "\n";
          $return  .= 'Home URL: ' . $sys_info['url_home'] . "\n";
          $return  .= 'Multisite: ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";
          $return  .= "\n" . '--- WORDPRESS INSTALLATION ---' . "\n";
          $return  .= 'WP Version: ' .  $sys_info['wp_version'] . "\n";
          $return  .= 'Permalink Structure: ' . $sys_info['wp_permalink_structure'] . "\n";
          $return  .= 'Parent Theme: ' . THEME_DISPLAYNAME .' '. THEME_VERSION . "\n";
          $return  .= 'Active Theme: ' . $sys_info['theme'] . "\n";
          $return  .= 'Show On Front: ' . $sys_info['wp_show_on_front'] . "\n";
          $return  .= 'Page On Front: ' . $sys_info['wp_page_on_front'] . "\n";
          $return  .= 'Page For Posts: ' . $sys_info['wp_page_for_posts'] . "\n";
          $return  .= 'Remote Post: ' . $sys_info['wp_remote_post'] . "\n";
          $return  .= 'Table Prefix: ' . $sys_info['wp_table_prefix_length'] . "\n";
          $return  .= 'WP_DEBUG: ' . $sys_info['wp_debug'] . "\n";
          $return  .= 'WP_MEMORY_LIMIT: ' . $sys_info['wp_memory'] . "\n";
          $return  .= 'WP_MAX_MEMORY_LIMIT: ' . $sys_info['wp_max_memory'] . "\n";
          $return  .= "\n" . '--- WORDPRESS ACTIVE PLUGINS  ---' . "\n";
          $return  .= $sys_info['plugins_active'];
          $return  .= "\n" . '--- WORDPRESS INACTIVE PLUGINS  ---' . "\n";
          $return  .= $sys_info['plugins_inactive'];
          if ( !empty($sys_info['plugins_multi_active'])) { 
          $return  .= "\n" . '--- WP NETWORK ACTIVE PLUGINS  ---' . "\n";
          $return  .= $sys_info['plugins_multi_active'];
          }
          if ( !empty($sys_info['plugins_multi_inactive'])) { 
          $return  .= "\n" . '--- WP NETWORK INACTIVE PLUGINS  ---' . "\n";
          $return  .= $sys_info['plugins_multi_inactive'];
          }
          $return  .= "\n" . '--- SERVER SETUP ---' . "\n";
          $return  .= 'PHP Version: ' . $sys_info['webserver_php_version'] . "\n";
          $return  .= 'MySQL Version: ' .  $sys_info['webserver_mysql_version'] . "\n";
          $return  .= 'Webserver: ' . $sys_info['webserver_server_software'] . "\n";
          $return  .= "\n" . '--- PHP CONFIGURATION ---' . "\n";
          $return  .= 'Safe Mode: ' . $sys_info['php_config_safe_mode'] . "\n";
          $return  .= 'Memory Limit: ' . $sys_info['php_config_memory_limit'] ."\n";
          $return  .= 'Upload Max Size: ' . $sys_info['php_config_post_max_size'] . "\n";
          $return  .= 'Upload Max Filesize: ' . $sys_info['php_config_upload_max_filesize'] . "\n";
          $return  .= 'Time Limit: ' . $sys_info['php_config_max_execution_time'] . "\n";
          $return  .= 'Max Input Vars: ' . $sys_info['php_config_max_input_vars'] . "\n";
          $return  .= 'Display Errors: ';
          $return  .=  $sys_info['php_config_display_errors'] ? 'True' : 'False';
          $return  .=  "\n";
          $return  .= "\n" . '--- PHP EXTENSIONS ---' . "\n";
          $return  .= 'cURL: ' . $sys_info['php_extension_curl']  . "\n";
          $return  .= 'fsockopen: ' . $sys_info['php_extension_fsockopen'] . "\n";
          $return  .= 'SOAP Client: ' . $sys_info['php_extension_soapclient'] . "\n";
          $return  .= 'Suhosin: ' . $sys_info['php_extension_suhosin'] . "\n";
          if ( isset( $_SESSION ) ) { 
          $return  .= "\n" . '--- PHP SESSION CONFIGURATION ---' . "\n";
          $return  .= 'Session: ' . $sys_info['php_session'] . "\n";
          $return  .= 'Session Name: ' . $sys_info['php_session_name'] . "\n";
          $return  .= 'Cookie Path: ' . $sys_info['php_session_cookie_path'] . "\n";
          $return  .= 'Save Path: ' . $sys_info['php_session_save_path'] . "\n";
          $return  .= 'Use Cookies: ' . $sys_info['php_session_use_cookies'] . "\n";
          $return  .= 'Use Only Cookies: ' . $sys_info['php_session_use_only_cookies'] . "\n";
          }
          $return  .= "\n" . '--- BROWSER INFO ---' . "\n";
          $return  .= $sys_info['browser'] . "\n";
          return $return;
        }
      }
	}
	
 endif;