//myChart.js - Created by Ryan Sandoval

//----------------Start of Initialization Script -------------//
$(function() {
    //Initialize all elements with class 'static-chart'
    staticCharts = [];
    $("canvas.static-chart").each(function(index) {
        staticCharts[index] = new staticChart(this);
    });

    //Initialize Dynamic Charts
    dynamicCharts = [];
    $("canvas.dynamic-chart").each(function(index) {
        console.log("Creating Chart " + index);
        dynamicCharts[index] = new dynamicChart(this);
    });
});

//----------------End of Initialization Script -------------//

/**
 * Base Class for charts
 * @author Ryan Sandoval
 * @package Chart
 * 
 * This class is extended by other chart classes.
 * It provides methods for rendering and data parsing.
 *
 * It also has a method for searching what a user clicked on,
 * if 'views/stats/graph-search-template' is loaded.
 * 
 * @uses moment See moment.js docs
 */
class chartBase {
    /**
     * Creates the Chart base.
     * Contains the property for setting debud mode on or off
     */
    constructor(canvas) {
        //Colors for the DataSets. 1st dataset uses first in array, 2nd uses second in array...
        this.colorSets = [
            {
                backgroundColor: "rgba(50, 14, 200, 0.5)",
                borderColor: "rgba(10,2,90,0.5)",
                hoverBackgroundColor: "rgba(99,21,255,0.9)",
                hoverBorderColor: "rgba(25, 4, 120, 1)"
            },
            {
                backgroundColor: "rgba(255, 58, 58, 0.5)",
                borderColor: "rgba(135, 13, 13, 0.5)",
                hoverBackgroundColor: "rgba(255, 137, 137, 0.5)",
                hoverBorderColor: "rgba(135, 13, 13, 0.5)"
            },
            {
                backgroundColor: "rgba(13,240,13,0.5)",
                borderColor: "rgba(13,100,13,0.5)",
                hoverBackgroundColor: "rgba(26, 255, 26, 1)",
                hoverBorderColor: "rgba(7, 50, 7, 1)"
            },
            {
                backgroundColor: "rgba(240,240,0,0.5)",
                borderColor: "rgba(120,120,0,0.5)",
                hoverBackgroundColor: "rgba(250, 250, 0, 1)",
                hoverBorderColor: "rgba(60, 60, 0, 1)"
            }
        ];

        /** 
         * @prop {obj} data 
         * Chart Data
         * Needs: 
         * data.dataSets[n].query - Contains the search query
         * data.dataSets[n].y     - Y vals to graph
         * data.dataSets[n].total - Total data points
         * x                      - X vals to graph
         * * @see statistic_model
         */
        this.data;

        this.canvas = canvas;
        this.chartWrapper = $(canvas).parents('.chart-wrapper');
        this.chartWrapper;
        this.chart;

        this.defaultType = "bar";

        /* Set to true to console.log() the chart object after rendering */
        this.debugMode = true;
    }

    /**
     * Generates the data array for use in the chart
     * configuration.
     * @param {obj} data The chart data. @see data above for what this needs
     * @return {obj} The data object to be inserted into chart.data
     */
    generateDataObj(data = this.data) {
        var datasets = []; //Contains the datasets

        //Create Datasets
        for (var [index, set] of data.dataSets.entries()) {
            datasets[index] = {
                label: set.label || "Series " + (index + 1),
                data: set.y,
                backgroundColor: this.colorSets[index].backgroundColor,
                borderColor: this.colorSets[index].borderColor,
                hoverBackgroundColor: this.colorSets[index]
                    .hoverBackgroundColor,
                hoverBorderColor: this.colorSets[index].hoverBorderColor,
                borderWidth: 1
            };
        }

        //Append data to dataObj
        var dataObj = {
            labels: data.x,
            datasets: datasets
        };
        return dataObj;
    }

    /**
     * Renders the chart on the canvas using Chart.js
     */
    renderChart() {
        if (!this.canvas) {
            throw "Canvas is not Set!";
        }

        //Refer to Chart.js docs for this
        this.chart = new Chart(this.canvas, {
            parent: this, //Refers to the chart object itself
            type: this.defaultType,
            data: this.generateDataObj(),
            options: {
                scales: {
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: true,
                                min: 0
                            }
                        }
                    ],
                    xAxes: [
                        {
                            ticks: {
                                minRotation: 35,
                                maxRotation: 80
                            }
                        }
                    ]
                },
                onClick: this.searchForData
            }
        });

        //Debug mode
        if (this.debugMode) {
            console.log(this);
        }
    }

    /**
     * Search for data user clicked on.
     * Requires (/application/view/stats/graph-search-form).
     * 
     * This will submit a form containing 'from_date', 'to_date', and 'query' to site.com/search/results
     *      query comes from the statistic_model during the get_data() ajax call.
     * @param  {event} e The click event
     */
    searchForData(e) {
        //We use a try block to see whether the user clicked on a datapoint or simply the background
        try {
            var pointIndex = this.getElementAtEvent(e)[0]._index;
            var datasetIndex = this.getElementAtEvent(e)[0]._datasetIndex;
        } catch (err) {
            if (err.name == "TypeError") {
                //Type error usually means that the user did not click on a bar
                return; //Just return if did not click on a bar
            } else {
                throw err;
            }
        }

        //Getting the date of the datapoint clicked
        var fromDate = moment(String(this.data.labels[pointIndex]));
        
        //Create toDate
        var toDate = moment(fromDate);
        switch (this.chart.config.parent.type) {
            case "daily":
                break;
            case "weekly":
                //Since date search is inclusive, we only look up to 6 days ahead,
                // as to not include next week
                toDate.add(6, "days");
                break;
            case "monthly":
                toDate.add(1, "month");
                break;
            case "yearly":
                toDate.add(1, "year");
                break;
        }

        // The search query export string received from statistics model
        let query = this.config.parent.data.dataSets[datasetIndex].query; 

        //Update the form and search
        $("#to_date").val(toDate.format("Y-MM-DD"));
        $("#from_date").val(fromDate.format("Y-MM-DD"));
        $("#query").val(query);
        $("#search-form").submit();
    }

    /**
     * Updates the chart canvas using this.generateDataObj() again
     * Note: This does not automatically fetch the new data from the server
     *       Run for getData(updateChart) for that
     */
    updateChart() {
        //Insert new Data
        console.log(this);
        this.chart.config.data = this.generateDataObj();
        this.chart.config.options.scales.yAxes[0].ticks.max;
        this.chart.update();

        if (this.debugMode) {
            console.log(this);
        }
        return true;
    }

    /**
     * Shows the spinner (if it exists in the chart wrapper).
     */
    showSpinner() {
        $(this.chartWrapper)
            .find("i.spinner")
            .show();
    }

    /**
     * Hides the spinner
     */
    hideSpinner() {
        $(this.chartWrapper)
            .find("i.spinner")
            .hide();
    }
} //---------------------- End of Chart Base --------------------

/**
 * Class for creating a static Chart
 * @extends chartBase
 */
class staticChart extends chartBase {
    
    /**
     * Constructs staticChart
     * The canvas element must contain the attribute 'data-chart' with chart data within
     * @param {selector} canvas Where to display Chart
     */
    constructor(canvas) {
        super(canvas);
        this.showSpinner();

        //Parse the data
        this.data = JSON.parse($(canvas).attr("data-chart"));
        if (!this.data) throw 'No chart data received!';
        this.renderChart();
        this.hideSpinner();
    }
} // ---------------- End of Static Chart -----------------------

/**
 * The class that hadles dynamic charts
 * 
 * @extends chartBase
 */
class dynamicChart extends chartBase {
    /**
     * Constucts the chartManager class.
     * The canvas element must contain the following attributes:
     *  - data-ajaxurl      Contains the url to get the data from
     * @param {obj} canvas Where to display the chart
     */
    constructor(canvas) {
        super(canvas);

        this.ajaxURL = $(canvas).attr("data-ajaxurl");
        this.limit = this.chartWrapper.find(".limit-input").val() || 10; //Number of datapoints to show
        this.type = this.chartWrapper.find(".interval-select").val() || "daily"; //Time interval type

        //Max and Min for datapoints
        this.limitMax = 365; 
        this.limitMin = 1; 

        this.offset = 0;

        //Hookup Listeners to the buttons.
        //These must be within the chartWrapper
        this.chartWrapper.find(".chart-left").click(() => this.scrollLeft());
        this.chartWrapper.find(".chart-right").click(() => this.scrollRight());
        this.chartWrapper.find('.interval-select').change(() => {
            let newInterval = this.chartWrapper.find('.interval-select').val();
            this.changeType(newInterval);
        });
        this.chartWrapper.find(".jump-button").click(() => {
            let jumpDate = this.chartWrapper.find(".jump-date").val();
            this.jumpTo(jumpDate);
        });
        this.chartWrapper.find(".limit-button").click(() => {
            let limitNum = this.chartWrapper.find(".limit-num").val();
            this.changeLimit(limitNum);
        });

        //Validate Data
        if (!this.ajaxURL) throw "Invalid ajax URL received!";

        this.getData(this.renderChart);
    }

    /**
     * Grabs chart data from the server using the AJAX URL
     * @param {function} callback - The callback when the data is recieved from the server
     *
     */
    getData(callback) {
        this.showSpinner();
        var [from_date, to_date] = this.dateFromOffset();

        if (this.debugMode) console.log(`Getting ${this.type} data from ${from_date} to ${to_date}`);

        $.get(
            this.ajaxURL,
            {
                interval_type: this.type,
                from_date: from_date,
                to_date: to_date
            },
            data => {
                this.data = JSON.parse(data);
                this.hideSpinner();
            }
        ).done(callback.bind(this));
    }

    /**
     * Creates the toDate and the fromDate that is used to request
     * the statistics data from the server.
     * The dates depend on the this.offset and this.type
     */
    dateFromOffset() {
        //Note: Added one so that different offsets don't have overlapping dates
        switch (this.type) {
            case "daily":
                var toDate = moment().add((this.limit * this.offset), "d");
                var fromDate = moment(toDate).subtract(this.limit - 1, "d");
                return [fromDate.format("Y-MM-DD"), toDate.format("Y-MM-DD")];
            case "weekly":
                var toDate = moment().add((this.limit * this.offset), "w");
                var fromDate = moment(toDate).subtract(this.limit - 1, "w");
                return [fromDate.format("Y-MM-DD"), toDate.format("Y-MM-DD")];
            case "monthly":
                var toDate = moment().add((this.limit * this.offset), "M");
                var fromDate = moment(toDate).subtract(this.limit - 1, "M");
                return [fromDate.format("Y-MM-DD"), toDate.format("Y-MM-DD")];
            case "yearly":
                var toDate = moment().add((this.limit * this.offset), "y");
                var fromDate = moment(toDate).subtract(this.limit - 1, "y");
                return [fromDate.format("Y-MM-DD"), toDate.format("Y-MM-DD")];
            default:
                break;
        }
    }

    /* Chart Manipulation - i.e. The things that make it dynamic */

    /**
     * Changes the date interval type of the chart.
     * interval type can be: 'daily', 'weekly', monthly', 'yearly'
     */
    changeType(type) {
        this.type = type;
        this.offset = 0;
        if (this.debugMode) console.log("Changing to: " + this.type);

        this.getData(this.updateChart);
    }

    /**
     * Moves all the datapoints to the left
     *
     * @param {number} amount The amount to shift offset by
     */
    scrollLeft(amount = 1) {
        this.offset -= amount;
        this.getData(this.updateChart);
    }

    /**
     * Moves all the datapoints to the right
     *
     * @param {number} amount The amount to shift offset by
     */
    scrollRight(amount = 1) {
        this.offset += amount;
        this.getData(this.updateChart);
    }

    /**
     * Sets the offset to a specified number based on the jump date given. Will automatically update the graph
     *
     * @param {string} jumpDate The string representing the date that the user wants to jump to.
     */
    jumpTo(jumpDate) {
        if (jumpDate) {
            var now = moment();
            jumpDate = moment(jumpDate); 

            //Find the offset of the day - depends on type of date interval
            switch (this.type) {
                case "daily":
                    var dateDiff = moment.duration(now.diff(jumpDate));
                    this.offset = -Math.floor(dateDiff.asDays() / this.limit);
                    break;
                case "weekly":
                    var dateDiff = moment.duration(now.diff(jumpDate));
                    this.offset = -Math.floor(dateDiff.asWeeks() / this.limit);
                    break;
                case "monthly":
                    var dateDiff = moment.duration(now.diff(jumpDate));
                    this.offset = -Math.floor(dateDiff.asMonths() / this.limit);
                    break;
                case "yearly":
                    var dateDiff = moment.duration(now.diff(jumpDate));
                    this.offset = -Math.floor(dateDiff.asYears() / this.limit);
                    break;
            }
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

// ------------ End of myChart.js -----------------------------