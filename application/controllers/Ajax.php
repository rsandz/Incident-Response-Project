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
		$this->load->model('Search_model');
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
	 * Gets the Description and/or type for fields in certain Tables.
	 * 		i.e. action table and project table both have descriptions and these will grab them
	 * For use with $.ajax(). Uses $_Get Array to get information
	 * @param $_Get string table
	 * @param $_Get string table_id
	 * 
	 */
	public function get_info()
	{
		$this->load->helper('inflector');
		$table = $this->input->get('table');
		$item_id = $this->input->get(singular($table).'_id');

		$attributes = array(
				singular($table).'_id' => $item_id
		);

		$query = $this->search_model->get_items($table, $attributes); 

		$db_names = array(
			'desc' => singular($table).'_desc',
			'type' => singular($table).'_type'
		);

		if (sizeof($query) > 0)
		{
			$data[$db_names['desc']] = isset($query[0]->{$db_names['desc']}) ? $query[0]->{$db_names['desc']} : 'No Descsription';
			$data[$db_names['type']] = isset($query[0]->{$db_names['type']}) ? $query[0]->{$db_names['type']} : 'No Type';
		}
		else
		{
			$data['error'] = 'No Descsription or Type';
		}

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
		$attributes =
			'type_id = '.$this->input->get('type_id').
			' AND is_active = 1'.
			' AND (project_id = '.$this->input->get('project_id').
			' OR is_global = 1)';
		$select = array(
			'action_name', 'action_id'
		);

		$query = $this->search_model->get_items('actions', $attributes, $select);
		$options = array();
		foreach ($query as $row) {
			$options[$row->action_id] = $row->action_name;
		}
		
		if (empty($options)) $options['NULL'] = 'No Actions';

		echo json_encode(form_dropdown('action', $options, NULL, 'id = "action-selector"'));
	}

	/**
	 * Gets the data for user log frequency.
	 * Used in the mystats charts
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 *                     				 @see $this->statistics_model->get_log_frequency() for more info
	 */
	public function get_user_log_frequency()
	{
		$data = $this->statistics_model->get_log_frequency($this->input->get('interval_type', TRUE));
		echo json_encode($data);
	}

	/**
	 * Gets the data for user hours.
	 * Used in the mystats charts
	 *
	 * @param $_Get string interval_type The interval type of the data ('daily', 'weekly', monthly', 'yearly')
	 *                     				 @see $this->statistics_model->get_user_hours() for more info
	 */
	public function get_user_hours()
	{
		$data = $this->statistics_model->get_user_hours($this->input->get('interval_type', TRUE));
		echo json_encode($data);
	}

}

/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */