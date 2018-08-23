<?php

use function GuzzleHttp\json_decode;
defined('BASEPATH') OR exit('No direct script access allowed');

/**	
 * Testing Controller
 * ==================
 * @author Ryan Sandoval
 * Used for testing features
 */
class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->view('templates/header');

	}

	/** Tests Search Model */
	public function search()
	{
		$this->load->model('Searching/search_model');
		$this->search_model
					->import_query('{"SB_keywords":[],"SB_keywords_in":["CONCAT(first_name, \" \", last_name)","team_name","project_name","action_name","type_name","log_desc"],"SB_keyword_type":"any","SB_from_date":null,"SB_to_date":null,"SB_action_types":[],"SB_projects":[],"SB_teams":[],"SB_users":"10","SB_null_teams":true,"SB_null_projects":true}');

		$export  = $this->search_model->export_query();
		$result = $this->search_model->search();
		$this->load->library('table');
		$table_result = $this->table->my_generate($result);

		echo $table_result;
		echo "<br>Unpaginated Rows - ".$this->search_model->unpaginated_rows;
		echo "<br>Paginated Rows - ".$result->num_rows();
		echo "<br><br> Last Query: ".$this->search_model->last_sql_query_string;
		echo '<br><br>JSON Export: '.$export;
		echo "<br><br> Errors: ".json_encode($this->search_model->get_errors());
	}


	/** Tests the Query Summary in search helper */
	public function query_summary()
	{
		$this->load->helper('search');
		echo query_summary('{"SB_keywords":[],"SB_keywords_in":["CONCAT(first_name, \" \", last_name)","team_name","project_name","action_name","type_name","log_desc"],"SB_keyword_type":"any","SB_from_date":null,"SB_to_date":null,"SB_action_types":[],"SB_projects":[],"SB_teams":[],"SB_users":"10","SB_null_teams":true,"SB_null_projects":true}');
	}

	/** Test Google Analytics */
	public function gapi()
	{
		$this->load->library('Google/analytics');
		$this->analytics->date('2018-06-01', 'yesterday');
		$this->analytics->metrics('ga:sessions', 'sessions');
		$this->analytics->metrics('ga:users', 'users');
		$this->analytics->metrics('ga:bounces', 'bounces');
		$this->analytics->metrics('ga:bounceRate', 'bounceRate');
		$report = $this->analytics->get_report();
		echo json_encode($report);
	}

	/** Tests The stats model */
	public function stats()
	{
		$this->load->model('Stats/statistics_model');
		$data = $this->statistics_model
				->from_date('2018-07-01') //7 Days * 24 Hours * 60 mins * 60 sec
				->to_date('now')
				->metrics('hours')
				->interval_type('yearly')
				->get();
		echo '<script> console.log('.json_encode($data).'); </script>';
	}

	/** Tests the stats graphing */
	public function stats_graph()
	{
		$this->load->helper('form');
		$data['interval_options'] = $this->config->item('interval_options');
		$this->load->view('stats/templates/chart-box', $data);
	}

	/** Test site notifications changing */
	public function site_notification()
	{
		$this->load->model('Settings/site_model');
		$this->site_model->set_site_notification('Test Notification');
	}

	/** SMS Test */
	public function sms()
	{
		$this->load->library('Investigation/sms_sender');
		$this->sms_sender->send_message('+17802451999', 'HELLO! Im sending this using PHP!');
	}

	/** Post Test */
	public function post()
	{
		echo json_encode($this->input->post());
	}

	public function chart()
	{
		$this->load->library('Investigation/investigator');
		$this->investigator
			->incident(1)
			->servChart_recent_activity();
		echo FCPATH.'generated_charts/recent_activity.jpg';
	}

	/** PHP INFO */
	public function info()
	{
		phpinfo();
	}

}
/* End of file Test.php */
/* Location: ./application/controllers/Test.php */

