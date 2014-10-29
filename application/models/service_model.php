<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Service_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->config->load('playbasis');
        $this->load->library('memcached_library');
        $this->load->helper('memcache');
        $this->load->library('mongo_db');
    }

    public function getRecentPoint($site_id, $reward_id, $offset, $limit, $show_login=false, $show_quest=false){

        $this->mongo_db->where('site_id', $site_id);

        if($show_login){
            if($reward_id){
                $this->mongo_db->where('reward_id', $reward_id);
            }
            $this->mongo_db->where_in('event_type', array('REWARD', 'LOGIN'));
        }else{
            if($reward_id){
                $this->mongo_db->where('reward_id', $reward_id);
            }else{
                $this->mongo_db->where_ne('reward_id', null);
            }
            $this->mongo_db->where_in('event_type', array('REWARD'));
            $this->mongo_db->where_gt('value', 0);
        }

        if(!$show_quest){
            $this->mongo_db->where('quest_id', null);
        }

        $this->mongo_db->limit((int)$limit);
        $this->mongo_db->offset((int)$offset);
        $this->mongo_db->select(array('reward_id', 'reward_name', 'item_id', 'value', 'message', 'date_added','action_log_id', 'pb_player_id', 'quest_id', 'mission_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->order_by(array('date_added' => -1));
        $event_log = $this->mongo_db->get('playbasis_event_log');

        foreach($event_log as $key => &$event){

            $this->mongo_db->where('site_id', $site_id);
            $this->mongo_db->where('_id', $event['pb_player_id']);
            $this->mongo_db->select(array(
                'cl_player_id',
                'username',
                'first_name',
                'last_name',
                'gender',
                'image',
                'exp',
                'level'));
            $this->mongo_db->select(array(), array('_id'));
            $player = $this->mongo_db->get('playbasis_player');

            $event['player'] = isset($player[0]) ? $player[0] : null;
            if(!$event['player']){
                unset($event_log[$key]);
                continue;
            }

            $actionAndStringFilter = $this->getActionNameAndStringFilter($event['action_log_id']);

            $event['date_added'] = datetimeMongotoReadable($event['date_added']);
            if($actionAndStringFilter){
                $event['action_name'] = $actionAndStringFilter['action_name'];
                $event['string_filter'] = $actionAndStringFilter['url'];
                $event['action_icon'] = $actionAndStringFilter['icon'];
            }
            if(isset($event['quest_id']) && $event['quest_id']){
                if(isset($event['mission_id']) && $event['mission_id']){
                    $event['action_name'] = 'mission_reward';
                }else{
                    $event['action_name'] = 'quest_reward';
                }
                $event['action_icon'] = 'fa-trophy';
            }
            unset($event['action_log_id']);
            unset($event['pb_player_id']);
            unset($event['quest_id']);
            unset($event['mission_id']);

            $event['reward_id'] = $event['reward_id']."";

            if($event['reward_name'] == "badge"){

                $this->mongo_db->select(array('badge_id','image','name','description','hint','sponsor','claim','redeem'));
                $this->mongo_db->select(array(),array('_id'));
                $this->mongo_db->where(array(
                    'site_id' => $site_id,
                    'badge_id' => $event['item_id'],
                    'deleted' => false
                ));
                $result = $this->mongo_db->get('playbasis_badge_to_client');
                if(isset($result[0])){
                    $event['badge']['badge_id'] = $result[0]['badge_id']."";
                    $event['badge']['image'] = $this->config->item('IMG_PATH') . $result[0]['image'];
                    $event['badge']['name'] = $result[0]['name'];
                    $event['badge']['description'] = $result[0]['description'];
                    $event['badge']['hint'] = $result[0]['hint'];
                }

            }

            unset($event['item_id']);
        }

        return $event_log;
    }

    private function getActionNameAndStringFilter($action_log_id){
        $this->mongo_db->select(array('action_name', 'url', 'client_id', 'site_id'));
        $this->mongo_db->select(array(), array('_id'));
        $this->mongo_db->where('_id', new MongoID($action_log_id));
        $returnThis = $this->mongo_db->get('playbasis_action_log');

        if($returnThis){
            $returnThis = $returnThis[0];

            $this->mongo_db->select(array('action_id', 'icon'));
            $this->mongo_db->where(array(
                'client_id' => $returnThis['client_id'],
                'site_id' => $returnThis['site_id'],
                'name' => $returnThis['action_name']
            ));
            $action = $this->mongo_db->get('playbasis_action_to_client');

            if($action){
                $returnThis['icon'] = $action[0]['icon'];
            }
        }else{
            return array();
        }

        return $returnThis;
    }

	public function findLatestAPIactivity($client_id, $site_id=0) {
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array('date_added'));
		$this->mongo_db->where(array('client_id' => $client_id));
		$this->mongo_db->order_by(array('date_added' => 'DESC'));
		$this->mongo_db->limit(1);
		$result = $this->mongo_db->get('playbasis_web_service_log');
		return $result ? $result[0]['date_added'] : null;
	}

    public function resetPlayerPoints($site_id, $client_id, $reward_id, $reward_name){
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('reward_id', $reward_id);
        $this->mongo_db->set('value', 0);
        $this->mongo_db->set('claimed', 0);
        $this->mongo_db->set('redeemed', 0);
        $reward = $this->mongo_db->update_all('playbasis_reward_to_player');

        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id'	=> null,
            'client_id'		=> $client_id,
            'site_id'		=> $site_id,
            'event_type'	=> "RESET",
            'action_log_id' => null,
            'message'		=> null,
            'reward_id'		=> $reward_id,
            'reward_name'	=> $reward_name,
            'item_id'		=> null,
            'value'			=> null,
            'objective_id'	=> null,
            'objective_name'=> null,
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }

    /*public function resetPlayerBadge($site_id, $client_id, $reward_id, $reward_name, $badge_id){
        $this->set_site_mongodb($site_id);

        $this->mongo_db->where('site_id', $site_id);
        $this->mongo_db->where('badge_id', $badge_id);
        $this->mongo_db->set('value', 0);
        $this->mongo_db->set('claimed', 0);
        $this->mongo_db->set('redeemed', 0);
        $reward = $this->mongo_db->update_all('playbasis_reward_to_player');

        $mongoDate = new MongoDate(time());
        return $this->mongo_db->insert('playbasis_event_log', array(
            'pb_player_id'	=> null,
            'client_id'		=> $client_id,
            'site_id'		=> $site_id,
            'event_type'	=> "RESET",
            'action_log_id' => null,
            'message'		=> null,
            'reward_id'		=> $reward_id,
            'reward_name'	=> $reward_name,
            'item_id'		=> $badge_id,
            'value'			=> null,
            'objective_id'	=> null,
            'objective_name'=> null,
            'date_added'	=> $mongoDate,
            'date_modified' => $mongoDate
        ));
    }*/

    public function listActiveClientsUsingAPI($days, $list_client_ids=null, $site_id=0) {
        $this->set_site_mongodb($site_id);
        $d = strtotime("-".$days." day");
        $this->mongo_db->where_gt('date_added', new MongoDate($d));
        if ($list_client_ids) $this->mongo_db->where_in('client_id', $list_client_ids);
        return $this->mongo_db->distinct('client_id', 'playbasis_web_service_log');
    }

    public function archive($m, $bucket, $folder, $pageSize=50) {
        $this->load->library('s3');

        $c = 0;

        /* find total "old" records to archive */
        $d = strtotime("-".$m." month");
        $this->mongo_db->where_lt('date_added', new MongoDate($d));
        $total = $this->mongo_db->count('playbasis_web_service_log');

        /* do paging over such records */
        $numPage = intval(ceil($total/(1.0*$pageSize)));
        for ($i = 0; $i < $numPage; $i++) {
            /* fetch the documents */
            $this->mongo_db->offset($i*$pageSize);
            $this->mongo_db->limit($pageSize);
            $documents = $this->mongo_db->get('playbasis_web_service_log');

            /* upload to S3 */
            $_ids = array();
            foreach ($documents as $document) {
                $id = $document['_id'];
                $result = $this->s3->putObject(json_encode($document), $bucket, $folder.'/'.$id.'.json', S3::ACL_PRIVATE);
                if ($result) {
                    array_push($_ids, $id);
                }
            }

            /* remove the documents */
            $this->mongo_db->where_in('_id', $_ids);
            $this->mongo_db->delete_all_with_ids('playbasis_web_service_log');

            $c += count($_ids);

            print('> '.($i+1).'/'.$numPage.' ('.$c.')'."\n");
        }

        return $c;
    }
}
?>