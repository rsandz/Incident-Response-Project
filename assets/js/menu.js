/**
 *  Javascript for making the mobile menu toggle when the burger is clicked on.
 *  @author Ryan Sandoval
 */
$(function() {
	$('.navbar-burger').click(function() {
		$(this).toggleClass('is-active');
		$('.navbar-menu').toggleClass('is-active');
	});
});