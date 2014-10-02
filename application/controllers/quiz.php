<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

function index_weight($obj) {
    return $obj['weight'];
}

function index_quiz_id($obj) {
    return $obj['quiz_id'];
}

function convert_MongoId_id($obj) {
    $_id = $obj['_id'];
    unset($obj['_id']);
    $obj['id'] = $_id->{'$id'};
    return $obj;
}

function convert_MongoId_quiz_id($obj) {
    $_id = $obj['quiz_id'];
    unset($obj['quiz_id']);
    $obj['id'] = $_id->{'$id'};
    return $obj;
}

class Quiz extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('player_model');
        $this->load->model('quiz_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
        $this->load->model('tracker_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/respond', 'resp');
    }

    public function list_get()
    {
        $this->benchmark->mark('start');

        $result = null;
        $nin = null;
        $player_id = $this->input->get('player_id');
        if ($player_id !== false) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'cl_player_id' => $player_id,
            ));
            if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            $arr = $this->quiz_model->find_quiz_done_by_player($this->client_id, $this->site_id, $pb_player_id);
            $nin = array_map('index_quiz_id', $arr);
        }
        $result = $this->quiz_model->find($this->client_id, $this->site_id, $nin);
        $result = array_map('convert_MongoId_id', $result);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function detail_get($quiz_id=null)
    {
        $this->benchmark->mark('start');

        if (empty($quiz_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('quiz_id')), 200);

        $result = $this->quiz_model->find_by_id($this->client_id, $this->site_id, new MongoId($quiz_id));
        if ($result === null) $this->response($this->error->setError('QUIZ_NOT_FOUND'), 200);
        $result = convert_MongoId_id($result);
        $result['date_start'] = $result['date_start'] ? $result['date_start']->sec : null;
        $result['date_expire'] = $result['date_expire'] ? $result['date_expire']->sec : null;
        $questions = $result['questions'];
        $total_score = 0;
        if (is_array($questions)) foreach ($questions as $question) {
            $total_score += $this->get_max_score_of_question($question['options']);
        }
        $result['total_score'] = $total_score;
        unset($result['questions']);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function random_get()
    {
        $this->benchmark->mark('start');

        $result = null;
        $nin = null;
        $player_id = $this->input->get('player_id');
        if ($player_id === false) $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);

        $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'cl_player_id' => $player_id,
        ));
        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        $arr = $this->quiz_model->find_quiz_done_by_player($this->client_id, $this->site_id, $pb_player_id);
        $nin = array_map('index_quiz_id', $arr);
        $result = $this->quiz_model->find($this->client_id, $this->site_id, $nin);
        $result = array_map('convert_MongoId_id', $result);

        if ($result) {
            $index = $this->random_weight(array_map('index_weight', $result));
            $result = $result[$index];
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function recent_get($player_id)
    {
        $this->benchmark->mark('start');

        if (empty($player_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);

        $result = null;
        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'cl_player_id' => $player_id,
        ));
        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);
        $result = $this->quiz_model->find_quiz_done_by_player($this->client_id, $this->site_id, $pb_player_id, 5);
        $result = array_map('convert_MongoId_quiz_id', $result);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    private function random_weight($weights) {
        if (!is_array($weights) || !(count($weights) > 0)) throw new Exception("$weights is not a non-empty array");
        $sum = 0;
        $acc = array();
        foreach ($weights as $weight) {
            $sum += $weight;
            array_push($acc, $sum);
        }
        $max = $acc[count($acc)-1];
        $ran = rand(0, $max-1);
        foreach ($acc as $i => $value) {
            if ($ran < $value) return $i;
        }
        return 0;
    }

    private function get_max_score_of_question($options) {
        $max = -1;
        if (is_array($options)) foreach ($options as $option) {
            $score = $option['score'];
            if ($score > $max) $max = $score;
        }
        return $max;
    }
}
?>