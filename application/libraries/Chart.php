<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Charting Library
 * ================
 * 
 * Allows charts to be created.
 */
class Chart
{
    protected $CI;

    protected $title;

    protected $ajax_url;

    protected $chart_data;

    private $interval_items;

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
     * Generates the HTML string that creates a sttic chart
     */
    public function generate_static()
    {
        $data['title'] = $this->title;
        $data['chart_data'] = $this->chart_data;
        return $this->CI->load->view('stats/templates/chart-static', $data, TRUE);

        $this->reset();
    }

    public function generate_dynamic()
    {
       $data['title'] = $this->title;
       $data['ajax_url'] = $this->ajax_url;
       $data['interval_options'] = $this->interval_items;
       return $this->CI->load->view('stats/templates/chart-box', $data, TRUE);

       $this->reset();
    }

    public function reset()
    {
        $to_reset = array(
            'title'          => '',
            'ajax_url'       => '',
            'chart_data'     => '',
        );
        
        foreach ($to_reset as $prop => $val)
        {
            $this->{$prop} = $val;
        }
    }


}

/* End of file Chart.php */
