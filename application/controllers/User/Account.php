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
class Account extends CI_Controller {

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

		$data['title'] = 'Admin Settings';

		$this->load->view('templates/header', $data);
		$this->load->view('templates/hero-head', $data);
		$this->load->view('templates/navbar', $data);
		$this->load->view('user/settings/admin-settings', $data);

		$this->load->view('templates/footer');
	}
}

/* End of file Account.php */
/* Location: ./application/controllers/User/Account.php */