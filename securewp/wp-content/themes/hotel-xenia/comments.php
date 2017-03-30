<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2014

File Description: Comments Display Template Part 

*/
if ( post_password_required() ) { return; }

// Prepare options that need to pass to template parts
$post_type                = get_post_type();
$options                  = array();
$options['content_align'] = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-contentalign', '' );

if ( have_comments() ) { 
  
  set_query_var( 'options', $options );
  Plethora_WP::get_template_part( 'templates/content/single_parts/comments-list' );
  set_query_var( 'options', $options );
  Plethora_WP::get_template_part( 'templates/content/single_parts/comments-paging' );
}
     
set_query_var( 'options', $options );
Plethora_WP::get_template_part( 'templates/content/single_parts/comments-new' );