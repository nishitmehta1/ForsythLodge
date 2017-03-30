<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

Single View Template Parts / Media, according to post format ( image, audio or video ) 
*/
$options = get_query_var( 'options' );
if ( is_array( $options ) ) { extract($options); }

if ( $post_format === 'gallery' ) { ?> <div class="slider_wrapper"><div class="owl-carousel owl-room-single-carousel"> <?php }

echo Plethora_Theme::get_post_media( array(
                                            'type'          => $post_format, 
                                            'stretch'       => true, 
                                            'link_to_post'  => false,
                              		) 
); 

if ( $post_format === 'gallery' ) { ?>  </div></div> <?php }
