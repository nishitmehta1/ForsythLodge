<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

MailChimp Form Shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Shortcode_Mailchimp') && !class_exists('Plethora_Shortcode_Mailchimp_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-featureslib/features/shortcode/mailchimp/shortcode-mailchimp.php
   */
  class Plethora_Shortcode_Mailchimp_Ext extends Plethora_Shortcode_Mailchimp { 

  }
}