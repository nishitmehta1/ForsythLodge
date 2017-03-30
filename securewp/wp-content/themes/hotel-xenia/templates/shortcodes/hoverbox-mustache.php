<!-- ========================== HOVER BOX ==========================-->

<div style="background-image: url('{{ bcg_url }}')" class="hoverbox wpb_content_element black_section neutralize_links {{ stretchy_ratio }} {{ el_class }} {{ css }}">
<div class="hoverbox_tran black_section transparent_film"></div>
	{{# link_url }}<a href="{{ link_url }}"{{# link_target }} target="{{ link_target }}"{{/ link_target }}{{# link_rel }} rel="{{ link_rel }}"{{/ link_rel }}>{{/ link_url }}
		{{# logo_url }}
		<div class="hoverbox_icon">
			<img src="{{ logo_url }}" alt="{{{ image_alt }}}">
		</div>
		{{/ logo_url }}
		{{# title }}
		<div class="hoverbox_title">
			<h2>{{{ title }}}</h2>
		</div>
		{{/ title }}
		{{# content }}
		<div class="hoverbox_paragraph">
			<p>{{{ content }}}</p>
		</div>
		{{/ content }}
	{{# link_url }}</a>{{/ link_url }}	
</div>

<!-- END======================= HOVER BOX ==========================-->