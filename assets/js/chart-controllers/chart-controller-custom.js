$(function()
	{
		var managers = 
			[
				new chartManager(
					{
						chartType: 'logs',
						intervalType: $('#interval_type1').val(),
						ajaxURLs: $('#ajax-link').attr('data') + '/get_custom_log_frequency/' + $('#query_index').val(),
						canvas: $('#logs-chart')
					}),
			 	new chartManager(
					{
						chartType: 'hours',
						intervalType: $('#interval_type2').val(),
						ajaxURLs: $('#ajax-link').attr('data') + '/get_custom_hours/' + $('#query_index').val(),
						canvas: $('#hours-chart')
					})
			];
		//Events for charts
		for (var i = 1; i <= managers.length; i++)
		{
			$('#chart-left'+i).click(function(i) 
				{
					return function()
					{
						managers[i - 1].scrollLeft();
					}
				}(i)
			);

			$('#chart-right'+i).click(function(i) {
					return function()
					{
						managers[i - 1].scrollRight();
					}
				}(i)
			);

			$('#interval_type'+i).change(function(i) {
				return function()
					{
						managers[i - 1].changeType($('#interval_type'+i).val());
					}
				}(i)
			);

			$('#jump'+i).click(function(i) {
					return function()
					{
						console.log('Jumping chart '+i+' on date '+ $('#jump-date'+i).val());
						managers[i - 1].jumpTo($('#jump-date'+i).val());
					}
				}(i)
			);
			$('#limit'+i).click(function(i) 
				{
					return function()
					{
						managers[i - 1].changeLimit($('#limit-num'+i).val());
					}
				}(i)
			);
		}

	});



