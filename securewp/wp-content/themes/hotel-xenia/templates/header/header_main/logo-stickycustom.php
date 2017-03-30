<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

Custom sticky Header Logo template part

*/
$options = get_query_var( 'options' );
extract( $options );
?>
<div class="<?php echo esc_attr( $sticky_custom_logo_class ); ?>">
  <a href="<?php echo esc_url(  $sticky_custom_logo_url ); ?>" class="<?php echo esc_attr( $sticky_custom_logo_url_class ); ?>">
    <?php echo wp_kses( $sticky_custom_logo_output_title, Plethora_Theme::allowed_html_for( 'paragraph' ) ); ?>
  </a>
  <?php echo wp_kses( $sticky_custom_logo_output_subtitle, Plethora_Theme::allowed_html_for( 'paragraph' ) ); ?>
</div>