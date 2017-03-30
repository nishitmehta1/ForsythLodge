<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Theme Functions file 

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS

// specify the $content_width variable out of Plethora_Setup
if ( ! isset( $content_width ) ) { $content_width = 960; }

class Plethora_Setup { 

    public $theme_slug;         // THEME SLUG
    public $theme_name;         // THEME NAME
    public $theme_ver;          // THEME VERSION
    public $theme_plugins;      // THEME REQUIRED/RECOMMENDED PLUGINS

    public function __construct() {

        // Perform some PHP version diagnostics check before anything else
        $php_version_approved = $this->approve_php_version();

        // Instantiate the theme class, if Plethora Framework is installed and PHP version diagnostics are fine
        if ( $php_version_approved ) {

            // Set theme basic Info
            $parent_dir_name = basename( get_template_directory() ); // covers possible directory name change
            $theme = wp_get_theme( $parent_dir_name );          // always get info by parent theme directory name
            $this->theme_slug    = $theme->get( 'TextDomain' );
            $this->theme_name    = $theme->get( 'Name' );
            $this->theme_ver     = $theme->get( 'Version' );
            $this->theme_plugins = array(      
                    'plethora-featureslib' => array( 'version' => '1.5.6' ),
                    'js_composer'          => array( 'version' => '5.0.1' ),
                    'contact-form-7'       => array( 'version' => '4.5' ),
                    'mailchimp-for-wp'     => array( 'version' => '4.0.4' ),
                    'wp-google-maps'       => array( 'version' => '6.3.18' ),
                    'envato-market'        => array( 'version' => '1.0' ),
            );

            // WP Features Config
            add_theme_support( 'post-thumbnails' );
            add_theme_support( 'title-tag' );
            add_theme_support( 'post-formats', array( 'image', 'video', 'audio', 'link' ) );
            add_theme_support( 'automatic-feed-links' );

            // DIR Constants
            define( 'PLE_THEME_DIR',              get_template_directory() );           // Theme folder
            define( 'PLE_THEME_INCLUDES_DIR',     PLE_THEME_DIR . '/includes' );            // Theme includes folder
            define( 'PLE_THEME_ASSETS_DIR',       PLE_THEME_DIR . '/assets' );              // Theme assets folder
            define( 'PLE_THEME_JS_DIR',           PLE_THEME_ASSETS_DIR . '/js' );           // Theme assets JavaScript folder
            define( 'PLE_THEME_FEATURES_DIR',     PLE_THEME_DIR . '/features' );            // Theme features folder
            define( 'PLE_THEME_TEMPLATES_DIR',    PLE_THEME_DIR . '/templates' );           // Theme template parts folder 
            define( 'PLE_CHILD_DIR',              get_stylesheet_directory() );         // Child theme folder
            define( 'PLE_CHILD_ASSETS_DIR',       PLE_CHILD_DIR . '/assets' );              // Child theme assets folder
            define( 'PLE_CHILD_JS_DIR',           PLE_CHILD_ASSETS_DIR . '/js' );           // Child theme assets JavaScript folder
            define( 'PLE_CHILD_FEATURES_DIR',     PLE_CHILD_DIR . '/features' );            // Child theme includes folder
            define( 'PLE_CHILD_TEMPLATES_DIR',    PLE_CHILD_DIR . '/templates' );           // Child theme template parts folder 

            // URI Constants
            define( 'PLE_THEME_URI',              get_template_directory_uri() );       // Theme folder
            define( 'PLE_THEME_INCLUDES_URI',     PLE_THEME_URI . '/includes' );            // Theme includes folder
            define( 'PLE_THEME_ASSETS_URI',       PLE_THEME_URI . '/assets' );              // Theme assets folder
            define( 'PLE_THEME_JS_URI',           PLE_THEME_ASSETS_URI . '/js' );           // Assets JavaScript folder
            define( 'PLE_THEME_FEATURES_URI',     PLE_THEME_URI . '/features' );            // Theme features folder
            define( 'PLE_THEME_TEMPLATES_URI',    PLE_THEME_URI . '/templates' );           // Theme template parts folder 
            define( 'PLE_CHILD_URI',              get_stylesheet_directory_uri() );     // Child theme folder
            define( 'PLE_CHILD_ASSETS_URI',       PLE_CHILD_URI . '/assets' );              // Child theme assets folder
            define( 'PLE_CHILD_JS_URI',           PLE_CHILD_ASSETS_URI . '/js' );           // Child theme assets JavaScript folder
            define( 'PLE_CHILD_FEATURES_URI',     PLE_CHILD_URI . '/features' );            // Child theme includes folder
            define( 'PLE_CHILD_TEMPLATES_URI',    PLE_CHILD_URI . '/templates' );           // Child theme template parts folder 

            // Load TGM class
            require_once( PLE_THEME_INCLUDES_DIR . '/core/helpers/plethora-tgm.php' );
            // Load Plethora_Theme class, along with the abstract Plethora class
            require_once( PLE_THEME_INCLUDES_DIR . '/core/plethora.php' );
            require_once( PLE_THEME_INCLUDES_DIR . '/theme.php' );
            
            // Create the theme object
            global $plethora_theme;
            $plethora_theme = new Plethora_Theme( $this->theme_slug, $this->theme_name, $this->theme_ver );

            // TGM configuration
            add_action( 'tgmpa_register', array( $this, 'tgm_init' ) );

            // Tasks performed after theme update
            $this->after_update();

            // Theme adjustments if the library plugin is inactive
            if ( ! Plethora_Theme::is_library_active() ) {

                // Add support for post and page post types ( necessary for content to be displayed )
                add_filter('plethora_supported_post_types', array($this, 'library_inactive_post_type_support' ));

                // Enqueue Google fonts manually
                add_action( 'wp_enqueue_scripts', array($this, 'library_inactive_google_fonts' ), 5);
            }

        } else {

            // Handle frontend error message
            if ( !is_admin() ) {

                $plethora_link  = 'http://plethorathemes.com/blog/dropping-support-for-php-5-3-x/';
                $wp_link        = 'https://wordpress.org/about/requirements/';
                $title          = esc_html__( 'This installation is running under an obsolete PHP version *', 'hotel-xenia' ) ;
                $output         = '<h1>'. $title .'</h1>';
                $output        .= '<p>';
                $output        .= esc_html__( 'To continue working with this theme, you have to upgrade your PHP to 5.4 or newer version.', 'hotel-xenia' );
                $output        .= esc_html__( 'Unfortunately we cannot ignore the fact that this PHP version is considered obsolete, non secure and with poor overall performance.', 'hotel-xenia' ) .'<br>';
                $output        .= '</p>';
                $output        .= '<p>';
                $output        .= '<strong>'. esc_html__( 'Please help us to deliver high quality and secure products...contact your host and ask for a switch to PHP 5.4 or newer.', 'hotel-xenia' ) .'</strong>';
                $output        .= esc_html__( 'This is a simple procedure that any decent hosting company should provide hassles-free. This restriction will disappear after switching to PHP 5.4 or newer.', 'hotel-xenia' );
                $output        .= '</p>';
                $output        .= '<h4>'. esc_html__( 'Read more: ', 'hotel-xenia' ) .'</h4>';
                $output        .= '<ul>';
                $output        .= '<li><a href="'. esc_url( $plethora_link ) .'" target="_blank"><strong>'. esc_html__( 'Plethora Themes is dropping support for PHP 5.3.x', 'hotel-xenia' ) .'</strong></a></li>';
                $output        .= '<li><a href="'. esc_url( $wp_link ) .'" target="_blank"><strong>'. esc_html__( 'WordPress recommended host configuration', 'hotel-xenia' ) .'</strong></a></li>';
                $output        .= '</ul>';
                $output        .= '<h6>'. esc_html__( '* This message concerns this website\'s administration...if you are a visitor, just ignore this.', 'hotel-xenia' ) .'</h6>';

                wp_die( $output, $title );
            }
        }
    }

    /**
    * Check if PHP Version is less than 5.4
    */
    public function approve_php_version() {

        $approve_php_version = false;

        if ( version_compare(PHP_VERSION, '5.4.0') >= 0 ) {
            
            $approve_php_version = true;

        } else {

            $approve_php_version = false;
            add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
        } 

        return $approve_php_version;
    }

    /**
    * Admin notice for PHP versions earlier than 5.4
    */
    public function php_version_notice() {

        if ( isset( $_GET['plethora_php_version_notice'] ) && sanitize_key( $_GET['plethora_php_version_notice'] ) === 'hide' ) {

            set_transient( 'plethora_php_version_notice', 'hide', HOUR_IN_SECONDS );
        }

        $notice_status = get_transient( 'plethora_php_version_notice' );

        if ( $notice_status !== 'hide' ) {

            $plethora_link  = 'http://plethorathemes.com/blog/dropping-support-for-php-5-3-x/';
            $wp_link        = 'https://wordpress.org/about/requirements/';
            $output         = '<h4 style="margin:0 0 10px;">'. esc_html__( 'Your installation is running under PHP ', 'hotel-xenia' ) ;
            $output        .= '<strong>'. PHP_VERSION .'</strong> '.'</h4>';
            $output        .= esc_html__( 'To continue working with this theme, you have to upgrade your PHP to 5.4 or newer version.', 'hotel-xenia' ) .'<br>';
            $output        .= esc_html__( 'Unfortunately we cannot ignore the fact that this PHP version is considered obsolete, non secure and with poor overall performance.', 'hotel-xenia' ) .'<br>';
            $output        .= '<strong>'. esc_html__( 'Please help us to deliver high quality and secure products...contact your host and ask for a switch to PHP 5.4 or newer.', 'hotel-xenia' ) .'</strong>' .'<br>';
            $output        .= esc_html__( 'This is a simple procedure that any decent hosting company should provide hassles-free. This restriction will disappear after switching to PHP 5.4 or newer.', 'hotel-xenia' );
            $output        .= '<p>';
            $output        .= '<a href="'. esc_url( $plethora_link ) .'" target="_blank"><strong>'. esc_html__( 'Read more on our blog', 'hotel-xenia' ) .'</strong></a> | ';
            $output        .= '<a href="'. esc_url( $wp_link ) .'" target="_blank"><strong>'. esc_html__( 'Read more on WordPress recommended host configuration', 'hotel-xenia' ) .'</strong></a> | ';
            $output        .= '<a href="'. esc_url( admin_url( '/') ) .'?plethora_php_version_notice=hide"><strong>'. esc_html__( 'Dismiss this notice', 'hotel-xenia' ) .'</strong></a>';
            $output        .= '</p>';
            echo '<div class="notice notice-error is-dismissible"><p>'. $output .'</p></div>'; 
        }
    }

    /**
    * Initiates TGM class
    * Hooked @ 'tgmpa_register'
    */
    public function tgm_init() {

        if ( is_admin() ) { // no need if not in admin
            $config            = array(
                'domain'            => $this->theme_slug,           // Text domain - likely want to be the same as your theme.
                'default_path'      => '',                          // Default absolute path to pre-packaged plugins
                'menu'              => 'install-required-plugins',  // Menu slug
                'has_notices'       => true,                        // Show admin notices or not
                'is_automatic'      => false,                       // Automatically activate plugins after installation or not
                'message'           => '',                          // Message to output right before the plugins table
                'strings'           => array(
                    'page_title'                      => esc_html__( 'Install Required Plugins', 'hotel-xenia' ),
                    'menu_title'                      => esc_html__( 'Install Plugins', 'hotel-xenia' ),
                    'installing'                      => esc_html__( 'Installing Plugin: %s', 'hotel-xenia' ), // %1$s = plugin name
                    'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'hotel-xenia' ),
                    'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'hotel-xenia' ), 
                    'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'hotel-xenia' ), 
                    'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'hotel-xenia' ), // %1$s = plugin name(s)
                    'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'hotel-xenia' ), 
                    'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s. Note that recommended plugins are usually associated with some features display, however they are not necessary if you don\'t plan to use them.', 'The following recommended plugins are currently inactive: %1$s. Note that recommended plugins are usually associated with some features display, however they are not necessary if you don\'t plan to use them.', 'hotel-xenia' ), // %1$s = plugin name(s)
                    'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'hotel-xenia'), // %1$s = plugin name(s)
                    'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'hotel-xenia' ), // %1$s = plugin name(s)
                    'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'hotel-xenia' ), // %1$s = plugin name(s)
                    'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'hotel-xenia' ),
                    'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'hotel-xenia' ),
                    'return'                          => esc_html__( 'Return to Required Plugins Installer', 'hotel-xenia' ),
                    'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'hotel-xenia' ),
                    'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'hotel-xenia' ), // %1$s = dashboard link
                    'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
                )
            );
            
            $plugins = $this->tgm_get_plugins();

            if ( !empty( $plugins ) ) {

                tgmpa( $plugins, $config );
            }
        }
    }

    /**
    * Includes all TGM plugins index, along with their configuration
    * It returns an array merged with $this->theme_plugins variable config,
    * and it's ready for TGM class initiation
    */
    public function tgm_get_plugins() {

        // REQUIRED: Plethora Features Library
        $plugins['plethora-featureslib'] = array(
                'name'               => esc_html__( 'Plethora Features Library', 'hotel-xenia' ),     // PLUGIN NAME
                'slug'               => 'plethora-featureslib',                // PLUGIN SLUG (Typically: folder name)
                'source'             => PLE_THEME_DIR . '/includes/plugins/plethora-featureslib.zip', // PLUGIN SOURCE
                'required'           => true, // If false, the plugin is only 'recommended' instead of required
        );

        // REQUIRED: WPBakery Visual Composer
        $plugins['js_composer'] = array(
                'name'     => esc_html__( 'WPBakery Visual Composer', 'hotel-xenia' ),
                'slug'     => 'js_composer',
                'source'   => PLE_THEME_DIR . '/includes/plugins/js_composer.zip',
                'required' => true,
        );

        // SUGGESTED: Slider Revolution
        $plugins['revslider'] = array(
                'name'     => esc_html__( 'Slider Revolution', 'hotel-xenia' ),
                'slug'     => 'revslider',
                'source'   => PLE_THEME_DIR . '/includes/plugins/revslider.zip',
                'required' => false,
        );

        // SUGGESTED: Contact Form 7
        $plugins['contact-form-7'] = array(
                'name'     => esc_html__( 'Contact Form 7', 'hotel-xenia' ),
                'slug'     => 'contact-form-7',
                'required' => false,
        );

        // SUGGESTED: MailChimp for WordPress
        $plugins['mailchimp-for-wp'] = array(
                'name'     => esc_html__( 'MailChimp for WordPress', 'hotel-xenia' ),
                'slug'     => 'mailchimp-for-wp',
                'required' => false,
        );

        // SUGGESTED: Instagram Feed
        $plugins['instagram-feed'] = array(
                'name'     => esc_html__( 'Instagram Feed', 'hotel-xenia' ),
                'slug'     => 'instagram-feed',
                'required' => false,
        );

        // SUGGESTED: WP Google Maps
        $plugins['wp-google-maps'] = array(
                'name'     => esc_html__( 'WP Google Maps', 'hotel-xenia' ),
                'slug'     => 'wp-google-maps',
                'required' => false,
        );

        // SUGGESTED: Envato Market
        $plugins['envato-market'] = array(
                'name'     => esc_html__( 'Envato Market', 'hotel-xenia' ),
                'slug'     => 'envato-market',
                'source'   => PLE_THEME_DIR . '/includes/plugins/envato-market.zip',
                'required' => false,
        );
       
        
        $tgm_plugins = array();
        $theme_plugins = apply_filters( 'plethora_theme_plugins', $this->theme_plugins );
        foreach ( $plugins as $plugin_slug => $plugin_tgm_config ) {

            if ( !empty( $theme_plugins[$plugin_slug]['version'] ) ) {

                $plugin_tgm_config['version'] = $theme_plugins[$plugin_slug]['version'];
                $tgm_plugins[] = $plugin_tgm_config;
            }
        }
        return $tgm_plugins;
    }

    /**
    * The method compares theme saved version with this one running. 
    * If different, it executes all actions set right after theme update
    */
    public function after_update() { 

      $theme_version_db = get_option( OPTNAME_THEME_VER, false );
      if ( $theme_version_db && version_compare( $this->theme_ver, $theme_version_db ) !== 0 ) { 

        // Recovers TGM notices, even if the user has dismissed this. 
        // MUST be done on every theme update, to make sure the current user gets a notice about the Plethora Framework plugin update
        $deleted = delete_metadata( 'user', null, 'tgmpa_dismissed_notice_tgmpa', null, true );

        ## START: Add any theme update actions here!
        do_action( 'plethora_after_update' );
        ## FINISH

        // After done with all actions, we update saved theme version
        $is_updated = update_option( OPTNAME_THEME_VER, $this->theme_ver );
        
      } elseif ( ! $theme_version_db ) {

        // Create saved theme version ( for version switches only )
        $is_saved = update_option( OPTNAME_THEME_VER, $this->theme_ver );
        // Initial theme version
        $is_initial = update_option( 'plethora_theme_ver_installed_initial', $this->theme_ver );
      }
    }

    /**
    * Enqueue Google fonts manually
    * Should be hooked only when PFL is inactive
    * Hooked @ 'wp_enqueue_scripts'
    */
    public function library_inactive_google_fonts() {

        wp_enqueue_style( 'Source Sans Pro', $this->library_inactive_font_source_sans_pro(), array(), '1.0.0' ); 
        wp_enqueue_style( 'Playfair Display', $this->library_inactive_font_playfair_display(), array(), '1.0.0' ); 
    }

    /**
    * Returns google fonts url, url encoded for wp_enqueue_style use
    * Translators: If there are characters in your language that are not supported
    * by chosen font(s), translate this to 'off'. Do not translate into your own language.
    */
    public function library_inactive_font_source_sans_pro() {

        $font_url = '';
        if ( 'off' !== _x( 'on', 'Google font: on or off', 'hotel-xenia' ) ) {
            
            $font_url = add_query_arg( 'family', urlencode( 'Source Sans Pro:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic&subset=latin' ), "//fonts.googleapis.com/css" );
        }
        return $font_url;
    }

    /**
    * Returns google fonts url, url encoded for wp_enqueue_style use
    * Translators: If there are characters in your language that are not supported
    * by chosen font(s), translate this to 'off'. Do not translate into your own language.
    */
    public function library_inactive_font_playfair_display() {

        $font_url = '';
        if ( 'off' !== _x( 'on', 'Google font: on or off', 'hotel-xenia' ) ) {
            
            $font_url = add_query_arg( 'family', urlencode( 'Playfair Display:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic&subset=latin' ), "//fonts.googleapis.com/css" );
        }
        return $font_url;
    }

    /**
    * Add support for post/page if the library plugin is inactive
    * Should be hooked only when PFL is inactive
    * Hooked @ 'plethora_supported_post_types'
    */
    public function library_inactive_post_type_support( $posttypes ) {

      $posttypes[] = 'post';
      $posttypes[] = 'page';
      array_unique($posttypes);
      return $posttypes;
    }

}

$setup = new Plethora_Setup;