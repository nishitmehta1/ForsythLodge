<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Footer Bar template part

*/
$options = get_query_var( 'options' );
extract( $options );
foreach ( $cols as $col ) { ?>

   <div class="<?php echo esc_attr( $col['col_class'] ); ?>">
		<?php echo trim( $col['col_content'] ); // has wp_kses_post validation on: includes/core/features/module/footerbar/module-footerbar.php ?>
   </div>

<?php }