<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AJAX Controller
 * ===============
 * @author Ryan Sandoval, June 2018
 *
 * All AJAX requests should be sent to this controller. Java script can access this controller's adress by 
 * using the data attribute in the 'ajax-link' hidden input located in the header of every webpage.
 *
 * This controller is mostly used for getting descriptions, and updated field values based on user selection.
 */
class Ajax extends CI_Controller {

	/**
	 * Constructor for the AJAX Controller
	 *
	 * Loads all necessary resources.
	 * Also setes the PHP default timezone as per the configuration in config/appconfig.php
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('statistics_model');
		$this->load->model('Form_get_model');
		$this->load->model('search_model');
		$this->load->helper('form');
		$this->load->helper('url');

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') //If not acessed by post, redirect away
		{
			redirect('home','refresh');
			show_error('No access allowed', 403);
		}
	}

	/**
	 * Main page for the Ajax Controller
	 * This will redirect the user away if it is not acessed by a script's post request.
	 * @return [type] [description]
	 */
	public function index() 
	{
		redirect('home','refresh');
		show_error('No access allowed', 403);
	}

	/**
	 * Gets the Description and/or action_type for fields in certain Tables.
	 * 		i.e. action table and project table both have descriptions and these will grab them
	 * For use with $.ajax(). Uses $_Get Array to get information
	 * @param $_Get string table
	 * @param $_Get string table_id
	 * 
	 */
	public function get_info()
	{
		$table = $this->input->get('table', TRUE);
		$id = $this->input->get('id', TRUE);

		$data = $this->Form_get_model->get_item_info($table, $id);

		echo json_encode($data);
	}

	/**
	 * Gets the value items in the action table to be displayed in a form.
	 * For use with $.ajax(). Uses $_Get array to get information
	 *
	 * @param  $_Get string type_id
	 * @param  $_Get string project_id
	 * 
	 */
	public function get_action_items()
	{
		$type_id = $this->input->get('type_id', TRUE);
		$project_id = $this->input->get('project_id', TRUE);

		echo json_encode($this->Form_get_model->active_actions_form($type_id, $project_id));
	}

	/**
	 * Gets the data for user log frequency.
	 * Used in the mystats charts
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 */
	public function get_user_log_frequency()
	{
		$data = $this->statistics_model->get_log_frequency(
			$this->input->get('interval_type', TRUE),
			array('users' => $this->session->user_id)
		);

		//Give the data a name. Used for the graph legend
		$data['name'] = 'User Log Frequency';
		echo json_encode($data);
	}

	/**
	 * Gets the data for user hours.
	 * Used in the mystats charts
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 */
	public function get_user_hours()
	{
		$data = $this->statistics_model->get_hours(
			$this->input->get('interval_type'),
			array('users' => $this->session->user_id)
		);

		//Give the data a name. Used for the graph legend
		$data['name'] = 'User Hours';
		echo json_encode($data);
	}

	/**
	 * Gets the data for project log frequency.
	 * Used in the projectstats charts
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 *                     				 @see $this->statistics_model->get_project_log_frequency() for more info
	 */
	public function get_project_log_frequency($project_id)
	{
		$data = $this->statistics_model->get_log_frequency(
			$this->input->get('interval_type', TRUE), 
			array('projects' => $project_id)
		);

		//Give the data a name. Used for the graph legend
		$data['name'] = 'Project Log Frequency';
		echo json_encode($data);
	}

	/**
	 * Gets the data for project hours.
	 * Used in the projectstats charts
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 *                     				 @see $this->statistics_model->get_project_hours() for more info
	 */
	public function get_project_hours($project_id)
	{
		$data = $this->statistics_model->get_hours(
			$this->input->get('interval_type', TRUE), 
			array('projects' => $project_id)
		);

		//Give the data a name. Used for the graph legend
		$data['name'] = 'Project Log Hours';
		echo json_encode($data);
	}

	/**
	 * Gets the data for team log frequency.
	 * Used in the projectstats charts
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 *                     				 @see $this->statistics_model->get_team_log_frequency() for more info
	 */
	public function get_team_log_frequency($team_id)
	{
		$data = $this->statistics_model->get_log_frequency(
			$this->input->get('interval_type', TRUE), 
			array('teams' => $team_id)
		);

		//Give the data a name. Used for the graph legend
		$data['name'] = 'Team Log Frequency';
		echo json_encode($data);
	}

	/**
	 * Gets the data for team hours.
	 * Used in the projectstats charts
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 *                     				 @see $this->statistics_model->get_team_hours() for more info
	 */
	public function get_team_hours($team_id)
	{
		$data = $this->statistics_model->get_hours(
			$this->input->get('interval_type', TRUE), 
			array('teams' => $team_id)
		);

		//Give the data a name. Used for the graph legend
		$data['name'] = 'Team Hours';
		echo json_encode($data);
	}

	/**
	 * Gets the data for custom log frequency.
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 *                     				 @see $this->statistics_model->get_custom_log_frequency() for more info
	 */
	public function get_custom_log_frequency($index)
	{
		$data = $this->statistics_model->get_log_frequency(
			$this->input->get('interval_type', TRUE),
			$this->session->{'query_'.$index}
		);

		//Give the data a name. Used for the graph legend
		$data['name'] = "Custom Stats {$index} Log Freq";
		echo json_encode($data);
	}

	/**
	 * Gets the data for custom hours.
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 *                     				 @see $this->statistics_model->get_custom_hours() for more info
	 */
	public function get_custom_hours($index)
	{
		$data = $this->statistics_model->get_hours(
			$this->input->get('interval_type', TRUE), 
			$this->session->{'query_'.$index}
		);

		//Give the data a name. Used for the graph legend
		$data['name'] = "Custom Stats {$index} Hours";
		echo json_encode($data);
	}

}

/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */