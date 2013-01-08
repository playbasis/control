<?php defined('BASEPATH') OR exit('No direct script access allowed');

//constant
define('TOKEN_EXPIRE', (3*24*3600));   // 3 days

class Auth_model extends CI_model{

	public function __construct(){
		parent::__construct();
	}

	#get api key and secret
	public function getApiInfo($data){
		/*
		$sql = "SELECT `site_id`,`client_id` FROM `playbasis_client_site` WHERE `api_key` = ?  AND `api_secret` = ? AND `date_expire` >= NOW() AND `status` = 1";

		$bindData = array(
			$data['key'],
			$data['secret'],
		);

		$result = $this->db->query($sql,$bindData);
		*/

		$this->db->select('site_id,client_id');
		
		$this->db->where(array('api_key'=>$data['key'],'api_secret'=>$data['secret'],'date_expire >='=>date('Y-m-d H:i:s'),'status'=>'1'));

		$result = $this->db->get('playbasis_client_site');
		
		return $result->row_array();	
	}

	#generate token and update token in database
	public function generateToken($data){

		// $sql = "INSERT INTO `playbasis_token` SET `client_id` = ? , `site_id` = ? , `token` = ?";

		// $bindData = array(
		// 	$data['client_id'],
		// 	$data['site_id'],
		// 	$token,
		// );

		// $result = $this->db->query($sql,$bindData);

		$this->db->select('token');
		
		$this->db->where(array('site_id'=>$data['site_id'],'client_id'=>$data['client_id'],'date_expire >'=>date('Y-m-d H:i:s')));

		$token = $this->db->get('playbasis_token');
		$token = $token->row_array();

		if(!$token){
			$token['token'] = hash('sha1',$data['key'].time().$data['secret']);
			$expire = date('Y-m-d H:i:s',time()+TOKEN_EXPIRE); 
			
			
			#delete old token
			$this->db->where(array('site_id'=>$data['site_id'],'client_id'=>$data['client_id']));
			$this->db->delete('playbasis_token');

			$this->db->insert('playbasis_token',array('client_id'=>$data['client_id'],'site_id'=>$data['site_id'],'token'=>$token['token'],'date_expire'=>$expire));

		}
		
		return $token;
	}

	#find token
	public function findToken($data){
		
		$this->db->select('client_id,site_id');
		$this->db->where($data);
		$result = $this->db->get('playbasis_token');

		return $result->row_array();

	}
}
?>