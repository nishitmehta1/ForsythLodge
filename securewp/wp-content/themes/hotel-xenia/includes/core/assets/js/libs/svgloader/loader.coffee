(($)->

    init = ()->

        ### LOADER MODAL FOR TEAM MEMBERS, SHOWCASE, PORTFOLIO AND BLOG SECTIONS ###

        # ADD PROGRESS INDICATOR
        loadProgressIndicator = ->
            prog = $ "span.progress_ball"
            prog.toggleClass "show"

        loader       = new SVGLoader( document.getElementById( 'loader' ), { speedIn : 250, easingIn : mina.easeinout, onEnd: loadProgressIndicator } )
        loaderModal  = document.querySelector(".loader-modal")
        $loaderModal = $(loaderModal)
        $loaderModal.on "click", ".close-handle", (e)->
            $loaderModal.scrollTop 0
            $loaderModal.fadeOut 250, ()->
                $loaderModal.attr('class', 'loader-modal');
            return

        loaderLauncher = ( options )->

            content   = options.content 
            className = options.className
            inject    = options.inject

            loader.show()

            setTimeout ()-> 
                $loaderModal.addClass(className) if className isnt 'undefined' 
                $loaderModal.html('').append($("<span class='close-handle' />"))
                ((content, inject)->
                    $.ajax
                        url: content
                        error: (data)->
                            $loaderModal.append( themeConfig.ajaxErrorMessage.open + content + themeConfig.ajaxErrorMessage.close ).fadeIn(250, ()-> 
                                    loader.hide()
                                    loadProgressIndicator()
                            )
                        success: ( data )->
                            window_height = $(window).height()
                            $main         = $(data).find(".main")
                            colorSet      = $main.find("").find("[data-colorset]").data("colorset") or ""
                            injectable    = $main.addClass('ajaxed ' + colorSet).css("min-height", window_height)
                            $loaderModal.append(injectable).fadeIn(250, ()-> 
                                    loadProgressIndicator()
                                    loader.hide()
                                    # INITIALIZE 3D LINK EFFECT
                                    ((selector) ->
                                      if !( document.body.style['webkitPerspective'] != undefined or document.body.style['MozPerspective'] != undefined)
                                        return
                                      _p.slice(document.querySelectorAll('a.roll')).forEach (a) ->
                                        a.innerHTML = '<span data-title="' + a.text + '">' + a.innerHTML + '</span>'
                                        return
                                      return
                                    )()
                            )
                            # ajaxCallback inject
                )(content, inject)  
            , 250 

            return

        ### SECTION: TEAM ###

        # $(".team_member .linkify").on "click", (e)->
        $(".linkify").on "click", (e)->
                e.preventDefault()
                _p.debugLog "Class 'ajax-call' detected."
                content = e.currentTarget.href
                loaderLauncher 
                    content     : content
                    className   : "loader-modal-content"

    ( document.getElementById( 'loader' ) ) && ( document.querySelector(".loader-modal") ) && init();

)(jQuery)