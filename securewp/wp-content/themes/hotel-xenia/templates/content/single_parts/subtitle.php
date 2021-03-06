<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Single View Template Parts / Subtitle
*/
$options = get_query_var( 'options' );
if ( is_array( $options ) ) { extract($options); }

$subtitle = Plethora_Theme::get_subtitle( array( 'tag' => 'p' ) );
if ( !empty( $subtitle ) ) { echo wp_kses( $subtitle, Plethora_Theme::allowed_html_for( 'heading' ) ); }