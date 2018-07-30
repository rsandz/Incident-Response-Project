$(function() {
    //Array of the staticChart objects
    staticCharts = [];

    //Initialize all elements with class 'static-chart'
    $(".static-chart").each(function(index) {
        console.log('Creating Chart' + index);
        
        staticCharts[index] = new staticChart(this);
    });
});

class staticChart {
    constructor(canvas) {
        this.canvas = canvas;
        this.jsonData = JSON.parse($(canvas).attr('data-chart'));
        console.log(this.jsonData);
        //Create the chart configuration
        this.data = {};
    }
}
