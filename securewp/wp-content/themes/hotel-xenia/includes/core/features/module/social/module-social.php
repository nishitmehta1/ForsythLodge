<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Social Icons manager

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Social') ) {

	/**
	 */
	class Plethora_Module_Social {


		// Feature display title  (string)
		public static $feature_title        = "Social Icons Manager";
		// Feature display description (string)
		public static $feature_description  = "Add your custom scripts";
		// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_control = false;
		// Default activation option status ( boolean )
		public static $theme_option_default	= false;
		// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $theme_option_requires= array();
		// Dynamic class construction ? ( boolean )
		public static $dynamic_construct	= true;
		// Additional method invocation ( string/boolean | method name or false )
		public static $dynamic_method		= false;

		function __construct(){
		  if ( is_admin() ) { 
		      // Set theme options tab for media panel
		      add_filter( 'plethora_themeoptions_general', array( $this, 'theme_options_tab'), 45);
	      }
		}

	  /**
	   * Theme options tab setup
	   * @since 1.0
	   *
	   */
	    static function theme_options_tab( $sections ) { 

			$adv_settings = array();
		    $adv_settings[] = array(
	            'id'         =>  THEMEOPTION_PREFIX .'social',
	            'type'       => 'repeater',
	            'title'      => esc_html__( 'Social Icons', 'plethora-framework' ),
				'subtitle'    => esc_html__('Add social icons for general use on your website', 'plethora-framework'),
	            'group_values' => true, // Group all fields below within the repeater ID
	            'item_name' => 'social icon', // Add a repeater block name to the Add and Delete buttons
	            // 'bind_title' => 'sidebar', // Bind the repeater block title to this field ID
	            //'static'     => 2, // Set the number of repeater blocks to be output
	            //'limit' => 2, // Limit the number of repeater blocks a user can create
	            'sortable' => true, // Allow the users to sort the repeater blocks or not
	            'fields'     => array(
	                array(
						'id'          => 'social_title',
						'type'        => 'text',
						'title'       => esc_html__( 'Title', 'plethora-framework' ),
						'placeholder' => esc_html__( 'Icon title', 'plethora-framework' ),
	                ),
	                array(
						'id'    => 'social_icon',
						'type'  => 'icons',
						'title' => esc_html__( 'Icon', 'plethora-framework' ),
						'options' => Plethora_Module_Icons::get_options_array(),
	                ),
	                array(
						'id'          => 'social_url',
						'type'        => 'text',
						'title'       => esc_html__( 'URL', 'plethora-framework' ),
						'placeholder' => esc_html__( 'Icon url', 'plethora-framework' ),
	                ),
	            ),
				'default' => method_exists('Plethora_Theme', 'default_socialicons' ) ? Plethora_Theme::default_socialicons() : self::default_socialicons()
			);

			// check that icon libraries are working normally...if not, produce a notice!
	    	$desc = '';
			$check_iconslibrary  = get_option( GENERALOPTION_PREFIX .'module_icons_diagnostics_wpremote', '' );
			if ( !empty( $check_iconslibrary ) ) {

				$desc = '<strong style="color:red">';
			    $desc .= esc_html__('IMPORTANT NOTICE: Icon libraries are not working as expected. This affects the functionality of this feature as well. ', 'plethora-framework');
			    $desc .= esc_html__('After resolving the icon libraries feature, return to this tab and click on "Reset Section" button to recover social icons too.', 'plethora-framework');
				$desc .= '</strong>';
		    }

			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Social Icons', 'plethora-framework'),
				'desc'       => $desc,
				'fields'     => $adv_settings
				);

			return $sections;
	    }

	      /**
	       * Set default icon set 
	       * @since 1.0
	       *
	       */
	    static function default_socialicons() {

	        $default_socialicons = array();
	        // IMPORTANT: this is necessary for repeater field...add a line for each record
	        $default_socialicons['redux_repeater_data'] = array(
	                               array( 'title'=> 'twitter' ),
	                               array( 'title'=> 'facebook' ),
	                               array( 'title'=> 'googleplus' ),
	                               array( 'title'=> 'linkedin' ),
	                               array( 'title'=> 'instagram' ),
	                               array( 'title'=> 'skype' ),
	                               array( 'title'=> 'email' ),
	                         );
	        $default_socialicons['social_title'] = array(
	                            'Twitter',
	                            'Facebook',
	                            'Google+',
	                            'LinkedIn',
	                            'Instagram',
	                            'Skype',
	                            'Send Us An Email',
	                          );
	        $default_socialicons['social_icon'] = array(
	                            'fa fa-twitter',
	                            'fa fa-facebook',
	                            'fa fa-google-plus',
	                            'fa fa-linkedin',
	                            'fa fa-instagram',
	                            'fa fa-skype',
	                            'fa fa-envelope',
	                          );
	        $default_socialicons['social_url'] = array(
	                            '#',
	                            '#',
	                            '#',
	                            '#',
	                            '#',
	                            '#',
	                            '#',
	                          );
	        return $default_socialicons;
	    }

		/**
		* Returns icon information
		* @param $return ( 'all': returns all icon data, 'title', 'icon', 'url' )
		* @return mixed ( string / array )
		* @since 1.0
		*
		*/
	    static function get_icon( $icon_slug, $return = '' ) { 

	    	if ( empty( $icon_slug ) ) { return ''; }

	    	$icon = '';
		    $socials   = Plethora_Theme::option( THEMEOPTION_PREFIX .'social', array());
		    if ( !empty( $socials['social_icon'] ) ) {
		    	
			    foreach ($socials['social_icon'] as $key => $social_icon ) {
				    if ( !empty($social_icon) && $social_icon == $icon_slug ) { 

						if ( $return == '' ) { 
							$icon[$icon_slug] = array(
								'title' => $socials['social_title'][$key],
								'icon'  => $socials['social_icon'][$key],
								'url'   => $socials['social_url'][$key]
							);

						} elseif ( $return == 'title' || $return == 'icon' || $return == 'url') { 

							$icon = $socials['social_'. $return][$key];
						}
				    } 
			    }
		    }
		    return $icon;
	    }

		/**
		* Returns all icons information
		* @param $return ( null or 'slug': returns icon slug, 'all': returns all data, )
		* @since 1.0
		*
		*/
	    static function get_icons( $return = 'slug' ) { 

	    	$return = $return === 'slug' || is_null( $return ) ? 'slug' : 'all';
	    	$icons = '';
		    $socials   = Plethora_Theme::option( THEMEOPTION_PREFIX .'social', array());
		    if ( isset($socials['social_icon']) ) { 
			    foreach ($socials['social_icon'] as $key => $social_icon ) {
			    	if ( $return === 'all' ) { 
						$icons[$social_icon] = array(
							'title' => $socials['social_title'][$key],
							'icon'  => $socials['social_icon'][$key],
							'url'   => $socials['social_url'][$key]
						);
					} else {
						$icons[$social_icon] = array(
							'icon'  => $socials['social_icon'][$key],
						);
					}
			    }
		    }

		    return $icons;
	    }

		/**
		* Returns all icons information in option format ( compatible with ReduxFramework )
		* @param required fields in array ( according to ReduxFramework required fields implementation )
		* @return array containing two values ( 'option', 'status')
		* @since 1.0
		*
		*/
	    static function get_icons_option( $required = array() ) { 

	    	// Prepare multicheckbox option for socials using Plethora_Module_Social:get_icons method
			$options = array();
			$default = array();
			$count = 0;

    		$all_socials = self::get_icons('all');
    		if ( is_array( $all_socials )) {
				foreach ( $all_socials as $social ) { 
					$count = $count + 1;
					$options[$social['icon']] = '<i class="fa '. $social['icon'] .'" style="display:inline-block; width:14px; padding-right:6px;"></i>'. $social['title'];
					$default[$social['icon']] = '1';
				}
			}

	    	if ( $count === 0 ) { 
	    		$status = '<span style="color:red">'. esc_html__('It seems that you have not set your social icons yet! Visit the \'General > Social Icons\' to edit your global social icons information and come back here to set the display status for each icon.', 'plethora-framework') .'</span>';
	    		$option = array();
	    	} else {

	    		$status = esc_html__('Visit the \'General > Social Icons\' tab to edit your global social icons information.', 'plethora-framework');
	 			$option = array(
					    	'id'       => METAOPTION_PREFIX .'socialbar-status',
							'required' => $required,						
					    	'type'     => 'checkbox',
					    	'title'    => esc_html__('Icons Display Status', 'plethora-framework'), 
					    	'subtitle' => esc_html__('Check icons to display on social bar', 'plethora-framework'),
					    	'desc'     => esc_html__('You may edit this list under \'General > Social Icons\' tab.', 'plethora-framework'),
					    	'options'  =>  $options,
					    	'default' => $default
							);
	    	}

	    	$return = array();
	    	$return['option'] = $option;
	    	$return['status'] = $status;
	    	return $return;
	    }
	}
}