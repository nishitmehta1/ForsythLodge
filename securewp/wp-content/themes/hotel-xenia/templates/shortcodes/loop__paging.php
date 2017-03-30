<div class="col-md-12 text-left">
<h2>PAGING</h2>
<p>Template file: <strong> templates/shortcodes/shortcode-postsgrid_paging.php</strong></p>
<h4>PAGES</h4>
<ul style="background-color: #F6F6F6; padding: 10px; list-style: none;">
	<li>paging_previous_post_link: <strong>{{{ paging_previous_post_link }}}</strong></li>
	<li>paging_previous_post_text: <strong>{{{ paging_previous_post_text }}}</strong></li>
	<li>paging_pages: 
  {{# paging_pages }}
		<ul style="background-color: #F0F0F0; margin: 10px 0 10px; list-style: none;">
			<li>number: <strong>{{{ number }}}</strong></li>
			<li>link: <strong>{{{ link }}}</strong></li>
			<li>text: <strong>{{{ text }}}</strong></li>
			<li>active_class: <strong>{{{ active_class }}}</strong></li>
  		</ul>
  {{/ paging_pages }}
  	</li>
	<li>paging_next_post_link: <strong>{{{ paging_next_post_link }}}</strong></li>
	<li>paging_next_post_text: <strong>{{{ paging_next_post_text }}}</strong></li>
</ul>
</div>

