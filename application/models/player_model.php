<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Player_model extends MY_Model
{
    public function getPlayers($data) {
        $player_data = array();

        $client_id = $this->User_model->getClientId();
        $site_id = $this->User_model->getSiteId();

        if (isset($data['client_id']) && isset($data['site_id'])) {
            $this->mongo_db->where('client_id', new MongoID($data['client_id']));
            $this->mongo_db->where('site_id', new MongoID($data['site_id']));
        }

        if (isset($data['order'])) {
            if (strtolower($data['order']) == 'desc') {
                $order = -1;
            }else{
                $order = 1;
            }
        }else{
            $order = 1;
        }

        $sort_data = array(
            'first_name',
            'exp',
            'level',
            'status',
            'sort_order',
            'date_added'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $this->mongo_db->order_by(array($data['sort'] => $order));
        }else{
            $this->mongo_db->order_by(array('_id' => $order));
        }

        if (!empty($data['start']) || !empty($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $this->mongo_db->limit((int)$data['limit']);
            $this->mongo_db->offset((int)$data['start']);
        }

        $results =  $this->mongo_db->get('playbasis_player');

        foreach ($results as $result) {
            $player_data[] = array(
                'pb_player_id' => $result['pb_player_id'],
                'username' => $result['username'],
                'first_name' => $result['first_name'],
                'last_name' => $result['last_name'],
                'email' => $result['email'],
                'nickname' => $result['nickname'],
                'gender' => $result['gender'] === '1' ? 'male' : 'female',
                'image' => $result['image'],
                'level' => $result['level'],
                'exp' => $result['exp'],
                'action' => $this->getPlayerAction($site_id, $client_id, $result['pb_player_id']),
                'status' => $result['status'],
                'date_added' => $result['date_added'],
                'date_modified' => $result['date_modified'],
                // 'points' => $this->getUserPoint($result['pb_player_id'] , $sql_reward),
                'points' => 0,
                'age' => $this->getAge($result['birth_date'])
            );
        }

        return $player_data;
    }

    private function getPlayerAction($site_id, $client_id, $player_id) {

        $this->mongo_db->where('client_id', new MongoID($client_id));
        $this->mongo_db->where('site_id', new MongoID($site_id));
        $this->mongo_db->where('pb_player_id', new MongoID($player_id));
        $results =  $this->mongo_db->get('playbasis_action_to_client');

        $player_action = array();

        foreach ($results as $result) {
            $player_action[$result['action_name']] = array(
                'action_name' => $result['action_name'],
                'amount' => $player_action[$result['action_name']]? $player_action[$result['action_name']]['amount'] + 1 : 1
            );
        }

        return $player_action;
    }

    private function getAge($birthdate) {
        $now = new DateTime();
        $birthdate = $this->datetimeMongotoReadable($birthdate);
        $oDateBirth = new DateTime($birthdate);
        $oDateInterval = $now->diff($oDateBirth);

        return $oDateInterval->y;
    }

    private function datetimeMongotoReadable($dateTimeMongo)
    {
        if ($dateTimeMongo) {
            if (isset($dateTimeMongo->sec)) {
                $dateTimeMongo = date("Y-m-d H:i:s", $dateTimeMongo->sec);
            } else {
                $dateTimeMongo = $dateTimeMongo;
            }
        } else {
            $dateTimeMongo = "0000-00-00 00:00:00";
        }
        return $dateTimeMongo;
    }
}
?>