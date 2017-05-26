<?php
/*
Template Name: safaris template
*/
?>

<?php get_header(); ?>

<div class="background-image-section inner-pages-bg">
	<div class="background-description container">
		<div class="text-center textbox">
			<h1> Safaris </h1>
		</div>
	</div>
	<div class="enquiry-band">
		<img class="enquiry-topi" src="<?php echo get_bloginfo("template_url"); ?>/images/enquiry-topi.svg"/>
		<div class="enquiry-topi"></div>
		<form class="booking-enquiry">
			<div class="date-picker-container">
				<input class="enquiry-date-picker" id="arrival-date" type="text" name="arrival-date" placeholder="Select arrival date">
			</div>
			<div class="date-picker-container">
				<input class="enquiry-date-picker" id="departure-date" type="text" name="departure-date" placeholder="Select departure date">
			</div>
			<input class="enquiry-submit" type="submit" value="Check availability">
		</form>
	</div>
</div>

<div class="first-row safaris-page">
	<div class="row row-eq-height">
		<div class="col-md-6 img-container">
			<img src="<?php echo get_bloginfo("template_url"); ?>/images/slide-1.jpg"/>
		</div>
		<div class="col-md-6 content-carousel-parent">
			<p class="tour-category">EXPERIENCES</p>
	    	<h2 class="tour-title">Satpura Walk Safari</h2>
	    	<span class="leaf-image"><img src="<?php echo get_bloginfo("template_url"); ?>/images/leaf.svg"/></span>
	    	<p class="tour-description">When you’ve got past the preliminaries; there is landscape that opens out like an ancient text to reveal the hieroglyphs of animal presence, there are trees to touch, birds to follow, and an endless series of insect curiosities to stop and wonder at.</p>
		</div>
	</div>
</div>

<?php get_footer(); ?>