<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	Table Helper
	===========
	@author Ryan Sandoval, June 2018

	This is a helper for table creation and pagination.
 */

if (!function_exists('get_pagelinks')) 
{
	/**
	 * Generates the pagination Depending on number of rows and amount of rows per page.
	 *
	 * @param array $data Must contain the keys 'num_rows' and 'per_page'
	 * @param string $url base_url as per code ignitor's specification. @see Pagination documentation for codeignitor
	 * 
	 * @return array The $data array, but now contains the 'page_links' in it.
	 */
	function get_pagelinks($data, $url = 'Search/results') 
	{
		$CI =& get_instance();

		//Default Value
		if (!isset($data['per_page']))
		{
			$data['per_page'] = 10;
		}

		$CI->load->library('pagination');
		
		$config['base_url']       = site_url($url);
		$config['total_rows']     = $data['num_rows'];
		$config['per_page']       = $data['per_page'];
		$config['num_tag_open']   = '<div class="pagination-link">';
		$config['num_tag_close']  = '</div>';
		$config['cur_tag_open']   = '<div class="pagination-link is-current">';
		$config['cur_tag_close']  = '</div>';
		$config['next_link']      = 'Next';
		$config['next_tag_open']  = '<div class="pagination-next">';
		$config['next_tag_close'] = '</div>';
		$config['prev_link']      = 'Previous';
		$config['prev_tag_open']  = '<div class="pagination-previous">';
		$config['prev_tag_close'] = '</div>';
		$config['first_tag_open']  = '<div class="pagination-next">';
		$config['first_tag_close'] = '</div>';
		$config['last_tag_open']  = '<div class="pagination-previous">';
		$config['last_tag_close'] = '</div>';

		$CI->pagination->initialize($config);

		$data['page_links'] = $CI->pagination->create_links();

		return $data['page_links'];
	}
}

if (!function_exists('generate_table'))
{
	/**
	 * Generates a table using Code Ignitor's table library
	 * @param  Array $data Contains a field 'table_data' with array of data to
	 * tabulate and 'heading' with table headers as an array.
	 * 
	 * @return string       Table HTML
	 */
	function generate_table($data)
	{
		$CI =& get_instance();

		///////////////////////////
		//TABLE AESTHETICS SETUP //
		///////////////////////////

		$template = array(
	    'table_open'            => '<table class="table is-striped is-fullwidth">',

	    'thead_open'            => '<thead class="thead">',
	    'thead_close'           => '</thead>',

	    'heading_row_start'     => '<tr class="tr">',
	    'heading_row_end'       => '</tr>',
	    'heading_cell_start'    => '<th class="th">',
	    'heading_cell_end'      => '</th>',

	    'tbody_open'            => '<tbody class="tbody">',
		'tbody_close'		 	=> '</tbody>',

	    'row_start'             => '<tr class="tr">',
	    'row_end'               => '</tr>',
	    'cell_start'            => '<td class="td">',
	    'cell_end'              => '</td>',

	    'table_close'           => '</table>'
		);

		if (!isset($data['heading']) && isset($data['table'])) //If heading is not set, tries to get it automatically
		{
			$CI->load->model('search_model');
			$data['heading'] = $this->search_model->get_table_headings($data['table']);
		}
		elseif (!isset($data['heading']))
		{
			show_error('No Table headings provided (in table_helper): <br> '.json_encode($data['table_data']));
		}

		$CI->table->set_heading($data['heading']);

		$CI->table->set_template($template);

		$table_html = $CI->table->generate($data['table_data']);
		$table_html .= '<script src="'.base_url('js/page-link-fix.js').'"></script>'; // Adds script to fix the page numbers not being clickable

		return $table_html;
	}
}