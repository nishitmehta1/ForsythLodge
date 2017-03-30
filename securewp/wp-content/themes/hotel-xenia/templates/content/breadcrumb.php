<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015
Breadcrumb template part
*/
// Get attributes sent by module class
$options = get_query_var( 'options' );
extract( $options );

/* If you need to add your custom markup here, uncomment the following 
var_dump to review all the options available for this template part */
 
// var_dump( $options );

echo Plethora_Theme::get_breadcrumb();