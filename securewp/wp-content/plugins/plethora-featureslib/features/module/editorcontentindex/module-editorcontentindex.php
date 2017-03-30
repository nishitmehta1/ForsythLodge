<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Editor Content Index Module Base Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( !class_exists('Plethora_Module_Editorcontentindex') ) {

  /**
   * @package Plethora Framework
   */
  class Plethora_Module_Editorcontentindex { 

    public static $feature_title         = "Editor Content Index Module";                // Feature display title  (string)
    public static $feature_description   = ""; // Feature display description (string)
    public static $theme_option_control  = true;                       // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                       // Default activation option status ( boolean )
    public static $theme_option_requires = array();                       // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                        // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;                       // Additional method invocation ( string/boolean | method name or false )
	
  	public $id_replacements = array();
  	public $nav_items       = array();
    public $post_types_supported = array( 'post', 'terminology' );
  	public function __construct() {

      if ( is_admin() ) {

        // Index headings on save post
        add_action( 'admin_footer', array( $this, 'remove_local_storage_notice'), 9);
        add_action( 'save_post', array( $this, 'index_content_headings'), 10, 2 );
        add_action( 'save_post', array( $this, 'update_content_heading_ids'), 10, 2 );
        add_action( 'save_post', array( $this, 'update_meta'), 10, 2 );
      } 
  	}

  /**
   * Just removes the annoying notice: 'The backup of this post in your browser is different from the version below'
   * Hooked @ 'admin_footer'
   */
  	public function remove_local_storage_notice() {

		  remove_action( 'admin_footer', '_local_storage_notice', 10 );
  	}

  /**
   * Index editor content headings on save_post
   * Hooked @ 'save_post'
   */
  	public function index_content_headings( $post_id, $post ) {

      if ( ! in_array( $post->post_type, $this->post_types_supported )  || ! isset( $post->post_content ) ) { return; }

  		$content = $post->post_content;
  		// If this is just a revision, don't send the email.
  		$this->raw_headings = $this->extract_tags( $content, 'h\d+', false, true );
      foreach ( $this->raw_headings as $heading ) {

    			// ALL helper variables
    			$oldtag_full                  = $heading['full_tag'];
    			$newtag                       = $heading['tag_name'];
    			$newtag_innercontent_raw      = $heading['contents'];
    			$newtag_innercontent_stripped = strip_tags( $newtag_innercontent_raw );
          $newtag_id                    = bin2hex( $newtag_innercontent_stripped ) ;
    			$newtag_attrs_raw             = array_merge( $heading['attributes'], array( 'id' => $newtag_id ) );
    			$newtag_attrs                 = '';
          foreach ( $newtag_attrs_raw as $attr_key => $attr_val ) {
            	$newtag_attrs .= ' '. $attr_key .'="'. $attr_val .'"';
          }
			     $newtag_full = '<'. $newtag . $newtag_attrs .'>'. $newtag_innercontent_raw .'</'. $newtag .'>';
            
          // If full HTML tags are not the same, then add this to the id replacements index
          $this->id_replacements[]  = array(
    				'find'    => $oldtag_full,
    				'replace' => $newtag_full
          );

        	// Add this to navigation items
        	$this->nav_items[] = array(
    				'id'          => $newtag_id,
    				'anchor_text' => $newtag_innercontent_stripped,
    				'level'       => str_replace( 'h', '', $newtag ),
        	);
        }
  	}

  /**
   * Replace ids according to the re
   * Hooked @ 'save_post'
   */
  	public function update_meta( $post_id, $post ) {

  		if ( in_array( $post->post_type, $this->post_types_supported ) ) { 

  		   update_post_meta( $post_id, METAOPTION_PREFIX .'contentindex_items', $this->nav_items );
  		}		
  	}
  /**
   * Replace ids according to the re
   * Hooked @ 'save_post'
   */
  	public function update_content_heading_ids( $post_id, $post ) {

      if ( ! in_array( $post->post_type, $this->post_types_supported )  ||  empty( $this->id_replacements ) || ! isset( $post->post_content ) ) { return; }

  		$content = $post->post_content;

	    foreach ( $this->id_replacements as $replacement ) {

        $content = str_replace( $replacement['find'], $replacement['replace'], $content );
	    }

	    // Since I want to call wp_update_post, unhook this function so it doesn't loop infinitely
	    remove_action( 'save_post', array( $this, 'update_content_heading_ids' ), 10 );
	    remove_action( 'save_post', array( $this, 'index_content_headings' ), 10 );
	    remove_action( 'save_post', array( $this, 'update_meta' ), 10 );
	    // save updated content
	    wp_update_post( array( 
			'ID'           => $post_id, 
			'post_content' => $content 
	    ));

	    // re-hook this function
	    add_action( 'save_post', array( $this, 'update_content_heading_ids' ), 10, 2 );
	    add_action( 'save_post', array( $this, 'index_content_headings' ), 10, 2 );
      add_action( 'save_post', array( $this, 'update_meta' ), 10, 2 );
  	}


  /**
   * PUBLIC | Returns given tag(s) occurencies out of given HTML string
   * Extremely handy when we want to extract information, such as headings, links, etc.
   * $html - (string) - the HTML source containing the requested tags
   * $tag - (string/array) - tag(s) to retrieve
   * $selfclosing - (array/null) - true if this is a selclosing tag, null to leave script do the job
   * $return_the_entire_tag - (array ) - true to get all tag info
   * $charset - (array/null) - set a diffent charshet ( default 'UTF-8')
   */
  public function extract_tags( $html, $tag, $selfclosing = null, $return_the_entire_tag = false, $charset = 'UTF-8' ){
       
      if ( is_array($tag) ){
          $tag = implode('|', $tag);
      }
       
      //If the user didn't specify if $tag is a self-closing tag we try to auto-detect it
      //by checking against a list of known self-closing tags.
      $selfclosing_tags = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta', 'col', 'param' );
      if ( is_null($selfclosing) ){
          $selfclosing = in_array( $tag, $selfclosing_tags );
      }
       
      //The regexp is different for normal and self-closing tags because I can't figure out 
      //how to make a sufficiently robust unified one.
      if ( $selfclosing ){
          $tag_pattern = 
              '@<(?P<tag>'.$tag.')           # <tag
              (?P<attributes>\s[^>]+)?       # attributes, if any
              \s*/?>                   # /> or just >, being lenient here 
              @xsi';
      } else {
          $tag_pattern = 
              '@<(?P<tag>'.$tag.')           # <tag
              (?P<attributes>\s[^>]+)?       # attributes, if any
              \s*>                 # >
              (?P<contents>.*?)         # tag contents
              </(?P=tag)>               # the closing </tag>
              @xsi';
      }
       
      $attribute_pattern = 
          '@
          (?P<name>\w+)                         # attribute name
          \s*=\s*
          (
              (?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)    # a quoted value
              |                           # or
              (?P<value_unquoted>[^\s"\']+?)(?:\s+|$)           # an unquoted value (terminated by whitespace or EOF) 
          )
          @xsi';
   
      //Find all tags 
      if ( !preg_match_all($tag_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ){
          //Return an empty array if we didn't find anything
          return array();
      }
       
      $tags = array();
      foreach ($matches as $match){
           
          //Parse tag attributes, if any
          $attributes = array();
          if ( !empty($match['attributes'][0]) ){ 
               
              if ( preg_match_all( $attribute_pattern, $match['attributes'][0], $attribute_data, PREG_SET_ORDER ) ){
                  //Turn the attribute data into a name->value array
                  foreach($attribute_data as $attr){
                      if( !empty($attr['value_quoted']) ){
                          $value = $attr['value_quoted'];
                      } else if( !empty($attr['value_unquoted']) ){
                          $value = $attr['value_unquoted'];
                      } else {
                          $value = '';
                      }
                       
                      //Passing the value through html_entity_decode is handy when you want
                      //to extract link URLs or something like that. You might want to remove
                      //or modify this call if it doesn't fit your situation.
                      $value = html_entity_decode( $value, ENT_QUOTES, $charset );
                       
                      $attributes[$attr['name']] = $value;
                  }
              }
          }
           
          $tag = array(
              'tag_name' => $match['tag'][0],
              'offset' => $match[0][1], 
              'contents' => !empty($match['contents'])?$match['contents'][0]:'', //empty for self-closing tags
              'attributes' => $attributes, 
          );
          if ( $return_the_entire_tag ){
              $tag['full_tag'] = $match[0][0];            
          }
            
          $tags[] = $tag;
      }
       
      return $tags;
    }
  }  	
}