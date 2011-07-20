<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
    
    function __construct() 
    {
        parent::__construct();
        
        $this->load->helper('url');
        
        $this->menu_pages = array(
              '' => 'Home',
              'main/about' => 'About'
            );
        
        $this->active = uri_string();
        
        $this->data['menu'] = $this->menu->render($this->menu_pages, $this->active);
        
    }

    public function index()
    {
        $this->data['page'] = 'main/home';
        $this->data['page_name'] = 'Home';

        $this->load->view('template', $this->data);
    }

    public function about()
    {
        $this->data['page'] = 'main/home';
        $this->data['page_name'] = 'About';

        $this->load->view('template',$this->data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
