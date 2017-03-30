//==================== GOOGLE MAPS ===========================================

(function(globals){

  "use strict";

  var themeConfig = globals.themeConfig || {};

  // themeConfig["GOOGLE_MAPS"] = { REQUIRED SETTINGS }

    function mapsInit() {

      themeConfig["GOOGLE_MAPS"].maps.forEach(function(map){

        console.log(map);

        var mapElement = document.getElementById( map.id );

        if ( mapElement !== null ){

          var myLatlng   = new google.maps.LatLng( map.lat, map.lon );       
          var mapOptions = {
            //zoom                     : map.zoom,
            zoom: 12,
            scrollwheel              : map.scrollWheel,                    
            disableDefaultUI         : map.disableDefaultUI,
            center                   : myLatlng,
            mapTypeId                : google.maps.MapTypeId[map.type],
            styles                   : map.styles,
            mapTypeControl           : map.type_switch,            
            mapTypeControlOptions    : {
              style                    : google.maps.MapTypeControlStyle[map.type_switch_style],   
              position                 : google.maps.ControlPosition[map.type_switch_position]
            },
            panControl               : map.pan_control,
            panControlOptions        : {
              position                 : google.maps.ControlPosition[map.pan_control_position]  
            },
            zoomControl              : map.zoom_control,
            zoomControlOptions       : {
              style                    : google.maps.ZoomControlStyle[map.zoom_control_style],                
              position                 : google.maps.ControlPosition[map.zoom_control_position]
            },
            scaleControl             : map.scale_control,  
            streetViewControlOptions : {
              position                 : google.maps.ControlPosition[map.streetView_position]
            }
          };

          /* DISABLE MAP SCROLLING / ENABLE UI ON MOBILE DEVICES */
          if ( window.innerWidth <= 990 ) {  
            mapOptions.draggable        = false;
            mapOptions.disableDefaultUI = false;
          }


          var gmap        = new google.maps.Map( mapElement, mapOptions );

          // INIT gmarker
          if ( map. animatedMarker ){
            var overlay = new CustomMarker( myLatlng, gmap, { marker_id: 'custom_marker' });
          }

          /*----[ STREETVIEW ]------------------------------------*/

          if ( map.streetView ) {
     
            var panorama = new google.maps.StreetViewPanorama( mapElement, { position: myLatlng } );
            gmap.setStreetView(panorama);

          }

          /*------------------------------------[ STREETVIEW ]----*/

          /*----[ MAP MARKER ]------------------------------------*/

          if ( map.marker && !map. animatedMarker ){

            var image = "";

            if ( map.markerImageSrc !== "" ){
              var image = {
                url    : map.markerImageSrc,
                origin : new google.maps.Point(0,0),
                size   : new google.maps.Size( map.markerImageWidth, map.markerImageHeight ),
                anchor : new google.maps.Point( map.markerAnchorX, map.markerAnchorY )
              };
            } 

            var marker     = new google.maps.Marker({
              position : myLatlng,
              map      : gmap,
              icon     : image,
              draggable: map.draggable,
              title    : map.markerTitle
            });

          }

          /*------------------------------------[ MAP MARKER ]----*/

          /*----[ INFO WINDOWS ]----------------------------------*/

          if ( map.infoWindow !== "" ){

            var infowindow = new google.maps.InfoWindow({ content: map.infoWindow });          

            google.maps.event.addListener( marker, 'click', function(){ infowindow.open( gmap, marker ); });

          }

          /*----------------------------------[ INFO WINDOWS ]----*/

        }

      });

    }

    if ( 
      typeof google !== "undefined" && google.maps 
      && ( typeof themeConfig["GOOGLE_MAPS"] !== "undefined" )
      // && ( themeConfig["GOOGLE_MAPS"].maps.length > 0 )
    ) google.maps.event.addDomListener( window, 'load', mapsInit );

}(this));

//END================= GOOGLE MAPS ===========================================