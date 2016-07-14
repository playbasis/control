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

    public function findLatestAPIactivity($client_id, $site_id = 0)
    {
        $this->set_site_mongodb($site_id);
        $this->mongo_db->select(array('date_added'));
        $this->mongo_db->where(array('client_id' => $client_id));
        $this->mongo_db->order_by(array('date_added' => 'DESC'));
        $this->mongo_db->limit(1);
        $result = $this->mongo_db->get('playbasis_web_service_log');
        return $result ? $result[0]['date_added'] : null;
    }

    public function listActiveClientsUsingAPI($days, $list_client_ids = null, $site_id = 0)
    {
        $this->set_site_mongodb($site_id);
        $d = strtotime("-" . $days . " day");
        $this->mongo_db->where_gt('date_added', new MongoDate($d));
        if ($list_client_ids) {
            $this->mongo_db->where_in('client_id', $list_client_ids);
        }
        return $this->mongo_db->distinct('client_id', 'playbasis_web_service_log');
    }

    public function archive($m, $bucket, $folder, $pageSize = 100)
    {
        $this->load->library('s3');

        $c = 0;

        /* find total "old" records to archive */
        $d = strtotime("-" . $m . " month");
        $this->mongo_db->where_lt('date_added', new MongoDate($d));
        $total = $this->mongo_db->count('playbasis_web_service_log');

        /* do paging over such records */
        while ($c < $total) {
            /* fetch the documents */
            $this->mongo_db->order_by(array('date_added' => 'ASC'));
            $this->mongo_db->limit($pageSize);
            $documents = $this->mongo_db->get('playbasis_web_service_log');

            /* upload to S3 */
            $_ids = array();
            foreach ($documents as $document) {
                $id = $document['_id'];
                $result = $this->s3->putObject(json_encode($document), $bucket, $folder . '/' . $id . '.json',
                    S3::ACL_PRIVATE);
                if ($result) {
                    array_push($_ids, $id);
                }
            }

            /* remove the documents */
            $this->mongo_db->where_in('_id', $_ids);
            $this->mongo_db->delete_all_with_ids('playbasis_web_service_log');

            $c += count($_ids);

            print('> ' . $c . '/' . $total . "\n");
        }

        return $c;
    }

    public function findCountryByDialCode($dialCode)
    {
        if ($dialCode == '+1') {
            return 'United States';
        }
        $this->mongo_db->where('d_code', $dialCode);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('countries');
        return $results ? $results[0]['name'] : null;
    }

    public function countApiUsage($client_id, $from, $to = null)
    {
        $record = null;
        if ($from && $to) {
            $record = $this->findApiUsageStat($client_id, $from, $to);
            if ($record) {
                return $record['n'];
            }
        }
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where_gte('date_added', new MongoDate(strtotime($from . ' 00:00:00')));
        if ($to) {
            $this->mongo_db->where_lt('date_added', new MongoDate(strtotime($to . ' 00:00:00')));
        }
        $n = $this->mongo_db->count('playbasis_web_service_log');
        if ($from && $to) {
            $this->saveApiUsageStat($client_id, $from, $to, $n);
        }
        return $n;
    }

    public function findApiUsageStat($client_id, $from, $to)
    {
        $this->mongo_db->where('client_id', $client_id);
        $this->mongo_db->where('from', $from);
        $this->mongo_db->where('to', $to);
        $this->mongo_db->limit(1);
        $results = $this->mongo_db->get('playbasis_web_service_usage');
        return $results ? $results[0] : array();
    }

    public function saveApiUsageStat($client_id, $from, $to, $n)
    {
        return $this->mongo_db->insert('playbasis_web_service_usage', array(
            'client_id' => $client_id,
            'from' => $from,
            'to' => $to,
            'n' => $n,
        ));
    }

    public function insertCountries($countries)
    {
        return $this->mongo_db->batch_insert('countries', $countries);
    }
}

?>