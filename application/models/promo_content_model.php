<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo_content_model extends MY_Model
{
    public function countPromoContents($client_id, $site_id)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('status', true);
        $total = $this->mongo_db->count('playbasis_promo_content_to_client');

        return $total;
    }

    public function insertPromoContent($data)
    {
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $insert_data = array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'name' => strtolower($data['name']),
            'desc' => $data['desc'],
            'date_start' => new MongoDate(strtotime($data['date_start'])),
            'date_end' => new MongoDate(strtotime($data['date_end'])),
            'status' => true,
            'date_added' => new MongoDate(),
            'date_modified' => new MongoDate()
        );
        $insert = $this->mongo_db->insert('playbasis_promo_content_to_client', $insert_data);

        return $insert;
    }
}