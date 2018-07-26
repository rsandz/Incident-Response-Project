<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->view('templates/header');

	}

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

	public function query_summary()
	{
		$this->load->helper('search');
		echo query_summary('{"SB_keywords":[],"SB_keywords_in":["CONCAT(first_name, \" \", last_name)","team_name","project_name","action_name","type_name","log_desc"],"SB_keyword_type":"any","SB_from_date":null,"SB_to_date":null,"SB_action_types":[],"SB_projects":[],"SB_teams":[],"SB_users":"10","SB_null_teams":true,"SB_null_projects":true}');
	}

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

}
/* End of file Test.php */
/* Location: ./application/controllers/Test.php */

