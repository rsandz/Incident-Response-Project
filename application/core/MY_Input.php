<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Input Class
 * ==============
 * @author Ryan Sandoval
 * 
 * Extends the Input to have better functionality.
 */
class MY_Input extends CI_Input
{
    public function __construct()
    {
        parent::__construct();
        log_message('debug', 'MY_Input has been Loaded (Some functions in Input have been extended/replaced)');
    }

    /**
	 * Fetch an item from the POST array, but automatically returns NULL
     * if that item is == ''
	 *
	 * @param	mixed	$index		Index for item to be fetched from $_POST
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
    public function post($index = NULL, $xss_clean = NULL)
    {
        $result = parent::post($index, $xss_clean);
        if ($result == '')
        {
            return NULL;
        }
        else
        {
            return $result;
        }
    }

    

}

/* End of file Input.php */
