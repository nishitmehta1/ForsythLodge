<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: No post Template Part 

*/
$post_type = Plethora_Theme::get_this_view_post_type();
$blog_noposts_title       = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-noposts-title');
$blog_noposts_description = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-noposts-description');
?>
<?php Plethora_Theme::dev_comment('Start >>> No Posts', 'layout'); ?>
	<article id="post-nopost" <?php post_class('post'); ?>>
	     <div class="post_header">
	          <h2 class="post_title">
	              <?php echo wp_kses_post( $blog_noposts_title ) ; ?>
	          </h2>
	     </div>
	     <div class="post_content">
	          <p><?php echo wp_kses_post( $blog_noposts_description ) ; ?></p>
	     </div>
	</article>
<?php Plethora_Theme::dev_comment('End <<< No Posts', 'layout'); ?>