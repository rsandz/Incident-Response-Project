<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        //Load Resources
        $this->load->model('Investigation/analytics_model');
        $this->load->library('Google/analytics');
        $this->load->library('Investigation/Incident_builder', NULL, 'ib');
        $this->load->config('analytics');
    }
    
    /**
     * Checks for any Incidents that may have occurred
     * according to the Google Analytics settings
     */
    public function incident_check()
    {
        //Get conditions to check
        $incident_conditions = $this->analytics_model->get_current_metrics();

        //Set the report contents
        foreach($incident_conditions as $cond)
        {
            $this->analytics->metrics($cond['metric_name'], $cond['metric_name']);
        }
        $this->analytics->date('today');

        //Get the report
        $report = $this->analytics->get_report();
        
        foreach ($incident_conditions as $cond)
        {
            $report_result = $report[$cond['metric_name']];
            switch ($cond['metric_operator'])
            {
                case '<':
                    $result = $report_result < $cond['metric_value'] ? FALSE : TRUE;
                    break;
                case '<=':
                    $result = $report_result <= $cond['metric_value'] ? FALSE : TRUE;
                    break;
                case '=':
                    $result = $report_result == $cond['metric_value'] ? FALSE : TRUE;
                    break;
                case '>=':
                    $result = $report_result >= $cond['metric_value'] ? FALSE : TRUE;
                    break;
                case '>':
                    $result = $report_result > $cond['metric_value'] ? FALSE : TRUE;
                    break;
                default:
                    log_message('error', 'Invalid Metric Operator for '.$cond['metric_name']);
                    $result = 'error';
                    break;
            }
            
            if ($result)
            {
                echo $cond['metric_name'].' was succesful<br>';
                continue;
            }
            else
            {
                echo $cond['metric_name'].' Failed';
                $humanized_metric_name = $this->config->item('valid_metrics')[$cond['metric_name']];
                $this->ib
                    ->auto(1)
                    ->name("{$humanized_metric_name} Metric met Incident Condition")
                    ->date('now')
                    ->desc("The previous Google analytic report showed that {$humanized_metric_name} {$cond['metric_operator']} {$cond['metric_value']}.")
                    ->create();
            }
                
        }
        
    }

}

/* End of file Cron.php */
