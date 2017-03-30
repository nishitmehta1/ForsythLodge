<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: SVG loader modal markup

*/
if ( wp_script_is( 'svgloader' ) ) {
?>
<div class="progress_ball"><div class="c_loader"><?php echo esc_html__( 'Loading...', 'hotel-xenia'); ?></div></div>
<div class="loader-modal"></div>
<div id="loader" class="pageload-overlay" data-opening="M20,15 50,30 50,30 30,30 Z;M0,0 80,0 50,30 20,45 Z;M0,0 80,0 60,45 0,60 Z;M0,0 80,0 80,60 0,60 Z" data-closing="M0,0 80,0 60,45 0,60 Z;M0,0 80,0 50,30 20,45 Z;M20,15 50,30 50,30 30,30 Z;M30,30 50,30 50,30 30,30 Z">
  <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 80 60" preserveAspectRatio="none">
    <path d="M30,30 50,30 50,30 30,30 Z"/>
  </svg>
</div><?php
}
?>