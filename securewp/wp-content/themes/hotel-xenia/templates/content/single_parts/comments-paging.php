<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Returns comments paging 
*/
// Get query var options and extract
$options = get_query_var( 'options' );
extract( $options );

if ( have_comments() ) { ?>
    <div id="comments_paging" class="no_padding_top no_padding_bottom">
    <?php 
      $page_comments = get_option('page_comments');
      if ( get_comment_pages_count() > 1 && $page_comments ) {  
      ?>
          <nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
            <div class="row">
              <div class="col-md-6 text-right"><div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'hotel-xenia' ) ); ?></div></div>
              <div class="col-md-6 text-left"><div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'hotel-xenia' ) ); ?></div></div>
            </div>
          </nav><!-- #comment-nav-below -->
      <?php
      } ?>
    </div>
<?php }