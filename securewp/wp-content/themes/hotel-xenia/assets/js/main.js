(function() {
  jQuery(function($) {

    FLApp = {

      init: function() {
        this.loadBindings();
        this.googleMap();
        this.syncOwlCarouselFirstSection();
        this.syncOwlCarouselSecondSection();
        this.syncOwlCarouselSecondSectionMobileVersion();
      },

      syncOwlCarouselFirstSection: function() {
        var sync1 = $("#sync1"),
          sync2 = $("#sync2"),
          syncedSecondary = true;

        sync1.owlCarousel({
          items : 1,
          slideSpeed : 1000,
          nav: true,
          autoplay: false,
          dots: true,
          loop: true,
          touchDrag  : false,
          mouseDrag  : false,
          animateIn: 'fadeIn',
          animateOut: 'fadeOut',
        }).on('changed.owl.carousel', syncPosition);

        sync2
          .on('initialized.owl.carousel', function () {
            sync2.find(".owl-item").eq(0).addClass("current");
          })
          .owlCarousel({
          items : 1,
          dots: true,
          nav: true,
          slideSpeed : 1000,
          slideBy: 1,
          animateIn: 'fadeIn',
          animateOut: 'fadeOut',
        }).on('changed.owl.carousel', syncPosition2);

        function syncPosition(el) {
          var count = el.item.count-1;
          var current = Math.round(el.item.index - (el.item.count/2) - .5);
          
          if(current < 0) {
            current = count;
          }
          if(current > count) {
            current = 0;
          }

          sync2
            .find(".owl-item")
            .removeClass("current")
            .eq(current)
            .addClass("current");
          var onscreen = sync2.find('.owl-item.active').length - 1;
          var start = sync2.find('.owl-item.active').first().index();
          var end = sync2.find('.owl-item.active').last().index();
          
          if (current > end) {
            sync2.data('owl.carousel').to(current, 100, true);
          }
          if (current < start) {
            sync2.data('owl.carousel').to(current - onscreen, 100, true);
          }
        }

        function syncPosition2(el) {
          if(syncedSecondary) {
            var number = el.item.index;
            sync1.data('owl.carousel').to(number, 100, true);
          }
        }

        sync2.on("click", ".owl-item", function(e){
          e.preventDefault();
          var number = $(this).index();
          sync1.data('owl.carousel').to(number, 300, true);
        });
      },

      syncOwlCarouselSecondSection: function() {
        var sync11 = $("#sync11"),
          sync22 = $("#sync22"),
          syncedSecondary = true;

                  sync11.owlCarousel({
          items : 1,
          slideSpeed : 1000,
          nav: true,
          autoplay: false,
          dots: true,
          loop: true,
          touchDrag  : false,
          mouseDrag  : false,
          animateIn: 'fadeIn',
          animateOut: 'fadeOut',
        }).on('changed.owl.carousel', syncPosition);

        sync22
          .on('initialized.owl.carousel', function () {
            sync22.find(".owl-item").eq(0).addClass("current");
          })
          .owlCarousel({
          items : 1,
          dots: true,
          nav: true,
          slideSpeed : 1000,
          slideBy: 1,
          animateIn: 'fadeIn',
          animateOut: 'fadeOut',
        }).on('changed.owl.carousel', syncPosition2);

        function syncPosition(el) {
          var count = el.item.count-1;
          var current = Math.round(el.item.index - (el.item.count/2) - .5);
          
          if(current < 0) {
            current = count;
          }
          if(current > count) {
            current = 0;
          }

          sync22
            .find(".owl-item")
            .removeClass("current")
            .eq(current)
            .addClass("current");
          var onscreen = sync22.find('.owl-item.active').length - 1;
          var start = sync22.find('.owl-item.active').first().index();
          var end = sync22.find('.owl-item.active').last().index();
          
          if (current > end) {
            sync22.data('owl.carousel').to(current, 100, true);
          }
          if (current < start) {
            sync22.data('owl.carousel').to(current - onscreen, 100, true);
          }
        }

        function syncPosition2(el) {
          if(syncedSecondary) {
            var number = el.item.index;
            sync11.data('owl.carousel').to(number, 100, true);
          }
        }

        sync22.on("click", ".owl-item", function(e){
          e.preventDefault();
          var number = $(this).index();
          sync11.data('owl.carousel').to(number, 300, true);
        });
      },

      syncOwlCarouselSecondSectionMobileVersion: function() {
        var sync111 = $("#sync111"),
          sync222 = $("#sync222"),
          syncedSecondary = true;

                  sync111.owlCarousel({
          items : 1,
          slideSpeed : 1000,
          nav: true,
          autoplay: false,
          dots: true,
          loop: true,
          touchDrag  : false,
          mouseDrag  : false,
          animateIn: 'fadeIn',
          animateOut: 'fadeOut',
        }).on('changed.owl.carousel', syncPosition);

        sync222
          .on('initialized.owl.carousel', function () {
            sync222.find(".owl-item").eq(0).addClass("current");
          })
          .owlCarousel({
          items : 1,
          dots: true,
          nav: true,
          slideSpeed : 1000,
          slideBy: 1,
          animateIn: 'fadeIn',
          animateOut: 'fadeOut',
        }).on('changed.owl.carousel', syncPosition2);

        function syncPosition(el) {
          var count = el.item.count-1;
          var current = Math.round(el.item.index - (el.item.count/2) - .5);
          
          if(current < 0) {
            current = count;
          }
          if(current > count) {
            current = 0;
          }

          sync222
            .find(".owl-item")
            .removeClass("current")
            .eq(current)
            .addClass("current");
          var onscreen = sync222.find('.owl-item.active').length - 1;
          var start = sync222.find('.owl-item.active').first().index();
          var end = sync222.find('.owl-item.active').last().index();
          
          if (current > end) {
            sync222.data('owl.carousel').to(current, 100, true);
          }
          if (current < start) {
            sync222.data('owl.carousel').to(current - onscreen, 100, true);
          }
        }

        function syncPosition2(el) {
          if(syncedSecondary) {
            var number = el.item.index;
            sync111.data('owl.carousel').to(number, 100, true);
          }
        }

        sync222.on("click", ".owl-item", function(e){
          e.preventDefault();
          var number = $(this).index();
          sync111.data('owl.carousel').to(number, 300, true);
        });
      },

      googleMap: function() {
        var lat = 22.595836,
            long = 78.1478153;


          google.maps.event.addDomListener(window, 'load', init);

          function init() {
            var mapOptions = {
              scrollwheel: false,
              navigationControl: false,
              mapTypeControl: false,
              scaleControl: false,
              draggable: false,
              zoom: 10,
              center:new google.maps.LatLng(lat, long),
              styles: [{"featureType":"administrative","elementType":"all","stylers":[{"visibility":"on"},{"lightness":33}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2e5d4"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#c5dac6"}]},{"featureType":"poi.park","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":20}]},{"featureType":"road","elementType":"all","stylers":[{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#c5c6c6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#e4d7c6"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#fbfaf7"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"color":"#acbcc9"}]}]
            };

            var mapElement = document.getElementById('map'),
                map = new google.maps.Map(mapElement, mapOptions);

            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, long),
                map: map,
                title: "Forsyth's Lodge",
                icon: templateUrl+'/images/marker.png'
            });
          }
      },

      loadBindings: function() {
        var thisApp = this;
      }
    };

    FLApp.init();

  });
})();