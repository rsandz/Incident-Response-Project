<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {

	/**
	 * Custom Pagination links creator
	 * Generates the pagination Depending on number of rows and amount of rows per page.
	 * @param int $num_rows The amount of rows in the table
	 * @param string $url The URL that the pagination will be used on.
	 *                    Example: (If $url = 'www.site.com/help')
	 *                    The second page will be: 'www.site.com/help/2'
	 * 
	 * @return string 	  Contains HTML string for the pages
	 */
	public function my_create_links($num_rows, $url)
	{
		$config['base_url']       = site_url($url);
		$config['total_rows']     = $num_rows;
		$config['per_page']       = $this->CI->config->item('per_page');
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

		$this->initialize($config);

		$page_links = $this->create_links();
		$page_links .= script_tag('js/page-link-fix.js'); 
		
		// Adds script to fix the page numbers not being clickable
		return $page_links;
	}
}

/* End of file MY_Pagination.php */
/* Location: ./application/libraries/MY_Pagination.php */

