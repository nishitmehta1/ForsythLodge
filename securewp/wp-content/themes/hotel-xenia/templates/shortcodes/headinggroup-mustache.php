<div class="heading_group_sc wpb_content_element {{ subtitle_position }} {{ extra_class }} {{ css }} {{ type }} {{ align }}">
    {{# background_title }}
        <div class="background_title">{{{ background_title }}}</div>
    {{/ background_title }} 

    {{# subtitle_top }}
        <span class="subtitle">{{{ subtitle }}}</span>
    {{/ subtitle_top }}

    {{{ title }}}

    {{^ subtitle_top }}
        <span class="subtitle">{{{ subtitle }}}</span>
    {{/ subtitle_top }}

    {{# divider }}
        <div class="svg_divider">{{{ divider }}}</div>
    {{/ divider }}    
</div>