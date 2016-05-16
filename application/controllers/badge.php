<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

class Badge extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->model('badge_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function index_get($badgeId = 0)
    {
        $data = $this->validToken;

        if ($this->input->get('tags')){
            $data = array_merge($data, array(
                'tags' => explode(',', $this->input->get('tags'))
            ));
        }

        if ($badgeId) {
            try {
                $badgeId = new MongoId($badgeId);
            } catch (MongoException $ex) {
                $badgeId = null;
            }
            //get badge by specific id
            $result = $this->badge_model->getBadge(array_merge($data, array(
                'badge_id' => new MongoId($badgeId)
            )));

            if (!$result) {
                $this->response($this->error->setError('BADGE_NOT_FOUND'), 200);
            }

            $badge['badge'] = $result;
            $this->response($this->resp->setRespond($badge), 200);
        } else {
            //get all badge relate to  clients
            $badgesList['badges'] = $this->badge_model->getAllBadges(array_merge($data, array(
                'tags' => $this->input->get('tags') ? explode(',', $this->input->get('tags')) : null
            )));
            $this->response($this->resp->setRespond($badgesList), 200);
        }
    }

    public function test_get()
    {
        echo '<pre>';
        $credential = array(
            'key' => 'abc',
            'secret' => 'abcde'
        );
        $token = $this->auth_model->getApiInfo($credential);
        echo '<br>getAllBadges:<br>';
        $result = $this->badge_model->getAllBadges($token);
        print_r($result);
        echo '<br>getBadge:<br>';
        $result = $this->badge_model->getBadge(array_merge($token, array(
            'badge_id' => $result[0]['badge_id']
        )));
        print_r($result);
        echo '</pre>';
    }
}

?>
