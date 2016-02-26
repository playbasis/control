<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_model extends MY_Model
{
    public function log($client_id, $from = null, $to = null)
    {
        //$this->set_site_mongodb($this->session->userdata('site_id'));
        $this->set_site_mongodb(0); // use default for admin
        $map = new MongoCode("function() { this.date_added.setTime(this.date_added.getTime()-(-7*60*60*1000)); emit(this.date_added.getFullYear()+'-'+('0'+(this.date_added.getMonth()+1)).slice(-2)+'-'+('0'+this.date_added.getDate()).slice(-2), 1); }");
        $reduce = new MongoCode("function(key, values) { return Array.sum(values); }");
        $query = array('client_id' => $client_id);
        if ($from || $to) {
            $query['date_added'] = array();
        }
        if ($from) {
            $query['date_added']['$gte'] = $this->new_mongo_date($from);
        }
        if ($to) {
            $query['date_added']['$lte'] = $this->new_mongo_date($to, '23:59:59');
        }
        $this->mongo_db->command(array(
            'mapReduce' => 'playbasis_web_service_log',
            'map' => $map,
            'reduce' => $reduce,
            'query' => $query,
            'out' => 'mapreduce_web_service_log',
        ));
        $result = $this->mongo_db->get('mapreduce_web_service_log');
        return $result;
    }
}

?>