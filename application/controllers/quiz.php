<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/libraries/REST2_Controller.php';

function index_weight($obj) {
    return $obj['weight'];
}

function index_quiz_id($obj) {
    return $obj['quiz_id'];
}

function index_pb_player_id($obj) {
    return $obj['pb_player_id'];
}

function convert_MongoId_id($obj) {
    $_id = $obj['_id'];
    unset($obj['_id']);
    $obj['quiz_id'] = $_id->{'$id'};
    return $obj;
}

function convert_MongoId_quiz_id($obj) {
    $_id = $obj['quiz_id'];
    unset($obj['quiz_id']);
    $obj['quiz_id'] = $_id->{'$id'};
    return $obj;
}

function convert_MongoId_question_id($obj) {
    $_id = $obj['question_id'];
    unset($obj['question_id']);
    $obj['question_id'] = $_id->{'$id'};
    return $obj;
}

function convert_MongoId_option_id($obj) {
    $_id = $obj['option_id'];
    unset($obj['option_id']);
    $obj['option_id'] = $_id->{'$id'};
    return $obj;
}

class Quiz extends REST2_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('action_model');
        $this->load->model('client_model');
        $this->load->model('player_model');
        $this->load->model('quiz_model');
        $this->load->model('reward_model');
        $this->load->model('email_model');
        $this->load->model('sms_model');
        $this->load->model('push_model');
        $this->load->model('tool/error', 'error');
        $this->load->model('tool/utility', 'utility');
        $this->load->model('tool/respond', 'resp');
        $this->load->model('tool/node_stream', 'node');
        $this->load->model('tracker_model');
    }

    public function list_get()
    {
        $this->benchmark->mark('start');

        /* param "player_id" */
        $player_id = $this->input->get('player_id');
        $nin = null;
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

        $type = $this->input->get('type');
        $results = $this->quiz_model->find($this->client_id, $this->site_id, $nin, $type);
        $results = array_map('convert_MongoId_id', $results);
        array_walk_recursive($results, array($this, "convert_mongo_object_and_image_path"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $results, 'processing_time' => $t)), 200);
    }

    public function detail_get($quiz_id = '')
    {
        $this->benchmark->mark('start');

        /* param "quiz_id" */
        if (empty($quiz_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('quiz_id')), 200);
        $quiz_id = new MongoId($quiz_id);
        $result = $this->quiz_model->find_by_id($this->client_id, $this->site_id, $quiz_id);
        if ($result === null) $this->response($this->error->setError('QUIZ_NOT_FOUND'), 200);

        /* param "player_id" */
        $player_id = $this->input->get('player_id');
        $record = null;
        if ($player_id !== false) {
            $pb_player_id = $this->player_model->getPlaybasisId(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'cl_player_id' => $player_id,
            ));
            if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);
            $record = $this->quiz_model->find_quiz_by_quiz_and_player($this->client_id, $this->site_id, $quiz_id, $pb_player_id);
        }

        $result = convert_MongoId_id($result);
        //$result['date_start'] = $result['date_start'] ? $result['date_start']->sec : null;
        //$result['date_expire'] = $result['date_expire'] ? $result['date_expire']->sec : null;
        $questions = isset($result['questions']) ? $result['questions'] : array();
        $total_max_score = 0;
        if (is_array($questions)) foreach ($questions as $question) {
            $total_max_score += $this->get_max_score_of_question($question['options']);
        }
        $result['total_max_score'] = $total_max_score;
        $result['total_questions'] = count($questions);
        unset($result['questions']);

        if ($record) {
            $result['questions'] = count($record['questions']);
            $result['total_score'] = $record['value'];
            $result['grade'] = $record['grade'];
            $result['date_join'] = $record['date_added']; // date which player start doing this quiz
        }

        array_walk_recursive($result, array($this, "convert_mongo_object_and_image_path"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function random_get($quest_id_to_skip=null)
    {
        $this->benchmark->mark('start');

        /* param "player_id" */
        $player_id = $this->input->get('player_id');
        $nin = null;
        if ($player_id === false) $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'cl_player_id' => $player_id,
        ));
        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $arr = $this->quiz_model->find_quiz_done_by_player($this->client_id, $this->site_id, $pb_player_id);
        $nin = array_map('index_quiz_id', $arr);
        $type = $this->input->get('type');
        $results = $this->quiz_model->find($this->client_id, $this->site_id, $type != 'poll' ? $nin : null, $type);
        $results = array_map('convert_MongoId_id', $results);

        $result = null;
        if ($results) {
            if ($quest_id_to_skip) {
                $results = $this->skip($results, $quest_id_to_skip);
                if (count($results) <= 0) array_push($results, $quest_id_to_skip); // we cannot skip if this is the only quiz left
            }
            $index = $this->random_weight(array_map('index_weight', $results));
            $result = $results[$index];

            array_walk_recursive($result, array($this, "convert_mongo_object_and_image_path"));
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    public function player_recent_get($player_id = '', $limit = 10)
    {
        $this->benchmark->mark('start');

        /* param "player_id" */
        if (empty($player_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'cl_player_id' => $player_id,
        ));
        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $results = $this->quiz_model->find_quiz_done_by_player($this->client_id, $this->site_id, $pb_player_id, $limit);
        $results = array_map('convert_MongoId_quiz_id', $results);
        array_walk_recursive($results, array($this, "convert_mongo_object_and_image_path"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $results, 'processing_time' => $t)), 200);
    }

    public function player_pending_get($player_id = '', $limit = 10)
    {
        $this->benchmark->mark('start');

        /* param "player_id" */
        if (empty($player_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'cl_player_id' => $player_id,
        ));
        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $results = $this->quiz_model->find_quiz_pending_by_player($this->client_id, $this->site_id, $pb_player_id, $limit);
        $results = array_map('convert_MongoId_quiz_id', $results);
        array_walk_recursive($results, array($this, "convert_mongo_object_and_image_path"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $results, 'processing_time' => $t)), 200);
    }

    public function question_get($quiz_id = '')
    {
        $this->benchmark->mark('start');

        /* param "quiz_id" */
        if (empty($quiz_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('quiz_id')), 200);
        $quiz_id = new MongoId($quiz_id);
        $quiz = $this->quiz_model->find_by_id($this->client_id, $this->site_id, $quiz_id);
        if ($quiz === null) $this->response($this->error->setError('QUIZ_NOT_FOUND'), 200);

        /* param "player_id" */
        $player_id = $this->input->get('player_id');
        $random = $this->input->get('random')== "1"?true:false;
        if ($player_id === false) $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'cl_player_id' => $player_id,
        ));
        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $result = $this->quiz_model->find_quiz_by_quiz_and_player($this->client_id, $this->site_id, $quiz_id, $pb_player_id);

        if (isset($quiz['question_order']) && $quiz['question_order'] ){
            if ($random){
                $this->response($this->error->setError('QUIZ_QUESTION_NOT_ALLOW_RANDOM'), 200);
            }
            $quiz['questions'] = $this->sortArray($quiz['questions'],"question_number","question");
        }

        $completed_questions = $result ? $result['questions'] : array();
        $question = null;
        $index = -1;
        $remain_count = count($completed_questions) - count($quiz['questions']);
        foreach ($quiz['questions'] as $i => $q) {
            if (!in_array($q['question_id'], $completed_questions)) {
                $question = $q; // get the first question in the quiz that the player has not submitted an answer
                $index = $i;
                if (($remain_count != 0) && (rand() %$remain_count == 0) ) break;
            }
        }
        if ($question) {
            $question['index'] = $index+1;
            $question['total'] = count($quiz['questions']);
            $question = convert_MongoId_question_id($question);
            foreach ($question['options'] as &$option) {
                $option = convert_MongoId_option_id($option);
                unset($option['score']);
                unset($option['explanation']);
            }
            array_walk_recursive($question, array($this, "convert_mongo_object_and_image_path"));
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $question, 'processing_time' => $t)), 200);
    }

    public function answer_post($quiz_id = '')
    {
        $this->benchmark->mark('start');

        /* param "quiz_id" */
        if (empty($quiz_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('quiz_id')), 200);
        $quiz_id = new MongoId($quiz_id);
        $quiz = $this->quiz_model->find_by_id($this->client_id, $this->site_id, $quiz_id);
        if ($quiz === null) $this->response($this->error->setError('QUIZ_NOT_FOUND'), 200);

        /* param "player_id" */
        $player_id = $this->input->post('player_id');
        if ($player_id === false) $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);
        $pb_player_id = $this->player_model->getPlaybasisId(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'cl_player_id' => $player_id,
        ));
        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        /* param "question_id" */
        $question_id = $this->input->post('question_id');
        if ($question_id === false) $this->response($this->error->setError('PARAMETER_MISSING', array('question_id')), 200);
        $question_id = new MongoId($question_id);
        $question = null;
        $total_max_score = 0;
        foreach ($quiz['questions'] as $q) {
            $total_max_score += $this->get_max_score_of_question($q['options']);
            if ($q['question_id'] == $question_id) {
                $question = $q;
            }
        }
        if (!$question) $this->response($this->error->setError('QUIZ_QUESTION_NOT_FOUND'), 200);

        /* param "option_id" */
        $option_id = $this->input->post('option_id');
        if ($option_id === false) $this->response($this->error->setError('PARAMETER_MISSING', array('option_id')), 200);
        $option_id = new MongoId($option_id);
        $option = null;
        $max_score = -1;
        foreach ($question['options'] as $o) {
            if ($o['score'] > $max_score) $max_score = $o['score'];
            if ($o['option_id'] == $option_id) {
                $option = $o;
            }
        }
        if (!$option) $this->response($this->error->setError('QUIZ_OPTION_NOT_FOUND'), 200);

        /* check to see if the question has already been answered by the player */
        $result = $this->quiz_model->find_quiz_by_quiz_and_player($this->client_id, $this->site_id, $quiz_id, $pb_player_id);
        $completed_questions = $result ? $result['questions'] : array();
        if (in_array($question_id, $completed_questions)) $this->response($this->error->setError('QUIZ_QUESTION_ALREADY_COMPLETED'), 200);

        if (isset($quiz['question_order']) && $quiz['question_order'] ){
            $quiz['questions'] = $this->sortArray($quiz['questions'],"question_number","question");
            $index = 0;
            if (!empty($completed_questions)){
                $latest_id = end($completed_questions); // get latest question id
                if(is_array($quiz['questions'])) foreach ($quiz['questions'] as $index => $q){
                    if ($latest_id == $q['question_id']) {
                        $index++;
                        break;
                    }
                }
            }
            if (($index) >= count($quiz['questions']) ||  $question_id != $quiz['questions'][$index]['question_id']){
                $this->response($this->error->setError('QUIZ_QUESTION_OUT_OF_SEQUENCE'), 200);
            }
        }

        /* get score from answering that option */
        $score = intval($option['score']);
        $explanation = $option['explanation'];
        $acc_score = $result ? $result['value'] : 0;
        $total_score = $acc_score + $score;

        /* if this is the last question, then grade the player's score */
        $grade = array();
        if (count($completed_questions) + 1 >= count($quiz['questions'])) {
            $percent = $total_max_score ? ($total_score*1.0)/$total_max_score*100 : 100;
            if (isset($quiz['grades'])) foreach ($quiz['grades'] as $g) {
                if ($g['start'] <= $percent && ($g['end'] < 100 ? $percent < $g['end'] : $percent <= $g['end'])) {
                    $grade = $g;
                    break;
                }
            }
            /* fire complete-quiz action */
            $completeQuizActionId = $this->action_model->findAction(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'action_name' => ACTION_COMPLETE_QUIZ,
            ));

            $platform = $this->auth_model->getOnePlatform($this->client_id, $this->site_id);
            $this->utility->request('engine', 'json', urlencode(json_encode(array(
                'api_key' => $platform['api_key'],
                'pb_player_id' => $pb_player_id.'',
                'action' => ACTION_COMPLETE_QUIZ,
            ))));
        }

        /* check to see if grade has any reward associated with it */
        $rewards = isset($grade['rewards']) ? $this->update_rewards($this->client_id, $this->site_id, $pb_player_id, $player_id, $grade['rewards']) : array();
        $grade['rewards'] = $this->filter_levelup($rewards);
        $grade['score'] = $score;
        $grade['max_score'] = $max_score;
        $grade['total_score'] = $total_score;
        $grade['total_max_score'] = $total_max_score;

        /* update player's score */
        $this->quiz_model->update_player_score($this->client_id, $this->site_id, $quiz_id, $pb_player_id, $question_id, $option_id, $score, $grade);

        $this->tracker_model->trackQuiz(array(
            'client_id' => $this->client_id,
            'site_id' => $this->site_id,
            'pb_player_id' => $pb_player_id,
            'quiz_id' => $quiz_id,
            'quiz_name' => $quiz['name'],
            'question' => $question,
            'option' => $option,
            'grade' => $grade,
            'quiz_completed' => false,
        ));
        if (isset($completeQuizActionId) && $completeQuizActionId) {
            $this->tracker_model->trackQuiz(array(
                'client_id' => $this->client_id,
                'site_id' => $this->site_id,
                'pb_player_id' => $pb_player_id,
                'quiz_id' => $quiz_id,
                'quiz_name' => $quiz['name'],
                'grade' => $grade,
                'quiz_completed' => true,
            ));
        }
        /* publish the reward (if any) */
        if (is_array($rewards)) foreach ($rewards as $reward) $this->publish_event($this->client_id, $this->site_id, $pb_player_id, $player_id, $quiz, $this->validToken['domain_name'], $reward);

        /* send feedback as necessary */
        if (isset($grade['feedbacks'])) {
            foreach (array('email', 'sms') as $type) {
                if (isset($grade['feedbacks'][$type]) && is_array($grade['feedbacks'][$type])) foreach ($grade['feedbacks'][$type] as $template_id => $val) {
                    if (isset($val['checked']) && $val['checked']) {
                        switch ($type) {
                        case 'email':
                            $this->processEmail(array(
                                'client_id' => $this->client_id,
                                'site_id' => $this->site_id,
                                'pb_player_id' => $pb_player_id,
                                'template_id' => $template_id,
                                'subject' => $val['subject'],
                            ));
                            break;
                        case 'sms':
                            $this->processSms(array(
                                'client_id' => $this->client_id,
                                'site_id' => $this->site_id,
                                'pb_player_id' => $pb_player_id,
                                'template_id' => $template_id,
                            ));
                            break;
                        case 'push':
                            $this->processPushNotification(array(
                                'client_id' => $this->client_id,
                                'site_id' => $this->site_id,
                                'pb_player_id' => $pb_player_id,
                                'template_id' => $template_id,
                            ));
                            break;
                        default:
                            break;
                        }
                    }
                }
            }
        }

        /* data */
        unset($grade['rewards']);
        $data = array(
            'options' => $question['options'], // return list of options with score and explanation
            'score' => $score,
            'max_score' => $max_score,
            'explanation' => $explanation,
            'total_score' => $total_score,
            'total_max_score' => $total_max_score,
            'grade' => $grade,
            'rewards' => $rewards
        );
        array_walk_recursive($data, array($this, "convert_mongo_object_and_image_path"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $data, 'processing_time' => $t)), 200);
    }

    public function rank_get($quiz_id = '', $limit = 10)
    {
        $this->benchmark->mark('start');

        /* param "quiz_id" */
        if (empty($quiz_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('quiz_id')), 200);
        $quiz_id = new MongoId($quiz_id);
        $quiz = $this->quiz_model->find_by_id($this->client_id, $this->site_id, $quiz_id);
        if ($quiz === null) $this->response($this->error->setError('QUIZ_NOT_FOUND'), 200);

        $results = array();
        $ranks = $this->quiz_model->sort_players_by_score($this->client_id, $this->site_id, $quiz_id, $limit);
        if ($ranks) foreach ($ranks as $rank) {
            $results[] = array(
                'pb_player_id' => $rank['pb_player_id'],
                'player_id' => $this->player_model->getClientPlayerId($rank['pb_player_id'], $this->site_id),
                'score' => $rank['value'],
            );
        }
        $nin = array_map('index_pb_player_id', $results);
        if (count($nin) < $limit) {
            $more = $limit - count($nin);
            $players = $this->player_model->find_player_with_nin($this->client_id, $this->site_id, $nin, $more);
            if ($players) foreach ($players as $player) {
                $results[] = array(
                    'pb_player_id' => $player['_id'],
                    'player_id' => $player['cl_player_id'],
                    'score' => 0,
                );
            }
        }

        array_walk_recursive($results, array($this, "convert_mongo_object_and_image_path"));

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $results, 'processing_time' => $t)), 200);
    }

    public function stat_get($quiz_id = '')
    {
        $this->benchmark->mark('start');

        /* param "quiz_id" */
        if (empty($quiz_id)) $this->response($this->error->setError('PARAMETER_MISSING', array('quiz_id')), 200);
        $quiz_id = new MongoId($quiz_id);
        $quiz = $this->quiz_model->find_by_id($this->client_id, $this->site_id, $quiz_id);
        if ($quiz === null) $this->response($this->error->setError('QUIZ_NOT_FOUND'), 200);

        $result = array();
        $stat = $this->quiz_model->calculate_frequency($this->client_id, $this->site_id, $quiz_id);
        $n = count($quiz['questions']);
        foreach ($quiz['questions'] as $i => $q) {
            $question_id = strval($q['question_id']);
            $options = $q['options'];
            $options = array();
            if ($q['options']) foreach ($q['options'] as $o) {
                $option_id = strval($o['option_id']);
                array_push($options, array(
                    'option' => $o['option'],
                    'option_image' => $o['option_image'],
                    'option_id' => $option_id,
                    'count' => isset($stat[$question_id][$option_id]) ? $stat[$question_id][$option_id] : 0,
                ));
            }
            array_push($result, array(
                'question' => $q['question'],
                'question_image' => $q['question_image'],
                'question_id' => strval($q['question_id']),
                'options' => $options,
                'index' => $i+1,
                'total' => $n,
            ));
        }

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $result, 'processing_time' => $t)), 200);
    }

    /*
     * reset quiz
     *
     * @param player_id string player id of client
     * @param quiz_id string (optional) id of quiz
     * return array
     */
    public function reset_post(){
        $this->benchmark->mark('start');

        $player_id = $this->input->post('player_id') ? $this->input->post('player_id') : $this->response($this->error->setError('PARAMETER_MISSING', array('player_id')), 200);

        $pb_player_id = $this->player_model->getPlaybasisId(array_merge($this->validToken, array(
            'cl_player_id' => $player_id
        )));

        if (!$pb_player_id) $this->response($this->error->setError('USER_NOT_EXIST'), 200);

        $quiz_id = $this->input->post('quiz_id') ? new MongoId($this->input->post('quiz_id')) : null;
        $results = $this->quiz_model->delete($this->client_id, $this->site_id, $pb_player_id, $quiz_id);

        $this->benchmark->mark('end');
        $t = $this->benchmark->elapsed_time('start', 'end');
        $this->response($this->resp->setRespond(array('result' => $results, 'processing_time' => $t)), 200);
    }

    private function filter_levelup($events) {
        $result = array();
        foreach ($events as $event) {
            if ($event['event_type'] == 'LEVEL_UP') continue;
            array_push($result, $event);
        }
        return $result;
    }

    private function update_rewards($client_id, $site_id, $pb_player_id, $cl_player_id, $rewards) {
        $events = array();
        foreach ($rewards as $type => $reward) {
            switch ($type) {
            case 'exp':
                $name = 'exp';
                $id = $this->reward_model->findByName(array('client_id' => $client_id, 'site_id' => $site_id), $name);
                $value = $reward['exp_value'];
                // update player's exp
                $lv = $this->client_model->updateExpAndLevel($value, $pb_player_id, $cl_player_id, array(
                    'client_id' => $client_id,
                    'site_id' => $site_id
                ));
                // if level up
                if ($lv > 0) {
                    array_push($events, array(
                        'event_type' => 'LEVEL_UP',
                        'value' => $lv
                    ));
                }
                array_push($events, array(
                    'event_type' => 'REWARD_RECEIVED',
                    'reward_type' => $name,
                    'reward_id' => $id,
                    'value' => $value
                ));
                break;
            case 'point':
                $name = 'point';
                $id = $this->reward_model->findByName(array('client_id' => $client_id, 'site_id' => $site_id), $name);
                $value = $reward['point_value'];
                $return_data = array();
                $this->client_model->updateCustomReward($name, $value, array(
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'pb_player_id' => $pb_player_id,
                    'player_id' => $cl_player_id
                ), $return_data);
                array_push($events, array(
                    'event_type' => 'REWARD_RECEIVED',
                    'reward_type' => $name,
                    'reward_id' => $id,
                    'value' => $value
                ));
                break;
            case 'badge':
                if (is_array($reward)) foreach ($reward as $badge) {
                    $id = $badge['badge_id'];
                    $value = $badge['badge_value'];
                    $this->client_model->updateplayerBadge($id, $value, $pb_player_id, $cl_player_id, $client_id, $site_id);
                    $badgeData = $this->client_model->getBadgeById($id, $site_id);
                    if (!$badgeData) break;
                    array_push($events, array(
                        'event_type' => 'REWARD_RECEIVED',
                        'reward_type' => 'badge',
                        'reward_id' => $id,
                        'reward_data' => $badgeData,
                        'value' => $value
                    ));
                }
                break;
            case 'custom':
                if (is_array($reward)) foreach ($reward as $custom) {
                    $name = $this->reward_model->getRewardName(array('client_id' => $client_id, 'site_id' => $site_id), $custom['custom_id']);
                    $id = $custom['custom_id'];
                    $value = $custom['custom_value'];
                    $return_data = array();
                    $this->client_model->updateCustomReward($name, $value, array(
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'pb_player_id' => $pb_player_id,
                        'player_id' => $cl_player_id
                    ), $return_data);
                    array_push($events, array(
                        'event_type' => 'REWARD_RECEIVED',
                        'reward_type' => $name,
                        'reward_id' => $id,
                        'value' => $value
                    ));
                }
                break;
            default:
                log_message('error', 'Unsupported type = '.$type);
                break;
            }
        }
        return $events;
    }

    private function publish_event($client_id, $site_id, $pb_player_id, $cl_player_id, $quiz, $domain_name, $event) {
        $message = null;
        if($event['value'] == 0 || empty($event['value']))return;
        
        switch ($event['event_type']) {
        case 'LEVEL_UP':
            $message = array('message' => $this->utility->getEventMessage('level', '', '', '', $event['value']), 'level' => $event['value']);
            $this->tracker_model->trackEvent('LEVEL', $message['message'], array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'pb_player_id' => $pb_player_id,
                'player_id' => $cl_player_id,
                'action_log_id' => null,
                'amount' => $event['value']
            ));
            break;
        case 'REWARD_RECEIVED':
            switch ($event['reward_type']) {
            case 'badge':
                $message = array('message' => $this->utility->getEventMessage('badge', '', '', $event['reward_data']['name']), 'badge' => $event['reward_data']);
                $this->tracker_model->trackEvent('REWARD', $message['message'], array(
                    'pb_player_id'	=> $pb_player_id,
                    'client_id'		=> $client_id,
                    'site_id'		=> $site_id,
                    'quiz_id'		=> $quiz['_id'],
                    'reward_type'	=> 'badge',
                    'reward_id'	    => $this->player_model->get_reward_id_by_name($this->validToken, 'badge'),
                    'reward_name'	=> $event['reward_type'],
                    'item_id'	    => $event['reward_id'],
                    'amount'	    => $event['value']
                ));
                break;
            default:
                $message = array('message' => $this->utility->getEventMessage('point', $event['value'], $event['reward_type']), 'amount' => $event['value'], 'point' => $event['reward_type']);
                $this->tracker_model->trackEvent('REWARD', $message['message'], array(
                    'pb_player_id'	=> $pb_player_id,
                    'client_id'		=> $client_id,
                    'site_id'		=> $site_id,
                    'quiz_id'		=> $quiz['_id'],
                    'reward_type'	=> 'point',
                    'reward_id'	    => $event['reward_id'],
                    'reward_name'	=> $event['reward_type'],
                    'amount'	    => $event['value']
                ));
                break;
            }
            break;
        }
        if ($message) {
            if ($event['event_type'] == 'LEVEL_UP') {
                $this->node->publish(array(
                    'client_id' => $client_id,
                    'site_id' => $site_id,
                    'pb_player_id' => $pb_player_id,
                    'player_id' => $cl_player_id,
                    'action_name' => 'quiz_reward',
                    'action_icon' => 'fa-trophy',
                    'message' => $message['message'],
                    'level' => $event['value'],
                    'quiz' => $quiz,
                ), $domain_name, $site_id);
            } else {
                if ($event['reward_type'] == 'badge') {
                    $this->node->publish(array(
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'pb_player_id' => $pb_player_id,
                        'player_id' => $cl_player_id,
                        'action_name' => 'quiz_reward',
                        'action_icon' => 'fa-trophy',
                        'message' => $message['message'],
                        'badge' => $event['reward_data'],
                        'quiz' => $quiz,
                    ), $domain_name, $site_id);
                } else {
                    $this->node->publish(array(
                        'client_id' => $client_id,
                        'site_id' => $site_id,
                        'pb_player_id' => $pb_player_id,
                        'player_id' => $cl_player_id,
                        'action_name' => 'quiz_reward',
                        'action_icon' => 'fa-trophy',
                        'message' => $message['message'],
                        'amount' => $event['value'],
                        'point' => $event['reward_type'],
                        'quiz' => $quiz,
                    ), $domain_name, $site_id);
                }
            }
        }
    }

    private function skip($results, &$skip_id) {
        $ret = array();
        foreach ($results as $result) {
            if ($result['quiz_id'] == $skip_id) {
                $skip_id = $result;
                continue;
            }
            array_push($ret, $result);
        }
        return $ret;
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

    private function processEmail($input) {
        /* check permission according to billing cycle */
        $access = true;
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $input['client_id'],
                $input['site_id'],
                "notifications",
                "email"
            );
        } catch(Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED")
                $access = false;
        }
        if (!$access) return false;

        /* get email */
        $player = $this->player_model->getById($input['site_id'], $input['pb_player_id']);
        $email = $player && isset($player['email']) ? $player['email'] : null;
        if (!$email) return false;

        /* check blacklist */
        $res = $this->email_model->isEmailInBlackList($email, $input['site_id']);
        if ($res) return false; // banned

        /* check valid template_id */
        $template = $this->email_model->getTemplateById($input['site_id'], $input['template_id']);
        if (!$template) return false;

        /* send email */
        $from = EMAIL_FROM;
        $to = $email;
        $subject = $input['subject'];
        if (!isset($player['code']) && strpos($template['body'], '{{code}}') !== false) $player['code'] = $this->player_model->generateCode($input['pb_player_id']);
        $message = $this->utility->replace_template_vars($template['body'], $player);
        $response = $this->utility->email($from, $to, $subject, $message);
        $this->email_model->log(EMAIL_TYPE_USER, $input['client_id'], $input['site_id'], $response, $from, $to, $subject, $message);
        return $response != false;
    }

    private function processSms($input) {
        /* check permission according to billing cycle */
        $access = true;
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $input['client_id'],
                $input['site_id'],
                "notifications",
                "sms"
            );
        } catch(Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED")
                $access = false;
        }
        if (!$access) return false;

        /* get phone number */
        $player = $this->player_model->getById($input['site_id'], $input['pb_player_id']);
        $phone = $player && isset($player['phone_number']) ? $player['phone_number'] : null;
        if (!$phone) return false;

        /* check valid template_id */
        $template = $this->sms_model->getTemplateById($input['site_id'], $input['template_id']);
        if (!$template) return false;

        /* send SMS */
        $this->config->load("twilio",TRUE);
        $config = $this->sms_model->getSMSClient($input['client_id'], $input['site_id']);
        $twilio = $this->config->item('twilio');
        $config['api_version'] = $twilio['api_version'];
        $this->load->library('twilio/twiliomini', $config);
        $from = $config['number'];
        $to = $phone;
        if (!isset($player['code']) && strpos($template['body'], '{{code}}') !== false) $player['code'] = $this->player_model->generateCode($input['pb_player_id']);
        $message = $this->utility->replace_template_vars($template['body'], $player);
        $response = $this->twiliomini->sms($from, $to, $message);
        $this->sms_model->log($input['client_id'], $input['site_id'], 'user', $from, $to, $message, $response);
        return $response->IsError;
    }

    private function processPushNotification($input)
    {
        /* check permission according to billing cycle */
        $access = true;
        try {
            $this->client_model->permissionProcess(
                $this->client_data,
                $input['client_id'],
                $input['site_id'],
                "notifications",
                "push"
            );
        } catch(Exception $e) {
            if ($e->getMessage() == "LIMIT_EXCEED")
                $access = false;
        }
        if (!$access) return false;

        /* get devices */
        $player = $this->player_model->getById($input['site_id'], $input['pb_player_id']);
        $devices = $this->player_model->listDevices($input['client_id'], $input['site_id'], $input['pb_player_id'], array('device_token', 'os_type'));
        if (!$devices) return false;

        /* check valid template_id */
        $template = $this->push_model->getTemplateById($input['site_id'], $input['template_id']);
        if (!$template) return false;

        /* send push notification */
        if (!isset($player['code']) && strpos($template['body'], '{{code}}') !== false) $player['code'] = $this->player_model->generateCode($input['pb_player_id']);
        $message = $this->utility->replace_template_vars($template['body'], $player);
        foreach ($devices as $device) {
            $this->push_model->initial(array(
                'device_token' => $device['device_token'],
                'messages' => $message,
                'badge_number' => 1,
                'data' => null,
            ), $device['os_type']);
        }
        return true;
    }

    /**
     * Use with array_walk and array_walk_recursive.
     * Recursive iterable items to modify array's value
     * from MongoId to string and MongoDate to readable date
     * @param mixed $item this is reference
     * @param string $key
     */
    private function convert_mongo_object(&$item, $key) {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else if (get_class($item) === 'MongoDate') {
                $item = datetimeMongotoReadable($item);
            }
        }
    }

    private function convert_mongo_object_and_image_path(&$item, $key)
    {
        if (is_object($item)) {
            if (get_class($item) === 'MongoId') {
                $item = $item->{'$id'};
            } else if (get_class($item) === 'MongoDate') {
                $item =  datetimeMongotoReadable($item);
            }
        }
        if($key === "image"){
            if(!empty($item)){
                $pattern = '#^'.$this->config->item('IMG_PATH').'#';
                preg_match($pattern, $item, $matches);
                if(!$matches){
                    $item = $this->config->item('IMG_PATH').$item;
                }
            }else{
                $item = $this->config->item('IMG_PATH')."no_image.jpg";
            }
        }
        if($key === "description_image"){
            if(!empty($item)){
                $item = $this->config->item('IMG_PATH').$item;
            }else{
                $item = $this->config->item('IMG_PATH')."no_image.jpg";
            }
        }
        if($key === "rank_image"){
            if(!empty($item)){
                $item = $this->config->item('IMG_PATH').$item;
            }else{
                $item = $this->config->item('IMG_PATH')."no_image.jpg";
            }
        }
        if($key === "question_image"){
            if(!empty($item)){
                $item = $this->config->item('IMG_PATH').$item;
            }else{
                $item = $this->config->item('IMG_PATH')."no_image.jpg";
            }
        }
        if($key === "option_image"){
            if(!empty($item)){
                $item = $this->config->item('IMG_PATH').$item;
            }else{
                $item = $this->config->item('IMG_PATH')."no_image.jpg";
            }
        }
    }
    private function sortArray ($list, $sort_by, $name){
        $result = $list;
        foreach ($list as $key => $raw){

            $temp_name[$key] = $raw[$name];
            $temp_value[$key] =  $raw[$sort_by];
        }
        if (isset($temp_value) && isset($temp_name))
        {
            array_multisort( $temp_value, SORT_ASC,$temp_name, SORT_ASC, $result);
        }
        return $result;
    }
}
?>