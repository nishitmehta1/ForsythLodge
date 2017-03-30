<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Page Post Type Config Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Posttype_Page') && !class_exists('Plethora_Posttype_Page_Ext') ) {

  	/**
   	* Extend base class
   	* Base class file: /plugins/plethora-featureslib/features/posttype/page/posttype-page.php
   	*/
  	class Plethora_Posttype_Page_Ext extends Plethora_Posttype_Page {

		// Plethora Index variables
		public static $feature_title         = "Page Post Type";								// Feature display title  (string)
		public static $feature_description   = "Contains all page related post configuration";	// Feature display description (string)
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = false;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		// auxiliary variables
		public $post_type = 'page';
		public $page_archive_posttype;
		public $page_archive_posttype_object;

		public $posttype_obj;

		public function __construct() {

			if ( is_admin() ) {

				// Single page Theme Options
				add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 100);

				// Single page Metaboxes. Hook on 'plethora_metabox_add' filter
				add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));

				// Archive static page metabox. Hook on 'plethora_metabox_add' filter
				add_filter( 'plethora_metabox_add', array($this, 'archive_metabox'));
			}
			
			// ONE PAGER SCROLLING FUNCTIONALITY ( always enqueue features on 20 )
			add_action( 'wp_enqueue_scripts', array( $this, 'one_pager') );	  
		}

		/** 
		* Returns theme options configuration. Collects global and theme-specific fields
		* Hooked @ 'plethora_themeoptions_content'
		*/
        public function single_themeoptions( $sections ) {

        	$fields = array();
        	$sections_index = Plethora_Posttype::single_options_sections_index_for( $this->post_type );
        	foreach ( $sections_index as $section => $section_config ) {

        		$section_fields = Plethora_Posttype::get_single_themeoptions_section_fields( $this, $section );
        		if ( !empty( $section_fields ) ) { 
        			$fields[] = array(
						'id'     => METAOPTION_PREFIX . 'single'. $this->post_type .'-'.$section.'-section',
						'type'   => 'section', 
						'title'  => ( !empty( $section_config['title'] ) ) ? $section_config['title'] : '',
						'desc'   => ( !empty( $section_config['desc'] ) ) ? $section_config['desc'] : '',
						'indent' => true
					);
        			$fields = array_merge( $fields, $section_fields );
        		}
			}

        	if ( !empty( $fields ) ) {

				$sections[] = array(
						'title'      => esc_html__('Single Page', 'hotel-xenia'),
						'heading'    => esc_html__('SINGLE PAGE VIEW OPTIONS', 'hotel-xenia'),
						'desc'       => esc_html__('These will be the default values for a new post you create. You have the possibility to override most of these settings on each post separately.', 'hotel-xenia') . '<br><span style="color:red;">'. esc_html__('Important: ', 'hotel-xenia') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'hotel-xenia') ,
						'subsection' => true,
						'fields'     => $fields
				);
			}
			return $sections;
        }

		/** 
		* Returns single options configuration. Collects all metabox fields
		* Hooked @ 'plethora_metabox_add'
		*/
        public function single_metabox( $metaboxes ) {

			// Cannot use global $post, so we are getting the 'post' url parameter
			$postid = isset( $_GET['post'] ) && is_numeric( $_GET['post'] )  ? $_GET['post'] : 0;
			// Get page IDs for all supported archives
			$archive_page_ids = self::get_static_archive_pages();

			if ( ! in_array( $postid, $archive_page_ids) || $postid === 0 ) {  

	        	$sections_index = Plethora_Posttype::single_options_sections_index_for( $this->post_type );
	        	$sections = array();
	        	$priority = 10;
	        	foreach ( $sections_index as $section => $section_config ) {

	        		$fields = Plethora_Posttype::get_single_metabox_section_fields( $this, $section );
		        	if ( !empty( $fields ) ) {

						$section_config['fields'] =  $fields;
						$sections[] = $section_config;
					}
				}

			    $metaboxes[] = array(
			        'id'            => 'metabox-single-'. $this->post_type,
			        'title'         => esc_html__( 'Page Options', 'hotel-xenia' ),
			        'post_types'    => array( $this->post_type ),
			        'position'      => 'normal', // normal, advanced, side
			        'priority'      => 'high', // high, core, default, low
			        'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
			        'sections'      => $sections,
			    );
			}
		    return $metaboxes;
        }

		/** 
		* Returns single options configuration. Collects all metabox fields
		* Hooked @ 'plethora_metabox_add'
		*/
        public function archive_metabox( $metaboxes ) {
			// Cannot use global $post, so we are getting the 'post' url parameter
			$postid = isset( $_GET['post'] ) && is_numeric( $_GET['post'] )  ? $_GET['post'] : 0;
			// Get page IDs for all supported archives
			$archive_page_ids = self::get_static_archive_pages();

			if ( in_array( $postid, $archive_page_ids)  && $postid !== 0 ) {  

	        	$sections_index = Plethora_Posttype::archive_options_sections_index_for( 'post' );
	        	$sections = array();
	        	$priority = 10;
	        	foreach ( $sections_index as $section => $section_config ) {

	        		$post_type_ext_instance = Plethora_Theme::get_feature_instance( 'Plethora_Posttype_Post_Ext' );
	        		$fields = Plethora_Posttype::get_archive_metabox_section_fields( $post_type_ext_instance, $section );
		        	if ( !empty( $fields ) ) {

						$section_config['fields'] =  $fields;
						$sections[] = $section_config;
					}
				}

				/* So, since this is an archive page, we have to add the archive metabox for this post
				   On archive pages, we just place an empty metabox for global metabox tabs hooking
				*/
	     		foreach ( $archive_page_ids as $post_type=>$page_id ) {

	     			if ( $postid === $page_id ) {

					    // Update variables for additional configuration
					    $posttype_object = get_post_type_object($post_type);
					    // print_r($posttype_object);
						$this->page_archive_posttype = $post_type;
						$this->page_options_text     = !empty( $posttype_object->has_archive ) ? ucfirst( $posttype_object->has_archive ) .' Page Options' : esc_html__('Blog Page Options', 'hotel-xenia');
						$this->page_view_text        = !empty( $posttype_object->has_archive ) ? 'View ' . ucfirst( $posttype_object->has_archive ) : esc_html__('View Blog', 'hotel-xenia');
						$this->page_tab_text         = !empty( $posttype_object->has_archive ) ? ucfirst( $posttype_object->has_archive ) : esc_html__('Blog', 'hotel-xenia');

					    $metaboxes[] = array(
					        'id'            => 'metabox-archive-'. $post_type ,
						    'title'         => $this->page_options_text . ' <small style="color:red;">' . sprintf( esc_html__('| For more advanced content options, please visit: Theme Options > Content > %s', 'hotel-xenia'), $this->page_tab_text ) . '</small>',
					        'post_types'    => array( $this->post_type ),
					        'position'      => 'normal', // normal, advanced, side
					        'priority'      => 'high', // high, core, default, low
					        'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
					        'sections'      => $sections,
					    );

				  		// Remove content editor
					    add_action( 'admin_init', array( $this, 'hide_editor'), 20);
					    // Changing Edit screen labels
					    add_action( 'init', array( $this, 'change_admin_screen_texts'), 999);
				    }
				}
			}
		    return $metaboxes;
        }

		// /** 
		// * Returns single options configuration. Collects global and theme-specific fields
		// * Hooked @ 'plethora_metabox_add'
		// */
  //       public function single_metabox( $metaboxes ) {

  //       	// setup theme options according to configuration
		// 	$opts        = $this->single_options();
		// 	$opts_config = $this->single_options_config();
		// 	$fields      = array();
  //       	foreach ( $opts_config as $opt_config ) {

		// 		$id          = $opt_config['id'];
		// 		$status      = $opt_config['metabox'];
		// 		$default_val = $opt_config['metabox_default'];
  //       		if ( $status && array_key_exists( $id, $opts ) ) {

  //       			if ( !is_null( $default_val ) ) { // will add only if not NULL }
		// 				$opts[$id]['default'] = $default_val;
		// 			}
		// 			$fields[] = $opts[$id];
  //       		}
  //       	}

		// 	$sections_content = array(
		// 		'title'      => esc_html__('Content', 'hotel-xenia'),
		// 		'heading'    => esc_html__('CONTENT OPTIONS', 'hotel-xenia'),
		// 		'icon_class' => 'icon-large',
		// 		'icon'       => 'el-icon-lines',
		// 		'fields'     => $fields
		// 	);

		// 	// Cannot use global $post, so we are getting the 'post' url parameter
		// 	$postid = isset( $_GET['post'] ) && is_numeric( $_GET['post'] )  ? $_GET['post'] : 0;
		// 	// Get page IDs for all supported archives
		// 	$archive_page_ids = self::get_static_archive_pages();
		// 	// Normal page metabox should be displayed only if this is NOT an archive page
		// 	if ( ! in_array( $postid, $archive_page_ids) || $postid === 0 ) {  

		// 		$sections = array();
		// 		$sections[] = $sections_content;
		// 		if ( has_filter( 'plethora_metabox_singlepage') ) {

		// 			$sections = apply_filters( 'plethora_metabox_singlepage', $sections );
		// 		}

		// 	    $metaboxes[] = array(
		// 	        'id'            => 'metabox-single-page',
		// 	        'title'         => esc_html__('Page Options', 'hotel-xenia' ),
		// 	        'post_types'    => array( $this->post_type ),
		// 	        'position'      => 'normal', // normal, advanced, side
		// 	        'priority'      => 'high', // high, core, default, low
		// 	        'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
		// 	        'sections'      => $sections,
		// 	    );

		// 	} else { // Display archive page metabox ( depending on the post )

		// 	}

	 //    	return $metaboxes;
  //       }

		/** 
		* Returns single options index for final configuration
		*/
        public function single_options() {

		    $single_options['layout'] = array(
							'id'      =>  METAOPTION_PREFIX .'page-layout',
							'title'   => esc_html__('Select Layout', 'hotel-xenia' ),
							'type'    => 'image_select',
							'options' => Plethora_Module_Style::get_options_array( array( 
																						'type'   => 'page_layouts',
																						'use_in' => 'redux',
																				   )
										 ),
			);

		    $single_options['sidebar'] = array(
							'id'       => METAOPTION_PREFIX .'page-sidebar',
							'type'     => 'select',
							'required' => array(METAOPTION_PREFIX .'page-layout','equals',array('right_sidebar','left_sidebar')),  
							'data'     => 'sidebars',
							'multi'    => false,
							'title'    => esc_html__('Select Sidebar', 'hotel-xenia'), 
			);

		    $single_options['colorset'] = array(
							'id'      => METAOPTION_PREFIX .'page-colorset',
							'type'    => 'button_set',
							'title'   => esc_html__('Content Section Color Set', 'hotel-xenia' ),
							'desc'    => esc_html__('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'hotel-xenia' ),
							'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																						  'use_in'          => 'redux',
																						  'prepend_options' => array(  'foo' => esc_html__('Default', 'hotel-xenia') ) ) ),
			);

		    $single_options['title'] = array(
							'id'      => METAOPTION_PREFIX .'page-title',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Title', 'hotel-xenia'),
							'desc'    => esc_html__('Enable/disable titles section display. You might want to disable this in case you are using media panel for titles display.', 'hotel-xenia'),
							'options' => array(
											1 => esc_html__('Display', 'hotel-xenia'),
											0 => esc_html__('Hide', 'hotel-xenia'),
										),
			);

		    $single_options['subtitle'] = array(
							'id'      => METAOPTION_PREFIX .'page-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Subtitle', 'hotel-xenia'),
							'options' => array(
											1 => esc_html__('Display', 'hotel-xenia'),
											0 => esc_html__('Hide', 'hotel-xenia'),
										),
			);

		    $single_options['subtitle-text'] = array(
							'id'        => METAOPTION_PREFIX .'page-subtitle-text',
							'type'      => 'text',
							'title'     => esc_html__('Subtitle', 'hotel-xenia'), 
							'translate' => true,
			);

		    $single_options['one-pager-speed'] = array(
							'id'       => METAOPTION_PREFIX .'one-pager-speed',
							'type'     => 'spinner', 
							'title'    => esc_html__('One Page Scrolling Speed', 'hotel-xenia'),
							"min"      => 100,
							"step"     => 100,
							"max"      => 4000,
			);

			// Additional fields added on Avoir >>> START
		    $single_options['containertype'] = array(
							'id'      => METAOPTION_PREFIX .'page-containertype',
							'type'    => 'button_set', 
							'title'   => esc_html__('Container Type', 'hotel-xenia'),
							'options' => array(
											'container'       => esc_html__( 'Default', 'hotel-xenia'),
											'container-fluid' => esc_html__( 'Fluid', 'hotel-xenia'),
										)
			);
		    $single_options['extraclass'] = array(
							'id'      => METAOPTION_PREFIX .'page-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Extra Classes', 'hotel-xenia'),
							'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'hotel-xenia'),
			);
			// Additional fields added on Avoir >>> END

			return $single_options;
        }

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
						'theme_options_default' => 'no_sidebar',
						'metabox'               => true,
						'metabox_default'       => NULL
					);
                	$config[] = array( 
						'id'                    => 'sidebar', 
						'theme_options'         => true, 
						'theme_options_default' => 'sidebar-pages',
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
						'theme_options_default' => 'foo',
						'metabox'               => true,
						'metabox_default'       => NULL
					);
                	$config[] = array( 
						'id'                    => 'extraclass', 
						'theme_options'         => true, 
						'theme_options_default' => '',
						'metabox'               => true,
						'metabox_default'       => NULL,
					);

               		if ( $section !== 'all' ) { break; }

            case 'auxiliary-navigation':
            case 'all':
                	$config[] = array( 
						'id'                    => 'one-pager-speed', 
						'theme_options'         => true, 
						'theme_options_default' => '300',
						'metabox'               => true,
						'metabox_default'       => ''
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
	        }
	        return $config;
		}

	 	# HELPER METHODS START ->


		/**
		* PUBLIC | Returns native, Plethora and any non Plethora archive page. 
		* Notice: A valid Plethora archive should be assigned on a page
		* Notice: Will work as expected after 'init' hook 
		* @since 1.0
		*/
		public static function get_static_archive_page( $post_type, $args = array() ) {

		    $default_args = array( 
		            'output' => 'ID',     // 'ID' ( static page ID ) | 'object' ( static page object )  | 'link' ( native OR static page link )   
		            );

		    // Merge user given arguments with default
		    $args = wp_parse_args( $args, $default_args);

		    // Get supported archives list
		    $supported_archives = Plethora_Theme::get_supported_post_types( array( 'type' => 'archives' ) );
		    
		    // Time to filter and return
		    $return = false;
		    if ( in_array( $post_type, $supported_archives ) ) { 

		      switch ( $post_type ) {
		        case 'post':
		          $page_id = get_option( 'page_for_'. $post_type .'s', 0);
		          break;
		        default:
		          $page_id = Plethora_Theme::option( THEMEOPTION_PREFIX . $post_type .'-staticpage', '');
		          break;
		      }

		      if ( $args['output'] === 'object' ) {

		          $return = get_post( $page_id );

		      } else {

		          $return = $page_id;
		      }

		      return apply_filters( 'plethora_static_archive_page', $return, $post_type, $args );
		    }
		}

		/**
		* PUBLIC | Returns native, Plethora and any non Plethora archive pages. 
		* Notice: Will work as expected after 'init' hook 
		* @since 1.0
		*/
		public static function get_static_archive_pages( $args = array() ) {

		    $default_args = array( 
		            'output' => 'ID',       // 'ID' | 'object'    
		            'exclude' => array(),   // array with exclude post type archives    
		            );

		    // Merge user given arguments with default
		    $args = wp_parse_args( $args, $default_args);
		    $args['exclude'] = is_array($args['exclude']) ? $args['exclude'] : array($args['exclude']); // Make sure that this will be an array

		    $supported_archives = Plethora_Theme::get_supported_post_types( array( 'type' => 'archives' ) );
		    $supported_archives = array_diff( $supported_archives, $args['exclude'] ); // Excludes
		    
		    $archive_pages = array();
		    foreach ( $supported_archives as $key=>$post_type ) {

		      $archive_pages[$post_type] =  self::get_static_archive_page( $post_type, $args );
		    }

		    return $archive_pages;
		} 	       
			/** 
			* Sets one pager JS variable configuration
			*/
		public function one_pager() {

			Plethora_Theme::set_themeconfig( "GENERAL", array('onePagerScrollSpeed' => intval( Plethora_Theme::option( METAOPTION_PREFIX .'one-pager-speed', 300 ) ) ) );
		}

		/** 
		* Removes main content editor ( used for archive metaboxes )
		*/
		public function hide_editor() { 

			if ( $this->page_archive_posttype === 'post' ) {

				remove_post_type_support('post', 'editor');
			}
		}


		/** 
		* Add main content editor ( used for archive metaboxes )
		*/
		public function display_editor() { 

			if ( $this->page_archive_posttype === 'post' ) {

				add_post_type_support('post', 'editor');
			}
		}

		/**
		* Modify registered post type labels
		*/
		public function change_admin_screen_texts() {
			
			global $wp_post_types;
			$wp_post_types['page']->labels->edit_item = $this->page_options_text; 		
			$wp_post_types['page']->labels->view_item = $this->page_view_text;		
		} 	  
 	# HELPER METHODS END <-
	}
}	