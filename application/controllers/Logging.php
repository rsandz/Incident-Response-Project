<?php
class Logging extends CI_controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('logging_model');
		$this->load->library('session');

	}

	public function log() {

		$data['title'] = 'Logging Form';
		$data['time_zone'] = 'America/Edmonton';

		//Get possible actions
        $data['actions'] = $this->logging_model->get_info('actions');
        $data['projects'] = $this->logging_model->get_info('projects');
        $data['teams'] = $this->logging_model->get_info('teams');

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('url');

		$this->form_validation->set_rules('date', 'Date', 'required');
		$this->form_validation->set_rules('time', 'Time', 'required');
        $this->form_validation->set_rules('action', 'Action', 'required'); 

		if ($this->form_validation->run() === FALSE) {	
			$this->load->view('templates/header', $data);
			$this->load->view('logging/logging-form', $data);
		} else {
			$data['title'] = 'Success';
			$this->logging_model->log_action();
			
			$this->load->view('templates/header');
			$this->load->view('logging/success');

		}	
	}



}
