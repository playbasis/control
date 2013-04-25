<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('TOKEN_EXPIRE', (3 * 24 * 3600)); // 3 days
class Auth_model extends CI_model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function getApiInfo($data)
	{
		$this->db->select('site_id,client_id,domain_name,site_name');
		$this->db->where(array(
			'api_key' => $data['key'],
			'api_secret' => $data['secret'],
			'date_expire >' => date('Y-m-d H:i:s'),
			'status' => '1'
		));
		$result = $this->db->get('playbasis_client_site');
		return $result->row_array();
	}
	public function generateToken($data)
	{
		$this->db->select('token');
		$this->db->where(array(
			'site_id' => $data['site_id'],
			'client_id' => $data['client_id'],
			'date_expire >' => date('Y-m-d H:i:s')
		));
		$token = $this->db->get('playbasis_token');
		$token = $token->row_array();
		if(!$token)
		{
			$token['token'] = hash('sha1', $data['key'] . time() . $data['secret']);
			$expire = date('Y-m-d H:i:s', time() + TOKEN_EXPIRE);
			//delete old token
			$this->db->where(array(
				'site_id' => $data['site_id'],
				'client_id' => $data['client_id']
			));
			$this->db->delete('playbasis_token');
			$this->db->insert('playbasis_token', array(
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
		$this->db->select('client_id,site_id');
		$this->db->where(array(
			'token' => $token,
			'date_expire >' => date('Y-m-d H:i:s')
		));
		$result = $this->db->get('playbasis_token');
		$info = $result->row_array();
		if($info)
		{
			$this->db->select('domain_name,site_name');
			$this->db->where($info);
			$result = $this->db->get('playbasis_client_site');
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
		$this->db->select('domain_name,site_name');
		$this->db->where($info);
		$result = $this->db->get('playbasis_client_site');
		$result = $result->row_array();
		if($result)
		{
			return array_merge($info, $result);
		}
		return null;
	}
	public function createTokenFromAPIKey($apiKey)
	{
		$this->db->select('client_id,site_id,domain_name,site_name');
		$this->db->where(array(
			'api_key' => $apiKey,
			));
		$result = $this->db->get('playbasis_client_site');
		return $result->row_array();
	}
}
?>