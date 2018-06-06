<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('statistics_model');
		$this->load->model('Search_model');
		$this->load->helper('form');
		$this->load->helper('url');

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE
	}

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
	 * Gets the valud items in the action table to be displayed in a form.
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

	public function get_user_log_frequency()
	{
		$data = $this->statistics_model->get_log_frequency($this->input->get('interval_type', TRUE));
		echo json_encode($data);
	}

}

/* End of file ajax.php */
/* Location: ./application/controllers/ajax.php */