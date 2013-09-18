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
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id']
		));
		$badgeSet = $this->mongo_db->get('playbasis_badge_to_client');
		if(!$badgeSet)
			return array();
		//get data on badges
		foreach($badgeSet as $key => &$badge)
		{
			//get badge data
			$this->mongo_db->select(array(
				'image',
				'name',
				'description',
				'hint'
			));
			$this->mongo_db->where(array(
				'_id' => $badge['badge_id'],
				'deleted' => false
			));
			$result = $this->mongo_db->get('playbasis_badge');
			if(!$result)
			{
				unset($badgeSet[$key]);
				continue;
			}
			$badge = $result[0];
			$badge['image'] = $this->config->item('IMG_PATH') . $badge['image'];
			$badge['badge_id'] = $badge['_id'];
			unset($badge['_id']);
		}
		unset($badge);
		return $badgeSet;
	}
	public function getBadge($data)
	{
		//get badge id
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select(array('badge_id'));
		$this->mongo_db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'badge_id' => $data['badge_id']
		));
		$result = $this->mongo_db->get('playbasis_badge_to_client');
		if(!$result || !$result[0])
			return array();
		$result = $result[0];
		unset($result['_id']);
		//get badge image
		$this->mongo_db->select(array(
			'image',
			'name',
			'description',
			'hint'
		));
		$this->mongo_db->where(array(
			'_id' => $data['badge_id'],
			'deleted' => false
		));
		$result = $this->mongo_db->get('playbasis_badge');
		if(!$result)
			return array();
		$result = $result[0];
		$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
		$result['badge_id'] = $result['_id'];
		unset($result['_id']);
		return $result;
	}
}
?>