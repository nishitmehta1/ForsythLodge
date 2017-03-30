<?php

/**
 * Woocommerce functionality
 * 
 */
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Woocommerce') ) {

	class Plethora_Module_Woocommerce {
		
		public static $feature_title         = "WooCommerce Support Module";							// Feature display title  (string)
		public static $feature_description   = "Adds support for WooCommerce plugin to your theme";	// Feature display description (string)
		public static $theme_option_control  = true;													// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();									// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;												// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;										// Additional method invocation ( string/boolean | method name or false )

		public $posttype_obj;
		public $post_type                      = 'product';
		// public $post_type_archive              = true;
		// public $post_type_public               = true;
		// public $post_type_supports             = array( 'title', 'editor', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions', 'page-attributes' );
		// public $post_type_primary_tax          = 'room-category';
		// public $post_type_primary_tax_public   = false;
		// public $post_type_secondary_tax        = 'room-tag';
		// public $post_type_secondary_tax_public = false;
		public $minicart;

		public function __construct() {

			if ( class_exists('woocommerce') ) {

			$this->posttype_obj = get_post_type_object( $this->post_type );

			# Basic WooCommerce support
				add_action( 'after_setup_theme', array( $this, 'support' ) );										// Primary WC support declaration
				add_action( 'plethora_module_wpless_files', array( $this, 'enqueue_less' ), 20);  // Style enqueing - keep priority to 20 to make sure that it will be loaded after Woo defaults
				add_filter( 'plethora_supported_post_types', array( $this, 'add_product_to_supported_post_types'), 10, 2 ); // declare frontend support manually ( this is mandatory, since there is not Plethora_Posttype_Product class )
				add_filter( 'plethora_this_page_id', array( $this, 'this_page_id') ); // filter page id return for the static shop page

			# Admin: theme Options & metaboxes
				add_filter( 'plethora_themeoptions_content', array($this, 'archive_themeoptions'), 10);				// Theme Options // Archive
				add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 120);				// Theme Options // Single Post
				add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));								// Metabox // Single Post	

			# Front: Plethorize Woo templating system

				// Remove woocommerce sidebar call...we have our own
				remove_all_actions( 'woocommerce_sidebar', 10 );

			# Front: Containers Setup ( hook on 'get_header' please )
				add_action( 'get_header', array( $this, 'containers') );    // Layout containers setup 
			
			# Front: Catalog Setup 

				// Shop page controls ( before loop )
				add_action( 'woocommerce_before_main_content', array( $this, 'catalog_breadcrumbs' ), 5);			// Shop page: Breadcrums
				add_filter( 'woocommerce_show_page_title', array( $this, 'catalog_title_display' ) );				// Shop page: Title display
				add_action( 'woocommerce_archive_description', array( $this, 'catalog_categorydescription' ), 1);	// Shop page: Category description display
				add_action( 'woocommerce_before_shop_loop', array( $this, 'catalog_resultscount'), 1);				// Shop page: Results count display
				add_action( 'woocommerce_before_shop_loop',	array( $this, 'catalog_orderby'), 1);					// Shop page: order by field
				
				// Shop page controls ( on loop )
				add_filter( 'loop_shop_per_page', array( $this, 'catalog_perpage' ), 20);							// Shop page: Products per page        
				add_filter( 'loop_shop_columns', array( $this, 'catalog_columns' ));								// Shop page: Columns 
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'catalog_rating' ), 1);			// Shop page: Rating display
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'catalog_price' ), 1);			// Shop page: Price display 
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'catalog_addtocart' ), 1);			// Shop page: Add-to-cart display 
				add_action( 'woocommerce_before_shop_loop_item_title',array( $this, 'catalog_salesflash' ), 1);		// Shop page: Sales flash icon display 

			# Front: Single Product Setup 
				add_action( 'woocommerce_before_main_content', array( $this, 'single_breadcrumbs' ), 5);			// Single product: Breadcrums
				add_action( 'woocommerce_before_single_product_summary',array( $this, 'single_salesflash' ), 1);	// Single product: Sales flash icon display 
				add_action( 'woocommerce_single_product_summary', array( $this, 'single_title' ) , 1);				// Single product: Title display
				add_action( 'woocommerce_single_product_summary', array( $this, 'single_rating' ), 1 );				// Single product: Rating display
				add_action( 'woocommerce_single_product_summary', array( $this, 'single_price' ), 1 );				// Single product: Price display
				add_action( 'woocommerce_single_product_summary', array( $this, 'single_addtocart' ), 1 );			// Single product: add-to-cart display
				add_action( 'woocommerce_single_product_summary', array( $this, 'single_meta' ), 1 );				// Single product: Meta display
				add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_description' ), 98 );				// Single product: Description tab display
				add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_reviews' ), 98 );					// Single product: Reviews tab display
				add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_attributes' ), 98 );				// Single product: Additional info tab display
				add_filter( 'woocommerce_related_products_args', array( $this, 'single_related' ), 10);				// Single product: Related products config
				add_filter( 'woocommerce_output_related_products_args', array( $this, 'single_related_config' ));	// Single product: Related products status
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );		// Single: Upsell products ( remove default )
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_upsell'), 15); 		// Single: Upsell products display ( "You May Also Like...")

			# Front: Other configuration
				add_action( 'init', array( $this, 'register_minitools' ), 20 );
			}
		}

		/**
		 * Declare support for Woocommerce
		 */
		public function support() {

			add_theme_support( 'woocommerce' );
		}

		/**
		 * Add 'product' CPT to Plethora supported post types
		 * Hooked on 'plethora_supported_post_types' filter
		 */
		public function add_product_to_supported_post_types( $supported, $args ) {

		  // Add this only when the call asks for plethora_only post types
		  if ( $args['plethora_only'] ) {

			$supported['product'] = $args['output'] === 'objects' ? get_post_type_object( 'product' ) : 'product' ;
		  }

		  return $supported;
		}

		/**
		 * Add support for the static shop page id
		 * Hooked on 'plethora_this_page_id' filter
		 */
		public function this_page_id( $page_id ){

			if ( is_shop() || (is_shop() && is_search()) || is_product_category() || is_product_tag() ) {

				return get_option( 'woocommerce_shop_page_id', 0);
			}

			return $page_id;
		}		

		/**
		 * Enqueue Woocommerce stylesheet
		 * Hooked on 'wp_enqueue_scripts' action
		 */
		public function enqueue_less( $less_files ) {

			$less_files['style'] = array(

				'handle' => ASSETS_PREFIX .'-dynamic-style',
				'src'    => PLE_THEME_ASSETS_URI.'/less/style-woocommerce.less',
				'deps'   => array( ASSETS_PREFIX .'-custom-bootstrap' ),
				'ver'    => false,
				'media'  => 'all'
			);
			return $less_files;
		}

		/**
		 * Setup container attributes ( classes, ids, etc )
		 */
		public function containers() {

			if ( is_product() || self::is_shop_catalog() ) {

				Plethora_Theme::add_container_attr( 'content_main_loop', 'class', 'plethora-woo' );
			}

			if ( self::is_shop_catalog() ) {

				Plethora_Theme::add_container_attr( 'content_main_loop', 'class', 'plethora-woo-shop-grid-'. Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-columns', 4) );
			
			} elseif ( is_product() ) {

				Plethora_Theme::add_container_attr( 'content_main_loop', 'class', 'plethora-woo-related-grid-'. Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4) );
			}
		}

		public function register_minitools() {
				if ( method_exists( 'Plethora_Module_Navminitools_Ext', 'register_minitool') ) {

					$this->register_minitool_cart();
					$this->register_minitool_account();
					// Ajax refresh for Plethora mini cart, if mini tool is displayed
					$this->minicart = Plethora_Module_Navminitools_Ext::get_minitool_status( 'woo_cart' );
					if ( $this->minicart ) {

						add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'get_minitool_output_woocart' ) ); 
					}
				}
		}

		public function register_minitool_cart() {

			$theme_options['cart-section-start'] = array(
				'id'       => 'woo-minicart-cart-start',
				'type'     => 'section',
				'title'    => esc_html__('Mini Tool > Woo Ajax Cart ( WooCommerce Only )', 'plethora-framework'),
				'indent'   => true,
			);

			$theme_options['cart-icon'] = array(
				'id'       => THEMEOPTION_PREFIX .'woo-minicart-cart-icon',
				'type'     => 'icons',
				'title'    => esc_html__('Cart Icon', 'plethora-framework'), 
				'desc'     => esc_html__('Select your cart icon', 'plethora-framework'),
				'options'  => Plethora_Module_Icons_Ext::get_options_array(),
			);

			$theme_options['cart-icon-size']	= array(
				'id'       => THEMEOPTION_PREFIX .'less-woo-minicart-cart-icon-size',
				'type'     => 'slider',
				'title'    => esc_html__('Cart Icon Size', 'plethora-framework'), 
				'desc'     => esc_html__('The icon size in pixels / Min: 8px / Max: 96px', 'plethora-framework'),
				'min'      => 8,
				'step'     => 1,
				'max'      => 96,
			);

			$theme_options['cart-title']	= array(
				'id'        => THEMEOPTION_PREFIX .'woo-minicart-cart-title',
				'type'      => 'text',
				'title'     => esc_html__('Cart Title', 'plethora-framework'), 
				'desc'      => esc_html__('The text that is usually displayed on hover', 'plethora-framework'),
				'translate' => true,
			);

			$theme_options['cart-count']	= array(
				'id'       => THEMEOPTION_PREFIX .'woo-minicart-cart-count',
				'type'     => 'switch', 
				'title'    => esc_html__('Cart Items Count', 'plethora-framework'),
			);

			$theme_options['cart-count-color']	= array(
				'id'          => THEMEOPTION_PREFIX .'less-woo-minicart-cart-count-color',
				'type'        => 'color', 
				'validate'    => 'color',
				'transparent' => false,
				'title'       => esc_html__('Cart Items Count // Text Color', 'plethora-framework'),
				'required'    => array( 
									array( THEMEOPTION_PREFIX .'woo-minicart-cart-count','=', true ),
								 ),
			);

			$theme_options['cart-count-bgcolor']	= array(
				'id'          => THEMEOPTION_PREFIX .'less-woo-minicart-cart-count-bgcolor',
				'type'        => 'color', 
				'validate'    => 'color',
				'transparent' => true,
				'title'       => esc_html__('Cart Items Count // Background Color', 'plethora-framework'),
				'required'    => array( 
									array( THEMEOPTION_PREFIX .'woo-minicart-cart-count','=', true ),
								 ),
			);

			$theme_options['cart-total']	= array(
				'id'       => THEMEOPTION_PREFIX .'woo-minicart-cart-total',
				'type'     => 'switch', 
				'title'    => esc_html__('Display Cart Subtotal Amount', 'plethora-framework'),
			);

			$theme_options['cart-section-end'] = array(
				'id'       => 'woo-minicart-cart-end',
				'type'     => 'section',
				'indent'   => false,
			);

			$args = array(
				'slug'           => 'woo_cart',
				'title'          => esc_html__( 'Woo Ajax Cart', 'plethora-framework' ),
				'desc'           => esc_html__( 'Mini ajax icon cart feature', 'plethora-framework' ),
				'theme_options'  => $theme_options,
				'output_method'  => array( $this, 'get_minitool_output_woocart' ),
			);

			if ( method_exists( 'Plethora_Module_Navminitools_Ext', 'register_minitool' ) ) {

				Plethora_Module_Navminitools_Ext::register_minitool( $args );
			}
		}

		public function register_minitool_account() {


			$theme_options['account-section-start'] = array(
				'id'       => 'woo-minicart-account-start',
				'type'     => 'section',
				'title'    => esc_html__('Mini Tool > Woo My Account ( WooCommerce Only )', 'plethora-framework'),
				'indent'   => true,
			);

			$theme_options['account-icon'] = array(
				'id'       => THEMEOPTION_PREFIX .'woo-minicart-account-icon',
				'type'     => 'icons',
				'title'    => esc_html__('My Account Icon', 'plethora-framework'), 
				'desc'     => esc_html__('Select an icon for My Account element', 'plethora-framework'),
				'options'  => Plethora_Module_Icons_Ext::get_options_array(),
			);

			$theme_options['account-icon-size'] = array(
				'id'       => THEMEOPTION_PREFIX .'less-woo-minicart-account-icon-size',
				'type'     => 'slider',
				'title'    => esc_html__('My Account Icon Size', 'plethora-framework'), 
				'desc'     => esc_html__('The icon size in pixels / Min: 8px / Max: 96px', 'plethora-framework'),
				'min'      => 8,
				'step'     => 1,
				'max'      => 96,
			);

			$theme_options['account-title-visit'] = array(
				'id'        => THEMEOPTION_PREFIX .'woo-minicart-account-title-visit',
				'type'      => 'text',
				'title'     => esc_html__('My Account Title / Logged', 'plethora-framework'), 
				'desc'      => esc_html__('The text that is usually displayed on hover, when a user is logged in', 'plethora-framework'),
				'translate' => true,
			);
			$theme_options['account-title-login'] = array(
				'id'        => THEMEOPTION_PREFIX .'woo-minicart-account-title-login',
				'type'      => 'text',
				'title'     => esc_html__('My Account Title / Non Logged', 'plethora-framework'), 
				'desc'      => esc_html__('The text that is usually displayed on hover, when a user is NOT logged in', 'plethora-framework'),
				'translate' => true,
			);
			$theme_options['account-section-end'] = array(
				'id'       => 'woo-minicart-account-end',
				'type'     => 'section',
				'indent'   => false,
			);

			$args = array(
				'slug'           => 'woo_account',
				'title'          => esc_html__( 'Woo My Account', 'plethora-framework' ),
				'desc'           => esc_html__( 'Mini icon link for account area', 'plethora-framework' ),
				'theme_options'  => $theme_options,
				'output_method'  => array( $this, 'get_minitool_output_wooaccount' ),
			);

			if ( method_exists( 'Plethora_Module_Navminitools_Ext', 'register_minitool' ) ) {

				Plethora_Module_Navminitools_Ext::register_minitool( $args );
			}
			
		}
		/**
		 * Returns Mini Cart template part
		 * Used also for Ajax reload when hooked on 
		 * 'woocommerce_add_to_cart_fragments' filter
		 */
		public function get_minitool_output_woocart( $fragments = array() ) {

			$options = array(
				'user_logged_in'     => is_user_logged_in(),
				'cart_url'           => WC()->cart->get_cart_url(),
				'cart_title'         => Plethora_Theme::option( THEMEOPTION_PREFIX .'woo-minicart-cart-title',  esc_html__( 'Visit cart', 'plethora-framework' ), 0, 0 ),
				'cart_icon'          => Plethora_Theme::option( THEMEOPTION_PREFIX .'woo-minicart-cart-icon',  'fa fa-shopping-cart', 0, 0 ),
				'cart_icon_size'     => Plethora_Theme::option( THEMEOPTION_PREFIX .'less-woo-minicart-cart-icon-size', 16, 0, false) .'px',
				'cart_count'         => Plethora_Theme::option( THEMEOPTION_PREFIX .'woo-minicart-cart-count', 1, 0, 0 ) ? WC()->cart->get_cart_contents_count() : false ,
				'cart_count_color'   => Plethora_Theme::option( THEMEOPTION_PREFIX .'less-woo-minicart-cart-count-color', '#ffffff', 0, false),
				'cart_count_bgcolor' => Plethora_Theme::option( THEMEOPTION_PREFIX .'less-woo-minicart-cart-count-bgcolor', '#2ecc71', 0, false),
				'cart_total'         => Plethora_Theme::option( THEMEOPTION_PREFIX .'woo-minicart-cart-total', 1, 0, 0 ) ? WC()->cart->get_cart_total() : false,
			);

			/* if this is an ajax call, then return $fragments array after we
			 * append the template content with the reload container selector ( #ple_woocart )
			 * as an array key ( container_selector => template_contents )
			 */
			ob_start();
			set_query_var( 'options', $options );
			get_template_part( 'templates/header/navigation_minitools_woocart' );
			get_template_part( 'templates/header/header_main/navigation_minitools_woocart' );
			if ( current_filter() === 'woocommerce_add_to_cart_fragments' ) {

				$fragments['#ple_woocart'] = ob_get_clean();
				return $fragments;
			}
			return ob_get_clean();
		}

		/**
		 * Return Account mini tool template part
		 */
		public function get_minitool_output_wooaccount( $fragments = array() ) {

			$options = array(
				'user_logged_in'     => is_user_logged_in(),
				'account_icon'       => Plethora_Theme::option( THEMEOPTION_PREFIX .'woo-minicart-account-icon',  'fa fa-user', 0, 0 ),
				'account_icon_size'  => Plethora_Theme::option( THEMEOPTION_PREFIX .'less-woo-minicart-account-icon-size', 16, 0, false) .'px',
				'account_user'       => is_user_logged_in() ? wp_get_current_user()->display_name : '',
				'account_url'        => get_permalink( get_option('woocommerce_myaccount_page_id') ),
				'account_title'      => is_user_logged_in() ? Plethora_Theme::option( THEMEOPTION_PREFIX .'woo-minicart-account-title-visit', esc_html__( 'My Account', 'plethora-framework' ), 0, 0 ) : Plethora_Theme::option( THEMEOPTION_PREFIX .'woo-minicart-account-title-login', esc_html__( 'Login / Register', 'plethora-framework' ), 0, 0 ),
			);

			ob_start();
			set_query_var( 'options', $options );
			get_template_part( 'templates/header/navigation_minitools_wooaccount' ); // support for AVOIR
			get_template_part( 'templates/header/header_main/navigation_minitools_wooaccount' );
			return ob_get_clean();
		}

		/**
		 * Title display filter. Returns always false due to design restrictions
		 * Hooked on 'woocommerce_show_page_title'
		 */
		public static function catalog_title_display( $display ) {

			$display = false; 
			return $display;
		}


		/**
		 * Shop page description action connected with Plethora control option
		 * Hooked on 'woocommerce_archive_description'
		 */
		public static function catalog_categorydescription() {
			$category_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-categorydescription', 'display' );
			if ( $category_display == 'hide') { 
				remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
			}
		}

		/**
		 * Returns items per page value, connected with Plethora control option
		 * Hooked on 'loop_shop_per_page'
		 */
		public static function catalog_perpage() {
			$products_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-perpage', 12, 0, false);
			return $products_per_page;			
		}

		/**
		 * Returns columns per row, connected with Plethora control option
		 * Hooked on 'loop_shop_columns'
		 */
		public static function catalog_columns() {

			$products_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-columns', 4, 0, false);
			return $products_per_page;			
		}

		/**
		 * Returns columns per row, connected with Plethora control option
		 * Hooked on 'loop_shop_columns'
		 */
		public static function catalog_breadcrumbs() {

			if ( self::is_shop_catalog() ) {
				$breadcrumbs = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-breadcrumbs', 'display');
				if ( $breadcrumbs == 'hide') { 
					remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
				}
			}
		}

		/**
		 * Result count display action, connected with Plethora control option
		 * Hooked on 'woocommerce_before_shop_loop'
		 */
		public static function catalog_resultscount() { 

			$resultscount_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-resultscount', 'display' );
			if ( $resultscount_display == 'hide' ) { 
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
			}			
		}

		/**
		 * Order by field display action, connected with Plethora control option
		 * Hooked on 'woocommerce_before_shop_loop'
		 */
		public static function catalog_orderby() {

			$orderby_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-orderby', 'display', 0, false);
			if ( $orderby_display == 'hide') { 
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
			}
		}

		/**
		 * Product rating display action, connected with Plethora control option
		 * Hooked on 'woocommerce_after_shop_loop_item_title'
		 */
		public static function catalog_rating() { 

			$rating_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-rating', 'display' );
			if ( $rating_display == 'hide') { 
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
			}
		}

		/**
		 * Product price display filter, connected with Plethora control option
		 * Hooked on 'woocommerce_after_shop_loop_item_title'
		 */
		public static function catalog_price() {

			$price_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-price', 'display' );
			if ( $price_display == 'hide') { 
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
			}
		}

		/**
		 * Product basket button display action, connected with Plethora control option
		 * Hooked on 'woocommerce_after_shop_loop_item'
		 */
		public static function catalog_addtocart() { 

			$cart_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-addtocart', 'display' );
			if ( $cart_display == 'hide' ) { 
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			}
		}

		/**
		 * Sales flash icon display action, connected with Plethora control option
		 * Hooked on 'woocommerce_before_shop_loop_item_title'
		 */
		public static function catalog_salesflash() {

			$salesflash_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-salesflash', 'display' );
			if ( $salesflash_display == 'hide' ) { 
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
			}			
		}

		/**
		 * Single product breadcrumb display action, connected with Plethora control option
		 * Hooked on 'woocommerce_before_main_content'
		 */
		public static function single_breadcrumbs() {

			if ( is_product() ) {
				$breadcrumbs = Plethora_Theme::option( METAOPTION_PREFIX .'product-breadcrumbs', 'display');
				if ( $breadcrumbs == 'hide') { 
					remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
				}

			}
		}

		/**
		 * Single product title display action, connected with Plethora control option
		 * Hooked on 'woocommerce_single_product_summary'
		 */
		public static function single_title() {

			$title_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-wootitle', 'display' );
			if ( ! $title_display ) { 
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			}
		}

		/**
		 * Single product rating display action, connected with Plethora control option
		 * Hooked on 'woocommerce_single_product_summary'
		 */
		public static function single_rating() {

			$rating_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-rating', 'display' );
			if ( $rating_display == 'hide') {
				remove_action( 'woocommerce_single_product_summary', 	 'woocommerce_template_single_rating', 10 );
			}
		}

		/**
		 * Single product sales flash display action, connected with Plethora control option
		 * Hooked on 'woocommerce_single_product_summary'
		 */
		public static function single_salesflash() {

			$salesflash_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-sale', 'display' );
			if ( $salesflash_display == 'hide') {
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );		
			}
		}

		/**
		 * Single product price display action, connected with Plethora control option
		 * Hooked on 'woocommerce_single_product_summary'
		 */
		public static function single_price() {

			$price_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-price', 'display' );
			if ( $price_display == 'hide') {
				remove_action( 'woocommerce_single_product_summary', 	 'woocommerce_template_single_price', 10 );	
			}
		}

		/**
		 * Single product add to cart button display action, connected with Plethora control option
		 * Hooked on 'woocommerce_single_product_summary'
		 */
		public static function single_addtocart() { 

			$cart_status = Plethora_Theme::option( METAOPTION_PREFIX .'product-addtocart', 'display');
			if ( $cart_status == 'hide') { 
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			}
		}

		/**
		 * Single product meta info display action, connected with Plethora control option
		 * Hooked on 'woocommerce_single_product_summary'
		 */
		public static function single_meta() { 

			$meta_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-meta', 'display');
			if ( $meta_display == 'hide') { 
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			}
		}

		/**
		 * Single product description tab display filter, connected with Plethora control option
		 * Hooked on 'woocommerce_product_tabs'
		 */
		public static function single_tab_description( $tabs ) {

			$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-description', 'display');
			if ( $tab_display == 'hide') { 
				unset( $tabs['description'] );
			}
			return $tabs;
		}	        

		/**
		 * Single product reviews tab display filter, connected with Plethora control option
		 * Hooked on 'woocommerce_product_tabs'
		 */
		public static function single_tab_reviews( $tabs ) {
		  
			$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-reviews', 'display');
			if ( $tab_display == 'hide') { 
				unset( $tabs['reviews'] );
			}
			return $tabs;
		}	        

		/**
		 * Single product attributes tab display filter, connected with Plethora control option
		 * Hooked on 'woocommerce_product_tabs'
		 */
		public static function single_tab_attributes( $tabs ) {
		  
			$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-attributes', 'display');
			if ( $tab_display == 'hide') { 
				unset( $tabs['additional_information'] );
			}
			return $tabs;
		}	        

		/**
		 * Single product related products box display filter, connected with Plethora control option
		 * Hooked on 'woocommerce_related_products_args'
		 */
		public static function single_related( $args ) {

			$related = Plethora_Theme::option( METAOPTION_PREFIX .'product-related', 'display');
			if ($related == 'display') {
				return $args;
			} else { 
				return array();
			}
		} 

		/**
		 * Single product related products box configuration filter, connected with Plethora control option
		 * Hooked on 'woocommerce_output_related_products_args'
		 */
		public static function single_related_config( $args ) {

			$posts_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-number', 4);
			$columns 		= Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4);
			$args['posts_per_page'] = $posts_per_page; 
			$args['columns'] 		= $columns;
			return $args;
		}

		/**
		 * Single product upsell products box configuration filter, connected with Plethora control option
		 * Hooked on 'woocommerce_after_single_product_summary'
		 */
		public static function single_upsell() {
			$upsell_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-upsell', 'display');
			$upsell_results = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-number', 4);
			$upsell_columns = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4);
			if ( $upsell_display == 'display' ) {
				woocommerce_upsell_display( $upsell_results, $upsell_columns ); 
			}
		}

		/**
		* Products archive (shop) view theme options configuration for REDUX
		* Hooked on 'plethora_themeoptions_content'
		*/
		public function archive_themeoptions( $sections ) {

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


			$page_for_shop	= get_option( 'woocommerce_shop_page_id', 0 );
			$desc_1 = esc_html__('These options affect ONLY shop catalog display.', 'plethora-framework');
			$desc_2 = esc_html__('These options affect ONLY shop catalog display...however it seems that you', 'plethora-framework'); 
			$desc_2 .= ' <span style="color:red">';
			$desc_2 .= esc_html__('have not set a shop page yet!', 'plethora-framework'); 
			$desc_2 .= '</span>';
			$desc_2 .= esc_html__('You can go for it under \'WooCommerce > Settings > Products > Display\'.', 'plethora-framework');
			$desc = $page_for_shop === 0 || empty($page_for_shop) ? $desc_2 :  $desc_1 ;
			$desc .= '<br>'. esc_html__('If you are using a speed optimization plugin, don\'t forget to <strong>clear cache</strong> after options update', 'plethora-framework');

			$sections[] = array(
				'title'      => esc_html__('Shop ( Woo )', 'plethora-framework'),
				'heading'    => esc_html__('WOOCOMMERCE PLUGIN // SHOP OPTIONS ( WooCommerce )', 'plethora-framework'),
				'desc'       => $desc,
				'subsection' => true,
				'fields'     => $fields
				);
			}

			return $sections;

		}

		/**
		* Single product view theme options configuration for REDUX
		* Hooked on 'plethora_themeoptions_content'
		*/
		public function single_themeoptions( $sections ) {


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

			$page_for_shop	= get_option( 'woocommerce_shop_page_id', 0 );
			$desc_1 = __('It seems that you <span style="color:red">have not set a shop page yet!</span>. You can go for it under <strong>WooCommerce > Settings > Products > Display</strong>.<br>', 'plethora-framework');
			$desc = $page_for_shop === 0 || empty($page_for_shop) ? $desc_1 :  '' ;
			$desc .=  esc_html__('These will be the default values for a new post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework');
			$sections[] = array(
				'title'      => esc_html__('Single Product ( Woo )', 'plethora-framework'),
				'heading' => esc_html__('WOOCOMMERCE PLUGIN // SINGLE PRODUCT VIEW', 'plethora-framework'),
				'subsection' =>  true,
				'desc' =>  $desc,
				'fields'     => $fields
				);
			}
			return $sections;
		}

		/**
		* Single product view metabox configuration for REDUX
		* Hooked on 'plethora_metabox_add'
		*/
		public function single_metabox( $metaboxes ) {

			$sections_index = Plethora_Posttype::single_options_sections_index_for( $this->post_type );
			$sections = array();
			$priority = 10;
			foreach ( $sections_index as $section => $section_config ) {

				$fields = Plethora_Posttype::get_single_metabox_section_fields( $this, $section, $this->posttype_obj );
				if ( !empty( $fields ) ) {

					$section_config['fields'] =  $fields;
					$sections[] = $section_config;
				}
			}



			// // setup theme options according to configuration
			// $opts          = $this->single_options();
			// $opts_config   = $this->single_options_config();
			// $fields        = array();
			// foreach ( $opts_config as $opt_config ) {

			// 	$id          = $opt_config['id'];
			// 	$status      = $opt_config['metabox'];
			// 	$default_val = $opt_config['metabox_default'];
			// 	if ( $status && array_key_exists( $id, $opts ) ) {

			// 		if ( !is_null( $default_val ) ) { // will add only if not NULL }
			// 			$opts[$id]['default'] = $default_val;
			// 		}
			// 		$fields[] = $opts[$id];
			// 	}
			// }

			// $sections_content = array(
			// 	'title' => esc_html__('Content', 'plethora-framework'),
			// 	'heading' => esc_html__('CONTENT OPTIONS', 'plethora-framework'),
			// 	'icon_class'    => 'icon-large',
			// 	'icon'       => 'el-icon-lines',
			// 	'fields'        => $fields
			// );

			// $sections = array();
			// $sections[] = $sections_content;

			// if ( has_filter( 'plethora_metabox_singleproduct') ) {

			// 	$sections = apply_filters( 'plethora_metabox_singleproduct', $sections );
			// }

			$metaboxes[] = array(
				'id'            => 'metabox-single-'. $this->post_type,
				'title'         => esc_html__( 'Page Options', 'plethora-framework' ),
				'post_types'    => array( $this->post_type ),
				'position'      => 'normal', // normal, advanced, side
				'priority'      => 'high', // high, core, default, low
				'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
				'sections'      => $sections,
			);

			return $metaboxes;
		}

		/** 
		* Returns ARCHIVE OPTIONS INDEX
		* It contains ALL possible archive options, no matter which theme
		*/
		public function archive_options() {

			$archive_options['layout']	= array(
							'id'      =>  METAOPTION_PREFIX .'archiveproduct-layout',
							'title'   => esc_html__( 'Catalog Layout', 'plethora-framework' ),
							'type'    => 'image_select',
							'options' => Plethora_Module_Style::get_options_array( array( 
																						'type'   => 'page_layouts',
																						'use_in' => 'redux',
																				   )
							)
			);

			$archive_options['sidebar']	= array(
							'id'       => METAOPTION_PREFIX .'archiveproduct-sidebar',
							'required' => array( METAOPTION_PREFIX .'archiveproduct-layout','equals',array('right_sidebar','left_sidebar') ),
							'type'     => 'select',
							'data'     => 'sidebars',
							'multi'    => false,
							'title'    => esc_html__('Catalog Sidebar', 'plethora-framework'), 
			);
			
			$archive_options['contentalign']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-contentalign',
							'type'    => 'button_set', 
							'title'   => esc_html__('Content Section Align', 'plethora-framework'),
							'desc'    => esc_html__('Affects all content section text alignment, except intro text ( you can set it as you like using the editor options ).', 'plethora-framework'),
							'options' => array(
											''            => esc_html__( 'Left', 'plethora-framework'),
											'text-center' => esc_html__( 'Center', 'plethora-framework'),
											'text-right'  => esc_html__( 'Right', 'plethora-framework'),
										 )
			);

			$archive_options['perpage']	= array(
							'id'            => METAOPTION_PREFIX .'archiveproduct-perpage',
							'type'          => 'slider',
							'title'         => esc_html__('Products Displayed Per Page', 'plethora-framework'), 
							"min"           => 4,
							"step"          => 4,
							"max"           => 240,
							'display_value' => 'text'
			);

			$archive_options['title']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-title',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Title On Content', 'plethora-framework'),
							'desc'    => esc_html__('Will display title on content view', 'plethora-framework'),
			);

			$archive_options['title-text']	= array(
							'id'        => METAOPTION_PREFIX .'archiveproduct-title-text',
							'type'      => 'text',
							'title'     => esc_html__('Default Title', 'plethora-framework'), 
							'translate' => true,
			);

			$archive_options['subtitle']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Subtitle On Content', 'plethora-framework'),
							'desc'    => esc_html__('Will display subtitle on content view', 'plethora-framework'),
			);

			$archive_options['subtitle-text']	= array(
							'id'        => METAOPTION_PREFIX .'archiveproduct-subtitle-text',
							'type'      => 'text',
							'title'     => esc_html__('Default Subtitle', 'plethora-framework'), 
							'desc'      => esc_html__('This is used ONLY as default subtitle for the headings section of the Media Panel', 'plethora-framework'), 
							'translate' => true,
			);

			$archive_options['columns']	= array(
							'id'            => METAOPTION_PREFIX .'archiveproduct-columns',
							'type'          => 'slider',
							'title'         => esc_html__('Products Grid Columns', 'plethora-framework'), 
							"min"           => 2,
							"step"          => 1,
							"max"           => 4,
							'display_value' => 'text'
			);

			$archive_options['categorydescription']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-categorydescription',
							'type'    => 'button_set',
							'title'   => esc_html__('Category Description', 'plethora-framework'),
							'desc'    => esc_html__('By default, category description ( if exists ) is displayed right after shop title.', 'plethora-framework'),
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'    => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$archive_options['breadcrumbs']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-breadcrumbs',
							'type'    => 'button_set',
							'title'   => esc_html__('Breadcrumbs ( Catalog View )', 'plethora-framework'),
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'    => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$archive_options['resultscount']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-resultscount',
							'type'    => 'button_set',
							'title'   => esc_html__('Results Count Info', 'plethora-framework'),
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'    => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$archive_options['orderby']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-orderby',
							'type'    => 'button_set',
							'title'   => esc_html__('Order Dropdown Field', 'plethora-framework'),
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'    => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$archive_options['rating'] = array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-rating',
							'type'    => 'button_set',
							'title'   => esc_html__('Ratings ( Catalog View )', 'plethora-framework'),
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'    => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$archive_options['price'] = array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-price',
							'type'    => 'button_set',
							'title'   => esc_html__('Prices ( Catalog View )', 'plethora-framework'),
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'    => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$archive_options['addtocart']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-addtocart',
							'type'    => 'button_set',
							'title'   => esc_html__('"Add To Cart" Button ( Catalog View )', 'plethora-framework'),
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'    => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$archive_options['salesflash']	= array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-salesflash',
							'type'    => 'button_set',
							'title'   => esc_html__('"Sale!" Icon ( Catalog View )', 'plethora-framework'),
							"default" => 'display',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'    => esc_html__('Hide', 'plethora-framework'),
									)
			);

			return $archive_options;
		}

		/** 
		* Returns single options index
		* It contains ALL possible single product options
		*/
		public function single_options() {

			$single_options['layout'] = array(
						'id'         =>  METAOPTION_PREFIX .'product-layout',
						'title'      => esc_html__( 'Product Post Layout', 'plethora-framework' ),
						'type'       => 'image_select',
						'customizer' => array(),
						'options'    => Plethora_Module_Style::get_options_array( array( 
																					'type'   => 'page_layouts',
																					'use_in' => 'redux',
																			   )
						)
			);

			$single_options['sidebar'] = array(
						'id'       => METAOPTION_PREFIX .'product-sidebar',
						'required' => array( METAOPTION_PREFIX .'product-layout','equals',array('right_sidebar','left_sidebar') ),
						'type'     => 'select',
						'data'     => 'sidebars',
						'multi'    => false,
						'title'    => esc_html__('Product Post Sidebar', 'plethora-framework'), 
			);

			$single_options['title'] = array(
						'id'      => METAOPTION_PREFIX .'product-title',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Title', 'plethora-framework'),
						'desc'    => esc_html__('Enable/disable titles section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
			);
			$single_options['subtitle'] = array(
						'id'      => METAOPTION_PREFIX .'product-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Subtitle', 'plethora-framework'),
						'desc'    => esc_html__('Enable/disable subtitles section display. You might want to disable this in case you are using media panel for titles display.', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$single_options['subtitle-text'] = array(
						'id'        => METAOPTION_PREFIX .'product-subtitle-text',
						'required' => array( METAOPTION_PREFIX .'product-subtitle','=', true ),
						'type'      => 'text',
						'title'     => esc_html__('Subtitle', 'plethora-framework'), 
						'desc'      => esc_html__('This is used ONLY as default subtitle for the headings section of the Media Panel', 'plethora-framework'),
						'translate' => true,
			);

			$single_options['wootitle'] = array(
						'id'      => METAOPTION_PREFIX .'product-wootitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display WooCommerce Title', 'plethora-framework'),
						'desc'    => esc_html__('Display the classic WooCommerce product title next to product image', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
			);

			$single_options['breadcrumbs'] = array(
						'id'      => METAOPTION_PREFIX .'product-breadcrumbs',
						'type'    => 'button_set',
						'title'   => esc_html__('Breadcrumbs ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['rating'] = array(
						'id'      => METAOPTION_PREFIX .'product-rating',
						'type'    => 'button_set',
						'title'   => esc_html__('Ratings ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['price'] = array(
						'id'      => METAOPTION_PREFIX .'product-price',
						'type'    => 'button_set',
						'title'   => esc_html__('Price  ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['addtocart'] = array(
						'id'      => METAOPTION_PREFIX .'product-addtocart',
						'type'    => 'button_set',
						'title'   => esc_html__('"Add To Cart" Button ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['meta'] = array(
						'id'      => METAOPTION_PREFIX .'product-meta',
						'type'    => 'button_set',
						'title'   => esc_html__('Product Categories', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['sale'] = array(
						'id'      => METAOPTION_PREFIX .'product-sale',
						'type'    => 'button_set',
						'title'   => esc_html__('"Sale" Icon ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['tab-description'] = array(
						'id'      => METAOPTION_PREFIX .'product-tab-description',
						'type'    => 'button_set',
						'title'   => esc_html__('Description Tab', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'    => esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['tab-reviews'] = array(
						'id'      => METAOPTION_PREFIX .'product-tab-reviews',
						'type'    => 'button_set',
						'title'   => esc_html__('Reviews Tab', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'    => esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['tab-attributes'] = array(
						'id'      => METAOPTION_PREFIX .'product-tab-attributes',
						'type'    => 'button_set',
						'title'   => esc_html__('Additional Information Tab', 'plethora-framework'),
						'descr'   => esc_html__('Remember that this tab is NOT displayed by defaul if product has no attributes', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'    => esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['related'] = array(
						'id'      => METAOPTION_PREFIX .'product-related',
						'type'    => 'button_set',
						'title'   => esc_html__('Related Products', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['upsell'] = array(
						'id'      => METAOPTION_PREFIX .'product-upsell',
						'type'    => 'button_set',
						'title'   => esc_html__('Upsell Products', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
			);

			$single_options['related-number'] = array(
						'id'            => METAOPTION_PREFIX .'product-related-number',
						'type'          => 'slider',
						'title'         => esc_html__('Related/Upsell Products Max Results', 'plethora-framework'), 
						"min"           => 2,
						"step"          => 1,
						"max"           => 36,
						'display_value' => 'text'
			);

			$single_options['related-columns'] = array(
						'id'            => METAOPTION_PREFIX .'product-related-columns',
						'type'          => 'slider',
						'title'         => esc_html__('Related/Upsell Products Columns', 'plethora-framework'), 
						"min"           => 2,
						"step"          => 1,
						"max"           => 4,
						'display_value' => 'text'
			);
			
			return $single_options;
		}
		/** 
		* Archive view options_config for theme options
		*/
		public function archive_options_config() {

			$archive_options_config = array(
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
							'theme_options_default' => 'sidebar-shop',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'contentalign', 
							'theme_options'         => true, 
							'theme_options_default' => '',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'perpage', 
							'theme_options'         => true, 
							'theme_options_default' => 12,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title', 
							'theme_options'         => true, 
							'theme_options_default' => false,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title-text', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__('Shop Title', 'plethora-framework'),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => false,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle-text', 
							'theme_options'         => true, 
							'theme_options_default' => esc_html__('Shop subtitle here', 'plethora-framework'),
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'columns', 
							'theme_options'         => true, 
							'theme_options_default' => 3,
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'categorydescription', 
							'theme_options'         => true, 
							'theme_options_default' => 'hide',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'breadcrumbs', 
							'theme_options'         => true, 
							'theme_options_default' => 'hide',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'resultscount', 
							'theme_options'         => true, 
							'theme_options_default' => 'hide',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'orderby', 
							'theme_options'         => true, 
							'theme_options_default' => 'hide',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'rating', 
							'theme_options'         => true, 
							'theme_options_default' => 'hide',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'price', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'addtocart', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'salesflash', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => false,
							'metabox_default'       => NULL
							),
			);

			return $archive_options_config;
		}

		/** 
		* Single view options_config for theme options / metaboxes
		*/
		public function single_options_config() {

			$single_options_config = array(

						array( 
							'id'                    => 'layout', 
							'theme_options'         => true, 
							'theme_options_default' => 'no_sidebar_narrow',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'sidebar', 
							'theme_options'         => true, 
							'theme_options_default' => 'sidebar-shop',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'title', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'subtitle-text', 
							'theme_options'         => false, 
							'theme_options_default' => NULL,
							'metabox'               => true,
							'metabox_default'       => ''
							),
						array( 
							'id'                    => 'wootitle', 
							'theme_options'         => true, 
							'theme_options_default' => true,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'breadcrumbs', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'rating', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'price', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'addtocart', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'meta', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'sale', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'tab-description', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'tab-reviews', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'tab-attributes', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'related', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'upsell', 
							'theme_options'         => true, 
							'theme_options_default' => 'display',
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'related-number', 
							'theme_options'         => true, 
							'theme_options_default' => 3,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
						array( 
							'id'                    => 'related-columns', 
							'theme_options'         => true, 
							'theme_options_default' => 3,
							'metabox'               => true,
							'metabox_default'       => NULL
							),
			);

			return $single_options_config;
		}
		// Just a helper to avoid writing all these conditionals
		public static function is_shop_catalog(){

			if (  is_shop() || ( is_shop() && is_search() ) || is_product_category() || is_product_tag() ) {

				return true;
			}
			return false;
		}
	}
}


// This should be applied before WC's activation, as it should declare shop image sizes right after theme activation
function image_dimensions() { 

	if ( !function_exists( 'plethora_woo_image_dimensions')) { 

		global $pagenow;
		if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {

			return;
		}

		$catalog = array(
			'width'  => '326', // px
			'height' => '326', // px
			'crop'   => 1 // true
		);

		$single = array(
			'width'  => '547', // px
			'height' => '547', // px
			'crop'   => 1 // true
		);

		$thumbnail = array(
			'width'  => '168', // px
			'height' => '168', // px
			'crop'   => 0 // false
		);

		// Image sizes
		update_option( 'shop_catalog_image_size', $catalog ); // Product category thumbs
		update_option( 'shop_single_image_size', $single ); // Single product image
		update_option( 'shop_thumbnail_image_size', $thumbnail ); // Image gallery thumbs 
	}
}
add_action( 'after_switch_theme', 'image_dimensions' );