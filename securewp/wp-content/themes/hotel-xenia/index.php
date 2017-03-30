<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2016

Description: Handles all pages content displays. Includes Header & Footer parts

Containers: none

Header template part handlers 
for Plethora_Theme::add_container_part() )

	'content_main_loop_options'
	'content_main_loop'

*/
get_header();

					// Main Loop
				if ( ! is_404() ) { 
					if ( have_posts() ) { 

						while ( have_posts() ) : the_post(); 

							/**
							* 'plethora_content_main_loop_options' hook
							* used for option preparation
							*/
							do_action( 'plethora_content_main_loop_options' ); 
							/**
							* 'plethora_content_main_loop' hook
							* used for template parts
							*/
							do_action( 'plethora_content_main_loop' ); 

						endwhile;
						 
					} else {

						get_template_part('templates/global/noposts' );
					}

				} else {

					/**
					* 'plethora_content_main_loop' hook
					*/
					do_action( 'plethora_content_main_loop' ); 
				}
get_footer();