<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: System Info Tab module

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Systeminfo') ) {

	/**
	 */
	class Plethora_Module_Systeminfo {


		// Feature display title  (string)
		public static $feature_title        = "System Info Module";
		// Feature display description (string)
		public static $feature_description  = "System Info Tab";
		// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_control = true;
		// Default activation option status ( boolean )
		public static $theme_option_default	= true;
		// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $theme_option_requires= array();
		// Dynamic class construction ? ( boolean )
		public static $dynamic_construct	= true;
		// Additional method invocation ( string/boolean | method name or false )
		public static $dynamic_method		= false;

		// public static $envato_itemid = '7712236';


		function __construct(){
			
			// Extremely important...load it only when on the theme options page OR IF ajax is working, otherwise JS errors will be produced on other admin pages
			global $pagenow ;    
			if ( ( is_admin() && $pagenow === 'admin.php' && isset($_REQUEST['page']) && $_REQUEST['page'] === THEME_OPTIONSPAGE ) || defined('DOING_AJAX') && DOING_AJAX  ) {

				add_filter( 'plethora_themeoptions_help', array( $this, 'theme_options_systeminfo_section'), 100);
			}
		}

/////// SYSTEM INFO SECTION -----> START
        /**
         * Sets SYSTEM INFO section on theme tabs
         * @return array
         */
		static function theme_options_systeminfo_section( $sections ) { 

			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('System Info', 'plethora-framework'),
				'heading'      => esc_html__('SYSTEM INFORMATION', 'plethora-framework'),
				'desc'       => self::get_system_info() ,
				'class'       => 'plethora_sysinfo' ,

				);

			return $sections;
		}

		static function get_system_info( $email_ver = false ) { 

			if ( class_exists( 'Plethora_System') ) { 

			  $sys_info = Plethora_System::all_info();

			  $return = '';
			  $return .= self::table_open();
			  $return .= self::table_head( 'WEBSITE INFO ' );
			  $return .= self::table_row( 'Site URL', $sys_info['url_site'], true );
			  $return .= self::table_row( 'Home URL', $sys_info['url_home'] );
			  $multisite = is_multisite() ? 'Yes' : 'No';
			  $return .= self::table_row( 'Multisite', $multisite, true );
			  $return .= self::table_head( 'WORDPRESS INSTALLATION' );
			  $return .= self::table_row( 'WP Version', $sys_info['wp_version'], true );
			  $return .= self::table_row( 'Permalink Structure', $sys_info['wp_permalink_structure'] );
			  $return .= self::table_row( 'Parent Theme', THEME_DISPLAYNAME .' '. THEME_VERSION , true);
			  $return .= self::table_row( 'Active Theme', $sys_info['theme'] );
			  $return .= self::table_row( 'Show On Front', $sys_info['wp_show_on_front'], true );
			  $return .= self::table_row( 'Page On Front', $sys_info['wp_page_on_front'] );
			  $return .= self::table_row( 'Page For Posts', $sys_info['wp_page_for_posts'], true );
			  // $return .= self::table_row( 'Remote Post', $sys_info['wp_remote_post'] );
			  $return .= self::table_row( 'Table Prefix', $sys_info['wp_table_prefix_length'], true );
			  if ( $sys_info['wp_debug'] == 'Enabled' ) { $sys_info['wp_debug'] = $sys_info['wp_debug'] . '. <span style="color:red">If development period has finished and this is your normal production installation, you should disable WP_DEBUG !</span>'; }
			  $return .= self::table_row( 'WP_DEBUG', $sys_info['wp_debug'] );
			  $return .= self::table_row( 'WP_MEMORY_LIMIT', $sys_info['wp_memory'], true );
			  $return .= self::table_row( 'WP_MAX_MEMORY_LIMIT', $sys_info['wp_max_memory'] );
			  $return .= self::table_head( 'WORDPRESS PLUGINS' );
			  $return .= self::table_row( 'Active plugins', self::fix_array( $sys_info['plugins_active'] ), true );
			  $return .= self::table_row( 'Inactive plugins', self::fix_array( $sys_info['plugins_inactive'] ) );
			  if ( !empty($sys_info['plugins_multi_active'])) { 
			  $return .= self::table_row( 'Network active plugins', self::fix_array( $sys_info['plugins_multi_active'] ), true );
			  $return .= self::table_row( 'Network inactive plugins', self::fix_array( $sys_info['plugins_multi_inactive'] ) );
			  }
			  $return .= self::table_head( 'SERVER SETUP' );
			  $return .= self::table_row( 'PHP Version', $sys_info['webserver_php_version'], true );
			  $return .= self::table_row( 'MySQL Version', $sys_info['webserver_mysql_version'] );
			  $return .= self::table_row( 'Webserver', $sys_info['webserver_server_software'], true );
			  $return .= self::table_head( 'PHP CONFIGURATION' );
			  $return .= self::table_row( 'Safe Mode', $sys_info['php_config_safe_mode'], true );
			  $return .= self::table_row( 'Memory Limit', $sys_info['php_config_memory_limit'] );
			  $return .= self::table_row( 'Upload Max Size', $sys_info['php_config_post_max_size'], true );
			  $return .= self::table_row( 'Upload Max Filesize', $sys_info['php_config_upload_max_filesize'] );
			  $return .= self::table_row( 'Time Limit', $sys_info['php_config_max_execution_time'], true );
			  $return .= self::table_row( 'Max Input Vars', $sys_info['php_config_max_input_vars'] );
			  $display_errors = $sys_info['php_config_display_errors'] ? 'True' : 'False';
			  $return .= self::table_row( 'Display Errors', $display_errors, true );
			  $return .= self::table_head( 'PHP EXTENSIONS' );
			  $return .= self::table_row( 'cURL', $sys_info['php_extension_curl'], true );
			  $return .= self::table_row( 'fsockopen', $sys_info['php_extension_fsockopen'] );
			  $return .= self::table_row( 'SOAP Client', $sys_info['php_extension_soapclient'], true );
			  $return .= self::table_row( 'Suhosin', $sys_info['php_extension_suhosin'] );
			  if ( isset( $_SESSION ) ) { 
			  $return .= self::table_head( 'PHP SESSION CONFIGURATION' );
			  $return .= self::table_row( 'Session', $sys_info['php_session'], true );
			  $return .= self::table_row( 'Session Name', $sys_info['php_session_name'] );
			  $return .= self::table_row( 'Cookie Path:', $sys_info['php_session_cookie_path'], true );
			  $return .= self::table_row( 'Save Path', $sys_info['php_session_save_path'] );
			  $return .= self::table_row( 'Use Cookies', $sys_info['php_session_use_cookies'], true );
			  $return .= self::table_row( 'Use Only Cookies', $sys_info['php_session_use_only_cookies'] );
			  }
			  $return .= self::table_head( 'BROWSER INFO' );
			  $return .= self::table_row( 'Browser', $sys_info['browser'], true );
			  $return .= self::table_head( 'CONSTANTS' );
			  $return .= self::table_row( 'THEME_SLUG', THEME_SLUG, true );
			  $return .= self::table_row( 'THEME_OPTVAR', THEME_OPTVAR, true );
			  $return .= self::table_row( 'WP_CONTENT_DIR', WP_CONTENT_DIR, true );
			  $return .= self::table_row( 'WP_CONTENT_URL', WP_CONTENT_URL, true );
			  $return .= self::table_row( 'WP_PLUGIN_DIR', WP_PLUGIN_DIR, true );
			  $return .= self::table_row( 'WP_PLUGIN_URL', WP_PLUGIN_URL, true );
			  $return .= self::table_row( 'TEMPLATEPATH', TEMPLATEPATH, true );
			  $status = file_exists( PLE_CORE_DIR ) ? ' <span style="color:green"> ( directory location is valid ) </span>' : ' <span style="color:red"> ( directory location is NOT valid ) </span>';
			  $return .= self::table_row( 'PLE_CORE_URI', PLE_CORE_URI, true );
			  $return .= self::table_row( 'PLE_CORE_DIR', PLE_CORE_DIR . $status, true );
			  $return .= self::table_row( 'PLE_THEME_URI', PLE_THEME_URI, true );
			  $status = file_exists( PLE_THEME_DIR ) ? ' <span style="color:green"> ( directory location is valid ) </span>' : ' <span style="color:red"> ( directory location is NOT valid ) </span>';
			  $return .= self::table_row( 'PLE_THEME_DIR', PLE_THEME_DIR . $status, true );
      		  if ( Plethora_Theme::is_library_active() ) { // this makes it easier to track issues!
				  $return .= self::table_row( 'PLE_FLIB_URI', PLE_FLIB_URI, true );
				  $status = file_exists( PLE_FLIB_DIR ) ? ' <span style="color:green"> ( directory location is valid ) </span>' : ' <span style="color:red"> ( directory location is NOT valid ) </span>';
				  $return .= self::table_row( 'PLE_FLIB_DIR', PLE_FLIB_DIR . $status, true );
				  $status = file_exists( PLE_FLIB_LIBS_DIR ) ? ' <span style="color:green"> ( directory location is valid ) </span>' : ' <span style="color:red"> ( directory location is NOT valid ) </span>';
				  $return .= self::table_row( 'PLE_FLIB_LIBS_DIR', PLE_FLIB_LIBS_DIR . $status, true );
			  }
			  $return .= self::table_head( 'OTHER INFO & DIAGNOSTICS' );
			  $return .= self::table_row( 'Uploads Directory Status', self::diagnostics_uploads_is_writable(), true );
			  $return .= self::table_row( 'Memory Issues', self::diagnostics_memory(), true );
			  $return .= self::table_close();
			  return $return;
			}
		}

		static function diagnostics_memory() { 

			$mem_notice = '<span style="color:darkgreen">'. esc_html__('No issues detected', 'plethora-framework') . '</span>';
			$php_version	 = Plethora_System::webserver('php_version');
			$php_memorylimit = @get_cfg_var('memory_limit') != false ? @get_cfg_var('memory_limit') : 0;
			$php_memorylimit = self::ram_in_megabytes( $php_memorylimit );
			if ( $php_memorylimit < 96 ) { 
      			
       			$mem_notice = esc_html__('LESS compiling functionality usualy demands 40-80MBs of RAM. This might get even bigger, especially if you have installed several memory consuming plugins OR you have a PHP version earlier than 5.3 OR your website has a lot of traffic!', 'plethora-framework');
      			$mem_notice .= '<br>';
       			$mem_notice .= esc_html__('Your installation has ', 'plethora-framework');
       			$mem_notice .=  '<b>PHP ver. '. $php_version .'</b> and ';
       			$mem_notice .=  '<b>'. $php_memorylimit .'MB RAM</b>  ( php.ini value ) available. ';
     			if ( $php_memorylimit < 96 ) { $mem_notice_part = esc_html__('Memory is ok, nevertheless, raising this limit to a minimum 128MB would be safer*. ', 'plethora-framework'); $style = ''; }
      			if ( $php_memorylimit < 85 ) { $mem_notice_part = esc_html__('Memory is close to MAX threshold. You might need to raise this limit*, especially if you plan to use several memory consuming plugins ( such as WooCommerce ). ', 'plethora-framework'); $style = 'brown'; }
      			if ( $php_memorylimit < 80 ) { $mem_notice_part = esc_html__('Memory is below the MAX threshold. You probably have to raise this limit*, especially if you plan to use several memory consuming plugins ( such as WooCommerce ). ', 'plethora-framework'); $style = 'brown'; }
      			if ( $php_memorylimit <= 60 ) { $mem_notice_part = esc_html__('Memory is below the MAX threshold. You <b>MUST raise this limit*</b>. ', 'plethora-framework'); $style = 'red'; }
      			if ( $php_memorylimit <= 40 ) { $mem_notice_part = esc_html__('Memory is below the MIN threshold. Even WordPress itself might have performance issues. You <b>DEFINITELY MUST raise this limit*</b>. ', 'plethora-framework'); $style = 'red'; }
      			$mem_notice .= $mem_notice_part;
      			$mem_notice .= esc_html__('Insufficient memory resources can cause a fatal error on the frontend during the custom stylesheet compiling. ', 'plethora-framework');
      			$mem_notice = '<span style="color:'.$style.'">'. $mem_notice .'</span>';
      			$mem_notice .= '<br>';
      			$mem_notice .= esc_html__('* If you want to raise your memory limit, its always best to ask your hosting provider to do it for you. ', 'plethora-framework');
      			$mem_notice .= esc_html__('If you have the technical skills to do it yourself, make sure that the memory limit value set is actualy available to your hosting package. ', 'plethora-framework');
      			$mem_notice .= esc_html__('Raising memory from 128MB to 256MB is safe for an average website. Plethora Themes always suggests the latter.', 'plethora-framework');
			}
			return $mem_notice;
		}

		static function diagnostics_uploads_is_writable() { 

			$upload_dir = wp_upload_dir();

			if (wp_is_writable( $upload_dir['basedir'] )){

			    return '<span style="color:darkgreen">'. esc_html__('Uploads directory is writable', 'plethora-framework') .'</style>';
			}
			return '<span style="color:red">'. esc_html__('Uploads directory is NOT writable. Several functionalities are not working as expected!', 'plethora-framework') .'</span>';
		}


/////// SYSTEM INFO SECTION <----- FINISH

/////// HELPERS ----> START

        static function ram_in_megabytes( $value ) {

			if (preg_match('/^(\d+)(.)$/', $value, $matches)) {
			    if ($matches[2] == 'M') {
			        $value = $matches[1]; // nnnM -> nnn MB
			    } else if ($matches[2] == 'K') {
			        $value = $matches[1] / 1024; // nnnK -> nnn KB
			        $value = @floor($value);
			    }
			}
			return $value;
        }

		static function table_open(){ 

		  $return = '<table width="100%" border="0" cellspacing="0" cellpadding="4" id="ticket_form">';
		  return $return;
		}

		static function table_close(){ 

		  $return = '</table>';
		  return $return;
		}

		static function table_head( $title, $text = ''){ 
		  $title = !empty($title) ? '<h3>'. $title .'</h3>' : '';
		  $text = !empty($text) ? '<p>'. $text .'</p>' : '';
		  $return = '<tr><td colspan="2">'. $title . $text .'</td></tr>';
		  return $return;
		}
		static function table_row( $title, $val, $col = false ){ 
			  $col = ( $col == true ) ? '#F3F3F3' : 'none';
		  $return = '<tr style="min-height:60px;"><td valign="top" style="background-color:'. $col .'"><b>'. $title .'</b></td><td valign="middle" style="background-color:'. $col .'">'.$val.'</td></tr>';
		  return $return;
		}

		static function table_search_row(){ 
		  $return = '<tr><td valign="top" style="width:20%;"></td><td valign="top" id="plethora_search" style="margin-top:20px; width:80%; height:250px !important"></td></tr>';
		  return $return;
		}

		static function fix_array( $array ){ 

		  $return = '';
		  foreach ( $array as $key => $val ) { 
		  	$return .= $val .'<br>';
		  }
		  return $return;
      	}
	}
/////// HELPERS <---- FINISH

}