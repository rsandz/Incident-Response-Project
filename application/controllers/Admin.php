<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Admin Controller
 * ================
 * Written by: Ryan Sandoval, May 2018
 *
 * Handles Administrative things.
 *
 * @Depreciated
 * 
 */
/**
 * Class for administrative database changes

 */
class Admin extends CI_Controller {
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->helper('form');
		
		$this->load->model('logging_model');

		if ($this->session->privileges !== 'admin')
		{
			redirect('home','refresh');
			show_error('401 - Not Authorized', 401);
		} 
	}
	/**
	 * Loads the main administration Dashboard
	 */
	public function index() 
	{
			$data['title'] = 'Admin Dashboard';
			$data['header']['text'] = "Admin Dashboard";
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('admin/admin-dashboard');
			$this->load->view('templates/footer');

	}

	/**
	 *	Creates a new item in database.
	 */

	public function create() 
	{
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->helper('inflector');
	

		$sess_data = array(
			'type' => $this->input->post('type', TRUE)
		);
		$this->session->set_userdata($sess_data);
		$type = $this->session->type;

		$fields = $this->db->list_fields($type);
		
		$data['title'] = ucwords($type.' Creation');
		$data['type'] = ucfirst($type);
		$data['field_data'] = $this->search_model->get_field_data($type);


		foreach ($data['field_data'] as $field)
		{
			$this->form_validation->set_rules($field->name, humanize($field->name), 'required');
		}


		//Form validation
		foreach ($data['field_data'] as $field)
		{
			$this->form_validation->set_rules($field->name, humanize($field->name), 'required');
		}


		if ($this->form_validation->run()) 
		{
			$result = $this->logging_model->log_item($data['type'], $data['field_data']);
			if ($result !== TRUE)
			{
				show_error($result);

			}
			else
			{
			$this->session->unset_userdata('type');

			$this->load->view('templates/header', $data);
			$this->load->view('admin/success');
			}
		}
		else
		{
			//Make the form

			if ($data['field_data'] !== NULL)
			{
				$this->load->view('templates/header', $data);
				$this->load->view('admin/create', $data);
			}
			else
			{
				show_error('Table was not found');
			}
		}
	}

}