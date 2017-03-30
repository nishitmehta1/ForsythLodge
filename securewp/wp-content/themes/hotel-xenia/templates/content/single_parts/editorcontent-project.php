<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Single View Template Parts / Project ( portfolio ) editor content 
*/
$options = get_query_var( 'options' );
if ( is_array( $options ) ) { extract($options); } ?>

<div class="portfolio_single_content">
<?php echo Plethora_Theme::get_post_content(); ?>
</div>