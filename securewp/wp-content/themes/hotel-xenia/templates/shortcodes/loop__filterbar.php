<?php /*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 		           (c) 2016

# POSTS GRID FILTER BAR TEMPLATE
# DESCRIPTION: 	The filter bar part of Posts Grid shortcode. 
			 	For item templates, check for files named like 'shortcode-postsgrid-{grid|masonry|list}.php' 
			 	For paging, check shortcode-postsgrid_paging.php

# CUSTOMIZATION / CREATION TIPS

	1. 	You may copy this file on your child theme directory ( under '/templates/shortcodes' folder )
		and customize the markup as you like.

	2. 	You may use, any of the values below to create your own markup. 
		
		General Mustache tips:
		
			i.   Double bracketed values cannot not include HTML, while triple bracketed values may include HTML. 
			ii.  Loop values start with {{# loopval }} and end with {{/ loopval }}
			iii. Empty value conditionals can be handled in a similar way as loop values ( check previous tip )
			iv.  All of the following values included, so you don't have to be afraid for missing ones

		Available mustache values:
		=======================================================================

		{{ filterbar_id }}:		Filter Bar ID ( should be used on filter bar element wrapper 'id' attribute. YOU SHOULD ALWAYS USE IT! )
		{{ filters_tax }}:		Filter terms taxonomy slug
		{{ resettitle }}:		Reset button title ( if empty, button should not be displayed )

		{{# filters }}			Filter terms loop starts

			{{ id }}:			Term ID
			{{ slug }}:			Term slug
			{{ name }}:			Term name	
			{{ taxonomy }}:		Term taxonomy ( i.e categories )
			{{ description }}:	Term description 
			{{ count }}:		Term count ( how many posts in total...not in items displayed ) 
			{{ filter_class }}:	Term filter class for script usage 

		{{/ filters }}			Items loop ends

	=======================================================================
*/
?>
<div id="{{ filterbar_id }}" class="filter_button_group">
	{{# resettitle }} <a class="filter_button" data-filter="*">{{ resettitle }}</a> {{/ resettitle }} 
	{{# filters }}
	<a class="filter_button" data-filter=".{{ filter_class }}">{{ name }}</a>
	{{/ filters }} 
</div>