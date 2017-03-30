<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

File Description: Newsletter Form Shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_NewsletterForm') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_NewsletterForm extends Plethora_Shortcode { 

      public static $feature_name          = "MailChimp Form";                // FEATURE DISPLAY TITLE 
      public static $feature_title         = "MailChimp Form Shortcode";      // FEATURE DISPLAY TITLE 
      public static $feature_description   = "Display MailChimp Form";        // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                             // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                             // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array();                          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                             // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;                            // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public $wp_slug                      = 'newsletterform';                 // THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $assets                = array(
                                                array( 'script' => 'newsletter_form' ),
                                                array( 'script' => 'newsletter_form_svg' ),
                                             );


      public static $shortcode_category    = "Forms";

      public function __construct() {

          // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'          => sprintf( esc_html__( '%s', 'plethora-framework' ), self::$feature_name ),
                      'description'   => sprintf( esc_html__( '%s', 'plethora-framework'), self::$feature_description ),
                      'class'         => '',
                      'weight'        => 1,
                      'category'      => sprintf( esc_html__( '%s', 'plethora-framework'), self:: $shortcode_category ),
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( self::$feature_name ), 
                      'params'        => $this->params(), 
                      );
          $this->add( $map );         // ADD ΤΗΕ SHORTCODE

          add_action("wp_ajax_newsletter_form", array( $this, "ajax_handler") );
          add_action("wp_ajax_nopriv_newsletter_form", array( $this, "ajax_handler") );

          // PLEFIXME: temporary themeconfig workaround
          Plethora_Theme::set_themeconfig( "NEWSLETTERS", array(
                    'messages' => array(
                        'successMessage' => esc_html__("SUCCESS", 'plethora-framework'),
                        'errorMessage'   => esc_html__("ERROR", 'plethora-framework'),
                        'required'       => esc_html__("This field is required.", 'plethora-framework'),
                        'remote'         => esc_html__("Please fix this field.", 'plethora-framework'),
                        'url'            => esc_html__("Please enter a valid URL.", 'plethora-framework'),
                        'date'           => esc_html__("Please enter a valid date.", 'plethora-framework'),
                        'dateISO'        => esc_html__("Please enter a valid date ( ISO ).", 'plethora-framework'),
                        'number'         => esc_html__("Please enter a valid number.", 'plethora-framework'),
                        'digits'         => esc_html__("Please enter only digits.", 'plethora-framework'),
                        'creditcard'     => esc_html__("Please enter a valid credit card number.", 'plethora-framework'),
                        'equalTo'        => esc_html__("Please enter the same value again.", 'plethora-framework'),
                        'name'           => esc_html__("Please specify your name", 'plethora-framework'),
                        'email'          => array( 
                                            'required' => esc_html__("We need your email address to contact you", 'plethora-framework'),
                                            'email'    => esc_html__("Your email address must be in the format of name@domain.com", 'plethora-framework')
                                            ),
                    )
          ));
      }

       /** 
       * Returns shortcode settings (compatible with Visual composer)
       *
       * @return array
       * @since 1.0
       *
       */
       public function params() {

          $params = array(

                  array(
                      "param_name"    => "email_placeholder",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      "heading"       => esc_html__("Email field placeholder", 'plethora-framework'),
                      "value"         => esc_html__( 'Email Address', 'plethora-framework'),                                 
                      "description"   => esc_html__("Text that appears in the email field placeholder", 'plethora-framework'),      
                      "admin_label"   => false,                                             
                  ),
                  /*** ENABLE FIRST NAME - SURNAME INPUT FIELDS [1] ***/
                  array(
                      "param_name"    => "name_inputbox",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h4",                                               
                      "class"         => "vc_hidden",                                                    
                      "heading"       => esc_html__("Display Name-Surname Box", 'plethora-framework'),
                      "value"         => array( 'Hide'=>'0', 'Show'=>'1' ),
                      "description"   => esc_html__("Displays two extra input fields for submitting first and last name.", 'plethora-framework'),      
                      "admin_label"   => false                                             
                  )
                  ,array(
                      "param_name"       => "firstname_placeholder",                                  
                      "type"             => "textfield",                                        
                      "holder"           => "h4",                                               
                      "class"            => "vc_hidden",                                                    
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Name field placeholder", 'plethora-framework'),
                      "value"            => esc_html__( 'First Name', 'plethora-framework'),                                     
                      "description"      => esc_html__("Text that appears in the name field placeholder", 'plethora-framework'),      
                      "admin_label"      => false,                                             
                      'dependency'       => array( 
                                          'element' => 'name_inputbox', 
                                          'value' => '1'   
                                      )
                  )
                  ,array(
                      "param_name"       => "lastname_placeholder",                                  
                      "type"             => "textfield",                                        
                      "holder"           => "h4",                                               
                      "class"            => "vc_hidden",                                                    
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Surname field placeholder", 'plethora-framework'),
                      "value"            => esc_html__( 'Last Name', 'plethora-framework'),                                     
                      "description"      => esc_html__("Text that appears in the surname field placeholder", 'plethora-framework'),      
                      "admin_label"      => false,                                             
                      'dependency'       => array( 
                                          'element' => 'name_inputbox', 
                                          'value' => '1'   
                                      )
                  ),
                  /*** /ENABLE FIRST NAME - SURNAME INPUT FIELDS [1] ***/
                  array(
                      "param_name"    => "button_text",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"       => esc_html__("Subscribe button text", 'plethora-framework'),
                      "value"         => esc_html__( 'SUBSCRIBE', 'plethora-framework'),                                   
                      "description"   => esc_html__("Text that appears in the Subscribe button (accepts html)", 'plethora-framework'),      
                      "admin_label"   => false,                                             
                  ),
                  array(
                      "param_name"    => "alignment",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"       => esc_html__("Alignment", 'plethora-framework'),
                      "value"         => array( 
                        'Center' => 'text-center', 
                        'Left'   => 'text-left',
                        'Right'  => 'text-right'
                        ),
                      "description"   => '',      
                      "admin_label"   => false                                             
                  ),
                  array(
                      "param_name"    => "icon_enable",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"       => esc_html__("Add icon next to the submit button?", 'plethora-framework'),
                      "value"         => array( 
                        'Disabled' => 'disabled', 
                        'Enabled'   => 'enabled'
                        ),
                      "admin_label"   => false                                             
                  ),
                  array(
                      "param_name"    => "icon",
                      "type"          => "iconpicker",
                      "holder"        => "",                                               
                      "class"         => "vc_hidden", 
                      "admin_label"   => false,                                             
                      'group'         => esc_html__( 'Icon', 'plethora-framework' ),
                      "heading"       => esc_html__('Select the icon that will be displayed in the submit button', 'plethora-framework'),
                      "description"   => esc_html__("Select an icon to display.", 'plethora-framework'),
                      'settings'      => array(
                        'type'         => 'plethora',
                        'iconsPerPage' => 56, // default 100, how many icons per/page to display
                      ),
                      'dependency'    => array( 
                                          'element' => 'icon_enable', 
                                          'value'   => array('enabled'),  
                                      )
                  ),
                  array(
                      "param_name"    => "svg_newsletter",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"       => esc_html__("Use SVG Newsletter Template", 'plethora-framework'),
                      "value"         => array( 'Disable'=>'0', 'Enable'=>'1' ),
                      "description"   => esc_html__("Displays alternative Newsletter design based on SVG. NOTE: Only one is allowed per page.", 'plethora-framework'),      
                      "admin_label"   => false                                             
                  ),
                  array(
                      "param_name"    => "svg_newsletter_title",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "",                                                    
                      'group'         => esc_html__( 'SVG options', 'plethora-framework' ),
                      "heading"       => esc_html__("SVG Newsletter Title", 'plethora-framework'),
                      "value"         => 'OUR NEWSLETTER',                                     
                      "description"   => esc_html__("Title that appears in the SVG Newsletter left section", 'plethora-framework'),      
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'svg_newsletter', 
                                          'value' => "1",   
                                      )
                  ),
                  array(
                      "param_name"    => "svg_newsletter_subtitle",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h4",                                               
                      "class"         => "",                                                    
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'group'         => esc_html__( 'SVG options', 'plethora-framework' ),
                      "heading"       => esc_html__("SVG Newsletter Subtitle", 'plethora-framework'),
                      "value"         => 'Subscribe and get daily notifications about our offers',
                      "description"   => esc_html__("Subtitle that appears in the SVG Newsletter left section", 'plethora-framework'),      
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'svg_newsletter', 
                                          'value' => "1",   
                                      )
                  ),
                  array(
                    "param_name"    => "svg_newsletter_image",                                  
                    "type"          => "attach_image",                                        
                    "holder"        => "",                                               
                    "class"         => "vc_hidden", 
                    'group'         => esc_html__( 'SVG options', 'plethora-framework' ),
                    'edit_field_class' => 'vc_col-sm-6 vc_column',
                    "heading"       => esc_html__("Background Image*", 'plethora-framework'),      
                    "description"   => esc_html__("*NOTE: Please use a 600x180 image.", 'plethora-framework'),      
                    "value"         => '',
                    "admin_label"   => false,                                              
                    'dependency'    => array( 
                                          'element' => 'svg_newsletter', 
                                          'value'   => '1',  
                                      )
                  ),
                  array(
                    "param_name"    => "css",
                    "type"          => "css_editor",
                    'group'         => esc_html__( 'Design options', 'plethora-framework' ),
                    "heading"       => esc_html__('CSS box', 'plethora-framework'),
                  ),
          );

          return $params;

       }


       /** 
       * Returns shortcode content
       *
       * @return array
       * @since 1.0
       *
       */
       public function content( $atts, $content = null ) {

          extract( shortcode_atts( array( 

            "email_placeholder"       => 'Email Address',                                     
            "button_text"             => 'SUBSCRIBE',
            "svg_newsletter"          => '0',
            "svg_newsletter_image"    => "",
            "svg_newsletter_title"    => "OUR NEWSLETTER",
            "svg_newsletter_subtitle" => "Subscribe and get daily notifications about our offers",
            /*** ENABLE FIRST NAME - SURNAME INPUT FIELDS [2] ***/
            "name_inputbox"           => '0',
            "firstname_placeholder"   => "First Name",
            "lastname_placeholder"    => "Last Name",
            /*** /ENABLE FIRST NAME - SURNAME INPUT FIELDS [2] ***/
            "alignment"               => "text-center",
            "icon_enable"             => "disabled",
            "icon"                    => "",
            "css"                     => ""

            ), $atts ) );

          $shortcode_atts = array(

            "email_placeholder" => esc_attr( $email_placeholder ),
            "button_text"       => esc_attr( $button_text ),
            "title"             => esc_attr( $svg_newsletter_title ),
            "subtitle"          => esc_attr( $svg_newsletter_subtitle ),
            /*** ENABLE FIRST NAME - SURNAME INPUT FIELDS [3] ***/
            "name_inputbox"     => ( $name_inputbox == '0' ) ? "" : "display",
            /*** ENABLE FIRST NAME - SURNAME INPUT FIELDS [3] ***/
            "firstname"         => esc_attr( $firstname_placeholder ),
            "lastname"          => esc_attr( $lastname_placeholder ),
            "action"            => admin_url( 'admin-ajax.php' ),
            "nonce"             => esc_attr( wp_create_nonce("newsletter_form_nonce") ),
            "alignment"         => $alignment,
            "css"               => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),

          );
          // ICON
          if ( $icon_enable == "enabled" ){
            $shortcode_atts["icon"] = "<i class='fa fa-" . $icon . "'></i>";
          }


          // IF SVG OPTION
          if ( $svg_newsletter === "1" ){

            $svg_newsletter_image = wp_get_attachment_image_src( $svg_newsletter_image, 'full' );
            $svg_newsletter_image = $svg_newsletter_image[0];

            // PLEFIXME: temporary themeconfig workaround
            Plethora_Theme::set_themeconfig( "SVG_NEWSLETTER", array(
                    'image' => $svg_newsletter_image ,
            ));

            $newsletter_template  = str_replace( "newsletterform", "newsletterformsvg", __FILE__ );
            return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => $newsletter_template ) );

          }
          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

      }

      public function ajax_handler() {

        if ( !wp_verify_nonce( $_POST['nonce'], "newsletter_form_nonce")) {
            exit("No naughty business please");
        }   

        $result['type']    = "error";
        $result['message'] = "";
        $result['debug']   = "";
        $email             = sanitize_email( $_POST["email"] );
        $firstname         = isset( $_POST["firstname"] ) ? sanitize_text_field( $_POST["firstname"] ) : "";
        $lastname          = isset( $_POST["surname"] ) ? sanitize_text_field( $_POST["surname"] ) : "";

         if( is_email( $email ) === false ) {
            $result['type']    = "error";
            $result['message'] = esc_html__( 'Invalid email', 'plethora-framework' );
            $result['debug']   = "";
         } else {
          // Send to MailChimp API

          $mailchimp_apikey = method_exists('Plethora_Theme', 'option') ? Plethora::option( METAOPTION_PREFIX . 'mailchimp_apikey') : '';
          $mailchimp_listid = method_exists('Plethora_Theme', 'option') ? Plethora::option( METAOPTION_PREFIX . 'mailchimp_listid') : '';

          if ( 
            class_exists('Plethora_Module_Mailchimp') 
            && $mailchimp_apikey !== ''
            && $mailchimp_listid !== ''
          ) { 

            $MailChimp        = new Plethora_Module_Mailchimp( $mailchimp_apikey );
            
            $mailchimp_data = array(
              'id'                => $mailchimp_listid,
              'email'             => array( 'email'=> $_POST['email'] ),
              'double_optin'      => false,
              'update_existing'   => true,
              'replace_interests' => false,
              'send_welcome'      => false,
            );

            if ( $firstname !== "" || $lastname !== "" ){
              $mailchimp_data['merge_vars'] = array( 'FNAME'=> $firstname, 'LNAME'=> $lastname );
            }

            $result = $MailChimp->call('lists/subscribe', $mailchimp_data );

            if ( $result === false ){

              $result['type']    = "error";
              $result['message'] = esc_html__( "ERROR", "plethora-theme" );
              $result['debug']   = esc_html__( "Error connecting to the MailChimp API endpoint.", 'plethora-framework');

            } else {

              if ( isset($result['status']) && $result['status'] === "error" ){

                $result['type']    = "error";
                $result['message'] = esc_html__( "ERROR", "plethora-theme" );
                $result['debug']   = $result['error'];

              } else {

                $result['debug']   = print_r( $result, true );
                $result['type']    = "success";
                $result['message'] = esc_html__( 'Successful request', 'plethora-framework' );


              }

            }

          } 

         }

         if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result; // ajax response...do not sanitize
         }
         else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
         }

         die();

        }


  }
  
 endif;