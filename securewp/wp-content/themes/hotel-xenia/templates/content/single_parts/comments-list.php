<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Single Post Template Parts // Content
*/
// Get query var options and extract
$options = get_query_var( 'options' );
extract( $options );

if ( have_comments() ) { ?>
  <div id="post_comments" class="post_comments">
    <h4><?php echo esc_html__('Comments', 'hotel-xenia') ?></h4>
    <!-- Comments List start -->
    <div class="comment">
        <?php
          wp_list_comments( array(
            'style'       => 'div',
            'avatar_size' => 100,
            'callback'    => array( 'Plethora_Theme', 'comments_list_callback'),
            'format'      =>'html5',
          ));
        ?>
    </div>
  </div>
<?php }