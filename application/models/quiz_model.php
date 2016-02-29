<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quiz_model extends MY_Model
{
    public function getQuiz($quiz_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($quiz_id));

        $this->mongo_db->where('deleted', false);
        $results = $this->mongo_db->get("playbasis_quiz_to_client");

        return $results ? $results[0] : null;
    }

    public function getQuizs($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id']);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        $sort_data = array(
            '_id',
            'name',
            'status',
        );

        if (isset($data['order']) && (utf8_strtolower($data['order']) == 'desc')) {
            $order = -1;
        } else {
            $order = 1;
        }

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        } else {
            $this->mongo_db->order_by(array('name' => $order));
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $results = $this->mongo_db->get("playbasis_quiz_to_client");

        return $results;
    }

    public function getTotalQuizs($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('deleted', false);
        $this->mongo_db->where('site_id', $data['site_id']);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        $results = $this->mongo_db->count("playbasis_quiz_to_client");

        return $results;
    }

    public function addQuizToClient($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['date_start'])) {
            $data['date_start'] = $data['date_start'] ? new MongoDate(strtotime($data['date_start'])) : null;
        }
        if (isset($data['date_expire'])) {
            $data['date_expire'] = $data['date_expire'] ? new MongoDate(strtotime($data['date_expire'])) : null;
        }

        $data = array_merge($data, array(
                'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                'deleted' => false
            )
        );

        return $this->mongo_db->insert('playbasis_quiz_to_client', $data);
    }

    public function editQuizToClient($quiz_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($quiz_id));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['name']) && !is_null($data['name'])) {
            $this->mongo_db->set('name', $data['name']);
        }
        if (isset($data['description']) && !is_null($data['description'])) {
            $this->mongo_db->set('description', $data['description']);
        }

        if (isset($data['weight']) && !is_null($data['weight'])) {
            $this->mongo_db->set('weight', $data['weight']);
        }

        if (isset($data['image']) && !is_null($data['image'])) {
            $this->mongo_db->set('image', $data['image']);
        }

        if (isset($data['description_image']) && !is_null($data['description_image'])) {
            $this->mongo_db->set('description_image', $data['description_image']);
        }

        if (isset($data['status'])) {
            $this->mongo_db->set('status', (boolean)$data['status']);
        }

        if (isset($data['date_start'])) {
            if ($data['date_start'] != '') {
                $this->mongo_db->set('date_start', new MongoDate(strtotime($data['date_start'])));
            } else {
                $this->mongo_db->set('date_start', null);
            }
        }

        if (isset($data['date_expire'])) {
            if ($data['date_expire'] != '') {
                $this->mongo_db->set('date_expire', new MongoDate(strtotime($data['date_expire'])));
            } else {
                $this->mongo_db->set('date_expire', null);
            }
        }

        if (isset($data['grades']) && !is_null($data['grades'])) {
            $this->mongo_db->set('grades', $data['grades']);
        }

        if (isset($data['questions']) && !is_null($data['questions'])) {
            $this->mongo_db->set('questions', $data['questions']);
        }

        if (isset($data['type']) && !is_null($data['type'])) {
            $this->mongo_db->set('type', $data['type']);
        }

        if (isset($data['question_order']) && !is_null($data['question_order'])) {
            $this->mongo_db->set('question_order', $data['question_order']);
        }

        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_quiz_to_client');

        return true;
    }

    public function delete($quiz_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($quiz_id));

        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_quiz_to_client');

        return true;
    }
}

?>