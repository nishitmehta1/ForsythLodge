<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Posts loop / Listing (default)
*/

$meta_date_wrapper_open       = '<span class="blog_post_date">';
$meta_author_wrapper_open     = '<span class="blog_post_author">';
$meta_categories_wrapper_open = '<span class="blog_post_categories">';
$meta_tags_wrapper_open       = '<span class="blog_post_tags">';
$meta_comments_wrapper_open   = '<span class="blog_post_comments">';
$span_wrapper_close           = '</span>';

?>
<div id="post-<?php the_ID(); ?>" <?php post_class( array( 'post', 'blog_post_listed' ) ); ?>>
    <div class="blog_post_listed_wrapper">

        <div class="blog_post_listed_meta">

            <?php echo Plethora_Theme::get_post_media( array( 'type' => get_post_format(), 'stretch' => true, 'link_to_post' => true, 'listing'=> true ) ); ?>

            <?php echo Plethora_Theme::get_post_infolabel( array( 'type' => 'date', 'prepend_html' => $meta_date_wrapper_open, 'append_html' => $span_wrapper_close ) ); ?>

            <?php echo Plethora_Theme::get_title(array( 'listing' => true, 'class' => array( 'textify_links' ), 'tag' => 'h2' ) ); ?>

            <?php echo Plethora_Theme::get_subtitle(array( 'listing' => true, 'class' => array( 'blog_post_listed_subtitle' ), 'tag' => 'p' ) ); ?>

            <?php echo Plethora_Theme::get_post_infolabel( array( 'type' => 'author', 'prepend_html' => $meta_author_wrapper_open . esc_html__('By ', 'hotel-xenia' ), 'listing' => true, 'append_html' => $span_wrapper_close ) ); ?>

            <?php echo Plethora_Theme::get_post_infolabel( array( 'type' => 'categories', 'prepend_html' => $meta_categories_wrapper_open . esc_html__('on ', 'hotel-xenia' ), 'listing' => true, 'append_html' => $span_wrapper_close ) ); ?>
            <?php echo Plethora_Theme::get_post_infolabel( array( 'type' => 'tags', 'prepend_html' => $meta_tags_wrapper_open . esc_html__('Tagged as ', 'hotel-xenia' ), 'listing' => true, 'append_html' => $span_wrapper_close ) ); ?>
            
            <?php echo Plethora_Theme::get_post_infolabel( array( 'type' => 'comments', 'prepend_html' => $meta_comments_wrapper_open .'<i class="fa fa-comments"></i> ', 'listing' => true, 'append_html' => $span_wrapper_close ) ); ?>

        </div>

        <div class="blog_post_listed_content_wrapper">
            <?php echo Plethora_Theme::get_post_content( array( 'listing' => true ) ); ?>
        </div>
    
        <?php echo Plethora_Theme::get_post_linkbutton( array( 'class' => 'btn btn-default' ) ); ?>

    </div>
</div>