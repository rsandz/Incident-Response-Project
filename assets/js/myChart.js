$(function() {
    //Array of the staticChart objects
    staticCharts = [];

    //Initialize all elements with class 'static-chart'
    $("canvas.static-chart").each(function(index) {
        staticCharts[index] = new staticChart(this);
    });

    //Array of Dynamic Charts
    dynamicCharts = [];
    $("canvas.dynamic-chart").each(function(index) {
        console.log('Creating Chart ' + index);
        dynamicCharts[index] = new dynamicChart(this);
    });
});

class chartBase{
    constructor()
    {
        this.colorSets = [{
            backgroundColor: "rgba(255, 58, 58, 0.5)",
            borderColor : 'rgba(135, 13, 13, 0.5)',
            hoverBackgroundColor : "rgba(255, 137, 137, 0.5)",
            hoverBorderColor: "rgba(135, 13, 13, 0.5)",
        },
        {
            backgroundColor : "rgba(50, 14, 200, 0.5)",
            borderColor : "rgba(10,2,90,0.5)",
            hoverBackgroundColor : "rgba(99,21,255,0.9)",
            hoverBorderColor : "rgba(25, 4, 120, 1)"
        },
        {
            backgroundColor : "rgba(13,240,13,0.5)",
            borderColor : "rgba(13,100,13,0.5)",
            hoverBackgroundColor : "rgba(26, 255, 26, 1)",
            hoverBorderColor : "rgba(7, 50, 7, 1)"
        },
        {
            backgroundColor : "rgba(240,240,0,0.5)",
            borderColor : "rgba(120,120,0,0.5)",
            hoverBackgroundColor : "rgba(250, 250, 0, 1)",
            hoverBorderColor : "rgba(60, 60, 0, 1)"
        }];

        this.data;
        this.canvas;
        this.chart;

        this.defaultType = 'bar';

        this.debugMode = true;
    }

    /**
     * Generates the data array for use in the chart 
     * configuration
     */
    generateDataObj(data = this.data)
    {
        var dataObj = {labels: data.x}; //Init dataObj and add x-labels
        var datasets = []; //Contains the datasets
        
        //Create Datasets
        for (var [index, set] of data.dataSets.entries())
        {
            datasets[index] = {
                     label: set.label || 'Series '+ (index + 1),
                     data : set.y,
                     backgroundColor: this.colorSets[index].backgroundColor,
                     borderColor : this.colorSets[index].borderColor,
                     hoverBackgroundColor : this.colorSets[index].hoverBackgroundColor,
                     hoverBorderColor: this.colorSets[index].hoverBorderColor,
                     borderWidth : 1,
                }
        }
        
        //Append data to dataObj
        dataObj.datasets = datasets;
        return dataObj;
    }

    /**
     * Renders the chart on the canvas
     */
    renderChart() {
        if (this.canvas) {
            this.chart = new Chart(this.canvas, {
                parent: this,
                type: this.defaultType,
                data: this.generateDataObj(),
                options: {
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: true,
                                    min: 0,
                                }
                            }
                        ],
                        xAxes:[
                            {
                                ticks:{
                                    minRotation: 35,
                                    maxRotation: 80
                                }
                            }
                        ],
                    },
                    onClick: this.searchForData
                }
            });
        }
        //Debug mode
        if (this.debugMode) {
            console.log(this);
        }
    }

    /**
     * Processes a user click and then creates and runs a search query for the data that the user clicked on.
     * The search data will come from the query array returned by the get data AJAX
     * @param  {event} e The click event
     */
    searchForData(e) {
        try {
            var pointIndex = this.getElementAtEvent(e)[0]._index; //Index of the bar that was clicked on
            var setIndex = this.getElementAtEvent(e)[0]._datasetIndex;
        } catch (err) {
            console.log(err);
            if (err.name == "TypeError") {
                //Type error usually means that the user did not click on a bar
                return; //Just return if did not click on a bar
            } else {
                throw err; // Throw the error again if it is not type error
            }
        }

        //Getting the date of the datapoint clicked
        var fromDate = moment(String(this.data.labels[pointIndex])); //Creating a date must use a string
        var toDate = moment(fromDate);

        //Create to date
        switch (this.chart.config.parent.type) {
            case "daily":
                break;
            case "weekly":
                //Since date search is inclusive, we only look up to 6 days ahead,
                // as to not include next week
                toDate.add(6, 'days');
                break;
            case "monthly":
                toDate.add(1, 'month');
                break;
            case "yearly":
                toDate.add(1, 'year');
                break;
        }

        let query = this.config.parent.data.dataSets[setIndex].query; // The search query

        //Append to form and search
        $("#to_date").val(toDate.format('Y-MM-DD'));
        $("#from_date").val(fromDate.format('Y-MM-DD'));
        $("#query").val(query);
        $("#search-form").submit();
    }

    /**
     * Updates the chart data and the canvas
     */
    updateChart() {

        //Insert new Data
        console.log(this);
        this.chart.config.data = this.generateDataObj();
        this.chart.config.options.scales.yAxes[0].ticks.max;
        this.chart.update();

        //Debug mode
        if (this.debugMode) {
            console.log(this);
        }
        return true;
    }
    showSpinner()
    {
        $(this.canvas).parents('.chart-box').find('i.spinner').show();
    }
    hideSpinner()
    {
        $(this.canvas).parents('.chart-box').find('i.spinner').hide();
    }
}//---------------------- End of Chart Base --------------------


class staticChart extends chartBase{
    constructor(canvas) {
        super();
        this.canvas = canvas;
        this.showSpinner();
        //Parse the data
        this.data = JSON.parse($(canvas).attr('data-chart'));
        this.renderChart();
        this.hideSpinner();
    }


       
} // ---------------- End of Static Chart -----------------------


/**
 * The class that hadles chart creation and modification
 * Contains the chart object
 *
 * The chart can be modified by the user by calling the following methods:
 *  -
 *
 */
class dynamicChart extends chartBase{
    
    /**
     * Constucts the chartManager class.
     * The chart element must contain the following attributes:
     *  - data-ajaxurl      Contains the url to get the data from
     * 
     * @param {obj} canvas Where to display the chart
     */
    constructor(canvas) {
        super();

        this.canvas = canvas;
        this.chartBox = $(canvas).parents('div.chart-box');
        this.ajaxURL = $(canvas).attr('data-ajaxurl');
        this.limit = this.chartBox.find('.limit-input').val() || 10; //Number of datapoints to show
        this.type = this.chartBox.find('.interval-select').val() || "daily";

        this.limitMax = 365;
        this.limitMin = 1;

        this.offset = 0;
        this.chart;

        //Hookup Listeners to the buttons
        this.chartBox.find('.chart-left').click(this.scrollLeft.bind(this));
        this.chartBox.find('.chart-right').click(this.scrollRight.bind(this));
        this.chartBox.find('.jump-button').click(() =>{
            let jumpDate = this.chartBox.find('.jump-date').val();
            this.jumpTo(jumpDate);
        });
        this.chartBox.find('.limit-button').click(() => {
            let limitNum = this.chartBox.find('.limit-num').val();
            this.changeLimit(limitNum);
        });

        //Validate Data
        if (!this.ajaxURL) throw 'Invalid ajax URL received!';

        this.createChart(this.type);
    }

    /**
     * The initialization function for the chart.
     * Gets the data from the server and renders the chart. 
     * Call this to create the chart.
     */
    createChart() {
        this.getData(this.renderChart);
    }

    /**
     * Refreshes the maximum Y-axis value.
     *
     * Use when Data displayed is changing
     *
     * @return {boolean} TRUE if successful
     */
    refreshMaxY(chartData) {
        //First must combine all chart data
        var allData = [];
        for (let data of chartData) {
            allData.push(...data.dataArray.map(x => parseInt(x, 10)));
        }
        this.maxY =
            allData.reduce(function(acc, curr) {
                return Math.max(acc, curr);
            }) + 2;
        return true;
    }

    /*
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
    getData(callback) {
        this.showSpinner();
        var [from_date, to_date] = this.dateFromOffset();
        if (this.debugMode) console.log(`Getting ${this.type} data from ${from_date} to ${to_date}`);
        $.get(this.ajaxURL, 
            { interval_type: this.type, from_date: from_date, to_date: to_date}, 
            (data) => {
                this.data = JSON.parse(data); 
                this.hideSpinner();
            }).done(callback.bind(this));
    }

    dateFromOffset()
    {
        switch (this.type) {
            case 'daily':
                var toDate = moment().add((this.limit + 1) * this.offset, 'd');
                var fromDate = moment(toDate).subtract(this.limit, 'd');
                return [fromDate.format('Y-MM-DD'), toDate.format('Y-MM-DD')];
            case 'weekly':
                var toDate = moment().add((this.limit + 1) * this.offset, 'w');
                var fromDate = moment(toDate).subtract(this.limit, 'w');
                return [fromDate.format('Y-MM-DD'), toDate.format('Y-MM-DD')];
            case 'monthly':
                var toDate = moment().add((this.limit + 1) * this.offset, 'M');
                var fromDate = moment(toDate).subtract(this.limit, 'M');
                return [fromDate.format('Y-MM-DD'), toDate.format('Y-MM-DD')];
            case 'yearly':
                var toDate = moment().add((this.limit + 1) * this.offset, 'y');
                var fromDate = moment(toDate).subtract(this.limit, 'y');
                return [fromDate.format('Y-MM-DD'), toDate.format('Y-MM-DD')];
            default:
                break;
        }
    }

    
    /**
     * Changes the date interval type of the chart.
     * interval type can be: 'daily', 'weekly', monthly', 'yearly'
     */
    changeType(type) {
        this.type = type;
        this.offset = 0;
        if (this.debugMode) console.log("Changing to: " + this.type);
        
        //Clear Data List
        this.dataList = [];  
        this.getData(this.updateChart);
    }

    /**
     * Moves all the datapoints to the left
     * For use with arrow buttons that can shift the graph
     *
     * @Number {amount} The amount of x units to shift the graph by
     */
    scrollLeft(amount = 1) {
        this.offset -= 1;
        console.log('click');
        this.getData(this.updateChart);
    }

    /**
     * Moves all the datapoints to the right
     * For use with arrow buttons that can shift the graph
     *
     * @Number {amount} The amount of x units to shift the graph by
     */
    scrollRight(amount = 1) {
        this.offset += 1;
        this.getData(this.updateChart);
    }

    /**
     * Sets the offset to a specified number based on the jump date given. Will automatically update the graph
     *
     * @param {string} jumpDate The string representing the date that the user wants to jump to.
     */
    jumpTo(jumpDate) {
        if (jumpDate) {
            //If it was set
            var offset = 0;
            var now = moment();
            jumpDate = moment(jumpDate); //Note, since there is no provided timezone, JavaScript assumes it is UTC. Use getUTC*() below

            //Find the offset of the day - depends on type of date interval

            switch (this.type) {
                case "daily":
                    var dateDiff = moment.duration(now.diff(jumpDate));
                    offset = - Math.floor(dateDiff.asDays()/this.limit);
                    break;
                case "weekly":
                    var dateDiff = moment.duration(now.diff(jumpDate));
                    offset = - Math.floor(dateDiff.asWeeks()/this.limit);
                    break;
                case "monthly":
                    var dateDiff = moment.duration(now.diff(jumpDate));
                    offset = - Math.floor(dateDiff.asMonths()/this.limit);
                    break;
                case "yearly":
                    var dateDiff = moment.duration(now.diff(jumpDate));
                    offset = - Math.floor(dateDiff.asYears()/this.limit);
                    break;
            }
            this.offset = offset;
            return this.getData(this.updateChart);
        } else {
            return false; // i.e. Do nothing if the date was not set
        }
    }
    /**
     * Used to change the limit (Amount of bars in the graph.)
     *
     * @param {number} newLimit The new number of graphs that you want to display.
     */
    changeLimit(newLimit) {
        if (newLimit > this.limitMax) {
            this.limit = this.limitMax;
        } else if (newLimit < this.limitMin) {
            this.limit = this.limitMin;
        } else {
            this.limit = newLimit;
        }

        return this.getData(this.updateChart);
    }
}
