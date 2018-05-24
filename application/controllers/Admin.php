<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class for administrative database changes

 */
class Admin extends CI_Controller {
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->helper('form');
		
		$this->load->model('logging_model');

		if ($this->session->privileges !== 'admin')
		{
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
			$this->load->view('hero-head', $data);
			$this->load->view('navbar', $data);
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
			'type' => $this->input->post('type')
		);
		$this->session->set_userdata($sess_data);
		$type = $this->session->type;

		$fields = $this->db->list_fields($type);
		
		$data['title'] = ucwords($type.' Creation');
		$data['type'] = ucfirst($type);
		$data['field_data'] = $this->logging_model->get_field_data($type);


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

	/**
	 * Views the entire action log
	 *
	 * @param int $page Page Number.
	 */
	public function view_logs($offset = 0) 
	{
		$per_page = 10;

		$table_data = $this->logging_model->get_entries_table($per_page, FALSE, $offset);
		$this->load->library('pagination');
		
		$config['base_url']       = site_url('Admin/view_logs');
		$config['total_rows']     = $table_data['total_rows'];
		$config['per_page']       = $per_page;
		$config['num_tag_open']   = '<div class="pagination-link">';
		$config['num_tag_close']  = '</div>';
		$config['cur_tag_open']   = '<div class="pagination-link is-current">';
		$config['cur_tag_close']  = '</div>';
		$config['next_link']      = 'Next';
		$config['next_tag_open']  = '<div class="pagination-next">';
		$config['next_tag_close'] = '</div>';
		$config['prev_link']      = 'Previous';
		$config['prev_tag_open']  = '<div class="pagination-previous">';
		$config['prev_tag_close'] = '</div>';
		$config['first_tag_open']  = '<div class="pagination-next">';
		$config['first_tag_close'] = '</div>';
		$config['last_tag_open']  = '<div class="pagination-previous">';
		$config['last_tag_close'] = '</div>';

		$this->pagination->initialize($config);

		$data['page_links'] = $this->pagination->create_links();
		$data['table'] = $table_data['table'];
		$data['total_entries'] = $table_data['total_rows'];

		$data['header']['colour'] = 'is-info';
		$data['header']['text'] = 'All Logs';
		$data['title'] = 'View Logs';

		$this->load->view('templates/header', $data);
		$this->load->view('hero-head', $data);
		$this->load->view('navbar', $data);
		$this->load->view('admin/view-logs', $data);
		$this->load->view('templates/footer', $data);
	}

}