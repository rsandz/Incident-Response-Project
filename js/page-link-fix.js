
//This will fix the fact that the pagination for codeignitor only creates the link in the text and not the button.
$(function() 
{
	$('.pagination-link:not(.is-current)').add('.pagination-next').add('.pagination-previous').each(function(index, el) {
		link = $(this).children('a').attr('href');
		$(this).wrap('<a href = "'+ link +'">', '</a>');
	});
})

