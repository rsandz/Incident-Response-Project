<?php
class Logging extends CI_controller {
	public function __construct() {
		parent::__construct();
		$this->load->model('logging_model');

	}

	public function log() {

		$data['title'] = 'Logging Form';
		//Get possible actions
        $data['actions'] = $this->logging_model->get_actions();

		$this->load->helper('form');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('action', 'Action', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');

        

		if ($this->form_validation->run() === FALSE) {	
			$this->load->view('templates/header', $data);
			$this->load->view('logging/logging-form', $data);
		} else {
			$this->logging_model->log_action();
			$this->load->view('logging/success');
		}

		
	}



}
