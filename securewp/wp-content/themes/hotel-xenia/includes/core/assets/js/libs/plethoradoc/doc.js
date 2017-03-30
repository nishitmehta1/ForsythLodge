(function($){
			  
    "use strict";

    $(window).on("load" , function() {

        var window_width = $(window).width();

        if (window_width <= 768) {
            $('.sitemap_toggle').on("click" , function() {

                $('.ex').toggleClass('open active');
                $('.sitemap').toggleClass('open');
                $('.overflow_wrapper').toggleClass('open');

            });
        };

        if (window_width > 768) {
            $('.sitemap_toggle').on("click" , function() {

                $('.ex').toggleClass('open active');
                $('.sitemap').toggleClass('open');

            });
        };    

        

    });			  

        
    var $htmlBody;

    $('.sitemap a').click(function(){ 
        
        //$('.sitemap').toggle();
        var href = $.attr(this, 'href');
        if (href != '#') {
            $htmlBody = $htmlBody || $('html, body');
            $htmlBody.animate({
                scrollTop: $(href).offset().top
            }, 500, function () {
                window.location.hash = href;
            });
        } else {    
            return false;
        }

    });
        

    $('.bs-docs-sidenav a').click(function(e) {
                              
        var href = $.attr(this, 'href');
        if (href != '#') {
            $('html, body').animate({
                scrollTop: jQuery(href).offset().top
            }, 500, function () {
                window.location.hash = href;
            });
        } else {    
            return false;
        }
    });

}(jQuery));




  //=================== LAZY LOADER ================================================

  (function($){

    $("img.lazy").unveil(200, function(){});


  })(jQuery);

  //END================ LAZY LOADER ================================================

  //=================== LIGHTBOX ===================================================

  (function($){

    var activityIndicatorOn = function(){
        $( '<div id="imagelightbox-loading"><div></div></div>' ).appendTo( 'body' );
      },
      activityIndicatorOff = function(){
        $( '#imagelightbox-loading' ).remove();
      },
      overlayOn = function(){
        $( '<div id="imagelightbox-overlay"></div>' ).appendTo( 'body' );
      },
      overlayOff = function(){
        $( '#imagelightbox-overlay' ).remove();
      },
      closeButtonOn = function( instance ){
        $( '<a href="#" id="imagelightbox-close">Close</a>' ).appendTo( 'body' ).on( 'click', function(){ $( this ).remove(); instance.quitImageLightbox(); return false; });
      },
      closeButtonOff = function(){
        $( '#imagelightbox-close' ).remove();
      },
      captionOn = function(){
            var description = $( 'a[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"] img' ).attr( 'alt' ) || "";
        if( description.length > 0 )
          $( '<div id="imagelightbox-caption">' + description + '</div>' ).appendTo( 'body' );
      },

        // DISPLAY CAPTION ON SINGLE POST VIEW
        captionOnSingle = function()
        {
            var description = $( 'a[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"]' ).attr( 'title' ) || "";
            if( description.length > 0 )
                $( '<div id="imagelightbox-caption">' + description + '</div>' ).appendTo( 'body' );
        },

        // DISPLAY CAPTION ON GALLERY GRID CLASSIC MODE. CAPTION IS BASED ON ALT ATTRIBUTE.
        captionOnGallery = function(){
            var description = $( 'a[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"]' ) || "";
            if ( description.attr('data-description') !== "undefined" && description.attr('data-description') !== "" ){
                description = description.attr('data-description');
            } else if ( description.attr('datas-caption') !== "undefined" && description.attr('datas-caption') !== "" ) {
                description = description.attr('data-caption');
            }
            if( description && description.length > 0 )
                $( '<div id="imagelightbox-caption">' + description + '</div>' ).appendTo( 'body' );
        },

        captionOff = function(){
          $( '#imagelightbox-caption' ).remove();
        };

        // ARROWS

        var arrowsOn = function( instance, selector ){
            var $arrows = $( '<button type="button" class="imagelightbox-arrow imagelightbox-arrow-left"></button><button type="button" class="imagelightbox-arrow imagelightbox-arrow-right"></button>' );
                $arrows.appendTo( 'body' );
                $arrows.on( 'click touchend', function( e ){
                  e.preventDefault();
                  var $this   = $( this ),
                      $target = $( selector + '[href="' + $( '#imagelightbox' ).attr( 'src' ) + '"]' ),
                      index   = $target.index( selector );
                  if( $this.hasClass( 'imagelightbox-arrow-left' ) ) {
                      index = index - 1;
                      if( !$( selector ).eq( index ).length ) index = $( selector ).length;
                  } else {
                      index = index + 1;
                      if( !$( selector ).eq( index ).length )
                          index = 0;
                  }
                  instance.switchImageLightbox( index ); 
                  return false;
            });
        },
        arrowsOff = function(){
          $( '.imagelightbox-arrow' ).remove();
        };

    //  MASONRY GALLERY INITIALIZATION
    if ( $().imageLightbox ) {

        // ADDING LIGHTBOX FOR GALLERY GRID / CLASSIC "PORTFOLIO STRICT" & MASONRY
        // var selectorGG = 'a[data-imagelightbox="gallery"]';  // ENABLE ARROWS
        var selectorGG = 'a.lightbox_gallery';                  // ENABLE ARROWS
        var instanceGG = $( 'a.lightbox_gallery' ).imageLightbox({
            /* WITH ARROWS */
            onStart:        function() { arrowsOn( instanceGG, selectorGG ); overlayOn(); closeButtonOn( instanceGG ); }, 
            onEnd:          function() { arrowsOff(); overlayOff(); captionOff(); closeButtonOff(); activityIndicatorOff(); }, 
            onLoadEnd:      function() { $( '.imagelightbox-arrow' ).css( 'display', 'block' ); captionOnGallery(); activityIndicatorOff(); },
            onLoadStart:    function() { captionOff(); activityIndicatorOn(); }
        });
        var selectorS = 'a[data-imagelightbox="gallery"]'; // ENABLE ARROWS
        var instanceS = $( 'a.lightbox_single' ).imageLightbox({
          /* WITH ARROWS */
          onStart:        function() { arrowsOn( instanceS, selectorS ); overlayOn(); closeButtonOn( instanceS ); },
          onEnd:          function() { arrowsOff(); overlayOff(); captionOff(); closeButtonOff(); activityIndicatorOff(); },
          onLoadEnd:      function() { $( '.imagelightbox-arrow' ).css( 'display', 'block' ); captionOnSingle(); activityIndicatorOff(); },
          onLoadStart:    function() { captionOff(); activityIndicatorOn(); }
        });

    }

  })(jQuery);

  //END================ LIGHTBOX ===================================================

 (function($){

    $('#search').hideseek({
        highlight: true,
        nodata: 'No results found'
    });

 })(jQuery);


 //========== WOW =======================

(function($){

    $(window).on("load",function(e) {

        var wow = new WOW(
          {
          animateClass: 'animated',
          offset:       100
          }
        );  

        wow.init();

    });   

}(jQuery));


/*
  (function($){

    $('pre code').each(function() {
      var code_html = $(this).html();
      var code_text = code_html.replace(/</g, '&lt;').replace('&lt;iframe>', '').replace('&lt;/iframe>', '');
      $(this).html(code_text);
    });

 })(jQuery);
*/




