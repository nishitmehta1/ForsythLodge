<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

Navigation Toggler template part

*/
$options = get_query_var( 'options' );
extract( $options );
?>
  <a class="menu-toggler">
  <?php if ( $label_more ) { ?>

      <span class="title above_threshold"><?php echo wp_kses( $label_more_text, Plethora_Theme::allowed_html_for( 'button' ) ); ?></span>

  <?php } ?>
  <?php if ( $label_menu ) { ?>

        <span class="title below_threshold"><?php echo wp_kses( $label_menu_text, Plethora_Theme::allowed_html_for( 'button' ) ); ?></span>

  <?php } ?>
  
  <span class="lines <?php echo esc_attr( $navicon_class ); ?>"></span></a>