<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Admin Controller
 * ================
 * Written by: Ryan Sandoval, May 2018
 *
 * Handles Administrative functionality and routing.
 *
 * @Depreciated
 *
 */
class Admin extends MY_Controller
{

	/**
	 * Construcor Method fo Admin Controller
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Settings/site_model', 'site_settings');
		$this->load->helper('form');
	}
	/**
	 * Loads the main administration Dashboard
	 */
	public function index()
	{
		$data['title'] = 'Admin Dashboard';
		$data['content'] = $this->load->view('admin/admin-dashboard', $data, TRUE);
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer');
	}

	/**
	 * Controller for the site settings page
	 */
	public function site_settings() 
	{
		$data['title'] = 'Site Settings';

		$this->form_validation->set_rules('submit', 'Submit', 'required');
		$this->form_validation->set_rules('site_notification', 'Site Notification', 'trim');
		
		
		if ($this->form_validation->run()) {
			//Update Data
			$this->site_settings->set_site_notification($this->input->post('site_notification', TRUE));
			
			//Create Success Notification
			$data['notification'] = "Settings Updated";
			$notifications = $this->load->view('templates/notification', $data, TRUE);
			$this->session->set_flashdata('notifications', $notifications);

			//Reset Validation
			$this->form_validation->reset_validation();
			$_POST = array();

			redirect(current_url());
		} 
		else 
		{
			$data['current_settings'] = $this->site_settings->get_all_settings();

			//Show the form
			$data['content'] = $this->load->view('admin/site-settings', $data, TRUE);
			$data['notifications'] = $this->notifications;

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/content-wrapper', $data);
			$this->load->view('templates/footer', $data);
		}
	}

}
