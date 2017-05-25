<?php
/*
Template Name: homepage template
*/
?>

<div class="pre-primary-menu">
	<ul class="second-menu">
	  	<li><a class="fb png-img" href="https://www.facebook.com/forsythsatpura" target="_blank"></a></li>
		<li><a class="twitter png-img" href="https://twitter.com/ForsythLodge" target="_blank"></a></li>
		<li><a class="insta png-img" href="https://www.instagram.com/forsythlodge" target="_blank"></a></li>
		<li class="book-now-btn"><a class="book-now" href="https://www.secure-booking-engine.com/booking/DULFjS_gRRAmFy3hUf4mvgYEIEa8yE2YEyf_9yU9AaOGW0BdCPc-tQ3mnfrGdsjL/1206/" target="_blank">Book Now</a></li>
	</ul>
</div>

<?php get_header(); ?>

<div class="background-image-section">
	<div class="background-description container">
		<div class="textbox">
			<h1>
				Satpura National Park<br>
				Highlands of Central Asia
			</h1>
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


<div class="welcome-section">
	<div class="container">
		<div class="col-md-offset-2 col-md-8">
			<div class="text-over-title">
				<h1>EXPERIENCES</h1>
				<h2>Discover the Ultimate In Wilderness Luxury</h2>
			</div>
		<!-- 	<div class="leaf-image"><img src="<?php echo get_bloginfo("template_url"); ?>/images/leaf.svg"/></div>
			<p class="description">Forsyth’s is a small wildlife lodge set in 44 ac\res of reclaimed jungle at the edge of the Satpura Tiger Reserve. We focus on a meaningful, sustainable wildlife and wilderness experience and this is reflected in the services we offer, in the property we manage, and in our partnerships with the park administration and the local community.</p>
			<div class="button-wrapper">
				<a href="#" class="filled-button">About Forsyth Ledge</a>
			</div> -->
		</div>
	</div>
</div>

<?php include 'slider-section.php'; ?>



<?php get_footer(); ?>

