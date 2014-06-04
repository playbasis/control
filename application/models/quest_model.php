<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quest_model extends MY_Model{


	public function getQuestsByClientSiteId($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));
		/*
		$dummyQuest = array();

		$dummyQuest[] = array(
			'_id'=>'123 Object ID',
			'quest_name' => 'Quest Name 1',
			'condition' => array(
				'datetime_start' => 'starting date',
				'datetime_end' => 'ending date'
			),
			'status' => true,
			'image'=>'the image 1'
		);

		$dummyQuest[] = array(
			'_id'=>'312 Object ID',
			'quest_name' => 'Quest Name 2',
			'condition' => array(
				'datetime_start' => 'starting date 2',
				'datetime_end' => 'ending date 2'
			),
			'status' => true,
			'image'=>'the image 2'
		);

		$dummyQuest[] = array(
			'_id'=>'321 Object ID',
			'quest_name' => 'Quest Name 3',
			'condition' => array(
				'datetime_start' => 'starting date 3', 
				'datetime_end' => 'ending date 3'
			),
			'status' => false,
			'image'=>'the image 3'
		);

		return $dummyQuest;
		*/

		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));

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

		return (isset($quest) || !empty($quest[0]))?$quest[0]:null;
	}

	public function getTotalQuestsClientSite($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

		return 3;

	}

    public function getCustomPoint($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

        $this->mongo_db->where('client_id',  new MongoID($data['client_id']));
        $this->mongo_db->where('site_id',  new MongoID($data['site_id']));
        $this->mongo_db->where('_id',  new MongoID($data['reward_id']));
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

        return $this->mongo_db->get('playbasis_badge_to_client');

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
        $this->mongo_db->delete('playbasis_quest_to_client');
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

    public function getActionsByClientSiteId($data){
        $this->set_site_mongodb($this->session->userdata('site_id'));

    	$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));
		$this->mongo_db->where('status',true);

		return $this->mongo_db->get('playbasis_action_to_client');
    }


}