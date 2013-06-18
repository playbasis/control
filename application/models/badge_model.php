<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Badge_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('playbasis');
        $this->load->library('memcached_library');
		$this->load->helper('memcache');
	}
	public function getAllBadges($data)
	{
		//get badge ids
		$this->set_site_mongodb($data['site_id']);		
		$this->mongo_db->select(array('badge_id'));
		$this->mongo_db->where(array(
			'client_id' => intval($data['client_id']),
			'site_id' => intval($data['site_id'])
		));
		$badgeSet = $this->mongo_db->get('badge_to_client');
		if(!$badgeSet)
			return array();
		//get data on badges
		foreach($badgeSet as &$badge)
		{
			//get badge data
			$this->mongo_db->select(array(
				'name',
				'description',
				'hint'
			));
			$this->mongo_db->where('badge_id', intval($badge['badge_id']));
			$result = $this->mongo_db->get('badge_description');
			assert($result);
			$result = $result[0];
			unset($result['_id']);
			$badge = array_merge($badge, $result);
			//get badge image
			$this->mongo_db->select(array('image'));
			$this->mongo_db->where('badge_id', intval($badge['badge_id']));
			$result = $this->mongo_db->get('badge');
			assert($result);
			$result = $result[0];
			unset($result['_id']);
			$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
			$badge = array_merge($badge, $result);
			unset($badge['_id']);
		}
		return $badgeSet;
	}
	public function getBadge($data)
	{
		//get badge id
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select(array('badge_id'));
		$this->mongo_db->where(array(
			'client_id' => intval($data['client_id']),
			'site_id' => intval($data['site_id']),
			'badge_id' => intval($data['badge_id'])
		));
		$result = $this->mongo_db->get('badge_to_client');
		if(!$result || !$result[0])
			return array();
		$result = $result[0];
		unset($result['_id']);
		$badge = array(
			'badge_id' => $data['badge_id']
		);
		//get badge data
		$this->mongo_db->select(array(
			'name',
			'description',
			'hint'
		));
		$this->mongo_db->where('badge_id', intval($badge['badge_id']));
		$result = $this->mongo_db->get('badge_description');
		assert($result);
		$result = $result[0];
		unset($result['_id']);
		$badge = array_merge($badge, $result);
		//get badge image
		$this->mongo_db->select(array('image'));
		$this->mongo_db->where('badge_id', intval($badge['badge_id']));
		$result = $this->mongo_db->get('badge');
		assert($result);
		$result = $result[0];
		unset($result['_id']);
		$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
		$badge = array_merge($badge, $result);
		return $badge;
	}
	public function getAllCollection($data)
	{
		//get collection ids
		$this->set_site($data['site_id']);
		$this->site_db()->select('collection_id');
		$this->site_db()->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id']
		));
		$collectionSet = db_get_result_array($this, 'playbasis_badge_collection_to_client');
		if(!$collectionSet)
			return array();
		//get data on collections
		foreach($collectionSet as &$collection)
		{
			//get collection data
			$this->site_db()->select('name,description');
			$this->site_db()->where('collection_id', $collection['collection_id']);
			$result = db_get_row_array($this, 'playbasis_badge_collection_description');
			$collection = array_merge($collection, $result);
			//get collection image
			$this->site_db()->select('image');
			$this->site_db()->where('collection_id', $collection['collection_id']);
			$result = db_get_row_array($this, 'playbasis_badge_collection');
			$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
			$collection = array_merge($collection, $result);
			//get badge_id related to a collection
			$this->site_db()->select('badge_id');
			$this->site_db()->where('collection_id', $collection['collection_id']);
			$result = db_get_result_array($this, 'playbasis_badge_to_collection');
			$collection['badge'] = $result;
		}
		return $collectionSet;
	}
	public function getCollection($data)
	{
		//get collection id
		$this->set_site($data['site_id']);
		$this->site_db()->select('collection_id');
		$this->site_db()->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'collection_id' => $data['collection_id']
		));
		$result = db_get_row_array($this, 'playbasis_badge_collection_to_client');
		if(!$result)
			return array();
		$collection = array(
			'collection_id' => $data['collection_id']
		);
		//get collection data
		$this->site_db()->select('name,description');
		$this->site_db()->where('collection_id', $collection['collection_id']);
		$result = db_get_row_array($this, 'playbasis_badge_collection_description');
		$collection = array_merge($collection, $result);
		//get badge image
		$this->site_db()->select('image');
		$this->site_db()->where('collection_id', $collection['collection_id']);
		$result = db_get_row_array($this, 'playbasis_badge_collection');
		$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
		$collection = array_merge($collection, $result);
		//get badge_id related to the collection
		$this->site_db()->select('badge_id');
		$this->site_db()->where('collection_id', $collection['collection_id']);
		$result = db_get_result_array($this, 'playbasis_badge_to_collection');
		$collection['badge'] = $result;
		return $collection;
	}
}
?>