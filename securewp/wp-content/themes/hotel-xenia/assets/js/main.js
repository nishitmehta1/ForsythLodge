
(function() {
    jQuery(function($) {

        FLApp = {

            init: function() {
                this.loadBindings();
            },

            loadBindings: function() {
                var thisApp = this;

                console.log('hello');

                $('.owl-carousel').owlCarousel({
      loop:true,
      margin:10,
      nav:true,
      responsive:{
          0:{
              items:1
          },
          600:{
              items:3
          },
          1000:{
              items:5
          }
      }
  });
            }
        };

        FLApp.init();

    });
})();