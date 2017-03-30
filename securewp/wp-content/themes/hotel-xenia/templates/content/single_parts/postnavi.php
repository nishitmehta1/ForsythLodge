<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            		  (c) 2016
Breadcrumb template part
*/
// Get attributes sent by module class
$options = get_query_var( 'options' );
extract( $options );

if ( !empty( $postnavi_prev_url ) ) { ?>
	<a href="<?php echo esc_url( $postnavi_prev_url ); ?>">
		<span class="previous_icon"><?php echo wp_kses( $postnavi_prev_label, Plethora_Theme::allowed_html_for( 'post' ) ); ?></span>
	</a>
<?php }

if ( !empty( $postnavi_next_url ) ) { ?>

	<a href="<?php echo esc_url( $postnavi_next_url ); ?>">
		<span class="next_icon"><?php echo wp_kses( $postnavi_next_label, Plethora_Theme::allowed_html_for( 'post' ) ); ?>  </span>
	</a>
<?php }