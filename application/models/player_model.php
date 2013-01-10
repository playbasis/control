<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Player_model extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
	//CRUD
	public function createPlayer($data){


		$this->db->insert('playbasis_player',array(
			'client_id'=>$data['client_id'],
			'site_id'=>$data['site_id'],
			'cl_player_id'=>$data['player_id'],
			'first_name'=>$data['first_name'],
			'last_name'=>$data['last_name'],
			'image'=>$data['image'],
			'email' =>$data['email'],
			'date_added' =>date('Y-m-d H:i:s'),
			'date_modified' =>date('Y-m-d H:i:s'),
			)
		);

		return $this->db->insert_id();
	}

	public function readPlayer($id,$fields){
		if(!$id)
			return array();
		
		if($fields)
			$this->db->select($fields);
		
		$this->db->where('pb_player_id',$id);
		$result = $this->db->get('playbasis_player');

		return $result->row_array(); 
	}

	public function readPlayers($fields,$offset=0,$limit=10){
		if($fields)
			$this->db->select($fields);

		$result = $this->db->get('playbasis_player',$offset,$limit);

		return $result->result_array();
	}

	public function updatePlayer($id,$fieldData){
		if(!$id)
			return false;
		
		$this->db->where('pb_player_id',$id);
		$this->db->update('playbasis_player',$fieldData);
		return true;
	}

	public function deletePlayer($id){
		if(!$id)
			return false;

		$this->db->where('pb_player_id',$id);
		$this->db->delete('playbasis_player');
		return true;
	}

	public function getPlaybasisId($clientData){
		if(!$clientData)
			return -1;

		$this->db->where(array('client_id'=>$clientData['client_id'],'site_id'=>$clientData['site_id'],'cl_player_id'=>$clientData['cl_player_id']));
		$this->db->select('pb_player_id');

		$result = $this->db->get('playbasis_player');

		if(!$result->row_array())
			return -1;

		//$id = $result->row_array();
		
		return $result->row_array()['pb_player_id']; 
	}
}
?>