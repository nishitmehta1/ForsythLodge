<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Button shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_AppStoreButton') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_AppStoreButton extends Plethora_Shortcode { 

    public static $feature_title         = "Appstore Button Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public $wp_slug                      = 'appstorebutton';  // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
    public static $assets;
   
    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'            => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'            => esc_html__("App Store Button", 'plethora-framework'), 
                    'description'     => esc_html__('with icon and styling settings', 'plethora-framework'), 
                    'class'           => '', 
                    'weight'          => 1, 
                    'category'        => 'Content', 
                    'icon'            => $this->vc_icon(), 
                    'content_element' => true,  // USE IN ITEM CONTAINER SHORTCODE
                    'params'          => $this->params(), 
                    );
        // Add the shortcode
        $this->add( $map );

    }

    /** 
    * Returns shortcode parameters for VC panel
    *
    * @return array
    * @since 2.0
    *
    */
    public function params() {

      $params = array(

                    // APP STORE BRANDS
                    array(
                      "param_name"    => "appstore_brand",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Which App Store?", 'plethora-framework'),      
                      "value"         => array(
                        'Google Play'   =>'google_play',
                        'Apple Store'   =>'apple_store',
                        'Amazon Apps'   =>'amazon_apps',
                        'Windows Store' =>'windows_store'
                        ),
                      "admin_label"   => false,                                              
                    ),
                    // GOOGLE PLAY LANGUAGES
                    array(
                      "param_name"    => "appstore_google_play_language",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Available Languages", 'plethora-framework'),      
                      "value"         => array(
                        'English'                                           => 'en',         // BOTH 
                        "Afrikaans"                                         => "af",                           
                        "العربية / Arabic"                                  => "ar",
                        "Беларуская / Belarusian"                           => "be",                           
                        "Български / Bulgarian"                             => "bg",                            
                        "Català / Catalan"                                  => "ca",                          
                        "中文 (中国) / Chinese (China)"                      => "zh-cn",                           
                        "中文（香港） / Chinese (Hong Kong)"                  => "zh-hk",                           
                        "中文 (台灣) / Chinese (Taiwan)"                     => "zh-tw",                          
                        "Hrvatski / Croatian"                               => "hr",            
                        "Česky / Czech"                                     => "cs",                            
                        "Dansk / Danish"                                    => "da", 
                        "Nederlands / Dutch"                                => "nl", // BOTH
                        "Eesti / Estonian"                                  => "et",                         
                        "فارسی / Farsi - Persian"                           => "fa",
                        "Tagalog / Filipino"                                => "fil",
                        "Suomi / Finnish"                                   => "fi",                          
                        "Français / French"                                 => "fr",  // BOTH                           
                        "Deutsch / German"                                  => "de",  // BOTH
                        "Ελληνικά / Greek"                                  => "el",                            
                        "Magyar / Hungarian"                                => "hu",                            
                        "Bahasa Indonesia / Indonesian"                     => "id-in",                            
                        "Italiano / Italian"                                => "it",  // BOTH                        
                        "日本語 / Japanese"                                  => "ja", // BOTH                        
                        "한국어 / Korean"                                     => "ko", // BOTH                           
                        "Latviešu / Latvian"                                => "lv",                          
                        "Lietuviškai / Lithuanian"                          => "lt",                           
                        "Bahasa Melayu / Malay"                             => "ms",                            
                        "Norsk (bokmål)‎ / Norwegian"                        => "no",                            
                        "Polski / Polish"                                   => "pl",              
                        "Português (Brasil) / Portuguese (Brazil)"          => "pt-br", // BOTH
                        "Português (Portugal) / Portuguese (Portugal)"      => "pt-pt",   // BOTH
                        "Română / Romanian"                                 => "ro",                         
                        "Русский / Russian"                                 => "ru",                          
                        "Српски / srpski / Serbian"                         => "sr",                          
                        "Slovenčina / Slovak"                               => "sk",                           
                        "Slovenščina / Slovenian"                           => "sl",                            
                        "Español (España) / Spanish (Spain)"                => "es",    // BOTH
                        "Español (Latinoamérica) / Spanish (Latin America)" => "es-419", // BOTH
                        "Svenska / Swedish"                                 => "sv",                          
                        "Kiswahili / Swahili"                               => "sw",                          
                        "ไทย / Thai"                                        => "th",                         
                        "Türkçe / Turkish"                                  => "tr",                          
                        "Українська / Ukrainian"                            => "uk",                            
                        "Tiếng Việt / Vietnamese"                           => "vi",                           
                        "isiZulu / Zulu"                                    => "zu"                         
                        ),
                      "admin_label"   => false,                                              
                      "dependency"    => array( 
                                          'element' => 'appstore_brand', 
                                          'value'   => array('google_play'),  
                                      )
                    ),
                    // GOOGLE PLAY BADGE SELECTION BASED ON AVAILABLE LANGUAGES ( GET IT ON + ANDROID APP ON BADGES )
                    array(
                      "param_name"    => "appstore_google_play_badges",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Select Badge Type", 'plethora-framework'),      
                      "value"         => array(
                        'Get It On'      => 'generic',
                        'Android App On' => 'app'
                        ),
                      "admin_label"   => false,                                              
                      "dependency"    => array( 
                                          'element' => 'appstore_google_play_language', 
                                          'value'   => array(
                                            'en',
                                            "nl", 
                                            "fr",                             
                                            "de",  
                                            "it",                          
                                            "ja",                         
                                            "ko",                            
                                            "pt-br", 
                                            "pt-pt",   
                                            "es",    
                                            "es-419", 
                                            ),  
                                      )
                    ),
                    // APPLE STORE LANGUAGES
                    array(
                      "param_name"    => "appstore_apple_store_language",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Available Languages", 'plethora-framework'),      
                      "value"         => array(
                        "English"                                      => "us-uk",
                        "العربية / Arabic"                             => "ar",
                        "Azərbaycan dili / Azerbaijani"                => "az",
                        "Bahasa Indonesia / Indonesian"                => "id",
                        "Bahasa Melayu / Malay"                        => "my",
                        "Čeština / Czech"                              => "cz",
                        "Dansk / Danish"                               => "dk",
                        "Deutsch / German"                             => "de",
                        "Eesti keel / Estonian"                        => "ee",
                        "Español / Spanish"                            => "es",
                        "Français / French"                            => "fr",
                        "Italiano / Italian"                           => "it",
                        "Latviski / Latvian"                           => "lv",
                        "Lietuviškai / Lithuanian"                     => "lt",
                        "Magyar / Hungarian"                           => "hu",
                        "Malti / Maltese"                              => "mt",
                        "Nederlands / Dutch"                           => "nl",
                        "Norsk / Norwegian"                            => "no",
                        "Polski / Polish"                              => "pl",
                        "Português (Portugal) / Portuguese (Portugal)" => "pt",
                        "Português (Brasil) / Portuguese (Brazil)"     => "ptbr",
                        "Русский / Russian"                            => "ru",
                        "Română / Romanian"                            => "ro",
                        "Slovenčina / Slovak"                          => "sk",
                        "Slovenščina / Slovenian"                      => "si",
                        "Suomi / Finnish"                              => "fi",
                        "Svenska / Swedish"                            => "se",
                        "Tagalog / Filipino"                           => "ph",
                        "Tiếng Việt / Vietnamese"                      => "vn",
                        "Türkçe / Turkish"                             => "tr",
                        "Ελληνικά / Greek"                             => "gr",
                        "日本語 / Japanese"                             => "jp  ",     
                        "简体中文 / Chinese Simplified"                 => "cn",
                        "繁體中文 / Chinese Traditional"                => "hk_tw",
                        "עברית / Hebrew"                               => "hb",
                        "ภาษาไทย / Thai "                             => "th",
                        "한국어 / Korean"                        => "kr",
                        "български / Bulgarian"                       => "bg"
                        ),
                      "admin_label"   => false,                                              
                      "dependency"    => array( 
                                          'element' => 'appstore_brand', 
                                          'value'   => array('apple_store'),  
                                      )
                    ),
                    // APPLE STORE BADGE SELECTION
                    array(
                      "param_name"    => "appstore_apple_store_badges",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Select Badge Type", 'plethora-framework'),      
                      "value"         => array(
                        'Download on the App Store' => 'download',
                        'Single Icon'               => 'icon'
                        ),
                      "admin_label"   => false,                                              
                      "dependency"    => array( 
                                          'element' => 'appstore_brand', 
                                          'value'   => array('apple_store'),  
                                      )
                    ),
                    // WINDOWS STORE BADGE SELECTION
                    array(
                      "param_name"    => "appstore_windows_store_badges",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Select Badge Type", 'plethora-framework'),      
                      "value"         => array(
                        'Download from (black)'       => 'black',
                        'Download from (cyan)'        => 'cyan',
                        'Download from (white/cyan)'  => 'rev_cyan',
                        'Download from (white/black)' => 'rev_black',
                        'Single Icon (black)'         => 'ICON_black',
                        'Single Icon (cyan)'          => 'ICON_cyan',
                        'Single Icon (white/black)'   => 'ICON_rev_black',
                        'Single Icon (white/cyan)'    => 'ICON_rev_cyan'
                        ),
                      "admin_label"   => false,                                              
                      "dependency"    => array( 
                                          'element' => 'appstore_brand', 
                                          'value'   => array('windows_store'),  
                                      )
                    ),
                    // WINDOWS STORE LANGUAGES
                    array(
                      "param_name"    => "appstore_windows_store_language",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Available Languages", 'plethora-framework'),      
                      "value"         => array(
                        "English"             => "English",
                        "Arabic"              => "Arabic",
                        "Belarusian"          => "Belarusian",
                        "Bengali"             => "Bengali",
                        "Bosnian"             => "Bosnian",
                        "Brazilian"           => "Brazilian",
                        "Bulgarian"           => "Bulgarian",
                        "Chinese_Simplified"  => "Chinese_Simplified",
                        "Chinese_Traditional" => "Chinese_Traditional",
                        "Chinese"             => "Chinese",
                        "Croatian"            => "Croatian",
                        "Czech"               => "Czech",
                        "Danish"              => "Danish",
                        "Dutch"               => "Dutch",
                        "Estonian"            => "Estonian",
                        "Filipino"            => "Filipino",
                        "Finnish"             => "Finnish",
                        "French"              => "French",
                        "German"              => "German",
                        "Greek"               => "Greek",
                        "Hebrew"              => "Hebrew",
                        "Hindi"               => "Hindi",
                        "Hungarian"           => "Hungarian",
                        "Indonesian"          => "Indonesian",
                        "Italian"             => "Italian",
                        "Japanese"            => "Japanese",
                        "Korean"              => "Korean",
                        "Latvian"             => "Latvian",
                        "Lithuanian"          => "Lithuanian",
                        "Malay"               => "Malay",
                        "Norwegian"           => "Norwegian",
                        "Polish"              => "Polish",
                        "Portuguese"          => "Portuguese",
                        "Romanian"            => "Romanian",
                        "Russian"             => "Russian",
                        "Serbian"             => "Serbian",
                        "Slovak"              => "Slovak",
                        "Slovenian"           => "Slovenian",
                        "Spanish"             => "Spanish",
                        "Swahili"             => "Swahili",
                        "Swedish"             => "Swedish",
                        "Thai"                => "Thai",
                        "Turkish"             => "Turkish",
                        "Ukranian"            => "Ukranian"
                        ),
                      "admin_label"   => false,                                              
                      "dependency"    => array( 
                                          'element' => 'appstore_brand', 
                                          'value'   => array('windows_store'),  
                                      )
                    ),
                    // AMAZON STORE BADGE SELECTION
                    array(
                      "param_name"    => "appstore_amazon_store_badges",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Select Badge Type", 'plethora-framework'),      
                      "value"         => array(
                        'Get It on Amazon (black)'    => 'get_black',
                        'Get It on Amazon (grey)'     => 'get_grey',
                        'Get It on Amazon (white)'    => 'get_white',
                        'Apps (black)'                => 'apps_black',
                        'Apps (grey)'                 => 'apps_grey',
                        'Apps (white)'                => 'apps_white',
                        'Available at Amazon (black)' => 'available_black',
                        'Available at Amazon (grey)'  => 'available_grey',
                        'Available at Amazon (white)' => 'available_white',
                        'Single Icon'                 => 'single_icon'
                        ),
                      "admin_label"   => false,                                              
                      "dependency"    => array( 
                                          'element' => 'appstore_brand', 
                                          'value'   => array('amazon_apps'),  
                                      )
                    ),
                    // BUTTON HEIGHT
                    array(
                      "param_name"    => "appstore_button_height",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Select the button height", 'plethora-framework'),      
                      "value"         => array(
                        '45px'   => '45',
                        '60px'   => '60'
                        ),
                      "admin_label"   => false,                                              
                    ),
                    array(
                      "param_name"    => "button_link",
                      "type"          => "vc_link",
                      "holder"        => "",
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Button link", 'plethora-framework'),
                      "value"         => '#',
                      "admin_label"   => false,                                               
                    ),
                    array(
                      "param_name"    => "button_align",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Button align", 'plethora-framework'),      
                      "value"         => array(
                        'Left'   => 'text-left',
                        'Center' => 'text-center',
                        'Right'  => 'text-right'
                        ),
                      "admin_label"   => false,                                              
                    ),
      );

      return $params;
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
        extract( shortcode_atts( array( 
          'appstore_brand'                  => 'google_play',
          'appstore_google_play_language'   => 'en',
          'appstore_google_play_badges'     => 'generic',
          'appstore_button_height'          => '45',
          'appstore_apple_store_badges'     => 'download',
          'appstore_apple_store_language'   => 'us-uk',
          'appstore_windows_store_language' => 'English',
          'appstore_windows_store_badges'   => 'black',
          'appstore_amazon_store_badges'    => 'get_black',
          'button_link'                     => '',
          'button_align'                    => 'text-left'
        ), $atts ) );

        $button_link        =  self::vc_build_link($button_link);
        $button_link['url'] = !empty( $button_link['url'] ) ? $button_link['url'] : '#';

        // PLACE ALL VALUES IN 'SHORTCODE_ATTS' VARIABLE
        $shortcode_atts = array (
                            'appstore_brand'         => $appstore_brand,
                            'appstore_button_height' => $appstore_button_height,
                            'btn_url'                => esc_url( $button_link['url'] ),
                            'btn_title'              => esc_attr( $button_link['title'] ),
                            'btn_align'              => $button_align,
                            'btn_target'             => !empty( $button_link['target'] ) ? esc_attr( $button_link['target'] ) : '_self'
                          );
        // GOOGLE PLAY
        if ( $appstore_brand == "google_play"){
          $google_play_src = "https://developer.android.com/images/brand/";
          $google_play_src .= $appstore_google_play_language;
          $google_play_src .= "_";
          $google_play_src .= $appstore_google_play_badges;
          $google_play_src .= "_rgb_wo_";
          $google_play_src .= $appstore_button_height;
          $google_play_src .= ".png";

          $shortcode_atts["google_play_src"] = $google_play_src;
          $shortcode_atts["google_play"]     = true;

        } elseif ( $appstore_brand == "windows_store" ) {

          $icon = strrpos( $appstore_windows_store_badges, "ICON_" );
          $appstore_windows_store_badges = str_replace( "ICON_", "", $appstore_windows_store_badges );
          $windows_store_src = PLE_FLIB_FEATURES_URI . "/shortcode/appstorebutton/assets/img/windows/";
          $windows_store_src .= $appstore_windows_store_language . "_wstore_${appstore_windows_store_badges}_";
          $windows_store_src .= ( $icon === false )? "258x67" : "40x40";
          $windows_store_src .= ".png";
          $shortcode_atts["windows_store_src"] = $windows_store_src;
          $shortcode_atts["windows_store"]     = true;

        } elseif ( $appstore_brand == "apple_store" ){

          if ( $appstore_apple_store_badges == "download" ){
            $apple_store_src = PLE_FLIB_FEATURES_URI . "/shortcode/appstorebutton/assets/svg/apple/";
            $apple_store_src .= "Download_on_the_App_Store_Badge_";
            $apple_store_src .= strtoupper($appstore_apple_store_language);
            $apple_store_src .= "_135x40.svg";
          } else {
            $apple_store_src = PLE_FLIB_FEATURES_URI . "/shortcode/appstorebutton/assets/svg/apple/App_Store.svg";
          }
          $shortcode_atts["apple_store_src"] = $apple_store_src;
          $shortcode_atts["apple_store"]     = true;

        // PLEDEV: ADD AMAZON APPS BUTTONS
        } elseif ( $appstore_brand == "amazon_apps" ){

          $amazon_store_src             = PLE_FLIB_FEATURES_URI . "/shortcode/appstorebutton/assets/png/amazon/";
          $appstore_amazon_store_badges = explode( "_", $appstore_amazon_store_badges );
          $badge                        = $appstore_amazon_store_badges[0];
          $badge_color                  = $appstore_amazon_store_badges[1];

          switch ( $badge ) {
            case 'single':
              $amazon_store_src .= "single_icon_" . $appstore_button_height . ".png";
              break;
            case 'get':
              $amazon_store_src .= "get_" . $badge_color . "_" . $appstore_button_height . ".png";
              break;
            case 'apps':
              $amazon_store_src .= "apps_" . $badge_color . "_" . $appstore_button_height . ".png";
              break;
            case 'available':
              $amazon_store_src .= "available_" . $badge_color . "_" . $appstore_button_height . ".png";
              break;
          }

          $shortcode_atts["amazon_store_src"] = $amazon_store_src;
          $shortcode_atts["amazon_store"]     = true;
        }

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

    }
	}
	
 endif;