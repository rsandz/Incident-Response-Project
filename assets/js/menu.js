$(function() {
	$('.navbar-burger').click(function() {
		$(this).toggleClass('is-active');
		$('.navbar-menu').toggleClass('is-active');
	});
});