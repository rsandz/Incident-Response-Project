/*
 *	Configuration 
 */

var debugMode = false; //Set to true to log the chart manager object to the console.


/**
 * The class that hadles chart creation and modification
 * Contains the chart object
 */
class chartManager
{
	/**
	 * Creates the chart manager
	 * @param {string} type - The date interval type i.e. 'Daily', 'Weekly', 'Monthly', 'Yearly'
	 * @param {number} limit - The number of bars to display
	 */
	constructor(type, limit = 10) 
	{
		this.limit = limit;
		this.limitMax = 365;
		this.limitMin = 1;
		this.offset = 0;
		this.type = type;
		this.chart = "";

		this.createChart(this.type);
	}

	/**
	 * Generates the data to graph
	 *
	 * @return {object} An object containing the arrays of data to plot and misc data
	 */
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
					//Steping by a Month
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
					//Steping by a Year
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
				break;
		}
		return {dateArray:dateArray, logArray: logArray, maxYVal: Math.max.apply(null, logArray) + 2};
	}

	/**
	 * ======================
	 * Chart Making Functions
	 * ======================
	 */

	 /**
	  * Grabs chart data from the server based on the property 'type'
	  *
	  * @param {functio} callback - The callback when the data is recieved from the server
	  * 
	  */
	getData(callback)
	{
		$.get($('#ajax-link').attr('data')+'/get_user_log_frequency',
		{'interval_type' : this.type}, (data) => {this.data = $.parseJSON(data);})
		.done(callback.bind(this));
	}

	/**
	 * The initialization function for the chart. 
	 * Gets the data from the server and renders the chart. Use this to create the chart.
	 */
	createChart()
	{
		this.getData(this.renderChart);
		return true;
	}

	/**
	 * Renders the chart on the canvas
	 */
	renderChart()
	{
		var chartData = this.generateData();
		if($('#chart').length)
		{
			var canvas =  document.getElementById("chart").getContext("2d");
			this.chart = new Chart(canvas,
			{
				parent : this,
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
			        onClick: 
			        	/**
			        	 * Processes a user click and then creates and runs a search query for the data that the user clicked on.
			        	 * @param  {event} e The click event
			        	 */
				        function(e) //For loading the graph search
				        {
				        	try
				        	{
				        		var index = this.getElementAtEvent(e)[0]._index; //Index of the bar that was clicked on
				        	}
				        	catch(err) 
				        	{
				        		if (err.name == 'TypeError') //Type error usually means that the user did not click on a bar
				        		{
				        			return; //Just return if did not click on a bar
				        		}
				        		else
				        		{
				        			throw err; // Throw the error again if it is not type error
				        		}
				        	}

				        	var dateObj = new Date(String(this.data.labels[index])); //Creating a date must use a string
				        	var toDateObj = new Date(dateObj.getTime());

				        	switch(this.chart.config.parent.type)
				        	{
				        		case 'daily':
				        			break;
				        		case 'weekly':
				        			toDateObj.setDate(toDateObj.getDate() + 6); //6 Days, as to not include the 1st day of the next week
				        			break;
				        		case 'monthly':
				        			toDateObj.setMonth(toDateObj.getMonth() + 1, 0); 
				        			//The zero argument sets the day to the last day of the current month and not the first day of the next month
				        			// i.e. If the 0 argument was not passed, the date range would be from Jan 01 to Feb 01 rather 
				        			break;
				        		case 'yearly':
				        			toDateObj.setFullYear(toDateObj.getFullYear() + 1);
				        			break;

				        	}

				        	var elementData = {
				        		fromDate : dateObj.getFullYear() + "-" + (dateObj.getMonth() + 1) + "-" + dateObj.getDate(),
				        		toDate : toDateObj.getFullYear() + "-" + (toDateObj.getMonth() + 1) + "-" + toDateObj.getDate(),
				        		//PLUS 1 for the month since js months go from 0-11 shile sql months go from 1-12
				        		amount : this.data.datasets[0].data[index]
				        		};

				        	console.log(elementData);
				        	$('#to_date').val(elementData.toDate);
				        	$('#from_date').val(elementData.fromDate);
				        	$('#search-form').submit();
				        }
				}
			});
			this.offset = 0;

			//Debug mode
			if (debugMode)
			{
				console.log(this);
			}

			return true;
		}
	}

	/**
	 * Changes the date interval type of the chart.
	 * interval type can be: 'daily', 'weekly', monthly', 'yearly'
	 */
	changeType(type)
	{
		this.type = type;
		this.offset = 0;
		console.log('Changing to: '+this.type);
		return this.getData(this.updateChart);
	}

	/**
	 * Updates the chart data and the canvas
	 */
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
		return true;
	}

	/**
	 * Sets the offset to a specified number based on the jump date given. Will automatically update the graph
	 */
	jumpTo()
	{
		var jumpDate = $('#jump-date').val(); 
		if (jumpDate) //If it was set
		{
			var offset = 0;
			var now = new Date();
			jumpDate = new Date(jumpDate); //Note, since there is no provided timezone, JavaScript assumes it is UTC. Use getUTC*() below

			//Find the offset of the day - depends on type of date interval

			switch(this.type)
			{
				case 'daily':
					offset = Math.floor((Date.now() - jumpDate) / 86400000); //Divide to go from ms to days
					break;
				case 'weekly':
					offset = Math.floor(Math.floor((Date.now() - jumpDate) / 86400000) / 7); //Get day offset and divide by 7!
					break;
				case 'monthly':
					//offset = Math.floor(Math.floor((Date.now() - jumpDate) / 86400000) / 30.5); //Get day offset and divide by 30.5 (Average days per month)!
					//The above is not very accurate
					
					let monthDiff = now.getMonth() - jumpDate.getUTCMonth();
					let yearDiff = now.getFullYear() - jumpDate.getUTCFullYear();
					offset = (monthDiff + yearDiff * 12);
					break;
				case 'yearly':
					offset = now.getFullYear() - jumpDate.getUTCFullYear();
					break;
			}
			console.log(offset);
			this.offset = offset;
			console.log(jumpDate);
			return this.updateChart();
		}
		else
		{
			return false; // i.e. Do nothing if the date was not set
		}
	}

	changeLimit()
	{
		if ($('#limit-num').val() > this.limitMax)
		{
			this.limit = this.limitMax;
		}
		else if($('#limit-num').val() < this.limitMin)
		{
			this.limit = this.limitMin;
		}
		else
		{
			this.limit = $('#limit-num').val();
		}

		return this.updateChart();
	}

}


$(function()
	{
		var manager = new chartManager($('#interval_type').val());
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

		$('#jump').click(() => manager.jumpTo());
		$('#limit').click( () => manager.changeLimit());
	});



