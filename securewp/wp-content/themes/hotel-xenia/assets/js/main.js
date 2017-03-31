(function() {
  jQuery(function($) {

    FLApp = {

      init: function() {
          this.loadBindings();
          this.syncOwlCarouselFirstSection();
          this.syncOwlCarouselSecondSection();
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
          transitionStyle: "fade",
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
          transitionStyle: "fade",
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
          transitionStyle: "fade",
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
          transitionStyle: "fade",
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

      loadBindings: function() {
        var thisApp = this;
      }
    };

    FLApp.init();

  });
})();