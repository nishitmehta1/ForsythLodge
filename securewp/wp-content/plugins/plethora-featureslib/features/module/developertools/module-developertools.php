<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Advanced theme options tab

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Developertools') ) {

	/**
	 */
	class Plethora_Module_Developertools {


		public static $feature_title         = "Developer Tools";					// FEATURE DISPLAY TITLE
		public static $feature_description   = "Integration module for developer tools & options";	// FEATURE DISPLAY DESCRIPTION 
		public static $theme_option_control  = false;											// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL? 
		public static $theme_option_default	 = false;											// DEFAULT ACTIVATION OPTION STATUS
		public static $theme_option_requires = array();											// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct	 = true;											// DYNAMIC CLASS CONSTRUCTION? 
		public static $dynamic_method		 = false;											// ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

		function __construct(){

		  if ( is_admin() ) { 

		      // Set theme options tab for media panel
		      add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_features_options_tab'), 998);
		      add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_devcomments_options_tab'), 999);
	      }
		}

		public static function get_features_options(){

			$controllers = Plethora_Theme::get_controllers();
			
			$config = array();
			foreach ( $controllers as $key=>$controller ) {

			    $config[] = array(
			              'id'=>'header-features-'.$controller['slug'].'-start',
			              'type' => 'section',
				      	  'indent' => true,
			              'title' => $controller['title'],
			              'subtitle' => $controller['description']
			            );
			    $features = Plethora_Theme::get_features( array( 'controller' => $controller['slug'] ) );
			    foreach ( $features as $key=>$feature ) {

			      	if ( !empty($feature['theme_option_control_config'])) {

			        	$config[] = $feature['theme_option_control_config'];
			    	}
			    }
			    $config = apply_filters( 'plethora_'.$controller['slug'].'_features_options', $config, $controller['slug'] );
			    $config[] = array(
			              'id'=>'header-features-'.$controller['slug'].'-end',
			              'type' => 'section',
				      	  'indent' => false,
			            );

			}
			return apply_filters( 'plethora_features_options', $config );
		}


	    static function theme_devcomments_options_tab( $sections ) { 


			$adv_settings = array();

			$adv_settings[] = array(
			              'id'=>'dev-options-start',
			              'type' => 'section',
				      	  'indent' => true,
			              'title' => 'Development Options',
			            );
		    $adv_settings[] = array(
				'id'          => THEMEOPTION_PREFIX . 'dev',
				'type'        => 'button_set',
				'title'       => esc_html__('Development Mode', 'plethora-framework'),
				'options'     => array( 1 => esc_html__('Enable', 'plethora-framework'), 0 => esc_html__('Disable', 'plethora-framework')),
				'desc'    => sprintf( esc_html__('%1$sNOTICE: Don\'t forget to switch back to Production mode after the final launch of the website!%2$s', 'plethora-framework'), '<strong style="color:red">', '</strong>' ),
				'default'     => 0
			);
			$desc  = esc_html__('Enable template part elements information. This will help you understand how the template system works and give you advanced information on how to customize each element! ', 'plethora-framework');
			$desc .= esc_html__('Note that for security reasons, developer comments will be output only to logged users with options editing capabilities. ', 'plethora-framework');
			$desc .= esc_html__('For each element, the following information is included as an HTML comment: ', 'plethora-framework');
			$desc .= '<ol>';
			$desc .= '<li>' . sprintf( esc_html__('%1$sLayout container slug%2$s', 'plethora-framework'), '<strong>', '</strong>' ) . '</li>';
			$desc .= '<li>' . sprintf( esc_html__('%1$sDisplay order%2$s within the container', 'plethora-framework'), '<strong>', '</strong>' ) . '</li>';
			$desc .= '<li>' . sprintf( esc_html__('If this is a %1$stemplate part%2$s file or a %1$smethod%2$s or a direct %1$sHTML markup%2$s output', 'plethora-framework'), '<strong>', '</strong>' ) . '</li>';
			$desc .= '<li>' . sprintf( esc_html__('Directions on how to %1$sedit, update or remove the element%2$s on your functions.php file', 'plethora-framework'), '<strong>', '</strong>' ) . '</li>';
			$desc .= '<li>' . sprintf( esc_html__('Directions on how to %1$splace your template part or HTML markup BEFORE and AFTER the element%2$s on your functions.php file', 'plethora-framework'), '<strong>', '</strong>' ) . '</li>';
			$desc .= '<li>' . sprintf( esc_html__('All %1$soptions passed to the template element%2$s and directions on how to edit them or add your own on your functions.php file', 'plethora-framework'), '<strong>', '</strong>' ) . '</li>';
			$desc .= '</ol>';
			$adv_settings[] = array(
				'id'      => THEMEOPTION_PREFIX . 'dev-customization_info',
				'type'    => 'button_set', 
				'required'=> array( THEMEOPTION_PREFIX .'dev','=', 1),						
				'title'   => esc_html__('HTML Comments // Template Elements', 'plethora-framework'),
				'desc'    => $desc,
				'default' => 'enable',
				'options' => array(
						'enable'  => esc_html__('Enable', 'plethora-framework'),
						'disable' => esc_html__('Disable', 'plethora-framework'),
						),
				);
			$adv_settings[] = array(
				'id'      => THEMEOPTION_PREFIX . 'dev-page_info',
				'type'    => 'button_set', 
				'required'=> array( THEMEOPTION_PREFIX .'dev','=', 1),						
				'title'   => esc_html__('HTML Comments // Page Info', 'plethora-framework'),
				'desc'    => esc_html__('Displays general information about the current page on top of the HTML document source', 'plethora-framework'),
				'default' => 'enable',
				'options' => array(
						'enable'  => esc_html__('Enable', 'plethora-framework'),
						'disable' => esc_html__('Disable', 'plethora-framework'),
						),
				);
			$adv_settings[] = array(
				'id'      => THEMEOPTION_PREFIX . 'dev-layout',
				'type'    => 'button_set', 
				'required'=> array( THEMEOPTION_PREFIX .'dev','=', 1),						
				'title'   => esc_html__('HTML Comments // Layout Checkpoints', 'plethora-framework'),
				'desc'    => esc_html__('Enable start/end layout checkpoints information. This will help you separate easily the most important parts of the page on the html source view!', 'plethora-framework'),
				'default' => 'enable',
				'options' => array(
						'enable'  => esc_html__('Enable', 'plethora-framework'),
						'disable' => esc_html__('Disable', 'plethora-framework'),
						),
				);
			$adv_settings[] = array(
			              'id'=>'dev-options-end',
			              'type' => 'section',
				      	  'indent' => false,
			            );


    		$desc = esc_html__('Those tools will help the developer to:' , 'plethora-framework');
    		$desc .= '<ol>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Produce unminified JS & CSS output ( where applied ) when developer mode is enabled. This is very helpful in terms of debugging.' , 'plethora-framework') . '</li>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Understand faster how the theme layout and template parts work ( and how you can easily customize them ) by enabling related comments inside HTML source of each page.' , 'plethora-framework') . '</li>';
    		$desc .= '</ol>';

    		$devmode_on = Plethora_Theme::is_developermode() ? ' <span style="color:aqua;" title="'. esc_html( 'Developer mode is enabled!', 'plethora-framework') .'">'. esc_html( 'DEV MODE ENABLED', 'plethora-framework') .'</span>' : '';
			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Developer Tools', 'plethora-framework') . $devmode_on,
				'desc'       => $desc,
				'heading'    => esc_html__('DEVELOPER TOOLS', 'plethora-framework'),
				'fields'     => $adv_settings
				);
			return $sections;
	    }

	    static function theme_features_options_tab( $sections ) { 

		    $adv_settings = self::get_features_options();
    		$desc = esc_html__('Deliver a light installation website by safely disabling several functionality that you will not actually use.' , 'plethora-framework') . '</li>';
    		$desc .= '<br><br>'. esc_html__('Please note the following' , 'plethora-framework') . '</li>';
    		$desc .= '<ol>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Deactivating a post type doesn\'t mean that its posts will be deleted from the database too. When you activate the post type again, your posts will be present!' , 'plethora-framework') . '</li>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('The Post Types manager will display custom post types created from third party plugins ( i.e. the "Custom Post Type UI" plugin). Deactivating third party CPTs will just remove the related archive/single view configuration tabs on THEME OPTIONS > CONTENT . In simple words, it will just remove any frontend display configuration, not the post type itself.' , 'plethora-framework') . '</li>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Deactivating a shortcode doesn\'t mean that all of its instances will be removed too. Unfortunately you will have to remove manualy those shortcodes on each page they are displayed.' , 'plethora-framework') . '</li>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Deactivating some features might cause the deactivation of other features affected by it. In example, deactivating the \'Profile Post Type\' will force the deactivation of the \'Profiles Grid\' shortcode too.' , 'plethora-framework') . '</li>';
    		$desc .= '</ol>';

			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Features Library', 'plethora-framework'),
				'desc'       => $desc,
				'heading'    => esc_html__('PLETHORA FEATURES LIBRARY ACTIVATION / DEACTIVATION', 'plethora-framework'),
				'fields'     => $adv_settings
				);

			return $sections;
	    }
	}
}