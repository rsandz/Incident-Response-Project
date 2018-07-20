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