<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Single Post Template Parts // Content
*/
$options = get_query_var( 'options' );
extract( $options );
$GLOBALS['comment'] = $comment;
$figure_class = $depth > 1 ? 'col-sm-2 col-md-2 col-md-offset-' . ($depth - 1) : 'col-sm-2 col-md-2';
$main_class = $depth > 1 ? 'col-sm-' . (11 - $depth) .' col-md-' . (11 - $depth) .'' : 'col-sm-10 col-md-10';
if ( $depth > 1 ) { echo '<div class="clearfix"></div>'; }
?>

<div <?php comment_class('row'); ?> id="comment-<?php comment_ID(); ?>">
  <figure class="<?php echo esc_attr( $figure_class ); ?>"><?php echo get_avatar( $comment, 100 ); ?> </figure>
  <div class="<?php echo esc_attr( $main_class ); ?>">
    <div class="comment_name"><?php comment_author($comment->comment_ID); ?> <?php comment_reply_link( array_merge( $args, array(  'reply_text' => esc_html__( 'Reply', 'hotel-xenia' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></div>
    <div class="comment_date"><i class="fa fa-clock-o"></i> <?php comment_date( '', $comment->comment_ID ) ?></div>
    <div class="the_comment">
      <?php comment_text(); ?>
    </div>
  </div>