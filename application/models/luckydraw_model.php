<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LuckyDraw_model extends MY_Model
{

    public function getLuckyDraw($luckydraw_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($luckydraw_id));
        $this->mongo_db->where('deleted', false);

        $results = $this->mongo_db->get("playbasis_luckydraw_to_client");

        return $results ? $results[0] : null;
    }

    public function getLuckyDraws($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        $sort_data = array(
            '_id',
            'name',
            'sort'
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

        $results = $this->mongo_db->get("playbasis_luckydraw_to_client");
        $results = $this->getEventsStatus($results);

        return $results;
    }

    public function getTotalLuckyDraws($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', $data['site_id']);
        $this->mongo_db->where('deleted', false);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/" . preg_quote(utf8_strtolower($data['filter_name'])) . "/i");
            $this->mongo_db->where('name', $regex);
        }

        $results = $this->mongo_db->count("playbasis_luckydraw_to_client");

        return $results;
    }

    private function getEventsStatus($db_results)
    {
        if (is_array($db_results) && !empty($db_results)) { //if is from getLuckyDraws
            foreach ($db_results as &$result) {
                $date_today = time();
                if ($date_today > $result['date_start']->sec
                    && $date_today > $result['date_end']->sec
                ) {
                    $result['status'] = "Done";
                } elseif ($date_today >= $result['date_start']->sec && $date_today <= $result['date_end']->sec) {
                    $result['status'] = "Ongoing";
                } else {
                    $result['status'] = "Planned";
                }
            }
        }
        return $db_results;
    }

    public function editLuckyDrawToClient($luckydraw_id, $data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($luckydraw_id));
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if (isset($data['name']) && !is_null($data['name'])) {
            $this->mongo_db->set('name', $data['name']);
        }

        if (isset($data['description']) && !is_null($data['description'])) {
            $this->mongo_db->set('description', $data['description']);
        }

        if (isset($data['date_start'])) {
            if ($data['date_start'] != '') {
                $this->mongo_db->set('date_start', new MongoDate(strtotime($data['date_start'])));
            } else {
                $this->mongo_db->set('date_start', null);
            }
        }

        if (isset($data['date_end'])) {
            if ($data['date_end'] != '') {
                $this->mongo_db->set('date_end', new MongoDate(strtotime($data['date_end'])));
            } else {
                $this->mongo_db->set('date_end', null);
            }
        }

        if (isset($data['participate_method']) && !is_null($data['participate_method'])) {
            if ($data['participate_method'] == "ask_to_join") {
                $this->mongo_db->set('participate_method', true);
            } elseif ($data['participate_method'] == "active_users_only") {
                $this->mongo_db->set('participate_method', false);
            }
        }

//        if(isset($data['rewards']) && !is_null($data['rewards'])){
//            $this->mongo_db->set('rewards', $data['rewards']);
//        }

        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_luckydraw_to_client');

        return true;
    }

    public function addLuckyDrawToClient($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if (isset($data['date_start'])) {
            $data['date_start'] = $data['date_start'] ? new MongoDate(strtotime($data['date_start'])) : null;
        }
        if (isset($data['date_end'])) {
            $data['date_end'] = $data['date_end'] ? new MongoDate(strtotime($data['date_end'])) : null;
        }

        if (isset($data['participate_method']) && !is_null($data['participate_method'])) {
            if ($data['participate_method'] == "ask_to_join") {
                $data['participate_method'] = true;
            } elseif ($data['participate_method'] == "active_users_only") {
                $data['participate_method'] = false;
            }
        }

        $data = array_merge($data, array(
                'date_added' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                'date_modified' => new MongoDate(strtotime(date("Y-m-d H:i:s"))),
                'deleted' => false
            )
        );

        return $this->mongo_db->insert('playbasis_luckydraw_to_client', $data);
    }

    public function delete($luckydraw_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoID($luckydraw_id));

        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_luckydraw_to_client');

        return true;
    }
}