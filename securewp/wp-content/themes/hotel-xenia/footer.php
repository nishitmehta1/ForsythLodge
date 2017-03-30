<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

Description: Handles all Footer parts. Is included as part of index.php
Related containers: 


Header containers attribute handlers 
for Plethora_Theme::add_container_attr() )

	'content_bottom'
	'footer'
	'footer_top'
	'footer_main'
	'footer_infobar'

=================================

Header template part handlers 
for Plethora_Theme::add_container_part() )

	'content_main_loop_after'
	'content_bottom'
	'footer_top'
	'footer_main'
	'footer_infobar'

*/
				/**
				* 'plethora_content_main_loop_after' hook
				*/
				do_action( 'plethora_content_main_loop_after' ); 

			/**
			* Main Content section close
			*/
			?>
			</div>
			<?php
			Plethora_Theme::dev_comment('  FINISH >> =================== CONTENT MAIN LOOP ==================', 'layout');

			/**
			* Main Content RIGHT section ( Right sidebar )
			*/
			$layout = Plethora_Theme::get_layout();
			if ( $layout === 'right_sidebar' ) { get_sidebar(); }  

		/**
		* Main Content section close
		*/
		?>
				</div>
			</div>
		</div>
		<?php
		Plethora_Theme::dev_comment('  FINISH >> =================== CONTENT MAIN =========================', 'layout');

		/**
		* Bottom content section
		*/
		if ( has_action( 'plethora_content_bottom' ) ) {

			/**
			* Bottom content section open
			*/
			Plethora_Theme::dev_comment('  START >> ==================== CONTENT BOTTOM  ===================', 'layout');
			?> 
			<div<?php echo Plethora_Theme::get_container_attrs( 'content_bottom' ); ?>> 
				<div class="<?php echo Plethora_Theme::get_container_type( 'content' ); ?>">
					<div class="row">
			<?php

					/**
					* Bottom content section hook
					*/
					do_action('plethora_content_bottom');

			/**
			* Bottom content section close
			*/
			?>
					</div>
				</div>
			</div>
			<?php
			Plethora_Theme::dev_comment('  FINISH >> =================== CONTENT BOTTOM ==================', 'layout');
		}	

		?>
	</div>
	<?php 
	Plethora_Theme::dev_comment('  FINISH >> =================== CONTENT  ==================', 'layout');

	 /**
	 * 'plethora_footer_before' hook
	 */
	 if ( has_action( 'plethora_footer_top' ) || has_action( 'plethora_footer_main' ) || has_action( 'plethora_footer_bar' ) ) { 

		// Get header containers type setup ( 'container' OR 'container-fluid' )
		$container_type = Plethora_Theme::get_container_type( $container = 'footer' );

		/**
		* Footer wrapper open
		*/
		Plethora_Theme::dev_comment(' START >> ========================= FOOTER ========================', 'layout');
		?>
		<div<?php echo Plethora_Theme::get_container_attrs( 'footer' ); ?>> 
		<?php 
		
		// Widgets wrapper must be applied only if footer_top and footer_main are displayed
		if ( has_action( 'plethora_footer_top' ) || has_action( 'plethora_footer_main' ) ) { 

			Plethora_Theme::dev_comment(' START >> ========================= FOOTER WIDGETS ========================', 'layout'); ?>
			<div class="footer_widgets">       
		<?php
		}

		 /**
		 * 'plethora_footer_top' hooks
		 */
		 if ( has_action( 'plethora_footer_top' ) ) {
			
			/**
			* Footer Top wrapper open
			*/
			?>
			<div<?php echo Plethora_Theme::get_container_attrs( 'footer_top' ); ?>>
				<div class="<?php echo Plethora_Theme::get_container_type( 'footer_top' ); ?>">       
					<div class="row">       
			<?php

				do_action('plethora_footer_top');

			/**
			* Footer Top wrapper close
			*/
			?>
					</div>
				</div>
			</div>
			<?php
			Plethora_Theme::dev_comment(' << FINISH ==================== FOOTER TOP ====================', 'layout');
		 }

		 /**
		 * 'plethora_footer_main' hooks
		 */
		 if ( has_action( 'plethora_footer_main' ) ) {
			

			/**
			* Footer main section wrapper open
			*/
			Plethora_Theme::dev_comment(' START >> ========================= FOOTER MAIN ========================', 'layout');
			?>
			<div<?php echo Plethora_Theme::get_container_attrs( 'footer_main' ); ?>>
				<div class="<?php echo Plethora_Theme::get_container_type( 'footer_main' ); ?>">       
					<div class="row">       
			<?php

				do_action('plethora_footer_main');

			/**
			* Footer main section wrapper close
			*/
			?>
					</div>
				</div>
			</div>
			<?php
			Plethora_Theme::dev_comment('  << FINISH ==================== FOOTER MAIN ====================', 'layout');
		 }
		// Widgets wrapper must be applied only if footer_top and footer_main are displayed
		if ( has_action( 'plethora_footer_top' ) || has_action( 'plethora_footer_main' ) ) { 
		 	?>
			</div>
		 	<?php   
			Plethora_Theme::dev_comment('  << FINISH ==================== FOOTER WIDGETS ====================', 'layout');
		}

		 /**
		 * 'plethora_footer_bar' hooks
		 */
		 if ( has_action( 'plethora_footer_bar' ) ) {
			

			/**
			* Footer Bar section wrapper open
			*/
			Plethora_Theme::dev_comment(' START >> ========================= FOOTER BAR ========================', 'layout');
			?>
			<div<?php echo Plethora_Theme::get_container_attrs( 'footer_bar' ); ?>>
				<div class="<?php echo Plethora_Theme::get_container_type( 'footer_bar' ); ?>">       
					<div class="row">       
			<?php

				do_action('plethora_footer_bar');

			/**
			* Footer Bar section wrapper close
			*/
			?>
					</div>
				</div>
			</div>
			<?php
			Plethora_Theme::dev_comment('  << FINISH ==================== FOOTER BAR ====================', 'layout');
		 }

		/**
		* Footer wrapper close
		*/
		?>
		</div>
		<?php
		Plethora_Theme::dev_comment('  << FINISH ========================= FOOTER ========================', 'layout');
	} 

	/**
	* 'plethora_body_close' hook
	*/
	do_action('plethora_hidden_markup'); 

	/**
	* Page wrapper close
	*/
	Plethora_Theme::dev_comment('  << FINISH ========================= PAGE WRAPPER ========================', 'layout');
	?>
	</div>
	<?php


	 // Call wp_footer
	 wp_footer(); ?>
</body>
</html>