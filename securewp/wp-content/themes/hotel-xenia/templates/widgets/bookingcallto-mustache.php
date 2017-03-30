<?php /*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 		           (c) 2016

# CALL TO BOOKING WIDGET TEMPLATE
# DESCRIPTION: 	This is the frontend template for the 'Call To Booking' shortcode. 
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
			vi.  Note for non latin chars display: prefer always triple brackets

	3. 	You may use, any of the values below to create your own markup. 
		
		Available Mustache values:
		=======================================================================

		{{{ target_price_text }}}			: Booking Target Price text
  		{{{ target_price_text_before }}}	: Booking Target Price prepended text
  		{{{ target_price_text_after }}}		: Booking Target Price appended text
		{{{ form_action_url }}}	 			: Form action
  		{{{ form_target }}}					: Form target attribute
		{{{ form_method }}}	 				: Form method ( 'get' or 'post' )
  		{{{ form_bgimage_url }}}			: Background image url
  		{{{ submit_title }}}				: Submit button title
  		{{{ submit_colsize }}}				: Submit column size class
  		{{{ submit_style }}}				: Submit style class
  		{{{ submit_size }}}					: Submit size class
  		{{{ submit_colorset }}}				: Submit color set class
  		{{{ submit_class }}}				: Submit extra class
		{{# fields }}
			{{ field_is_select }}			: If true, this is a select field
			{{ field_is_input }}			: If true, this is an input field
			{{ field_input_type }}			: Type of the input field
			{{ field_not_hidden }}			: If true, this is not a hidden input field
			{{{ field_colsize }}}			: Field column size class
			{{{ field_label }}}			 	: Field label
			{{{ field_name }}}			 	: Field name
			{{{ field_value }}}				: Field value ( empty if multi option field )
			{{# field_options }}			: Field options ( empty if non multi option field )
				{{{ opt_val }}}					: Option value
				{{{ opt_title }}}				: Option title
				{{{ opt_selected }}}			: Selected attribute
  			{{/ field_options }}
			{{{ field_id }}}				: Field id attr
			{{{ field_placeholder }}}		: Field placeholder attr
			{{{ field_multiple }}}			: Allow multiple selections attr ( only for select fields )
  		{{/ fields }}
		{{ id }}							: Unique ID for counter element
		{{ el_class }}						: Extra Class
		{{ css }}							: Design Options CSS configuration class

		{{{ id_base }}}						: Default WP widget slug
		{{{ before_widget }}}				: Default WP 'before_widget' markup
		{{{ after_widget }}}				: Default WP 'after_widget' markup


        =======================================================================
*/
?>

{{{ before_widget }}}

<div{{# form_bgimage_url }} style="background-image: url('{{{ form_bgimage_url }}}')"{{/ form_bgimage_url }} class="pl_room_booking_widget boxed_plus black_section dark_form large_form {{ el_class }}">
	{{# target_price_text }}
		{{# target_price_text_before }}
			<p class="price_before_text">{{{ target_price_text_before }}}</p>
		{{/ target_price_text_before }}
		<p class="the_price_wrapper">
		<span>{{{ target_price_text }}}</span>
		{{# target_price_text_after }}<small>{{{ target_price_text_after }}}</small>{{/ target_price_text_after }}
		</p>
	{{/ target_price_text }}
<form action="{{{ form_action_url }}}" method="{{{ form_method }}}" target="{{{ form_target }}}">

	{{# fields }}
	
		{{# field_is_input }}
		{{# field_not_hidden }}
		<div class="{{{ field_id }}}">
			<div class="form-group">
		  		<label>{{{ field_label }}}</label>
		{{/ field_not_hidden }}
		  		<input type="{{{ field_input_type }}}" name="{{{ field_name }}}" value="{{{ field_value }}}" id="{{{ field_id }}}" placeholder="{{{ field_placeholder }}}">
		{{# field_not_hidden }}
			</div>
		</div>
		{{/ field_not_hidden }}
		{{/ field_is_input }}

		{{# field_is_select }}
		<div class="{{{ field_id }}}">
			<div class="form-group">
				<label>{{{ field_label }}}</label>
				<select name="{{{ field_name }}}" id="{{{ field_id }}}"{{{ field_multiple }}}>
					{{# field_options }}
					<option value="{{{ opt_val }}}"{{{ opt_selected }}}>{{{ opt_title }}}</option>
					{{/ field_options }}
				</select>
			</div>
		</div>
		{{/ field_is_select }}

	{{/ fields }}

		<div class="{{{ field_id }}}">
			<button type="submit" class="{{{ submit_style }}} {{{ submit_size }}} {{{ submit_colorset }}} {{{ submit_class }}}">{{{ submit_title }}}</button>
		</div>
	</form>
</div>

{{{ after_widget }}}