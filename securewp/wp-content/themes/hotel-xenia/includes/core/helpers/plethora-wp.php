<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2014

Description: An intermediary class between WP and Plethora framework to handle efficiently deprecated functions
Version: 1.2

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


/**
 * An intermediary class between WP and Plethora framework to handle efficiently deprecated functions
 * 
 * @package Plethora Framework
 * @version 1.0
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c) 2015
 *
 */
class Plethora_WP {


//// WORDPRESS FUNCTIONS WRAPPERS -> START

	/**
	 * Load a template part into a template (other than header, sidebar, footer). 
	 * More on http://codex.wordpress.org/Function_Reference/get_template_part
	 */
    static function get_template_part( $slug, $name = '' ) {
        $display_name   = !empty($name) ? $name .'.php' : '';
        $display_slug   = empty( $display_name ) ? $slug .'.php' : $slug .'-';
        $current_filter = function_exists( 'current_filter' ) ? current_filter() : '';
        $display_hook   = !empty( $current_filter )  ? '|| Added as WP action hook @ '. $current_filter : '';
        Plethora_Theme::dev_comment('TEMPLATE PART FILE LOADED: '. $display_slug . $display_name . $display_hook, 'templateparts');
    	get_template_part( $slug, $name );
        Plethora_Theme::dev_comment('TEMPLATE PART FILE FINISHED: '. $display_slug . $display_name, 'templateparts');
    }

//// WORDPRESS FUNCTIONS WRAPPERS <- FINISH


//// GENERAL PLETHORA CONDITIONALS & SNIPPETS -> START

    /**
     * Return categories in title->value array. Based on WP get_categories. Used mostly on shortcode features. 
     * Check http://codex.wordpress.org/Function_Reference/get_categories for further documentation
     *
	 * @param $user_args, $taxonomy, $fieldtitle, $fieldvalue
   	 * @return array
     * @since 1.0
     *
     */

    static function categories( $user_args = array(), $fieldtitle = 'name', $fieldvalue = 'cat_ID'  ) {

		// Default arguments
		$default_args = array(
			'type'                     => '',
			'child_of'                 => 0,
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false 

		); 		    

		// Merge default and user given arguments
		$args = array_merge($default_args, $user_args);

		// Get the categories
	    $categories = get_categories( $args );

		// Return values in array, according to $fieldtitle and $fieldvalue variables
		$return = Array();
	    
    	foreach ( $categories as $category ) { 

            $return[$category->$fieldtitle] = $category->$fieldvalue;
    	}

	    ksort($return);
	    return $return;

	}	


	/**
	 * is_edit_page 
	 * function to check if the current page is a post edit page
	 * 
	 * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
	 * @return boolean
	 */

	static function is_edit_page($new_edit = null){
	    global $pagenow;
	    //make sure we are on the backend
	    if (!is_admin()) return false;


	    if($new_edit == "edit")
	        return in_array( $pagenow, array( 'post.php',  ) );
	    elseif($new_edit == "new") //check for new post page
	        return in_array( $pagenow, array( 'post-new.php' ) );
	    else //check for either new or edit
	        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	}

	/**
	 * Retrieves the attachment ID from the file URL
	 * 
	 * @param  $image_url
	 * @return string
	 * @version 1.1
	 */
	public static function get_imageid_by_url( $image_url ) {
		global $wpdb;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
		if ( $attachment )  { 

	        return $attachment[0]; 
		} else {

			return 0;
		}
	}

    public static function get_reduxoption_image_src( $value, $size = 'full' ) {

        $image_id = !empty( $value['id'] ) ? $value['id'] : 0;
        $image_src = $value['url'];
        if ( $image_id ) {

            $image_src_by_id = wp_get_attachment_image_src( $image_id, $size );
            $image_src = !empty( $image_src_by_id[0] ) ? $image_src_by_id[0] : $value['url'];
        }
        return $image_src;
    }

    public static function stringCaseConvert( $text, $options ){

        $defaults = array(

            "case" => "underscore"  // "underscore", "camel", "upper", "lower", etc.

        );

        $options = ( isset($options) && is_array($options) )? array_replace_recursive ( $defaults , $options ) : $defaults;

        switch ( $options["case"] ) {
            case 'underscore':
                $text = str_replace( " ", "_", strtolower( $text ) );
                break;

            case 'slug':
                $text = str_replace( " ", "-", strtolower( $text ) );
                break;

            case 'title':
                $text = strtoupper(substr($text, 0, 1)) . substr($text, 1);
                break;

            case 'hyphen-to-underscore':
                $text = str_replace( "-", "_", $text );
                break;
            
            default:
                $text = str_replace( " ", "_", strtolower( $text ) );
                break;
        }

        return $text;

    }

    /**
    * Render Mustache Widget Template
    * @return string
    */
    public static function renderMustache( $options ){

        $defaults = array(
            "data"     => '', 
            "file"     => '',
            "override" => false,
            "module"   => false,
            'force_template_part' => false
        );

        $options = ( isset($options) && is_array($options) )? array_replace_recursive ( $defaults , $options ) : $defaults;

        $pattern       = '/(.*)-(.*).php$/';    // GRAB FEATURE NAME: 'widget-', 'shortode-', etc. FROM FILENAME: 'shortcode-entry.php'
        $full_pathname = ( $options['override'] ) ? "shortcode-" . basename( $options['file'] ) : basename( $options['file'] );

        if ( ! class_exists('Mustache_Engine') ){  require_once PLE_FLIB_LIBS_DIR . '/mustache/mustache.php';  }

        if ( preg_match( $pattern, $full_pathname, $matches, PREG_OFFSET_CAPTURE) || $options['force_template_part'] ){

            $mustache = new Mustache_Engine;
            ob_start();
            if ( $options['force_template_part'] ) {
                $slug = $options['force_template_part'][0];
                $name = isset( $options['force_template_part'][1] ) ? $options['force_template_part'][1] : '';
                Plethora_WP::get_template_part( $slug, $name );

            } else {

                $feature_dir  = ( $options['module'] ) ? "modules" : $matches[1][0] . "s"; // TURN SINGLE INTO PLURAL: 'widget' => 'widgets', 'shortcode' => 'shortcodes'
                $feature_file = ( $options['module'] ) ? $matches[1][0] . "-" . $matches[2][0] : $matches[2][0];
                Plethora_WP::get_template_part( "templates/" . $feature_dir . "/" . $feature_file, 'mustache' );
            }

            $mustache_tmpl = ob_get_contents();
            ob_end_clean();

            ob_start();                                                             
            echo trim( $mustache->render( $mustache_tmpl, $options['data'] ) ); 
            return ob_get_clean();  
        } 
    }

    /**
    * [showPageTemplate description]
    * @return [type] [description]
    */
    public static function showPageTemplate( $options ){

        $defaults = array( 
            "before" => "<!-- TEMPLATE PART: ",
            "after"  => " -->",
            "always" => false
        );

        $options = ( isset($options) && is_array($options) )? array_replace_recursive ( $defaults , $options ) : $defaults;

        global $template;
        
        $template_name = substr( $template, ( strpos( $template, 'wp-content/') + 10 ) );

        if ( Plethora_Theme::is_developermode() || $options["always"] === true ){

            return $options["before"] . $template_name . $options["after"];

        }  

    }

    static function apply_data_attrs( $filter ) {

    	$return = array();
    	$data_attrs = array();
    	if ( has_filter( $filter) ) {
	    	$data_attrs = apply_filters( $filter, $data_attrs );
	    	foreach ( $data_attrs as $attr_key => $attr_val ) {

	    		$return[] = 'data-'. esc_attr( $attr_key ) .'="'. esc_attr( $attr_val ) .'"';
	    	}
    	}

    	return $return;
    }

    /**
    * Read file using Redux WP_Filesystem proxy
    * @return string|boolean
    */
	static function get_file_contents( $file, $args = array() ) {

        if ( Plethora_Theme::is_library_active() && class_exists('ReduxFrameworkInstances')  ) {

		  $redux = ReduxFrameworkInstances::get_instance( THEME_OPTVAR );
		  return $redux->filesystem->execute( 'get_contents', $file, $args );

        } else {

          return false;
        }
	}

    /**
    * Write to file using Redux WP_Filesystem proxy
    * @return boolean
    */
	static function write_to_file( $file, $content, $args = array() ) {

        if ( Plethora_Theme::is_library_active() && class_exists('ReduxFrameworkInstances')  ) {

    		$redux = ReduxFrameworkInstances::get_instance( THEME_OPTVAR );
    		$args['content'] = $content;
    		return $redux->filesystem->execute( 'put_contents', $file, $args );

        } else {

          return false;
        }
	}

//// GENERAL PLETHORA CONDITIONALS & SNIPPETS <- FINISH
}