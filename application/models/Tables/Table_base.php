<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Class for Table Models
 * ===========================
 * Contains common functions
 */
class Table_base extends MY_Model 
{
	/** @var array The sorting values */
	protected $sort;

    /**
	 * Constructor for this class
	 * Loads the necessary resources
	 */
	public function __construct()
	{
		parent::__construct();
		$this->admin_mode = $this->authentication->check_admin();
		$this->user_to_lock = FALSE;
		$this->sort = array('sort_field' => NULL, 'sort_dir' => 'desc');
	}
    
	/*
		Sorting Functions
	*/

	/**
	 * Sets the sort field and direction during get methods
	 * @param string|array $field Field to sort by. If given array, will extract
	 * 						sort_field and sort_dir from it
	 * @param string $dir asc or desc
	 */
	public function sort($field, $dir = NULL)
	{
		if (is_array($field))
		{
			//sort_field and sort_dir in array
			$this->sort = $field;
			return $this;
		}

		if ($dir != 'desc' && $dir != 'asc') $dir = 'desc';
		$this->sort = array(
			'sort_field' => $field,
			'sort_dir' => $dir
		);
		return $this;
	}

	/**
	 * Applies sorting for database queries
	 * @return Table_base Method Chaining
	 */
	protected function apply_sort()
	{
		$this->db->order_by($this->sort['sort_field'], $this->sort['sort_dir']);
		return $this;
	}
    

}

/* End of file Table_base.php */

