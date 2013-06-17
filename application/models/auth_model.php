<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('TOKEN_EXPIRE', (3 * 24 * 3600)); // 3 days
class Auth_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('mongo_db');
	}
	public function getApiInfo($data)
	{
		$this->set_site_mongodb(0);
		$this->mongo_db->select(array(
			'site_id',
			'client_id',
			'domain_name',
			'site_name'
		));
		$this->mongo_db->where(array(
			'api_key' => $data['key'],
			'api_secret' => $data['secret'],
			'status' => true
		));
		$this->mongo_db->where_gt('date_expire', new MongoDate(time()));
		$result = $this->mongo_db->get('client_site');
		if($result)
			return $result[0];
		return array();
	}
	public function generateToken($data)
	{
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select('token');
		$this->mongo_db->where(array(
			'site_id' => $data['site_id'],
			'client_id' => $data['client_id'],
		));
		$this->mongo_db->where_gt('date_expire', new MongoDate(time()));
		$token = $this->mongo_db->get('token');
		if($token && $token[0])
		{
			$result['token'] = $token[0]['token'];
			return $result;
		}
		$token = array();
		$token['token'] = hash('sha1', $data['key'] . time() . $data['secret']);
		$expire = new MongoDate(time() + TOKEN_EXPIRE);
		$updated = array();
		foreach(self::$dblist as $key => $value)
		{
			//keep track of which db is already updated
			if(isset($updated[$value]))
				continue;
			$updated[$value] = true;
			//delete old token
			$this->set_site_mongodb($key);
			$this->mongo_db->where(array(
				'site_id' => $data['site_id'],
				'client_id' => $data['client_id']
			));
			$this->mongo_db->delete('token');
			//insert new token
			$this->mongo_db->insert('token', array(
				'client_id' => $data['client_id'],
				'site_id' => $data['site_id'],
				'token' => $token['token'],
				'date_expire' => $expire
			));
		}
		return $token;
	}
	public function findToken($token)
	{
		$this->set_site_mongodb(0);
		$this->mongo_db->select(array(
			'client_id',
			'site_id'
		));
		$this->mongo_db->where(array(
			'token' => $token,
		));
		$this->mongo_db->where_gt('date_expire', new MongoDate(time()));
		$result = $this->mongo_db->get('token');
		if($result && $result[0])
		{
			$info = $result[0];
			$this->set_site_mongodb($info['site_id']);
			$this->mongo_db->select(array(
				'domain_name',
				'site_name'
			));
			$this->mongo_db->where($info);
			$result = $this->mongo_db->get('client_site');
			return array_merge($info, ($result) ? $result[0] : array());
		}
		return null;
	}
	public function createToken($client_id, $site_id)
	{
		$info = array(
			'client_id' => $client_id,
			'site_id' => $site_id
		);
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'domain_name',
			'site_name'
		));
		$this->mongo_db->where($info);
		$result = $this->mongo_db->get('client_site');
		$result = $result->row_array();
		if($result && $result[0])
			return array_merge($info, $result[0]);
		return null;
	}
	public function createTokenFromAPIKey($apiKey)
	{
		$this->set_site_mongodb(0);
		$this->mongo_db->select(array(
			'client_id',
			'site_id',
			'domain_name',
			'site_name'
		));
		$this->mongo_db->where(array(
			'api_key' => $apiKey,
		));
		$result = $this->mongo_db->get('client_site');
		return ($result) ? $result[0] : array();
	}
}
?>