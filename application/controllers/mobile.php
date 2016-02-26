<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST2_Controller.php';

class Mobile extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //mock mobile request
    public function checkin_post()
    {

        $data = array(
            'status' => true,
            'message' => 'success',
            'data' => array(
                'image' => null,
                'text_message' => 'some message',
                'post' => null,
            ),
        );

        $post = $this->input->post();

        if (!isset($post['keyword']) || empty($post['keyword'])) {
            $data['message'] = 'default message';
            $data['data']['image'] = 'default image';

        } else {

            switch ($post['keyword']) {
                //select image here and message here
            }
        }

        if ($post) {
            $data['data']['post'] = $post;
        } else {
            $data['data']['post'] = array();
        }

        //response
        $this->response($data, 200);
    }

    public function checkin_get()
    {

        $data = array(
            'status' => false,
            'message' => 'this service not available for GET method. Try POST method instead',
            'data' => array(),
        );

        //response
        $this->response($data, 200);
    }
}