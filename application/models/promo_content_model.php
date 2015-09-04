<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo_content_model extends MY_Model
{
    public function countPromoContents($client_id, $site_id)
    {
        $this->mongo_db->where('client_id', new MongoId($client_id));
        $this->mongo_db->where('site_id', new MongoId($site_id));
        $this->mongo_db->where('status', true);
        $total = $this->mongo_db->count('playbasis_promo_content_to_client');

        return $total;
    }
}