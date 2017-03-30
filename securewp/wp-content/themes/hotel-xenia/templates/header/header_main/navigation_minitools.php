<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

Navigation Mini Tools template part

*/
$options = get_query_var( 'options' );
extract( $options );
?>
  <div class="<?php echo esc_attr( $minitools_class ); ?>">
    <?php echo trim( $minitools_output ); ?>    
  </div>