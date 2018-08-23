/* This script handles the search box in various selection pages in the site*/

$(function() {
	$('#selection-search').keyup(function() {
		updateSelection($(this).val());
	});

	$('#select-display').change(updateDisplay)
});

//During Searching
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


//Change Display Format
	function updateDisplay(){
		console.log($(this).val());
		if ($(this).val() == 'bars') {
			$('.selection-item').removeClass('is-4');
			$('.selection-item').addClass('is-12');
		}
		else {
			$('.selection-item').addClass('is-4');
			$('.selection-item').removeClass('is-12');
		}
	}