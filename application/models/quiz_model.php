<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quiz_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
    }

    public function find($client_id, $site_id, $nin = null, $type = null, $tags = null)
    {
        $d = new MongoDate(time());
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('name', 'image', 'description', 'description_image', 'weight', 'tags'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where(array('status' => true, 'deleted' => false));
        $this->mongo_db->where(array(
            '$and' => array(
                array('$or' => array(array('date_start' => array('$lte' => $d)), array('date_start' => null))),
                array('$or' => array(array('date_expire' => array('$gt' => $d)), array('date_expire' => null)))
            )
        ));
        if ($nin !== null) {
            $this->mongo_db->where_not_in('_id', $nin);
        }
        if ($type) {
            $this->mongo_db->where('type', $type);
        }
        if ($tags) {
            $this->mongo_db->where_in('tags', $tags);
        }
        $result = $this->mongo_db->get('playbasis_quiz_to_client');

        return $result;
    }

    public function find_by_id($client_id, $site_id, $quiz_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(), array('client_id', 'site_id', 'date_added', 'date_modified'));
        $this->mongo_db->where('_id', $quiz_id);
        $this->mongo_db->where('site_id', $site_id);
        $results = $this->mongo_db->get('playbasis_quiz_to_client');

        return $results ? $results[0] : null;
    }

    public function find_quiz_by_quiz_and_player($client_id, $site_id, $quiz_id, $pb_player_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array(
            'quiz_id',
            'value',
            'questions',
            'answers',
            'grade',
            'date_added',
            'date_modified'
        ));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('quiz_id', $quiz_id);
        $this->mongo_db->where('pb_player_id', $pb_player_id);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_quiz_to_player');

        return $results ? $results[0] : null;
    }

    public function find_quiz_by_player($client_id, $site_id, $pb_player_id, $limit = -1)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('quiz_id', 'value', 'questions', 'grade'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('pb_player_id', $pb_player_id);
        $this->mongo_db->order_by(array('date_modified' => -1));
        if ($limit > 0) {
            $this->mongo_db->limit($limit);
        }
        $results = $this->mongo_db->get('playbasis_quiz_to_player');

        return $results;
    }

    public function find_quiz_pending_and_done_by_player($client_id, $site_id, $pb_player_id, $limit = -1)
    {
        $output = array('pending' => array(), 'completed' => array());
        $results = $this->find_quiz_by_player($client_id, $site_id, $pb_player_id, $limit);
        if (is_array($results)) {
            foreach ($results as $result) {
                $quiz_id = $result['quiz_id'];
                $quiz = $this->find_by_id($client_id, $site_id, $quiz_id);
                $total_questions = count($quiz['questions']);
                $completed_questions = count($result['questions']);
                $pending = $completed_questions < $total_questions;
                $result['total_completed_questions'] = $completed_questions;
                if ($pending) {
                    $result['total_pending_questions'] = $total_questions - $completed_questions;
                }
                unset($result['questions']);
                array_push($output[$pending ? 'pending' : 'completed'], $result);
            }
        }
        return $output;
    }

    public function find_quiz_pending_by_player($client_id, $site_id, $pb_player_id, $limit = -1)
    {
        $results = $this->find_quiz_pending_and_done_by_player($client_id, $site_id, $pb_player_id, $limit);
        return $results['pending'];
    }

    public function find_quiz_done_by_player($client_id, $site_id, $pb_player_id, $limit = -1)
    {
        $results = $this->find_quiz_pending_and_done_by_player($client_id, $site_id, $pb_player_id, $limit);
        return $results['completed'];
    }

    public function update_player_score(
        $client_id,
        $site_id,
        $quiz_id,
        $pb_player_id,
        $question_id,
        $option_id,
        $score,
        $grade
    ) {
        $d = new MongoDate(time());
        $result = $this->find_quiz_by_quiz_and_player($client_id, $site_id, $quiz_id, $pb_player_id);
        $questions = $result ? $result['questions'] : array();
        $answers = $result ? $result['answers'] : array();
        array_push($questions, $question_id);
        array_push($answers, array('option_id' => $option_id, 'score' => $score, 'date_added' => $d));
        if (!$result) {
            return $this->mongo_db->insert('playbasis_quiz_to_player', array(
                'client_id' => $client_id,
                'site_id' => $site_id,
                'quiz_id' => $quiz_id,
                'pb_player_id' => $pb_player_id,
                'value' => $score,
                'questions' => array($question_id),
                'answers' => $answers,
                'grade' => $grade,
                'date_added' => $d,
                'date_modified' => $d
            ));
        } else {
            $this->mongo_db->where('client_id', $client_id);
            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->where('quiz_id', $quiz_id);
            $this->mongo_db->where('pb_player_id', $pb_player_id);
            $this->mongo_db->set('questions', $questions);
            $this->mongo_db->set('answers', $answers);
            $this->mongo_db->set('value', $score + $result['value']);
            $this->mongo_db->set('grade', $grade);
            $this->mongo_db->set('date_modified', $d);
            return $this->mongo_db->update('playbasis_quiz_to_player');
        }
    }

    public function sort_players_by_score($client_id, $site_id, $quiz_id, $limit)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('pb_player_id', 'value'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('quiz_id', $quiz_id);
        $this->mongo_db->order_by(array('value' => 'DESC'));
        $this->mongo_db->limit($limit);
        $result = $this->mongo_db->get('playbasis_quiz_to_player');

        return $result;
    }

    public function calculate_frequency($client_id, $site_id, $quiz_id)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('questions', 'answers'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('quiz_id', $quiz_id);
        $result = $this->mongo_db->get('playbasis_quiz_to_player');
        $stat = array();
        if ($result) {
            foreach ($result as $each) {
                if (isset($each['answers'])) {
                    foreach ($each['answers'] as $i => $value) {
                        if (isset($each['questions'][$i])) {
                            $question = strval($each['questions'][$i]);
                            $answer = strval($value['option_id']);
                            if (!isset($stat[$question])) {
                                $stat[$question] = array();
                            }
                            if (!isset($stat[$question][$answer])) {
                                $stat[$question][$answer] = 0;
                            }
                            $stat[$question][$answer]++;
                        }
                    }
                }
            }
        }

        return $stat;
    }

    /*
     * delete quiz
     *
     * @param client_id string client_id
     * @param site_id string site_id
     * @param player_id string pb_player_id
     * @param quiz_id string (optional) id of quiz
     * return string
     */
    public function delete($client_id, $site_id, $player_id, $quiz_id = null)
    {

        $this->set_site_mongodb($site_id);

        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('pb_player_id', $player_id);
        if ($quiz_id) {
            $this->mongo_db->where('quiz_id', $quiz_id);
            $this->mongo_db->delete('playbasis_quiz_to_player');
        } else {
            $this->mongo_db->delete_all('playbasis_quiz_to_player');
        }

        return 'success';
    }
}

?>