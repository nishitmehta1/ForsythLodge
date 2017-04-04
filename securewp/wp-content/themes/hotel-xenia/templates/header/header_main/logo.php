<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                   (c) 2016

Logo template part
*/
$options = get_query_var( 'options' );
extract( $options );
?>

<div class="<?php echo esc_attr( $logo_class ); ?>">

  <a href="<?php echo esc_attr( $logo_url ); ?>" class="<?php echo esc_attr( $logo_url_class ); ?>">
    <img src="<?php echo get_bloginfo("template_url"); ?>/images/logo.svg" alt="Site Logo">
  </a>

</div>