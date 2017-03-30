<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Displays Blog intro text

*/
$options = get_query_var( 'options' );
extract( $options );
?>
    <div class="blog_intro">
          <?php echo wp_kses_post( $intro_text ); ?> 
    </div>