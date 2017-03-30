<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

Single Room View Template Parts / Amenities
*/
$options = get_query_var( 'options' );
if ( is_array( $options ) ) { extract($options); }
?>
<p class="persons"><?php echo wp_kses( $persons_text, Plethora_Theme::allowed_html_for( 'heading' ) ); ?></p>