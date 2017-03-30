<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

Description: Handles all Header parts. Is included as part of index.php

Header containers attribute handlers 
for Plethora_Theme::add_container_attr() )

	'header', 
	'header_topbar', 
	'header_main'
	'content', 
	'content_top', 
	'content_main'
	'content_main_left'

=================================

Header template part handlers 
for Plethora_Theme::add_container_part() )

	'head_before'
	'body_open'
	'header_topbar'
	'header_main'
	'mediapanel'
	'content_top'
	'content_main_loop_before'
*/
?><!doctype html><?php Plethora_Theme::dev_comment( null, 'page_info' ); ?>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>"><?php 
	/**
	 * 'plethora_head_before' hook
	 *
	 * @hooked Plethora_Theme::head_meta() - 10 ( Meta settings )
	 * @hooked Plethora_Theme::favicons() - 20 ( Favicons )
	 */
	 do_action('plethora_head_before');  

	  // Call wp_head
	 wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php
	 /**
	 * 'plethora_body_open' hook
	 *
	 * @hooked Plethora_Theme::wrapper_overflow_open() - 10 ( overflow wrapper opening div )
	 * 
	 */
	 do_action('plethora_body_open'); 

	/**
	* Main page wrapper open ( closing tag on footer.php )
	*/
	Plethora_Theme::dev_comment(' START >> ========================= PAGE WRAPPER ========================', 'layout');
	?>
	<div<?php echo Plethora_Theme::get_container_attrs( 'page' ); ?>>

	<?php

	 if ( has_action( 'plethora_header_topbar' ) || has_action( 'plethora_header_main' ) ) { 

		/**
		* Header wrapper open
		*/
	 	Plethora_Theme::dev_comment(' START >> ========================= HEADER ========================', 'layout');
		?>
		<div<?php echo Plethora_Theme::get_container_attrs( 'header' ); ?>>
		<?php

		 /**
		 * 'plethora_header_topbar' hooks
		 */
		 if ( has_action( 'plethora_header_topbar' ) ) {
		 	
			/**
			* Header Top Bar wrapper open
			*/
		 	Plethora_Theme::dev_comment(' START >> ========================= HEADER TOP BAR ========================', 'layout');
			?>
			<div<?php echo Plethora_Theme::get_container_attrs( 'header_topbar' ); ?>>
				<div class="<?php echo Plethora_Theme::get_container_type( 'header' ); ?>">
			<?php

		 		do_action('plethora_header_topbar');

			/**
			* Header Top Bar wrapper close
			*/
			?>
				</div>
			</div>
			<?php
	 		Plethora_Theme::dev_comment(' << FINISH ==================== HEADER TOP BAR ====================', 'layout');
		 }

		 /**
		 * 'plethora_header_main' hooks
		 */
		 if ( has_action( 'plethora_header_main' ) ) {
		 	

			/**
			* Header main section wrapper open
			*/
		 	Plethora_Theme::dev_comment(' START >> ========================= HEADER MAIN ========================', 'layout');
			?>
			<div<?php echo Plethora_Theme::get_container_attrs( 'header_main' ); ?>>
				<div class="<?php echo Plethora_Theme::get_container_type( 'header' ); ?>">
			<?php

		 		do_action('plethora_header_main');

			/**
			* Header main section wrapper close
			*/
			?>
				</div>
				<?php
				/**
				* Off-container position: After Header Main Container Markup
				*/
				do_action( 'plethora_header_main_after_container_markup' ); 
				?>			
			</div>
			<?php
	 		Plethora_Theme::dev_comment('  << FINISH ==================== HEADER MAIN ====================', 'layout');
		 }

		/**
		* Header wrapper close
		*/
		?>
		</div>
		<?php
	 	Plethora_Theme::dev_comment('  << FINISH ========================= HEADER ========================', 'layout');
	} 


	/**
	* 'plethora_media_panel' hooks
	*/
	if ( has_action( 'plethora_mediapanel' ) ) {

		/**
		* Media Panel wrapper open ( note that containers are given on media panel template parts )
		*/
		Plethora_Theme::dev_comment(' START >> ========================= MEDIA PANEL ========================', 'layout');
		?>
		<div<?php echo Plethora_Theme::get_container_attrs( 'mediapanel' ); ?>>
		<?php

				do_action('plethora_mediapanel');

		/**
		* Media Panel  wrapper close
		*/
		?>
		</div>
		<?php
		Plethora_Theme::dev_comment('  << FINISH ==================== MEDIA PANEL ====================', 'layout');
	}

	Plethora_Theme::dev_comment('   >> START ========================= CONTENT  ========================', 'layout');
	?>
	<div<?php echo Plethora_Theme::get_container_attrs( 'content' ); ?>>
	<?php

		/**
		* Content titles section
		*/
		if ( has_action( 'plethora_content_titles' ) ) {

			/**
			* Content titles open
			*/
		 	Plethora_Theme::dev_comment('  START >> ==================== CONTENT TITLES  ===================', 'layout');
			?> 
			<div<?php echo Plethora_Theme::get_container_attrs( 'content_titles' ); ?>> 
				<div class="<?php echo Plethora_Theme::get_container_type( 'content' ); ?>">
			<?php

					/**
					* Content titles section hook
					*/
					do_action('plethora_content_titles');

			/**
			* Content titles close
			*/
			?>
				</div>
			</div>
			<?php
		 	Plethora_Theme::dev_comment('  FINISH >> =================== CONTENT TITLES ==================', 'layout');
		}	
		
		/**
		* Top content section
		*/
		if ( has_action( 'plethora_content_top' ) ) {

			/**
			* Top content section open
			*/
		 	Plethora_Theme::dev_comment('  START >> ==================== CONTENT TOP  ===================', 'layout');
			?> 
			<div<?php echo Plethora_Theme::get_container_attrs( 'content_top' ); ?>> 
				<div class="<?php echo Plethora_Theme::get_container_type( 'content' ); ?>">
					<div class="row">
			<?php

					/**
					* Top content section hook
					*/
					do_action('plethora_content_top');

			/**
			* Top content section close
			*/
			?>
					</div>
				</div>
			</div>
			<?php
		 	Plethora_Theme::dev_comment('  FINISH >> =================== CONTENT TOP ==================', 'layout');
		}	

		/**
		* Main Content section open
		*/
		Plethora_Theme::dev_comment('  START >> ========================= CONTENT MAIN ========================', 'layout');
		?> 
		<div<?php echo Plethora_Theme::get_container_attrs( 'content_main' ); ?>> 
			<div class="<?php echo Plethora_Theme::get_container_type( 'content' ); ?>">
				<div class="row">
		<?php

			/**
			* Main Content LEFT ( Left sidebar )
			*/
  			$layout = Plethora_Theme::get_layout();
		  	if ( $layout === 'left_sidebar' ) { get_sidebar(); } 

			/**
			* Main Content LOOP section open
			*/
			Plethora_Theme::dev_comment('  START >> ========================= CONTENT MAIN LOOP ========================', 'layout');
			?> <div<?php echo Plethora_Theme::get_container_attrs( 'content_main_loop' ); ?>> <?php

				/**
				* 'plethora_content_main_loop_before' hook
				*/
				do_action( 'plethora_content_main_loop_before' ); 