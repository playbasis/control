<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Badge_model extends CI_Model{
	
	public function __construct(){
			parent::__construct();

			$this->config->load('playbasis');
		}

	public function getAllBadges($data){

		//select badge
		$this->db->select('badge_id');
		$this->db->where(array('client_id'=>$data['client_id'],'site_id'=>$data['site_id']));

		$result = $this->db->get('playbasis_badge_to_client');

		$badgeSet = $result->result_array();

		if(!$badgeSet)
			return array();

		//get badge information
		foreach ($badgeSet as &$badge) {
			//get badge data
			$this->db->select('name,description,hint');
			$this->db->where('badge_id',$badge['badge_id']);
			$result = $this->db->get('playbasis_badge_description');

			$result = $result->row_array();

			$badge = array_merge($badge,$result);


			//get badge image
			$this->db->select('image');
			$this->db->where('badge_id',$badge['badge_id']);
			$result = $this->db->get('playbasis_badge');

			$result = $result->row_array();
			$result['image'] = $this->config->item('IMG_PATH').$result['image'];

			$badge = array_merge($badge,$result);
		}

		return $badgeSet;
	}

	public function getBadge($data){

		//check badge
		$this->db->select('badge_id');
		$this->db->where(array('client_id'=>$data['client_id'],'site_id'=>$data['site_id'],'badge_id'=>$data['badge_id']));
		$result = $this->db->get('playbasis_badge_to_client');

		$result = $result->row_array();

		if(!$result)
			return array();

		$badge = array('badge_id'=>$data['badge_id']);
		
		$this->db->select('name,description,hint');
		$this->db->where('badge_id',$badge['badge_id']);
		$result = $this->db->get('playbasis_badge_description');

		$result = $result->row_array();

		$badge = array_merge($badge,$result);


		//get badge image
		$this->db->select('image');
		$this->db->where('badge_id',$badge['badge_id']);
		$result = $this->db->get('playbasis_badge');

		$result = $result->row_array();
		$result['image'] = $this->config->item('IMG_PATH').$result['image'];
		
		$badge = array_merge($badge,$result);

		return $badge;
	}

	public function getAllCollection($data){
		//select collection
		$this->db->select('collection_id');
		$this->db->where(array('client_id'=>$data['client_id'],'site_id'=>$data['site_id']));

		$result = $this->db->get('playbasis_badge_collection_to_client');

		$collectionSet = $result->result_array();

		if(!$collectionSet)
			return array();

		//get collection information
		foreach ($collectionSet as &$collection) {
			//get collection data
			$this->db->select('name,description');
			$this->db->where('collection_id',$collection['collection_id']);
			$result = $this->db->get('playbasis_badge_collection_description');

			$result = $result->row_array();

			$collection = array_merge($collection,$result);


			//get collection image
			$this->db->select('image');
			$this->db->where('collection_id',$collection['collection_id']);
			$result = $this->db->get('playbasis_badge_collection');

			$result = $result->row_array();
			$result['image'] = $this->config->item('IMG_PATH').$result['image'];

			$collection = array_merge($collection,$result);


			//get badge_id relate to collection
			$this->db->select('badge_id');
			$this->db->where('collection_id',$collection['collection_id']);
			$result = $this->db->get('playbasis_badge_to_collection');

			$result = $result->result_array();
			$collection['badge'] = $result;
		}

		return $collectionSet;
	}

	public function getCollection($data){
		//check collection
		$this->db->select('collection_id');
		$this->db->where(array('client_id'=>$data['client_id'],'site_id'=>$data['site_id'],'collection_id'=>$data['collection_id']));
		$result = $this->db->get('playbasis_badge_collection_to_client');

		$result = $result->row_array();

		if(!$result)
			return array();

		$collection = array('collection_id'=>$data['collection_id']);
		
		$this->db->select('name,description');
		$this->db->where('collection_id',$collection['collection_id']);
		$result = $this->db->get('playbasis_badge_collection_description');

		$result = $result->row_array();

		$collection = array_merge($collection,$result);


		//get badge image
		$this->db->select('image');
		$this->db->where('collection_id',$collection['collection_id']);
		$result = $this->db->get('playbasis_badge_collection');

		$result = $result->row_array();
		$result['image'] = $this->config->item('IMG_PATH').$result['image'];
		
		$collection = array_merge($collection,$result);

		//get badge_id relate to collection
		$this->db->select('badge_id');
		$this->db->where('collection_id',$collection['collection_id']);
		$result = $this->db->get('playbasis_badge_to_collection');

		$result = $result->result_array();
		$collection['badge'] = $result;


		return $collection;
	}
}
?>