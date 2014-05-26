<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quest_model extends MY_Model{


	public function getQuestsByClientSiteId($data){
		
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

	public function getTotalQuestsClientSite($data){

		return 3;

	}

	public function getCustomPoints($data){

		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));
		$this->mongo_db->where_not_in('name', array('badge', 'point', 'exp'));

		return $this->mongo_db->get('playbasis_reward_to_client');

	}

	public function getBadgesByClientSiteId($data){

		$this->mongo_db->where('client_id',  new MongoID($data['client_id']));
		$this->mongo_db->where('site_id',  new MongoID($data['site_id']));

		return $this->mongo_db->get('playbasis_badge_to_client');

	}


}