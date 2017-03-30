  
/*!
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2013-2015
                        
Theme Name: HOTEL XENIA
File Version: 1.0
This file contains the necessary Javascript for the theme to function properly.

*/

//========================== PLETHORA HELPER FUNCTIONS ==============================================

(function( window, doc, $ ){

  "use strict";

  /*** POLYFILLS ***/

  // SHIM POLYFILL FOR: requestAnimationFrame
  window.requestAnimationFrame = window.requestAnimationFrame || window.webkitRequestAnimationFrame ||
                                 window.mozRequestAnimationFrame || window.oRequestAnimationFrame ||
                                 window.msRequestAnimationFrame || function (cb){window.setTimeout(cb,1000/60);};

  var _p = _p || {};

  /*** OBJECT EXTEND: By @toddmotto ***/

  _p.extend = function( target, source ) {
      var merged = Object.create(target);
      Object.keys(source).map(function (prop) {  prop in merged && (merged[prop] = source[prop]);  });
      return merged;
  };

  /*** MULTI SLICE ***/

  _p.slice = function(){
    return [].slice.call.apply( [].slice, arguments );
  }

  /*** BOOLEAN OPERATOR CHECK ***/

  _p.checkBool = function(val){
      return ({1:1,true:1,on:1,yes:1}[(((typeof val !=="number")?val:(val>0))+"").toLowerCase()])?true:false;
  };

  /*** DEBUGGING CONSOLE ***/

  _p.debugLog = function(){
    themeConfig && themeConfig.debug && console.log.apply( console, arguments );
  }

  /*** DETECT INTERNET EXPLORER ***/

  _p.isIE = function() {
    var myNav = navigator.userAgent.toLowerCase();
    return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
  }

  /*** SVG CREATION UTILITY FUNCTION ***/

  _p.SVGMold  = function( type, options ){
  var molding = doc.createElementNS('http://www.w3.org/2000/svg', type );
  for (var key in options) options.hasOwnProperty(key) && molding.setAttribute( key, options[key]);
    return molding;
  }

  /*** PUBSUB ***/

  _p.PubSub = {};

  (function(q) {
      var topics = {}, subUid = -1;
      q.subscribe = function(topic, func) {
          if (!topics[topic]) {
              topics[topic] = [];
          }
          var token = (++subUid).toString();
          topics[topic].push({
              token: token,
              func: func
          });
          return token;
      };

      q.publish = function(topic, args) {
          if (!topics[topic]) {
              return false;
          }
          setTimeout(function() {
              var subscribers = topics[topic],
                  len = subscribers ? subscribers.length : 0;

              while (len--) {
                  subscribers[len].func(topic, args);
              }
          }, 0);
          return true;

      };

      q.unsubscribe = function(token) {
          for (var m in topics) {
              if (topics[m]) {
                  for (var i = 0, j = topics[m].length; i < j; i++) {
                      if (topics[m][i].token === token) {
                          topics[m].splice(i, 1);
                          return token;
                      }
                  }
              }
          }
          return false;
      };
  }(_p.PubSub));

  /*** SCROLL ON CLICK ***/

   $(window).bind( 'hashchange', function(e) {
    console.log(parseInt(window.location.hash.replace("#", ""))); 
   });

  $.extend( $.easing, { easeOutQuart: function (x, t, b, c, d) { return -c * ((t=t/d-1)*t*t*t - 1) + b; }, });

  _p.scrollOnClick = function(e){

    var HeaderHeight = $('.header').outerHeight();

    _p.debugLog("Scrolled...");
    e.preventDefault();                   // PREVENT DEFAULT ANCHOR CLICK BEHAVIOR
    var hash        = this.hash;          // STORE HASH
    var hashElement = $(this.hash);       // CACHE $.SELECTOR
     if ( hashElement.length > 0 ){
       $('html, body').animate({ scrollTop: Math.round(hashElement.offset().top) - HeaderHeight }, themeConfig["GENERAL"]["onePagerScrollSpeed"],'easeOutQuart', 
        function(){  
          /*** ADD HASH TO URL WHEN FINISHED [v1.3] | Thank you @LeaVerou! ***/
          if ( history.pushState ) history.pushState( null, null, hash ); // Old Method: window.location.hash = hash 
        });
     }

  }

  return window._p = _p;

}( window, document, jQuery ));

//END---------------------------------------------------------------------- PLETHORA HELPER FUNCTIONS

//========================== HEADER VARIATIONS ======================================================

(function($){

  "use strict";

    // Declaring some vars
    var header_height = $('.header').height();
    var window_height = $(window).height();
    var usable_height = window_height - header_height;
    var scroll_offset_trigger = themeConfig["GENERAL"].scroll_offset_trigger;

    // 1. Sticky Header always on Top. You have to add class "sticky_header" to the .header element
    
    if( $('.header.sticky_header:not(".transparent")').length ) { 

      var $body = $('body');
      $body.css( 'margin-top', header_height );
      $(window).on( 'load resize', function(){
        var header_height = $('.header').height();
        $body.css( 'margin-top', header_height );
      });

    }

    // 2. Sticky Header Bottom. You have to add class "bottom_sticky_header" to the .header element

    // 3. Appearing from Top Sticky Header. You have to add class "appearing_sticky_header" to the .header element

    if( $('.header.appearing_sticky_header').length ) {
      
      var $sticky_nav = $('.header.appearing_sticky_header');

      $(window).scroll(function () {
        if ($(this).scrollTop() > scroll_offset_trigger) {
            $sticky_nav.addClass("stuck");
        } else {
            $sticky_nav.removeClass("stuck");
        }
      }); 
    
      var window_top = $(window).scrollTop();

      if (window_top > scroll_offset_trigger) {
          $sticky_nav.addClass("stuck");
      } else {
          $sticky_nav.removeClass("stuck");
      } 

    }    
    
    // 4. Starting on Bottom and sticking on top. You have to add class "bottom_to_top_sticky_header" to the header.header element

    if( $('.header.bottom_to_top_sticky_header').length ) {
    
      var traveling_nav = $('.header.bottom_to_top_sticky_header');
      
      $(window).scroll(function () {
          if ($(this).scrollTop() > usable_height) {
              traveling_nav.addClass("stuck");
          } else {
              traveling_nav.removeClass("stuck");
          }
      }); 
      
      var window_top = $(window).scrollTop();
      if (window_top > usable_height) {
          traveling_nav.addClass("stuck");
      } else {
          traveling_nav.removeClass("stuck");
      }

    }

    // Alternative Sticky Header

    if( $('body.sticky_header_alt').length ) {

      var alternative_sticky_header = $('body.sticky_header_alt .header');

      //================================================================================

      if( $('.header:not(.bottom_to_top_sticky_header)').length ) {

        $(window).scroll(function () {
          if ($(this).scrollTop() > scroll_offset_trigger) {
              alternative_sticky_header.addClass("alt_header_triggered");
          } else {
              alternative_sticky_header.removeClass("alt_header_triggered");
          }
        });       
        var window_top = $(window).scrollTop();
        if (window_top > scroll_offset_trigger) {
            alternative_sticky_header.addClass("alt_header_triggered");
        } else {
            alternative_sticky_header.removeClass("alt_header_triggered");
        }

      }

      //================================================================================

      if( $('.header.bottom_to_top_sticky_header').length ) {
    
        var traveling_nav = $('.header.bottom_to_top_sticky_header');
        
        $(window).scroll(function () {
            if ($(this).scrollTop() > usable_height) {
                traveling_nav.addClass("stuck alt_header_triggered");
            } else {
                traveling_nav.removeClass("stuck alt_header_triggered");
            }
        }); 
        
        var window_top = $(window).scrollTop();
        if (window_top > usable_height) {
            traveling_nav.addClass("stuck alt_header_triggered");
        } else {
            traveling_nav.removeClass("stuck alt_header_triggered");
        }

      }

      //================================================================================

    }


}(jQuery));

//END----------------------------------------------------------------------------- HEADER VARIATIONS 

//========================== PRIMARY MENU CONSTRUCTOR ===============================================

(function($){

  "use strict";

    // Set the collapsing width for the header menu and the tools on the header
    var menu_collapsing_width = themeConfig["GENERAL"].menu_switch_to_mobile;
    var tools_collapsing_width = themeConfig["GENERAL"].minitools_switch_to_mobile;

    // If there are dropdowns on the primary nav, go on
    if ($('.header nav.primary_nav ul > li > ul').length) {

        // Add the appropriate classes to the primary nav
        $('.header nav.primary_nav ul > li > ul').addClass('menu-dropdown-content');        
        var lihaschildren = $('.header nav.primary_nav ul > li > ul').parent();
        lihaschildren.addClass('lihaschildren menu-dropdown');
        var atoggledropdown = $('.lihaschildren > a');
        atoggledropdown.addClass('menu-dropdown-toggle');
        
        // Click Menu Functionality (.click_menu class on .header)
        $('.click_menu a.menu-dropdown-toggle').on("click" , function(e) {
            $(this).parent('li').siblings('li').children('ul.open').removeClass('open');
            $(this).parent('li').siblings('li').children('ul').children('li').children('ul.open').removeClass('open');
            $(this).siblings('ul').children('li').children('ul.open').removeClass('open');
            $(this).siblings().toggleClass('open');
            e.stopPropagation();
        });

        // When we have a Click Menu and an item has both children and a link, then onClick don't serve the link, show the children. Basic UX Stuff.
        $('.click_menu a.menu-dropdown-toggle').attr( 'onclick' , 'return false');

        // Close Dropdown when clicking elsewhere
        $(document.body).on('click', function(){
            $('.menu-dropdown-content').removeClass('open');
        });

    };

    // Centered in Menu Inline Logo Feature (.header_centered and .logo_centered_in_menu on .header)
    if ( $('.header.logo_centered_in_menu').length ) {

      // Count the number of top level menu elements
      var count_of_lis = $('.primary_nav ul.nav > li').length;

      if (count_of_lis % 2 === 0 ) {
        // If count is even, target the middle li
        var center_of_lis = count_of_lis / 2;
        var li = $('.primary_nav ul.nav > li:nth-child(' + center_of_lis + ')')
      } else {
        // else if count is odd, add a fake li to make them even and target the middle li
        $('.primary_nav ul.nav').prepend('<li class="fake"></li>');
        var center_of_lis = count_of_lis / 2 + 0.5;
        var li = $('.primary_nav ul.nav > li:nth-child(' + center_of_lis + ')')
      }

      var logo_div = $(".logo");
      var maxWidth = 0;
      var elemWidth = 0;
      
      // Make all 1st-level elements of the menu, equal width
      /*$('.primary_nav ul.nav > li').each(function() {
          elemWidth = parseInt($(this).css('width'));
          if (parseInt($(this).css('width')) > maxWidth) {
              maxWidth = elemWidth;
          }
      });
      $('.primary_nav ul.nav > li').each(function() {
          $(this).css('width', maxWidth + "px");
      });*/

      // Insert the logo in the middle of the menu
      logo_div.insertAfter(li).wrap('<li class="logo_in_nav"></li>');

    };

    // Collapser from the mainbar of the header to the secondary widgetized area and vice versa
    $(window).on("load resize" , function() { 

        //var window_width = $(window).width(); // This includes the scrollbars
        function viewport() {
            var e = window, a = 'inner';
            if (!('innerWidth' in window )) {
                a = 'client';
                e = document.documentElement || document.body;
            }
            return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
        }
        var window_width = viewport().width; // This should match your media query
        var mainbar_container = $(".header .mainbar").children('[class^=container]');
        var primary_nav = $(".header nav.primary_nav");
        var primary_in_secondary_nav = $(".secondary_nav_widgetized_area nav.primary_nav");
        var header_container = $(".header .mainbar").children('[class^=container]');
        var nav_wrapper = $(".header .mainbar").children('[class^=container]').children(".main_nav_wrapper");
        var header_container_width = header_container.width();
        var rest_width = (window_width - header_container_width) * 0.5;
        var toggler_and_tools_div = $('div.toggler_and_tools');
        var tools_div = $('div.header_tools');
        var tools_div_in_secondary_nav = $(".secondary_nav_widgetized_area div.header_tools");
        //var logo_div = $(".logo");
        var logo_in_nav = $("li.logo_in_nav .logo");
        var header_not_centered = $('.header').not('.header_centered').not('.nav_centered').not('.nav_left').not('.logo_centered_in_menu');
        var header_centered_with_logo = $('.header.logo_centered_in_menu');

        // On mobile states move the primary menu in the secondary widgetized area and vice versa. Also handle the centered-logo-in-menu functionality.
        if (window_width <= menu_collapsing_width) {
          if (header_centered_with_logo.length) {
            logo_in_nav.unwrap().prependTo(mainbar_container);
          }  
          primary_nav.prependTo(".secondary_nav_widgetized_area");
        } else {
            if ( primary_in_secondary_nav.length ) {
              primary_in_secondary_nav.prependTo(nav_wrapper);
              if (header_centered_with_logo.length) {  
                logo_div.insertAfter(li).wrap('<li class="logo_in_nav"></li>');
              }  
            }
        };

        // On mobile states move the tools in the secondary widgetized area and vice versa
        if (window_width <= tools_collapsing_width) {
            tools_div.prependTo(".secondary_nav_widgetized_area");
        } else {
            if ( tools_div_in_secondary_nav.length ) {
                tools_div_in_secondary_nav.prependTo(toggler_and_tools_div);
            }
        };

        // The toggler of the secondary & mobile menu along with the tools rests always on the right and pushes the rest of the header elements accordingly when it comes close to them on resize
        var toggler_and_tools_width = $(".toggler_and_tools").outerWidth();
        var min_position_width = rest_width - toggler_and_tools_width;
        // If tools exist on header make them target of the pushing, else push the primary navigation.
        //if ( tools_div.length ) {
        //    var padding_target = tools_div;
        //} else {
        var padding_target = primary_nav;
        //}
        // Apply this functionality only when the header is not centered
        if ( header_not_centered.length ) {
          if (min_position_width <= 15) {
              padding_target.css("padding-right", -min_position_width + 15);
          };
        };

    });

    // Open and close the secondary widgetized area that holds the mobile nav menu
    $(window).on("load" , function() {

      var header_height = $('.header').height();

      $("a.menu-toggler").on("click",function() {  
          $(this).toggleClass( "active" );
          $(".secondary_nav_widgetized_area").toggleClass( "secondary_nav_is_open" );
          $(".main").toggleClass( "secondary_nav_is_open" );
          $(".header").toggleClass( "secondary_nav_is_open" );
          //$(".head_panel").toggleClass( "secondary_nav_is_open" );
          $("footer").toggleClass( "secondary_nav_is_open" );
          $(".copyright").toggleClass( "secondary_nav_is_open" );
          if ( $(this).hasClass("active") ) {
            $(".secondary_nav_widgetized_area").css('padding-top' , header_height);
          } else {
            
            $(".secondary_nav_widgetized_area").css('padding-top' , '0');
          }
          
      });

      // When clicking on a nav link of the secondary widgetized area, close the area
      $(".secondary_nav_widgetized_area nav a").on("click",function() {  
          $("a.menu-toggler").toggleClass( "active" );
          $(".secondary_nav_widgetized_area").toggleClass( "secondary_nav_is_open" );
          $(".main").toggleClass( "secondary_nav_is_open" );
          $(".header").toggleClass( "secondary_nav_is_open" );
          //$(".head_panel").toggleClass( "secondary_nav_is_open" );
          $("footer").toggleClass( "secondary_nav_is_open" );
          $(".copyright").toggleClass( "secondary_nav_is_open" );
          $(".secondary_nav_widgetized_area").css('padding-top' , '0');
      });

    }); 

    // Now that everything is set, make the primary nav visible
    $(document).ready(function() {
      $('.header nav.primary_nav').css('visibility' , 'visible');
    });  
    // $(window).on("resize" , function() {
    //   $('.header nav.primary_nav').css('visibility' , 'visible');
    // });        

}(jQuery));

//END----------------------------------------------------------------------- PRIMARY MENU CONSTRUCTOR   

//========================== FADE OUT HEADER OnSCROLL EFFECT ========================================

(function($){

  "use strict";

  if ( $('.head_panel.fade_on_scroll').length ) {

    $(window).on('scroll', function() {
      var element = $('.head_panel');
      var ft = $(this).scrollTop();
      element.css({ 'opacity' : (1 - ft/400) });
    });

  }  

}(jQuery));

//END------------------------------------------------------------- FADE OUT HEADER OnSCROLL EFFECT

//========================== LOADER ==============================================================

(function($) {

  "use strict";

  $(window).load(function(){
    setTimeout(function(){
      $('.loading').addClass("hidden");
      $('.loader-logo').addClass("slideOutUp");
      $('.loader').addClass("slideOutUp");
      $('body').addClass("body-animated");
    }, 10);
  });
  
}(jQuery));

//END-------------------------------------------------------------------------------------- LOADER

//========================= SCROLL ON CLICK OF A HASH-LINK init ==================================

(function($){

  $(".header, .head_panel, .main")
      .find('a[href^="#"], button[href^="#"]')
      .add("a.scrollify")
      .on('click', _p.scrollOnClick );

})(jQuery);

//END--------------------------------------------------------- SCROLL ON CLICK OF A HASH-LINK init

//========================= PARALLAX for Head Panel ==============================================

(function($){

  $('.parallax-window').each(function(){

    var bg_image = $(this).css("background-image").replace('url(','').replace(')','').replace(/\"/g, '').replace(/\'/g, '');
    $(this).addClass("transparent").css("background-image","none").attr("data-parallax", "scroll").attr("data-image-src", bg_image).attr("data-position", "center top");

  }); 

}(jQuery));

//END--------------------------------------------------------------------- PARALLAX for Head Panel

//========================= WOW (REVEAL ON SCROLL INIT FOR NO-TOUCH DEVICES) =====================

(function($){

  if ($('.no-touch').length) {
    var wow = new WOW({
      animateClass : 'animated',
      offset       :       100
    });
    wow.init();
  }

})(jQuery);

//END-------------------------------------------- WOW (REVEAL ON SCROLL INIT FOR NO-TOUCH DEVICES)

//=================== SECTION SEPARATORS =========================================================

  (function($){

    var $separator_top    = $(".separator_top");
    var $separator_bottom = $(".separator_bottom");

    if ($separator_top.length) {
      $separator_top.each(function(){
        $(this).prepend( "<div class='separator_top'><div>" );
      }); 
    }
    if ($separator_bottom.length) {
      $separator_bottom.each(function(){
        $(this).append( "<div class='separator_bottom'><div>" );
      }); 
    }

  }(jQuery));

//END------------------------------------------------------------------------------SECTION SEPERATORS 

//========================== SELECT TAG ARROW STYLING ===============================================

(function($){

  "use strict";

  $('select:not([multiple=multiple])').wrap( "<div class='select_wrapper'></div>" );

}(jQuery));

//END----------------------------------------------------------------------- SELECT TAG ARROW STYLING

//========================== FORM INPUT=NUMBER STYLING ==============================================

(function($){

  "use strict";

  $('input[type="number"]').not('.cart_item input[type="number"]').before(function() {
      return $('<span />', {
          'class': 'spinner',
          text: '-'
      }).on('click', {input : this}, function(e) {
          e.data.input.value = (+e.data.input.value) - 1;
      });
  }).after(function() {
      return $('<span />', {
          'class': 'spinner',
          text: '+'
      }).on('click', {input : this}, function(e) {
          e.data.input.value = (+e.data.input.value) + 1;
      });
  });

}(jQuery));

//END---------------------------------------------------------------------- FORM INPUT=NUMBER STYLING