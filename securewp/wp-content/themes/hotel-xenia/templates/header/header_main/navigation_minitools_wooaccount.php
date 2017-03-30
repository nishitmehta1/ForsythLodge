<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

File Description: Woo User Account Cart Mini Tool

*/
$options = get_query_var( 'options' );
extract( $options );
?>
<div class="ple_woo_container" id="ple_wooaccount">
    <a class="ple_woo_user" href="<?php echo esc_url( $account_url ); ?>" title="<?php echo esc_attr( $account_title ); ?>">
     <?php if ( !empty( $account_icon ) ) { echo '<i class="'. esc_attr( $account_icon ).'"></i>';  } ?>
    </a>
</div>