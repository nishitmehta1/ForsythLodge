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
<div id="{{ grid_id }}" class="posts_slider_classic_horizontal_wrapper {{ el_class }}">   
    {{# items }}
        <div class="posts_list_item {{# item_filter_classes }} {{ filter_class }}{{/ item_filter_classes }}{{# item_term_classes }} {{ term_class_slug }}{{/ item_term_classes }} {{ item_colorset }} {{ item_attr_extraclass }}" >
        <div class="flex">
                <div class="col-sm-6 posts_list_item_photo" style="background-image: url('{{ item_media }}')"></div>
                <div class="col-sm-6">
                <div class="posts_list_item_content">
                    {{# item_date_month_txt }}<span class="blog_post_date">{{ item_date_month_txt }} {{ item_date_year_full }}</span>{{/ item_date_month_txt }}{{# item_author_name }}<span class="blog_post_author">by <span><a href="{{ item_author_link }}">{{ item_author_name }}</a></span></span>{{/ item_author_name }}
                    {{# item_title }}<h3 class="textify_links">{{# item_link }}<a href="{{ item_link }}" title="{{ item_title }}" target="{{ item_link_target }}" class="{{ item_link_class }}">{{/ item_link }}{{{ item_title }}}{{# item_link }}</a>{{/ item_link }}</h3>{{/ item_title }}
                    {{# item_subtitle }}<p class="subtitle">{{{ item_subtitle }}}</p>{{/ item_subtitle }} 
                    {{# item_excerpt }}<p class="excerpt">{{{ item_excerpt }}}</p>{{/ item_excerpt }}
                    {{# item_link }}<a href="{{ item_link }}" title="{{ item_title }}" target="{{ item_link_target }}" class="btn btn-link {{ item_link_class }}">{{{ item_link_label }}}</a>{{/ item_link }}
                </div>
                </div>
        </div>        
        </div>
    {{/ items }}
</div>