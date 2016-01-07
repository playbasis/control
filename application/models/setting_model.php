<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends MY_Model
{

    public function retrieveSetting($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));


        $this->mongo_db->where('client_id', $data['client_id']);
        $this->mongo_db->where('site_id', $data['site_id']);
        $results =  $this->mongo_db->get("playbasis_setting");
        $results = $results ? $results[0] : null;

        if ($results['password_policy_enable'] == false) unset($results['password_policy']);
        return $results;
    }


    public function createSetting($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $date = new MongoDate();
        $date_array = array(
            'date_added' => $date,
            'date_modified' => $date
        );
        $insert_data = array_merge($data,$date_array);

        $insert = $this->mongo_db->insert('playbasis_setting', $insert_data);

        return $insert;
    }

    public function updateSetting($data)
    {
        $this->mongo_db->where('client_id', new MongoID($data['client_id']));
        $this->mongo_db->where('site_id', new MongoID($data['site_id']));

        if ($this->retrieveSetting($data)){
            $date = new MongoDate();
            $date_array = array(
                'date_modified' => $date
            );

            $update_data = array_merge($data,$date_array);

            foreach ($update_data as $key => $value)
            {
                $this->mongo_db->set($key,$value);
            }
            $update = $this->mongo_db->update('playbasis_setting');

            return $update;
        }
        else{
            return $this->createSetting($data);
        }

    }

}