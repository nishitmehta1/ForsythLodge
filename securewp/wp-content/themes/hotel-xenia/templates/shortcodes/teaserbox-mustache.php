<!-- ========================== TEASER BOX ==========================-->

<div class="teaser_box wpb_content_element {{ orientation_style }} {{ text_align }} {{ box_colorset }} {{ boxed_styling }} {{ el_class }} {{ css }} {{# media_type_image }} with_image {{/ media_type_image }}">

  {{# media_type_image }}
  <div class="figure with_image {{ media_colorset }} {{ media_style }}">
  {{/ media_type_image }}

  {{# media_type_icon }}
  <div class="figure with_icon textify_links {{ media_colorset }} {{ media_style }}">
  {{/ media_type_icon }}

    {{# teaser_link_url }}
      <a href="{{ teaser_link_url }}" title="{{ teaser_link_title }}" target="{{ teaser_link_target}}"> 
    {{/ teaser_link_url }}

      {{# media_type_image }}
        
        {{# media_ratio }} <div class="{{ media_ratio }}" style="background-image:url('{{ image }}')"></div> {{/ media_ratio }}
        
        {{# no_media_ratio }} <img src="{{ image }}" alt="{{ title }}"> {{/ no_media_ratio }}  

      {{/ media_type_image }}
        
      {{# media_type_icon }} 

        {{# media_ratio }}<div class="{{ media_ratio }}"><i class="{{ icon }}"></i></div>{{/ media_ratio }}

        {{# no_media_ratio }}<i class="{{ icon }}"></i>{{/ no_media_ratio }}

      {{/ media_type_icon }}

    {{# teaser_link_url }}
    </a>
    {{/ teaser_link_url }}

  {{# media_type_image }}  
  </div>
  {{/ media_type_image }}  

  {{# media_type_icon }}  
  </div>
  {{/ media_type_icon }} 

  <div class="content {{ text_boxed_styling }}">
    
    <div class="hgroup textify_links">{{# title }}<h4>{{# teaser_link_url }}<a href="#">{{/ teaser_link_url }}{{{ title }}}{{# teaser_link_url }}</a>{{/ teaser_link_url }}</h4>{{/ title }}{{# subtitle }}<p>{{{ subtitle }}}</p>{{/ subtitle }}</div>

    {{# content }} <div class="desc"><p>{{{ content }}}</p></div> {{/ content }}

  </div>

</div>

<!-- END======================= TEASER BOX ==========================-->
