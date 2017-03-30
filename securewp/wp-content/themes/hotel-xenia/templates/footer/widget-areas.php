<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2014

Footer Template Parts / Widget areas ( 1st & 2nd row )
*/
$options = get_query_var( 'options' );
extract( $options );
Plethora_Theme::dev_comment('   ========================= FOOTER WIDGETS '. $row_desc .' ========================', 'layout');

foreach ( $widget_areas as $widget_area ) { ?>

		<div class="<?php echo esc_attr( $widget_area['class'] ); ?>"><?php dynamic_sidebar( $widget_area['sidebar'] ); ?></div>

<?php } 

Plethora_Theme::dev_comment('   END ========================= FOOTER WIDGETS '. $row_desc .' FINISH ========================', 'layout'); ?>