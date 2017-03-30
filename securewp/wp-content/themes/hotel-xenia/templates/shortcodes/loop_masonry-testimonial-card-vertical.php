<?php /*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 		      (c) 2016

# LOOP SHORTCODE(S) TEMPLATE
# Check documentation and customization tips on 'loop_type_posttype_example-name.php'
*/
?>
<div id="{{ grid_id }}" class="grid_wrapper masonry_grid clearfix testimonials_grid_card_vertical_wrapper {{ el_class }}">   
    {{# items }}
    <div class="testimonials_list_item grid_item {{ item_attr_class }} col-xs-12 {{# item_filter_classes }} {{ filter_class }}{{/ item_filter_classes }}{{# item_term_classes }} {{ term_class_slug }}{{/ item_term_classes }} {{ item_attr_extraclass }}">
	    <div class="testimonial_item_wrapper {{ item_colorset }}">
		        {{# item_media }}
		        <div class="testimonials_list_photo_wrapper">        
		        	<div class="testimonials_list_photo stretchy_wrapper ratio_1-1" style="background-image: url('{{ item_media }}')"></div>
		        </div>
		        {{/ item_media }}
		        <div class="testimonials_list_text_wrapper">
			        <div class="testimonials_list_text">
			            {{# item_title }}<h5>{{{ item_title }}}</h5>{{/ item_title }}
			            <div class="testimonials_list_text_editor">{{{ item_editorcontent }}}</div>
			            {{# item_testimonial_author }}<span class="testimonial_author">{{{ item_testimonial_author }}}</span>{{/ item_testimonial_author }}
			            {{# item_testimonial_author_role }}<span class="testimonial_author_role">{{{ item_testimonial_author_role }}}</span>{{/ item_testimonial_author_role }}
			        </div>
		        </div>
	    </div>
    </div>
    {{/ items }}
</div>                