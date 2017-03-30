<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Single View Template Parts / Tags ( or any other admin set secondary taxonomy )
*/
$options = get_query_var( 'options' );
if ( is_array( $options ) ) { extract($options); } 

$tags = Plethora_Theme::get_post_infolabel( array( 'type' => 'tags' ) );
if ( !empty( $tags ) ) { ?>
<span class="blog_post_tags"><?php echo esc_html__('Tagged as', 'hotel-xenia' ) .' '. $tags; ?></span>
<?php }