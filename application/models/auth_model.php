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
			'_id',
			'client_id',
			'domain_name',
			'site_name'
		));
		$this->mongo_db->where(array(
			'api_key' => $data['key'],
			'api_secret' => $data['secret'],
			'status' => true
		));
		$result = $this->mongo_db->get('playbasis_client_site');
		if($result)
		{
			$result = $result[0];
			$result['site_id'] = $result['_id'];
			unset($result['_id']);
			return $result;
		}
		return array();
	}
	public function generateToken($data)
	{
		$this->set_site_mongodb($data['site_id']);
		$this->mongo_db->select(array(
			'token',
			'date_expire'
		));
		$this->mongo_db->where(array(
			'site_id' => $data['site_id'],
			'client_id' => $data['client_id'],
		));
		$this->mongo_db->where_gt('date_expire', new MongoDate(time()));
		$token = $this->mongo_db->get('playbasis_token');
		if($token && $token[0])
		{
			$result['token'] = $token[0]['token'];
//			$result['date_expire'] = date('Y-m-d H:i:s', $token[0]['date_expire']->sec);
			$result['date_expire'] = datetimeMongotoReadable($token[0]['date_expire']);
			return $result;
		}
		return $this->renewToken($data);
	}
	public function renewToken($data)
	{
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
			$this->mongo_db->delete_all('playbasis_token');
			//insert new token
			$this->mongo_db->insert('playbasis_token', array(
				'client_id' => $data['client_id'],
				'site_id' => $data['site_id'],
				'token' => $token['token'],
				'date_expire' => $expire
			));
		}
//		$token['date_expire'] = date('Y-m-d H:i:s', $expire->sec);
		$token['date_expire'] = datetimeMongotoReadable($expire);
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
		$result = $this->mongo_db->get('playbasis_token');
		if($result && $result[0])
		{
			$info = $result[0];
			$info['_id'] = $info['site_id'];
			unset($info['site_id']);
			$this->set_site_mongodb($info['_id']);
			$this->mongo_db->select(array(
				'domain_name',
				'site_name'
			));
			$this->mongo_db->where($info);
			$result = $this->mongo_db->get('playbasis_client_site');
			$result = array_merge($info, ($result) ? $result[0] : array());
			$result['site_id'] = $result['_id'];
			unset($result['_id']);
			return $result;
		}
		return null;
	}
	public function createToken($client_id, $site_id)
	{
		$info = array(
			'client_id' => $client_id,
			'_id' => $site_id
		);
		$this->set_site_mongodb($site_id);
		$this->mongo_db->select(array(
			'domain_name',
			'site_name'
		));
		$this->mongo_db->where($info);
		$result = $this->mongo_db->get('playbasis_client_site');
		if($result && $result[0])
		{
			$result = array_merge($info, $result[0]);
			$result['site_id'] = $result['_id'];
			unset($result['_id']);
			return $result;
		}
		return null;
	}
	public function createTokenFromAPIKey($apiKey)
	{
		$this->set_site_mongodb(0);
		$this->mongo_db->select(array(
			'client_id',
			'_id',
			'domain_name',
			'site_name'
		));
		$this->mongo_db->where(array(
			'api_key' => $apiKey,
			));
		$result = $this->mongo_db->get('playbasis_client_site');
		if($result && $result[0])
		{
			$result = $result[0];
			$result['site_id'] = $result['_id'];
			unset($result['_id']);
			return $result;
		}
		return array();
	}
}
?>
