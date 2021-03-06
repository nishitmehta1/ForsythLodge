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
<div id="{{ grid_id }}" class="grid_wrapper list_grid row clearfix">    
        {{# items }}
        <div class="blog_post_listed grid_item {{ item_attr_class }} col-xs-12 {{# item_filter_classes }} {{ filter_class }}{{/ item_filter_classes }}{{# item_term_classes }} {{ term_class_slug }}{{/ item_term_classes }}">
        <div class="blog_post_listed_wrapper {{ item_attr_extraclass }}">
                <div class="row">
                <div class="col-sm-6 col-lg-5">
                <div class="blog_post_listed_media">
                        {{# item_media }}
                        {{# item_link }}<a href="{{ item_link }}" target="{{ item_link_target }}" class="{{ item_link_class }}">{{/ item_link }}
                        {{# items_media_ratio }}<figure style="background-image: url('{{ item_media }}')" class="{{ items_media_ratio }} "  title="{{ item_title }}">{{/ items_media_ratio }}
                        {{^ items_media_ratio }}<figure><img src="{{ item_media }}" alt="{{ item_title }}"></figure>{{/ items_media_ratio }}   
                        {{# items_media_ratio }}</figure>{{/ items_media_ratio }}
                        {{# item_link }}</a>{{/ item_link }}
                        {{/ item_media }}
                </div>
                </div>
                <div class="col-sm-6 col-lg-7 boxed">
                <div class="blog_post_listed_texts">
                        {{# item_date_month_txt }}<span class="blog_post_date">{{ item_date_month_txt }} {{ item_date_year_full }}</span>{{/ item_date_month_txt }}
                        {{# item_title }}<h4 class="textify_links">{{# item_link }}<a href="{{ item_link }}" target="{{ item_link_target }}" class="{{ item_link_class }}">{{/ item_link }}{{{ item_title }}}{{# item_link }}</a>{{/ item_link }}</h4>{{/ item_title }}
                        {{# item_author_name }}<span class="blog_post_author">by <span><a href="{{ item_author_link }}">{{ item_author_name }}</a></span></span>{{/ item_author_name }}
                        {{# item_excerpt }}<div class="blog_post_listed_content_wrapper">{{{ item_excerpt }}}</div>{{/ item_excerpt }}
                        {{# item_link }}<a href="{{ item_link }}" target="{{ item_link_target }}" class="btn btn-default {{ item_link_class }}">{{{ item_link_label }}}</a>{{/ item_link }} 
                </div>
                </div>
                </div>            
        </div> 
        </div>       
        {{/ items }}
</div>