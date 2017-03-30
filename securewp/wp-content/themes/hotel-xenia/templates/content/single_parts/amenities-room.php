<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

Single Room View Template Parts / Amenities
*/
$options = get_query_var( 'options' );
if ( is_array( $options ) ) { extract($options); }
?>
<div class="room_single_amenities owl-carousel owlcarousel-singleroom-amenities">
 <?php foreach ( $amenities as $amenity ) { ?>
	<div class="text-center item">
		<div class="row">
		<?php switch ( $amenity['icon_type'] ) {
			case 'library_icon':
			default: 
				?>
				<i data-toggle="tooltip" data-placement="top" class="<?php echo esc_attr( $amenity['icon_class'] ); ?>" data-original-title="<?php echo esc_attr( $amenity['desc'] ); ?>"></i>
				<span><?php echo wp_kses( $amenity['title'], Plethora_Theme::allowed_html_for( 'heading' ) ); ?></span>
				<?php
				break;
		
			case 'custom_icon':
				?>
				<img src="<?php echo esc_url( $amenity['icon_url'] ); ?>" data-toggle="tooltip" data-placement="top" class="<?php echo esc_attr( $amenity['icon_class'] ); ?>" data-original-title="<?php echo esc_attr( $amenity['desc'] ); ?>"/>
				<span><?php echo wp_kses( $amenity['title'], Plethora_Theme::allowed_html_for( 'heading' ) ); ?></span>
				<?php
			break;
		} ?>
		</div>
	</div>
<?php } ?>
</div>