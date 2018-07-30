$(function() {
    //Array of the staticChart objects
    staticCharts = [];

    //Initialize all elements with class 'static-chart'
    $(".static-chart").each(function(index) {
        console.log('Creating Chart' + index);
        
        staticCharts[index] = new staticChart(this);
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
        }];
    }

}

class staticChart extends chartBase{
    constructor(canvas) {
        super();
        this.canvas = canvas;
        this.data = JSON.parse($(canvas).attr('data-chart'));
        this.renderChart();
        console.log(this.colorSets);
    }

    renderChart()
    {
        //Get Background Colours
        //var backgroundColours = this.getBackgroundColours();
       
        var chartConfig = {
            type : 'bar',
            data: {
               labels : this.data.stats.x,
               datasets : [{
                    label: this.data.label || null,
                    data : this.data.stats.y,
                    backgroundColor: this.colorSets[0].backgroundColor,
                    borderColor : this.colorSets[0].borderColor,
                    hoverBackgroundColor : this.colorSets[0].hoverBackgroundColor,
                    hoverBorderColor: this.colorSets[0].hoverBorderColor,
                    borderWidth : 1,
               }] 
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        };
        
        var myChart = new Chart(this.canvas, chartConfig);
        console.log(myChart);
    }

       
}

