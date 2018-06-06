//Chart Data Manager Class
class chartManager
{
	constructor(type, limit = 10) 
	{
		this.limit = limit;
		this.offset = 0;
		this.type = type;
		this.chart = "";
	}

	generateData()
	{
		var dateArray = [];
		var logArray = [];
		console.log(this);
		var dataDates = this.data.logData.map(x => new Date(x.log_date));

		//Correct for timezone, since the above puts the date in UTC
		for (var i = dataDates.length - 1; i >= 0; i--) {
			dataDates[i].setMinutes(dataDates[i].getMinutes() + dataDates[i].getTimezoneOffset());
		}
		switch (this.type)
		{
			case 'daily':
				for (var i = this.limit - 1 + this.offset; i >= this.offset; i--) 
				{
					let currentDate = new Date();
					currentDate.setDate(currentDate.getDate() - i);
					dateArray.push(currentDate);

					//Find the logs on this day
					let index = dataDates.findIndex(x => x.toDateString() == currentDate.toDateString())
					if (index == -1)
					{
						logArray.push(0);
					}
					else
					{
						logArray.push(this.data.logData[index].amount);
					}
				}
				break;
			case 'weekly':
				for (var i = this.limit - 1 + this.offset; i >= this.offset; i--) 
				{
					console.log(this.data);
					let currentDate = new Date();
					//Steping by a week
					currentDate.setDate(currentDate.getDate() - 7*i);

					//Get Closest Monday
					currentDate.setDate(currentDate.getDate() - currentDate.getDay() + 1); //Plus once since we want to start on monday, but JS considers 0 a Sunday
					dateArray.push(currentDate);

					let limitDate = new Date(currentDate.getTime());
					limitDate.setDate(limitDate.getDate() + 7);

					//Find the logs on this day
					let index = dataDates.findIndex(x => (currentDate <= x && x < limitDate))
					if (index == -1)
					{
						logArray.push(0);
					}
					else
					{
						logArray.push(this.data.logData[index].amount);
					}
				}
		}
		return {dateArray:dateArray, logArray: logArray, maxYVal: Math.max.apply(null, logArray) + 2};
	}

	/**
	 * ======================
	 * Chart Making Functions
	 * ======================
	 */

	getData(callback)
	{
		var self = this;
		$.get($('#ajax-link').attr('data')+'/get_user_log_frequency',
		{'interval_type' : this.type}, (data) => {self.data = $.parseJSON(data);})
		.done((data) => {callback(self)});
	}

	createChart()
	{
		this.getData(this.renderChart);
	}

	renderChart(self)
	{
		var chartData = self.generateData();
		if($('#chart').length)
		{
			var canvas =  document.getElementById("chart").getContext("2d");
			self.chart = new Chart(canvas,
			{
				type: 'bar',
				data: 
				{
					labels: chartData.dateArray.map(x => x.toDateString()),
					datasets: [
					{
						label: 'Number of Logs',
						data: chartData.logArray,
						backgroundColor: 'rgba(255, 99, 132, 0.2)',
						borderColor: 'rgba(255,99,132,1)',
						borderWidth: 1
					}]
				},
				options: 
				{
			        scales: 
			        {
			            yAxes: 
			            [{
			                ticks: {
			                    beginAtZero:true,
			                    min: 0,
			                    max: chartData.maxYVal,
			                }
			            }]
			        },
				}
			});
			self.offset = 0;
		}
	}

	changeType(type)
	{
		this.type = type;
		this.offset = 0;
		console.log('Changing to: '+this.type);
		this.getData(this.updateChart);
	}

	updateChart(self = this) //TODO readd the spinner
	{
		switch (self.type)
		{
			case 'daily':
				var chartData = self.generateData();
				var label_array = chartData.dateArray.map(x => x.toDateString());
				break;
			case 'weekly':
				var chartData = self.generateData();
				var label_array = chartData.dateArray.map(x => x.toDateString());
				break;
			case 'monthly':
			case 'yearly':
				break;
			default:
				console.log('Interval Error');
				break;
		}

		self.chart.data.labels = label_array;
		self.chart.data.datasets[0].data = chartData.logArray;
		self.chart.options.scales.yAxes[0].ticks.max = chartData.maxYVal;
		self.chart.update();
	}

}

// Chart Variables
var manager = new chartManager('daily');

$(function()
	{
		manager.createChart('daily');

		$('#chart-left').click(function() {
			manager.offset += 1;
			manager.updateChart();
		});

		$('#chart-right').click(function() {
			manager.offset -= 1;
			manager.updateChart();
		});

		$('#interval_type').change(function() {
			manager.changeType($('#interval_type').val());
		})
	});



