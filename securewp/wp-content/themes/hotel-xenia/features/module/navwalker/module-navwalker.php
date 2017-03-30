<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Nav Walker Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Navwalker') && !class_exists('Plethora_Module_Navwalker_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/module-navwalker.php
   */
  class Plethora_Module_Navwalker_Ext extends Plethora_Module_Navwalker { 

		/**
		 * @see Walker::start_lvl()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int $depth Depth of page. Used for padding.
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "\n$indent<ul role=\"menu\">\n";
		}

		/**
		 * @see Walker::start_el()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param int $current_page Menu item ID.
		 * @param object $args
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			/**
			 * Dividers, Headers or Disabled
			 * =============================
			 * Determine whether the item is a Divider, Header, Disabled or regular
			 * menu item. To prevent errors we use the strcasecmp() function to so a
			 * comparison that is not case sensitive. The strcasecmp() function returns
			 * a 0 if the strings are equal.
			 */
			if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
				$output .= $indent . '<li role="presentation" class="divider">';
			} else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
				$output .= $indent . '<li role="presentation" class="divider">';
			} else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
				$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
			} else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
				$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
			} else {

				$class_names = $value = '';
				$classes     = empty( $item->classes ) ? array() : (array) $item->classes;
				$classes[]   = 'menu-item-' . $item->ID;
				$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

				if ( is_object($args) && $args->has_children && $depth === 0)
					$class_names .= ' lihaschildren';

				if ( is_object($args) && $args->has_children && $depth > 0 )
					$class_names .= ' sublihaschildren';

				if ( in_array( 'current-menu-item', $classes ) )
					$class_names .= ' active';

				$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

				$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
				$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

				$output .= $indent . '<li' . $id . $value . $class_names .'>';

				$atts = array();
				$atts['title']  = ! empty( $item->title )	? $item->title	: '';
				$atts['target'] = ! empty( $item->target )	? $item->target	: '';
				$atts['rel']    = ! empty( $item->xfn )		? $item->xfn	: '';

				$atts['href'] = ! empty( $item->url ) ? $item->url : '';

				/*
				// If item has_children remove the link on the top-level items. Used for Open-on-Click Sub-Menus.
				if ( is_object($args) && $args->has_children && $depth === 0 ) {
					$atts['href']   		= '#';
				} else {
					$atts['href'] = ! empty( $item->url ) ? $item->url : '';
				}
				*/

				$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

				$attributes = '';
				foreach ( $atts as $attr => $value ) {
					if ( ! empty( $value ) ) {
						$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
						$attributes .= ' ' . $attr . '="' . $value . '"';
					}
				}

				if ( is_object($args) ) { $item_output = $args->before; }

				/*
				 * Glyphicons
				 * ===========
				 * Since the menu item is NOT a Divider or Header we check the see
				 * if there is a value in the attr_title property. If the attr_title
				 * property is NOT null we apply it as the class name for the glyphicon.
				 */
				if ( isset($item_output) ){

					if ( ! empty( $item->attr_title ) )
						$item_output .= '<a'. $attributes .'><span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span>&nbsp;';
					else
						$item_output .= '<a'. $attributes .'>';

					$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
					//$dropdown_svgicon = '<span class="dropdown-icon"><svg width="10" height="7"><g></g><line fill="none" stroke="#fdfdfd" x1="-4.54458" y1="-5.33302" x2="5.38279" y2="4.59435"></line> <line fill="none" stroke="#fdfdfd" x1="4.67324" y1="4.59752" x2="14.81695" y2="-5.54619"></line> </svg></span>';
					$dropdown_svgicon = '';
					$item_output .= ( $args->has_children && 0 === $depth ) ? ' '. $dropdown_svgicon .' </a>' : '</a>';
					$item_output .= $args->after;

					$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

				} else {

					$output = '<li><a href="'. esc_url( admin_url('nav-menus.php') ) .'">'. esc_html__( "Please Create a Menu", 'hotel-xenia' ) . '</a>';

				}

			}
		}
  }
}