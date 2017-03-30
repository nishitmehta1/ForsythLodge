<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Service Post Type Config Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Posttype_Service') && !class_exists('Plethora_Posttype_Service_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-featureslib/features/posttype/service/posttype-service.php
   */
  class Plethora_Posttype_Service_Ext extends Plethora_Posttype_Service { 

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
                  'theme_options_default' => 'no_sidebar_narrow',
                  'metabox'               => true,
                  'metabox_default'       => NULL
                );
                $config[] = array( 
                  'id'                    => 'sidebar', 
                  'theme_options'         => true, 
                  'theme_options_default' => 'sidebar-services',
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
                  'theme_options_default' => 'black_section',
                  'metabox'               => true,
                  'metabox_default'       => NULL
                );
                $config[] = array( 
                  'id'                    => 'content-align', 
                  'theme_options'         => true, 
                  'theme_options_default' => 'text-center',
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
                  'id'                    => 'overlay-title', 
                  'theme_options'         => true, 
                  'theme_options_default' => 1,
                  'metabox'               => true,
                  'metabox_default'       => NULL
                );
                $config[] = array( 
                  'id'                    => 'overlay-title-text', 
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
                $config[] = array( 
                  'id'                    => 'excerpt', 
                  'theme_options'         => true, 
                  'theme_options_default' => true,
                  'metabox'               => true,
                  'metabox_default'       => NULL
                );
                $config[] = array( 
                  'id'                    => 'divider', 
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
                  'theme_options_default' => 'image',
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