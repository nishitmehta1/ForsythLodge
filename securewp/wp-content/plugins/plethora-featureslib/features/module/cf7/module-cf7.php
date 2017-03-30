<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2014-2015

Contact Form 7 module base class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Cf7') ) {


	/**
	*/
	class Plethora_Module_Cf7 {

		public static $feature_title         = "Contact Forms 7 Compatibility Module";                                             // FEATURE DISPLAY TITLE
		public static $feature_description   = ""; // FEATURE DISPLAY DESCRIPTION 
		public static $theme_option_control  = true;                                                               // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL?
		public static $theme_option_default  = true;                                                               // DEFAULT ACTIVATION OPTION STATUS 
		public static $theme_option_requires = array();                                                            // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;                                                               // DYNAMIC CLASS CONSTRUCTION? 
		public static $dynamic_method        = false;                                                              // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

		function __construct() {

			if ( class_exists( 'WPCF7' ) ) {

				// CF7 element replacements
				add_filter( 'wpcf7_form_elements', array( $this, 'form_elements') );

				// Custom CF7 fields
				add_action( 'wpcf7_init', array( $this, 'add_custom_fields' ) );
				
				// Add generator menu for custom CF7 fields 
				add_action( 'wpcf7_admin_init', array( $this, 'add_generator_menu' ), 500 );
 
				// HTML5 support options
				$this->html5_support();

				if ( is_admin() ) {

					add_filter( 'plethora_themeoptions_modules', array( $this, 'theme_options_tab'), 10);
				}
			}
		} 

		/**
		* Use this to manipulate Contact Form 7 default markup and styling
		* Hooked @ 'wpcf7_form_elements'
		*/
		public function form_elements( $content ) {

			return $content;  
		}

		/**
		* Options for HTML5 form fields
		* Read more here: http://contactform7.com/faq/does-contact-form-7-support-html5-input-types/
		*/
		public function html5_support() {

			$html5_support          = Plethora_Theme::option( THEMEOPTION_PREFIX .'cf7-html5', true );
			$html5_support_fallback = Plethora_Theme::option( THEMEOPTION_PREFIX .'cf7-html5-fallback', true );
			
			// HTML5 fields supported by default on CF7. 
			// We remove this support if disabled via Theme Options panel
			if ( ! $html5_support_fallback ) {

				add_filter( 'wpcf7_support_html5', '__return_false' );
			}

			// IMPORTANT: date support fallback for Firefox(!)
			if ( $html5_support_fallback ) {

				add_filter( 'wpcf7_support_html5_fallback', '__return_true' );
			}
		}

		/**
		* Add custom CF7 fields
		*/
		public function add_custom_fields() {

			if ( function_exists( 'wpcf7_add_form_tag' ) ) {

				// Posts Select Field
				if ( $this->get_customfield_status( 'select_posts' ) ) {

					wpcf7_add_form_tag( array( 'select_posts', 'select_posts*' ), array( $this, 'select_posts_handler' ), true );
				}
				// Categories Select Field
				if ( $this->get_customfield_status( 'select_categories' ) ) {

					wpcf7_add_form_tag( array( 'select_categories', 'select_categories*' ), array( $this, 'select_categories_handler' ), true );
				}
			}
		}

		/**
		* Add custom CF7 fields generator menu
		*/
		public function add_generator_menu() {

			if ( class_exists( 'WPCF7_TagGenerator' ) ) {

				$tag_generator = WPCF7_TagGenerator::get_instance();

				// Posts Select Field
				if ( $this->get_customfield_status( 'select_posts' ) ) {

					$tag_generator->add( 'posts', esc_html__( 'Select posts', 'plethora-framework' ) .' ( '. esc_html__( 'by', 'plethora-framework' ) .' '. THEME_DISPLAYNAME .')', array( $this, 'select_posts_generator_menu' ) );
				}

				// Categories Select Field
				if ( $this->get_customfield_status( 'select_categories' ) ) {

					$tag_generator->add( 'terms', esc_html__( 'Select terms', 'plethora-framework' ) .' ( '. esc_html__( 'by', 'plethora-framework' ) .' '. THEME_DISPLAYNAME .')', array( $this, 'select_categories_generator_menu' ) );
				}
			}
		}


		/**
		* Posts Select Field
		*/
		public function select_posts_handler( $tag ) {

			$tag = new WPCF7_FormTag( $tag );
			if ( empty( $tag->name ) ) { return ''; }
			$validation_error = wpcf7_get_validation_error( $tag->name );

			$class = wpcf7_form_controls_class( $tag->type );

			if ( $validation_error )
				$class .= ' wpcf7-not-valid';

			$atts = array();

			$atts['class'] = $tag->get_class_option( $class );
			$atts['id'] = $tag->get_id_option();
			$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

			if ( $tag->is_required() )
				$atts['aria-required'] = 'true';

			$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

			$include_blank  = $tag->has_option( 'include_blank' );
			$multiple       = $tag->has_option( 'multiple' );
			$post_type      = $tag->has_option( 'post_type' ) ? $tag->get_option( 'post_type' ) : 'post';
			$posts_per_page = $tag->has_option( 'limit' ) ? $tag->get_option( 'limit' ) : -1;
			$order_by       = $tag->has_option( 'order_by' ) ? $tag->get_option( 'order_by' ) : 'menu_order';
			$order          = $tag->has_option( 'order' ) ? $tag->get_option( 'order' ) : 'ASC';
			$which_val      = $tag->has_option( 'which_val' ) ? $tag->get_option( 'which_val' ) : 'post_title';

			// Get values according to selected post type
			$post_args = array(
				'posts_per_page'   => ( ( $posts_per_page[0] == 0 ) ? -1 : $posts_per_page[0] ),
				'orderby'          => $order_by[0],
				'order'            => $order[0],
				'post_type'        => $post_type[0],
				'post_status'      => 'publish',
				'suppress_filters' => false,
			);
			$posts = get_posts( $post_args );
			$values = array();
			$labels = array();
			foreach ( $posts as $post ) {
				// had to do a switch implementation here, to avoid string issues (!!)
				switch ( $which_val[0] ) {
					case 'ID':
						$values[] = $post->ID;
						break;
					case 'post_name':
						$values[] = $post->post_name;
						break;
					
					default:
					case 'post_title':
						$values[] = $post->post_title;
						break;
				}

				$labels[] = $post->post_title;
			}
			wp_reset_postdata();

			if ( $data = (array) $tag->get_data_option() ) {
				$values = array_merge( $values, array_values( $data ) );
				$labels = array_merge( $labels, array_values( $data ) );
			}

			$defaults = array();

			$default_choice = $tag->get_default_option( null, 'multiple=1' );

			foreach ( $default_choice as $value ) {
				$key = array_search( $value, $values, true );

				if ( false !== $key ) {
					$defaults[] = (int) $key + 1;
				}
			}

			if ( $matches = $tag->get_first_match_option( '/^default:([0-9_]+)$/' ) ) {
				$defaults = array_merge( $defaults, explode( '_', $matches[1] ) );
			}

			$defaults = array_unique( $defaults );

			$shifted = false;

			if ( $include_blank || empty( $values ) ) {
				array_unshift( $labels, '---' );
				array_unshift( $values, '' );
				$shifted = true;
			}

			$html = '';
			$hangover = wpcf7_get_hangover( $tag->name );

			foreach ( $values as $key => $value ) {
				$selected = false;

				if ( $hangover ) {
					if ( $multiple ) {
						$selected = in_array( esc_sql( $value ), (array) $hangover );
					} else {
						$selected = ( $hangover == esc_sql( $value ) );
					}
				} else {
					if ( ! $shifted && in_array( (int) $key + 1, (array) $defaults ) ) {
						$selected = true;
					} elseif ( $shifted && in_array( (int) $key, (array) $defaults ) ) {
						$selected = true;
					}
				}

				$item_atts = array(
					'value' => $value,
					'selected' => $selected ? 'selected' : '' );

				$item_atts = wpcf7_format_atts( $item_atts );

				$label = isset( $labels[$key] ) ? $labels[$key] : $value;

				$html .= sprintf( '<option %1$s>%2$s</option>',
					$item_atts, esc_html( $label ) );
			}

			if ( $multiple )
				$atts['multiple'] = 'multiple';

			$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

			$atts = wpcf7_format_atts( $atts );

			$html = sprintf(
				'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
				sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

			return $html;
		}

		/**
		* Categories Select Field
		*/
		public function select_categories_handler( $tag ) {

			$tag = new WPCF7_FormTag( $tag );
			if ( empty( $tag->name ) ) { return ''; }
			$validation_error = wpcf7_get_validation_error( $tag->name );

			$class = wpcf7_form_controls_class( $tag->type );

			if ( $validation_error )
				$class .= ' wpcf7-not-valid';

			$atts = array();

			$atts['class'] = $tag->get_class_option( $class );
			$atts['id'] = $tag->get_id_option();
			$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

			if ( $tag->is_required() )
				$atts['aria-required'] = 'true';

			$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

			// get field configuration...all values are arrays, use first array element to get values
			$include_blank = $tag->has_option( 'include_blank' );
			$multiple      = $tag->has_option( 'multiple' );
			$taxonomy      = $tag->has_option( 'taxonomy' ) ? $tag->get_option( 'taxonomy' ) : 'category';
			$hide_empty    = $tag->has_option( 'hide_empty' ) ? 1 : 0;
			$limit         = $tag->has_option( 'limit' ) ? $tag->get_option( 'limit' ) : 0;
			$order_by      = $tag->has_option( 'order_by' ) ? $tag->get_option( 'order_by' ) : 'menu_order';
			$order         = $tag->has_option( 'order' ) ? $tag->get_option( 'order' ) : 'ASC';
			$which_val     = $tag->has_option( 'which_val' ) ? $tag->get_option( 'which_val' ) : 'name';
			
			// Get values according to selected post type
			$terms_args = array(
				'number'     => ( ( $limit[0] == -1 ) ? 0 : $limit[0] ),
				'orderby'    => $order_by[0],
				'order'      => $order[0],
				'taxonomy'   => $taxonomy[0],
				'hide_empty' => $hide_empty,
			);
			$terms = get_terms( $terms_args );
			$values = array();
			$labels = array();

			if ( !is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					// had to do a switch implementation here, to avoid string issues (!!)
					switch ( $which_val[0] ) {
						case 'description':
							$values[] = $term->description;
							break;
						case 'slug':
							$values[] = $term->slug;
							break;
						
						default:
						case 'name':
							$values[] = $term->name;
							break;
					}
					$labels[] = $term->name;
				}
			}
			wp_reset_postdata();

			if ( $data = (array) $tag->get_data_option() ) {
				$values = array_merge( $values, array_values( $data ) );
				$labels = array_merge( $labels, array_values( $data ) );
			}

			$defaults = array();

			$default_choice = $tag->get_default_option( null, 'multiple=1' );

			foreach ( $default_choice as $value ) {
				$key = array_search( $value, $values, true );

				if ( false !== $key ) {
					$defaults[] = (int) $key + 1;
				}
			}

			if ( $matches = $tag->get_first_match_option( '/^default:([0-9_]+)$/' ) ) {
				$defaults = array_merge( $defaults, explode( '_', $matches[1] ) );
			}

			$defaults = array_unique( $defaults );

			$shifted = false;

			if ( $include_blank || empty( $values ) ) {
				array_unshift( $labels, '---' );
				array_unshift( $values, '' );
				$shifted = true;
			}

			$html = '';
			$hangover = wpcf7_get_hangover( $tag->name );

			foreach ( $values as $key => $value ) {
				$selected = false;

				if ( $hangover ) {
					if ( $multiple ) {
						$selected = in_array( esc_sql( $value ), (array) $hangover );
					} else {
						$selected = ( $hangover == esc_sql( $value ) );
					}
				} else {
					if ( ! $shifted && in_array( (int) $key + 1, (array) $defaults ) ) {
						$selected = true;
					} elseif ( $shifted && in_array( (int) $key, (array) $defaults ) ) {
						$selected = true;
					}
				}

				$item_atts = array(
					'value' => $value,
					'selected' => $selected ? 'selected' : '' );

				$item_atts = wpcf7_format_atts( $item_atts );

				$label = isset( $labels[$key] ) ? $labels[$key] : $value;

				$html .= sprintf( '<option %1$s>%2$s</option>',
					$item_atts, esc_html( $label ) );
			}

			if ( $multiple )
				$atts['multiple'] = 'multiple';

			$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

			$atts = wpcf7_format_atts( $atts );

			$html = sprintf(
				'<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
				sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

			return $html;
		}

		function select_posts_generator_menu( $contact_form, $args = '' ) {

			$args = wp_parse_args( $args, array() );
			?>
			<div class="control-box">
				<fieldset>
					<legend>
						<?php echo esc_html__( 'Create a select field that will display dynamically posts of a given post type.', 'plethora-framework'); ?>
						<?php echo sprintf( esc_html__( 'This is a custom CF7 field, available on all Plethora themes. More details on %1$s%3$s documentation%2$s', 'plethora-framework'), '<a href="'.THEME_DOCURL.'">', '</a>', THEME_DISPLAYNAME ); ?>
					</legend>
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Field type', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'plethora-framework' ) ); ?></legend>
										<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'plethora-framework' ) ); ?></label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Field Name Attribute', 'plethora-framework' ) ); ?></label></th>
								<td>
									<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" />
									<br><small><?php echo sprintf( esc_html__( 'If you plan to use this form in conjuction with Plethora\'s call to form shortcodes or widgets, please avoid giving a name identical to post or tax slugs, ( i.e. avoid using %1$spost%2$s or %1$scategory%2$s or %1$spost_tag%2$s )', 'plethora-framework' ), '<strong>', '<strong>' ); ?></small>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Post Type', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Post Type', 'plethora-framework' ) ); ?></legend>
										<?php
										$supported_post_types = Plethora_Theme::get_supported_post_types( array( 'output' => 'objects', 'exclude' => 'page' ) );
										foreach ( $supported_post_types as $post_type => $post_type_obj ) {
											$checked = $post_type === 'post' ? ' checked' : '';
										?>
										<label class="plethora_cf7_label_for_radio"><input type="radio" name="post_type" value="<?php echo esc_attr( $post_type ); ?>" class="option"<?php echo $checked; ?>/><?php echo esc_html( $post_type_obj->label ); ?> ( slug: <strong><?php echo esc_html( $post_type ); ?></strong> )</label>
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Post Option Value', 'plethora-framework' ) ); ?>
									<br><small><?php echo esc_html( __( 'The value(s) that you will receive in mail )', 'plethora-framework' ) ); ?></small>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Post Option Value', 'plethora-framework' ) ); ?></legend>
										<?php
										$which_vals = array(
											'post_title' => esc_html__( 'Post title ( suggested )', 'plethora-framework' ),
											'post_name'  => esc_html__( 'Post slug', 'plethora-framework' ),
											'ID'         => esc_html__( 'Post ID', 'plethora-framework' )
										);
										foreach ( $which_vals as $which_val => $which_val_label ) {
											$checked = $which_val === 'post_title' ? ' checked' : '';
										?>
										<label class="plethora_cf7_label_for_radio"><input type="radio" name="which_val" value="<?php echo esc_attr( $which_val ); ?>" class="option"<?php echo $checked; ?>/><?php echo esc_html( $which_val_label ); ?> <small></small> </label>
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"><?php echo esc_html( __( 'Posts Limit', 'plethora-framework' ) ); ?></label></th>
								<td><input type="number" name="limit" class="limitvalue oneline numeric option" id="<?php echo esc_attr( $args['content'] . '-limit' ); ?>" value="0" min="0" /> <small><?php echo esc_html__( '( 0 will return all )', 'plethora-framework' ); ?></small></td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Order Posts By', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Order Posts By', 'plethora-framework' ) ); ?></legend>
										<?php
										$order_bys  = array(
											'ID'            => esc_html__( 'ID', 'plethora-framework' ),
											'author'        => esc_html__( 'Author', 'plethora-framework' ),
											'title'         => esc_html__( 'Title', 'plethora-framework' ),
											'date'          => esc_html__( 'Date created', 'plethora-framework' ),
											'modified'      => esc_html__( 'Date modified', 'plethora-framework' ),
											'parent'        => esc_html__( 'Parent term', 'plethora-framework' ),
											'rand'          => esc_html__( 'Random', 'plethora-framework' ),
											'comment_count' => esc_html__( 'Comment count', 'plethora-framework' ),
											'menu_order'    => esc_html__( 'Menu order', 'plethora-framework' ),
										);
										foreach ( $order_bys as $order_by => $order_by_label ) {
											$checked = $order_by === 'menu_order' ? ' checked' : '';
										?>
										<label class="plethora_cf7_label_for_radio"><input type="radio" name="order_by" value="<?php echo esc_attr( $order_by ); ?>" class="option"<?php echo $checked; ?>/><?php echo esc_html( $order_by_label ); ?> </label>
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Order Posts', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Order Posts', 'plethora-framework' ) ); ?></legend>
										<?php
										$orders    = array(
											'ASC'  => esc_html__( 'Ascending', 'plethora-framework' ),
											'DESC' => esc_html__( 'Descending', 'plethora-framework' )
										);
										foreach ( $orders as $order => $order_label ) {
											$checked = $order === 'ASC' ? ' checked' : '';
										?>
										<label class="plethora_cf7_label_for_radio"><input type="radio" name="order" value="<?php echo esc_attr( $order ); ?>" class="option"<?php echo $checked; ?>/><?php echo esc_html( $order_label ); ?> </label>
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Other Options', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Other Options', 'plethora-framework' ) ); ?></legend>
										<label><input type="checkbox" name="default:get" class="option" /> <?php echo esc_html( __( 'Accept default value from URL variable', 'plethora-framework' ) ); ?></label><br />
										<label><input type="checkbox" name="multiple" class="option" /> <?php echo esc_html( __( 'Allow multiple selection', 'plethora-framework' ) ); ?></label><br />
										<label><input type="checkbox" name="include_blank" class="option" /> <?php echo esc_html( __( 'Insert a blank item as the first option', 'plethora-framework' ) ); ?></label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'plethora-framework' ) ); ?></label></th>
								<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
							</tr>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'plethora-framework' ) ); ?></label></th>
								<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="insert-box">
				<input type="text" name="select_posts" class="tag code" readonly="readonly" onfocus="this.select()" />
				<div class="submitbox">
					<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'plethora-framework' ) ); ?>" />
				</div>
				<br class="clear" />
				<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'plethora-framework' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
			</div>
			<?php
		}

		function select_categories_generator_menu( $contact_form, $args = '' ) {

			$args = wp_parse_args( $args, array() );
			?>
			<div class="control-box">
				<fieldset>
					<legend>
						<?php echo esc_html__( 'Create a select field that will display dynamically the terms of a given taxonomy.', 'plethora-framework'); ?>
						<?php echo sprintf( esc_html__( 'This is a custom CF7 field, available on all Plethora themes. More details on %1$s%3$s documentation%2$s', 'plethora-framework'), '<a href="'.THEME_DOCURL.'">', '</a>', THEME_DISPLAYNAME ); ?>
					</legend>
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Field type', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'plethora-framework' ) ); ?></legend>
										<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'plethora-framework' ) ); ?></label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Field Name Attribute', 'plethora-framework' ) ); ?></label></th>
								<td>
									<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" />
									<br><small><?php echo sprintf( esc_html__( 'If you plan to use this form in conjuction with Plethora\'s call to form shortcodes or widgets, please avoid giving a name identical to post or tax slugs, ( i.e. avoid using %1$spost%2$s or %1$scategory%2$s or %1$spost_tag%2$s )', 'plethora-framework' ), '<strong>', '<strong>' ); ?></small>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Taxonomy', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Taxonomy', 'plethora-framework' ) ); ?></legend>
										<?php
										$supported_taxonomies = Plethora_Theme::get_supported_taxonomies( array( 'exclude' => 'post_format' ) );
										foreach ( $supported_taxonomies as $taxonomy ) {
											$checked = $taxonomy->name === 'category' ? ' checked' : '';
										?>
										<label class="plethora_cf7_label_for_radio"><input type="radio" name="taxonomy" value="<?php echo esc_attr( $taxonomy->name ); ?>" class="option"<?php echo $checked; ?>/><?php echo esc_html( $taxonomy->label ); ?> ( taxonomy slug: <strong><?php echo esc_html( $taxonomy->name  ); ?></strong> )</label>
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<?php echo esc_html( __( 'Term Option Value', 'plethora-framework' ) ); ?>
									<br><small><?php echo esc_html( __( 'The value(s) that you will receive in mail )', 'plethora-framework' ) ); ?></small>
								</th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Term Option Value', 'plethora-framework' ) ); ?></legend>
										<?php
										$which_vals    = array(
											'name'        => esc_html__( 'Term Name', 'plethora-framework' ),
											'description' => esc_html__( 'Term Description', 'plethora-framework' ),
											'slug'        => esc_html__( 'Term Slug', 'plethora-framework' ),
										);
										foreach ( $which_vals as $which_val => $which_val_label ) {
											$checked = $which_val === 'name' ? ' checked' : '';
										?>
										<label class="plethora_cf7_label_for_radio"><input type="radio" name="which_val" value="<?php echo esc_attr( $which_val ); ?>" class="option"<?php echo $checked; ?>/><?php echo esc_html( $which_val_label ); ?> </label>
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"><?php echo esc_html( __( 'Terms Limit', 'plethora-framework' ) ); ?></label></th>
								<td><input type="number" name="limit" class="limitvalue oneline numeric option" id="<?php echo esc_attr( $args['content'] . '-limit' ); ?>" value="0" min="0" /> <small><?php echo esc_html__( '( 0 will return all )', 'plethora-framework' ); ?></small></td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Terms Ordered By', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Terms Ordered By', 'plethora-framework' ) ); ?></legend>
										<?php
										$order_bys  = array(
											'name'        => esc_html__( 'Name', 'plethora-framework' ),
											'slug'        => esc_html__( 'Slug', 'plethora-framework' ),
											'term_id'     => esc_html__( 'ID', 'plethora-framework' ),
											'description' => esc_html__( 'Description', 'plethora-framework' ),
											'count'       => esc_html__( 'Related posts count', 'plethora-framework' ),
											'menu_order'  => esc_html__( 'Menu order', 'plethora-framework' ),
										);
										foreach ( $order_bys as $order_by_slug => $order_by_label ) {
											$checked = $order_by_slug === 'name' ? ' checked' : '';
										?>
										<label class="plethora_cf7_label_for_radio"><input type="radio" name="order_by" value="<?php echo esc_attr( $order_by_slug ); ?>" class="option"<?php echo $checked; ?>/><?php echo esc_html( $order_by_label ); ?> </label>
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Terms Order', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Terms Order', 'plethora-framework' ) ); ?></legend>
										<?php
										$orders    = array(
											'ASC'  => esc_html__( 'Ascending', 'plethora-framework' ),
											'DESC' => esc_html__( 'Descending', 'plethora-framework' )
										);
										foreach ( $orders as $order => $order_label ) {
											$checked = $order === 'ASC' ? ' checked' : '';
										?>
										<label class="plethora_cf7_label_for_radio"><input type="radio" name="order" value="<?php echo esc_attr( $order ); ?>" class="option"<?php echo $checked; ?>/><?php echo esc_html( $order_label ); ?> </label>
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo esc_html( __( 'Other Options', 'plethora-framework' ) ); ?></th>
								<td>
									<fieldset>
										<legend class="screen-reader-text"><?php echo esc_html( __( 'Other Options', 'plethora-framework' ) ); ?></legend>
										<label><input type="checkbox" name="hide_empty" class="option" /> <?php echo esc_html( __( 'Hide terms that are not assigned to any post', 'plethora-framework' ) ); ?></label><br />
										<label><input type="checkbox" name="default:get" class="option" /> <?php echo esc_html( __( 'Accept default value from URL variable', 'plethora-framework' ) ); ?></label><br />
										<label><input type="checkbox" name="multiple" class="option" /> <?php echo esc_html( __( 'Allow multiple selection', 'plethora-framework' ) ); ?></label><br />
										<label><input type="checkbox" name="include_blank" class="option" /> <?php echo esc_html( __( 'Insert a blank item as the first option', 'plethora-framework' ) ); ?></label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'plethora-framework' ) ); ?></label></th>
								<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
							</tr>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'plethora-framework' ) ); ?></label></th>
								<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="insert-box">
				<input type="text" name="select_categories" class="tag code" readonly="readonly" onfocus="this.select()" />
				<div class="submitbox">
					<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'plethora-framework' ) ); ?>" />
				</div>
				<br class="clear" />
				<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'plethora-framework' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
			</div>
			<?php
		}


		/**
		* Returns all options configuration for the theme options panel.
		* Hooked at 'plethora_themeoptions_modules' filter
		*/
		public function theme_options_tab( $sections ) {


			$sections[] = array(
			  'subsection' => true,
			  'title'      => esc_html__('Contact Form 7', 'plethora-framework'),
			  'heading'    => esc_html__('CONTACT FROM 7', 'plethora-framework'),
			  'fields'     => array(
				  array(
					'id'          => THEMEOPTION_PREFIX .'cf7-html5',
					'type'        => 'switch',
					'title'       => esc_html__('HTML5 Support', 'plethora-framework'),
					'description' => sprintf( esc_html__('If you don’t wish to use HTML5 input types, you can disable it. %1$sRead more about this%2$s.', 'plethora-framework'),'<a href="http://contactform7.com/faq/does-contact-form-7-support-html5-input-types/" target="_blank">', '</a>' ),
					'default'     => 1,
				  ),
				  array(
					'id'          => THEMEOPTION_PREFIX .'cf7-html5-fallback',
					'type'        => 'switch',
					'title'       => esc_html__('HTML5 Support Fallback', 'plethora-framework'),
					'description' => sprintf( esc_html__('Even the most modern browser, do not provide out-of-the-box support for some HTML5 input types. Enabling this fallback will help you overcome those issues. %1$sRead more about this%2$s.', 'plethora-framework'),'<a href="http://contactform7.com/faq/does-contact-form-7-support-html5-input-types/" target="_blank">', '</a>' ),
					'default'     => 1,
					'required' => array(
									array( THEMEOPTION_PREFIX .'cf7-html5', 'equals', array( true ) )
								  )
				  ),
				  array(
					'id'       => THEMEOPTION_PREFIX .'cf7-customfields',
					'type'     => 'checkbox',
					'title'    => esc_html__('Custom Form Fields', 'plethora-framework'),
					'description' => sprintf( esc_html__('%s adds some custom fields on CF7 panel. You may want to disable some of them, in case you are using a similar third party plugin solution.', 'plethora-framework'), '<strong>'. THEME_DISPLAYNAME .'</strong>' ),
					'options'  => $this->get_customfields( 'options' ),
					'default'  => $this->get_customfields( 'defaults' ),
				  ),
				)
			);

			return $sections;
		}

		/**
		* Returns ALL VC parameters index
		* Please do not include third party VC implementations ( i.e. WP, Woo, CF7, etc. )
		* All VC deprecated elements should be set to 'vc_status' => false
		* Latest version check: 4.12.1
		*/
		public function get_customfields_index() {

			$customfields_index = array(
				'select_posts'      => array( 'desc' => esc_html__( 'Select Posts', 'plethora-framework' ), 'status' => true ),
				'select_categories' => array( 'desc' => esc_html__( 'Select Categories', 'plethora-framework' ), 'status' => true ),
			);

			// sort index according to desc
			uasort( $customfields_index, function( $a, $b ) { return strcmp($a["desc"], $b["desc"]); } );
			return $customfields_index;
		}

		/**
		* Returns all elements configuration for direct use with related 
		* option on theme options panel. 
		*/
		public function get_customfields( $return_what = 'options' ) {

			$return = array();
			$all_elements = $this->get_customfields_index();

			foreach ( $all_elements as $elem_key => $element_data ) {
				
				if ( $return_what === 'options' ) {

					$return[$elem_key] = $element_data['desc'];

				} elseif ( $return_what === 'defaults' ) {

					$return[$elem_key] = $element_data['status'] ;
				}
			}
			return $return;
		}

		public function get_customfield_status( $field ) {

			$customfields_status = Plethora_Theme::option( THEMEOPTION_PREFIX .'cf7-customfields', $this->get_customfields( 'defaults' ) );
			if ( isset( $customfields_status[$field] ) && $customfields_status[$field] ) {

				return true;
			}
			return false;
		}
	}
}