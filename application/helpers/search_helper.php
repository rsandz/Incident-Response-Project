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

if (!function_exists('parse_keywords'))
{
	/**
	 * Parses the given string into an array.
	 * Intended to use for interpreting the keyword data from keyword search. @see $this->results() for usage
	 * 
	 * @param  string $string The string to parse
	 * @return array         String turned into an array
	 */
	function parse_keywords($string) 
		{
			//TODO: add tags in the future. i.e. not: Bob (Interpreted as - rows without keyword 'bob') 
		
			$keywords = explode(' ', $string);

			foreach ($keywords as $key => $keyword) 
			{
				if ($keyword == "")
				{
					unset($keywords[$key]);
				}
			}

			return $keywords;
		}
}

if (!function_exists('query_from_post'))
{
	/**
	 * This will generate an associative array containing all search query data 
	 * from the post array
	 * @return array The search query from the user.
	 */
	function query_from_post()
	{	
		//Get CI Instance
		$CI =& get_instance();

		//Used to remove any elements in array that are empty
		$cleaner = function($item) 
		{
			if (empty($item))
			{
				return FALSE;
			}
			return TRUE;
		};

		$query = array(
			'keywords' => parse_keywords($CI->input->post('keywords', TRUE)),
			'keyword_filters' => $CI->input->post('kfilters') !== NULL ? $CI->input->post('kfilters', TRUE) : NULL,
			'ksearch_type' => $CI->input->post('ksearch_type', TRUE),

			'from_date' => (string)$CI->input->post('from_date' , TRUE),
			'to_date' => (string)$CI->input->post('to_date', TRUE),

			'action_types' => $CI->input->post('action_types[]', TRUE),

			'projects' => $CI->input->post('projects[]', TRUE),
			'null_projects' => $CI->input->post('null_projects', TRUE),
		
			'teams' => $CI->input->post('teams[]', TRUE),
			'null_teams' => $CI->input->post('null_teams', TRUE),

			'users' => $CI->input->post('users[]', TRUE),


			'back_url' => $CI->input->post('back_url') //URL for the back button for graph search
		);

		//Filter through each element in $query and ensure they're not empty.
		foreach($query as &$item)
		{
			if(is_array($item))
			{
				$item = array_filter($item, $cleaner);
			}
		}
		return $query;
	}
}

if (!function_exists('query_from_session'))
{
	/**
	 * This will generate an associative array containing the query data 
	 * from query in a the session
	 * Will also replace the to and from dates if they are in the post array.
	 * @param int $index Index of the stored query
	 * @return array The search query from the user.
	 */
	function query_from_session($index)
	{	
		//Get CI Instance
		$CI =& get_instance();

		$query = $CI->session->{'query_'.$CI->input->post('query_index', TRUE)};
		if (!empty($CI->input->post('from_date' , TRUE)))
		{
			$query['from_date'] = (string)$CI->input->post('from_date' , TRUE);
		}
		if(!empty($CI->input->post('to_date', TRUE)))
		{
			$query['to_date'] = (string)$CI->input->post('to_date', TRUE);
		}

		return $query;
	}
}

if (!function_exists('query_in_session'))
{
	/**
	 * If the query is already in the post (as a JSON string)
	 * this will retrieve it and return an assosiative array from the JSON
	 *
	 * This will also update the following in the resulting query:
	 * - from_date and to_date if they are in the $_Post array
	 * - back_url if they are in the post array
	 * 
	 * @return array The query as an associative array
	 */
	function query_in_post()
	{
		//Get CI Instance
		$CI =& get_instance();

		$query = json_decode($CI->input->post('query', TRUE), TRUE);

		//Replace some parts of the query with new data
		if (!empty($CI->input->post('from_date' , TRUE)))
		{
			$query['from_date'] = (string)$CI->input->post('from_date' , TRUE);
		}
		if(!empty($CI->input->post('to_date', TRUE)))
		{
			$query['to_date'] = (string)$CI->input->post('to_date', TRUE);
		}
		if(!empty($CI->input->post('back_url', TRUE)))
		{
			$query['back_url'] = $CI->input->post('back_url', TRUE);
		}

		return $query;
	}
}

if (!function_exists('query_to_string'))
{
	/**
	 * Turns the result from post_to_query() into a string
	 * @param  array $query The query array
	 * @return string        The query array in html string form
	 */
	function query_to_string($query)
	{
		//Excluded filters to show.
		$exclude = ['ksearch_type', 'keyword_filters', 'back_url'];

		$CI =& get_instance();
		/*
			Load Resources
			==============
		*/
		$CI->load->model('search_model');

		$html_string = "<ul>"; //Start Organized List

		foreach($query as $label => $filters)
		{
			if (empty($filters) OR in_array($label, $exclude))
			{
				//Continue and dont display empty/excluded filters
				continue;
			}

			switch ($label) //For some data (i.e. 'users') we have to get the real name and not just the id
			{
				case 'users':
					//Get user name
					$filter_string = "";
					foreach ($filters as $user_id)
					{
						$filter_string .= $CI->search_model->get_user_name($user_id) . ', ';
					}
				break;

				case 'teams':			
					//Get team name
					$filter_string = "";
					foreach ($filters as $team_id)
					{
						$filter_string .= $CI->search_model->get_team_name($team_id) . ', ';
					}
				break;

				case 'projects':
					//Get project name
					$filter_string = "";
					foreach ($filters as $project_id)
					{
						$filter_string .= $CI->search_model->get_project_name($project_id) . ', ';
					}
				break;

				case 'action_types':
					//Get action type name
					$filter_string = "";
					foreach ($filters as $type_id)
					{
						$filter_string .= $CI->search_model->get_type_name($type_id) . ', ';
					}
				break;

				default:
				//Just display thhumanized version of the string or array
				if (is_array($filters))
				{
					//Turn array into string
					$filter_string = implode(', ', $filters);
				}
				else
				{
					$filter_string = $filters;
				}
				break;

			}

			$html_string .= '<li>';
			$html_string .= '<b>'.humanize($label)."</b>: ".humanize($filter_string);
			$html_string .= '</li>';
		}

		$html_string .= '</ul>'; //End unorganized list

		return $html_string;
	}
}