<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2014

Description: Installation system info class
Version: 1.0

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


	/**
	 * Installation system info class
	 * 
	 * @package Plethora Framework
	 * @version 1.2
	 * @author Plethora Dev Team
	 * @copyright Plethora Themes (c) 2014
	 *
	 */
	class Plethora_System {


		/**
		 * Returns all system info in a single array
		 * 
		 */
		static function all_info() { 

			if ( ! is_admin() ) { return null; }
			
			$sys_info = array();
			// Theme version & child info
			$sys_info['theme']							= self::theme();
			// URL info
			$sys_info['url_site']						= self::url('site');
			$sys_info['url_home']						= self::url('home');
			// WordPress settings info
			$sys_info['wp_version']						= self::wp('version');
			$sys_info['wp_permalink_structure']			= self::wp('permalink_structure');
			$sys_info['wp_show_on_front']				= self::wp('show_on_front');
			$sys_info['wp_page_on_front']				= self::wp('page_on_front');
			$sys_info['wp_page_for_posts']				= self::wp('page_for_posts');
			// $sys_info['wp_remote_post']					= self::wp('remote_post');
			$sys_info['wp_table_prefix_length']			= self::wp('table_prefix_length');
			$sys_info['wp_debug']						= self::wp('debug');
			$sys_info['wp_memory']						= self::wp('memory');
			$sys_info['wp_max_memory']					= defined('WP_MAX_MEMORY_LIMIT') ? WP_MAX_MEMORY_LIMIT : 'Not defined!';
			// WordPress plugins info
			$sys_info['plugins_active']					= self::plugins('active');
			$sys_info['plugins_inactive']				= self::plugins('inactive');
			$sys_info['plugins_multi_active']			= self::plugins('multi_active');
			$sys_info['plugins_multi_inactive']			= self::plugins('multi_inactive');
			// Webserver info
			$sys_info['webserver_php_version']			= self::webserver('php_version');
			$sys_info['webserver_mysql_version']		= self::webserver('mysql_version');
			$sys_info['webserver_server_software']		= self::webserver('server_software');
			// PHP Configuration info
			$sys_info['php_config_safe_mode']			= self::php_config('safe_mode');
			$sys_info['php_config_memory_limit']		= @get_cfg_var('memory_limit') != false ? @get_cfg_var('memory_limit') : 'N/A';
			$sys_info['php_config_upload_max_filesize']	= self::php_config('upload_max_filesize');
			$sys_info['php_config_post_max_size']		= self::php_config('post_max_size');
			$sys_info['php_config_max_execution_time']	= self::php_config('max_execution_time');
			$sys_info['php_config_max_input_vars']		= self::php_config('max_input_vars');
			$sys_info['php_config_display_errors']		= self::php_config('display_errors');
			// PHP extensions info
			$sys_info['php_extension_curl']				= self::php_extension('curl');
			$sys_info['php_extension_fsockopen']		= self::php_extension('fsockopen');
			$sys_info['php_extension_soapclient']		= self::php_extension('soapclient');
			$sys_info['php_extension_suhosin']			= self::php_extension('suhosin');
			// PHP session info
			$sys_info['php_session']					= self::php_session('session');
			$sys_info['php_session_name']				= self::php_session('name');
			$sys_info['php_session_cookie_path']		= self::php_session('cookie_path');
			$sys_info['php_session_save_path']			= self::php_session('save_path');
			$sys_info['php_session_use_cookies']		= self::php_session('use_cookies');
			$sys_info['php_session_use_only_cookies']	= self::php_session('use_only_cookies');
			// Browser info
			$sys_info['browser']						= self::browser();

			return $sys_info;
		}		

		/**
		 * Returns theme name, version and if child theme is activated
		 * 
		 */
		static function theme() { 

			if ( ! is_admin() ) { return null; }

            $theme_data     = wp_get_theme();
            $theme          = $theme_data->Name . ' ' . $theme_data->Version;
            $is_child       = is_child_theme() ? ' (child theme)' : '';
            return $theme;

		}

		/**
		 * Returns various url information
	 	 * @param $type ( 'site', 'home' )
		 */
		static function url( $type = '' ) {

			if ( ! is_admin() ) { return null; }

			if ( $type == 'site' || empty($type) ) { 

				return site_url();
			
			} else if ( $type == 'home' ) { 

				return home_url();

			}
		}		

		/**
		 * Returns various WordPress installation information
	 	 * @param $type ( 'version', 'permalink_structure', 'show_on_front', 'page_on_front', 'page_for_posts', 'remote_post', 'table_prefix_length', 'debug', 'memory' )
		 */
		static function wp( $type = '' ) {

			if ( ! is_admin() ) { return null; }

			if ( $type == 'version' || empty($type) ) { 

				return get_bloginfo( 'version' );
			
			} else if ( $type == 'permalink_structure' ) { 

				return ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' );

			} else if ( $type == 'show_on_front' ) { 

				return get_option( 'show_on_front' );

			} else if ( $type == 'page_on_front' ) { 

				$front_page_id = get_option( 'page_on_front' );
				return ( $front_page_id != 0 ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' );

			} else if ( $type == 'page_for_posts' ) { 

				$blog_page_id = get_option( 'page_for_posts' );
				return ( $blog_page_id != 0 ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' );

			} else if ( $type == 'remote_post' ) { 

				if ( ! defined( 'SSINFO_VERSION' ) ) {

                    define( 'SSINFO_VERSION', '1.0.0' );

                }

               // Make sure wp_remote_post() is working
                $request['cmd'] = '_notify-validate';

                $params = array(
                    'sslverify'  => false,
                    'timeout'    => 60,
                    'user-agent' => 'SSInfo/' . SSINFO_VERSION,
                    'body'       => $request
                );

                $response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

                if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                    $WP_REMOTE_POST = 'wp_remote_post() works';
                } else {
                    $WP_REMOTE_POST = 'wp_remote_post() does not work';
                }

                return $WP_REMOTE_POST;

			} else if ( $type == 'table_prefix_length' ) { 

                global $wpdb;
				return strlen( $wpdb->prefix ) . ' ( ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: over 16 chars...too long!' : 'acceptable' ) . " )";

			} else if ( $type == 'debug' ) { 

				return ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' );

			} else if ( $type == 'memory' ) { 

				return ( defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : 'Not set' ); ;

			} 

		}

		/**
		 * Returns information about plugins installed ( in array )
	 	 * @param $type ( 'active', 'inactive', 'multi_active', 'multi_inactive' )
		 * 
		 */
		static function plugins( $type = '' ) {

			if ( ! is_admin() ) { return null; }

			if ( $type == 'active' || empty($type) ) { 

                $plugins        = self::get_plugins();
                $active_plugins = get_option( 'active_plugins', array() );
                $return_plugins = array();

                foreach ( $plugins as $plugin_path => $plugin ) {
                    if ( ! in_array( $plugin_path, $active_plugins ) ) {
                        continue;
                    }

                    $return_plugins[] = $plugin['Name'] . ': ' . $plugin['Version'];
                }

                return $return_plugins;
			
			} else if ( $type == 'inactive' ) { 

                $plugins        = self::get_plugins();
                $active_plugins = get_option( 'active_plugins', array() );
                $return_plugins = array();

                foreach ( $plugins as $plugin_path => $plugin ) {
                    if ( in_array( $plugin_path, $active_plugins ) ) {
                        continue;
                    }

                    $return_plugins[] = $plugin['Name'] . ': ' . $plugin['Version'];
                }

                return $return_plugins;

			} else if ( $type == 'multi_active' ) {

				if ( is_multisite() ) { 

                	$plugins        = function_exists('wp_get_active_network_plugins') ? wp_get_active_network_plugins() : array();
                    $active_plugins = get_site_option( 'active_sitewide_plugins', array() );
                	$return_plugins = array();

                    foreach ( $plugins as $plugin_path => $plugin ) {
                        $plugin_base = plugin_basename( $plugin_path );

                        if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
                            continue;
                        }

                        $return_plugins[] = $plugin['Name'] . ': ' . $plugin['Version'];
                    }

                	return $return_plugins;

				} else { 

					return null; 

				}

			} else if ( $type == 'multi_inactive' ) {

				if ( is_multisite() ) { 

                	$plugins        = function_exists('wp_get_active_network_plugins') ? wp_get_active_network_plugins() : array();
                    $active_plugins = get_site_option( 'active_sitewide_plugins', array() );
                	$return_plugins = array();
                    
                    foreach ( $plugins as $plugin_path ) {
                        $plugin_base = plugin_basename( $plugin_path );

                        if ( array_key_exists( $plugin_base, $active_plugins ) ) {
                            continue;
                        }

                        $return_plugins[] = $plugin['Name'] . ': ' . $plugin['Version'];
                    }

                	return $return_plugins;

				} else { 

					return null; 

				}
			}			
		}

		static function get_plugins(){

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$all_plugins = get_plugins();

			return $all_plugins;
		}

		/**
		 * Returns information about the webserver 
	 	 * @param $type ( 'php_version', 'mysql_version', 'server_software' )
		 * 
		 */
		static function webserver( $type = '' ) {

			if ( ! is_admin() ) { return null; }

			if ( $type == 'php_version' || empty($type) ) { 

				return PHP_VERSION;

			} else if ( $type == 'mysql_version') { 

                if ( function_exists( 'mysqli_get_client_version' ) ) {

                    return mysqli_get_client_version(); 

                } else { 

                	return null;
                }

			} else if ( $type == 'server_software') { 

				return $_SERVER['SERVER_SOFTWARE'];
			}
		}

		/**
		 * Returns any PHP configuration, given the correct config variable
		 * 
		 */
		static function php_config( $type ) {

			if ( ! is_admin() ) { return null; }

			if ( $type == 'safe_mode') {

			 	return ini_get( 'safe_mode' ) ? 'Yes' : 'No';

			} else {

				return ini_get( $type ); 
			
			}
		
		}

		/**
		 * Returns information about the most important PHP extensions for WP
	 	 * @param $type ( 'cURL', 'fsockopen', 'soapclient', 'suhosin' )
		 * 
		 */
		static function php_extension( $type ) {

			if ( ! is_admin() ) { return null; }

			if ( $type == 'curl' ) { 

				return function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported';

			} else if ( $type == 'fsockopen') { 

				return function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported';

			} else if ( $type == 'soapclient') { 

				return function_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed';

			} else if ( $type == 'suhosin') { 

				return function_exists( 'suhosin' ) ? 'Installed' : 'Not Installed';

			}		
		}


		/**
		 * Returns information about PHP Session
	 	 * @param $type ( 'session', 'name', 'cookie_path', 'save_path', 'use_cookies', 'use_only_cookies' )
		 * 
		 */
		static function php_session( $type ) {

			if ( ! is_admin() ) { return null; }

			if ( isset( $_SESSION ) ) {
				if ( $type == 'session' ) { 

					return isset( $_SESSION ) ? 'Enabled' : 'Disabled';

				} else if ( $type == 'name') { 

					return esc_html( ini_get( 'session.name' ) );

				} else if ( $type == 'cookie_path') { 

					return esc_html( ini_get( 'session.cookie_path' ) );

				} else if ( $type == 'save_path') { 

					return esc_html( ini_get( 'session.save_path' ) ) ;

				} else if ( $type == 'use_cookies') { 

					return ini_get( 'session.use_cookies' ) ? 'On' : 'Off';

				} else if ( $type == 'use_only_cookies') { 

					return ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off';
				}

			} else { 

				return null;
			}		
		}

		/**
		 * Returns information about browser
		 * 
		 */
		static function browser() {

			if ( ! is_admin() ) { return null; }

			if ( class_exists( 'Plethora_Browser' ) ) {

                $browser = new Plethora_Browser();
                return $browser;
			
			} else { 

				return 'Browser info cannot be detected';
			}
		}
	}


    /**
     * Browser detection class
     *
     * @author      Original Author: Chris Schuld (http://chrisschuld.com/)
     * @author      Modifications for EDD: Chris Christoff
     * @version     1.9
     * 
     */
    class Plethora_Browser {
        public $_agent = '';
        public $_browser_name = '';
        public $_version = '';
        public $_platform = '';
        public $_os = '';
        public $_is_aol = false;
        public $_is_mobile = false;
        public $_is_robot = false;
        public $_aol_version = '';

        public $BROWSER_UNKNOWN = 'unknown';
        public $VERSION_UNKNOWN = 'unknown';

        public $BROWSER_OPERA = 'Opera'; // Http://www.opera.com/
        public $BROWSER_OPERA_MINI = 'Opera Mini'; // Http://www.opera.com/mini/
        public $BROWSER_WEBTV = 'WebTV'; // Http://www.webtv.net/pc/
        public $BROWSER_IE = 'Internet Explorer'; // Http://www.microsoft.com/ie/
        public $BROWSER_POCKET_IE = 'Pocket Internet Explorer'; // Http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
        public $BROWSER_KONQUEROR = 'Konqueror'; // Http://www.konqueror.org/
        public $BROWSER_ICAB = 'iCab'; // Http://www.icab.de/
        public $BROWSER_OMNIWEB = 'OmniWeb'; // Http://www.omnigroup.com/applications/omniweb/
        public $BROWSER_FIREBIRD = 'Firebird'; // Http://www.ibphoenix.com/
        public $BROWSER_FIREFOX = 'Firefox'; // Http://www.mozilla.com/en-US/firefox/firefox.html
        public $BROWSER_ICEWEASEL = 'Iceweasel'; // Http://www.geticeweasel.org/
        public $BROWSER_SHIRETOKO = 'Shiretoko'; // Http://wiki.mozilla.org/Projects/shiretoko
        public $BROWSER_MOZILLA = 'Mozilla'; // Http://www.mozilla.com/en-US/
        public $BROWSER_AMAYA = 'Amaya'; // Http://www.w3.org/Amaya/
        public $BROWSER_LYNX = 'Lynx'; // Http://en.wikipedia.org/wiki/Lynx
        public $BROWSER_SAFARI = 'Safari'; // Http://apple.com
        public $BROWSER_IPHONE = 'iPhone'; // Http://apple.com
        public $BROWSER_IPOD = 'iPod'; // Http://apple.com
        public $BROWSER_IPAD = 'iPad'; // Http://apple.com
        public $BROWSER_CHROME = 'Chrome'; // Http://www.google.com/chrome
        public $BROWSER_ANDROID = 'Android'; // Http://www.android.com/
        public $BROWSER_GOOGLEBOT = 'GoogleBot'; // Http://en.wikipedia.org/wiki/Googlebot
        public $BROWSER_SLURP = 'Yahoo! Slurp'; // Http://en.wikipedia.org/wiki/Yahoo!_Slurp
        public $BROWSER_W3CVALIDATOR = 'W3C Validator'; // Http://validator.w3.org/
        public $BROWSER_BLACKBERRY = 'BlackBerry'; // Http://www.blackberry.com/
        public $BROWSER_ICECAT = 'IceCat'; // Http://en.wikipedia.org/wiki/GNU_IceCat
        public $BROWSER_NOKIA_S60 = 'Nokia S60 OSS Browser'; // Http://en.wikipedia.org/wiki/Web_Browser_for_S60
        public $BROWSER_NOKIA = 'Nokia Browser'; // * all other WAP-based browsers on the Nokia Platform
        public $BROWSER_MSN = 'MSN Browser'; // Http://explorer.msn.com/
        public $BROWSER_MSNBOT = 'MSN Bot'; // Http://search.msn.com/msnbot.htm
        // Http://en.wikipedia.org/wiki/Msnbot  (used for Bing as well)

        public $BROWSER_NETSCAPE_NAVIGATOR = 'Netscape Navigator'; // Http://browser.netscape.com/ (DEPRECATED)
        public $BROWSER_GALEON = 'Galeon'; // Http://galeon.sourceforge.net/ (DEPRECATED)
        public $BROWSER_NETPOSITIVE = 'NetPositive'; // Http://en.wikipedia.org/wiki/NetPositive (DEPRECATED)
        public $BROWSER_PHOENIX = 'Phoenix'; // Http://en.wikipedia.org/wiki/History_of_Mozilla_Firefox (DEPRECATED)

        public $PLATFORM_UNKNOWN = 'unknown';
        public $PLATFORM_WINDOWS = 'Windows';
        public $PLATFORM_WINDOWS_CE = 'Windows CE';
        public $PLATFORM_APPLE = 'Apple';
        public $PLATFORM_LINUX = 'Linux';
        public $PLATFORM_OS2 = 'OS/2';
        public $PLATFORM_BEOS = 'BeOS';
        public $PLATFORM_IPHONE = 'iPhone';
        public $PLATFORM_IPOD = 'iPod';
        public $PLATFORM_IPAD = 'iPad';
        public $PLATFORM_BLACKBERRY = 'BlackBerry';
        public $PLATFORM_NOKIA = 'Nokia';
        public $PLATFORM_FREEBSD = 'FreeBSD';
        public $PLATFORM_OPENBSD = 'OpenBSD';
        public $PLATFORM_NETBSD = 'NetBSD';
        public $PLATFORM_SUNOS = 'SunOS';
        public $PLATFORM_OPENSOLARIS = 'OpenSolaris';
        public $PLATFORM_ANDROID = 'Android';

        public $OPERATING_SYSTEM_UNKNOWN = 'unknown';

        function __construct( $useragent = '' ) {
            $this->reset();

            if ( $useragent != '' ) {
                $this->setUserAgent( $useragent );
            } else {
                $this->determine();
            }
        }

        /**
         * Reset all properties
         */
        function reset() {
            $this->_agent        = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $this->_browser_name = $this->BROWSER_UNKNOWN;
            $this->_version      = $this->VERSION_UNKNOWN;
            $this->_platform     = $this->PLATFORM_UNKNOWN;
            $this->_os           = $this->OPERATING_SYSTEM_UNKNOWN;
            $this->_is_aol       = false;
            $this->_is_mobile    = false;
            $this->_is_robot     = false;
            $this->_aol_version  = $this->VERSION_UNKNOWN;
        }

        /**
         * Check to see if the specific browser is valid
         *
         * @param string $browserName
         *
         * @return True if the browser is the specified browser
         */
        function isBrowser( $browserName ) {
            return ( 0 == strcasecmp( $this->_browser_name, trim( $browserName ) ) );
        }

        /**
         * The name of the browser.  All return types are from the class contants
         *
         * @return string Name of the browser
         */
        function getBrowser() {
            return $this->_browser_name;
        }

        /**
         * Set the name of the browser
         *
         * @param $browser The name of the Browser
         */
        function setBrowser( $browser ) {
            return $this->_browser_name = $browser;
        }

        /**
         * The name of the platform.  All return types are from the class contants
         *
         * @return string Name of the browser
         */
        function getPlatform() {
            return $this->_platform;
        }

        /**
         * Set the name of the platform
         *
         * @param $platform The name of the Platform
         */
        function setPlatform( $platform ) {
            return $this->_platform = $platform;
        }

        /**
         * The version of the browser.
         *
         * @return string Version of the browser (will only contain alpha-numeric characters and a period)
         */
        function getVersion() {
            return $this->_version;
        }

        /**
         * Set the version of the browser
         *
         * @param $version The version of the Browser
         */
        function setVersion( $version ) {
            $this->_version = preg_replace( '/[^0-9,.,a-z,A-Z-]/', '', $version );
        }

        /**
         * The version of AOL.
         *
         * @return string Version of AOL (will only contain alpha-numeric characters and a period)
         */
        function getAolVersion() {
            return $this->_aol_version;
        }

        /**
         * Set the version of AOL
         *
         * @param $version The version of AOL
         */
        function setAolVersion( $version ) {
            $this->_aol_version = preg_replace( '/[^0-9,.,a-z,A-Z]/', '', $version );
        }

        /**
         * Is the browser from AOL?
         *
         * @return boolean True if the browser is from AOL otherwise false
         */
        function isAol() {
            return $this->_is_aol;
        }

        /**
         * Is the browser from a mobile device?
         *
         * @return boolean True if the browser is from a mobile device otherwise false
         */
        function isMobile() {
            return $this->_is_mobile;
        }

        /**
         * Is the browser from a robot (ex Slurp,GoogleBot)?
         *
         * @return boolean True if the browser is from a robot otherwise false
         */
        function isRobot() {
            return $this->_is_robot;
        }

        /**
         * Set the browser to be from AOL
         *
         * @param $isAol
         */
        function setAol( $isAol ) {
            $this->_is_aol = $isAol;
        }

        /**
         * Set the Browser to be mobile
         *
         * @param boolean $value is the browser a mobile brower or not
         */
        function setMobile( $value = true ) {
            $this->_is_mobile = $value;
        }

        /**
         * Set the Browser to be a robot
         *
         * @param boolean $value is the browser a robot or not
         */
        function setRobot( $value = true ) {
            $this->_is_robot = $value;
        }

        /**
         * Get the user agent value in use to determine the browser
         *
         * @return string The user agent from the HTTP header
         */
        function getUserAgent() {
            return $this->_agent;
        }

        /**
         * Set the user agent value (the construction will use the HTTP header value - this will overwrite it)
         *
         * @param $agent_string The value for the User Agent
         */
        function setUserAgent( $agent_string ) {
            $this->reset();
            $this->_agent = $agent_string;
            $this->determine();
        }

        /**
         * Used to determine if the browser is actually "chromeframe"
         *
         * @since 1.7
         * @return boolean True if the browser is using chromeframe
         */
        function isChromeFrame() {
            return ( strpos( $this->_agent, 'chromeframe' ) !== false );
        }

        /**
         * Returns a formatted string with a summary of the details of the browser.
         *
         * @return string formatted string with a summary of the browser
         */
        function __toString() {
            $text1    = $this->getUserAgent(); // Grabs the UA string
            $UAline1  = substr( $text1, 0, 32 ); // The first line we print should only be the first 32 characters of the UA string
            $text2    = $this->getUserAgent(); // Now we grab it again and save it to a string
            $towrapUA = str_replace( $UAline1, '', $text2 ); // The rest of the printoff (other than first line) is equivalent
            // to the whole string minus the part we printed off. IE
            // User Agent:      thefirst32charactersfromUAline1
            //                  the rest of it is now stored in
            //                  $text2 to be printed off
            // But we need to add spaces before each line that is split other than line 1
            $space = '';
            for ( $i = 0; $i < 25; $i ++ ) {
                $space .= ' ';
            }

            // Now we split the remaining string of UA ($text2) into lines that are prefixed by spaces for formatting
            $wordwrapped = chunk_split( $towrapUA, 32, "\n $space" );

            return "Platform: {$this->getPlatform()} \n" .
                   "Browser Name: {$this->getBrowser()}  \n" .
                   "Browser Version: {$this->getVersion()} \n" .
                   "User Agent String: $UAline1 \n\t\t\t  " .
                   "$wordwrapped";
        }

        /**
         * Protected routine to calculate and determine what the browser is in use (including platform)
         */
        function determine() {
            $this->checkPlatform();
            $this->checkBrowsers();
            $this->checkForAol();
        }

        /**
         * Protected routine to determine the browser type
         *
         * @return boolean True if the browser was detected otherwise false
         */
        function checkBrowsers() {
            return (
                // Well-known, well-used
                // Special Notes:
                // (1) Opera must be checked before FireFox due to the odd
                //     user agents used in some older versions of Opera
                // (2) WebTV is strapped onto Internet Explorer so we must
                //     check for WebTV before IE
                // (3) (deprecated) Galeon is based on Firefox and needs to be
                //     tested before Firefox is tested
                // (4) OmniWeb is based on Safari so OmniWeb check must occur
                //     before Safari
                // (5) Netscape 9+ is based on Firefox so Netscape checks
                //     before FireFox are necessary
                $this->checkBrowserWebTv() ||
                $this->checkBrowserInternetExplorer() ||
                $this->checkBrowserOpera() ||
                $this->checkBrowserGaleon() ||
                $this->checkBrowserNetscapeNavigator9Plus() ||
                $this->checkBrowserFirefox() ||
                $this->checkBrowserChrome() ||
                $this->checkBrowserOmniWeb() ||

                // Common mobile
                $this->checkBrowserAndroid() ||
                $this->checkBrowseriPad() ||
                $this->checkBrowseriPod() ||
                $this->checkBrowseriPhone() ||
                $this->checkBrowserBlackBerry() ||
                $this->checkBrowserNokia() ||

                // Common bots
                $this->checkBrowserGoogleBot() ||
                $this->checkBrowserMSNBot() ||
                $this->checkBrowserSlurp() ||

                // WebKit base check (post mobile and others)
                $this->checkBrowserSafari() ||

                // Everyone else
                $this->checkBrowserNetPositive() ||
                $this->checkBrowserFirebird() ||
                $this->checkBrowserKonqueror() ||
                $this->checkBrowserIcab() ||
                $this->checkBrowserPhoenix() ||
                $this->checkBrowserAmaya() ||
                $this->checkBrowserLynx() ||

                $this->checkBrowserShiretoko() ||
                $this->checkBrowserIceCat() ||
                $this->checkBrowserW3CValidator() ||
                $this->checkBrowserMozilla() /* Mozilla is such an open standard that you must check it last */
            );
        }

        /**
         * Determine if the user is using a BlackBerry (last updated 1.7)
         *
         * @return boolean True if the browser is the BlackBerry browser otherwise false
         */
        function checkBrowserBlackBerry() {
            if ( stripos( $this->_agent, 'blackberry' ) !== false ) {
                $aresult  = explode( "/", stristr( $this->_agent, "BlackBerry" ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( $aversion[0] );
                $this->_browser_name = $this->BROWSER_BLACKBERRY;
                $this->setMobile( true );

                return true;
            }

            return false;
        }

        /**
         * Determine if the user is using an AOL User Agent (last updated 1.7)
         *
         * @return boolean True if the browser is from AOL otherwise false
         */
        function checkForAol() {
            $this->setAol( false );
            $this->setAolVersion( $this->VERSION_UNKNOWN );

            if ( stripos( $this->_agent, 'aol' ) !== false ) {
                $aversion = explode( ' ', stristr( $this->_agent, 'AOL' ) );
                $this->setAol( true );
                $this->setAolVersion( preg_replace( '/[^0-9\.a-z]/i', '', $aversion[1] ) );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is the GoogleBot or not (last updated 1.7)
         *
         * @return boolean True if the browser is the GoogletBot otherwise false
         */
        function checkBrowserGoogleBot() {
            if ( stripos( $this->_agent, 'googlebot' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'googlebot' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( str_replace( ';', '', $aversion[0] ) );
                $this->_browser_name = $this->BROWSER_GOOGLEBOT;
                $this->setRobot( true );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is the MSNBot or not (last updated 1.9)
         *
         * @return boolean True if the browser is the MSNBot otherwise false
         */
        function checkBrowserMSNBot() {
            if ( stripos( $this->_agent, "msnbot" ) !== false ) {
                $aresult  = explode( "/", stristr( $this->_agent, "msnbot" ) );
                $aversion = explode( " ", $aresult[1] );
                $this->setVersion( str_replace( ";", "", $aversion[0] ) );
                $this->_browser_name = $this->BROWSER_MSNBOT;
                $this->setRobot( true );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is the W3C Validator or not (last updated 1.7)
         *
         * @return boolean True if the browser is the W3C Validator otherwise false
         */
        function checkBrowserW3CValidator() {
            if ( stripos( $this->_agent, 'W3C-checklink' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'W3C-checklink' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( $aversion[0] );
                $this->_browser_name = $this->BROWSER_W3CVALIDATOR;

                return true;
            } else if ( stripos( $this->_agent, 'W3C_Validator' ) !== false ) {
                // Some of the Validator versions do not delineate w/ a slash - add it back in
                $ua       = str_replace( "W3C_Validator ", "W3C_Validator/", $this->_agent );
                $aresult  = explode( '/', stristr( $ua, 'W3C_Validator' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( $aversion[0] );
                $this->_browser_name = $this->BROWSER_W3CVALIDATOR;

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is the Yahoo! Slurp Robot or not (last updated 1.7)
         *
         * @return boolean True if the browser is the Yahoo! Slurp Robot otherwise false
         */
        function checkBrowserSlurp() {
            if ( stripos( $this->_agent, 'slurp' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'Slurp' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( $aversion[0] );
                $this->_browser_name = $this->BROWSER_SLURP;
                $this->setRobot( true );
                $this->setMobile( false );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Internet Explorer or not (last updated 1.7)
         *
         * @return boolean True if the browser is Internet Explorer otherwise false
         */
        function checkBrowserInternetExplorer() {

            // Test for v1 - v1.5 IE
            if ( stripos( $this->_agent, 'microsoft internet explorer' ) !== false ) {
                $this->setBrowser( $this->BROWSER_IE );
                $this->setVersion( '1.0' );
                $aresult = stristr( $this->_agent, '/' );
                if ( preg_match( '/308|425|426|474|0b1/i', $aresult ) ) {
                    $this->setVersion( '1.5' );
                }

                return true;
                // Test for versions > 1.5
            } else if ( stripos( $this->_agent, 'msie' ) !== false && stripos( $this->_agent, 'opera' ) === false ) {
                // See if the browser is the odd MSN Explorer
                if ( stripos( $this->_agent, 'msnb' ) !== false ) {
                    $aresult = explode( ' ', stristr( str_replace( ';', '; ', $this->_agent ), 'MSN' ) );
                    $this->setBrowser( $this->BROWSER_MSN );
                    $this->setVersion( str_replace( array( '(', ')', ';' ), '', $aresult[1] ) );

                    return true;
                }
                $aresult = explode( ' ', stristr( str_replace( ';', '; ', $this->_agent ), 'msie' ) );
                $this->setBrowser( $this->BROWSER_IE );
                $this->setVersion( str_replace( array( '(', ')', ';' ), '', $aresult[1] ) );

                return true;
                // Test for Pocket IE
            } else if ( stripos( $this->_agent, 'mspie' ) !== false || stripos( $this->_agent, 'pocket' ) !== false ) {
                $aresult = explode( ' ', stristr( $this->_agent, 'mspie' ) );
                $this->setPlatform( $this->PLATFORM_WINDOWS_CE );
                $this->setBrowser( $this->BROWSER_POCKET_IE );
                $this->setMobile( true );

                if ( stripos( $this->_agent, 'mspie' ) !== false ) {
                    $this->setVersion( $aresult[1] );
                } else {
                    $aversion = explode( '/', $this->_agent );
                    $this->setVersion( $aversion[1] );
                }

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Opera or not (last updated 1.7)
         *
         * @return boolean True if the browser is Opera otherwise false
         */
        function checkBrowserOpera() {
            if ( stripos( $this->_agent, 'opera mini' ) !== false ) {
                $resultant = stristr( $this->_agent, 'opera mini' );
                if ( preg_match( '/\//', $resultant ) ) {
                    $aresult  = explode( '/', $resultant );
                    $aversion = explode( ' ', $aresult[1] );
                    $this->setVersion( $aversion[0] );
                } else {
                    $aversion = explode( ' ', stristr( $resultant, 'opera mini' ) );
                    $this->setVersion( $aversion[1] );
                }
                $this->_browser_name = $this->BROWSER_OPERA_MINI;
                $this->setMobile( true );

                return true;
            } else if ( stripos( $this->_agent, 'opera' ) !== false ) {
                $resultant = stristr( $this->_agent, 'opera' );
                if ( preg_match( '/Version\/(10.*)$/', $resultant, $matches ) ) {
                    $this->setVersion( $matches[1] );
                } else if ( preg_match( '/\//', $resultant ) ) {
                    $aresult  = explode( '/', str_replace( "(", " ", $resultant ) );
                    $aversion = explode( ' ', $aresult[1] );
                    $this->setVersion( $aversion[0] );
                } else {
                    $aversion = explode( ' ', stristr( $resultant, 'opera' ) );
                    $this->setVersion( isset( $aversion[1] ) ? $aversion[1] : "" );
                }
                $this->_browser_name = $this->BROWSER_OPERA;

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Chrome or not (last updated 1.7)
         *
         * @return boolean True if the browser is Chrome otherwise false
         */
        function checkBrowserChrome() {
            if ( stripos( $this->_agent, 'Chrome' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'Chrome' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( $aversion[0] );
                $this->setBrowser( $this->BROWSER_CHROME );

                return true;
            }

            return false;
        }


        /**
         * Determine if the browser is WebTv or not (last updated 1.7)
         *
         * @return boolean True if the browser is WebTv otherwise false
         */
        function checkBrowserWebTv() {
            if ( stripos( $this->_agent, 'webtv' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'webtv' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( $aversion[0] );
                $this->setBrowser( $this->BROWSER_WEBTV );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is NetPositive or not (last updated 1.7)
         *
         * @return boolean True if the browser is NetPositive otherwise false
         */
        function checkBrowserNetPositive() {
            if ( stripos( $this->_agent, 'NetPositive' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'NetPositive' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( str_replace( array( '(', ')', ';' ), '', $aversion[0] ) );
                $this->setBrowser( $this->BROWSER_NETPOSITIVE );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Galeon or not (last updated 1.7)
         *
         * @return boolean True if the browser is Galeon otherwise false
         */
        function checkBrowserGaleon() {
            if ( stripos( $this->_agent, 'galeon' ) !== false ) {
                $aresult  = explode( ' ', stristr( $this->_agent, 'galeon' ) );
                $aversion = explode( '/', $aresult[0] );
                $this->setVersion( $aversion[1] );
                $this->setBrowser( $this->BROWSER_GALEON );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Konqueror or not (last updated 1.7)
         *
         * @return boolean True if the browser is Konqueror otherwise false
         */
        function checkBrowserKonqueror() {
            if ( stripos( $this->_agent, 'Konqueror' ) !== false ) {
                $aresult  = explode( ' ', stristr( $this->_agent, 'Konqueror' ) );
                $aversion = explode( '/', $aresult[0] );
                $this->setVersion( $aversion[1] );
                $this->setBrowser( $this->BROWSER_KONQUEROR );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is iCab or not (last updated 1.7)
         *
         * @return boolean True if the browser is iCab otherwise false
         */
        function checkBrowserIcab() {
            if ( stripos( $this->_agent, 'icab' ) !== false ) {
                $aversion = explode( ' ', stristr( str_replace( '/', ' ', $this->_agent ), 'icab' ) );
                $this->setVersion( $aversion[1] );
                $this->setBrowser( $this->BROWSER_ICAB );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is OmniWeb or not (last updated 1.7)
         *
         * @return boolean True if the browser is OmniWeb otherwise false
         */
        function checkBrowserOmniWeb() {
            if ( stripos( $this->_agent, 'omniweb' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'omniweb' ) );
                $aversion = explode( ' ', isset( $aresult[1] ) ? $aresult[1] : "" );
                $this->setVersion( $aversion[0] );
                $this->setBrowser( $this->BROWSER_OMNIWEB );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Phoenix or not (last updated 1.7)
         *
         * @return boolean True if the browser is Phoenix otherwise false
         */
        function checkBrowserPhoenix() {
            if ( stripos( $this->_agent, 'Phoenix' ) !== false ) {
                $aversion = explode( '/', stristr( $this->_agent, 'Phoenix' ) );
                $this->setVersion( $aversion[1] );
                $this->setBrowser( $this->BROWSER_PHOENIX );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Firebird or not (last updated 1.7)
         *
         * @return boolean True if the browser is Firebird otherwise false
         */
        function checkBrowserFirebird() {
            if ( stripos( $this->_agent, 'Firebird' ) !== false ) {
                $aversion = explode( '/', stristr( $this->_agent, 'Firebird' ) );
                $this->setVersion( $aversion[1] );
                $this->setBrowser( $this->BROWSER_FIREBIRD );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Netscape Navigator 9+ or not (last updated 1.7)
         * NOTE: (http://browser.netscape.com/ - Official support ended on March 1st, 2008)
         *
         * @return boolean True if the browser is Netscape Navigator 9+ otherwise false
         */
        function checkBrowserNetscapeNavigator9Plus() {
            if ( stripos( $this->_agent, 'Firefox' ) !== false && preg_match( '/Navigator\/([^ ]*)/i', $this->_agent, $matches ) ) {
                $this->setVersion( $matches[1] );
                $this->setBrowser( $this->BROWSER_NETSCAPE_NAVIGATOR );

                return true;
            } else if ( stripos( $this->_agent, 'Firefox' ) === false && preg_match( '/Netscape6?\/([^ ]*)/i', $this->_agent, $matches ) ) {
                $this->setVersion( $matches[1] );
                $this->setBrowser( $this->BROWSER_NETSCAPE_NAVIGATOR );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Shiretoko or not (https://wiki.mozilla.org/Projects/shiretoko) (last updated 1.7)
         *
         * @return boolean True if the browser is Shiretoko otherwise false
         */
        function checkBrowserShiretoko() {
            if ( stripos( $this->_agent, 'Mozilla' ) !== false && preg_match( '/Shiretoko\/([^ ]*)/i', $this->_agent, $matches ) ) {
                $this->setVersion( $matches[1] );
                $this->setBrowser( $this->BROWSER_SHIRETOKO );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Ice Cat or not (http://en.wikipedia.org/wiki/GNU_IceCat) (last updated 1.7)
         *
         * @return boolean True if the browser is Ice Cat otherwise false
         */
        function checkBrowserIceCat() {
            if ( stripos( $this->_agent, 'Mozilla' ) !== false && preg_match( '/IceCat\/([^ ]*)/i', $this->_agent, $matches ) ) {
                $this->setVersion( $matches[1] );
                $this->setBrowser( $this->BROWSER_ICECAT );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Nokia or not (last updated 1.7)
         *
         * @return boolean True if the browser is Nokia otherwise false
         */
        function checkBrowserNokia() {
            if ( preg_match( "/Nokia([^\/]+)\/([^ SP]+)/i", $this->_agent, $matches ) ) {
                $this->setVersion( $matches[2] );
                if ( stripos( $this->_agent, 'Series60' ) !== false || strpos( $this->_agent, 'S60' ) !== false ) {
                    $this->setBrowser( $this->BROWSER_NOKIA_S60 );
                } else {
                    $this->setBrowser( $this->BROWSER_NOKIA );
                }
                $this->setMobile( true );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Firefox or not (last updated 1.7)
         *
         * @return boolean True if the browser is Firefox otherwise false
         */
        function checkBrowserFirefox() {
            if ( stripos( $this->_agent, 'safari' ) === false ) {
                if ( preg_match( "/Firefox[\/ \(]([^ ;\)]+)/i", $this->_agent, $matches ) ) {
                    $this->setVersion( $matches[1] );
                    $this->setBrowser( $this->BROWSER_FIREFOX );

                    return true;
                } else if ( preg_match( "/Firefox$/i", $this->_agent, $matches ) ) {
                    $this->setVersion( "" );
                    $this->setBrowser( $this->BROWSER_FIREFOX );

                    return true;
                }
            }

            return false;
        }

        /**
         * Determine if the browser is Firefox or not (last updated 1.7)
         *
         * @return boolean True if the browser is Firefox otherwise false
         */
        function checkBrowserIceweasel() {
            if ( stripos( $this->_agent, 'Iceweasel' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'Iceweasel' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( $aversion[0] );
                $this->setBrowser( $this->BROWSER_ICEWEASEL );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Mozilla or not (last updated 1.7)
         *
         * @return boolean True if the browser is Mozilla otherwise false
         */
        function checkBrowserMozilla() {
            if ( stripos( $this->_agent, 'mozilla' ) !== false && preg_match( '/rv:[0-9].[0-9][a-b]?/i', $this->_agent ) && stripos( $this->_agent, 'netscape' ) === false ) {
                $aversion = explode( ' ', stristr( $this->_agent, 'rv:' ) );
                preg_match( '/rv:[0-9].[0-9][a-b]?/i', $this->_agent, $aversion );
                $this->setVersion( str_replace( 'rv:', '', $aversion[0] ) );
                $this->setBrowser( $this->BROWSER_MOZILLA );

                return true;
            } else if ( stripos( $this->_agent, 'mozilla' ) !== false && preg_match( '/rv:[0-9]\.[0-9]/i', $this->_agent ) && stripos( $this->_agent, 'netscape' ) === false ) {
                $aversion = explode( '', stristr( $this->_agent, 'rv:' ) );
                $this->setVersion( str_replace( 'rv:', '', $aversion[0] ) );
                $this->setBrowser( $this->BROWSER_MOZILLA );

                return true;
            } else if ( stripos( $this->_agent, 'mozilla' ) !== false && preg_match( '/mozilla\/([^ ]*)/i', $this->_agent, $matches ) && stripos( $this->_agent, 'netscape' ) === false ) {
                $this->setVersion( $matches[1] );
                $this->setBrowser( $this->BROWSER_MOZILLA );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Lynx or not (last updated 1.7)
         *
         * @return boolean True if the browser is Lynx otherwise false
         */
        function checkBrowserLynx() {
            if ( stripos( $this->_agent, 'lynx' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'Lynx' ) );
                $aversion = explode( ' ', ( isset( $aresult[1] ) ? $aresult[1] : "" ) );
                $this->setVersion( $aversion[0] );
                $this->setBrowser( $this->BROWSER_LYNX );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Amaya or not (last updated 1.7)
         *
         * @return boolean True if the browser is Amaya otherwise false
         */
        function checkBrowserAmaya() {
            if ( stripos( $this->_agent, 'amaya' ) !== false ) {
                $aresult  = explode( '/', stristr( $this->_agent, 'Amaya' ) );
                $aversion = explode( ' ', $aresult[1] );
                $this->setVersion( $aversion[0] );
                $this->setBrowser( $this->BROWSER_AMAYA );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Safari or not (last updated 1.7)
         *
         * @return boolean True if the browser is Safari otherwise false
         */
        function checkBrowserSafari() {
            if ( stripos( $this->_agent, 'Safari' ) !== false && stripos( $this->_agent, 'iPhone' ) === false && stripos( $this->_agent, 'iPod' ) === false ) {
                $aresult = explode( '/', stristr( $this->_agent, 'Version' ) );
                if ( isset( $aresult[1] ) ) {
                    $aversion = explode( ' ', $aresult[1] );
                    $this->setVersion( $aversion[0] );
                } else {
                    $this->setVersion( $this->VERSION_UNKNOWN );
                }
                $this->setBrowser( $this->BROWSER_SAFARI );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is iPhone or not (last updated 1.7)
         *
         * @return boolean True if the browser is iPhone otherwise false
         */
        function checkBrowseriPhone() {
            if ( stripos( $this->_agent, 'iPhone' ) !== false ) {
                $aresult = explode( '/', stristr( $this->_agent, 'Version' ) );
                if ( isset( $aresult[1] ) ) {
                    $aversion = explode( ' ', $aresult[1] );
                    $this->setVersion( $aversion[0] );
                } else {
                    $this->setVersion( $this->VERSION_UNKNOWN );
                }
                $this->setMobile( true );
                $this->setBrowser( $this->BROWSER_IPHONE );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is iPod or not (last updated 1.7)
         *
         * @return boolean True if the browser is iPod otherwise false
         */
        function checkBrowseriPad() {
            if ( stripos( $this->_agent, 'iPad' ) !== false ) {
                $aresult = explode( '/', stristr( $this->_agent, 'Version' ) );
                if ( isset( $aresult[1] ) ) {
                    $aversion = explode( ' ', $aresult[1] );
                    $this->setVersion( $aversion[0] );
                } else {
                    $this->setVersion( $this->VERSION_UNKNOWN );
                }
                $this->setMobile( true );
                $this->setBrowser( $this->BROWSER_IPAD );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is iPod or not (last updated 1.7)
         *
         * @return boolean True if the browser is iPod otherwise false
         */
        function checkBrowseriPod() {
            if ( stripos( $this->_agent, 'iPod' ) !== false ) {
                $aresult = explode( '/', stristr( $this->_agent, 'Version' ) );
                if ( isset( $aresult[1] ) ) {
                    $aversion = explode( ' ', $aresult[1] );
                    $this->setVersion( $aversion[0] );
                } else {
                    $this->setVersion( $this->VERSION_UNKNOWN );
                }
                $this->setMobile( true );
                $this->setBrowser( $this->BROWSER_IPOD );

                return true;
            }

            return false;
        }

        /**
         * Determine if the browser is Android or not (last updated 1.7)
         *
         * @return boolean True if the browser is Android otherwise false
         */
        function checkBrowserAndroid() {
            if ( stripos( $this->_agent, 'Android' ) !== false ) {
                $aresult = explode( ' ', stristr( $this->_agent, 'Android' ) );
                if ( isset( $aresult[1] ) ) {
                    $aversion = explode( ' ', $aresult[1] );
                    $this->setVersion( $aversion[0] );
                } else {
                    $this->setVersion( $this->VERSION_UNKNOWN );
                }
                $this->setMobile( true );
                $this->setBrowser( $this->BROWSER_ANDROID );

                return true;
            }

            return false;
        }

        /**
         * Determine the user's platform (last updated 1.7)
         */
        function checkPlatform() {
            if ( stripos( $this->_agent, 'windows' ) !== false ) {
                $this->_platform = $this->PLATFORM_WINDOWS;
            } elseif ( stripos( $this->_agent, 'iPad' ) !== false ) {
                $this->_platform = $this->PLATFORM_IPAD;
            } elseif ( stripos( $this->_agent, 'iPod' ) !== false ) {
                $this->_platform = $this->PLATFORM_IPOD;
            } elseif ( stripos( $this->_agent, 'iPhone' ) !== false ) {
                $this->_platform = $this->PLATFORM_IPHONE;
            } elseif ( stripos( $this->_agent, 'mac' ) !== false ) {
                $this->_platform = $this->PLATFORM_APPLE;
            } elseif ( stripos( $this->_agent, 'android' ) !== false ) {
                $this->_platform = $this->PLATFORM_ANDROID;
            } elseif ( stripos( $this->_agent, 'linux' ) !== false ) {
                $this->_platform = $this->PLATFORM_LINUX;
            } elseif ( stripos( $this->_agent, 'Nokia' ) !== false ) {
                $this->_platform = $this->PLATFORM_NOKIA;
            } elseif ( stripos( $this->_agent, 'BlackBerry' ) !== false ) {
                $this->_platform = $this->PLATFORM_BLACKBERRY;
            } elseif ( stripos( $this->_agent, 'FreeBSD' ) !== false ) {
                $this->_platform = $this->PLATFORM_FREEBSD;
            } elseif ( stripos( $this->_agent, 'OpenBSD' ) !== false ) {
                $this->_platform = $this->PLATFORM_OPENBSD;
            } elseif ( stripos( $this->_agent, 'NetBSD' ) !== false ) {
                $this->_platform = $this->PLATFORM_NETBSD;
            } elseif ( stripos( $this->_agent, 'OpenSolaris' ) !== false ) {
                $this->_platform = $this->PLATFORM_OPENSOLARIS;
            } elseif ( stripos( $this->_agent, 'SunOS' ) !== false ) {
                $this->_platform = $this->PLATFORM_SUNOS;
            } elseif ( stripos( $this->_agent, 'OS\/2' ) !== false ) {
                $this->_platform = $this->PLATFORM_OS2;
            } elseif ( stripos( $this->_agent, 'BeOS' ) !== false ) {
                $this->_platform = $this->PLATFORM_BEOS;
            } elseif ( stripos( $this->_agent, 'win' ) !== false ) {
                $this->_platform = $this->PLATFORM_WINDOWS;
            }
        }
    }