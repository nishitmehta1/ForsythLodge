<?php /*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 		           (c) 2016

# TYPEWRITER HEADING SHORTCODE TEMPLATE
# DESCRIPTION: 	This is the frontend template for the 'Typewriter Heading' shortcode. 
# FORMAT: 		Mustache template

# DEV TIPS

	1. 	You can customize this template ( and all similar ones ) by copying it to your child theme,
		under '/templates/shortcodes' directory.

	2.	General Mustache tips:
		
			i.   Double bracketed values cannot not include HTML, while triple bracketed values may include HTML. 
			ii.  Loop values start with {{# loopval }} and end with {{/ loopval }}
			iii. Empty value conditionals can be handled in a similar way as loop values ( check previous tip )
			iv.  All of the following values included, so you don't have to be afraid for missing ones
			v.   All values are properly escaped according to WordPress.org's development guidelines

	3. 	You may use, any of the values below to create your own markup. 
		
		Available mustache values:
		=======================================================================

		{{{ pre }}}					: Title // Pre-Typewriter Text ( no HTML please )
		parts						: Title // Typewriter Text Parts ( no HTML please ) ( loop, us as seen below )
			{{# parts }}
				{{{ text }}}		: Typewritten text
				{{ class }}			: Typewritten text wrapper tag class
	  		{{/ parts }}
		{{ tag }}					: Heading Tag
		{{{ subtitle }}}			: Subtitle text
		{{ subtitle_position }}		: Subtitle Position
		{{ align }}					: Text Align
		{{ el_class }}				: Extra Class
		{{ css }}					: Design Options CSS configuration class

		=======================================================================
*/
?>
 <div class="text-block heading_group_sc wpb_content_element {{ subtitle_position }} {{ align }} {{ el_class }} {{ css }} ">
    {{# subtitle_top }}
        <span class="subtitle">{{{ subtitle }}}</span>
    {{/ subtitle_top }}


	<{{ tag }} class="cd-headline letters type">
		{{# pre }} <span>{{{ pre }}}</span> {{/ pre }}
		<span class="cd-words-wrapper">
		{{# parts }}
			<b{{# class }} class="{{ class }}"{{/ class }}>{{{ text }}}</b>
		{{/ parts }}
		</span>
	</{{ tag }}>

    {{^ subtitle_top }}
        <span class="subtitle">{{{ subtitle }}}</span>
    {{/ subtitle_top }}

    {{# divider }}
        <div class="svg_divider">{{{ divider }}}</div>
    {{/ divider }}    
</div>