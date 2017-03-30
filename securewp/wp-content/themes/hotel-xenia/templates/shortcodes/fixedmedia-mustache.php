<div class="pl_fixed_ratio_media wpb_content_element">
    {{# image_link }}
    <div class="{{{ stretchy_ratio }}} {{{ transparent_overlay }}} {{{ color_set }}} {{{ bgimage_valign }}}" style="background-image: url('{{{ image_link }}}');">
      <img alt="{{{ image_alt }}}" src="{{{ image_link }}}">
    </div>
    {{/ image_link }}

    {{# video_frame }}
    <div class="video_iframe {{{ stretchy_ratio }}} {{{ transparent_overlay }}} {{{ color_set }}}">
      {{{ video_frame }}}
    </div>
    {{/ video_frame }}
</div>