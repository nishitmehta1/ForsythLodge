<?php /*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 		           (c) 2016

# COUNTER SHORTCODE TEMPLATE
# DESCRIPTION: 	This is the frontend template for the 'Counter' shortcode. 
# FORMAT: 		Mustache template

# DEV TIPS

	1. 	You can customize this template by copying it to your child theme,
		under '/templates/shortcodes' directory ( COMMON FOR ALL SHORTCODES ).

	2.	General Mustache tips ( COMMON FOR ALL MUSTACHE TEMPLATES ):
		
			i.   Double bracketed values cannot not include HTML, while triple bracketed values may include HTML. 
			ii.  Loop values start with {{# loopval }} and end with {{/ loopval }}
			iii. Empty value conditionals can be handled in a similar way as loop values ( check previous tip )
			iv.  All of the following values included, so you don't have to be afraid for missing ones
			v.   All values are properly escaped according to WordPress.org's development guidelines

	3. 	You may use, any of the values below to create your own markup. 
		
		Available Mustache values:
		=======================================================================

		{{ title }}						: Title ( no HTML please )
		{{{ content }}}					: Subtitle ( HTML allowed )
		{{ counter_value }}				: Counter Value ( only number value )
		{{ counter_time }}				: Counter Time ( in milliseconds )
		{{ counter_delay }}				: Counter Delay ( in milliseconds )
		{{ animation_class }}			: Animation type class value
		{{ animation_data_attr }}		: Animation type data attribute value ( for 'data-os-animation' )
		{{ animation_delay_data_attr }}	: Animation delay data attribute value ( for 'data-os-animation-delay' )
		{{ icon }}						: Icon class ( suggested use in <i class="{{ icon }}"></i> format )
		{{ id }}						: Unique ID for counter element
		{{ el_class }}					: Extra Class
		{{ css }}						: Design Options CSS configuration class

		=======================================================================
*/
?>
<div class="counter_sc_wrapper wpb_content_element {{ el_class }}{{ css }}">
	{{# icon }}<i class="{{ icon }}"></i>{{/ icon }}
	{{# counter_value }}<span id="{{ id }}" class="counter">{{ counter_value }}</span>{{/ counter_value }}
	{{# title }}<h4>{{{ title }}}</h4>{{/ title }}
	{{# content }}<p>{{{ content }}}</p>{{/ content }}
</div>