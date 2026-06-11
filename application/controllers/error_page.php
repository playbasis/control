<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error_page extends CI_Controller
{
    function error_404()
    {
        $data["heading"] = "404 Page Not Found";
        $data["message"] = "The page you requested was not found ";
        $this->load->view('error', $data);
    }
}

?>
