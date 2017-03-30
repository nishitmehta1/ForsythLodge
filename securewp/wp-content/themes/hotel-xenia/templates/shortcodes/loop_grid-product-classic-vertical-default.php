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
<div id="{{ grid_id }}" class="grid_wrapper product_grid_classic_vertical row clearfix {{ gutter }}">    
    {{# items }}
        <div class="product_listed grid_item {{ item_attr_class }} col-xs-12 {{# item_filter_classes }} {{ filter_class }}{{/ item_filter_classes }}{{# item_term_classes }} {{ term_class_slug }}{{/ item_term_classes }} {{ item_attr_extraclass }}">
            <div class="product_listed_wrapper teaser_box wpb_content_element text-center">
                <div class="figure with_image">
                    {{# item_link }}
                    <a href="{{ item_link }}" title="{{ item_title }}" target="{{ item_link_target }}" class="{{ item_link_class }}">
                    {{/ item_link }}
                    {{# items_media_ratio }}
                    <div style="background-image: url('{{ item_media }}')" class="{{ items_media_ratio }} "  title="{{ item_title }}">
                    {{/ items_media_ratio }}
                    {{^ items_media_ratio }}
                    <img src="{{ item_media }}" alt="{{ item_title }}">
                    {{/ items_media_ratio }}   
                    {{# items_media_ratio }}
                    </div>
                    {{/ items_media_ratio }} 
                    {{# item_link }}
                    </a>
                    {{/ item_link }}
                    {{# item_woo_saleicon_class }}
                    <p class="sale_wrapper secondary_section {{ item_woo_saleicon_class }}">{{{ item_woo_saleicon_text }}}</p>
                    {{/ item_woo_saleicon_class }} 
                </div>
                <div class="content">
                    <div class="hgroup textify_links">
                        {{# item_title }}
                        <h4>
                        {{# item_link }}
                        <a href="{{ item_link }}" title="{{ item_title }}" target="{{ item_link_target }}" class="{{ item_link_class }}">
                        {{/ item_link }}
                        {{{ item_title }}}
                        {{# item_link }}
                        </a>
                        {{/ item_link }}
                        </h4>
                        {{/ item_title }}                      
                    </div>
                    {{# item_woo_price }}
                    <p class="price">{{{ item_woo_price_currency }}}{{{ item_woo_price }}}</p>
                    {{/ item_woo_price }} 
                    {{# item_excerpt }}
                    <p class="desc">{{{ item_excerpt }}}</p>
                    {{/ item_excerpt }}                     
                    {{# item_link }}
                    <a href="{{ item_link }}" target="{{ item_link_target }}" class="btn btn-default {{ item_link_class }}">{{{ item_link_label }}}</a>
                    {{/ item_link }}
                    {{# item_woo_addtocart_url }}
                    <a href="{{ item_woo_addtocart_url }}" class="btn btn-primary">{{{ item_woo_addtocart_text }}}</a>
                    {{/ item_woo_addtocart_url }}                        
                </div>
            </div>   
        </div>      
    {{/ items }}
</div>