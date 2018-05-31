<?php 
/**
 * Logging Controller
 * ==================
 * Written by: Ryan Sandoval, May 2018
 *
 * This controller handles the functionality regarding logging actions.It allows users to log actions using an interface
 * 	It also contains methods used by $.ajax() to request descriptions on certain action_types, actions and projects
 * 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logging extends CI_controller {
	public function __construct() 
	{
		parent::__construct();
		$this->load->model('logging_model');
		$this->load->model('search_model');
		$this->load->helper('form');

		date_default_timezone_set($this->config->item('timezone')); //SETS DEFAULT TIME ZONE
	}

	/**
	 * Controller for the log form
	 */
	public function log() 
	{

		$data['title'] = 'Logging Form';

        $data['projects'] = $this->search_model->get_items('projects');
        $data['types'] = $this->search_model->get_items('action_types', array('is_active !=' => 0));
        $data['teams'] = $this->search_model->get_items('teams');

		$this->load->library('form_validation');
		$this->load->helper('url');

		$this->form_validation->set_rules('date', 'Date', 'required');
		$this->form_validation->set_rules('time', 'Time', 'required');
        $this->form_validation->set_rules('action', 'Action', 'required'); 

		if ($this->form_validation->run() === FALSE) 
		{	
			$data['header'] = array(
				'text' => 'Logging form',
				'colour' => 'is-success');

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('logging/logging-form', $data);
			$this->load->view('js/descriptions');
		}
		else 
		{
			if ($this->session->user_id !== NULL) 
			{
			$data['title'] = 'Success';
			$this->logging_model->log_action('form');

			$this->load->view('templates/header');
			$this->load->view('logging/success');
			}
			else
			{
				show_error('User is not logged in', 401);
			}

		}	
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


}
