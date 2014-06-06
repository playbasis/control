<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quest_model extends MY_Model{


	public function getQuestsByClientSiteId($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

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

		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));
		$this->mongo_db->where_not_in('deleted', array(true));
		$this->mongo_db->order_by(array('sort_order' => 1));

		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
		    $regex = new MongoRegex("/".utf8_strtolower($data['filter_name'])."/i");
		    $this->mongo_db->where('quest_name', $regex);
		}

		return $this->mongo_db->get('playbasis_quest_to_client');
	}

	public function getQuestByClientSiteId($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        if(isset($data['short_detail']) && $data['short_detail']){
            $this->mongo_db->select(array('quest_name','description','hint','image'));
            $this->mongo_db->select(array(),array('_id'));
        }

		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));

		$this->mongo_db->where('_id',  new MongoID($data['quest_id']));		

		$quest = $this->mongo_db->get('playbasis_quest_to_client');

		return (isset($quest) || !empty($quest[0]))?$quest[0]:array();
	}

	public function getTotalQuestsClientSite($data){

		$this->set_site_mongodb($this->session->userdata('site_id'));

		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));
		$this->mongo_db->where_not_in('deleted', array(true));

		return $this->mongo_db->count('playbasis_quest_to_client');

	}

    public function getCustomPoint($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
        // $this->mongo_db->where('_id',  new MongoID($data['reward_id']));
        $this->mongo_db->where('reward_id',  new MongoID($data['reward_id']));
        $this->mongo_db->where_not_in('name', array('badge', 'point', 'exp'));

        $results = $this->mongo_db->get("playbasis_reward_to_client");

        return $results?$results[0]:array();
    }

	public function getCustomPoints($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));
		$this->mongo_db->where_not_in('name', array('badge', 'point', 'exp'));

		return $this->mongo_db->get('playbasis_reward_to_client');

	}

    public function getBadge($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('badge_id','image','name','description','hint','sponsor','claim','redeem'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
        $this->mongo_db->where('badge_id',  new MongoID($data['badge_id']));

        $result = $this->mongo_db->get('playbasis_badge_to_client');
        return $result?$result[0]:array();

    }

	public function getBadgesByClientSiteId($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));

		return $this->mongo_db->get('playbasis_badge_to_client');

	}

	/*
	public function increaseOrderByOne($action_id){
	    $this->set_site_mongodb($this->session->userdata('site_id'));

	    $this->mongo_db->where('_id', new MongoId($action_id));
	    $theAction = $this->mongo_db->get('playbasis_action');

	    $currentSort = $theAction[0]['sort_order'];
	    
	    $newSort = $currentSort+1;

	    $this->mongo_db->where('_id', new MongoID($action_id));
	    $this->mongo_db->set('sort_order', $newSort);
	    $this->mongo_db->update('playbasis_action');

	}
	*/
	
	public function increaseOrderByOneClient($quest_id, $client_id){
	    $this->set_site_mongodb($this->session->userdata('site_id'));

	    $this->mongo_db->where('_id', new MongoId($quest_id));
	    $this->mongo_db->where('client_id', new MongoId($client_id));
	    $theQuest = $this->mongo_db->get('playbasis_quest_to_client');

	    $currentSort = $theQuest[0]['sort_order'];
	    
	    $newSort = $currentSort+1;

	    $this->mongo_db->where('_id', new MongoID($quest_id));
	    $this->mongo_db->where('client_id', new MongoId($client_id));
	    $this->mongo_db->set('sort_order', $newSort);
	    $this->mongo_db->update('playbasis_quest_to_client');

	}

	/*
	public function decreaseOrderByOne($action_id){
	    $this->set_site_mongodb($this->session->userdata('site_id'));

	    $this->mongo_db->where('_id', new MongoId($action_id));
	    $theAction = $this->mongo_db->get('playbasis_action');

	    $currentSort = $theAction[0]['sort_order'];
	    
	    if($currentSort != 0){
	        $newSort = $currentSort-1;    

	        $this->mongo_db->where('_id', new MongoID($action_id));
	        $this->mongo_db->set('sort_order', $newSort);
	        $this->mongo_db->update('playbasis_action');
	    }
	}
	*/

	public function decreaseOrderByOneClient($quest_id, $client_id){
	    $this->set_site_mongodb($this->session->userdata('site_id'));

	    $this->mongo_db->where('_id', new MongoId($quest_id));
	    $this->mongo_db->where('client_id', new MongoId($client_id));
	    $theAction = $this->mongo_db->get('playbasis_quest_to_client');

	    $currentSort = $theAction[0]['sort_order'];
	    
	    if($currentSort != 0){
	        $newSort = $currentSort-1;    

	        $this->mongo_db->where('_id', new MongoID($quest_id));
	        $this->mongo_db->where('client_id', new MongoId($client_id));
	        $this->mongo_db->set('sort_order', $newSort);
	        $this->mongo_db->update('playbasis_quest_to_client');
	    }
	}

	public function deleteQuestClient($quest_id){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('_id', new MongoId($quest_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->set('status', false);
        $this->mongo_db->update('playbasis_quest_to_client');

        $this->mongo_db->where('quest_id', new MongoId($quest_id));
        $this->mongo_db->set('deleted', true);
        $this->mongo_db->update_all('playbasis_quest_to_player');

    }

    public function addQuestToClient($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

    	return $this->mongo_db->insert('playbasis_quest_to_client', $data);
    }

    public function getExpId($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

    	$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));
	    $this->mongo_db->where('name', 'exp');
    	
    	$results = $this->mongo_db->get("playbasis_reward_to_client");


    	return (isset($results[0]['reward_id']))?$results[0]['reward_id']:null;
    }

    public function getPointId($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

    	$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));
    	$this->mongo_db->where('name', 'point');

    	$results = $this->mongo_db->get("playbasis_reward_to_client");

    	return (isset($results[0]['reward_id']))?$results[0]['reward_id']:null;
    }

    public function getAction($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->select(array('action_id','name','description','icon','color'));
        $this->mongo_db->select(array(),array('_id'));
        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
        $this->mongo_db->where('action_id',  new MongoID($data['action_id']));

        $result = $this->mongo_db->get('playbasis_action_to_client');
        return $result?$result[0]:array();

    }

    public function getActionsByClientSiteId($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

    	$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));
		$this->mongo_db->where('status',true);

		return $this->mongo_db->get('playbasis_action_to_client');
    }

    public function editQuestToClient($quest_id, $data){

    	$this->mongo_db->where('_id', new MongoID($quest_id));

    	$this->mongo_db->set('quest_name', isset($data['quest_name'])?$data['quest_name']:'');

    	$this->mongo_db->set('description', isset($data['description'])?$data['description']:'');
    	
        $this->mongo_db->set('hint', isset($data['hint'])?$data['hint']:'');

    	$this->mongo_db->set('image', isset($data['image'])?$data['image']:'');

        $this->mongo_db->set('mission_order', isset($data['mission_order'])?$data['mission_order']:'');

    	$this->mongo_db->set('status', isset($data['status'])?$data['status']:'');
		
    	$this->mongo_db->set('sort_order', isset($data['sort_order'])?$data['sort_order']:'');

    	$this->mongo_db->set('condition', isset($data['condition'])?$data['condition']:array());

    	$this->mongo_db->set('rewards', isset($data['rewards'])?$data['rewards']:array());

    	$this->mongo_db->set('missions', isset($data['missions'])?$data['missions']:array());

    	$this->mongo_db->set('date_modified', new MongoDate(strtotime(date("Y-m-d H:i:s"))));

        $this->mongo_db->update('playbasis_quest_to_client');


        if(isset($data['missions']) && !is_null($data['missions'])){
            foreach($data['missions'] as $m){
                $this->mongo_db->where(array(
                    'quest_id' => new MongoId($quest_id),
                    'missions.mission_id' => new MongoId($m['mission_id']),
                ));
                $this->mongo_db->set(array('missions.$.mission_name' => isset($m['mission_name'])?$m['mission_name']:''));
                $this->mongo_db->set(array('missions.$.mission_number' => isset($m['mission_number'])?$m['mission_number']:''));
                $this->mongo_db->set(array('missions.$.description' => isset($m['description'])?$m['description']:''));
                $this->mongo_db->set(array('missions.$.hint' => isset($m['hint'])?$m['hint']:''));
                $this->mongo_db->set(array('missions.$.image' => isset($m['image'])?$m['image']:''));
                if(isset($m['completion'])){
                    $this->mongo_db->set(array('missions.$.completion' => $m['completion']));
                }
                if(isset($m['rewards'])){
                	$this->mongo_db->set(array('missions.$.rewards' => $m['rewards']));	
                }
                $this->mongo_db->update_all('playbasis_quest_to_player');
            }
        }else{
            $this->mongo_db->set(array('missions' => array())); 
            $this->mongo_db->update_all('playbasis_quest_to_player');
        }

    	return true;

    }


}