<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('array_clean'))
{
	function array_clean($array)
	{
		foreach($array as $key => $value)
		{
			if (empty($value))
			{
				unset($array[$key]);
			}
		}

		return $array;
	}
}
/* End of file MY_array_helper.php */
/* Location: ./application/helpers/MY_array_helper.php */
