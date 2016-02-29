<?php

class Dummy_model extends CI_Model
{
    public function dummyAddPlayer($data)
    {
        $this->db->insert('playbasis_player', $data);
    }

    public function getActionToClient($data)
    {
        $this->db->select('name');
        $this->db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id']
        ));
        $result = $this->db->get('playbasis_action_to_client');
        return $result->result_array();
    }

    public function getRandomPlayer($data)
    {
        // $this->db->select('cl_player_id');
        // $this->db->where(array('client_id'=>$cid,'site_id'=>$sid));
        // $this->db->order_by('pb_player_id','random');
        // $result = $this->db->get('playbasis_player',0,100);
        $sql = "SELECT `cl_player_id` FROM `playbasis_player` WHERE `client_id` = ? AND `site_id` = ? ORDER BY RAND() LIMIT 0," . $data['limit'];
        $bindData = array(
            $data['client_id'],
            $data['site_id']
        );
        $result = $this->db->query($sql, $bindData);
        return $result->result_array();
    }

    public function getToken($data)
    {
        $this->db->select('token');
        $this->db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'date_expire >' => date('Y-m-d H:i:s')
        ));
        $result = $this->db->get('playbasis_token');
        $result = $result->row_array();
        if ($result) {
            return $result['token'];
        }
        return 0;
    }

    public function getKeySecret($data)
    {
        $this->db->select('`api_key` as `key`,`api_secret` as `secret`');
        $this->db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id']
        ));
        $result = $this->db->get('playbasis_client_site');
        return $result->row_array();
    }
}

?>