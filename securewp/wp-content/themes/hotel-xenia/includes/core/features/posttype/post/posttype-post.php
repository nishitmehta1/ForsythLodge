<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Post Type Feature Class. Post type is a native WP feature...however 
this class is used to create post/blog options not only for the native post type but also
for non Plethora CPTs created by third party tools, such as Custom Posts UI
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Post') ) {  
 
	class Plethora_Posttype_Post {

        // Plethora Index variables
		public static $feature_title         = "Native Post Type";								// Feature display title  (string)
		public static $feature_description   = "Contains all native post type configuration";	// Feature display description (string)
		public static $theme_option_control  = false;												// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = false;												// Default activation option status ( boolean )
		public static $theme_option_requires = array();												// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;												// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;												// Additional method invocation ( string/boolean | method name or false )

        // Auxilliary variables
		public $custom_post_types;
		public $activated_custom_post_types;

		public function __construct() {

			add_action( 'init', array( $this, 'add_custom_posts_support' ), 15 );
			add_action( 'init', array( $this, 'check_custom_posts_status' ), 16 );
			
			if ( is_admin() ) {
				
				add_action( 'init', array( $this, 'init' ), 17 );
			}
		}

		public function init() {

			// Built in posts archive/single theme options
			add_filter( 'plethora_themeoptions_content', array($this, 'archive_themeoptions'), 5);
			add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 110);
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));		
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox_audio'));		
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox_video'));

			// Non Plethora CPTs archive/single theme options
			add_filter( 'plethora_themeoptions_content', array($this, 'archive_themeoptions_custom'), 50);
			add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions_custom'), 150);
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox_custom'));				
		}

		// Add support for user created CPTs
		public function add_custom_posts_support() {

			// Get all post types that have a single view on frontend
			$all_post_types    = Plethora_Theme::get_supported_post_types( array( 
										'type'          => 'singles', 
										'plethora_only' => false, 
										'output'        => 'objects' 
									));
			// Get all Plethora CPTs that have a single view on frontend
			$plethora_post_types  = Plethora_Theme::get_supported_post_types( array( 
										'type'          => 'singles', 
										'plethora_only' => true, 
										'output'        => 'objects' 
									));
			// Add support for non Plethora post types
			$this->custom_post_types = array_diff_key( $all_post_types, $plethora_post_types );
			add_filter( 'plethora_posttype_features_options', array( $this, 'posttype_features_options'), 10, 2 );			
		}

		// Add theme options controls for user created CPTs
		public function posttype_features_options( $options, $controller ) {

			foreach ( $this->custom_post_types as $post_type => $post_type_obj ) {

				$options[] = array(
					'id'       => THEMEOPTION_PREFIX . $controller .'-'. $post_type .'-status',
					'type'     => 'switch',
					'title'    => $post_type_obj->labels->singular_name .' '. esc_html__( 'Post Type', 'plethora-framework'),
					'subtitle' => '<span style="color:red">'. esc_html__('Third Party Feature / Plugin', 'plethora-framework') .'</span>',
					'desc'     => sprintf( esc_html__('This is a third party plugin custom post type. This option will activate/deactivate %1$s frontend options support for this CPT. Deactivating support does NOT mean that the post type will not be still active.', 'plethora-framework'), THEME_DISPLAYNAME ),
					'on'       => esc_html__('Activated', 'plethora-framework'),
					'off'      => esc_html__('Deactivated', 'plethora-framework'),
					'default'  => 1,
				);
			}

			return $options;

		}

		// Check if users have disabled our functionality via theme option control
		public function check_custom_posts_status() {

			$custom_post_types = array();
			foreach ( $this->custom_post_types as $post_type => $post_type_obj ) {

				$is_activated = Plethora_Theme::option( THEMEOPTION_PREFIX .'posttype-'. $post_type .'-status', 1 );
				
				if ( $is_activated ) {

					$custom_post_types[$post_type] = $post_type_obj;
				}
			}

			$this->activated_custom_post_types = $custom_post_types;
		}

		/**
		* Posts archive (blog) view theme options configuration for REDUX
		* Filter hook @ 'plethora_themeoptions_content'
		*/
        public function archive_themeoptions( $sections  ) {

        	// setup theme options according to configuration
			$opts        = $this->archive_options();
			$opts_config = $this->archive_options_config();
			$fields      = array();
        	foreach ( $opts_config as $opt_config ) {

				$id          = $opt_config['id'];
				$status      = $opt_config['theme_options'];
				$default_val = $opt_config['theme_options_default'];
        		if ( $status && array_key_exists( $id, $opts ) ) {

        			if ( !is_null( $default_val ) ) { // will add only if not NULL }
						$opts[$id]['default'] = $default_val;
					}
					
					// a smal workaround to remove subtitles that HAVE to be displayed on CPT
        			if ( isset( $opts[$id]['subtitle'] ) ) { 
						unset( $opts[$id]['subtitle'] );
					}

					$fields[] = $opts[$id];
        		}
        	}

        	if ( !empty( $fields ) ) {

				$page_for_posts	= get_option( 'page_for_posts', 0 );
				$desc_1 = esc_html__('These options affect your posts catalog display.', 'plethora-framework');
				$desc_2 = esc_html__('These options affect your posts catalog display...however it seems that you', 'plethora-framework'); 
				$desc_2 .= ' <span style="color:red">';
				$desc_2 .= esc_html__('have not set a static posts page yet!.', 'plethora-framework');
				$desc_2 .= '</span>';
				$desc_2 .= esc_html__('You can go for it under \'Settings > Reading\'', 'plethora-framework');
				$desc = $page_for_posts === 0 || empty($page_for_posts) ? $desc_2 :  $desc_1 ;
				$desc .= '<br>'. sprintf( esc_html__('If you are using a speed optimization plugin, don\'t forget to %1$sclear cache%2$s after options update', 'plethora-framework'), '<strong>', '</strong>' );

				$sections[] = array(
					'title'      => esc_html__('Blog', 'plethora-framework'),
					'heading'    => esc_html__('BLOG OPTIONS', 'plethora-framework'),
					'desc'       => $desc,
					'subsection' => true,
					'fields'     => $fields
				);
			}
			return $sections;
        }

		/** 
		* CPT archive views theme options configuration for REDUX
		* Filter hook @ 'plethora_themeoptions_content'
		*/
        public function archive_themeoptions_custom( $sections  ) {

			foreach ( $this->activated_custom_post_types as $post_type => $post_type_obj ) {

				if ( $post_type_obj->has_archive ) {

		        	$post_type_label = $post_type_obj->label;
		        	$post_type_label_singular = !empty( $post_type_obj->labels->singular_name ) ? $post_type_obj->labels->singular_name : ucfirst( $post_type_label ) ;

		        	// setup theme options according to configuration
					$opts        = $this->archive_options( $post_type_obj );
					$opts_config = $this->archive_options_config( $post_type_obj );
					$fields      = array();
		        	foreach ( $opts_config as $opt_config ) {

						$id          = $opt_config['id'];
						$status      = $opt_config['theme_options'];
						$default_val = $opt_config['theme_options_default'];
		        		if ( $status && array_key_exists( $id, $opts ) ) {

		        			if ( !is_null( $default_val ) ) { // will add only if not NULL }
								$opts[$id]['default'] = $default_val;
							}
							$fields[] = $opts[$id];
		        		}
		        	}

		        	if ( !empty( $fields ) ) {

			        	$desc  = sprintf( esc_html__( 
			        		'*IMPORTANT: this custom post type archive is registered via a third party plugin. This tab\'s options will help you configure its ARCHIVE view on %1$s frontend. If you just don\'t need frontend support for %2$s, you have the option to deactivate it on %3$s', 'plethora-framework' ), 
			        		THEME_DISPLAYNAME,
			        		'<strong>'. strtolower( $post_type_label ) .'</strong>',
			        		'<strong>Theme Options > Advanced > Features Library > Post Types Manager</strong>'
						);
						$sections[] = array(
							'title'      => $post_type_label . ' '. esc_html__('Archive *', 'plethora-framework'),
							'heading'    => strtoupper( $post_type_label ) . ' '. esc_html__('ARCHIVE OPTIONS', 'plethora-framework'),
							'desc'       => $desc,
							'subsection' => true,
							'fields'     => $fields
						);
					}
				}
			}
			return $sections;
        }

		/** 
		* Single view theme options configuration for REDUX
		* Filter hook @ 'plethora_themeoptions_content'
		*/
        public function single_themeoptions( $sections  ) {

        	// setup theme options according to configuration
			$opts        = $this->single_options();
			$opts_config = $this->single_options_config();
			$fields      = array();
        	foreach ( $opts_config as $opt_config ) {

				$id          = $opt_config['id'];
				$status      = $opt_config['theme_options'];
				$default_val = $opt_config['theme_options_default'];
        		if ( $status && array_key_exists( $id, $opts ) ) {

        			if ( !is_null( $default_val ) ) { // will add only if not NULL }
						$opts[$id]['default'] = $default_val;
					}
					
					// a smal workaround to remove subtitles that HAVE to be displayed on CPT
        			if ( isset( $opts[$id]['subtitle'] ) ) { 
						unset( $opts[$id]['subtitle'] );
					}

					$fields[] = $opts[$id];
        		}
        	}

        	if ( !empty( $fields ) ) {

				$sections[] = array(
					'title'   => 'Single Post',
					'heading' => esc_html__('SINGLE POST VIEW OPTIONS', 'plethora-framework'),
					'desc'    => esc_html__('These will be the default values for a new post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
					'subsection' => true,
	                'fields'     => $fields
				);
			}
			return $sections;

		}

		/** 
		* CPT single views theme options configuration for REDUX
		* Filter hook @ 'plethora_themeoptions_content'
		*/
        public function single_themeoptions_custom( $sections ) {

        	foreach ( $this->activated_custom_post_types as $post_type => $post_type_obj ) { 

				$post_type_label          = $post_type_obj->label;
				$post_type_label_singular = !empty( $post_type_obj->labels->singular_name ) ? $post_type_obj->labels->singular_name : ucfirst( $post_type_label ) ;
				$desc                     = sprintf( esc_html__( 
								        		'*IMPORTANT: this custom post type is registered via a third party plugin. This tab\'s options will help you configure its single view on %1$s frontend. If you just don\'t need frontend support for %2$s, you have the option to deactivate it on %3$s', 'plethora-framework' ), 
								        		THEME_DISPLAYNAME,
								        		'<strong>'. strtolower( $post_type_label ) .'</strong>',
								        		'<strong>Theme Options > Advanced > Features Library > Post Types Manager</strong>'
											);


	        	// setup theme options according to configuration
				$opts        = $this->single_options( $post_type_obj );
				$opts_config = $this->single_options_config( $post_type_obj );
				$fields      = array();
	        	foreach ( $opts_config as $opt_config ) {

					$id          = $opt_config['id'];
					$status      = $opt_config['theme_options'];
					$default_val = $opt_config['theme_options_default'];
	        		if ( $status && array_key_exists( $id, $opts ) ) {

	        			if ( !is_null( $default_val ) ) { // will add only if not NULL }
							$opts[$id]['default'] = $default_val;
						}
						$fields[] = $opts[$id];
	        		}
	        	}

	        	if ( !empty( $fields ) ) {

					$sections[] = array(
						'title'      => $post_type_label .' *',
						'heading'    => esc_html__('SINGLE', 'plethora-framework') .' '. strtoupper( $post_type_label_singular ) .' '. esc_html__('OPTIONS', 'plethora-framework'),
						'desc'       => $desc,
						'subsection' => true,
						'fields'     => $fields
					);
				}
			}

			return $sections;
        }

		/** 
		* Returns METABOX options configuration for single post views
		* Filter hook @ 'plethora_metabox_add'
		*/
        public function single_metabox( $metaboxes ) {

        	// setup theme options according to configuration
			$opts          = $this->single_options();
			$opts_config   = $this->single_options_config();
			$fields        = array();
        	foreach ( $opts_config as $opt_config ) {

				$id          = $opt_config['id'];
				$status      = $opt_config['metabox'];
				$default_val = $opt_config['metabox_default'];
        		if ( $status && array_key_exists( $id, $opts ) ) {

        			if ( !is_null( $default_val ) ) { // will add only if not NULL }
						$opts[$id]['default'] = $default_val;
					}
					if ( isset( $opts[$id]['subtitle'] ) ) {

						unset( $opts[$id]['subtitle'] );
					}
					
					$fields[] = $opts[$id];
        		}
        	}

			$sections_content = array(
				'title'      => esc_html__('Content', 'plethora-framework'),
				'heading'    => esc_html__('CONTENT OPTIONS', 'plethora-framework'),
				'icon_class' => 'icon-large',
				'icon'       => 'el-icon-lines',
				'fields'     => $fields
			);

			$sections = array();
			$sections[] = $sections_content;


			// This filter is used to hook additional option sections...LEAVE IT THERE!
			if ( has_filter( 'plethora_metabox_singlepost') ) {

				$sections = apply_filters( 'plethora_metabox_singlepost', $sections );
			}
			if ( !empty( $fields ) ) {

			    $metaboxes[] = array(
			        'id'            => 'metabox-single-post',
			        'title'         => esc_html__( 'Page Options', 'plethora-framework' ),
			        'post_types'    => array( 'post'),
			        'position'      => 'normal', // normal, advanced, side
			        'priority'      => 'high', // high, core, default, low
			        'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
			        'sections'      => $sections,
			    );
		    }

		    return $metaboxes;
        }

		/** 
		* Returns METABOX options configuration for single CPT views
		* Filter hook @ 'plethora_metabox_add'
		*/
        public function single_metabox_custom( $metaboxes ) {

        	foreach ( $this->activated_custom_post_types as $post_type => $post_type_obj ) { 

	        	$post_type_label = $post_type_obj->label;
	        	$post_type_label_singular = !empty( $post_type_obj->labels->singular_name ) ? $post_type_obj->labels->singular_name : ucfirst( $post_type_label ) ;

	        	// setup theme options according to configuration
				$opts          = $this->single_options( $post_type_obj );
				$opts_config   = $this->single_options_config( $post_type_obj );
				$fields        = array();
	        	foreach ( $opts_config as $opt_config ) {

					$id          = $opt_config['id'];
					$status      = $opt_config['metabox'];
					$default_val = $opt_config['metabox_default'];
	        		if ( $status && array_key_exists( $id, $opts ) ) {

	        			if ( !is_null( $default_val ) ) { // will add only if not NULL }
							$opts[$id]['default'] = $default_val;
						}
						$fields[] = $opts[$id];
	        		}
	        	}

				$sections_content = array(
					'title'      => esc_html__('Content', 'plethora-framework'),
					'heading'    => esc_html__('CONTENT OPTIONS', 'plethora-framework'),
					'icon_class' => 'icon-large',
					'icon'       => 'el-icon-lines',
					'fields'     => $fields
				);

				$sections = array();
				$sections[] = $sections_content;


				// This filter is used to hook additional option sections...LEAVE IT THERE!
				if ( has_filter( 'plethora_metabox_single'. $post_type ) ) {

					$sections = apply_filters( 'plethora_metabox_single'. $post_type , $sections );
				}
				if ( !empty( $fields ) ) {

				    $metaboxes[] = array(
				        'id'            => 'metabox-single-'. $post_type,
				        'title'         => $post_type_label  . esc_html__( ' Options', 'plethora-framework' ),
				        'post_types'    => array( $post_type ),
				        'position'      => 'normal', // normal, advanced, side
				        'priority'      => 'high', // high, core, default, low
				        'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
				        'sections'      => $sections,
				    );
			    }
			}

		    return $metaboxes;
        }

		/** 
		* Returns additional AUDIO METABOX options configuration for single post views
		* Filter hook @ 'plethora_metabox_add'
		*/
        public function single_metabox_audio( $metaboxes ) {

		    $sections = array();

		    $sections[] = array(
		        'icon_class'    => 'icon-large',
		        'icon'          => 'el-icon-website',
		        'fields'        => array(


					array(
						'id'=> METAOPTION_PREFIX .'content-audio',
						'type' => 'text', 
						'title' => esc_html__('Audio Link', 'plethora-framework'),
						'desc' => esc_html__('Enter audio url/share link from: <strong>SoundCloud | Spotify | Rdio </strong>', 'plethora-framework'),
						'validate' => 'url',
						),

		        )
		    );

		    $metaboxes[] = array(
		        'id'            => 'metabox-single-post-audio',
		        'title'         => esc_html__('Featured Audio', 'plethora-framework' ),
		        'post_types'    => array( 'post'),
		        'post_format'    => array( 'audio'),
		        'position'      => 'side', // normal, advanced, side
		        'priority'      => 'low', // high, core, default, low
		        'sections'      => $sections,
		    );

		    return $metaboxes;
		}

		/** 
		* Returns additional VIDEO METABOX options configuration for single post views
		* Filter hook @ 'plethora_metabox_add'
		*/
        public function single_metabox_video( $metaboxes ) {

		    $sections = array();

		    $sections[] = array(
		        'icon_class'    => 'icon-large',
		        'icon'          => 'el-icon-website',
		        'fields'        => array(


					array(
						'id'=> METAOPTION_PREFIX .'content-video',
						'type' => 'text', 
						'title' => esc_html__('Video Link', 'plethora-framework'),
						'desc' => esc_html__('Enter video url/share link from: <strong>YouTube | Vimeo | Dailymotion | Blip | Wordpress.tv</strong>', 'plethora-framework'),
						'validate' => 'url',
						),

		        )
		    );

		    $metaboxes[] = array(
		        'id'            => 'metabox-single-post-video',
		        'title'         => esc_html__('Featured Video', 'plethora-framework' ),
		        'post_types'    => array( 'post'),
		        'post_format'    => array( 'video'),
		        'position'      => 'side', // normal, advanced, side
		        'priority'      => 'low', // high, core, default, low
		        'sections'      => $sections,
		    );

		    return $metaboxes;
        }

		/** 
		* Returns ARCHIVE OPTIONS INDEX
		* It contains ALL possible archive options, no matter which theme OR CPT
		*/
        public function archive_options( $post_type_obj = '' ) {

        	if ( ! is_object( $post_type_obj ) ) { 

        		$post_type_obj = get_post_type_object( 'post' );
        	}

			$post_type                = $post_type_obj->name;
			$post_type_label          = $post_type_obj->label;
			$post_type_label_singular = $post_type_obj->labels->singular_name;

		    $archive_options['page-start'] = array(
							'id'     => 'archive'.$post_type.'-page-start',
							'type'   => 'section',
							'title'  => esc_html__('Blog Page Options', 'plethora-framework'),
							'indent' => true,
			);

		    $archive_options['layout'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-layout',
							'title'   => esc_html__('Page Layout', 'plethora-framework' ),
							'type'    => 'image_select',
							'options' => Plethora_Module_Style::get_options_array( array( 
																						'type'   => 'page_layouts',
																						'use_in' => 'redux',
																				   )
										 ),
			);

		    $archive_options['sidebar'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-sidebar',
							'required' => array(METAOPTION_PREFIX .'archive'.$post_type.'-layout','equals',array('right_sidebar','left_sidebar')),  
							'type'     => 'select',
							'data'     => 'sidebars',
							'multi'    => false,
							'title'    => esc_html__('Sidebar', 'plethora-framework'), 
			);

		    $archive_options['colorset'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-colorset',
							'type'    => 'button_set',
							'title'   => esc_html__('Content Section Color Set', 'plethora-framework' ),
							'desc'    => esc_html__('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
							'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																						  'use_in'          => 'redux',
																						  'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
			);

		    $archive_options['title'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-title',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Title On Content', 'plethora-framework'),
							'desc'    => esc_html__('Enable/disable titles section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
			);

		    $archive_options['title-text'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-title-text',
							'type'      => 'text',
							'title'     => esc_html__('Default Title', 'plethora-framework'), 
							'translate' => true,
			);

		    $archive_options['title-tax'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-title-tax',
							'type'    => 'button_set', 
							'title'   => esc_html__('Selected Taxonomy Title', 'plethora-framework'),
							'desc'    => esc_html__('Title behavior when a taxonomy archive ( category, tag, etc ) is displayed', 'plethora-framework'),
							'options' => array(
											0 => esc_html__('Default Title', 'plethora-framework'),
											1 => esc_html__('Taxonomy Title', 'plethora-framework'),
										),
			);

			if ( $post_type === 'post' ) {

			    $archive_options['title-author'] = array(
								'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-title-author',
								'type'    => 'button_set', 
								'title'   => esc_html__('Selected Author Title', 'plethora-framework'),
								'desc'    => esc_html__('Title behavior when an author archive is displayed', 'plethora-framework'),
								'options' => array(
												0 => esc_html__('Default Title', 'plethora-framework'),
												1 => esc_html__('Author Display Name', 'plethora-framework'),
											),
				);

			    $archive_options['title-date'] = array(
								'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-title-date',
								'type'    => 'button_set', 
								'title'   => esc_html__('Selected Date Title', 'plethora-framework'),
								'desc'    => esc_html__('Title behavior when a date view is selected', 'plethora-framework'),
								'options' => array(
												0 => esc_html__('Default Title', 'plethora-framework'),
												1 => esc_html__('Selected Month', 'plethora-framework'),
											),
				);
			}

		    $archive_options['subtitle'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Subtitle On Content', 'plethora-framework'),
			);

		    $archive_options['subtitle-text'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-subtitle-text',
							'type'      => 'text',
							'title'     => esc_html__('Default Subtitle', 'plethora-framework'), 
							'translate' => true,
			);

		    $archive_options['subtitle-tax'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-tax-subtitle',
							'type'    => 'button_set', 
							'title'   => esc_html__('Selected Taxonomy Subtitle', 'plethora-framework'),
							'desc'    => esc_html__('Subtitle behavior when a category OR tag archive is displayed', 'plethora-framework'),
							'options' => array(
											0 => esc_html__('Default Subtitle', 'plethora-framework'),
											1 => esc_html__('Taxonomy Description', 'plethora-framework'),
										),
			);

			if ( $post_type === 'post' ) {

			    $archive_options['author-subtitle'] = array(
								'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-author-subtitle',
								'type'    => 'button_set', 
								'title'   => esc_html__('Selected Author Subtitle', 'plethora-framework'),
								'desc'    => esc_html__('Subtitle behavior when an author archive is displayed', 'plethora-framework'),
								'options' => array(
												0 => esc_html__('Default Subtitle', 'plethora-framework'),
												1 => esc_html__('Author Bio', 'plethora-framework'),
											),
				);

			    $archive_options['date-subtitle'] = array(
								'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-date-subtitle',
								'type'    => 'button_set', 
								'title'   => esc_html__('Selected Date Subtitle', 'plethora-framework'),
								'desc'    => esc_html__('Subtitle behavior when a date view is selected', 'plethora-framework'),
								'options' => array(
												0 => esc_html__('Default Subtitle', 'plethora-framework'),
												1 => esc_html__('Empty', 'plethora-framework'),
											),
				);
			}

		    $archive_options['listings-start'] = array(
						'id'     => 'archive'.$post_type.'-listings-start',
						'type'   => 'section',
						'title'  => esc_html__('Posts Listings Options', 'plethora-framework'),
						'indent' => true,
			);

		    $archive_options['listtype'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-listtype',
							'type'    => 'button_set', 
							'title'   => $post_type_label . ' '. esc_html__('Catalog Type', 'plethora-framework'),
							'options' => array(
								'classic' => esc_html__('Classic', 'plethora-framework'), 
								'compact' => esc_html__('Compact', 'plethora-framework'), 
							)
			);

		    $archive_options['mediadisplay'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-mediadisplay',
							'type'     => 'button_set', 
							'title'    => esc_html__('Featured Media Display', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html__('This post type supports feature image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support featured image', 'plethora-framework') .'</div>',
							'options'  => array(
									'inherit'       => 'According To Post Format',
									'featuredimage' => 'Force Featured Image Display',
									'hide'          => 'Do Not Display',
									),
			);

		    $archive_options['listing-content'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-listing-content',
							'type'     => 'button_set', 
							'title'    => esc_html__('Content/Excerpt Display', 'plethora-framework'), 
							'subtitle' => post_type_supports( $post_type, 'editor' ) ? '<span style="color:green">'. esc_html__('This post type supports editor content', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support editor content', 'plethora-framework') .'</div>',
							'desc'     => esc_html__('Displaying content will allow you to display posts containing the WP editor "More" tag.', 'plethora-framework'),
							'options'  => array(
								'excerpt' => esc_html__('Display Excerpt', 'plethora-framework'), 
								'content' => esc_html__('Display Content', 'plethora-framework') 
							)
			);

		    $archive_options['listing-subtitle'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-listing-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Subtitle', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html__('This post type supports subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support subtitle', 'plethora-framework') .'</div>',
							'options' => array(
											1 => esc_html__('Display', 'plethora-framework'),
											0 => esc_html__('Hide', 'plethora-framework'),
										),
			);
		    if ( $post_type === 'post' ) {

			    $archive_options['info-primarytax'] = array(
								'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-info-category',
								'type'    => 'switch', 
								'title'   => esc_html__('Display Categories Info', 'plethora-framework'),
				);

			    $archive_options['info-secondarytax'] = array(
								'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-info-tags',
								'type'    => 'switch', 
								'title'   => esc_html__('Display Tags Info', 'plethora-framework'),
				);

		    } else {
			    // use only for cpts
			    $archive_options['info-primarytax'] = array(
								'id'    => METAOPTION_PREFIX.'archive'. $post_type .'-info-primarytax',
								'type'  => 'switch', 
								'title' => sprintf( esc_html__('Display Primary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
								'desc'  => sprintf( esc_html__('You may choose the primary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
				);

			    // use only for cpts
			    $archive_options['info-secondarytax'] = array(
								'id'      => METAOPTION_PREFIX.'archive'. $post_type .'-info-secondarytax',
								'type'    => 'switch', 
								'title'   => sprintf( esc_html__('Display Secondary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
								'desc'    => sprintf( esc_html__('You may choose the secondary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
				);

		    }
		    $archive_options['info-author'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-info-author',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Author Info', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'author' ) ? '<span style="color:green">'. esc_html__('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support authors', 'plethora-framework') .'</div>',
							'desc' 	   => $post_type !== 'post' ? esc_html__('Display a non linked author label', 'plethora-framework') : '',
			);

		    $archive_options['info-date'] = array(
							'id'    => METAOPTION_PREFIX .'archive'.$post_type.'-info-date',
							'type'  => 'switch', 
							'title' => esc_html__('Display Date Info', 'plethora-framework'),
							'desc'  => $post_type !== 'post' ? esc_html__('Display a non linked date label', 'plethora-framework') : '',
			);

		    $archive_options['info-comments'] = array(
							'id'       => METAOPTION_PREFIX .'archive'.$post_type.'-info-comments',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Comments Count Info', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'comments' ) ? '<span style="color:green">'. esc_html__('This post type supports comments', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support comments', 'plethora-framework') .'</div>',
			);

		    $archive_options['show-linkbutton'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton',
							'type'    => 'switch', 
							'title'   => esc_html__('Display "Read More" Button', 'plethora-framework'),
			);

		    $archive_options['show-linkbutton-text'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton-text',
							'type'      => 'text',
							'required'  => array(METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton', '=', 1),
							'title'     => esc_html__('Button Text', 'plethora-framework'),
							'translate' => true,
			);

		    $archive_options['noposts-title'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-noposts-title',
							'type'      => 'text', 
							'title'     => sprintf( esc_html__('No %1s Title Text', 'plethora-framework'), $post_type_label ),
							'translate' => true,
			);

		    $archive_options['noposts-description'] = array(
							'id'        => METAOPTION_PREFIX .'archive'.$post_type.'-noposts-description',
							'type'      => 'textarea', 
							'title'     => sprintf( esc_html__('No %1s Description Text', 'plethora-framework'), $post_type_label ),
							'translate' => true,
			);

			// Additional archive options added on Avoir >>> START
		    $archive_options['containertype'] = array(
							'id'      => METAOPTION_PREFIX.'archive'.$post_type .'-containertype',
							'type'    => 'button_set', 
							'title'   => esc_html__('Container Type', 'plethora-framework'),
							'options' => array(
											'container'       => esc_html__( 'Default', 'plethora-framework'),
											'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
										)
			);
		    $archive_options['extraclass'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type .'-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Extra Classes', 'plethora-framework'),
							'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
			);

		    $archive_options['content-align'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type .'-contentalign',
							'type'    => 'button_set', 
							'title'   => esc_html__('Content Section Align', 'plethora-framework'),
							'desc'    => esc_html__('Affects all content section text alignment, except intro text ( you can set it as you like using the editor options ).', 'plethora-framework'),
							'options' => array(
											''            => esc_html__( 'Left', 'plethora-framework'),
											'text-center' => esc_html__( 'Center', 'plethora-framework'),
											'text-right'  => esc_html__( 'Right', 'plethora-framework'),
										 )
			);

		    $archive_options['intro-text'] = array(
							'id'      => METAOPTION_PREFIX .'archive'.$post_type .'-introtext',
							'type'    => 'editor', 
							'title'   => esc_html__('Intro Text', 'plethora-framework'),
							'desc'    => esc_html__('This will be displayed right before the posts catalog', 'plethora-framework'),
						    'args'   => array(
						        'teeny'            => false,
						        'textarea_rows'    => 7
						    )			
			);
			// Additional fields added on Avoir >>> END

			return $archive_options;
        }

		/** 
		* Returns single options index
		* It contains ALL possible single options, no matter which theme OR CPT
		*/
        public function single_options( $post_type_obj = '' ) {

        	if ( ! is_object( $post_type_obj ) ) { 

        		$post_type_obj = get_post_type_object( 'post' );
        		$post_type_obj->has_archive = 1;
        	}

			$post_type                = $post_type_obj->name;
			$post_type_label          = $post_type_obj->label;
			$post_type_label_singular = $post_type_obj->labels->singular_name;

		    $single_options['singleview-start'] = array(
							'id'       => $post_type .'-singleview-start',
							'type'     => 'section',
							'title'    => sprintf( esc_html__('Single %1$s View', 'plethora-framework'), $post_type_label_singular ),
							'subtitle' => sprintf( esc_html__('These options affect this %1$s\'s single view display', 'plethora-framework'), $post_type_label ),
							'indent'   => true,
			);

		    $single_options['layout'] = array(
							'id'      =>  METAOPTION_PREFIX . $post_type .'-layout',
							'title'   => esc_html__('Select Layout', 'plethora-framework' ),
							'type'    => 'image_select',
							'options' => Plethora_Module_Style::get_options_array( array( 
																							'type'   => 'page_layouts',
																							'use_in' => 'redux',
																					   )
										),
			);

		    $single_options['sidebar'] = array(
							'id'       => METAOPTION_PREFIX . $post_type .'-sidebar',
							'required' => array(METAOPTION_PREFIX . $post_type.'-layout','equals',array('right_sidebar','left_sidebar')),  
							'type'     => 'select',
							'data'     => 'sidebars',
							'multi'    => false,
							'title'    => esc_html__('Select Sidebar', 'plethora-framework'), 
			);

		    $single_options['colorset'] = array(
							'id'      => METAOPTION_PREFIX . $post_type  .'-colorset',
							'type'    => 'button_set',
							'title'   => esc_html__('Content Section Color Set', 'plethora-framework' ),
							'desc'    => esc_html__('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
							'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																							  'use_in'          => 'redux',
																							  'prepend_options' => array(  'foo' => esc_html__('Default', 'plethora-framework') ) ) ),
			);

		    $single_options['title'] = array(
							'id'       => METAOPTION_PREFIX . $post_type  .'-title',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Title', 'plethora-framework'),
							'desc'    => esc_html__('Enable/disable titles section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html__('This post type supports title/subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support title', 'plethora-framework') .'</div>',
							'options'  => array(
											1 => esc_html__('Display', 'plethora-framework'),
											0 => esc_html__('Hide', 'plethora-framework'),
										),
			);

		    $single_options['subtitle'] = array(
							'id'      => METAOPTION_PREFIX . $post_type  .'-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Subtitle', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html__('This post type supports title/subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support title', 'plethora-framework') .'</div>',
							'options' => array(
											1 => esc_html__('Display', 'plethora-framework'),
											0 => esc_html__('Hide', 'plethora-framework'),
										),
			);

		    $single_options['subtitle-text'] = array(
							'id'       => METAOPTION_PREFIX . $post_type  .'-subtitle-text',
							'type'     => 'text',
							'title'    => esc_html__('Subtitle', 'plethora-framework'), 
							'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html__('This post type supports title/subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support title', 'plethora-framework') .'</div>',
							'translate' => true,
			);

		    $single_options['mediadisplay'] = array(
							'id'       => METAOPTION_PREFIX . $post_type  .'-mediadisplay',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Featured Media', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html__('This post type supports featured image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support featured image', 'plethora-framework') .'</div>',
			);

		    $single_options['media-stretch'] = array(
							'id'       => METAOPTION_PREFIX . $post_type  .'-media-stretch',
							'type'     => 'button_set', 
							'title'    => esc_html__('Media Display Ratio', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html__('This post type supports featured image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support featured image', 'plethora-framework') .'</div>',
							'desc'     => esc_html__('Will be applied on single AND listing view', 'plethora-framework'),
							'options'  => Plethora_Module_Style::get_options_array( array( 
													'type'            => 'stretchy_ratios',
													'prepend_options' => array( 'foo_stretch' => esc_html__('Native Ratio', 'plethora-framework' ) ),
			                                        )),            
			);

		    if ( $post_type === 'post' ) { 

			    $single_options['info-primarytax'] = array(
							'id'      => METAOPTION_PREFIX . $post_type  .'-categories',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Categories Info', 'plethora-framework'),
				);

			    $single_options['info-secondarytax'] = array(
							'id'      => METAOPTION_PREFIX . $post_type  .'-tags',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Tags Info', 'plethora-framework'),
				);

			} else {

			    // only for CPTs
			    $single_options['info-primarytax'] = array(
							'id'    => METAOPTION_PREFIX . $post_type .'-info-primarytax',
							'type'  => 'switch', 
							'title' => sprintf( esc_html__('Display Primary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
							'desc'  => sprintf( esc_html__('You may choose the primary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),

				);
			    $single_options['info-primarytax-slug'] = array(
							'id'       => METAOPTION_PREFIX . $post_type .'-info-primarytax-slug',
							'required' => array( METAOPTION_PREFIX . $post_type .'-info-primarytax','=', 1),						
							'type'     => 'select', 
							'title'    => esc_html__('Set Primary Taxonomy Label', 'plethora-framework'),
							'desc'     => esc_html__('You should select a taxonomy that is associated with the specific post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
							'data'     => 'taxonomies',
							'args'     => array( 'public' => 1 ),
				);

			    // only for CPTs
			    $single_options['info-secondarytax'] = array(
							'id'    => METAOPTION_PREFIX . $post_type .'-info-secondarytax',
							'type'  => 'switch', 
							'title' => sprintf( esc_html__('Display Secondary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
							'desc'  => sprintf( esc_html__('You may choose the secondary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
				);
			    $single_options['info-secondarytax-slug'] = array(
							'id'       => METAOPTION_PREFIX . $post_type .'-info-secondarytax-slug',
							'required' => array( METAOPTION_PREFIX . $post_type .'-info-secondarytax','=', 1),						
							'type'     => 'select', 
							'title'    => esc_html__('Set Secondary Taxonomy Label', 'plethora-framework'),
							'desc'     => esc_html__('You should select a taxonomy that is associated with the specific post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
							'data'     => 'taxonomies',
							'args'     => array( 'public' => 1 ),
				);

			}

		    $single_options['author'] = array(
							'id'       => METAOPTION_PREFIX . $post_type  .'-author',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Author Info', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'author' ) ? '<span style="color:green">'. esc_html__('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support authors', 'plethora-framework') .'</div>',
			);

		    $single_options['date'] = array(
							'id'    => METAOPTION_PREFIX . $post_type  .'-date',
							'type'  => 'switch', 
							'title' => esc_html__('Display Date Info', 'plethora-framework'),
			);

		    $single_options['comments'] = array(
							'id'       => METAOPTION_PREFIX . $post_type  .'-comments',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Comments Count Info', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'comments' ) ? '<span style="color:green">'. esc_html__('This post type supports comments', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support comments', 'plethora-framework') .'</div>',
			);

		    $single_options['singleview-end'] = array(
							'id'     => $post_type .'-singleview-end',
							'type'   => 'section',
							'indent' => false,
			);

		    if ( $post_type_obj->has_archive ) { // should be displayed only if this post type supports archive views

			    $single_options['listview-start'] = array(
								'id'       => $post_type .'-listview-start',
								'type'     => 'section',
								'title'    => sprintf( esc_html__( '%1$s Archive Listing View', 'plethora-framework' ), $post_type_label ),
								'subtitle' => sprintf( esc_html__( 'These options affect this %1$s\'s display when displayed as a listing', 'plethora-framework' ), $post_type_label_singular ),
								'indent'   => true,
				);

			    $single_options['archive-mediadisplay'] = array(
								'id'       => METAOPTION_PREFIX .'archive'. $post_type  .'-mediadisplay',
								'type'     => 'button_set', 
								'title'    => esc_html__('Featured Media Display', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html__('This post type supports featured image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support featured image', 'plethora-framework') .'</div>',
								'desc'     => '<strong>'. esc_html__('According To Post Format', 'plethora-framework') .'</strong> '. esc_html__('will display the featured video/audio in posts list (according on its post format).', 'plethora-framework') . esc_html__('You can set the post format on Format box on the right', 'plethora-framework'),
								'options'  => array(
										'inherit'       => 'According To Post Format',
										'featuredimage' => 'Force Featured Image Display',
										'hide'          => 'Do Not Display',
										),
				);

			    $single_options['archive-listing-content'] = array(
								'id'       => METAOPTION_PREFIX .'archive'. $post_type  .'-listing-content',
								'type'     => 'button_set', 
								'title'    => esc_html__('Content/Excerpt Display', 'plethora-framework'), 
								'subtitle' => post_type_supports( $post_type, 'editor' ) ? '<span style="color:green">'. esc_html__('This post type supports editor content', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support editor content', 'plethora-framework') .'</div>',
								'desc'    => esc_html__('Displaying content will allow you to display posts containing the WP editor "More" tag.', 'plethora-framework'),
								'options'  => array(
										'excerpt' => esc_html__('Display Excerpt', 'plethora-framework'), 
										'content' => esc_html__('Display Content', 'plethora-framework') 
									)
				);

			    $single_options['archive-listing-subtitle'] = array(
								'id'       => METAOPTION_PREFIX .'archive'. $post_type  .'-listing-subtitle',
								'type'     => 'switch', 
								'title'    => esc_html__('Display Subtitle', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html__('This post type supports title/subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support title/subtitle', 'plethora-framework') .'</div>',
								'options'  => array(
												1 => esc_html__('Display', 'plethora-framework'),
												0 => esc_html__('Hide', 'plethora-framework'),
											),
				);

			    if ( $post_type === 'post' ) {


				    $single_options['archive-info-primarytax'] = array(
									'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-info-category',
									'type'    => 'switch', 
									'title'   => esc_html__('Display Categories', 'plethora-framework'),
					);

				    $single_options['archive-info-secondarytax'] = array(
									'id'      => METAOPTION_PREFIX .'archive'.$post_type.'-info-tags',
									'type'    => 'switch', 
									'title'   => esc_html__('Display Tags', 'plethora-framework'),
					);

				} else {

				    $single_options['archive-info-primarytax'] = array(
									'id'    => METAOPTION_PREFIX .'archive'. $post_type .'-info-primarytax',
									'type'  => 'switch', 
									'title' => sprintf( esc_html__('Display Primary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
									'desc'  => sprintf( esc_html__('You may choose the primary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
					);

				    $single_options['archive-info-secondarytax'] = array(
									'id'    => METAOPTION_PREFIX .'archive'. $post_type .'-info-secondarytax',
									'type'  => 'switch', 
									'title' => sprintf( esc_html__('Display Secondary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
									'desc'  => sprintf( esc_html__('You may choose the secondary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
					);


				}

			    $single_options['archive-info-author'] = array(
								'id'       => METAOPTION_PREFIX .'archive'. $post_type  .'-info-author',
								'type'     => 'switch', 
								'subtitle' => post_type_supports( $post_type, 'author' ) ? '<span style="color:green">'. esc_html__('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support authors', 'plethora-framework') .'</div>',
								'title'    => esc_html__('Display Author Info', 'plethora-framework'),
				);

			    $single_options['archive-info-date'] = array(
								'id'    => METAOPTION_PREFIX .'archive'. $post_type  .'-info-date',
								'type'  => 'switch', 
								'title' => esc_html__('Display Date Info', 'plethora-framework'),
				);

			    $single_options['archive-info-comments'] = array(
								'id'       => METAOPTION_PREFIX .'archive'. $post_type  .'-info-comments',
								'type'     => 'switch', 
								'title'    => esc_html__('Display Comments Count Info', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'comments' ) ? '<span style="color:green">'. esc_html__('This post type supports comments', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html__('This post type does not support comments', 'plethora-framework') .'</div>',
				);

			    $single_options['archive-show-linkbutton'] = array(
								'id'    => METAOPTION_PREFIX .'archive'. $post_type  .'-show-linkbutton',
								'type'  => 'switch', 
								'title' => esc_html__('Display "Read More" Button', 'plethora-framework'),
				);

			    $single_options['listview-end'] = array(
								'id'     => $post_type  .'-listview-end',
								'type'   => 'section',
								'indent' => false,
				);
			}

			// Additional fields added on Avoir >>> START
		    $single_options['containertype'] = array(
							'id'      => METAOPTION_PREFIX . $post_type .'-containertype',
							'type'    => 'button_set', 
							'title'   => esc_html__('Container Type', 'plethora-framework'),
							'options' => array(
											'container'       => esc_html__( 'Default', 'plethora-framework'),
											'container-fluid' => esc_html__( 'Fluid', 'plethora-framework'),
										)
			);
		    $single_options['excerpt'] = array(
							'id'      => METAOPTION_PREFIX . $post_type .'-excerpt',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Excerpt', 'plethora-framework'),
			);

		    $single_options['divider'] = array(
							'id'      => METAOPTION_PREFIX . $post_type .'-divider',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Divider', 'plethora-framework'),
			);

		    $single_options['content-align'] = array(
							'id'      => METAOPTION_PREFIX . $post_type .'-contentalign',
							'type'    => 'button_set', 
							'title'   => esc_html__('Content Section Align', 'plethora-framework'),
							'desc'    => esc_html__('Affects all content section text alignment, except editor text.', 'plethora-framework'),
							'options' => array(
											''            => esc_html__( 'Left', 'plethora-framework'),
											'text-center' => esc_html__( 'Center', 'plethora-framework'),
											'text-right'  => esc_html__( 'Right', 'plethora-framework'),
										 )
			);

		    $single_options['featured'] = array(
							'id'    => METAOPTION_PREFIX . $post_type .'-featured',
							'type'  => 'switch', 
							'title' => esc_html__('Featured Post', 'plethora-framework'),
							'desc'  => esc_html__('Setting this post as featured, will give it special treatment on several shortcode displays ( i.e. Posts Loop shortcode ).', 'plethora-framework'),
			);

		    $single_options['extraclass'] = array(
							'id'      => METAOPTION_PREFIX . $post_type .'-extraclass',
							'type'    => 'text', 
							'title'   => esc_html__('Extra Classes', 'plethora-framework'),
							'desc'    => esc_html__('Style content container differently - add one or multiple class names and refer to them in custom CSS.', 'plethora-framework'),
			);
			// Additional fields added on Avoir >>> END

			return $single_options;
        }

		/** 
		* Archive view CONFIGURATION for theme options and metabox panels
		* Common for native posts and user set CPTs
		*/
		public function archive_options_config() {

			$config = array(
						array( 
							'id'                    => 'page-start', 
							'theme_options'         => true, 
							'theme_options_default' => NULL,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'layout', 
							'theme_options'         => true, 
							'theme_options_default' => 'right_sidebar',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'sidebar', 
							'theme_options'         => true, 
							'theme_options_default' => 'sidebar-default',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'colorset', 
							'theme_options'         => true, 
							'theme_options_default' => 'foo',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title-text', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'The Blog', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title-tax', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title-author', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title-date', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle-text', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Articles & News', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle-tax', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'author-subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'date-subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 0,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listings-start', 
							'theme_options'         => true, 
							'theme_options_default' => NULL,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listtype', 
							'theme_options'         => true, 
							'theme_options_default' => 'classic',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'mediadisplay', 
							'theme_options'         => true, 
							'theme_options_default' => 'inherit',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listing-content', 
							'theme_options'         => true, 
							'theme_options_default' => 'content',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listing-subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 0,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-primarytax', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-secondarytax', 
							'theme_options'         => true, 
							'theme_options_default' => 0,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-author', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-date', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-comments', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'show-linkbutton', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'show-linkbutton-text', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Read More', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'noposts-title', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'No posts where found!', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'noposts-description', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__( 'Unfortunately, no posts were found! Please try again soon!', 'plethora-framework' ),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
			);

			return $config;
		}

		/** 
		* Posts single view options_config for theme options and metabox panels
		*/
		public function single_options_config() {

			$config = array(
						array( 
							'id'                    => 'singleview-start', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'layout', 
							'theme_options'         => true, 
							'theme_options_default' => 'right_sidebar',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'sidebar', 
							'theme_options'         => true, 
							'theme_options_default' => 'sidebar-default',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'colorset', 
							'theme_options'         => true, 
							'theme_options_default' => 'foo',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => 0,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle-text', 
							'theme_options'         => false, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'mediadisplay', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'media-stretch', 
							'theme_options'         => true, 
							'theme_options_default' => 'stretchy_wrapper ratio_2-1',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-primarytax', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-primarytax-slug', 
							'theme_options'         => true, 
							'theme_options_default' => 'category',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-secondarytax', 
							'theme_options'         => true, 
							'theme_options_default' => 0,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'info-secondarytax-slug', 
							'theme_options'         => true, 
							'theme_options_default' => 'post_tag',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'author', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'date', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'comments', 
							'theme_options'         => true, 
							'theme_options_default' => 1,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'listview-start', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-mediadisplay', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-listing-content', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-listing-subtitle', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-info-primarytax', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-info-secondarytax', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-info-author', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-info-date', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-info-comments', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'archive-show-linkbutton', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
			);

			return $config;
		}
	}
}