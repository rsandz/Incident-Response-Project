<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Search Helper
 * =============
 * @author Ryan Sandoval
 * 
 * This helper contains functions that help with the search functionality
 * of the web app.
 */

 if (!function_exists('get_search_sort'))
 {
	 /**
	  * Gets the Search Sorting Preference from the session. 
	  * If not set, it will set it to Data and Time Descending.
	  * 
	  * @return array The Sorting Preference. Can just plug into search builder
	  */
	 function get_search_sort()
	 {
		$CI =& get_instance();
		$sort_by = $CI->session->sort_by;
		if (empty($sort_by))
		{
			$sort_by = array('date' => 'desc', 'time' => 'desc');
			$CI->session->set_userdata('sort_by', $sort_by);
		}
		return $sort_by;
	 }
 }

 if (!function_exists('set_search_sort'))
 {
	 function set_search_sort($field, $dir)
	 {
		$CI =& get_instance();

		switch ($field) {
			case 'date':
				$sort_by = array('date' => $dir, 'time' => $dir);
				break;
			default:
				$sort_by = array($field => $dir, 'date' => 'DESC');
				break;
		}

		$CI->session->set_userdata('sort_by', $sort_by);
	 }
 }

 if (!function_exists('get_sort_dropdown'))
 {
	 /**
	  * Returns an HTML string for dropdowns containing
	  * possible sort options
	  */
	 function get_sort_dropdown()
	 {
		$CI =& get_instance();
		$CI->load->helper('form');

		//Get Possible dropdowns
		$field_options = array(
			'date' => 'Date',
			'id' => 'Entry Order', 
			'action' => 'Action Name',
			'hours' => 'Number of Hours',
			'user' => 'User Name',
			'team' => 'Team Name',
			'project' => 'Project Name',
			'type' => 'Action Type Name'
		);
		$direction_options = array('desc' => 'Descending', 'asc' => 'Ascending');
		
		//Get current settings (They will be set as default for dropdowns)
		$curr_sort = get_search_sort(); //Keys are fields while values are directions
		$curr_field = array_keys($curr_sort)[0];
		$curr_val = $curr_sort[$curr_field];

		//Create Select inuts
		$data['sort_fields'] = form_dropdown('sort_field', $field_options, $curr_field, 'id="sort-fields"');
		$data['sort_dir'] = form_dropdown('sort_dir', $direction_options, $curr_val, 'id="sort-dir"');		

		//Put into template
		$drop_down = $CI->load->view('search/templates/sort_dropdown', $data, TRUE);

		return $drop_down;
	 }
 }

if (!function_exists('export_summary'))
{
	/**
	 * Creates an HTML string summary of the provided JSON export string
	 * @return string        The summary in html string form
	 */
	 function query_summary($export_json)
	{
		//Get resources
		$CI =& get_instance();
		$CI->load->model('get_model');

		$html_string = "<ul>"; //Start Organized List

		$query = json_decode($export_json);

		foreach($query as $label => $filters)
		{
			if (empty($filters)) continue;
			
			//For some data (i.e. 'users') we have to get the real name and not just the id
			switch ($label) 
			{
				case 'SB_users':
					$humanized_vals = "";
					$humanized_label = substr(humanize($label), 3);
					
					if (!is_array($filters))
					{
						$humanized_vals = $CI->get_model->get_user_name($filters);
						break;
					}

					foreach ($filters as $filter)
					{
						$humanized_vals .= $CI->get_model->get_user_name($filter) . ', ';
					}
					break;

				case 'SB_teams':
					$humanized_vals = "";
					$humanized_label = substr(humanize($label), 3);
					
					if (!is_array($filters))
					{
						$humanized_vals = $CI->get_model->get_team_name($filters);
						break;
					}

					foreach ($filters as $filter)
					{
						$humanized_vals .= $CI->get_model->get_team_name($filter) . ', ';
					}
					break;
				case 'SB_projects':
					$humanized_vals = "";
					$humanized_label = substr(humanize($label), 3);
					
					if (!is_array($filters))
					{
						$humanized_vals = $CI->get_model->get_project_name($filters);
						break;
					}

					foreach ($filters as $filter)
					{
						$humanized_vals .= $CI->get_model->get_project_name($filter) . ', ';
					}
					break;
				case 'SB_action_types':

					$humanized_vals = "";
					$humanized_label = substr(humanize($label), 3);

					if (!is_array($filters))
					{
						$humanized_vals = $CI->get_model->get_type_name($filters);
						break;
					}

					foreach ($filters as $filter)
					{
						$humanized_vals .= $CI->get_model->get_type_name($filter) . ', ';
					}
					break;

				case 'SB_null_teams':

					$humanized_label = "Null Teams";
					if ($filters == 0)
					{
						$humanized_vals = 'Yes';
					}
					else
					{
						$humanized_vals = 'No';
					}
					break;
				case 'SB_null_projects':

					$humanized_label = "Null Projects";
					if ($filters == 0)
					{
						$humanized_vals = 'Yes';
					}
					else
					{
						$humanized_vals = 'No';
					}

					break;

				case 'SB_keywords_in':

					$humanized_vals = "";
					$humanized_label = substr(humanize($label), 3);

					if (!is_array($filters))
					{
						$humanized_vals = $filters;
						break;
					}
					
					foreach ($filters as $filter)
					{
						if ($filter == 'CONCAT(first_name, " ", last_name)')
						{
							$humanized_vals .= 'User Name, ';
							continue;
						}

						$humanized_vals .= humanize($filter).', ';

					}
					break;
					
				case 'SB_keywords' || 'SB_keyword_type':
					$humanized_vals = "";
					$humanized_label = substr(humanize($label), 3);

					if (!is_array($filters))
					{
						$humanized_vals = $filters;
						break;
					}
					
					$humanized_vals .= implode(' ,', $filters);
					break;


				default:
					break;
			}

			$html_string .= '<li>';
			$html_string .= '<b>'.$humanized_label."</b>: ".$humanized_vals;
			$html_string .= '</li>';
		}

		$html_string .= '</ul>'; //End unorganized list

		return $html_string;
	}
}