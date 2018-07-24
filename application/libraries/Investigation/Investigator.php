<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Investigator
{
	protected $CI;

	public function __construct()
	{
        $this->CI =& get_instance();

        //Load models 
        $this->CI->load->model('Investigation/investigation_model');

	}

	/**
	 * Returns Data for the most recent investigations.
	 * The amount fetched is configured in $per_page set in appconfig.
	 *
	 * @param  integer $offset Offset for rows to fetch. Can use for pagination
	 * @return array          The array contains the following:
	 *                            'data' => sql object
	 *                            'total_rows' => Total results if query was not limited.
	 *                            					i.e. All possible results
	 *                            'num_rows' => Number of rows in the sql object
	 */
	public function recent_incidents($offset = 0)
	{
		$data = $this->CI->investigation_model->get_all_incidents($offset);
		return array( 
			'data' => $data, 
			'total_rows' => $this->CI->investigation_model->total_rows,
			'num_rows' => $data->num_rows()
		);
	}

	public function investigate()
	{
		
	}

}

/* End of file Investigator.php */
/* Location: ./application/libraries/Investigation/Investigator.php */
