<?php /*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 		           (c) 2016

# LOOP SHORTCODE(S) TEMPLATE
# Check documentation and customization tips on 'loop_type_posttype_example-name.php'
*/
?>
<div id="{{ grid_id }}" class="grid_wrapper row clearfix {{ gutter }} rooms_grid_classic_vertical_wrapper {{ el_class }}"> 	
	{{# items }}
        <div class="rooms_grid_item grid_item col-xs-12 {{ item_attr_class }} {{# item_filter_classes }} {{ filter_class }}{{/ item_filter_classes }}{{# item_term_classes }} {{ term_class_slug }}{{/ item_term_classes }} {{ item_attr_extraclass }}" >
                <div class="rooms_grid_item_photo">
                        {{# item_link }}<a href="{{ item_link }}" title="{{ item_title }}" target="{{ item_link_target }}" class="{{ item_link_class }}">{{/ item_link }}
                        {{# items_media_ratio }}<div style="background-image: url('{{ item_media }}')" class="{{ items_media_ratio }} "  title="{{ item_title }}">{{/ items_media_ratio }}
                        {{^ items_media_ratio }}<img src="{{ item_media }}" alt="{{ item_title }}">{{/ items_media_ratio }}   
                        {{# items_media_ratio }}</div>{{/ items_media_ratio }}
                        {{# item_link }}</a>{{/ item_link }}
                </div>

                <div class="rooms_grid_item_content {{ item_colorset }}">
                        {{# item_title }}<h3 class="textify_links">{{# item_link }}<a href="{{ item_link }}" title="{{ item_title }}" target="{{ item_link_target }}" class="{{ item_link_class }}">{{/ item_link }}{{{ item_title }}}{{# item_link }}</a>{{/ item_link }}</h3>{{/ item_title }}
                        {{# item_subtitle }}<p class="subtitle">{{{ item_subtitle }}}</p>{{/ item_subtitle }}
                        <div class="room_listed_amenities">
                        {{# item_room_amenities }}                            
                               {{# icon_url }} <img src="{{{ icon_url }}}" data-toggle="tooltip" data-placement="top" class="{{ icon_class }}" data-original-title="{{{ desc }}}"/> {{/ icon_url }}
                               {{# icon_class }} <i data-toggle="tooltip" data-placement="top" class="{{ icon_class }}" data-original-title="{{{ desc }}}"></i> {{/ icon_class }}                            
                        {{/ item_room_amenities }}  
                        </div>                        
                        {{# item_excerpt }}<p class="excerpt">{{{ item_excerpt }}}</p>{{/ item_excerpt }}                        
                        {{# item_target_price_text }}
                        <p class="booking_price">
                                {{# item_target_price_text_before }}<span class="booking_price_before">{{{ item_target_price_text_before }}}</span>{{/ item_target_price_text_before }}
                                <span class="booking_price">{{{ item_target_price_text }}}</span>
                                {{# item_target_price_text_after }}<span class="booking_price_after">{{{ item_target_price_text_after }}}</span>{{/ item_target_price_text_after }}
                        </p>
                        {{/ item_target_price_text }}

                        {{# item_link }}<a href="{{ item_link }}" title="{{ item_title }}" target="{{ item_link_target }}" class="btn btn-link {{ item_link_class }}">{{{ item_link_label }}}</a>{{/ item_link }}
                </div>

        </div>        
        {{/ items }}
</div>