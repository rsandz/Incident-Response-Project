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
				break;
			case 'monthly':
				for (var i = this.limit - 1 + this.offset; i >= this.offset; i--) 
				{
					let currentDate = new Date();
					//Steping by a week
					currentDate.setMonth(currentDate.getMonth() - i);

					//Remove Time and Day portions
					currentDate.setHours(0,0,0,0);
					currentDate.setDate(1);

					dateArray.push(currentDate);

					let limitDate = new Date(currentDate.getTime());
					limitDate.setMonth(currentDate.getMonth() + 1);

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
				break;
				case 'yearly':
				for (var i = this.limit - 1 + this.offset; i >= this.offset; i--) 
				{
					let currentDate = new Date();
					//Steping by a week
					currentDate.setFullYear(currentDate.getFullYear() - i);

					//Remove Time and Day portions
					currentDate.setHours(0,0,0,0);
					currentDate.setMonth(0,1);

					dateArray.push(currentDate);

					let limitDate = new Date(currentDate.getTime());
					limitDate.setFullYear(currentDate.getFullYear() + 1);

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
				console.log(dataDates);
				break;
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
		$.get($('#ajax-link').attr('data')+'/get_user_log_frequency',
		{'interval_type' : this.type}, (data) => {this.data = $.parseJSON(data);})
		.done(callback.bind(this));
	}

	createChart()
	{
		this.getData(this.renderChart);
	}

	renderChart()
	{
		console.log(this);
		var chartData = this.generateData();
		if($('#chart').length)
		{
			var canvas =  document.getElementById("chart").getContext("2d");
			this.chart = new Chart(canvas,
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
			        onClick: function(e) //For loading the graph search
			        {
			        	var index = this.getElementAtEvent(e)[0]._index;
			        	var dateObj = new Date(this.data.labels[index]);
			        	var elementData = {
			        		date : dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1) + "-" + dateObj.getDate(),
			        		//PLUS 1 for the month since js months go from 0-11 shile sql months go from 1-12
			        		amount : this.data.datasets[0].data[index]
			        		};
			        	console.log();
			        	$('#to_date').val(elementData.date);
			        	$('#from_date').val(elementData.date);
			        	$('#search-form').submit();
			        }
				}
			});
			this.offset = 0;
		}
	}

	changeType(type)
	{
		this.type = type;
		this.offset = 0;
		console.log('Changing to: '+this.type);
		this.getData(this.updateChart);
	}

	updateChart() //TODO readd the spinner
	{
		let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
		switch (this.type)
		{
			case 'daily':
			case 'weekly':
				var chartData = this.generateData();
				var label_array = chartData.dateArray.map(x => x.toDateString());
				break;
			case 'monthly':
				var chartData = this.generateData();
				var label_array = chartData.dateArray.map(x => (months[x.getMonth()]+ " " + x.getFullYear()));
				break;
			case 'yearly':
				var chartData = this.generateData();
				var label_array = chartData.dateArray.map(x => x.getFullYear());
				console.log(chartData);
				break;
			default:
				console.log('Interval Error');
				break;
		}

		this.chart.data.labels = label_array;
		this.chart.data.datasets[0].data = chartData.logArray;
		this.chart.options.scales.yAxes[0].ticks.max = chartData.maxYVal;
		this.chart.update();
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



