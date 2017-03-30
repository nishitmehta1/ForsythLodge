<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

File Description: Woo Icon Cart Mini Tool

*/
$options = get_query_var( 'options' );
extract( $options );
?>
<div class="ple_woo_container" id="ple_woocart">
    <a class="ple_woo_cart_icon" href="<?php echo esc_url( $cart_url ); ?>" title="<?php echo esc_attr( $cart_title ); ?>">
    <?php if ( !empty( $cart_icon ) ) { echo '<i class="'. esc_attr( $cart_icon ).'"></i>';  } ?>
    <?php if ( !empty( $cart_count ) ) { echo '<span class="ple_woo_cart_count">'. esc_attr( $cart_count ).'</span>';  } ?>
    <?php if ( !empty( $cart_total ) ) { echo $cart_total; } ?>
    </a>
</div>