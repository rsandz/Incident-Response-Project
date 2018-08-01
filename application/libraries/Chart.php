<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Charting Library
 * ================
 * @author Ryan Sandoval
 * @package Chart
 * 
 * Library for creating graphs.\
 * Its main job is to receive data from the satistics model and construct 
 * the charts in a way that myChart.js will be able to interpret.
 * 
 * ----------------------------------
 *      Creating Graphs
 * ----------------------------------
 * 
 * 1. Static Charts
 *  - Specify the title and chart_data (Don't json_encode)
 *      - chart_data must be in the same format as what is returned by 
 *        statistic_model->get()
 *  - Call generate_static()
 *  - Put the returned string into an HTML file.
 * 
 * 2. Dynamic Charts
 *  - Specify the title and the URL were to get the data by AJAX.
 *  - See myChart.js located in `/assets/js` for what the URL needs to provide
 *  - Call generate_dynamic()
 *  - Put the returned string into an HTML file
 * 
 *  ex. 
 *     PHP:
 *     $chart = chart->title('My Chart')
 *                   ->chart_data($data)
 *                   ->generate_static();
 *     HTML:
 *     <div><?php echo $chart; ?></div>
 * 
 * Styling the Charts
 * ------------------
 * This library uses the following templates:
 *  Dynamic charts: 'views/stats/templates/chart-box'
 *  Static Charts:  'views/stats/templates/chart-static'
 *    
 */
class Chart
{
    /** @var obj Code igniter instance */
    protected $CI;

    /** @var string The title for the chart */
    protected $title;

    /** @var string The URL to get dynamic data from */
    protected $ajax_url;

    /** @var array Chart data array from statistics model */
    protected $chart_data;

    /** @var array The valid time intervals */
    private $interval_items;

    /**
     * Constrcutor fot eh Charting Library
     * Initialization and Resource loading
     */
    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->config('stats_config');
        $this->interval_items = $this->CI->config->item('interval_options');
    }

    /**
     * Sets the title for the chart
     * @param string $title
     * @param Chart  For method chaining
     */
    public function title($title) 
    {   
        $this->title = $title;
        return $this;
    }

    /**
     * Sets the chart data for static charts
     * Simply pass in the data from the statistic model.
     * @param  array $chart_data
     * @return Chart For method chaining
     */
    public function chart_data($chart_data)
    {
        $this->chart_data = $chart_data;
        return $this;
    }

    /**
     * Sets the ajax URL for dynamic charts
     * @param string @ajax_url
     * @return Chart For method chaining
     */
    public function ajax_url($ajax_url)
    {
        $this->ajax_url = $ajax_url;
        return $this;
    }

    /**
     * Generates the HTML string that creates a static chart
     * @return string The HTML for the static chart
     */
    public function generate_static()
    {
        $data['title'] = $this->title;
        $data['chart_data'] = $this->chart_data;
        $this->reset();
        return $this->CI->load->view('stats/templates/chart-static', $data, TRUE);
    }

    /**
     * Generates the HTML string that creates a dynamic chart
     * @return string THe HTML for the dynamic chart
     */
    public function generate_dynamic()
    {
       $data['title'] = $this->title;
       $data['ajax_url'] = $this->ajax_url;
       $data['interval_options'] = $this->interval_items;
       $this->reset();
       return $this->CI->load->view('stats/templates/chart-box', $data, TRUE);
    }

    /**
     * Resets the Stored values in the library
     * @return void
     */
    public function reset()
    {
        $to_reset = array(
            'title'          => '',
            'ajax_url'       => '',
            'chart_data'     => '',
        );
        
        foreach ($to_reset as $prop => $val)
        {
            //For each of the properties, set to their defaults
            $this->{$prop} = $val;
        }
    }


}

/* End of file Chart.php */
