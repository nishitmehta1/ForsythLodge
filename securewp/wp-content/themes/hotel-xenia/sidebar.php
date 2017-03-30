<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2016

Description: Handles left/right sidebars. Is included as part of index.php
Related containers: 'content_main_left', 
					'content_main_right', 
*/
Plethora_Theme::dev_comment(' START >> ========================= SIDEBAR ========================', 'layout');
$layout   = Plethora_Theme::get_layout();
$container = $layout === 'left_sidebar' ? 'content_main_left' : 'content_main_right';
/**
* Sidebar wrapper open
*/
?>
<div<?php echo Plethora_Theme::get_container_attrs( $container ); ?>>
<?php

	// Get and display the user selected sidebar 
	$sidebar = Plethora_Theme::get_main_sidebar();
	dynamic_sidebar( $sidebar );
/**
* Sidebar wrapper close
*/
?>
</div>
<?php
Plethora_Theme::dev_comment('  << FINISH ==================== SIDEBAR ====================', 'layout');