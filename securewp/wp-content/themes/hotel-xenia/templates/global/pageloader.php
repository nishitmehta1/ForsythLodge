<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Page loader effect output

*/
$options = get_query_var( 'options' );
extract( $options );
?>
<div class="loading text-center vertical-middle">
  <div class="row">
    <?php if ( !empty( $loader_url ) ) { ?>
    <img src="<?php echo esc_url( $loader_url ); ?>" alt="Loader" class="loader"/>
    <?php } ?>
    <?php if ( !empty( $logo_url ) ) { ?>
    <img src="<?php echo esc_url( $logo_url ); ?>" alt="Loader Logo" class="loader_logo"/>
    <?php } ?>
  </div>
</div>