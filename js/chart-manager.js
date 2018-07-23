/**
 * The class that hadles chart creation and modification
 * Contains the chart object
 *
 * The chart can be modified by the user by calling the following methods:
 *  - 
 * 
 */
class chartManager
{

	/**
	 * Constucts the chartManager class
	 *
	 * Provide a configuration such that
	 * @param {object} config = {
	 * 		chartType, //'hours' or 'logs'
	 * 		intervalType, //'daily', 'weekly', 'monthly', 'yearly' (Default Daily)
	 * 		ajaxURL, //The Ajax URL to get data from
	 * 		canvas, //Selector for the canvas
	 *
	 * 		//Optional
	 * 		limit, //Amount of Bars to show (Default 10). Can be changed by the user
	 * 		limitMax, //Maximum amount of bars to show. Ceiling of limit if limit was changed by the user (Default 365)
	 * 		limitMin, //Minimum amount of bars to show. Floor of limit if limit was changed by the user (Default 1)
	 * }
	 */
	constructor(config) 
	{
		/*
		 *	Configuration 
		 */

		this.debugMode = true; //Set to true to log the chart manager object to the console.
		this.config = {
			logs :
			{
				dataName : 'Number of Logs'	,
				chartFormat : 'bar'
			},
			hours :
			{
				dataName : 'Number of hours',
				chartFormat : 'line',
				lineTension : 0
			}
		};

		this.chartType = config.chartType;
		this.canvas = config.canvas;
		this.ajaxURLs = config.ajaxURLs;

		this.limit = config.limit || 10;
		this.type = config.intervalType || 'daily';
		this.limitMax = config.limitMax || 365;
		this.limitMin = config.limitMin || 1;
		this.maxY = 0;
		this.dataList = [];

		this.offset = 0;
		this.chart = "";
		this.createChart(this.type);

		
	}

	/**
	 * Generates the data to graph
	 *
	 * Provide a data object with the following:
	 * 	{property} stats (Must contain 'x' property and 'y' property. X and Y axes respectively)
	 *
	 * @return {object} An object containing the arrays of data to plot and misc data
	 */
	generateData(data)
	{
		var dateArray = []; //Array of all the sates (X-axis)
		var dataArray = []; //Array of the data points (Y-axis)
		var dataDates = data.stats.map(dataItem => new Date(dataItem.x)); //Dates from Ajax call
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
						dataArray.push(0);
					}
					else
					{
						dataArray.push(data.stats[index].y);
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
						dataArray.push(0);
					}
					else
					{
						dataArray.push(data.stats[index].y);
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
						dataArray.push(0);
					}
					else
					{
						dataArray.push(String(data.stats[index].y));
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
						dataArray.push(0);
					}
					else
					{
						dataArray.push(data.stats[index].y);
					}
				}
				break;
		}

		//Remove any 'null's from dataArray and turn them into zeros
		for(let data in dataArray)
		{
			if (dataArray[data] == null)
			{
				dataArray[data] = 0;
			}
		}

		return {dateArray:dateArray, dataArray: dataArray, label: data.name};
	}

	/**
	 * Generates the chart data from the dataList in the chart manager
	 * 
	 * data is the array in the chart config that contains all the data sets
	 * and x-axis labels
	 *
	 * @return {object} The data object
	 */
	generateChartData()
	{
		var chartData = [];
		for(let data of this.dataList)
		{
			chartData.push(this.generateData(data));
		}
		//Get Background Colours
		var backgroundColours = this.getBackgroundColours();
		//Create DataSets
		var dataSets = [];
		for(let [index, data] of chartData.entries())
		{
			dataSets.push({
				label:  data.label || this.config[this.chartType].dataName,
				data: data.dataArray,
				backgroundColor: backgroundColours[index],
				borderColor: backgroundColours[index],
				pointHoverBackgroundColor: 'rgba(0, 0, 200, 0.5)',
				hoverBackgroundColor: 'rgba(0, 0, 200, 0.5)',
				hoverBorderColor: 'rgba(0, 0, 200, 0.5)',
				borderWidth: 1,
				lineTension: this.config[this.chartType].lineTension
			})
		}

		this.refreshMaxY(chartData);

		//Labels (The x-axis values)
		let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
		switch (this.type)
		{
			case 'daily':
			case 'weekly':
				var label_array = chartData[0].dateArray.map(x => x.toDateString());
				break;
			case 'monthly':
				var label_array = chartData[0].dateArray.map(x => (months[x.getMonth()]+ " " + x.getFullYear()));
				break;
			case 'yearly':
				var label_array = chartData[0].dateArray.map(x => x.getFullYear());
				break;
			default:
				console.log('Interval Error');
				break;
		}
		return {labels : label_array, datasets : dataSets};
	}

	/**
	 * Gets or sets background colours based on whether
	 * they exist or not.
	 *
	 * @return {array} The array of background colours.
	 * 
	 */
	getBackgroundColours()
	{
		if (!this.backgroundColours)
		{
			this.backgroundColours = [];
			//Make background colours
			for(let index in this.ajaxURLs)
			{
				this.backgroundColours.push('hsla(' + Math.random() * 360 +', 100%, 77%, 0.8)');
			}
		}

		return this.backgroundColours;
	}

	/**
	 * Refreshes the maximum Y-axis value.
	 *
	 * Use when Data displayed is changing
	 *
	 * @return {boolean} TRUE if successful
	 */
	refreshMaxY(chartData)
	{
		//First must combine all chart data
		var allData = [];
		for (let data of chartData)
		{
			allData.push(...data.dataArray.map(x => parseInt(x, 10)));
		}
		this.maxY = allData.reduce(function(acc, curr) 
			{
				return Math.max(acc, curr);
			}) + 2;
		return true;
	}

	/**
	 * ======================
	 * Chart Making Functions
	 * ======================
	 */

	 /**
	  * Grabs chart data from the server based on the property 'type'
	  *
	  * @param {function} callback - The callback when the data is recieved from the server
	  * 
	  */
	getData(callback)
	{
		if (Array.isArray(this.ajaxURLs))
		{
			var requests = [];
			for (let url of this.ajaxURLs)
				{
					requests.push($.get
					(
						url,
						{'interval_type' : this.type},
						(data) => this.dataList.push(JSON.parse(data))
					));
				}
			$.when(...requests).then(callback.bind(this));
		}
		else
		{
			$.get
			(
				this.ajaxURLs,
				{'interval_type' : this.type}, (data) => this.dataList.push(JSON.parse(data))
			).done(callback.bind(this));
		}
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
		var chartData = this.generateChartData();
		
		if(this.canvas.length)
		{
			this.chart = new Chart(this.canvas,
			{
				parent : this,
				type: this.config[this.chartType].chartFormat,
				data: chartData,
				options: 
				{
			        scales: 
			        {
			            yAxes: 
			            [{
			                ticks: {
			                    beginAtZero:true,
			                    min: 0,
			                    max: this.maxY,
			                }
			            }]
			        },
			        onClick: this.searchForData
				}
			});
			this.offset = 0;

			//Debug mode
			if (this.debugMode)
			{
				console.log(this);
			}

			return true;
		}
	}

	/**
	 * Processes a user click and then creates and runs a search query for the data that the user clicked on.
	 * The search data will come from the query array returned by the get data AJAX
	 * @param  {event} e The click event
	 */
    searchForData(e) //For loading the graph search
    {
    	try
    	{
    		var index = this.getElementAtEvent(e)[0]._index; //Index of the bar that was clicked on
    		var dataSetIndex = this.getElementAtEvent(e)[0]._datasetIndex;
    	}
    	catch(err) 
    	{
    		console.log(err);
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
    		query : this.config.parent.dataList[dataSetIndex].query, // The search query
    		amount : this.data.datasets[dataSetIndex].data[index]
    		};
    	$('#to_date').val(elementData.toDate);
    	$('#from_date').val(elementData.fromDate);
    	$('#query').val(elementData.query);
    	$('#search-form').submit();
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
		//Clear Data List
		this.dataList = [];
		return this.getData(this.updateChart);
	}

	/**
	 * Updates the chart data and the canvas
	 */
	updateChart() //TODO readd the spinner
	{
		var chartData = this.generateChartData();

		//Remove previous data
		this.chart.config.data.labels = [];
		this.chart.config.data.datasets = []; 
		this.chart.update();

		//Insert new Data
		this.chart.data = chartData;
		this.chart.options.scales.yAxes[0].ticks.max = this.maxY;
		this.chart.update();

		//Debug mode
		if (this.debugMode)
		{
			console.log(this);
		}
		return true;
	}

	/**
	 * Moves all the datapoints to the left
	 * For use with arrow buttons that can shift the graph
	 * 
	 * @Number {amount} The amount of x units to shift the graph by
	 */
	scrollLeft(amount = 1)
	{
		this.offset += 1;
		this.updateChart();
	}

	/**
	 * Moves all the datapoints to the right
	 * For use with arrow buttons that can shift the graph
	 * 
	 * @Number {amount} The amount of x units to shift the graph by
	 */
	scrollRight(amount = 1)
	{
		this.offset -= 1;
		this.updateChart();
	}

	/**
	 * Sets the offset to a specified number based on the jump date given. Will automatically update the graph
	 *
	 * @param {string} jumpDate The string representing the date that the user wants to jump to.
	 */
	jumpTo(jumpDate)
	{
		if (jumpDate) //If it was set
		{
			var offset = 0;
			var now = new Date();
			jumpDate = new Date(jumpDate); //Note, since there is no provided timezone, JavaScript assumes it is UTC. Use getUTC*() below

			//Find the offset of the day - depends on type of date interval

			switch(this.type)
			{
				case 'daily':
					offset = Math.round((Date.now() - jumpDate) / 86400000); //Divide to go from ms to days
					break;
				case 'weekly':
					offset = Math.round(Math.round((Date.now() - jumpDate) / 86400000) / 7); //Get day offset and divide by 7!
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
			this.offset = offset;
			return this.updateChart();
		}
		else
		{
			return false; // i.e. Do nothing if the date was not set
		}
	}
	 /**
	  * Used to change the limit (Amount of bars in the graph.)
	  *
	  * @param {number} newLimit The new number of graphs that you want to display.
	  */
	changeLimit(newLimit)
	{
		if (newLimit > this.limitMax)
		{
			this.limit = this.limitMax;
		}
		else if(newLimit < this.limitMin)
		{
			this.limit = this.limitMin;
		}
		else
		{
			this.limit = newLimit;
		}

		return this.updateChart();
	}

}
