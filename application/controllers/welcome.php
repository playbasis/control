<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/MY_Controller.php';

class Welcome extends MY_Controller
{
//class Welcome extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        if (!$this->User_model->isLogged()) {
            redirect('/login', 'refresh');
        }


        $lang = get_lang($this->session, $this->config);
        $this->lang->load($lang['name'], $lang['folder']);
    }

    public function index()
    {
        $this->load->view('welcome_message');
    }

    public function test()
    {
        $this->load->model('Reward_model');

        $data = array(
            'plan_id' => "52d910df8d8c89002d000124",
            'reward_id' => "52d910da8d8c89002d0000b8"
        );
        $this->Reward_model->getRewards($data);
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */