<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class LuckyDraw_model extends MY_Model{

    public function getLuckyDraw($luckydraw_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id',  new MongoID($luckydraw_id));

        $results = $this->mongo_db->get("playbasis_luckydraw_to_client");

        return $results ? $results[0] : null;
    }

    public function getLuckyDraws($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', $data['site_id']);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".preg_quote(utf8_strtolower($data['filter_name']))."/i");
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

    public function getTotalLuckyDraws($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('site_id', $data['site_id']);

        if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
            $regex = new MongoRegex("/".preg_quote(utf8_strtolower($data['filter_name']))."/i");
            $this->mongo_db->where('name', $regex);
        }

        $results = $this->mongo_db->count("playbasis_luckydraw_to_client");

        return $results;
    }

    private function getEventsStatus($db_results){
        if (is_array($db_results) && count($db_results)>1){ //if is from getLuckyDraws
            foreach ($db_results as &$result) {
                $date_today = time();
                if ($date_today > $result['date_start']->sec && $date_today > $result['date_end']->sec){
                    $result['status'] = "Done";
                }elseif ($date_today >= $result['date_start']->sec && $date_today <= $result['date_end']->sec){
                    $result['status'] = "Ongoing";
                }else{
                    $result['status'] = "Planned";
                }
            }
        }
        return $db_results;
    }

}
?>