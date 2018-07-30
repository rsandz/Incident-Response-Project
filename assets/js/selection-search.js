/* This script handles the search box in various selection pages in the site*/

$(function() {
	$('#selection-search').keyup(function() {
		updateSelection($(this).val());
	});
});

function updateSelection(keywords) 
{
	$('.selection-title').each(function()
			{
				let pattern = new RegExp(keywords, 'i');
				if (pattern.test($(this).html()))
				{
					$(this).parentsUntil('columns', '.selection-item').show();
				}
				else
				{
					$(this).parentsUntil('columns', '.selection-item').hide();
				}
			}
		);
}