<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('TOKEN_EXPIRE', (3 * 24 * 3600)); // 3 days
class Auth_model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function getApiInfo($data)
	{
		$this->set_site(0);
		$this->site_db()->select('site_id,client_id,domain_name,site_name');
		$this->site_db()->where(array(
			'api_key' => $data['key'],
			'api_secret' => $data['secret'],
			'date_expire >' => date('Y-m-d H:i:s'),
			'status' => '1'
		));
		$result = $this->site_db()->get('playbasis_client_site');
		return $result->row_array();
	}
	public function generateToken($data)
	{
		$this->set_site($data['site_id']);
		$this->site_db()->select('token');
		$this->site_db()->where(array(
			'site_id' => $data['site_id'],
			'client_id' => $data['client_id'],
			'date_expire >' => date('Y-m-d H:i:s')
		));
		$token = $this->site_db()->get('playbasis_token');
		$token = $token->row_array();
		if(!$token)
		{
			$token['token'] = hash('sha1', $data['key'] . time() . $data['secret']);
			$expire = date('Y-m-d H:i:s', time() + TOKEN_EXPIRE);
			$updated = array();
			foreach($this->dbs as $key => $value)
			{
				//keep track of which db is already updated
				$group = $this->dbGroups[$key];
				if(isset($updated[$group]))
					continue;
				$updated[$group] = true;
				//delete old token
				$value->where(array(
					'site_id' => $data['site_id'],
					'client_id' => $data['client_id']
				));
				$value->delete('playbasis_token');
				//insert new token
				$value->insert('playbasis_token', array(
					'client_id' => $data['client_id'],
					'site_id' => $data['site_id'],
					'token' => $token['token'],
					'date_expire' => $expire
				));
			}
		}
		return $token;
	}
	public function findToken($token)
	{
		$this->set_site(0);
		$this->site_db()->select('client_id,site_id');
		$this->site_db()->where(array(
			'token' => $token,
			'date_expire >' => date('Y-m-d H:i:s')
		));
		$result = $this->site_db()->get('playbasis_token');
		$info = $result->row_array();
		if($info)
		{
			$this->set_site($info['site_id']);
			$this->site_db()->select('domain_name,site_name');
			$this->site_db()->where($info);
			$result = $this->site_db()->get('playbasis_client_site');
			return array_merge($info, $result->row_array());
		}
		return null;
	}
	public function createToken($client_id, $site_id)
	{
		$info = array(
			'client_id' => $client_id,
			'site_id' => $site_id
		);
		$this->set_site($site_id);
		$this->site_db()->select('domain_name,site_name');
		$this->site_db()->where($info);
		$result = $this->site_db()->get('playbasis_client_site');
		$result = $result->row_array();
		if($result)
			return array_merge($info, $result);
		return null;
	}
	public function createTokenFromAPIKey($apiKey)
	{
		$this->set_site(0);
		$this->site_db()->select('client_id,site_id,domain_name,site_name');
		$this->site_db()->where(array(
			'api_key' => $apiKey,
			));
		$result = $this->site_db()->get('playbasis_client_site');
		return $result->row_array();
	}
}
?>