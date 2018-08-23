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
		$this->load->model('Tables/user_model');
		$this->load->model('Tables/team_model');
		$this->load->library('form_validation');

		$this->authentication->check_login();
	}

	/**
	 * Contains account information for the currently logged in user
	 * For example:
	 * 	- Name
	 * 	- Join Date (TODO)
	 * 	- The User's Teams
	 * @return void
	 */
	public function index()
	{
		$data['user_teams'] = $this->team_model->get_user_teams($this->session->user_id, FALSE);
		$data['title'] = 'Account';
		
		//Content
		$data['content'] = $this->load->view('user/my-info', $data, TRUE);

		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer', $data);
	}


	/**	
	 * Controller for Settings per user.
	 * Allows to change User Info
	 * @return void
	 */
	public function settings()
	{
		$data['notifications'] = $this->notifications;
		$data['title'] = 'Account Settings';
		$data['errors'] = $this->errors;
		$data['current_info'] = $this->user_model->get_by_id($this->session->user_id);

		//Load Page
		$data['content'] = $this->load->view('user/settings/account-settings', $data, TRUE);
		$this->load->view('templates/header', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('templates/content-wrapper', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Save the User info from the settings page
	 * @return void
	 */
	public function save_user_info()
	{
		$this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim');
		

		if ($this->form_validation->run())
		{
			$update_data = array(
				'first_name' => $this->input->post('first_name', TRUE),
				'last_name' => $this->input->post('last_name', TRUE),
				'phone_num' => $this->input->post('phone_num', TRUE),
				'user_desc' => $this->input->post('user_desc', TRUE)
			);
	
			$this->user_model->update($this->session->user_id, $update_data);
			set_notification('Your Information has been updated');
		}
		else 
		{
			set_error('An error has Occured During Updating your Info.');
		}
		
		redirect(site_url('Account/settings'));
	}

	public function set_password()
	{
		$this->form_validation->set_rules('current_pass', 'Current Password', 'required');
		$this->form_validation->set_rules('new_pass', 'New Password', 'required');
		
		$user_id = $this->session->user_id;
		$current_pass = $this->input->post('current_pass', TRUE);
		$new_pass = $this->input->post('new_pass', TRUE);

		if ($this->form_validation->run() && $this->authentication->validate_pass($user_id, $current_pass))
		{
			//Reset pass if validation rules set and current password is correct
			$this->authentication->reset_pass($user_id, $new_pass);
			set_notification('Your Password has been changed');
		}
		else
		{
			set_error('Incorrect Current Password. Your Password did not change.');
		}

		redirect(site_url('Account/settings'));
	}

	/**
	 * Admin Settings
	 * @return void
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
			$this->admin_settings->notify_incident_email(
				$this->session->user_id, $this->input->post('notify_incident_email', TRUE)
			);
			$this->admin_settings->notify_incident_sms(
				$this->session->user_id, $this->input->post('notify_incident_sms', TRUE)
			);

			set_notification('Your Settings have been updated');
			redirect(current_url(),'refresh');
		
		}
		else
		{
			//Create form
			$data['current_settings'] = $this->admin_settings->get_current_settings($this->session->user_id);
			$data['has_phone_num'] = $this->user_model->get_by_id($this->session->user_id)->phone_num !== NULL;
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