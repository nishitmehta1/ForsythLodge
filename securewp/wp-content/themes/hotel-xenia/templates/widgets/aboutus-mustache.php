<!-- ========================== WIDGET ABOUT US ==========================-->

{{{ before_widget }}}

	{{# title }}
		{{{ before_title }}} {{{ title }}} {{{ after_title }}}
	{{/ title }}

<div class="pl_about_us_widget {{ orientation }} {{ extra_class }}">

	{{# logo }}
		<p><img src='{{ logo }}' alt='logo'{{{ #logo_max_width }}}  style="max-width:{{ logo_max_width }}"{{ /logo_max_width }}></p>
	{{/ logo }}	

	{{# description }}
		<p>{{{ description }}}</p>
	{{/ description }}

	{{# telephone }}

		<p class='contact_detail'><i class='fa fa-phone'></i><span>{{{ telephone }}}</span></p>

	{{/ telephone }}

	{{# email }}

		<p class='contact_detail'><i class='fa fa-envelope'></i><span><a href='mailto:{{{ email }}}'>{{{ email }}}</a></span></p>

	{{/ email }}

	{{# url }}

		<p class='contact_detail'><i class='fa fa-link'></i><span><a target='_blank' href='{{{ url }}}'>{{{ url }}}</a></span></p>

	{{/ url }}

	{{# address }}
	
	<p class="contact_detail">
		{{# googleMapURL }}
		<a href='https://www.google.com/maps/place/{{ googleMapURL }}' target='_blank'>
		{{/ googleMapURL }}
			<i class='fa fa-location-arrow'></i>
		{{# googleMapURL }}
		</a>
		{{/ googleMapURL }}
		<span>{{ address }}</span>
	</p>

	{{/ address }}

	{{# socials }}
	<p class="social">
	{{/ socials }}

		{{# social_items }}
			<a href='{{{ social_url }}}' target='_blank' title="{{{ social_title }}}"><i class='{{{ social_icon }}}'></i></a>
		{{/ social_items }}
	{{# socials }}
	</p>
	{{/ socials }}

	
	
</div>

{{{ after_widget }}}

<!-- END======================= WIDGET ABOUT US ==========================-->

