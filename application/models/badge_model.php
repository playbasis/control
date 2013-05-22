<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Badge_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->config->load('playbasis');
	}
	public function getAllBadges($data)
	{
//		//get badge ids
//		$this->db->select('badge_id');
//		$this->db->where(array(
//			'client_id' => $data['client_id'],
//			'site_id' => $data['site_id']
//		));
//		$result = $this->db->get('playbasis_badge_to_client');
//		$badgeSet = $result->result_array();
//		if(!$badgeSet)
//			return array();
//		//get data on badges
//		foreach($badgeSet as &$badge)
//		{
//			//get badge data
//			$this->db->select('name,description,hint');
//			$this->db->where('badge_id', $badge['badge_id']);
//			$result = $this->db->get('playbasis_badge_description');
//			$result = $result->row_array();
//			$badge = array_merge($badge, $result);
//			//get badge image
//			$this->db->select('image');
//			$this->db->where('badge_id', $badge['badge_id']);
//			$result = $this->db->get('playbasis_badge');
//			$result = $result->row_array();
//			$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
//			$badge = array_merge($badge, $result);
//		}
//		return $badgeSet;

        // name for memcached
        $sql = "SELECT badge_id FROM playbasis_badge_to_client WHERE client_id = ".$data['client_id']." AND site_id = ".$data['site_id'];
        $md5name = md5($sql);
        $table = "playbasis_badge_to_client";

        $results = $this->memcached_library->get('sql_' . $md5name.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

        //get badge ids
		$this->db->select('badge_id');
		$this->db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id']
		));
		$result = $this->db->get('playbasis_badge_to_client');
		$badgeSet = $result->result_array();
		if(!$badgeSet)
			return array();
		//get data on badges
		foreach($badgeSet as &$badge)
		{
			//get badge data
			$this->db->select('name,description,hint');
			$this->db->where('badge_id', $badge['badge_id']);
			$result = $this->db->get('playbasis_badge_description');
			$result = $result->row_array();
			$badge = array_merge($badge, $result);
			//get badge image
			$this->db->select('image');
			$this->db->where('badge_id', $badge['badge_id']);
			$result = $this->db->get('playbasis_badge');
			$result = $result->row_array();
			$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
			$badge = array_merge($badge, $result);
		}

        $this->memcached_library->add('sql_' . $md5name.".".$table, $badgeSet);

		return $badgeSet;
	}
	public function getBadge($data)
	{
//		//get badge id
//		$this->db->select('badge_id');
//		$this->db->where(array(
//			'client_id' => $data['client_id'],
//			'site_id' => $data['site_id'],
//			'badge_id' => $data['badge_id']
//		));
//		$result = $this->db->get('playbasis_badge_to_client');
//		$result = $result->row_array();
//		if(!$result)
//			return array();
//		$badge = array(
//			'badge_id' => $data['badge_id']
//		);
//		//get badge data
//		$this->db->select('name,description,hint');
//		$this->db->where('badge_id', $badge['badge_id']);
//		$result = $this->db->get('playbasis_badge_description');
//		$result = $result->row_array();
//		$badge = array_merge($badge, $result);
//		//get badge image
//		$this->db->select('image');
//		$this->db->where('badge_id', $badge['badge_id']);
//		$result = $this->db->get('playbasis_badge');
//		$result = $result->row_array();
//		$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
//		$badge = array_merge($badge, $result);
//		return $badge;

        // name for memcached
        $sql = "SELECT badge_id FROM playbasis_badge_to_client WHERE client_id = ".$data['client_id']." AND site_id = ".$data['site_id']." AND badge_id = ".$data['badge_id'];
        $md5name = md5($sql);
        $table = "playbasis_badge_to_client";

        $results = $this->memcached_library->get('sql_' . $md5name.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

        //get badge id
		$this->db->select('badge_id');
		$this->db->where(array(
			'client_id' => $data['client_id'],
			'site_id' => $data['site_id'],
			'badge_id' => $data['badge_id']
		));
		$result = $this->db->get('playbasis_badge_to_client');
		$result = $result->row_array();
		if(!$result)
			return array();
		$badge = array(
			'badge_id' => $data['badge_id']
		);
		//get badge data
		$this->db->select('name,description,hint');
		$this->db->where('badge_id', $badge['badge_id']);
		$result = $this->db->get('playbasis_badge_description');
		$result = $result->row_array();
		$badge = array_merge($badge, $result);
		//get badge image
		$this->db->select('image');
		$this->db->where('badge_id', $badge['badge_id']);
		$result = $this->db->get('playbasis_badge');
		$result = $result->row_array();
		$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
		$badge = array_merge($badge, $result);

        $this->memcached_library->add('sql_' . $md5name.".".$table, $badge);

		return $badge;

	}
	public function getAllCollection($data)
	{
//		//get collection ids
//		$this->db->select('collection_id');
//		$this->db->where(array(
//			'client_id' => $data['client_id'],
//			'site_id' => $data['site_id']
//		));
//		$result = $this->db->get('playbasis_badge_collection_to_client');
//		$collectionSet = $result->result_array();
//		if(!$collectionSet)
//			return array();
//		//get data on collections
//		foreach($collectionSet as &$collection)
//		{
//			//get collection data
//			$this->db->select('name,description');
//			$this->db->where('collection_id', $collection['collection_id']);
//			$result = $this->db->get('playbasis_badge_collection_description');
//			$result = $result->row_array();
//			$collection = array_merge($collection, $result);
//			//get collection image
//			$this->db->select('image');
//			$this->db->where('collection_id', $collection['collection_id']);
//			$result = $this->db->get('playbasis_badge_collection');
//			$result = $result->row_array();
//			$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
//			$collection = array_merge($collection, $result);
//			//get badge_id related to a collection
//			$this->db->select('badge_id');
//			$this->db->where('collection_id', $collection['collection_id']);
//			$result = $this->db->get('playbasis_badge_to_collection');
//			$result = $result->result_array();
//			$collection['badge'] = $result;
//		}
//		return $collectionSet;


        // name for memcached
        $sql = "SELECT collection_id FROM playbasis_badge_collection_to_client WHERE client_id = ".$data['client_id']." AND site_id = ".$data['site_id'];
        $md5name = md5($sql);
        $table = "playbasis_badge_collection_to_client";

        $results = $this->memcached_library->get('sql_' . $md5name.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

        //get collection ids
        $this->db->select('collection_id');
        $this->db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id']
        ));
        $result = $this->db->get('playbasis_badge_collection_to_client');
        $collectionSet = $result->result_array();
        if(!$collectionSet)
            return array();
        //get data on collections
        foreach($collectionSet as &$collection)
        {
            //get collection data
            $this->db->select('name,description');
            $this->db->where('collection_id', $collection['collection_id']);
            $result = $this->db->get('playbasis_badge_collection_description');
            $result = $result->row_array();
            $collection = array_merge($collection, $result);
            //get collection image
            $this->db->select('image');
            $this->db->where('collection_id', $collection['collection_id']);
            $result = $this->db->get('playbasis_badge_collection');
            $result = $result->row_array();
            $result['image'] = $this->config->item('IMG_PATH') . $result['image'];
            $collection = array_merge($collection, $result);
            //get badge_id related to a collection
            $this->db->select('badge_id');
            $this->db->where('collection_id', $collection['collection_id']);
            $result = $this->db->get('playbasis_badge_to_collection');
            $result = $result->result_array();
            $collection['badge'] = $result;
        }

        $this->memcached_library->add('sql_' . $md5name.".".$table, $collectionSet);

        return $collectionSet;

	}
	public function getCollection($data)
	{
//		//get collection id
//		$this->db->select('collection_id');
//		$this->db->where(array(
//			'client_id' => $data['client_id'],
//			'site_id' => $data['site_id'],
//			'collection_id' => $data['collection_id']
//		));
//		$result = $this->db->get('playbasis_badge_collection_to_client');
//		$result = $result->row_array();
//		if(!$result)
//			return array();
//		$collection = array(
//			'collection_id' => $data['collection_id']
//		);
//		//get collection data
//		$this->db->select('name,description');
//		$this->db->where('collection_id', $collection['collection_id']);
//		$result = $this->db->get('playbasis_badge_collection_description');
//		$result = $result->row_array();
//		$collection = array_merge($collection, $result);
//		//get badge image
//		$this->db->select('image');
//		$this->db->where('collection_id', $collection['collection_id']);
//		$result = $this->db->get('playbasis_badge_collection');
//		$result = $result->row_array();
//		$result['image'] = $this->config->item('IMG_PATH') . $result['image'];
//		$collection = array_merge($collection, $result);
//		//get badge_id related to the collection
//		$this->db->select('badge_id');
//		$this->db->where('collection_id', $collection['collection_id']);
//		$result = $this->db->get('playbasis_badge_to_collection');
//		$result = $result->result_array();
//		$collection['badge'] = $result;
//		return $collection;

        // name for memcached
        $sql = "SELECT collection_id FROM playbasis_badge_collection_to_client WHERE client_id = ".$data['client_id']." AND site_id = ".$data['site_id']." AND collection_id = ".$data['collection_id'];
        $md5name = md5($sql);
        $table = "playbasis_badge_collection_to_client";

        $results = $this->memcached_library->get('sql_' . $md5name.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

        //get collection id
        $this->db->select('collection_id');
        $this->db->where(array(
            'client_id' => $data['client_id'],
            'site_id' => $data['site_id'],
            'collection_id' => $data['collection_id']
        ));
        $result = $this->db->get('playbasis_badge_collection_to_client');
        $result = $result->row_array();
        if(!$result)
            return array();
        $collection = array(
            'collection_id' => $data['collection_id']
        );
        //get collection data
        $this->db->select('name,description');
        $this->db->where('collection_id', $collection['collection_id']);
        $result = $this->db->get('playbasis_badge_collection_description');
        $result = $result->row_array();
        $collection = array_merge($collection, $result);
        //get badge image
        $this->db->select('image');
        $this->db->where('collection_id', $collection['collection_id']);
        $result = $this->db->get('playbasis_badge_collection');
        $result = $result->row_array();
        $result['image'] = $this->config->item('IMG_PATH') . $result['image'];
        $collection = array_merge($collection, $result);
        //get badge_id related to the collection
        $this->db->select('badge_id');
        $this->db->where('collection_id', $collection['collection_id']);
        $result = $this->db->get('playbasis_badge_to_collection');
        $result = $result->result_array();
        $collection['badge'] = $result;

        $this->memcached_library->add('sql_' . $md5name.".".$table, $collection);

        return $collection;
	}
}
?>