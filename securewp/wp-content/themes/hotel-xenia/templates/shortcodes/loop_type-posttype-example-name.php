<?php /*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 		           (c) 2016

# SAMPLE TEMPLATE FOR LOOP SHORTCODES OUTPUT
# DESCRIPTION: 	The main items display part of Posts Grid OR Posts Slider shortcode. 
			 	To customize filter bar section output, check loop__filterbar.php
			 	To customize paging section output, check loop__paging.php

# CUSTOMIZATION / CREATION TIPS

	1. 	Create your own Loop shortcode(s) template, using the following naming pattern:

		'loop_{grid|masonry|list|slider}-{anypost|post_type}-{your-custom-template-reference-name}.php'
		
		Template files will be included automatically on the shortcode panel.
		So, let's say that you create a 'loop_masonry-post-my-custom-masonry-grid.php' 
		template, it will be included on the 'Posts Loop' shortcode, under the available 
		'Masonry Template' dropdown field as 'My Custom Masonry Grid', and you have to 
		choose it for display. 

		If you name it 'loop_masonry-anypost-my-custom-masonry-grid.php',the template will be 
		available on ALL loop shortcodes ( 'Posts Loop', 'Products Loop', etc.). 

		All related templates must be placed on your child theme, under '/templates/shortcodes' 
		directory.

	2. 	You may use, any of the values below to create your own markup. 
		
		General Mustache tips:
		
			i.   Double bracketed values escape HTML, while triple bracketed values may include HTML. 
			ii.  Attribute values MUST contain double bracketed values ONLY  
			iii. Loop values start with {{# loopval }} and end with {{/ loopval }}
			iv.  Empty value conditionals can be handled in a similar way as loop values ( check previous tip )
			v.   All of the following values included, so you don't have to be afraid for missing ones

		Available mustache values:
		=======================================================================
			# GENERAL VALUES
			{{ grid_id }}:							Grid ID ( should be used on grid element wrapper 'id' attribute. YOU SHOULD ALWAYS USE IT! )
			{{ gutter }}:							Gutter behavior class ( NOTICE: n/a for slider shortcodes )

			# ITEMS LOOP ( loop, us as seen below )
			{{# items }}							Items loop starts

				# BASIC VALUES
				{{ item_count }} 					Display order number
				{{ item_id }} 						Post WP ID
				{{ item_attr_class }} 				Item classes ( as defined in Plethora_Shortcode_Anypostloop class )	
				{{ item_attr_extraclass }} 			User given classes
				{{ item_colorset }} 				Color set class
				{{ item_post_type }} 				Post type
				{{ item_link }} 					Link to single post view
				{{ item_link_target }}				Link target attribute value
				{{ item_link_class }}				Link class attribute value
				{{{ item_link_label }}}				Link fixed label ( i.e. 'Read More' for buttons )
				{{ item_media }} 					Featured image link
				{{ items_media_ratio }}				Featured image ratio (or none)
				{{ items_hover_transparency }}      Transparency level on hover ( if utilized by the template )
				{{{ item_title }}} 					Post title
				{{{ item_subtitle }}} 				Post subtitle
				{{{ item_editorcontent }}}			Post editor content
				{{{ item_excerpt }}} 				Post excerpt
				{{ item_date_day_num }} 			Post creation date / day in number ( i.e. '06' )
				{{ item_date_day_txt }} 			Post creation date / day in text ( i.e. 'Fri' )
				{{ item_date_month_num }} 			Post creation date / month in number ( i.e. '09' )
				{{ item_date_month_txt }} 			Post creation date / month in text ( i.e. 'Sep' )
				{{ item_date_year_abr }} 			Post creation date / year abbreviation ( i.e. '16' )
				{{ item_date_year_full }} 			Post creation date / year full ( i.e. '2016' )
				{{{ item_author_name }}} 			Author name
				{{ item_author_link }}	 			Author link
				{{ item_comments_number }}	 		Comments count number
				{{ item_comments_link }} 			Comments link on single post view

				# ALL TAXONOMY TERMS CLASSES ( loop all taxonomy terms as class attribute values, mostly used for terms styling reference )		
				{{# item_term_classes }}
					{{ term_class_slug }}			Tax term class: slug ( can be used a reference class )
					{{ term_class_colorset }}		Tax term class: color set ( can be used a reference class, if supported for selected taxonomy )
		  		{{/ item_term_classes }}

				# FILTER BAR TAXONOMY TERMS CLASSES ( NOTICE: n/a for slider shortcodes ) ( loop filterbar taxonomy slugs as class attribute values )
				{{# item_filter_classes }}
					{{ filter_class }}				Selected filter bar taxonomy term slug, prefixed with: 'filter_'
		  		{{/ item_filter_classes }}
				
				# PRIMARY TAXONOMY TERMS ( loop, us as seen below )
				{{# item_primarytax_terms }}
					{{ term_id }}					Primary tax term: term ID
					{{ term_slug }}					Primary tax term: term slug
					{{ term_link }}					Primary tax term: term link
					{{{ term_name }}}				Primary tax term: term name
					{{ term_colorset }}				Primary tax term: color set ( if supported for selected taxonomy )
		  		{{/ item_primarytax_terms }}
				
				# SECONDARY TAXONOMY TERMS ( loop, us as seen below )
				{{# item_secondarytax_terms }}
					{{ term_id }}					Secondary tax term: term ID
					{{ term_slug }}					Secondary tax term: term slug
					{{ term_link }}					Secondary tax term: term link
					{{{ term_name }}}				Secondary tax term: term name
					{{ term_colorset }}				Secondary tax term: color set ( if supported for selected taxonomy )
		  		{{/ item_secondarytax_terms }}

				# SPECIAL VALUES: WOO PRODUCTS
				{{ item_woo_price }} 				Product price ( only for WooCommerce Products )
				{{{ item_woo_price_currency }}} 	Product Price currency ( only for WooCommerce Products )
				{{ item_woo_addtocart_url }} 		Product 'Add To Cart' link ( only for WooCommerce Products )
				{{{ item_woo_addtocart_text }}}		Product 'Add To Cart' text ( only for WooCommerce Products ) 
				{{ item_woo_saleicon_class }} 		Product 'Sale' icon style class ( only for WooCommerce Products )
				{{{ item_woo_saleicon_text }}}		Product 'Sale' icon text ( only for WooCommerce Products )

				# SPECIAL VALUES: SOCIAL ICONS ( if supported in selected post type )
				{{# item_socials }}
					{{{ social_title }}}			Social title
					{{ social_icon }}				Social icon class
					{{ social_url }}				Social link
					{{ social_url_target }}			Social link target
		  		{{/ item_socials }}

				# SPECIAL VALUES: TESTIMONIALS ( supported only for testimonial loops )
				{{{ item_testimonial_author }}}:		Testimonial author name
				{{{ item_testimonial_author_role }}}: 	Testimonial author role

				# SPECIAL VALUES: BOOKING TARGET PRICE ( if supported in selected post type )
				{{{ item_target_price_text }}}:			Booking Target Price text
				{{{ item_target_price_text_before }}}: 	Booking Target Price Before text
				{{{ item_target_price_text_after }}}: 	Booking Target Price After text

				# SPECIAL VALUES: ROOM AMENITIES ( if supported in selected post type )
				{{# item_room_amenities }}
					{{ icon_class }}:		Booking Target Price text
					{{{ desc }}}:			Booking Target Price text
					{{{ icon_url }}}:		Booking Target Price text
		  		{{/ item_room_amenities }}

			
			{{/ items }}							Items loop ends

	=======================================================================
*/
?>
<div class="postgrid_grid_wrapper">
	<div class="grid_wrapper row clearfix">
		
		{{# items }}
        <div class="grid_item gris_hover_overlay {{ item_attr_class }} col-xs-12 {{# item_filter_classes }}{{ filter_class }} {{/ item_filter_classes }} {{ item_attr_extraclass }}" >
        	<a href="{{ item_link }}" class="{{ items_media_ratio }} " style="background-image: url('{{ item_media }}')">
        		<img src="{{ item_media }}" alt="{{ item_title }}">
                <div class="grid_item_overlay {{^ item_primarytax_terms }}{{^ term_colorset }}black_section{{/ term_colorset }}{{/ item_primarytax_terms }} {{# item_primarytax_terms }}{{ term_colorset }}{{/ item_primarytax_terms }} {{ items_hover_transparency }}"></div>
                <div class="grid_item_overlay_inner {{^ item_primarytax_terms }}{{^ term_colorset }}black_section{{/ term_colorset }}{{/ item_primarytax_terms }} {{# item_primarytax_terms }}{{ term_colorset }} {{/ item_primarytax_terms }} transparent">
                	{{# item_title }}<h2>{{ item_title }}</h2>{{/ item_title }}
                	<p class="primary_taxonomy">{{# item_primarytax_terms }}<span>{{{ term_name }}}</span>{{/ item_primarytax_terms }}</p>
                	{{# item_subtitle }}<p class="subtitle">{{ item_subtitle }}</p>{{/ item_subtitle }}
                	{{# item_excerpt }}<p class="excerpt">{{ item_excerpt }}</p>{{/ item_excerpt }}
                	{{# items_date }}<p class="date">{{ item_date_month_txt }} / {{ item_date_year_full }}</p>{{/ items_date }}
                	{{# item_author_name }}<p class="author">{{ item_author_name }}</p>{{/ item_author_name }}
                	<p class="secondary_taxonomy">{{# item_secondarytax_terms }}<span>{{ term_name }}</span>{{/ item_secondarytax_terms }}</p>
                </div>
        	</a>
        </div>
        {{/ items }}
	</div>
</div>