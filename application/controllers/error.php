<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends CI_Controller {
    
    function __construct() 
    {
        parent::__construct();
    }

    public function error_404()
    {
        echo 'error';
    }

   
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

