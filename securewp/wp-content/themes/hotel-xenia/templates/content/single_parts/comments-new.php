<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Returns new comments form 
*/

// Get query var options and extract
$options = get_query_var( 'options' );
extract( $options );

if ( comments_open() ) {
?>
<div id="new_comment" class="new_comment">
  <?php 

  $commenter = wp_get_current_commenter();
  $req = get_option( 'require_name_email' );
  $aria_req = ( $req ? ' aria-required="true"' : '' );

  $new_comment_args = array( 
    'fields'               => apply_filters( 'comment_form_default_fields', array( 
                                'author'=> '<div class="row"><div class="col-sm-6 col-md-4 comment-form-author"><input placeholder="'. esc_html__('Your name', 'hotel-xenia') .'" type="text" class="form-control" id="author" name="author" value="'. esc_attr( $commenter['comment_author'] )  .'"' . $aria_req . '></div>', 
                                'email' => '<div class="col-sm-6 col-md-4 comment-form-email"><input placeholder="'. esc_html__('Your email', 'hotel-xenia') .'" type="text" class="form-control"  id="email" name="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"' . $aria_req . '></div></div>',
                              )),
    'comment_field'        => '<div class="row"><div class="col-sm-12 col-md-8"><textarea rows="5" placeholder="'. esc_html__('Comments', 'hotel-xenia') .'" class="form-control"  id="comment" name="comment"' . $aria_req . '></textarea></div></div>',
    'comment_notes_before' => '',
    'comment_notes_after'  => '',
    'title_reply'          => esc_html__( 'Add Comment', 'hotel-xenia' ),
    'title_reply_to'       => esc_html__( 'Reply to %s', 'hotel-xenia' ),
    'cancel_reply_link'    => esc_html__( 'Cancel', 'hotel-xenia' ),
    'label_submit'         => esc_html__( 'Add Comment', 'hotel-xenia' ),
    'class_submit'         => 'btn btn-default'
  );

  comment_form( $new_comment_args );
  ?>
</div>
<?php }