<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

Global Template Parts -> Search form(s) for 404 page
*/
$options = get_query_var( 'options' );
if ( is_array( $options ) ) { extract($options); } ?>

<div class="search_form">
  <form role="search" method="get" name="s" id="s" action="<?php echo esc_url( home_url( '/' )); ?>">
    <div class="col-md-8 col-md-offset-2">
      <input type="text" class="form-control text-center" name="s" id="search">
    </div>
    <div class="col-md-12 text-center"> 
      <button type="submit" id="submit_btn" class="btn btn-primary wpb_content_element"><?php echo esc_html( $submit_text ); ?></button>
    </div>
  </form>
</div>
<?php
