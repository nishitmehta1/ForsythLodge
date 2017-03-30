<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Page Template Parts / Title

*/
?>
	<div class="title_in_content">
	  <?php echo Plethora_Theme::get_title( array( 'tag' => 'h1', 'class' => array(), ) ); ?>
	</div>
	<div class="subtitle_in_content">  
	  <?php echo Plethora_Theme::get_subtitle( array( 'tag' => 'p', 'class' => array( 'lead' ), ) ); ?>
	</div>