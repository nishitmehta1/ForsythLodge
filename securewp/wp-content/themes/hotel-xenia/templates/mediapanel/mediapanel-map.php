<?php /*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 		           (c) 2016

# MEDIA PANEL TEMPLATE
# TYPE(S): Map
# DESCRIPTION: 	Displays selected Google map
# CUSTOMIZATION / CREATION TIPS

	1. 	Create your own media panel template, using the following naming pattern: 
		'mediapanel-{yourcustomtemplatename}.php'
		Note that all module templates must be placed on your child theme, under '/templates/modules'
		directory.

	2.  You may add or change default media panel functionality using any of the 
		available filters
			
			'plethora_mediapanel_config'	: Filter to the full media panel configuration
			'plethora_mediapanel_types'		: Add/edit available media types
			'plethora_mediapanel_title'		: Add/edit media panel heading group title
			'plethora_mediapanel_subtitle'	: Add/edit media panel heading group subtitle
			'plethora_mediapanel_background': Add/edit media panel background configuration


	3. 	You may use, any of the values below to create your own markup. 
		
		General Mustache tips:
		
			i.   Double bracketed values cannot not include HTML, while triple bracketed values may include HTML. 
			ii.  Loop values start with {{# loopval }} and end with {{/ loopval }}
			iii. Empty value conditionals can be handled in a similar way as loop values ( check previous tip )
			iv.  All of the following values included, so you don't have to be afraid for missing ones

		Available mustache values for Media Panel templates:
        =======================================================================

		{{ colorset }}							Color Set class
		{{ transparentoverlay }}				Transparent Overlay class
		{{ fadeonscroll }}						Fade Effect On Page Scroll class
		{{ fullheight }}						Full Height class
		{{ hgroup }}							True if title or subtitle is not empty
		{{{ hgroup_title }}}					Heading group title
		{{{ hgroup_subtitle }}}					Heading group subtitle
		{{ hgroup_textalign }}					Heading group text align
		{{ image }}								Image url selected for background ( already loaded, only for reference )
        {{ slides }}                        Plethora slides start ( when a Plethora slider is selected for background )

          {{ bg_image }}                    Background image url
          {{ colorset }}                    Slide Color Set class
          {{ transparentfilm }}             Item classes ( as defined in Plethora_Shortcode_Postsgrid class ) 
          {{{ captions }}}                  True if any of the titles has content
          {{{ caption_title }}}             Main Caption Title
          {{{ caption_subtitle }}}          Main Caption Subtitle
          {{{ caption_secondarytitle }}}    Additional Caption Title
          {{{ caption_secondarytext }}}     Additional Caption Text
          {{ caption_colorset }}            Caption Color Set class
          {{ caption_transparentfilm }}     Slide Background Transparency class
          {{ caption_size }}                Caption Size class
          {{ caption_align }}               Caption Container Align class
          {{ caption_textalign }}           Caption Text Align class
          {{ caption_neutralizetext }}      Neutralize Links class ( links to be displayed as normal text )
          {{ caption_animation }}           Caption Animation class
          {{ caption_headingstyle }}        Caption Text Style class
          {{ caption_button }}              True if button has basic elements ( text and link )
          {{ caption_buttonlinktext }}      Button Link Text ( not visible if empty )
          {{ caption_buttonlinkurl }}       Button Link URL ( not visible if empty or '#' )
          {{ caption_buttonstyle }}         Button Style class
          {{ caption_buttonsize }}          Button Size class
          {{ caption_buttonlinktarget }}    Button Link URL 'target' attribute value

        {{/ slides }}             Slides loop ends

	=======================================================================
*/
?>
<div id="map" class="map {{ fullheight }}"></div>
<div class="{{ hgroup_container }}">
	{{# hgroup }}
    <div class="hgroup {{ hgroup_textalign }} {{ hgroup_width }} {{ hgroup_style }}">

      {{# hgroup_subtitle_before }}
      <p class="pretitle">{{{ hgroup_subtitle }}}</p>
      {{/ hgroup_subtitle_before }}

      {{# hgroup_title }}
      <h1 class="title">{{{ hgroup_title }}}</h1>
      {{/ hgroup_title }}	    

      {{# hgroup_subtitle_after }}
      <p class="subtitle">{{{ hgroup_subtitle }}}</p>
      {{/ hgroup_subtitle_after }}

      {{{ hgroup_breadcrumb }}}
    </div>
    {{/ hgroup }}
</div>