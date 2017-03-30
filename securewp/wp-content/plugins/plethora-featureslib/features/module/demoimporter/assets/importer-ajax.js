//// INIT SCRIPTS START
/*
Demo import panel display init
*/
jQuery(document).on( 'click', '.start_import', function( event ) {
	jQuery.ajax({
	    url: pdi.ajaxurl, 
	    type: 'post',
	    data: 'demo_slug='+ this.id +'&action=pdi_demo_panel&pdi_nonce='+ pdi.pdi_nonce , // Notice the action name here! This is the basis on which WP calls your process_my_ajax_call() function.
	    cache: false,
	    beforeSend: function ( ) {

			jQuery('.og-grid').css( 'visibility', 'hidden' );
			jQuery('.og-grid').css( 'display','none' );
			jQuery('.pdi .loader').css( 'visibility', 'visible' );
	    },
	    success: function ( response ) {
			
			jQuery('.pdi .loader').css( 'visibility', 'hidden' );
			jQuery('.pdi-workpanel').prepend( response );
	    },
	    error: function ( response ) {

			jQuery('.pdi .loader').css( 'visibility', 'hidden' );
			jQuery('.og-grid').css( 'visibility', 'visible' );
			jQuery('.og-grid').css( 'display','initial' );
			alert( 'It seems that for some reason, the import procedure cannot start right now. Please try again later.' );
	    }
	});
})	
//// INIT SCRIPTS END

//// FUNCTIONS START

/*
Import demo init
*/
jQuery(document).on( 'click', '.import_init', function( event ) {
	event.preventDefault();
	var $import_button = jQuery(this);
	import_button_url = $import_button.attr( 'href' );
	$import_button.attr( 'href', '#' );
	$import_button.removeClass('import_init');
	$import_button.blur();
	imports_counter = 0;
	jQuery(document).queue('imports', queue_import_events( 'in_progress', imports_counter++, import_button_url ) );
	jQuery.each( pdi.actions, function( import_type, data ) {
		
		if ( jQuery('#'+ import_type ).length && import_type == 'attachment' ) {

			if ( parseInt( pdi.attachments_count[$import_button.attr('id')] ) > 0 ) { 

				for (post_import_id = 0; post_import_id < pdi.attachments_count[$import_button.attr('id')] ; post_import_id++) { 

					imports_counter++;
					jQuery(document).queue('imports', queue_import( $import_button.attr('id'), import_type, data, imports_counter, post_import_id ) );
				}
			}

		} else if ( jQuery('#'+ import_type ).length && import_type != 'attachment' ) {

			imports_counter++;
			jQuery(document).queue('imports', queue_import( $import_button.attr('id'), import_type, data, imports_counter ) );
		}			
	});

	jQuery(document).queue('imports', queue_import_events( 'finished', imports_counter++, import_button_url ) );
	jQuery(document).dequeue('imports');
	
})	

/*
Adds an import side event to execution queue
*/
function queue_import_events( event_action, next, import_button_url ){

    return function(next){

		switch( event_action ) {

		    case 'in_progress':

				jQuery('.import_button').html('');	
				jQuery('.import_button').addClass('loadingbutton');
				jQuery('.import_button').css('background-color', '#C11313');
		        next();
		        break;

		    case 'finished':
				jQuery('.import_button').html('Import Finished!<br>Click To Check Your Site!');	
				jQuery('.import_button').css('background-color', '#0dbf1a');	
				jQuery('.import_button').removeClass('loadingbutton');
				jQuery('.import_button').attr( 'href', import_button_url );
		        next();
		        break;
		} 
    }
}

/*
Adds an import action to execution queue
*/
function queue_import( demo_slug, import_type, data, next, post_import_key ){

    return function(next){

		if ( import_type == 'attachment') {

			do_import_single_post( demo_slug, import_type, data, post_import_key, next )

		} else {

			do_import( demo_slug, import_type, data, next );
		}
    }
}

/*
Sends an ajax request to initiate import
*/
function do_import( demo_slug, import_type, data, next ) {

	jQuery.ajax({
	    url: pdi.ajaxurl, 
	    async: true,
	    type: 'post',
	    data: 'demo_slug='+ demo_slug +'&action=pdi_import&response_method='+ data.response_method +'&pdi_nonce='+ pdi.pdi_nonce , // Notice the action name here! This is the basis on which WP calls your process_my_ajax_call() function.
	    cache: false,
	    beforeSend: function ( ) {

	    	jQuery( '.'+ import_type +'.pdi-status' ).addClass( 'loading' );
			jQuery( '.'+ import_type +'.pdi-status').html( '' );
	    },
	    success: function ( response ) {

	    	jQuery( '.'+ import_type +'.pdi-status').removeClass( 'loading' );
			jQuery( '.'+ import_type +'.pdi-status').addClass( 'success' );
			jQuery( '.'+ import_type +'.pdi-status').html( data.success_notice );
	    	next();

	    },
	    error: function ( response ) {

	    	jQuery( '.'+ import_type +'.pdi-status').removeClass( 'loading' );
			jQuery( '.'+ import_type +'.pdi-status').addClass( 'failure' );
			jQuery( '.'+ import_type +'.pdi-status').html( data.error_notice );

	    }
	});
}

/*
Sends an ajax request to initiate post import
*/
function do_import_single_post( demo_slug, import_type, data, post_import_key, next ) {

	jQuery.ajax({
	    url: pdi.ajaxurl, 
	    async: true,
	    type: 'post',
	    data: 'demo_slug='+ demo_slug +'&action=pdi_import&response_method='+ import_type +'&post_import_key='+ post_import_key +'&pdi_nonce='+ pdi.pdi_nonce , // Notice the action name here! This is the basis on which WP calls your process_my_ajax_call() function.
	    cache: false,
	    beforeSend: function ( ) {

	    	jQuery( '.'+ import_type +'.pdi-status .placeholder' ).html( ' ' );
	    	jQuery( '.'+ import_type +'.pdi-status .placeholder' ).addClass( 'loading' );
	    	jQuery( '.a'+ post_import_key ).addClass( 'strong' );
	    	jQuery( '.a'+ post_import_key ).removeClass( 'hide' );
	    	jQuery( '.a'+ post_import_key ).prepend( data.beforeSend_notice + '<br>' );
	    },
	    success: function ( response ) {

	    	// jQuery( '.a'+ post_import_key ).removeClass( 'loading' );
	    	jQuery( '.'+ import_type +'.pdi-status .placeholder' ).removeClass( 'loading' );
	    	
	    		jQuery( '.a'+ post_import_key ).addClass( 'hide' );

	    	next();

	    },
	    error: function ( response ) {

			jQuery( '.a'+ post_import_key ).append( data.error_notice );
			next();
	    }
	});
}


//// FUNCTIONS END