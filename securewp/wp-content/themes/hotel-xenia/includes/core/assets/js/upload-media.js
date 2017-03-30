jQuery(function($){

 function media_upload( button_class) {

    var _custom_media         = true,
        _orig_send_attachment = wp.media.editor.send.attachment;

    $('body').on('click', '#widgets-right ' + button_class, function(e) {

        var button           = $(this).attr('id');
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var id                  = document.getElementById(button).getAttribute('id').replace('_button', '');
        _custom_media           = true;
        wp.media.editor.send.attachment = function(props, attachment){
            if ( _custom_media  ) {
               $( '.' + id + '_url' ).val( attachment.url );
               $('.' + id + '_thumbnail').attr( 'src', attachment.url ).css('display','block');   
            } else {
                return _orig_send_attachment.apply( button, [props, attachment] );
            }
        };
        wp.media.editor.open(button);
        return false;
    });

}

media_upload( '.custom_media_upload');

});