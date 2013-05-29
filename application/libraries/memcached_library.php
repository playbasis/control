<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Memcached_library
{
	
	private $config;
	private $local_cache = array();
	private $m;
	private $client_type;
	private $ci;
	protected $errors = array();
    private $keep = array();
    private $table_array = true;
	
	
	public function __construct()
	{
		$this->ci =& get_instance();
		
		// Lets try to load Memcache or Memcached Class
		$this->client_type = class_exists('Memcache') ? "Memcache" : (class_exists('Memcached') ? "Memcached" : FALSE);
		
		if($this->client_type) 
		{
			$this->ci->load->config('memcached');
			$this->config = $this->ci->config->item('memcached');
			
			// Which one should be loaded
			switch($this->client_type)
			{
				case 'Memcached':
					$this->m = new Memcached();
					break;
				case 'Memcache':
					$this->m = new Memcache();
					// Set Automatic Compression Settings
					if ($this->config['config']['auto_compress_tresh'])
					{
						$this->setcompressthreshold($this->config['config']['auto_compress_tresh'], $this->config['config']['auto_compress_savings']);
					}
					break;
			}
			log_message('debug', "Memcached Library: $this->client_type Class Loaded");
			
			$this->auto_connect();	
		}
		else
		{
			log_message('error', "Memcached Library: Failed to load Memcached or Memcache Class");
		}
	}
	
	/*
	+-------------------------------------+
		Name: auto_connect
		Purpose: runs through all of the servers defined in
		the configuration and attempts to connect to each
		@param return : none
	+-------------------------------------+
	*/
	private function auto_connect()
	{
		foreach($this->config['servers'] as $key=>$server)
		{
			if(!$this->add_server($server))
			{
				$this->errors[] = "Memcached Library: Could not connect to the server named $key";
				log_message('error', 'Memcached Library: Could not connect to the server named "'.$key.'"');
			}
			else
			{
				log_message('debug', 'Memcached Library: Successfully connected to the server named "'.$key.'"');
			}
		}
	}
	
	/*
	+-------------------------------------+
		Name: add_server
		Purpose: 
		@param return : TRUE or FALSE
	+-------------------------------------+
	*/
	public function add_server($server)
	{
		extract($server);
//        return $this->m->addServer($host, $port, $weight, $timeout);
        switch($this->client_type)
        {
            case 'Memcached':
                return $this->m->addServer($host, $port, $weight, $timeout);
                break;
            case 'Memcache':
                return $this->m->addServer($host, $port, $persistent , $weight, $timeout);
                break;
        }
	}
	
	/*
	+-------------------------------------+
		Name: add
		Purpose: add an item to the memcache server(s)
		@param return : TRUE or FALSE
	+-------------------------------------+
	*/
	public function add($key = NULL, $value = NULL, $expiration = NULL)
	{
        if($this->table_array)
            $this->keep_table($key);

		if(is_null($expiration))
		{
			$expiration = $this->config['config']['expiration'];
		}
		if(is_array($key))
		{
			foreach($key as $multi)
			{
				if(!isset($multi['expiration']) || $multi['expiration'] == '')
				{
					$multi['expiration'] = $this->config['config']['expiration'];
				}
				$this->add($this->key_name($multi['key']), $multi['value'], $multi['expiration']);
			}
		}
		else
		{
			$this->local_cache[$this->key_name($key)] = $value;
			switch($this->client_type)
			{
				case 'Memcache':
					$add_status = $this->m->add($this->key_name($key), $value, $this->config['config']['compression'], $expiration);
					break;
					
				default:
				case 'Memcached':
					$add_status = $this->m->add($this->key_name($key), $value, $expiration);
					break;
			}
			
			return $add_status;
		}
	}
	
	/*
	+-------------------------------------+
		Name: set
		Purpose: similar to the add() method but uses set
		@param return : TRUE or FALSE
	+-------------------------------------+
	*/
	public function set($key = NULL, $value = NULL, $expiration = NULL)
	{
        if($this->table_array)
            $this->keep_table($key);

		if(is_null($expiration))
		{
			$expiration = $this->config['config']['expiration'];
		}
		if(is_array($key))
		{
			foreach($key as $multi)
			{
				if(!isset($multi['expiration']) || $multi['expiration'] == '')
				{
					$multi['expiration'] = $this->config['config']['expiration'];
				}
				$this->set($this->key_name($multi['key']), $multi['value'], $multi['expiration']);
			}
		}
		else
		{
			$this->local_cache[$this->key_name($key)] = $value;
			switch($this->client_type)
			{
				case 'Memcache':
					$add_status = $this->m->set($this->key_name($key), $value, $this->config['config']['compression'], $expiration);
					break;
					
				default:
				case 'Memcached':
					$add_status = $this->m->set($this->key_name($key), $value, $expiration);
					break;
			}
			
			return $add_status;
		}
	}
	
	/*
	+-------------------------------------+
		Name: get
		Purpose: gets the data for a single key or an array of keys
		@param return : array of data or multi-dimensional array of data
	+-------------------------------------+
	*/
	public function get($key = NULL)
	{
        if($this->table_array)
            if(!$this->check_table($key))
                return FALSE;

		if($this->m)
		{
			if(isset($this->local_cache[$this->key_name($key)]))
			{
				return $this->local_cache[$this->key_name($key)];
			}
			if(is_null($key))
			{
				$this->errors[] = 'The key value cannot be NULL';
				return FALSE;
			}
			
			if(is_array($key))
			{
				foreach($key as $n=>$k)
				{
					$key[$n] = $this->key_name($k);
				}
				return $this->m->getMulti($key);
			}
			else
			{
				return $this->m->get($this->key_name($key));
			}
		}
		return FALSE;		
	}
	
	
	/*
	+-------------------------------------+
		Name: delete
		Purpose: deletes a single or multiple data elements from the memached servers
		@param return : none
	+-------------------------------------+
	*/
	public function delete($key, $expiration = NULL)
	{
		if(is_null($key))
		{
			$this->errors[] = 'The key value cannot be NULL';
			return FALSE;
		}
		
		if(is_null($expiration))
		{
			$expiration = $this->config['config']['delete_expiration'];
		}
		
		if(is_array($key))
		{
			foreach($key as $multi)
			{
				$this->delete($multi, $expiration);
			}
		}
		else
		{
			unset($this->local_cache[$this->key_name($key)]);
			return $this->m->delete($this->key_name($key), $expiration);
		}
	}

    public function update_delete($name, $expiration=NULL)
    {
        if($this->table_array)
            $this->delete_table($name);

        return true;
//        if(is_null($name))
//        {
//            $this->errors[] = 'The name value cannot be NULL';
//            return FALSE;
//        }
//
//        if(is_null($expiration))
//        {
//            $expiration = $this->config['config']['delete_expiration'];
//        }
//
//        switch($this->client_type)
//        {
//            case 'Memcache':
//                $allSlabs = $this->m->getExtendedStats('slabs');
//                foreach($allSlabs as $server => $slabs) {
//                    if($slabs)
//                        foreach($slabs as $slabId => $slabMeta) {
//                            $cdump = $this->m->getExtendedStats('cachedump',(int)$slabId);
//                            if($cdump)
//                                foreach($cdump as $keys => $arrVal) {
//                                    if (!is_array($arrVal)) continue;
//                                    foreach($arrVal as $k => $v) {
//                                        if(preg_match('/'.$name.'/', $k)){
//                                            $this->m->delete($k);
//                                        }
//                                    }
//                                }
//                        }
//                }
//                return true;
//                break;
//
//            default:
//            case 'Memcached':
//                $all_keys = $this->m->getAllKeys();
//                foreach($all_keys as $k => $v) {
//                    if(preg_match('/'.$name.'/', $v)){
//                        $this->memcache->delete($v);
//                    }
//                }
//                return true;
//                break;
//        }

    }
	
	/*
	+-------------------------------------+
		Name: replace
		Purpose: replaces the value of a key that already exists
		@param return : none
	+-------------------------------------+
	*/
	public function replace($key = NULL, $value = NULL, $expiration = NULL)
	{
		if(is_null($expiration))
		{
			$expiration = $this->config['config']['expiration'];
		}
		if(is_array($key))
		{
			foreach($key as $multi)
			{
				if(!isset($multi['expiration']) || $multi['expiration'] == '')
				{
					$multi['expiration'] = $this->config['config']['expiration'];
				}
				$this->replace($multi['key'], $multi['value'], $multi['expiration']);
			}
		}
		else
		{
			$this->local_cache[$this->key_name($key)] = $value;
			
			switch($this->client_type)
			{
				case 'Memcache':
					$replace_status = $this->m->replace($this->key_name($key), $value, $this->config['config']['compression'], $expiration);
					break;
				
				default:
				case 'Memcached':
					$replace_status = $this->m->replace($this->key_name($key), $value, $expiration);
					break;
			}
			
			return $replace_status;
		}
	}
	
	/*
	+-------------------------------------+
		Name: flush
		Purpose: flushes all items from cache
		@param return : none
	+-------------------------------------+
	*/
	public function flush()
	{
		return $this->m->flush();
	}
	
	/*
	+-------------------------------------+
		Name: getversion
		Purpose: Get Server Vesion Number
		@param Returns a string of server version number or FALSE on failure. 
	+-------------------------------------+
	*/
	public function getversion()
	{
		return $this->m->getVersion();
	}
	
	/*
	+-------------------------------------+
		Name: getstats
		Purpose: Get Server Stats
		Possible: "reset, malloc, maps, cachedump, slabs, items, sizes"
		@param returns an associative array with server's statistics. Array keys correspond to stats parameters and values to parameter's values.
	+-------------------------------------+
	*/
	public function getstats($type="items")
	{
		switch($this->client_type)
		{
			case 'Memcache':
				$stats = $this->m->getStats($type);
				break;
			
			default:
			case 'Memcached':
				$stats = $this->m->getStats();
				break;
		}
		return $stats;
	}
	
	/*
	+-------------------------------------+
		Name: setcompresstreshold
		Purpose: Set When Automatic compression should kick-in
		@param return TRUE/FALSE
	+-------------------------------------+
	*/
	public function setcompressthreshold($tresh, $savings=0.2)
	{
		switch($this->client_type)
		{
			case 'Memcache':
				$setcompressthreshold_status = $this->m->setCompressThreshold($tresh, $savings=0.2);
				break;
				
			default:
				$setcompressthreshold_status = TRUE;
				break;
		}
		return $setcompressthreshold_status;
	}
	
	/*
	+-------------------------------------+
		Name: key_name
		Purpose: standardizes the key names for memcache instances
		@param return : md5 key name
	+-------------------------------------+
	*/
	private function key_name($key)
	{
		return md5(strtolower($this->config['config']['prefix'].$key));
	}


    private function keep_table($key)
    {
        if($this->m->get($this->key_name("table")))
            $this->keep = $this->m->get($this->key_name("table"));

        $data = explode(".", $key);
        if(isset($data[1]))
            $this->keep[$data[1]][$data[0]] = true;

        switch($this->client_type)
        {
            case 'Memcache':
                $this->m->set($this->key_name("table"), $this->keep, $this->config['config']['compression'], $this->config['config']['expiration']);
                break;

            default:
            case 'Memcached':
                $this->m->set($this->key_name("table"), $this->keep, $this->config['config']['expiration']);
                break;
        }

        return true;
    }

    private function check_table($key)
    {
        if($this->m->get($this->key_name("table")))
            $this->keep = $this->m->get($this->key_name("table"));

        $data = explode(".", $key);
        return (isset($data[1]) && isset($this->keep[$data[1]]) && isset($this->keep[$data[1]][$data[0]]) && $this->keep[$data[1]][$data[0]]);
    }

    private function delete_table($table)
    {
        if($this->m->get($this->key_name("table")))
            $this->keep = $this->m->get($this->key_name("table"));

        $this->keep[$table] = null;

        switch($this->client_type)
        {
            case 'Memcache':
                $this->m->set($this->key_name("table"), $this->keep, $this->config['config']['compression'], $this->config['config']['expiration']);
                break;

            default:
            case 'Memcached':
                $this->m->set($this->key_name("table"), $this->keep, $this->config['config']['expiration']);
                break;
        }

        return null;
    }
	
}	
/* End of file memcached_library.php */
/* Location: ./application/libraries/memcached_library.php */