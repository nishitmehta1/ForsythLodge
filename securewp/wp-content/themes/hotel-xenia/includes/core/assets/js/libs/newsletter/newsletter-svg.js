  //==================== SVG NEWSLETTER ========================================

  (function($){

    function SVGNewsletterInit(){

      var svgWrapper        = $(".svg_newsletter");
      if ( svgWrapper.length ){

        var svgWrapperW  = svgWrapper.outerWidth();

        // CREATE SVG CONTAINER
        var svgNode = _p.SVGMold('svg',{
          width   : svgWrapperW + "px",
          height  : "220px",
          version : '1.1',
          xmlns   : 'http://www.w3.org/2000/svg',
          x       : "0px",
          y       : "0px"
        });
        var polygonGroup = _p.SVGMold( 'g', {
          //transform: "translate(-" + ( svgWrapperW * 5/100 ) + ",0)"
        });
        // RIGHT POLYGON SHAPE
        var right_polygon = _p.SVGMold('polygon', {
          points: svgWrapperW * 100/100 + ",220 420,220 560,40 " + svgWrapperW * 100/100 + ",40",
          fill: themeConfig["GENERAL"].headerBgColor
        });
        // LEFT POLYGON SHAPE
        var left_polygon = _p.SVGMold( 'polygon', {
          points : "600,180 " + svgWrapperW * 0/100 + ",180 " + svgWrapperW * 0/100 + ",0 440,0",
          fill   : themeConfig["GENERAL"].brandSecondary
        });
        var left_polygon_image = _p.SVGMold( 'polygon', {
          points : "600,180 " + svgWrapperW * 0/100 + ",180 " + svgWrapperW * 0/100 + ",0 440,0",
          fill   : "url(#svgImage)",
        });
        // CREATE SVG DEFS: PATTERN + PATTERN IMAGE
        if ( themeConfig["SVG_NEWSLETTER"].image !== "" ){
          var svgImage = new Image();
              svgImage.src = themeConfig["SVG_NEWSLETTER"].image; 
          var defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
          var patt_left = _p.SVGMold('pattern',{
            id           : 'svgImage',
            patternUnits : 'userSpaceOnUse',
            width        : '600',
            height       : '180',
            x            : '0',
            y            : '0'
          });
          var image = document.createElementNS('http://www.w3.org/2000/svg', 'image');
              image.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', svgImage.src );
              image.setAttribute('x', '0');
              image.setAttribute('y', '0');
              image.setAttribute('width', '600' );
              image.setAttribute('height', '180');
              image.setAttribute('opacity', '0.5');
          patt_left.appendChild(image); // APPEND IMAGE TO PATTERN
          defs.appendChild(patt_left);  // APPEND PATTERN TO DEFS
          svgNode.appendChild(defs);
        }
        polygonGroup.appendChild(right_polygon);
        polygonGroup.appendChild(left_polygon);
        polygonGroup.appendChild(left_polygon_image);
        svgNode.appendChild(polygonGroup);

        svgWrapper.append(svgNode);

      }

    };

    ( typeof SVGNewsletterInit !== "undefined" && themeConfig["SVG_NEWSLETTER"] ) && SVGNewsletterInit();

  })(jQuery);

  //END================= SVG NEWSLETTER ========================================