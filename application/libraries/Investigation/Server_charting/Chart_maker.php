<?php

defined('BASEPATH') OR exit('No direct script access allowed');
include('PHPGraphLib/phpgraphlib.php');
/**
 * Chart Maker Library (Server-side)
 * =================================
 * 
 * Used in rendering graphs serverside.
 * For use in sending emails with charts.
 * Library Wrapper for the PHPChartlib Library
 * 
 * NOTE: The PHPChart library likes to put out notices, so I've used
 * the '@' operator to silence create_graph().
 * Undoing this will cause the redirect function to not work.
 * I know its bad practice but there's nothing else I could do since
 * the library is no longer maintained and it's the only MIT
 * licensed one.
 */
class Chart_maker
{
    protected $CI;
    
    /** @var obj The graph object */
    protected $graph;

    /** @var int Sets the current dataset that data is being added to */
    protected $current_dataset;
    
    /** @var obj The file name of the graph */
    public $file_name;
    /** @var array The data to insert into the graph */
    public $data_points;
    /** @var string The title of the graph */
    public $title;
    /** @var array The Legend */
    public $legend = array();
    /** @var array X-axis */
    public $x;

    protected $colours = array(
        '50, 14, 200',
        '255, 58, 58',
        '13, 240,13'
    );


    public function __construct()
    {
        $this->CI =& get_instance();

        $this->file_name = 'chart';
        $this->title('Chart');
        $this->current_dataset = 0;
    }

    public function render()
    {
        $graph = new PHPGraphLib(700, 500, "generated_charts/{$this->file_name}.jpg");
        $graph->addData(...$this->data_points);
        $graph->setTitle($this->title);
        $graph->setXValuesHorizontal(TRUE);
        $graph->setBarColor(...$this->colours);
        if (!empty($this->legend)) 
        {
            $graph->setLegend(TRUE);
            $graph->setLegendTitle(...$this->legend);
        }
        @$graph->createGraph();
    }


    /**
     * Adds data to the graph
     * Data an be passed as an array or as x and y value (i.e. add_data(array) or add_data(x,y))
     * @param mixed $data Can be an array of data (X values are keys)
     *                    or if entering only a single data points, the key
     * @param int $value The y-value of the data point, if a key was entered in the data param
     * @return Chart_maker For method chaining
     */
    public function add_data($data, $value = NULL)
    {
        if (is_array($data))
        {
            $this->data_points[$this->current_dataset] = $data;
        }
        else{
            $this->data_points[$this->current_dataset][$data] = $value;
        }

        return $this;
    }

    /**
     * Add Data Set to the chart
     * @param array $dataset One data set containing all the y values
     * @return Chart_maker 
     */
    public function add_dataset($dataset)
    {
        $this->data_points[] = $dataset;
        return $this;
    }

    /**
     * Use to swtich to different dataset
     * Useful when adding data one by one using add_data
     * Note: see phpChartLib for info on using associative array to define x-axis
     * @param int $dataset Which Dataset to swtich too
     * @return Chart_maker Method Chaining
     */
    public function switch_dataset($dataset = 0)
    {
        $this->current_dataset = $dataset;

        return $this;
    }

    /**
     * Sets the title of the graph
     * @param string $title
     * @return Chart_maker Method Chaining
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Sets the legend of the graph
     * @param array @legend Array of legends
     * @return Chart_maker
     */
    public function legend($legend = array())
    {
        $this->legend = $legend;
        return $this;
    }

    /**
     * Sets the file name of the generated chart.
     * Located in BASEPATH/generated_charts
     * @param string $file_name 
     * @return Chart_maker 
     */
    public function file_name($file_name)
    {
        $this->file_name = $file_name;
        return $this;
    }
    

}

/* End of file Chart_maker.php */
