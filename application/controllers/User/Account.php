<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Account Controller
 * ==================
 * @author Ryan Sandoval
 * 
 * The Account controller allows the user to view information
 * about their account. It also conatins the pages that allows
 * the user to change their account settings.
 *
 * Administrative Settings are also located here.
 * 	(i.e. Change Incident E-Mail Settings.)
 * 
 */
class Account extends MY_Controller {

	/**
	 * Constructor for account controller.
	 * Loads all the necessary resources.
	 */
	public function __construct()
	{
		parent::__construct();

		//Load Resources
		$this->load->model('get_model');
		$this->load->helper('form');

		$this->authentication->check_login();
	}

	/**
	 * Contains account information for the currently logged in user
	 * For example:
	 * 	- Name
	 * 	- Join Date (TODO)
	 * 	- The User's Teams
	 * 
	 */
	public function index()
	{
		$data['user_teams'] = $this->get_model->get_user_teams($this->session->user_id, FALSE);
		$data['title'] = 'Account';

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('user/my-info', $data);

		$this->load->view('templates/footer');
	}

	public function settings()
	{

	}

	/**
	 * Admin Settings
	 */
	public function admin_settings()
	{
		$this->authentication->check_admin(TRUE);
		
		$this->load->model('Settings/admin_model', 'admin_settings');
		$this->load->library('form_validation');

		$this->form_validation->set_rules('submit', 'Submit', 'required');

		$data['title'] = 'Admin Settings';

		if ($this->form_validation->run())
		{
			$this->admin_settings->notify_new_incident(
				$this->session->user_id, $this->input->post('notify_new_incident', TRUE)
			);
			$this->admin_settings->notify_investigated(
				$this->session->user_id, $this->input->post('notify_investigated', TRUE)
			);

			set_notification('Your Settings have been updated');
			redirect(current_url(),'refresh');
		
		}
		else
		{
			//Create form
			$data['current_settings'] = $this->admin_settings->get_current_settings($this->session->user_id);
			$data['notifications'] = $this->session->notifications;
			$data['content'] = $this->load->view('user/settings/admin-settings', $data, TRUE);

			$this->load->view('templates/header', $data);
			$this->load->view('templates/navbar', $data);
			$this->load->view('templates/content-wrapper', $data);
			$this->load->view('templates/footer', $data);
		}
	}
}

/* End of file Account.php */
/* Location: ./application/controllers/User/Account.php */