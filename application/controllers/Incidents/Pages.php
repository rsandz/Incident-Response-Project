<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pages Controller
 * ================
 * @author Ryan Sandoval
 * @package Investigation
 * 
 * This is the controller for the pages in the incidents functinalilty.
 * It will be able to handle:
 * 	- New Incident creation
 * 	- Historical Incidents
 * 	- Running investigations
 */
class Pages extends MY_Controller {

	/**
	 * Constructor for this controller.
	 * Loads the Neccessary resources/libraries
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('Investigation/incident_builder', NULL, 'ib');
		$this->load->library('Investigation/investigator');
		$this->load->model('Investigation/investigation_model');
		$this->load->helper('form');

		$this->authentication->check_admin(TRUE);
	}

	/**
	 * Main page for the Incidents functionality.
	 * Shows a recent incidents table, along with a control panel.
	 * @return [type] [description]
	 */
	public function index()
	{
		$data = array('title' => 'Incidents Overview');

		//Get Recent Incidents
		$recent_incidents = $this->investigation_model->get_all_incidents(); // Contains 'num_rows' & 'data'
		//Tabulate the Data
		$this->load->library('table');
		$data['incidents_table'] = $this->table->my_generate($recent_incidents);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('admin/tabs');
		$this->load->view('incidents/main');
		$this->load->view('templates/footer');
	}

	/**
	 * The controller for the create incident page
	 * Contains the form for creating the incident
	 */
	public function create_incident()
	{
		//Load Resources
		$this->load->library('form_validation');

		$this->form_validation->set_rules('incident_name', 'Incident Name', 'required');
		$this->form_validation->set_rules('incident_date', 'Incident Date', 'required');

		if ($this->form_validation->run())
		{
			//Validation good, put into database
			$this->ib
				->name($this->input->post('incident_name', TRUE))
				->date($this->input->post('incident_date', TRUE))
				->time($this->input->post('incident_time', TRUE))
				->desc($this->input->post('incident_desc', TRUE))
				->auto(FALSE)
				->create();

			//Success Page
			$data['title'] = 'Incident Created';
			$data['success_msg'] = 'The Incident has been created';
			$data['success_back_url'] = site_url('Incidents');
			
			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('admin/tabs');
			$this->load->view('templates/success');
			$this->load->view('templates/footer');
		}
		else
		{
			$data = array('title' => 'Incidents');

			//Generate Error
			$data['errors'] = $this->load->view('templates/errors', $data, TRUE);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/hero-head', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('admin/tabs');
			$this->load->view('incidents/create');
			$this->load->view('templates/footer');
		}
	}

	/**
	 * Selection Page for the report
	 * @param  integer $offset The pagination offset for the table
	 */
	public function view_incidents($offset = 0)
	{
		$this->load->library('table');
		$this->load->library('pagination');

		//ID not set so show the selection screen
		$data['title'] = 'View Incidents';
		$table_data = $this->investigation_model->report_table_data($offset);
		$num_rows = $this->investigation_model->total_rows;
		$new_headers = array('Name', 'Date', 'Time', 'Description', 'Automated', 'Created By', 'Report');

		$data['table'] = $this->table->my_generate($table_data, $new_headers);
		$data['page_links'] = $this->pagination->my_create_links($num_rows, 'Incidents/report/select/');

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('admin/tabs');
		$this->load->view('incidents/select-report', $data);
		$this->load->view('templates/footer');
	}

	/**
	 * Shows the report for the chosen Incident ID
	 * @param int $incident_id The ID of the incident to view the report for
	 */
	public function report($incident_id)
	{
		//Set the incident
		$this->investigator->incident($incident_id);
		$data['report'] = $this->investigator->get_html_report($incident_id);
		$data['title'] = "Report for Incident #{$incident_id}";

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('admin/tabs');
		$this->load->view('incidents/view-report', $data);
		$this->load->view('templates/footer');
	}

	public function analytics_settings()
	{
		$data['title'] = 'Google Analytics Settings';

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('admin/tabs');
		$this->load->view('incidents/analytics-settings', $data);
		$this->load->view('templates/footer');
	}

}

/* End of file Pages.php */
/* Location: ./application/controllers/Incidents/Pages.php */