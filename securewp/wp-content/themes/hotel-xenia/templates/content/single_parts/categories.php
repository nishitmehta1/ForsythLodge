<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Single View Template Parts / Categories ( or any other admin set primary taxonomy )
*/
$options = get_query_var( 'options' );
if ( is_array( $options ) ) { extract($options); } 

$categories = Plethora_Theme::get_post_infolabel( array( 'type' => 'categories' ) );
if ( !empty( $categories ) ) { ?>
<span class="blog_post_categories"><?php echo esc_html__('On', 'hotel-xenia' ) .' '. $categories; ?></span>
<?php }