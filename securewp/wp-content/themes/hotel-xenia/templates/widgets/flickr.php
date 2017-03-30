<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

Flickr widget template part

*/

// PLENOTE: Turn this into a Mustache Template

// Get attributes sent by set_query_var
$atts = get_query_var( 'widget_atts' );
// Extract $atts to variables
if ( is_array( $atts ) ) { extract($atts); ?>

  <!-- ========================== FLICKR WIDGET =============================-->

  <?php

		$output = $before_widget;
		echo trim( $output );   

		if ( $title ) echo "<h4>" . esc_html( $title ) . "</h4>";		

		echo '<div id="latest-flickr-images"><div id="' . esc_attr( WIDGETS_PREFIX ) . 'flickr-widget" class="'. esc_attr( $widget_id ) .'"></div></div>';

		if($screen_name) { 
			$http_type = is_ssl() ? 'https' : 'http';	// Make a request over https if an ssl is used
			$get_url = $http_type .'://api.flickr.com/services/feeds/photos_public.gne?id='. $screen_name; 

			?>
			<script type="text/javascript">
			jQuery(document).ready(function($){	 			   
				// Our very special jQuery JSON fucntion call to Flickr, gets details of the most recent images			   
				$.getJSON("<?php echo esc_url( $get_url ) ?>&lang=en-us&format=json&jsoncallback=?", function(data){  //YOUR IDGETTR GOES HERE

					var htmlString = "<ul>";					
					$.each(data.items, function(i,item){																														   

					if(i<=<?php echo esc_attr( $photos_to_display ); ?>) {
												
							// I only want the ickle square thumbnails
							var sourceSquare = ( item.media.m ).replace( "_m.jpg", "_s.jpg" );		
							htmlString += '<li><a href="' + item.link + '" target="_blank">';
							htmlString += '<img src="' + sourceSquare + '" alt="' + item.title + '" title="' + item.title + '"/>';
							htmlString += '</a></li>';
						}
					});		
					
				// Pop our HTML in the #images DIV	
				$('.<?php echo esc_attr( $widget_id ); ?>').html(htmlString + "</ul>");
				
				// Close down the JSON function call
				});
				
			// The end of our jQuery function	
			});	
			</script>
		<?php }
		$output = $after_widget;
		echo trim( $output );


 ?>

  <!-- END======================= FLICKR WIDGET =============================-->

 <?php }