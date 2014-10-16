<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quiz_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('memcached_library');
        $this->load->helper('memcache');
    }

    public function find($client_id, $site_id, $nin=null) {
        $d = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('name','image','description','description_image','weight'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where(array('status' => true, 'deleted' => false));
        $this->mongo_db->where(array('$and' => array(
            array('$or' => array(array('date_start' => array('$lte' => $d)), array('date_start' => null))),
            array('$or' => array(array('date_expire' => array('$gt' => $d)), array('date_expire' => null)))
        )));
        if ($nin !== null) $this->mongo_db->where_not_in('_id', $nin);
        $result = $this->mongo_db->get('playbasis_quiz_to_client');

        return $result;
    }

    public function find_by_id($client_id, $site_id, $quiz_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(),array('client_id','site_id','date_added','date_modified'));
        $this->mongo_db->where('_id', $quiz_id);
        $results = $this->mongo_db->get('playbasis_quiz_to_client');

        return $results ? $results[0] : null;
    }

    public function find_quiz_by_quiz_and_player($client_id, $site_id, $quiz_id, $pb_player_id) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('quiz_id','value','questions','grade','date_added','date_modified'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where('quiz_id', $quiz_id);
        $this->mongo_db->where('pb_player_id', $pb_player_id);
        $results = $this->mongo_db->get('playbasis_quiz_to_player');

        return $results ? $results[0] : null;
    }

    public function find_quiz_by_player($client_id, $site_id, $pb_player_id, $limit=-1) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('quiz_id','value','questions','grade'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where('pb_player_id', $pb_player_id);
        $this->mongo_db->order_by(array('date_modified' => -1));
        if ($limit > 0) $this->mongo_db->limit($limit);
        $results = $this->mongo_db->get('playbasis_quiz_to_player');

        return $results;
    }

    public function find_quiz_pending_and_done_by_player($client_id, $site_id, $pb_player_id, $limit=-1) {
        $output = array('pending' => array(), 'completed' => array());
        $results = $this->find_quiz_by_player($client_id, $site_id, $pb_player_id, $limit);
        if (is_array($results)) foreach ($results as $result) {
            $quiz_id = $result['quiz_id'];
            $quiz = $this->find_by_id($client_id, $site_id, $quiz_id);
            $total_questions = count($quiz['questions']);
            $completed_questions = count($result['questions']);
            $pending = $completed_questions < $total_questions;
            $result['total_completed_questions'] = $completed_questions;
            if ($pending) $result['total_pending_questions'] = $total_questions - $completed_questions;
            unset($result['questions']);
            array_push($output[$pending ? 'pending' : 'completed'], $result);
        }
        return $output;
    }

    public function find_quiz_pending_by_player($client_id, $site_id, $pb_player_id, $limit=-1) {
        $results = $this->find_quiz_pending_and_done_by_player($client_id, $site_id, $pb_player_id, $limit);
        return $results['pending'];
    }

    public function find_quiz_done_by_player($client_id, $site_id, $pb_player_id, $limit=-1) {
        $results = $this->find_quiz_pending_and_done_by_player($client_id, $site_id, $pb_player_id, $limit);
        return $results['completed'];
    }

    public function update_player_score($client_id, $site_id, $quiz_id, $pb_player_id, $question_id, $score, $grade) {
        $d = new MongoDate(time());
        $result = $this->find_quiz_by_quiz_and_player($client_id, $site_id, $quiz_id, $pb_player_id);
        if (!$result) {
            return $this->mongo_db->insert('playbasis_quiz_to_player', array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'quiz_id' => $quiz_id,
                'pb_player_id' => $pb_player_id,
                'value' => $score,
                'questions' => array($question_id),
                'grade' => $grade,
                'date_added' => $d,
                'date_modified' => $d
            ));
        } else {
            $questions = $result['questions'];
            array_push($questions, $question_id);
            $this->mongo_db->where('client_id', $client_id);
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->where('quiz_id', $quiz_id);
            $this->mongo_db->where('pb_player_id', $pb_player_id);
            $this->mongo_db->set('questions', $questions);
            $this->mongo_db->set('value', $score);
            $this->mongo_db->set('grade', $grade);
            $this->mongo_db->set('date_modified', $d);
            return $this->mongo_db->update('playbasis_quiz_to_player');
        }
    }

    public function sort_players_by_score($client_id, $site_id, $quiz_id, $limit) {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('pb_player_id','value'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('quiz_id', $quiz_id);
        $this->mongo_db->order_by(array('value' => 'DESC'));
        $this->mongo_db->limit($limit);
        $result = $this->mongo_db->get('playbasis_quiz_to_player');

        return $result;
    }
}
?>