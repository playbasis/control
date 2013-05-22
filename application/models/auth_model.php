<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('TOKEN_EXPIRE', (3 * 24 * 3600)); // 3 days
class Auth_model extends CI_model
{
	public function __construct()
	{
		parent::__construct();
        $this->load->library('memcached_library');
	}
	public function getApiInfo($data)
	{
//		$this->db->select('site_id,client_id,domain_name,site_name');
//		$this->db->where(array(
//			'api_key' => $data['key'],
//			'api_secret' => $data['secret'],
//			'date_expire >' => date('Y-m-d H:i:s'),
//			'status' => '1'
//		));
//		$result = $this->db->get('playbasis_client_site');
//		return $result->row_array();


        // name for memcached
        $sql = "SELECT site_id, client_id, domain_name, site_name FROM playbasis_client_site WHERE api_key = ".$data['key']." AND api_secret = ".$data['secret']." AND date_expire = ".date('Y-m-d H:i:s')." AND status = 1";
        $md5query = md5($sql);
        $table = "playbasis_client_site";

        $results = $this->memcached_library->get('sql_' . $md5query.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

        // so if cannot get any result
        $this->db->select('site_id,client_id,domain_name,site_name');
		$this->db->where(array(
			'api_key' => $data['key'],
			'api_secret' => $data['secret'],
			'date_expire >' => date('Y-m-d H:i:s'),
			'status' => '1'
		));
		$result = $this->db->get('playbasis_client_site');
        $result = $result->row_array();

        $this->memcached_library->add('sql_' . $md5query.".".$table, $result);

		return $result;
	}
	public function generateToken($data)
	{
//		$this->db->select('token');
//		$this->db->where(array(
//			'site_id' => $data['site_id'],
//			'client_id' => $data['client_id'],
//			'date_expire >' => date('Y-m-d H:i:s')
//		));
//		$token = $this->db->get('playbasis_token');
//		$token = $token->row_array();
//		if(!$token)
//		{
//			$token['token'] = hash('sha1', $data['key'] . time() . $data['secret']);
//			$expire = date('Y-m-d H:i:s', time() + TOKEN_EXPIRE);
//			//delete old token
//			$this->db->where(array(
//				'site_id' => $data['site_id'],
//				'client_id' => $data['client_id']
//			));
//			$this->db->delete('playbasis_token');
//			$this->db->insert('playbasis_token', array(
//				'client_id' => $data['client_id'],
//				'site_id' => $data['site_id'],
//				'token' => $token['token'],
//				'date_expire' => $expire
//			));
//		}
//		return $token;

        // name for memcached
        $sql = "SELECT token FROM playbasis_token WHERE api_key = ".$data['key']." AND api_secret = ".$data['secret']." AND date_expire = ".date('Y-m-d H:i:s');
        $md5query = md5($sql);
        $table = "playbasis_token";

        $results = $this->memcached_library->get('sql_' . $md5query.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

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

            // clear memcached on this table
            $this->memcached_library->update_delete($table);
        }

        $this->memcached_library->add('sql_' . $md5query.".".$table, $token);

        return $token;
	}
	public function findToken($token)
	{
//		$this->db->select('client_id,site_id');
//		$this->db->where(array(
//			'token' => $token,
//			'date_expire >' => date('Y-m-d H:i:s')
//		));
//		$result = $this->db->get('playbasis_token');
//		$info = $result->row_array();
//		if($info)
//		{
//			$this->db->select('domain_name,site_name');
//			$this->db->where($info);
//			$result = $this->db->get('playbasis_client_site');
//			return array_merge($info, $result->row_array());
//		}
//		return null;

        // name for memcached
        $sql = "SELECT client_id, site_id FROM playbasis_token WHERE token = ".$token." AND date_expire = ".date('Y-m-d H:i:s');
        $md5query = md5($sql);
        $table = "playbasis_token";

        $results = $this->memcached_library->get('sql_' . $md5query.".".$table);

        // gotcha i got result
        if ($results)
            return $results;

        // so if cannot get any result
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

            $this->memcached_library->add('sql_' . $md5query.".".$table, array_merge($info, $result->row_array()));

			return array_merge($info, $result->row_array());
		}

        $this->memcached_library->add('sql_' . $md5query.".".$table, null);

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
            // for clear memcached data
            $table = "playbasis_client_site";
            $this->memcached_library->update_delete($table);

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

        // for clear memcached data
        $table = "playbasis_client_site";
        $this->memcached_library->update_delete($table);

		return $result->row_array();
	}
}
?>