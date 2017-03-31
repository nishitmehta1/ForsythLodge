<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2017

File Description: Child Theme Functions file 

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

//---------------------------------------------------------------//
//						DO YOUR STUFF HERE!!
//---------------------------------------------------------------//

wp_enqueue_style( 'owl.carousel.main', get_template_directory_uri() . '/assets/css/owl.carousel.min.css',false,'2.2','all');
wp_enqueue_style( 'owl.theme.main', get_template_directory_uri() . '/assets/css/owl.theme.default.min.css',false,'2.2','all');

wp_enqueue_script( 'main', get_template_directory_uri() . '/assets/js/main.js', array ( 'jquery' ), 1.1, true);
wp_enqueue_script( 'map', get_template_directory_uri() . '/assets/js/map.js', array ( 'jquery' ), 1.1, true);
wp_enqueue_script( 'owl.carousel.2.22.1', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', array ( 'jquery' ), 2.2, true);

