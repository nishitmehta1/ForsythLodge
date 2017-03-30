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
<div id="{{ grid_id }}" class="grid_wrapper masonry_grid row clearfix {{ gutter }}">	
        {{# items }}
        <div class="grid_item gris_hover_overlay {{ item_attr_class }} col-xs-12 {{# item_filter_classes }} {{ filter_class }}{{/ item_filter_classes }}{{# item_term_classes }} {{ term_class_slug }}{{/ item_term_classes }} {{ item_attr_extraclass }}" >
                {{# item_link }}<a href="{{ item_link }}" target="{{ item_link_target }}" class="{{ item_link_class }}">{{/ item_link }}
                        {{# items_media_ratio }}<div style="background-image: url('{{ item_media }}')" class="{{ items_media_ratio }} "  title="{{ item_title }}">{{/ items_media_ratio }}
                        {{^ items_media_ratio }}<img src="{{ item_media }}" alt="{{ item_title }}">{{/ items_media_ratio }}   
                        {{# items_media_ratio }}</div>{{/ items_media_ratio }} 
                        <div class="grid_item_overlay {{^ item_primarytax_terms }}{{^ term_colorset }}black_section{{/ term_colorset }}{{/ item_primarytax_terms }} {{# item_primarytax_terms }}{{ term_colorset }}{{/ item_primarytax_terms }} {{ items_hover_transparency }}"></div>
                        <div class="grid_item_overlay_inner {{^ item_primarytax_terms }}{{^ term_colorset }}black_section{{/ term_colorset }}{{/ item_primarytax_terms }} {{# item_primarytax_terms }}{{ term_colorset }} {{/ item_primarytax_terms }} transparent">
                        	{{# item_title }}<h2>{{{ item_title }}}</h2>{{/ item_title }}
                        	<p class="primary_taxonomy">{{# item_primarytax_terms }}<span>{{{ term_name }}}</span>{{/ item_primarytax_terms }}</p>
                        	{{# item_subtitle }}<p class="subtitle">{{{ item_subtitle }}}</p>{{/ item_subtitle }}
                        	{{# item_excerpt }}<p class="excerpt">{{{ item_excerpt }}}</p>{{/ item_excerpt }}
                        	{{# item_date_month_txt }}<p class="date">{{ item_date_month_txt }} / {{ item_date_year_full }}</p>{{/ item_date_month_txt }}
                        	{{# item_author_name }}<p class="author">{{ item_author_name }}</p>{{/ item_author_name }}
                        	<p class="secondary_taxonomy">{{# item_secondarytax_terms }}<span>{{ term_name }}</span>{{/ item_secondarytax_terms }}</p>
                        </div>
                {{# item_link }}</a>{{/ item_link }}
        </div>
        {{/ items }}
</div>