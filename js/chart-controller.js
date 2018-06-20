$(function()
	{
		var managers = 
			[
				new chartManager('logs', $('#interval_type1').val(), $('#logs-chart')),
			 	new chartManager('hours', $('#interval_type2').val(), $('#hours-chart'))
			];
		console.log('#chart-left'+1);
		//Events for charts
		for (var i = 1; i <= managers.length; i++)
		{
			$('#chart-left'+i).click(function(i) 
				{
					return function()
					{
						managers[i - 1].offset += 1;
						managers[i - 1].updateChart();
					}
				}(i)
			);

			$('#chart-right'+i).click(function(i) {
					return function()
					{
						managers[i - 1].offset -= 1;
						managers[i - 1].updateChart();
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



